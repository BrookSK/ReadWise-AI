<?php
namespace App\Controllers;

use Core\Controller;
use Core\DB;

class AdminController extends Controller
{
    public function index()
    {
        $this->guard();
        $this->view('admin/index', ['title' => 'Dashboard Admin']);
    }

    public function settings()
    {
        $this->guard();
        $pdo = DB::pdo();
        $get = $pdo->prepare('SELECT `key`,`value` FROM system_settings WHERE `key` IN ("asaas_api_key","asaas_env","chatgpt_api_key")');
        $get->execute();
        $settings = [];
        foreach ($get->fetchAll() as $r) { $settings[$r['key']] = $r['value']; }
        $this->view('admin/settings', ['title' => 'Configurações do Sistema','settings'=>$settings]);
    }

    public function saveSettings()
    {
        $this->guard();
        $keys = ['asaas_api_key','asaas_env','chatgpt_api_key'];
        $pdo = DB::pdo();
        foreach ($keys as $k) {
            $v = $_POST[$k] ?? null;
            if ($v === null) continue;
            $up = $pdo->prepare('INSERT INTO system_settings(`key`,`value`) VALUES(:k,:v) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)');
            $up->execute([':k'=>$k, ':v'=>$v]);
        }
        header('Location: ' . BASE_URL . 'admin/settings?success=1');
    }

    private function guard()
    {
        if (empty($_SESSION['user'])) { header('Location: ' . BASE_URL . 'auth/login'); exit; }
        $uid = (int)$_SESSION['user']['id'];
        $stmt = DB::pdo()->prepare('SELECT is_admin FROM users WHERE id = :id');
        $stmt->execute([':id'=>$uid]);
        $r = $stmt->fetch();
        if (!$r || (int)$r['is_admin'] !== 1) { http_response_code(403); echo 'Acesso negado'; exit; }
    }
}
