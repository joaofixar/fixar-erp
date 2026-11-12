<?php
namespace hardness;
set_time_limit(600);

$g['empresaAtual'] = 2;

switch ($g['r'][3]) {


	case 'scriptConexaoBases':
		global $sys;
		ini_set('display_errors', 1);
		error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE ^ E_WARNING);

		/*
			ERP AMAZON
			Host: 52.3.178.25
			SSH 22: root / $$hard**0982
			MySQL: root / $$hard@@0982

			NOTA AMAZON
			Host: 23.23.29.24
			SSH 22: root / $$hard**0982
			Mysql: root / !hard@3871

			CEIGON:
			Host: sistema.ceigon.com.br
			VPN: 192.168.10.12
			SSH porta 6022: root / master
			MySQL: (igual SSH)

			GUIMEPA
			Host: hardness.guimepa.com.br
			SSH 2022: root / F4Am9xHSlgaD
			MySQL 3306: root / F4Am9xHSlgaD
			cd opt/bitnami/apache2/htdocs/hardness3

			AZPR
			Host: azpr.senhorpc.com.br
			SSH 2022: root / DdcDdPgM74nF
			MySQL: root / DdcDdPgM74nF

			CHA AMAZON
			Host: 52.7.139.38
			SSH 22: root / $$hard**0982
			Mysql: root / $$hard@@0982

			ALUMIFIX
			Host: alumifix.hardness.com.br
			SSH 2022: root / #qos!0982
			MySQL: root / !aLu#Window!

			ANEISRCS
			Host: aneisrcs.infoqos.com.br
			Porta 8080 para http
			SSH 2022: root / #qos!0982
			MySQL: root #rCs!0982 3306
			http://189.32.61.169/login
	
		*/

		// Variável db2 é a conexão com a base e001
		$db2=mysqli_connect('23.23.29.24', 'root', '!hard@3871',$sys['dbDatabase']);
		if(!$db2){echo mysqli_connect_error($db2);}
		$g['conexaoBanco'] = $db2;
		// $db_selected2 = mysql_select_db("{$sys['dbDatabase']}", $db2);

		$List_Servers=array(
								array(
									'serverName' => 'CHA AMAZON',
									'serverHost' => '52.7.139.38',
									'serverUser' => 'root',
									'serverPass' => '$$hard@@0982'
								),
								array(
									'serverName' => 'ANEISRCS',
									'serverHost' => 'aneisrcs.infoqos.com.br',
									'serverUser' => 'root',
									'serverPass' => '#rCs!0982'
								),
								array(
									'serverName' => 'ALUMIFIX',
									'serverHost' => 'alumifix.hardness.com.br',
									'serverUser' => 'root',
									'serverPass' => '!aLu#Window!'
								),
								array(
									'serverName' => 'AZPR',
									'serverHost' => 'azpr.senhorpc.com.br',
									'serverUser' => 'root',
									'serverPass' => 'DdcDdPgM74nF'
								),
								array(
									'serverName' => 'GUIMEPA',
									'serverHost' => 'hardness.guimepa.com.br',
									'serverUser' => 'root',
									'serverPass' => 'F4Am9xHSlgaD'
								),
								array(
									'serverName' => 'CEIGON',
									'serverHost' => '192.168.10.12',
									'serverUser' => 'root',
									'serverPass' => 'master'
								),
								array(
									'serverName' => 'ERP',
									'serverHost' => '52.3.178.25',
									'serverUser' => 'root',
									'serverPass' => '$$hard@@0982'
								),
								// O servidor de nota SEMPRE tem que ser por último por causa do mysql_close lá em baixo
								array(
									'serverName' => 'NOTA AMAZON',
									'serverHost' => '23.23.29.24',
									'serverUser' => 'root',
									'serverPass' => '!hard@3871'
								)
						   );
		
		foreach ($List_Servers as $key => $server) {
			echo "Host: ".$server['serverHost']." User: ".$server['serverUser']." Pass: ".$server['serverPass']."<br />";

			$db1=mysql_connect($server['serverHost'], $server['serverUser'], $server['serverPass']);
			if(!$db1){echo mysql_error();}

			// Lista todas as bases de dados dos servidores
			$db_list = mysql_list_dbs($db1);
			while ($row = mysql_fetch_object($db_list)) {
				
				// Pega somente as bases válidas e que forem maior que e001
				if(substr($row->Database, 0,1)=='e' && substr($row->Database, 1,10) > '001'){
					$db_selected = mysql_select_db($row->Database);
					echo "Base: ".$row->Database."<br />";
					
					$C004=mysqli_query("SELECT * FROM {$row->Database}.C004 WHERE C004_Id='1'",$db1);
					$mC004 = mysql_fetch_array($C004);

					// Caso seja o servidor da CHA, pega o ID do D024 da do sysconfig e não o campo C004_D024_Id_Hardness porque porque é diferente
					if($server['serverHost']=='52.7.139.38'){
						$D024=mysqli_query("SELECT D024_Id from {$sys['dbDatabase']}.D024 WHERE D024_Nome_Empresa like '%{$mC004['C004_Nome_Abreviado']}%'",$db2);
						$mD024=mysql_fetch_array($D024);
						$mC004['C004_D024_Id_Hardness'] = $mD024['D024_Id'];
					}

					// Listando todos usuários ativos da base corrente
					$C007=mysqli_query("SELECT * FROM {$row->Database}.C007 WHERE C007_Ativo='S';",$db1);
					while($mC007 = mysql_fetch_array($C007)){
						$D013_Nome_Contato = "{$mC007['C007_Nome']} - {$mC007['C007_Id']} - {$mC007['C007_Login']}";
						// Verifica se já possui um regsitro vinculado ao D013, caso possua ele atualiza o contato.
						$D013=mysqli_query("SELECT * FROM {$sys['dbDatabase']}.D013 WHERE D013_D024_Id='{$mC004['C004_D024_Id_Hardness']}' AND D013_C007_Id_Cliente='{$mC007['C007_Id']}'",$db2);
						if(mysql_num_rows($D013) > 0){
							$mD013=mysql_fetch_array($D013);
							mysqli_query("UPDATE {$sys['dbDatabase']}.D013 SET D013_Nome_Contato='{$D013_Nome_Contato}',D013_E_Mail='{$mC007['C007_Email']}' WHERE D013_Id='{$mD013['D013_Id']}'",$db2);
						}else{
			
							// Caso não possua faz a inserção
							if($mC007['C007_Id']!='63'){
								$mC007['C007_Email'] = strtolower($mC007['C007_Email']);
								$sql = "INSERT INTO {$sys['dbDatabase']}.D013 (D013_D024_Id, D013_Nome_Contato,D013_E_Mail,D013_C007_Id_Cliente) VALUES ({$mC004['C004_D024_Id_Hardness']}, '{$D013_Nome_Contato}','{$mC007['C007_Email']}','{$mC007['C007_Id']}');";
								$D024=mysqli_query($sql,$db2);
								echo "Ins: ".$sql."<br />";
							}
						}
						echo "C007: ".$mC007['C007_Nome']."<br />";
					}
				}
			}
			mysql_close($db1);
		}

	break;
}