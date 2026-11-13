<?php
/* Compatibilidade Task 15: não inferir ambiente para guia sem GNRE007. */
if(!$guide||trim((string)$guide['GNRE001_Tipo_GNRE'])===''||$activeRules)return;
?>
<section class="gnre-panel"><h2>Consulta do rascunho</h2><p class="error">Esta guia não possui snapshot oficial vinculado. O fluxo legado não pode selecionar um ambiente; crie uma nova guia pelo fluxo atual.</p></section>
