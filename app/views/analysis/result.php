<?php $res = null; if (!empty($analysis) && !empty($analysis['result_json'])) { $res = json_decode($analysis['result_json'], true); } $aid = !empty($analysis['id']) ? (int)$analysis['id'] : 0; $qpref = $res && !empty($res['pontos_principais'][0]) ? $res['pontos_principais'][0] : 'modernidade'; ?>
<div class="container" style="max-width:980px">
  <div class="topbar">
    <a class="btn btn-ghost" style="color:#1e3a8a;border-color:#e5e7eb" href="<?php echo BASE_URL; ?>dashboard/documents">Voltar</a>
    <div style="display:flex;gap:8px">
      <a class="btn btn-ghost" style="color:#1e3a8a;border-color:#e5e7eb" href="<?php echo BASE_URL . 'search?q=' . urlencode($qpref); ?>">Pesquisa Avançada</a>
      <a class="btn btn-secondary" href="<?php echo BASE_URL . 'analysis/pdf?id=' . $aid; ?>">Baixar PDF</a>
    </div>
  </div>

  <h1 class="h1" style="font-size:30px">Resultado da Análise</h1>
  <div class="meta">Documento: Exemplo.pdf • Modelo: <?php echo !empty($analysis['model_version']) ? htmlspecialchars($analysis['model_version']) : 'GPT-5'; ?> • Verificação: <?php echo ($res && isset($res['verificacao_ok']) && $res['verificacao_ok']) ? 'OK' : 'A verificar'; ?></div>
  <hr class="sep">

  <section style="margin-top:10px">
    <h2 class="h2">Resumo</h2>
    <p class="lead"><?php echo $res && !empty($res['resumo']) ? htmlspecialchars($res['resumo']) : 'Este é um exemplo de texto de resumo. O sistema apresentará um resumo objetivo com até X palavras, mantendo rigor e citando trechos quando apropriado ("trecho literal", p. 12).'; ?></p>
  </section>

  <section style="margin-top:10px">
    <h2 class="h2">Principais Pontos</h2>
    <ul class="list">
      <?php if ($res && !empty($res['pontos_principais']) && is_array($res['pontos_principais'])): foreach($res['pontos_principais'] as $p): ?>
        <li><?php echo htmlspecialchars($p); ?></li>
      <?php endforeach; else: ?>
        <li>Tese central e hipótese do autor</li>
        <li>Metodologia e corpus</li>
        <li>Conclusões e implicações</li>
      <?php endif; ?>
    </ul>
  </section>

  <section style="margin-top:10px">
    <h2 class="h2">Análise Crítica</h2>
    <p><?php echo $res && !empty($res['analise_critica']) ? nl2br(htmlspecialchars($res['analise_critica'])) : 'Discussão densa relacionando com Foucault, Bourdieu, Butler, Arendt, Mbembe, Beauvoir, Fraser, Said, quando pertinente. Citações literais entre aspas e referência de página. Quando não for possível confirmar, declarar: "não é possível confirmar".'; ?></p>
  </section>

  <section style="margin-top:10px">
    <h2 class="h2">Obras Relacionadas</h2>
    <ul class="list">
      <?php if ($res && !empty($res['referencias']) && is_array($res['referencias'])): foreach($res['referencias'] as $r): ?>
        <li><?php echo htmlspecialchars($r); ?></li>
      <?php endforeach; else: ?>
        <li>Referência 1 — DOI/ISBN (link verificável)</li>
        <li>Referência 2 — DOI/ISBN (link verificável)</li>
      <?php endif; ?>
    </ul>
  </section>

  <section style="margin-top:10px">
    <h2 class="h2">Checklist de Verificação</h2>
    <ul class="list">
      <li>Afirmações verificáveis com fonte? ✔</li>
      <li>Citações literais entre aspas com página? ✔</li>
      <li>Trechos não confirmáveis marcados? ✔</li>
    </ul>
  </section>
</div>
