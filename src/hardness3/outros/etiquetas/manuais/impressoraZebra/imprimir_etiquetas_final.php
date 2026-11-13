<?php

// Cria ou inicia uma sessão
include "../include/sessao.inc";

// Conexão com o banco de dados
include "../include/conexao.inc";

// Funções Genéricas em PHP
include "../func/funcoes_genericas.inc";

include "../include/pasta_usuario.inc";

//-- $Diretorio_NFe é definido no arquivo include/pasta_usuario.inc
$cArquivo=$Diretorio_Usuario."/arq_temp/etiqueta.txt";

 $cSql_T006=sql_query("select T006_Codigo_Produto,
                                T006_Codigo_Substituto,
                                T006_Descricao_Produto,
                                D037_Unidade,
                                T006_Quantidade_Itens_Etiqueta T006_Quantidade,
                                D082_Marca,
                                T006_Quantidade_Etiqueta,
                                D024_Nome_Empresa
                           from T006
                      left join T005 on T006_T005_Id=T005_Id
                      left join D024 on D024_Id=T005_D024_Id
                      left join D037 on D037_Id=T006_D037_Id
                      left join D009 on D009_Id=T006_D009_Id
                      left join D049 on D049_Id=D009_D049_Id
                      left join D082 on D082_Id=D049_D082_Id
                          where T006_T005_Id='$T005_Id'
                            and (T006_T006_Id is null or T006_T006_Id<=0)
                            and (T006_Flag_Status!=3)
                            and (T006_Flag_Tipo_Produto='AA' or T006_Flag_Tipo_Produto='' or T006_Flag_Tipo_Produto is null)
                       order by (select group_concat(D004_Local) from T066 left join D004 on D004_Id=T066_D004_Id where T066_D009_Id=D009_Id),T006_D009_Id");
                       //and (T006_Flag_Tipo_Produto='AA' or T006_Flag_Tipo_Produto='' or T006_Flag_Tipo_Produto is null)
                            //and (T006_Quantidade>T006_Quantidade_Separacao or T006_Quantidade_Separacao is null)
 if (mysql_num_rows($cSql_T006)==0)
 {
    msg_alerta("Digite a quantidade");
    exit;
 }

while ($mT006=mysql_fetch_array($cSql_T006))
{

    if ($mT006[T006_Quantidade_Etiqueta]<=0)
    {
        continue;
    }

    if (!empty($mT006[T006_Codigo_Substituto]))
    {
       $mT006[T006_Codigo_Produto]=$mT006[T006_Codigo_Substituto];
    }
    
    $Qte_Etiq=$mT006[T006_Quantidade]/$mT006[T006_Quantidade_Etiqueta];
    
    $QteProd=$mT006[T006_Quantidade] / $Qte_Etiq;
    
    $Resto = $Qte_Etiq - ($mT006[T006_Quantidade]*$Qte_Prod);
    $xQt=0;
    for ($x=1;$x<=$Qte_Etiq;$x++)
    {
        $mLinha[]="^xa";
        $mLinha[]="^LH10,10";
        $mLinha[]="^FO50,220^A0N25,25,^FDCLIENTE: ".trim($mT006[D024_Nome_Empresa])."^FS";
        $mLinha[]="^FO50,260^A0N25,25,^FDMATERIAL: ".trim($mT006[T006_Descricao_Produto])." - ".trim($mT006[D082_Marca])."^FS";
        $mLinha[]="^FO50,300^A0N25,25,^FDQTDE: ".number_format($mT006[T006_Quantidade_Etiqueta],2,',','.')."^FS";
        $mLinha[]="^FO225,300^A0N25,25,^FD ".trim($mT006[D037_Unidade])."^FS";
        $mLinha[]="^FO350,300^A0N25,25,^FDCODIGO: ".trim($mT006[T006_Codigo_Produto])."^FS";
        $mLinha[]="^FT20,192^XGNEWLA000.GRF,1,1^FS";
        $mLinha[]="^PQ1,0,1,Y^XZ";

        $xQt+=$mT006[T006_Quantidade_Etiqueta];
    }
        if ($Resto>0 and ($mT006[T006_Quantidade]-$xQt)>0)
        {
            $mT006[T006_Quantidade_Etiqueta]=$mT006[T006_Quantidade]-$xQt;
            $mLinha[]="^xa";
            $mLinha[]="^LH10,10";
            $mLinha[]="^FO50,220^A0N25,25,^FDCLIENTE: ".trim($mT006[D024_Nome_Empresa])." - ".trim($mT006[D082_Marca])."^FS";
            $mLinha[]="^FO50,260^A0N25,25,^FDMATERIAL: ".trim($mT006[T006_Descricao_Produto])."^FS";
            $mLinha[]="^FO50,300^A0N25,25,^FDQTDE: ".number_format($mT006[T006_Quantidade_Etiqueta],2,',','.')."^FS";
            $mLinha[]="^FO225,300^A0N25,25,^FD ".trim($mT006[D037_Unidade])."^FS";
            $mLinha[]="^FO350,300^A0N25,25,^FDCODIGO: ".trim($mT006[T006_Codigo_Produto])."^FS";
            $mLinha[]="^FT20,192^XGNEWLA000.GRF,1,1^FS";
            $mLinha[]="^PQ1,0,1,Y^XZ";
        }

}

if (file_exists($cArquivo))
{
   unlink($cArquivo);
}

if (!file_put_contents($cArquivo,implode("\n",$mLinha)))
{
   msg_alerta("Não foi possivel gravar o arquivo $cArquivo");
}

eval("echo `lpr -Petiqueta $cArquivo`;");


?>
