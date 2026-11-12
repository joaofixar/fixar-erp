<?php
namespace hardness;
global $g;

/*
# BLOCO B (ZERO) # BLOCO B: ESCRITURAÇÃO E APURAÇÃO DO ISS #

A mudança mais importante para o SPED Fiscal 2019 foi a inclusão do Bloco B, que se refere à escrituração e apuração do ISS. 
NA DATA DE 14/01/2019 ESSE REGISTRO SÓ É OBRIGATORIO PARA EMPRESAS DO DISTRITO FEDERAL PARA OUTRAS EMPRESAS SOMENTE PRECISAR TER O REGISTRO B001(ABERTURA DO BLOCO B) E REGISTRO B990(ENCERRAMENTO DO BLOCO B)
Por enquanto, apenas as empresas do Distrito Federal estão obrigadas a informar atividades com incidência de ISS no SPED Fiscal, mas trata-se de um sinal claro de que essa exigência deve se estender para as demais empresas prestadoras de serviço.

OUTROS REGISTROS DO BLOCO B SÓ SERÃO DESENVOLVIDOS QUANDO PASSAREM A SER OBRIGATÓRIOS PARA TODAS AS EMPRESAS OU QUANDO TIVERMOS EMPRESAS DO DF

REG	   OCOR    DESCRIÇÃO																	
B001	1	   Abertura do Bloco B
B990	1	   Encerramento do bloco
*/
$contador_bloco_B = 1;

$IND_DAD = '1';

# REGISTRO K001
$B001 = array(																#TAM
	$REG = 'B001',															#004* 	
	$IND_DAD																#001 	Indicador de movimento (0 Com dados / 1 Sem dados)
);
$B001 = implode("|", $B001);
$layout .= '|' . $B001 . '|' . _SALTO_LINHA;
$contador_bloco_B++;


# REGISTRO B990 - ENCERRAMENTO DO BLOCO B
$B990 = array(																#TAM
	$REG = 'B990',															#004	
	$QTD_LIN_B = $contador_bloco_B											#-		Quantidade total de linhas do Bloco 0
);
$B990 = implode("|", $B990);
$layout .= '|' . $B990 . '|' . _SALTO_LINHA;

$linhas_bloco_B = $contador_bloco_B;

