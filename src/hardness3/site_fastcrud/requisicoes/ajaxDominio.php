<?php
require_once('../conf.php');
require_once("../funcoesCriacaoConta.php");
$resposta = array('code' => true, 'data' => array());

switch($_REQUEST['funcao']) {
	case 'atualizarComboCidades':
		$D024_D018_Id = isset($_REQUEST['D024_D018_Id']) ? $_REQUEST['D024_D018_Id'] : false;
		$sqlD020 = mysqli_query($conNOTA,"SELECT * FROM D020 WHERE D020_D018_Id = '{$D024_D018_Id}'");
		if(mysqli_num_rows($sqlD020) > 0){
			$resposta['code'] = true;
			$options = '<option value="">Selecione uma cidade</option>';
			while($D020 = mysqli_fetch_array($sqlD020)){
				$options .= "<option value=\"{$D020['D020_Id']}\">{$D020['D020_Nome_Cidade']}</option>";
			}
			$resposta['data'] = $options;
		} else {
			$resposta['code'] = false;
		}
	break;

	case 'validaCNPJ':
		if(!empty($_REQUEST['D024_Cnpj'])){
			$sqlD024 = mysqli_query($conNOTA, "SELECT * FROM D024 WHERE D024_Cnpj = '{$_REQUEST['D024_Cnpj']}'");
			if(mysqli_num_rows($sqlD024) > 0){
				$resposta['code'] = false;
			} else {
				$resposta['code'] = true;
			}
			if(rValidarCnpj($_REQUEST['D024_Cnpj']) !== true){
				$resposta['code'] = false;
				$resposta['data'] = 'invalido';
			}
		}
	break;

	case 'validaDominioEmpresa':
		if(!empty($_REQUEST['Dominio'])){
			$retorno = verificarDominioValido($_REQUEST['Dominio'], $_REQUEST['Server']);
			if($retorno !== true){
				$resposta['data'] = $retorno;
				$resposta['code'] = false;
			} else {
				$resposta['code'] = true;
			}
		}
	break;

	case 'calcularPreco':
		$modulos = (!empty($_REQUEST['modulos'])) ? explode(",", $_REQUEST['modulos']) : array();
		$preco = calcularPreco($modulos);
		$resposta['data'] = $preco;
		$resposta['code'] = true;
	break;

	case 'verificaTipoConta':
		$resposta['code'] = false;
		$sqlC025 = mysqli_query($conNOTA, "SELECT C025_Flag_Transferido FROM C025 WHERE C025_Login = '{$_REQUEST['Cnpj']}'");
		if(mysqli_num_rows($sqlC025) > 0){
			$rowC025 = mysqli_fetch_array($conNOTA, $sqlC025);
			if($rowC025['C025_Flag_Transferido'] == '1'){
				$resposta['code'] = true;
			}
		}
	break;

	case 'validaCPF':
		if(!empty($_REQUEST['D024_CPF_Responsavel'])){
			if(rValidarCpf($_REQUEST['D024_CPF_Responsavel']) !== true){
				$resposta['code'] = false;
			} else {
				$resposta['code'] = true;
			}
		}
	break;

	case 'limparLoginSenha':
		setcookie("registerCode", "", time()-1000, "/");
		setcookie("dataCode", "", time()-1000, "/");
		setcookie("opcaoSalvarDados", "", time()-1000, "/");
		setcookie("accountType", "", time()-1000, "/");
	break;
}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";

/**
* Valida um CNPJ
* 
* @param string $cnpj
*/
function rValidarCnpj($cnpj) {

	if (strlen($cnpj) != 18) {
	    return "O tamanho do CNPJ está incorreto, deve conter ponto, barra e traço.";
	}

	$soma1 = ($cnpj[0] * 5) +
	         ($cnpj[1] * 4) +
	         ($cnpj[3] * 3) +
	         ($cnpj[4] * 2) +
	         ($cnpj[5] * 9) +
	         ($cnpj[7] * 8) +
	         ($cnpj[8] * 7) +
	         ($cnpj[9] * 6) +
	         ($cnpj[11] * 5) +
	         ($cnpj[12] * 4) +
	         ($cnpj[13] * 3) +
	         ($cnpj[14] * 2);

	$resto = $soma1 % 11;
	$digito1 = $resto < 2 ? 0 : 11 - $resto;

	$soma2 = ($cnpj[0] * 6) +
	        ($cnpj[1] * 5) +
	        ($cnpj[3] * 4) +
	        ($cnpj[4] * 3) +
	        ($cnpj[5] * 2) +
	        ($cnpj[7] * 9) +
	        ($cnpj[8] * 8) +
	        ($cnpj[9] * 7) +
	        ($cnpj[11] * 6) +
	        ($cnpj[12] * 5) +
	        ($cnpj[13] * 4) +
	        ($cnpj[14] * 3) +
	        ($cnpj[16] * 2);
	$resto = $soma2 % 11;
	$digito2 = $resto < 2 ? 0 : 11 - $resto;
	$retorno = (($cnpj[16] == $digito1) && ($cnpj[17] == $digito2));
	return ($retorno) ? true : "O CNPJ é inválido.";
}

/**
* Valida um CPF
* 
* @param string $cpf
*/
function rValidarCpf($cpf) {
	$erro = "O CPF é inválido.";
	if (strlen($cpf) != 14) {
	    return "O tamanho do CPF está incorreto, deve conter ponto e traço.";
	}
	$cpf=str_replace(".","",$cpf);
	$cpf=str_replace("-","",$cpf);
	$cpf=trim($cpf);
	$nulos = array("12345678909","11111111111","22222222222","33333333333",
	               "44444444444","55555555555","66666666666","77777777777",
	               "88888888888","99999999999","00000000000");
	/* Retira todos os caracteres que nao sejam 0-9 */
	$aux="";
	for ($i=1; $i<=strlen($cpf); $i++){
	  $ch=substr($cpf,$i-1,1);
	  if (ord($ch)>=48 && ord($ch)<=57){
	    $aux=$aux.$ch;
	  }
	}
	$cpf = $aux;
	/* Retorna falso se o cpf for nulo */
	foreach ( $nulos as $nulo ) {
	  if($cpf == $nulo){
	    return $erro;
	  }
	}
	/*Calcula o penúltimo dígito verificador*/
	$acum=0;
	for($i=0; $i<9; $i++) {
	  $acum+= @$cpf[$i]*(10-$i);
	}

	$x=$acum % 11;
	$acum = ($x>1) ? (11 - $x) : 0;
	/* Retorna falso se o digito calculado eh diferente do passado na string */
	if ($acum != @$cpf[9]){
	  return $erro;
	}
	/*Calcula o último dígito verificador*/
	$acum=0;
	for ($i=0; $i<10; $i++){
	  $acum+= @$cpf[$i]*(11-$i);
	}

	$x=$acum % 11;
	$acum = ($x > 1) ? (11-$x) : 0;
	/* Retorna falso se o digito calculado eh diferente do passado na string */
	if ( $acum != @$cpf[10]){
	  return $erro;
	}
	if (empty($cpf)) { return $erro; }
	/* Retorna verdadeiro se o cpf eh valido */
	return true;
}
