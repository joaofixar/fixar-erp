<?php
namespace hardness;

// Busca os dados da empresa usuária
$cSql=mysql_query("select * from C004 where C004_Id='{$g['empresaAtual']}'");
$mC004=mysql_fetch_array($cSql);

$echo = <<<EOT
<html>
<head>
    <title>Inventário</title>
    <meta name='ROBOTS' content='NOINDEX,NOFOLLOW'>
    <meta http-equiv='Pragma' content='no-cache'>
    <meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1'>
    <link rel=stylesheet href="/hardness/classes/classe_tamanho_medio_firefox.css" type="text/css">
    <!-- Funções Genéricas em Javascript -->
    <script src='/hardness/func/funcoes_genericas.js'></script>
    <script src='/hardness/func/ajax.js'></script>

    <style type="text/css"><!--
      A:link {text-decoration:none; color:000000}
      A:visited {text-decoration:none; color:000000}
      A:hover {text-decoration:underline; color:000000; cursor:hand}
      A:active {text-decoration:underline; color:000000; cursor:hand}

      .fonte
      {
       font-family:arial,verdade;
       font-size:11px;
   }
    --></style>
</head>
<body bottommargin=0 topmargin=0 rightmargin=0 leftmargin=0 bgcolor='white'>
EOT;

$Valor_Preco=$r_Valor_Preco;
$Valores=$r_Valores;
$Estoque = $r_Estoque;
$Ordem=$r_Ordem;
$Departamento=$r_Departamento;
$Setor=$r_Setor;
$Grupo=$r_Grupo;
$Subgrupo=$r_Subgrupo;
$nacimpo=$r_nacimpo;
$Condicao='';
$Filtro_Fornecedor='';

function Hoje(){
    return date("Y-m-d");
}
function agora(){
    return date("d/m/Y H:m:s");
}

if (empty($r_Data)) {
    $r_Data=Hoje();
}

$xData=$r_Data;

$r_Data=gCorrigeDataInverte($r_Data);

//Arquivo CSV
if(!empty($r_CSV)){
    $dir = $g['pathDadosUsuario'] . 'tmp/';
    is_dir($dir) or mkdir($dir, 0777, true);
    $file = $dir . "inventario_{$r_Data}.csv";
    $fp = fopen($file, 'w');
}

if ($xData==Hoje()) {
    $Saldo = "D009_Quantidade_Estoque_Real";
    $Custo = "D009_Valor_Custo_Unitario";
} else {
    $Saldo = "(select T001_Quantidade_Saldo_Mes_Anterior from T001 where T001_D009_Id=D009_Id and T001_Data_Lancamento>'$r_Data' limit 1)";
    if($Valores == 2){
        $Custo = "(select T001_Valor_Custo_Medio_Mes_Anterior from T001 where T001_D009_Id=D009_Id and T001_Data_Lancamento>'$r_Data' limit 1)";
    } else {
        $Custo = "(select T001_Valor_Custo_Mes_Anterior from T001 where T001_D009_Id=D009_Id and T001_Data_Lancamento>'$r_Data' limit 1)";
    }
}

if ($r_Fornecedor>0) {
    $Filtro_Fornecedor="and D049_D024_Id='$r_Fornecedor'";
}

if ($Departamento>0) {
    $filtroDepartamento="and D001_C008_Id='$Departamento'";
}
if ($Setor>0) {
    $filtroSetor="and D001_D060_Id='$Setor'";
}
if ($Grupo>0) {
    $filtroGrupo="and D001_D003_Id='$Grupo'";
}
if ($Subgrupo>0) {
    $filtroSubGrupo="and D001_D002_Id='$Subgrupo'";
}

if($nacimpo=='N'){
    $Filtro_Nacional_Importado = " AND (D049_Origem_Mercadoria='0' OR D049_Origem_Mercadoria='3' OR D049_Origem_Mercadoria='4' OR D049_Origem_Mercadoria='5')";
} else if($nacimpo=='I'){
    $Filtro_Nacional_Importado = " AND (D049_Origem_Mercadoria='1' OR D049_Origem_Mercadoria='2' OR D049_Origem_Mercadoria='6' OR D049_Origem_Mercadoria='7')";
} else {
    $Filtro_Nacional_Importado = '';
}

