<?php
namespace hardness;

set_time_limit(0);

require_once('sistema/bi.php');

echo "Inicio B.I. Novo " . date("H:i:s") . "\n<br />";

BI::carregaEstrutura();

/****************************************************
 * Nota Fiscal de Entrada
 ****************************************************/

$anos = 4;
if(isset($anosProcessarBI)){
    // essa variavel pode ser definida no confUsuario
	$anos = $anosProcessarBI;
}
$anoPesquisa = date('Y');
$mesPesquisa = date('m');
echo "Notas Fiscais...\n<br/>";
while ($anos > 0) {
    while ($mesPesquisa > 0) {
        // Corrigir: MySQL server has gone away
        $con = mysqli_init();
        $con -> options(MYSQLI_OPT_CONNECT_TIMEOUT, 3600);
        $con -> real_connect($confUsuario['dbHost'], $confUsuario['dbUser'], $confUsuario['dbPass'], $confUsuario['dbDatabase']);
        $g['conexaoBanco'] = $con;

        $ultimoDiaMes = $anoPesquisa . '-' . str_pad($mesPesquisa, 2, "0", STR_PAD_LEFT) . '-' . date("t", mktime(0,0,0,$mesPesquisa,'01',$anoPesquisa));
        $primeiroDiaMes = $anoPesquisa . '-' . str_pad($mesPesquisa, 2, "0", STR_PAD_LEFT)  . '-' . '01';

        echo "\n<br/>Pesquisando {$primeiroDiaMes} - {$ultimoDiaMes}\n<br />";
        $sql = <<<EOT
            SELECT /*+max_execution_time(3600000)*/
                T007_Id,
                T008_Id,
                T008_Valor_Total_Preco,
                T008_Quantidade,
                T008_Valor_ICMS,
                T008_Valor_ICMS_Substituicao_Retencao,
                T008_Valor_IPI,
                T008_Valor_PIS,
                T008_Valor_COFINS,
                T007_Data_Emissao,
                T007_Data_Saida,
                T007_Data_Vencimento,
                T007_Flag_Expedicao,
                T007_Flag_Cancelada,
                D024_Id,
                D024_Nome_Empresa,
                D024_Nome_Fantasia,
                D024_Endereco,
                D024_Cep,
                D024_Bairro,
                D024_Cidade,
                D018_UF,
                D020_Nome_Cidade,
                Vendedor.C007_Login as Vendedor_C007_Login,
                Vendedor.C007_Email as Vendedor_C007_Email,
                Vendedor.C007_Nome as Vendedor_C007_Nome,
                Vendedor.C007_Primeiro_Nome as Vendedor_C007_Primeiro_Nome,
                Vendedor.C007_Id as Vendedor_C007_Id,
                Supervisor.C007_Login as Supervisor_C007_Login,
                Supervisor.C007_Email as Supervisor_C007_Email,
                Supervisor.C007_Nome as Supervisor_C007_Nome,
                Supervisor.C007_Primeiro_Nome as Supervisor_C007_Primeiro_Nome,
                Supervisor.C007_Id as Supervisor_C007_Id,
                D001_Descricao_Produto,
                D082_Marca,
                C008_Tipo,
                D003_Nome_Linha,
                D060_Descricao_Grupo,
                D002_Descricao_Produto,
                D001_Codigo_Produto,
                D006_Id,
                D006_Codigo_CFOP,
                D006_Descricao,
                D006_Flag_Entrada_Saida,
                D006_Flag_Devolucao,
                D006_Flag_Venda_Compra_Outros,
                D006_Flag_Gera_Contas,
                D006_Flag_Estoque,
                D021_Nome_Empresa,
                C004_Nome_Empresa,
                C004_Nome_Abreviado,
                D009_Id,
                C004_Id
            FROM T007
            LEFT JOIN C007 as Vendedor ON Vendedor.C007_Id=T007_C007_Id_Vendedor_Interno
            LEFT JOIN C007 as Supervisor ON Supervisor.C007_Id=Vendedor.C007_C007_Id
            LEFT JOIN D006 ON D006_Id=T007_D006_Id
            LEFT JOIN T008 ON T008_T007_Id=T007_Id
            LEFT JOIN D024 ON T007_D024_Id=D024_Id
            LEFT JOIN D018 ON D018_Id=D024_D018_Id
            LEFT JOIN D020 ON D020_Id=D024_D020_Id
            LEFT JOIN D009 ON D009_Id=T008_D009_Id
            LEFT JOIN D049 ON D049_Id=D009_D049_Id 
            LEFT JOIN D001 ON D001_Id=D049_D001_Id 
            LEFT JOIN C008 ON C008_Id=D001_C008_Id 
            LEFT JOIN D082 ON D082_Id=D049_D082_Id 
            LEFT JOIN D003 ON D003_Id=D001_D003_Id
            LEFT JOIN D002 ON D002_Id=D001_D002_Id
            LEFT JOIN D060 ON D060_Id=D001_D060_Id
            LEFT JOIN D021 ON D021_Id=T007_D021_Id
            LEFT JOIN C004 ON C004_Id=T007_C004_Id
            WHERE T007_Data_Emissao >= '{$primeiroDiaMes}' and T007_Data_Emissao <= '{$ultimoDiaMes}'
            GROUP BY T008_Id
EOT;
        $result = mysqli_query($sql);
        echo mysqli_error();
        if (!$result) { die('Invalid query: ' . mysqli_error() . "\n<br/>SQL: " . $sql); }
        $mesPesquisa--;
        $count = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            $registro = [];

            $registro['T008_Valor_Total_Preco'] = $row['T008_Valor_Total_Preco'];
            $registro['T008_Quantidade'] = $row['T008_Quantidade'];
            $registro['T008_Valor_ICMS'] = $row['T008_Valor_ICMS'];
            $registro['T008_Valor_ICMS_Substituicao_Retencao'] = $row['T008_Valor_ICMS_Substituicao_Retencao'];
            $registro['T008_Valor_IPI'] = $row['T008_Valor_IPI'];
            $registro['T008_Valor_PIS'] = $row['T008_Valor_PIS'];
            $registro['T008_Valor_COFINS'] = $row['T008_Valor_COFINS'];
            $registro['T007_Id'] = $row['T007_Id'];
            $registro['T008_Id'] = $row['T008_Id'];
            $registro['T007_Flag_Expedicao'] = $row['T007_Flag_Expedicao'];
            $registro['T007_Flag_Cancelada'] = $row['T007_Flag_Cancelada'];
            $registro['T007_Data_Emissao'] = $row['T007_Data_Emissao'];
            $registro['T007_Data_Saida'] = $row['T007_Data_Saida'];
            $registro['T007_Data_Vencimento'] = $row['T007_Data_Vencimento'];

            BI::preencherRegistroData($row['T007_Data_Emissao'], $registro);
            // T007_Data_Saida
            // T007_Data_Vencimento

            $registro['D024_Id'] = $row['D024_Id'];
            $registro['D024_Nome_Empresa'] = $row['D024_Nome_Empresa'];
            $registro['D024_Nome_Fantasia'] = $row['D024_Nome_Fantasia'];
            $registro['D024_Endereco'] = $row['D024_Endereco'];
            $registro['D024_Cep'] = $row['D024_Cep'];
            $registro['D024_Bairro'] = $row['D024_Bairro'];
            $registro['D024_Cidade'] = $row['D024_Cidade'];
            $registro['D018_UF'] = $row['D018_UF'];
            $registro['D020_Nome_Cidade'] = $row['D020_Nome_Cidade'];

            $registro['Vendedor_C007_Login'] = $row['Vendedor_C007_Login'];
            $registro['Vendedor_C007_Email'] = $row['Vendedor_C007_Email'];
            $registro['Vendedor_C007_Nome'] = $row['Vendedor_C007_Nome'];
            $registro['Vendedor_C007_Primeiro_Nome'] = $row['Vendedor_C007_Primeiro_Nome'];
            $registro['Vendedor_C007_Id'] = $row['Vendedor_C007_Id'];
            $registro['Supervisor_C007_Login'] = $row['Supervisor_C007_Login'];
            $registro['Supervisor_C007_Email'] = $row['Supervisor_C007_Email'];
            $registro['Supervisor_C007_Nome'] = $row['Supervisor_C007_Nome'];
            $registro['Supervisor_C007_Primeiro_Nome'] = $row['Supervisor_C007_Primeiro_Nome'];
            $registro['Supervisor_C007_Id'] = $row['Supervisor_C007_Id'];

            $registro['D009_Id'] = $row['D009_Id'];
            $registro['D082_Marca'] = $row['D082_Marca'];
            $registro['C008_Tipo'] = $row['C008_Tipo'];
            $registro['D003_Nome_Linha'] = $row['D003_Nome_Linha'];
            $registro['D060_Descricao_Grupo'] = $row['D060_Descricao_Grupo'];
            $registro['D002_Descricao_Produto'] = $row['D002_Descricao_Produto'];
            $registro['D001_Codigo_Produto'] = $row['D001_Codigo_Produto'];
            $registro['D001_Descricao_Produto'] = $row['D001_Descricao_Produto'];

            $registro['D006_Id'] = $row['D006_Id'];
            $registro['D006_Codigo_CFOP'] = $row['D006_Codigo_CFOP'];
            $registro['D006_Descricao'] = $row['D006_Descricao'];
            $registro['D006_Flag_Entrada_Saida'] = $row['D006_Flag_Entrada_Saida'];
            $registro['D006_Flag_Devolucao'] = $row['D006_Flag_Devolucao'];
            $registro['D006_Flag_Venda_Compra_Outros'] = $row['D006_Flag_Venda_Compra_Outros'];
            $registro['D006_Flag_Gera_Contas'] = $row['D006_Flag_Gera_Contas'];
            $registro['D006_Flag_Estoque'] = $row['D006_Flag_Estoque'];

            $registro['D021_Nome_Empresa'] = $row['D021_Nome_Empresa'];

            $registro['C004_Id'] = $row['C004_Id'];
            $registro['C004_Nome_Empresa'] = $row['C004_Nome_Empresa'];
            $registro['C004_Nome_Abreviado'] = $row['C004_Nome_Abreviado'];

            BI::incluirRegistro($registro);

            $count++;
            if (($count % 1000) == 0) {
                echo "($count) ";
            }
        }
    }
    $mesPesquisa = 12;
    $anoPesquisa--;
    $anos--;
}

BI::processarIncluirRegistro();

echo "\n<br />";
echo "The End! " . date("H:i:s"). "\n<br />";
