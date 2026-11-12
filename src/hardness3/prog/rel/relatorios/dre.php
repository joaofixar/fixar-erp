<?php
namespace hardness;

$Empresa = $_REQUEST['Empresa'];
$Cliente = $_REQUEST['Cliente'];
$empresasGrupo = $_REQUEST['empresasGrupo'];

set_time_limit(0);

?>

<html>
<head>
<title>DRE</title>
 
<meta name='ROBOTS' content='NOINDEX,NOFOLLOW'>  <meta http-equiv='Pragma' content='no-cache'>  <meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1'>
<link rel=stylesheet href="/hardness/classes/classe_tamanho_medio_firefox.css" type="text/css">
<!-- Funções Genéricas em Javascript -->
<script src='/hardness/func/funcoes_genericas.js'></script>
<script src='/hardness/func/ajax.js'></script>

<STYLE TYPE="text/css"><!--
  A:link {text-decoration:none; color:000000}
  A:visited {text-decoration:none; color:000000}
  A:hover {text-decoration:underline; color:000000; cursor:hand}
  A:active {text-decoration:underline; color:000000; cursor:hand}

-->

<style>
.fonte
{
 font-size:12px;
}
</style>
</head>
<body bottommargin=0 topmargin=0 rightmargin=0 leftmargin=0 bgcolor='white'>

<?php

if (empty($Data)) {
   $Data=date("Y-m-d");
}

$xData=$Data;

$Data=gCorrigeDataInverte($Data);
$Data_Inicial=gCorrigeDataInverte($_REQUEST['Data_Inicial']);
$Data_Final=gCorrigeDataInverte($_REQUEST['Data_Final']);

$filtroC004='';

if (!empty($Empresa)) {
	$Empresa = explode(";", $Empresa);
	foreach ($Empresa as $value) {
		$value=trim($value);
		if (substr($value, 0, 1)=='!') {
			if (empty($filtroC004)){
			    $filtroC004=' where (';
			} else {
			    $filtroC004.=' and ';
			}
			$value = str_replace("!", "", $value);
		    $filtroC004 .= "C004_Nome_Abreviado not like '%$value%' ";
		} else {
			if (empty($filtroC004)){
			    $filtroC004=' where (';
			} else {
			    $filtroC004.=' or ';
			}
		    $filtroC004 .= "C004_Nome_Abreviado like '%$value%' ";
		}
	}
	$filtroC004 .=') ';
}

$filtro='';

if (!empty($Cliente)) {
	$Cliente = explode(";", $Cliente);
	foreach ($Cliente as $value) {
		$value=trim($value);
		if (substr($value, 0, 1)=='!') {
			if (empty($filtro)){
			    $filtro=' and (';
			} else {
			    $filtro.=' and ';
			}
			$value = str_replace("!", "", $value);
		    $filtro .= "D024_Nome_Empresa not like '%$value%' ";
		} else {
			if (empty($filtro)){
			    $filtro=' and (';
			} else {
			    $filtro.=' or ';
			}
		    $filtro .= "D024_Nome_Empresa like '%$value%' ";
		}
	}
	$filtro .=') ';
}

if ($empresasGrupo=='N'){
	$filtro .= " and D024_Flag_Empresas_Grupo='$empresasGrupo' ";
}

/*
if (!empty($CFOP)) {
	$CFOP = explode(";", $CFOP);
	foreach ($CFOP as $value) {
		$value=trim($value);
		if (substr($value, 0, 1)=='!') {
			if (empty($filtro)){
			    $filtro=' where (';
			} else {
			    $filtro.=' and ';
			}
			$value = str_replace("!", "", $value);
		    $filtro .= "D006_Descricao not like '%$value%' ";
		} else {
			if (empty($filtro)){
			    $filtro=' where (';
			} else {
			    $filtro.=' or ';
			}
		    $filtro .= "D006_Descricao like '%$value%' ";
		}
	}
	$filtro .=') '
}
*/

