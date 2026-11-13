<?php

//-- Fun��es Gen�ricas em PHP
//require "../hardness/func/funcoes_genericas.inc";
global $g;
require_once("old/lib/funcoes_genericas.inc");
$Diretorio_Imagens="/hardness/dados_usuarios/{$confUsuario['dbDatabase']}/imagens";
$cArquivo_Cabecalho_Relatorio="{$g['pathDadosAntigo']}cabecalho_relatorio.php";
if ($p==false)
{
    echo "<p style='font-size:16px;color: var(--BGVermelho);font-family:arial'>O link está incorreto, favor pedir suporte tecnico. (1)</p>";
    exit;
}

$xp=$p;
$Empresa_Atual=$xp[0];
$T010_Id=$xp[1];
$D024_Id=$xp[2];
$Tipo_Opcoes='ComValor';


// Busca os dados da empresa usu�ria
$cSql=mysql_query("select * from C004 where C004_Id='{$g['empresaAtual']}'");
$mC004=mysql_fetch_array($cSql);

$cSql_T010=mysql_query("select T010_Flag_Marca_Individual,
                               T010_Flag_Quantidade_Individual,
                               T010_Id,
                               T010_Flag_Cotacao_Finalizada,
                               T173_T010_Id,
                               D024_Id,
                               D036_Tipo_Transporte,
                               C007_Email_Assinatura
						from T010
						left join D024 on D024_Id=T010_D024_Id
						left join T173 on T010_Id=T173_T010_Id
            left join D036 on D036_Id=T173_D036_Id
            left join C007 on C007_Id=T010_C007_Id
						where T010_Id='$T010_Id'");
$mT010=mysql_fetch_array($cSql_T010);

if ($mT010['T010_Flag_Cotacao_Finalizada']=='S')
{
    echo "<p style='font-size:16px;color:blue;font-family:arial'>Esta cotação já foi finalizada.</p>";
    exit;
}

if (($mT010['T010_Id']<=0))
{
    echo "<p style='font-size:16px;color: var(--BGVermelho);font-family:arial'>O link está incorreto, favor pedir suporte técnico. (3 $p $D024_Id)</p>";
    exit;
}

echo "
<html>
<head>
<title>Cotação de Compra N.$T010_Id</title>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<meta name='ROBOTS' content='NOINDEX,NOFOLLOW'>  
<meta http-equiv='Pragma' content='no-cache'>
<link rel=stylesheet href='../hardness/classes/classe_tamanho_medio_firefox.css' type='text/css'>
<!-- Funções Gen´wricas em Javascript -->
<script src='../hardness/func/funcoes_genericas.js'></script>
<script src='../hardness/func/ajax.js'></script>

<STYLE TYPE='text/css'><!--
  A:link {text-decoration:none; color:000000}
  A:visited {text-decoration:none; color:000000}
  A:hover {text-decoration:underline; color:000000; cursor:hand}
  A:active {text-decoration:underline; color:000000; cursor:hand}

.fontemed
{
  font-size:12px;
  font-family:arial,verdana;
}
#divLoadingCenter {
	width:150px;
	height:25px;
	position: absolute;
	top:50%;
	left:48%;
	margin-top:-50px;
	margin-left:-50px;
	z-index:auto;
	background-color: yellow;
	border: 1px solid #BDCBD8;
	clear:both;
	vertical-align:middle;
	color: red;
	padding:11px;
        font-family:arial, tahoma, verdana, helvetica, sans-serif;
	font-size:14px;
	text-align:center;
}
.titulo_relatorio2{
	font-size:20px;
	font-weight:bold;
}
--></style>
<!-- //$('#{$g['divId']}').showLoading(); //$('#{$g['divId']}').hideLoading(); -->
<script>
var zCount=0;
function GravarEdicaoCampo(Objeto,Id) {
   if (Objeto.type=='checkbox') {
      var Conteudo_Campo_Editado=Objeto.checked;
   } else {
      var Conteudo_Campo_Editado=Objeto.value;
   }
   var Campo_Editado=Objeto.name;
   //MostrarMensagem('inline');
   document.getElementById('fme_gravar').src='/est/est008/gravar_campo_editado_online/?T010_Id=$T010_Id&T059_Id='+Id+'&T174_Id='+Id+'&Campo_Editado='+Campo_Editado+'&Conteudo_Campo_Editado='+Conteudo_Campo_Editado+ '&D049_Id=' + Id + '&ajax=true&p={$g['empresaAtual']}&noEmp=true';
   setTimeout (function(){
   		$('#{$g['divId']}').showLoading();
   },100);
   setTimeout (function(){
   		$('#{$g['divId']}').hideLoading();
   },400);

}
function MostrarMensagem(Visivel,Forcar) {
    document.getElementById('divLoadingCenter').style.display=Visivel;
    document.getElementById('divLoadingCenter').style.zIndex = zCount++;
    if (Forcar=='S') {
        document.getElementById('fme_mensagem').src='mensagem_carregando.php3?Visivel='+Visivel;
    }
}
setTimeout (function(){
	$('#T174_Valor_Custo_Unitario').focus();
},100);

