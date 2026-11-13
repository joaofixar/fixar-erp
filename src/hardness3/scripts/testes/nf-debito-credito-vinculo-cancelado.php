<?php
/** Regressão da aba NF Débito/Crédito e vínculos; sem aplicação ou banco real. */
namespace hardness;
if (PHP_SAPI !== 'cli') { exit; }
error_reporting(E_ALL);
set_error_handler(function ($nivel, $mensagem) { throw new \RuntimeException($mensagem); });
$raiz = dirname(__DIR__, 2);
$verificacoes = 0;
function conferir($condicao, $mensagem) {
    global $verificacoes;
    $verificacoes++;
    if (!$condicao) { throw new \RuntimeException($mensagem); }
}
$fonte = file_get_contents($raiz . '/prog/ven/ven003/grid_func-callback.php');
$inicio = strpos($fonte, 'function ven003ExibirVinculoNFDC(');
$fim = strpos($fonte, 'function ValidaCampoMDFe(', $inicio);
conferir($inicio !== false && $fim !== false, 'Callback não localizado');
eval('namespace hardness; ' . substr($fonte, $inicio, $fim - $inicio));
foreach (['Débito', 'Crédito'] as $tipo) {
    $ativa = "NF 0000031 - $tipo - Tipo 08";
    conferir(ven003ExibirVinculoNFDC(['campoAtual'=>$ativa]) === $ativa, 'Nota ativa foi alterada');
    $cancelada = "NF 0000031 (c) - $tipo - Tipo 08";
    $html = ven003ExibirVinculoNFDC(['campoAtual'=>$cancelada]);
    conferir(strpos($html, 'color:var(--BGVermelho, #d00)') !== false, 'Número cancelado não está vermelho');
    conferir(strpos($html, '>0000031 (c)</span>') !== false, 'Marcação não ficou ao lado do número');
    conferir(strip_tags($html) === $cancelada, 'Vínculo/tipo fiscal perdido');
    $mistas = ven003ExibirVinculoNFDC(['campoAtual'=>$cancelada . ' | NF 0000032 - ' . $tipo . ' - Tipo 01']);
    conferir(substr_count($mistas, '<span') === 1, 'Nota ativa vinculada recebeu cor de cancelada');
    conferir(strpos($mistas, ' | NF 0000032 - ') !== false, 'Outra nota vinculada foi removida');
}
conferir(ven003ExibirVinculoNFDC(['todosCampos'=>['NFDebitoCredito'=>'NF 0000031 (c) - Débito - Tipo 08', 'T007_Flag_Cancelada'=>'N']]) !== 'NF 0000031 (c) - Débito - Tipo 08', 'Status da origem prevaleceu sobre o vínculo');
conferir(ven003ExibirVinculoNFDC(['campoAtual'=>'NF 0000031 - Débito - Tipo 08', 'todosCampos'=>['T007_Flag_Cancelada'=>'S']]) === 'NF 0000031 - Débito - Tipo 08', 'Cancelamento da origem contaminou o vínculo');
foreach (['-', 'Atualização de base pendente', ''] as $texto) {
    conferir(ven003ExibirVinculoNFDC(['campoAtual'=>$texto]) === $texto, 'Fallback alterado');
}
conferir(ven003ExibirVinculoNFDC([]) === '-', 'Ausência de dados falhou');
$html = ven003ExibirVinculoNFDC(['campoAtual'=>'<script>alert(1)</script> NF 0000031 (c)']);
conferir(strpos($html, '<script>') === false && strpos($html, '&lt;script&gt;') !== false, 'HTML recebido não foi escapado');

// Executa o case real da nova grade com o framework e a existência da coluna simulados.
$possuiCampo = true;
function mysql_query($sql) {
    conferir($sql === "SHOW COLUMNS FROM T007A LIKE 'T007A_T007_Id_Origem_NF_Debito_Credito'", 'Consulta não prevista no teste');
    return true;
}
function mysql_num_rows($sql) { global $possuiCampo; return $possuiCampo ? 1 : 0; }
function gBotaoAuditoria($tabela) { conferir($tabela === 'T007', 'Auditoria de tabela incorreta'); }
$g = array('empresaAtual'=>123, 'C031'=>[], 'divId'=>'testeGrid', 'divIdRoot'=>'testeRoot',
    'r_'=>'/ven/ven003/grid/ven003GridListaNFDebitoCredito/');
