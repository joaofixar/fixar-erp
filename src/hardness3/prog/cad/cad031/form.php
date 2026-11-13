<?php
namespace hardness;
require_once('prog/cad/cad031/form_func-js.php');    
require_once("bibliotecas/classes/CAD031.php");
$CAD031 = new CAD031();   

switch ($g['r'][3]) {
	/**
	* @author AndreySliwinski
	* @since 28/11/2011  
	*/
	case 'cad031FormCadastroEmpresas':
		global $g;
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;

		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;

		/**
		 * PARTE 2: Definição do formulário
		 */
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = true;
		if ($formMultiTab) {
			$formId = $r_formId;
		} else {
			$formId = uniqid();
		}

		$whereD020 = "WHERE D020_D018_Id = '0'";
		if(!empty($acaoId)) {
			$C004 = mysql_fetch_array(mysql_query("SELECT C004_D018_Id FROM C004 WHERE C004_Id = '{$acaoId}'"));
			if($C004['C004_D018_Id'] > 0){
				$whereD020 = "WHERE D020_D018_Id = '{$C004['C004_D018_Id']}'";
			}
		}		

		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'C004';
		$form_campo_pk = 'C004_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad031FormCadastroEmpresasEnviar';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad031/form/cad031FormCadastroEmpresas/';
		// Editor
		$form_editor = array(
			'geral' => '{"C004_Flag_Ativo":{"top":421,"left":45.453125,"width":144,"hidden":0},"C004_Nome_Empresa":{"top":7,"left":39,"width":472,"hidden":0},"C004_Nome_Abreviado":{"top":30,"left":34,"width":472,"hidden":0},"C004_D018_Id":{"top":53,"left":100,"width":223,"hidden":0},"C004_D020_Id":{"top":76,"left":81,"width":223,"hidden":0},"C004_Cnpj":{"top":99,"left":86,"width":216,"hidden":0},"C004_Inscricao_Estadual":{"top":122,"left":25,"width":216,"hidden":0},"C004_Inscricao_Municipal":{"top":145,"left":29,"width":216,"hidden":0},"C004_Numero_Suframa":{"top":168,"left":39,"width":216,"hidden":0},"C004_Matriz":{"top":191,"left":85,"width":200,"hidden":0},"C004_Logradouro":{"top":214,"left":68,"width":216,"hidden":0},"C004_Numero":{"top":236.984375,"left":76,"width":60,"hidden":0},"C004_Complemento":{"top":259.984375,"left":55,"width":216,"hidden":0},"C004_Bairro":{"top":282.984375,"left":86,"width":216,"hidden":0},"C004_Cep":{"top":305.984375,"left":95,"width":136,"hidden":0},"C004_DDD_Telefone":{"top":328.984375,"left":80,"width":40,"hidden":0},"C004_Telefone":{"top":328.984375,"left":176,"width":88,"hidden":0},"C004_DDD_Fax":{"top":351.984375,"left":103,"width":40,"hidden":0},"C004_Fax":{"top":351.984375,"left":176,"width":88,"hidden":0},"C004_Site":{"top":374.984375,"left":104,"width":216,"hidden":0},"C004_E_Mail":{"top":397.984375,"left":92,"width":216,"hidden":0}}',
			'parametros' => '{"C004_Casas_Decimais_Preco":{"top":7,"left":26.84375,"width":35,"hidden":0},"C004_IPV_Padrao":{"top":7,"left":644.125,"width":72,"hidden":0},"C004_Observacao_Fiscal":{"top":30,"left":32.75,"height":60,"width":344,"hidden":0},"C004_IPV_Padrao_Sem_Lucro":{"top":30,"left":593.15625,"width":72,"hidden":0},"C004_Meses_MMF":{"top":30,"left":957.984375,"width":45,"hidden":1},"C004_ICF_Padrao":{"top":53,"left":644.8125,"width":72,"hidden":0},"C004_IPV_Padrao_Demonstrativo":{"top":76,"left":527.390625,"height":154,"width":152,"hidden":0},"C004_CFOP_Venda_Dentro_Estado":{"top":76,"left":887.515625,"width":45,"hidden":1},"C004_Observacao_Promocao":{"top":99,"left":10.828125,"height":62,"width":344,"hidden":0},"C004_CFOP_Venda_Fora_Estado":{"top":99,"left":899.515625,"width":45,"hidden":1},"C004_CFOP_ST_Venda_Fora_Estado":{"top":122,"left":915.90625,"width":45,"hidden":1},"C004_CFOP_ST_Venda_Dentro_Estado":{"top":145,"left":887.90625,"width":45,"hidden":1},"C004_Obs_Reduzido":{"top":168,"left":26,"height":62,"width":344,"hidden":0},"C004_CFOP_CONSUMO_Venda_Dentro_Estado":{"top":168,"left":831.390625,"width":45,"hidden":1},"C004_CFOP_CONSUMO_Venda_Fora_Estado":{"top":214,"left":843.390625,"width":45,"hidden":1},"C004_Observacao_Ordem_Compra":{"top":237,"left":33.53125,"height":85,"width":344,"hidden":0},"C004_Flag_Decimais_Sugestao_Compra":{"top":259.984375,"left":884.09375,"width":200,"hidden":1},"C004_Emite_NF_Caixa":{"top":282.984375,"left":1000.546875,"width":200,"hidden":1},"C004_Observacao_Cotacao":{"top":329,"left":69.6875,"height":85,"width":344,"hidden":0},"C004_Linhas_Obs_Fiscal":{"top":328.984375,"left":1108.546875,"width":45,"hidden":1},"C004_Observacao_Logo":{"top":374.984375,"left":996.9375,"width":220,"hidden":1},"C004_Observao_Cotation":{"top":421,"left":20.796875,"height":85,"width":344,"hidden":0},"C004_Observao_Purchase_Order":{"top":513,"left":20.796875,"height":85,"width":344,"hidden":0}}',
			'impostos' => '{"C004_Flag_Tipo_Preenchimento_Reforma_Tributaria":{"top":375,"left":9.96875,"width":447,"hidden":0},"C004_Flag_Natureza_Pessoa_Juridica":{"top":7,"left":54.84375,"width":300,"hidden":0},"C004_Flag_Tipo_Atividade":{"top":30,"left":110.5625,"width":300,"hidden":0},"C004_Flag_Incidencia_Tributaria":{"top":53,"left":95.8125,"width":300,"hidden":0},"C004_Flag_Metodo_Apropriacao_Credito":{"top":76,"left":27.15625,"width":300,"hidden":0},"C004_Flag_Tipo_Contribuicao":{"top":99,"left":93.71875,"width":300,"hidden":0},"C004_Flag_Escrituracao_Contribuicao":{"top":122,"left":80.6875,"width":300,"hidden":0},"C004_Codigo_Regime_Tributario":{"top":145,"left":175.046875,"width":300,"hidden":0},"C004_Flag_Empresa_Simples":{"top":168,"left":109.984375,"width":300,"hidden":0},"C004_Flag_Isento_ICMS":{"top":190.984375,"left":135.765625,"width":300,"hidden":0},"C004_Flag_Isento_ISS":{"top":213.984375,"left":146,"width":300,"hidden":0},"C004_Flag_Isento_IPI":{"top":236.984375,"left":149.125,"width":300,"hidden":0},"C004_Percentual_PIS":{"top":260,"left":175.34375,"width":56,"hidden":0},"C004_Percentual_COFINS":{"top":283,"left":153.625,"width":56,"hidden":0},"C004_Percentual_IRPJ":{"top":306,"left":170.09375,"width":56,"hidden":0},"C004_Percentual_CSLL":{"top":329,"left":166.265625,"width":56,"hidden":0},"C004_Classificacao_Contribuinte":{"top":352,"left":59.015625,"width":447,"hidden":0}}',
			'pedidoDeVenda' => '{"C004_PEDVEN_Botao_Liberar_Separacao":{"top":7,"left":12,"width":300,"hidden":0}}',
			'configuracoes' => '{"C004_Ultimo_Numero_Nota_Fiscal":{"top":283,"left":618.421875,"width":104,"hidden":1},"C004_Caminho_Sistema":{"top":7,"left":44.65625,"width":220,"hidden":0},"C004_Codigo_Agencia_Correios":{"top":7,"left":560.34375,"width":56,"hidden":0},"C004_Ultimo_Numero_Cupom_Fiscal":{"top":237,"left":603.09375,"width":104,"hidden":0},"C004_Banco_Dados":{"top":30,"left":51.390625,"width":70,"hidden":0},"C004_Login_Portal_Postal":{"top":30,"left":575.015625,"width":104,"hidden":0},"C004_Ultimo_Numero_ACP":{"top":214,"left":595.125,"width":104,"hidden":0},"C004_Banco_Configuracao":{"top":53,"left":32.15625,"width":70,"hidden":0},"C004_Senha_Portal_Postal":{"top":53,"left":571.65625,"width":104,"hidden":0},"C004_Ultimo_Numero_Nota_Fiscal_Eletronica":{"top":122,"left":596.703125,"width":104,"hidden":0},"C004_Responsavel_Contato":{"top":76,"left":27.09375,"width":220,"hidden":0},"C004_D024_Id_Hardness":{"top":76,"left":615.234375,"width":104,"hidden":0},"C004_Ultimo_Numero_Serie_NFE":{"top":145,"left":619.671875,"width":104,"hidden":0},"C004_CNAE_Fiscal":{"top":99,"left":78.53125,"width":70,"hidden":0},"CnaeAlerta":{"top":99,"left":225,"width":null,"hidden":0},"C004_Ultimo_Numero_NFCe":{"top":168,"left":589.421875,"width":104,"hidden":0},"C004_Senha_Certificados":{"top":122,"left":46.203125,"width":70,"hidden":0},"btnDownloadCertificado":{"top":120,"left":240,"width":null,"hidden":0},"C004_Ultimo_Numero_Serie_NFCE":{"top":191,"left":612.40625,"width":104,"hidden":0},"uploadArquivoCert":{"top":145,"left":47.890625,"width":333,"hidden":0},"C004_Ambiente_NFe":{"top":168,"left":61.8125,"width":200,"hidden":0},"C004_Versao_NFe":{"top":191,"left":82,"width":200,"hidden":0},"C004_D024_Id":{"top":213.984375,"left":104.78125,"width":222,"hidden":0},"C004_Fuso_Horario":{"top":236.984375,"left":75.640625,"width":244,"hidden":0},"C004_Identificacao_CSC":{"top":259.984375,"left":105.84375,"width":72,"hidden":0},"C004_Codigo_CSC":{"top":282.984375,"left":117.765625,"width":328,"hidden":0},"C004_Ordem_Empresa":{"top":306,"left":60.28125,"width":56,"hidden":0}}',
			'juros' => '{"C004_Taxa_Juros":{"top":7,"left":41,"width":45,"hidden":0},"C004_Parcelas_Isentas_Juros":{"top":30,"left":26,"width":45,"hidden":0},"C004_Tipo_Juros":{"top":53,"left":44,"width":200,"hidden":0}}',
			'visual' => '{"C004_Menu_Extra_Html":{"top":7,"left":17.640625,"height":77,"width":808,"hidden":0},"C004_Logo_Etiqueta":{"top":145,"left":5.984375,"height":269,"width":808,"hidden":0},"ExemploMenuExtra":{"top":99,"left":81,"width":null,"hidden":0}}',
			'mdfe' => '{"C004_Flag_Emitente":{"top":7,"left":47.796875,"width":416,"hidden":0},"C004_Flag_Transportador":{"top":30,"left":29.859375,"width":416,"hidden":0},"C004_D172_Id":{"top":53,"left":41.859375,"width":265,"hidden":0},"btnSeguradoraPadrao":{"top":51,"left":432,"width":null,"hidden":0},"C004_D173_Id":{"top":53,"left":527.28125,"width":159,"hidden":1},"C004_D170_Id":{"top":76,"left":64.265625,"width":265,"hidden":0},"btnVeiculoPadrao":{"top":74,"left":432,"width":null,"hidden":0},"C004_Ambiente_MDFe":{"top":99,"left":54.75,"width":160,"hidden":0},"C004_Versao_MDFe":{"top":122,"left":66.96875,"width":54,"hidden":0},"C004_Tipo_Transporte_Padrao":{"top":145,"left":7.234375,"width":288,"hidden":0},"C004_Tipo_Carga_Padrao":{"top":168,"left":30.0625,"width":288,"hidden":0}}',
			'cte' => '{"C004_RNTRC":{"top":7,"left":55.640625,"width":137,"hidden":0},"C004_Ultimo_Numero_CTe":{"top":30,"left":19.15625,"width":137,"hidden":0},"C004_Ultimo_Numero_Serie_CTe":{"top":53,"left":42.125,"width":137,"hidden":0},"C004_Ambiente_CTe":{"top":76,"left":19.71875,"width":143,"hidden":0},"C004_Versao_CTe":{"top":99,"left":32.453125,"width":54,"hidden":0}}'
		);
		
		if($g['nivelAlcada']==4){
			$type = 'button';
		}else{
			$type = 'hidden';
		}
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					//'C004_Id' => array('name' => 'C004_Id', 'label' => 'Empresa Atual', 'type' => 'hidden', 'value'=>$g['empresaAtual'], 'length' => array(30,40)),
					'C004_Nome_Empresa' => array('name' => 'C004_Nome_Empresa', 'label' => 'Nome Empresa', 'type' => 'text', 'length' => array(60,80)),
					'C004_Nome_Abreviado' => array('name' => 'C004_Nome_Abreviado', 'label' => 'Nome Abreviado', 'type' => 'text', 'length' => array(40,60)),
					'C004_D018_Id' => array('name' => 'C004_D018_Id', 'label' => 'UF', 'type' => 'select', 'extra' => "onchange=\"alteraEstadoEmpresas(this, '{$g['divId']}')\"", 'select' => 
						gGeraSelect('D018', 'D018_Id', 'D018_UF,D018_Estado', 'order by D018_UF,D018_Estado', true)
					),
					'C004_D020_Id' => array('name' => 'C004_D020_Id', 'label' => 'Cidade', 'type' => 'select', 'select' => 
						gGeraSelect('D020', 'D020_Id', 'D020_Nome_Cidade', $whereD020, 'order by D020_Nome_Cidade', true)
					),
					'C004_Cnpj' => array('name' => 'C004_Cnpj', 'label' => 'CNPJ', 'type' => 'cnpj'),
					'C004_Inscricao_Estadual' => array('name' => 'C004_Incricao_Estadual', 'label' => 'Inscrição Estadual', 'type' => 'text', 'length' => array(40,60)),
					'C004_Inscricao_Municipal' => array('name' => 'C004_Incricao_Municipal', 'label' => 'Inscrição Municipal', 'type' => 'text', 'length' => array(40,60)),
					'C004_Numero_Suframa' => array('name' => 'C004_Numero_Suframa', 'label' => 'Número Suframa', 'type' => 'text', 'length' => array(40,9)),
					'C004_Matriz' => array('name' => 'C004_Matriz','label'=>'Matriz','extra' =>'style="width:200px"', 'type' => 'select', 
						'select' => array(
							array('title' => 'Sim', 'value' => "1"),
							array('title' => 'Não', 'value' => "2", 'selected' => true)
						)
					),
					'C004_Logradouro' => array('name' => 'C004_Logradouro', 'label' => 'Endereço', 'type' => 'text', 'length' => array(60,60)),
					'C004_Numero' => array('name' => 'C004_Numero', 'label' => 'Número', 'type' => 'text', 'length' => array(8,8)),
					'C004_Complemento' => array('name' => 'C004_Complemento', 'label' => 'Complemento', 'type' => 'text', 'length' => array(40,60)),
					'C004_Bairro' => array('name' => 'C004_Bairro', 'label' => 'Bairro', 'type' => 'text', 'length' => array(40,60)),
					'C004_Cep' => array('name' => 'C004_Cep', 'label' => 'Cep', 'type' => 'text', 'length' => array(9,9)),
					'C004_DDD_Telefone' => array('name' => 'C004_DDD_Telefone', 'label' => 'Telefone', 'type' => 'text', 'length' => array(2,2)),
					'C004_Telefone' => array('name' => 'C004_Telefone', 'label' => '', 'type' => 'text', 'length' => array(12,12), 'align'=>'right'),
					'C004_DDD_Fax' => array('name' => 'C004_DDD_Fax', 'label' => 'Fax', 'type' => 'text', 'length' => array(2,2)),
					'C004_Fax' => array('name' => 'C004_Fax', 'label' => '', 'type' => 'text', 'length' => array(12,12), 'align'=>'right'),
					'C004_Site' => array('name' => 'C004_Site', 'label' => 'Site', 'type' => 'text', 'length' => array(40,60)),
					'C004_E_Mail' => array('name' => 'C004_E_Mail', 'label' => 'E-Mail', 'type' => 'text', 'length' => array(40,60)),
					'C004_Flag_Ativo' => array('name' => 'C004_Flag_Ativo','label'=>'Empresa ativa?','extra' =>'style="width:200px"', 'type' => 'select', 
						'select' => array(
							array('title' => 'Sim', 'value' => "S"),
							array('title' => 'Não', 'value' => "N")
						)
					),
				),
				'parametros' => array(
					// Campo normal (do banco)
					'C004_Meses_MMF' => array('name' => 'C004_Meses_MMF', 'label' => 'Meses MMF', 'type' => 'text', 'length' => array(5,5)),
					'C004_CFOP_Venda_Dentro_Estado' => array('name' => 'C004_CFOP_Venda_Dentro_Estado', 'label' => 'CFOP Venda dentro do estado','align'=>'right', 'style'=>'float:right', 'type' => 'text', 'length' => array(5,5)),
					'C004_CFOP_Venda_Fora_Estado' => array('name' => 'C004_CFOP_Venda_Fora_Estado', 'label' => 'CFOP Venda fora do estado', 'type' => 'text', 'length' => array(5,5)),
					'C004_CFOP_ST_Venda_Dentro_Estado' => array('name' => 'C004_CFOP_ST_Venda_Dentro_Estado', 'label' => 'CFOP ST Venda dentro do estado','align'=>'right', 'style'=>'float:right', 'type' => 'text', 'length' => array(5,5)),
					'C004_CFOP_ST_Venda_Fora_Estado' => array('name' => 'C004_CFOP_ST_Venda_Fora_Estado', 'label' => 'CFOP ST Venda fora do estado', 'type' => 'text', 'length' => array(5,5)),
					'C004_CFOP_CONSUMO_Venda_Dentro_Estado' => array('name' => 'C004_CFOP_CONSUMO_Venda_Dentro_Estado', 'label' => 'CFOP CONSUMO Venda dentro do estado','align'=>'right', 'style'=>'float:right', 'type' => 'text', 'length' => array(5,5)),
					'C004_CFOP_CONSUMO_Venda_Fora_Estado' => array('name' => 'C004_CFOP_CONSUMO_Venda_Fora_Estado', 'label' => 'CFOP CONSUMO Venda fora do estado', 'type' => 'text', 'length' => array(5,5)),
					'C004_Emite_NF_Caixa' => array('name' => 'C004_Emite_NF_Caixa','label'=>'Caixa Emite NF','align'=>'right', 'style'=>'float:right','extra' =>'style="width:200px"', 'type' => 'select', 
						'select' => array(
							array('title' => 'Sim', 'value' => "1"),
							array('title' => 'Não', 'value' => "2", 'selected' => true)
						)
					),
					//'C004_Endereco_Impressora_Nf' => array('name' => 'C004_Endereco_Impressora_Nf', 'label' => 'Impressora NF', 'type' => 'text', 'length' => array(40,80)),
					//'C004_Impressora_Pedido_Almox' => array('name' => 'C004_Impressora_Pedido_Almox', 'label' => 'Impressora Pedido Almox', 'type' => 'text', 'length' => array(40,80)),
					//'C004_Impressora_Comercial' => array('name' => 'C004_Impressora_Comercial', 'label' => 'Impressora Comercial', 'type' => 'text', 'length' => array(40,80)),
					//'C004_Impressora_Balcao' => array('name' => 'C004_Impressora_Balcao', 'label' => 'Impressora Balcão', 'type' => 'text', 'length' => array(40,80)),
					'C004_Flag_Decimais_Sugestao_Compra' => array('name' => 'C004_Flag_Decimais_Sugestao_Compra','label'=>'Decimais na Sugestão Compra','extra' =>'style="width:200px"', 'type' => 'select', 
						'select' => array(
							array('title' => 'Sim', 'value' => "S"),
							array('title' => 'Não', 'value' => "N", 'selected' => true)
						)
					),
					'C004_Linhas_Obs_Fiscal' => array('name' => 'C004_Linhas_Obs_Fiscal', 'label' => 'Linhas Obs Fiscal', 'type' => 'text','align'=>'right', 'style'=>'float:right', 'length' => array(5,5)),
					'C004_Casas_Decimais_Preco' => array('name' => 'C004_Casas_Decimais_Preco', 'label' => 'Casas Decimais no <br> Processo de Venda', 'type' => 'text', 'length' => array(3,5)),
					'C004_Observacao_Logo' => array('name' => 'C004_Observacao_Logo', 'label' => 'Observação Logo','align'=>'right', 'style'=>'float:right', 'type' => 'text', 'length' => array(40,80)),
					'C004_Observacao_Fiscal' => array('name' => 'C004_Observacao_Fiscal', 'label' => '<br>Observação Fiscal', 'type' => 'textarea', 'length' => array(7,60)),
					'C004_Obs_Reduzido' => array('name' => 'C004_Obs_Reduzido', 'label' => 'Obs ICMS Reduzido', 'type' => 'textarea', 'length' => array(3,60)),
					'C004_Observacao_Promocao' => array('name' => 'C004_Observacao_Promocao', 'label' => 'Observação Promoção', 'type' => 'textarea', 'length' => array(3,60)),
					'C004_Observacao_Ordem_Compra' => array('name' => 'C004_Observacao_Ordem_Compra', 'label' => 'Observação <br />Ordem de Compra', 'type' => 'textarea', 'length' => array(7,60), 'uppercase' => false),
					'C004_Observacao_Cotacao' => array('name' => 'C004_Observacao_Cotacao', 'label' => 'Observação <br />Cotação', 'type' => 'textarea', 'length' => array(7,60), 'uppercase' => false),
					'C004_IPV_Padrao' => array('name' => 'C004_IPV_Padrao', 'label' => 'IPV Padrão', 'type' => 'moeda', 'casasDecimais' => '4'),
					'C004_IPV_Padrao_Sem_Lucro' => array('name' => 'C004_IPV_Padrao_Sem_Lucro', 'label' => 'IPV Padrão sem lucro', 'type' => 'moeda', 'casasDecimais' => '4'),
					'C004_ICF_Padrao' => array('name' => 'C004_ICF_Padrao', 'label' => 'ICF Padrão', 'type' => 'moeda', 'casasDecimais' => '4'),
					'C004_IPV_Padrao_Demonstrativo' => array('name' => 'C004_IPV_Padrao_Demonstrativo', 'label' => 'Demonstrativo IPV', 'type' => 'textarea', 'style'=>'float:right', 'length' => array(5,5)),
					'C004_Observao_Cotation' => array('name' => 'C004_Observao_Cotation', 'label' => 'Observação <br/>Cotation<br/>(Important Remarks)', 'type' => 'textarea', 'length' => array(7,60), 'uppercase' => false),
					'C004_Observao_Purchase_Order' => array('name' => 'C004_Observao_Purchase_Order', 'label' => 'Observação <br/>Purchase Order<br/>(Important Remarks)', 'type' => 'textarea', 'length' => array(7,60), 'uppercase' => false),
				),
				'impostos' => array(
					// Campo normal (do banco)
					'C004_Flag_Natureza_Pessoa_Juridica' => array('name' => 'C004_Flag_Natureza_Pessoa_Juridica','label'=>'Natureza da Pessoa Jurídica','extra' =>'style="width:300px"', 'type' => 'select', 
						'select' => array(
							array('title' => 'Pessoa jurídica em geral', 'value' => '00', 'selected' => true),
							array('title' => 'Sociedade cooperativa', 'value' => '01'),
							array('title' => 'Entidade sujeita ao PIS/Pasep exclusivamente com base na Folha de Salários', 'value' => '02')
						)
					),
					'C004_Flag_Tipo_Atividade' => array('name' => 'C004_Flag_Tipo_Atividade','label'=>'Tipo de Atividade','extra' =>'style="width:300px"', 'type' => 'select', 
						'select' => array(
							array('title' => 'Industrial ou equiparado a industrial', 'value' => '0', 'selected' => true),
							array('title' => 'Outros', 'value' => '1')
						)
					),
					'C004_Flag_Incidencia_Tributaria' => array('name' => 'C004_Flag_Incidencia_Tributaria','label'=>'Incidência Tributária','extra' =>'style="width:300px"', 'type' => 'select', 
						'select' => array(
							array('title' => 'Não Cumulativo', 'value' => '1', 'selected' => true),
							array('title' => 'Cumulativo', 'value' => '2'),
							array('title' => 'Ambos', 'value' => '3')
						)
					),
					'C004_Flag_Metodo_Apropriacao_Credito' => array('name' => 'C004_Flag_Metodo_Apropriacao_Credito','label'=>'Método de Apropriação ao Crédito','extra' =>'style="width:300px"', 'type' => 'select', 
						'select' => array(
							array('title' => 'Não possui', 'value' => '0', 'selected' => true),
							array('title' => 'Método de Apropriação Direta', 'value' => '1'),
							array('title' => 'Método de Rateio Proporcional (Receita Bruta)', 'value' => '2')							
						)
					),
					'C004_Flag_Tipo_Contribuicao' => array('name' => 'C004_Flag_Tipo_Contribuicao','label'=>'Tipo de Contribuição','extra' =>'style="width:300px"', 'type' => 'select', 
						'select' => array(
							array('title' => 'Contribuição Exclusivamente a Alíquota Básica', 'value' => '1', 'selected' => true),
							array('title' => 'Contribuição a Alíquotas Específicas (Diferenciadas e/ou por Unidade de Medida de Produto)', 'value' => '2')
						)
					),
					'C004_Flag_Escrituracao_Contribuicao' => array('name' => 'C004_Flag_Escrituracao_Contribuicao','label'=>'Critério de Escrituração','extra' =>'style="width:300px"', 'type' => 'select', 
						'select' => array(
							array('title' => 'Regime de Competência - Escrituração detalhada', 'value' => '9', 'selected' => true), 
							array('title' => 'Regime de Caixa – Escrituração consolidada', 'value' => '1'),
							array('title' => 'Regime de Competência - Escrituração consolidada', 'value' => '2')														
						)
					),
					'C004_Codigo_Regime_Tributario' => array('name' => 'C004_Codigo_Regime_Tributario','label'=>'CRT','extra' =>'style="width:300px"', 'type' => 'select', 
						'select' => array(
							array('title' => '1 - Simples Nacional', 'value' => "1"),
							array('title' => '2 - Simples Nacional é excesso de sublimite de receita bruta', 'value' => "2"),
							array('title' => '3 - Regime Normal', 'value' => "3", 'selected' => true)
						)
					),
					'C004_Flag_Empresa_Simples' => array('name' => 'C004_Flag_Empresa_Simples','label'=>'Empresa Simples','extra' =>'style="width:300px"', 'type' => 'select', 
						'select' => array(
							array('title' => 'Sim', 'value' => 1),
							array('title' => 'Não', 'value' => 2, 'selected' => true)
						)
					),
					'C004_Flag_Isento_ICMS' => array('name' => 'C004_Flag_Isento_ICMS','label'=>'Isento ICMS','extra' =>'style="width:300px"', 'type' => 'select', 
						'select' => array(
							array('title' => 'Sim', 'value' => 1),
							array('title' => 'Não', 'value' => 2, 'selected' => true)
						)
					),
					'C004_Flag_Isento_ISS' => array('name' => 'C004_Flag_Isento_ISS','label'=>'Isento ISS','extra' =>'style="width:300px"', 'type' => 'select', 
						'select' => array(
							array('title' => 'Sim', 'value' => 1),
							array('title' => 'Não', 'value' => 2, 'selected' => true)
						)
					),
					'C004_Flag_Isento_IPI' => array('name' => 'C004_Flag_Isento_IPI','label'=>'Isento IPI','extra' =>'style="width:300px"', 'type' => 'select', 
						'select' => array(
							array('title' => 'Sim', 'value' => 1),
							array('title' => 'Não', 'value' => 2, 'selected' => true)
						)
					),
					'C004_Percentual_PIS' => array('name' => 'C004_Percentual_PIS', 'label' => '% PIS', 'type' => 'moeda', 'length' => array(5,10)),
					'C004_Percentual_COFINS' => array('name' => 'C004_Percentual_COFINS', 'label' => '% COFINS', 'type' => 'moeda', 'length' => array(5,10)),
					'C004_Percentual_IRPJ' => array('name' => 'C004_Percentual_IRPJ', 'label' => '% IRPJ', 'type' => 'moeda', 'length' => array(5,10)),
					'C004_Percentual_CSLL' => array('name' => 'C004_Percentual_CSLL', 'label' => '% CSLL', 'type' => 'moeda', 'length' => array(5,10)),
					'C004_Classificacao_Contribuinte' => array('name' => 'C004_Classificacao_Contribuinte','label'=>'Classificação do Contribuinte','type' => 'select', 
						'select' => array(
							array('title' => 'Industrial - Transformação', 'value' => '00'),							
							array('title' => 'Industrial - Beneficiamento', 'value' => '01'),							
							array('title' => 'Industrial - Montagem', 'value' => '02'),							
							array('title' => 'Industrial - Acondicionamento ou Reacondicionamento', 'value' => '03'),							
							array('title' => 'Industrial - Renovação ou Recondicionamento', 'value' => '04'),							
							array('title' => 'Equiparado a industrial - Por opção', 'value' => '05'),							
							array('title' => 'Equiparado a industrial - Importação Direta', 'value' => '06'),							
							array('title' => 'Equiparado a industrial - Por lei específica', 'value' => '07'),							
							array('title' => 'Equiparado a industrial - Não enquadrado nos códigos 05, 06 ou 07', 'value' => '08'),							
							array('title' => 'Outros', 'value' => '09'),						
						)
					),
					/* [REFORMA TRIBUTÁRIA] - Gabriel Cegantini 24/11/2025
        			================================================================================ */
					'C004_Flag_Tipo_Preenchimento_Reforma_Tributaria' => array('name' => 'C004_Flag_Tipo_Preenchimento_Reforma_Tributaria','label'=>'Tipo Preenchimento Reforma Tributária','type' => 'select', 
						'select' => array(
							array('title' => 'MANUAL', 'value' => '0', 'selected' => true),											
							array('title' => 'AUTOMÁTICO PELA NCM E CFOP', 'value' => '1'),
						)
					),
					/* ================================================================================ */
				),
				'pedidoDeVenda' => array(
					// Campo normal (do banco)
					'C004_PEDVEN_Botao_Liberar_Separacao' => array('name' => 'C004_PEDVEN_Botao_Liberar_Separacao','label'=>'Observação de Pré-Separação','extra' =>'style="width:300px"', 'type' => 'select', 
						'select' => array(
							array('title' => 'Sim', 'value' => "S"),
							array('title' => 'Não', 'value' => "N", 'selected' => true)
						)
					),
				),
				'configuracoes' => array(
					// Campo normal (do banco)
					'C004_Caminho_Sistema' => array('name' => 'C004_Caminho_Sistema', 'label' => 'Caminho Sistema', 'type' => 'text', 'length' => array(40,80)),
					'C004_Banco_Dados' => array('name' => 'C004_Banco_Dados', 'label' => 'Banco de Dados', 'type' => 'text', 'length' => array(10,15)),
					'C004_Banco_Configuracao' => array('name' => 'C004_Banco_Configuracao', 'label' => 'Banco Configuração', 'type' => 'text', 'length' => array(10,15)),
					'C004_Responsavel_Contato' => array('name' => 'C004_Responsavel_Contato', 'label' => 'Contador Resposável', 'type' => 'text', 'length' => array(40,80)),
					'C004_CNAE_Fiscal' => array('name' => 'C004_CNAE_Fiscal', 'label' => 'CNAE Fiscal', 'type' => 'text', 'length' => array(10,80)),
					'C004_Senha_Certificados' => array('name' => 'C004_Senha_Certificados', 'label' => 'Senha Certificados', 'type' => 'password', 'length' => array(10,80), 'uppercase' => false),
					'uploadArquivoCert' => array('name' => 'uploadArquivoCert', 'reject'=>true, 'label' => 'Upload Certificado', 'type' => 'file', 'length' => array(10,80)),
					'C004_Ambiente_NFe' => array('name' => 'C004_Ambiente_NFe','label'=>'Ambiente NFe','extra' =>'style="width:200px"', 'type' => 'select', 
						'select' => array(
							array('title' => '1 - Produção', 'value' => "1"),
							array('title' => '2 - Homologação', 'value' => "2", 'selected' => true)
						)
					),
					'C004_D024_Id' => array('name' => 'C004_D024_Id','label'=>'Cliente', 'type' => 'pesquisa', 'pesquisa' => array('D024','D024_Id','D024_Id,D024_Nome_Empresa,D020_Nome_Cidade,D018_UF,D024_Cpf,D024_Cnpj,D024_DDD_Telefone_1,D024_Telefone_1','left join D020 on D020_Id=D024_D020_Id left join D018 on D018_Id=D024_D018_Id WHERE D024_Flag_Cliente="S" and D024_Flag_Ativo="S" order by D024_Nome_Empresa','(%1) %2 [%3/%4] - %5 Tel:(%7)%8'), 'length' => array(40,30)),
					'CnaeAlerta' => array('name' => 'CnaeAlerta', 'type' => 'comentario', 'value' => '(informar apenas se existir inscrição municipal)', 'reject' => true),
					'C004_Fuso_Horario' => array('name' => 'C004_Fuso_Horario', 'label'=>'Fuso Horário', 'uppercase' => false, 'type' => 'select', 
						'select' => array(
							array('title' => 'Padrão (Brasília)', 'value' => "", 'selected' => true),
							array('title' => 'Fernando de Noronha (UTC -2)', 'value' => "America/Noronha"),
							array('title' => 'Brasília (UTC -3/ -2)', 'value' => "America/Sao_Paulo"),
							array('title' => 'Brasília (UTC -3)', 'value' => "America/Recife"),
							array('title' => 'Mato Grosso (UTC -4/-3)', 'value' => "America/Cuiaba"),
							array('title' => 'Rondônia (UTC -4)', 'value' => "America/Porto_Velho")
						)
					),
					'C004_Identificacao_CSC' => array('name' => 'C004_Identificacao_CSC', 'label' => 'Id CSC', 'type' => 'text'),
					'C004_Codigo_CSC' => array('name' => 'C004_Codigo_CSC', 'label' => 'CSC', 'type' => 'text'),
					'C004_Codigo_Agencia_Correios' => array('name' => 'C004_Codigo_Agencia_Correios', 'label' => 'Código da agência dos correios', 'type' => 'text'),
					'C004_Login_Portal_Postal' => array('name' => 'C004_Login_Portal_Postal', 'label' => 'Login portal postal', 'type' => 'text', 'uppercase' => false),
					'C004_Senha_Portal_Postal' => array('name' => 'C004_Senha_Portal_Postal', 'label' => 'Senha portal postal', 'type' => 'password', 'uppercase' => false),
					'C004_D024_Id_Hardness' => array('name' => 'C004_D024_Id_Hardness', 'label' => 'ID Suporte', 'type' => 'text'),
					'C004_Versao_NFe' => array('name' => 'C004_Versao_NFe','label'=>'Versão NFe','extra' =>'style="width:200px"', 'type' => 'select', 
						'select' => array(
							array('title' => '3.10', 'value' => "3.10", 'selected' => true),
							array('title' => '4.00', 'value' => "4.00")
						)
					),
					'btnDownloadCertificado' => array('name' => 'btnDownloadCertificado', 'type'=> $type, 'value' => 'Download Certificado', 'reject' => true),
					'C004_Ordem_Empresa' => array('name' => 'C004_Ordem_Empresa', 'label' => 'Ordem empresa', 'type' => 'moeda', 'length' => array(5,5),'casasDecimais' => '0'),
					'C004_Ultimo_Numero_Nota_Fiscal' => array('name' => 'C004_Ultimo_Numero_Nota_Fiscal', 'label' => 'Último Nº NF'),
					'C004_Ultimo_Numero_Cupom_Fiscal' => array('name' => 'C004_Ultimo_Numero_Cupom_Fiscal', 'label' => 'Último Nº CF'),
					'C004_Ultimo_Numero_ACP' => array('name' => 'C004_Ultimo_Numero_ACP', 'label' => 'Último Nº ACP'),
					'C004_Ultimo_Numero_Nota_Fiscal_Eletronica' => array('name' => 'C004_Ultimo_Numero_Nota_Fiscal_Eletronica', 'label' => 'Último Nº NFe'),
					'C004_Ultimo_Numero_Serie_NFE' => array('name' => 'C004_Ultimo_Numero_Serie_NFE', 'label' => 'Série NFe'),
					'C004_Ultimo_Numero_NFCe' => array('name' => 'C004_Ultimo_Numero_NFCe', 'label' => 'Último Nº NFCe'),
					'C004_Ultimo_Numero_Serie_NFCE' => array('name' => 'C004_Ultimo_Numero_Serie_NFCE', 'label' => 'Série NFCe'),

				),
				'juros' => array(
					// Campo normal (do banco)
					'C004_Taxa_Juros' => array('name' => 'C004_Taxa_Juros', 'label' => 'Taxa de juros', 'type' => 'moeda', 'length' => array(5,10)),
					'C004_Parcelas_Isentas_Juros' => array('name' => 'C004_Parcelas_Isentas_Juros', 'label' => 'Parcelas isentas', 'type' => 'text', 'length' => array(5,10)),
					'C004_Tipo_Juros' => array('name' => 'C004_Tipo_Juros','label'=>'Tipo de juros','extra' =>'style="width:200px"', 'type' => 'select', 
						'select' => array(
							array('title' => 'Simples', 'value' => "S", 'selected' => true),
							array('title' => 'Composto', 'value' => "C")
						)
					),					
				),
				'visual' => array(
					'C004_Menu_Extra_Html' => array('name' => 'C004_Menu_Extra_Html', 'label' => 'Menu Extra', 'type' => 'textarea', 'uppercase' => false),
					'ExemploMenuExtra' => array('name' => 'ExemploMenuExtra', 'type' => 'comentario', 'label' => '', 'reject' => true, 'value' => htmlentities('<span style="color: agua; margin-left: 6px; font-weight: 900; font-size: 37px; text-shadow: 1px 2px 0px black;">[[empresaAtual]]</span>')),
					'C004_Logo_Etiqueta' => array('name' => 'C004_Logo_Etiqueta', 'label' => 'Logo Etiqueta', 'type' => 'textarea', 'uppercase' => false),
				),
				'mdfe' => array(
					'C004_Flag_Emitente' => array('name' => 'C004_Flag_Emitente','label'=>'Tipo de Emitente','type' => 'select', 
						'select' => array(
							array('title' => '...', 'value' => "", 'selected' => true),
							array('title' => '1 - Prestador de Serviço', 'value' => "1"),
							array('title' => '2 - Carga Própria', 'value' => "2")
						)
					),
					'C004_Flag_Transportador' => array('name' => 'C004_Flag_Transportador','label'=>'Tipo do Transportador','type' => 'select', 
						'select' => array(
							array('title' => '...', 'value' => ""),
							array('title' => '1 - Empresas de Transporte Rodoviário de Carga (ETC)', 'value' => "1"),
							array('title' => '2 - Tranportador Autônomo de Cargas (TAC)', 'value' => "2"),
							array('title' => '3 - Cooperativa de Transporte Rodoviário de Cargas (CTC)', 'value' => "3")
						)
					),
					'C004_D172_Id' => array('name' => 'C004_D172_Id', 'label' => 'Seguradora Padrão', 'type' => 'select', 'extra' => "onchange=\"alteraSeguradora(this, '{$g['divId']}')\"", 'select' => 
						gGeraSelect('D172', 'D172_Id', 'D172_Nome', 'WHERE D172_Flag_Ativo = \'S\' AND D172_C004_Id = ' . $r_acaoId . ' ORDER BY D172_Nome', true)
					),
					'C004_D173_Id' => array('name' => 'C004_D173_Id', 'label' => 'Apólice Padrão', 'type' => 'select', 'select' => 
						gGeraSelect('D173', 'D173_Id', 'D173_Identificacao', 'where D173_Flag_Ativo=\'S\' order by D173_Identificacao', true)					),
					'btnSeguradoraPadrao' => array('name' => 'btnSeguradoraPadrao', 'type' => 'button', 'reload' => false, 'value' => 'Incluir', 'reject' => true),
					'C004_D170_Id' => array('name' => 'C004_D170_Id', 'label' => 'Veículo Padrão', 'type' => 'pesquisa', 'pesquisa' => array('D170', 'D170_Id', 'D170_Identificacao_Veiculo', 'WHERE D170_C004_Id = ' . $r_acaoId . ' AND D170_Flag_Ativo=\'S\' ORDER BY D170_Identificacao_Veiculo')),
					'btnVeiculoPadrao' => array('name' => 'btnVeiculoPadrao', 'type'=>'button', 'reload' => false, 'value' => 'Incluir', 'reject' => true),
					'C004_Ambiente_MDFe' => array('name' => 'C004_Ambiente_MDFe','label'=>'Ambiente MDF-e','type' => 'select', 
						'select' => array(
							array('title' => '...', 'value' => ""),
							array('title' => '1 - Produção', 'value' => "1", 'selected' => true),
							array('title' => '2 - Homologação', 'value' => "2")
						)
					),
					'C004_Versao_MDFe' => array('name' => 'C004_Versao_MDFe','label'=>'Versão MDF-e','type' => 'select', 
						'select' => array(
							array('title' => '3.00', 'value' => "3.00", 'selected' => true)
						)
					),
					'C004_Tipo_Transporte_Padrao' => array('name' => 'C004_Tipo_Transporte_Padrao','label'=>'Tipo de Transporte Padrão','type' => 'select', 
						'select' => array(
							array('title' => '...', 'value' => ""),
							array('title' => '1 - Rodoviário Tração', 'value' => "1"),
							array('title' => '2 - Rodoviário Reboque', 'value' => "2"),
							array('title' => '3 - Navio', 'value' => "3"),
							array('title' => '4 - Balsa', 'value' => "4"),
							array('title' => '5 - Aeronave', 'value' => "5"),
							array('title' => '6 - Vagão', 'value' => "6"),
							array('title' => '7 - Outros', 'value' => "7")
						), 'type' => 'hidden'
					),
					'C004_Tipo_Carga_Padrao' => array('name' => 'C004_Tipo_Carga_Padrao','label'=>'Tipo de Carga Padrão', 'type' => 'select', 
						'select' => array(
							array('title' => '...', 'value' => ""),
							array('title' => '1 - Conteiner', 'value' => "1"),
							array('title' => '2 - ULD', 'value' => "2"),
							array('title' => '3 - Pallet', 'value' => "3"),
							array('title' => '4 - Outros', 'value' => "4")
						), 'type' => 'hidden'
					)
				),
				'cte' => array(													
					'C004_Ambiente_CTe' => array('name' => 'C004_Ambiente_CTe','label'=>'Ambiente CT-e','type' => 'select', 
						'select' => array(							
							array('title' => '1 - Produção', 'value' => "1"),
							array('title' => '2 - Homologação', 'value' => "2",'selected' => true)
						)
					),
					'C004_Versao_CTe' => array('name' => 'C004_Versao_CTe','label'=>'Versão CT-e','type' => 'select', 
						'select' => array(
							array('title' => '3.00', 'value' => "3.00"),
							array('title' => '4.00', 'value' => "4.00", 'selected' => true)
						)
					),										
					'C004_Ultimo_Numero_CTe' => array('name' => 'C004_Ultimo_Numero_CTe', 'label' => 'Último Nº CT-e'),
					'C004_Ultimo_Numero_Serie_CTe' => array('name' => 'C004_Ultimo_Numero_Serie_CTe', 'label' => 'Série CT-e'),
					'C004_RNTRC' => array('name' => 'C004_RNTRC', 'label' => 'RNTRC'),
				)
			)
		);

        $form = new Formulario('formcad024FormPrincipal');
		$form->definirFormularioArray($formArray);
		echo <<<EOT
		    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#btnVeiculoPadrao').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Veículos', '/cad/cad064/content/cad064ContentFormCadastrarVeiculos/', '&tab=geral',  [550,550]);
					return false;
				});
				$('#btnSeguradoraPadrao').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Seguradoras', '/cad/cad066/content/cad066ContentFormCadastrarSeguradoras/', '&tab=geral',  [550,550]);
					return false;
				});
			}</script></div>
