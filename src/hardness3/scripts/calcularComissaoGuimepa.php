<?php
namespace hardness;
$confUsuario = array(
	'dbDatabase' => 'dev_e229',
	'dbHost' => '127.0.0.1',
	'dbUser' => 'root',
	'dbPass' => 'hardness',
);
$con=@mysqli_connect($confUsuario['dbHost'], $confUsuario['dbUser'], $confUsuario['dbPass'],$confUsuario['dbDatabase']);
$Error=mysqli_connect_error($con);
if (!empty($Error)) { echo $Error; die(); }
// $dbs=mysql_select_db($confUsuario['dbDatabase'], $con) or die (mysql_error());
mysqli_set_charset($con,'utf8');
mysqli_query($con,'SET NAMES utf8;');

public function calcularComissao($IPV, $perfil, $valorTotalPreco){
	
	if($perfil=="V"){
		
		/*
			IPV estiver entre 1,25 e 1,4 será utilizado a fórmula: %Com = 0,03333*IPV - 0,0367.
			Se estiver entre o IPV 1,41 e 1,9 será utilizado essa fórmula: %Com = 0,05*IPV - 0,06.
			E se estiver entre o IPV 1,91 e 2,5 será utilizado essa fórmula: %Com = 0,025*IPV - 0,0125.

			$percentualComissao = (0.036 * $IPV - 0.04);
		
			if ($percentualComissao>0.05){
				$percentualComissao=0.05;
			}elseif($percentualComissao <0.005){
				$percentualComissao=0.005;
			}
		*/
		
		if($IPV <= 1.25){
			$percentualComissao=0.005;
		}elseif(($IPV >1.25) && ($IPV <1.4)){
			$percentualComissao = (0.033333 * $IPV - 0.0367);
		}elseif(($IPV>=1.40) && ($IPV < 1.9)){
			$percentualComissao = (0.05 * $IPV - 0.06);
		}elseif(($IPV>=1.90) && ($IPV <= 2.5)){
			$percentualComissao = (0.025 * $IPV - 0.0125);
		}elseif($IPV>=2.5){
			$percentualComissao = 0.05;
		}
	
	} else if($perfil == "C"){
		
		if($IPV <= 1.499){
			$percentualComissao = 0.01;
		}else if(($IPV > 1.499) && ($IPV <= 1.599)){
			$percentualComissao = 0.02;
		}else if(($IPV > 1.599) && ($IPV <= 1.699)){
			$percentualComissao = 0.03;
		}else if(($IPV > 1.699) && ($IPV <= 1.799)){
			$percentualComissao = 0.04;
		}else if(($IPV > 1.799) && ($IPV <= 1.899)){
			$percentualComissao = 0.05;
		}else if(($IPV > 1.899) && ($IPV <= 2.099)){
			$percentualComissao = 0.06;
		}else if(($IPV > 2.099) && ($IPV <= 2.399)){
			$percentualComissao = 0.07;
		}else if(($IPV > 2.399) && ($IPV <= 2.499)){
			$percentualComissao = 0.08;
		}else if($IPV >= 2.500){
			$percentualComissao = 0.1;
		}
	} else {
		$percentualComissao=0;
	}
	
	log('IPV: '. $IPV);
	log('percentual Comissao: '. $percentualComissao);
	
	$valorComissao = $valorTotalPreco * $percentualComissao;
	$return = array();
	$return['percentualComissao'] = $percentualComissao;
	$return['valorComissao'] = $valorComissao;
	return $return;
	
}


$T004=mysqli_query($con,"SELECT * FROM T004 LEFT JOIN T003 ON T003_Id=T004_T003_Id");

while ($mT004=mysqli_fetch_array($T004)){

	$C007=mysqli_query($con,"SELECT * FROM C007 LEFT JOIN C019 ON C019_C007_Id=C007_Id LEFT JOIN C021 on C021_Id=C019_C021_Id WHERE C007_Id='{$mT004['T003_C007_Id_Vendedor_Interno']}'");
	$mC007=mysqli_fetch_array($C007);

	$retorno = array();
	if($mC007['C021_Id'] == 25){
		// O segundo parâmetro do método é indicando se ele é um vendedor ou um comprador (o cálculo das comissões varia de perfis compradores para vendedores)
		$retorno = calcularComissao($mT004['T004_IPV'], 'V', $mT004['T004_Valor_Total_Preco']);
		
	}else if($mC007['C021_Id'] == 26){
		// O segundo parâmetro do método é indicando se ele é um vendedor ou um comprador (o cálculo das comissões varia de perfis compradores para vendedores)
		$retorno = calcularComissao($mT004['T004_IPV'], 'C', $mT004['T004_Valor_Total_Preco']);
	}

	$upT004 = mysqli_query($con,"UPDATE T004 SET T004_Valor_Comissao='{$retorno['valorComissao']}', T004_Percentual_Comissao='{$retorno['percentualComissao']}' WHERE T004_Id='{$mT004['T004_Id']}'");
	$upT003 = mysqli_query($con,"UPDATE T003 SET T003_Valor_Total_Comissao=(SELECT SUM(T004_Valor_Comissao) FROM T004 WHERE T004_T003_Id=T003_Id) WHERE T003_Id = '{$mT004['T004_T003_Id']}'");
	echo "Item {$mT004['T004_Id']} do orçamento {$mT004['T003_Id']} atualizado\n";
}
echo "FIM ORÇAMENTO\n";

