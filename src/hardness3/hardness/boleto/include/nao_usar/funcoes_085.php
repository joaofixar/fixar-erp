<?php
namespace hardness;
// +----------------------------------------------------------------------+
// | BoletoPhp - Vers�o Beta                                              |
// +----------------------------------------------------------------------+
// | Este arquivo est� dispon�vel sob a Licen�a GPL dispon�vel pela Web   |
// | em http://pt.wikipedia.org/wiki/GNU_General_Public_License           |
// | Voc� deve ter recebido uma c�pia da GNU Public License junto com     |
// | esse pacote; se n�o, escreva para:                                   |
// |                                                                      |
// | Free Software Foundation, Inc.                                       |
// | 59 Temple Place - Suite 330                                          |
// | Boston, MA 02111-1307, USA.                                          |
// +----------------------------------------------------------------------+

// +----------------------------------------------------------------------+
// | Originado do Projeto BBBoletoFree que tiveram colabora��es de Daniel |
// | William Schultz e Leandro Maniezo que por sua vez foi derivado do	  |
// | PHPBoleto de Jo�o Prado Maia e Pablo Martins F. Costa				        |
// | 																	                                    |
// | Se vc quer colaborar, nos ajude a desenvolver p/ os demais bancos :-)|
// | Acesse o site do Projeto BoletoPhp: www.boletophp.com.br             |
// +----------------------------------------------------------------------+

// +----------------------------------------------------------------------+
// | Equipe Coordena��o Projeto BoletoPhp: <boletophp@boletophp.com.br>   |
// | Desenvolvimento Boleto Bradesco: Ramon Soares						            |
// +----------------------------------------------------------------------+


error_reporting(E_ALL ^ E_DEPRECATED || E_NOTICE);

$codigobanco = "085";
$codigo_banco_com_dv = b085_geraCodigoBanco($codigobanco);
$mensagemLocalPagamento = "Pagável preferencialmente nas cooperativas do sistema Ailos.";


$codigo_barras = $this->codigo_barras;
$nossonumero = $this->nosso_numero;
$linha_digitavel = $this->linha_digitavel;

function b085_digitoVerificador_nossonumero($numero) {
	$resto2 = b085_modulo_11($numero, 7, 1);
     $digito = 11 - $resto2;
     if ($digito == 10) {
        $dv = "P";
     } elseif($digito == 11) {
     	$dv = 0;
	} else {
        $dv = $digito;
     	}
	 return $dv;
}


function b085_digitoVerificador_barra($numero) {
	$resto2 = b085_modulo_11($numero, 9, 1);
     if ($resto2 == 0 || $resto2 == 1 || $resto2 == 10 || $resto2 == 11) {
        $dv = 1;
     } else {
	 	$dv = 11 - $resto2;
     }
	 return $dv;
}


// FUN��ES
// Algumas foram retiradas do Projeto PhpBoleto e modificadas para atender as particularidades de cada banco

function b085_formata_numero($numero,$loop,$insert,$tipo = "geral") {
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
  $i = round(b085_esquerda($texto,2));
  $texto = b085_direita($texto,strlen($texto)-2);
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
} //Fim da fun��o

function b085_esquerda($entra,$comp){
	return substr($entra,0,$comp);
}

function b085_direita($entra,$comp){
	return substr($entra,strlen($entra)-$comp,$comp);
}

function b085_fator_vencimento($data) {
	$data = explode("/",$data);
	$ano = $data[2];
	$mes = $data[1];
	$dia = $data[0];
    return(abs((b085_dateToDays("1997","10","07")) - (b085_dateToDays($ano, $mes, $dia))));
}

