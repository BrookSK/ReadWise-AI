<section style="min-height:100vh;background:linear-gradient(180deg,#f8fafc 0,#fff 100%);display:grid;place-items:center;padding:32px">
  <div class="auth-card">
    <div class="auth-header">
      <div class="logo-badge" style="background:#eaf1ff;color:#1e3fae">📘</div>
      <div class="auth-title">Bem-vindo ao ReadWise AI</div>
      <div class="auth-sub">Análises críticas verificáveis para estudantes universitários</div>
      <div class="auth-tabs">
        <a class="tab" href="<?php echo BASE_URL; ?>auth/register">Cadastro</a>
        <a class="tab active" href="#">Login</a>
      </div>
    </div>

    <?php if (!empty($error)): ?>
      <div class="alert" style="margin:10px 0"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form class="auth-form" method="post" action="<?php echo BASE_URL; ?>auth/doLogin">
      <div class="form-row">
        <label>E-mail *</label>
        <input name="email" type="email" placeholder="seu@email.com" required>
      </div>
      <div class="form-row">
        <label>Senha *</label>
        <input name="senha" type="password" placeholder="Sua senha" required>
      </div>
      <button class="btn btn-secondary" type="submit">Entrar</button>
    </form>
  </div>
</section>
