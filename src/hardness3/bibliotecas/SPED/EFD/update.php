<?php
namespace hardness;
require_once("conf.php");

mysql_query("set autocommit=0");
mysql_query("begin");

$sql=mysql_query("SELECT T007_Id FROM T007 LEFT JOIN T008 ON T007_Id = T008_T007_Id WHERE T008_Item_Pedido_Compra = '0'");
while($res = mysql_fetch_array($sql)){
	mysql_query("SET @ITEM:=0;");
	mysql_query(" UPDATE T008 SET T008_Item_Pedido_Compra=@ITEM:=@ITEM+1 WHERE T008_T007_Id = '{$res['T007_Id']}'");
	echo mysql_error();	
}

$resultado = mysql_query("SELECT T008_Item_Pedido_Compra FROM T008 WHERE T008_Item_Pedido_Compra > 150");
if(mysql_num_rows($resultado) > 0){
	mysql_query("ROLLBACK");
} else {
	mysql_query("COMMIT");
	echo "Ok";
}

/* SCRIPT DE ATUALIZAÇÃO DE NUMEROS DO TELEFONE DOS CLIENTES
$sql = mysql_query("SELECT * FROM D024");
while($array = mysql_fetch_array($sql)){
	if(strlen($array['D024_Telefone_1']) > 9){	
		preg_match('/^([0-9]{2})-? ?([0-9]{4}-? ?[0-9]{4})$/', $array['D024_Telefone_1'], $new);
		if(empty($new)){
			echo $array['D024_Telefone_1'] . "\n";
		} else {
			$new[2] = str_replace('-', '', $new[2]);
			$new[2] = str_replace(' ', '', $new[2]);
			echo $new[1] . ' - ' . $new[2] . "\n";
			mysql_query("UPDATE D024 SET D024_Telefone_1 = '{$new['2']}', D024_DDD_Telefone_1 = '{$new['1']}' WHERE D024_Id = '{$array['D024_Id']}'");
		}
	}
}*/

//SCRIPT DE ATUALIZAÇÃO DE ESTOQUE
/*$D001 = mysql_query("SELECT D009_Id, T066_Id, D001_Codigo_Descricao FROM D001 LEFT JOIN D049 ON D001_Id = D049_D001_Id LEFT JOIN D009 ON D049_Id = D009_D049_Id LEFT JOIN T066 ON D009_Id = T066_D009_Id WHERE D001_Codigo_Descricao IS NOT NULL");
while($res = mysql_fetch_array($D001)){
  mysql_query("INSERT INTO T001 (T001_D009_Id, T001_T066_Id, T001_Flag_Operacao, T001_Data_Lancamento, T001_Descricao_Operacao, T001_Quantidade, T001_Numero_Controle) 
                VALUES ('{$res['D009_Id']}','{$res['T066_Id']}','B', CURDATE(), 'Balanço Inicial', '{$res['D001_Codigo_Descricao']}', '')
              ");
}
mysql_query("UPDATE D001 SET D001_Codigo_Descricao = null");

*/

/*
$infAdFisco = "SAPORE (PERMUTION)PERIODO DE ENTREGA: 21/01/2013 A 21/01/2013PEDIDO(S): 117989ENDERECO DE ENTREGA: , - INSCRICAO UNICA - REGIME ESPECIAL 2.684-02";
$l = 0; $txt = '';
		        for ($i=0; $i<=strlen($infAdFisco); $i++)
				{
		            $txt.= $infAdFisco[$i];
		            if (strlen($txt) >= 132 || $i >= strlen($infAdFisco)){
				echo '  '."/n";
		                echo $text;
		                $txt = '';
		                $l++;
		            }
		        }
*/
/*
$T206 = mysql_query("SELECT * FROM T206");
while($res = mysql_fetch_array($T206)){
	mysql_query("UPDATE T206 SET T206_Id = T206_Id WHERE T206_Id = {$res['T206_Id']});
}

*/