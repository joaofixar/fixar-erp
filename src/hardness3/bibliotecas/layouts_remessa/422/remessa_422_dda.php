<?php
namespace hardness;

//-- NOME DA TELA :
//-- SERA CRIADO UM ARQUIVO PHP DENTRO DA PASTA DADOS_USUARIOS COMO O NOME QUE FOR DEFINIDO
//-- SERVE PARA GUARDAR OS FILTROS E CONDICOES QUE O USUARIO ESCOLHER, PORTANTO,
//-- PARA CADA FILTRO, O IDEIAL É QUE TENHA UM NOME DIFERENTE

//$cNomeTelaFiltro='receber';
//include "../include/include_filtro.php";

DEFINE("_SALTO_LINHA",CHR(13).CHR(10));

$cClausula  = " select ";
$cClausula .= " D007_C004_Id, ";
$cClausula .= " D007_D062_Id, ";
$cClausula .= " C004_Cnpj, ";
$cClausula .= " C004_Nome_Empresa, ";
$cClausula .= " D007_Numero_Agencia, ";
$cClausula .= " D007_Digito_Verificador_Agencia, ";
$cClausula .= " D007_Numero_Conta, ";
$cClausula .= " D007_Digito_Verificador_Conta ";
$cClausula .= " from D007 ";
$cClausula .= " left join C004 on C004_Id=D007_C004_Id ";
$cClausula .= " where D007_Id='$D007_Id' ";
if (!empty($xConsulta_Clausula_Filtro))
{
    $Clausula .= " and ".$xConsulta_Clausula_Filtro;
}

$D007 = mysql_query($cClausula);
$mD007=mysql_fetch_array($D007);

if (strlen($mD007['D007_Numero_Agencia'])<=0)
{
   return "Não informado o número da agência no cadastro de contas";
}
if (strlen($mD007['D007_Numero_Agencia'])>4)
{
   return "Número da agência deve ter no máximo 4 caracteres";
}
if (strlen($mD007['D007_Numero_Conta'])<=0)
{
   return "Não informado o número da conta no cadastro de contas";
}
if (strlen($mD007['D007_Numero_Conta'])>5)
{
   return "Número da conta deve ter no máximo 5 caracteres";
}
if (strlen($mD007['D007_Digito_Verificador_Conta'])<=0)
{
   return "Não informado o digito verificador da conta no cadastro de contas";
}
if (strlen($mD007['D007_Digito_Verificador_Conta'])>2)
{
   return "Dígito verificador da conta deve ter no máximo 2 caracteres";
}

$cClausula  = " select ";
$cClausula .= " T002_Id, ";
$cClausula .= " T002_Numero_Duplicata, ";
$cClausula .= " T002_Numero_Documento, ";
$cClausula .= " T002_Valor_Total, ";
$cClausula .= " date_format(T002_Data_Vencimento,'%d%m%y') as T002_Data_Vencimento, ";
$cClausula .= " D024_Flag_Pessoa_Fisica_Juridica, ";
$cClausula .= " D024_Cnpj, ";
$cClausula .= " D024_Cpf, ";
$cClausula .= " D024_Nome_Empresa, ";
$cClausula .= " if(D024_Cobranca_Rua!='',concat(ifnull(D024_Cobranca_Rua,''),' ',ifnull(D024_Cobranca_Complemento,'')),D024_Endereco) D024_Endereco, ";
$cClausula .= " if(D024_Cobranca_Rua!='','',D024_Numero) D024_Numero, ";
$cClausula .= " if(D024_Cobranca_Bairro!='',D024_Cobranca_Bairro,D024_Bairro) D024_Bairro, ";
$cClausula .= " if(D024_Cobranca_Rua!='',D024_Cobranca_Cep,D024_Cep) D024_Cep, ";
$cClausula .= " D020_Nome_Cidade, ";
$cClausula .= " D018_UF ";
$cClausula .= " from T002 ";
$cClausula .= " left join D024 on D024_Id=T002_D024_Id ";
$cClausula .= " left join D020 on D020_Id=if(ifnull(D024_D020_Cobranca,0)>0,D024_D020_Cobranca,D024_D020_Id) ";
$cClausula .= " left join D018 on D018_Id=D020_D018_Id ";
$cClausula .= " left join D027 on D027_Id=T002_D027_Id ";
$cClausula .= " left join T117 on T117_Id=T002_T117_Id ";
$cClausula .= " left join D014 on D014_Id=T002_D014_Id ";
$cClausula .= " left join D046 on D046_Id=D024_D046_Id ";
$cClausula .= " left join T007 on T007_Id=T002_T007_Id ";
$cClausula .= " left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno ";
$cClausula .= " where T002_C004_Id='{$g['empresaAtual']}' ";
$cClausula .= " and T002_Data_Vencimento!='0000-00-00' ";
$cClausula .= " and ifnull(T002_T035_Id,0)<=0 ";
$cClausula .= " and (D024_Flag_Pessoa_Fisica_Juridica='F' or D024_Flag_Pessoa_Fisica_Juridica='J')";
$cClausula .= " and T002_Flag_Enviar_Banco='S' ";
$cClausula .= " and (T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null)";
if (!empty($xConsulta_Clausula_Filtro))
{
   $cClausula .= " and ".$xConsulta_Clausula_Filtro;
}
$cClausula .= " order by D024_Id ";
echo $cClausula;
$cSql_T002 = mysql_query($cClausula);
$cErro=mysql_error();
if (!empty($cErro))
{
    return "Ocorreu um erro:$cErro/p/p$cClausula";
}
if (mysql_num_rows($cSql_T002)==0)
{
   return "Nenhum titulo encontrado";
}

