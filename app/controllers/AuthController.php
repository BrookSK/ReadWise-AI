<?php
namespace App\Controllers;

use Core\Controller;
use Core\DB;

class AuthController extends Controller
{
    public function register()
    {
        $this->view('auth/register', [ 'title' => 'Cadastro' ]);
    }

    public function login()
    {
        $this->view('auth/login', [ 'title' => 'Login' ]);
    }

    public function doRegister()
    {
        $nome = trim($_POST['nome'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));
        $telefone = trim($_POST['telefone'] ?? '');
        $universidade = trim($_POST['universidade'] ?? '');
        $curso = trim($_POST['curso'] ?? '');
        $senha = $_POST['senha'] ?? '';
        if ($nome === '' || $email === '' || strlen($senha) < 6) {
            http_response_code(400); echo 'Dados inválidos'; return;
        }
        $pdo = DB::pdo();
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :e');
        $stmt->execute([':e'=>$email]);
        if ($stmt->fetch()) { http_response_code(400); echo 'E-mail já cadastrado'; return; }
        $hash = password_hash($senha, PASSWORD_BCRYPT);
        $ins = $pdo->prepare('INSERT INTO users (nome,email,telefone,universidade,curso,password_hash,plano_atual,uso_gratuito_usado) VALUES (:n,:e,:t,:u,:c,:h,\'gratuito\',0)');
        $ins->execute([':n'=>$nome,':e'=>$email,':t'=>$telefone,':u'=>$universidade,':c'=>$curso,':h'=>$hash]);
        $uid = (int)$pdo->lastInsertId();
        $_SESSION['user'] = ['id'=>$uid,'nome'=>$nome,'email'=>$email,'plano'=>'gratuito'];
        header('Location: ' . BASE_URL . 'dashboard');
    }

    public function doLogin()
    {
        $email = strtolower(trim($_POST['email'] ?? ''));
        $senha = $_POST['senha'] ?? '';
        $stmt = DB::pdo()->prepare('SELECT * FROM users WHERE email = :e');
        $stmt->execute([':e'=>$email]);
        $u = $stmt->fetch();
        if (!$u || !password_verify($senha, $u['password_hash'])) {
            http_response_code(401);
            $this->view('auth/login', [ 'title' => 'Login', 'error' => 'E-mail ou senha inválidos' ]);
            return;
        }
        $_SESSION['user'] = [
            'id'=>(int)$u['id'],
            'nome'=>$u['nome'],
            'email'=>$u['email'],
            'plano'=>$u['plano_atual'],
            'is_admin'=>(int)($u['is_admin'] ?? 0)
        ];
        if (!empty($_SESSION['user']['is_admin'])) {
            header('Location: ' . BASE_URL . 'admin');
        } else {
            header('Location: ' . BASE_URL . 'dashboard');
        }
    }

    public function logout()
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
        header('Location: ' . BASE_URL);
    }
}
