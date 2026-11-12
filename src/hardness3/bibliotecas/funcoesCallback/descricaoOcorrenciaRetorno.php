<?php
namespace hardness;
function descricaoOcorrenciaRetorno($array){
	global $g;
	$dataRecebimentoT038 = gCorrigeData($array['todosCampos']['T038_Data_Recebimento']);
	$valorAbatimento = $array['todosCampos']['T038_Valor_Abatimento'];
	$valorAbatimentoInvalido = gCorrigeNumero($valorAbatimento);

	$motivo399 = array(
		'1' => 'Valor do desconto não informado',
		'2' => 'Inexistência de agência do HSBC na praça do sacado',
		'3' => 'CEP do sacado incorreto ou inválido',
		'4' => 'Cadastro do cedente não aceita banco correspondente',
		'5' => 'Tipo de moeda inválido',
		'6' => 'Prazo de protesto indefinido (não informado)/inválido ou prazo de protesto inferior ao tempo decorrido da data de vencimento em relação ao envio da instrução de alteração de prazo',
		'7' => 'Data do vencimento inválida',
		'8' => 'Nosso número(número bancário) utilizado não possui vinculação com a conta cobrança',
		'9' => 'Taxa mensal de mora acima do permitido (170%)',
		'10' => 'Taxa de multa acima do permitido (10% ao mês)',
		'11' => 'Data limite de desconto inválida',
		'12' => 'CEP inválido/inexistência de Ag HSBC',
		'13' => 'Valor/Taxa de multa inválida',
		'14' => 'Valor diário da multa não informado',
		'15' => 'Quantidade de dias após o vencimento para incidência da multa não informada',
		'16' => 'Outras irregularidades',
		'17' => 'Data de início da multa inválida',
		'18' => 'Nosso número já existente para outro título',
		'19' => 'Valor do título inválido',
		'20' => 'Ausência de CEP/Endereço/CNPJ ou Sacador avalista',
		'21' => 'Título sem borderô',
		'22' => 'Número da conta do cedente não cadastrado',
		'23' => 'Instrução não permitida para título em garantia de operação',
		'24' => 'Condição de desconto não permitida para título em garantia de operação',
		'25' => 'Utilizada em mais de uma instrução de multa',
		'26' => 'Ausência de endereço do sacado',
		'27' => 'CEP inválido do sacado',
		'28' => 'Ausência do CPF/CNPJ do sacado em título com instrução de protesto',
		'29' => 'Agência cedente informada inválida',
		'30' => 'Número da conta do cedente inválido',
		'31' => 'Contrado garantia não cadastrado/inválido',
		'32' => 'Tipo de carteira inválido',
		'33' => 'Conta corrente do cedente não compatível com o orgão do contratante',
		'34' => 'Faixa de aplicação não cadastrada/inválida',
		'35' => 'Nosso número inválido',
		'36' => 'Data de emissão do título inválida',
		'37' => 'Valor do título acima de R$ 5.000.000,00',
		'38' => 'Data de desconto menor que data da emissão',
		'39' => 'Espécie inválida',
		'40' => 'Ausên cia de nome do sacador avalista',
		'41' => 'Data de início da multa menor que data de emissão',
		'42' => 'Quantidade de moeda variável inválida',
		'43' => 'Controle do participante inválido',
		'44' => 'Nosso número duplicado no mesmo movimento',
		'45' => 'Título não aceito para compaar a carteira de garantias',
		'50' => 'Titulo liquidado em '. $dataRecebimentoT038,
		'51' => 'Data de emissão da ocorrência inválida',
		'52' => 'Nosso número duplicado',
		'53' => 'Código de ocorrência comandada inválido',
		'54' => 'Valor do desconto concedido inválido ('.$valorAbatimentoInvalido.')',
		'55' => 'Data de prorrogação de vencimento não informada',
		'56' => 'Outras irregularidades',
		'57' => 'Ocorrência não permitida para titulo em garantia de operações',
		'58' => 'Nosso número comandado na intrução/ocorrência não possui vinculação com a conta cobrança',
		'59' => 'Nosso número comandado na baixa não possui vinculação com a conta cobrança',
		'60' => 'Valor do desconto igual ou maior que o valor do título',
		'61' => 'Título com valor em moeda varíavel não permite condição de desconto',
		'62' => 'Data do desconto informada não coincide com o registro do título',
		'63' => 'Título não possui condição de desconto diário',
		'64' => 'Título baixado em '.$dataRecebimentoT038,
		'65' => 'Título devolvido em '.$dataRecebimentoT038,
		'66' => 'Valor do título não confere com o registro',
		'67' => 'Nosso número não informado',
		'68' => 'Nosso número inválido',
		'69' => 'Concessão de abatimento não é permitida para moeda diferente de real',
		'70' => 'Valor do abatimento concedido inválido',
		'71' => 'Cancelamento comandado sobre título sem abatimento',
		'72' => 'Concessão de desconto não é permitida para moeda diferente de real',
		'73' => 'Valor do desconto não informado',
		'74' => 'Cancelamento comandado sobre título sem desconto',
		'75' => 'Data de vencimento alterado inválida '.$dataRecebimentoT038,
		'76' => 'Data de prorrogação de vencimento inválida',
		'77' => 'Data da intrução inválida',
		'78' => 'Protesto comandado em duplicidade no mesmo dia',
		'79' => 'Título não possui instrução de protesto ou está com entrada já confirmada em cartório',
		'80' => 'Título não possui condição de desconto',
		'81' => 'Título não possui instrução de abatimento',
		'82' => 'Valor de juros inválido',
		'83' => 'Nosso número inexistente',
		'84' => 'Baixa/Liquidação por orgão não autorizado',
		'85' => 'Instrução de protesto recusada/inválida',
		'86' => 'Instrução não permitida para banco correspondente',
		'87' => 'Valor da instrução inválido',
		'88' => 'Instrução invpalida para tipo de carteira',
		'89' => 'Valor do desconto informado não coincide com o registro do título',
	);

	$motivo341 = array(
		'2' => array(
			'01' => 'Cep sem atendimento de protesto no momento'
		),
		'3' => array(
			'03' => 'Cep sem atendimento de protesto no momento',
			'04' => 'Sigla do estado inválida',
			'05' => 'Prazo da operação menor que prazo mínimo ou maior que o máximo',
			'07' => 'Valor do título maior que 10.000.000,00',
			'08' => 'Nome do sacado não informado ou deslocado',
			'09' => 'Agência encerrada',
			'10' => 'Logradouro não informado ou deslocado',
			'11' => 'Cep não numérico ou cep inválido',
			'12' => 'Nome não informado ou deslocado (bancos correspondentes)',
			'13' => 'Cep incompatível com a sigla do estado',
			'14' => 'Nosso número já registrado no cadastro do banco ou fora da faixa',
			'15' => 'Nosso número em duplicidade no mesmo movimento',
			'18' => 'Data de entrada inválida para operar com esta carteira',
			'19' => 'Ocorrência inválida',
			'21' => 'Carteira não aceita depositária correspondente estado da agência diferente do estado do sacado ag. cobradora não consta no cadastro ou encerrando',
			'22' => 'Carteira não permitida (necessário cadastrar faixa livre)',
			'26' => 'Agência/conta não liberada para operar com cobrança',
			'27' => 'Cnpj do cedente inapto devolução de título em garantia',
			'29' => 'Categoria da conta inválida',
			'30' => 'Entradas bloqueadas, conta suspensa em cobrança',
			'31' => 'Conta não tem permissão para protestar (contate seu gerente)',
			'35' => 'Iof maior que 5%',
			'36' => 'Quantidade de moeda incompatível com valor do título',
			'37' => 'Não numérico ou igual a zeros',
			'42' => 'Nosso número fora de faixa',
			'52' => 'Empresa não aceita banco correspondente',
			'53' => 'Empresa não aceita banco correspondente - cobrança mensagem',
			'54' => 'Banco correspondente - título com vencimento inferior a 15 dias',
			'55' => 'Cep não pertence à depositária informada',
			'56' => 'Vencimento superior a 180 dias da data de entrada',
			'57' => 'Cep só depositária bco do brasil com vencto inferior a 8 dias',
			'60' => 'Valor do abatimento inválido',
			'61' => 'Juros de mora maior que o permitido',
			'62' => 'Valor do desconto maior que valor do título',
			'63' => 'Valor da importância por dia de desconto (idd) não permitido',
			'64' => 'Data de emissão do título inválida',
			'65' => 'Taxa inválida (vendor)',
			'66' => 'Data de vencimento invalida/fora de prazo de operação (mínimo ou máximo)',
			'67' => 'Valor do título/quantidade de moeda inválido',
			'68' => 'Carteira inválida ou não cadastrada no intercâmbio da cobrança',
			'69' => 'Carteira inválida para títulos com rateio de crédito',
			'70' => 'Cedente não cadastrado para fazer rateio de crédito',
			'78' => 'Duplicidade de agência/conta beneficiária do rateio de crédito',
			'80' => 'Quantidade de contas beneficiárias do rateio maior do que o permitido (máximo de 30 contas por título)',
			'81' => 'Conta para rateio de crédito inválida / não pertence ao itaú',
			'82' => 'Desconto/abatimento não permitido para títulos com rateio de crédito',
			'83' => 'Valor do título menor que a soma dos valores estipulados para rateio',
			'84' => 'Agência/conta beneficiária do rateio é a centralizadora de crédito do cedente',
			'85' => 'Agência/conta do cedente é contratual / rateio de crédito não permitido',
			'86' => 'Código do tipo de valor inválido / não previsto para títulos com rateio de crédito',
			'87' => 'Registro tipo 4 sem informação de agências/contas beneficiárias do rateio',
			'90' => 'Cobrança mensagem - número da linha da mensagem inválido ou quantidade de linhas excedidas',
			'97' => 'Cobrança mensagem sem mensagem (só de campos fixos), porém com registro do tipo 7 ou 8',
			'98' => 'Registro mensagem sem flash cadastrado ou flash informado diferente do cadastrado',
			'99' => 'Conta de cobrança com flash cadastrado e sem registro de mensagem correspondente',
			'91' => 'Dac agencia / conta corrente inválido',
			'92' => 'Dac agencia / conta corrente / carteira / nosso número inválido',
			'93' => 'Sigla estado inválida',
			'94' => 'Sigla estado incompativel com o cep do sacado',
			'95' => 'Cep do sacado não numérico ou inválido',
			'96' => 'Endereço / nome / cidade sacado inválido'
		),
		'15' => array(
			'01' => 'Carteira/nº número não numérico',
			'04' => 'Nosso número em duplicidade num mesmo movimento',
			'05' => 'Solicitação de baixa para título já baixado ou liquidado',
			'06' => 'Solicitação de baixa para título não registrado no sistema',
			'07' => 'Cobrança prazo curto – solicitação de baixa p/ título não registrado no sistema',
			'08' => 'Solicitação de baixa para título em floating',
			'10' => 'Valor do titulo faz parte de garantia de emprestimo',
			'11' => 'Pago através do sispag por crédito em c/c e não baixado'
		),
		'16' => array(
			'01' => 'Instrução/ocorrência não existente',
			'03' => 'Conta não tem permissão para protestar (contate seu gerente)',
			'06' => 'Nosso número igual a zeros',
			'09' => 'Cnpj/cpf do sacador/avalista inválido',
			'10' => 'Valor do abatimento igual ou maior que o valor do título',
			'11' => 'Segunda instrução/ocorrência não existente',
			'14' => 'Registro em duplicidade',
			'15' => 'Cnpj/cpf informado sem nome do sacador/avalista',
			'19' => 'Valor do abatimento maior que 90% do valor do título',
			'20' => 'Existe sustacao de protesto pendente para o titulo',
			'21' => 'Título não registrado no sistema',
			'22' => 'Título baixado ou liquidado',
			'23' => 'Instrução não aceita por ter sido emitido último aviso ao sacado',
			'24' => 'Instrução incompatível - existe instrução de protesto para o título',
			'25' => 'Instrução incompatível – não existe instrução de protesto para o título',
			'26' => 'Instrução não aceita por já ter sido emitida a ordem de protesto ao cartório',
			'27' => 'Instrução não aceita por não ter sido emitida a ordem de protesto ao cartório',
			'28' => 'Já existe uma mesma instrução cadastrada anteriormente para o título',
			'29' => 'Valor líquido + valor do abatimento diferente do valor do título registrado',
			'30' => 'Existe uma instrução de não protestar ativa para o título',
			'31' => 'Existe uma ocorrência do sacado que bloqueia a instrução',
			'32' => 'Depositária do título = 9999 ou carteira não aceita protesto',
			'33' => 'Alteração de vencimento igual à registrada no sistema ou que torna o título vencido',
			'34' => 'Instrução de emissão de aviso de cobrança para título vencido antes do vencimento',
			'35' => 'Solicitação de cancelamento de instrução inexistente',
			'36' => 'Título sofrendo alteração de controle (agência/conta/carteira/nosso número)',
			'37' => 'Instrução não permitida para a carteira',
			'38' => 'Instrução não permitida para título com rateio de crédito'
		),
		'17' => array(
			'02' => 'Agência cobradora inválida ou com o mesmo conteúdo',
			'04' => 'Sigla do estado inválida',
			'05' => 'Data de vencimento inválida ou com o mesmo conteúdo 06 valor do título com outra alteração simultânea',
			'08' => 'Nome do sacado com o mesmo conteúdo',
			'09' => 'Agência/conta incorreta',
			'11' => 'Cep inválido',
			'12' => 'Número inscrição inválido do sacador avalista',
			'13' => 'Seu número com o mesmo conteúdo',
			'16' => 'Abatimento/alteração do valor do título ou solicitação de baixa bloqueada',
			'20' => 'Espécie inválida',
			'21' => 'Agência cobradora não consta no cadastro de depositária ou em encerramento',
			'23' => 'Data de emissão do título inválida ou com mesmo conteúdo',
			'41' => 'Campo aceite inválido ou com mesmo conteúdo',
			'42' => 'Alteração inválida para título vencido',
			'43' => 'Alteração bloqueada – vencimento já alterado',
			'53' => 'Instrução com o mesmo conteúdo',
			'54' => 'Data vencimento para bancos correspondentes inferior ao aceito pelo banco',
			'55' => 'Alterações iguais para o mesmo controle (agência/conta/carteira/nosso número)',
			'56' => 'Cnpj/cpf inválido não numérico ou zerado',
			'57' => 'Prazo de vencimento inferior a 15 dias',
			'60' => 'Valor de iof – alteração não permitida para carteiras de n.s. – moeda variável',
			'61' => 'Título já baixado ou liquidado ou não existe título correspondente no sistema',
			'66' => 'Alteração não permitida para carteiras de notas de seguros – moeda variável',
			'67' => 'Nome inválido do sacador avalista',
			'72' => 'Endereço inválido – sacador avalista',
			'73' => 'Bairro inválido – sacador avalista',
			'74' => 'Cidade inválida – sacador avalista',
			'75' => 'Sigla estado inválido – sacador avalista',
			'76' => 'Cep inválido – sacador avalista',
			'81' => 'Alteração bloqueada – título com protesto',
			'87' => 'Alteração bloqueada – título com rateio de crédito'
		),
		'18' => array(
			'16' => 'Abatimento/alteração do valor do título ou solicitação de baixa bloqueados',
			'40' => 'Não aprovada devido ao impacto na elegibilidade de garantias',
			'41' => 'Automaticamente rejeitada',
			'42' => 'Confirma recebimento de instrução – pendente de análise'
		),
		'24' => array(
			'1610' => 'Documentação solicitada ao cedente',
			'3103' => 'Insuficiencia de dados no modelo 4006',
			'3111' => 'Sustação solicitada ag. cedente',
			'3129' => 'Titulo nao enviado a cartorio',
			'3137' => 'Aguardar um dia util apos o vencto para protestar',
			'3145' => 'Dm/dmi sem comprovante autenticado ou declaracao',
			'3152' => 'Falta contrato de serv(ag.ced:enviar)',
			'3160' => 'Nome do sacado incompleto/incorreto',
			'3178' => 'Nome do cedente incompleto/incorreto',
			'3186' => 'Nome do sacador incompleto/incorreto',
			'3194' => 'Tit aceito: identif assinante do cheq',
			'3202' => 'Tit aceito: rasurado ou rasgado',
			'3210' => 'Tit aceito: falta tit.(ag.ced:enviar)',
			'3228' => 'Atos da corregedoria estadual',
			'3236' => 'Nao foi possivel efetuar o protesto',
			'3244' => 'Protesto sustado / cedente não entregou a documentação',
			'3251' => 'Documentacao irregular',
			'3269' => 'Data de emissão do título inválida / irregular',
			'3277' => 'Especie invalida para protesto',
			'3285' => 'Praça não atendida pela rede bancária',
			'3293' => 'Centralizadora de protesto nao recebeu a documentacao',
			'3301' => 'Cnpj/cpf do sacado inválido / incorreto',
			'3319' => 'Sacador/avalista e pessoa física',
			'3327' => 'Cep do sacado incorreto',
			'3335' => 'Depositária incompatível com cep do sacado',
			'3343' => 'Cnpj/cpf sacador invalido / incorreto',
			'3350' => 'Endereço do sacado insuficiente',
			'3368' => 'Praça pagto incompatível com endereço',
			'3376' => 'Falta número/espécie do título',
			'3384' => 'Título aceito s/ assinatura do sacador',
			'3392' => 'Título aceito s/ endosso cedente ou irregular',
			'3400' => 'Título sem local ou data de emissão',
			'3418' => 'Título aceito com valor extenso diferente do numérico',
			'3426' => 'Título aceito definir espécie da duplicata',
			'3434' => 'Data emissão posterior ao vencimento',
			'3442' => 'Título aceito documento não protestável',
			'3459' => 'Título aceito extenso vencimento irregular',
			'3467' => 'Título aceito falta nome favorecido',
			'3475' => 'Título aceito falta praça de pagamento',
			'3483' => 'Título aceito falta cpf assinante cheque',
			'3491' => 'Falta número do título (seu número)',
			'3509' => 'Cartório da praça com atividade suspensa',
			'3517' => 'Data apresentacao menor que a data vencimento',
			'3525' => 'Falta comprovante da prestacao de servico',
			'3533' => 'Cnpj/cpf sacado incompativel c/ tipo de documento',
			'3541' => 'Cnpj/cpf sacador incompativel c/ especie',
			'3558' => 'Tit aceito: s/ assinatura do sacado',
			'3566' => 'Falta data de emissao do titulo',
			'3574' => 'Saldo maior que o valor do titulo',
			'3582' => 'Tipo de endosso invalido',
			'3590' => 'Devolvido por ordem judicial',
			'3608' => 'Dados do titulo nao conferem com disquete',
			'3616' => 'Sacado e sacador avalista são a mesma pessoa',
			'3624' => 'Comprovante ilegivel para conferencia e microfilmagem',
			'3632' => 'Confirmar se sao dois emitentes',
			'3640' => 'Endereco do sacado igual ao do sacador ou do portador',
			'3657' => 'Endereco do cedente incompleto ou nao informado',
			'3665' => 'Endereco do emitente no cheque igual ao do banco sacado',
			'3673' => 'Falta motivo da devolucao no cheque ou ilegivel',
			'3681' => 'Titulo com direito de regresso vencido',
			'3699' => 'Titulo apresentado em duplicidade',
			'3707' => 'Lc emitida manualmente(titulo do banco/ca)',
			'3715' => 'Nao protestar lc(titulo do banco/ca)',
			'3723' => 'Eliminar o protesto da lc(titulo do banco/ca)',
			'3731' => 'Titulo ja protestado',
			'3749' => 'Titulo - falta traducao por tradutor publico',
			'3756' => 'Falta declaracao de saldo assinada no titulo',
			'3764' => 'Contrato de cambio - falta conta grafica',
			'3772' => 'Sacado falecido',
			'3780' => 'Especie de titulo que o banco nao protesta',
			'3798' => 'Ausencia do documento fisico',
			'3806' => 'Ordem de protesto sustada, motivo',
			'3814' => 'Sacado apresentou quitação do título',
			'3822' => 'Sacado irá negociar com cedente',
			'3830' => 'Cpf incompatível com a espécie do título',
			'3848' => 'Título de outra jurisdição territorial',
			'3855' => 'Título com emissão anterior a concordata do sacado',
			'3863' => 'Sacado consta na lista de falência',
			'3871' => 'Apresentante não aceita publicação de edital',
			'3889' => 'Cartório com problemas operacionais',
			'3897' => 'Envio de titulos para protesto temporariamente paralisado',
			'3905' => 'Cedente com conta em cobranca suspensa',
			'3913' => 'Cep do sacado eh uma caixa postal',
			'3921' => 'Espécie não protestável no estado',
			'3939' => 'Falta endereço ou documento do sacador avalista',
			'3947' => 'Corrigir a especie do titulo',
			'3954' => 'Erro de preenchimento do titulo',
			'3962' => 'Valor divergente entre titulo e comprovante',
			'3970' => 'Condominio nao pode ser protestado p/ fins falimentares',
			'3988' => 'Vedada intimacao por edital para protesto falimentar'
		),
		'25' => array(
			'1313' => 'Solicita a prorrogação do vencimento para:',
			'1321' => 'Solicita a dispensa dos juros de mora',
			'1339' => 'Não recebeu a mercadoria',
			'1347' => 'A mercadoria chegou atrasada',
			'1354' => 'A mercadoria chegou avariada',
			'1362' => 'A mercadoria chegou incompleta',
			'1370' => 'A mercadoria não confere com o pedido',
			'1388' => 'A mercadoria está à disposição',
			'1396' => 'Devolveu a mercadoria',
			'1404' => 'Não recebeu a fatura',
			'1412' => 'A fatura está em desacordo com a nota fiscal',
			'1420' => 'O pedido de compra foi cancelado',
			'1438' => 'A duplicata foi cancelada',
			'1446' => 'Que nada deve ou comprou',
			'1453' => 'Que mantém entendimentos com o sacador',
			'1461' => 'Que pagará o título em:',
			'1479' => 'Que pagou o título diretamente ao cedente em:',
			'1487' => 'Que pagará o título diretamente ao cedente em:',
			'1495' => 'Que o vencimento correto é:',
			'1503' => 'Que tem desconto ou abatimento de:',
			'1719' => 'Sacado não foi localizado; confirmar endereço',
			'1727' => 'Sacado está em regime de concordata',
			'1735' => 'Sacado está em regime de falência',
			'1750' => 'Sacado se recusa a pagar juros bancários',
			'1768' => 'Sacado se recusa a pagar comissão de permanência',
			'1776' => 'Não foi possível a entrega do boleto ao sacado',
			'1784' => 'Boleto não entregue, mudou-se / desconhecido',
			'1792' => 'Boleto não entregue, cep errado / incompleto',
			'1800' => 'Boleto não entregue, número não existe/endereço incompleto',
			'1818' => 'Boleto não retirado pelo sacado. reenviado pelo correio para carteiras com emissão pelo banco',
			'1826' => 'Endereço de e-mail inválido/cobrança mensagem. boleto enviado pelo correio',
			'1834' => 'Boleto dda, divida reconhecida pelo sacado',
			'1842' => 'Boleto dda, divida não reconhecida pelo sacado'
		),
		'57' => array(
			'1156' => 'Não protestar',
			'2261' => 'Dispensar juros/comissão de permanência'
		),
		'69' => array(
			'11' => 'Cheque sem fundos - primeira apresentação.',
			'12' => 'Cheque sem fundos - segunda apresentação.',
			'13' => 'Conta encerrada.',
			'14' => 'Prática espúria.',
			'20' => 'Folha de cheque cancelada por solicitação do correntista.',
			'21' => 'Contra-ordem (ou revogação) ou oposição (ou sustação) ao pagamento pelo emitente ou pelo portador.',
			'22' => 'Divergência ou insuficiência de assinatura.',
			'23' => 'Cheques emitidos por entidades e órgãos da administração pública federal direta e indireta, em desacordo com os requisitos constantes do artigo 74, § 2º, do decreto-lei nº 200, de 25.02.1967.',
			'24' => 'Bloqueio judicial ou determinação do banco central do brasil.',
			'25' => 'Cancelamento de talonário pelo banco sacado.',
			'28' => 'Contra-ordem (ou revogação) ou oposição (ou sustação) ao pagamento ocasionada por furto ou roubo.',
			'29' => 'Cheque bloqueado por falta de confirmação do recebimento do talonário pelo correntista.',
			'30' => 'Furto ou roubo de malotes.',
			'31' => 'Erro formal (sem data de emissão, com o mês grafado numericamente, ausência de assinatura, -registro do valor por extenso).',
			'32' => 'Ausência ou irregularidade na aplicação do carimbo de compensação.',
			'33' => 'Divergência de endosso.',
			'34' => 'Cheque apresentado por estabelecimento bancário que  o indicado no cruzamento em preto, sem o endosso-mandato.',
			'35' => 'Cheque fraudado, emitido sem prévio controle ou responsabilidade do estabelecimento bancário ("cheque universal"), ou ainda com adulteração da praça sacada.',
			'36' => 'Cheque emitido com mais de um endosso.',
			'40' => 'Moeda inválida.',
			'41' => 'Cheque apresentado a banco que  o sacado.',
			'42' => 'Cheque -compensável na sessão ou sistema de compensação em que foi apresentado.',
			'43' => 'Cheque, devolvido anteriormente pelos motivos 21, 22, 23, 24, 31 ou 34, -passível de reapresentação em virtude de persistir o motivo da devolução.',
			'44' => 'Cheque prescrito.',
			'45' => 'Cheque emitido por entidade obrigada a realizar movimentação e utilização de recursos financeiros do tesouro nacional mediante ordem bancária.',
			'48' => 'Cheque de valor superior ao estabelecido, emitido sem a identificação do beneficiário, devendo ser devolvido a qualquer tempo.',
			'49' => 'Remessa nula, caracterizada pela reapresentação de cheque devolvido pelos motivos 12, 13, 14, 20, 25, 28, 30, 35, 43, 44 e 45, podendo a sua devolução ocorrer a qualquer tempo.'
		),
	);
	
	$motivo748 = array(
		'3' => array(
			'02' => 'Código do registro detalhe inválido',
			'03' => 'Código da ocorrência inválido',
			'04' => 'Código de ocorrência não permitida para a carteira',
			'05' => 'Código de ocorrência não numérico',
			'08' => 'Nosso número inválido',
			'09' => 'Nosso número duplicado',
			'10' => 'Carteira inválida',
			'16' => 'Data de vencimento inválida',
			'18' => 'Vencimento fora do prazo de operação',
			'20' => 'Valor do título inválido',
			'21' => 'Espécie do título inválida',
			'22' => 'Espécie não permitida para a carteira',
			'24' => 'Data de emissão inválida',
			'38' => 'Prazo para protesto inválido',
			'44' => 'Cooperativa de crédito/agência beneficiária não prevista',
			'45' => 'Nome do pagador inválido',
			'46' => 'Tipo/número de inscrição do pagador inválidos',
			'47' => 'Endereço do pagador não informado',
			'48' => 'CEP irregular',
			'49' => 'Número de Inscrição do pagador/avalista inválido',
			'50' => 'Pagador/avalista não informado',
			'63' => 'Entrada para título já cadastrado',
			'A1' => 'Praça do pagador não cadastrada.',
			'A2' => 'Tipo de cobrança do título divergente com a praça do pagador.',
			'A3' => 'Cooperativa/agência depositária divergente: atualiza o cadastro de praças da Coop./agência beneficiária',
			'B4' => 'Tipo de moeda inválido',
			'B5' => 'Tipo de desconto/juros inválido',
			'B6' => 'Mensagem padrão não cadastrada',
			'B7' => 'Seu número inválidoPercentual de multa inválido',
			'B8' => 'Percentual de multa inválido',
			'B9' => 'Valor ou percentual de juros inválido',
			'C6' => 'Título já liquidado',
			'D5' => 'Quantidade inválida no pedido de boletos pré-impressos da cobrança sem registro',
			'D7' => 'Cidade ou Estado do pagador não informado',
			'F6' => 'Nosso número / número da parcela fora de seqüência – total de parcelas inválido',
			'H7' => 'Espécie de documento necessita beneficiário ou avalista PJ',
			'H9' => 'Dados do título não conferem com disquete',
			'I1' => 'Pagador e Sacador Avalista são a mesma pessoa',
			'I2' => 'Aguardar um dia útil após o vencimento para protestar',
			'I3' => 'Data do vencimento rasurada',
			'I4' => 'Vencimento – extenso não confere com número',
			'I5' => 'Falta data de vencimento no título',
			'I6' => 'DM/DMI sem comprovante autenticado ou declaração',
			'I7' => 'Comprovante ilegível para conferência e microfilmagem',
			'I8' => 'Nome solicitado não confere com emitente ou pagador',
			'I9' => 'Confirmar se são 2 emitentes. Se sim, indicar os dados dos 2',
			'J1' => 'Endereço do pagador igual ao do pagador ou do portador',
			'J2' => 'Endereço do apresentante incompleto ou não informado',
			'J3' => 'Rua/número inexistente no endereço',
			'J4' => 'Falta endosso do favorecido para o apresentante',
			'J5' => 'Data da emissão rasurada',
			'J6' => 'Falta assinatura do pagador no título',
			'J7' => 'Nome do apresentante não informado/incompleto/incorreto',
			'J8' => 'Erro de preenchimento do titulo',
			'J9' => 'Título com direito de regresso vencido',
			'K1' => 'Título apresentado em duplicidade',
			'K2' => 'Título já protestado',
			'K3' => 'Letra de cambio vencida – falta aceite do pagador',
			'K4' => 'Falta declaração de saldo assinada no título',
			'K5' => 'Contrato de câmbio – Falta conta gráfica',
			'K6' => 'Ausência do documento físico',			
			'K7' => 'Pagador falecido',
			'K8' => 'Pagador apresentou quitação do título',			
			'K9' => 'Título de outra jurisdição territorial',			
			'L1' => 'Título com emissão anterior a concordata do pagador',			
			'L2' => 'Pagador consta na lista de falência',			
			'L3' => 'Apresentante não aceita publicação de edital',			
			'L4' => 'Dados do Pagador em Branco ou inválido',			
		),
		
		'6' => array(
			'A8' => 'Recebimento da liquidação fora da rede Sicredi - via compensação eletrônica',
			'H5' => 'Recebimento de liquidação fora da rede Sicredi – VLB Inferior – Via Compensação',
			'H6' => 'Recebimento de liquidação fora da rede Sicredi – VLB Superior – Via Compensação',
			'H8' => 'Recebimento de liquidação fora da rede Sicredi – Contingência Via Compe',
			'X1' => 'Regularização centralizadora – Rede Sicredi',
			'X2' => 'Regularização centralizadora – Compensação',
			'X3' => 'Regularização centralizadora – Banco correspondente',
			'X4' => 'Regularização centralizadora - VLB Inferior - via compensação',
			'X5' => 'Regularização centralizadora - VLB Superior - via compensação',
			'X0' => 'Pago com cheque',
			'X6' => 'Pago com cheque – bloqueado 24 horas',
			'X7' => 'Pago com cheque – bloqueado 48 horas',
			'X8' => 'Pago com cheque – bloqueado 72 horas',
			'X9' => 'Pago com cheque – bloqueado 96 horas',
			'XA' => 'Pago com cheque – bloqueado 120 horas',
			'XB' => 'Pago com cheque – bloqueado 144 horas',
		),
		'10' => array(
			'14' => 'Título protestado',
		),
		'17' => array(
			'A8' => 'Recebimento da liquidação fora da rede Sicredi - via compensação eletrônica',
			'H5' => 'Recebimento de liquidação fora da rede Sicredi – VLB Inferior – Via Compensação',
			'H6' => 'Recebimento de liquidação fora da rede Sicredi – VLB Superior – Via Compensação',
			'H8' => 'Espécie de documento necessita beneficiário ou avalista PJ',
		),
		'19' => array(
			'A' => 'Aceito',
			'D' => 'Desprezado',
		),
		'23' => array(
			'G2' => 'Título aceito: sem a assinatura do pagador',
			'G3' => 'Título aceito: rasurado ou rasgado',
			'G4' => 'Título aceito: falta título (cooperativa/ag. beneficiária deverá enviá-lo)',
			'G6' => 'Título aceito: sem endosso ou beneficiário irregular',
			'G7' => 'Título aceito: valor por extenso diferente do valor numérico',
		),
		'24' => array(
			'48' => 'CEP irregular',
		),
		'27' => array(
			'07' => 'Cooperativa/agência/conta/dígito inválidos',
			'08' => 'Nosso número inválido',
			'10' => 'Carteira inválida',
			'15' => 'Cooperativa/carteira/agência/conta/nosso número inválidos',
			'40' => 'Título com ordem de protesto emitida',
			'60' => 'Movimento para título não cadastrado',
			'A1' => 'Praça do pagador não cadastrada.',
			'C6' => 'Título já liquidado',
			'C7' => 'Título já baixado',
		),
		'28' => array(
			'03' => 'Código da ocorrência inválido',
			'04' => 'Código de ocorrência não permitida para a carteira',
			'08' => 'Nosso número inválido',
			'A9' => 'Tarifa de manutenção de título vencido',
			'B1' => 'Tarifa de baixa da carteira',
			'B3' => 'Tarifa de registro de entrada do título',
			'F5' => 'Tarifa de entrada na rede Sicredi',
		),
		'29' => array(
			'M2' => 'Não reconhecimento da dívida pelo pagador',
		),
		'30' => array(
			'01' => 'Código do banco inválido',
			'05' => 'Código de ocorrência não numérico',
			'08' => 'Nosso número inválido',
			'15' => 'Cooperativa/carteira/agência/conta/nosso número inválidos',
			'29' => 'Valor do desconto maior/igual ao valor do título',
			'33' => 'Valor do abatimento inválido',
			'34' => 'Valor do abatimento maior/igual ao valor do título',
			'38' => 'Prazo para protesto inválido',
			'39' => 'Pedido para protesto não permitido para o título',
			'40' => 'Título com ordem de protesto emitida',
			'60' => 'Movimento para título não cadastrado',
			'C6' => 'Título já liquidado',
			'C7' => 'Título já baixado',
		),
		'32' => array(
			'01' => 'Código do banco inválido',
			'02' => 'Código do registro detalhe inválido',
			'03' => 'Código da ocorrência inválido',
			'04' => 'Código de ocorrência não permitida para a carteira',
			'05' => 'Código de ocorrência não numérico',
			'07' => 'Cooperativa/agência/conta/dígito inválidos',
			'08' => 'Nosso número inválido',
			'10' => 'Carteira inválida',
			'15' => 'Cooperativa/carteira/agência/conta/nosso número inválidos',
			'16' => 'Data de vencimento inválida',
			'17' => 'Data de vencimento anterior à data de emissão',
			'21' => 'Espécie do título inválida',
			'22' => 'Espécie não permitida para a carteira',
			'24' => 'Data de emissão inválida',
			'29' => 'Valor do desconto maior/igual ao valor do título',
			'31' => 'Concessão de desconto - existe desconto anterior',
			'33' => 'Valor do abatimento inválido',
			'34' => 'Valor do abatimento maior/igual ao valor do título',
			'36' => 'Concessão de abatimento - existe abatimento anterior',
			'38' => 'Prazo para protesto inválido',
			'39' => 'Pedido para protesto não permitido para o título',
			'40' => 'Título com ordem de protesto emitida',
			'41' => 'Pedido cancelamento/sustação sem instrução de protesto',
			'45' => 'Nome do pagador inválido',
			'46' => 'Tipo/número de inscrição do pagador inválidos',
			'47' => 'Endereço do pagador não informado',
			'60' => 'Movimento para título não cadastrado',
			'A1' => 'Praça do pagador não cadastrada.',
			'A2' => 'Tipo de cobrança do título divergente com a praça do pagador.',
			'A4' => 'Beneficiário não cadastrado ou possui CGC/CIC inválido',
			'A5' => 'Pagador não cadastrado',
			'A6' => 'Data da instrução/ocorrência inválida',
			'B4' => 'Tipo de moeda inválido',
			'B5' => 'Tipo de desconto/juros inválido',
			'B6' => 'Mensagem padrão não cadastrada',
			'B7' => 'Seu número inválido',
			'B8' => 'Percentual de multa inválido',
			'B9' => 'Valor ou percentual de juros inválido',
			'C6' => 'Título já liquidado',
			'C7' => 'Título já baixado',
			'D2' => 'Espécie de documento não permite protesto de título',
			'F7' => 'Falta de comprovante de prestação de serviço',
			'F8' => 'Nome do beneficiário incompleto / incorreto.',
			'F9' => 'CNPJ / CPF incompatível com o nome do pagador / Sacador Avalista',
			'G1' => 'CNPJ / CPF do pagador Incompatível com a espécie',
			'G5' => 'Praça de pagamento incompatível com o endereço',
			'G8' => 'Saldo maior que o valor do título',
			'G9' => 'Tipo de endosso inválido',
			'H1' => 'Nome do pagador incompleto / Incorreto',
			'L3' => 'Apresentante não aceita publicação de edital',
			'L4' => 'Dados do Pagador em Branco ou inválido',
			'J8' => 'Erro de preenchimento do titulo',
		),
		'35' => array(
			'M1' => 'Reconhecimento da dívida pelo pagador',
		),
	);

	$motivo085 = array(
		'2' => array(
			'1' => 'Código do Banco Inválido',
			'2' => 'Código do Registro Detalhe Inválido',
			'3' => 'Código do Segmento Inválido',
			'4' => 'Código de Movimento Não Permitido para Carteira',
			'5' => 'Código de Movimento Inválido',
			'6' => 'Tipo/Número de Inscrição do Cedente Inválidos',
			'7' => 'Agência/Conta/DV Inválido',
			'8' => 'Nosso Número Inválido',
			'9' => 'Nosso Número Duplicado',
			'10' => 'Carteira Inválida',
			'11' => 'Forma de Cadastramento do Título Inválido',
			'12' => 'Tipo de Documento Inválido',
			'13' => 'Identificação da Emissão do Boleto Inválida',
			'14' => 'Identificação da Distribuição do Boleto Inválida',
			'15' => 'Características da Cobrança Incompatíveis',
			'16' => 'Data de Vencimento Inválida',
			'17' => 'Data de Vencimento Anterior a Data de Emissão',
			'18' => 'Vencimento Fora do Prazo de Operação',
			'19' => 'Título a Cargo de Bancos Correspondentes com Vencimento Inferior a XX Dias',
			'20' => 'Valor do Título Inválido',
			'21' => 'Espécie do Título Inválida',
			'22' => 'Espécie do Título Não Permitida para a Carteira',
			'23' => 'Aceite Inválido',
			'24' => 'Data da Emissão Inválida',
			'25' => 'Data da Emissão Posterior a Data de Entrada',
			'26' => 'Código de Juros de Mora Inválido',
			'27' => 'Valor/Taxa de Juros de Mora Inválido',
			'28' => 'Código do Desconto Inválido',
			'29' => 'Valor do Desconto Maior ou Igual ao Valor do Título',
			'30' => 'Desconto a Conceder Não Confere',
			'31' => 'Concessão de Desconto - Já Existe Desconto Anterior',
			'33' => 'Valor do Abatimento Inválido',
			'34' => 'Valor do Abatimento Maior ou Igual ao Valor do Título',
			'35' => 'Valor a Conceder Não Confere',
			'36' => 'Concessão de Abatimento - Já Existe Abatimento Anterior',
			'37' => 'Código para Protesto Inválido',
			'38' => 'Prazo para Protesto Inválido',
			'39' => 'Pedido de Protesto Não Permitido para o Título',
			'40' => 'Título com Ordem de Protesto Emitida',
			'41' => 'Pedido de Cancelamento/Sustação para Títulos sem Instrução de Protesto',
			'42' => 'Código para Baixa/Devolução Inválido',
			'43' => 'Prazo para Baixa/Devolução Inválido',
			'44' => 'Código da Moeda Inválido',
			'45' => 'Nome do Sacado Não Informado',
			'46' => 'Tipo/Número de Inscrição do Sacado Inválidos',
			'47' => 'Endereço do Sacado Não Informado',
			'48' => 'CEP Inválido',
			'49' => 'CEP Sem Praça de Cobrança (Não Localizado)',
			'50' => 'CEP Referente a um Banco Correspondente',
			'51' => 'CEP incompatível com a Unidade da Federação',
			'52' => 'Unidade da Federação Inválida',
			'53' => 'Tipo/Número de Inscrição do Sacador/Avalista Inválidos',
			'54' => 'Sacador/Avalista Não Informado',
			'55' => 'Nosso número no Banco Correspondente Não Informado',
			'56' => 'Código do Banco Correspondente Não Informado',
			'57' => 'Código da Multa Inválido',
			'58' => 'Data da Multa Inválida',
			'59' => 'Valor/Percentual da Multa Inválido',
			'60' => 'Movimento para Título Não Cadastrado',
			'61' => 'Alteração da Agência Cobradora/DV Inválida',
			'62' => 'Tipo de Impressão Inválido',
			'63' => 'Entrada para Título já Cadastrado',
			'64' => 'Número da Linha Inválido',
			'65' => 'Código do Banco para Débito Inválido',
			'66' => 'Agência/Conta/DV para Débito Inválido',
			'79' => 'Data Juros de Mora Inválido',
			'80' => 'Data do Desconto Inválida',
			'86' => 'Seu Número Inválido',
			'A4' => 'Sacado DDA',
			'A5' => 'Registro Rejeitado – Título já Liquidado',
			'A7' => 'Título já se encontra na situação Pretendida',
			'A8' => 'Título pendente na CIP (Existe uma instrução pendente de processamento na CIP)',
			'A9' => 'Título pendente na CIP (título é DDA e não possui número de identificação na CIP)',
			'P1' => 'Enviado Cooperativa Emite e Expede',
			'R1' => 'Registro Online',
			'A4R1' => 'Sacado DDA/Registro online',
		),
		'28' => array(
			'01' => 'Tarifa de Extrato de Posição',
			'02' => 'Tarifa de Manutenção de Título Vencido',
			'03' => 'Tarifa de Sustação',
			'04' => 'Tarifa de Protesto',
			'05' => 'Tarifa de Outras Instruções',
			'06' => 'Tarifa de Outras Ocorrências',
			'07' => 'Tarifa de Envio de Duplicata ao Sacado',
			'08' => 'Custas de Protesto',
			'09' => 'Custas de Sustação de Protesto',
			'10' => 'Custas de Cartório Distribuidor',
			'11' => 'Custas de Edital',
			'12' => 'Tarifa Sobre Devolução de Título Vencido',
			'13' => 'Tarifa Sobre Registro Cobrada na Baixa/Liquidação',
			'14' => 'Tarifa Sobre Reapresentação Automática',
			'15' => 'Tarifa Sobre Rateio de Crédito',
			'16' => 'Tarifa Sobre Informações Via Fax',
			'17' => 'Tarifa Sobre Prorrogação de Vencimento',
			'18' => 'Tarifa Sobre Alteração de Abatimento/Desconto',
			'19' => 'Tarifa Sobre Arquivo mensal (Em Ser)',
			'20' => 'Tarifa Sobre Emissão de Bloqueto Pré-Emitido pelo Banco',
			'H1' => 'Instrução Protesto confirmada',
			'H2' => 'Inclusão de instrução de Protesto automática',
			'E1' => 'Envio de cancelamento',
			'C2' => 'Cancelamento de instrução automática',
		),
		'06' => array(
			'01' => 'Por Saldo',
			'02' => 'Por Conta',
			'03' => 'Liquidação no Guichê de Caixa em Dinheiro',
			'04' => 'Compensação Eletrônica',
			'05' => 'Compensação Convencional',
			'06' => 'Por Meio Eletrônico',
			'07' => 'Após Feriado Local',
			'08' => 'Em Cartório',
			'30' => 'Liquidação no Guichê de Caixa em Cheque',
			'31' => 'Liquidação em banco correspondente',
			'32' => 'Liquidação Terminal de Auto-Atendimento',
			'33' => 'Liquidação na Internet (Home banking)',
			'34' => 'Liquidado Office Banking',
			'35' => 'Liquidado Correspondente em Dinheiro',
			'36' => 'Liquidado Correspondente em Cheque',
			'37' => 'Liquidado por meio de Central de Atendimento (Telefone)',
		),
		'09' => array(
			'01' => 'Por Saldo',
			'02' => 'Por Conta',
			'03' => 'Liquidação no Guichê de Caixa em Dinheiro',
			'04' => 'Compensação Eletrônica',
			'05' => 'Compensação Convencional',
			'06' => 'Por Meio Eletrônico',
			'07' => 'Após Feriado Local',
			'08' => 'Em Cartório',
			'30' => 'Liquidação no Guichê de Caixa em Cheque',
			'31' => 'Liquidação em banco correspondente',
			'32' => 'Liquidação Terminal de Auto-Atendimento',
			'33' => 'Liquidação na Internet (Home banking)',
			'34' => 'Liquidado Office Banking',
			'35' => 'Liquidado Correspondente em Dinheiro',
			'36' => 'Liquidado Correspondente em Cheque',
			'37' => 'Liquidado por meio de Central de Atendimento (Telefone)',
		),
		'17' => array(
			'01' => 'Por Saldo',
			'02' => 'Por Conta',
			'03' => 'Liquidação no Guichê de Caixa em Dinheiro',
			'04' => 'Compensação Eletrônica',
			'05' => 'Compensação Convencional',
			'06' => 'Por Meio Eletrônico',
			'07' => 'Após Feriado Local',
			'08' => 'Em Cartório',
			'30' => 'Liquidação no Guichê de Caixa em Cheque',
			'31' => 'Liquidação em banco correspondente',
			'32' => 'Liquidação Terminal de Auto-Atendimento',
			'33' => 'Liquidação na Internet (Home banking)',
			'34' => 'Liquidado Office Banking',
			'35' => 'Liquidado Correspondente em Dinheiro',
			'36' => 'Liquidado Correspondente em Cheque',
			'37' => 'Liquidado por meio de Central de Atendimento (Telefone)',
		),
	);

	

	$statusOc = $array['todosCampos']['T038_Codigo_Natureza_Recebimento'];
	$T002_Id = $array['todosCampos']['T038_T002_Id'];
	$T002_Valor_Total = $array['todosCampos']['T002_Valor_Total'];
	$valorRecebido = (float) $array['todosCampos']['T038_Valor_Recebido'];
	$outrasRecebimentos = $array['todosCampos']['T038_Valor_Outros_Recebimentos'];
	$outrasDespesas = (float) $array['todosCampos']['T038_Valor_Outras_Despesas'];
	$valorIOF = $array['todosCampos']['T038_Valor_Taxa_IOF'];
	$valorJuros = $array['todosCampos']['T038_Valor_Juros_Mora'];
	$valorAbatimento = $array['todosCampos']['T038_Valor_Abatimento'];
	$valorDesconto = $array['todosCampos']['T038_Valor_Desconto'];
	$dataRecebimento = $array['todosCampos']['T002_Data_Recebimento'];	
	$dataCredito = $array['todosCampos']['T038_Data_Credito'];
	$codigoRejeicao = $array['todosCampos']['T038_Codigo_Motivo'];
	$valorCalculado = $T002_Valor_Total + $valorJuros + $outrasRecebimentos + $valorIOF - $outrasDespesas;
	$codigoBanco = trim($array['todosCampos']['T037_Codigo_Banco']);
	$infoComplementar_1 = $array['todosCampos']['T038_Info_Complementar_1'];
	$infoComplementar_2 = $array['todosCampos']['T038_Info_Complementar_2'];
	$infoComplementar_3 = $array['todosCampos']['T038_Info_Complementar_3'];

	$valorRecebido = (string) $valorRecebido;
	$valorCalculado = (string) $valorCalculado;
	if($codigoBanco == '237') {
		if($dataRecebimento != '0000-00-00' && $T002_Id != '0'){
			return 'Baixado';
		} else if($statusOc == '06' || $statusOc == '15' || ($statusOc == '17' && $dataCredito != '0000-00-00')){
			if($valorRecebido == $valorCalculado){
				return 'Será Processado';
			} else if($T002_Id == '0'){
				return 'Titulo não Encontrado';
			} else if($valorRecebido != $valorCalculado){
				return 'Divergência de Valor';			
			} else if($valorAbatimento > 0 || $valorDesconto > 0){
				return 'Verificar Desconto/Abatimento';
			}
		} else if($statusOc != '06' && $statusOc != '15') {
			if($T002_Id == '0'){
				return 'Titulo não Encontrado';
			} else {
				return 'Não será processado';
			}
		}
	} else if($codigoBanco == '085') {
		if($dataRecebimento != '0000-00-00' && $T002_Id != '0'){
			return 'Baixado';
		} else if($statusOc == '6' && $valorRecebido == $valorCalculado){
			return 'Será Processado';
		}else if($valorRecebido != $valorCalculado && $statusOc == '6'){
			return 'Divergência de Valor';
		}else if($T002_Id == '0'){
			return 'Titulo não Encontrado';
		} else if($valorAbatimento > 0 || $valorDesconto > 0){
			return 'Verificar Desconto/Abatimento';
		} else {
			return $motivo085[$codigoRejeicao];
		}
	}else if($codigoBanco == '399'){
		$ocorrenciasAceitas = array('06','07','15','16','31','32','33','36','38','39');
		if($statusOc == '3'){
			return $motivo399[$codigoRejeicao];
		} else if($dataRecebimento != '0000-00-00' && $T002_Id != '0'){
			return 'Baixado';
		} else if(in_array($statusOc, $ocorrenciasAceitas)){
			if($valorRecebido == $valorCalculado){
				return 'Será Processado';
			} else if($T002_Id == '0'){
				return 'Titulo não Encontrado';
			} else if($valorRecebido != $valorCalculado){				
				return 'Divergência de Valor';			
			} else if($valorAbatimento > 0 || $valorDesconto > 0){
				return 'Verificar Desconto/Abatimento';
			}
		} else if(!in_array($statusOc, $ocorrenciasAceitas)) {
			if($T002_Id == '0'){
				return 'Titulo não Encontrado';
			} else {
				return 'Não será processado';
			}
		}		
	} else if($codigoBanco == '001'){
		if($dataRecebimento != '0000-00-00' && $T002_Id != '0'){
			return 'Baixado';
		} else if(($statusOc == '5' || $statusOc == '6' || $statusOc == '15') && $T002_Id != '0'){
			return 'Será Processado';
		} else if($T002_Id == '0'){
			return 'Titulo não Encontrado';
		} else if($valorRecebido != $valorCalculado && $valorRecebido > 0){
			return 'Divergência de Valor';			
		} else if($valorAbatimento > 0 || $valorDesconto > 0){
			return 'Verificar Desconto/Abatimento';
		}
	} else if($codigoBanco == '341'){
		$valorCalculado = $T002_Valor_Total;
		if($dataRecebimento != '0000-00-00' && $T002_Id != '0'){
			return 'Baixado';
		} else if(($statusOc == '6' || $statusOc == '8') && $valorRecebido == $valorCalculado){
			return 'Será Processado';
		} else if($T002_Id == '0'){
			return 'Titulo não Encontrado';
		} else if($valorRecebido != $valorCalculado && ($statusOc == '6' || $statusOc == '8')){
			return 'Divergência de Valor';
		} else if($valorAbatimento > 0 || $valorDesconto > 0){
			return 'Verificar Desconto/Abatimento';
		} else {
			//Banco itau possui até 4 códigos de rejeição por ocorrência
			$retorno = 'Não será processado';
			$motivo341 = $motivo341[$statusOc];
			$ocorrenciasComuns = array('2','3','15','16','17','18', '69');
			if(in_array($statusOc, $ocorrenciasComuns)){
				preg_match_all('#[0-9]{2}#', $codigoRejeicao, $arrayCodigoRejeicao);
				if(count($arrayCodigoRejeicao[0]) > 0)  { $retorno = ''; }
				foreach($arrayCodigoRejeicao[0] as $codigoRejeicao){
					$retorno .= $motivo341[$codigoRejeicao] . '<br />';
				}
			} else {
				//Ocorrencias que possuem datas na informação complementar
				$arrayInfoComplementarData = array('1461', '1479', '1487', '1495', '1313');
				//Ocorrencias que possuem valor na informação complementar
				$arrayInfoComplementarValor = array('1503');
				$retorno = $motivo341[$infoComplementar_1];
				if($statusOc == '25' && in_array($infoComplementar_1, $arrayInfoComplementarData)){
					$retorno .= ' ' . converteData($infoComplementar_2);
				} else if($statusOc == '25' && in_array($infoComplementar_1, $arrayInfoComplementarValor)){
					$retorno .= ' ' . converteNumero($infoComplementar_3);
				}
			}
			return $retorno;
		}
	} else if($codigoBanco == '748'){
		if($dataRecebimento != '0000-00-00' && $T002_Id != '0'){
			return 'Baixado';
		} else if($statusOc == '6' && $valorRecebido == $valorCalculado){
			return 'Será Processado';
		}else if($valorRecebido != $valorCalculado && $statusOc == '6'){
			return 'Divergência de Valor';
		}else if($T002_Id == '0'){
			return 'Titulo não Encontrado';
		} else if($valorAbatimento > 0 || $valorDesconto > 0){
			return 'Verificar Desconto/Abatimento';
		} else {
			//Banco itau possui até 4 códigos de rejeição por ocorrência
			$retorno = 'Não será processado';
			$motivo748 = $motivo748[$statusOc];
			$ocorrenciasComuns = array('2','3','10', '15','16','17','18', '19', '23', '24', '27', '28', '29', '30', '32', '35');
			if(in_array($statusOc, $ocorrenciasComuns)){
				preg_match_all('#[A-Za-z0-9-]{2}#', $codigoRejeicao, $arrayCodigoRejeicao);				
				if(count($arrayCodigoRejeicao[0]) > 0) { 
					$retorno = ''; 
				}
				foreach($arrayCodigoRejeicao[0] as $codigoRejeicao){
					if (!empty($retorno) && !empty($motivo748[$codigoRejeicao])) {
						$retorno .= '<br />';
					}
					$retorno .= $motivo748[$codigoRejeicao];
				}
			}
			return $retorno;
		}
	}
}