while ($mT002 = mysql_fetch_array($cSql_T002))
{

    //-- ELIMINA CARACATERES INVALIDOS PARA CFP OU CNPJ
    if ($mT002['D024_Flag_Pessoa_Fisica_Juridica']=='F')
    {
       $cCpfCnpjTipo='01';
       $mT002['D024_Cpf'] = trim($mT002['D024_Cpf']);
       $cCpfCnpj  = str_replace(array(".","-","/"," "),"",$mT002['D024_Cpf']);
    }
    elseif ($mT002['D024_Flag_Pessoa_Fisica_Juridica']=='J')
    {
       $cCpfCnpjTipo='02';
       $mT002['D024_Cnpj'] = trim($mT002['D024_Cnpj']);
       $cCpfCnpj  = str_replace(array(".","-","/"," "),"",$mT002['D024_Cnpj']);
    }

    //-- ELIMINA CARACATERES INVALIDOS PARA CEP
    $mT002['D024_Cep'] = trim($mT002['D024_Cep']);
    $mT002['D024_Cep'] = str_replace(array(".","-"," "),"",$mT002['D024_Cep']);
    
    //-- AGREGA O CAMPO NUMERO AO ENDERECO
    if (!empty($mT002['D024_Numero']))
    {
       $mT002['D024_Endereco'] = $mT002['D024_Endereco'].', '.$mT002['D024_Numero'];
    }
    
    if (empty($mT002['D024_Endereco'])) { return "A Empresa {$mT002['D024_Nome_Empresa']} está sem Endereço. Remessa cancelada!"; }
    if (empty($mT002['D024_Bairro'])) { return "A Empresa {$mT002['D024_Nome_Empresa']} está sem Bairro. Remessa cancelada!"; }
    if (empty($mT002['D024_Cep'])) { return "A Empresa {$mT002['D024_Nome_Empresa']} está sem Cep. Remessa cancelada!"; }
    if (empty($mT002['D020_Nome_Cidade'])) { return  "A Empresa {$mT002['D024_Nome_Empresa']} está sem Cidade. Remessa cancelada!"; }

}

