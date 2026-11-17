<?php
namespace hardness;
die("Não está mais em uso.");

$confUsuarioERP = array(
	'dbDatabase' => 'global',
	'dbHost' => '187.45.202.72',
	'dbUser' => 'root',
	'dbPass' => '$$hard@@0982',
	//'dbPass' => 'hardness',
);
$conERP=@mysql_connect($confUsuarioERP['dbHost'], $confUsuarioERP['dbUser'], $confUsuarioERP['dbPass']);
$Error=mysql_error();
if (!empty($Error)) { echo $Error; die(); }
mysql_select_db($confUsuarioERP['dbDatabase'], $conERP) or die (mysql_error());
mysql_set_charset('utf8');
mysql_query('SET NAMES utf8;');

$confUsuarioNOTA = array(
	'dbDatabase' => 'global',
	'dbHost' => '127.0.0.1',
	'dbUser' => 'root',
	'dbPass' => '!hard@3871',
);
$conNOTA=mysql_connect($confUsuarioNOTA['dbHost'], $confUsuarioNOTA['dbUser'], $confUsuarioNOTA['dbPass']);
$Error=mysql_error();
if (!empty($Error)) { echo $Error; die(); }
mysql_select_db($confUsuarioNOTA['dbDatabase'], $conNOTA) or die (mysql_error());
mysql_set_charset('utf8', $conNOTA);
mysql_query('SET NAMES utf8;', $conNOTA);
mysql_query("USE e001", $conNOTA);

// Verificações
$argv = $_SERVER['argv'];
if (!isset($argv[1]) || empty($argv[1])) {
	echo "Faltou o parametro com o Banco de Dados a ser transferido.\n";
	die();
}
$sql = "SELECT * FROM C025 WHERE LOWER(C025_Banco_Dados) = '{$argv[1]}'";
$res = mysql_query($sql, $conNOTA);
if (mysql_num_rows($res) != 1) {
	echo "Banco invalido.\n";
	die();
}
$C025 = mysql_fetch_assoc($res);
if (empty($C025['C025_Dominio'])) {
	echo "C025_Dominio em branco para este Banco.\n";
	die();
}
if (empty($C025['C025_Flag_Transferido'])) {
	echo "Banco ja transferido.\n";
	die();
}

if ($C025['C025_Dominio'] == 'encaixe' || $C025['C025_Dominio'] == 'maximus' || $C025['C025_Dominio'] == 'sigma') {
	echo "Conflito de dominio.\n";
	die();
}

echo "{$argv[1]} ...";

// Remove o acesso ao dominio
$cmd = "ssh -p 2022 root@{$confUsuarioERP['dbHost']} \"chmod 000 /var/www/sites/{$C025['C025_Dominio']}.hardness.com.br-*\"";
shell_exec($cmd);
//echo $cmd . "\n";

// Copia BANCO
$cmd = 'ionice -c3 rm -f /tmp/tmp.sql';
shell_exec($cmd);
//echo $cmd . "\n";
$cmd = "echo 'DROP DATABASE {$argv[1]}; CREATE DATABASE IF NOT EXISTS {$argv[1]}; USE {$argv[1]};' > /tmp/tmp.sql && mysqldump --host='{$confUsuarioERP['dbHost']}' --user='{$confUsuarioERP['dbUser']}' --password='{$confUsuarioERP['dbPass']}' --routines {$argv[1]} >> /tmp/tmp.sql";
shell_exec($cmd);
//echo $cmd . "\n";
$cmd = "ionice -c3 mysql --user='{$confUsuarioNOTA['dbUser']}' --password='{$confUsuarioNOTA['dbPass']}' < /tmp/tmp.sql";
shell_exec($cmd);
//echo $cmd . "\n";

// Cria usuario no banco
//mysql_query("DROP USER '{$argv[1]}'@'localhost';", $conERP);
//mysql_query("FLUSH PRIVILEGES;", $conERP);
//mysql_query("CREATE USER '{$argv[1]}'@'localhost' IDENTIFIED BY '{$C025['C025_Login']}';", $conERP);
//mysql_query("FLUSH PRIVILEGES;", $conERP);
//mysql_query("GRANT ALL PRIVILEGES ON `{$argv[1]}`.* TO '{$argv[1]}'@'localhost' WITH GRANT OPTION;", $conERP);
//mysql_query("GRANT ALL PRIVILEGES ON `global`.* TO '{$argv[1]}'@'localhost' WITH GRANT OPTION;", $conERP);
//mysql_query("FLUSH PRIVILEGES;", $conERP);

// Altera C004_Localizacao_Certificados
$sql = "USE {$argv[1]}";
mysql_query($sql, $conNOTA);
//echo $sql . "\n";
$sql = "UPDATE C004 SET C004_Localizacao_Certificados = '/var/www/hardness/dados_usuarios/{$argv[1]}/nfe/certs/'";
mysql_query($sql, $conNOTA);
//echo $sql . "\n";

// Dados usuarios
$cmd = "ionice -c3 rsync -av -e \"ssh -p 2022\" root@187.45.202.72:/var/www/dados/v2/dados_usuarios/{$argv[1]} /var/www/hardness/dados_usuarios/";
shell_exec($cmd);
//echo $cmd . "\n";

// Flag
$sql = "USE e001";
mysql_query($sql, $conNOTA);
//echo $sql . "\n";
$sql = "UPDATE C025 SET C025_Flag_Transferido = 0 WHERE C025_Id = '{$C025['C025_Id']}'";
$res = mysql_query($sql, $conNOTA);
//echo $sql . "\n";

echo " pronto!\n";