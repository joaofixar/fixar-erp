<?php
namespace hardness;
$codigobanco = "422";
$codigo_banco_com_dv = b422_geraCodigoBanco($codigobanco);
$dv_banco = "7";
$nummoeda = "9";
$fator_vencimento = b422_fator_vencimento($this->data_vencimento);

//valor tem 10 digitos, sem virgula
$valor = b422_formata_numero($this->valor_duplicata,10,0,"valor");
//agencia ? 4 digitos
$agencia = b422_formata_numero($this->numero_agencia,5,0);
//conta ? 5 digitos + 1 do dv
$conta = b422_formata_numero($this->numero_conta,9,0);
// $conta_dv = b422_formata_numero($this->getDVConta(),1,0);
//carteira 175
$carteira = $this->numero_carteira;
//nosso_numero no maximo 8 digitos
$nnum = b422_formata_numero($this->nosso_numero,9,0);
//tipo de cobran?a boleto safra, fixo "2"
$tipo_cobranca = "2";

$codigo_barras = $codigobanco.$nummoeda.$fator_vencimento.$valor.$dv_banco.$agencia.$conta.$nnum.$tipo_cobranca;

mysqli_query("update T117 set T117_Codigo_Barras='$codigo_barras' where T117_Id='$nT117_Id'");

// 43 numeros para o calculo do digito verificador
$dv = b422_digitoVerificador_barra($codigo_barras);
// Numero para o codigo de barras com 44 digitos
$linha = substr($codigo_barras,0,4).$dv.substr($codigo_barras,4,44);

$nossonumero = $carteira.'/'.$nnum.'-'.b422_modulo_10($agencia.$conta.$carteira.$nnum);
$agencia_codigo = $agencia."/". $conta."-".b422_modulo_10($agencia.$conta);
//die('linha 1 ' . $linha);
$dadosboleto["codigo_barras"] = $linha;
$dadosboleto["linha_digitavel"] = b422_monta_linha_digitavel($linha); // verificar
$dadosboleto["agencia_codigo"] = $agencia_codigo ;
$dadosboleto["nosso_numero"] = $nossonumero;
$dadosboleto["codigo_banco_com_dv"] = $codigo_banco_com_dv;
$dadosBeneficiario = $this->nome_cedente . " " . $this->cpf_cnpj_cedente . " <br /> " . $this->endereco_cedente . " <br /> ".$this->cidade_cedente." / ".$this->estado_cedente ." - ".$this->cep_cedente;
$mensagemLocalPagamento = "Pagável em qualquer Banco do Sistema de Compensação";
$linha_digitavel = $dadosboleto["linha_digitavel"]; 
$codigo_barras = $dadosboleto["codigo_barras"];

//die("linha digitavel = ".$dadosboleto["linha_digitavel"] );
//die("codigo de barras = ".$dadosboleto["codigo_barras"]);
?>
