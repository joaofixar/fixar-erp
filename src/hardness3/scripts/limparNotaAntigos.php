<?php
namespace hardness;
die("Não está mais em uso.");

$confUsuario = array(
	'dbDatabase' => 'e001',
	'dbHost' => '192.168.10.3',
	'dbUser' => 'root',
	'dbPass' => '',
);
$con=@mysqli_connect($confUsuario['dbHost'], $confUsuario['dbUser'], $confUsuario['dbPass'],$confUsuario['dbDatabase']);
$Error=mysqli_connect_error($con);
if (!empty($Error)) { echo $Error; die(); }
// $dbs=mysql_select_db($confUsuario['dbDatabase'], $con) or die (mysql_error());
mysql_set_charset('utf8');
mysqli_query('SET NAMES utf8;');

ini_set('display_errors', 1);
error_reporting(E_ALL);

$sql = <<<EOT
SELECT 
    DISTINCT C025_Banco_Dados,
    C025_Id,
    D024_Id,
    D024_Nome_Empresa
FROM
    T002 
    LEFT JOIN D024 
        ON D024_Id = T002_D024_Id 
    LEFT JOIN D027 
        ON D027_Id = T002_D027_Id 
    LEFT JOIN T117 
        ON T117_Id = T002_T117_Id 
    LEFT JOIN D014 
        ON D014_Id = T002_D014_Id 
    LEFT JOIN D032 
        ON D032_Id = T002_D032_Id 
    LEFT JOIN D046 
        ON D046_Id = D024_D046_Id 
    LEFT JOIN T007 
        ON T007_Id = T002_T007_Id 
    LEFT JOIN T035 
        ON T035_Id = T002_T035_Id 
    LEFT JOIN C007 
        ON C007_Id = T007_C007_Id_Vendedor_Interno 
    LEFT JOIN T103 
        ON T002_Id = T103_T002_Id 
    LEFT JOIN C025 ON C025_D024_Id=D024_Id
WHERE (
        (1 = 1) 
        AND (
            (
                T002_Data_Vencimento >= '2006-01-01 00:00:00' 
                AND T002_Data_Vencimento <= '2013-06-01 23:59:59'
            )
        ) 
        AND (
            (
                T002_Data_Recebimento IS NULL 
                OR T002_Data_Recebimento = '' 
                OR T002_Data_Recebimento = '0000-00-00' 
                OR T002_Data_Recebimento = '0000-00-00 00:00:00'
            )
        ) 
        AND (
            (
                T002_Data_Baixa IS NULL 
                OR T002_Data_Baixa = '' 
                OR T002_Data_Baixa = '0000-00-00' 
                OR T002_Data_Baixa = '0000-00-00 00:00:00'
            )
        )
    ) 
    AND (
        T002_C004_Id = 1 
        AND (
            T002_T002_Id_Agrupado <= 0 
            OR T002_T002_Id_Agrupado IS NULL
        )
    ) 
    AND C025_Banco_Dados IS NOT NULL
EOT;
$res = mysql_query($sql);
while ($row = mysql_fetch_assoc($res)) {
	$sql = "SELECT T007_Data_Emissao FROM `{$row['C025_Banco_Dados']}`.T007 ORDER BY T007_Data_Emissao DESC LIMIT 1";
	$resT007 = mysql_query($sql);
	$rowT007 = mysql_fetch_array($resT007);
	if (empty($rowT007['T007_Data_Emissao']) || strtotime($rowT007['T007_Data_Emissao']) < strtotime('2013-06-01')) {
		//echo "{$row['C025_Banco_Dados']} - {$row['D024_Id']} - {$rowT007['T007_Data_Emissao']} - {$row['D024_Nome_Empresa']} \n";
		$BD = strtolower($row['C025_Banco_Dados']);
		//echo "mysqldump --force --skip-lock-tables --events --routines --user=backup --password='' {$BD} | gzip > ./{$BD}.sql.gz\n";
		//echo "mv /var/www/hardness/dados_usuarios/{$BD} ./\n";
		//echo "UPDATE C025 SET C025_Flag_Removido = '1' WHERE C025_Id = '{$row['C025_Id']}';\n";
		echo "DROP DATABASE {$BD};\n";
	}
}
