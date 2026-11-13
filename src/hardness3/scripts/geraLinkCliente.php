<?php
die("Não está mais em uso.");
// para base:
$db = 'dev_e212';

// chave encrypt (não mudar)
$key = 'hard123';
$senha = md5($db . $key);
$linkCompacto = base64_url_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), 'C' . $db, MCRYPT_MODE_CBC, md5(md5($key))));
$linkNormal = base64_url_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), 'N' . $db, MCRYPT_MODE_CBC, md5(md5($key))));

// cria usuário no banco
$confUsuario = array(
	'dbDatabase' => $db,
	'dbHost' => '127.0.0.1',
	'dbUser' => 'root',
	'dbPass' => '',
);
$con=@mysql_connect($confUsuario['dbHost'], $confUsuario['dbUser'], $confUsuario['dbPass']);
$Error=mysql_error();
if (!empty($Error)) { echo $Error; die(); }
$dbs=mysql_select_db($confUsuario['dbDatabase'], $con) or die (mysql_error());
mysql_set_charset('utf8');
mysql_query('SET NAMES utf8;');

$locals = array('localhost', '%');
foreach ($locals as $local) {
	$result = mysql_query("drop user 'H{$db}'@'{$local}';");
	$result = mysql_query("create user 'H{$db}'@'{$local}' identified by '{$senha}';");
	$result = mysql_query("grant all privileges on {$db}.* to 'H{$db}'@'{$local}' ;");
	$result = mysql_query("grant all privileges on e001.A001 to 'H{$db}'@'{$local}' ;");
}

// Info
echo "\nBanco: {$db}\n";
echo "Senha do banco: {$senha}\n";
echo "Link Compacto: http://erp.hardness.com.br/{$linkCompacto}/\n";
echo "Link Normal: http://erp.hardness.com.br/{$linkNormal}/\n\n";

function base64_url_encode($input) {
	return strtr(base64_encode($input), '+/=', '-_,');
}
