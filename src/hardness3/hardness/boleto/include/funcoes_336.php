<?php
namespace hardness;

// ==============================
// 1) CÓDIGO DO BANCO COM DV (C6)
// ==============================
function b336_geraCodigoBanco($numero) {
    $banco = substr($numero, 0, 3);
    $soma = 0;
    $peso = 2;

    for ($i = 2; $i >= 0; $i--) {
        $soma += intval($banco[$i]) * $peso;
        $peso++;
        if ($peso > 9) $peso = 2;
    }

    $resto = $soma % 11;
    $dv = 11 - $resto;

    if (in_array($dv, [0, 1, 10, 11])) {
        $dv = 1;
    }

    return $banco . '-' . $dv;
}

// ========================================
// 2) DV GERAL DO CÓDIGO DE BARRAS (C6)
// ========================================
function b336_calculaDvCodigoBarras($codigo43) {
    $soma = 0;
    $peso = 2;

    for ($i = strlen($codigo43) - 1; $i >= 0; $i--) {
        $soma += intval($codigo43[$i]) * $peso;
        $peso++;
        if ($peso > 9) $peso = 2;
    }

    $resto = $soma % 11;
    $dv = 11 - $resto;

    if (in_array($dv, [0, 1, 10, 11])) {
        $dv = 1;
    }

    return $dv;
}

// ==============================================
// 3) STRING BASE DO CÓDIGO DE BARRAS (SEM DV)
// ==============================================
function b336_montaCodigoBarrasBase($fatorVencimento, $valorTitulo, $campoLivre) {
    return
        '336' .
        '9' .
        str_pad($fatorVencimento, 4, '0', STR_PAD_LEFT) .
        str_pad($valorTitulo, 10, '0', STR_PAD_LEFT) .
        $campoLivre; // total = 43 dígitos
}

// =====================================================
// 4) CÓDIGO DE BARRAS COMPLETO (44 DÍGITOS)
// =====================================================
function b336_geraCodigoBarrasCompleto($fatorVencimento, $valorTitulo, $campoLivre) {
    $base = b336_montaCodigoBarrasBase($fatorVencimento, $valorTitulo, $campoLivre);
    $dv = b336_calculaDvCodigoBarras($base);

    return substr($base, 0, 4) . $dv . substr($base, 4);
}

// =====================================================
// 5) LINHA DIGITÁVEL (FEBRABAN)
// =====================================================
function b336_geraLinhaDigitavel($codigoBarras) {
    // Campo 1: banco + moeda + campo livre (1–5)
    $campo1 = substr($codigoBarras, 0, 4) . substr($codigoBarras, 19, 5);
    $dv1 = b336_modulo10($campo1);
    $campo1f = substr($campo1, 0, 5) . '.' . substr($campo1, 5) . $dv1;

    // Campo 2: campo livre (6–15)
    $campo2 = substr($codigoBarras, 24, 10);
    $dv2 = b336_modulo10($campo2);
    $campo2f = substr($campo2, 0, 5) . '.' . substr($campo2, 5) . $dv2;

    // Campo 3: campo livre (16–25)
    $campo3 = substr($codigoBarras, 34, 10);
    $dv3 = b336_modulo10($campo3);
    $campo3f = substr($campo3, 0, 5) . '.' . substr($campo3, 5) . $dv3;

    // Campo 4: DV geral
    $campo4 = substr($codigoBarras, 4, 1);

    // Campo 5: fator + valor
    $campo5 = substr($codigoBarras, 5, 4) . substr($codigoBarras, 9, 10);

    return "$campo1f $campo2f $campo3f $campo4 $campo5";
}

// ==============================
// 6) MÓDULO 10 (FEBRABAN)
// ==============================
function b336_modulo10($num) {
    $soma = 0;
    $fator = 2;

    for ($i = strlen($num) - 1; $i >= 0; $i--) {
        $calc = intval($num[$i]) * $fator;
        if ($calc > 9) {
            $calc = array_sum(str_split($calc));
        }
        $soma += $calc;
        $fator = ($fator == 2) ? 1 : 2;
    }

    return (10 - ($soma % 10)) % 10;
}

// ===========================================
// 7) DV DO NOSSO NÚMERO (C6 – SEM EXCEÇÕES)
// ===========================================
function b336_dvNossoNumero($numeroBase) {
    $soma = 0;
    $peso = 2;

    for ($i = strlen($numeroBase) - 1; $i >= 0; $i--) {
        $soma += intval($numeroBase[$i]) * $peso;
        $peso++;
        if ($peso > 9) $peso = 2;
    }

    return 11 - ($soma % 11);
}

// ===========================================
// 8) FUNÇÕES AUXILIARES (COPIADAS DO 341)
// ===========================================

