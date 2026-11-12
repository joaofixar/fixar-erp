<?php
namespace hardness;
set_time_limit(60000);

require_once(__DIR__ . "/../diversos/SQLUtils.php");

class BaseIntegracao {	
	protected function sql2Json($sql) {		
		$SQLUtils = new SQLUtils();

		return $SQLUtils->sql2Json($sql);
	}

	protected function execute_sql($sql) {		
		$SQLUtils = new SQLUtils();

		return $SQLUtils->execute_sql($sql);
	}

	protected function fetch_row($query) {		
		$SQLUtils = new SQLUtils();

		return $SQLUtils->fetch_row($query);
	}
	
	protected function transaction_start() {		
		$SQLUtils = new SQLUtils();

		return $SQLUtils->transaction_start();
	}
	
  public function transaction_close($commit = true) {		
		$SQLUtils = new SQLUtils();

		return $SQLUtils->transaction_close($commit);
	}

	protected function validateParams($params) {
		$fields = array();

		foreach ($params as $param) {
			$value = $this->paramFromGet($param);

			if (empty($value)) {
				array_push($fields, $param);
			}
		}

		if (!empty($fields)) {
      http_response_code(400);
      return false;
		}

		return true;
	}

	protected function paramFromGet($param) {
		return $_GET[$param];
	}

	protected function base64Decode($base64) {
		return base64_decode($base64);
	}

	protected function getPorcentagem($valor, $porcento) {
		$v_valor    = floatval($valor);
		$v_porcento = floatval($porcento);

		if (($v_valor > 0) && ($v_porcento > 0)) {
			return ($v_porcento / $v_valor) * 100;
		} else {
			return 0;
		}
	}
}