$cClausula  = " select ";
$cClausula .= " T002_Id, ";
$cClausula .= " T002_Numero_Duplicata, ";
$cClausula .= " T002_Numero_Documento, ";
$cClausula .= " T002_Valor_Total, ";
$cClausula .= " date_format(T002_Data_Vencimento,'%d%m%y') as T002_Data_Vencimento, ";
$cClausula .= " D024_Flag_Pessoa_Fisica_Juridica, ";
$cClausula .= " D024_Cnpj, ";
$cClausula .= " D024_Cpf, ";
$cClausula .= " D024_Nome_Empresa, ";
$cClausula .= " if(D024_Cobranca_Rua!='',concat(ifnull(D024_Cobranca_Rua,''),' ',ifnull(D024_Cobranca_Complemento,'')),D024_Endereco) D024_Endereco, ";
$cClausula .= " if(D024_Cobranca_Rua!='','',D024_Numero) D024_Numero, ";
$cClausula .= " if(D024_Cobranca_Bairro!='',D024_Cobranca_Bairro,D024_Bairro) D024_Bairro, ";
$cClausula .= " if(D024_Cobranca_Rua!='',D024_Cobranca_Cep,D024_Cep) D024_Cep, ";
$cClausula .= " D020_Nome_Cidade, ";
$cClausula .= " D018_UF ";
$cClausula .= " from T002 ";
$cClausula .= " left join D024 on D024_Id=T002_D024_Id ";
$cClausula .= " left join D020 on D020_Id=if(ifnull(D024_D020_Cobranca,0)>0,D024_D020_Cobranca,D024_D020_Id) ";
$cClausula .= " left join D018 on D018_Id=D020_D018_Id ";
$cClausula .= " left join D027 on D027_Id=T002_D027_Id ";
$cClausula .= " left join T117 on T117_Id=T002_T117_Id ";
$cClausula .= " left join D014 on D014_Id=T002_D014_Id ";
$cClausula .= " left join D046 on D046_Id=D024_D046_Id ";
$cClausula .= " left join T007 on T007_Id=T002_T007_Id ";
$cClausula .= " left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno ";
$cClausula .= " where T002_C004_Id='{$g['empresaAtual']}' ";
$cClausula .= " and T002_Data_Vencimento!='0000-00-00' ";
$cClausula .= " and ifnull(T002_T035_Id,0)<=0 ";
$cClausula .= " and (D024_Flag_Pessoa_Fisica_Juridica='F' or D024_Flag_Pessoa_Fisica_Juridica='J')";
$cClausula .= " and T002_Flag_Enviar_Banco='S' ";
$cClausula .= " and (T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null)";
if (!empty($xConsulta_Clausula_Filtro))
{
   $cClausula .= " and ".$xConsulta_Clausula_Filtro;
}
$cClausula .= " order by D024_Id ";
$cSql_T002 = mysql_query($cClausula);
$cErro=mysql_error();
if (!empty($cErro))
{
    return "Ocorreu um erro:$cErro/p/p$cClausula";
}
if (mysql_num_rows($cSql_T002)==0)
{
   return "Nenhum titulo encontrado";
}

//-- ELIMINA O ARQUIVO CASO O MESMO EXISTA
$cArquivo='/tmp/remessa_dda_341.txt';
if (file_exists($cArquivo))
{
   unlink("$cArquivo");
}

//-- Abre o arquivo
$cArquivo_Aberto = fopen( $cArquivo, 'x' );

//-- Abre a tela de download do arquivo
/*
header('Content-type: application/x-msdownload');
header('Content-Disposition: attachment; filename='.basename($cArquivo));
header('Pragma: no-cache');
header('Expires: 0');
*/

//-- INICIA UMA SESSAO NO MYSQL
mysql_query("set autocommit=0");
mysql_query("start transaction");

$cClausula  = " insert into T035 ( ";
$cClausula .= " T035_D007_Id,  ";
$cClausula .= " T035_C004_Id, ";
$cClausula .= " T035_D062_Id, ";
$cClausula .= " T035_Data_Processamento ";
$cClausula .= " ) values (  ";
$cClausula .= " '$D007_Id',  ";
$cClausula .= " '{$mD007['D007_C004_Id']}', ";
$cClausula .= " '{$mD007['D007_D062_Id']}', ";
$cClausula .= " now() ";
$cClausula .= " )  ";
$cT035 = mysql_query($cClausula);
$cErro=mysql_error();
if (!empty($cErro))
{
    mysql_query("ROLLBACK");
    return "Ocorreu um erro:$cErro/p/p$cClausula";
} 
$nT035_Id = $g['mysqlLastId'];
$lastId = $nT035_Id;

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//-- INICIO DA EXPORTACAO DOS DADOS....
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$cClausula = " select * from C004 where C004_Id='{$g['empresaAtual']}' ";
$cSql_C004 = mysql_query($cClausula);
$mC004 = mysql_fetch_array($cSql_C004);
$cErro=mysql_error();
if (!empty($cErro))
{
    mysql_query("ROLLBACK");
    return "Ocorreu um erro:$cErro/p/p$cClausula";
}

$mC004['C004_Nome_Empresa'] = substr($mC004['C004_Nome_Empresa'],0,30);
$mC004['C004_Nome_Empresa'] = str_pad($mC004['C004_Nome_Empresa'] ,30," ",STR_PAD_RIGHT);

$cContaCorrente = "291002";
$cHoje = date("dmy",time());

$nSeq=1;
$nSequencial = str_pad($nSeq  , 6,"0",STR_PAD_LEFT);

