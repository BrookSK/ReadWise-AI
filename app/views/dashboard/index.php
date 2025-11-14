<div class="container">
  <div class="topbar">
    <a class="logo" href="<?php echo BASE_URL; ?>" style="color:#1e3a8a"><span class="logo-badge">📘</span><span>ReadWise AI</span></a>
    <div style="display:flex;align-items:center;gap:12px">
      <span class="chip">Gratuito</span>
      <a href="<?php echo BASE_URL; ?>account" title="Minha Conta" style="display:inline-block">
        <?php $av = $_SESSION['user']['avatar_url'] ?? null; if ($av): ?>
          <img src="<?php echo htmlspecialchars($av); ?>" alt="avatar" class="avatar" style="object-fit:cover;width:32px;height:32px;border-radius:999px;border:1px solid #e5e7eb" />
        <?php else: ?>
          <div class="avatar"></div>
        <?php endif; ?>
      </a>
    </div>
  </div>
  <hr class="sep">

  <h2 class="h2">Olá, Lucas!</h2>
  <div class="meta">UNIP • Filosofia</div>

  <div class="alert" style="margin:14px 0">✨ Você tem 1 análise gratuita disponível! Faça upload de um documento e experimente a análise crítica verificável.</div>

  <h3 class="h2" style="font-size:20px">Ações Rápidas</h3>
  <div class="quick-grid" style="margin-top:10px">
    <a class="qcard" href="<?php echo BASE_URL; ?>dashboard/upload" style="text-decoration:none;color:inherit"><strong>Novo Upload</strong><br><span class="meta">Envie um novo documento</span></a>
    <a class="qcard" href="<?php echo BASE_URL; ?>dashboard/documents" style="text-decoration:none;color:inherit"><strong>Meus Documentos</strong><br><span class="meta">Veja todos os uploads</span></a>
    <a class="qcard" href="<?php echo BASE_URL; ?>dashboard/history" style="text-decoration:none;color:inherit"><strong>Histórico</strong><br><span class="meta">Análises anteriores</span></a>
    <a class="qcard" href="<?php echo BASE_URL; ?>dashboard/plans" style="text-decoration:none;color:inherit"><strong>Planos</strong><br><span class="meta">Gerencie sua assinatura</span></a>
  </div>

  <div class="empty" style="margin-top:20px">
    <div style="font-size:18px;font-weight:700;margin-bottom:8px">Nenhum documento ainda</div>
    <div class="meta">Comece fazendo upload de um documento acadêmico</div>
    <div style="margin-top:12px"><a class="btn btn-secondary btn-sm" href="<?php echo BASE_URL; ?>dashboard/upload">Fazer Primeiro Upload</a></div>
  </div>
</div>