</script>
</head>
<body bottommargin=0 topmargin=0 rightmargin=0 leftmargin=0 bgcolor='white'>
";

//--  MENSAGEM "CARREGANDO..."
echo "<div id='divLoadingCenter' style='display:none;'>
          <img src='../hardness/imagens/gif/loading.gif' alt='' /><br />
          &nbsp;Aguarde...
      </div>";
/*
if ($Forn<=1)
{
    $Campo_Custo='';

    if ($mT010['T010_Flag_Marca_Individual']=='S')
    {
        $Campo_Marca='_1';
    }
    if ($mT010['T010_Flag_Quantidade_Individual']=='S')
    {
        $Campo_Quantidade='_1';
    }
}
else if ($Forn>1)
{
    $Campo_Custo='_'.($Forn-1);
    if ($mT010['T010_Flag_Marca_Individual']=='S')
    {
        $Campo_Marca='_'.$Forn;
    }
    if ($mT010['T010_Flag_Quantidade_Individual']=='S')
    {
        $Campo_Quantidade='_'.$Forn;
    }
}
*/

$cClausula  = " select ";
$cClausula .= " T059_Id, ";
$cClausula .= " D049_Id, ";
$cClausula .= " D037_Unidade, ";
$cClausula .= " T010_D024_Id, ";
$cClausula .= " T010_D024_Id_1, ";
$cClausula .= " T010_D024_Id_2, ";
$cClausula .= " T010_D024_Id_3, ";
$cClausula .= " T010_D024_Id_4, ";
$cClausula .= " T010_D024_Id_5, ";
$cClausula .= " T174_Valor_Custo_Unitario, ";
$cClausula .= " T174_IPI, ";
$cClausula .= " T174_Id, ";
$cClausula .= " T174_Quantidade, ";
$cClausula .= " T174_D009_Id, ";
$cClausula .= " D001_Codigo_Produto, ";
$cClausula .= " D001_Descricao_Produto, ";
$cClausula .= " T059_Descricao_Produto, ";
$cClausula .= " D009_Quantidade_Estoque, ";
$cClausula .= " T059_Valor_Custo_Unitario, ";
$cClausula .= " T010_Data, ";
$cClausula .= " if(ifnull(T059_Quantidade>0,0),T059_Quantidade,T059_Quantidade) T059_Quantidade, ";
$cClausula .= " if(ifnull(T059_D009_Id,0)>0,T059_D009_Id,T059_D009_Id) T059_D009_Id, ";
$cClausula .= " T010_Id, ";
$cClausula .= " D024_Nome_Empresa, ";
$cClausula .= " C007_Primeiro_Nome, ";
$cClausula .= " C007_Email_Assinatura, ";
$cClausula .= " D049_Codigo_Produto_Fornecedor, ";
$cClausula .= " D049_Modelo, ";
$cClausula .= " D001_Id, ";
$cClausula .= " D082_Marca, ";
$cClausula .= " T174_Flag_Indisponivel, ";
$cClausula .= " T174_Valor_ST, ";
$cClausula .= " T174_Valor_Total ";
$cClausula .= " from T059 ";
$cClausula .= " left join T010 on T010_Id=T059_T010_Id ";
$cClausula .= " left join D024 on D024_Id={$D024_Id} ";
//$cClausula .= " left join T012 on T012_T059_Id=T059_Id ";
$cClausula .= " left join T173 on T173_D024_Id={$D024_Id} AND T173_T010_Id={$T010_Id} ";
$cClausula .= " left join T174 on T059_Id=T174_T059_Id AND T174_T010_Id={$T010_Id} AND T174_T173_Id=T173_Id";
$cClausula .= " left join C007 on C007_Id=T010_C007_Id ";
$cClausula .= " left join D009 on D009_Id=if(ifnull(T059_D009_Id,0)>0,T059_D009_Id,T059_D009_Id) ";
$cClausula .= " left join D049 on D049_Id=D009_D049_Id ";
$cClausula .= " left join D083 on D049_Id=D083_D049_Id and D083_D024_Id=$D024_Id";
$cClausula .= " left join D001 on D001_Id=D049_D001_Id ";
$cClausula .= " left join D037 on D037_Id=D001_D037_Id ";
$cClausula .= " left join D082 on D082_Id=D049_D082_Id ";
$cClausula .= " left join C008 on C008_Id=D001_C008_Id ";
$cClausula .= " where T059_T010_Id='$T010_Id' AND T174_Flag_Excluido='N' ";
/**
*Variavel n�o declarada. 
if ($Ocultar_Comprados=='true')
{
    $cClausula .= " and (select T012_Id from T012 where T012_T059_Id=T059_Id limit 1) is null ";
}
*/
$cClausula .= " order by substring(D001_Codigo_Produto,1,locate('-',concat(D001_Codigo_Produto,'-'))),floor(substring(D001_Codigo_Produto,locate('-',concat(D001_Codigo_Produto,'-'))+1,20))";