function b085_dateToDays($year,$month,$day) {
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

function b085_modulo_10($num) {
		$numtotal10 = 0;
        $fator = 2;

        // Separacao dos numeros
        for ($i = strlen($num); $i > 0; $i--) {
            // pega cada numero isoladamente
            $numeros[$i] = substr($num,$i-1,1);
            // Efetua multiplicacao do numero pelo (falor 10)
            // 2002-07-07 01:33:34 Macete para adequar ao Mod10 do Ita�
            $temp = $numeros[$i] * $fator; 
            $temp0=0;
            foreach (preg_split('//',$temp,-1,PREG_SPLIT_NO_EMPTY) as $k=>$v){ $temp0+=$v; }
            $parcial10[$i] = $temp0; //$numeros[$i] * $fator;
            // monta sequencia para soma dos digitos no (modulo 10)
            $numtotal10 += $parcial10[$i];
            if ($fator == 2) {
                $fator = 1;
            } else {
                $fator = 2; // intercala fator de multiplicacao (modulo 10)
            }
        }
		
        // v�rias linhas removidas, vide fun��o original
        // Calculo do modulo 10
        $resto = $numtotal10 % 10;
        $digito = 10 - $resto;
        if ($resto == 0) {
            $digito = 0;
        }
		
        return $digito;
		
}

function b085_modulo_11($num, $base=9, $r=0)  {
    /**
     *   Autor:
     *           Pablo Costa <pablo@users.sourceforge.net>
     *
     *   Fun��o:
     *    Calculo do Modulo 11 para geracao do digito verificador 
     *    de boletos bancarios conforme documentos obtidos 
     *    da Febraban - www.febraban.org.br 
     *
     *   Entrada:
     *     $num: string num�rica para a qual se deseja calcularo digito verificador;
     *     $base: valor maximo de multiplicacao [2-$base]
     *     $r: quando especificado um devolve somente o resto
     *
     *   Sa�da:
     *     Retorna o Digito verificador.
     *
     *   Observa��es:
     *     - Script desenvolvido sem nenhum reaproveitamento de c�digo pr� existente.
     *     - Assume-se que a verifica��o do formato das vari�veis de entrada � feita antes da execu��o deste script.
     */                                        

    $soma = 0;
    $fator = 2;

    /* Separacao dos numeros */
    for ($i = strlen($num); $i > 0; $i--) {
        // pega cada numero isoladamente
        $numeros[$i] = substr($num,$i-1,1);
        // Efetua multiplicacao do numero pelo falor
        $parcial[$i] = $numeros[$i] * $fator;
        // Soma dos digitos
        $soma += $parcial[$i];
        if ($fator == $base) {
            // restaura fator de multiplicacao para 2 
            $fator = 1;
        }
        $fator++;
    }

    /* Calculo do modulo 11 */
    if ($r == 0) {
        $soma *= 10;
        $digito = $soma % 11;
        if ($digito == 10) {
            $digito = 0;
        }
        return $digito;
    } elseif ($r == 1){
        $resto = $soma % 11;
        return $resto;
    }
}

function b085_monta_linha_digitavel($codigo) {
  // CAMPO 1
	// 01-03    -> Código do banco sem o digito
	// 04-04    -> Código da Moeda (9-Real)
	// 05-09    -> Número do Convênio
  // 10-10    -> DV CAMPO 1
  
  // CAMPO 2
	// 11-11    -> Número do convênio
  // 12-19    -> Número da conta corrente
  // 20-20    -> Número do boleto
  // 21-21    -> DV CAMPO 2
  
  // CAMPO 3
	// 22-29    -> Número do boleto (sem dígito)
	// 30-31    -> Número da Carteira
  // 32-32    -> DV CAMPO 3
  
  // CAMPO 4
  // 33-33    -> DV do Código de barras
  
  // CAMPO 5
  // 34-37    -> Fator Vencimento
  // 38-47    -> Valor

    // CAMPO 1    
		$p1 = substr($codigo, 0, 4);						// Numero do banco + moeda
		$p2 = substr($codigo, 4, 5);					    // número do convênio
		$p3 = b085_modulo_10("$p1$p2");		    // Digito do campo 1
		$p4 = "$p1$p2$p3";								// União
		$campo1 = substr($p4, 0, 5).'.'.substr($p4, 5);

		// CAMPO 2
		$p1 = substr($codigo, 9, 1);						//último número do convênio
		$p2 = substr($codigo, 10, 8);                     //conta corrente com dv
		$p3 = substr($codigo, 18, 1);                     //primeiro número do boleto (nosso número)
		$p4 = b085_modulo_10("$p1$p2$p3");		    //Digito do campo 2
		$p5 = "$p1$p2$p3$p4";
		$campo2 = substr($p5, 0, 5).'.'.substr($p5, 5);

		// CAMPO 3
		$p1 = substr($codigo, 19, 8);						//restante do número do boleto (nosso número)
		$p2 = substr($codigo, 27, 2);           //número da carteira
		$p3 = b085_modulo_10("$p1$p2");					    //Digito do Campo 3
		$p4 = "$p1$p2$p3";
		$campo3 = substr($p4, 0, 5).'.'.substr($p4, 5);

		// CAMPO 4 - digito verificador do codigo de barras
		$campo4 = substr($codigo, 4, 1);

		// CAMPO 5
		$p1 = substr($codigo, 30, 4);   //fator vencimento
		$p2 = substr($codigo, 34, 10);  //valor do boleto
		$campo5 = "$p1$p2";

		return "$campo1 $campo2 $campo3 $campo4 $campo5 \n";
}

function b085_geraCodigoBanco($numero) {
    $parte1 = substr($numero, 0, 3);
    $parte2 = b085_modulo_10($parte1);
    return $parte1 . "-" . $parte2;
}

?>
