<?php
/**
* Servidor de LOG
*/
$home = __FILE__;
$home = str_replace('scripts/queueNF', '', dirname($home));
chdir($home);
require 'scripts/queueNF/conf.php';
require 'scripts/queueNF/codigos.php';

// Conexão com o banco
$con = mysqli_connect( $confUsuario['dbHost'], $confUsuario['dbUser']  , $confUsuario['dbPass'],$confUsuario['dbDatabase']);
echo mysqli_connect_error($con)."/n";

mysqli_set_charset('utf8');
mysqli_query('SET NAMES utf8;');
echo mysqli_error($con)."/n";

echo $_REQUEST['log']."\n";

$log = unserialize($_REQUEST['log']);

foreach ($log as &$value) {
        $value = mysqli_real_escape_string($con,$value);
}
$sql = <<<EOT
INSERT INTO Q002
(Q002_Servidor_Id, Q002_Database, Q002_C004_Id, Q002_T007_Id, Q002_Etapa, Q002_Codigo_Resposta, Q002_Resposta_Extra, Q002_Soap_Debug, Q002_Numero_NF, Q002_Retorno_Sefaz, Q002_T007_Codigo_CFOP, Q002_T007_Serie, Q002_T007_D024_Nome_Empresa, Q002_T007_Data_Emissao, Q002_C004_Nome_Abreviado, Q002_C004_Telefone, Q002_Url, Q002_DataHora, Q002_T007_Valor_Total)
VALUES
('{$log['Servidor_Id']}', '{$log['Database']}', '{$log['C004_Id']}', '{$log['T007_Id']}', '{$log['Etapa']}', '{$log['Codigo_Resposta']}', '{$log['Resposta_Extra']}', '{$log['Soap_Debug']}', '{$log['Numero_NF']}', '{$log['Retorno_Sefaz']}', '{$log['T007_Codigo_CFOP']}', '{$log['T007_Serie']}', '{$log['T007_D024_Nome_Empresa']}', '{$log['T007_Data_Emissao']}', '{$log['C004_Nome_Abreviado']}', '{$log['C004_Telefone']}', '{$log['Url']}', NOW(), '{$log['T007_Valor_Total']}');
EOT;

mysqli_query($con,$sql);
echo mysqli_error($con);
$Q002_Id = mysqli_insert_id($con);
echo mysqli_error($con);

// Q003
$Q003_Md5 = mysqli_real_escape_string($con,md5($log['Database'] . $log['C004_Id'] . $log['T007_Id']));
$sql = "SELECT * FROM Q003 WHERE Q003_Md5 = '{$Q003_Md5}';";
$res = mysqli_query($con,$sql);
if (mysqli_num_rows($res) == 0) {
        $xml = !empty($log['XML']) ? $log['XML'] : '';
        $sql = "INSERT INTO Q003 (Q003_Md5, Q003_Q002_Id, Q003_XML) VALUES ('{$Q003_Md5}', '{$Q002_Id}', '{$xml}');";
} else {
        $xml = !empty($log['XML']) ? ", Q003_XML = '{$log['XML']}' " : '';
        $sql = "UPDATE Q003 SET Q003_Q002_Id = '{$Q002_Id}'{$xml} WHERE Q003_Md5 = '{$Q003_Md5}';";
}
mysqli_query($con,$sql);
echo mysqli_error($con);
