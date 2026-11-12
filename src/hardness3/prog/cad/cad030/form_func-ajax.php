<?php
namespace hardness;

require_once('bibliotecas/classes/CAD030.php');
$CAD030 = new CAD030();

$resposta = array('code' => true, 'data' => array());
/**
 * Ações
 */
switch($g['r'][3]) {
	case 'testarEmail':
		$C007_Id = isset($r_C007_Id) ? $r_C007_Id : false;
		$emailDestino = isset($_REQUEST['emailDestino']) ? trim($_REQUEST['emailDestino']) : '';

		if (empty($C007_Id)) {
			$resposta['code'] = false;
			$resposta['data'] = 'Registro do usuario nao informado.';
			break;
		}

		if ($g['C031']['recebimentoEmail'] == 'N') {
			$retorno = $CAD030->testarEmail($C007_Id, $emailDestino);
			if (is_array($retorno) && !empty($retorno['code'])) {
				$resposta['code'] = true;
				$resposta['data'] = $retorno['data'];
			} else {
				$resposta['code'] = false;
				$resposta['data'] = is_array($retorno) && isset($retorno['data']) ? $retorno['data'] : 'Nao foi possivel enviar o e-mail de teste.';
			}
		} else {
			$C007_Id = mysql_real_escape_string($C007_Id);
			$sql = "SELECT C007_Email_POP_Servidor, C007_Email_SMTP_Usuario, C007_Email_SMTP_Senha, C007_Email_POP_Porta FROM C007 WHERE C007_Id = '{$C007_Id}';";
			$resultado = mysql_query($sql);
			$C007 = mysql_fetch_assoc($resultado);

			require_once('sistema/comunicacaoEmail.php');
			$mail = new comunicacaoEmail($C007['C007_Email_POP_Servidor'], $C007['C007_Email_SMTP_Usuario'], $C007['C007_Email_SMTP_Senha'], 'E', false, $C007['C007_Email_POP_Porta']);
			$pastas = $mail->listarPastas();

			if ($pastas === false) {
				$resposta['code'] = false;
				$resposta['data'] = "Erro ao tentar conectar.<br />Por favor, verifique se o Servidor/Login/Senha estão corretos.";
			} else {
				$select = array();
				foreach ($pastas as $pasta) {
					$mailbox = explode('}', $pasta);
					$select[] = array('title' => $mailbox[1], 'value' => $mailbox[1]);
				}
				$select = mysql_real_escape_string(serialize($select));
				$sql = "UPDATE C007 SET C007_Email_Pastas = '{$select}' WHERE C007_Id = '{$C007_Id}';";
				mysql_query($sql);
				$resposta['code'] = true;
				$resposta['data'] = 'Pastas de e-mail localizadas com sucesso.';
			}
		}
	break;

	case 'recriarSenha':
		global $g;
		$Login = $r_RecriarSenha_Login;
		$Senha = $r_RecriarSenha_Senha;

		$resposta['data'] = '';

		if(empty($Login) || empty($Senha)){

			$resposta['data'].='Digite a senha e o login';
			$resposta['code'] = false;

		}else{
			// É necessário um usuário com privilégio root para poder criar os usuários
			mysqli_close($g['conexaoBanco']);
			$con=mysqli_connect($confUsuario['dbHost'], $Login, $Senha,$confUsuario['dbDatabase']);
			// $dbs=mysql_select_db($confUsuario['dbDatabase'], $con);
			if (!empty(mysql_error())){
				$resposta['data'].='0. '.mysql_error();
			} else {
				$g['conexaoBanco'] = $con;
				$dbHost = $confUsuario['dbHost'];
				$sql = "SELECT C007_Login,C007_Id,C007_Ativo FROM C007";
				if (!empty($r_C007_Id)) {
					$sql .= " WHERE C007_Id = '{$r_C007_Id}'";
				}
				$res = mysql_query($sql);
				log("SQL= ".$sql);
				log('retorno usuarios '.mysql_num_rows($res));
				$array = array();
				while ($row = mysql_fetch_assoc($res)) {
					mysql_query("UPDATE `{$confUsuario['dbDatabase']}`.C007 SET C007_Banco_Senha = '' WHERE C007_Id = '{$row['C007_Id']}'");
					$novaSenha = md5(uniqid());
					$novaSenha = mysql_real_escape_string(md5($novaSenha));
					$user = mysql_real_escape_string($row['C007_Login'] . $row['C007_Id'] . $confUsuario['dbDatabase']);
					mysql_query("DROP USER '{$user}'@'{$dbHost}'");
					$user = mysql_real_escape_string($row['C007_Login'] . $row['C007_Id']);
					mysql_query("DROP USER '{$user}'@'{$dbHost}'");		
					if ($row['C007_Ativo'] == 'S'){
						mysql_query("CREATE USER '{$user}'@'{$dbHost}' IDENTIFIED BY '{$novaSenha}'");
						if (!empty(mysql_error())){
							$resposta['data'].="3. ($user) - ".mysql_error().'<br>';
							break;
						} else {
							mysql_query("GRANT ALTER, ALTER ROUTINE, CREATE, CREATE ROUTINE, CREATE TEMPORARY TABLES, CREATE VIEW, DELETE, DROP, EVENT, EXECUTE, INDEX, INSERT, LOCK TABLES, REFERENCES, SELECT, SHOW VIEW, TRIGGER, UPDATE ON `{$confUsuario['dbDatabase']}`.* TO '{$user}'@'{$dbHost}' WITH GRANT OPTION");
							// mysql_query("GRANT ALL PRIVILEGES ON `{$confUsuario['dbDatabase']}`.* TO '{$user}'@'{$dbHost}'");
							if (!empty(mysql_error())){
								$resposta['data'].="4. $user ".mysql_error().'<br>';
								break;
							}
							mysql_query("GRANT ALTER, ALTER ROUTINE, CREATE, CREATE ROUTINE, CREATE TEMPORARY TABLES, CREATE VIEW, DELETE, DROP, EVENT, EXECUTE, INDEX, INSERT, LOCK TABLES, REFERENCES, SELECT, SHOW VIEW, TRIGGER, UPDATE ON `global`.* TO '{$user}'@'{$dbHost}'");
							if (!empty(mysql_error())){
								$resposta['data'].="5. $user ".mysql_error().'<br>';
								break;
							}
							mysql_query("UPDATE `{$confUsuario['dbDatabase']}`.C007 SET C007_Banco_Senha = '" . mysql_real_escape_string($novaSenha) . "' WHERE C007_Id = '{$row['C007_Id']}'");
							if (!empty(mysql_error())){
								$resposta['data'].='1. '.mysql_error();
								break;
							}
						}
					}
				}
				mysql_query("FLUSH PRIVILEGES");
			}
		}
		if (empty($resposta['data'])){
			$resposta['data'] = 'Processo concluído!';
			$resposta['code'] = true;
		} else {
			$resposta['code'] = false;					
		}

	break;	

}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
?>
