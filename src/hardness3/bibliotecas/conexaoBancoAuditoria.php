<?php
namespace hardness;

if(isset($confUsuarioAuditoria)){

	//Conexão do banco da auditoria
	$conAuditoria=mysqli_connect($confUsuarioAuditoria['dbHost'], $confUsuarioAuditoria['dbUser'], $confUsuarioAuditoria['dbPass']);
	$ErrorAuditoria=mysqli_connect_error($conAuditoria);

	//Verificando se Banco existe no servidor auditoria, caso não exista o banco é criado
	if(empty($ErrorAuditoria)){

		$sqlDB = \mysqli_query($conAuditoria,"SHOW DATABASES LIKE {$confUsuarioAuditoria['dbDatabase']}");
		$DB = mysqli_num_rows($sqlDB);
		
		if($DB<=1){
			\mysqli_query($conAuditoria,"CREATE DATABASE IF NOT EXISTS {$confUsuarioAuditoria['dbDatabase']}");
			$ErrorAuditoria = mysqli_error($conAuditoria);

			if(empty($ErrorAuditoria)){
				mysqli_select_db($conAuditoria,$confUsuarioAuditoria['dbDatabase']);
			}
		}else{
			mysqli_select_db($conAuditoria,$confUsuarioAuditoria['dbDatabase']);
		}

		if(empty($ErrorAuditoria)){
			$g['conexaoBancoAuditoria'] = $conAuditoria;
		}
	}
}

if(!isset($g['conexaoBancoAuditoria'])){
	$g['conexaoBancoAuditoria'] = $g['conexaoBanco'];
}

mysqli_set_charset($g['conexaoBancoAuditoria'],'utf8');
\mysqli_query($g['conexaoBancoAuditoria'],'SET NAMES utf8;');