$g['ui'] = new class {
    public $extraOriginal;
    public function gridFiltroExtra($ativo, $filtros, $extra) { $this->extraOriginal = $extra; return $extra; }
    public function gridFiltroPrint($filtros) { return ''; }
    public function grid($colunas, $dados) { return ['thead'=>'', 'tbody'=>'', 'tfoot'=>'']; }
};
$g['sqlAuto'] = new class {
    public function gerarSQLGrid($colunas, $from, $extra) { return ['SELECT simulado', []]; }
};
$g['smarty'] = new class {
    public $valores = [];
    public $template;
    public function assign($nome, $valor) { $this->valores[$nome] = $valor; }
    public function display($template) { $this->template = $template; }
};
$fonteGrid = file_get_contents($raiz . '/prog/ven/ven003/grid.php');
$inicio = strpos($fonteGrid, "case 'ven003GridListaNFDebitoCredito':");
$fim = strpos($fonteGrid, "case 'ven003GridNFDebitoCreditoAutorizada':", $inicio);
conferir($inicio !== false && $fim !== false, 'Case da nova grade não localizado');
$codigoGrid = 'namespace hardness; switch ("ven003GridListaNFDebitoCredito") {' . substr($fonteGrid, $inicio, $fim-$inicio) . '}';
ob_start();
eval($codigoGrid);
$htmlGrade = ob_get_clean();
$colunas = array_column($grid, null, 'colunaTitulo');
foreach (['NF', 'Modelo', 'Série', 'Operação', 'Tipo Débito/Crédito', 'NF ORIGEM', 'Emissão', 'Cliente', 'Total'] as $titulo) {
    conferir(isset($colunas[$titulo]), 'Coluna ausente: ' . $titulo);
}
conferir(strpos($colunas['NF']['colunaValor'], "IF(T007_Flag_Cancelada='S',' (c)','')") !== false, 'Cancelamento da própria NF ausente');
conferir(strpos($numeroOrigemNFDC, "IF(NFOrigemCancelada='S',' (c)','')") !== false, 'Cancelamento da origem ausente');
conferir(strpos($extra, 'LEFT JOIN (') !== false, 'Notas sem origem seriam excluídas');
conferir(strpos($extra, 'NFOrigemEmpresa = T007.T007_C004_Id') !== false, 'Origem sem isolamento de empresa');
conferir(strpos($extra, 'NFOrigemId = T007A.T007A_T007_Id_Origem_NF_Debito_Credito') !== false, 'Vínculo incorreto');
conferir(strpos($extra, "T007_C004_Id = '123'") !== false, 'Listagem sem isolamento de empresa');
conferir(strpos($extra, "T007_Flag_Finalidade_NFe IN ('5','6')") !== false, 'Listagem não restringe débito/crédito');
conferir(strpos($extra, 'T008') === false && strpos($extra, 'T005') === false, 'Pedido/manual foram indevidamente restringidos');
conferir(strpos($extra, 'T007_Numero_Protocolo_Nfe') === false && strpos($extra, "T007_Flag_Cancelada =") === false, 'Canceladas ou não autorizadas foram excluídas');
conferir($g['ui']->extraOriginal === $extra, 'SQL original não passou pelo framework');
conferir($g['smarty']->valores['gridId'] === md5($g['r_']), 'Personalização não usa rota própria');
conferir(isset($g['smarty']->valores['gridPersonalizacao']), 'Personalização não registrada');
conferir($g['smarty']->template === 'grid.tpl', 'Template não é nativo');
conferir(isset($gridFiltro['NFOrigemNumeroBruto']), 'Filtro pela NF origem ausente');
conferir(strpos($htmlGrade, 'acaoClickLinha(') !== false && strpos($htmlGrade, 'data.stopPropagation()') !== false, 'Navegação independente da origem ausente');
$principal = substr($fonteGrid, strpos($fonteGrid, "case 'ven003GridPrincipal':"), strpos($fonteGrid, "case 'ven003GridAnalitico':") - strpos($fonteGrid, "case 'ven003GridPrincipal':"));
conferir(strpos($principal, 'NFDebitoCredito') === false && strpos($principal, 'valorColunaNFDC') === false, 'Coluna antiga continua na principal');
conferir(strpos(file_get_contents($raiz . '/prog/ven/ven003/tab.php'), "'/ven/ven003/content/ven003ContentListaNFDebitoCredito/', 'NF Débito/Crédito'") !== false, 'Aba não registrada');
conferir(strpos(file_get_contents($raiz . '/prog/ven/ven003/content.php'), "'/ven/ven003/grid/ven003GridListaNFDebitoCredito/'") !== false, 'Content não aponta para a grade');

