<?php
namespace hardness;
// +----------------------------------------------------------------------+
// | BoletoPhp - Vers�o Beta                                              |
// +----------------------------------------------------------------------+
// | Este arquivo est� dispon�vel sob a Licen�a GPL dispon�vel pela Web   |
// | em http://pt.wikipedia.org/wiki/GNU_General_Public_License           |
// | Voc� deve ter recebido uma c�pia da GNU Public License junto com     |
// | esse pacote; se n�o, escreva para:                                   |
// |                                                                      |
// | Free Software Foundation, Inc.                                       |
// | 59 Temple Place - Suite 330                                          |
// | Boston, MA 02111-1307, USA.                                          |
// +----------------------------------------------------------------------+

// +----------------------------------------------------------------------+
// | Originado do Projeto BBBoletoFree que tiveram colabora��es de Daniel |
// | William Schultz e Leandro Maniezo que por sua vez foi derivado do	  |
// | PHPBoleto de Jo�o Prado Maia e Pablo Martins F. Costa                |
// |                                                                      |
// | Se vc quer colaborar, nos ajude a desenvolver p/ os demais bancos :-)|
// | Acesse o site do Projeto BoletoPhp: www.boletophp.com.br             |
// +----------------------------------------------------------------------+

// +---------------------------------------------------------------------------------+
// | Equipe Coordena��o Projeto BoletoPhp: <boletophp@boletophp.com.br>              |
// | Desenvolvimento Boleto Banco do Brasil: Daniel William Schultz / Leandro Maniezo|
// +---------------------------------------------------------------------------------+


$codigobanco = "001";
$codigo_banco_com_dv = b001_geraCodigoBanco($codigobanco);

//agencia � sempre 4 digitos
$agencia = b001_formata_numero($this->numero_agencia,4,0);
//conta � sempre 8 digitos
$conta = b001_formata_numero($this->numero_conta,8,0);
//agencia e conta
$agencia_codigo = $agencia."-". b001_modulo_11($agencia) ." / ". $conta ."-". b001_modulo_11($conta);