EOT;
		if($tab == 'configuracoes'){

			echo <<<EOT
			    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#btnDownloadCertificado').unbind('click').bind('click', function(data) {
						$('#baixarCert').attr("src", "{$g['urlRaiz']}hardness3/dados_usuarios/{$g['dbDatabase']}/nfe/cert/{$r_acaoId}.pfx");	
						sleep(100);
						$('#baixarCert').attr("src", "");	
					});
				}</script></div>
				<iframe id='baixarCert' style='display: none'></iframe>
EOT;
		}


		/**
		 * PARTE 3: Processamento do formulário (se foi enviado)
		 * E carregamento dos valores (se não foi enviado parametro acaoId)
		 */
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				// Insert ou Update?
				if (!empty($r_acaoId)) {
					// Update
					$arquivoLogo = $form->campoValorEnviado('uploadArquivoLogo');
					$form->campoSetarValor('C004_Logo_Marca', $arquivoLogo['name'], true);
					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $r_acaoId, $form)) {
						// Após executar o insert, vamos gravar os arquivos (se enviado)
						//$arquivo = $form->campoValorEnviado('uploadArquivoLogo');
						$arquivo = $form->campoValorEnviado('uploadArquivoCert');
						$error = false;
						if (!empty($arquivo['name']) && $arquivo['error'] == UPLOAD_ERR_OK) {
							$pathinfo = pathinfo($arquivo['name']);
							$pathinfo['extension'] = empty($pathinfo['extension']) ? '.tmp' : $pathinfo['extension'];
							if ($pathinfo['extension'] != 'pfx') {
								echo gGeraAlertMsg('Erro ao enviar certificado', 'O arquivo deve ser no formato ".PFX"');
								$error = true;
							} else {
								$dir = "dados_usuarios/{$confUsuario['dbDatabase']}/nfe/cert";
								is_dir($dir) or mkdir($dir, 0777, true);
								$file = $dir . '/' . $r_acaoId . '.' . $pathinfo['extension'];
								$fileKey = $dir . '/' . $r_acaoId . '_key.pem';
								$fileKey2 = $dir . '/' . $r_acaoId . '.pem';
								$fileCert = $dir . '/' . $r_acaoId . '_cert.pem';
								// Gera certificado
								$key = file_get_contents($arquivo['tmp_name']);
								$resp = openssl_pkcs12_read($key, $x509certdata, $form->campoValorEnviado('C004_Senha_Certificados'));
								if (empty($x509certdata['pkey'])) {
							    	echo gGeraAlertMsg('Erro ao enviar certificado', 'Erro ao verificar certificado. Erro: ' . openssl_error_string() . ' Dados: ' . $x509certdata['pkey'] . ' - ' . $x509certdata['cert'] . " - ");
							    	$error = true;
							    } else {
							    	// Copia arquivo
									if (file_exists($file)) { unlink($file); }
									if (file_exists($fileKey)) { unlink($fileKey); }
									if (file_exists($fileKey2)) { unlink($fileKey2); }
									if (file_exists($fileCert)) { unlink($fileCert); }
									move_uploaded_file($arquivo['tmp_name'], $file);
									file_put_contents($fileKey, $x509certdata['pkey']);
									file_put_contents($fileKey2, $x509certdata['pkey']);
        							file_put_contents($fileCert, $x509certdata['cert']);
									// Guarda certificado no banco
									$cC004_Certificado_Digital = base64_encode($key);
									mysql_query("update C004 set C004_Certificado_Digital='{$cC004_Certificado_Digital}', C004_Localizacao_Certificados = '" . realpath($dir) . "/' where C004_Id='{$r_acaoId}'");
								}
							}
						}
						if (!$error) {
							// Fecha a janela, recarrega o parent (grid) e emite uma mensagem de alerta
							$alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
							echo <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
										setTimeout(function() {
											{$alert}
										}, 150);
	  								});
									$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
									// O FW atualiza a linha do grid automaticamente, somente recarregar se não for grid
									if ($('#{$g['divIdParent']}').find('table.grid').length === 0) {
										divRefresh('{$g['divIdParent']}', true);
									}
								} </script></div>
