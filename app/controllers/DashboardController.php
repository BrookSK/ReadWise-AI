<?php
namespace App\Controllers;

use Core\Controller;
use Core\DB;

class DashboardController extends Controller
{
    public function index()
    {
        if (empty($_SESSION['user'])) { header('Location: ' . BASE_URL . 'auth/login'); return; }
        $user = $_SESSION['user'];
        $this->view('dashboard/index', ['title' => 'Dashboard','user'=>$user]);
    }

    public function plans()
    {
        if (empty($_SESSION['user'])) { header('Location: ' . BASE_URL . 'auth/login'); return; }
        $user = $_SESSION['user'];
        $this->view('dashboard/plans', ['title' => 'Planos e Créditos','user'=>$user]);
    }

    public function history()
    {
        if (empty($_SESSION['user'])) { header('Location: ' . BASE_URL . 'auth/login'); return; }
        $uid = (int)$_SESSION['user']['id'];
        $stmt = DB::pdo()->prepare('SELECT id, upload_id, status, created_at, cost_estimated, tokens_total FROM analyses WHERE user_id = :u ORDER BY id DESC');
        $stmt->execute([':u'=>$uid]);
        $rows = $stmt->fetchAll();
        $this->view('dashboard/history', ['title' => 'Histórico de Análises','items'=>$rows]);
    }

    public function documents()
    {
        if (empty($_SESSION['user'])) { header('Location: ' . BASE_URL . 'auth/login'); return; }
        $uid = (int)$_SESSION['user']['id'];
        $stmt = DB::pdo()->prepare('SELECT * FROM file_uploads WHERE user_id = :u ORDER BY id DESC');
        $stmt->execute([':u'=>$uid]);
        $uploads = $stmt->fetchAll();
        $this->view('dashboard/documents', ['title' => 'Meus Documentos','uploads'=>$uploads]);
    }

    public function upload()
    {
        if (empty($_SESSION['user'])) { header('Location: ' . BASE_URL . 'auth/login'); return; }
        $this->view('dashboard/upload', ['title' => 'Upload de Documento','user'=>$_SESSION['user']]);
    }
}
