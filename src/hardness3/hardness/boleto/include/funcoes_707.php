<?php
namespace hardness;

// +----------------------------------------------------------------------+
// | BoletoPhp - Versão Beta                                              |
// +----------------------------------------------------------------------+
// | Este arquivo está disponível sob a Licença GPL disponível pela Web   |
// | em http://pt.wikipedia.org/wiki/GNU_General_Public_License           |
// | Você deve ter recebido uma cópia da GNU Public License junto com     |
// | esse pacote; se não, escreva para:                                   |
// |                                                                      |
// | Free Software Foundation, Inc.                                       |
// | 59 Temple Place - Suite 330                                          |
// | Boston, MA 02111-1307, USA.                                          |
// +----------------------------------------------------------------------+

// +----------------------------------------------------------------------+
// | Originado do Projeto BBBoletoFree que tiveram colaborações de Daniel |
// | William Schultz e Leandro Maniezo que por sua vez foi derivado do	  |
// | PHPBoleto de João Prado Maia e Pablo Martins F. Costa				  |
// | 																	  |
// | Se vc quer colaborar, nos ajude a desenvolver p/ os demais bancos :-)|
// | Acesse o site do Projeto BoletoPhp: www.boletophp.com.br             |
// +----------------------------------------------------------------------+

// +----------------------------------------------------------------------+
// | Equipe Coordenação Projeto BoletoPhp: <boletophp@boletophp.com.br>   |
// | Desenvolvimento Boleto Daycovel                  |
// +----------------------------------------------------------------------+



error_reporting(E_ALL ^ E_DEPRECATED || E_NOTICE);

// FUNÇÕES
// Algumas foram retiradas do Projeto PhpBoleto e modificadas para atender as particularidades de cada banco

function gerarCodigoBarras($banco,$vencimento,$valor,$agencia,$carteira,$operacao,$nosso_numero){

    $fator_vencimento = b707_fator_vencimento($vencimento);
    $valor            = number_format($valor,2,',','');    
    $valor            = b707_formata_numero($valor,10,0,"valor");
    $agencia          = b707_formata_numero($agencia,4,0);
    $carteira         = b707_formata_numero($carteira,3,0);
    $operacao         = b707_formata_numero($operacao,7,0);
    $nnum             = b707_formata_numero($nosso_numero,11,0);
 
    // Código do Banco na Camara de Compensação = '707'
    // Tamanho 3
    $codigo_barras  = $banco;
    
    // Código da Moeda = '9'
    // Tamanho 1  
    $codigo_barras .= '9';
    
    // Fator de Vencimento (Anexo 6 do manual de cobranca bytes)
    // Tamanho 4    
    $codigo_barras .= $fator_vencimento;
    
    // Valor
    // Tamanho 10
    $codigo_barras .= $valor;
    
    // Agencia
    // Tamanho 4
    $codigo_barras .= $agencia;

    // Carteira
    // Tamanho 3
    $codigo_barras .= $carteira;

    // Operação
    // Tamanho 7
    $codigo_barras .= $operacao;
    
    // Nosso Número com dv
    // Tamanho 11
    $codigo_barras .= $nnum;
    
    // 43 digitos para o calculo do digito verificador
    // DAC  código de Barras     
    $dv = b707_digitoVerificador_barra($codigo_barras);
        
    // 44 digitos com o digito verificador geral do codigo de barras
    $codigo_barras = substr($codigo_barras,0,4).$dv.substr($codigo_barras,4,43);

    /* ********* NÃO TEM NO MANUAL
    // DAC [Ag?ncia/Conta/Carteira/Nosso N?mero] (Anexo 4 do manual de cobranca bytes)
    // Tamanho 1
    $codigo_barras .= b422_modulo_10($agencia.$conta.$carteira.$nnum);

    // N.? da Conta Corrente
    // Tamanho 5
    $codigo_barras .= $conta;

    // DAC [Ag?ncia/Conta Corrente] (Anexo 3 do manual de cobranca bytes)
    // Tamanho 1
    $codigo_barras .= b422_modulo_10($agencia.$conta);

    // Zeros
    // Tamanho 3
    $codigo_barras .= '000';
    */ 
 
    return $codigo_barras;
 
 }

 function b707_digitoVerificador_barra($numero) {
    $resto2 = b707_modulo_11($numero, 9, 1);
    $digito = 11 - $resto2;
      if ($digito == 0 || $digito == 1 || $digito == 10  || $digito == 11) {
          $dv = 1;
      } else {
          $dv = $digito;
      }
    return $dv;
}
 
function b707_adiciona_dv_nossonumero($numero) {
    $resto = b707_modulo_11($numero);
	  $digito = 11 - $resto;
    if ($digito == 0 || $digito == 1 || $digito == 10  || $digito == 11) {
      $dv = 1;
    } else {
      $dv = $digito;
    }
    $numero .= trim($dv); // Adiciona o digito verificador ao nosso numero
    $numero  = str_pad( $numero, 9, "0", STR_PAD_LEFT); // Adiciona zeros a esquerda ao nosso numero
     
	  return $numero;
}

