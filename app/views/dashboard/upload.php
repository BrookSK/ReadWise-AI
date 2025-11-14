<div class="container">
  <div class="topbar">
    <a class="btn btn-ghost" style="color:#1e3a8a;border-color:#e5e7eb" href="<?php echo BASE_URL; ?>dashboard">Voltar ao Dashboard</a>
    <div></div>
  </div>
  <h2 class="h2">Upload de Documento</h2>

  <?php if (!empty($_GET['error'])): $e = $_GET['error']; $msg = 'Arquivo inválido.'; if($e==='ext') $msg='Extensão não permitida. Use PDF/EPUB/DOCX/TXT.'; if($e==='size') $msg='Arquivo excede 10MB.'; if($e==='invalid') $msg='Nenhum arquivo enviado ou upload falhou.'; ?>
    <div class="alert" style="margin-top:10px"><?php echo htmlspecialchars($msg); ?></div>
  <?php endif; ?>

  <form class="dropzone" style="margin-top:14px" method="post" action="<?php echo BASE_URL; ?>upload/store" enctype="multipart/form-data">
    <div>
      <div style="font-size:42px;text-align:center">⬆️</div>
      <div class="center" style="margin-top:8px">Arraste seu PDF aqui<br><span class="meta">ou selecione abaixo</span></div>
      <div class="center" style="margin-top:12px">
        <input type="file" name="file" accept=".pdf,.epub,.docx,.txt" style="display:block;margin:0 auto 10px auto">
        <button class="btn btn-secondary btn-sm" type="submit">Enviar</button>
      </div>
      <div class="center meta" style="margin-top:6px">PDF/EPUB/DOCX/TXT • Máximo 10MB</div>
    </div>
  </form>

  <div class="steps" style="margin-top:14px">
    <h3 class="h2" style="font-size:18px;margin:0 0 6px">Como funciona?</h3>
    <ol>
      <li>Selecione ou arraste um arquivo PDF (máximo 10MB)</li>
      <li>O sistema extrairá o texto do documento</li>
      <li>A IA analisará o conteúdo</li>
      <li>Você receberá um resumo e análise crítica completa</li>
      <li>O resultado ficará disponível em "Meus Documentos"</li>
    </ol>
  </div>
</div>
