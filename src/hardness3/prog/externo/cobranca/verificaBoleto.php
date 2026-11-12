<?php
namespace hardness;
/*
*	SCRIPT DE VERIFICAÇÃO DE BOLETOS ATRASADOS
*	OS BOLETOS SÃO VERIFICADOS NA BASE DA HARDNESS
*	CASO O CLIENTE SEJA DE UM REPRESENTANTE, VERIFICAMOS BOLETOS DO REPRESENTANTE TAMBÉM
*/
include_once("../../../configuracoes/sysConfig.php");
require_once('../../../bibliotecas/funcoesGerais.php');

$Cnpj_Empresa_Usuaria=$_GET['i'];
$Local=$_GET['Local'];
$Usuario_Atual=$_GET['Usuario_Atual'];

if (strlen($Cnpj_Empresa_Usuaria)==14)
{
    $Cnpj_Empresa_Usuaria=md5($Cnpj_Empresa_Usuaria);
}

$servidor = (strpos($sys['urlEmpresa'], 'http') === false) ? 'http://'.$sys['urlEmpresa'] : $sys['urlEmpresa'];
//$servidor="http://bug_fabio";

//-- CONEXAO DIRETA COM O BANCO DE DADOS
$con =@mysqli_connect($sys['dbHost'], $sys['dbUser'], $sys['dbPass'],$sys['dbDatabase']);
// $dbs = @mysql_select_db($sys['dbDatabase'], $con) or die (mysql_error());
$g['conexaoBanco'] = $con;
$key = "#hard!";
$D024 = mysql_fetch_assoc(mysql_query("SELECT D024_Id FROM D024 WHERE md5(D024_Cnpj) = '{$Cnpj_Empresa_Usuaria}'"));
$D024_Id = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $D024['D024_Id'], MCRYPT_MODE_CBC, md5(md5($key))));

echo <<<EOT
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	</head>
	<link rel=stylesheet href="{$servidor}/hardness/classes/classe_tamanho_medio_firefox.css" type="text/css">
	<style type="text/css">
		a:link {text-decoration:none; color:000000}
		a:visited {text-decoration:none; color:000000}
		a:hover {text-decoration:none; color:000000; background-color=none; cursor=hand}
		a:active {text-decoration:none; color:000000; background-color=none; cursor=hand}
	</style>
	<body topmargin=0 leftmargin=0>
EOT;

