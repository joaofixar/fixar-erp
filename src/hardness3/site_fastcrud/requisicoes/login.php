<?php
/*
* Login
* 
* @param login
* @param senha
* @return JSON
*/
$resposta = array('code' => true, 'data' => array(), 'opcoes' => true);

$erros = array();
if (!isset($_REQUEST['login']) || empty($_REQUEST['login'])) {
	$resposta['code'] = false;
	$resposta['data'] = 'Por favor, preencha o login.';
} else if ($_REQUEST['tipoConta'] != 'novo' && (!isset($_REQUEST['senha']) || empty($_REQUEST['senha']))) {
	$resposta['code'] = false;
	$resposta['data'] = 'Por favor, preencha a senha.';
} else {
	$login = mysql_real_escape_string($_REQUEST['login']);
	$senha = mysql_real_escape_string($_REQUEST['senha']);

	if(empty($_COOKIE['opcaoSalvarDados']) || $_COOKIE['opcaoSalvarDados'] != $_REQUEST['opcaoSalvarDados']){
		setcookie("opcaoSalvarDados", $_REQUEST['opcaoSalvarDados'], time()+(60*60*24*365), '/');
	}
	
	if($_REQUEST['tipoConta'] == 'novo'){
		$sqlC025 = mysql_query("SELECT * FROM C025 WHERE C025_Login='$login' AND C025_Flag_Transferido = '1'", $conNOTA);	
		if(mysql_num_rows($sqlC025) == 1){
			$mC025 = mysql_fetch_array($sqlC025);			
			$resposta['bancoDados'] = $mC025['C025_Banco_Dados'];
			$resposta['code'] = true;
			$resposta['data'] = <<<EOT
				<form name='form_acesso' id='form_acesso' method='post' action='http://nota.hardness.com.br/hardness3/redireciona.php'>
				<input type='hidden' name='db' id='db' value='{$mC025['C025_Banco_Dados']}'>
				<input type='hidden' name='user' id='user' value='{$_REQUEST['usuario']}'>
				<input type='hidden' name='pass' id='pass' value='{$senha}'>
				</form>
EOT;
			if($_REQUEST['opcaoSalvarDados'] == 'login') {
				$xcnpj = base64_encode($login.'412596');
				setcookie("registerCode", $xcnpj, time()+(60*60*24*365), '/');
				
				setcookie("dataCode", "", time()-1000, "/");

				$resposta['opcoes'] = false;
			}  else if($_REQUEST['opcaoSalvarDados'] == 'nunca'){
				setcookie("registerCode", "", time()-1000, "/");
				setcookie("dataCode", "", time()-1000, "/");
			}
			setcookie("accountType", '1', time()+(60*60*24*365), '/');
			setcookie("lastId", md5($login), time()+(60*60*24*365), '/');

			$check = verificarRestricao($mC025['C025_D024_Id'], $conNOTA);
			if($check !== true){
				$resposta['data'] = "location.replace('https://hardness.hardness.com.br/externo/cobranca/atualizarBoleto/?ajax=true&noEmp=true&u=1&p=1&a={$check}')";
				$resposta['code'] = 'restricao';
			}
		} else {
			$resposta['code'] = false;
			$resposta['data'] = 'Não foi possível localizar o cadastro de sua empresa, por favor verifique o login informado.';
		}
	} else {
		// Acesso NOTA
		$C025 = mysql_query("SELECT C025_Id,C025_Banco_Dados,C025_D024_Id,C025_Login,C025_Senha FROM C025 WHERE C025_Login='$login' AND C025_Senha='$senha'", $conNOTA);
		$uc = '';	// é cotador?
		if (mysql_num_rows($C025)<=0) {
			$C025=mysql_query("SELECT C025_Id,C025_Banco_Dados,C025_D024_Id,C025_Login,C025_Senha,C026_Id, (SELECT T002_Data_Recebimento FROM T002 WHERE T002_D024_Id=D024_Id ORDER BY T002_Data_Recebimento DESC LIMIT 1) AS Recebimento FROM C025 LEFT JOIN C026 ON C026_Id=C025_C026_Id LEFT JOIN D024 ON D024_Id=C025_D024_Id WHERE C026_Login='$login' AND C026_Senha='$senha' AND C025_Flag_Ativo != 'N' ORDER BY C025_Flag_Transferido,Recebimento DESC;", $conNOTA);
			$uc = 'S';
			if (mysql_num_rows($C025)<=0) {
				$resposta['code'] = false;
				$resposta['data'] = 'Login e Senha Incorretos!';
			}
		}
		if ($resposta['code']) {
			$mC025=mysql_fetch_array($C025);
			// Log
			if ($uc == 'S')
			{
			    $uc = $mC025['C026_Id'];
			    $IP_Acesso=$_SERVER['REMOTE_ADDR'];
			    $cClausula  = " insert into A001 ( ";
			    $cClausula .= " A001_D024_Id, ";
			    $cClausula .= " A001_Data_Acesso, ";
			    $cClausula .= " A001_IP, ";
			    $cClausula .= " A001_Tipo ";
			    $cClausula .= " ) VALUES ( ";
			    $cClausula .= " '{$mC025['C025_D024_Id']}', ";
			    $cClausula .= " now(), ";
			    $cClausula .= " '$IP_Acesso', ";
			    $cClausula .= " '5' ";
			    $cClausula .= " ) ";
			    mysql_query($cClausula, $conNOTA);
			}
			else
			{
			    $IP_Acesso=$_SERVER['REMOTE_ADDR'];
			    $cClausula  = " insert into $Banco_Dados.A001 ( ";
			    $cClausula .= " A001_D024_Id, ";
			    $cClausula .= " A001_Data_Acesso, ";
			    $cClausula .= " A001_IP, ";
			    $cClausula .= " A001_Tipo ";
			    $cClausula .= " ) VALUES ( ";
			    $cClausula .= " '{$mC025['C025_D024_Id']}', ";
			    $cClausula .= " now(), ";
			    $cClausula .= " '$IP_Acesso', ";
			    $cClausula .= " '1' ";
			    $cClausula .= " ) ";
			    mysql_query($cClausula, $conNOTA);
			}
			// Redirect
			$cnpj = $mC025['C025_Login'];
			$senha = $mC025['C025_Senha'];
			$resposta['data'] = <<<EOT
				<form name='form_acesso' id='form_acesso' method='post' action='https://nfe.hardness.com.br/hardness/principal/login_nfe.php'>
				<input type='hidden' name='nHeight_Padrao' id='nHeight_Padrao' value=''>
				<input type='hidden' name='nWidth_Padrao' id='nWidth_Padrao' value=''>
				<input type='hidden' name='e' id='e' value='1'>
				<input type='hidden' name='b' id='b' value='{$mC025['C025_Banco_Dados']}'>
				<input type='hidden' name='l' id='l' value='$cnpj'>
				<input type='hidden' name='s' id='s' value='$senha'>
				<input type='hidden' name='w' id='w' value='web'>
				<input type='hidden' name='b' id='b' value='{$mC025['C025_Banco_Dados']}'>
				<input type='hidden' name='sb' id='sb' value='{$mC025['C025_Senha']}'>
				<input type='hidden' name='uc' id='uc' value='$uc'>
				</form>
EOT;
			// <input type='hidden' name='PaginaQueIniciou' id='PaginaQueIniciou' value='$PaginaQueIniciou'>
			if($_REQUEST['opcaoSalvarDados'] == 'login') {
				$xcnpj = base64_encode($cnpj.'412596');
				setcookie("registerCode", $xcnpj, time()+(60*60*24*365), '/');
				
				setcookie("dataCode", "");

				$resposta['opcoes'] = false;
			} else if($_REQUEST['opcaoSalvarDados'] == 'senha'){
				$xcnpj = base64_encode($cnpj.'412596');
				setcookie("registerCode", $xcnpj, time()+(60*60*24*365), '/');

				$xsenha = base64_encode('893785'.$senha);
				setcookie("dataCode", $xsenha, time()+(60*60*24*365), '/');
				
				$resposta['opcoes'] = false;
			} else if($_REQUEST['opcaoSalvarDados'] == 'nunca'){
				setcookie("registerCode", "", time()-1000, "/");
				setcookie("dataCode", "", time()-1000, "/");
			}
			setcookie("accountType", '0', time()+(60*60*24*365), '/');
			setcookie("lastId", md5($login), time()+(60*60*24*365), '/');

			if(empty($uc)){
				$check = verificarRestricao($mC025['C025_D024_Id'], $conNOTA);
				if($check !== true){
					$resposta['data'] = "location.replace('https://hardness.hardness.com.br/externo/cobranca/atualizarBoleto/?ajax=true&noEmp=true&u=1&p=1&a={$check}')";
					$resposta['code'] = 'restricao';
				}
			}
		}
	}
}