EOT;
						}
						if ($arquivoLogo['error'] == UPLOAD_ERR_OK) {
							// Gravar o arquivo no disco
							$pathinfo = pathinfo($arquivoLogo['name']);
							$pathinfo['extension'] = empty($pathinfo['extension']) ? '.tmp' : $pathinfo['extension'];
							$dir = "{$g['pathDados']}imagens/logo";
							//$dir = "{$g['pathDados']}di";
							is_dir($dir) or mkdir($dir, 0777, true);
							$file = $dir . '/' . $r_acaoId . '.' . $pathinfo['extension'];
							move_uploaded_file($arquivoLogo['tmp_name'], $file);
							//gRedimensionaImagem($arquivoLogo, 200, $file);
							// copiar tambem para o hardness, usado na hora de abrir o logo na impressão da expedicao de mercadorias
							$fileGambi=$confUsuario['pathRaiz'].'../hardness/'.$g['pathDados'].'imagens/'.$arquivoLogo['name'];
							copy($file, $fileGambi);
						}
					}
				} else {
					// Insert
					$arquivoLogo = $form->campoValorEnviado('uploadArquivoLogo');
					$form->campoSetarValor('C004_Logo_Marca', $arquivoLogo['name'], true);
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						if ($arquivoLogo['error'] == UPLOAD_ERR_OK) {
							// Gravar o arquivo no disco
							$pathinfo = pathinfo($arquivoLogo['name']);
							$pathinfo['extension'] = empty($pathinfo['extension']) ? '.tmp' : $pathinfo['extension'];
							$dir = "{$g['pathDados']}imagens/logo";
							//$dir = "{$g['pathDados']}di";
							is_dir($dir) or mkdir($dir, 0777, true);
							$file = $dir . '/' . $g['mysqlLastId'] . '.' . $pathinfo['extension'];
							move_uploaded_file($arquivoLogo['tmp_name'], $file);
						}
						// Fecha a janela, recarrega o parent (grid), emite uma mensagem de alerta e abre o registro para alteração (o que acabou de ser incluido)
						$acaoId = $g['mysqlLastId'];
						$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						
						$CAD031->inserirAposC004($acaoId);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								// Fecha a janela, recarrega o grid e abre a alteração
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										//abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}' , unique(), '', 'Editar Empresas - ID: ' + '{$acaoId}', '/cad/cad031/content/cad031ContentCadastroEmpresas/', '&acaoId=' + encodeURIComponent('{$acaoId}'), [700,650]);
										{$alert}
									}, 150);
	  							});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								divRefresh('{$g['divIdParent']}', true);
							} </script></div>