log($filtroDepartamento);
log($filtroSetor);
log($filtroGrupo);
log($filtroSubGrupo);
log($Filtro_Fornecedor);
log($Filtro_Nacional_Importado);

//
// CABEÇALHO
//
$echo .= "<table align=center border='0' valign=top  width='100%' cellpadding=3 cellspacing=0 bgcolor=white style='border-bottom:1px dashed silver'>";
    $echo .= "<tr>";
        $echo .= "<td valign=top>";
            $echo .= "<table align=center border='0' cellspacing=0 cellpading=0 width='100%' height=10 valign=top>";
                $echo .= "<tr>";
                    $echo .= "<td bgcolor=white width=80%>";
                        //-- $cArquivo_Cabecalho_Relatorio é definido no arquivo include/pasta_usuario.inc
                        //include $cArquivo_Cabecalho_Relatorio;
                        $Diretorio_Imagens="/hardness/dados_usuarios/{$confUsuario['dbDatabase']}/imagens/";
                        if(empty($r_CSV)){
                            include $g['pathDadosAntigo']."/cabecalho_relatorio.php";
                        }
                    $echo .= "</td>";
                    $echo .= "<td bgcolor=white align=center width=20% rowspan=2>";
                        $echo .= "<font class='titulo_relatorio'>Inventário</font><font class=fonte><br><b>$xData</b>";
                        $echo .= "</font><br>";
                    $echo .= "</td>";
                $echo .= "</tr>";
                $echo .= "<tr bgcolor=white>";
                    $echo .= "<td colspan=2>";
                        $echo .= "<font class=fonte>Impresso</b> &nbsp;<b>".agora()."</b></font>";
                    $echo .= "</td>";
                $echo .= "</tr>";
            $echo .= "</table>";
        $echo .= "</td>";
    $echo .= "</tr>";
    $echo .= "<tr>";
    $echo .= "<td>";

    $select = "SELECT D003_Nome_Linha,
                      D002_Descricao_Produto,
                      D001_Codigo_Produto,
                      D001_Descricao_Produto,
                      $Saldo Saldo,
                      D001_Peso_Unitario_Kg,
                      if($Valor_Preco=1,D041_Valor_Preco_Revenda,if($Valor_Preco=2,D041_Valor_Base,D041_Valor_Preco_Consumidor)) Preco,
                          $Custo Custo,
                      (select T001_Id from T001 where T001_D009_Id=D009_Id and T001_Data_Lancamento>'$r_Data' limit 1) ultimoID,
                      D009_Valor_Custo_Ultima_Compra,
                      D009_Valor_Custo_Tabela,
                      D009_Valor_Preco_Tabela,
                      D009_Valor_Custo_Medio_Unitario,
                      D009_Quantidade_Estoque,
                      D037_Unidade,
                      D001_Id,
                      D009_Data_Ultima_Entrada,
                      D024_Nome_Empresa,
                      D049_Flag_Nacional_Importado,
                      D005_Classificacao_Fiscal
                 from D001
            left join D005 on D001_D005_Id=D005_Id
            left join D049 on D049_D001_Id=D001_Id
            leFt join D009 on D009_D049_Id=D049_Id
            left join C008 on C008_Id=D001_C008_Id
            left join D003 on D003_Id=D001_D003_Id
            left join D002 on D002_Id=D001_D002_Id
            left join D060 on D060_Id=D001_D060_Id
            left join D037 on D037_Id=D001_D037_Id
            left join D024 on D024_Id=D049_D024_Id
            left join D041 on D041_D049_Id=D049_Id
                where true
                      $filtroDepartamento
                      $filtroSetor
                      $filtroGrupo
                      $filtroSubGrupo
                      $Filtro_Fornecedor
                      $Filtro_Nacional_Importado
                  and D009_C004_Id='{$g['empresaAtual']}'
             order by D060_Descricao_Grupo,$Ordem";
    $D001=mysql_query($select);
    log($select);
    echo mysql_error();

    //
    // CABECALHO DAS COLUNAS
    //
    $echo .= "<table align=center border='0' cellpadding=2 cellspacing=1 width='100%' height='100%'>";
    $csv = array();
    $total=0;
    $count=1;
    $cor='#F4F4F4';
    $Grupo='XXX';

    $mPreco[1]='Revenda';
    $mPreco[2]='Atacado';
    $mPreco[3]='Varejo';

        $echo .= "<tr bgcolor='silver'>";
            $echo .= "<td align=left>";
                $echo .= "<font class=fonte>Item</font>";
                $csv[] = utf8_decode("Item");
            $echo .= "</td>";
            $echo .= "<td align=center>";
                $echo .= "<font class=fonte>Código</font>";
                $csv[] = utf8_decode("Código");
            $echo .= "</td>";
            $echo .= "<td align=center>";
                $echo .= "<font class=fonte>NCM</font>";
                $csv[] = utf8_decode("NCM");
            $echo .= "</td>";
            $echo .= "<td>";
                $echo .= "<font class=fonte>Descrição</font>";
                $csv[] = utf8_decode("Descrição");
            $echo .= "</td>";
            $echo .= "<td align=center>";
                $echo .= "<font class=fonte>UN</font>";
                $csv[] = utf8_decode("UN");
            $echo .= "</td>";
            $echo .= "<td align=right>";
                $echo .= "<font class=fonte>Saldo</font>";
                $csv[] = utf8_decode("Saldo");
            $echo .= "</td>";

        if ($Valores>0)
        {
            $echo .= "<td align=right>";
                $echo .= "<font class=fonte>Custo</font>";
                $csv[] = utf8_decode("Custo");
            $echo .= "</td>";
            $echo .= "<td align=right>";
                $echo .= "<font class=fonte>Total</font>";
                $csv[] = utf8_decode("Total");
            $echo .= "</td>";
        }

        if ($Valor_Preco>0)
        {
            $echo .= "<td align=right>";
                $echo .= "<font class=fonte>$mPreco[$Valor_Preco]</font>";
                $csv[] = utf8_decode($mPreco[$Valor_Preco]);
            $echo .= "</td>";
        }

            $echo .= "<td align=right>";
                $echo .= "<font class=fonte>Peso Kg</font>";
                $csv[] = utf8_decode("Peso Kg");
            $echo .= "</td>";
            $echo .= "<td align=right>";
                $echo .= "<font class=fonte>Total Kg</font>";
                $csv[] = utf8_decode("Total Kg");
            $echo .= "</td>";
        $echo .= "</tr>";
        //Gera CSV do cabeçalho do itens
        fputcsv($fp, $csv, ';');


        $cor='#E5E5E5';

    while ($mD001=mysql_fetch_array($D001)) {
        $csv = array();
        if ($mD001[ultimoID]<=0){
            $mD001[Saldo] = $mD001[D009_Quantidade_Estoque];
            if($Valores == 2){
                $mD001[Custo] = $mD001[D009_Valor_Custo_Medio_Unitario];
            } else {
                $mD001[Custo] = $mD001[D009_Valor_Custo_Ultima_Compra];
            }
        }

        if ($Estoque=='1' and $mD001[Saldo]<=0) {
            continue;
        } else if ($Estoque=='2' and ($mD001[Saldo]>0 or $mD001[Saldo]<0)) {
            continue;
        } else if ($Estoque=='3' and $mD001[Saldo]>=0) {
            continue;
        }

        $numero_linha++;
        unset($cor_unit);
        unset($cor_total);

        if ($mD001[Custo]<=0) {
            $cor_unit="style='color:black;'";
        }
        if (round($mD001[Saldo]*$mD001[Custo],2)<=0) {
            $cor_total="style='color:black;'";
        }

        if ($cor=='#E5E5E5') {
            $cor='white';
        } else {
            $cor='#E5E5E5';
        }

        $echo .= "<tr bgcolor='$cor'>";
            $echo .= "<td align=left>";
                $csv[] = $count;
                $echo .= "<font class=fonte>".$count++."</font>";
            $echo .= "</td>";
            $echo .= "<td align=center>";
                $echo .= "<font class=fonte>$mD001[D001_Codigo_Produto]</font>";
                $csv[] = $mD001['D001_Codigo_Produto'];
            $echo .= "</td>";
            $echo .= "<td align=center>";
                $echo .= "<font class=fonte>$mD001[D005_Classificacao_Fiscal]&nbsp;</font>";
                $csv[] = $mD001['D005_Classificacao_Fiscal'];
            $echo .= "</td>";
            $echo .= "<td>";
                $echo .= "<font class=fonte>$mD001[D001_Descricao_Produto]&nbsp;</font>";
                $csv[] = $mD001['D001_Descricao_Produto'];
            $echo .= "</td>";
            $echo .= "<td align=center>";
                $echo .= "<font class=fonte>$mD001[D037_Unidade]</font>";
                $csv[] = $mD001['D037_Unidade'];
            $echo .= "</td>";
            $echo .= "<td align=right>";
                $echo .= "<font class=fonte>".number_format($mD001['Saldo'],2,',','.')."</font>";
                $csv[] = number_format($mD001['Saldo'],2,',','.');
            $echo .= "</td>";

        if ($Valores>0)
        {
            $echo .= "<td align=right>";
                $echo .= "<font class=fonte $cor_unit>".number_format($mD001[Custo],2,',','.')."</font>";
                $csv[] = number_format($mD001[Custo],2,',','.');
            $echo .= "</td>";
            $echo .= "<td align=right>";
                $echo .= "<font class=fonte $cor_total>".number_format($mD001[Saldo]*$mD001[Custo],2,',','.')."</font>";
                $csv[] = number_format($mD001[Saldo]*$mD001[Custo],2,',','.');
            $echo .= "</td>";
        }

        if ($Valor_Preco>0)
        {
            $echo .= "<td align=right>";
                $echo .= "<font class=fonte $cor_unit>".number_format($mD001[Preco],2,',','.')."</font>";
                $csv[] = number_format($mD001[Preco],2,',','.');
            $echo .= "</td>";
        }

            $echo .= "<td align=right>";
                $echo .= "<font class=fonte $cor_unit>".number_format($mD001[D001_Peso_Unitario_Kg],3,',','.')."</font>";
                $csv[] = number_format($mD001[D001_Peso_Unitario_Kg],3,',','.');
            $echo .= "</td>";
            $echo .= "<td align=right>";
                $echo .= "<font class=fonte $cor_total>".number_format($mD001[Saldo]*$mD001[D001_Peso_Unitario_Kg],3,',','.')."</font>";
                $csv[] = number_format($mD001[Saldo]*$mD001[D001_Peso_Unitario_Kg],3,',','.');
            $echo .= "</td>";
            //Grava no CSV o item
            fputcsv($fp, $csv, ';');

        $total+=($mD001[Saldo]*$mD001[Custo]);
        $total_kg+=($mD001[Saldo]*$mD001[D001_Peso_Unitario_Kg]);

        $echo .= "</tr>";
        $Total_Itens++;
    }

    $csv = array();
    if ($cor=='#E5E5E5') {
        $cor='white';
    } else {
        $cor='#E5E5E5';
    }

        $echo .= "<tr bgcolor='$cor'>";
            $echo .= "<td align=left ";
            if ($Valores>0)
            {
                $echo .= " colspan=7 ";
                $colspan = 7;
            } else {
                $echo .= " colspan=6 ";
                $colspan = 6;
            }
            $echo .= ">";
            for($i=0;$i<$colspan;$i++){
                $csv[] = "";
            }
            $echo .= "</td>";

        if ($Valores>0)
        {
            $echo .= "<td align=right>";
                $echo .= "<font class=fonte><b>".number_format($total,2,',','.')."</b></font>";
                $csv[] = number_format($total,2,',','.');
            $echo .= "</td>";
        }

            $echo .= "<td align=left>";
                $echo .= "<font class=fonte></font>";
                $csv[] = "";
            $echo .= "</td>";

        if ($Valor_Preco>0)
        {
            $echo .= "<td align=left>";
                $echo .= "<font class=fonte></font>";
                $csv[] = "";
            $echo .= "</td>";
        }

            $echo .= "<td align=right>";
                $echo .= "<font class=fonte><b>".number_format($total_kg,3,',','.')."</b></font>";
                $csv[] = number_format($total_kg,3,',','.');
            $echo .= "</td>";
        $echo .= "</tr>";
        //Grava os totais no CSV
        fputcsv($fp, $csv, ';');
    $echo .= "</table>";

    //Persiste o arquivo no servidor
    if(!empty($r_CSV)){
        fclose($fp);
    }
        $echo .= "</td>";
    $echo .= "</tr>";
$echo .= "</table>";

//Verifica se o formato é CSV ou impresso
if(!empty($r_CSV)){
    header("Location: /hardness3/{$g['pathDadosUsuario']}tmp/inventario_{$r_Data}.csv");
} else {
    echo $echo;
}
