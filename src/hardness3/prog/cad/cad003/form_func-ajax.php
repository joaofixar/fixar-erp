<?php
namespace hardness;

if (isset($g['r'][3]) && in_array($g['r'][3], array('portalRepresentanteLiberar', 'portalRepresentanteRevogar'), true)) {
    require_once __DIR__ . '/portal-acesso.php';
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    try {
        $resultadoPortal = cad003PortalProcessar($g, $confUsuario, $g['r'][3], $_POST, isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '');
        echo json_encode($resultadoPortal, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    } catch (RepresentantePortalErro $erroPortal) {
        http_response_code($erroPortal->status); echo json_encode(array('error' => $erroPortal->getMessage()), JSON_UNESCAPED_UNICODE);
    } catch (\Throwable $erroPortal) {
        http_response_code(503); echo json_encode(array('error' => 'Não foi possível concluir a solicitação. Tente novamente mais tarde.'), JSON_UNESCAPED_UNICODE);
    }
    exit;
}

$resposta = array('code' => true, 'data' => array());
/**
 * Ações
 */
switch($g['r'][3]) {
	case 'alteraEstadoRepresentantes':
		$D021_D018_Id = isset($r_D021_D018_Id) ? $r_D021_D018_Id : false;
		$resposta['code'] = true;
		$resposta['data']['D021_D020_Id'] = gProcessaSelect(gGeraSelect('D020', 'D020_Id', 'D020_Nome_Cidade', 'WHERE D020_D018_Id = '.$r_D021_D018_Id.' ORDER BY D020_Nome_Cidade'));
	break;	
}
echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";

?>