EOT;
					}
				}
			}
		} else {
			// Carrega os valores caso seja um update
			if (!empty($r_acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $r_acaoId);	
				// Mostra erros de formulário uteis ao desenvolvedor
				if ($processado !== true) {
					reset($form->formArray['campos']);
					if ($tab == key($form->formArray['campos'])) {
						echo $processado;
					}
				}
			}
		}

		/**
		 * PARTE 4: Gerar o formulário
		 */
		$gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');
	break;
	
	case 'cad031FormCadastroLogomarca':
		global $g;
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
		
		/**
		 * PARTE 2: Definição do formulário
		 */
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($r_formcad024FormPrincipal)) ? $r_formcad024FormPrincipal['formId'] : $r_formId;
		} else {
			$formId = uniqid();
		}
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'C004';
		$form_campo_pk = 'C004_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad031FormCadastroLogomarcaEnviar';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad031/form/cad031FormCadastroLogomarca/';
		$form_editor = array(
			'logomarca' => '{"cad031FormCadastroLogomarcaEnviar":{"top":5,"left":464,"width":null,"hidden":0},"uploadArquivoLogo":{"top":7,"left":16,"width":333,"hidden":0}}'
		);
		
		$form = new Formulario('cad031FormCadastroLogomarca');
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				/*'logomarca' => array(
					// Campo normal (do banco)
					'uploadArquivoLogo' => array('name' => 'uploadArquivoLogo', 'reject'=>true, 'label' => 'Upload Logomarca', 'type' => 'file', 'length' => array(10,80)),
				),*/
				
				'logomarca' => array(
					// Campo normal (do banco)
					'uploadArquivoLogo' => array('name' => 'uploadArquivoLogo', 'reject'=>true, 'label' => 'Upload Logomarca', 'type' => 'file', 'length' => array(10,80)),
					'C004_Logo_Marca' => array('name' => 'C004_Logo_Marca', 'label' => 'Nome Arquivo', 'type' => 'hidden', 'length' => array(10,80), 'uppercase'=>false),
				),
			),
		);	
		$formArray['campos']['logomarca']['cad031FormCadastroLogomarcaEnviar'] = array('name' => 'cad031FormCadastroLogomarcaEnviar', 'type'=>'button', 'reload' => false, 'value' => 'Cadastrar', 'align' => 'right', 'reject' => true);
		//$formArray['campos']['logomarca']['C004_Logo_Marca'] = array('name' => 'C004_Logo_Marca', 'label' => 'Nome Arquivo', 'type' => 'hidden', 'length' => array(10,80), 'uppercase'=>false);
			
		
		$form->definirFormularioArray($formArray);

		/**
		 * PARTE 3: Processamento do formulário (se foi enviado)
		 * E carregamento dos valores (se não foi enviado parametro acaoId)
		 */
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				// Insert ou Update?
				if (!empty($r_acaoId)) {
					// Update
					$arquivoLogo = $form->campoValorEnviado('uploadArquivoLogo');
					$form->campoSetarValor('C004_Logo_Marca', $arquivoLogo['name'], true);
					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $r_acaoId, $form)) {
						// Após executar o insert, vamos gravar os arquivos (se enviado)
						//$arquivo = $form->campoValorEnviado('uploadArquivoLogo');
						$arquivo = $form->campoValorEnviado('uploadArquivoCert');
						$error = false;
						if (!empty($arquivo['name']) && $arquivo['error'] == UPLOAD_ERR_OK) {
							$pathinfo = pathinfo($arquivo['name']);
							$pathinfo['extension'] = empty($pathinfo['extension']) ? '.tmp' : $pathinfo['extension'];
							if ($pathinfo['extension'] != 'pfx') {
								echo gGeraAlertMsg('Erro ao enviar certificado', 'O arquivo deve ser no formato PFX');
								$error = true;
							} else {
								$dir = "dados_usuarios/{$confUsuario['dbDatabase']}/nfe/cert";
								is_dir($dir) or mkdir($dir, 0777, true);
								$file = $dir . '/' . $r_acaoId . '.' . $pathinfo['extension'];
								$fileKey = $dir . '/' . $r_acaoId . '_key.pem';
								$fileCert = $dir . '/' . $r_acaoId . '._cert.pem';
								// Gera certificado
								$key = file_get_contents($arquivo['tmp_name']);
								$resp = openssl_pkcs12_read($key, $x509certdata, $form->campoValorEnviado('C004_Senha_Certificados'));
								if (empty($x509certdata['pkey'])) {
							    	echo gGeraAlertMsg('Erro ao enviar certificado', 'Senha do certificado é inválida!' . $x509certdata['pkey'] . ' - ' . $x509certdata['cert']);
							    	$error = true;
							    } else {
							    	// Copia arquivo
									if (file_exists($file)) { unlink($file); }
									if (file_exists($fileKey)) { unlink($fileKey); }
									if (file_exists($fileCert)) { unlink($fileCert); }
									move_uploaded_file($arquivo['tmp_name'], $file);
									file_put_contents($fileKey, $x509certdata['pkey']);
        							file_put_contents($fileCert, $x509certdata['cert']);
									// Guarda certificado no banco
									$cC004_Certificado_Digital = base64_encode($key);
									mysql_query("update C004 set C004_Certificado_Digital='{$cC004_Certificado_Digital}' where C004_Id='{$r_acaoId}'");
								}
							}
						}
						if (!$error) {
							// Fecha a janela, recarrega o parent (grid) e emite uma mensagem de alerta
							$alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
							echo <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
										setTimeout(function() {
											{$alert}
										}, 150);
	  								});
									$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
									// O FW atualiza a linha do grid automaticamente, somente recarregar se não for grid
									if ($('#{$g['divIdParent']}').find('table.grid').length === 0) {
										divRefresh('{$g['divIdParent']}', true);
									}
								} </script></div>