/*
$nummoeda = "9";
$fator_vencimento = b001_fator_vencimento($this->data_vencimento);

//valor tem 10 digitos, sem virgula
$valor = b001_formata_numero($this->valor_duplicata,10,0,"valor");
//Zeros: usado quando convenio de 7 digitos
$livre_zeros='000000';

if ($this->formatacao_convenio!=8 and $this->formatacao_convenio!=7 and $this->formatacao_convenio!=6)
{
   echo "Formatacao do convenio deve ser 6,7 ou 8, mais esta definida como: ".$this->formatacao_convenio;
   exit;
}

// Carteira 18 com Conv�nio de 8 d�gitos
if ($this->formatacao_convenio == "8") {
	$convenio = b001_formata_numero($this->numero_convenio,8,0,"convenio");
	// Nosso n�mero de at� 9 d�gitos
	$nossonumero = b001_formata_numero($this->nosso_numero,9,0);
	$dv=b001_modulo_11("$codigobanco$nummoeda$fator_vencimento$valor$livre_zeros$convenio$nossonumero".$this->numero_carteira);
	$linha="$codigobanco$nummoeda$dv$fator_vencimento$valor$livre_zeros$convenio$nossonumero".$this->numero_carteira;
	//montando o nosso numero que aparecer� no boleto
	$nossonumero = $convenio . $nossonumero ."-". b001_modulo_11($convenio.$nossonumero);
}

// Carteira 18 com Conv�nio de 7 d�gitos
if ($this->formatacao_convenio == "7") {
	$convenio = b001_formata_numero($this->numero_convenio,7,0,"convenio");
	// Nosso n�mero de at� 10 d�gitos
	$nossonumero = b001_formata_numero($this->nosso_numero,10,0);
	$dv=b001_modulo_11("$codigobanco$nummoeda$fator_vencimento$valor$livre_zeros$convenio$nossonumero".$this->numero_carteira);
	$linha="$codigobanco$nummoeda$dv$fator_vencimento$valor$livre_zeros$convenio$nossonumero".$this->numero_carteira;
    $nossonumero = $convenio.$nossonumero;
	//N�o existe DV na composi��o do nosso-n�mero para conv�nios de sete posi��es
}

// Carteira 18 com Conv�nio de 6 d�gitos
if ($this->formatacao_convenio == "6") {
	$convenio = b001_formata_numero($this->numero_convenio,6,0,"convenio");

	if ($this->formatacao_nosso_numero == "1") {

		// Nosso n�mero de at� 5 d�gitos
		$nossonumero = b001_formata_numero($this->nosso_numero,5,0);
		$dv = b001_modulo_11("$codigobanco$nummoeda$fator_vencimento$valor$convenio$nossonumero$agencia$conta".$this->numero_carteira);
		$linha = "$codigobanco$nummoeda$dv$fator_vencimento$valor$convenio$nossonumero$agencia$conta".$this->numero_carteira;
		//montando o nosso numero que aparecer� no boleto
		$nossonumero = $convenio . $nossonumero ."-". b001_modulo_11($convenio.$nossonumero);
	}

	if ($this->formatacao_nosso_numero == "2") {

		// Nosso n�mero de at� 17 d�gitos
		$nservico = "21";
		$nossonumero = b001_formata_numero($this->nosso_numero,17,0);
		$dv = b001_modulo_11("$codigobanco$nummoeda$fator_vencimento$valor$convenio$nossonumero$nservico");
		$linha = "$codigobanco$nummoeda$dv$fator_vencimento$valor$convenio$nossonumero$nservico";
	}
}

$linha = b001_monta_linha_digitavel($linha);

mysqli_query("update T117 set T117_Codigo_Barras='$linha' where T117_Id='$nT117_Id'");
*/
?>

	<div id="boleto">

    <table cellspacing=0 cellpadding=0 width=666 border=0><TBODY><TR><TD class=ct width=666><div align=right><b class=cp>Recibo do Pagador</b></div></TD></tr></tbody></table>
		<table class="header" border=0 cellspacing="0" cellpadding="0">
		<tbody>
		<tr>
			<td width=150><IMG SRC="imagens/logobb.jpg"></td>
			<td width=50>
        <div class="field_cod_banco"><?php echo $codigo_banco_com_dv?></div>
			</td>
			<td class="linha_digitavel"><span style='font-size:16px'><?php echo $this->linha_digitavel?></span></td>
		</tr>
		</tbody>
		</table>

		<table class="line" cellspacing="0" cellpadding="0">
		<tbody>
		<tr class="titulos">
			<td class="cedente">Benefici�rio</TD>
			<td class="ag_cod_cedente">Ag�ncia / C�digo do Beneficiario</td>
			<td class="especie">Esp�cie</TD>
			<td class="qtd">Quantidade</TD>
			<td class="nosso_numero">Nosso n�mero</td>
		</tr>

		<tr class="campos">
			<td class="cedente"><?php echo $this->nome_cedente; ?>&nbsp;</td>
			<td class="ag_cod_cedente"><?php echo $agencia_codigo ?> &nbsp;</td>
			<td class="especie"><?php echo $this->dados_especie; ?>&nbsp;</td>
			<TD class="qtd"><?php echo $this->dados_quantidade ?>&nbsp;</td>
			<TD class="nosso_numero"><?php echo $this->nosso_numero; ?>&nbsp;</td>
		</tr>
		</tbody>
		</table>


		<table class="line" cellspacing="0" cellpadding="0">
		<tbody>
		<tr class="titulos">
			<td class="sacado">Endere�o do Benefici�rio</td>
		</tr>
		<tr class="campos">
			<td class="sacado"><?php echo $this->endereco_cedente.' '.$this->bairro_cedente.' '.$this->cidade_cedente.' '.$this->estado_cedente.' - '.$this->cep_cedente?></td>
		</tr>
		</tbody>
		</table>


		<table class="line" cellspacing="0" cellPadding="0">
		<tbody>
		<tr class="titulos">
			<td class="num_doc">N�mero do documento</td>
			<td class="contrato">Contrato</TD>
			<td class="cpf_cei_cnpj">CPF/CEI/CNPJ</TD>
			<td class="vencmento">Vencimento</TD>
			<td class="valor_doc">Valor documento</TD>
		</tr>
		<tr class="campos">
			<td class="num_doc"><?php echo $this->numero_documento ?></td>
			<td class="contrato"><?php echo $this->numero_contrato?></td>
			<td class="cpf_cei_cnpj"><?php echo $this->cpf_cnpj_cedente?></td>
			<td class="vencimento"><?php echo $this->data_vencimento; ?></td>
			<td class="valor_doc"><?php echo $this->valor_duplicata?></td>
		</tr>
      </tbody>
      </table>

		<table class="line" cellspacing="0" cellPadding="0">
		<tbody>
		<tr class="titulos">
			<td class="desconto">(-) Desconto / Abatimento</td>
			<td class="outras_deducoes">(-) Outras dedu��es</td>
			<td class="mora_multa">(+) Mora / Multa</td>
			<td class="outros_acrescimos">(+) Outros acr�scimos</td>
			<td class="valor_cobrado">(=) Valor cobrado</td>
		</tr>
		<tr class="campos">
			<td class="desconto">&nbsp;</td>
			<td class="outras_deducoes">&nbsp;</td>
			<td class="mora_multa">&nbsp;</td>
			<td class="outros_acrescimos">&nbsp;</td>
			<td class="valor_cobrado">&nbsp;</td>
		</tr>
		</tbody>
		</table>

      
		<table class="line" cellspacing="0" cellpadding="0">
		<tbody>
		<tr class="titulos">
			<td class="sacado">Pagador</td>
		</tr>
		<tr class="campos">
			<td class="sacado"><?php echo $this->nome_sacado.' - '.$this->cpf_cnpj_sacado;?></td>
		</tr>
		</tbody>
		</table>
		
		<div class="footer">
			<p>Autentica��o mec�nica</p>
            <span class="campo">
  <?php

  $T002=mysqli_query("select T002_Numero_Duplicata,T002_Data_Vencimento,T002_Valor_Total from T002 where T002_T117_Id='$nT117_Id'");
  echo "<table border=0 width=300 cellspacing=1 bgcolor=black>";
   echo "<tr bgcolor=white>";
    echo "<td align=center>";
     echo "<font class=fonte>N�mero</font>";
    echo "</td>";
    echo "<td align=center>";
     echo "<font class=fonte>Vencimento</font>";
    echo "</td>";
    echo "<td align=right>";
     echo "<font class=fonte>Valor</font>";
    echo "</td>";
   echo "</tr>";
  while ($mT002=mysql_fetch_array($T002)) {
     echo "<tr bgcolor=white>";
      echo "<td align=center>";
       echo "<font class=fonte>$mT002[T002_Numero_Duplicata]</font>";
      echo "</td>";
      echo "<td align=center>";
       echo "<font class=fonte>".gCorrigeData($mT002['T002_Data_Vencimento'])."</font>";
      echo "</td>";
      echo "<td align=right>";
       echo "<font class=fonte>R$ $mT002[T002_Valor_Total]</font>";
      echo "</td>";
     echo "</tr>";
  }

  echo "</table>";

  ?>
            </span>
		</div>

		
		
		<div class="cut">
			<p>Corte na linha pontilhada</p>
		</div>


		<table class="header" border=0 cellspacing="0" cellpadding="0">
		<tbody>
		<tr>
			<td width=150><IMG SRC="imagens/logobb.jpg"></td>
			<td width=50>
        <div class="field_cod_banco"><?php echo $codigo_banco_com_dv?></div>
			</td>
			<td class="linha_digitavel"><span style='font-size:16px'><?php echo $this->linha_digitavel?></span></td>
		</tr>
		</tbody>
		</table>

		<table class="line" cellspacing="0" cellpadding="0">
		<tbody>
		<tr class="titulos">
			<td class="local_pagto">Local de pagamento</td>
			<td class="vencimento2">Vencimento</td>
		</tr>
		<tr class="campos">
			<td class="local_pagto">QUALQUER BANCO AT� O VENCIMENTO</td>
			<td class="vencimento2"><?php echo $this->data_vencimento; ?></td>
		</tr>
		</tbody>
		</table>
		
		<table class="line" cellspacing="0" cellpadding="0">Beneficiario
		<tbody>
		<tr class="titulos">
			<td class="cedente2">Beneficiario</td>
			<td class="ag_cod_cedente2">Ag�ncia/C�digo Benefici�rio</td>
		</tr>
		<tr class="campos">
			<td class="cedente2"><?php echo $this->nome_cedente; ?></td>
			<td class="ag_cod_cedente2"><?php echo $agencia_codigo; ?></td>
		</tr>
		</tbody>
		</table>

		<table class="line" cellspacing="0" cellpadding="0">
		<tbody>
		<tr class="titulos">
			<td class="data_doc">Data do documento</td>
			<td class="num_doc2">No. documento</td>
			<td class="especie_doc">Esp�cie doc.</td>
			<td class="aceite">Aceite</td>
			<td class="data_process">Data process.</td>
			<td class="nosso_numero2">Nosso n�mero</td>
		</tr>
		<tr class="campos">
			<td class="data_doc"><?php echo $this->getDataEmissaoBoleto()?></td>
			<td class="num_doc2"><?php echo $this->numero_documento ?></td>
			<td class="especie_doc"><?php echo $this->dados_especie_doc; ?></td>
			<td class="aceite"><?php echo $this->dados_aceite; ?></td>
			<td class="data_process"><?php echo $this->data_processamento; ?></td>
			<td class="nosso_numero2"><?php echo $this->nosso_numero; ?></td>
		</tr>
		</tbody>
		</table>

		<table class="line" cellspacing="0" cellPadding="0">
		<tbody>
		<tr class="titulos">
			<td class="reservado">Uso do  banco</td>
			<td class="carteira">Carteira</td>
			<td class="especie2">Esp�cie</td>
			<td class="qtd2">Quantidade</td>
			<td class="xvalor">x Valor</td>
			<td class="valor_doc2">(=) Valor documento</td>
		</tr>
		<tr class="campos">
			<td class="reservado"><?php echo $this->dados_uso_banco; ?></td>
   			<td class="carteira"><?php echo $this->numero_carteira; ?> <?php echo isset($dadosboleto["variacao_carteira"]) ? $dadosboleto["variacao_carteira"] : '&nbsp;' ?></td>
			<td class="especie2"><?php echo $this->dados_especie ?></td>
			<td class="qtd2"><?php echo $this->dados_quantidade ?></td>
			<td class="xvalor"><?php echo $this->dados_valor_unitario; ?></td>
			<td class="valor_doc2"><?php echo $this->valor_duplicata; ?></td>
		</tr>
		</tbody>
		</table>
		
		
		<table class="line" cellspacing="0" cellpadding="0">
		<tbody>
		<tr><td class="last_line" rowspan="6">
			<table class="line" cellspacing="0" cellpadding="0">
			<tbody>
			<tr class="titulos">
				<td class="instrucoes">
						Instru��es (Texto de responsabilidade do Beneficiario)
				</td>
			</tr>
			<tr class="campos">
				<td class="instrucoes" rowspan="5">
					<p><?php echo $this->instrucoes1; ?></p>
					<p><?php echo $this->instrucoes2; ?></p>
					<p><?php echo $this->instrucoes3; ?></p>
					<p><?php echo $this->instrucoes4; ?></p>
				</td>
			</tr>
			</tbody>
			</table>
		</td></tr>
		
		<tr><td>
			<table class="line" cellspacing="0" cellpadding="0">
			<tbody>
			<tr class="titulos">
				<td class="desconto2">(-) Desconto / Abatimento</td>
			</tr>
			<tr class="campos">
				<td class="desconto2">&nbsp;</td>
			</tr>
			</tbody>
			</table>
		</td></tr>
		
		<tr><td>
			<table class="line" cellspacing="0" cellpadding="0">
			<tbody>
			<tr class="titulos">
				<td class="outras_deducoes2">(-) Outras dedu��es</td>
			</tr>
			<tr class="campos">
				<td class="outras_deducoes2">&nbsp;</td>
			</tr>
			</tbody>
			</table>
		</td></tr>

		<tr><td>
			<table class="line" cellspacing="0" cellpadding="0">
			<tbody>
			<tr class="titulos">
				<td class="mora_multa2">(+) Mora / Multa</td>
			</tr>
			<tr class="campos">
				<td class="mora_multa2">&nbsp;</td>
			</tr>
			</tbody>
			</table>
		</td></tr>

		<tr><td>
			<table class="line" cellspacing="0" cellpadding="0">
			<tbody>
			<tr class="titulos">
				<td class="outros_acrescimos2">(+) Outros Acr�scimos</td>
			</tr>
			<tr class="campos">
				<td class="outros_acrescimos2">&nbsp;</td>
			</tr>
			</tbody>
			</table>
		</td></tr>

		<tr><td class="last_line">
			<table class="line" cellspacing="0" cellpadding="0">
			<tbody>
			<tr class="titulos">
				<td class="valor_cobrado2">(=) Valor cobrado</td>
			</tr>
			<tr class="campos">
				<td class="valor_cobrado2">&nbsp;</td>
			</tr>
			</tbody>
			</table>
		</td></tr>
		</tbody>
		</table>
		
		
		<table class="line" cellspacing="0" cellPadding="0">
		<tbody>
		<tr class="titulos">
			<td class="sacado2">Pagador</td>
		</tr>
		<tr class="campos">
			<td class="sacado2">
				<p><?php echo $this->nome_sacado.' - '.$this->cpf_cnpj_sacado; ?></p>
				<p><?php echo $this->endereco_sacado?></p>
				<p><?php echo $this->getEndereco2Sacado()?></p>
			</td>
		</tr>
		</tbody>
		</table>		
		
		<table class="line" cellspacing="0" cellpadding="0">
		<tbody>
		<tr class="titulos">
			<td class="sacador_avalista" colspan="2">Pagador/Avalista</td>
		</tr>
		<tr class="campos">
			<td class="sacador_avalista">&nbsp;</td>
			<td class="cod_baixa">C�d. baixa</td>
		</tr>
		</tbody>
		</table>		
    <table cellspacing=0 cellpadding=0 width=666 border=0><TBODY><TR><TD width=666 align=right ><font style="font-size: 10px;">Autentica��o mec�nica - Ficha de Compensa��o</font></TD></tr></tbody></table>
		<div class="barcode">
			<p><?php fbarcode001($this->codigo_barras); ?></p>
		</div>
		<div class="cut">
			<p>Corte na linha pontilhada</p>
		</div>

	</div>

</body>

</html>
