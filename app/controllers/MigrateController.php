<?php
namespace App\Controllers;

use Core\Controller;
use Core\DB;

class MigrateController extends Controller
{
    public function run()
    {
        $pdo = DB::pdo();
        // cria tabela migrations se não existir
        $pdo->exec(
            "CREATE TABLE IF NOT EXISTS migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                filename VARCHAR(191) NOT NULL,
                applied_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_filename (filename(120))
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        $dir = __DIR__ . '/../../database/migrations/';
        if (!is_dir($dir)) { @mkdir($dir, 0775, true); }
        $files = glob($dir . '*.sql');
        sort($files, SORT_STRING);

        $applied = [];
        $stmt = $pdo->query('SELECT filename FROM migrations');
        foreach ($stmt->fetchAll() as $row) { $applied[$row['filename']] = true; }

        $appliedNow = [];
        foreach ($files as $path) {
            $file = basename($path);
            if (isset($applied[$file])) { continue; }
            $sql = file_get_contents($path);
            // executa statements separados por ; ignorando linhas em branco
            $statements = array_filter(array_map('trim', preg_split('/;\s*\n/', $sql)));
            try {
                $pdo->beginTransaction();
                foreach ($statements as $s) {
                    if ($s !== '') { $pdo->exec($s); }
                }
                $ins = $pdo->prepare('INSERT INTO migrations (filename) VALUES (:f)');
                $ins->execute([':f' => $file]);
                $pdo->commit();
                $appliedNow[] = $file;
            } catch (\Throwable $e) {
                $pdo->rollBack();
                http_response_code(500);
                echo 'Erro na migration ' . htmlspecialchars($file) . ': ' . $e->getMessage();
                return;
            }
        }

        header('Content-Type: application/json');
        echo json_encode(['applied' => $appliedNow], JSON_UNESCAPED_UNICODE);
    }
}