function b336_formata_numero($numero,$loop,$insert,$tipo = "geral") {
	if ($tipo == "geral") {
		$numero = str_replace(",","",$numero);
		while(strlen($numero)<$loop){
			$numero = $insert . $numero;
		}
	}
	if ($tipo == "valor") {
		/*
		retira as virgulas
		formata o numero
		preenche com zeros
		*/
		$numero = str_replace(",","",$numero);
		$numero = str_replace(".","",$numero);
		while(strlen($numero)<$loop){
			$numero = $insert . $numero;
		}
	}
	if ($tipo == "convenio") {
		while(strlen($numero)<$loop){
			$numero = $numero . $insert;
		}
	}
	return $numero;
}

function b336_fator_vencimento($data) {
	$data = explode("/",$data);
	$ano = $data[2];
	$mes = $data[1];
	$dia = $data[0];

    // Converte a data do título para dias absolutos
    $diasVencimento = b336_dateToDays($ano, $mes, $dia);
    // Data base antiga
    $dataBaseAntiga = b336_dateToDays("1997", "10", "07");
    // Data de início da nova regra, a partir de 22/02/2025 vai retornar 1000 e assim por diante - 23/02/2025 = 1001 - 24/02/2025 = 1002 ...
    $dataBaseNova   = b336_dateToDays("2025", "02", "22");

    // Verifica se a data do título é anterior ou posterior a 21/02/2025
    if ($diasVencimento < $dataBaseNova) {
        // regra antiga
        $fator = abs($dataBaseAntiga - $diasVencimento); 
    } else {
        // regra nova
        $fator = 1000 + abs($dataBaseNova - $diasVencimento);        
    }
        
    return $fator;
}

function b336_dateToDays($year,$month,$day) {
    $century = substr($year, 0, 2);
    $year = substr($year, 2, 2);
    if ($month > 2) {
        $month -= 3;
    } else {
        $month += 9;
        if ($year) {
            $year--;
        } else {
            $year = 99;
            $century --;
        }
    }
    return ( floor((  146097 * $century)    /  4 ) +
            floor(( 1461 * $year)        /  4 ) +
            floor(( 153 * $month +  2) /  5 ) +
                $day +  1721119);
}

function b336_esquerda($entra,$comp){
	return substr($entra,0,$comp);
}

function b336_direita($entra,$comp){
	return substr($entra,strlen($entra)-$comp,$comp);
}

// ===========================================
// 9) FUNÇÃO PARA GERAÇÃO DO CÓDIGO DE BARRAS
// ===========================================
function fbarcode($valor){
    $fino = 1 ;
    $largo = 3 ;
    $altura = 50 ;

    $barcodes[0] = "00110" ;
    $barcodes[1] = "10001" ;
    $barcodes[2] = "01001" ;
    $barcodes[3] = "11000" ;
    $barcodes[4] = "00101" ;
    $barcodes[5] = "10100" ;
    $barcodes[6] = "01100" ;
    $barcodes[7] = "00011" ;
    $barcodes[8] = "10010" ;
    $barcodes[9] = "01010" ;
    for($f1=9;$f1>=0;$f1--){ 
        for($f2=9;$f2>=0;$f2--){  
        $f = ($f1 * 10) + $f2 ;
        $texto = "" ;
        for($i=1;$i<6;$i++){ 
            $texto .=  substr($barcodes[$f1],($i-1),1) . substr($barcodes[$f2],($i-1),1);
        }
        $barcodes[$f] = $texto;
        }
    }

    //Desenho da barra

    //Guarda inicial
    ?><img src=imagens/p.png width=<?php echo $fino?> height=<?php echo $altura?> border=0><img 
    src=imagens/b.png width=<?php echo $fino?> height=<?php echo $altura?> border=0><img 
    src=imagens/p.png width=<?php echo $fino?> height=<?php echo $altura?> border=0><img 
    src=imagens/b.png width=<?php echo $fino?> height=<?php echo $altura?> border=0><img 
    <?php
    $texto = $valor ;
    if((strlen($texto) % 2) <> 0){
        $texto = "0" . $texto;
    }

    // Draw dos dados
    while (strlen($texto) > 0) {
        $i = round(b336_esquerda($texto,2));
        $texto = b336_direita($texto,strlen($texto)-2);
        $f = $barcodes[$i];
        for($i=1;$i<11;$i+=2){
        if (substr($f,($i-1),1) == "0") {
            $f1 = $fino ;
        }else{
            $f1 = $largo ;
        }
        ?>
        src=imagens/p.png width=<?php echo $f1?> height=<?php echo $altura?> border=0><img 
        <?php
        if (substr($f,$i,1) == "0") {
            $f2 = $fino ;
        }else{
            $f2 = $largo ;
        }
        ?>
        src=imagens/b.png width=<?php echo $f2?> height=<?php echo $altura?> border=0><img 
        <?php
        }
    }

    // Draw guarda final
    ?>
    src=imagens/p.png width=<?php echo $largo?> height=<?php echo $altura?> border=0><img 
    src=imagens/b.png width=<?php echo $fino?> height=<?php echo $altura?> border=0><img 
    src=imagens/p.png width=<?php echo 1?> height=<?php echo $altura?> border=0> 
    <?php
}
?>