// Busca os dados da empresa usu?ia
$C004=mysql_query("select * from C004 $filtroC004 Order by C004_Id");

$retorno = <<<EOT

<style type="text/css">
.td{
	font-weight: normal;
	font-size: 14px;
	font-family: arial, verdana;
	background: #f4f4f4;
	padding: 6px 6px 6px 6px;
}
</style>
  
  <table cellspacing=1px cellpadding=1px><tr><td valign=top>

	<table valign=top align=center width=190px>
		<tr>
			<td class='td' colspan=2 style='background:lightblue;font-weight:bold' align=center>
	            D.R.E.
			</td>
		</tr>
		<tr>
			<td class='td' style='background:#DDEAEC;font-weight:bold;' height=20px>
	            (+) Receitas
			</td>
		</tr>
		<tr>
			<td class='td'>
	            (-) Impostos
			</td>
		</tr>
		<tr>
			<td class='td'>
	            (-) Devolucoes
			</td>
		</tr>
		<tr>
			<td class='td' style='background:#DDEAEC;font-weight:bold'>
	            (=) Receita liquida
			</td>
		</tr>
		<tr>
			<td class='td'>
	            (-) Custo produtos
			</td>
		</tr>
		<tr>
			<td class='td' style='background:#DDEAEC;font-weight:bold'>
	            (=) Resultado bruto
			</td>
		</tr>
		<tr>
			<td class='td'>
	            (-) Despesa venda
			</td>
		</tr>
		<tr>
			<td class='td'>
	            (-) Despesa administrativa
			</td>
		</tr>
		<tr>
			<td class='td' style='background:#DDEAEC;font-weight:bold'>
	            (=) Resultado antes IRPJ 
			</td>
		</tr>
		<tr>
			<td class='td'>
	            (-) Provisao IRPJ
			</td>
		</tr>
		<tr>
			<td class='td' style='background:#DDEAEC;font-weight:bold'>
	            (=) Lucro bruto
			</td>
		</tr>
		<tr>
			<td class='td'>
	            (-) Participacao socios
			</td>
		</tr>
		<tr>
			<td class='td' style='background:#DDEAEC;font-weight:bold'>
	            (=) Lucro liquido
			</td>
		</tr>
	</table>
  </td>
EOT;


