<?php
namespace hardness;
function mostrarMsgErro($nNumeroErro,$cArquivo_Assinado="",$cRetornoSefaz="")
{
	global $nT276_Id;
	global $Usuario_Atual;
	global $oMDFe;
  global $xml;
	
  $cClausula = " select * from global.T137 where T137_Numero_Erro='$nNumeroErro'";
  $cSql = mysqli_query($cClausula);
  $mT137 = mysql_fetch_array($cSql);

  if (empty($mT137))
  {
    msg_erro_MDFe(200, $nNumeroErro, false, $cRetornoSefaz);
  }
  $cClausula  = " select ";
  $cClausula .= " T276_Numero_Protocolo_MDFe ";
  $cClausula .= " from T276 ";
  $cClausula .= " where T276_Id='$nT276_Id' ";
  $cSql_T276=mysqli_query($cClausula);
  $mT276=mysql_fetch_array($cSql_T276);
   
	if ($mT137['T137_Flag_Refazer_Chave_Acesso']=='S' and $nT276_Id>0)
	{
		if (!empty($mT276['T276_Numero_Protocolo_MDFe']))
		{
			msg_erro_MDFe(201, $nNumeroErro, true, $cRetornoSefaz);
			return false;
		}
		
		$cClausula  = " update T276 set ";
		$cClausula .= " T276_Chave_Acesso_MDFe='',";
		$cClausula .= " T276_Numero_Recibo_MDFe=NULL,";
		$cClausula .= " T276_Data_Hora_Recebimento_MDFe='0000-00-00 00:00:00' ";
		$cClausula .= " where T276_Id='$nT276_Id'";
		mysqli_query($cClausula);
		$cErro = mysql_error();
		if (!empty($cErro))
		{
			msg_erro_MDFe(117);
		  return false;
		}
		
	}
									                                     
  if (empty($cArquivo_Assinado))
  {
    $cClausula  = " select ";
    $cClausula .= " T276_Chave_Acesso_MDFe ";
    $cClausula .= " from T276 ";
    $cClausula .= " where T276_Id='$nT276_Id' ";
    $cSql_T276=mysqli_query($cClausula);
    $mT276=mysql_fetch_array($cSql_T276);
    $cArquivo_Assinado=$oMDFe->getPastaArquivoXML()."assinado/MDFe".$mT276['T276_Chave_Acesso_MDFe'].'.xml';
  }
                    
  $xml = file_get_contents($cArquivo_Assinado);
  msg_erro_MDFe(202, $nNumeroErro, true, $cRetornoSefaz);
}