$T059 = mysql_query($cClausula);
   echo mysql_error();

    //
    // CABE�ALHO
    //

 echo "<table align=center border='0' valign=top cellpadding=0 cellspacing=5 bgcolor=white width='880' >";
  echo "<tr>";
   echo "<td valign=top>";
    echo "<table align=center border='0' valign=top cellpadding=3 cellspacing=2 bgcolor=white width='880' >";
     echo "<tr>";
      echo "<td valign=top>";

       echo "<table align=center border='0' cellspacing=0 cellpading=0 width='100%' height=10 valign=top>";
        echo "<tr>";
         echo "<td bgcolor=white width=80%><br />";

                //-- $cArquivo_Cabecalho_Relatorio � definido no arquivo include/pasta_usuario.inc
                include $cArquivo_Cabecalho_Relatorio;

         echo "</td>";
           echo "</tr>";
          echo "</table>";
         echo "</td>";
         echo "<td bgcolor=white align=center width=20% rowspan=2>";
           echo "<font class='titulo_relatorio2'>COTAÇÃO DE COMPRA <br> No.$T010_Id</font>";
           echo "</font><br /><br />";
           if ($mC004['C004_Flag_Empresa_Simples']=='1') {
               echo "<table border=0 cellpadding=5 style='border:1px dotted black' bgcolor=#f4f4f4><tr><td align=center><font class=fonte style='font-size:12px;color: var(--BGVermelho)'>Empresa Participante do<br><b>SUPER SIMPLES</b></td></tr></table>";
           }
         echo "</td>";
       echo "</tr>";
       echo "<tr>";
         echo "<td colspan=2>";
           echo "<font class=fontemed>Impresso</b> &nbsp;<b>".agora()."</b></font>";
           echo "</td>";
         echo "</td>";
       echo "</tr>";
     echo "</table>";

    echo "</td>";
   echo "</tr>";

   echo "<tr>";

      echo "<td>";

                $cClausula  = " select ";
                $cClausula .= " D024_Id, ";
                $cClausula .= " D024_Nome_Empresa, ";
                $cClausula .= " D024_Nome_Fantasia, ";
                $cClausula .= " D024_Endereco, ";
                $cClausula .= " D024_Numero, ";
                $cClausula .= " D020_Nome_Cidade, ";
                $cClausula .= " D018_UF, ";
                $cClausula .= " D024_Bairro, ";
                $cClausula .= " D024_Cep, ";
                $cClausula .= " D024_Flag_Pessoa_Fisica_Juridica, ";
                $cClausula .= " if(D024_Cnpj='',D024_Cpf,D024_Cnpj) D024_Cnpj, ";
                $cClausula .= " D024_Flag_Revenda_Consumidor, ";
                $cClausula .= " D024_Flag_Tipo_Empresa, ";
                $cClausula .= " D024_Nome_Contato, ";
                $cClausula .= " D024_Observacoes, ";
                $cClausula .= " D024_Inscricao_Estadual, ";
                $cClausula .= " D024_Praca_Pagamento,  ";
                $cClausula .= " D024_Aviso, ";
                $cClausula .= " D024_Flag_Classe, ";
                $cClausula .= " D024_Flag_Liberado, ";
                $cClausula .= " D030_Nome_Pais, ";
                $cClausula .= " D024_Observacao_Compra,  ";
                $cClausula .= " D024_Data_Irregular, ";
                $cClausula .= " D024_DDD_Telefone_1,  ";
                $cClausula .= " D024_Telefone_1,  ";
                $cClausula .= " D024_Telefone_2,  ";
                $cClausula .= " D024_Fax, ";
                $cClausula .= " D024_Dias_Carencia_Pagamento, ";
                $cClausula .= " D024_Dia_Semana_Pagamento, ";
                $cClausula .= " D024_Dia_Mes_Pagamento, ";
                $cClausula .= " D024_Observacoes, ";
                $cClausula .= " T010_Observacao, ";
