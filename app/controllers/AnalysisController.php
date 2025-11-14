<?php
namespace App\Controllers;

use Core\Controller;
use Core\DB;

class AnalysisController extends Controller
{
    public function options()
    {
        $uploadId = isset($_GET['upload_id']) ? (int)$_GET['upload_id'] : 0;
        $estimate = null;
        if ($uploadId > 0) {
            $stmt = DB::pdo()->prepare('SELECT id, CHAR_LENGTH(text_ref) AS len FROM file_uploads WHERE id = :id');
            $stmt->execute([':id'=>$uploadId]);
            if ($row = $stmt->fetch()) {
                $chars = (int)$row['len'];
                $tokens_in = (int)ceil($chars / 4); // ~4 chars por token
                $tokens_out = 1000; // default médio
                $tokens_total = $tokens_in + $tokens_out;
                $cost_est = round(($tokens_total/1000) * 0.20, 2);
                $estimate = ['pages'=>null,'tokens_in'=>$tokens_in,'tokens_out'=>$tokens_out,'total'=>$tokens_total,'cost'=>$cost_est];
            }
        }
        $this->view('analysis/options', ['title' => 'Gerar Resumo/Análise','upload_id'=>$uploadId,'estimate'=>$estimate]);
    }

    public function create()
    {
        if (empty($_SESSION['user'])) { header('Location: ' . BASE_URL . 'auth/login'); return; }
        $userId = (int)$_SESSION['user']['id'];
        $uploadId = isset($_POST['upload_id']) ? (int)$_POST['upload_id'] : null;
        // Mock de cálculo baseado no tamanho do upload
        $depth = $_POST['depth'] ?? 'Curto';
        $focus = $_POST['focus'] ?? 'Temático';
        $lang = $_POST['lang'] ?? 'pt';
        $cit = isset($_POST['citations']) ? 1 : 0;

        $tokens_in = 6000; // fallback
        if ($uploadId) {
            $stmtU = DB::pdo()->prepare('SELECT CHAR_LENGTH(text_ref) AS len FROM file_uploads WHERE id = :id');
            $stmtU->execute([':id'=>$uploadId]);
            if ($r = $stmtU->fetch()) {
                $tokens_in = (int)ceil(((int)$r['len']) / 4);
            }
        }
        $tokens_out = ($depth === 'Extenso' ? 3000 : ($depth === 'Médio' ? 1500 : 800));
        $tokens_total = $tokens_in + $tokens_out;
        $cost_est = round(($tokens_total/1000) * 0.20, 2); // custo interno simulado

        $pdo = DB::pdo();
        // Política: 1 análise gratuita por usuário; após isso, cobra R$1,00 por execução (PAYG) do saldo users.balance
        $price_user = 0.00;
        $pdo->beginTransaction();
        try {
            $uRow = $pdo->prepare('SELECT uso_gratuito_usado, balance FROM users WHERE id = :id FOR UPDATE');
            $uRow->execute([':id'=>$userId]);
            $u = $uRow->fetch();
            if (!$u) { throw new \Exception('Usuário não encontrado'); }
            if ((int)$u['uso_gratuito_usado'] === 0) {
                // consume free
                $pdo->prepare('UPDATE users SET uso_gratuito_usado = 1 WHERE id = :id')->execute([':id'=>$userId]);
                $price_user = 0.00;
            } else {
                $price_user = 1.00;
                if ((float)$u['balance'] < $price_user) {
                    $pdo->rollBack();
                    header('Location: ' . BASE_URL . 'dashboard/plans?error=saldo');
                    return;
                }
                $pdo->prepare('UPDATE users SET balance = balance - :p WHERE id = :id')->execute([':p'=>$price_user, ':id'=>$userId]);
            }

            $stmt = $pdo->prepare("INSERT INTO analyses (upload_id,user_id,prompt_used,model_version,tokens_in,tokens_out,tokens_total,cost_estimated,status,result_json,pdf_url) VALUES (:uidl, :usr, :prompt, 'gpt-5', :tin, :tout, :tt, :cost, 'completed', :json, NULL)");
            $json = json_encode([
                'resumo' => 'Resumo simulado...',
                'pontos_principais' => ['ponto A','ponto B'],
                'analise_critica' => 'Análise crítica simulada...',
                'referencias' => ['Ref 1','Ref 2'],
                'pdf_url' => null,
                'verificacao_ok' => true,
                'observacoes_verificacao' => [],
                'preco_usuario' => $price_user
            ], JSON_UNESCAPED_UNICODE);
            $stmt->execute([
                ':uidl' => $uploadId,
                ':usr' => $userId,
                ':prompt' => "depth=$depth;focus=$focus;lang=$lang;citations=$cit",
                ':tin' => $tokens_in,
                ':tout' => $tokens_out,
                ':tt' => $tokens_total,
                ':cost' => $cost_est,
                ':json' => $json,
            ]);
            $id = (int)$pdo->lastInsertId();
            $pdo->commit();
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            http_response_code(500);
            echo 'Erro ao criar análise: ' . $e->getMessage();
            return;
        }

        header('Location: ' . BASE_URL . 'analysis/result?id=' . $id);
        exit;
    }

