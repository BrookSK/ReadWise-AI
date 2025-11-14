<div class="container" style="max-width:980px">
  <div class="topbar">
    <a class="btn btn-ghost" style="color:#1e3a8a;border-color:#e5e7eb" href="<?php echo BASE_URL; ?>analysis/result">Voltar ao Resultado</a>
    <div></div>
  </div>

  <h1 class="h1" style="font-size:28px">Pesquisa Avançada</h1>
  <div class="meta">Busque no conteúdo do upload e nas análises geradas <?php echo !empty($q) ? '• termo atual: "'.htmlspecialchars($q).'"' : ''; ?></div>

  <div class="searchbar" style="margin-top:12px">
    <form action="<?php echo BASE_URL; ?>search" method="get" style="display:flex;gap:10px;flex:1">
      <input name="q" type="text" value="<?php echo htmlspecialchars($q ?? ''); ?>" placeholder="Busque por termos, conceitos, autores...">
      <button class="btn btn-secondary" type="submit">Pesquisar</button>
    </form>
  </div>

  <div class="filters">
    <div class="filter"><input id="f1" type="radio" name="fonte" checked><label for="f1">Upload</label></div>
    <div class="filter"><input id="f2" type="radio" name="fonte"><label for="f2">Análise</label></div>
    <div class="filter"><input id="f3" type="radio" name="fonte"><label for="f3">Ambos</label></div>
    <div class="filter"><label>Proximidade semântica</label><select style="border:none"><option>Padrão</option><option>Alta</option><option>Baixa</option></select></div>
  </div>

  <div class="results">
    <?php if (!empty($results)): foreach($results as $r): ?>
      <div class="result">
        <div><strong>Upload #<?php echo (int)$r['upload_id']; ?> — trecho (pos <?php echo (int)$r['position']; ?>)</strong></div>
        <div class="snippet"><?php echo htmlspecialchars(mb_strimwidth($r['chunk_text'],0,320,'...')); ?></div>
        <div class="meta" style="margin-top:6px">Score: <?php echo number_format((float)$r['score'], 2); ?></div>
      </div>
    <?php endforeach; else: ?>
      <div class="result"><div class="meta">Nenhum resultado encontrado. Tente outros termos.</div></div>
    <?php endif; ?>
  </div>
</div>
