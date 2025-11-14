<?php
namespace App\Controllers;

use Core\Controller;
use Core\DB;

class AccountController extends Controller
{
    public function index()
    {
        if (empty($_SESSION['user'])) { header('Location: ' . BASE_URL . 'auth/login'); return; }
        $uid = (int)$_SESSION['user']['id'];
        $stmt = DB::pdo()->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->execute([':id'=>$uid]);
        $user = $stmt->fetch();
        $this->view('account/index', ['title'=>'Minha Conta','u'=>$user]);
    }

    public function update()
    {
        if (empty($_SESSION['user'])) { header('Location: ' . BASE_URL . 'auth/login'); return; }
        $uid = (int)$_SESSION['user']['id'];
        $nome = trim($_POST['nome'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');
        $universidade = trim($_POST['universidade'] ?? '');
        $curso = trim($_POST['curso'] ?? '');

        $stmt = DB::pdo()->prepare('UPDATE users SET nome=:n, telefone=:t, universidade=:u, curso=:c WHERE id=:id');
        $stmt->execute([':n'=>$nome, ':t'=>$telefone, ':u'=>$universidade, ':c'=>$curso, ':id'=>$uid]);
        $_SESSION['user']['nome'] = $nome;
        header('Location: ' . BASE_URL . 'account');
    }

    public function avatar()
    {
        if (empty($_SESSION['user'])) { header('Location: ' . BASE_URL . 'auth/login'); return; }
        if (empty($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) { header('Location: ' . BASE_URL . 'account?error=avatar'); return; }
        $uid = (int)$_SESSION['user']['id'];
        $f = $_FILES['avatar'];
        $mime = mime_content_type($f['tmp_name']);
        $allowed = ['image/jpeg'=>'.jpg','image/png'=>'.png','image/webp'=>'.webp'];
        if (!isset($allowed[$mime])) { header('Location: ' . BASE_URL . 'account?error=avatar'); return; }
        $dir = __DIR__ . '/../../storage/avatars/';
        if (!is_dir($dir)) { @mkdir($dir, 0775, true); }
        $name = 'u'.$uid.'_'.time().$allowed[$mime];
        $dest = $dir . $name;
        if (!move_uploaded_file($f['tmp_name'], $dest)) { header('Location: ' . BASE_URL . 'account?error=avatar'); return; }
        $url = 'storage/avatars/'.$name; // relative to project root
        $stmt = DB::pdo()->prepare('UPDATE users SET avatar_url=:a WHERE id=:id');
        $stmt->execute([':a'=>$url, ':id'=>$uid]);
        $_SESSION['user']['avatar_url'] = $url;
        header('Location: ' . BASE_URL . 'account');
    }
}