// Função generica, Troca o formado da data para dd-mm-aaaa
function Troca_Data_dma($data,$separador='/') {
    $mdata[0]=substr($data,8,2);
    $mdata[1]=substr($data,5,2);
    $mdata[2]=substr($data,0,4);
    if ((!empty($data)) and ($data!='0000-00-00')) {
       $result=implode($separador,$mdata);
       return $result;
    }
}
$tem_boleto = 'N';
if (!empty($Cnpj_Empresa_Usuaria)) {
	$sqlC004 = mysql_query("SELECT * FROM C004 WHERE C004_Id = '1'");
	$rowC004 = mysql_fetch_assoc($sqlC004);

	//Select dos boletos do cliente
	$cClausula  = " select ";
	$cClausula .= " T117_Id, ";
	$cClausula .= " T002_Data_Vencimento, ";
	$cClausula .= " D024_D046_Id, ";
	$cClausula .= " datediff(current_date(),T002_Data_Vencimento) dias ";
	$cClausula .= " from T117 ";
	$cClausula .= " left join D024 on D024_Id=T117_D024_Id ";
	$cClausula .= " left join T002 on T002_T117_Id=T117_Id ";
	$cClausula .= " left join D007 on D007_Id=T117_D007_Id ";
	$cClausula .= " where md5(D024_Cnpj)='".$Cnpj_Empresa_Usuaria."' ";
	$cClausula .= " and T117_Flag_Cancelado!='S'";
	$cClausula .= " AND (T002_T002_Id_Agrupado<=0 OR T002_T002_Id_Agrupado IS NULL)";
	$cClausula .= " order by T117_Data_Vencimento desc";
	$T117=mysql_query($cClausula);
	$mT117 = mysql_fetch_array($T117);
	echo mysql_error();

	//Verifica se os clientes tipo "NF-E" não possuem boleto ou tem boletos vencidos a mais de 366 dias
	if ((mysql_num_rows($T117) <= 0 || $mT117['dias'] > 366) && ($mT117['D024_D046_Id'] == '2')) {
	    echo "<br><br><br><font style='font-size:18;font-weight:bold;color:red;background:white'>EXISTEM PENDENCIAS EM SEU CADASTRO, FAVOR ENTRAR EM CONTATO COM NOSSO SUPORTE PELO TELEFONE {$rowC004['C004_DDD_Telefone']} {$rowC004['C004_Telefone']}.</font>";
	    exit;
	}

	//Select dos boletos do cliente
	$cClausula  = " select ";
	$cClausula .= " T117_Id, ";
	$cClausula .= " T117_D024_Id, ";
	$cClausula .= " T117_Data_Emissao, ";
	$cClausula .= " T117_Data_Vencimento, ";
	$cClausula .= " T117_Valor_Total, ";
	$cClausula .= " T117_Nosso_Numero,";
	$cClausula .= " T117_Flag_Cancelado,";
	$cClausula .= " T002_Data_Vencimento, ";
	$cClausula .= " D007_Nome_Banco, ";
	$cClausula .= " datediff(T117_Data_Vencimento,current_date()) as Dias_Vencer, ";
	$cClausula .= " datediff(current_date(),T002_Data_Vencimento) as Dias_Vencido, ";
	$cClausula .= " datediff(T117_Data_Vencimento,T002_Data_Vencimento) as Dias_Prorrogado ";
	$cClausula .= " from T117 ";
	$cClausula .= " left join D024 on D024_Id=T117_D024_Id ";
	$cClausula .= " left join T002 on T002_T117_Id=T117_Id ";
	$cClausula .= " left join D007 on D007_Id=T117_D007_Id ";
	$cClausula .= " where md5(D024_Cnpj)='".$Cnpj_Empresa_Usuaria."' ";
	$cClausula .= " and datediff(T002_Data_Vencimento,current_date())>=0 ";
	$cClausula .= " and datediff(T002_Data_Vencimento,current_date())<=30 ";
	$cClausula .= " and T117_Flag_Cancelado='N' ";
	$cClausula .= " and T002_Data_Recebimento='0000-00-00' ";
	$cClausula .= " AND (T002_T002_Id_Agrupado<=0 OR T002_T002_Id_Agrupado IS NULL)";
	$cClausula .= " order by T117_Data_Vencimento";
	$cSql_T117=mysql_query($cClausula);

   	echo mysql_error();
   	if (mysql_num_rows($cSql_T117) > 0){
   		//Impressão dos boletos do cliente
        while ($mT117 = mysql_fetch_array($cSql_T117)) {
		    echo <<<EOT
		    	<div id="divBoleto" style="border: 0px; width: 550px; height: 130px; font-size: 14px; margin-top: 10px; padding: 0px; font-family: Arial; text-align: justify">
		    	<table width=512px border=0 cellpadding=5 bgcolor=white style='border: 1px dashed green;' align=left>
EOT;
	        if ($mT117['Dias_Vencer'] == 0) {
	        	$dias_vencimento = " (hoje)";
	        } elseif ($mT117['Dias_Vencer'] == 1) {
	        	$dias_vencimento = " (amanhã) ";
	        } elseif ($mT117['Dias_Vencer'] > 2) {
	        	$dias_vencimento = " (daqui a $mT117[Dias_Vencer] dias)";
	        }
	        $md5BoletoCliente = md5($mT117['T117_Id'].$mT117['T117_D024_Id']);
	        $vencimentoBoleto = troca_data_dma($mT117['T117_Data_Vencimento']);
	        $urlBoleto = $servidor."/hardness3/prog/externo/cobranca/gerar_boleto_empresa.php?cQuery=".$md5BoletoCliente."&buscarBoletoBanco=suporte";
	        echo <<<EOT
					<tr>
						<td colspan=2 style="border-bottom:0px solid silver">
							<font style="font-family:arial,verdana;font-size:11px;">
								Olá! Já está disponível o boleto para pagamento.
						</td>
					</tr>
					<tr>
						<td width=10 style="border-left:0px solid silver">
							<a target="_blank' href="{$urlBoleto}">
								<img src="$servidor/hardness/imagens/png/boleto.png" border=0><font color=blue>
							</a>
						</td>
						<td class=fonte style="font-size:11px;border-right:0px solid silver">
							<a target="_blank" href="{$urlBoleto}">
								vencimento em <b>{$vencimentoBoleto} {$dias_vencimento} </b><br>
								<font style="color:green;font-weight:bold">Clique aqui para imprimir o boleto</font>
							</a>
EOT;
			if (abs($mT117['Dias_Prorrogado']) >= 3) {
          		//echo "<BR><font color=red>CASO ESTE NOVO ACORDO NÃO SEJA REGULARIZADO NO PRAZO DE <b>".($mT117['Dias_Vencer'])." DIA(S)</b> O SERVIÇO PODERÁ SER <b>SUSPENSO</b>.";
			}
            echo <<<EOT
            			</td>
                   	</tr>
	      			<tr>
	       				<td width=10 class=fonte colspan=2 style="font-size:11px;border-top:0px solid silver">
	         				O pagamento pode ser realizado pela internet ou em qualquer agência bancária até a data do vencimento.
	        			</td>
	       			</tr>
	      		</table>
	      		</div>
EOT;
    	}
    	$tem_boleto = 'S';
	}

	//Select dos boletos vencidos a mais de 3 dias
	$cClausula  = " select ";
	$cClausula .= " T117_Id, ";
	$cClausula .= " T117_D024_Id, ";
	$cClausula .= " T117_Data_Emissao, ";
	$cClausula .= " T117_Data_Vencimento, ";
	$cClausula .= " T117_Valor_Total, ";
	$cClausula .= " T117_Nosso_Numero,";
	$cClausula .= " T117_Flag_Cancelado,";
	$cClausula .= " T002_Data_Vencimento, ";
	$cClausula .= " D007_Nome_Banco, ";
	$cClausula .= " datediff(current_date(),T117_Data_Vencimento) as Dias_Vencer, ";
	$cClausula .= " datediff(T117_Data_Vencimento,T002_Data_Vencimento) as Dias_Prorrogado, ";
	$cClausula .= " datediff(current_date(),T002_Data_Vencimento) as Dias_Vencido ";
	$cClausula .= " from T117 ";
	$cClausula .= " left join D024 on D024_Id=T117_D024_Id ";
	$cClausula .= " left join T002 on T002_T117_Id=T117_Id ";
	$cClausula .= " left join D007 on D007_Id=T117_D007_Id ";
	$cClausula .= " where md5(D024_Cnpj)='".$Cnpj_Empresa_Usuaria."' ";
	$cClausula .= " and datediff(current_date,T002_Data_Vencimento)>0 ";
	$cClausula .= " and T117_Flag_Cancelado='N' ";
	$cClausula .= " and T002_Data_Recebimento='0000-00-00' ";
	$cClausula .= " AND (T002_T002_Id_Agrupado<=0 OR T002_T002_Id_Agrupado IS NULL)";
	$cClausula .= " order by T117_Data_Vencimento";
	$cSql_T117=mysql_query($cClausula);

	echo mysql_error();
	if (mysql_num_rows($cSql_T117) > 0) {
    	echo <<<EOT
    		<div id="divBoletoAtrasado" style="border: 0px; width: 490px; height: 133px; font-size: 14px; margin-top: 10px; padding: 0px; font-family: Arial; text-align: justify">
			<table class="tableBoletoHardness" width=512 border=0 cellpadding=5 bgcolor=white style="border: 1px dashed red;">
				<tr>
					<td colspan=2 style="border-bottom:0px solid silver">
						<font style="font-family:arial,verdana;font-size:11px;">
						Consta em nossa base a(s) pendendência(s) abaixo relacionada(s)
					</td>
				</tr>
EOT;
		$alteraFundo = false;
        while ($mT117 = mysql_fetch_array($cSql_T117)) {
			if($mT117['Dias_Vencer'] > 0) {
                $dias_vencimento = " (".$mT117['Dias_Vencer']." dias atrás)";
			} else {
                $dias_vencimento = "";
			}
			$dias = 10;
            if (isset($_REQUEST['Gestao'])) {
               	$dias = 15;
            }

			$md5BoletoCliente = md5($mT117['T117_Id'].$mT117['T117_D024_Id']);
			$vencimentoBoleto = troca_data_dma($mT117['T117_Data_Vencimento']);
			$urlBoleto = $servidor."/hardness3/prog/externo/cobranca/gerar_boleto_empresa.php?cQuery=".$md5BoletoCliente."&buscarBoletoBanco=suporte";
			echo <<<EOT
                <tr>
                    <td width=10 style='border-left:0px solid silver'>
                        <a target="_blank" href="{$urlBoleto}">
                    	    <img src='$servidor/hardness/imagens/png/boleto.png' border=0><font color=blue>
                        </a>
                    </td>
                    <td class=fonte style='font-size:11px;border-right:0px solid silver'>
                        <a target='_blank' href="{$servidor}/externo/cobranca/atualizarBoleto/?ajax=true&noEmp=true&u=1&p=1&a={$D024_Id}">
                    	    vencimento em <b>{$vencimentoBoleto}<font style='color:red;'>{$dias_vencimento}</b><br>
                        	<font style='color:green;font-size:14px;font-weight:bold'>Clique aqui para gerar novo boleto</font><br>
                        </a>
EOT;
            if (abs($mT117['Dias_Prorrogado']) >= 3) {
				//echo "<BR><font color=red>CASO ESTA PENDÊNCIA NÃO SEJA REGULARIZADA NO PRAZO DE <b>".($mT117[Dias_Vencer])." DIA(S)</b> O SERVIÇO PODERÁ SER <b>SUSPENSO</b>.";
			} else if (abs($mT117['Dias_Vencer'])>=3) {
				//echo "<BR><font color=red>CASO ESTA PENDÊNCIA NÃO SEJA REGULARIZADA NO PRAZO DE <b>".($dias-abs($mT117[Dias_Vencer]))." DIA(S)</b> O SERVIÇO PODERÁ SER <b>SUSPENSO</b>.";
			}
           	echo <<<EOT
           			</td>
               	</tr>
EOT;
            if(abs($mT117['Dias_Prorrogado']) >= 3) {
				if((abs($mT117['Dias_Vencer']) <= 0) || (abs($mT117['Dias_Prorrogado']) > $dias)) {
                    /*echo <<<EOT
                    	<script>
                        	top.location.replace("{$servidor}/externo/cobranca/atualizarBoleto/?ajax=true&noEmp=true&u=1&p=1&a={$D024_Id}");
                        </script>
EOT;*/
				}
			} else if ($dias-abs($mT117['Dias_Vencer']) <= 0) {
            	echo <<<EOT
            		<script>
                  		top.location.replace("{$servidor}/externo/cobranca/atualizarBoleto/?ajax=true&noEmp=true&u=1&p=1&a={$D024_Id}");
              		</script>
EOT;
			}
            if(abs($mT117['Dias_Vencido']) >= $dias) {
    			echo <<<EOT
            		<script>
                  		top.location.replace("{$servidor}/externo/cobranca/atualizarBoleto/?ajax=true&noEmp=true&u=1&p=1&a={$D024_Id}");
              		</script>
EOT;
			}
			if(abs($mT117['Dias_Vencido']) >= ($dias/2)) {
				$alteraFundo = true;
				$prazoSuspensao = $dias-abs($mT117['Dias_Vencido']);
				$prazoSuspensao .= ($prazoSuspensao > 1) ? ' dias' : ' dia';
    			echo <<<EOT
            		<tr>
						<td width=10 class=fonte colspan=2 style='font-size:12px; border-top:0px solid silver; color: var(--BGVermelho); font-weight: bold;'>
							ATENÇÃO! O acesso ao sistema será suspenso dentro de {$prazoSuspensao}.
						</td>
					</tr>
EOT;
			}
		}
		if($alteraFundo){
			echo <<<EOT
				<style>
					.tableBoletoHardness {
						background-color: #FBECEC;
					}
				</style>
EOT;
		}
		echo <<<EOT
				<tr>
					<td width=10 class=fonte colspan=2 style='font-size:11px;border-top:0px solid silver''>
						Desconsiderar caso a(s) pendência(s) já tenha(m) sido quitada(s), assim que for acusado em nosso sistema a(s) mesma(s) será(ão) baixada(s).
					</td>
				</tr>
			</table></div>
EOT;
		$tem_boleto='S';
	}

	if ($tem_boleto == 'S') {
   		//-- Encontra todos os a vencer
		$cClausula  = " select ";
		$cClausula .= " * ";
		$cClausula .= " from T135 ";
		$cClausula .= " left join D024 on D024_Id=T135_D024_Id ";
		$cClausula .= " where md5(D024_Cnpj)='".$Cnpj_Empresa_Usuaria."' ";
		$cClausula .= "   and T135_Data_Termino<=(Curdate() + interval 30 day) ";
		$cClausula .= "   and T135_Flag_Renovado!='S' ";
		$cClausula .= "   and T135_Flag_Renovado!='C' ";
		$cSql_T135=mysql_query($cClausula);

	   	echo mysql_error();
	   	if (mysql_num_rows($cSql_T135) > 0) {
	   		$mT135=mysql_fetch_array($cSql_T135);
	    	echo <<<EOT
	    		<table width=500 border=0 cellpadding=5 cellspacing=0 bgcolor=white style='border: 1px dashed green; border-top:0px'>";
                    <tr>
                        <td colspan=2 style='border-bottom:0px solid silver'>
                        	<font style='font-family:arial,verdana;font-size:12px;'>
                          		{$mT135['D024_Nome_Empresa']}, <b>agradecemos à confiança e parceria</b> durante estes últimos 12 meses! A Hardness Sistemas vem trabalhando constantemente para ofereca você, serviços e produtos que atendam melhor as suas necessidades e expectativas!<br>
                        </td>
                    </tr>
                    <tr>
                        <td class=fonte style='font-size:12px;border-right:0px solid silver'>
                        	Caso a sua empresa não queira renovar os serviços, basta nos enviar um e-mail pedindo o <font style='color:red'>cancelamento</font> para {$rowC004['C004_E_Mail']}.<br>
                        </td>
                    </tr>
	      			<tr>
	       				<td width=10 class=fonte colspan=2 style='font-size:12px;border-top:0px solid silver''>
	         				<b>Obrigado!</b>
	         				<BR>
	         				<BR>
	         				Equipe {$rowC004['C004_Nome_Abreviado']}
	        			</td>
	       			</tr>
	      		</table>
EOT;
	   	}
    } else {
		//-- Encontra todos os a vencer
		$cClausula  = " select ";
		$cClausula .= " * ";
		$cClausula .= " from T135 ";
		$cClausula .= " left join D024 on D024_Id=T135_D024_Id ";
		$cClausula .= " where md5(D024_Cnpj)='".$Cnpj_Empresa_Usuaria."' ";
		$cClausula .= "   and T135_Data_Termino>Curdate() ";
		$cClausula .= "   and T135_Data_Termino<=(Curdate() + interval 30 day) ";
		$cClausula .= "   and T135_Flag_Renovado!='S' ";
		$cClausula .= "   and T135_Flag_Renovado!='C' ";
		$cSql_T135=mysql_query($cClausula);

		echo mysql_error();
	  	if(mysql_num_rows($cSql_T135) > 0) {
	   		$mT135=mysql_fetch_array($cSql_T135);
	     	echo <<<EOT
	     		<table width=500 border=0 cellpadding=5 cellspacing=0 bgcolor=#EAF7EA style='border: 2px solid green;'>
                    <tr>
                        <td colspan=2 style='border-bottom:0px solid silver' bgcolor=green>
                        	<font style='font-family:arial,verdana;font-size:11px;'>
                        	<font style='color:white;font-weight:bold;font-size:13px'>RENOVAÇÃO ANUAL</font>
                        </td>
                    </tr>
                    <tr>
                        <td colspan=2 style='border-bottom:0px solid silver'>
                    	    <font style='font-family:arial,verdana;font-size:12px;'>
                    	    	{$mT135['D024_Nome_Empresa']}, <b>agradecemos à confiança e parceria</b> durante estes últimos 12 meses! A {$rowC004['C004_Nome_Abreviado']} vem trabalhando constantemente para oferecer a você, serviços e produtos que atendam melhor as suas necessidades e expectativas!<br>
                        </td>
                    </tr>
                    <tr>
                        <td class=fonte style='font-size:12px;border-right:0px solid silver'>
                          	Caso a sua empresa não queira renovar os serviços, basta nos enviar um e-mail pedindo o <font style='color:red'>cancelamento</font> para {$rowC004['C004_E_Mail']}.<br>
                        </td>
                    </tr>
	      			<tr>
	       				<td width=10 class=fonte colspan=2 style='font-size:12px;border-top:0px solid silver''>
	         				<b>Obrigado!</b>
	         				<BR>
	         				<BR>
	         				Equipe {$rowC004['C004_Nome_Abreviado']}
	        			</td>
	       			</tr>
	      		</table>
EOT;
		}
   	}
}

//Verificação de boletos na base da Hardness
if($sys['dominio'] == 'hardness.com.br'){
	echo <<<EOT
		<iframe src="http://nfe.hardness.com.br/site/mensagens.php?Local=S&i={$Cnpj_Empresa_Usuaria}&Usuario_Atual=63&Gestao=true" frameborder=0 style='border:0px; padding:0px; width:99%; height:98%'></iframe>
EOT;
}else{
	echo <<<EOT
		<iframe src="http://{$sys['urlEmpresa']}/hardness/site/mensagens.php?Local=S&i={$Cnpj_Empresa_Usuaria}&Usuario_Atual=63&Gestao=true" frameborder=0 style='border:0px; padding:0px; width:99%; height:98%'></iframe>
EOT;
}
