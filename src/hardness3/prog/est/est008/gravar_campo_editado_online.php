<?php
namespace hardness;
// abre uma conex�o no servidor
//$con=@mysqli_connect('localhost','root','#qos!0982');
//$dbs=mysql_select_db("e009",$con) or die (mysql_error());
$Empresa_Atual=2;
$Diretorio_Empresa="../hardness/dados_usuarios/e009";
$Diretorio_Imagens="../hardness/dados_usuarios/e009/imagens";
$cArquivo_Cabecalho_Relatorio="../hardness/dados_usuarios/e009/cabecalho_relatorio.php";

//-- Fun��es Gen�ricas em PHP
require "../hardness/func/funcoes_genericas.inc";
$Campo_Editado=$r_Campo_Editado;
$Conteudo_Campo_Editado=$r_Conteudo_Campo_Editado;
$T174_Id=$r_T174_Id;
$T173_Id=$r_T173_Id;
$T059_Id=$r_T059_Id;
$T010_Id=$r_T010_Id;
$D049_Id=$r_D049_Id;

if ($Campo_Editado=='T174_Valor_Custo_Unitario')
{

   $Conteudo_Campo_Editado=gCorrigeNumeroInverte($Conteudo_Campo_Editado,5);

   mysql_query("update T174 set $Campo_Editado='$Conteudo_Campo_Editado', T174_Valor_Custo_Unitario_Forn='$Conteudo_Campo_Editado', T174_Hora_Email_Forn=CURRENT_TIMESTAMP() where T174_Id='$T174_Id'");
   
}
elseif($Campo_Editado=='T174_IPI')
{

   $Conteudo_Campo_Editado=formata_valor_gravar($Conteudo_Campo_Editado,5);

   mysql_query("update T174 set $Campo_Editado='$Conteudo_Campo_Editado', T174_IPI_Forn='$Conteudo_Campo_Editado', T174_Hora_Email_Forn=CURRENT_TIMESTAMP() where T174_Id='$T174_Id'");
   
}
elseif ($Campo_Editado=='T059_Quantidade')
{

   $Conteudo_Campo_Editado=formata_valor_gravar($Conteudo_Campo_Editado,$mConfig['C020_Casas_Decimais_Quantidade']+1);

   mysql_query("update T059 set $Campo_Editado='$Conteudo_Campo_Editado' where T059_Id='$T059_Id'");
}
elseif ($Campo_Editado=='T174_Flag_Gerar_OC')
{

   if ($Conteudo_Campo_Editado=='true')
   {
       $Conteudo_Campo_Editado='1';
   }
   else
   {
       $Conteudo_Campo_Editado='2';
   }

   mysql_query("update T174 set $Campo_Editado='$Conteudo_Campo_Editado' where T174_Id='$T174_Id'");
}
elseif ($Campo_Editado=='T174_D009_Id')
{

   mysql_query("update T174 set $Campo_Editado='$Conteudo_Campo_Editado' where T174_Id='$T174_Id'");

}
elseif ($Campo_Editado=='T174_Flag_Indisponivel')
{
	if($Conteudo_Campo_Editado == "true"){
		$Conteudo_Campo_Editado="S";
	} else {
		$Conteudo_Campo_Editado="N";
	}
   mysql_query("update T174 set $Campo_Editado='$Conteudo_Campo_Editado' where T174_Id='$T174_Id'");

}
else if($Campo_Editado=='T174_Valor_ST'){

   $Conteudo_Campo_Editado=formata_valor_gravar($Conteudo_Campo_Editado,3);
   mysql_query("update T174 set $Campo_Editado='$Conteudo_Campo_Editado' where T174_Id='$T174_Id'");
}
else if($Campo_Editado ==  "T010_Codigo_Incoterm"){
   $Conteudo_Campo_Editado=addslashes($Conteudo_Campo_Editado);
   mysql_query("update T010 set $Campo_Editado='$Conteudo_Campo_Editado' where T010_Id='$T010_Id'");
}
else if($Campo_Editado ==  "T174_ICMS"){
   $Conteudo_Campo_Editado=formata_valor_gravar($Conteudo_Campo_Editado,3);
   mysql_query("update T174 set $Campo_Editado='$Conteudo_Campo_Editado' where T174_Id='$T174_Id'");
}
else if($Campo_Editado == "T173_D036_Id"){
   mysql_query("update T173 set $Campo_Editado='$Conteudo_Campo_Editado' where T173_Id='$T173_Id'");
}
else if($Campo_Editado == "T173_Data_Entrega"){
   mysql_query("update T173 set $Campo_Editado='$Conteudo_Campo_Editado' where T173_Id='$T173_Id'");
}
else if($Campo_Editado == "T173_Forma_Pagamento"){
   $Conteudo_Campo_Editado=StrToMaiusculo($Conteudo_Campo_Editado);
   mysql_query("update T173 set $Campo_Editado='$Conteudo_Campo_Editado' where T173_Id='$T173_Id'");
}
else if($Campo_Editado == "T174_Flag_ST"){
   mysql_query("update T174 set $Campo_Editado='$Conteudo_Campo_Editado' where T174_Id='$T174_Id'");
}
else if($Campo_Editado == "T174_Flag_Origem_Produto"){
   mysql_query("update T174 set $Campo_Editado='$Conteudo_Campo_Editado' where T174_Id='$T174_Id'");
}
else if($Campo_Editado == "T173_Tipo_Transporte"){
   mysql_query("update T173 set $Campo_Editado='$Conteudo_Campo_Editado' where T173_Id='$T173_Id'");
}
else if($Campo_Editado == "T174_Prazo_Entrega_Dias"){
   mysql_query("update T174 set $Campo_Editado='$Conteudo_Campo_Editado' where T174_Id='$T174_Id'");
}
else if(!empty($D049_Id)){
   $Conteudo_Campo_Editado=addslashes($Conteudo_Campo_Editado);
   mysql_query("update D049 set $Campo_Editado='$Conteudo_Campo_Editado' where D049_Id='$D049_Id'");
} 
else
{
   $Conteudo_Campo_Editado=StrToMaiusculo($Conteudo_Campo_Editado);
   $Conteudo_Campo_Editado=addslashes($Conteudo_Campo_Editado);

   mysql_query("update T174 set $Campo_Editado='$Conteudo_Campo_Editado' where T174_Id='$T174_Id'");
}


//$cSql=mysql_query("call T174_Gravar_Totalizacao('$T174_Id')");

echo "<script>";
echo "parent.MostrarMensagem('none');";
echo "</script>";

?>
