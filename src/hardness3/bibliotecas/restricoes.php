<?php
namespace hardness;
$rRegx = array(
	'naoVazio' 		=> array('/^.+$/m', 'Não pode ser vazio.'),
	'naoSelecionado'=> array('/^[^0]([0-9]+)?$/', 'Selecione um valor.'),
	'data' 			=> array('/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/', 'Tem que ser uma data no formato: DD/MM/AAAA.'),
	'email' 		=> array('/(?:[a-z0-9\'\.\-_\+\|]++|&amp;)+@[a-z0-9\-]+\.(?:[a-z0-9\-]+\.)*[a-z]+/Uusmi', 'Tem que ser um e-mail válido.'),
	'tempo' 		=> array('/^[0-9]{2}:[0-9]{2}$/', 'Tem que ser um tempo (Horas:Minutos): HH:MM.'),
	'numero' 		=> array('/^[0-9]+$/', 'Tem que ser um número.'),
);

/************************************************************************************************
* Funções de Restrições
*************************************************************************************************/
/**
* Verifica uma expressão regular (pré-definida ou não)
* 
* @param string $regx
* @param string $valor
* @return mixed
*/
function rRegx($regx, $valor) {
	global $rRegx;
	$regx_ = (isset($rRegx[$regx])) ? $rRegx[$regx][0] : $regx;
	$matched = preg_match($regx_, trim($valor));
	if ($matched == 0) {
		return (isset($rRegx[$regx])) ? $rRegx[$regx][1] : false;
	} else {
		return true;
	}
}

/**
* Tem que ser um número (int ou float), e pode ser 0
* 
* @param mixed $valor
*/
function rNumero($valor) {
	$valor = str_replace('.', '', $valor);
	$valor = str_replace(',', '.', $valor);
	return (is_numeric($valor)) ? true : "Tem que ser um número.";
}

/**
* Tem que ser um número (int ou float) e maior que 0
* 
* @param mixed $valor
*/
function rNumeroMaiorQueZero($valor) {
	$valor = str_replace('.', '', $valor);
	$valor = str_replace(',', '.', $valor);
	return (is_numeric($valor) && $valor > 0) ? true : "Tem que ser um número maior que zero.";
}

/**
* Data tem que ser maior ou igual a data atual
* 
* @param string $valor
*/
function rDataMaiorIgualAtual($valor) {
	$atual = date('Y-m-d');
	$param = implode(preg_match("~\/~", $valor) == 0 ? "/" : "-", array_reverse(explode(preg_match("~\/~", $valor) == 0 ? "-" : "/", $valor)));
	$dataAtual=strtotime($atual);
	$dataParam=strtotime($param);
	return ($dataAtual <= $dataParam) ? true : "Não pode ser anterior à data atual.";
}

/**
* Verifica duplicidade de um campo
* 
* @param mixed $campo
* @param mixed $valor
*/
function rDuplicidade($campo, $valor, $acaoId, $campoTexto = false, $permiteVazio = false, $campoFK = false, $parentFK = false) {
	//Verifica se permite vazio
	if($permiteVazio && $valor == ''){
		return true;
	}
	$campoTexto = $campoTexto ?  $campoTexto : $campo;
	$tabela = explode('_', $campo);
	$tabela = $tabela[0];
	$update = '';
	if (!empty($acaoId)) {
		$update = " AND {$tabela}_Id != '" . mysql_real_escape_string($acaoId) . "'";
	}
	if($campoFK && $parentFK){
		$checkFK = " AND {$campoFK} = '{$parentFK}'";
	}
	$sql = "SELECT {$campo} from {$tabela} WHERE {$campo} = '" . mysql_real_escape_string($valor) . "'{$update}{$checkFK};";
	$result = mysql_query($sql);
	if (mysql_error()) { return "Erro Interno. Por favor contate o suporte técnico. ({$sql})"; }
	if (mysql_num_rows($result) > 0) { return "Não é permitido duplicidade de registros para o este campo. '{$valor}' já existe no campo '{$campoTexto}'!"; }
	return true;
}

/**
* Valida um CNPJ
* 
* @param string $cnpj
*/
function rValorAlfanumericoCnpj($caractere) {
	$codigoAscii = ord(strtoupper($caractere));
	return $codigoAscii - 48;
}

