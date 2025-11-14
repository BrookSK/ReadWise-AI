<?php
namespace App\Controllers;

use Core\Controller;
use Core\DB;

class SearchController extends Controller
{
    public function index()
    {
        $q = isset($_GET['q']) ? trim((string)$_GET['q']) : '';
        $results = [];
        if ($q !== '') {
            $pdo = DB::pdo();
            // Busca FULLTEXT com ranking simples
            $stmt = $pdo->prepare("SELECT upload_id, position, chunk_text, MATCH(chunk_text) AGAINST(:q IN NATURAL LANGUAGE MODE) AS score
                                    FROM embedding_chunks
                                    WHERE MATCH(chunk_text) AGAINST(:q IN NATURAL LANGUAGE MODE)
                                    ORDER BY score DESC
                                    LIMIT 20");
            $stmt->execute([':q' => $q]);
            $results = $stmt->fetchAll();
        }
        $this->view('search/index', [
            'title' => 'Pesquisa Avançada',
            'q' => $q,
            'results' => $results,
        ]);
    }
}