function converteData($data){ //Recebe uma data DDMMAA e converte para data formato banco
	if($data > 0){
		$dia = substr($data, 0, 2);
		$mes = substr($data, 2, 2);
		$ano = substr($data, 4, 2);
		return gCorrigeData(date("Y-m-d", mktime(0, 0, 0, $mes, $dia, $ano)));
	} else {
		return "0000-00-00";
	}
}

function converteNumero($numero){ //Recebe um numero inteiro e converte os 2 ultimos para decimais
	if(isset($numero)) {
		$decimal = substr($numero, -2);
		$numero = substr($numero, 0, -2);			
		return gCorrigeNumero($numero . '.' . $decimal);
	} else {
		return 0;
	}
}

/*
	$motivos = array(
		//'ocorrência' => ('codigoMotivo' => 'descriçãoMotivo')
		'02' => array(
			'00' => 'Ocorrência Aceita',
			'01' => 'Código do Banco Inválido',
			'04' => 'Cód. do Mov. não permitido para a carteira',
			'15' => 'Características da cobrança incompatíveis',
			'17' => 'Data de vencimento anterior a data de emissão',
			'21' => 'Espécie do Titulo Inválido',
			'24' => 'Data de Emissão Inválida',
			'27' => 'Taxa de Juros mora inválido',
			'38' => 'Prazo para protesto inválido',
			'39' => 'Pedido para protesto não permitido para titulo',
			'43' => 'Prazo para baixa e devolução inválido',
			'45' => 'Nome do sacado inválido',
			'46' => 'Tipo/Num. de inscrição do sacado inválidos',
			'47' => 'Endereço do sacado não informado',
			'48' => 'Cep Inválido',
			'50' => 'Cep referente a Banco correspondente',
			'53' => 'N° de inscrição do Sacador/avalista inválidos',
			'54' => 'Sacador/avalista não informado',
			'67' => 'Débito automatico agendado',
			'68' => 'Débito não agendado - erro nos dados de remessa',
			'69' => 'Débito não agendado - Sacado não consta no cadastro de autorizante',
			'70' => 'Débito não agendado - Cedente não autorizado pelo sacado',
			'71' => 'Débito não agendado - Cedente não part. da modalidade de déb. automatico',
			'72' => 'Débito não agendado - Código de moeda diferente de R$',
			'73' => 'Débito não agendado - Data de vencimento inválida/vencida',
			'75' => 'Débito não agendado - Tipo do núm. de inscr. do sacado debitado inválido',
			'76' => 'Sacado Eletrônico DDA',
			'86' => 'Seu número do documento inválido',
			'89' => 'Email do sacado não enviado - Titulo com débito automatico',
			'90' => 'Email do sacado não enviado - Titulo de cobrança sem registro'
		),
		
		'03' => array(
			'02' => 'Código do registro detalhe inválido',
			'03' => 'Código da Ocorrência inválida',
			'04' => 'Código da Ocorrência não permitida para a carteira',
			'05' => 'Código de Ocorrência não numérico',
			'07' => 'Agência/Conta/Digito inválido',
			'08' => 'Nosso numero inválido',
			'09' => 'Nosso numero duplicado',
			'10' => 'Carteira Inválida',
			'13' => 'Identificação da emissão do bloqueto inválida',
			'16' => 'Data de vencimento inválida',
			'18' => 'Vencimento fora do prazo de operação',
			'20' => 'Valor do titulo inválido',
			'21' => 'Espécie do titulo inválida',
			'22' => 'Espécie não permitida para a carteira',
			'24' => 'Data de emissão inválida',
			'28' => 'Código de desconto inválido',
			'38' => 'Prazo para protesto inválido',
			'44' => 'Agencia Cedente não prevista',
			'45' => 'Nome do sacado não informado',
			'46' => 'Tipo/núm. de inscrição do sacado inválidos',
			'47' => 'Endereço do sacado não informado',
			'48' => 'Cep Inválido',
			'50' => 'Cep Irregular - Banco correspondente',
			'63' => 'Entrada de titulo ja cadastrado',
			'65' => 'Limite excedido',
			'66' => 'Número autorização inexistente',
			'68' => 'Débito não agendado - erro nos dados de remessa',
			'69' => 'Débito não agendado - Sacado não consta no cadastro de autorizante',
			'70' => 'Débito não agendado - Cedente não autorizado pelo sacado',
			'71' => 'Débito não agendado - Cedente não participa do débito automatico',
			'72' => 'Débito não agendado - Código de moeda diferente de R$',
			'73' => 'Débito não agendado - Data de vencimento inválida',
			'75' => 'Débito não agendado - Tipo do núm. de inscrição do debitado inválido',
		),
		
		'06' => array(
			'00' => 'Titulo pago com dinheiro',
			'15' => 'Titulo pago com cheque',
			'42' => 'Rateio não efetuado, cod. Calculo 2'
		),

		'09' => array(
			'00' => 'Ocorrência aceita',
			'10' => 'Baixa comandada pelo cliente'			
		),

		'10' => array(
			'00' => 'Baixado conforme instruções da agência',
			'14' => 'Titulo protestado',
			'15' => 'Titulo excluído',
			'16' => 'Titulo baixado pelo banco por decurso prazo',
			'17' => 'Titulo baixado transferido carteira',
			'20' => 'Titulo baixado e transferido para desconto'
		),

		'15' => array(
			'00' => 'Titulo pago com dinheiro',
			'15' => 'Titulo pago com cheque'
		),

		'17' => array(
			'00' => 'Titulo pago com dinheiro',
			'15' => 'Titulo pago com cheque'
		),

		'24' => array(
			'48' => 'Cep inválido'
		),

		'27' => array(
			'04' => 'Código de ocorrência não permitido para a carteira',
			'07' => 'Agência/Conta/Digito inválidos',
			'08' => 'Nosso número inválido',
			'10' => 'Carteira inválida',
			'15' => 'Carteira/Agência/Conta/Nosso número inválidos',
			'40' => 'Titulo com ordem de protesto emitido',
			'42' => 'Código para baixa/devolução via Telebradesco inválido',
			'60' => 'Movimento para Título não cadastrado',
			'77' => 'Transferência para desconto não permitido para a carteira',
			'85' => 'Titulo com pagamento vinculado'
		),

		'28' => array(
			'02' => 'Tarifa de permanência título cadastrado',
			'03' => 'Tarifa de sustentação',
			'04' => 'Tarifa de protesto',
			'05' => 'Tarifa de outras instruções',
			'06' => 'Tarifa de outras ocorrências',
			'08' => 'Custas de protesto',
			'12' => 'Tarifa de registro',
			'13' => 'Tarifa título pago no Bradesco',
			'14' => 'Tarifa título pago compensação',
			'15' => 'Tarifa título baixado não pago',
			'16' => 'Tarifa alteração de vencimento',
			'17' => 'Tarifa concessão abatimento',
			'18' => 'Tarifa cancelamento de abatimento',
			'19' => 'Tarifa concessão desconto',
			'20' => 'Tarifa cancelamento de desconto',
			'21' => 'Tarifa título pago cics',
			'22' => 'Tarifa título pago internet',
			'23' => 'Tarifa título pago term. gerencial serviços',
			'24' => 'Tarifa título pago Pág-Contas',
			'25' => 'Tarifa título pago Fone Fácil',
			'26' => 'Tarifa título Déb. Postagem',
			'27' => 'Tarifa impressão de títulos pendentes',
			'28' => 'Tarifa título pago BND',
			'29' => 'Tarifa título pago Term. Multi função',
			'30' => 'Impressão de títulos baixados',
			'31' => 'Impressão de títulos pagos',
			'32' => 'Tarifa título pago Pagfor',
			'33' => 'Tarifa reg/pgto - guichê caixa',
			'34' => 'Tarifa título pago retaguarda',
			'35' => 'Tarifa título pago Subcentro',
			'36' => 'Tarifa título pago Cartão de Crédito',
			'37' => 'Tarifa título pago Comp. Eletrônica',
			'38' => 'Tarifa título Baix. Pg. Cartório',
			'39' => 'Tarifa título baixado acerto BCO',
			'40' => 'Baixa registro em duplicidade',
			'41' => 'Tarifa título baixado decurso prazo',
			'42' => 'Tarifa título baixado Judicialmente',
			'43' => 'Tarifa título baixado via remessa',
			'44' => 'Tarifa título baixado rastreamento',
			'45' => 'Tarifa título baixado conf. Pedido',
			'46' => 'Tarifa título baixado protestado',
			'47' => 'Tarifa título baixado p/ devolução',
			'48' => 'Tarifa título baixado franco pagto',			
			'49' => 'Tarifa título baixado SUST/RET/CARTÓRIO',
			'50' => 'Tarifa título baixado SUS/SEM/REM/CARTÓRIO',
			'51' => 'Tarifa título transferido desconto',
			'52' => 'Cobrado baixa manual',	
			'53' => 'Baixa por acerto cliente',
			'54' => 'Tarifa baixa por contabilidade',
			'55' => 'Tr. tentativa cons deb aut',
			'56' => 'Tr. credito online',
			'57' => 'Tarifa reg/pagto Bradesco Expresso',
			'58' => 'Tarifa emissão Papeleta',
			'59' => 'Tarifa fornec papeleta semi preenchida',
			'60' => 'Acondicionador de papeletas (RPB)S',
			'61' => 'Acond. de papeletas (RPB)s Personal',
			'62' => 'Papeleta formulário branco',
			'63' => 'Formulário A4 serrilhado',
			'64' => 'Fornecimento de softwares de transmiss',
			'65' => 'Fornecimento de softwares consulta',
			'66' => 'Fornecimento Micro Completo',
			'67' => 'Fornecimento MODEN',
			'68' => 'Fornecimento de máquina de Fax',
			'69' => 'Fornecimento de máquinas óticas',
			'70' => 'Fornecimento de impressoras',
			'71' => 'Reativação de título',
			'72' => 'Alteração de produto negociado',
			'73' => 'Tarifa emissão de contra recibo',
			'74' => 'Tarifa emissão 2° via papeleta',
			'75' => 'Tarifa regravação arquivo retorno',
			'76' => 'Arq. Títulos a vencer mensal',
			'77' => 'Listagem auxiliar de crédito',
			'78' => 'Tarifa cadastro cartela instrução permanente',
			'79' => 'Canalização de crédito',
			'80' => 'Cadastro de Mensagem Fixa',
			'81' => 'Tarifa reapresentação automática título',
			'82' => 'Tarifa registro título déb. automático',
			'83' => 'Tarifa Rateio de Crédito',
			'84' => 'Emissão papeleta sem valor',
			'85' => 'Sem uso',
			'86' => 'Cadastro de reembolso de diferença',
			'87' => 'Relatório fluxo de pagamento',
			'88' => 'Emissão extrado mov. carteira',
			'89' => 'Mensagem campo local de pagto',
			'90' => 'Cadastro concessionária serv. publ.',
			'91' => 'Classif. Extrato conta corrente',
			'92' => 'Contabilidade especial',
			'93' => 'Realimentação pagamento',
			'94' => 'Repasse de créditos',
			'95' => 'Tarifa reg. pagto Banco Postal',
			'96' => 'Tarifa reg. pagto outras mídias',
			'97' => 'Tarifa Reg/Pagto - Net Empresa',
			'98' => 'Tarifa de título pago vencido',
			'99' => 'TR Tít. Baixado por decurso prazo',	
			'100' => 'Arquivo retorno antecipado',
			'101' => 'Arquivo retorno hora/hora',
			'102' => 'TR. Agendamento Déb Aut',
			'105' => 'TR. Agendamento rat. Crédito',
			'106' => 'TR. Emissão aviso rateio',
			'107' => 'Extrato de protesto'
		),

		'29' = array(
			'78' => 'Sacado alega que faturamento é indevido',
			'95' => 'Sacado aceita/reconhece o faturamento'
		),

		'30' = array(
			'01' => 'Código do Banco Inválido',
			'04' => 'Código de Ocorr. não permitido para a carteira',
			'05' => 'Código da Ocorr. não numérico',
			'08' => 'Nosso número inválido',
			'15' => 'Característica da cobrança incompatível',
			'16' => 'Data de vencimento inválido',
			'17' => 'Data de vencimento anterior a data de emissão',
			'18' => 'Vencimento fora do prazo de operação',
			'24' => 'Data de emissão inválida',
			'26' => 'Código de juros de mora inválido',
			'27' => 'Valor/taxa de juros de mora inválido',
			'28' => 'Código de desconto inválido',
			'29' => 'Valor de desconto maior/igual ao valor do Título',
			'30' => 'Desconto a conceder não confere',
			'31' => 'Concessão de desconto já existente',
			'32' => 'Valor do IOF inválido',
			'33' => 'Valor do abatimento inválido',
			'34' => 'Valor do abatimento maior/igual ao valor do Título',
			'38' => 'Prazo para protesto inválido',
			'39' => 'Pedido de protesto não permitido para o Título',
			'40' => 'Título com ordem de protesto emitido',
			'42' => 'Código para baixa/devolução inválido',
			'46' => 'Tipo/número de inscrição do sacado inválidos',
			'48' => 'Cep inválido',
			'53' => 'Tipo/número de inscrição do sacador/avalista inválidos',
			'54' => 'Sacador/avalista não informado',
			'57' => 'Código da multa inválido',
			'58' => 'Data da multa inválida',
			'60' => 'Movimento para título não cadastrado',
			'79' => 'Data de Juros da mora inválida',
			'80' => 'Data do desconto inválida',
			'85' => 'Título com pagamento vinculado',
			'88' => 'Email Sacado não lido no prazo 5 dias',
			'91' => 'Email Sacado não recebido'
		),

		'30' = array(
			'01' => 'Código do Banco inválido',
			'02' => 'Código do registro detalhe inválido',
			'04' => 'Código de ocorrência não permitido para a carteira',
			'05' => 'Código de ocorrência não numérico',
			'07' => 'Agência/Conta/Digito inválidos',
			'08' => 'Nosso número inválido',
			'10' => 'Carteira inválida',
			'15' => 'Características da cobrança incompatíveis',
			'16' => 'Data de vencimento inválida',
			'17' => 'Data de vencimento anterior a data de emissão',
			'18' => 'Vencimento fora do prazo de operação',
			'20' => 'Valor do título inválido',
			'21' => 'Espécie do Título Inválida',
			'22' => 'Espécie não permitida para a carteira',
			'24' => 'Data de emissão inválida',
			'28' => 'Código de desconto via Telebradesco inválido',
			'29' => 'Valor de desconto maior/igual ao valor do Título',
			'30' => 'Desconto a conceder não confere',
			'31' => 'Concessão de desconto - Ja existe desconto anterior',
			'33' => 'Valor do abatimento inválido',
			'34' => 'Valor do abatimento maior/igual ao valor do Título',
			'36' => 'Concessão abatimento - Já existe abatimento anterior',
			'38' => 'Prazo para protesto inválido',
			'39' => 'Pedido de protesto não permitido para o Título',
			'40' => 'Título com ordem de protesto emitido',
			'41' => 'Pedido cancelament/sustentação para título sem instrução de protesto',
			'42' => 'Código para baixa/devolução invalido',
			'45' => 'Nome do sacado não informado',
			'46' => 'Tipo/número de inscrição do sacado inválidos',
			'47' => 'Endereço do sacado não informado',
			'48' => 'Cep inválido',
			'50' => 'Cep referente a um banco correspondente',
			'53' => 'Tipo de inscrição do sacador avalista inválidos',
			'60' => 'Movimento para título não cadastrado',
			'85' => 'Título com pagamento vinculado',
			'86' => 'Seu número inválido',
			'94' => 'Título penhorado - Instrução não liberada pela Agência'
		),

		'35' = array(
			'81' => 'Tentativas esgotadas, baixado',
			'82' => 'Tentativas esgotadas, pendente',
			'83' => 'Cancelado pelo sacado e mantido pendente, conforme negociação',
			'84' => 'Cancelado pelo sacado e baixado, conforme neogociação'
		),
	);

	//399
	'030' => Número de parcela “Até” não informado',
	'031' => Quantidade parcelas inválida',
	'032' => Quantidade parcelas não informada',
	'033' => Parcela “De” maior que parcela “Até”',
	'034' => Data primeiro vencimento inválida',
	'035' => Data primeiro vencimento anterior a data de hoje',
	'036' => Data primeiro vencimento já informada no Capa',
	'037' => Data vencimento parcela única inválida',
	'038' => Data vencimento parcela única anterior a data de hoje',
	'039' => Data vencimento parcela única já informado no Capa',
	'040' => Tipo de moeda inválido',
	'042' => Valor da parcela inválido',
	'043' => Valor da parcela não informado',
	'044' => Valor da parcela já informado no Capa',
	'045' => Valor total das parcelas inválido',
	'046' => Valor total das parcelas não informado',
	'047' => Valor da parcela única inválido',
	'048' => Valor da parcela única não informado',
	'049' => Valor da parcela única já informado no Capa',
	'050' => Valor total da parcela única inválido',
	'051' => Valor total da parcela única não informado',
	'058' => Código do documento inválido',
	'059' => Código do documento não numérico',
	'060' => Código do documento não informado',
	'070' => BDU do cedente zerado',
	'073' => BDU do cedente já está encerrado',
	'075' => Cedente está cancelado',
	'076' => Código do formulário cancelado ou zerado',
	'080' => Tipo de moeda deve ser igual em todos os detalhes do lote',
	'081' => Se o tipo de moeda foi informado, é obrigatório informar o “Valor Parc”.e o “Total Valor da Parc.”',
	'082' => Se um dos valores foi informado, é obrigatório informar o tipo da moeda',
	'083' => O campo “Qt. Parc.” Não pode ser superior ao resultado do cálculo (Parc. Até – Parc. De) + 1.',
	'084' => A quantidade de parcelas não pode ser superior a 60',
	'085' => O preenchimento da “Qt Parc”. é obrigatório e tem que ser maior que zero',
	'087' => A quantidade total de parcelas a emitir (Qt. Parc. X Qt. Carnês) ultrapassou 60.000.',
	'089' => Código material disponível somente para emissão empresa',
	'090' => Tipo de moeda inválido',
	'091' => Uso de parcela única indevido para quantidade parcela 1',
	'096' => Para bloqueto auto-envelopado, obrigatório informar o nome do sacado',
	'097' => Para bloqueto auto-envelopado, obrigatório informar a rua do sacado',
	'098' => Para bloqueto auto-envelopado, obrigatório informar o número da residência do sacado.',
	'099' => Para bloqueto auto envelopado, obrigatório informar a cidade do sacado.',
	'100' => Para bloqueto auto-envelopado, obrigatório informar o Estado do sacado.',
	'112' => CEP do sacado inválido',
	'114' => Arquivo recusado por duplicidade',
	'115' => Arquivo vazio',
	'116' => Arquivo sem registros anexos',
	'117' => Seqüência de registros inválida',
	'118' => Código de registro diferente de zero',
	'119' => Código de remessa diferente de um (1)',
	'120' => Literal remessa diferente de (Remessa)',
	'121' => Código de serviço diferente de um (1)',
	'122' => Literal Cobrança diferente de (Cobrança)',
	'123' => Data de gravação não numérica',
	'124' => Densidade de gravação diferente de 1600 e 6250',
	'125' => Literal densidade diferente de (BPI)',
	'126' => Hora de gravação não numérica',
	'127' => Número seqüencial não numérico',
	'128' => Número seqüência fora de seqüência',
	'129' => Código do banco diferente de “399”',
	'130' => Lote de serviços diferente de zeros',
	'131' => Lote de serviço Header fora da seqüência',
	'132' => Código Layout diferente de 020',
	'133' => Tipo de registro Header inválido',
	'134' => Tipo de operação diferente de R',
	'135' => Tipo de serviço diferente de 02',
	'136' => Forma de lançamento diferente de 00 (zeros)',
	'137' => Versão de lote diferente de 010 (dez)',
	'138' => Número de remessa não numérico',
	'139' => Tipo de montagem só pode ser  0 (zero) ou 1 (um)',
	'140' => Quantidade Lotes diferente do informado no arquivo',
	'141' => Quantidade de registro trailer Arq. Diferente do informado',
	'142' => Arquivo sem o registro Header',
	'143' => Arquivo sem o registro Trailer',
	'144' => Arquivo sem o registro Header e Trailer',
	'145' => Formulário incompatível com código de postagem',
	'151' => Cedente sem cadastro de postagem no HSBC',
*/