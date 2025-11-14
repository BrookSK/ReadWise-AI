<div class="container">
  <div class="topbar">
    <a class="btn btn-ghost" style="color:#1e3a8a;border-color:#e5e7eb" href="<?php echo BASE_URL; ?>admin">Voltar</a>
    <div></div>
  </div>

  <h2 class="h2">Configurações do Sistema</h2>

  <?php if (!empty($_GET['success'])): ?>
    <div class="alert" style="margin-top:10px">Configurações salvas com sucesso.</div>
  <?php endif; ?>

  <form class="qcard" style="margin-top:12px" method="post" action="<?php echo BASE_URL; ?>admin/saveSettings">
    <h3 class="h2" style="font-size:18px">Integrações</h3>
    <div class="form-row" style="margin-top:10px">
      <label>API Key do ChatGPT (OpenAI)</label>
      <input type="password" name="chatgpt_api_key" value="<?php echo htmlspecialchars($settings['chatgpt_api_key'] ?? ''); ?>" placeholder="********************************">
    </div>
    <div class="form-row">
      <label>Asaas - Ambiente (sandbox|production)</label>
      <input type="text" name="asaas_env" value="<?php echo htmlspecialchars($settings['asaas_env'] ?? 'sandbox'); ?>">
    </div>
    <div class="form-row">
      <label>Asaas - API Key</label>
      <input type="password" name="asaas_api_key" value="<?php echo htmlspecialchars($settings["asaas_api_key"] ?? ''); ?>" placeholder="********************************">
    </div>
    <div class="form-row">
      <label>Webhook de Upload (recebe base64)</label>
      <input type="url" name="webhook_upload_url" value="<?php echo htmlspecialchars($settings['webhook_upload_url'] ?? ''); ?>" placeholder="https://exemplo.com/webhook">
      <div class="meta">Se vazio, o webhook não será chamado.</div>
    </div>
    <button class="btn btn-secondary btn-sm" type="submit" style="margin-top:12px">Salvar Configurações</button>
  </form>

  <div class="qcard" style="margin-top:12px">
    <h3 class="h2" style="font-size:18px">Informações Importantes</h3>
    <ul class="list" style="margin-top:6px">
      <li>A API Key do ChatGPT é armazenada no banco via tabela system_settings</li>
      <li>As análises consomem tokens da sua conta OpenAI</li>
      <li>Recomendamos monitorar o uso da API no dashboard da OpenAI</li>
      <li>Alterações nas configurações entram em vigor imediatamente</li>
    </ul>
  </div>
</div>
