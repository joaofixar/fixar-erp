<?php
/**
* Código de todos os erros internos (nao da SEFAZ)
*/
$codigos = array(
	0	=> '',
	// 1 -> 100 (OK)
	1	=> 'Gerando XML ...',
	2	=> 'Assinando XML ...',
	3	=> 'Transmitindo MDF-e para a SEFAZ ...',
	4	=> 'XML Gerado! Assinando ...',
	5	=> 'XML Assinado! Transmitindo ...',
	6	=> 'Aguardando retorno da Sefaz ...',
	7	=> 'Damdfe Gerado!',
	8	=> 'MDF-e Enviada! Aguardando confirmacao ...',
	9	=> 'MDF-e Enviado e Autorizado!',																		// botao de Gerar Danfe
	10	=> 'Refazendo MDF-e ...',
	11 	=> 'Refazendo MDF-e para ser enviada via SVC ...',
	12	=> 'Iniciando Processo ...',
	13	=> 'Enviado e-mail ...',
	14  => 'Encerrando MDF-e ...',
	15  => 'MDF-e Encerrado',
	
	// 100 -> 1000 (ERROS)
	100	=> 'ERRO Interno: SELECT na T276 nao retornou resultado!',
	101	=> 'MDF-e-e VERSAO 1.10 nao e mais suportado.',
	102	=> 'Versao nao foi definida no MDFE (T276_Versao_MDFe), verifique!',
	104	=> 'Tipo Certificado A3 nao e mais suportado.',
	105	=> 'Não foi definida corretamente a modalidade de transporte (T276_Flag_Modalidade_Transporte)!',
	106	=> 'MDFe esta cancelado nao pode ser transmitido para a sefaz!',
	107	=> 'Senha do certificado invalida.',
	108	=> 'Senha do certificado em branco.',
	109	=> 'Erro Gerando XML!',																					// TODO: botao refazer (refazer_envio_nfe.php)
	110 => 'ERRO Interno: Tentando gravar a chave de acesso na tabela T276!',
	111 => 'ERRO Interno: Tentando gravar na T285!',
	112 => 'ERRO Interno: Falha ao copiar arrquivo!',
	113 => 'MDF-e nao foi assinado, impossível transmitir!',
	114 => 'Ocorreu um erro ao gravar o retorno do Sefaz. Por favor entre em contato com o suporte e informe o numero do recibo.',
	115 => 'A MDF-e foi enviada, porem, ocorreu um erro ao gravar o retorno do Sefaz na nota fiscal. Por favor entre em contato com o suporte e informe o numero do recibo.',
	116 => 'A MDF-e foi enviada, porem, ao consultar o protocolo, ocorreu um erro.',
	117 => 'ERRO Interno: Tentando limpar chave de acesso.',
	118 => 'ESSE MDF-E JA POSSUI PROTOCOLO, NAO PODE SER ENVIANDO PELA SVC.',
	119 => 'Ja existe um numero de protocolo para essa nota, processo nao pode ser refeito.',
	120 => 'ERRO Interno: Salvando dados na T276.',
	121 => 'Certificado Digital não encontrado.',
	
	// 200 - 300 (Dentro do erro da Sefaz)
	200 => 'Atencao! Erro nao encontrado registro de erro na global.T137 - verificar e incluir este erro.',		// Somente LOG! Nao faz update na T276, nao para o processo. && extra = T136_Numero_Erro
	201 => 'Nao foi possivel limpar a chave de acesso, pois esta nota ja tem protocolo.',						// extra = T136_Numero_Erro
	202 => 'Erro Sefaz',																						// extra = T136_Numero_Erro
);