$possuiCampo = false;
ob_start(); eval($codigoGrid); ob_end_clean();
conferir($numeroOrigemNFDC === "'Atualização de base pendente' AS NFOrigemNumero", 'Proteção de base antiga removida');
conferir($idOrigemNFDC === 'NULL AS NFOrigemId' && $joinOrigemNFDC === '', 'Base antiga geraria SQL inválido');
conferir(!isset($gridFiltro['NFOrigemNumeroBruto']), 'Base antiga recebeu filtro inexistente');
foreach ([null, 0, ''] as $id) {
    conferir(ven003ExibirOrigemNFDC(['campoAtual'=>'-', 'todosCampos'=>['NFOrigemId'=>$id]]) === '-', 'Nota sem origem recebeu link');
}
$origem = ven003ExibirOrigemNFDC(['campoAtual'=>'NF 0000029 (c) - Série 15', 'todosCampos'=>['NFOrigemId'=>567, 'T007_Flag_Cancelada'=>'N']]);
conferir(strpos($origem, 'data-nf-origem="567"') !== false && strpos($origem, '>0000029 (c)</span>') !== false, 'Link/cancelamento da origem incorreto');
foreach (['5'=>6, '6'=>8] as $finalidade=>$quantidade) {
    for ($n=1; $n<=$quantidade; $n++) {
        $tipo = sprintf('%02d', $n);
        $texto = ven003ExibirTipoFiscalNFDC(['campoAtual'=>$tipo, 'todosCampos'=>['T007_Flag_Finalidade_NFe'=>$finalidade]]);
        conferir(strpos($texto, $tipo . ' - ') === 0, 'Tipo fiscal sem descrição');
    }
}
conferir(ven003ExibirTipoFiscalNFDC(['campoAtual'=>'06', 'todosCampos'=>['T007_Flag_Finalidade_NFe'=>'6']]) === '06 - Pagamento antecipado', 'Tipo de pedido incorreto');
conferir(ven003ExibirTipoFiscalNFDC(['campoAtual'=>'01', 'todosCampos'=>['T007_Flag_Finalidade_NFe'=>'5']]) === '01 - Multa e juros', 'Crédito 01 confundido com débito 01');
conferir(ven003ExibirTipoFiscalNFDC(['campoAtual'=>'<script>', 'todosCampos'=>[]]) === '&lt;script&gt;', 'Tipo desconhecido sem escape');
conferir(ven003ExibirSituacaoNFDC(['todosCampos'=>['T007_Flag_Cancelada'=>'S', 'T007_Numero_Protocolo_Nfe'=>'123']]) === 'Cancelada', 'Protocolo prevaleceu sobre cancelamento');
conferir(ven003ExibirSituacaoNFDC(['todosCampos'=>[]]) === 'Sem protocolo', 'Estado de rascunho incorreto');
if (in_array('--javascript', $argv, true)) {
    preg_match('~<script[^>]*>(.*?)</script>~s', $htmlGrade, $javascript);
    echo $javascript[1];
    exit;
}
echo "OK: $verificacoes verificações da aba e vínculos; sem banco ou SEFAZ.\n";
