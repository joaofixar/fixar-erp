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

		$quantidade = $_GET['quantidade'];
		$T269_Id = $_GET['T269_Id'];
		if($quantidade<1){
			header('HTTP/1.0 400 Bad Request');
			$retorno['code'] = 400;
			$retorno['mensagem'] = "Quantidade não pode ser menor que 1.";
		}else{
			$retorno = array();
			$T269 = mysql_query("SELECT T269_Id
								FROM T269
								WHERE T269_Id='{$T269_Id}'");
			if (mysql_num_rows($T269) > 0) {
				mysql_query("UPDATE T269 SET T269_Quantidade = '{$quantidade}' WHERE T269_Id='{$T269_Id}'");
				$error = mysql_error();

				if (empty($error)) {
					header('HTTP/1.0 200 Ok');
					$retorno['code'] = 200;
					$retorno['mensagem'] = "Alterado com sucesso!";
				} else {
					header('HTTP/1.0 400 Bad Request');
					$retorno['code'] = 400;
					$retorno['mensagem'] = "Erro ao alterar quantidade do produto:" . $error;
				}
			} else {
				header('HTTP/1.0 400 Bad Request');
				$retorno['code'] = 400;
				$retorno['mensagem'] = "Produto não se encontra no carrinho.";
			}
		}		

		echo json_encode($retorno, JSON_UNESCAPED_UNICODE);

		die();

	break;

	case 'contatoSenha':
		global $g;

		$retorno = $API001->auth($_GET['API_AUTH']);

		if (is_array($retorno)) {
			echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
			die();
		}
		$retorno = array();
		$senhas = base64_decode($_GET['mudarS']);
		
		list($D013_Senha_Atual, $D013_Senha_Nova,$D013_Senha_Nova_Repeticao) = explode(':',$senhas);
		$D013 = mysql_query("SELECT D013_Id,D179_Senha ,D013_E_Mail,D179_Login 
							   FROM D013
						  LEFT JOIN D179 ON D179_Login=D013_E_Mail
							  WHERE D179_Senha  = '{$D013_Senha_Atual}' 
							    AND D013_Id='{$g['clienteAtualContato']}'");
		$rD013 = mysql_fetch_assoc($D013);
		if(mysql_num_rows($D013)>=1){
			if ($D013_Senha_Nova==$D013_Senha_Nova_Repeticao) {
				if($D013_Senha_Nova_Repeticao!=$rD013['D179_Senha']){
					mysql_query("UPDATE D179 SET D179_Senha = '{$D013_Senha_Nova_Repeticao}' WHERE D179_Login='{$rD013["D179_Login"]}'");
					header('HTTP/1.0 200 Ok');
					$retorno['code'] = 200;
					$retorno['mensagem'] = "Alterado com sucesso!";
				}else{
					header('HTTP/1.0 400 Bad Request');
					$retorno['code'] = 400;
					$retorno['mensagem'] = "A senha nova digitada é igual a senha em cadastro.";
				}
			} else {
				header('HTTP/1.0 400 Bad Request');
				$retorno['code'] = 400;
				$retorno['mensagem'] = "A senha nova não confere em ambos os campos.";
			}
		}else{
			header('HTTP/1.0 400 Bad Request');
			$retorno['code'] = 400;
			$retorno['mensagem'] = "A senha atual digitada não confere com a senha em cadastro.";
		}
		echo "\n";
		echo json_encode($retorno, JSON_UNESCAPED_UNICODE);

		die();

	break;

	default:
		$API001->retorno404();
}