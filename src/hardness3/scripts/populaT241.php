<?php
namespace hardness;

$confUsuario = array(
	'dbDatabase' => 'dev_e234',
	'dbHost' => '127.0.0.1',
	'dbUser' => 'root',
	'dbPass' => 'hardness',
);
$con=@mysqli_connect($confUsuario['dbHost'], $confUsuario['dbUser'], $confUsuario['dbPass'],$confUsuario['dbDatabase']);
$Error=mysqli_connect_error($con);
if (!empty($Error)) { echo $Error; die(); }
$g['conexaoBanco'] = $con;
// $dbs=mysql_select_db($confUsuario['dbDatabase'], $con) or die (mysql_error());
mysql_set_charset('utf8');
mysqli_query('SET NAMES utf8;');

include('../bibliotecas/funcoesGerais.php');

$count1 = 0;
$D001 = array();
$sqlD009 = mysql_query("SELECT D009_Id, D001_Id FROM D009 LEFT JOIN D049 ON D049_Id=D009_D049_Id LEFT JOIN D001 ON D001_Id=D049_D001_Id GROUP BY D009_Id ORDER BY D009_Id");
while($rowD009 = mysql_fetch_array($sqlD009)){
	$sqlD024 = mysql_query("SELECT DISTINCT T001_D024_Id FROM T001 LEFT JOIN D006 ON D006_Id=T001_D006_Id WHERE T001_D009_Id = '{$rowD009['D009_Id']}' AND T001_Flag_Operacao = 'E' AND T001_D024_Id > 0 AND D006_Flag_Venda_Compra_Outros = 'C' AND D006_Flag_Atualiza_Custo = 'S'");
	while($rowD024 = mysql_fetch_array($sqlD024)){
		if(!in_array($rowD024['T001_D024_Id'], $D001[$rowD009['D001_Id']])){
			$D001[$rowD009['D001_Id']][] = $rowD024['T001_D024_Id'];
			mysqli_query("INSERT INTO T241 (T241_D024_Id, T241_D001_Id, T241_Flag_Homologado) VALUES ('{$rowD024['T001_D024_Id']}', '{$rowD009['D001_Id']}', 'S')");
			$count1++;
		}
	}
}
echo "Fornecedores cadastrados: {$count1}";