while ($mC004=mysql_fetch_array($C004)) {

	$retorno .= "<td>";

	$filtroEmpresa = $filtro . " AND C004_Id='{$mC004['C004_Id']}'";

	$mArq = explode(".", $mC004['C004_Logo_Marca']);
	$nExt = count($mArq) - 1;
	$cExtensao_Arquivo = $mArq[$nExt];
	$Arquivo_Logo = "/hardness3/".$g['pathDados'] . "/imagens/logo/{$mC004[C004_Id]}.";

	$Arquivo_Logo.=$cExtensao_Arquivo;

	if (empty($cExtensao_Arquivo)){
		$Arquivo_Logo='';
	}

	$Receitas = mysql_query("select sum(T007_Valor_Total) 
		                       from T007
		                  left join D006 on D006_Id=T007_D006_Id
		                  left join C004 on C004_Id=T007_C004_Id
		                  left join D024 on D024_Id=T007_D024_Id
		                      where T007_Data_Emissao>='$Data_Inicial'
		                        and T007_Data_Emissao<='$Data_Final'
		                        and D006_Flag_Venda_Compra_Outros='V'
		                        and T007_Flag_Cancelada!='S'
		                        and D006_Flag_Devolucao!='S'
		                        $filtroEmpresa");
	$mReceitas = mysql_fetch_array($Receitas);

	/*
	if ($mReceitas[0]<=0){
		$Receitas = mysql_query("select sum(T002_Valor_Recebido) 
		                       from T002
		                      where T002_Data_Recebimento>='$Data_Inicial'
		                        and T002_Data_Recebimento<='$Data_Final'");
		$mReceitas = mysql_fetch_array($Receitas);
	}
	*/

	$valorReceitas = gCorrigeNumero($mReceitas[0]);

	$impostos = mysql_query("select sum(T007_Valor_Total_ICMS+T007_Valor_Total_IPI+T007_Valor_ICMS_Substituicao_Retencao+T007_Valor_Total_PIS+T007_Valor_Total_COFINS) 
		                       from T007
		                  left join D006 on D006_Id=T007_D006_Id
		                  left join C004 on C004_Id=T007_C004_Id
		                  left join D024 on D024_Id=T007_D024_Id
		                      where T007_Data_Emissao>='$Data_Inicial'
		                        and T007_Data_Emissao<='$Data_Final'
		                        and D006_Flag_Venda_Compra_Outros='V'
		                        and T007_Flag_Cancelada!='S'
		                        and D006_Flag_Devolucao!='S'
		                        $filtroEmpresa");
	$mImpostos = mysql_fetch_array($impostos);

	/*if ($mImpostos[0]<=0){
		$impostos = mysql_query("select sum(T019_Valor_Lancamento) *-1 
			          	               from T019
			          	          left join D014 on D014_Id=T019_D014_Id
			                	      where T019_Data_Lancamento>='$Data_Inicial'
			                    	    and T019_Data_Lancamento<='$Data_Final'
			                        	and D014_Grupo_DRE = '4'");
		$mImpostos = mysql_fetch_array($impostos);
	}
	*/

	$valorImpostos = gCorrigeNumero($mImpostos[0]);

	$devolucaoEmitida = mysql_query("select sum(T007_Valor_Total) 
		                       from T007
		                  left join D006 on D006_Id=T007_D006_Id
		                  left join C004 on C004_Id=T007_C004_Id
		                  left join D024 on D024_Id=T007_D024_Id
		                      where T007_Data_Emissao>='$Data_Inicial'
		                        and T007_Data_Emissao<='$Data_Final'
		                        and D006_Flag_Entrada_Saida='E'
		                        and T007_Flag_Cancelada!='S'
		                        and D006_Flag_Devolucao='S'
		                        $filtroEmpresa");
	$mDevolucaoEmitida = mysql_fetch_array($devolucaoEmitida);
	$valorDevolucoes = $mDevolucaoEmitida[0];

	$devolucaoRecebida = mysql_query("select sum(T013_Valor_Total) 
		                       from T013
		                  left join D006 on D006_Id=T013_D006_Id
		                  left join C004 on C004_Id=T013_C004_Id
		                  left join D024 on D024_Id=T013_D024_Id
		                      where T013_Data_Entrada>='$Data_Inicial'
		                        and T013_Data_Entrada<='$Data_Final'
		                        and D006_Flag_Entrada_Saida='E'
		                        and T013_Flag_Cancelada!='S'
		                        and D006_Flag_Devolucao='S'
		                        $filtroEmpresa");
	$mDevolucaoRecebida = mysql_fetch_array($devolucaoRecebida);
	$valorDevolucoes += $mDevolucaoRecebida[0];

	$valorDevolucoes = gCorrigeNumero($valorDevolucoes);

	$valorReceitaLiquida = gCorrigeNumero( gCorrigeNumeroInverte($valorReceitas) - gCorrigeNumeroInverte($valorImpostos) - gCorrigeNumeroInverte($valorDevolucoes) );

	$custoProdutos = mysql_query("select sum(T008_Valor_Total_Custo) 
		          	               from T008
		          	          left join T007 on T007_Id=T008_T007_Id
		            	      left join D006 on D006_Id=T007_D006_Id
			                  left join C004 on C004_Id=T007_C004_Id
			                  left join D024 on D024_Id=T007_D024_Id
		                	      where T007_Data_Emissao>='$Data_Inicial'
		                    	    and T007_Data_Emissao<='$Data_Final'
		                        	and D006_Flag_Venda_Compra_Outros='V'
			                        and T007_Flag_Cancelada!='S'
		                        	and D006_Flag_Devolucao!='S'
		                        	$filtroEmpresa");
	$mCustoProdutos = mysql_fetch_array($custoProdutos);
	$valorCustoProdutos = gCorrigeNumero($mCustoProdutos[0]);

	$valorResultadoBruto = gCorrigeNumero( gCorrigeNumeroInverte($valorReceitaLiquida) - gCorrigeNumeroInverte($valorCustoProdutos) );

	$despesasVenda = mysql_query("select sum(T019_Valor_Lancamento) *-1 
		          	               from T019
		          	          left join D014 on D014_Id=T019_D014_Id
		          	          left join C004 on C004_Id=T019_C004_Id
			                  left join D024 on D024_Id=T019_D024_Id
		                	      where T019_Data_Lancamento>='$Data_Inicial'
		                    	    and T019_Data_Lancamento<='$Data_Final'
		                        	and D014_Grupo_DRE = '1'
		                        	$filtroEmpresa");
	$mDespesasVenda = mysql_fetch_array($despesasVenda);
	$valorDespesasVenda = gCorrigeNumero($mDespesasVenda[0]);

	$despesasAdministrativa = mysql_query("select sum(T019_Valor_Lancamento) *-1 
		          	               from T019
		          	          left join D014 on D014_Id=T019_D014_Id
		          	          left join C004 on C004_Id=T019_C004_Id
			                  left join D024 on D024_Id=T019_D024_Id
		                	      where T019_Data_Lancamento>='$Data_Inicial'
		                    	    and T019_Data_Lancamento<='$Data_Final'
		                        	and D014_Grupo_DRE = '2'
		                        	$filtroEmpresa");
	$mDespesasAdministrativa = mysql_fetch_array($despesasAdministrativa);
	$valorDespesasAdministrativa = gCorrigeNumero($mDespesasAdministrativa[0]);

	$valorResultadoAntesIRPJ = gCorrigeNumero( gCorrigeNumeroInverte($valorResultadoBruto) - ( gCorrigeNumeroInverte($valorDespesasVenda) + gCorrigeNumeroInverte($valorDespesasAdministrativa) ) );

	$valorProvisaoIRPJ = gCorrigeNumero( 0 );

	$valorLucroBruto = gCorrigeNumero( gCorrigeNumeroInverte($valorResultadoAntesIRPJ) - gCorrigeNumeroInverte($valorProvisaoIRPJ) );

	$despesasParticipacao = mysql_query("select sum(T019_Valor_Lancamento) *-1 
		          	               from T019
		          	          left join D014 on D014_Id=T019_D014_Id
		          	          left join C004 on C004_Id=T019_C004_Id
			                  left join D024 on D024_Id=T019_D024_Id
		                	      where T019_Data_Lancamento>='$Data_Inicial'
		                    	    and T019_Data_Lancamento<='$Data_Final'
		                        	and D014_Grupo_DRE = '3'
		                        	$filtroEmpresa");
	$mDespesasParticipacao = mysql_fetch_array($despesasParticipacao);
	$valorDespesasParticipacao = gCorrigeNumero($mDespesasParticipacao[0]);

	$valorLucroLiquido = gCorrigeNumero( gCorrigeNumeroInverte($valorLucroBruto) - gCorrigeNumeroInverte($valorDespesasParticipacao) );

	$retorno .= <<<EOT
		<table valign=top align=center width=120px>
			<tr>
				<td class='td' colspan=2 style='background:lightblue;font-weight:bold' align=center>
		            {$mC004['C004_Nome_Abreviado']}
				</td>
			</tr>
			<tr>
				<td class='td' align=right style='background:#DDEAEC;font-weight:bold'>
		            $valorReceitas
				</td>
			</tr>
			<tr>
				<td class='td' align=right>
		            $valorImpostos
				</td>
			</tr>
			<tr>
				<td class='td' align=right>
		            $valorDevolucoes
				</td>
			</tr>
			<tr>
				<td class='td' align=right style='background:#DDEAEC;font-weight:bold'>
		            $valorReceitaLiquida
				</td>
			</tr>
			<tr>
				<td class='td' align=right>
		            $valorCustoProdutos
				</td>
			</tr>
			<tr>
				<td class='td' align=right style='background:#DDEAEC;font-weight:bold'>
		            $valorResultadoBruto
				</td>
			</tr>
			<tr>
				<td class='td' align=right>
		            $valorDespesasVenda
				</td>
			</tr>
			<tr>
				<td class='td' align=right>
		            $valorDespesasAdministrativa
				</td>
			</tr>
			<tr>
				<td class='td' align=right style='background:#DDEAEC;font-weight:bold'>
		            $valorResultadoAntesIRPJ
				</td>
			</tr>
			<tr>
				<td class='td' align=right>
		            $valorProvisaoIRPJ
				</td>
			</tr>
			<tr>
				<td class='td' align=right style='background:#DDEAEC;font-weight:bold'>
		            $valorLucroBruto
				</td>
			</tr>
			<tr>
				<td class='td' align=right>
		            $valorDespesasParticipacao
				</td>
			</tr>
			<tr>
				<td class='td' align=right style='background:#DDEAEC;font-weight:bold'>
		            $valorLucroLiquido
				</td>
			</tr>
		</table>
EOT;

	$retorno .= "</td>";

}

$retorno .= "<td>";

$filtroTotal = str_replace('where','and (',$filtroC004).') ';
$filtroTotal .= $filtro;

$Arquivo_Logo='';

$Receitas = mysql_query("select sum(T007_Valor_Total) 
	                       from T007
	                  left join D006 on D006_Id=T007_D006_Id
	                  left join C004 on C004_Id=T007_C004_Id
	                  left join D024 on D024_Id=T007_D024_Id
	                      where T007_Data_Emissao>='$Data_Inicial'
	                        and T007_Data_Emissao<='$Data_Final'
	                        and D006_Flag_Venda_Compra_Outros='V'
	                        and T007_Flag_Cancelada!='S'
	                        and D006_Flag_Devolucao!='S'
	                        $filtroTotal");
$mReceitas = mysql_fetch_array($Receitas);

/*
if ($mReceitas[0]<=0){
	$Receitas = mysql_query("select sum(T002_Valor_Recebido) 
	                       from T002
	                      where T002_Data_Recebimento>='$Data_Inicial'
	                        and T002_Data_Recebimento<='$Data_Final'");
	$mReceitas = mysql_fetch_array($Receitas);
}
*/

$valorReceitas = gCorrigeNumero($mReceitas[0]);

$impostos = mysql_query("select sum(T007_Valor_Total_ICMS+T007_Valor_Total_IPI+T007_Valor_ICMS_Substituicao_Retencao+T007_Valor_Total_PIS+T007_Valor_Total_COFINS) 
	                       from T007
	                  left join D006 on D006_Id=T007_D006_Id
	                  left join C004 on C004_Id=T007_C004_Id
	                  left join D024 on D024_Id=T007_D024_Id
	                      where T007_Data_Emissao>='$Data_Inicial'
	                        and T007_Data_Emissao<='$Data_Final'
	                        and D006_Flag_Venda_Compra_Outros='V'
	                        and T007_Flag_Cancelada!='S'
	                        and D006_Flag_Devolucao!='S'
	                        $filtroTotal");
$mImpostos = mysql_fetch_array($impostos);

/*if ($mImpostos[0]<=0){
	$impostos = mysql_query("select sum(T019_Valor_Lancamento) *-1 
		          	               from T019
		          	          left join D014 on D014_Id=T019_D014_Id
		                	      where T019_Data_Lancamento>='$Data_Inicial'
		                    	    and T019_Data_Lancamento<='$Data_Final'
		                        	and D014_Grupo_DRE = '4'");
	$mImpostos = mysql_fetch_array($impostos);
}
*/

$valorImpostos = gCorrigeNumero($mImpostos[0]);

$devolucaoEmitida = mysql_query("select sum(T007_Valor_Total) 
	                       from T007
	                  left join D006 on D006_Id=T007_D006_Id
	                  left join C004 on C004_Id=T007_C004_Id
	                  left join D024 on D024_Id=T007_D024_Id
	                      where T007_Data_Emissao>='$Data_Inicial'
	                        and T007_Data_Emissao<='$Data_Final'
	                        and D006_Flag_Entrada_Saida='E'
	                        and T007_Flag_Cancelada!='S'
	                        and D006_Flag_Devolucao='S'
	                        $filtroTotal");
$mDevolucaoEmitida = mysql_fetch_array($devolucaoEmitida);
$valorDevolucoes = $mDevolucaoEmitida[0];

$devolucaoRecebida = mysql_query("select sum(T013_Valor_Total) 
	                       from T013
	                  left join D006 on D006_Id=T013_D006_Id
	                  left join C004 on C004_Id=T013_C004_Id
	                  left join D024 on D024_Id=T013_D024_Id
	                      where T013_Data_Entrada>='$Data_Inicial'
	                        and T013_Data_Entrada<='$Data_Final'
	                        and D006_Flag_Entrada_Saida='E'
	                        and T013_Flag_Cancelada!='S'
	                        and D006_Flag_Devolucao='S'
	                        $filtroTotal");
$mDevolucaoRecebida = mysql_fetch_array($devolucaoRecebida);
$valorDevolucoes += $mDevolucaoRecebida[0];

$valorDevolucoes = gCorrigeNumero($valorDevolucoes);

$valorReceitaLiquida = gCorrigeNumero( gCorrigeNumeroInverte($valorReceitas) - gCorrigeNumeroInverte($valorImpostos) - gCorrigeNumeroInverte($valorDevolucoes) );

$custoProdutos = mysql_query("select sum(T008_Valor_Total_Custo) 
	          	               from T008
	          	          left join T007 on T007_Id=T008_T007_Id
	            	      left join D006 on D006_Id=T007_D006_Id
		                  left join C004 on C004_Id=T007_C004_Id
		                  left join D024 on D024_Id=T007_D024_Id
	                	      where T007_Data_Emissao>='$Data_Inicial'
	                    	    and T007_Data_Emissao<='$Data_Final'
	                        	and D006_Flag_Venda_Compra_Outros='V'
		                        and T007_Flag_Cancelada!='S'
	                        	and D006_Flag_Devolucao!='S'
	                        	$filtroTotal");
$mCustoProdutos = mysql_fetch_array($custoProdutos);
$valorCustoProdutos = gCorrigeNumero($mCustoProdutos[0]);

$valorResultadoBruto = gCorrigeNumero( gCorrigeNumeroInverte($valorReceitaLiquida) - gCorrigeNumeroInverte($valorCustoProdutos) );

$despesasVenda = mysql_query("select sum(T019_Valor_Lancamento) *-1 
	          	               from T019
	          	          left join D014 on D014_Id=T019_D014_Id
	          	          left join C004 on C004_Id=T019_C004_Id
		                  left join D024 on D024_Id=T019_D024_Id
	                	      where T019_Data_Lancamento>='$Data_Inicial'
	                    	    and T019_Data_Lancamento<='$Data_Final'
	                        	and D014_Grupo_DRE = '1'
	                        	$filtroTotal");
$mDespesasVenda = mysql_fetch_array($despesasVenda);
$valorDespesasVenda = gCorrigeNumero($mDespesasVenda[0]);

$despesasAdministrativa = mysql_query("select sum(T019_Valor_Lancamento) *-1 
	          	               from T019
	          	          left join D014 on D014_Id=T019_D014_Id
	          	          left join C004 on C004_Id=T019_C004_Id
		                  left join D024 on D024_Id=T019_D024_Id
	                	      where T019_Data_Lancamento>='$Data_Inicial'
	                    	    and T019_Data_Lancamento<='$Data_Final'
	                        	and D014_Grupo_DRE = '2'
	                        	$filtroTotal");
$mDespesasAdministrativa = mysql_fetch_array($despesasAdministrativa);
$valorDespesasAdministrativa = gCorrigeNumero($mDespesasAdministrativa[0]);

$valorResultadoAntesIRPJ = gCorrigeNumero( gCorrigeNumeroInverte($valorResultadoBruto) - ( gCorrigeNumeroInverte($valorDespesasVenda) + gCorrigeNumeroInverte($valorDespesasAdministrativa) ) );

$valorProvisaoIRPJ = gCorrigeNumero( 0 );

$valorLucroBruto = gCorrigeNumero( gCorrigeNumeroInverte($valorResultadoAntesIRPJ) - gCorrigeNumeroInverte($valorProvisaoIRPJ) );

$despesasParticipacao = mysql_query("select sum(T019_Valor_Lancamento) *-1 
	          	               from T019
	          	          left join D014 on D014_Id=T019_D014_Id
	          	          left join C004 on C004_Id=T019_C004_Id
		                  left join D024 on D024_Id=T019_D024_Id
	                	      where T019_Data_Lancamento>='$Data_Inicial'
	                    	    and T019_Data_Lancamento<='$Data_Final'
	                        	and D014_Grupo_DRE = '3'
	                        	$filtroTotal");
$mDespesasParticipacao = mysql_fetch_array($despesasParticipacao);
$valorDespesasParticipacao = gCorrigeNumero($mDespesasParticipacao[0]);

$valorLucroLiquido = gCorrigeNumero( gCorrigeNumeroInverte($valorLucroBruto) - gCorrigeNumeroInverte($valorDespesasParticipacao) );

$retorno .= <<<EOT
	<table valign=top align=center width=120px class='ui-state-default ui-corner-all'>
		<tr>
			<td class='td' colspan=2 style='background:lightblue;font-weight:bold' align=center>
	            TOTAL
			</td>
		</tr>
		<tr>
			<td class='td' align=right style='background:#DDEAEC;font-weight:bold'>
	            $valorReceitas
			</td>
		</tr>
		<tr>
			<td class='td' align=right>
	            $valorImpostos
			</td>
		</tr>
		<tr>
			<td class='td' align=right>
	            $valorDevolucoes
			</td>
		</tr>
		<tr>
			<td class='td' align=right style='background:#DDEAEC;font-weight:bold'>
	            $valorReceitaLiquida
			</td>
		</tr>
		<tr>
			<td class='td' align=right>
	            $valorCustoProdutos
			</td>
		</tr>
		<tr>
			<td class='td' align=right style='background:#DDEAEC;font-weight:bold'>
	            $valorResultadoBruto
			</td>
		</tr>
		<tr>
			<td class='td' align=right>
	            $valorDespesasVenda
			</td>
		</tr>
		<tr>
			<td class='td' align=right>
	            $valorDespesasAdministrativa
			</td>
		</tr>
		<tr>
			<td class='td' align=right style='background:#DDEAEC;font-weight:bold'>
	            $valorResultadoAntesIRPJ
			</td>
		</tr>
		<tr>
			<td class='td' align=right>
	            $valorProvisaoIRPJ
			</td>
		</tr>
		<tr>
			<td class='td' align=right style='background:#DDEAEC;font-weight:bold'>
	            $valorLucroBruto
			</td>
		</tr>
		<tr>
			<td class='td' align=right>
	            $valorDespesasParticipacao
			</td>
		</tr>
		<tr>
			<td class='td' align=right style='background:#DDEAEC;font-weight:bold'>
	            $valorLucroLiquido
			</td>
		</tr>
	</table>
EOT;

$retorno .= "</td>";

$retorno .= "</tr></table>";

echo $retorno;

?>



