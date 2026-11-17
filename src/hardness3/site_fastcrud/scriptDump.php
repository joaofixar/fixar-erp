<?php

$confUsuarioERP = array(
        'dbDatabase' => 'global',
        'dbHost' => '52.22.24.146',
        'dbUser' => 'root',
        'dbPass' => '$$hard@@0982',
);
$conERP=@mysql_connect($confUsuarioERP['dbHost'], $confUsuarioERP['dbUser'], $confUsuarioERP['dbPass']);
$Error=mysql_error();
if (!empty($Error)) { echo $Error; die(); }
mysql_select_db($confUsuarioERP['dbDatabase'], $conERP) or die (mysql_error());
mysql_set_charset('utf8');
mysql_query('SET NAMES utf8;');


$argv = $_SERVER['argv'];
$C025_Banco_Dados  		 = $argv[1];
$senha 			   		 = $argv[2];
$login 			   		 = $argv[3];
$email 			   		 = $argv[4];
$modulos 		   		 = $argv[5];
$D024_Nome_Empresa 		 = base64_decode($argv[6]);
$D024_Cnpj 				 = base64_decode($argv[7]);
$D024_Inscricao_Estadual = base64_decode($argv[8]);
$D024_Id 				 = base64_decode($argv[9]);
$D018_Codigo_IBGE   	 = base64_decode($argv[10]);
$D020_Codigo_IBGE   	 = base64_decode($argv[11]);
$D024_Bairro 			 = base64_decode($argv[12]);
$D024_Logradouro 		 = base64_decode($argv[13]);
$D024_Numero 	 		 = base64_decode($argv[14]);
$D024_Complemento 		 = base64_decode($argv[15]);
$D024_Cep 	 		 	 = base64_decode($argv[16]);
$D024_DDD_Telefone	 	 = base64_decode($argv[17]);
$D024_Telefone	 	 	 = base64_decode($argv[18]);

$arqDump = '/root/dump.sql';
$arqTmp = '/tmp/' . md5(mt_rand() . time()) . '.sql';
shell_exec("ionice -c3 cp {$arqDump} {$arqTmp}");
$dump = file_get_contents($arqTmp);
$dump = str_replace('`e000`@`%`', "`{$C025_Banco_Dados}`@`localhost`", $dump);
$dump = str_replace('e000', "{$C025_Banco_Dados}", $dump);
file_put_contents($arqTmp, $dump);
	
shell_exec("ionice -c3 mysql --user='{$confUsuarioERP['dbUser']}' --password='\$\$hard@@0982' < {$arqTmp}");
shell_exec("ionice -c3 rm -f {$arqTmp}");

$C025_Banco_Dados_Senha = md5($C025_Banco_Dados);
// Cria usuario no banco
mysql_query("DROP USER '{$C025_Banco_Dados}'@'%';", $conERP);
mysql_query("FLUSH PRIVILEGES;", $conERP);
mysql_query("CREATE USER '{$C025_Banco_Dados}'@'%' IDENTIFIED BY '{$C025_Banco_Dados_Senha}';", $conERP);
mysql_query("FLUSH PRIVILEGES;", $conERP);
mysql_query("GRANT ALL PRIVILEGES ON `{$C025_Banco_Dados}`.* TO '{$C025_Banco_Dados}'@'%' WITH GRANT OPTION;", $conERP);
mysql_query("GRANT ALL PRIVILEGES ON `global`.* TO '{$C025_Banco_Dados}'@'%' WITH GRANT OPTION;", $conERP);
mysql_query("FLUSH PRIVILEGES;", $conERP);

mysql_query("USE {$C025_Banco_Dados}", $conERP);

