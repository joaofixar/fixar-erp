<?php
/**
* Código de todos os erros internos (nao da SEFAZ)
*/
$codigos = array(
	0	=> '',
	// 1 -> 100 (OK)
	1	=> 'Gerando XML ...',
	2	=> 'Assinando XML ...',
	3	=> 'Transmitindo NF-e para a SEFAZ ...',
	4	=> 'XML Gerado! Assinando ...',
	5	=> 'XML Assinado! Transmitindo ...',
	6	=> 'Aguardando retorno da Sefaz ...',
	7	=> 'Danfe Gerado!',
	8	=> 'NF-e Enviada! Aguardando confirmacao ...',
	9	=> 'NF-e Enviada e Autorizada!',																		// botao de Gerar Danfe
	10	=> 'Refazendo NF ...',
	11 	=> 'Refazendo NF para ser enviada via SVC ...',
	12	=> 'Iniciando Processo ...',
	13	=> 'Enviado e-mail ...',
	14	=> 'Enviado e-mail boleto...',
	15  => 'NFC-e emitida em contingência',
	
	// 100 -> 1000 (ERROS)
	100	=> 'ERRO Interno: SELECT na T007 nao retornou resultado!',
	101	=> 'NF-e VERSAO 1.10 nao e mais suportado.',
	102	=> 'Versao nao foi definida na nota fiscal (T007_Versao_NFe), verifique!',
	103	=> 'NFe esta definida como treinamento e o ambiente nao e homologacao, verifique!',
	104	=> 'Tipo Certificado A3 nao e mais suportado.',
	105	=> 'O Tipo do documento nao e NF-e. Verifique!',
	106	=> 'NOTA FISCAL ESTA CANCELADA NAO PODE SER TRANSMITIDA PARA O SEFAZ!',
	107	=> 'Senha do certificado invalida.',
	108	=> 'Senha do certificado em branco.',
	109	=> 'Erro Gerando XML!',																					// TODO: botao refazer (refazer_envio_nfe.php)
	110 => 'ERRO Interno: Tentando gravar a chave de acesso na tabela T007!',
	111 => 'ERRO Interno: Tentando gravar na T127!',
	112 => 'ERRO Interno: Falha ao copiar arrquivo!',
	113 => 'Nota Fiscal nao foi assinada, impossível transmitir!',
	114 => 'Ocorreu um erro ao gravar o retorno do Sefaz. Por favor entre em contato com o suporte e informe o numero do recibo.',
	115 => 'A NF-e foi enviada, porem, ocorreu um erro ao gravar o retorno do Sefaz na nota fiscal. Por favor entre em contato com o suporte e informe o numero do recibo.',
	116 => 'A NF-e foi enviada, porem, ao consultar o protocolo, ocorreu um erro.',
	117 => 'ERRO Interno: Tentando limpar chave de acesso.',
	118 => 'ESSA NOTA JA POSSUI PROTOCOLO, NAO PODE SER ENVIANDO PELA SVC.',
	119 => 'Ja existe um numero de protocolo para essa nota, processo nao pode ser refeito.',
	120 => 'ERRO Interno: Salvando dados na T007.',
	121 => 'Certificado Digital não encontrado.',
	122 => 'Erro de Mysql.',
	
	// 200 - 300 (Dentro do erro da Sefaz)
	200 => 'Atencao! Erro nao encontrado registro de erro na global.T132 - verificar e incluir este erro.',		// Somente LOG! Nao faz update na T007, nao para o processo. && extra = T136_Numero_Erro
	201 => 'Nao foi possivel limpar a chave de acesso, pois esta nota ja tem protocolo.',						// extra = T136_Numero_Erro
	202 => 'Erro Sefaz',																						// extra = T136_Numero_Erro
);