    public function result()
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $data = null;
        if ($id > 0) {
            $stmt = DB::pdo()->prepare('SELECT * FROM analyses WHERE id = :id');
            $stmt->execute([':id' => $id]);
            $data = $stmt->fetch();
        }
        $this->view('analysis/result', ['title' => 'Resultado da Análise', 'analysis' => $data]);
    }

    public function pdf()
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $stmt = DB::pdo()->prepare('SELECT * FROM analyses WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        $title = 'ReadWise AI - Resultado';
        $body = 'PDF de exemplo (mock).';
        if ($row && !empty($row['result_json'])) {
            $j = json_decode($row['result_json'], true);
            if (!empty($j['resumo'])) { $body = substr($j['resumo'], 0, 200); }
        }

        // Constroi PDF mínimo (1 página, Helvetica)
        $w = 595; $h = 842; // A4 points
        $content = "BT /F1 18 Tf 50 800 Td (".self::pdfEscape($title).") Tj ET\n";
        $content .= "BT /F1 12 Tf 50 770 Td (".self::pdfEscape($body).") Tj ET\n";
        $objects = [];
        $objects[] = "1 0 obj <</Type /Catalog /Pages 2 0 R>> endobj\n";
        $objects[] = "2 0 obj <</Type /Pages /Kids [3 0 R] /Count 1>> endobj\n";
        $objects[] = "3 0 obj <</Type /Page /Parent 2 0 R /MediaBox [0 0 $w $h] /Resources <</Font <</F1 5 0 R>>>> /Contents 4 0 R>> endobj\n";
        $stream = "4 0 obj <</Length ".strlen($content).">> stream\n".$content."endstream endobj\n";
        $objects[] = $stream;
        $objects[] = "5 0 obj <</Type /Font /Subtype /Type1 /BaseFont /Helvetica>> endobj\n";

        $pdf = "%PDF-1.4\n";
        $offsets = [0];
        foreach ($objects as $obj) {
            $offsets[] = strlen($pdf);
            $pdf .= $obj;
        }
        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n0 ".(count($objects)+1)."\n";
        $pdf .= "0000000000 65535 f \n";
        for ($i=1;$i<=count($objects);$i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }
        $pdf .= "trailer <</Size ".(count($objects)+1)."/Root 1 0 R>>\nstartxref\n".$xrefOffset."\n%%EOF";

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="resultado-'.$id.'.pdf"');
        header('Content-Length: '.strlen($pdf));
        echo $pdf;
        exit;
    }

    private static function pdfEscape($s){
        $s = str_replace(["\\", "(", ")", "\r", "\n"],["\\\\","\\(","\\)",""," "],$s);
        return $s;
    }
}
