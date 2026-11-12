<?php
namespace hardness;
set_time_limit(60000);

require_once(__DIR__."/diversos/Validacao.php");
$validacao = new Validacao();

if ($validacao->validarAcesso()) {
  $moduloIntegracao  = $g['r'][3];
  $recursoIntegracao = $g['r'][4];
  $instrucaoRequire  = "{$moduloIntegracao}/{$recursoIntegracao}.php";

  ///require_once($instrucaoRequire);

  if (@include_once($instrucaoRequire)) {
    $strClasseIntegracao = "hardness\\".$recursoIntegracao;
    $objClasseIntegracao = new $strClasseIntegracao();
  
    header("Content-Type: application/json; charset=UTF-8");
    echo $objClasseIntegracao->integrar();
  } else {
    return http_response_code(400);
  }
} else {
  return http_response_code(403);
  die();
}