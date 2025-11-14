<section style="min-height:100vh;background:linear-gradient(180deg,#f8fafc 0,#fff 100%);display:grid;place-items:center;padding:32px">
  <div class="auth-card">
    <div class="auth-header">
      <div class="logo-badge" style="background:#eaf1ff;color:#1e3fae">📘</div>
      <div class="auth-title">Bem-vindo ao ReadWise AI</div>
      <div class="auth-sub">Análises críticas verificáveis para estudantes universitários</div>
      <div class="auth-tabs">
        <a class="tab active" href="#">Cadastro</a>
        <a class="tab" href="<?php echo BASE_URL; ?>auth/login">Login</a>
      </div>
    </div>

    <form class="auth-form" method="post" action="<?php echo BASE_URL; ?>auth/doRegister">
      <div class="form-row">
        <label>Nome Completo *</label>
        <input name="nome" type="text" placeholder="Seu nome completo" required>
      </div>
      <div class="form-row">
        <label>E-mail *</label>
        <input name="email" type="email" placeholder="seu@email.com" required>
      </div>
      <div class="form-row">
        <label>Telefone (WhatsApp)</label>
        <input name="telefone" type="text" placeholder="(00) 00000-0000">
      </div>
      <div class="form-row">
        <label>Universidade *</label>
        <input name="universidade" type="text" placeholder="Ex: USP, UNICAMP, UFRJ..." required>
      </div>
      <div class="form-row">
        <label>Curso *</label>
        <input name="curso" type="text" placeholder="Ex: Ciências Sociais, Filosofia..." required>
      </div>
      <div class="form-row">
        <label>Senha *</label>
        <input name="senha" type="password" placeholder="Mínimo 6 caracteres" required>
      </div>
      <button class="btn btn-secondary" type="submit">Criar Conta Gratuita</button>
      <div class="auth-note">Ao criar uma conta, você ganha 1 análise gratuita</div>
    </form>
  </div>
</section>