EOT;
						}
						if ($arquivoLogo['error'] == UPLOAD_ERR_OK) {
							// Gravar o arquivo no disco
							$pathinfo = pathinfo($arquivoLogo['name']);
							$pathinfo['extension'] = empty($pathinfo['extension']) ? '.tmp' : $pathinfo['extension'];
							$dir = "{$g['pathDados']}imagens/logo";
							//$dir = "{$g['pathDados']}di";
							is_dir($dir) or mkdir($dir, 0777, true);
							$file = $dir . '/' . $r_acaoId . '.' . $pathinfo['extension'];
							move_uploaded_file($arquivoLogo['tmp_name'], $file);
							//gRedimensionaImagem($arquivoLogo, 200, $file);
							// copiar tambem para o hardness, usado na hora de abrir o logo na impressão da expedicao de mercadorias
							$fileGambi=$confUsuario['pathRaiz'].'../hardness/'.$g['pathDados'].'imagens/'.$arquivoLogo['name'];
							copy($file, $fileGambi);
							// copiar tambem para o hardness, usado na hora de abrir o logo na impressão da danfe
							$fileGambi=$confUsuario['pathRaiz'].'../hardness/'.$g['pathDados'].'imagens/'.$r_acaoId . '_nfe.' . $pathinfo['extension'];
							copy($file, $fileGambi);
						}
					}
				} else {
					// Insert
					$arquivoLogo = $form->campoValorEnviado('uploadArquivoLogo');
					$form->campoSetarValor('C004_Logo_Marca', $arquivoLogo['name'], true);
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						if ($arquivoLogo['error'] == UPLOAD_ERR_OK) {
							// Gravar o arquivo no disco
							$pathinfo = pathinfo($arquivoLogo['name']);
							$pathinfo['extension'] = empty($pathinfo['extension']) ? '.tmp' : $pathinfo['extension'];
							$dir = "{$g['pathDados']}imagens/logo";
							//$dir = "{$g['pathDados']}di";
							is_dir($dir) or mkdir($dir, 0777, true);
							$file = $dir . '/' . $g['mysqlLastId'] . '.' . $pathinfo['extension'];
							move_uploaded_file($arquivoLogo['tmp_name'], $file);
						}
						// Fecha a janela, recarrega o parent (grid), emite uma mensagem de alerta e abre o registro para alteração (o que acabou de ser incluido)
						$acaoId = $g['mysqlLastId'];
						$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								// Fecha a janela, recarrega o grid e abre a alteração
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										//abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}' , unique(), '', 'Editar Empresas - ID: ' + '{$acaoId}', '/cad/cad031/content/cad031ContentCadastroEmpresas/', '&acaoId=' + encodeURIComponent('{$acaoId}'), [700,650]);
										{$alert}
									}, 150);
	  							});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								divRefresh('{$g['divIdParent']}', true);
							} </script></div>
