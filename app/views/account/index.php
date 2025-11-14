<div class="container" style="max-width:780px">
  <div class="topbar">
    <div class="h2">Minha Conta</div>
    <a class="btn btn-ghost" style="color:#1e3a8a;border-color:#e5e7eb" href="<?php echo BASE_URL; ?>dashboard">Voltar ao Dashboard</a>
  </div>

  <div class="qcard" style="margin-top:8px">
    <div class="h2" style="font-size:18px;margin-bottom:10px">Informações Pessoais</div>

    <div style="display:flex;gap:20px;align-items:flex-start;flex-wrap:wrap">
      <div style="min-width:120px;text-align:center">
        <?php $av = $u['avatar_url'] ?? null; if ($av): ?>
          <img src="<?php echo htmlspecialchars($av); ?>" alt="avatar" style="width:96px;height:96px;border-radius:999px;object-fit:cover;border:1px solid #e5e7eb"/>
        <?php else: ?>
          <div style="width:96px;height:96px;border-radius:999px;background:#e5edff;border:1px solid #e5e7eb"></div>
        <?php endif; ?>
        <form method="post" action="<?php echo BASE_URL; ?>account/avatar" enctype="multipart/form-data" style="margin-top:8px">
          <input type="file" name="avatar" accept="image/png,image/jpeg,image/webp" required>
          <button class="btn btn-secondary btn-sm" type="submit" style="margin-top:6px">Enviar Foto</button>
        </form>
      </div>

      <form method="post" action="<?php echo BASE_URL; ?>account/update" style="flex:1;min-width:280px;display:grid;gap:10px">
        <div class="form-row">
          <label>Email</label>
          <input type="email" value="<?php echo htmlspecialchars($u['email']); ?>" disabled>
          <div class="meta">O email não pode ser alterado</div>
        </div>
        <div class="form-row">
          <label>Nome Completo</label>
          <input name="nome" type="text" value="<?php echo htmlspecialchars($u['nome']); ?>">
        </div>
        <div class="form-row">
          <label>Telefone</label>
          <input name="telefone" type="text" value="<?php echo htmlspecialchars($u['telefone'] ?? ''); ?>">
        </div>
        <div class="form-row">
          <label>Universidade</label>
          <input name="universidade" type="text" value="<?php echo htmlspecialchars($u['universidade'] ?? ''); ?>">
        </div>
        <div class="form-row">
          <label>Curso</label>
          <input name="curso" type="text" value="<?php echo htmlspecialchars($u['curso'] ?? ''); ?>">
        </div>
        <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:4px">
          <a class="btn btn-ghost btn-sm" style="border-color:#e5e7eb;color:#111827" href="<?php echo BASE_URL; ?>auth/logout">Sair</a>
          <button class="btn btn-secondary" type="submit">Salvar Alterações</button>
        </div>
      </form>
    </div>
  </div>
</div>
