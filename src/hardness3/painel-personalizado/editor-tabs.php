<?php
namespace hardness;

require_once __DIR__ . '/config.php';
$tabs = array();
$tabsList = array();
$painelParametros = '&tab=geral&acaoId=PAINEL%20PERSONALIZAVEL&divReload=' . urlencode($r_divReload);
$painelSqlEmpresas = 'SELECT C004_Id, C004_Nome_Abreviado FROM C004';
if (!empty($r_empresa)) $painelSqlEmpresas .= ' WHERE C004_Id=' . (int) $r_empresa;
$painelSqlEmpresas .= ' ORDER BY C004_Id';
$painelEmpresas = mysql_query($painelSqlEmpresas);
while ($painelEmpresas && ($painelEmpresa = mysql_fetch_assoc($painelEmpresas))) {
    $painelEmpresaId = (int) $painelEmpresa['C004_Id'];
    $painelTab = array(uniqid(), '/sistema/personalizacaoConf/form/alterarPainel/', $painelEmpresa['C004_Nome_Abreviado'] . (is_file(painelPersonalizadoArquivoEditor($painelEmpresaId)) ? ' *' : ''), $painelParametros . '&empresa=' . $painelEmpresaId);
    // A primeira aba é aberta pelo componente nativo: priorizar a empresa da sessão.
    if ($painelEmpresaId === (int) $g['empresaAtual']) {
        array_unshift($tabsList, $painelTab);
    } else {
        $tabsList[] = $painelTab;
    }
}
echo gProcessaTabs($tabsList, $tabs);
$g['smarty']->assign('tabs', $tabs);
$g['smarty']->display('tab.tpl');
