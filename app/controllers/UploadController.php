<?php
namespace App\Controllers;

use Core\Controller;
use Core\DB;
use App\Helpers\TextExtract;

class UploadController extends Controller
{
    public function store()
    {
        if (empty($_SESSION['user'])) { header('Location: ' . BASE_URL . 'auth/login'); return; }
        $userId = (int)$_SESSION['user']['id'];
        if (empty($_FILES['file'])) {
            header('Location: ' . BASE_URL . 'dashboard/upload?error=invalid');
            return;
        }
        // Mapeia erros nativos do PHP para mensagens adequadas
        $phpErr = (int)($_FILES['file']['error'] ?? UPLOAD_ERR_NO_FILE);
        if ($phpErr !== UPLOAD_ERR_OK) {
            if ($phpErr === UPLOAD_ERR_INI_SIZE || $phpErr === UPLOAD_ERR_FORM_SIZE) {
                header('Location: ' . BASE_URL . 'dashboard/upload?error=size');
                return;
            }
            header('Location: ' . BASE_URL . 'dashboard/upload?error=invalid');
            return;
        }
        $f = $_FILES['file'];
        $orig = $f['name'];
        $mime = mime_content_type($f['tmp_name']);
        $size = (int)$f['size'];
        $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
        $allowed = ['pdf','epub','docx','txt'];
        $maxSize = 20 * 1024 * 1024; // 20MB
        if (!in_array($ext, $allowed)) {
            header('Location: ' . BASE_URL . 'dashboard/upload?error=ext');
            return;
        }
        if ($size > $maxSize) {
            header('Location: ' . BASE_URL . 'dashboard/upload?error=size');
            return;
        }

        $dir = __DIR__ . '/../../storage/uploads/';
        if (!is_dir($dir)) { @mkdir($dir, 0775, true); }
        $safe = time() . '_' . preg_replace('/[^a-z0-9\._\-]+/i','_', $orig);
        $dest = $dir . $safe;
        if (!move_uploaded_file($f['tmp_name'], $dest)) {
            http_response_code(500);
            die('Falha ao salvar arquivo.');
        }

        // Envia webhook com base64 e metadados (não persiste base64)
        try {
            // Busca URL no system_settings
            $webhookUrl = null;
            try {
                $cfg = DB::pdo()->prepare('SELECT `value` FROM system_settings WHERE `key` = "webhook_upload_url" LIMIT 1');
                $cfg->execute();
                $row = $cfg->fetch();
                if ($row && !empty($row['value'])) { $webhookUrl = trim($row['value']); }
            } catch (\Throwable $e) { /* ignore */ }
            if ($webhookUrl) {
                $bin = @file_get_contents($dest);
                if ($bin !== false) {
                    $b64 = base64_encode($bin);
                    $payload = [
                        'event' => 'upload.created',
                        'user_id' => $userId,
                        'filename' => $orig,
                        'stored_filename' => $safe,
                        'mime' => $mime,
                        'size_bytes' => $size,
                        'extension' => $ext,
                        'base64' => $b64,
                    ];
                    // Preferir cURL, com timeout curto
                    if (function_exists('curl_init')) {
                        $ch = curl_init($webhookUrl);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
                        curl_setopt($ch, CURLOPT_TIMEOUT, 8);
                        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);
                        @curl_exec($ch);
                        @curl_close($ch);
                    } else {
                        $ctx = stream_context_create([
                            'http' => [
                                'method' => 'POST',
                                'header' => "Content-Type: application/json\r\n",
                                'content' => json_encode($payload),
                                'timeout' => 8,
                            ]
                        ]);
                        @file_get_contents($webhookUrl, false, $ctx);
                    }
                    unset($b64, $bin, $payload);
                }
            }
        } catch (\Throwable $e) {
            // Não bloquear fluxo de upload em caso de falha do webhook
        }

        // Extração via helper (PDF com OCR, EPUB, DOCX, TXT)
        $text = TextExtract::fromFile($dest, $ext);
        if ($text === '') {
            $text = 'Texto não extraído nesta etapa (dev).';
        }

        // Salva registro do upload
        $pdo = DB::pdo();
        // cria registro inicial em processing
        $stmt0 = $pdo->prepare("INSERT INTO file_uploads (user_id, filename, mime, pages, size_bytes, text_ref, status) VALUES (:uid, :fn, :mime, NULL, :sz, '', 'processing')");
        $stmt0->execute([':uid'=>$userId, ':fn'=>$safe, ':mime'=>$mime, ':sz'=>$size]);
        $uploadId = (int)$pdo->lastInsertId();
        // atualiza com texto e status pronto
        $stmt = $pdo->prepare("UPDATE file_uploads SET text_ref = :txt, status = 'ready' WHERE id = :id");
        $stmt->execute([':txt'=>$text, ':id'=>$uploadId]);

        // Chunking simples por 800 caracteres
        $chunks = str_split($text, 800);
        $cstmt = $pdo->prepare("INSERT INTO embedding_chunks (upload_id, chunk_text, vector_ref, position) VALUES (:uid, :txt, NULL, :pos)");
        $pos = 0;
        foreach ($chunks as $ch) {
            $cstmt->execute([':uid'=>$uploadId, ':txt'=>$ch, ':pos'=>$pos++]);
        }

        header('Location: ' . BASE_URL . 'dashboard/documents');
        exit;
    }
}