function b707_formata_numero($numero,$loop,$insert,$tipo = "geral") {
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
  $i = round(b707_esquerda($texto,2));
  $texto = b707_direita($texto,strlen($texto)-2);
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
} //Fim da função

function b707_esquerda($entra,$comp){
	return substr($entra,0,$comp);
}

function b707_direita($entra,$comp){
	return substr($entra,strlen($entra)-$comp,$comp);
}

function b707_fator_vencimento($data) {
	$data = explode("/",$data);
	$ano = $data[2];
	$mes = $data[1];
	$dia = $data[0];

  // Converte a data do título para dias absolutos
  $diasVencimento = b707_dateToDays($ano, $mes, $dia);
  // Data base antiga
  $dataBaseAntiga = b707_dateToDays("1997", "10", "07");
  // Data de início da nova regra, a partir de 22/02/2025 vai retornar 1000 e assim por diante - 23/02/2025 = 1001 - 24/02/2025 = 1002 ...
  $dataBaseNova   = b707_dateToDays("2025", "02", "22");

  // Verifica se a data do título é anterior ou posterior a 21/02/2025
  if ($diasVencimento < $dataBaseNova) {
      // regra antiga
      $fator = abs($dataBaseAntiga - $diasVencimento); 
  } else {
      // regra nova
      $fator = 1000 + abs($dataBaseNova - $diasVencimento);        
  }
    
  return $fator;  

  //return(abs((b707_dateToDays("1997","10","07")) - (b707_dateToDays($ano, $mes, $dia))));
}

function b707_dateToDays($year,$month,$day) {
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

function b707_modulo_11($num)  {
    $soma  = 0;
    $fator = 2;
    $base  = 9;

    /* Separacao dos numeros */
    for ($i = strlen($num); $i > 0; $i--) {
        // pega cada numero isoladamente
        $numeros[$i] = substr($num,$i-1,1);
        // Efetua multiplicacao do numero pelo falor
        $soma += ($numeros[$i] * $fator);
        if ($fator == $base) {
            // restaura fator de multiplicacao para 2 
            $fator = 1;
        }
        $fator++;
    }

    $resto = fmod($soma, 11);
    if ($resto <= 0 || $resto == 10 || $resto == 1) {
        $digito = 1;
    } else {
        $digito = 11 - $resto;
    }

    return $digito;
}

function b707_modulo_10($num) {
		$numtotal10 = 0;
        $fator = 2;

        // Separacao dos numeros
        for ($i = strlen($num); $i > 0; $i--) {
            // pega cada numero isoladamente
            $numeros[$i] = substr($num,$i-1,1);
            // Efetua multiplicacao do numero pelo (falor 10)
            // 2002-07-07 01:33:34 Macete para adequar ao Mod10
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

// Montar linha digitável
function b707_monta_linha_digitavel($codigo) {
		// campo 1
    // composto pelo código do banco, código da moeda e as cinco primeiras posições do campo livre do cód de barras (veja definição abaixo), mais o dígito verificador deste campo
    $banco     = substr($codigo,0,3);
    $moeda     = substr($codigo,3,1);
    $agCar     = substr($codigo,4,5); //Agencia + Carteira
		$dv1       = b707_modulo_10($banco.$moeda.$agCar);

		// campo 2
    // composto pelas posições 6 a 15 do campo livre do cód de barras e dígito verificador deste campo
		$carOpeNum = substr($codigo,10,10); // Carteira + Operacao + Nosso Numero
		$dv2       = b707_modulo_10($carOpeNum);

		// campo 3
    // composto pelas posições 16 a 25 do campo livre do cód de barras e dígito verificador deste campo
		$num       = substr($codigo,21,10); // Nosso Numero
		$dv3       = b707_modulo_10($num);

		// campo 4
    // Dígito verificador do código de barras
		$dv4       = substr($codigo,32,1);

		// campo 5
    // Composto pelo fator de vencimento e pelo valor nominal do documento sem edição/dedução (sem ponto e vírgula)
    $fator     = substr($codigo,33,4);
    $valor     = substr($codigo,37,10);

    $campo1    = substr($banco.$moeda.$agCar.$dv1,0,5) . '.' . substr($banco.$moeda.$agCar.$dv1,5,5);
    $campo2    = substr($carOpeNum.$dv2,0,5) . '.' . substr($carOpeNum.$dv2,5,6);
    $campo3    = substr($num.$dv3,0,5) . '.' . substr($num.$dv3,5,6);
    $campo4    = $dv4;
    $campo5    = $fator.$valor;

    return "$campo1 $campo2 $campo3 $campo4 $campo5"; 
}

function b707_geraCodigoBanco($numero) {
    $parte1 = substr($numero, 0, 3);
    $parte2 = b707_modulo_11($parte1);
    return $parte1 . "-" . $parte2;
}

?>
