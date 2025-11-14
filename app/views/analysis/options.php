<div class="overlay">
  <div class="modal">
    <div class="modal-header">
      <strong>Gerar Resumo / Análise</strong>
      <a class="btn btn-ghost" style="border-color:#e5e7eb;color:#111827" href="<?php echo BASE_URL; ?>dashboard/documents">✕</a>
    </div>
    <form class="modal-body" method="post" action="<?php echo BASE_URL; ?>analysis/create">
      <input type="hidden" name="upload_id" value="<?php echo isset($upload_id)?(int)$upload_id:0; ?>">
      <div class="grid-2">
        <div>
          <div class="form-row">
            <label>Nível de profundidade</label>
            <select class="select" name="depth">
              <option value="Curto">Curto</option>
              <option value="Médio">Médio</option>
              <option value="Extenso">Extenso</option>
            </select>
          </div>
          <div class="form-row">
            <label>Foco</label>
            <select class="select" name="focus">
              <option value="Temático">Temático</option>
              <option value="Teórico">Teórico</option>
              <option value="Crítica">Crítica</option>
            </select>
          </div>
          <div class="form-row">
            <label>Idioma</label>
            <select class="select" name="lang">
              <option value="pt">Português</option>
              <option value="en">Inglês</option>
            </select>
          </div>
          <div class="form-row" style="display:flex;align-items:center;gap:10px">
            <input id="cit" name="citations" type="checkbox" checked>
            <label for="cit">Incluir citações literais</label>
          </div>
        </div>
        <div>
          <div class="qcard">
            <div style="font-weight:700;margin-bottom:6px">Estimativa de custo</div>
            <table class="table">
              <tr><td class="muted">Tokens entrada (aprox.)</td><td style="text-align:right"><?php echo !empty($estimate)?number_format($estimate['tokens_in']):'—'; ?></td></tr>
              <tr><td class="muted">Tokens saída (aprox.)</td><td style="text-align:right">1.000</td></tr>
              <tr><td class="muted">Total de tokens</td><td style="text-align:right"><?php echo !empty($estimate)?number_format($estimate['total']):'—'; ?></td></tr>
              <tr><td class="muted">Custo interno</td><td style="text-align:right">R$ <?php echo !empty($estimate)?number_format($estimate['cost'],2,',','.'): '—'; ?></td></tr>
              <tr><td class="muted">Preço ao usuário</td><td style="text-align:right"><strong>R$ 0,00 (1ª análise)</strong> depois R$ 1,00/execução</td></tr>
            </table>
            <div class="muted" style="margin-top:8px">Antes de gerar: "Estimativa de custo: R$ 0,XX — confirmar geração?"</div>
          </div>
        </div>
      </div>

      <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:14px">
        <a class="btn btn-ghost" style="border-color:#e5e7eb;color:#111827" href="<?php echo BASE_URL; ?>dashboard/documents">Cancelar</a>
        <button class="btn btn-secondary" type="submit">Confirmar e Gerar</button>
      </div>
    </form>
  </div>
</div>