$resposta['debug'] = $_REQUEST['opcaoSalvarDados'] . ' ' . $_COOKIE['opcaoSalvarDados'] . ' ' . $_COOKIE['registerCode'] . ' ' . $_COOKIE['dataCode'];

if($_COOKIE['opcaoSalvarDados'] == 'nunca'){
	$resposta['opcoes'] = false;
}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";


function verificarRestricao($D024_Id)
{
	//Key usada pra encriptar o D024_Id
	$key = "#hard!";
	
	//-- Encontra todos os a vencer
	$cClausula  = " select ";
	$cClausula .= " T117_Id, ";
	$cClausula .= " T002_Data_Vencimento, ";
	$cClausula .= " datediff(current_date(),T002_Data_Vencimento) dias ";
	$cClausula .= " from T117 ";
	$cClausula .= " left join D024 on D024_Id=T117_D024_Id ";
	$cClausula .= " left join T002 on T002_T117_Id=T117_Id ";
	$cClausula .= " left join D007 on D007_Id=T117_D007_Id ";
	$cClausula .= " where D024_Id='{$D024_Id}' ";
	$cClausula .= " and T117_Flag_Cancelado!='S' ";
	$cClausula .= " AND (T002_T002_Id_Agrupado<=0 OR T002_T002_Id_Agrupado IS NULL)";
	$cClausula .= " and T002_Data_Recebimento='0000-00-00' ";
	$cClausula .= " order by T117_Data_Vencimento desc";
	$T117=mysql_query($cClausula);

	$mT117 = mysql_fetch_array($T117);

	//echo mysql_error();
	if (mysql_num_rows($T117)<=0 or $mT117[dias]>366) {
		return md5($D024_Id);
	}
	//echo $mT117[T002_Data_Vencimento].'-'.$mT117[dias];

	//-- Encontra todos os vencidos a mais de 7 dias
	$cClausula  = " select ";
	$cClausula .= " T117_Id, ";
	$cClausula .= " T117_D024_Id, ";
	$cClausula .= " T117_Data_Emissao, ";
	$cClausula .= " T117_Data_Vencimento, ";
	$cClausula .= " T117_Valor_Total, ";
	$cClausula .= " T117_Nosso_Numero,";
	$cClausula .= " T117_Flag_Cancelado,";
	$cClausula .= " T002_Data_Vencimento, ";
	$cClausula .= " D024_Cnpj, ";
	$cClausula .= " D007_Nome_Banco, ";
	$cClausula .= " datediff(current_date(),T117_Data_Vencimento) as Dias_Vencer, ";
	$cClausula .= " datediff(T002_Data_Vencimento,current_date()) as Dias_Prorrogado ";
	$cClausula .= " from T117 ";
	$cClausula .= " left join D024 on D024_Id=T117_D024_Id ";
	$cClausula .= " left join T002 on T002_T117_Id=T117_Id ";
	$cClausula .= " left join D007 on D007_Id=T117_D007_Id ";
	$cClausula .= " where D024_Id='{$D024_Id}' ";
	$cClausula .= " and (datediff(current_date,T117_Data_Vencimento)>=5";
	$cClausula .= "  or (datediff(current_date,T117_Data_Vencimento)>0 and datediff(T117_Data_Vencimento,T002_Data_Vencimento)>=5))";
	$cClausula .= " and T117_Flag_Cancelado='N' ";
	$cClausula .= " and T002_Data_Recebimento='0000-00-00' ";
	$cClausula .= " and T002_Data_Vencimento<curdate() ";
	$cClausula .= " AND (T002_T002_Id_Agrupado<=0 OR T002_T002_Id_Agrupado IS NULL)";
	$cClausula .= " order by T117_Data_Vencimento";
	$cSql_T117=mysql_query($cClausula);

	//echo mysql_error();
	if (mysql_num_rows($cSql_T117)>0) {
	    while ($mT117 = mysql_fetch_array($cSql_T117)) {
			if (abs($mT117['Dias_Prorrogado'])>3) {
				return md5($D024_Id);
			} else if (($mT117['Dias_Vencer']>0) and (7-abs($mT117['Dias_Vencer'])<=0)) {
				return md5($D024_Id);
			}
		}
	}
	return true;
}