$cString  = "";
$cString .= "01";
$cString .= "REMESSA";
$cString .= "01";
$cString .= "COBRANCA       ";
$cString .= "385800291002        ";
$cString .= "$mC004[C004_Nome_Empresa]";
$cString .= "341";
$cString .= "BANCO ITAU SA  ";
$cString .= $cHoje;
$cString .= str_repeat(" ",290);
$cString .= "S";
$cString .= "110";
$cString .= $nSequencial;

fwrite($cArquivo_Aberto, $cString ._SALTO_LINHA);

while ($mT002 = mysql_fetch_array($cSql_T002))
{
    $cClausula  = " update T002 set ";
    $cClausula .= " T002_T035_Id='$nT035_Id'  ";
    $cClausula .= " where T002_Id='{$mT002['T002_Id']}' ";
    mysqli_query($cClausula);
    $cErro=mysql_error();
    if (!empty($cErro))
    {
        mysql_query("ROLLBACK");
        return "Ocorreu um erro:$cErro/p/p$cClausula";
    }

    //-- NUMERO SEQUENCIAL PARA CADA LINHA DO ARQUIVO (CONFORME LEIAUTE)
    $nSeq++;
    $nSequencial = str_pad($nSeq  , 6,"0",STR_PAD_LEFT);
    
    if (!empty($mT002['T002_Numero_Documento']))
    {
       $xc="93";
    }
    else
    {
       $xc="  ";
    }

    //-- ELIMINA CARACATERES INVALIDOS PARA CFP OU CNPJ
    if ($mT002['D024_Flag_Pessoa_Fisica_Juridica']=='F')
    {
       $cCpfCnpjTipo='01';
       $mT002['D024_Cpf'] = trim($mT002['D024_Cpf']);
       $cCpfCnpj  = str_replace(array(".","-","/"," "),"",$mT002['D024_Cpf']);
    }
    elseif ($mT002['D024_Flag_Pessoa_Fisica_Juridica']=='J')
    {
       $cCpfCnpjTipo='02';
       $mT002['D024_Cnpj'] = trim($mT002['D024_Cnpj']);
       $cCpfCnpj  = str_replace(array(".","-","/"," "),"",$mT002['D024_Cnpj']);
    }

    //-- ELIMINA CARACATERES INVALIDOS PARA CEP
    $mT002['D024_Cep'] = trim($mT002['D024_Cep']);
    $mT002['D024_Cep'] = str_replace(array(".","-"," "),"",$mT002['D024_Cep']);
    
    //-- AGREGA O CAMPO NUMERO AO ENDERECO
    if (!empty($mT002['D024_Numero']))
    {
       $mT002['D024_Endereco'] = $mT002['D024_Endereco'].', '.$mT002['D024_Numero'];
    }
    
    $xValorMora = ($mT002['T002_Valor_Total'] / 500);
    
    $mT002['T002_Valor_Total'] = str_replace(".","",$mT002['T002_Valor_Total']);
    $xValorMora              = str_replace(".","",round($xValorMora,2));
    
    if (empty($mT002['D024_Endereco'])) { return "A Empresa {$mT002['D024_Nome_Empresa']} está sem Endereço. Remessa cancelada!"; }
    if (empty($mT002['D024_Bairro'])) { return "A Empresa {$mT002['D024_Nome_Empresa']} está sem Bairro. Remessa cancelada!"; }
    if (empty($mT002['D024_Cep'])) { return "A Empresa {$mT002['D024_Nome_Empresa']} está sem Cep. Remessa cancelada!"; }
    if (empty($mT002['D020_Nome_Cidade'])) { return "A Empresa {$mT002['D024_Nome_Empresa']} está sem Cidade. Remessa cancelada!"; }

    //-- CORTA O CONTEUDO DO CAMPO SE FOR MAIOR DO QUE O QUE ESTA DEFINIDO NO LEIAUTE
    $mT002['D024_Nome_Empresa']     = substr($mT002['D024_Nome_Empresa']    ,0,30);
    $mT002['D024_Endereco']         = substr($mT002['D024_Endereco']        ,0,40);
    $mT002['D024_Bairro']           = substr($mT002['D024_Bairro']          ,0,12);
    $mT002['D024_Cep']              = substr($mT002['D024_Cep']             ,0, 8);
    $mT002['D020_Nome_Cidade']      = substr($mT002['D020_Nome_Cidade']     ,0,15);
    $mT002['D018_UF']               = substr($mT002['D018_UF']              ,0, 2);
    $mT002['T002_Numero_Duplicata'] = substr($mT002['T002_Numero_Duplicata'],0,10);
    $mT002['T002_Numero_Documento'] = substr($mT002['T002_Numero_Documento'],0,30);

    //-- ACRESCENTA ESPAÇOS OU ZEROS SE O CONTEUDO DO CAMPO FOR MENOR DO QUE ESTA DEFINIDO NO LEIAUTE
    $cCpfCnpj                     = str_pad($cCpfCnpj                    ,14,"0",STR_PAD_LEFT);
    $mT002['D024_Nome_Empresa']     = str_pad($mT002['D024_Nome_Empresa']    ,30," ",STR_PAD_RIGHT);
    $mT002['D024_Endereco']         = str_pad($mT002['D024_Endereco']        ,40," ",STR_PAD_RIGHT);
    $mT002['D024_Bairro']           = str_pad($mT002['D024_Bairro']          ,12," ",STR_PAD_RIGHT);
    $mT002['D024_Cep']              = str_pad($mT002['D024_Cep']             , 8," ",STR_PAD_RIGHT);
    $mT002['D020_Nome_Cidade']      = str_pad($mT002['D020_Nome_Cidade']     ,15," ",STR_PAD_RIGHT);
    $mT002['D018_UF']               = str_pad($mT002['D018_UF']              , 2," ",STR_PAD_RIGHT);
    $mT002['T002_Numero_Duplicata'] = str_pad($mT002['T002_Numero_Duplicata'],10," ",STR_PAD_RIGHT);
    $mT002['T002_Numero_Documento'] = str_pad($mT002['T002_Numero_Documento'],30," ",STR_PAD_RIGHT);
    $mT002['T002_Valor_Total']      = str_pad($mT002['T002_Valor_Total']     ,13,"0",STR_PAD_LEFT);
    $xValorMora                   = str_pad($xValorMora                  ,13,"0",STR_PAD_LEFT);
    
    //-- CRIA A LINHA DA COBRANCA
    $cString  = "";
    $cString .= "10000000000000000385800";
    $cString .= $cContaCorrente;
    $cString .= "                                 ";
    $cString .= "000000000000000000000112";
    $cString .= "                     ";
    $cString .= "I01";
    $cString .= $mT002['T002_Numero_Duplicata'];
    $cString .= $mT002['T002_Data_Vencimento'];
    $cString .= $mT002['T002_Valor_Total'];
    $cString .= "341";
    $cString .= "0000001";
    $cString .= "N";
    $cString .= $cHoje;
    $cString .= "81";
    $cString .= $xc;
    $cString .= $xValorMora;
    $cString .= str_repeat(" ",6);
    $cString .= str_repeat("0",39);
    $cString .= $cCpfCnpjTipo;
    $cString .= $cCpfCnpj;
    $cString .= $mT002['D024_Nome_Empresa'];
    $cString .= "          ";
    $cString .= $mT002['D024_Endereco'];
    $cString .= $mT002['D024_Bairro'];
    $cString .= $mT002['D024_Cep'];
    $cString .= $mT002['D020_Nome_Cidade'];
    $cString .= $mT002['D018_UF'];
    $cString .= $mT002['T002_Numero_Documento'];
    $cString .= "    ";
    $cString .= $mT002['T002_Data_Vencimento'];
    $cString .= "01 ";
    $cString .= $nSequencial;
    
    fwrite($cArquivo_Aberto, $cString ._SALTO_LINHA);
}

// -- RODAPE
$nSeq++;
$nSequencial = str_pad($nSeq  , 6,"0",STR_PAD_LEFT);

$cString  = "";
$cString .= "9";
$cString .= str_repeat(" ",393);
$cString .= $nSequencial;

fwrite($cArquivo_Aberto, $cString ._SALTO_LINHA);

fclose($cArquivo_Aberto);
$Ponteiro=fopen($cArquivo, "r");
$Conteudo=fread($Ponteiro, filesize($cArquivo));

$Conteudo=str_replace("'"," ",$Conteudo);

$cClausula  = " update T035 set ";
$cClausula .= " T035_Conteudo_Arquivo='$Conteudo'  ";
$cClausula .= " where T035_Id='$nT035_Id' ";
mysql_query($cClausula);
$cErro=mysql_error();
if (!empty($cErro))
{
    mysql_query("ROLLBACK");
    return "Ocorreu um erro:$cErro/p/p$cClausula";
}
mysql_query("COMMIT");

//echo $Conteudo;

?>
