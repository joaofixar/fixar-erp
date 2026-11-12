<?php
namespace hardness;
set_time_limit(60000);
require_once(__DIR__ . "/../base/BaseIntegracao.php");

class Pdv extends BaseIntegracao {
  public function integrar() {
    $method = $_SERVER['REQUEST_METHOD'];

    switch ($method) {
      case 'POST':
        return $this->postData();
        break;
      case 'GET':
        return $this->getData();
        break;
      default:
      return http_response_code(400);
        break;
    }
  }

  public function getData() {
    $param = array("pdv");
    $validate = $this->validateParams($param);

    if ($validate) {
      $pdv = $this->paramFromGet("pdv");

      $sql =  "SELECT c040.C040_Id                AS ID_INTEGRACAO,
                      c040.C040_Ambiente          AS AMBIENTE,
                      c040.C040_Versao_NFCE       AS VERSAO,
                      c040.C040_Lote_NFCE         AS LOTE_NFCE,
                      c040.C040_Numero_NFCE       AS NUMERO_NFCE,
                      c040.C040_Serie_NFCE        AS SERIE,
                      c040.C040_Impressora_Nome   AS IMPRESSORA_NOME,
                      c040.C040_Impressora_Porta  AS IMPRESSORA_PORTA,
                      c040.C040_Impressora_Modelo AS IMPRESSORA_MODELO,
                      CAST(1 AS UNSIGNED)         AS STATUS
                 FROM c040
                WHERE c040.C040_Id = '{$pdv}'";

      return $this->sql2Json($sql);
    }
  }

  public function postData() {
    require_once(__DIR__ . "/../diversos/JsonUtils.php");
    $JsonUtils = new JsonUtils();

    $json = file_get_contents('php://input');
    $pdvs = $JsonUtils->json2Array($json);

    if (!is_array($pdvs)) {
      http_response_code(400);
      return false;
    }

    foreach ($pdvs as $pdv) {
      $this->transaction_start();

      try {
        $sql = "UPDATE c040 SET
                  C040_Numero_NFCE = '$pdv->NUMERO_NFCE'
                WHERE C040_Id = '$pdv->ID_INTEGRACAO'";

        $this->execute_sql($sql);

        $this->transaction_close(true);
      } catch (Exception $expt) {
        $this->transaction_close(false);
        break;
      }
    }

    http_response_code(200);
  }
}