$sql = "UPDATE C004 SET ";
$sql .= "C004_Nome_Empresa = '{$D024_Nome_Empresa}', ";
$sql .= "C004_Nome_Abreviado = '{$D024_Nome_Empresa}', ";
$sql .= "C004_Cnpj = '{$D024_Cnpj}', ";
$sql .= "C004_Uf = '{$D018_Codigo_IBGE}', ";
$sql .= "C004_Cidade = '{$D020_Codigo_IBGE}', ";
$sql .= "C004_Logradouro = '{$D024_Logradouro}', ";
$sql .= "C004_Bairro = '{$D024_Bairro}', ";
$sql .= "C004_Numero = '{$D024_Numero}', ";
$sql .= "C004_Complemento = '{$D024_Complemento}', ";
$sql .= "C004_Cep = '{$D024_Cep}', ";
$sql .= "C004_DDD_Telefone = '{$D024_DDD_Telefone}', ";
$sql .= "C004_Telefone = '{$D024_Telefone}', ";
$sql .= "C004_Caminho_Sistema = '{$C025_Banco_Dados}', ";
$sql .= "C004_Banco_Dados = '{$C025_Banco_Dados}', ";
$sql .= "C004_Banco_Configuracao = '{$C025_Banco_Dados}', ";
$sql .= "C004_Inscricao_Estadual = '{$D024_Inscricao_Estadual}', ";
$sql .= "C004_Localizacao_Certificados = '/var/www/dados/v3/dados_usuarios/{$C025_Banco_Dados}/nfe/certs/', ";
$sql .= "C004_D024_Id_Hardness = '{$D024_Id}', ";
$sql .= "C004_Modulos = '0,{$modulos}' ";
mysql_query($sql, $conERP);

//Insert C020
mysql_query("INSERT INTO C020 (C020_C004_Id, C020_Versao_NFe) VALUES ('1', '3.10')");

//Insert da configuração padrão por módulos
mysql_query("INSERT INTO C031 (C031_C004_Id, C031_Campo, C031_Valor_Padrao, C031_Valor, C031_Observacao) VALUES ('1', 'tipoSistema', 'M', 'M', 'Define o tipo do sistema, se é completo ou por aqusicao de módulos.'), ('1', 'desabilitarCotacaoEmergencia', 'S', 'S', 'Desabilita as ferramentas de cotação de emergência.'), ('1', 'desabilitarCompraEmergencia', 'S', 'S', 'Desabilita as ferramentas de compra de emergência.'), ('1', 'desabilitarMontagemEmergencia', 'S', 'S', 'Desabilita as ferramentas de montagem de emergência.'), ('1', 'desabilitarServicoEmergencia', 'S', 'S', 'Desabilita as ferramentas de serviço de emergência.'), ('1', 'desabilitarProducaoEmergencia', 'S', 'S', 'Desabilita as ferramentas de produção de emergência.');");

//Insert do usuário
$senha_ = mysql_real_escape_string(base64_encode($senha));
$login_ = strtoupper($login);
$email = strtolower($email);
mysql_query("INSERT INTO C007 (C007_Login, C007_Senha, C007_Nome, C007_Primeiro_Nome, C007_Email, C007_Flag_Vendedor) VALUES ('{$login}', '{$senha_}', '{$login_}', '{$login_}', '{$email}', 'D')", $conERP);

// Personalizações padrões
//$arqPerso = "/home/fabio/html/hardness3/site/personalizacao.tar.gz";
//if (!file_exists($arqPerso)) {
//	$arqPerso = "/var/www/html/hardness3/site/personalizacao.tar.gz";
//}
//$dir = "/var/www/dados/v3/dados_usuarios/{$C025_Banco_Dados}";
//is_dir($dir) or mkdir($dir, 0777, true);
//$cmd = "cd {$dir} && tar -zxvf {$arqPerso}";
//shell_exec($cmd);


$cmd = "mkdir /var/www/dados/v3/dados_usuarios/{$C025_Banco_Dados}";
shell_exec($cmd);

$cmd = "chmod 777 -R /var/www/dados/v3/dados_usuarios/{$C025_Banco_Dados}";
shell_exec($cmd);

$cmd = "ln -s /var/www/dados/v3/dados_usuarios/e000/personalizacaoForm /var/www/dados/v3/dados_usuarios/{$C025_Banco_Dados}/personalizacaoForm";
shell_exec($cmd);

$cmd = "ln -s /var/www/dados/v3/dados_usuarios/e000/personalizacaoGrid /var/www/dados/v3/dados_usuarios/{$C025_Banco_Dados}/personalizacaoGrid";
shell_exec($cmd);

$cmd = "ln -s /var/www/dados/v3/dados_usuarios/e000/personalizacaoTab /var/www/dados/v3/dados_usuarios/{$C025_Banco_Dados}/personalizacaoTab";
shell_exec($cmd);

$cmd = "ln -s /var/www/dados/v3/dados_usuarios/e000/personalizacaoConf /var/www/dados/v3/dados_usuarios/{$C025_Banco_Dados}/personalizacaoConf";
shell_exec($cmd);