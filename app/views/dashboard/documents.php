<div class="container">
  <div class="topbar">
    <a class="btn btn-ghost" style="color:#1e3a8a;border-color:#e5e7eb" href="<?php echo BASE_URL; ?>dashboard">Dashboard</a>
    <a class="btn btn-secondary btn-sm" href="<?php echo BASE_URL; ?>dashboard/upload">Novo Upload</a>
  </div>
  <h2 class="h2">Meus Documentos</h2>

  <div class="list" style="margin-top:14px">
    <?php if (!empty($uploads)): foreach($uploads as $up): ?>
      <?php 
        $badgeClass = 'status-processing';
        if ($up['status']==='ready') $badgeClass='status-ready';
        if ($up['status']==='error') $badgeClass='status-error';
      ?>
      <div class="list-item">
        <div>
          <strong><?php echo htmlspecialchars($up['filename']); ?></strong>
          <div class="meta">Mime: <?php echo htmlspecialchars($up['mime']); ?> · Tamanho: <?php echo number_format((int)$up['size_bytes']/1024/1024,2); ?> MB</div>
          <div style="margin-top:8px;display:flex;gap:8px">
            <a class="btn btn-secondary btn-sm" href="<?php echo BASE_URL; ?>analysis/options?upload_id=<?php echo (int)$up['id']; ?>">Gerar resumo/análise</a>
            <a class="btn btn-ghost btn-sm" style="border-color:#e5e7eb;color:#1e3a8a" href="<?php echo BASE_URL; ?>search?q=<?php echo urlencode(pathinfo($up['filename'], PATHINFO_FILENAME)); ?>">Pesquisar</a>
          </div>
        </div>
        <div class="status-badge <?php echo $badgeClass; ?>"><?php echo ucfirst($up['status']); ?></div>
      </div>
    <?php endforeach; else: ?>
      <div class="empty">Nenhum documento enviado ainda.</div>
    <?php endif; ?>
  </div>
</div>
