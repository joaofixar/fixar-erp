<?php
namespace hardness;
global $g;
// abre uma conex�o no servidor
//$con=@mysqli_connect('localhost','root','#qos!0982');
//$dbs=mysql_select_db("e009",$con) or die (mysql_error());
$Empresa_Atual=2;
$Diretorio_Empresa="../hardness/dados_usuarios/e009";
$Diretorio_Imagens="../hardness/dados_usuarios/e009/imagens";
$cArquivo_Cabecalho_Relatorio="../hardness/dados_usuarios/e009/cabecalho_relatorio.php";

//-- Fun��es Gen�ricas em PHP
//require "../hardness/func/funcoes_genericas.inc";
$Campo_Editado=$r_Campo_Editado;
$Conteudo_Campo_Editado=$r_Conteudo_Campo_Editado;
$T219_Id=$r_T219_Id;
$T218_Id=$r_T218_Id;
$T010_Id=$r_T010_Id;
$D049_Id=$r_D049_Id;
$T219A_Id=$r_T219A_Id;
$D024_Id=$r_D024_Id;

if ($g['C031']['habilitaEmail'] == 'S') { // Verifica se o parâmetro global está habilitado
   if($Campo_Editado == "T218A_Desconto_Cotacao"){ //Grava os campos modificados
      $Conteudo_Campo_Editado=gCorrigeNumeroInverte($Conteudo_Campo_Editado,5);
      $update = mysql_query("update T218A set $Campo_Editado='{$Conteudo_Campo_Editado}' where T218A_T218_Id='{$T218_Id}'");
   }
   if($Campo_Editado == "T218A_Flag_Desconto"){
      mysql_query("update T218A set $Campo_Editado='{$Conteudo_Campo_Editado}' where T218A_T218_Id='{$T218_Id}'");
      mysql_query("update T218A set T218A_Flag_Desconto_Tela='{$Conteudo_Campo_Editado}' where T218A_T218_Id='{$T218_Id}'");

   }
  // Seleciona o valor do desconto e o tipo de desconto
  $selectDesconto = "select 
                        T218A_Desconto_Cotacao,
                        T218A_Flag_Desconto
                     from 
                        T218A 
                     where 
                        T218A_T218_Id='{$T218_Id}'";
$dadosDesconto = mysql_query($selectDesconto);
$varDesconto = mysql_fetch_assoc($dadosDesconto);

$flag = $varDesconto['T218A_Flag_Desconto'];
$desconto = $varDesconto['T218A_Desconto_Cotacao'];
   
   //É necessário que o tipo e o valor do desconto estejam selecionados para fazer os cálculos
   if(!empty($desconto) and !is_null($desconto) and $desconto != 0 and !is_null($flag)){
      $selectPreco = "select 
                           T219_Valor_Preco_Unitario,
                           T219_Id,
                           T218_Valor_Total_Produtos
                        from 
                           T218A
                        left join T218 on T218A_T218_Id = T218_Id
                        left join T219 on T219_T218_Id = T218_Id
                        where 
                           T218A_T218_Id ='{$T218_Id}'";
      $dadosPreco = mysql_query($selectPreco);

      while($varPreco = mysql_fetch_assoc($dadosPreco)){
         $unitario  = $varPreco['T219_Valor_Preco_Unitario'];
         $total     = $varPreco['T218_Valor_Total_Produtos'];
         $T219_Id   = $varPreco['T219_Id'];

         if($flag == 'R'){  // Se o desconto for em reais, calcula a porcentagem em relação ao total.
            $descPercentual = $desconto/$total;
         }else{ //Se o desconto for em porcentagem, só transforma em valor decimal.
            $descPercentual = $desconto/100;
         }
         
         // Calcula o valor restante 
         $descPercentual = 1 - $descPercentual;

         // Aplica o desconto ao valor unitário
         $unitario = $unitario*$descPercentual;

         //Grava o valor unitário. O total é gravado automaticamente pelo resto do sistema.
         mysql_query("update T219 set T219_Valor_Preco_Unitario='{$unitario}' where T219_Id='{$T219_Id}'");

         mysql_query("update T218A set T218A_Flag_Desconto = NULL where T218A_T218_Id='{$T218_Id}'");
         

      }
   }


}

