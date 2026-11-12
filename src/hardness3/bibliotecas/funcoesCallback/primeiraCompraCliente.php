<?php
namespace hardness;

function primeiraCompraCliente($array, $param){
	if (!isset($array['todosCampos']['D024_Id'])){
		return $array['campoAtual'];
	}
	$D024_Id = $array['todosCampos']['D024_Id'];
	$T007_Id = isset($array['todosCampos']['T007_Id']) ? $array['todosCampos']['T007_Id'] : '9999999999999';
	$T007 = mysql_query("select count(*) from T007 where T007_D024_Id='{$D024_Id}' and T007_Id<'{$T007_Id}'");
	$mT007 = mysql_fetch_array($T007);
	if ($mT007[0]<=0){
		return $array['campoAtual']."<br><span style='color:white;background:#ef23e5;padding: 1px 4px 1px 4px;font-weight:bold'>P R I M E I R A &nbsp;&nbsp; C O M P R A</span>";
	} else {
		return $array['campoAtual'];
	}
}