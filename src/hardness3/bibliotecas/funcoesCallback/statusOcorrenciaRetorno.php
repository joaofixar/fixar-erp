<?php
namespace hardness;
function statusOcorrenciaRetorno($array){
	global $g;
	$dataCredito = $array['todosCampos']['T038_Data_Credito'];
	$codigoBanco = $array['todosCampos']['T037_Codigo_Banco'];
	//ARRAYS COM AS OCORRENCIAS DE CADA BANCO
	$oc237 = array( // BRADESCO
		'2' => 'Entrada Confirmada',
		'3' => 'Entrada Rejeitada',
		'6' => 'Liquidado',
		'9' => 'Baixado Automaticamente',
		'10' => 'Baixado conforme Instr.',
		'11' => 'Em ser - Arquivo de Titulos Pend.',
		'12' => 'Abatimento Concedido',
		'13' => 'Abatimento Cancelado',
		'14' => 'Vencimento Alterado',
		'15' => 'Liquidado',
		'16' => 'Titulo pago em Cheque',
		'17' => 'Liquidação após baixa ou Titulo não registrado',
		'18' => 'Acerto de Depositária',
		'19' => 'Conf. Receb. Inst. de Protesto',
		'20' => 'Conf. Receb. Instrução Sustentação de Protesto',
		'21' => 'Acerto do Contr. do Partc.',
		'22' => 'Titulo com Pagamento Cancelado',
		'23' => 'Entrada do Titulo em Cartorio',
		'24' => 'Entrada Rejeitada por Cep Irregular',
		'25' => 'Conf. Receb. Inst. de Prot. Falimentar',
		'27' => 'Baixa Rejeitada',
		'28' => 'Débito de Tarifas/Custas',
		'29' => 'Ocorrências do Sacado',
		'30' => 'Alteração de Dados Rejeitados',
		'32' => 'Instrução Rejeitada',
		'33' => 'Conf. Pedido Alter. Dados',
		'34' => 'Ret. de Cartorio e Manut. de Carteira',
		'35' => 'Desagendamento do Débito Automatico',
		'40' => 'Estorno de Pagamento',
		'55' => 'Sustentado Judicial',
		'68' => 'Acerto dos dados do Rateio de Crédito',
		'69' => 'Cancelamento dos dados do Rateio'
	);

	$oc341 = array( // ITAU
		'2' => 'Entrada Confirmada Com Possibilidade De Mensagem',
		'3' => 'Entrada Rejeitada',
		'4' => 'Alteração De Dados - Nova Entrada Ou Alteração/Exclusão De Dados Acatada',
		'5' => 'Alteração De Dados – Baixa',
		'6' => 'Liquidação Normal',
		'7' => 'Liquidação Parcial – Cobrança Inteligente (B2b)',
		'8' => 'Liquidação Em Cartório',
		'9' => 'Baixa Simples',
		'10' => 'Baixa Por Ter Sido Liquidado',
		'11' => 'Em Ser (Só No Retorno Mensal)',
		'12' => 'Abatimento Concedido',
		'13' => 'Abatimento Cancelado',
		'14' => 'Vencimento Alterado',
		'15' => 'Baixas Rejeitadas',
		'16' => 'Instruções Rejeitadas',
		'17' => 'Alteração/Exclusão De Dados Rejeitados',
		'18' => 'Cobrança Contratual - Instruções/Alterações Rejeitadas/Pendentes',
		'19' => 'Confirma Recebimento De Instrução De Protesto',
		'20' => 'Confirma Recebimento De Instrução De Sustação De Protesto /Tarifa',
		'21' => 'Confirma Recebimento De Instrução De Não Protestar',
		'23' => 'Título Enviado A Cartório/Tarifa',
		'24' => 'Instrução De Protesto Rejeitada / Sustada / Pendente',
		'25' => 'Alegações Do Sacado',
		'26' => 'Tarifa De Aviso De Cobrança',
		'27' => 'Tarifa De Extrato Posição (B40x)',
		'28' => 'Tarifa De Relação Das Liquidações',
		'29' => 'Tarifa De Manutenção De Títulos Vencidos',
		'30' => 'Débito Mensal De Tarifas (Para Entradas E Baixas)',
		'32' => 'Baixa Por Ter Sido Protestado',
		'33' => 'Custas De Protesto',
		'34' => 'Custas De Sustação',
		'35' => 'Custas De Cartório Distribuidor',
		'36' => 'Custas De Edital',
		'37' => 'Tarifa De Emissão De Boleto/Tarifa De Envio De Duplicata',
		'38' => 'Tarifa De Instrução',
		'39' => 'Tarifa De Ocorrências',
		'40' => 'Tarifa Mensal De Emissão De Boleto/Tarifa Mensal De Envio De Duplicata',
		'41' => 'Débito Mensal De Tarifas – Extrato De Posição (B4ep/B4ox)',
		'42' => 'Débito Mensal De Tarifas – Outras Instruções',
		'43' => 'Débito Mensal De Tarifas – Manutenção De Títulos Vencidos',
		'44' => 'Débito Mensal De Tarifas – Outras Ocorrências',
		'45' => 'Débito Mensal De Tarifas – Protesto',
		'46' => 'Débito Mensal De Tarifas – Sustação De Protesto',
		'47' => 'Baixa Com Transferência Para Desconto',
		'48' => 'Custas De Sustação Judicial',
		'51' => 'Tarifa Mensal Ref A Entradas Bancos Correspondentes Na Carteira',
		'52' => 'Tarifa Mensal Baixas Na Carteira',
		'53' => 'Tarifa Mensal Baixas Em Bancos Correspondentes Na Carteira',
		'54' => 'Tarifa Mensal De Liquidações Na Carteira',
		'55' => 'Tarifa Mensal De Liquidações Em Bancos Correspondentes Na Carteira',
		'56' => 'Custas De Irregularidade',
		'57' => 'Instrução Cancelada',
		'59' => 'Baixa Por Crédito Em C/C Através Do Sispag',
		'60' => 'Entrada Rejeitada Carnê',
		'61' => 'Tarifa Emissão Aviso De Movimentação De Títulos (2154)',
		'62' => 'Débito Mensal De Tarifa - Aviso De Movimentação De Títulos (2154)',
		'63' => 'Título Sustado Judicialmente',
		'64' => 'Entrada Confirmada Com Rateio De Crédito',
		'69' => 'Cheque Devolvido',
		'71' => 'Entrada Registrada, Aguardando Avaliação',
		'72' => 'Baixa Por Crédito Em C/C Através Do Sispag Sem Título Correspondente',
		'73' => 'Confirmação De Entrada Na Cobrança Simples – Entrada Não Aceita Na Cobrança Contratual',
		'76' => 'Cheque Compensado',
	);

	$oc399 = array( //HSBC
		'2' => 'Entrada confirmada',
		'3' => 'Entrada Rejeitada ou Instrução Rejeitada',
		'6' => 'Liquidação normal em dinheiro',
		'7' => 'Liquidação por conta em dinheiro',
		'9' => 'Baixa automática',
		'10' => 'Baixado conforme instruções',
		'11' => 'Títulos em ser',
		'12' => 'Abatimento Concedido',
		'13' => 'Abatimento Cancelado',
		'14' => 'Vencimento prorrogado',
		'15' => 'Liquidação em cartório em dinheiro',
		'16' => 'Liquidação - baixado/devolvido em data anterior dinheiro',
		'17' => 'Entregue em cartório',
		'18' => 'Instrução automática de protesto',
		'21' => 'Instrução de alteração de mora',
		'22' => 'Instrução de protesto processada/re-emitida',
		'23' => 'Cancelamento de protesto processado',
		'27' => 'Número do cedente ou controle do participante Alterado',
		'31' => 'Liquidação normal em cheque/compensação/banco correspondente',
		'32' => 'Liquidação em cartório em cheque',
		'33' => 'Liquidação por conta em cheque',
		'36' => 'Liquidação - baixado/devolvido em data anterior em cheque',
		'37' => 'Baixa de título Protestado',
		'38' => 'Liquidação de título não registrado - em dinheiro',
		'39' => 'Liquidação de título não registrado - em cheque',
		'49' => 'Vencimento Alterado',
		'51' => 'Título DDA aceito pelo sacado',
		'52' => 'Título DDA não reconhecido pelo sacado',
		'69' => 'Despesas/custas de cartório',
		'70' => 'Ressarcimento sobre títulos',
		'71' => 'Instrução não permitida para título em garantia de operação',
		'72' => 'Concessão de desconto aceito',
		'73' => 'Cancelamento condição de desconto fixo aceito',
		'74' => 'Cancelamento de desconto diário aceito'
	);

	$oc001 = array( //BANCO DO BRASIL
		'2' => 'Confirmação de Entrada de Título',
		'3' => 'Comando recusado',
		'5' => 'Liquidado sem registro (carteira 17 - tipo 4)',
		'6' => 'Liquidação Normal',
		'7' => 'Liquidação por Conta',
		'8' => 'Liquidação por Saldo',
		'9' => 'Baixa de Titulo',
		'10' => 'Baixa Solicitada',
		'11' => 'Títulos em Ser (constara somente do arquivo de existência de cobrança, fornecido mediante solicitação do cliente)',
		'12' => 'Abatimento Concedido',
		'13' => 'Abatimento Cancelado',
		'14' => 'Alteração de Vencimento do título',
		'15' => 'Liquidação em Cartório',
		'16' => 'Confirmação de alteração de juros de mora',
		'19' => 'Confirmação de recebimento de instruções para protesto',
		'20' => 'Debito em Conta',
		'21' => 'Alteração do Nome do Sacado',
		'22' => 'Alteração do Endereço do Sacado',
		'23' => 'Indicação de encaminhamento a cartório',
		'24' => 'Sustar Protesto',
		'25' => 'Dispensar Juros de mora',
		'26' => 'Alteração do número do título dado pelo Cedente (Seu número) – 10 e 15 digitos',
		'28' => 'Manutenção de titulo vencido',
		'31' => 'Conceder desconto',
		'32' => 'Não conceder desconto',
		'33' => 'Retificar desconto',
		'34' => 'Alterar data para desconto',
		'35' => 'Cobrar Multa',
		'36' => 'Dispensar Multa',
		'37' => 'Dispensar Indexador',
		'38' => 'Dispensar prazo limite para recebimento',
		'39' => 'Alterar prazo limite para recebimento',
		'41' => 'Alteração do número do controle do participante (25 digitos)',
		'42' => 'Alteração do número do documento do sacado (CNPJ/CPF)',
		'44' => 'Título pago com cheque devolvido',
		'46' => 'Título pago com cheque, aguardando compensação',
		'72' => 'Alteração de tipo de cobrança (específico para títulos das carteiras 11 e 17)',
		'96' => 'Despesas de Protesto',
		'97' => 'Despesas de Sustação de Protesto',
		'98' => 'Debito de Custas Antecipadas'
	);

	$oc104 = array( //CAIXA ECONÔMICA
		'1' => 'Entrada Confirmada',
		'2' => 'Baixa Manual Confirmada',
		'3' => 'Abatimento Concedido',
		'4' => 'Abatimento Cancelado',
		'5' => 'Vencimento Alterado',
		'6' => 'Uso da Empresa Alterado',
		'8' => 'Prazo de Devolução Alterado',
		'9' => 'Alteração Confirmada',
		'10' => 'Alteração com reemissão de Boleto Confirmada',
		'11' => 'Alteração da opção de Protesto para Devolução Confirmada',
		'12' => 'Alteração da opção de Devolução para Protesto Confirmada',
		'20' => 'Em Ser',
		'21' => 'Liquidação',
		'22' => 'Liquidação em Cartório',
		'23' => 'Baixa por Devolução',
		'25' => 'Baixa por Protesto',
		'26' => 'Título enviado para Cartório',
		'27' => 'Sustação de protesto',
		'28' => 'Estorno de Protesto',
		'29' => 'Estorno de Sustação de Protesto',
		'30' => 'Alteração de Título',
		'31' => 'Tarifa sobre Título Vencido',
		'32' => 'Outras Tarifas de Alteração',
		'33' => 'Estorno de Baixa / Liquidação',
		'34' => 'Tarifas Diversas',
		'99' => 'Rejeição do Título – Código rejeição informado nas pos 80 a 82',
	);

	$oc033 = array( // Santander
		'1' => 'Título não existe',
		'2' => 'Entrada tít. confirmada',
		'3' => 'Entrada tít. rejeitada',
		'6' => 'Liquidação',
		'7' => 'Liquidação por conta',
		'8' => 'Liquidação por saldo',
		'9' => 'Baixa automática',
		'10' => 'Tít. baix. conf. instrução ou por título protestado',
		'11' => 'em ser',
		'12' => 'Abatimento concedido',
		'13' => 'Abatimento cancelado',
		'14' => 'Prorrogação de vencimento',
		'15' => 'Enviado para Cartório',
		'16' => 'Tít. já baixado/liquidado',
		'17' => 'Liquidado em cartório',
		'21' => 'Entrada em Cartório',
		'22' => 'Retirado de cartório',
		'24' => 'Custas de Cartório',
		'25' => 'Protestar Título',
		'26' => 'Sustar Protesto',
		'35' => 'Título DDA Reconhecido pelo Pagador',
		'36' => 'Título DDA Não Reconhecido pelo Pagador',
		'37' => 'Título DDA Recusado pela CIP', 
		'38' => 'Não Protestar (antes de iniciar o ciclo deprotesto)', 
		'39' => 'Espécie de Título Não Permite a Instrução',
		'61' => 'Confirmação de Alteração do Valor Nominal do Título',
		'62' => 'Confirmação de Alteração do Valor ou Percentual mínimo',
		'63' => 'Confirmação de Alteração do Valor ou Percentual máximo',
		'93' => 'Baixa Operacional Enviado pela CIP',
		'94' => 'Cancelamento da Baixa Operacional Enviado pela CIP',
	);

	$oc748 = array( //SICREDI
		'2' => 'Entrada Confirmada',
		'3' => 'Entrada Rejeitada',
		'6' => 'Liquidação Normal',
		'9' => 'Baixado automaticamente via arquivo',
		'10' => 'Baixado conforme instruções da cooperativa de crédito',
		'12' => 'Abatimento concedido',
		'13' => 'Abatimento cancelado',
		'14' => 'Vencimento alterado',
		'15' => 'Liquidação em cartório',
		'17' => 'Liquidação após baixa',
		'19' => 'Confirmação de recebimento de instrução de protesto',
		'20' => 'Confirmação de recebimento de instrução de sustação de protesto',
		'23' => 'Entrada de título em cartório',
		'24' => 'Entrada rejeitada por CEP irregular',
		'27' => 'Baixa rejeitada',
		'28' => 'Tarifa',
		'29' => 'Rejeição do Pagador',
		'30' => 'Alteração rejeitada',
		'32' => 'Instrução rejeitada',
		'33' => 'Confirmação de pedido de alteração de outros dados',
		'34' => 'Retirado de cartório e manutenção em carteira',
		'35' => 'Aceite do pagador',
	);

	$oc085 = array( //VIACREDI
		'2' => 'Entrada Confirmada',
		'3' => 'Entrada Rejeitada',
		'6' => 'Liquidação Normal',
		'7' => 'Confirmação do Recebimento da Instrução de Desconto',
		'8' => 'Confirmação do Recebimento do Cancelamento do Desconto',
		'9' => 'Baixa',
		'12' => 'Confirmação Recebimento Instrução de Abatimento',
		'13' => 'Confirmação Recebimento Instrução de Cancelamento Abatimento',
		'14' => 'Confirmação Recebimento Instrução Alteração de Vencimento',
		'17' => 'Liquidação Após Baixa ou Liquidação Título Não Registrado',
		'19' => 'Confirmação Recebimento Instrução de Protesto',
		'20' => 'Confirmação Recebimento Instrução de Sustação/Cancelamento de Protesto',
		'23' => 'Remessa a Cartório (Aponte em Cartório)',
		'24' => 'Retirada de Cartório e Manutenção em Carteira',
		'25' => 'Protestado e Baixado (Baixa por Ter Sido Protestado)',
		'26' => 'Instrução Rejeitada',
		'27' => 'Confirmação do Pedido de Alteração de Outros Dados',
		'38' => 'Débito de Tarifas/Custas',
		'42' => 'Confirmação da alteração dos dados do Sacado',
		'46' => 'Instrução para cancelar protesto confirmada',
		'76' => 'Liquidação de boleto cooperativa emite e expede',
		'77' => 'Liquidação de boleto após baixa ou não registrado cooperativa emite e expede',
		'91' => 'Título em aberto não enviado ao pagador',
		'92' => 'Inconsistência Negativação Serasa',
		'93' => 'Inclusão Negativação via Serasa',
		'94' => 'Exclusão Negativação Serasa ',
	);

	$oc422 = array( //SAFRA
		'2' => 'ENTRADA CONFIRMADA',
		'3' => 'ENTRADA REJEITADA',
		'4' => 'TRANSFERÊNCIA DE CARTEIRA (ENTRADA)',
		'5' => 'TRANSFERÊNCIA DE CARTEIRA (BAIXA)',
		'6' => 'LIQUIDAÇÃO NORMAL',
		'9' => 'BAIXADO AUTOMATICAMENTE',
		'10' => 'BAIXADO CONFORME INSTRUÇÕES',
		'11' => 'TÍTULOS EM SER (PARA ARQUIVO MENSAL)',
		'12' => 'ABATIMENTO CONCEDIDO',
		'13' => 'ABATIMENTO CANCELADO',
		'14' => 'VENCIMENTO ALTERADO',
		'15' => 'LIQUIDAÇÃO EM CARTÓRIO',
		'19' => 'CONFIRMAÇÃO DE INSTRUÇÃO DE PROTESTO',
		'20' => 'CONFIRMAÇÃO DE SUSTAR PROTESTO',
		'21' => 'TRANSFERÊNCIA DE BENEFICIÁRIO',
		'23' => 'TÍTULO ENVIADO A CARTÓRIO',
		'40' => 'BAIXA DE TÍTULO PROTESTADO',
		'41' => 'LIQUIDAÇÃO DE TÍTULO BAIXADO',
		'42' => 'TÍTULO RETIRADO DO CARTÓRIO',
		'43' => 'DESPESA DE CARTÓRIO',
		'44' => 'ACEITE DO TÍTULO DDA PELO PAGADOR',
		'45' => 'NÃO ACEITE DO TÍTULO DDA PELO PAGADOR',
		'51' => 'VALOR DO TÍTULO ALTERADO',
		'52' => 'ACERTO DE DATA DE EMISSAO',
		'53' => 'ACERTO DE COD ESPECIE DOCTO',
		'54' => 'ALTERACAO DE SEU NUMERO',
		'56' => 'INSTRUÇÃO NEGATIVAÇÃO ACEITA',
		'57' => 'INSTRUÇÃO BAIXA DE NEGATIVAÇÃO ACEITA',
		'58' => 'INSTRUÇÃO NÃO NEGATIVAR ACEITA', 
	);

	$oc756 = array( //SICOOB
		'2' => 'Entrada confirmada',
		'3' => 'Entrada rejeitada',
		'4' => 'Transferência de carteira/Entrada',
		'5' => 'Liquidação Sem Registro',
		'6' => 'Liquidação Normal',
		'9' => 'Baixa de Titulo',
		'10' => 'Baixa Solicitada a pedido do beneficiário',
		'11' => 'Títulos em Ser (arquivo mensal)',
		'14' => 'Alteração de Vencimento',
		'15' => 'Liquidação em Cartório',
		'23' => 'Encaminhado a Protesto',
		'27' => 'Confirmação Alteração Dados',
		'48' => 'Confirmação de instrução de transferência de carteira',
	);

	$statusOc = $array['todosCampos']['T038_Codigo_Natureza_Recebimento'];	
	$ocorrencias = ${"oc" . $codigoBanco};
	if(array_key_exists($statusOc, $ocorrencias)){

		//Mostrar ocorrencias de rejeição em vermelho
		//Bradesco
		if(($codigoBanco == '237') && ($statusOc == '3' || $statusOc == '27' || $statusOc == '30' || $statusOc == '32')){
			return '<span style="color:white; background-color:red ; padding:5px">'.$statusOc . ' - ' . $ocorrencias[$statusOc].'<span>';
		}
		//Itaú
		if(($codigoBanco == '341') && ($statusOc == '15' || $statusOc == '3' || $statusOc == '16' || $statusOc == '24' || $statusOc == '60')){
			return '<span style="color:white; background-color:red ; padding:5px">'.$statusOc . ' - ' . $ocorrencias[$statusOc].'<span>';
		}
		//HSBC
		if($codigoBanco == '399' && $statusOc == '3'){
			return '<span style="color:white; background-color:red ; padding:5px">'.$statusOc . ' - ' . $ocorrencias[$statusOc].'<span>';
		}
		//Banco do Brasil
		if($codigoBanco == '001' && $statusOc == '3'){
			return '<span style="color:white; background-color:red ; padding:5px">'.$statusOc . ' - ' . $ocorrencias[$statusOc].'<span>';
		}
		//Caixa
		if($codigoBanco == '104' && $statusOc == '99'){
			return '<span style="color:white; background-color:red ; padding:5px">'.$statusOc . ' - ' . $ocorrencias[$statusOc].'<span>';
		}
		//Santander
		if($codigoBanco == '33' && $statusOc == '3'){
			return '<span style="color:white; background-color:red ; padding:5px">'.$statusOc . ' - ' . $ocorrencias[$statusOc].'<span>';
		}
		//Sicredi
		if(($codigoBanco == '748') && ($statusOc == '3' || $statusOc == '24' || $statusOc == '27')){
			return '<span style="color:white; background-color:red ; padding:5px237">'.$statusOc . ' - ' . $ocorrencias[$statusOc].'<span>';
		}
		//Viacredi
		if(($codigoBanco == '085') && ($statusOc == '3' || $statusOc == '26' || $statusOc == '77' || $statusOc == '91' || $statusOc == '92')){
			return '<span style="color:white; background-color:red ; padding:5px237">'.$statusOc . ' - ' . $ocorrencias[$statusOc].'<span>';
		}
		//SAFRA
		if(($codigoBanco == '422') && ($statusOc == '3' || $statusOc == '13' || $statusOc == '14' || $statusOc == '45' || $statusOc == '58')){
			return '<span style="color:red; background-color:white ; padding:5px237">'.$statusOc . ' - ' . $ocorrencias[$statusOc].'<span>';
		}
		//SICOOB
		if(($codigoBanco == '756') && ($statusOc == '3')){
			return '<span style="color:white; background-color:red ; padding:5px237">'.$statusOc . ' - ' . $ocorrencias[$statusOc].'<span>';
		}

		if($codigoBanco == '237' && $statusOc == '17'){
			if($dataCredito != '0000-00-00'){
				return $statusOc . ' - ' . 'Liquidação após Baixa';
			} else {
				return $statusOc . ' - ' . 'Titulo não registrado';
			}			
		} else {
			return $statusOc . ' - ' . $ocorrencias[$statusOc];
		}		
	} else {
		return $statusOc;
	}
}