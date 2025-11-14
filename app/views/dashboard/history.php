<div class="container">
  <div class="topbar">
    <a class="btn btn-ghost" style="color:#1e3a8a;border-color:#e5e7eb" href="<?php echo BASE_URL; ?>dashboard">Voltar ao Dashboard</a>
    <div></div>
  </div>
  <h2 class="h2">Histórico de Análises</h2>
  <?php if (empty($items)): ?>
    <div class="empty" style="margin-top:16px">
      <div style="font-size:18px;font-weight:700;margin-bottom:8px">Nenhuma análise ainda</div>
      <div class="meta">Faça upload de um documento para ver o histórico de análises.</div>
      <div style="margin-top:12px"><a class="btn btn-secondary btn-sm" href="<?php echo BASE_URL; ?>dashboard/upload">Fazer Upload</a></div>
    </div>
  <?php else: ?>
    <div class="list" style="margin-top:14px">
      <?php foreach($items as $it): ?>
        <div class="list-item">
          <div>
            <strong>Analise #<?php echo (int)$it['id']; ?></strong>
            <div class="meta">Upload: <?php echo (int)$it['upload_id']; ?> · <?php echo htmlspecialchars($it['created_at']); ?> · Tokens: <?php echo (int)$it['tokens_total']; ?></div>
          </div>
          <div style="display:flex;gap:8px;align-items:center">
            <div class="meta">R$ <?php echo number_format((float)$it['cost_estimated'], 2, ',', '.'); ?></div>
            <a class="btn btn-ghost btn-sm" style="border-color:#e5e7eb;color:#1e3a8a" href="<?php echo BASE_URL; ?>analysis/result?id=<?php echo (int)$it['id']; ?>">Abrir</a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