$T006=mysqli_query($con,"SELECT * FROM T006 LEFT JOIN T005 ON T005_Id=T006_T005_Id");
while ($mT006 = mysqli_fetch_array($T006)){
	$C007 = mysqli_query($con,"SELECT * FROM C007 LEFT JOIN C019 ON C019_C007_Id=C007_Id LEFT JOIN C021 on C021_Id=C019_C021_Id WHERE C007_Id='{$mT006['T005_C007_Id_Vendedor_Interno']}'");
	$mC007=mysqli_fetch_array($C007);

	$retorno = array();
	if($mC007['C021_Id'] == 25){
		
		// O segundo parâmetro do método é indicando se ele é um vendedor ou um comprador (o cálculo das comissões varia de perfis compradores para vendedores)
		$retorno = calcularComissao($mT006['T006_IPV'], 'V', $mT006['T006_Valor_Total_Preco']);
		
	}else if($mC007['C021_Id'] == 26){
		
		// O segundo parâmetro do método é indicando se ele é um vendedor ou um comprador (o cálculo das comissões varia de perfis compradores para vendedores)
		$retorno = calcularComissao($mT006['T006_IPV'], 'C', $mT006['T006_Valor_Total_Preco']);
	}

	$upT006 = mysqli_query($con,"UPDATE T006 SET T006_Valor_Comissao='{$retorno['valorComissao']}', T006_Percentual_Comissao='{$retorno['percentualComissao']}' WHERE T006_Id='{$mT006['T006_Id']}'");
	$upT005 = mysqli_query($con,"UPDATE T005 SET T005_Valor_Total_Comissao=(SELECT SUM(T006_Valor_Comissao) FROM T006 WHERE T006_T005_Id=T005_Id) WHERE T005_Id = '{$mT006['T006_T005_Id']}'");
	echo "Item {$mT006['T006_Id']} do pedido {$mT006['T005_Id']} atualizado\n";
}
echo "FIM Pedido\n";

$T008=mysqli_query($con,"SELECT * FROM T008 LEFT JOIN T007 ON T007_Id=T008_T007_Id");

while($mT008 = mysqli_fetch_array($T008)){

	$C007 = mysqli_query($con,"SELECT * FROM C007 LEFT JOIN C019 ON C019_C007_Id=C007_Id LEFT JOIN C021 on C021_Id=C019_C021_Id WHERE C007_Id='{$mT008['T007_C007_Id_Vendedor_Interno']}'");
	$mC007=mysqli_fetch_array($C007);

	if($mC007['C021_Id'] == 25){
		
		// O segundo parâmetro do método é indicando se ele é um vendedor ou um comprador (o cálculo das comissões varia de perfis compradores para vendedores)
		$retorno = calcularComissao($mT008['T008_IPV'], 'V', $mT008['T008_Valor_Total_Preco']);
		
	}else if($mC007['C021_Id'] == 26){
		
		// O segundo parâmetro do método é indicando se ele é um vendedor ou um comprador (o cálculo das comissões varia de perfis compradores para vendedores)
		$retorno = calcularComissao($mT008['T008_IPV'], 'C', $mT008['T008_Valor_Total_Preco']);
	}

	$upT008 = mysqli_query($con,"UPDATE T008 SET T008_Valor_Comissao='{$retorno['valorComissao']}', T008_Percentual_Comissao='{$retorno['percentualComissao']}' WHERE T008_Id='{$mT008['T008_Id']}'");
	$upT007 = mysqli_query($con,"UPDATE T007 SET T007_Valor_Total_Comissao=(SELECT SUM(T008_Valor_Comissao) FROM T008 WHERE T008_T007_Id=T007_Id) WHERE T007_Id = '{$mT008['T008_T007_Id']}'");	
	echo "Item {$mT008['T008_Id']} da nota {$mT008['T007_Id']} atualizado\n";
}
echo "FIM NOTA\n";