EOT;
					}
				}
			}
		} else {
			// Carrega os valores caso seja um update
			if (!empty($r_acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $r_acaoId);
				// Mostra erros de formulário uteis ao desenvolvedor
				if ($processado !== true) {
					reset($form->formArray['campos']);
					if ($tab == key($form->formArray['campos'])) {
						echo $processado;
					}
				}
			}
		}

		/**
		 * PARTE 4: Gerar o formulário
		 */
		$gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');	
	break;

	case 'cad031FormCadastroLogoB2B':
		global $g;
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
		
		/**
		* PARTE 2: Definição do formulário
		*/
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($r_formcad024FormPrincipal)) ? $r_formcad024FormPrincipal['formId'] : $r_formId;
		} else {
			$formId = uniqid();
		}
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'C004';
		$form_campo_pk = 'C004_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad031FormCadastroLogoB2BEnviar';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad031/form/cad031FormCadastroLogoB2B/';
		$form_editor = array(
			'logomarca' => '{"uploadArquivoCelularB2B":{"top":53,"left":1.53125,"width":328,"hidden":0},"uploadArquivoLogo":{"top":7,"left":51.640625,"width":328,"hidden":0},"cad031FormCadastroLogoB2BEnviar":{"top":28,"left":544,"width":null,"hidden":0}}'
		);
		
		$form = new Formulario('cad031FormCadastroLogoB2B');
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				/*'logomarca' => array(
					// Campo normal (do banco)
					'uploadArquivoLogo' => array('name' => 'uploadArquivoLogo', 'reject'=>true, 'label' => 'Upload Logomarca', 'type' => 'file', 'length' => array(10,80)),
				),*/
				
				'logomarca' => array(
					// Campo normal (do banco)
					'uploadArquivoLogo' => array('name' => 'uploadArquivoLogo', 'reject'=>true, 'label' => 'Upload Logo B2B', 'type' => 'file', 'length' => array(10,80)),
					'C004_Logo_Marca_B2B' => array('name' => 'C004_Logo_Marca_B2B', 'label' => 'Nome Arquivo', 'type' => 'hidden', 'length' => array(10,80), 'uppercase'=>false),
					'uploadArquivoCelularB2B' => array('name' => 'uploadArquivoCelularB2B', 'reject'=>true, 'label' => 'Upload imagem celular B2B', 'type' => 'file', 'length' => array(10,80)),
					'C004_Imagem_Celular_B2B' => array('name' => 'C004_Imagem_Celular_B2B', 'label' => 'Nome Arquivo', 'type' => 'hidden', 'length' => array(10,80), 'uppercase'=>false),
				),
			),
		);	
		$formArray['campos']['logomarca']['cad031FormCadastroLogoB2BEnviar'] = array('name' => 'cad031FormCadastroLogoB2BEnviar', 'type'=>'button', 'reload' => false, 'value' => 'Cadastrar', 'align' => 'right', 'reject' => true);
		// $formArray['campos']['logomarca']['cad031FormCadastroLogoB2BEnviar'] = array('name' => 'cad031FormCadastroLogoB2BEnviar', 'type'=>'button', 'reload' => false, 'value' => 'Cadastrar', 'align' => 'right', 'reject' => true);
		//$formArray['campos']['logomarca']['C004_Logo_Marca_B2B'] = array('name' => 'C004_Logo_Marca_B2B', 'label' => 'Nome Arquivo', 'type' => 'hidden', 'length' => array(10,80), 'uppercase'=>false);
			
		
		$form->definirFormularioArray($formArray);

		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		* E carregamento dos valores (se não foi enviado parametro acaoId)
		*/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				// Insert ou Update?
				$sqlC004 = mysql_query("SELECT C004_Id,C004_Logo_Marca_B2B,C004_Imagem_Celular_B2B FROM C004 WHERE C004_Id='$r_acaoId'");
				log("SELECT C004_Id,C004_Logo_Marca_B2B,C004_Imagem_Celular_B2B FROM C004 WHERE C004_Id='$r_acaoId'");
				$C004 = mysql_fetch_assoc($sqlC004);
				if (!empty($r_acaoId)) {
					// Update
					$arquivoLogo = $form->campoValorEnviado('uploadArquivoLogo');
					$ArquivoCelularB2B = $form->campoValorEnviado('uploadArquivoCelularB2B');

					if(empty($arquivoLogo['name'])){
						$arquivoLogo['name']=$C004['C004_Logo_Marca_B2B'];
					}

					if (empty($ArquivoCelularB2B['name'])) {
						$ArquivoCelularB2B['name']=$C004['C004_Imagem_Celular_B2B'];
					}

					$pathinfo = pathinfo($arquivoLogo['name']);
					$pathinfo['extension'] = empty($pathinfo['extension']) ? '.tmp' : $pathinfo['extension'];
					$pathinfoCelular = pathinfo($ArquivoCelularB2B['name']);
					$pathinfoCelular['extension'] = empty($pathinfoCelular['extension']) ? '.tmp' : $pathinfoCelular['extension'];

					$imagemLogoB2B = 'B2B_' . $r_acaoId .'.' .$pathinfoCelular['extension'];
					$imagemCelularB2B = 'B2B_Celular_' . $r_acaoId . '.' . $pathinfoCelular['extension'];
					log("imagem logo:".$imagemLogoB2B);
					log("imagem celular:".$imagemCelularB2B);

					$form->campoSetarValor('C004_Imagem_Celular_B2B', $imagemCelularB2B, true);
					$form->campoSetarValor('C004_Logo_Marca_B2B', $imagemLogoB2B, true);

					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $r_acaoId, $form)) {
						// Após executar o insert, vamos gravar os arquivos (se enviado)
						// Fecha a janela, recarrega o parent (grid) e emite uma mensagem de alerta
						$alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										{$alert}
									}, 150);
								});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								// O FW atualiza a linha do grid automaticamente, somente recarregar se não for grid
								if ($('#{$g['divIdParent']}').find('table.grid').length === 0) {
									divRefresh('{$g['divIdParent']}', true);
								}
							} </script></div>
