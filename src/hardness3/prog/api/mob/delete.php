<?php
namespace hardness;
//funções gerais e funções pertinentes a api do mobile
require_once('bibliotecas/classes/API001.php');
$API001 = new API001();

/**
 * Ações
 */
switch($g['r'][3]) {
	case 'produto':
		global $g;

		$retorno = $API001->auth($_GET['API_AUTH']);

		if (is_array($retorno)) {
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}

		$T269_Id = $_GET['T269_Id'];
		$retorno = array();

		$T269 = mysql_query("SELECT T269_Id
									FROM T269
									WHERE T269_Id='{$T269_Id}'");
		if (mysql_num_rows($T269) > 0) {
			mysql_query("DELETE FROM T269 WHERE T269_Id={$T269_Id}");
			$error = mysql_error();

			if (empty($error)) {
				header('HTTP/1.0 200 Ok');
				$retorno['code'] = 201;
				$retorno['mensagem'] = "Deletado com sucesso!";
			} else {
				header('HTTP/1.0 400 Bad Request');
				$retorno['code'] = 400;
				$retorno['mensagem'] = "Erro ao deletar produto:" . $error;
			}
		} else {
			header('HTTP/1.0 400 Bad Request');
			$retorno['code'] = 400;
			$retorno['mensagem'] = "Produto não se encontra no carrinho.";
		}

		print_r($retorno);
		die();
	break;

	default:
		$API001->retorno404();
}