//                $cClausula .= " T010_Data_Entrega, ";
//                $cClausula .= " T010_Forma_Pagamento, ";
                $cClausula .= " C007_Primeiro_Nome ";
                $cClausula .= " from T010 ";
                $cClausula .= " left join D024 on D024_Id='$D024_Id' ";
                $cClausula .= " left join D020 on D024_D020_Id=D020_Id ";
                $cClausula .= " left join D018 on D020_D018_Id=D018_Id ";
                $cClausula .= " left join D030 on D024_D030_Id=D030_Id ";
                $cClausula .= " left join C007 on C007_Id=D024_C007_Vendedor_Interno ";
                $cClausula .= " where T010_Id='$T010_Id'";
                $cSql_D024 = mysql_query($cClausula);
                
                echo mysql_error();

                $mD024=mysql_fetch_array($cSql_D024);

                echo "<br /><table border=0 align=center cellspacing=0 bgcolor='white' cellpadding=2 width=100% style='border:1px solid black'>";

                	echo "<tr>";
                      echo "<td valign=top height=100>";
					  	echo "<form name='formCotacao' />";
                			echo "<table border=0 cellspacing=0 width=100% cellpadding=1 bgcolor=gray>";

                    			echo "<tr>";
                   			      	echo "<td align=left bgcolor=white colspan=2 style='border-bottom:1px solid silver'>";
                  				  		 echo "<font class=fontemed>Razão Social<br><b>{$mD024['D024_Nome_Empresa']}</b></font>";
                					echo "</td>";
                   			      	echo "<td align=left bgcolor=white colspan=2 style='border-bottom:1px solid silver'>";
                  				  		 echo "<font class=fontemed>CNPJ / CPF<br><b>{$mD024['D024_Cnpj']}</b></font>";
                					echo "</td>";
                   			      	echo "<td align=left bgcolor=white colspan=2 style='border-bottom:1px solid silver'>";
                                        echo "<font class=fontemed>I.E.<br>";
                                        if (empty($mD024['D024_Inscricao_Estadual']))
                                        {
                                            echo "<font style='color: var(--BGVermelho)'><b>&nbsp</b></font></font>";
                                        }
                                        else
                                        {
                                            echo "<b>{$mD024['D024_Inscricao_Estadual']}</b></font>";
                                        }
                					echo "</td>";
                				echo "</tr>";

                    			echo "<tr>";
                   			      	echo "<td align=left bgcolor=white colspan=2 style='border-bottom:1px solid silver'>";
                                        echo "<font class=fontemed>Endereço<br>";
                                        if (empty($mD024['D024_Endereco']))
                                        {
                                            echo "<font style='color: var(--BGVermelho)'><b>&nbsp</b></font></font>";
                                        }
                                        else
                                        {
                                            echo "<b>{$mD024['D024_Endereco']}</b></font>";
                                        }
                					echo "</td>";
                   			      	echo "<td align=left bgcolor=white  style='border-bottom:1px solid silver'>";
                                        echo "<font class=fontemed>Número<br>";
                                        if (empty($mD024['D024_Numero']))
                                        {
                                            echo "<font style='color: var(--BGVermelho)'><b>&nbsp</b></font></font>";
                                        }
                                        else
                                        {
                                            echo "<b>{$mD024['D024_Numero']}</b></font>";
                                        }
                					echo "</td>";
                   			      	echo "<td align=left bgcolor=white  style='border-bottom:1px solid silver'>";
                                        echo "<font class=fontemed>Bairro<br>";
                                        if (empty($mD024['D024_Bairro']))
                                        {
                                            echo "<font style='color: var(--BGVermelho)'><b>&nbsp</b></font></font>";
                                        }
                                        else
                                        {
                                            echo "<b>{$mD024['D024_Bairro']}</b></font>";
                                        }
                					echo "</td>";
                					echo "<td bgcolor=white align=left style='border-bottom:1px solid silver'>";
                					   	  echo "<font class=fontemed>Cidade<br><b>{$mD024['D020_Nome_Cidade']}</b> </font>";
                					echo "</td>";
                					echo "<td bgcolor=white align=left style='border-bottom:1px solid silver'>";
                					   	  echo "<font class=fontemed>UF<br><b>{$mD024['D018_UF']}</b> </font>";
                					echo "</td>";
                				echo "</tr>";

                				echo "<tr>";
                					echo "<td align=left bgcolor=white >";
                                        echo "<font class=fontemed>DDD<br>";
                                        if (empty($mD024['D024_DDD_Telefone_1']))
                                        {
                                            echo "<font style='color: var(--BGVermelho)'><b>&nbsp</b></font></font>";
                                        }
                                        else
                                        {
                                            echo "<b>{$mD024['D024_DDD_Telefone_1']}</b></font>";
                                        }
                					echo "</td>";
                					echo "<td align=left bgcolor=white >";
                                        echo "<font class=fontemed>Telefone<br>";
                                        if (empty($mD024['D024_Telefone_1']))
                                        {
                                            echo "<font style='color: var(--BGVermelho)'><b>&nbsp</b></font></font>";
                                        }
                                        else
                                        {
                                            echo "<b>{$mD024['D024_Telefone_1']}</b></font>";
                                        }
                					echo "</td>";
                					echo "<td bgcolor=white align=left>";
                							echo "<font class=fontemed>FAX<br><b>{$mD024['D024_Fax']}&nbsp;</b></font>";
                						echo "</a>";
                					echo "</td>";
                					echo "<td bgcolor=white >";
                							echo "<font class=fontemed>Contato<br><b>{$mD024['D024_Nome_Contato']}&nbsp;</b></font>";
                						echo "</a>";
                					echo "</td>";
                					echo "<td bgcolor=white colspan=2>";
                                        echo "<font class=fontemed>Vendedor<br>";
                                        if (empty($mD024['C007_Primeiro_Nome']))
                                        {
                                            echo "<font style='color: var(--BGVermelho)'><b>&nbsp</b></font></font>";
                                        }
                                        else
                                        {
                                            echo "<b>{$mD024['C007_Primeiro_Nome']}</b></font>";
                                        }
                						echo "</a>";
                					echo "</td>";
                				echo "</tr>";

                			echo "</table>";
                		echo "</td>";
                	echo "</tr>";

                echo "</table>";

         echo "</td>";

   echo "</tr>";

   if (!empty($mD024['T010_Observacao']))
   {
       echo "<tr>";

        echo "<td align=center>";

          echo "<table border=0 cellspacing=0 width='880' align=center cellpadding=5 style='border:1px solid black'>";
         	echo "<tr>";
               echo "<td align=left width='100' class='fontemed' style='font-weight:bold;font-size:12px'>";
                  echo $mD024['T010_Observacao'];
               echo "</td>";
           echo "</tr>";
         echo "</table>";

        echo "</td>";

       echo "</tr>";
   }
   echo "<tr>";

    echo "<td align=center>";

      echo "<table border=0 cellspacing=0 width='880' align=center cellpadding=5 style='border:1px solid black'>";
     	echo "<tr>";
           echo "<td align=left width='130' class='fontemed' style='font-weight:bold;font-size:12px'>";
              echo 'Data de Entrega:';
           echo "</td>";
           echo "<td align=left class='fontemed' style='font-weight:bold;font-size:14px'>";
              //echo gCorrigeData($mD024['T010_Data_Entrega']);
           echo "</td>";
           echo "<td align=left width='55' class='fontemed' style='font-weight:bold;font-size:12px'>";
              echo 'Transporte:';
           echo "</td>";
           echo "<td align=left class='fontemed' style='font-weight:bold;font-size:12px'>";
              echo $mT010['D036_Tipo_Transporte'];
           echo "</td>";
       echo "</tr>";
     	echo "<tr>";
           echo "<td align=left width='130' class='fontemed' style='font-weight:bold;font-size:12px'>";
              echo 'Forma de Pagamento:';
           echo "</td>";
           echo "<td align=left class='fontemed' style='font-weight:bold;font-size:12px'>";
              //echo $mD024['T010_Forma_Pagamento'];
           echo "</td>";
       echo "</tr>";
     echo "</table>";

    echo "</td>";

   echo "</tr>";

   echo "<tr>";
    echo "<td align=center>";
    echo "<span style='margin-top:-5px;display:block;'><font color='red' size='2'><b>Obs: Para separar casas decimais utilize apenas vírgula e não é necessário separar a unidade de milhar. Ex: 1000,00.</b></font></span>";
  echo "<table border=0 cellspacing=1 width='880' align=center cellpadding=3 style='border:1px solid black'>";
	echo "<tr bgcolor=#DBDBDB>";
  		echo "<td align=center width='70' class='fontemed' style='font-weight:bold;border-bottom:1px solid #808080'>";
             echo "Cod<br />Produto";
  		echo "</td>";
  		echo "<td align=center width='70' class='fontemed' style='font-weight:bold;border-bottom:1px solid #808080'>";
             echo "Cod<br />Fornecedor";
  		echo "</td>";
   		echo "<td align=left width='350' class='fontemed' style='font-weight:bold;border-bottom:1px solid #808080'>";
             echo "Descrição";
		echo "</td>";
		echo "<td align=left width='50' class='fontemed' style='font-weight:bold;border-bottom:1px solid #808080'>";
             echo "Marca";
		echo "</td>";
		echo "<td align=right width='40' class='fontemed' style='font-weight:bold;border-bottom:1px solid #808080'>";
             echo "Qtde";
		echo "</td>";
		echo "<td align=right width='20' class='fontemed' style='font-weight:bold;border-bottom:1px solid #808080'>";
             echo "Un";
		echo "</td>";
		echo "<td align=right width='100' class='fontemed' style='font-weight:bold;border-bottom:1px solid #808080'>";
             echo "Preço Unitário";
		echo "</td>";
		echo "<td align=right width='60' class='fontemed' style='font-weight:bold;border-bottom:1px solid #808080'>";
             echo "IPI(%)";
		echo "</td>";
		echo "<td align=right width='20' class='fontemed' style='font-weight:bold;border-bottom:1px solid #808080'>";
             echo "ST(R$)";
		echo "</td>";
        // echo "<td align=right width='20' class='fontemed' style='font-weight:bold;border-bottom:1px solid #808080'>";
        //      echo "Total(R$)";
        // echo "</td>";
    echo "</tr>";

  $cor='#E5E5E5';
  
  $Total=0;

  while ($mT059 = mysql_fetch_array($T059))
  {

       if ($cor=='#E5E5E5')
       {
           $cor='white';
       }
       else
       {
           $cor='#E5E5E5';
       }


  	   echo "<tr bgcolor=$cor Id='l_{$mT059['T059_Id']}' height=19>";

          echo "<td align=center class='fontemed' >";
            echo $mT059['D001_Codigo_Produto'];
          echo "</td>";
          echo "<td align=center class='fontemed' >";
          	if(empty($mT059['D049_Codigo_Produto_Fornecedor'])){
				echo "<input type='text' name='D049_Codigo_Produto_Fornecedor' id='D049_Codigo_Produto_Fornecedor' value='{$mT059['D049_Codigo_Produto_Fornecedor']}' size=10 style='border:1px solid #000;background:#fff;text-align:right;font-size:12px;-moz-border-radius:0;' tabindex=100  onBlur=\"GravarEdicaoCampo(this,'{$mT059['D049_Id']}')\">";
          	}else{
				echo $mT059['D049_Codigo_Produto_Fornecedor'];
          	}
          echo "</td>";
          echo "<td align=left class='fontemed' >";
            echo $mT059['T059_Descricao_Produto'].' <b>'.$mT059['D049_Modelo'].'</b>';
          echo "</td>";
          echo "<td align=left class='fontemed' >";
          	//echo $mT059['D082_Marca'];
               if (($mT010['T010_Flag_Marca_Individual']=='S') and ($mT059['T174_Valor_Custo_Unitario']<=0))
               {
                   $D009=mysql_query("select D009_Id,
                                             D082_Marca
                                        from D009
                                   left join D049 on D049_Id=D009_D049_Id
                                   left join D082 on D082_Id=D049_D082_Id
                                       where D049_D001_Id='{$mT059['D001_Id']}'
                                         and D009_C004_Id='$Empresa_Atual'");


                   /**
                   if ($mT059['T059_D009_Id_1']!=$mT059['T059_D009_Id'])
                   {
                       $cor_D009_1='#FFFFC0';
                   }
                   */

                   echo "<select name='T174_D009_Id' id='T174_D009_Id' tabindex=0 class='campo' style='border:1px solid #000;background:#fff;font-size:12px;-moz-border-radius:0; width:100%;' onChange=\"GravarEdicaoCampo(this,'{$mT059['T174_Id']}')\">";
                   while ($mD009=mysql_fetch_array($D009))
                   {
                         echo "<option value='{$mD009['D009_Id']}' ";
                         if ($mD009['D009_Id']==$mT059['T174_D009_Id'])
                         {
                             echo " selected ";
                         }
                         echo ">{$mD009['D082_Marca']}</option>";
                   }
                   echo "</select>";
               }
               else
               {   
                   $D009=mysql_query("select D082_Marca                                             
                                        from T174
                                   left join D009 on D009_Id=T174_D009_Id      
                                   left join D049 on D049_Id=D009_D049_Id
                                   left join D082 on D082_Id=D049_D082_Id
                                       where T174_Id='{$mT059['T174_Id']}'
                                         and D009_C004_Id='$Empresa_Atual'");
                   $mD009=mysql_fetch_array($D009);               	            	   
                   echo $mD009['D082_Marca'];
               }
          echo "</td>";
          echo "<td align=right class='fontemed' >";
               if (($mT010['T010_Flag_Quantidade_Individual']=='S') and ($mT059['T174_Valor_Custo_Unitario']<=0))
               {
      			  echo "<input type='text' name='T174_Quantidade' id='T174_Quantidade' value='".number_format($mT059['T174_Quantidade'],$g['c020']['C020_Casas_Decimais_Quantidade'],',','.')."' size=10 style='border:1px solid #000;background:#fff;text-align:right;font-size:12px;-moz-border-radius:0;' tabindex=100  onBlur=\"GravarEdicaoCampo(this,'{$mT059['T174_Id']}')\">";
   			   }
   			   else
   			   {
                   echo number_format($mT059['T174_Quantidade'],$g['c020']['C020_Casas_Decimais_Quantidade'],',','.');
   			   }
          echo "</td>";
          echo "<td>";
          	echo $mT059['D037_Unidade'];
          echo "</td>";
          if ($mT059['T174_Valor_Custo_Unitario']<=0)
          {
             echo "<td align=right class='fontemed' >";
		  	    echo "<input type='text' name='T174_Valor_Custo_Unitario' id='T174_Valor_Custo_Unitario' value='".number_format($mT059['T174_Valor_Custo_Unitario'],4,',','.')."' size=8 style='border:1px solid #000;background:#fff;text-align:right;font-size:12px;-moz-border-radius:0;' tabindex=200  onBlur=\"GravarEdicaoCampo(this,'{$mT059['T174_Id']}')\">";
             echo "</td>";
          }
          else
          {
             echo "<td align=right class='fontemed'>";
                echo number_format($mT059['T174_Valor_Custo_Unitario'],4,',','.');
             echo "</td>";
          }
		  if ($mT059['T174_IPI']<=0)
          {
             echo "<td align=right class='fontemed'>";
		  	    echo "<input type='text' name='T174_IPI' id='T174_IPI' value='".number_format($mT059['T174_IPI'],2,',','.')."' size=8 style='border-color:#000;background:#fff;text-align:right;font-size:12px;-moz-border-radius:0;' tabindex=200  onBlur=\"GravarEdicaoCampo(this,'{$mT059['T174_Id']}')\">";
             echo "</td>";
          }
          else
          {
             echo "<td align=right class='fontemed'>";
                echo number_format($mT059['T174_IPI'],2,',','.');
             echo "</td>";
          }
          echo "<td align=right class='fontemed'>";
            echo "<input type='text' name='T174_Valor_ST' id='T174_Valor_ST' value='".number_format($mT059['T174_Valor_ST'],2,',','.')."' size=8 style='border-color:#000;background:#fff;text-align:right;font-size:12px;-moz-border-radius:0;' tabindex=200  onBlur=\"GravarEdicaoCampo(this,'{$mT059['T174_Id']}')\">";
          echo "</td>";
          // echo "<td align=right class='fontemed'>";
          //   echo number_format($mT059['T174_Valor_Total'],2,',','.');
          // echo "</td>";
       echo "</tr>";
       $Total+=$mT059['T174_Valor_Custo_Unitario']*$mT059['T059_Quantidade'];

  }
echo "</form>";
echo "</table>";

echo "<table align=center border='0' valign=top cellpadding=0 cellspacing=5 bgcolor=white width='880' >";
 echo "<tr>";
  echo "<td align=left>";
    echo "<font class=fonte style='font-size:12px'>Hardness Sistemas - www.hardness.com.br";
  echo "</td>";
 echo "</tr>";
echo "</table>";

if (!empty($mT010['C007_Email_Assinatura'])) {
  $assinatura = $mT010['C007_Email_Assinatura'];
  echo "<br /><br /><div style=\"text-align:left; font-size: 12px;\"><span align=\"left\">{$assinatura}</span></div>";
}
echo "<iframe name='fme_gravar' id='fme_gravar' src='' style='position:absolute;display:none;top:0;width:100;height:100;'></iframe>";


?>