EOT;
						if ($arquivoLogo['error'] == UPLOAD_ERR_OK) {
							log("entrou para gravar no disco");
							// Gravar o arquivo no disco
							$dir = "{$g['pathDados']}imagens/logo";
							//$dir = "{$g['pathDados']}di";
							is_dir($dir) or mkdir($dir, 0777, true);
							// $file = $dir . '/B2B_' . $r_acaoId . '.' . $pathinfo['extension'];
							$file = $dir . '/' . $imagemLogoB2B;
							move_uploaded_file($arquivoLogo['tmp_name'], $file);
							$file = $dir . '/' . $imagemCelularB2B;
							move_uploaded_file($ArquivoCelularB2B['tmp_name'], $file);
							//gRedimensionaImagem($arquivoLogo, 200, $file);
							// copiar tambem para o hardness, usado na hora de abrir o logo na impressão da expedicao de mercadorias
							$fileGambi=$confUsuario['pathRaiz'].'../hardness/'.$g['pathDados'].'imagens/'.'B2B_'.$arquivoLogo['name'];
							copy($file, $fileGambi);
							// copiar tambem para o hardness, usado na hora de abrir o logo na impressão da danfe
							$fileGambi=$confUsuario['pathRaiz'].'../hardness/'.$g['pathDados'].'imagens/'.$r_acaoId . '_nfe.' . $pathinfo['extension'];
							copy($file, $fileGambi);
							$fileGambi = $confUsuario['pathRaiz'] . '../hardness/' . $g['pathDados'] . 'imagens/'. 'B2B_Celular_'.$ArquivoCelularB2B['name'];
							copy($file, $fileGambi);
						}
					}
				} else {
					// Insert
					$arquivoLogo = $form->campoValorEnviado('uploadArquivoLogo');
					$ArquivoCelularB2B = $form->campoValorEnviado('uploadArquivoCelularB2B');
					$form->campoSetarValor('C004_Logo_Marca_B2B', $arquivoLogo['name'], true);
					$form->campoSetarValor('C004_Imagem_Celular_B2B', $ArquivoCelularB2B['name'], true);
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						if ($arquivoLogo['error'] == UPLOAD_ERR_OK) {
							// Gravar o arquivo no disco
							$pathinfo = pathinfo($arquivoLogo['name']);
							$pathinfo['extension'] = empty($pathinfo['extension']) ? '.tmp' : $pathinfo['extension'];
							$dir = "{$g['pathDados']}imagens/logo";
							//$dir = "{$g['pathDados']}di";
							is_dir($dir) or mkdir($dir, 0777, true);
							$file = $dir . '/' . $g['mysqlLastId'] . '.' . $pathinfo['extension'];
							move_uploaded_file($arquivoLogo['tmp_name'], $file);
						}
						// Fecha a janela, recarrega o parent (grid), emite uma mensagem de alerta e abre o registro para alteração (o que acabou de ser incluido)
						$acaoId = $g['mysqlLastId'];
						$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								// Fecha a janela, recarrega o grid e abre a alteração
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										//abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}' , unique(), '', 'Editar Empresas - ID: ' + '{$acaoId}', '/cad/cad031/content/cad031ContentCadastroEmpresas/', '&acaoId=' + encodeURIComponent('{$acaoId}'), [700,650]);
										{$alert}
									}, 150);
								});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								divRefresh('{$g['divIdParent']}', true);
							} </script></div>
EOT;
					}
				}
			}
		} else {
			// Carrega os valores caso seja um update
			if (!empty($r_acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $r_acaoId);
				// Mostra erros de formulário uteis ao desenvolvedor
				if ($processado !== true) {
					reset($form->formArray['campos']);
					if ($tab == key($form->formArray['campos'])) {
						echo $processado;
					}
				}
			}
		}

		/**
		* PARTE 4: Gerar o formulário
		*/
		$gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');	
		
	break;
	
	case 'cad031FormCadastroObservacoes':
		global $g;
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
		
		/**
		 * PARTE 2: Definição do formulário
		 */
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($r_formcad024FormPrincipal)) ? $r_formcad024FormPrincipal['formId'] : $r_formId;
		} else {
			$formId = uniqid();
		}
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'C004';
		$form_campo_pk = 'C004_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad031FormRodapeEnviar';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad031/form/cad031FormCadastroObservacoes/';
		// Editor
		$form_editor = array(
			'rodape' => '{"C004_Observacao_Rodape_Orcamento_":{"top":7,"left":16,"height":122,"width":698,"hidden":0},"C004_Observacao_Rodape_Pedido_":{"top":168,"left":16,"height":122,"width":681,"hidden":0},"cad031FormRodapeEnviar":{"top":327,"left":112,"width":null,"hidden":0}}'
		);
		
		$form = new Formulario('cad031FormCadastroObservacoes');
		// Definição do Formulário
		$formArray = array(
			'campos' => array(		
				'rodape' => array(
					'C004_Observacao_Rodape_Orcamento_' => array('name' => 'C004_Observacao_Rodape_Orcamento_', 'label' => 'Observação Rodapé <br>Orçamento', 'type' => 'textarea', 'length' => array(7,75), 'reject' => true, 'uppercase' => false),
					'C004_Observacao_Rodape_Orcamento' => array('name' => 'C004_Observacao_Rodape_Orcamento', 'type' => 'hidden', 'uppercase' => false),
					'C004_Observacao_Rodape_Pedido_' => array('name' => 'C004_Observacao_Rodape_Pedido_', 'label' => 'Observação Rodapé <br>Pedido', 'type' => 'textarea', 'length' => array(7,75), 'reject' => true),
					'C004_Observacao_Rodape_Pedido' => array('name' => 'C004_Observacao_Rodape_Pedido', 'type' => 'hidden', 'uppercase' => false),
				),
			),
		);	
		$formArray['campos']['rodape']['cad031FormRodapeEnviar'] = array('name' => 'cad031FormRodapeEnviar', 'type'=>'button', 'reload' => false, 'value' => 'Enviar', 'align' => 'right', 'reject' => true);
		//$formArray['campos']['logomarca']['C004_Logo_Marca'] = array('name' => 'C004_Logo_Marca', 'label' => 'Nome Arquivo', 'type' => 'hidden', 'length' => array(10,80), 'uppercase'=>false);
			
		
		$form->definirFormularioArray($formArray);
		echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					var myEditor = new nicEditor({
						iconsPath : '/hardness3/static/img/nicEditorIcons.gif', 
						buttonList: ['fontSize','bold','italic','underline','left','center','right','justify','strikeThrough','subscript','superscript','hr','html','forecolor','upload']
					}).panelInstance('C004_Observacao_Rodape_Pedido_');
					myEditor.addEvent('blur', function() {
						if (typeof(myEditor.instanceById('C004_Observacao_Rodape_Pedido_')) != 'undefined') {
							$('#C004_Observacao_Rodape_Pedido').val(myEditor.instanceById('C004_Observacao_Rodape_Pedido_').getContent());
						}
					});
					setTimeout(function() {
						$('.nicEdit-panelContain').css({'border': '1px solid #4297D7', 'background': 'url("/hardness3/static/img/jquery.ui/ui-bg_gloss-wave_55_5c9ccc_500x100.png") repeat-x scroll 50% 50% #5C9CCC'});
						$('.nicEdit-main').height(70).css('overflow', 'auto');
						if (myEditor.instanceById('C004_Observacao_Rodape_Pedido_') != 'undefined') {
							myEditor.instanceById('C004_Observacao_Rodape_Pedido_').setContent($('#C004_Observacao_Rodape_Pedido').val());
						}
					}, 100);
					
					var myEditor2 = new nicEditor({
						iconsPath : '/hardness3/static/img/nicEditorIcons.gif', 
						buttonList: ['fontSize','bold','italic','underline','left','center','right','justify','strikeThrough','subscript','superscript','hr','html','forecolor','upload']
					}).panelInstance('C004_Observacao_Rodape_Orcamento_');
					myEditor2.addEvent('blur', function() {
						if (typeof(myEditor2.instanceById('C004_Observacao_Rodape_Orcamento_')) != 'undefined') {
							$('#C004_Observacao_Rodape_Orcamento').val(myEditor2.instanceById('C004_Observacao_Rodape_Orcamento_').getContent());
						}
					});
					setTimeout(function() {
						$('.nicEdit-panelContain').css({'border': '1px solid #4297D7', 'background': 'url("/hardness3/static/img/jquery.ui/ui-bg_gloss-wave_55_5c9ccc_500x100.png") repeat-x scroll 50% 50% #5C9CCC'});
						$('.nicEdit-main').height(70).css('overflow', 'auto');
						if (myEditor2.instanceById('C004_Observacao_Rodape_Orcamento_') != 'undefined') {
							myEditor2.instanceById('C004_Observacao_Rodape_Orcamento_').setContent($('#C004_Observacao_Rodape_Orcamento').val());
						}
					}, 100);
			    } </script></div>
EOT;
		/**
		 * PARTE 3: Processamento do formulário (se foi enviado)
		 * E carregamento dos valores (se não foi enviado parametro acaoId)
		 */
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
				// Form sem erros
			} else if ($formCodigo == 2) {
				// Insert ou Update?
				
				if (!empty($r_acaoId)) {
					// Update
					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $r_acaoId, $form)) {
						// Imprime uma mensagem e fecha a janela
						$alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										{$alert}
									}, 150);
  								});
								// O FW atualiza a linha do grid automaticamente, somente recarregar se não for grid
								if ($('#{$g['divIdParent']}').find('table.grid').length === 0) {
									divRefresh('{$g['divIdParent']}', true);
								}
							} </script></div>
EOT;
					}
				} else {
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						// Imprime uma mensagem e fecha a janela
						$alert = gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
								setTimeout(function() {
									{$alert}
								}, 150);
  							});
							divRefresh('{$g['divIdParent']}', true);
						} </script></div>
