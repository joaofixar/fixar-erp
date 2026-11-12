<?php
namespace hardness;
$codigobanco = "341";
$codigo_banco_com_dv = b341_geraCodigoBanco($codigobanco);
$nummoeda = "9";
$fator_vencimento = b341_fator_vencimento($this->data_vencimento);

//valor tem 10 digitos, sem virgula
$valor = b341_formata_numero($this->valor_duplicata,10,0,"valor");
//agencia ? 4 digitos
$agencia = b341_formata_numero($this->numero_agencia,4,0);
//conta ? 5 digitos + 1 do dv
$conta = b341_formata_numero($this->numero_conta,5,0);
// $conta_dv = b341_formata_numero($this->getDVConta(),1,0);
//carteira 175
$carteira = $this->numero_carteira;
//nosso_numero no maximo 8 digitos
$nnum = b341_formata_numero($this->nosso_numero,8,0);

$codigo_barras = $codigobanco.$nummoeda.$fator_vencimento.$valor.$carteira.$nnum.b341_modulo_10($agencia.$conta.$carteira.$nnum).$agencia.$conta.b341_modulo_10($agencia.$conta).'000';

mysqli_query("update T117 set T117_Codigo_Barras='$codigo_barras' where T117_Id='$nT117_Id'");

// 43 numeros para o calculo do digito verificador
$dv = b341_digitoVerificador_barra($codigo_barras);
// Numero para o codigo de barras com 44 digitos
$linha = substr($codigo_barras,0,4).$dv.substr($codigo_barras,4,43);

$nossonumero = $carteira.'/'.$nnum.'-'.b341_modulo_10($agencia.$conta.$carteira.$nnum);
$agencia_codigo = $agencia."/". $conta."-".b341_modulo_10($agencia.$conta);

$dadosboleto["codigo_barras"] = $linha;
$dadosboleto["linha_digitavel"] = b341_monta_linha_digitavel($linha); // verificar
$dadosboleto["agencia_codigo"] = $agencia_codigo ;
$dadosboleto["nosso_numero"] = $nossonumero;
$dadosboleto["codigo_banco_com_dv"] = $codigo_banco_com_dv;
$dadosBeneficiario = $this->nome_cedente . " " . $this->cpf_cnpj_cedente . " <br /> " . $this->endereco_cedente . " <br /> ".$this->cidade_cedente." / ".$this->estado_cedente ." - ".$this->cep_cedente;
$mensagemLocalPagamento = "Até o vencimento, preferencialmente no Itaú. Após o vencimento, somente no Itaú";
$linha_digitavel = $dadosboleto["linha_digitavel"]; 
$codigo_barras = $dadosboleto["codigo_barras"];
?>