if ($Campo_Editado=='T219_Valor_Preco_Unitario')
{
   $Conteudo_Campo_Editado=gCorrigeNumeroInverte($Conteudo_Campo_Editado,5);

   mysql_query("update T219 set $Campo_Editado='$Conteudo_Campo_Editado', T219_Valor_Preco_Original='$Conteudo_Campo_Editado' where T219_Id='$T219_Id'");
   
   if ($g['C031']['habilitaEmail'] == 'S') { 
      /*************************************************************************************************************
       * SE O USUÁRIO ALTEROU O PREÇO UNITÁRIO, O DESCONTO NÃO DEVE APARECER NA TELA DE CABEÇALHO PARA A EMPRESA.
      *************************************************************************************************************/
      $update = mysql_query("update 
                              T218A
                              join T218 on T218A_T218_Id = T218_Id 
                              join T219 on T219_T218_Id = T218_Id 
                           set 
                              T218A_Flag_Desconto = NULL,
                              T218A_Desconto_Cotacao = 0,
                              T218A_Flag_Desconto_Tela = NULL 
                           where 
                              T219_Id='{$T219_Id}'");
   }

   log("update T219 set $Campo_Editado='$Conteudo_Campo_Editado' where T219_Id='$T219_Id'");

//    echo <<<EOT
//    <script type='text/javascript'>
//       alert("update T219 set $Campo_Editado='$Conteudo_Campo_Editado', T219_Valor_Preco_Unitario='$Conteudo_Campo_Editado'where T219_Id='$T219_Id'");
//    </script>
// EOT;
   
}
elseif($Campo_Editado=='T219_Aliquota_IPI' or $Campo_Editado=='T219_Aliquota_ICMS' or $Campo_Editado=='T219_Valor_ST' or $Campo_Editado=='T219_ST_VA')
{

   $Conteudo_Campo_Editado=formataValorGravar($Conteudo_Campo_Editado,5);

   mysql_query("update T219 set $Campo_Editado='$Conteudo_Campo_Editado' where T219_Id='$T219_Id'");
   
}
elseif ($Campo_Editado=='T219_Quantidade')
{

   $Conteudo_Campo_Editado=formataValorGravar($Conteudo_Campo_Editado,4);

   mysql_query("update T219 set $Campo_Editado='$Conteudo_Campo_Editado' where T219_Id='$T219_Id'");
}
elseif ($Campo_Editado=='T219_D009_Id')
{

   mysql_query("update T219 set $Campo_Editado='$Conteudo_Campo_Editado' where T219_Id='$T219_Id'");

}
elseif ($Campo_Editado=='T219_Flag_Indisponivel')
{
   if($Conteudo_Campo_Editado == "true"){
      $Conteudo_Campo_Editado="S";
   } else {
      $Conteudo_Campo_Editado="N";
   }
   mysql_query("update T219 set $Campo_Editado='$Conteudo_Campo_Editado' where T219_Id='$T219_Id'");
}
else if($Campo_Editado=='T219_Valor_ST'){

   $Conteudo_Campo_Editado=formataValorGravar($Conteudo_Campo_Editado,3);
   mysql_query("update T219 set $Campo_Editado='$Conteudo_Campo_Editado' where T219_Id='$T219_Id'");
}
else if($Campo_Editado=='T219_Valor_ST_Digitado'){

   $Conteudo_Campo_Editado=formataValorGravar($Conteudo_Campo_Editado,3);
   mysql_query("update T219 set $Campo_Editado='$Conteudo_Campo_Editado' where T219_Id='$T219_Id'");
}
else if($Campo_Editado ==  "T219_Valor_ICMS"){
   $Conteudo_Campo_Editado=formataValorGravar($Conteudo_Campo_Editado,3);
   mysql_query("update T219 set $Campo_Editado='$Conteudo_Campo_Editado' where T219_Id='$T219_Id'");
}
else if($Campo_Editado == "T218_D036_Id"){
   mysql_query("update T218 set $Campo_Editado='$Conteudo_Campo_Editado' where T218_Id='$T218_Id'");
}
else if($Campo_Editado == "T218_Data_Entrega"){
   mysql_query("update T218 set $Campo_Editado='$Conteudo_Campo_Editado' where T218_Id='$T218_Id'");
}
else if($Campo_Editado == "T218_Observacao"){
   mysql_query("update T218 set $Campo_Editado='$Conteudo_Campo_Editado' where T218_Id='$T218_Id'");
}
else if($Campo_Editado == "T218_Prazo_Entrega"){
   mysql_query("update T218 set $Campo_Editado='$Conteudo_Campo_Editado' where T218_Id='$T218_Id'");
}
else if($Campo_Editado == "T218_Forma_Pagamento"){
   mysql_query("update T218 set $Campo_Editado='$Conteudo_Campo_Editado' where T218_Id='$T218_Id'");
}
else if($Campo_Editado == "T219_Flag_ST"){
   mysql_query("update T219 set $Campo_Editado='$Conteudo_Campo_Editado' where T219_Id='$T219_Id'");
}
else if($Campo_Editado == "T218_Tipo_Transporte"){
   mysql_query("update T218 set $Campo_Editado='$Conteudo_Campo_Editado' where T218_Id='$T218_Id'");
}
else if($Campo_Editado == "T219_Observacao_Entrega"){
   mysql_query("update T219 set $Campo_Editado='$Conteudo_Campo_Editado' where T219_Id='$T219_Id'");
}
else if($Campo_Editado == "T218_Flag_Frete"){
   mysql_query("update T218 set $Campo_Editado='$Conteudo_Campo_Editado' where T218_Id='$T218_Id'");
}
else if($Campo_Editado == "T218_Nome_Transportadora"){
   mysql_query("update T218 set $Campo_Editado='$Conteudo_Campo_Editado' where T218_Id='$T218_Id'");
}
else if($Campo_Editado == "T219_Codigo_Substituto"){
   mysql_query("update T219 set $Campo_Editado='$Conteudo_Campo_Editado' where T219_Id='$T219_Id'");
}
else if($Campo_Editado == "T219_Prazo_Entrega_Dias"){
   mysql_query("update T219 set $Campo_Editado='$Conteudo_Campo_Editado' where T219_Id='$T219_Id'");
}
else if($Campo_Editado == "T219A_Data_Entrega"){
   mysql_query("update T219A set $Campo_Editado='$Conteudo_Campo_Editado' where T219A_Id='{$T219A_Id}'");
}else if($Campo_Editado == "T219A_Prazo_Faturamento"){
   mysql_query("update T219A set $Campo_Editado='$Conteudo_Campo_Editado' where T219A_Id='{$T219A_Id}'");
}else if($Campo_Editado == "D024_Valor_Limite"){
   mysql_query("update D024 set $Campo_Editado='$Conteudo_Campo_Editado' where D024_Id='{$D024_Id}'");
}
else if(!empty($D049_Id)){
   $Conteudo_Campo_Editado=addslashes($Conteudo_Campo_Editado);
   mysql_query("update D049 set $Campo_Editado='$Conteudo_Campo_Editado' where D049_Id='$D049_Id'");
} 
else
{
   $Conteudo_Campo_Editado=StrToMaiusculo($Conteudo_Campo_Editado);
   $Conteudo_Campo_Editado=addslashes($Conteudo_Campo_Editado);

   mysql_query("update T219 set $Campo_Editado='$Conteudo_Campo_Editado' where T219_Id='$T219_Id'");
}
if($Campo_Editado == "opcaoUsuario"){   
   mysql_query("update T218 set T218_Forma_Pagamento='$Conteudo_Campo_Editado' where T218_Id='$T218_Id'");
}

//$cSql=mysql_query("call T219_Gravar_Totalizacao('$T219_Id')");

echo "<script>";
echo "parent.MostrarMensagem('none');";
echo "</script>";

// Formata o Valor Digitado Para Gravar Corretamente no Banco
function formataValorGravar($valor,$mil) {

   return str_replace(',','.',str_replace('.','',$valor));

}

?>
