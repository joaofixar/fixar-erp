<?php
namespace hardness;

$resposta = array('code' => true, 'data' => array());

//$resposta['code'] = (
//    (isset($_SESSION['podeMudarUsuarios']) || ($g['usuarioAtual'] == '63'))
//    && (
//        $g['C031']['tipoSistema'] == 'C'
//        || $_SERVER['SERVER_NAME'] != 'xnota.hardness.com.br'
//        || ($_SERVER['SERVER_NAME'] == 'nota.hardness.com.br' && $confUsuario['dbDatabase'] == 'e000')
//    )
//) ? true : false;

$resposta['code'] = gVerificaRoot();

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
