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
// | PHPBoleto de Jo�o Prado Maia e Pablo Martins F. Costa				  |
// | 																	  |
// | Se vc quer colaborar, nos ajude a desenvolver p/ os demais bancos :-)|
// | Acesse o site do Projeto BoletoPhp: www.boletophp.com.br             |
// +----------------------------------------------------------------------+

// +----------------------------------------------------------------------+
// | Equipe Coordena��o Projeto BoletoPhp: <boletophp@boletophp.com.br>   |
// | Desenvolvimento Boleto Unibanco: Elizeu Alcantara                    |
// | Colabora��es: Humberto Coelho Oliveira				                  |
// +----------------------------------------------------------------------+


// FUN��ES
// Algumas foram retiradas do Projeto PhpBoleto e modificadas para atender as particularidades de cada banco

function b409_digitoVerificador($numero) {
    $digito = b409_modulo_11($numero);
    if (in_array((int)$digito,array(0,1,10))) {
        $digito = 1;
	}
    return $digito;
}

function b409_formata_numero($numero,$loop,$insert,$tipo = "geral") {
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


function b409_fbarcode($valor){

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
  $i = round(b409_esquerda($texto,2));
  $texto = b409_direita($texto,strlen($texto)-2);
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

function b409_esquerda($entra,$comp){
	return substr($entra,0,$comp);
}

function b409_direita($entra,$comp){
	return substr($entra,strlen($entra)-$comp,$comp);
}

function b409_fator_vencimento($data) {
	$data = explode("/",$data);
	$ano = $data[2];
	$mes = $data[1];
	$dia = $data[0];
    return(abs((b409_dateToDays("1997","10","07")) - (b409_dateToDays($ano, $mes, $dia))));
}

function b409_dateToDays($year,$month,$day) {
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

function b409_modulo_10($num) {
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

function b409_modulo_11($num, $base=9, $r=0)  {
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

function b409_monta_linha_digitavel($codigo) {

        $banco    = substr($codigo,0,3);
        $moeda    = substr($codigo,3,1);
        $k        = substr($codigo,4,1);
        $fator    = substr($codigo,5,4);
        $valor    = substr($codigo,9,10);
        $cvt 	  = substr($codigo,19,1);
        $codigo_cliente = substr($codigo,20,7);
        $zero  	  = substr($codigo,27,2);
        $nossonumero_dv = substr($codigo,29,15);
		
        //-- 1 a 3 - n�mero do Unibanco: 409 (n�mero FIXO)
        //-- 4 - c�digo da moeda. Real (R$)=9 (n�mero FIXO)
        //-- 5 - c�digo para transa��o CVT: 5 (n�mero FIXO) (5=7744-5)
        //-- 6 a 9 - 4 primeiros algarismos do c�digo do cliente no C�DIGO DE BARRAS(Informado pelo Banco)
        //-- 10 - d�gito verificador do 1� grupo,  calculado pelo m�dulo 10 (p�gina 10)
        $p1 = $banco.$moeda.$cvt.substr($codigo_cliente,0,4);
        $dv_1 = b409_modulo_10($p1);
        $campo1 = substr($p1,0,5).'.'.substr($p1,-4).$dv_1;

        //-- 11 A 13 - o 5� ao 7� algarismos do c�digo do cliente no C�DIGO DE BARRAS (Informado pelo Banco)
        //-- 14 a 15 - vago. Usar 00
        //-- 16 a 20 - 5 primeiros algarismos do n�mero de refer�ncia do cliente (Nosso N�mero)
        //-- 21 - d�gito verificador do 2� grupo,  calculado pelo m�dulo 10 (p�gina 10)
        $p12 = substr($codigo_cliente,4,3).$zero.substr($nossonumero_dv,0,5);
        $dv_2 = b409_modulo_10($p12);
        $campo2 = substr($p12,0,5).'.'.substr($p12,-5).$dv_2;
		
		//-- 22 a 31 - do 6� ao 15� algarismo do n�mero de refer�ncia do cliente
        //-- 32 - d�gito verificador do 3� grupo,  calculado pelo m�dulo 10 (p�gina 10)
        $p13 = substr($nossonumero_dv,5,10);
        $dv_3 = b409_modulo_10($p13);
        $campo3 = substr($p13,0,5).'.'.substr($p13,-5).$dv_3;
		
        //-- 33 - d�gito verificador do C�DIGO DE BARRAS. � o MESMO d�gito j� calculado na seq��ncia num�rica de origem do C�DIGO DE BARRAS
        $campo4 = $k;
		
        // 5. Campo composto pelo valor nominal pelo valor nominal do documento, sem
        // indicacao de zeros a esquerda e sem edicao (sem ponto e virgula). Quando se
        // tratar de valor zerado, a representacao deve ser 000 (tres zeros).
        
        //-- 34 a 37 - fator de vencimento em 4 algarismos, conforme tabela da p�gina 14
        //-- 38 a 47 - valor do t�tulo com zeros � esquerda

        $campo5 = $fator.$valor;
        return "$campo1 $campo2 $campo3 $campo4 $campo5";
}

function b409_geraCodigoBanco($numero) {
    $parte1 = substr($numero, 0, 3);
    $parte2 = b409_modulo_11($parte1);
    return $parte1 . "-" . $parte2;
}

?>