EOT;
					}
				}
			}
		} else {
			// Carrega os valores caso seja um update
			if (!empty($r_acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $r_acaoId);
				$form->campoSetarValor('C004_Observacao_Rodape_Orcamento_', utf8_decode(htmlentities($form->campoValor('C004_Observacao_Rodape_Orcamento'))));
				$form->campoSetarValor('C004_Observacao_Rodape_Pedido_', utf8_decode(htmlentities($form->campoValor('C004_Observacao_Rodape_Pedido'))));
				// mostra erros de formulário uteis ao desenvolvedor
				if ($processado !== true) {
					reset($form->formArray['campos']);
					if ($tab == key($form->formArray['campos'])) {
						echo $processado;
					}
				}
			}
		}

		/**
		 * PARTE 4: Gerar o formulário
		 */
		$gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');	
	break;

	case 'cad031FormAvisosIndex':
		global $g;

		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
		$acaoId = isset($_REQUEST['acaoId']) ? $_REQUEST['acaoId'] : false;
		$C004_Id = isset($_REQUEST['C004_Id']) ? $_REQUEST['C004_Id'] : false;
		
		/**
		 * PARTE 2: Definição do formulário
		 */
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}

		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'C032';
		$form_campo_pk = 'C032_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad031FormAvisosIndexBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad031/form/cad031FormAvisosIndex/';
		// A lista de empresas não indica empresa. Sem identificador, o campo fica
		// de fora do SQL e a coluna grava nulo. String vazia quebra o insert.
		$semEmpresa = ($C004_Id === false || $C004_Id === '');

		// Editor
		$form_editor = array(
			'geral' => '{"C032_Flag_Ativo":{"top":7,"left":8.265625,"width":57,"hidden":0},"C032_Descricao":{"top":30,"left":41.734375,"height":39,"width":632,"hidden":0},"C032_Aviso_":{"top":76,"left":63.75,"height":150,"width":639,"hidden":0}}',
		);

		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'C032_C004_Id' => array('name' => 'C032_C004_Id', 'label' => '', 'type' => 'hidden', 'value' => $C004_Id, 'reject' => $semEmpresa),
					'C032_Aviso_' => array('name' => 'C032_Aviso_', 'label' => 'Aviso', 'type' => 'textarea', 'length' => array(7,75), 'reject' => true, 'uppercase' => false),
					'C032_Aviso' => array('name' => 'C032_Aviso', 'type' => 'hidden', 'uppercase' => false),
					'C032_Flag_Ativo' => array('name' => 'C032_Flag_Ativo', 'label'=>'Aviso está ativo?', 'type' => 'select','select' => 
						array (
							array('title' => 'Sim', 'value' => 'S', 'selected' => true),
							array('title' => 'Não', 'value' => 'N'),
						)
					),
					'C032_Descricao' => array('name' => 'C032_Descricao', 'label' => 'Descrição', 'type' => 'textarea', 'length' => array(10,20)),
				)
			)
		);
		
		$formArray['campos']['geral']['divReload'] 	= array('name' => 'divReload', 'label'=>'', 'type' => 'hidden','value' => @$r_divReload, 'reject' => true);	

		$form = new Formulario('cad031FormAvisosIndex');
		$form->definirFormularioArray($formArray);
		 		echo <<<EOT
	 		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
	 			var myEditor = new nicEditor({
	 				iconsPath : '/hardness3/static/img/nicEditorIcons.gif', 
	 				buttonList: ['fontSize','bold','italic','underline','left','center','right','justify','strikeThrough','subscript','superscript','hr','html','forecolor']
	 			}).panelInstance('C032_Aviso_');
	 			myEditor.addEvent('blur', function() {
	 				if (typeof(myEditor.instanceById('C032_Aviso_')) != 'undefined') {
	 					$('#C032_Aviso').val(myEditor.instanceById('C032_Aviso_').getContent());
	 				}
	 			});
	 			function copiarAvisoParaCampo() {
	 				if (typeof(myEditor.instanceById('C032_Aviso_')) != 'undefined') {
	 					$('#C032_Aviso').val(myEditor.instanceById('C032_Aviso_').getContent());
	 				}
	 			}
	 			setTimeout(function() {
	 				$('.nicEdit-panelContain').css({'border': '1px solid #4297D7', 'background': 'url("/hardness3/static/img/jquery.ui/ui-bg_gloss-wave_55_5c9ccc_500x100.png") repeat-x scroll 50% 50% #5C9CCC'});
	 				$('.nicEdit-main').height(70).css('overflow', 'auto');
	 				$('.nicEdit-main').height(70).css('background-color', '#FFF');
	 				$('.nicEdit-main').css('background-color', '#B7B7B7');
	 				if (myEditor.instanceById('C032_Aviso_') != 'undefined') {
	 					myEditor.instanceById('C032_Aviso_').setContent($('#C032_Aviso').val());
	 				}
	 				$('#cad031FormAvisosIndexBtn').on('mousedown', copiarAvisoParaCampo);
	 			}, 100);
	 	    } </script></div>
EOT;

		/**
		 * PARTE 3: Processamento do formulário (se foi enviado)
		 * E carregamento dos valores (se não foi enviado parametro acaoId)
		 */
		// Com identificador, a empresa da requisição entra na gravação. O gerador
		// lê o campo enviado; sem esta cópia o identificador não vira coluna.
		if (!$semEmpresa && isset($_REQUEST['cad031FormAvisosIndex']) && is_array($_REQUEST['cad031FormAvisosIndex'])) {
			$_REQUEST['cad031FormAvisosIndex']['C032_C004_Id'] = $C004_Id;
		}
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				// Texto do aviso. A imagem fica no formulário cad031FormAvisosImagem.
				if (!empty($r_acaoId)) {
					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $r_acaoId, $form)) {
						$alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
						echo <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
										setTimeout(function() {
										}, 150);
										{$alert}
	  								});
									$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
									if ($('#{$g['divIdParent']}').find('table.grid').length === 0) {
										divRefresh('{$g['divIdParent']}', true);
									}
							} </script></div>
EOT;
					}
				} else if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
					echo <<<EOT
		                            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								        $("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
			                                divRefresh('{$g['divIdParent']}', true);
		                            } </script></div>
EOT;
				}
			}
		} else {
			// Carrega os valores caso seja um update
			if (!empty($r_acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $r_acaoId);
				$form->campoSetarValor('C032_Aviso_', utf8_decode(htmlentities($form->campoValor('C032_Aviso'))));
				// Mostra erros de formulário uteis ao desenvolvedor
				if ($processado !== true) {
					reset($form->formArray['campos']);
					if ($tab == key($form->formArray['campos'])) {
						echo $processado;
					}
				}
			}
		}

		/**
		 * PARTE 4: Gerar o formulário
		 */
		$gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="cad031FormAvisosIndexBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

	break;

	case 'cad031FormAvisosImagem':
		global $g;

		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
		$acaoId = isset($_REQUEST['acaoId']) ? $_REQUEST['acaoId'] : false;
		$C004_Id = isset($_REQUEST['C004_Id']) ? $_REQUEST['C004_Id'] : false;
		
		/**
		 * PARTE 2: Definição do formulário
		 */
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}

		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'C032';
		$form_campo_pk = 'C032_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad031FormAvisosImagensBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad031/form/cad031FormAvisosImagem/';

		// Editor
		$form_editor = array(
			'geral' => '{"uploadAviso":{"top":30,"left":4.78125,"width":245,"hidden":0},"tamanhoImagem":{"top":4,"left":-1,"width":null,"hidden":0},"cad031FormAvisosImagensBtn":{"top":28,"left":336,"width":null,"hidden":0}}',
		);

		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'tamanhoImagem' => array('name' => 'tamanhoImagem', 'label'=>'Tamanho da imagem: Largura: 630px - Altura: 140px','type' => 'titulo', 'reject' => true),
					'C032_Imagem' => array('name' => 'C032_Imagem', 'label' => 'Upload Aviso', 'type' => 'hidden', 'uppercase'=>false, 'length' => array(10,80)),
				)
			)
		);
		$formArray['campos']['geral']['cad031FormAvisosImagensBtn'] = array('name' => 'cad031FormAvisosImagensBtn', 'type' => 'submit', 'reload' => false, 'value' => 'Incluir' , 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['geral']['uploadAviso'] = array('name' => 'uploadAviso', 'label' => 'Imagem Aviso', 'type' => 'file', 'length' => array(24, 24), 'reject' => true, 'uppercase' => false);

		$form = new Formulario('cad031FormAvisosIndex');
		$form->definirFormularioArray($formArray);

		/**
		 * PARTE 3: Processamento do formulário (se foi enviado)
		 * E carregamento dos valores (se não foi enviado parametro acaoId)
		 */
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			// Form sem erros
			} else if ($formCodigo == 2) {
				// Insert ou Update?
				$arquivo = $form->campoValorEnviado('uploadAviso');
				if ($arquivo['error'] == UPLOAD_ERR_OK) {
					$form->campoSetarValor('C032_Imagem', $arquivo['name'], true);
					if (!empty($r_acaoId)) {
						// Update
						if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $r_acaoId, $form)) {
							
							$pathinfo = pathinfo($arquivo['name']);
							$pathinfo['extension'] = empty($pathinfo['extension']) ? '.tmp' : $pathinfo['extension'];
							$dir = "{$g['pathDados']}imagens/avisos";
							is_dir($dir) or mkdir($dir, 0777, true);
							$file = $dir . '/'. $arquivo['name'];
							echo $file;
							move_uploaded_file($arquivo['tmp_name'], $file);

							$alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
					
							echo <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
										setTimeout(function() {
										}, 150);
										{$alert}
	  								});
									$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
									// O FW atualiza a linha do grid automaticamente, somente recarregar se não for grid
									if ($('#{$g['divIdParent']}').find('table.grid').length === 0) {
										divRefresh('{$g['divIdParent']}', true);
									}
							} </script></div>
EOT;
						}	
					} 
				}
			}
		} 

		/**
		 * PARTE 4: Gerar o formulário
		 */
		$gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');

	break;

	case 'cad031FormAvisosPerfil':
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;

		/**
		 * PARTE 2: Definição do formulário
		 */
		// Este mesmo form será exibido em multiplas tabs ou não?
		$formMultiTab = false;
		if ($formMultiTab) {
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'C036';
		$form_campo_pk = 'C036_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad031FormAvisosPerfil_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad031/form/cad031FormAvisosPerfil/';

		$form_editor = array(
            'geral' => '{"C036_Data_Inicial":{"top":76,"left":20.625,"width":344,"hidden":0},"C036_C004_Id":{"top":7,"left":31.4375,"width":351,"hidden":0},"C036_Data_Final":{"top":99,"left":28.296875,"width":344,"hidden":0},"C036_C021_Id":{"top":30,"left":49.421875,"width":351,"hidden":0},"C036_C007_Id":{"top":53,"left":37.09375,"width":351,"hidden":0}}',
        );

		// novo formulário
		$form = new Formulario('cad031FormAvisosPerfil');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'C036_C032_Id' => array('name' => 'C036_C032_Id', 'label' => 'Aviso', 'value' => "{$r_C032_Id}", 'type' => 'hidden'),
					'C036_C021_Id' => array('name' => 'C036_C021_Id', 'label' => 'Perfil', 'type' => 'select', 'select' =>
						gGeraSelect('C021', 'C021_Id', 'C021_Nome_Perfil', 'ORDER BY C021_Nome_Perfil', true)
					),
					'C036_C007_Id' => array('name' => 'C036_C007_Id', 'label' => 'Usuário', 'type' => 'select', 'select' =>
						gGeraSelect('C007', 'C007_Id', 'C007_Nome', 'ORDER BY C007_Nome', true)
					),
					'C036_C004_Id' => array('name' => 'C036_C004_Id', 'label' => 'Empresa', 'type' => 'select', 'select' =>
						gGeraSelect('C004', 'C004_Id', 'C004_Nome_Abreviado', 'ORDER BY C004_Id', true)
					),
					'C036_Data_Inicial' => array('name' => 'C036_Data_Inicial', 'label'=>'Data inicial', 'type' => 'data'),
					'C036_Data_Final' => array('name' => 'C036_Data_Final', 'label'=>'Data final', 'type' => 'data'),
				)
			)
		);

		$form->definirFormularioArray($formArray);

		/**
		 * PARTE 3: Processamento do formulário (se foi enviado)
		 * 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		 */

		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
				// Form sem erros
			} else if ($formCodigo == 2) {
				// Insert ou Update?
				if (!empty($acaoId)) {
					// Update
					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
						// Imprime uma mensagem e fecha a janela
						$alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
									{$alert}
									}, 150);
  								});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								// O FW atualiza a linha do grid automaticamente, somente recarregar se não for grid
								if ($('#{$g['divIdParent']}').find('table.grid').length === 0) {
									divRefresh('{$g['divIdParent']}', true);
								}
							} </script></div>
EOT;
					}
				} else {
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						// Imprime uma mensagem e fecha a janela
						$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
									{$alert}
									}, 150);
  								});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								divRefresh('{$g['divIdParent']}', true);
							} </script></div>
EOT;
					}
				}
			}
		} else {
			// Carrega os valores caso seja um update
			if (!empty($acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $acaoId);
				// mostra erros de formulário uteis ao desenvolvedor
				if ($processado !== true) {
					reset($form->formArray['campos']);
					if ($tab == key($form->formArray['campos'])) {
						echo $processado;
					}
				}
			}
		}


		/**
		* PARTE 4: Gerar o formulário
		*/
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="cad031FormAvisosPerfil_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

}
?>