function rValidarCnpj($cnpj) {
	//if (strlen($cnpj) == 14) {
 	//	$cnpj = $cnpj[0].$cnpj[1].'.'.$cnpj[2].$cnpj[3].$cnpj[4].'.'.$cnpj[5].$cnpj[6].$cnpj[7].'/'.$cnpj[8].$cnpj[9].$cnpj[10].$cnpj[11].'-'.$cnpj[12].$cnpj[13];
 	//}

	$cnpj = strtoupper(trim($cnpj));

	if (strlen($cnpj) != 18) {
	    return "O tamanho do CNPJ está incorreto, deve conter ponto, barra e traço.";
	}
	else if($cnpj[2] !== '.' || $cnpj[6] !== '.' || $cnpj[10] !== '/' || $cnpj[15] !== '-')
	{
		return "O formato do CNPJ está incorreto<b>(verifique os pontos, barras e traços)</b>.<br> Este deve ser: XX<b>.</b>XXX<b>.</b>XXX<b>/</b>XXXX<b>-</b>XX";
	}

	$cnpjSemMascara = str_replace(array('.', '/', '-'), '', $cnpj);

	if (!preg_match('/^[A-Z0-9]{12}[0-9]{2}$/', $cnpjSemMascara)) {
		return "O formato do CNPJ está incorreto. Para CNPJ alfanumérico, as 12 primeiras posições devem conter letras e/ou números e os 2 últimos caracteres devem ser numéricos.";
	}

	if ($cnpjSemMascara === '00000000000000') {
		return "O CNPJ é inválido.";
	}

	$pesos = array(6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2);
	$soma1 = 0;
	$soma2 = 0;

	for ($i = 0; $i < 12; $i++) {
		$valor = rValorAlfanumericoCnpj($cnpjSemMascara[$i]);
		$soma1 += $valor * $pesos[$i + 1];
		$soma2 += $valor * $pesos[$i];
	}

	$resto = $soma1 % 11;
	$digito1 = $resto < 2 ? 0 : 11 - $resto;

	$soma2 += $digito1 * $pesos[12];
	$resto = $soma2 % 11;
	$digito2 = $resto < 2 ? 0 : 11 - $resto;
	$retorno = (($cnpjSemMascara[12] == $digito1) && ($cnpjSemMascara[13] == $digito2));
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
	else if($cpf[3] !== '.' OR $cpf[7] !== '.' OR $cpf[11] !== '-')
	{
		return "O formato do CPF está incorreto<b>(verifique os pontos e traços)</b>.<br> Este deve ser: XXX<b>.</b>XXX<b>.</b>XXX<b>-</b>XX";
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

/**
* Faz a verificação de integridade da chave estrangeira
* 
* @param string $campo
* @param mixed $valor
* @return bol
*/
function rVerificaFK($campo, $valor) {
	if (empty($valor)) {
		return "Não pode ser vazio.";
	} else if ($valor == '...') {
		return "Um item deve ser selecionado.";
	}
	// Tabela
	$campo_ = explode('_', $campo);
	$tabela = $campo_[0];
	$valor = mysql_real_escape_string($valor);
	$sql = "SELECT {$campo} FROM {$tabela} WHERE {$campo} = '{$valor}'";
	$result = mysql_query($sql);
	if (mysql_error()) { return "Erro no SQL ao verificar a FK ({$sql})."; }
	return (mysql_num_rows($result) > 0) ? true : "A entrada ({$valor}) não existe na tabela ({$tabela}). Restrição de chave estrangeira.";
}

/**
* Tem que ser um tempo maior que 00:00
* 
* @param mixed $valor
*/
function rTempoMaiorQueZero($valor) {
	$valor = str_replace(':', '', $valor);
	return (is_numeric($valor) && $valor > 0) ? true : "O tempo tem que ser maior que zero.";
}

/**
* Data pode ser menor até 30 dias
* 
* @param string $valor
*/
function rDataMenorTrintaDias($valor) {
	$atual = date('Y-m-d');
	$param = implode(preg_match("~\/~", $valor) == 0 ? "/" : "-", array_reverse(explode(preg_match("~\/~", $valor) == 0 ? "-" : "/", $valor)));
	$dataAtual=strtotime($atual);
	return (strtotime("+30 days", strtotime($param)) > time()) ? true : "Não pode ser mais que 30 dias da data atual.";
}

/**
* Tem que ser um número (int ou float) e estar dentro dos valores determinados
* 
* @param mixed $valor
*/
function rNumeroValido($valor, $valoresValidos) {
	$valor = str_replace('.', '', $valor);
	$valor = str_replace(',', '.', $valor);
	if(!is_numeric($valor)) {
		return "O dado informado precisa ser um número.";
	}
	if(!in_array($valor, $valoresValidos)){
		$validos = implode(", ", $valoresValidos);
		return "O valor informado não é valido.<br>Valores válidos: {$validos}";
	}
	return true;
}
