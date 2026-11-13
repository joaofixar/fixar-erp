<?php
namespace hardness;
require_once("bibliotecas/classes/EST016.php");
$EST016 = new EST016();

require_once("bibliotecas/classes/CAD002.php");
$CAD002 = new CAD002();

require_once('prog/est/est016/form_func-js.php');

require_once("old/lib/funcoes.php");

/**
 * Ações
 */
switch ($g['r'][3]) {
	/**
	* Form Principal
	* Chamado em: est016tab02
	*/
	case 'est016form01':
		/**
			* PARTE 1: Variavéis envidas para o formulário
			*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$r_T224_Id = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;

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
		
		// busca o ID do cliente pra carregar os contatos, caso seja edição
		$idCliente='99999999999';
		if ($acaoId>0){
			$sql = "SELECT T224_D024_Id,D020_D022_Id,D022_D036_Id, D024_D022_Id, D024_D020_Id FROM T224 LEFT JOIN D024 ON D024_Id=T224_D024_Id LEFT JOIN D020 ON D020_Id=D024_D020_Id LEFT JOIN D022 ON D022_Id=D020_D022_Id LEFT JOIN D036 ON D036_Id=D022_D036_Id WHERE T224_Id = '{$acaoId}'";
			$result = mysql_query($sql);
			$aD024 = mysql_fetch_assoc($result);		
			$idTransportadoraCidade=$aD024['D020_D022_Id'];
			$idCliente=$aD024['T224_D024_Id'];
			$idTransportadora=$aD024['D024_D022_Id'];
            $idTipoTransp=$aD024['D022_D036_Id'];
		} 		

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'T224';
		$form_campo_pk = 'T224_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('est016FormCabecalhoBtnAdd', 'est016FormCabecalhoBtnCln', 'est016FormCabecalhoBtnNew');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est016/form/est016form01/';

		if($g['C031']['habilitaComprasImportacao'] == 'S'){
			$sqlFlagImport    = mysql_query("select T224A_Flag_Importacao from T224A where T224A_T224_Id='{$r_T224_Id}'");
			$resultFlagImport = mysql_fetch_array($sqlFlagImport);
			$exibirImp = 'select';
			$exibirMoeda = 'moeda';

			if($resultFlagImport['T224A_Flag_Importacao'] == 'S'){
				$desabilita = false;
				$exibirText = 'text';
				$exibirPesq =  'pesquisa';
				$meusCampos = ', "T224A_Dolar_Cambial":{"top":220,"left":21,"width":212,"hidden":1}, "T224A_D024_Id":{"top":280,"left":21,"width":212,"hidden":1}, "T224A_Flag_Importacao":{"top":240,"left":21,"width":212,"hidden":1}, "T224A_Numero_Processo_Importacao":{"top":260,"left":21,"width":212,"hidden":1}';
			}else{
				$desabilita  = true;
				$exibirText = 'hidden';
				$exibirPesq =  'hidden';
			}

		}else{
			$exibirImp = 'hidden';
			$exibirMoeda = 'hidden';
			$exibirText = 'hidden';
			$exibirPesq =  'hidden';
		}
		
		$form_editor = array(
			'geral' => '{"T224_D024_Id":{"top":6.98529052734375,"left":88.7714467048645,"width":267.039216,"hidden":0},"T224_D013_Id":{"top":6.98529052734375,"left":483.7622685432434,"width":287,"hidden":0},"est016FormContatos":{"top":4.993865966796875,"left":831.985321521759,"width":null,"hidden":0},"T224_D006_Id":{"top":0,"left":0,"width":358.960784,"hidden":1},"T224_C007_Id_Vendedor_Interno":{"top":29.993865966796875,"left":67.6776967048645,"width":287,"hidden":0},"T224_Data_Emissao":{"top":29.993865966796875,"left":480.43811082839966,"width":280.039216,"hidden":0},"T224_D116_Id":{"top":0,"left":0,"width":49.960784000000004,"hidden":1},"T224_Flag_ACP":{"top":52.98713684082031,"left":34.35968065261841,"width":288,"hidden":0},"T224_Flag_Destino_Produto":{"top":52.98713684082031,"left":485.2634892463684,"width":288,"hidden":0},"T224_Nome_Transportadora":{"top":0,"left":0,"width":120,"hidden":1},"T224_Prazo_Entrega":{"top":75.99571228027344,"left":39.53737688064575,"width":280.039216,"hidden":0},"T224_Prazos_Pagamentos":{"top":75.99571228027344,"left":417.24877977371216,"width":280.039216,"hidden":0},"T224_Data_Entrega":{"top":98.98898315429688,"left":44.837624073028564,"width":280.039216,"hidden":0},"T224_Flag_Dividido":{"top":0,"left":0,"width":66.96078399999999,"hidden":1},"T224_Observacao":{"top":121.99755859375,"left":66.97303819656372,"height":62.039216,"width":280.039216,"hidden":0},"T224_Observacao_Comercial":{"top":121.99755859375,"left":436.02941942214966,"height":62.039216,"width":280.039216,"hidden":0},"T224_Pedido_Ecommerce":{"top":0,"left":0,"width":120,"hidden":1},"T224_Codigo_Rastreio":{"top":0,"left":0,"width":120,"hidden":1},"T224_C007_Id_Embalagem":{"top":0,"left":0,"width":192.960784,"hidden":1},"T224_Observacao_Nota_Fiscal":{"top":190.99264526367188,"left":67.35600328445435,"height":62.039216,"width":280.039216,"hidden":0},"T224_Observacao_1":{"top":190.99264526367188,"left":456.8627324104309,"height":62.039216,"width":280.039216,"hidden":0},"T224_Flag_Totalizar_Peso":{"top":0,"left":0,"width":66.96078399999999,"hidden":1},"T224_Quantidade_Volumes":{"top":0,"left":0,"width":127,"hidden":1},"T224_Valor_Despesas_Acessorias":{"top":259.98773193359375,"left":17.43259859085083,"width":280.039216,"hidden":0},"T224_Flag_Transporte_Coleta":{"top":259.98773193359375,"left":468.09130907058716,"width":288,"hidden":0},"T224_Peso_Liquido":{"top":0,"left":0,"width":127,"hidden":1},"T224_Flag_Frete":{"top":282.996337890625,"left":97.77880144119263,"width":288,"hidden":0},"T224_D109_Id":{"top":282.996337890625,"left":475.3216862678528,"width":287,"hidden":0},"T224_Custo_Frete":{"top":0,"left":0,"width":80,"hidden":1},"T224_Valor_Frete":{"top":305.9895935058594,"left":69.65380144119263,"width":280.039216,"hidden":0},"T224_Hora_Coleta":{"top":305.9895935058594,"left":464.7671513557434,"width":280.039216,"hidden":0},"T224_Percentual_Comissao":{"top":0,"left":0,"width":104,"hidden":1},"T224_D036_Id":{"top":328.9981689453125,"left":100.91912126541138,"width":289,"hidden":0},"T224_D022_Id":{"top":351.99139404296875,"left":47.24264574050903,"width":265.039216,"hidden":0},"T224_Ordem_Compra":{"top":0,"left":0,"width":280,"hidden":1},"TransportadoraPadrao":{"top":375,"left":9.712009906768799,"width":288,"hidden":0},"button":{"top":372.9932861328125,"left":415.99264574050903,"width":null,"hidden":0},"est016FormCabecalhoBtnCln":{"top":349.9693603515625,"left":655.9895939826965,"width":null,"hidden":0},"est016FormCabecalhoBtnAdd":{"top":349.9693603515625,"left":735.998749256134,"width":null,"hidden":0},"T224_Peso_Bruto":{"top":0,"left":0,"width":127,"hidden":1},"T224_D163_Id":{"top":0,"left":0,"width":294.960784,"hidden":1},"T224_Especie":{"top":0,"left":0,"width":127,"hidden":1},"T224_Flag_Expedicao":{"top":0,"left":0,"width":110.96078399999999,"hidden":1}' . $meusCampos . '}'
		);

		//Buscas destinos, foi centralizado nesta classe e vai valer para todos os locais
		$destinoProdutos   = $CAD002->retornaDestinoProdutos(false, 'C');

		// novo formulário
		$form = new Formulario('form_est016_form1');

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)

		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T224A_Dolar_Cambial' => array('name' => 'T224A_Dolar_Cambial', 'label'=>'Cotação Dólar Cambial','type' => $exibirMoeda, 'length' => array(20,10)),
					'T224A_D024_Id' => array('name' => 'T224A_D024_Id','label'=>'Pessoa Importação', 'type' => $exibirPesq, 'pesquisa' => array('D024', 'D024_Id', 'D024_Nome_Empresa', "where D024_Flag_Pessoa_Fisica_Juridica = 'I'")),
					'T224A_Flag_Importacao' => array('name' => 'T224A_Flag_Importacao', 'label'=>'Importação?', 'type' => $exibirImp, 'select' => 
											array(
												array('title' => 'Não', 'value' => 'N'),
												array('title' => 'Sim', 'value' => 'S')
											),
										),		
					'T224A_Numero_Processo_Importacao' => array('name' => 'T224A_Numero_Processo_Importacao','label'=>'Nr. PI','type' =>$exibirText , 'disabled'=>$desabilita,'length' => array(10,10),'type' => $exibirText ),
					'T224_D024_Id' => array('name' => 'T224_D024_Id','label'=>'Cliente', 'type' => 'pesquisa','extra' => "onchange=\"carregaCFOP_Contatos(this, '{$g['divId']}');\"", 'pesquisa' => array('D024','D024_Id','D024_Id,D024_Nome_Empresa,D020_Nome_Cidade,D018_UF,D024_Cpf,D024_Cnpj','left join D020 on D020_Id=D024_D020_Id left join D018 on D018_Id=D024_D018_Id WHERE D024_Flag_Fornecedor="S" and D024_Flag_Ativo="S" order by D024_Nome_Empresa','(%1) %2 [%3 - %4]'), 'length' => array(50,30)),
					'T224_D013_Id' => array('name' => 'T224_D013_Id', 'label' => 'Contato', 'type' => 'select',
						'select' => gGeraSelect('D013','D013_Id','D013_Nome_Contato,D013_DDD_Telefone_1,D013_Telefone_1,D013_E_Mail',"where D013_D024_Id=$idCliente AND D013_Flag_Ativo = 'S'",true),
						'selectRefresh' => array('D013','D013_Id','D013_Nome_Contato,D013_DDD_Telefone_1,D013_Telefone_1,D013_E_Mail',"where D013_D024_Id=$idCliente AND D013_Flag_Ativo = 'S'",true),
					),
					'est016FormContatos' => array('name' => 'est016FormContatos', 'type'=>'button', 'reload' => false, 'value' => 'Incluir', 'reject' => true),
					'T224_D006_Id' => array('name' => 'T224_D006_Id', 'label'=>'CFOP','type' => 'select', 'select' => 
        				gGeraSelect('D006','D006_Id','D006_Codigo_CFOP,D006_Descricao',' WHERE D006_Flag_Inativo!="S" AND D006_Flag_Departamento="COMERCIAL" ORDER BY D006_Codigo_CFOP',true),
					),											
					'T224_Data_Emissao' => array('name' => 'T224_Data_Emissao','label'=>'Emissão','type' => 'data', 'length' => array(20,10)),
					'T224_Data_Entrega' => array('name' => 'T224_Data_Entrega','label'=>'Data de Entrega','type' => 'data', 'length' => array(20,10)),
					'T224_C007_Id_Vendedor_Interno' => array('name' => 'T224_C007_Id_Vendedor_Interno', 'label'=>'Comprador','type' => 'select', 'select' => 
						gGeraSelect('C007','C007_Id','C007_Login','WHERE C007_Flag_Vendedor!="" AND C007_Ativo="S"',true),
					),
					'T224_Percentual_Comissao' => array('name' => 'T224_Percentual_Comissao', 'label'=>'Comissão %','value'=>'0,00','type' => 'moeda', 'length' => array(20,10)),
					'T224_Flag_ACP' => array('name' => 'T224_Flag_ACP', 'label'=>'Tipo Documento','type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => 0),
							array('title' => 'NF', 'value' => 1),
							array('title' => 'Romaneio', 'value' => 2),
							array('title' => 'ACP', 'value' => 3),
							array('title' => 'Cupom Fiscal', 'value' => 4),
							array('title' => 'NF D1', 'value' => 5),
							array('title' => 'NFe', 'value' => 6, 'selected' => true),
							array('title' => 'NFSe', 'value' => 7),							
						)
					),
					'T224_Prazo_Entrega' => array('name' => 'T224_Prazo_Entrega', 'label'=>'Prazo de Entrega','type' => 'text', 'length' => array(53,40)),
					'T224_Prazos_Pagamentos' => array('name' => 'T224_Prazos_Entregas', 'label'=>'Forma de Pagamento','type' => 'text', 'length' => array(53,40)),
					'T224_D116_Id' => array('name' => 'T224_D116_Id', 'label' => 'Veículo','type' => 'select',
						'select'=> gGeraSelect('D116', 'D116_Id', 'D116_Placa,D133_Modelo,D134_Cor', "LEFT JOIN D133 on D133_Id=D116_D133_Id LEFT JOIN D134 on D134_Id=D116_D134_Id WHERE D116_D024_Id=$idCliente", true),
						'selectRefresh' => array('D116', 'D116_Id', 'D116_Placa,D133_Modelo,D134_Cor', "LEFT JOIN D133 on D133_Id=D116_D133_Id LEFT JOIN D134 on D134_Id=D116_D134_Id WHERE D116_D024_Id=$idCliente", true)
					),
					'T224_Ordem_Compra' => array('name' => 'T224_Ordem_Compra', 'label'=>'Ordem de Compra','type' => 'text', 'length' => array(53,30)),
					//'tituloObservacoes' => array('name' => 'tituloObservacoes', 'label'=>'Observações','type' => 'titulo', 'reject' => true),
					'T224_Observacao' => array('name' => 'T224_Observacao', 'label'=>'Fornecedor','type' => 'textarea','extra' => 'title="Esta observação será visualizada pelo cliente (Não será impressa na DANFE)"', 'length' => array(3,50)),
					'T224_Observacao_Comercial' => array('name' => 'T224_Observacao_Comercial', 'label'=>'Comercial Interna','type' => 'textarea','extra' => 'title="Observação de uso interno da área comercial (Não será impressa na DANFE)"', 'length' => array(3,50)),
					'T224_Observacao_Nota_Fiscal' => array('name' => 'T224_Observacao_Nota_Fiscal', 'label'=>'Nota Fiscal','type' => 'textarea','extra' => 'title="Esta observação será impressa na DANFE"', 'length' => array(3,50)),
					'T224_Observacao_1' => array('name' => 'T224_Observacao_1', 'label'=>'Almox.:','type' => 'textarea','extra' => 'title="Observação de uso interno da área de estoque (Não será impressa na DANFE)"', 'length' => array(3,50)),
					'T224_Flag_Frete' => array('name' => 'T224_Flag_Frete','label'=>'Frete', 'type' => 'select',
						'select' => array(
							array('title' => '0-EMITENTE', 'value' => '0'),
							array('title' => '1-DESTINATÁRIO', 'value' => '1'),
							array('title' => '2-TERCEIROS', 'value' => '2'),
							array('title' => '9-SEM FRETE', 'value' => '9'),
						),
					),
					'T224_Valor_Frete' => array('name' => 'T224_Valor_Frete', 'label'=>'Valor Frete','value'=>'0,00','type' => 'moeda', 'length' => array(20,20)),
					'T224_D022_Id' => array('name' => 'T224_D022_Id', 'label' => 'Transportadora', 'type' => 'pesquisa', 'pesquisa' => array('D022','D022_Id','D022_Nome_Empresa,D020_Nome_Cidade,D018_UF','left join D020 on D020_Id=D022_D020_Id left join D018 on D018_Id=D022_D018_Id'),'extra' => "onchange=\"carregaTipoTransp(this, '{$g['divId']}')\"", 'length' => array(50,90)),
					'T224_D036_Id' => array('name' => 'T224_D036_Id', 'label' => 'Tipo', 'type' => 'select', 'select' =>
						gGeraSelect('D036','D036_Id','D036_Tipo_Transporte','',true),
					),
					'TransportadoraPadrao' => array('name' => 'TransportadoraPadrao', 'label' => 'Transportadora Padrão', 'type' => 'select','reject' =>true ,'disabled' => true,
						'select' => gGeraSelect('D022','D022_Id','D022_Nome_Empresa,D020_Nome_Cidade,D018_UF',"left join D020 on D020_Id=D022_D020_Id left join D018 on D018_Id=D022_D018_Id WHERE D022_Id='{$idTransportadora}' OR D022_Id = '{$idTransportadoraCidade}'",false), 
					),
					'button' => array('name' => 'button', 'type' => 'button','value' =>'Usar Padrão','reload' => false,'extra' => "onclick='copiaTransportadora({$idTipoTransp})'",'reject'=>true),
					'nada' => array('name' => 'nada', 'label' => 'Transportadora', 'type' => 'hidden', 'reject'=>true, 'length' => array(30,30)),
					'T224_Flag_Revenda_Consumidor' => array('name' => 'T224_Flag_Revenda_Consumidor', 'label'=>'T224_Flag_Revenda_Consumidor','type' => 'hidden'
						//'select' => 
							//array(
								//array('title' => '...', 'value' => 0, 'selected' => true),
								//array('title' => 'Revenda', 'value' => 1),
								//array('title' => 'Consumidor atacado', 'value' => 2),
								//array('title' => 'Consumidor varejo', 'value' => 3),
							//)
					),
					'T224_Flag_Expedicao' => array('name' => 'T224_Flag_Expedicao', 'label'=>'Expedição','type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => 0),
							array('title' => 'Comercial', 'value' => 1),
							array('title' => 'Balcão', 'value' => 2),
						)
					),
					'T224_Quantidade_Volumes' => array('name' => 'T224_Quantidade_Volumes', 'label' => 'Volumes', 'type' => 'text'),
					'T224_Especie' => array('name' => 'T224_Especie', 'label' => 'Espécie', 'type' => 'text'),
					'T224_Peso_Liquido' => array('name' => 'T224_Peso_Liquido', 'label' => 'Peso Liquido', 'type' => 'text'),
					'T224_Peso_Bruto' => array('name' => 'T224_Peso_Bruto', 'label' => 'Peso Bruto', 'type' => 'text'),
					'T224_Nome_Transportadora' => array('name' => 'T224_Nome_Transportadora', 'label' => 'Nome transportadora', 'type' => 'text'),
					'T224_Flag_Dividido' => array('name' => 'T224_Flag_Dividido', 'label'=>'Dividido','type' => 'select','select' => 
						array(
							array('title' => 'Não', 'value' => 'N'),
							array('title' => 'Sim', 'value' => 'S'),
						)
					),
					'T224_Pedido_Ecommerce' => array('name' => 'T224_Pedido_Ecommerce', 'label' => 'N° Ecommerce', 'type' => 'text'),
					'T224_Codigo_Rastreio' => array('name' => 'T224_Codigo_Rastreio', 'label' => 'Codigo de Rastreio', 'type' => 'text'),
					'T224_C007_Id_Embalagem' => array('name' => 'T224_C007_Id_Embalagem', 'label' => 'Embalado por', 'type' => 'select', 'select' =>
						gGeraSelect('C007', 'C007_Id', 'C007_Primeiro_Nome', '', true)
					),
					'T224_Custo_Frete' => array('name' => 'T224_Custo_Frete', 'label' => 'Custo Frete', 'type' => 'moeda'),
					'T224_Flag_Totalizar_Peso' => array('name' => 'T224_Flag_Totalizar_Peso', 'label'=>'Totalizar Peso','type' => 'select','select' => 
						array(
							array('title' => 'Não', 'value' => 'N'),
							array('title' => 'Sim', 'value' => 'S'),
						)
					),
					'T224_D109_Id' => array('name' => 'T224_D109_Id', 'label'=>'Motorista','type' => 'select','select' => gGeraSelect('D109', 'D109_Id', 'D109_Nome', "WHERE D109_Funcao = 'MOTORISTA'",true)),
					'T224_Hora_Coleta' => array('name' => 'T224_Hora_Coleta', 'label' => 'Hora Coleta', 'type' => 'tempo'),
					'T224_Flag_Transporte_Coleta' => array('name' => 'T224_Flag_Transporte_Coleta', 'label' => 'Tipo coleta', 'type' => 'select', 'select' => 
						array(
							array('title' => '...', 'value' => ''),
							array('title' => 'MOTO', 'value' => '1'),
							array('title' => 'CARRO', 'value' => '2')
						)
					),
					'T224_D163_Id' => array('name' => 'T224_D163_Id', 'label' => 'Status Ordem Compra', 'type' => 'select', 'select' =>
						gGeraSelect('D163','D163_Id','D163_Status','',true),
					),
					'T224_Valor_Despesas_Acessorias' => array('name' => 'T224_Valor_Despesas_Acessorias', 'label'=>'Valor desp. acessoria','value'=>'0,00','type' => 'moeda', 'length' => array(20,20)),
					'T224_Flag_Destino_Produto' => array('name' => 'T224_Flag_Destino_Produto','label'=>'Destino', 'type' => 'select', 
						'select' => $destinoProdutos
					),
				)
			)
		);
		/** Botão Limpar / Novo Registro
		if (!empty($acaoId)) {
			$formArray['campos']['geral']['est016FormCabecalhoBtnNew'] = array('name' => 'est016FormCabecalhoBtnNew', 'type'=>'submit', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right; margin-top:20px;', 'reject' => true);
		}
		*/
		$formArray['campos']['geral']['est016FormCabecalhoBtnCln'] = array('name' => 'est016FormCabecalhoBtnCln', 'type'=>'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right; margin-top:20px;', 'reject' => true);
		$formArray['campos']['geral']['est016FormCabecalhoBtnAdd'] = array('name' => 'est016FormCabecalhoBtnAdd', 'type'=>'button', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right; margin-top:20px;', 'reject' => true);
		$form->definirFormularioArray($formArray);
		
		$sql = "SELECT T224_D024_Id FROM T224 WHERE T224_Id = '{$r_T224_Id}';";
		$resultado = mysql_query($sql);
		$row = mysql_fetch_array($resultado);
		echo <<<EOT
		    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		    	$('#est016FormContatos').unbind('click');
				$('#est016FormContatos').unbind('click').bind('click', function(data) {
					var id = '{$row['T224_D024_Id']}';
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Cliente/Fornecedor - ID: ' + id, '/cad/cad001/content/cad001contentCadastro/', '&acaoId=' + encodeURIComponent(id) + '&tabela=D024' + '&tab=contatos', [950,553]);
					return false;
				});
		    } </script></div>
EOT;

		/**
			* Na maioria dos casos não será necessário alterar nada daqui para baixo
			* Somente em casos especiais, para alterar pequenos detalhes ou modificações do visual
			*/


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
						
						$T224 = mysql_query("SELECT T224_Flag_Lista_Preco, D022_Nome_Empresa FROM T224 LEFT JOIN D022 ON D022_Id=T224_D022_Id WHERE T224_Id = '{$acaoId}'");
 						$rT224 = mysql_fetch_array($T224);
						// Imprime uma mensagem e fecha a janela
						mysql_query("UPDATE T224 SET T224_Nome_Transportadora='{$rT224['D022_Nome_Empresa']}' WHERE T224_Id = '{$acaoId}'");
						echo gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!');
						$lista = $form->campoValorEnviado('T224_Flag_Lista_Preco');
						//Verifica se a lista de preço foi alterada
						if(($rT224['T224_Flag_Lista_Preco'] != $lista) && !empty($lista)){
							$EST016->alteraLista($acaoId, $lista);
							echo gGeraAlertMsg('Atenção', 'A lista de preço foi alterada, os valores de tabela dos produtos serão modificados.');	
						}					
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								//divRefresh('{$r_divIdMain}', true);
								divRefresh($('.contentCabecalhoLayout').parent().attr('id'));
							} </script></div>
EOT;
					}
				} else {
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						// Imprime uma mensagem e fecha a janela
						$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!');
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									{$alert}
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
		/*
		// Definição dos botões
		$botoesMid = '';
		$botoesEsq = '';
		$botoesDir = '';
		// $botoesDir .= '<button type="submit" id="content_form1_btn">Enviar</button>';
		// $botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
		*/

		break;

		/**
		 * Formulário Produtos
		 */
	case 'est016FormMoeda':
		$tab = isset($_REQUEST['tab']) ? $_REQUEST['tab'] : 'geral';
		$T224_Id = isset($_REQUEST['T224_Id']) ? (int) $_REQUEST['T224_Id'] : (isset($_REQUEST['acaoId']) ? (int) $_REQUEST['acaoId'] : 0);
		$formId = uniqid();
		$T224A = mysql_query("SELECT T224A_Id FROM T224A WHERE T224A_T224_Id = '{$T224_Id}'");
		$mT224A = mysql_fetch_assoc($T224A);
		if (empty($mT224A['T224A_Id'])) {
			mysql_query("INSERT INTO T224A (T224A_T224_Id) VALUES ('{$T224_Id}')");
			$T224A_Id = mysql_insert_id();
		} else {
			$T224A_Id = $mT224A['T224A_Id'];
		}

		$form_tabela = 'T224A';
		$form_campo_pk = 'T224A_Id';
		$enviar_btnId = 'est016FormMoedaBtn';
		$enviar_acaoHref = '/est/est016/form/est016FormMoeda/';

		$form_editor = array(
			'geral' => '{"T224A_D044_Id":{"top":7,"left":73,"width":150,"hidden":0},"T224A_Cotacao_Moeda":{"top":30,"left":14,"width":110,"hidden":0},"est016FormMoedaBtn":{"top":74,"left":144,"width":null,"hidden":0}}',
		);

		$form = new Formulario('est016FormMoeda');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T224A_D044_Id' => array(
						'name' => 'T224A_D044_Id',
						'label' => 'Moeda',
						'type' => 'select',
						'select' => gGeraSelect('D044', 'D044_Id', 'D044_Moeda,D044_Simbolo', 'ORDER BY D044_Moeda')
					),
					'T224A_Cotacao_Moeda' => array(
						'name' => 'T224A_Cotacao_Moeda',
						'label' => 'Cotação da Moeda',
						'type' => 'moeda',
						'align' => 'right',
						'length' => array(10, 10)
					),
					'est016FormMoedaBtn' => array(
						'name' => 'est016FormMoedaBtn',
						'type' => 'submit',
						'reload' => false,
						'value' => 'Alterar',
						'align' => 'right',
						'style' => 'float:right;',
						'reject' => true
					),
				)
			)
		);
		$form->definirFormularioArray($formArray);

		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			} else if ($formCodigo == 2 && !empty($T224A_Id)) {
				$D044_Id = (int) $form->campoValorEnviado('T224A_D044_Id');
				$cotacao = gCorrigeNumeroInverte($form->campoValorEnviado('T224A_Cotacao_Moeda'));
				if ($D044_Id > 1 && $cotacao <= 0) {
					echo gGeraAlertMsg('Cotação obrigatória', 'Informe uma cotação maior que zero para a moeda estrangeira.');
				} else if ($g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $T224A_Id, $form)) {
					echo gGeraAlertMsg('Operação OK', 'Moeda alterada com sucesso!');
					echo <<<EOT
					<div class="divHidden"><script type="text/javascript">if (typeof jQuery != 'undefined') { divRefresh('{$g['divId']}'); }</script></div>
EOT;
				}
			}
		} else if (!empty($T224A_Id)) {
			$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $T224A_Id);
			if ($processado !== true) {
				reset($form->formArray['campos']);
				if ($tab == key($form->formArray['campos'])) {
					echo $processado;
				}
			}
		}

		$gerado = gGeraCamposForm($form, $tab, $T224_Id, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']);
		$g['smarty']->assign('form_tabela', $form_tabela);
		echo $gerado['pagina'];
		$g['smarty']->assign('formId', $formId);
		$g['smarty']->display('formEditor.tpl');
	break;

	case 'est016form02':
		$g['smarty']->assign('navSql', "T224_C004_Id = '{$g['empresaAtual']}'");
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']}').data('sqlPesquisa', '[T224_Id, T224_Data_Emissao as Data, T224_Data_Entrega as Data, D024_Nome_Empresa, D024_Nome_Fantasia]');
				$('#{$g['divId']}').data('labelPesquisa', '[Pedido, Emissão, Entrega, Razão Social, Nome Fantasia]');
				$('#{$g['divId']}').data('sqlExtra', '[LEFT JOIN D024 ON T224_D024_Id = D024_Id]');
			} </script></div>
EOT;
		gHabilitaNavegacao($g['divId'], 'EST016', 'est016form02');		
		
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$T224_Id = (isset($r_T224_Id)) ? $r_T224_Id : false;
		
		$T224=mysql_query("SELECT T224_D024_Id FROM T224 WHERE T224_Id='{$r_T224_Id}'");
		$mT224=mysql_fetch_array($T224);

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
		$form_tabela = 'T225';
		$form_campo_pk = 'T225_Id';
        $form_parent = 'T224_Id';
        $form_parent_valor = $T224_Id;
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('est016FormProdutosBtnAdd', 'est016FormProdutosBtnCln', 'est016FormProdutosBtnNew');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est016/form/est016form02/';

		if($g['C031']['habilitaComprasImportacao'] == 'S'){
			$sqlFlagImport    = mysql_query("select T224A_Flag_Importacao from T224A where T224A_T224_Id='{$r_T224_Id}'");
			$resultFlagImport = mysql_fetch_array($sqlFlagImport);

			if($resultFlagImport['T224A_Flag_Importacao'] == 'S'){
				$meusCampos = ', "T225A_Valor_Dolar_Unitario":{"top":220,"left":21,"width":212,"hidden":1}'; 
			}else{
				$meusCampos = '';
			}
		}else{
			$meusCampos = '';
		}
        //form editor
        $form_editor = array(
            'geral' => '{"T225_Flag_Destino_Produto":{"top":99,"left":276.578125,"width":128,"hidden":0},"Procura_Produto":{"top":7,"left":35.359375,"width":122,"hidden":0},"Marca":{"top":7,"left":283.3125,"width":120,"hidden":0},"T225_D005_Id":{"top":7,"left":473.5,"width":95,"hidden":0},"T225_Descricao_Produto":{"top":30,"left":17.640625,"width":360,"hidden":0},"estoqueLiquido":{"top":30,"left":466.125,"width":88,"hidden":0},"T225_Aliquota_ICMS":{"top":30,"left":841,"width":24,"hidden":1},"T225_Quantidade":{"top":53,"left":9.65625,"width":72,"hidden":0},"T225_D037_Id":{"top":53,"left":160,"width":63,"hidden":0},"T225_Valor_Preco_Sem_Desconto_Unitario":{"top":53,"left":236.84375,"width":120,"hidden":0},"estoqueLoja":{"top":53,"left":463.078125,"width":88,"hidden":0},"T225_Aliquota_IPI":{"top":53,"left":848,"width":24,"hidden":1},"T225_Flag_Origem_Produto":{"top":76,"left":30.484375,"width":144,"hidden":0},"T225_Observacao_Entrega":{"top":76,"left":252.125,"width":120,"hidden":0},"estoqueTotal":{"top":76,"left":460.640625,"width":88,"hidden":0},"T225_ST_ICMS_Interno":{"top":76,"left":805,"width":24,"hidden":1},"T225_D022_Id":{"top":76,"left":927,"width":407,"hidden":1},"T225_Codigo_Substituto":{"top":99,"left":13.953125,"width":136,"hidden":0},"ordemCompra":{"top":99,"left":489.375,"width":88,"hidden":0},"T225_ICMS_Inter_Estadual":{"top":99,"left":817,"width":24,"hidden":1},"T225_Observacao":{"top":122,"left":14.578125,"width":360,"hidden":0},"estoqueFora":{"top":122,"left":461.25,"width":88,"hidden":0},"T225_ST_VA":{"top":122,"left":834,"width":40,"hidden":1},"T225_Percentual_Desconto":{"top":122,"left":959,"width":104,"hidden":1},"UltimoPrecoCompra":{"top":145,"left":31.078125,"height":62,"width":360,"hidden":0},"T225_Flag_ST":{"top":145,"left":839,"width":24,"hidden":1},"T225_T208_Id":{"top":145,"left":980,"width":42,"hidden":1},"T225_D006_Id":{"top":168,"left":824,"width":352,"hidden":1},"est016FormProdutosBtnCln":{"top":189,"left":480,"width":null,"hidden":0},"est016FormProdutosBtnAdd":{"top":189,"left":560,"width":null,"hidden":0},"T225_Hora_Entrega":{"top":191,"left":1051,"width":127,"hidden":1}' . $meusCampos . '}',
        );

		$pesquisaGrid = $EST016->pesquisaGridItemOC($T224_Id,$mT224);

        $sqlVeiculo = mysql_query("select T224_D024_Id,T224_D116_Id from T224 where T224_Id='{$r_T224_Id}'");
		$resultVeiculo = mysql_fetch_array($sqlVeiculo);
		// novo formulário
		$form = new Formulario('est016FormProdutosForm');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		
		if($g['C031']['habilitaComprasImportacao'] == 'S'){
			$sqlFlagImport    = mysql_query("select T224A_Flag_Importacao from T224A where T224A_T224_Id='{$r_T224_Id}'");
			$resultFlagImport = mysql_fetch_array($sqlFlagImport);
			if($resultFlagImport['T224A_Flag_Importacao'] == 'S'){
				$desabilitaU = false;
				$desabilitaR = true;
				$exibirMoeda = 'moeda';
			}else{
				$desabilitaU = true;
				$desabilitaR = false;
				$exibirMoeda = 'hidden';
			}

		}else{
			$desabilita = false;
			$exibirMoeda = 'hidden';
		}

		//Buscas destinos, foi centralizado nesta classe e vai valer para todos os locais
		$destinoProdutos   = $CAD002->retornaDestinoProdutos(false, 'C');

		$formArray = array(
			'campos' => array(
				'geral' => array(
					'Procura_Produto' => array('name' => 'Procura_Produto' , 'label' => 'Produto', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'length' => array(20,50), 'reject' => true),
					'Marca' => array('name' => 'Marca', 'label' => 'Marca', 'type' => 'text', 'length' => array(20,255), 'reject' => true, 'disabled' => true, 'restricao' => false),
					'T225_Codigo_Substituto' => array('name' => 'T225_Codigo_Substituto', 'label' => 'Código forn.', 'type' => 'text', 'length' => array(20,255), 'align' => 'right', 'style' => 'float:right;'),
					'T225_Descricao_Produto' => array('name' => 'T225_Descricao_Produto', 'label' => 'Descrição', 'type' => 'text', 'length' => array(64, 120)),
					'T225_Quantidade' => array('name' => 'T225_Quantidade', 'label' => 'Quantidade', 'type' => 'moeda', 'length' => array(20, 30)),
					'T225_D037_Id' => array('name' => 'T225_D037_Id', 'label' => '', 'type' => 'select', 'align' => 'right', 'style' => 'float:right;', 'select' => 
		            	gGeraSelect('D037', 'D037_Id', 'D037_Unidade', '', true)
					),
					'T225_Valor_Preco_Sem_Desconto_Unitario' => array('name' => 'T225_Valor_Preco_Sem_Desconto_Unitario', 'label' => 'Preço Unitário', 'type' => 'moeda', 'disabled'=>$desabilitaR,'length' => array(20, 30)),
					'T225A_Valor_Dolar_Unitario' => array('name' => 'T225A_Valor_Dolar_Unitario', 'label'=>'Preço Unitário Dolar','type' => $exibirMoeda, 'disabled' => $desabilitaU, 'length' => array(20,10)),
					'T225_Percentual_Desconto' => array('name' => 'T225_Percentual_Desconto', 'label' => '% Desconto', 'type' => 'moeda', 'length' => array(15, 30), 'reject'=>true),
					'T225_D005_Id' => array('name' => 'T225_D005_Id', 'label' => 'NCM', 'type' => 'select', 'readonly' => true, 'extra' => "onfocus='this.initialSelect = this.selectedIndex;' onchange='this.selectedIndex = this.initialSelect;'", 'align' => 'right', 'select' => 
                        gGeraSelect('D005', 'D005_Id', 'D005_Classificacao_Fiscal,SUBSTRING(D005_Observacao[[virgula]]1[[virgula]]20)', 'where D005_Flag_Ativo="S"', true)
                    ),
					'T225_Aliquota_IPI' => array('name' => 'T225_Aliquota_IPI', 'label' => 'IPI', 'align' => 'right', 'readonly'=>true, 'type' => 'moeda', 'length' => array(5,20)),
					'T225_Aliquota_ICMS' => array('name' => 'T225_Aliquota_ICMS', 'label' => 'ICM', 'align' => 'right', 'readonly'=>true, 'type' => 'moeda', 'length' => array(5,20)),
					'T225_Flag_ST' => array('name' => 'T225_Flag_ST', 'label' => 'ST?', 'type' => 'text', 'readonly'=>true, 'length' => array(5,20)),
					'T225_ST_VA' => array('name' => 'T225_ST_VA', 'label' => 'MVA', 'align' => 'right', 'readonly'=>true, 'type' => 'moeda', 'length' => array(5,20)),
					'T225_ST_ICMS_Interno' => array('name' => 'T225_ST_ICMS_Interno', 'label' => 'ICM Interno', 'readonly'=>true, 'align' => 'right', 'type' => 'moeda', 'length' => array(5,20)),
					'T225_ICMS_Inter_Estadual' => array('name' => 'T225_ICMS_Inter_Estadual', 'label' => 'ICM Inter', 'readonly'=>true, 'align' => 'right', 'type' => 'moeda', 'length' => array(5,20)),
					'T225_Aliquota_IPI' => array('name' => 'T225_Aliquota_IPI', 'label' => 'IPI', 'align' => 'right', 'readonly'=>true, 'type' => 'moeda', 'length' => array(5,20), 'style' => 'float:right;'),
					//'T225_Numero_Pedido_Compra' => array('name' => 'T225_Numero_Pedido_Compra', 'label' => 'Número OC', 'type' => 'moeda', 'length' => array(20, 30), 'align' => 'right', 'style' => 'float:right;'),
					//'T225_Item_Pedido_Compra' => array('name' => 'T225_Item_Pedido_Compra', 'label' => 'Item OC', 'type' => 'moeda', 'length' => array(20, 30)),
					//'T225_IPV' => array('name' => 'T225_IPV', 'label' => 'IPV', 'type' => 'moeda', 'length' => array(20, 30), 'align' => 'right', 'style' => 'float:right;'),
					'T225_Observacao' => array('name' => 'T225_Observacao', 'label' => 'Observação', 'type' => 'text', 'length' => array(64, 120)),
					'T225_Observacao_Entrega' => array('name' => 'T225_Observacao_Entrega', 'label' => 'Entrega dias', 'type' => 'text'),
					'T225_Flag_Origem_Produto' => array('name' => 'T225_Flag_Origem_Produto', 'label' => 'Origem', 'type' => 'select', 'select' => 
						array(
							array('title' => '...', 'value'=>''),
							array('title' => '0 - Nacional - exceto as indicadas nos códigos 3 a 5', 'value'=>'0'),
							array('title' => '1 - Estrangeira - Importação direta, exceto a indicada no código 6', 'value' =>'1'),
							array('title' => '2 - Estrangeira - Adquirida no mercado interno, exceto a indicada no código 7', 'value'=>'2'),
							array('title' => '3 - Nacional - mercadoria ou bem com Conteúdo de Importação superior a 40%', 'value' =>'3'),
							array('title' => '4 - Nacional - cuja produção tenha sido feita em conformidade com os processos produtivos básicos de que tratam as legislações citadas nos Ajustes', 'value'=>'4'),
							array('title' => '5 - Nacional - mercadoria ou bem com Conteúdo de Importação inferior ou igual a 40%', 'value' =>'5'),
							array('title' => '6 - Estrangeira - Importação direta, sem similar nacional, constante em lista da CAMEX', 'value'=>'6'),
							array('title' => '7 - Estrangeira - Adquirida no mercado interno, sem similar nacional, constante em lista da CAMEX', 'value' =>'7'),
							array('title' => '8 - Nacional - Mercadoria ou bem com Conteúdo de Importação superior a 70% (setenta por cento)', 'value' =>'8')
						)
					),
            		// Campos Hidden
					'T225_T224_Id' => array('name' => 'T225_T224_Id', 'label' => '', 'type' => 'hidden', 'value' => @$r_T224_Id),
					'T225_C004_Id' => array('name' => 'T225_C004_Id', 'label' => '', 'type' => 'hidden', 'value' => $g['empresaAtual']),
					'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
					'T225_D009_Id' => array('name' => 'T225_D009_Id', 'label' => 'T225_D009_Id', 'type' => 'hidden'),
					'D001_Id' => array('name' => 'D001_Id', 'label' => 'D001_Id', 'type' => 'hidden', 'reject'=>true),
					'UltimoPrecoCompra' => array('name' => 'UltimoPrecoCompra', 'label' => "<a href=\"#\" onclick=\"abrirJanela(event, divIdRootAberto(), gridPegarDivId(\$(this)), unique(), 'Produto', '.: Produto :.', '/cad/cad002/content/form2/', '&acaoId=' + encodeURIComponent(\$('#{$g['divId']} .itemForm-D001_Id').val()), [1000,600]); return false;\">Histórico</a>", 'type' => 'textarea', 'reject'=>true, 'readonly'=>true),
					'T225_D022_Id' => array('name' => 'T225_D022_Id', 'label' => 'Transportadora', 'type' => 'select', 'select' => 
		            	gGeraSelect('D022', 'D022_Id', 'D022_Nome_Empresa', '', true)
					),
					'T225_T208_Id' => array('name' => 'T225_T208_Id', 'label' => 'Rota', 'type' => 'select', 'select' => 
		            	gGeraSelect('T208', 'T208_Id', 'T208_Rota', '', true)
					),
					'T225_Hora_Entrega' => array('name' => 'T225_Hora_Entrega', 'label' => 'Hora Entrega', 'type' => 'tempo', 'length' => array(20, 30)),
					//'NcmProduto' => array('name' => 'NcmProduto', 'label' => 'NcmProduto','reject' => 'true', 'type' => 'hidden'),
					'T225_D006_Id' => array('name' => 'T225_D006_Id', 'label' => 'CFOP', 'readonly'=>true, 'restricao' => false,'type' => 'select',
						'select' => gGeraSelect('D006','D006_Id','D006_Codigo_CFOP,D006_Descricao',' WHERE D006_Flag_Inativo!="S" AND D006_Flag_Departamento="COMERCIAL" ORDER BY D006_Codigo_CFOP DESC', true),
					),
					'estoqueLiquido' => array('name' => 'estoqueLiquido', 'label' => "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Pedidos', '/crm/crm001/content/crm001ContentPedidosProduto/', '&acaoId=' + encodeURIComponent(\$('#{$g['divId']} .itemForm-T225_D009_Id').val()), [700,400]); return false;\">Est. Liq.</a>", 'type' => 'moeda', 'length' => array(5,20), 'reject' => true, 'readonly'=>true, 'style' => 'float:right; text-align:right;'),
					'estoqueLoja' => array('name' => 'estoqueLoja', 'label' => 'Est. Loja', 'type' => 'moeda', 'length' => array(5,20), 'reject' => true, 'readonly'=>true, 'style' => 'float:right; text-align:right;'),
					'estoqueTotal' => array('name' => 'estoqueTotal', 'label' => 'Est. Total', 'type' => 'moeda', 'length' => array(5,20), 'reject' => true, 'readonly'=>true, 'style' => 'float:right; text-align:right;'),
					'ordemCompra' => array('name' => 'ordemCompra', 'label' => "<a href=\"#\" onclick=\"abrirJanela(event, divIdRootAberto(), gridPegarDivId(\$(this)), unique(), 'Ordem de Compra', '.: Ordem de Compra :.', '/ven/ven001/content/ven001contentProdutoOC/', '&acaoId=' + encodeURIComponent(\$('#{$g['divId']} .itemForm-T225_D009_Id').val()), [700,400]); return false;\">OC</a>", 'type' => 'moeda', 'length' => array(5,20), 'style' => 'float:right; text-align:right;', 'reject' => true, 'readonly'=>true),
					'estoqueFora' => array('name' => 'estoqueFora', 'label' => "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Estoque Fora', '/cad/cad002/content/cad002ContentEstoqueExterno/', '&acaoId=' + encodeURIComponent(\$('#{$g['divId']} .itemForm-D001_Id').val()), [700,400]); return false;\">Est. Fora</a>", 'type' => 'moeda', 'length' => array(5,20), 'reject' => true, 'readonly'=>true, 'style' => 'float:right; text-align:right;'),
					'T225_Flag_Destino_Produto' => array('name' => 'T225_Flag_Destino_Produto','label'=>'Destino', 'type' => 'select', 
						'select' => $destinoProdutos
					),
					'T225A_Data_Entrega' => array('name' => 'T225A_Data_Entrega', 'label' => 'Data Entrega', 'type' => 'data'),
				)
			)
		);
		// Botão Limpar / Novo Registro
		if (!empty($acaoId)) {
			$formArray['campos']['geral']['est016FormProdutosBtnNew'] = array('name' => 'est016FormProdutosBtnNew', 'type'=>'submit', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		}
		$formArray['campos']['geral']['est016FormProdutosBtnAdd'] = array('name' => 'est016FormProdutosBtnAdd', 'type'=>'button', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['geral']['est016FormProdutosBtnCln'] = array('name' => 'est016FormProdutosBtnCln', 'type'=>'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);

		if($g['C031']['permiteOrdemCompraProdutoPreCadastro'] == 'S') {
			$formArray['campos']['geral']['est016BtnCadastrarProduto'] = array('name' => 'est016BtnCadastrarProduto', 'type'=>'button', 'reload' => false, 'value' => 'Pré-Cadastrar Produto', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		}
		$form->definirFormularioArray($formArray);

		echo <<<EOT
		    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		    	$('#est016BtnCadastrarProduto').unbind('click');
				$('#est016BtnCadastrarProduto').unbind('click').bind('click', function(data) {
					//var id = $('#{$g['divId']} #T218_D024_Id').val();
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Pré-cadastrar Produto', '/cad/cad002/content/cad002ContentPreCadastro/', '&T224_Id={$r_T224_Id}&divForm={$g['divId']}', [600,430]);											
					return false;
				});
		    } </script></div>
EOT;
		/**
		 * PARTE 3: Processamento do formulário (se foi efnviado)
		 * 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		 */

		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
				// Form sem erros
			} else if ($formCodigo == 2) {
				$T225_Id = false;
				$retorno = $EST016->incluirProduto($form);
				if ($retorno !== true) {
					echo gGeraAlertMsg('Erro', $retorno);	
				} else {
					echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								//divRefresh('{$form->campoValorEnviado('divReload')}', true);
								//divRefresh('{$r_divOutro}', true);
								divRefresh('{$g['divId']}', true);
								divRefresh('{$form->campoValorEnviado('divReload')}', true, false, false, '{$T225_Id}', 'T225_Id'); // grid
								divRefresh('{$r_divOutro}', true, false, 2); // totais
							} </script></div>
EOT;
					// Em caso de sucesso, limpa o form (a ação padrão é manter os dados) não precisar dar: divRefresh('{$g['divId']}', true);
					$form->limpar();
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
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk, false, $form_parent, $form_parent_valor);


		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');
			

		break;

		/**
		 * Formulário Serviços
		 */
	case 'est016form03':
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$r_T224_Id = isset($r_est016FormServicosForm) ? $r_est016FormServicosForm['T230_T224_Id'] : $r_T224_Id;

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
		$form_tabela = 'T230';
		$form_campo_pk = 'T230_Id';
        $form_parent = 'T224_Id';
        $form_parent_valor = $r_T224_Id;
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('est016FormServicosBtnAdd', 'est016FormServicosBtnCln', 'est016FormServicosBtnNew');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est016/form/est016form03/';

		//form editor
		$form_editor = array('geral' => '{"Procura_Tipo":{"top":7,"left":53,"width":248,"hidden":0},"T230_Titulo":{"top":30,"left":58,"width":264,"hidden":0},"est001FormVeiculosBtnNew":{"top":166,"left":384,"width":null,"hidden":0},"est016FormServicosBtnCln":{"top":189,"left":240,"width":null,"hidden":0},"T230_Descricao":{"top":53,"left":34,"width":264,"hidden":0},"est016FormServicosBtnAdd":{"top":189,"left":304,"width":null,"hidden":0},"T230_Valor_Unitario":{"top":122,"left":40,"width":72,"hidden":0},"T230_Tempo_Estimado":{"top":145,"left":6,"width":75,"hidden":0},"T230_D116_Id":{"top":168,"left":48,"width":269,"hidden":0}}');

		// Campo: Procura_Servico
		$pesquisaGridExtra = <<<EOT
			left join D049 on D049_D001_Id=D001_Id
			left join D009 on D009_D049_Id=D049_Id
			left join D082 on D082_Id=D049_D082_Id
			left join D037 on D037_Id=D001_D037_Id
			where D009_C004_Id = '{$g['empresaAtual']}'
			order by D001_Codigo_Produto
EOT;
		$pesquisaGrid = array(
			'D112',																								// tabela
			'D112_Id',																							// chave primaria
			'D112_Titulo, D112_Descricao',																		// campos
			'',																									// extras sql
			'Pesquisa de Serviço',																				// título da janela
			'Serviço,Descrição',																					// descrição dos campos
			'campoProcuraServicoPedido',																				// callback js
			$g['divId']																							// divId
		);
        $sqlVeiculo = mysql_query("select T224_D024_Id,T224_D116_Id from T224 where T224_Id='{$r_T224_Id}'");
		$resultVeiculo = mysql_fetch_array($sqlVeiculo);
		// novo formulário
		$form = new Formulario('est016FormServicosForm');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'Procura_Servico' => array('name' => 'Procura_Servico' , 'label' => 'Serviço', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'length' => array(50,30), 'reject' => true),
					'T230_T212_Id' => array('name' => 'T230_T212_Id','label'=>'Título','type' => 'select',
						'select' => gGeraSelect('T212', 'T212_Id', 'T212_Nome', '', true)
					),
					'T230_Titulo' => array('name' => 'T230_Titulo','type' => 'hidden'),
					'T230_D112_Id' => array('name' => 'T230_D112_Id','type' => 'hidden'),
					'T230_Descricao' => array('name' => 'T230_Descricao','label'=>'Descrição','type' => 'textarea', 'length' => array(2,46)),
					//'T230_Quantidade' => array('name' => 'T230_Quantidade','label'=>'Quantidade','type' => 'text', 'length' => array(10,8)),
					//'T230_Valor_Unitario' => array('name' => 'T230_Valor_Unitario','label'=>'Valor Unitário','type' => 'moeda', 'length' => array(15,10)),
					'T230_Valor_Unitario' => array('name' => 'T230_Valor_Unitario','label'=>'Valor Total','type' => 'moeda', 'length' => array(15,10)),
					'T230_Tempo_Estimado' => array('name' => 'T230_Tempo_Estimado','label'=>'Tempo Estimado','type' => 'text', 'length' => array(10,10)),
					'T230_D110_Id' => array('name' => 'T230_D110_Id','label'=>'Serviço','type' => 'hidden', 'length' => array(10,10)),
					//'T230_Hora_Inicio' => array('name' => 'T230_Hora_Inicio','label'=>'Hora Início','type' => 'text', 'length' => array(10,10)),
		            //'T230_Hora_Fim' => array('name' => 'T230_Hora_Fim','label'=>'Hora Fim','type' => 'text', 'length' => array(10,10)),
					'T230_T224_Id' => array('name' => 'T230_T224_Id', 'type' => 'hidden', 'value'=> @$r_T224_Id, 'length' => array(50,30)),
					'T230_Quantidade' => array('name' => 'T230_Quantidade','label'=>'Quantidade', 'value'=>'1', 'type' => 'hidden'),
					//'T230_Valor_Unitario' => array('name' => 'T230_Valor_Unitario','label'=>'Valor total', 'value'=>'1', 'type' => 'hidden'),
					'T230_Status' => array('name' => 'T230_Status','label'=>'Status', 'value'=>'P', 'type' => 'hidden'),
					'T230_Hora_Inicio' => array('name' => 'T230_Hora_Inicio','label'=>'Hora Início', 'value'=>'0000-00-00 00:00:00', 'type' => 'hidden'),
					'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
				)
			)
		);
		// Botão Limpar / Novo Registro
		if (!empty($acaoId)) {
			$formArray['campos']['geral']['est016FormServicosBtnNew'] = array('name' => 'est016FormServicosBtnNew', 'type'=>'submit', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		}
		$formArray['campos']['geral']['est016FormServicosBtnCln'] = array('name' => 'est016FormServicosBtnCln', 'type'=>'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['geral']['est016FormServicosBtnAdd'] = array('name' => 'est016FormServicosBtnAdd', 'type'=>'button', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$form->definirFormularioArray($formArray);

		$sql = "SELECT T224_D024_Id FROM T224 WHERE T224_Id = '{$r_T224_Id}';";
		$resultado = mysql_query($sql);
		$row = mysql_fetch_array($resultado);

		/**
		 * PARTE 3: Processamento do formulário (se foi efnviado)
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
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								divRefresh('{$form->campoValorEnviado('divReload')}', true);
								divSetarAcaoId('{$g['divId']}', '');
								divRefresh('{$g['divId']}', true);
							} </script></div>
EOT;
					}
				} else {
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						$retorno = $EST016->insereServico($form, $g['mysqlLastId']);
						$erro = ($retorno === true) ? '' : "alert('$retorno');";
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								divRefresh('{$form->campoValorEnviado('divReload')}', true);
								divRefresh('{$g['divId']}', true);
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
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk, false, $form_parent, $form_parent_valor);


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
		$botoesMid = '';
		$botoesEsq = '';
		$botoesDir = '';
		//$botoesDir .= '<button type="submit" id="content_form1_btn">Enviar</button>';
		//$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
			
		break;

		/**
		 * Formulário Materiais
		 */
	case 'est016form04':
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
		$form_tabela = 'T149';
		$form_campo_pk = 'T149_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('est016FormMateriaisBtnAdd', 'est016FormMateriaisBtnCln', 'est016FormMateriaisBtnNew');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est016/form/est016form04/';
		
			// Campo: Procura_Produto
			$pesquisaGridExtra = <<<EOT
				left join D049 on D049_D001_Id=D001_Id
				left join D009 on D009_D049_Id=D049_Id
				left join D082 on D082_Id=D049_D082_Id
				left join D037 on D037_Id=D001_D037_Id
				where (D009_C004_Id = '{$g['empresaAtual']}'
				  and D001_Flag_Pre_Cadastro!='S')
				group by D009_Id
				order by D001_Codigo_Produto
EOT;
			$pesquisaGrid = array(
				'D001',																								// tabela
				'D009_Id',																							// chave primaria
				'D001_Codigo_Produto,D001_Descricao_Produto,D082_Marca,D009_Quantidade_Estoque_Liquido',			// campos
				$pesquisaGridExtra,																					// extras sql
				'Pesquisa de Material',																				// título da janela
				'Código,Descrição,Marca,Estoque',																	// descrição dos campos
				'campoProcuraProdutoMaterial',																				// callback js
				$g['divId'],																						// divId
				'',//(isset($r_est001FormProdutosForm) ? $r_est001FormProdutosForm['T219_T218_Id'] : $r_T218_Id),		// parametro extra
				array(																								// parametros para cada campo do grid
					array('colunaAlinhar' => 'center'),
					array(),
					array(),
					array('callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right')
				)
			);			

		// novo formulário
		$form = new Formulario('form_fin002_form4');

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
			'editar_material' => array(
				'est016FormMateriaisPesquisa' => array('name' => 'est001FormMateriaisPesquisa' , 'label' => 'Material', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'length' => array(20,30), 'reject' => true),
				'Descricao_Produto' => array('name' => 'Descricao_Produto', 'label' => 'Descrição', 'type' => 'text', 'length' => array(64, 120),'reject' => true),
				'est016_Marca' => array('name' => 'est001_Marca', 'label' => 'Marca', 'type' => 'text', 'length' => array(20,255), 'reject' => true),			
		
				'T149_T230_Id' => array('name' => 'T149_T230_Id','label'=>'', 'type' => 'hidden','value' => @$r_T230_Id, 'length' => array(60,20)),
				'T149_D009_Id' => array('name' => 'T149_D009_Id', 'label' => 'T149_D009_Id', 'type' => 'hidden'),
				'T149_Quantidade' => array('name' => 'T149_Quantidade','label'=>'Quantidade', 'type' => 'text', 'length' => array(10,10)),
				'T149_D037_Id' => array('name' => 'T149_D037_Id', 'label'=>'Unidade', 'extra'=>'style="width:182px;"', 'type' => 'select', 'select'=>
						gGeraSelect('D037', 'D037_Id', 'D037_Unidade', 'order by D037_Unidade', true),
				),
				//'content_form4_btn' => array('name' => 'content_form4_btn', 'type'=>'submit','reload'=>false,'value'=>'Incluir','align' => 'right', 'reject'=>true),
				'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
		
		)
		)
		);
		
		// Botão Limpar / Novo Registro
		if (!empty($acaoId)) {
			 $formArray['campos']['editar_material']['est016FormMateriaisBtnNew'] = array('name' => 'est016FormMateriaisBtnNew', 'type'=>'button', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		}
		$formArray['campos']['editar_material']['est016FormMateriaisBtnCln'] = array('name' => 'est016FormMateriaisBtnCln', 'type'=>'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['editar_material']['est016FormMateriaisBtnAdd'] = array('name' => 'est016FormMateriaisBtnAdd', 'type'=>'submit', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
	

		$form->definirFormularioArray($formArray);

		/**
			* Na maioria dos casos não será necessário alterar nada daqui para baixo
			* Somente em casos especiais, para alterar pequenos detalhes ou modificações do visual
			*/


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
						// Atualiza o Grid
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								divRefresh('{$form->campoValorEnviado('divReload')}', true);
								divSetarAcaoId('{$g['divId']}', '');
								divRefresh('{$g['divId']}', true);
							} </script></div>
EOT;
					}
				} else {
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						// Atualiza o Grid
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								divRefresh('{$form->campoValorEnviado('divReload')}', true);
								divRefresh('{$g['divId']}', true);
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
		$g['smarty']->display('form.tpl');

		/**
			* Botões
			*/
		// Definição dos botões
		$botoesMid = '';
		$botoesEsq = '';
		$botoesDir = '';
		//$botoesDir .= '<button type="submit" id="content_form4_btn">Enviar</button>';
		//$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

		break;
			
	case 'est016form05':
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
		$form_tabela = 'T148';
		$form_campo_pk = 'T148_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('est016FormProcessosBtnAdd', 'est016FormProcessosBtnCln', 'est016FormProcessosBtnNew');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est016/form/est016form05/';

		// novo formulário
		$form = new Formulario('form_fin002_form5');

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
			'geral' => array(
			'T148_T230_Id' => array('name' => 'T148_T230_Id','label'=>'', 'type' => 'hidden','value' => @$r_T230_Id),
			'T148_Titulo' => array('name' => 'T148_Titulo','label'=>'Título', 'type' => 'text','length' => array(50,30)),
			'T148_Descricao' => array('name' => 'T148_Descricao','label'=>'Descrição', 'type' => 'textarea','length' => array(3,47)),
			//'content_form5_btn' => array('name' => 'content_form5_btn', 'type'=>'submit','reload'=>false,'value'=>'Incluir','align' => 'right', 'reject'=>true),
			'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
		
		)
		)
		);
		
			// Botão Limpar / Novo Registro
			if (!empty($acaoId)) {
				 $formArray['campos']['geral']['est016FormProcessosBtnNew'] = array('name' => 'est016FormProcessosBtnNew', 'type'=>'button', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
			}
			$formArray['campos']['geral']['est016FormProcessosBtnCln'] = array('name' => 'est016FormProcessosBtnCln', 'type'=>'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
			$formArray['campos']['geral']['est016FormProcessosBtnAdd'] = array('name' => 'est016FormProcessosBtnAdd', 'type'=>'submit', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		

		$form->definirFormularioArray($formArray);

		/**
			* Na maioria dos casos não será necessário alterar nada daqui para baixo
			* Somente em casos especiais, para alterar pequenos detalhes ou modificações do visual
			*/


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
								divRefresh('{$form->campoValorEnviado('divReload')}', true);
								divSetarAcaoId('{$g['divId']}', '');
								divRefresh('{$g['divId']}', true);
							} </script></div>
EOT;
					}
				} else {
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						// Atualiza o Grid
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								divRefresh('{$form->campoValorEnviado('divReload')}', true);
								divRefresh('{$g['divId']}', true);
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
		$g['smarty']->display('form.tpl');

		/**
			* Botões
			*/
		// Definição dos botões
		$botoesMid = '';
		$botoesEsq = '';
		$botoesDir = '';
		//$botoesDir .= '<button type="submit" id="content_form5_btn">Enviar</button>';
		//$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

		break;
		
		case 'est016FormProdutosEditar':
			/**
			* PARTE 1: Variavéis envidas para o formulário
			*/
			// Variável indica qual tab do form será exibida
			$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
			// Utilizada em UPDATE, é o ID do registro que está sendo editado
			$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;

			$T224 = mysql_query("SELECT T224_D024_Id, T224_Id from T225 LEFT JOIN T224 on T224_Id=T225_T224_Id where T225_Id='{$r_acaoId}'");
			$mT224 = mysql_fetch_array($T224);

			/**
			* PARTE 2: Definição do formulário
			*/
			// Este mesmo form será exibido em multiplas tabs ou não?
			$formMultiTab = true;
			if ($formMultiTab) {
				$formId = (isset($r_est016FormProdutosForm)) ? $r_est016FormProdutosForm['formId'] : $r_formId;
			} else {
				$formId = uniqid();
			}
			// A qual tabela este form está relacionada, e a chave primária da tabela
			$form_tabela = 'T225';
			$form_campo_pk = 'T225_Id';
			$form_parent_valor = $mT224['T224_Id'];

			// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
			$enviar_btnId = 'est016FormEditarProdutosBtn';
			// Form que será executado ao clicar no botão
			$enviar_acaoHref = '/est/est016/form/est016FormProdutosEditar/';

			if($g['C031']['habilitaComprasImportacao'] == 'S'){
			
				$sqlFlagImport    = mysql_query("select T224A_Flag_Importacao from T224A where T224A_T224_Id='{$r_T224_Id}'");
				$resultFlagImport = mysql_fetch_array($sqlFlagImport);
				if($resultFlagImport['T224A_Flag_Importacao'] == 'S'){
					$meusCampos = ', "T225A_Valor_Dolar_Unitario":{"top":220,"left":21,"width":212,"hidden":1}';
				}
				else{
					$meusCampos = '';
				}
			}else{
				$meusCampos = '';
			}

			//form editor
			$form_editor = array(
				'item' 		 => '{"Procura_Produto_Edicao":{"top":7,"left":53,"width":408,"hidden":0},"Marca":{"top":30,"left":53,"width":88,"hidden":0},"T225_D037_Id":{"top":30,"left":428.00006103515625,"width":51,"hidden":0},"T225_Descricao_Produto":{"top":52.933319091796875,"left":34,"width":424,"hidden":0},"T225_Quantidade":{"top":75.93331909179688,"left":28,"width":102,"hidden":0},"T225_IPV":{"top":76,"left":394.00006103515625,"width":102,"hidden":0},"T225_Valor_Custo_Unitario":{"top":99,"left":23,"width":102,"hidden":0},"T225_Valor_Preco_Sem_Desconto_Unitario":{"top":122,"left":16,"width":102,"hidden":0},"T225_D006_Id":{"top":144.99996948242188,"left":56,"width":432,"hidden":0},"T225_Observacao":{"top":167.99996948242188,"left":30,"width":424,"hidden":0},"T225_T208_Id":{"top":190.99996948242188,"left":68,"width":75,"hidden":1},"T225_D022_Id":{"top":213.99996948242188,"left":21,"width":212,"hidden":1}' . $meusCampos . '}',
				'ipi'  		 => '{"T225_D005_Id":{"top":7,"left":93,"width":175,"hidden":0},"T225_Aliquota_IPI":{"top":30,"left":99,"width":102,"hidden":0},"T225_Situacao_Tributaria_IPI":{"top":53,"left":19,"width":315,"hidden":0}}',
				'icms' 		 => '{"T225_Aliquota_ICMS":{"top":7,"left":87.9666748046875,"width":102,"hidden":0},"T225_Percentual_Reducao_ICMS":{"top":29.98333740234375,"left":70.9666748046875,"width":102,"hidden":0},"T225_D059_Id":{"top":52.98333740234375,"left":63.9666748046875,"width":288,"hidden":0},"T225_Flag_ST":{"top":75.98332214355469,"left":46.9666748046875,"width":58,"hidden":0},"T225_ST_ICMS_Interno":{"top":99.00001525878906,"left":50,"width":102,"hidden":0},"T225_ST_ICMS_Interno_MVA":{"top":99.00001525878906,"left":313.00006103515625,"width":102,"hidden":0},"T225_ICMS_Inter_Estadual":{"top":122.00001525878906,"left":18,"width":102,"hidden":0},"T225_ICMS_Inter_Estadual_MVA":{"top":122.00001525878906,"left":279.00006103515625,"width":102,"hidden":0},"T225_ST_VA":{"top":145.00001525878906,"left":130,"width":102,"hidden":0}}',
				'pis-cofins' => '{"T225_Aliquota_PIS":{"top":7,"left":109.9666748046875,"width":102,"hidden":0},"T225_Flag_Isento_PIS":{"top":29.98333740234375,"left":108.9666748046875,"width":58,"hidden":0},"T225_Situacao_Tributaria_PIS":{"top":52.98333740234375,"left":30,"width":288,"hidden":0},"T225_Aliquota_COFINS":{"top":75.98332214355469,"left":87.9666748046875,"width":102,"hidden":0},"T225_Flag_Isento_COFINS":{"top":99.00001525878906,"left":108.9666748046875,"width":58,"hidden":0},"T225_Situacao_Tributaria_COFINS":{"top":122.00001525878906,"left":8,"width":288,"hidden":0}}'
			);

		// Campo: Procura_Produto
		$pesquisaGridExtra = <<<EOT
			left join D049 on D049_D001_Id=D001_Id
			left join D041 on D041_D049_Id=D049_Id
			left join D009 on D009_D049_Id=D049_Id
			left join D082 on D082_Id=D049_D082_Id
			left join D037 on D037_Id=D001_D037_Id 
			LEFT JOIN D083 on D083_D001_Id=D001_Id AND D083_D024_Id='{$mT224['T224_D024_Id']}'
			where (D009_C004_Id = '{$g['empresaAtual']}'
			and D001_Flag_Ativo='S')
			group by D009_Id
			order by D001_Codigo_Produto ASC
EOT;
		$pesquisaGrid = array(
			'D001',																											              // tabela
			'D009_Id',																										              // chave primaria
			'D001_Codigo_Produto||D083_Codigo_Produto_Fornecedor||D001_Descricao_Produto||D082_Marca||D009_Preco_1||D009_Preco_2||D009_Preco_3||D009_Quantidade_Estoque||D009_Quantidade_Pedido(D009_Id,1) as Pedido||D009_Quantidade_Estoque_Liquido||D009_Id as D009Id||D001_Descricao_Ingles||D009_Id||D049_Id',	// campos
			$pesquisaGridExtra,																								              // extras sql
			'Pesquisa de Produto',																										  // título da janela
			'Cód.,Cód.Cli,Descrição,Marca,Preço 1,Preço 2,Preço 3,Estoque Real,Pedidos,Estoque Líq,Estoque MP,Descrição Inglês,Link,D049_Id',	  // descrição dos campos
			'est016CampoProcuraProdutoEditar',																					          // callback js
			$g['divId'],																									              // divId
			array('', $mT224['T224_Id']),					              										  // parametro extra
			array(																											              // parametros para cada campo do grid
				array('colunaAlinhar' => 'center'),
				array('colunaAlinhar' => 'center'),
				array('style' => 'font-weight:bold'),
				array(),
				array('callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right','style' => 'font-weight:bold'),
				array('callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right','style' => 'font-weight:bold'),
				array('callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right','style' => 'font-weight:bold'),
				array('callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right','style' => 'font-weight:bold'),
				array('callback' => 'qtePedidos', 'colunaAlinhar' => 'right','style' => 'font-weight:normal', 'colunaExibir' => false),
				array('callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right','style' => 'font-weight:bold'),
				array('callback' => 'validaEstoqueMP', 'colunaAlinhar' => 'right','style' => 'font-weight:bold'),				
                array(),
				array('callback' => 'campoLinkProdutoPesquisaGrid'),
				array('colunaExibir' => false),
			),
			'D009_Id||D049_Id||D009_Preco_1||D009_Preco_2||D009_Preco_3||D009_Quantidade_Estoque||D009_Quantidade_Estoque_Liquido' // Campos que são ignorados na cláusula where do pesquisaGrid
		);

			$T225=mysql_query("select * 
			                    from T225 
		                   left join D009 on D009_Id=T225_D009_Id
		                   left join D049 on D049_Id=D009_D049_Id
		                   left join D082 on D082_Id=D049_D082_Id
			                   where T225_Id='$acaoId'");
			$mT225=mysql_fetch_array($T225);

			$descricaoCFOP  = ($g['C031']['descricaoCFOPExibida'] == 'P') ? 'D006_Descricao' : 'IF(D006_Descricao_Comercial !="",D006_Descricao_Comercial,D006_Descricao)';

			// novo formulário
			$form = new Formulario('est016FormProdutosForm');
			// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)

			if($g['C031']['habilitaComprasImportacao'] == 'S'){
				$sqlFlagImport    = mysql_query("select T224A_Flag_Importacao from T224A where T224A_T224_Id='{$r_T224_Id}'");
				$resultFlagImport = mysql_fetch_array($sqlFlagImport);
	
				if($resultFlagImport['T224A_Flag_Importacao'] == 'S'){
					$desabilitaU = false;
					$desabilitaR = true;
					$exibirMoeda = 'moeda';
				}else{
					$desabilitaU = true;
					$desabilitaR = false;
					$exibirMoeda = 'hidden';
				}
	
			}else{
				$desabilita = false;
				$exibirMoeda = 'hidden';
			}

			$formArray = array(
			'campos' => array(
			'item' => array(
				'Procura_Produto_Edicao' => array('name' => 'Procura_Produto_Edicao' , 'label' => 'Produto', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid,'length' => array(30,30), 'reject' => true, 'value' => "{$mT225['T225_Codigo_Produto']}"),
				'T225_Codigo_Produto' => array('name' => 'T225_Codigo_Produto', 'label' => 'CódigoProd', 'type' => 'hidden', 'length' => array(15, 30)),
				'Marca' => array('name' => 'Marca', 'label' => 'Marca', 'type' => 'text', 'length' => array(64,255), 'reject' => true, 'disabled' => true, 'value' => "$mT225[D082_Marca]"),
				'T225_Descricao_Produto' => array('name' => 'T225_Descricao_Produto', 'label' => 'Descrição', 'type' => 'text', 'length' => array(64, 120)),
				'T225_Quantidade' => array('name' => 'T225_Quantidade', 'label' => 'Quantidade', 'type' => 'moeda', 'length' => array(15, 30)),
				'T225_D037_Id' => array('name' => 'T225_D037_Id', 'label' => 'Unidade', 'type' => 'select', 'align' => 'right', 'style' => 'float:right;', 'select' => 
					gGeraSelect('D037', 'D037_Id', 'D037_Unidade', '', true)
				),
				'T225_Valor_Custo_Unitario' => array('name' => 'T225_Valor_Custo_Unitario', 'label' => 'Custo Unitário', 'type' => 'moeda', 'disabled'=>$desabilitaR,'length' => array(20, 30)),
				'T225_Valor_Preco_Sem_Desconto_Unitario' => array('name' => 'T225_Valor_Preco_Sem_Desconto_Unitario', 'label' => 'Preço Unitário', 'type' => 'moeda', 'disabled'=>$desabilitaR,'length' => array(20, 30)),
				'T225A_Valor_Dolar_Unitario' => array('name' => 'T225A_Valor_Dolar_Unitario', 'label'=>'Preço Unitário Dolar','type' => $exibirMoeda, 'disabled' => $desabilitaU, 'length' => array(20,10)),
				'T225_IPV' => array('name' => 'T225_IPV', 'label' => 'IPV', 'type' => 'moeda', 'length' => array(15, 30), 'align' => 'right', 'style' => 'float:right;'),
				'T225_D006_Id' => array('name' => 'T225_D006_Id', 'label' => 'CFOP', 'type' => 'select',
					'select' => gGeraSelect('D006','D006_Id',"LPAD(D006_Id[[VIRGULA]]4[[VIRGULA]]0),D006_Codigo_CFOP,{$descricaoCFOP}",' WHERE D006_Flag_Inativo!="S" AND D006_Flag_Departamento="COMERCIAL"  ORDER BY D006_Codigo_CFOP', true, false, '%1 - %2 - %3'),
				),
				'T225_Observacao' => array('name' => 'T225_Observacao', 'label' => 'Observação', 'type' => 'text', 'length' => array(65, 120)),
				'T225_T208_Id' => array('name' => 'T225_T208_Id', 'label' => 'Rota', 'type' => 'select', 'select' => gGeraSelect('T208', 'T208_Id', 'T208_Rota', '')),
				'T225_D022_Id' => array('name' => 'T225_D022_Id', 'label' => 'Transportador', 'type' => 'select', 'select' => gGeraSelect('D022', 'D022_Id', 'D022_Nome_Empresa', '')),
				// Campos Hidden
				'T225_C004_Id' => array('name' => 'T225_C004_Id', 'label' => '', 'type' => 'hidden'),
				'T225_D009_Id' => array('name' => 'T225_D009_Id', 'label' => '', 'type' => 'hidden'),
				'T225_D082_Id' => array('name' => 'T225_D082_Id', 'label' => '', 'type' => 'hidden'),
				'T225_T224_Id' => array('name' => 'T225_T224_Id', 'label' => '', 'type' => 'hidden', 'value'=>$mT224['T224_Id']),
				'T225_Valor_Preco_Original' => array('name' => 'T225_Valor_Preco_Original', 'label' => '', 'type' => 'hidden'),
				'formId' => array('name' => 'formId', 'label' => '', 'type' => 'hidden', 'value' => $formId, 'reject' => true),
			),
			//TAB IPI
			'ipi' => array(
			    'T225_D005_Id' => array('name' => 'T225_D005_Id', 'label' => 'NCM', 'type' => 'select',  'select' => 
			        gGeraSelect('D005', 'D005_Id', 'D005_Classificacao_Fiscal, D149_Aliquota_IPI', "LEFT JOIN D149 ON D149_D005_Id=D005_Id AND D149_C004_Id='{$g['empresaAtual']}' WHERE D005_Flag_Ativo='S' ORDER BY D005_Classificacao_Fiscal, D005_Observacao", true)
			    ),
			    'T225_Aliquota_IPI' => array('name' => 'T225_Aliquota_IPI', 'label' => 'IPI %', 'type' => 'moeda', 'length' => array(15, 20)),
			    'T225_Situacao_Tributaria_IPI' => array('name' => 'T225_Situacao_Tributaria_IPI', 'label' => 'Situação Tributária IPI', 'type' => 'select',  'select' => 
			        gGeraSelect('D088', 'D088_Codigo_ST', 'D088_Codigo_ST,D088_Descricao', '', true)
			    ),
			),
			//TAB ICMS
			'icms' => array(
			    'T225_Aliquota_ICMS' => array('name' => 'T225_Aliquota_ICMS', 'label' => 'Aliquota ICMS', 'type' => 'moeda', 'length' => array(15, 20)),
			    'T225_Percentual_Reducao_ICMS' => array('name' => 'T225_Percentual_Reducao_ICMS', 'label' => '% Reducao base', 'type' => 'moeda', 'length' => array(15, 20)),
			    'T225_D059_Id' => array('name' => 'T225_D059_Id', 'label' => 'Situação Tributária', 'type' => 'select', 'extra'=>'style="width:288px;"',  'select' => 
			        gGeraSelect('D059', 'D059_Id', 'D059_Codigo_ST,D059_Descricao_Origem', '', true)
			    ),
			    'T225_Flag_ST' => array('name' => 'T225_Flag_ST', 'label' => 'Substituição Tributária', 'type' => 'select', 'select' => 
			        array(
			            array('title' => 'Sim', 'value' => 'S'),
			            array('title' => 'Não', 'value' => 'N', 'selected' => true)
			        )
			    ),
			    'T225_ST_ICMS_Interno' => array('name' => 'T225_ST_ICMS_Interno', 'label' => 'Aliquitoa ICMS Interno', 'type' => 'moeda', 'length' => array(15, 20)),
				'T225_ST_ICMS_Interno_MVA' => array('name' => 'T225_ST_ICMS_Interno_MVA', 'label' => 'Aliquota ICMS Interno MVA', 'type' => 'moeda', 'length' => array(15, 20), 'align'=>'right', 'style'=>'float:right'),
			    'T225_ICMS_Inter_Estadual' => array('name' => 'T225_ICMS_Inter_Estadual', 'label' => 'Aliquitoa ICMS Inter Estadual', 'type' => 'moeda', 'length' => array(15, 20)),
				'T225_ICMS_Inter_Estadual_MVA' => array('name' => 'T225_ICMS_Inter_Estadual_MVA', 'label' => 'Aliquitoa ICMS Inter Estadual MVA', 'type' => 'moeda', 'length' => array(15, 20), 'align'=>'right', 'style'=>'float:right'),
			    'T225_ST_VA' => array('name' => 'T225_ST_VA', 'label' => 'MVA', 'type' => 'moeda', 'length' => array(15, 20)),
			    
			),
			//TAB PIS/COFINS
			'pis-cofins' => array(
			    'T225_Aliquota_PIS' => array('name' => 'T225_Aliquota_PIS', 'label' => 'PIS %', 'type' => 'moeda', 'length' => array(15, 20)),
			    'T225_Flag_Isento_PIS' => array('name' => 'T225_Flag_Isento_PIS', 'label' => 'Isento', 'type' => 'select', 'select' => 
			        array(
			            array('title' => 'Sim', 'value' => 'S'),
			            array('title' => 'Não', 'value' => 'N', 'selected' => true)
			        )
			    ),
			    'T225_Situacao_Tributaria_PIS' => array('name' => 'T225_Situacao_Tributaria_PIS', 'label' => 'Situação Tributária PIS', 'extra'=>'style="width:288px;"', 'type' => 'select', 'select' =>
			        array(
			            array('title' => '...', 'value' => '', 'selected' => true),
			            array('title' => 'Operação Tributável (base de cálculo = valor da operação alíquota normal (cumulativo/não cumulativo))', 'value' => '01'),
			            array('title' => 'Operação Tributável (base de cálculo = valor a operação(alíquota diferenciada))', 'value' => '02'),
			            array('title' => 'Operação Tributável (base de cálculo = quantidade estdida x alíquota por unidade de produto)', 'value' => '03'),
			            array('title' => 'Operação Tributável (tributação monofísica (alíquota zero))', 'value' => '04'),
			            array('title' => 'Operação Tributável (alíquota zero)', 'value' => '06'),
			            array('title' => 'Operação Isenta da Contribuição', 'value' => '07'),
			            array('title' => 'Operação Sem Incidência da Contribuição', 'value' => '08'),
			            array('title' => 'Operação com Suspensão da Contribuição', 'value' => '09'),
			            array('title' => 'Outras Operações', 'value' => '99'),
			        )
			    ),

			    'T225_Aliquota_COFINS' => array('name' => 'T225_Aliquota_COFINS', 'label' => 'COFINS %', 'type' => 'moeda', 'length' => array(15, 20)),

			    'T225_Flag_Isento_COFINS' => array('name' => 'T225_Flag_Isento_COFINS', 'label' => 'Isento', 'type' => 'select', 'select' => 
			        array(
			            array('title' => 'Sim', 'value' => 'S'),
			            array('title' => 'Não', 'value' => 'N', 'selected' => true)
			        )
			    ),
			    'T225_Situacao_Tributaria_COFINS' => array('name' => 'T225_Situacao_Tributaria_COFINS', 'label' => 'Situação Tributária COFINS', 'extra'=>'style="width:288px;"', 'type' => 'select', 'select' =>
			        array(
			            array('title' => '...', 'value' => '', 'selected' => true),
			            array('title' => 'Operação Tributável (base de cálculo = valor da operação alíquota normal (cumulativo/não cumulativo))', 'value' => '01'),
			            array('title' => 'Operação Tributável (base de cálculo = valor a operação(alíquota diferenciada))', 'value' => '02'),
			            array('title' => 'Operação Tributável (base de cálculo = quantidade estdida x alíquota por unidade de produto)', 'value' => '03'),
			            array('title' => 'Operação Tributável (tributação monofísica (alíquota zero))', 'value' => '04'),
			            array('title' => 'Operação Tributável (alíquota zero)', 'value' => '06'),
			            array('title' => 'Operação Isenta da Contribuição', 'value' => '07'),
			            array('title' => 'Operação Sem Incidência da Contribuição', 'value' => '08'),
			            array('title' => 'Operação com Suspensão da Contribuição', 'value' => '09'),
			            array('title' => 'Outras Operações', 'value' => '99'),
			        )
			    ),
			)
			)
			);
			// Botão Limpar / Novo Registro
			if (!empty($acaoId)) {
				$formArray['campos']['produto']['est016FormProdutosBtnNew'] = array('name' => 'est016FormProdutosBtnNew', 'type'=>'submit', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
			}
			$formArray['campos']['produto']['est016FormProdutosBtnCln'] = array('name' => 'est016FormProdutosBtnCln', 'type'=>'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
			$formArray['campos']['produto']['est016FormProdutosBtnAdd'] = array('name' => 'est016FormProdutosBtnAdd', 'type'=>'button', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
			$form->definirFormularioArray($formArray);

			/**
			* PARTE 3: Processamento do formulário (se foi efnviado)
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
                                // $T225_D059_Id = $form->campoValorEnviado('T225_D059_Id');
                                // $sqlD059 = mysql_query("SELECT D059_Codigo_ST FROM D059 WHERE D059_Id='{$T225_D059_Id}'");
                                // $rowSql = mysql_fetch_array($sqlD059);
                                // mysql_query("UPDATE T225 SET T225_Flag_Situacao_Tributaria='0{$rowSql['D059_Codigo_ST']}' WHERE T225_Id='{$acaoId}'");                                
                                
								$alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
								// Imprime uma mensagem e fecha a janela
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
								divRefresh($('#{$g['divIdParent']}').closest('.ui-tabs-panel').find('.content-2columns:eq(1)').attr('id'), true, false, 2); // totais
							} </script></div>
EOT;
							}
					} else {
						// Insert
						// Nunca será insert neste FORM
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
			$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk,false, $form_parent, $form_parent_valor);


			/**
			* PARTE 5: Exibir o formulário
			*/
			$g['smarty']->assign('formularioID', $form->formularioID());
			$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
			echo $gerado['pagina'];
			if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
			$g['smarty']->display('formEditor.tpl');
		break;

		case 'est016FormPagamento':
			//$acaoId = (isset($r_acaoId)) ? $r_acaoId : false; 
			$T224_Id = (isset($r_T224_Id)) ? $r_T224_Id : false;
			  
			//echo $T224_Id;
			//('qqqq');
			/**
			* PARTE 1: Variavéis envidas para o formulário
			*/
			// Variável indica qual tab do form será exibida
			$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
			$r_divReload = isset($r_est016FormPagamento) ? $r_est016FormPagamento['divReload'] : $r_divReload;
			// Utilizada em UPDATE, é o ID do registro que está sendo editado		

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
			$form_tabela = 'T226';
			$form_campo_pk = 'T226_Id';
            $form_parent = 'T224_Id';
            $form_parent_valor = $T224_Id;
			// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
			$enviar_btnId = 'est016FormPagamento_btn';
			// Form que será executado ao clicar no botão
			$enviar_acaoHref = '/est/est016/form/est016FormPagamento/';
            $form_editor = array(
                'geral' => '{"Total_Parcelas":{"top":7,"left":32,"width":166,"hidden":0},"selecionaPrimeiroVencimento":{"top":30,"left":32,"width":105,"hidden":0},"primeiroVencimentoNum":{"top":30,"left":192,"width":40,"hidden":0},"primeiroVencimentoData":{"top":30,"left":208,"width":279,"hidden":0},"selecionaDias":{"top":53,"left":22,"width":105,"hidden":0},"IntervalorDias":{"top":53,"left":192,"width":40,"hidden":0},"diaFixo":{"top":53,"left":208,"width":180,"hidden":0},"Portador_D031_Id":{"top":76,"left":33,"width":165,"hidden":0},"est016FormPagamento_btn":{"top":74,"left":272,"width":null,"hidden":0},"est016FormAdicionarParcela_btn":{"top":74,"left":848.0000610351562,"width":null,"hidden":1},"T224_Observacao_Pagamento":{"top":122,"left":862.0000610351562,"height":32,"width":157,"hidden":1},"est016SalvarObservacao_btn":{"top":51,"left":816.0000610351562,"width":null,"hidden":1}}',
            );

			// novo formulário
			$form = new Formulario('est016FormPagamento');			
			
			$select = array();
			$select[] = array('value' => '0', 'title' => 'A COMBINAR');
			// pega as configurações de juros da empresa atual
			$C004=mysql_query("select * from C004 where C004_Id='{$g['empresaAtual']}'");
			$mC004=mysql_fetch_array($C004);
			$tipoJuros=$mC004['C004_Tipo_Juros'];
			$taxaJuros=$mC004['C004_Taxa_Juros'];
			$parcelasIsentas=$mC004['C004_Parcelas_Isentas_Juros'];			
			$T224=mysql_query("select * from T224 where T224_Id='$T224_Id'");
			$mT224=mysql_fetch_array($T224);
			
			//SELECIONANDO OS SERVIÇOS DESSE ORÇAMENTO PARA CALCULAR O TOTAL
			$sql = "SELECT T230_Valor_Total FROM T230 where T230_T224_Id='{$T224_Id}'";
			$T230_sql = mysql_query($sql);
			
			$totalServicos=0;
			// LOOPING PARA CALCULAR O TOTAL DOS SERVIÇOS SOMADO COM TOTAL DO PEDIDO
			while($T230 = mysql_fetch_array($T230_sql)){
				$totalServicos+=$T230['T230_Valor_Total'];
			}
			
			//SOMANDO O TOTAL DO ORÇAMENTO COM O TOTAL DOS SERVIÇOS
			$valorTotalPedido = $totalServicos + $mT224['T224_Valor_Total'];
						
			for ($Parcela=1; $Parcela<=24; $Parcela++) {
				$valorParcela=$EST016->calcularParcelaCombo($Parcela,$valorTotalPedido,$tipoJuros,$taxaJuros,$parcelasIsentas);				
				if ($Parcela>$parcelasIsentas){
					$valorTotal=gCorrigeNumero($Parcela*gCorrigeNumeroInverte($valorParcela));
					$valorParcela.="*";
				}else{
					$valorTotal=gCorrigeNumero($valorTotalPedido);
				}
				if($Parcela=='1' or $Parcela == $mT224['T224_Pgto_Parcelas']){
					$select[] = array('value' => $Parcela, 'title' => str_pad($Parcela,2,"0",STR_PAD_LEFT) . " x" . " - " .$valorParcela." = ".$valorTotal, 'selected'=>true);
				}else{
					$select[] = array('value' => $Parcela, 'title' => str_pad($Parcela,2,"0",STR_PAD_LEFT) . " x" . " - " .$valorParcela." = ".$valorTotal);
				}
			}
			
			if(($mT224['T224_Pgto_Primeiro_Vcto_Valor'] != "") || (!empty($mT218['T224_Pgto_Primeiro_Vcto_Valor']))){
				$primeiroVctoValor = $mT224['T224_Pgto_Primeiro_Vcto_Valor'];
			}else{
				$primeiroVctoValor = $g['conf']->camposValorPadrao("primeiroVencimentoNum");
			}
			if(($mT224['T224_Pgto_Dias_Vcto_Valor'] != "") || (!empty($mT224['T224_Pgto_Dias_Vcto_Valor']))){
				$diaVctoValor = $mT224['T224_Pgto_Dias_Vcto_Valor'];
			}else{
				$diaVctoValor = $g['conf']->camposValorPadrao("IntervalorDias");
			}
			if(($mT224['T224_Pgto_Data'] != "") || (!empty($mT224['T224_Pgto_Data']))){
				$pgtoData = $mT224['T224_Pgto_Data'];
			}else{
				$pgtoData = "";
			}
			
			// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
			$formArray = array(
				'campos' => array(
					'geral' => array(
						'Total_Parcelas' => array('name' => 'Total_Parcelas', 'label' => 'Parcelas', 'type' => 'select', 'select' => $select,'uppercase'=>false, 'reject' => true),
						//'valorParcelaSimulacao' => array('name' => 'valorParcelaSimulacao', 'label' => '', 'type' => 'text','extra'=>'style="background:#E8E8E8"','reject' => true, 'length' => array(3,6), 'align' => 'right', 'readonly'=>'readonly'),
						'selecionaPrimeiroVencimento' => array('name' => 'selecionaPrimeiroVencimento', 'label'=>'1º Venc.', 'reject'=>true, 'type' => 'select', 'select' => 
						array(
							array('title' => 'Dias', 'value' => 'Dias', 'selected' => ($mT224['T224_Pgto_Primeiro_Vcto_Tipo'] == "Dias") ? true : false),
							array('title' => 'Data', 'value' => 'Data', 'selected' => ($mT224['T224_Pgto_Primeiro_Vcto_Tipo'] == "Data") ? true : false),
						)),
						'primeiroVencimentoNum' => array('name' => 'primeiroVencimentoNum', 'label' => '', 'value'=> $primeiroVctoValor, 'type' => 'text', 'reject' => true, 'length' => array(3,6), 'align' => 'right'),
						'primeiroVencimentoData' => array('name' => 'primeiroVencimentoData', 'label' => '', 'value'=> $primeiroVctoValor, 'type' => 'data', 'reject' => true, 'align' => 'right'),
						'selecionaDias' => array('name' => 'selecionaDias', 'label'=>'Dias Venc.', 'type' => 'select', 'reject'=>true, 'select' => 
						array(
							array('title' => 'Intervalo', 'value' => 'I', 'selected' => ($mT224['T224_Pgto_Dias_Vcto_Tipo'] == "Intervalo") ? true : false),
							array('title' => 'Dia Fixo', 'value' => 'F', 'selected' => ($mT224['T224_Pgto_Dias_Vcto_Tipo'] == "Dia Fixo") ? true : false),
						)),			
						'IntervalorDias' => array('name' => 'IntervalorDias', 'label' => '', 'type' => 'text', 'value'=>$diaVctoValor, 'reject' => true, 'length' => array(3,6), 'align' => 'right'),
						'diaFixo' => array('name' => 'diaFixo', 'label' => '', 'type' => 'text', 'reject' => true, 'value'=>$diaVctoValor, 'length' => array(3,2), 'align' => 'right'),												
						'Portador_D031_Id' => array('name' => 'D031_Id','label'=>'Portador', 'reject'=>true, 'type' => 'select', 'select' =>
							gGeraSelect('D031', 'D031_Id', 'D031_Portador',' order by D031_Portador', false, $mT224['T224_Pgto_Portador'])
						),
						'est016FormPagamento_btn' => array('name' => 'est016FormPagamento_btn', 'type'=>'submit','reload'=>false,'value'=>'Gerar Parcelas','align' => 'right', 'reject'=>true),
						'est016FormAdicionarParcela_btn' => array('name' => 'est016FormAdicionarParcela_btn', 'type'=>'submit', 'reload' => false, 'value' => 'Adicionar Parcela', 'align' => 'right', 'reject' => true),
						'T224_Observacao_Pagamento' => array('name' => 'T224_Observacao_Pagamento', 'label' => 'Observação', 'type' => 'textarea', 'length' => array(1,23),'reject' => true),
						'est016SalvarObservacao_btn' => array('name' => 'est016SalvarObservacao_btn', 'type'=>'submit', 'reload' => false, 'value' => 'Salvar Observação', 'align' => 'right', 'reject' => true),
						'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
						'T224_Id' => array('name' => 'T224_Id', 'label' => '', 'type' => 'hidden', 'value' => @$r_T224_Id, 'reject' => true),
					)
				)
			);
 
		if($g['C031']['usarFretePrimeiraParcela'] == 'S'){
			$formArray['campos']['geral']['usarFreteParcela'] = array('name' => 'usarFreteParcela', 'label'=>'Frete primeira parcela', 'reject'=>true, 'type' => 'select','select' => 
																	array(
																		array('title' => '...', 'value' => ''),
																		array('title' => 'Sim', 'value' => 'on'),
																		array('title' => 'Não', 'value' => 'off'),
																	)
																);

			$formArray['campos']['geral']['usarSTParcela'] = array('name' => 'usarSTParcela', 'label'=>'ST primeira parcela', 'reject'=>true, 'type' => 'select','select' => 
																	array(
																		array('title' => '...', 'value' => ''),
																		array('title' => 'Sim', 'value' => 'on'),
																		array('title' => 'Não', 'value' => 'off'),
																	)
																);
			$formArray['campos']['geral']['usarIPIParcela'] = array('name' => 'usarIPIParcela', 'label'=>'IPI primeira parcela', 'reject'=>true, 'type' => 'select','select' => 
																	array(
																		array('title' => '...', 'value' => ''),
																		array('title' => 'Sim', 'value' => 'on'),
																		array('title' => 'Não', 'value' => 'off'),
																	)
																);
		}
			
		$form->definirFormularioArray($formArray);
		
		echo <<<EOT
		    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		    	$('#est016FormAdicionarParcela_btn').unbind('click');
				$('#est016FormAdicionarParcela_btn').unbind('click').bind('click', function(data) {
					var id = '{$row['T224_D024_Id']}';
						$.getJSON('/est/est016/form_func-ajax/inserirParcela/?ajax=true=' + '&' + $("#{$formId}").serialize() + '&callback=?', function(request) {
							if (request.code) {															
								divRefresh("{$r_divReload}");
							} else {
								var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
    							dialogConfirm("Erro!", request.data, buttons)
							}
						});
					return false;
				});
				$('#est016SalvarObservacao_btn').unbind('click').bind('click', function(data) {
					$.getJSON('/est/est016/form_func-ajax/salvarObservacao/?ajax=true=' + '&' + $("#{$formId}").serialize() + '&T224_Id=' + encodeURIComponent('{$T224_Id}') + '&callback=?', function(request) {
						if (request.code) {															
							divRefresh('{$g['divId']}');
						} else {
							var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
							dialogConfirm("Erro!", request.data, buttons)
						}
					});
					return false;
				});
				$('#{$g['divId']} #selecionaDias').change(function() {
					if ($(this).val() == 'I') {
						$('#{$g['divId']} #diaFixo').val('').hide();
						$('#{$g['divId']} #IntervalorDias').show();
						$('#{$g['divId']} #IntervalorDias').focus();
					} else {
						$('#{$g['divId']} #IntervalorDias').val('').hide();
						$('#{$g['divId']} #diaFixo').show();
						$('#{$g['divId']} #diaFixo').focus();
					}
				});
				$('#{$g['divId']} #selecionaDias').trigger('change');
				
				$('#{$g['divId']} #selecionaPrimeiroVencimento').change(function() {
					if ($(this).val() == 'Dias') {
						$('#{$g['divId']} #primeiroVencimentoData').val('').hide();
						$('#{$g['divId']} #primeiroVencimentoNum').show();
						$('#{$g['divId']} #primeiroVencimentoNum').focus();
					} else {
						$('#{$g['divId']} #primeiroVencimentoNum').val('').hide();
						$('#{$g['divId']} #primeiroVencimentoData').show();
						$('#{$g['divId']} #primeiroVencimentoData').focus();
					}
				});
				$('#{$g['divId']} #selecionaPrimeiroVencimento').trigger('change');	
				/*
				// calcula e mostra o valor das parcelas (simulação)
				var changeHandler = function() {					
					$.getJSON('/est/est016/form_func-ajax/calcularParcelaSimulacao/?ajax=true=' + '&' + $("#{$formId}").serialize() + '&callback=?', function(request) {
						if (request.code) {															
							//divRefresh("{$r_divReload}");
							$('#{$g['divId']} #valorParcelaSimulacao').val(request.data);
						} else {
							var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
    						dialogConfirm("Erro!", request.data, buttons)
						}
					});										
				}				
				$('#{$g['divId']} #Total_Parcelas').change(changeHandler).keyup(changeHandler);
				*/							
		    } </script></div>
EOT;

		
		     
		/**
		 * PARTE 3: Processamento do formulário (se foi efnviado)
		 * 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		 */

			if ($formCodigo = $form->processarFormulario($form_tabela)) {
				// Form com erros
				if ($formCodigo == 1) {
					echo gGeraAlertMsgErroForm($form);
					// Form sem erros
				} else if ($formCodigo == 2) {
					// Gerar Boleto			
					$retorno = $EST016->atualizarParcelas($form);
					if ($retorno === true) {
						$alert = gGeraAlertMsg('Operação OK', 'Parcelas cadastradas com sucesso.', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									if (typeof jQuery != 'undefined') {
                                       //divRefresh("{$g['divId']}", true);
                                       divRefresh('{$form->campoValorEnviado('divReload')}', true);
                                       //divRefresh('{$g['divId']}', true);
                               	}
							} </script></div>
EOT;
					} else {
						echo gGeraAlertMsg('Error', 'Erro ao gerar Pacelas:<br />' . $retorno);
					}
				}
			}  else {
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
		$gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk, false, $form_parent, $form_parent_valor);


		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');

	break;
	
	case 'est016FormTotais':
	
	// implementação da rubia, retirada toda essa parte 
	/* 
	$Empresa_Atual = $g['empresaAtual'];
	
	$T224=mysql_query("select T224_Flag_Travar_Pedido,
                          T224_Flag_Devolucao
                     from T224
                    where T224_Id='$T224_Id'");
	echo mysql_error();
	$mT224=mysql_fetch_array($T224);

	if (($mT224['T224_Flag_Devolucao']=='S') or ($mT224['T224_Flag_Travar_Pedido']==1)) {
	//    $Pedido_Travado='S';
	}
	if ($mT224['T224_Flag_Travar_Pedido']!=1) {

	   //-- ATUALIZA O PESO DOS ITENS
	   mysql_query("update T225
	             left join T224 on T224_Id=T225_T224_Id
	             left join D009 on D009_Id=T225_D009_Id
	             left join D049 on D049_Id=D009_D049_Id
	             left join D001 on D001_Id=D049_D001_Id
	                   set T225_Peso_Unitario=D001_Peso_Unitario_Kg
	                 where T224_Id='$T224_Id' 
	                   and T224_C004_Id='$Empresa_Atual'");

	    //-- TOTALIZA O PEDIDO
	    $cSql_T225=mysql_query("select T225_Id from T225 where T225_T224_Id='$T224_Id' order by T225_Id");
	    while ($mT225=mysql_fetch_array($cSql_T225)) {
	          mysql_query("call T225_Gravar_Totalizacao('$mT225[T225_Id]')");
	    }
	    mysql_query("call T224_Gravar_Totalizacao_3('$T224_Id')");
	}
	*/
	
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
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		
		
		$form_tabela = 'T224';
		$form_campo_pk = 'T224_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = '';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '';

		if($g['C031']['habilitaComprasImportacao'] == 'S'){
			$sqlFlagImport    = mysql_query("select T224A_Flag_Importacao from T224A where T224A_T224_Id='{$r_T224_Id}'");
			$resultFlagImport = mysql_fetch_array($sqlFlagImport);
			if($resultFlagImport['T224A_Flag_Importacao'] == 'S'){
				$meusCampos = ', "T225A_Valor_Dolar_Total":{"top":220,"left":21,"width":212,"hidden":1}';
			}else{
				$meusCampos = '';
			}
		}else{
			$meusCampos = '';
		}
        
        //form editor
        $form_editor = array(
            'geral' => '{"T224_Valor_Total_Produtos_Sem_Desconto":{"top":7,"left":31,"width":225,"hidden":0},"T224_Valor_Desconto":{"top":30,"left":30,"width":225,"hidden":0},"T224_Valor_Total_Servicos":{"top":53,"left":31,"width":225,"hidden":0},"T224_Valor_ICMS_Substituicao_Retencao":{"top":76,"left":56,"width":225,"hidden":0},"T224_Valor_Total_IPI":{"top":99,"left":64,"width":225,"hidden":0},"T224_Valor_Despesas_Acessorias":{"top":122,"left":14,"width":225,"hidden":0},"T224_Valor_Total_Agregados":{"top":145,"left":20,"width":225,"hidden":0},"T224_Valor_Troca":{"top":168,"left":40,"width":225,"hidden":0},"T224_Valor_Frete":{"top":191,"left":49,"width":225,"hidden":0},"T224_Valor_Total":{"top":214,"left":52,"width":225,"hidden":0},"T224_Valor_Total_ICMS":{"top":236.99996948242188,"left":50,"width":225,"hidden":0},"T225_Valor_ICMS_Credito":{"top":259.9999694824219,"left":13,"width":225,"hidden":0},"T224_IPV":{"top":282.9999694824219,"left":58,"width":225,"hidden":0},"T224_Peso_Liquido":{"top":305.9999694824219,"left":32,"width":225,"hidden":0}' . $meusCampos . '}'
        );
		
		//$totalServicosConfirmado = $EST016->calculaTotalServicos($r_acaoId);
		$totalGeralConfirmado = $EST016->calculaTotalGeral($r_acaoId);
		
        /*
        * ************** IMPORTANTEEEEEE!!!! *****************
        *  Qualquer coisa que for mudado daqui para baixo até o próximo marcador, precisa ser mudado tbm na função $EST016->motivoPedidoLiberacao, e no documento est016/form_func-ajax.php -> case 'verificaAlcada';
        *  Se possível, quando der, criar uma única função para os três casos de uso.
        */
		//Calcula Desconto por alçada
		$descontoPedido = $EST016->calculaPercentualDescontoPedido($r_acaoId);
		$descontoVendedor = gCorrigeNumeroInverte($g['conf']->ini('alcadaDescontoVendedor'));
		$descontoSupervisor = gCorrigeNumeroInverte($g['conf']->ini('alcadaDescontoSupervisor'));

		$alcadaDescontoVendedorPorProduto = $g['conf']->ini('alcadaDescontoVendedorPorProduto');
		$alcadaDescontoSupervisorPorProduto = $g['conf']->ini('alcadaDescontoSupervisorPorProduto');
		$alcadaDescontoSupervisorPorProduto = gCorrigeNumeroInverte($alcadaDescontoSupervisorPorProduto);

		//Calcula prazo médio por alçada
		$prazoMedioAlcadaVendedor = $EST016->calculaPrazoMedioAlcadaVendedor($r_acaoId);
		$prazoMedioAlcadaSupervisor = $EST016->calculaPrazoMedioAlcadaSupervisor($r_acaoId);
		$retorno=mysql_query("SELECT AVG(T226_Prazos_Dias) FROM T226 WHERE T226_T224_Id='{$r_acaoId}'");
		$T224=mysql_fetch_array($retorno);				
		$prazoMedio=$T224['AVG(T226_Prazos_Dias)'];
        $retornoSaldoMinimo = $EST016->verificaFuturoSaldoProduto($r_acaoId,true);

        $T224MaiorDesconto = $EST016->retornaMaiorDescontoPedido($r_acaoId);

        $usarAlcadaPorProduto = $g['conf']->ini('usarAlcadaPorProduto');

  		//Verificações de alçada
		if(($descontoPedido <= $descontoVendedor) && ($prazoMedio <= $prazoMedioAlcadaVendedor) && ($retornoSaldoMinimo==0)){

			$prazoMedio=gCorrigeNumero($T224['AVG(T226_Prazos_Dias)']);
			$descontoPedidoUsar = gCorrigeNumero($descontoPedido);
			$T224MaiorDescontoUsar = gCorrigeNumero($T224MaiorDesconto);
			$alcadaPedido = "Alçada: Vendedor";
			$alcadaDesconto = "Desconto: {$descontoPedidoUsar}%";
			$alcadaMaiorDesconto = "Maior Desconto: {$T224MaiorDescontoUsar}%";
			$alcadaPrazoMedio = "Prazo Médio: {$prazoMedio} Dias";

		} elseif (($descontoPedido <= $descontoSupervisor) && ($prazoMedio <= $prazoMedioAlcadaSupervisor)){

			$prazoMedio=gCorrigeNumero($T224['AVG(T226_Prazos_Dias)']);
			$descontoPedidoUsar = gCorrigeNumero($descontoPedido);
			$T224MaiorDescontoUsar = gCorrigeNumero($T224MaiorDesconto);
			$alcadaPedido = "Alçada: Supervisor";
			$alcadaDesconto = "Desconto: {$descontoPedidoUsar}%";
			$alcadaMaiorDesconto = "Maior Desconto: {$T224MaiorDescontoUsar}%";
			$alcadaPrazoMedio = "Prazo Médio: {$prazoMedio} Dias";

		} else {

			$prazoMedio=gCorrigeNumero($T224['AVG(T226_Prazos_Dias)']);
			$descontoPedidoUsar = gCorrigeNumero($descontoPedido);
			$T224MaiorDescontoUsar = gCorrigeNumero($T224MaiorDesconto);
			$alcadaPedido = "Alçada: Diretor";
			$alcadaDesconto = "Desconto: {$descontoPedidoUsar}%";
			$alcadaMaiorDesconto = "Maior Desconto: {$T224MaiorDescontoUsar}%";
			$alcadaPrazoMedio = "Prazo Médio: {$prazoMedio} Dias";
		}

		// Verificação apenas para quando a global $usarAlcadaPorProduto for igual a sim
		if($usarAlcadaPorProduto == "S"){

			if($T224MaiorDesconto <= $alcadaDescontoVendedorPorProduto){

				$prazoMedio=gCorrigeNumero($T224['AVG(T226_Prazos_Dias)']);
				$descontoPedidoUsar = gCorrigeNumero($descontoPedido);
				$T224MaiorDescontoUsar = gCorrigeNumero($T224MaiorDesconto);
				$alcadaPedido = "Alçada: Vendedor";
				$alcadaDesconto = "Desconto: {$descontoPedidoUsar}%";
				$alcadaMaiorDesconto = "Maior Desconto: {$T224MaiorDescontoUsar}%";
				$alcadaPrazoMedio = "Prazo Médio: {$prazoMedio} Dias";

			}elseif($T224MaiorDesconto <= $alcadaDescontoSupervisorPorProduto){

				$prazoMedio=gCorrigeNumero($T224['AVG(T226_Prazos_Dias)']);
				$descontoPedidoUsar = gCorrigeNumero($descontoPedido);
				$T224MaiorDescontoUsar = gCorrigeNumero($T224MaiorDesconto);
				$alcadaPedido = "Alçada: Supervisor";
				$alcadaDesconto = "Desconto: {$descontoPedidoUsar}%";
				$alcadaMaiorDesconto = "Maior Desconto: {$T224MaiorDescontoUsar}%";
				$alcadaPrazoMedio = "Prazo Médio: {$prazoMedio} Dias";

			}else{

				$prazoMedio=gCorrigeNumero($T224['AVG(T226_Prazos_Dias)']);
				$descontoPedidoUsar = gCorrigeNumero($descontoPedido);
				$T224MaiorDescontoUsar = gCorrigeNumero($T224MaiorDesconto);
				$alcadaPedido = "Alçada: Diretor";
				$alcadaDesconto = "Desconto: {$descontoPedidoUsar}%";
				$alcadaMaiorDesconto = "Maior Desconto: {$T224MaiorDescontoUsar}%";
				$alcadaPrazoMedio = "Prazo Médio: {$prazoMedio} Dias";

			}
		}
        
        $motivoPedidoLiberacao= '';
        $retornoStatus=mysql_query("SELECT * FROM T224 WHERE T224_Id='{$r_acaoId}'");
        $T224Status=mysql_fetch_array($retornoStatus);
        if($T224Status['T224_Flag_Status'] == '14'){
            $motivoPedidoLiberacao .= "                APROVADO     \n";
        }
        if(!empty($T224Status['T224_Motivo_Pedido_Liberacao'])){
            $motivoPedidoLiberacao .= $T224Status['T224_Motivo_Pedido_Liberacao'];
        }/* else{
			//Função não existe no EST016
            $motivoPedidoLiberacao .= $EST016->motivoPedidoLiberacao($r_acaoId);
        } */

        $total=$T224Status['T224_Valor_Total_Produtos'];

		$T225=mysql_query("select sum(T225_Valor_ICMS_Credito) T225_Valor_ICMS_Credito,
                          T225_Aliquota_ICMS_Credito
                     	  from T225
                    	  where T225_T224_Id='{$r_acaoId}'
                    	  group by T225_T224_Id");
		echo mysql_error();
		$mT225=mysql_fetch_array($T225);
   
        $mT225['T008_Valor_ICMS_Credito']=number_format($mT225['T225_Valor_ICMS_Credito'],2,',','.');

        /*
        * 
        *Aqui termina verificação de alçadaa-------------------- 
        * 
        */
		
		// Definição do Formulário
		//$exibirMoeda = ($g['C031']['habilitaComprasImportacao'] == 'S') ? 'moeda' : 'hidden';

		if($g['C031']['habilitaComprasImportacao'] == 'S'){
			$sqlFlagImport    = mysql_query("select T224A_Flag_Importacao from T224A where T224A_T224_Id='{$r_T224_Id}'");
			$resultFlagImport = mysql_fetch_array($sqlFlagImport);
			if($resultFlagImport['T224A_Flag_Importacao'] == 'S'){
				$desabilitaU = false;
				$desabilitaR = true;
				$exibirMoeda = 'moeda';
			}else{
				$desabilitaU = true;
				$desabilitaR = false;
				$exibirMoeda = 'hidden';
			}

		}else{
			$desabilita = false;
			$exibirMoeda = 'hidden';
		}

		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
     				'T224_Valor_Total_Produtos_Sem_Desconto' => array('name' => 'T224_Valor_Total_Produtos_Sem_Desconto', 'label' => 'Produtos', 'type' => 'moeda', 'length' => array(40, 80), 'disabled' => 'true', 'extra'=>'style=background:#E8E8E8;'),
					'T225A_Valor_Dolar_Total' => array('name' => 'T225A_Valor_Dolar_Total', 'label'=>'Total U$','type' => $exibirMoeda, 'reject' => 'true', 'disabled'=>'true','length' => array(40, 80),'extra'=>'style=background:#E8E8E8;'),
     				'T224_Valor_Desconto' => array('name' => 'T224_Valor_Desconto', 'label' => 'Desc.R$.', 'type' => 'moeda', 'length' => array(40,80),'extra' => "onchange=\"gravarDescontoPedido(this, '{$g['divId']}', '{$r_T224_Id}', 'T224_Valor_Desconto');\"",'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
     				'T224_Valor_Desconto_Manual' => array('name' => 'T224_Valor_Desconto_Manual', 'label' => 'Desc Manual.', 'type' => 'moeda', 'length' => array(40,80),'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
     				'T224_Valor_Total_Servicos' => array('name' => 'T224_Valor_Total_Servicos', 'label' => 'Serviços', 'type' => 'moeda', 'length' => array(40, 80), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					'T224_Valor_ICMS_Substituicao_Retencao' => array('name' => 'T224_Valor_ICMS_Substituicao_Retencao', 'label' => 'S.T.', 'type' => 'moeda', 'length' => array(40,40), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					'T224_Valor_Total_IPI' => array('name' => 'T224_Valor_Total_IPI', 'label' => 'IPI', 'type' => 'moeda', 'length' => array(40,80), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					'T224_Valor_Despesas_Acessorias' => array('name' => 'T224_Valor_Despesas_Acessorias', 'label' => 'Desp.Acess', 'type' => 'moeda', 'length' => array(40,80), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					'T224_Valor_Total_Agregados' => array('name' => 'T224_Valor_Total_Agregados', 'label' => 'Agregados', 'type' => 'moeda', 'length' => array(40,80), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					'T224_Valor_Troca' => array('name' => 'T224_Valor_Troca', 'label' => 'Trocas', 'type' => 'moeda', 'length' => array(40,80), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					'T224_Valor_Frete' => array('name' => 'T224_Valor_Frete', 'label' => 'Frete', 'type' => 'moeda', 'length' => array(40,80), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
     				'T224_Valor_Total' => array('name' => 'T224_Valor_Total', 'label' => 'Total', 'type' => 'moeda', 'length' => array(40, 80), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					'T224_Valor_Total_ICMS' => array('name' => 'T224_Valor_Total_ICMS', 'label' => 'ICMS', 'type' => 'moeda', 'length' => array(40,80), 'disabled' => 'true', 'extra'=>'style=background:#E8E8E8;'),
                    'T225_Valor_ICMS_Credito' => array('name' => 'T225_Valor_ICMS_Credito', 'value' => $mT225['T225_Valor_ICMS_Credito'], 'label' => 'ICMS Crédito', 'type' => 'moeda', 'length' => array(40,80), 'casasDecimais' => '2', 'disabled' => 'true','reject' => true, 'extra'=>'style=background:#E8E8E8;'),
					'T224_IPV' => array('name' => 'T224_IPV', 'label' => 'IPV', 'type' => 'text', 'length' => array(40,80), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					'T224_Peso_Liquido' => array('name' => 'T224_Peso_Liquido', 'label' => 'Peso KG', 'type' => 'moeda', 'length' => array(40,80), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					//'T224_Valor_Total' => array('name' => 'T224_Valor_Total', 'label' => 'Total','reject' => true, 'value' =>gCorrigeNumero($totalGeralConfirmado), 'type' => 'moeda', 'length' => array(40,80), 'disabled' => 'true',  'extra'=>'style=background:#E8E8E8;'),
					'est016AlcadaPedido' => array('name' => 'est016AlcadaPedido', 'label' => '','type' => 'text','reject' => true,'value' => $alcadaPedido, 'length' => array(20,40), 'disabled' => 'true',  'extra'=>'style=margin-left:200px;:right;background:yellow;border-color: var(--BGVermelho);','length' => array(30,30)),
					'est016AlcadaDesconto' => array('name' => 'est016AlcadaDesconto', 'label' => '','type' => 'text','reject' => true,'value' => $alcadaDesconto, 'length' => array(20,40), 'disabled' => 'true',  'extra'=>'style=margin-left:200px;:right;background:yellow;border-color: var(--BGVermelho);','length' => array(30,30)),

					'est016AlcadaDescontoProduto' => array('name' => 'est016AlcadaDescontoProduto', 'label' => '','type' => 'text','reject' => true,'value' => $alcadaMaiorDesconto, 'length' => array(20,40), 'disabled' => 'true',  'extra'=>'style=margin-left:200px;:right;background:yellow;border-color: var(--BGVermelho);','length' => array(30,30)),

					'est016AlcadaPrazoMedio' => array('name' => 'est016AlcadaPrazoMedio', 'label' => '', 'type' => 'text','reject' => true,'value' => $alcadaPrazoMedio, 'length' => array(20,40), 'disabled' => 'true',  'extra'=>'style=margin-left:200px;:right;background:yellow;border-color: var(--BGVermelho);','length' => array(30,30)),
                    'est016AlcadaMotivoLiberacao' => array('name' => 'est016AlcadaMotivoLiberacao', 'label' => '', 'type' => 'textarea','reject' => true,'value' => $motivoPedidoLiberacao, 'length' => array(5,26), 'extra'=>'style=margin-left:200px;:right;background:yellow;border-color: var(--BGVermelho);'),
				)
			)
		);
        $form = new Formulario('form-est001FormTotais');
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
			} 
		} //else {
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
		//}
         
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
	
	case 'est016FormAplicarIpv':
	
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
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'T224';
		$form_campo_pk = 'T224_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = '';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '';
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'T224_Id' => array('name' => 'T224_Id', 'label' => 'T224_Id', 'type' => 'hidden', 'length' => array(40, 80), 'reject' => true, 'value' => $r_T224_Id),
     				'IPV' => array('name' => 'IPV', 'label' => 'Aplicar IPV', 'type' => 'moeda', 'length' => array(40, 80), 'reject' => true),
				)
			)
		);
        $form = new Formulario('form-est001FormTotais2');
		$form->definirFormularioArray($formArray);
         
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
		$g['smarty']->display('form.tpl');
		

	break;
	
	case 'est016FormImportarItens':
	
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
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'T224';
		$form_campo_pk = 'T224_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = '';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '';
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'T224_Id' => array('name' => 'T224_Id', 'label' => 'T224_Id', 'type' => 'hidden', 'length' => array(40, 80), 'reject' => true, 'value' => $r_T224_Id),
     				'T224_Id_Origem' => array('name' => 'T224_Id_Origem', 'label' => 'Informar Ítens', 'type' => 'text', 'length' => array(40, 80), 'reject' => true),
				)
			)
		);
        $form = new Formulario('form-est001FormTotais2');
		$form->definirFormularioArray($formArray);
         
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
		$g['smarty']->display('form.tpl');
		

	break;
	
	case 'est016FormFaturarEmergencia':
         global $g;
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($r_acaoId)) ? $r_acaoId : false;

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
		$form_tabela = 'T224';
		$form_campo_pk = 'T224_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est016FormFaturarEmergencia_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est016/form/est016FormFaturarEmergencia/';
				
		// novo formulário
		$form = new Formulario('est016FormFaturarEmergencia');

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T224_Observacao_Fatura_Emergencia' => array('name' => 'T224_Observacao_Fatura_Emergencia', 'label'=>'Observação', 'type' => 'textarea', 'length' => array(3,60)),
					'T224_Liberacao_Credito_Id' => array('name' => 'T224_Liberacao_Credito_Id', 'label'=>'IdOperador', 'type' => 'hidden','value' => $g['usuarioAtual']),
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
				// Estorna o Título
				$retorno = $EST016->faturarEmergencia($acaoId, $form);
				$retorno = ($retorno === true) ? 'Pedido faturado com emergência!' : $retorno;
				// Mensagem
				$alert = gGeraAlertMsg('Faturamento com emergência', $retorno, false);
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
		
		/**
		* PARTE 4: Gerar o formulário
		*/
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);
		
		
		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());		
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		//echo '<div style="padding:5px">Estorno de título - ID: '.$acaoId.'</div>';
		echo $gerado['pagina'];				
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('form.tpl');
		
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="est016FormFaturarEmergencia_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		//$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
				
	break;
	
	case 'est016FormDocumentos':
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
		$form_tabela = 'T227';
		$form_campo_pk = 'T227_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('est016FormDocumentosBtnAdd', 'est016FormDocumentosBtnCln', 'est016FormDocumentosBtnNew');
        // Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est016/form/est016FormDocumentos/';

		//form editor
		$form_editor = array(
			'geral' => '{"uploadArquivo":{"top":7,"left":26.984375,"width":208,"hidden":0},"T227_Descricao":{"top":30,"left":73.71875,"height":62,"width":342,"hidden":0},"est016FormDocumentosBtnAdd":{"top":97,"left":128,"width":null,"hidden":0},"est016FormImportarDoc":{"top":97,"left":192,"width":null,"hidden":0}}',
		);

		// Array de definição do formulário
		$r_T224_Id = isset($r_est016FormDocumentos) ? $r_est016FormDocumentos['T227_T224_Id'] : $r_T224_Id;
		                                                           
		$tmp = gGeraSelect('T224', 'T224_Id', 'T224_Numero_DI', "WHERE T224_Id = $r_T224_Id");
		foreach($tmp as &$value) { $value['selected'] = true; }
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('est016FormDocumentos');
		$formArray = array(
			'campos' => array(
				'geral' => array(
				)
			)
		);
		//$formArray['campos']['geral']['__T227_T224_Id'] 		= array('name' => '__T227_T224_Id', 'label'=>'Número Pedido', 'type' => 'select', 'extra'=>'style="width:382px;"', 'disabled' => true, 'reject'=> true, 'select' => 
		//	$tmp
		//);
		$formArray['campos']['geral']['T227_Descricao'] 	= array('name' => 'T227_Descricao', 'label'=>'Descrição', 'type' => 'textarea','length' => array(3,60));
		$formArray['campos']['geral']['uploadArquivo'] 			= array('name' => 'uploadArquivo', 'label' => 'Arquivo documento', 'type' => 'file', 'length' => array(24, 24), 'reject' => true, 'uppercase' => false);
		$formArray['campos']['geral']['T227_Nome_Arquivo'] 		= array('name' => 'T227_Nome_Arquivo', 'type' => 'hidden', 'uppercase' => false);
		if (!empty($r_acaoId)) {
			 $formArray['campos']['geral']['est016FormDocumentosBtnNew'] = array('name' => 'est016FormDocumentosBtnNew', 'type' => 'button', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		}
		$formArray['campos']['geral']['T227_T224_Id'] 			= array('name' => 'T227_T224_Id', 'type' => 'hidden', 'value' => $r_T224_Id);
		$formArray['campos']['geral']['T227_C007_Id'] 			= array('name' => 'T227_C007_Id', 'type' => 'hidden', 'value' => $g['usuarioAtual']);
		$formArray['campos']['geral']['T227_Data_Hora_Upload'] 			= array('name' => 'T227_Data_Hora_Upload', 'type' => 'hidden', 'value' => date('Y-m-d H:i:s'));
		//$formArray['campos']['geral']['est016FormDocumentosBtnCln'] = array('name' => 'est016FormDocumentosBtnCln', 'type' => 'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['geral']['est016FormDocumentosBtnAdd'] = array('name' => 'est016FormDocumentosBtnAdd', 'type' => 'submit', 'reload' => false, 'value' => empty($r_acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
        $formArray['campos']['geral']['est016FormImportarDoc'] = array('name' => 'est016FormImportarDoc', 'type' => 'button', 'reload' => false, 'value' => 'Importar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['geral']['divReload'] 	= array('name' => 'divReload', 'label'=>'', 'type' => 'hidden','value' => @$r_divReload, 'reject' => true);
		
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
						echo <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									if (typeof jQuery != 'undefined') {
										divRefresh("{$g['divId']}", true);
										divRefresh('{$form->campoValorEnviado('divReload')}', true);
										divRefresh('{$g['divId']}', true);
									}
								} </script></div>
EOT;
					}
				} else {
					// Insert
					// Antes de criar o registro no banco, vamos verificar se o arquivo foi realmente enviado
					$arquivo = $form->campoValorEnviado('uploadArquivo');
					if ($arquivo['error'] == UPLOAD_ERR_OK) {
						// Neste caso, guardamos o nome do arquivo no banco
						$form->campoSetarValor('T227_Nome_Arquivo', $arquivo['name'], true);
						if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
							// Gravar o arquivo no disco
							$pathinfo = pathinfo($arquivo['name']);
							$pathinfo['extension'] = empty($pathinfo['extension']) ? '.tmp' : $pathinfo['extension'];
							//$dir = "{$g['pathDados']}documentos/T224_".str_repeat('0',5-strlen($form->campoValorEnviado('T227_T224_Id'))).$form->campoValorEnviado('T227_T224_Id');
							// Salvava na mesma pasta que salva os documentos do pedido, então não abrir o arquivo correto
							//$dir = "{$g['pathDados']}pedido";
							$dir = "{$g['pathDados']}ordemCompra";
							is_dir($dir) or mkdir($dir, 0777, true);
							$file = $dir . '/' . $g['mysqlLastId'] . '.' . $pathinfo['extension'];
							move_uploaded_file($arquivo['tmp_name'], $file);
							echo <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									if (typeof jQuery != 'undefined') {
										divRefresh("{$g['divId']}", true);
										divRefresh('{$form->campoValorEnviado('divReload')}', true);
										divRefresh('{$g['divId']}', true);
									}
								} </script></div>
EOT;
						}
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
						echo  $processado;
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
		$botoesEsq = '';
		$botoesDir = '';
		// $botoesDir .= '<button type="submit" id="content_form1_btn">Enviar</button>';
		// $botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo $g['smarty']->fetch('buttons-floatBottom.tpl');
		// Ações dos botões
		// A ação do botão Enviar é definido dentro do formulário
        echo <<<EOT
            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                $('#est016FormImportarDoc').unbind('click').bind('click', function(data) {
                    abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Importar Documento', '/est/est016/content/est016ContentImportarDoc/', '&T224_Id=' + encodeURIComponent('{$r_T224_Id}') + '&divReload=' + encodeURIComponent('{$r_divReload}'), [300,100]);
                });
            } </script></div>
EOT;
	break;
	
	case 'est016FormDetalhesCheque':
         global $g;
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
		$form_tabela = 'T226';
		$form_campo_pk = 'T226_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est016FormDetalhesCheque_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est016/form/est016FormDetalhesCheque/';
				
		// novo formulário
		$form = new Formulario('est016ContentDetalhesCheque');

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T226_Numero_Cheque' => array('name' => 'T226_Numero_Cheque', 'label' => 'Número', 'type' => 'text', 'length' => array(20, 120)),
					'T226_Data_Cheque' => array('name' => 'T226_Data_Cheque', 'label' => 'Data', 'type' => 'data', 'length' => array(20, 120)),					
					'T226_Agencia_Cheque' => array('name' => 'T226_Agencia_Cheque', 'label' => 'Agência', 'type' => 'text', 'length' => array(20, 120)),
					'T226_Conta_Corrente_Cheque' => array('name' => 'T226_Conta_Corrente_Cheque', 'label' => 'Conta Corrente', 'type' => 'text', 'length' => array(20, 120)),					
					'T226_Banco_Cheque' => array('name' => 'T226_Banco_Cheque', 'label' => 'Banco', 'type' => 'text', 'length' => array(50, 120)),									
					'T226_Nome_Cheque' => array('name' => 'T226_Nome_Cheque', 'label' => 'Nome', 'type' => 'text', 'length' => array(50, 120)),					
					'T226_Cidade_Cheque' => array('name' => 'T226_Cidade_Cheque', 'label' => 'Cidade', 'type' => 'text', 'length' => array(50, 120)),
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
							divRefresh('{$g['divIdParent']}', true);
						} </script></div>
EOT;
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
		//echo '<div style="padding:5px">Estorno de título - ID: '.$acaoId.'</div>';
		echo $gerado['pagina'];				
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('form.tpl');
				
	break;
	
	case 'est016FormPedidoFaturamento':
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
		$form_tabela = 'T224';
		$form_campo_pk = 'T224_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('est016FormPedidoFaturamentoBtnAdd', 'est016FormPedidoFaturamentoBtnCln', 'est016FormPedidoFaturamentoBtnNew');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est016/form/est016FormPedidoFaturamento/';

		// form editor
		$form_editor = array('geral' => '{"T224_D024_Id_Nfe":{"top":7,"left":18,"width":215,"hidden":0},"T224_D024_Id_Contas":{"top":30,"left":12,"width":215,"hidden":0},"est016FormPedidoFaturamentoBtnCln":{"top":51,"left":256,"width":null,"hidden":0},"est016FormPedidoFaturamentoBtnAdd":{"top":51,"left":336,"width":null,"hidden":0}}');

		// novo formulário
		$form = new Formulario('fis009FormCadastroPais');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T224_D024_Id_Nfe' => array('name' => 'T224_D024_Id_Nfe','label'=>'Cliente para emissão de NFe', 'type' => 'pesquisa', 'length' => array(50,30),
						'pesquisa' => array('D024','D024_Id','D024_Id,D024_Nome_Empresa,D020_Nome_Cidade,D018_UF,D024_Cpf,D024_Cnpj','left join D020 on D020_Id=D024_D020_Id left join D018 on D018_Id=D024_D018_Id WHERE D024_Flag_Fornecedor="S" and D024_Flag_Ativo="S" order by D024_Nome_Empresa','(%1) %2 [%3 - %4]'),
					),
					'T224_D024_Id_Contas' => array('name' => 'T224_D024_Id_Contas','label'=>'Cliente para contas a receber', 'type' => 'pesquisa', 'length' => array(50,30),
						'pesquisa' => array('D024','D024_Id','D024_Id,D024_Nome_Empresa,D020_Nome_Cidade,D018_UF,D024_Cpf,D024_Cnpj','left join D020 on D020_Id=D024_D020_Id left join D018 on D018_Id=D024_D018_Id where D024_Flag_Fornecedor="S" and D024_Flag_Ativo="S" order by D024_Nome_Empresa','(%1) %2 [%3 - %4]'),
					),
				)
			)
		);
		$formArray['campos']['geral']['est016FormPedidoFaturamentoBtnCln'] = array('name' => 'est016FormPedidoFaturamentoBtnCln', 'type'=>'button', 'reload' => false, 'value' => 'Limpar', 'style' => 'float:left;', 'reject' => true);
		$formArray['campos']['geral']['est016FormPedidoFaturamentoBtnAdd'] = array('name' => 'est016FormPedidoFaturamentoBtnAdd', 'type'=>'button', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:left;', 'reject' => true);

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
									{$alert}
								divRefresh('{$g['divIdParent']}', true);
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
									{$alert}
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

	break;	
	
	case 'est016formTrocas':

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
		$form_tabela = 'T222';
		$form_campo_pk = 'T222_Id';
        $form_parent = 'T224_Id';
        $form_parent_valor = $r_T224_Id;
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('est016formTrocasBtnAdd', 'est016formTrocasBtnCln', 'est016formTrocasBtnNew');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est016/form/est016formTrocas/';
		
		//form editor
		$form_editor = array( 'trocas' => '{"Procura_Produto_Troca":{"top":7,"left":53,"width":345,"hidden":0},"Descricao_Produto":{"top":30,"left":41,"width":345,"hidden":0},"Marca_Produto":{"top":53,"left":60,"width":125,"hidden":0},"T222_Quantidade":{"top":76,"left":35,"width":125,"hidden":0},"T222_Valor_Preco_Unitario":{"top":99,"left":65,"width":125,"hidden":0},"T222_Operacao":{"top":122,"left":42,"width":132,"hidden":0},"T222_Observacao":{"top":145,"left":30,"width":345,"hidden":0},"est016formTrocasBtnAdd":{"top":166,"left":384,"width":null,"hidden":0},"est016formTrocasBtnCln":{"top":166,"left":320,"width":null,"hidden":0},"est016BtnCadastrarProdutoTroca":{"top":166,"left":176,"width":null,"hidden":0}}');

		// Campo: Procura_Produto
		$pesquisaGridExtra = <<<EOT
			left join D049 on D049_D001_Id=D001_Id
			left join D009 on D009_D049_Id=D049_Id
			left join D082 on D082_Id=D049_D082_Id
			left join D037 on D037_Id=D001_D037_Id
			where (D009_C004_Id = '{$g['empresaAtual']}'
			and D001_Flag_Ativo!='N')
			group by D009_Id
			order by D001_Codigo_Produto
EOT;
		$pesquisaGrid = array(
			'D001',																								// tabela
			'D009_Id',																							// chave primaria
			'D001_Codigo_Produto,D001_Descricao_Produto,D082_Marca,D009_Quantidade_Estoque_Liquido',			// campos
			$pesquisaGridExtra,																					// extras sql
			'Pesquisa de Produto',																				// título da janela
			'Código,Descrição,Marca,Estoque',																	// descrição dos campos
			'est016CampoProcuraProdutoTroca',																	// callback js
			$g['divId'],																						// divId
			(isset($r_est016FormProdutosForm) ? $r_est016FormProdutosForm['T222_T224_Id'] : $r_T224_Id),		// parametro extra
			array(																								// parametros para cada campo do grid
				array('colunaAlinhar' => 'center'),
				array(),
				array(),
				array('callback' => 'gCorrigeNumero', 'colunaAlinhar' => 'right')
			)
		);
		// novo formulário
		$form = new Formulario('est016formTrocasForm');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'trocas' => array(
					'Procura_Produto_Troca' => array('name' => 'Procura_Produto_Troca' , 'label' => 'Produto', 'type' => 'pesquisaGrid', 'pesquisaGrid' => $pesquisaGrid, 'length' => array(64,120), 'reject' => true),
					'Descricao_Produto' => array('name' => 'Descricao_Produto', 'label' => 'Descrição', 'reject'=>true, 'readonly'=>true, 'type' => 'text', 'length' => array(64, 120)),
					'Marca_Produto' => array('name' => 'Marca_Produto', 'label' => 'Marca', 'type' => 'text', 'length' => array(20,255),'readonly'=>true, 'reject' => true),									
					'T222_Quantidade' => array('name' => 'T222_Quantidade', 'label' => 'Quantidade', 'type' => 'moeda', 'length' => array(20, 30)),
					'T222_Valor_Preco_Unitario' => array('name' => 'T222_Valor_Preco_Unitario', 'label' => 'Valor', 'type' => 'moeda', 'length' => array(20, 30)),
					'T222_Operacao' => array('name' => 'T222_Operacao', 'label' => 'Operação', 'type' => 'select', 'align' => 'right', 'style' => 'float:right;', 'select' => 
						array(
							array('title' => 'Parte Pagamento', 'value' => 'P', 'selected' => true),
							array('title' => 'Troca', 'value' => 'T'),
						)
					),					
					'T222_Observacao' => array('name' => 'T222_Observacao', 'label' => 'Observação', 'type' => 'text', 'length' => array(64, 120)), 
					// Campos Hidden
					'T222_T224_Id' => array('name' => 'T222_T224_Id', 'label' => '', 'type' => 'hidden', 'value' => @$r_T224_Id),
					'T222_C004_Id' => array('name' => 'T222_C004_Id', 'label' => '', 'type' => 'hidden', 'value' => $g['empresaAtual']),
					'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
					'T222_D009_Id' => array('name' => 'T222_D009_Id', 'label' => '', 'type' => 'hidden'),

				),
			)
		);
		// Botão Limpar / Novo Registro
		if (!empty($acaoId)) {
			 $formArray['campos']['trocas']['est016formTrocasBtnNew'] = array('name' => 'est016formTrocasBtnNew', 'type'=>'submit', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		}
		$formArray['campos']['trocas']['est016formTrocasBtnAdd'] = array('name' => 'est016formTrocasBtnAdd', 'type'=>'button', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['trocas']['est016formTrocasBtnCln'] = array('name' => 'est016formTrocasBtnCln', 'type'=>'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['trocas']['est016BtnCadastrarProdutoTroca'] = array('name' => 'est016BtnCadastrarProdutoTroca', 'type'=>'button', 'reload' => false, 'value' => 'Pré-Cadastrar Produto', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);	
		$form->definirFormularioArray($formArray);
		
		echo <<<EOT
		    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		    	$('#est016BtnCadastrarProdutoTroca').unbind('click');
				$('#est016BtnCadastrarProdutoTroca').unbind('click').bind('click', function(data) {
					//var id = $('#{$g['divId']} #T224_D024_Id').val();
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Pré-cadastrar Produto', '/cad/cad002/content/cad002ContentPreCadastro/', '', [600,300]);											
					return false;
				});
		    } </script></div>
EOT;

		/**
		 * PARTE 3: Processamento do formulário (se foi efnviado)
		 * 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		 */

		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
				// Form sem erros
			} else if ($formCodigo == 2) {
				$retorno = $EST016->incluirProdutoTroca($form);
				if ($retorno !== true) {
					echo gGeraAlertMsg('Erro', $retorno);	
				} else {
					//echo gGeraAlertMsg('Operação OK', "Com sucesso");	
					echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								divRefresh('{$form->campoValorEnviado('divReload')}', true);
								divRefresh('{$g['divId']}', true);
							} </script></div>
EOT;
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
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk, false, $form_parent, $form_parent_valor);


		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');

	break;	
	
	
    case 'est016FormAplicarDescontoPercentual':
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
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'T224';
		$form_campo_pk = 'T224_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est016AplicarDescontoBtnExecutar';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est016/form/est016FormAplicarDescontoPercentual/';

		//form editor
		$form_editor = array('geral' =>'{"Desconto":{"top":4,"left":18,"width":175},"CampoAplicarDesconto":{"top":26,"left":4,"width":177}}');
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					//'T224_Id' => array('name' => 'T224_Id', 'label' => 'T224_Id', 'type' => 'hidden', 'length' => array(40, 80), 'reject' => true, 'value' => $r_T224_Id),
     				'Desconto' => array('name' => 'Desconto', 'label' => '% Desconto', 'type' => 'moeda', 'length' => array(30, 80), 'reject' => true),
     				'CampoAplicarDesconto' => array('name' => 'CampoAplicarDesconto', 'label' => 'Desconto base', 'type'=>'select', 'reject' => true, 'select' => 
						array(
							array('title' => 'Preço tabela', 'value' => 'tabela','selected'=>true),
							array('title' => 'Preço pedido', 'value' => 'pedido'),
						),
					),
				)
			)
		);
        $form = new Formulario('form-est016FormAplicarDesconto');
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
				// Ações dos botões
				$novoIdTelaForm = uniqid();
				echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
						var \$buttons = {
							"Sim": function() {
								var CampoAplicarDesconto = $('#{$g['divId']} #CampoAplicarDesconto').val();
								var Desconto = $('#{$g['divId']} #Desconto').val();
								$('#{$g['divId']}').showLoading();
								$.getJSON('/est/est016/form_func-ajax/est016aplicarDesconto/?ajax=true&T224_Id=' + encodeURIComponent('{$r_T224_Id}') + '&CampoAplicarDesconto=' + encodeURIComponent(CampoAplicarDesconto) + '&Desconto=' + encodeURIComponent(Desconto) + '&callback=?', function(request) {
									if (request.code) {
										var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    				dialogConfirm('Ok', request.data, \$buttons);
					    				divRefresh('{$g['divIdParent']}');
					    				fecharJanela('{$g['divId']}');
									} else {
										var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    				dialogConfirm('Erro', request.data, \$buttons);
									}
									$('#{$g['divId']}').hideLoading();
								});
								$(this).dialog("destroy").remove();
							},
							"Não": function() { $(this).dialog("destroy").remove(); } 
						};
						dialogConfirm('Aplicar Desconto', 'Você tem certeza que deseja aplicar o desconto? ', \$buttons);
					} </script></div>
EOT;
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
		$botoesMid = '';
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="est016AplicarDescontoBtnExecutar">Executar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
    
    case 'est016FormImportarDoc':
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
        $form_tabela = 'T227';
        $form_campo_pk = 'T227_Id';
        // ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
        $enviar_btnId = 'est016FormDocumentosImportar';
        // Form que será executado ao clicar no botão
        $enviar_acaoHref = '/est/est016/form/est016FormImportarDoc/';
        // Array de definição do formulário
        $r_T224_Id = isset($r_est016FormDocumentos) ? $r_est016FormDocumentos['T227_T224_Id'] : $r_T224_Id;
                                                                   
        $tmp = gGeraSelect('T224', 'T224_Id', 'T224_Numero_DI', "WHERE T224_Id = $r_T224_Id");
        foreach($tmp as &$value) { $value['selected'] = true; }
        // Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
        $form = new Formulario('est016FormDocumentosImportar');
        $formArray = array(
            'campos' => array(
                'geral' => array(
                )
            )
        );
        $formArray['campos']['geral']['Numero_Pedido']     = array('name' => 'Numero_Pedido', 'label'=>'N° Pedido', 'type' => 'text','reject' => true,'length' => array(20,30));
        $formArray['campos']['geral']['T227_T224_Id']             = array('name' => 'T227_T224_Id', 'type' => 'hidden', 'value' => $r_T224_Id);
        $formArray['campos']['geral']['T227_C007_Id']             = array('name' => 'T227_C007_Id', 'type' => 'hidden', 'value' => $g['usuarioAtual']);
        $formArray['campos']['geral']['T227_Data_Hora_UpLoad']             = array('name' => 'T227_Data_Hora_UpLoad', 'type' => 'hidden', 'value' => date('Y-m-d H:i:s'));
        $formArray['campos']['geral']['divReload']     = array('name' => 'divReload', 'label'=>'', 'type' => 'hidden','value' => @$r_divReload, 'reject' => true);
        
        $form->definirFormularioArray($formArray);
        
        /**
        * PARTE 3: Processamento do formulário (se foi enviado)
        *            E carregamento dos valores (se não foi enviado e possui acaoId)
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
                        echo <<<EOT
                                <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                                    if (typeof jQuery != 'undefined') {
                                        divRefresh("{$g['divId']}", true);
                                        divRefresh('{$form->campoValorEnviado('divReload')}', true);
                                        divRefresh('{$g['divId']}', true);
                                    }
                                } </script></div>
EOT;
                    }
                } else {
                    // Importação
                    $retorno = $EST016->importarDocsPedido($form);
                    if ($retorno === true) {
                        $alert .= gGeraAlertMsg('Operação OK', 'Documentos importados com sucesso!', false);
                        echo <<<EOT
                            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                                divRefresh('{$form->campoValorEnviado('divReload')}', true);
                                if (typeof jQuery != 'undefined') {
                                    $("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
                                        setTimeout(function() {
                                    {$alert}
                                }, 150);
                                });
                                $("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
                                }
                            } </script></div>
EOT;
                    }else{
                       $alert .= gGeraAlertMsg('ERRO', "{$retorno}", false);
                        echo <<<EOT
                            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                                if (typeof jQuery != 'undefined') {
                                    {$alert}
                                    divRefresh('{$form->campoValorEnviado('divReload')}', true);
                                }
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
                        echo  $processado;
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
        $g['smarty']->display('form.tpl');
        
        
        
        /**
        * Botões
        */
        // Definição dos botões
        $botoesEsq = '';
        $botoesDir = '';
        $botoesDir .= '<button type="submit" id="est016FormDocumentosImportar">Enviar</button>';
        $botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
        $g['smarty']->assign('botoesId', $g['divId']);
        $g['smarty']->assign('botoesEsq', $botoesEsq);
        $g['smarty']->assign('botoesDir', $botoesDir);
        echo $g['smarty']->fetch('buttons-floatBottom.tpl');
        // Ações dos botões
        // A ação do botão Enviar é definido dentro do formulário
        echo <<<EOT
            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
            } </script></div>
EOT;
    break;
    
    case 'est016FormCancelarPedido':

        /**
        * PARTE 1: Variavéis envidas para o formulário
        */
        // Variável indica qual tab do form será exibida
        $tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
        // Utilizada em UPDATE, é o ID do registro que está sendo editado
        $T224_Id = (isset($r_T224_Id)) ? $r_T224_Id : false;
        
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

		$form_editor = array(
			'geral'=>'{"T224_Observacao_Pedido_Perdido":{"top":7,"left":14,"height":113,"width":242,"hidden":0}}'
		);

        // A qual tabela este form está relacionada, e a chave primária da tabela
        $form_tabela = 'T224';
        $form_campo_pk = 'T224_Id';
        // ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
        $enviar_btnId = 'est016FormCancelarPedido_Btn';
        // Form que será executado ao clicar no botão
        $enviar_acaoHref = '/est/est016/form/est016FormCancelarPedido/';
        // Array de definição do formulário
        //$r_T224_Id = isset($r_est016FormDocumentos) ? $r_est016FormDocumentos['T227_T224_Id'] : $r_T224_Id;
                                                                   
        ///$tmp = gGeraSelect('D047', 'D047_Id', 'D047_Descricao_Motivo', "order by D047_Descricao_Motivo");
//        foreach($tmp as &$value) { $value['selected'] = true; }
        // Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
        $form = new Formulario('est016FormCancelarPedidoForm');
        //$formArray['campos']['geral']['T224_D047_Id']     				= 
        $formArray['campos']['geral']['T224_Observacao_Pedido_Perdido'] = array('name' => 'T224_Observacao_Pedido_Perdido', 'label'=>'Observação', 'type' => 'textarea');
        $formArray['campos']['geral']['T224_Id'] = array('name' => 'T224_Id', 'label'=>'Observação', 'type' => 'hidden', 'value'=>$T224_Id);
        //$formArray['campos']['geral']['divReload']     = array('name' => 'divReload', 'label'=>'', 'type' => 'hidden','value' => @$r_divReload, 'reject' => true);
        
        $form->definirFormularioArray($formArray);
        
        /**
        * PARTE 3: Processamento do formulário (se foi enviado)
        *            E carregamento dos valores (se não foi enviado e possui acaoId)
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
                        echo <<<EOT
                                <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                                    if (typeof jQuery != 'undefined') {
                                        divRefresh("{$g['divId']}", true);
                                        divRefresh('{$form->campoValorEnviado('divReload')}', true);
                                        divRefresh('{$g['divId']}', true);
                                    }
                                } </script></div>
EOT;
                    }
                } else {
                    $retorno = $EST016->est016VerificaCancelarPedido($form);
                    
                    if ($retorno === true) {
                        $alert .= gGeraAlertMsg('Operação OK', 'Pedido Cancelado com sucesso!', false);
                        echo <<<EOT
                            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                                divRefresh('{$g['divIdParent']}', true);
                                if (typeof jQuery != 'undefined') {
                                    $("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
                                        setTimeout(function() {
                                    {$alert}
                                }, 150);
                                });
                                $("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
                                }
                            } </script></div>
EOT;
                    }else{
                       $alert = gGeraAlertMsg('ERRO', "{$retorno}", false);
                        echo <<<EOT
                            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                                if (typeof jQuery != 'undefined') {
                                    {$alert}
                                }
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
                        echo  $processado;
                    }
                }
            }
        }
        
        
        /**
        * PARTE 4: Gerar o formulário
        */
        $gerado = gGeraCamposForm($form, $tab, $T224_Id, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);
        
        
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
        $botoesEsq = '';
        $botoesDir = '';
        $botoesDir .= '<button type="submit" id="est016FormCancelarPedido_Btn">Enviar</button>';
        $botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
        $g['smarty']->assign('botoesId', $g['divId']);
        $g['smarty']->assign('botoesEsq', $botoesEsq);
        $g['smarty']->assign('botoesDir', $botoesDir);
        echo $g['smarty']->fetch('buttons-floatBottom.tpl');
        // Ações dos botões
        // A ação do botão Enviar é definido dentro do formulário
        echo <<<EOT
            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
            } </script></div>
EOT;
    break;
    
    
    case 'est016FormAgregados':
            /**
            * PARTE 1: Variavéis envidas para o formulário
            */
            // Variável indica qual tab do form será exibida
            $tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
            // Utilizada em UPDATE, é o ID do registro que está sendo editado
            $T225_Id = (isset($_REQUEST['T225_Id'])) ? $_REQUEST['T225_Id'] : false;
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
            $form_tabela = 'T204';
            $form_campo_pk = 'T204_Id';
            // ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
            $enviar_btnId = array('est016FormAgregadosBtnAdd', 'est016FormAgregadosBtnCln', 'est016FormAgregadosBtnNew');
            // Form que será executado ao clicar no botão
            $enviar_acaoHref = '/est/est016/form/est016FormAgregados/';

            // novo formulário
            $form = new Formulario('est016FormAgregados');

            // Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
            $formArray = array(
                'campos' => array(
                    'geral' => array(
                    'T204_T225_Id' => array('name' => 'T204_T225_Id', 'type' => 'hidden', 'value' => @$T225_Id, 'length' => array(50,30)),
                    'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
                    'T204_Descricao' => array('name' => 'T204_Descricao','label'=>'Descrição','type' => 'text', 'length' => array(50,30)),
                    'T204_Valor_Unitario' => array('name' => 'T204_Valor_Unitario','label'=>'Valor','type' => 'moeda','align' => 'right','length' => array(10,10)),
                    'T204_Observacao' => array('name' => 'T204_Observacao','label'=>'Observação','type' => 'textarea', 'length' => array(2,47)),
                    'T204_T209_Id' => array('name' => 'T204_T209_Id','label'=>'Tipo','type' => 'select', 
                    	'select' => gGeraSelect('T209', 'T209_Id', 'T209_Nome'))
                    ),
                ),
            );
            
            // Botão Limpar / Novo Registro
            if (!empty($acaoId)) {
                 $formArray['campos']['geral']['est016FormAgregadosBtnNew'] = array('name' => 'est016FormProcessosBtnNew', 'type'=>'button', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
            }
            //$formArray['campos']['geral']['est016FormAgregadosBtnCln'] = array('name' => 'est016FormProcessosBtnCln', 'type'=>'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
            $formArray['campos']['geral']['est016FormAgregadosBtnAdd'] = array('name' => 'est016FormProcessosBtnAdd', 'type'=>'submit', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
            $form->definirFormularioArray($formArray);




            /**
            * PARTE 3: Processamento do formulário (se foi enviado)
            *          E carregamento dos valores (se não foi enviado e possui acaoId)
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
                                echo <<<EOT
                            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                                divRefresh('{$form->campoValorEnviado('divReload')}', true);
                                divSetarAcaoId('{$g['divId']}', '');
                                divRefresh('{$g['divId']}', true);
                                } </script></div>
EOT;
                            }
                    } else {
                        // Insert
                        if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
                            // Imprime uma mensagem e fecha a janela
                            echo <<<EOT
                            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                                divRefresh('{$form->campoValorEnviado('divReload')}', true);
                                divRefresh('{$g['divId']}', true);
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
            $g['smarty']->display('form.tpl');

    break;

    case 'est016FormIncluirPedido':
    	global $g;
    	/**
			* PARTE 1: Variavéis envidas para o formulário
			*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$r_T224_Id = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$GerarOCEstoque = isset($r_GerarOCEstoque) ? $r_GerarOCEstoque : false;

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
		
		// busca o ID do cliente pra carregar os contatos, caso seja edição
		$idCliente='99999999999';
		if ($acaoId>0){
			$sql = "SELECT T224_D024_Id,D020_D022_Id,D022_D036_Id, D024_D022_Id FROM T224 LEFT JOIN D024 ON D024_Id=T224_D024_Id LEFT JOIN D020 ON D020_Id=D024_D020_Id LEFT JOIN D022 ON D022_Id=D020_D022_Id LEFT JOIN D036 ON D036_Id=D022_D036_Id WHERE T224_Id = '{$acaoId}'";
			$result = mysql_query($sql);
			$aD024 = mysql_fetch_assoc($result);		
			$idCliente=$aD024['T224_D024_Id'];
			$idTransportadora=$aD024['D024_D022_Id'];
            $idTipoTransp=$aD024['D022_D036_Id'];
		} 		
		$idTipoTransp=isset($idTipoTransp) ? $idTipoTransp : false;

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'T224';
		$form_campo_pk = 'T224_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est016FormCabecalhoBtnAdd';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est016/form/est016FormIncluirPedido/';
		
		$form_editor = array(
			'geral'=>'{"est016FormContatos":{"top":5,"left":752.0000610351562,"width":null,"hidden":0},"T224_Flag_Lista_Preco":{"top":7,"left":4,"width":76,"hidden":1},"T224_D024_Id":{"top":7,"left":91,"width":263,"hidden":0},"T224_D013_Id":{"top":7,"left":533,"width":159,"hidden":0},"T224_C007_Id_Vendedor_Interno":{"top":30,"left":75,"width":192,"hidden":0},"T224_Data_Emissao":{"top":30,"left":706.0000610351562,"width":56,"hidden":0},"T224_D006_Id":{"top":53,"left":95,"width":192,"hidden":0},"T224_D021_Id":{"top":53,"left":499,"width":240,"hidden":0},"T224_Flag_ACP":{"top":76,"left":45,"width":192,"hidden":0},"T224_Percentual_Comissao":{"top":76,"left":686.0000610351562,"width":56,"hidden":0},"T224_Prazo_Entrega":{"top":99,"left":39,"width":280,"hidden":0},"T224_Data_Entrega":{"top":99,"left":400,"width":280,"hidden":0},"est016FormVeiculos":{"top":120,"left":352,"width":null,"hidden":0},"T224_D116_Id":{"top":122,"left":87,"width":200,"hidden":0},"tituloOC":{"top":142,"left":364,"width":null,"hidden":1},"T224_Ordem_Compra":{"top":168,"left":35,"width":280,"hidden":0},"tituloObservacoes":{"top":188,"left":385,"width":null,"hidden":1},"T224_Observacao":{"top":214,"left":90,"width":292,"hidden":0},"T224_Observacao_Comercial":{"top":214,"left":439,"width":292,"hidden":0},"T224_Observacao_Nota_Fiscal":{"top":282.9999694824219,"left":69,"width":292,"hidden":0},"T224_Observacao_1":{"top":282.9999694824219,"left":486,"width":292,"hidden":0},"tituloTransporte":{"top":348.9999694824219,"left":398,"width":null,"hidden":1},"T224_Flag_Frete":{"top":374.9999694824219,"left":97,"width":112,"hidden":0},"T224_Valor_Frete":{"top":374.9999694824219,"left":501,"width":120,"hidden":0},"est016FormCabecalhoBtnCln":{"top":395.9999694824219,"left":688.0000610351562,"width":null,"hidden":0},"est016FormCabecalhoBtnAdd":{"top":395.9999694824219,"left":752.0000610351562,"width":null,"hidden":0},"T224_D022_Id":{"top":397.9999694824219,"left":48,"width":254,"hidden":0},"T224_D036_Id":{"top":397.9999694824219,"left":534,"width":128,"hidden":0},"button":{"top":418.9999694824219,"left":416,"width":null,"hidden":0},"TransportadoraPadrao":{"top":420.9999694824219,"left":16,"width":282,"hidden":0}}'
		);

		// novo formulário
		$form = new Formulario('form_est016_form1');

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T224_D024_Id' => array('name' => 'T224_D024_Id','label'=>'Cliente', 'type' => 'pesquisa','extra' => "onchange=\"carregaCFOP_Contatos(this, '{$g['divId']}'); carregaListaPreco(this, '{$g['divId']}');\"", 'pesquisa' => array('D024','D024_Id','D024_Id,D024_Nome_Empresa,D024_Nome_Fantasia,D020_Nome_Cidade,D018_UF,D024_Cpf,D024_Cnpj','left join D020 on D020_Id=D024_D020_Id left join D018 on D018_Id=D024_D018_Id WHERE D024_Flag_Fornecedor="S" and D024_Flag_Ativo="S" order by D024_Nome_Empresa','(%1) %2 - %3 [%4 - %5]'), 'length' => array(50,30)),
					'T224_D013_Id' => array('name' => 'T224_D013_Id', 'label' => 'Contato', 'type' => 'select',
						'select' => gGeraSelect('D013','D013_Id','D013_Nome_Contato',"where D013_D024_Id=$idCliente AND D013_Flag_Ativo = 'S'",true),
						'selectRefresh' => array('D013','D013_Id','D013_Nome_Contato,D013_DDD_Telefone_1,D013_Telefone_1',"where D013_D024_Id=$idCliente AND D013_Flag_Ativo = 'S'",true),
					),
					'est016FormContatos' => array('name' => 'est016FormContatos', 'type'=>'button', 'reload' => false, 'value' => 'Incluir', 'reject' => true),
					'T224_D006_Id' => array('name' => 'T224_D006_Id', 'label'=>'CFOP','type' => 'select', 'select' => 
        						gGeraSelect('D006','D006_Id','D006_Codigo_CFOP,D006_Descricao',' WHERE D006_Flag_Inativo!="S" AND D006_Flag_Departamento="COMERCIAL"  ORDER BY D006_Codigo_CFOP',true),
					),											
					'T224_Data_Emissao' => array('name' => 'T224_Data_Emissao','label'=>'Emissão','type' => 'data', 'length' => array(20,10), 'value'=>date("d/m/Y")),
					'T224_C007_Id_Vendedor_Interno' => array('name' => 'T224_C007_Id_Vendedor_Interno', 'label'=>'Vendedor','type' => 'select', 'select' => 
								gGeraSelect('C007','C007_Id','C007_Login','WHERE C007_Flag_Vendedor!=""',true),
					),
					'T224_D021_Id' => array('name' => 'T224_D021_Id', 'label'=>'Representante','type' => 'select', 'select' => 
								gGeraSelect('D021','D021_Id','D021_Nome_Empresa','',true),
					),	
					'T224_Percentual_Comissao' => array('name' => 'T224_Percentual_Comissao', 'label'=>'Comissão %','value'=>'0,00','type' => 'moeda', 'length' => array(20,10)),
					'T224_Flag_ACP' => array('name' => 'T224_Flag_ACP', 'label'=>'Tipo Documento','type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => 0),
							array('title' => 'NF', 'value' => 1),
							array('title' => 'Romaneio', 'value' => 2),
							array('title' => 'ACP', 'value' => 3),
							array('title' => 'Cupom Fiscal', 'value' => 4),
							array('title' => 'NF D1', 'value' => 5),
							array('title' => 'NFe', 'value' => 6, 'selected' => true),
							array('title' => 'NFSe', 'value' => 7),							
						)
					),
					'T224_Prazo_Entrega' => array('name' => 'T224_Prazo_Entrega', 'label'=>'Prazo de Entrega','type' => 'text', 'length' => array(53,30)),
					'T224_Data_Entrega' => array('name' => 'T224_Data_Entrega', 'label'=>'Data entrega','type' => 'data'),
					'T224_D116_Id' => array('name' => 'T224_D116_Id', 'label' => 'Veículo','type' => 'select',
						'select'=> gGeraSelect('D116', 'D116_Id', 'D116_Placa,D133_Modelo,D134_Cor', "LEFT JOIN D133 on D133_Id=D116_D133_Id LEFT JOIN D134 on D134_Id=D116_D134_Id WHERE D116_D024_Id=$idCliente", true),
						'selectRefresh' => array('D116', 'D116_Id', 'D116_Placa,D133_Modelo,D134_Cor', "LEFT JOIN D133 on D133_Id=D116_D133_Id LEFT JOIN D134 on D134_Id=D116_D134_Id WHERE D116_D024_Id=$idCliente", true)
					),
					'T224_Flag_Lista_Preco' => 	array('name' => 'T224_Flag_Lista_Preco', 'label' => 'Lista Preço', 'type' => 'select', 'select' => 
						array(
							array('title' => 'Preço 1', 'value' => '1'),   
							array('title' => 'Preço 2', 'value' => '2'),   
							array('title' => 'Preço 3', 'value' => '3'),
						)
					),
					'tituloOC' => array('name' => 'tituloOC', 'label'=>'Ordem de Compra','type' => 'titulo', 'reject' => true),
					'T224_Ordem_Compra' => array('name' => 'T224_Ordem_Compra', 'label'=>'Ordem de Compra','type' => 'text', 'length' => array(53,30)),
					'tituloObservacoes' => array('name' => 'tituloObservacoes', 'label'=>'Observações','type' => 'titulo', 'reject' => true),
					'T224_Observacao' => array('name' => 'T224_Observacao', 'label'=>'Cliente','type' => 'textarea','extra' => 'title="Esta observação será visualizada pelo cliente (Não será impressa na DANFE)"', 'length' => array(3,50)),
					'T224_Observacao_Comercial' => array('name' => 'T224_Observacao_Comercial', 'label'=>'Comercial Interna','type' => 'textarea','extra' => 'title="Observação de uso interno da área comercial (Não será impressa na DANFE)"', 'length' => array(3,50)),
					'T224_Observacao_Nota_Fiscal' => array('name' => 'T224_Observacao_Nota_Fiscal', 'label'=>'Nota Fiscal','type' => 'textarea','extra' => 'title="Esta observação será impressa na DANFE"', 'length' => array(3,50)),
					'T224_Observacao_1' => array('name' => 'T224_Observacao_1', 'label'=>'Almox.:','type' => 'textarea','extra' => 'title="Observação de uso interno da área de estoque (Não será impressa na DANFE)"', 'length' => array(3,50)),
					'tituloTransporte' => array('name' => 'tituloTransporte', 'label'=>'Transporte','type' => 'titulo', 'reject' => true),
					'T224_Flag_Frete' => array('name' => 'T224_Flag_Frete','label'=>'Frete', 'type' => 'select',
						'select' => array(
							array('title' => '0-EMITENTE', 'value' => '0'),
							array('title' => '1-DESTINATÁRIO', 'value' => '1'),
							array('title' => '2-TERCEIROS', 'value' => '2'),
							array('title' => '9-SEM FRETE', 'value' => '9'),
						),
					),
					'T224_Valor_Frete' => array('name' => 'T224_Valor_Frete', 'label'=>'Valor Frete','value'=>'0,00','type' => 'moeda', 'length' => array(20,20)),
					'T224_D022_Id' => array('name' => 'T224_D022_Id', 'label' => 'Transportadora', 'type' => 'pesquisa', 'pesquisa' => array('D022','D022_Id','D022_Nome_Empresa,D020_Nome_Cidade,D018_UF','left join D020 on D020_Id=D022_D020_Id left join D018 on D018_Id=D022_D018_Id'),'extra' => "onchange=\"carregaTipoTransp(this, '{$g['divId']}')\"", 'length' => array(50,90)),
					'T224_D036_Id' => array('name' => 'T224_D036_Id', 'label' => 'Tipo', 'type' => 'select', 'select' =>
							gGeraSelect('D036','D036_Id','D036_Tipo_Transporte','',true),
					),
					'TransportadoraPadrao' => array('name' => 'TransportadoraPadrao', 'label' => 'Transportadora Padrão', 'type' => 'select','reject' =>true ,'disabled' => true,
						'select' => gGeraSelect('D022','D022_Id','D022_Nome_Empresa,D020_Nome_Cidade,D018_UF',"left join D020 on D020_Id=D022_D020_Id left join D018 on D018_Id=D022_D018_Id",false), 
					),
					'button' => array('name' => 'button', 'type' => 'button','value' =>'Usar Padrão','reload' => false,'extra' => "onclick='copiaTransportadora({$idTipoTransp})'",'reject'=>true),
					'nada' => array('name' => 'nada', 'label' => 'Transportadora', 'type' => 'hidden', 'reject'=>true, 'length' => array(30,30)),
					'T224_Flag_Revenda_Consumidor' => array('name' => 'T224_Flag_Revenda_Consumidor', 'label'=>'T224_Flag_Revenda_Consumidor','type' => 'hidden'
						//'select' => 
							//array(
								//array('title' => '...', 'value' => 0, 'selected' => true),
								//array('title' => 'Revenda', 'value' => 1),
								//array('title' => 'Consumidor atacado', 'value' => 2),
								//array('title' => 'Consumidor varejo', 'value' => 3),
							//)
					),
					'T224_Quantidade_Volumes' => array('name' => 'T224_Quantidade_Volumes', 'label'=>'Volumes','type' => 'text'),
					'T224_C004_Id' => array('name' => 'T224_C004_Id', 'label'=>'Transporte','type' => 'hidden', 'value' => $g['empresaAtual']),
					'GerarOCEstoque' => array('name' => 'GerarOCEstoque','label'=>'GerarOCEstoque', 'type' => 'hidden', 'value' => @$r_GerarOCEstoque,'reject' => true),
				)
			)
		);
		/** Botão Limpar / Novo Registro
		if (!empty($acaoId)) {
			$formArray['campos']['geral']['est016FormCabecalhoBtnNew'] = array('name' => 'est016FormCabecalhoBtnNew', 'type'=>'submit', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right; margin-top:20px;', 'reject' => true);
		}
		*/
		//$formArray['campos']['geral']['est016FormCabecalhoBtnCln'] = array('name' => 'est016FormCabecalhoBtnCln', 'type'=>'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right; margin-top:20px;', 'reject' => true);
		//$formArray['campos']['geral']['est016FormCabecalhoBtnAdd'] = array('name' => 'est016FormCabecalhoBtnAdd', 'type'=>'button', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right; margin-top:20px;', 'reject' => true);
		$form->definirFormularioArray($formArray);
		
		$sql = "SELECT T224_D024_Id FROM T224 WHERE T224_Id = '{$r_T224_Id}';";
		$resultado = mysql_query($sql);
		$row = mysql_fetch_array($resultado);
		echo <<<EOT
		    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		    	$('#est016FormContatos').unbind('click');
				$('#est016FormContatos').unbind('click').bind('click', function(data) {
					var id = '{$row['T224_D024_Id']}';
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Cliente/Fornecedor - ID: ' + id, '/cad/cad001/content/cad001contentCadastro/', '&acaoId=' + encodeURIComponent(id) + '&tabela=D024' + '&tab=contatos', [950,553]);
					return false;
				});
		    } </script></div>
EOT;

		/**
		* Na maioria dos casos não será necessário alterar nada daqui para baixo
		* Somente em casos especiais, para alterar pequenos detalhes ou modificações do visual
		*/


		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		* E carregamento dos valores (se não foi enviado e possui acaoId)
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
					$T224 = mysql_query("SELECT T224_Flag_Lista_Preco, T224_D024_Id, T224_D006_Id FROM T224 WHERE T224_Id = '{$acaoId}'");
 					$rT224 = mysql_fetch_array($T224);
					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {						
						echo gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!');
						$lista = $form->campoValorEnviado('T224_Flag_Lista_Preco');
						//Verifica se a lista de preço foi alterada
						if($rT224['T224_Flag_Lista_Preco'] != $lista){							
							$EST016->alteraLista($acaoId, $lista);
							echo gGeraAlertMsg('Atenção', 'A lista de preço foi alterada, os valores de tabela dos produtos serão modificados.');	
						}					
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								divRefresh('{$r_divIdMain}', true);
							} </script></div>
EOT;
					}
				} else {
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						$T224_Id = $g['mysqlLastId'];
						$T224 = mysql_query("SELECT T224_Flag_Lista_Preco, T224_D024_Id, T224_D006_Id FROM T224 WHERE T224_Id = '{$T224_Id}'");
 						$rT224 = mysql_fetch_array($T224);
						//Altera a empresa a faturar caso possua no cadastro do cliente
						$D024 = mysql_query("SELECT D024_Empresa_Emissora FROM D024 WHERE D024_Id = '{$rT224['T224_D024_Id']}'");
						$resD024 = mysql_fetch_array($D024); 
						if(isset($resD024['D024_Empresa_Emissora'])) {
							mysql_query("UPDATE T224 SET T224_D024_Id_Nfe = '{$resD024['D024_Empresa_Emissora']}' WHERE T224_Id = '{$T224_Id}'");
						}
						// Imprime uma mensagem e fecha a janela
						$alert = gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!');
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								divRefresh('{$g['divIdParent']}');
								abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' ,unique(), '', 'Editar Ordem de Compra - ID: ' + {$T224_Id}, '/est/est016/content/est016content02/', '&acaoId=' + encodeURIComponent('{$T224_Id}') + '&tabela=T224', [1000,615]);
							} </script></div>
EOT;
						$acaoId = $g['mysqlLastId'];
						if($form->campoValorEnviado('GerarOCEstoque') == true){
							$retorno = $EST016->insereItemEstoque($acaoId);
						}
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
		$botoesMid = '';
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="est016FormCabecalhoBtnAdd">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
    break;

    case 'est016FormAplicarDescontoReais':
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
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'T224';
		$form_campo_pk = 'T224_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est016AplicarDescontoReaisBtnExecutar';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est016/form/est016FormAplicarDescontoReais/';

		$T224 = mysql_query("SELECT T224_Valor_Total,T224_Valor_Desconto_Manual FROM T224 WHERE T224_Id='{$r_T224_Id}'");
		$mT224 = mysql_fetch_array($T224);

		// $valorTotalAntigo = $mT224['T224_Valor_Total'] + $mT224['T224_Valor_Desconto_Manual'];
		// $descontoPercentual = gCorrigeNumero((($valorTotalAntigo - $mT224['T224_Valor_Total']) / $valorTotalAntigo) * 100);   
		// $descontoPercentual = ($descontoPercentual == "0,00" ? "" : $descontoPercentual);

		//form editor
		$form_editor = array('geral' =>'{"DescontoReais":{"top":7,"left":5,"width":178,"hidden":0}}');
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					//'T224_Id' => array('name' => 'T224_Id', 'label' => 'T224_Id', 'type' => 'hidden', 'length' => array(40, 80), 'reject' => true, 'value' => $r_T224_Id),
     				'DescontoReais' => array('name' => 'DescontoReais', 'label' => 'Valor (R$)', 'value'=>'', 'type' => 'moeda', 'length' => array(30, 80), 'reject' => true),
				)
			)
		);
        $form = new Formulario('form-est016FormAplicarDesconto');
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
				// Ações dos botões
				$novoIdTelaForm = uniqid();
				echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
						var \$buttons = {
							"Sim": function() {
								var CampoAplicarDesconto = $('#{$g['divId']} #CampoAplicarDesconto').val();
								var DescontoReais = $('#{$g['divId']} #DescontoReais').val();
								$('#{$g['divId']}').showLoading();
								$.getJSON('/est/est016/form_func-ajax/est016aplicarDescontoReais/?ajax=true&T224_Id=' + encodeURIComponent('{$r_T224_Id}') + '&DescontoReais=' + encodeURIComponent(DescontoReais) + '&callback=?', function(request) {
									if (request.code) {
										var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    				dialogConfirm('Ok', request.data, \$buttons);
					    				divRefresh('{$g['divIdParent']}');
					    				fecharJanela('{$g['divId']}');
									} else {
										var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    				dialogConfirm('Erro', request.data, \$buttons);
									}
									$('#{$g['divId']}').hideLoading();
								});
								$(this).dialog("destroy").remove();
							},
							"Não": function() { $(this).dialog("destroy").remove(); } 
						};
						dialogConfirm('Aplicar Desconto', 'Você tem certeza que deseja aplicar o desconto? ', \$buttons);
					} </script></div>
EOT;
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
		$botoesMid = '';
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="est016AplicarDescontoReaisBtnExecutar">Executar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'est016FormAplicarDescontoTotal':
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
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'T224';
		$form_campo_pk = 'T224_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est016AplicarDescontoTotalBtnExecutar';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est016/form/est016FormAplicarDescontoTotal/';

		$T224 = mysql_query("SELECT T224_Valor_Total FROM T224 WHERE T224_Id='{$r_T224_Id}'");
		$mT224 = mysql_fetch_array($T224);

		//form editor
		$form_editor = array('geral' =>'{"DescontoValorTotal":{"top":7,"left":5,"width":178,"hidden":0}}');
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
     				'DescontoValorTotal' => array('name' => 'DescontoValorTotal', 'label' => 'Valor Total (R$)', 'value'=>gCorrigeNumero($mT224['T224_Valor_Total']), 'type' => 'moeda', 'length' => array(30, 80), 'reject' => true),
				)
			)
		);
        $form = new Formulario('form-est016FormAplicarDescontoTotal');
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
				// Ações dos botões
				$novoIdTelaForm = uniqid();
				echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
						var \$buttons = {
							"Sim": function() {
								var DescontoValorTotal = $('#{$g['divId']} #DescontoValorTotal').val();
								$('#{$g['divId']}').showLoading();
								$.getJSON('/est/est016/form_func-ajax/est016aplicarDescontoTotal/?ajax=true&T224_Id=' + encodeURIComponent('{$r_T224_Id}') + '&DescontoValorTotal=' + encodeURIComponent(DescontoValorTotal) + '&callback=?', function(request) {
									if (request.code) {
										var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    				dialogConfirm('Ok', request.data, \$buttons);
					    				divRefresh('{$g['divIdParent']}');
					    				fecharJanela('{$g['divId']}');
									} else {
										var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    				dialogConfirm('Erro', request.data, \$buttons);
									}
									$('#{$g['divId']}').hideLoading();
								});
								$(this).dialog("destroy").remove();
							},
							"Não": function() { $(this).dialog("destroy").remove(); } 
						};
						dialogConfirm('Aplicar Desconto', 'Você tem certeza que deseja aplicar o desconto? ', \$buttons);
					} </script></div>
EOT;
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
		$botoesMid = '';
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="est016AplicarDescontoTotalBtnExecutar">Executar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'est016FormCotar':
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
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'T189';
		$form_campo_pk = 'T189_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est016FormCotar_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est016/form/est016FormCotar/';

		//form editor
		$form_editor = array(
			'geral' =>'{"T189_Observacao":{"top":7,"left":5,"height":120,"width":217,"hidden":0},"T189_T225_Id":{"top":122,"left":0,"width":127,"hidden":1},"T189_D009_Id":{"top":145,"left":0,"width":127,"hidden":1}}'
		);
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
     				'T189_Observacao' => array('name' => 'T189_Observacao', 'label' => 'Observação', 'type' => 'textarea', 'length' => array(8, 40)), 
					// Campos Hidden
					'T189_T225_Id' => array('name' => 'T189_T225_Id', 'label' => '', 'type' => 'hidden', 'value' => $r_T225_Id),
					'T189_D009_Id' => array('name' => 'T189_D009_Id', 'label' => '', 'type' => 'hidden', 'value' => $r_D009_Id),
				)
			)
		);
        $form = new Formulario('est016FormCotar');
		$form->definirFormularioArray($formArray);
		/**
		 * PARTE 3: Processamento do formulário (se foi enviado)
		 * E carregamento dos valores (se não foi enviado e possui acaoId)
		 */

		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			} else if ($formCodigo == 2) {
				$EST016->cotarProduto($form);
				echo <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
						divRefresh('{$g['divIdParent']}', true);
						$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
					} </script></div>
EOT;
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

	case 'est016FormComprar':
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
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'T189';
		$form_campo_pk = 'T189_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est016FormComprar_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est016/form/est016FormComprar/';

		//form editor
		$form_editor = array(
			'geral' =>'{"T189_Observacao":{"top":7,"left":5,"height":120,"width":217,"hidden":0},"T189_T225_Id":{"top":122,"left":0,"width":127,"hidden":1},"T189_D009_Id":{"top":145,"left":0,"width":127,"hidden":1}}'
		);
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
     				'T189_Observacao' => array('name' => 'T189_Observacao', 'label' => 'Observação', 'type' => 'textarea', 'length' => array(8, 40)), 
					// Campos Hidden
					'T189_T225_Id' => array('name' => 'T189_T225_Id', 'label' => '', 'type' => 'hidden', 'value' => $r_T225_Id),
					'T189_D009_Id' => array('name' => 'T189_D009_Id', 'label' => '', 'type' => 'hidden', 'value' => $r_D009_Id),
				)
			)
		);
        $form = new Formulario('est016FormCotar');
		$form->definirFormularioArray($formArray);
		/**
		 * PARTE 3: Processamento do formulário (se foi enviado)
		 * E carregamento dos valores (se não foi enviado e possui acaoId)
		 */

		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			} else if ($formCodigo == 2) {
				$EST016->comprarProduto($form);
				echo <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
						divRefresh('{$g['divIdParent']}', true);
						$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
					} </script></div>
EOT;
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

	case 'est016FormMudarPedidoEmpresa':
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
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'T224';
		$form_campo_pk = 'T224_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est016FormMudarPedidoEmpresa_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est016/form/est016FormMudarPedidoEmpresa/';

		//form editor
		$form_editor = array(
			
		);
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'T224_C004_Id' => array('name' => 'T224_C004_Id', 'label' => 'Empresa', 'type' => 'select', 'select' => 
						gGeraSelect('C004', 'C004_Id', 'C004_Nome_Empresa', '')
					), 
				)
			)
		);
		$form = new Formulario('est016FormMudarPedidoEmpresa');
		$form->definirFormularioArray($formArray);
		/**
		 * PARTE 3: Processamento do formulário (se foi enviado)
		 * E carregamento dos valores (se não foi enviado e possui acaoId)
		 */

		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
			} else if ($formCodigo == 2) {
				$C004_Id_Novo = $form->campoValorEnviado('T224_C004_Id');
				$retorno = $EST016->mudarEmpresaPedidosSelecionados($C004_Id_Novo);
				if($retorno === true){
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
							divRefresh('{$g['divIdParent']}', true);
							$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
						} </script></div>
EOT;
				} else {
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
							var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
    						dialogConfirm("Erro!", '{$retorno}', buttons)
						} </script></div>
EOT;
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
		$botoesMid = '';
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="est016FormMudarPedidoEmpresa_btn">Mudar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'est016FormAtualizarCFOP':
		global $g;
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
		$form_tabela = 'T224';
		$form_campo_pk = 'T224_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est016FormAtualizarCFOP_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est016/form/est016FormAtualizarCFOP/';
        
        //form editor
        $form_editor = array(
            'geral' => '{"linha":{"top":7,"left":26.99139404296875,"width":null,"hidden":0},"D006_Id":{"top":33,"left":30.86700439453125,"width":319,"hidden":0},"est016FormAtualizarCFOP_btn":{"top":31,"left":415.99261474609375,"width":null,"hidden":0},"est016FormAtualizarCFOPRegras":{"top":76,"left":31.969970703125,"width":null,"hidden":0},"linha4":{"top":110,"left":26.99139404296875,"width":null,"hidden":0},"opcaoIPI":{"top":148,"left":61.6881103515625,"width":13,"hidden":0},"opcaoNCM":{"top":148,"left":178.00244140625,"width":13,"hidden":0},"opcaoICMS":{"top":171,"left":48.33026123046875,"width":13,"hidden":0},"opcaoUnidade":{"top":171,"left":108.532470703125,"width":13,"hidden":0},"opcaoST":{"top":194,"left":61.5042724609375,"width":13,"hidden":0},"opcaoPISCofins":{"top":217,"left":15.4718017578125,"width":13,"hidden":0},"opcaoPeso":{"top":194,"left":121.982177734375,"width":13,"hidden":0}}',
        );

		$descricaoCFOP  = ($g['C031']['descricaoCFOPExibida'] == 'P') ? 'D006_Descricao' : 'IF(D006_Descricao_Comercial !="",D006_Descricao_Comercial,D006_Descricao) as D006_Descricao';
		
		$form = new Formulario('est016FormAtualizarCFOP');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D006_Id' => array('name' => 'D006_Id', 'label' => 'CFOP', 'type' => 'select', 'reject' => true,
						'select' => gGeraSelect('D006', 'D006_Id', "LPAD(D006_Id[[VIRGULA]]4[[VIRGULA]]0), D006_Codigo_CFOP,{$descricaoCFOP}", 'ORDER BY D006_Codigo_CFOP DESC', true, false, '%1 - %2 - %3')
					),
				),
			)
		);

		$formArray['campos']['geral']['est016FormAtualizarCFOP_btn'] = array('name' => 'est016FormAtualizarCFOP_btn', 'type'=>'submit', 'reload' => false, 'value' => 'Atualizar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['geral']['linha'] = array('name' => 'linha', 'type'=>'titulo', 'value' => '', 'label' => 'Atualizar manualmente:', 'reject' => true);
		$opcoesSelecionadas = json_decode($r_opcoes, true);
		if (!is_array($opcoesSelecionadas)) $opcoesSelecionadas = array();
		$formArray['campos']['geral']['linha4'] = array('name' => 'linha4', 'type'=>'titulo', 'value' => '', 'label' => 'Após atualizar a CFOP, atualizar também:', 'reject' => true);
		$camposAtualizacao = array('opcaoIPI' => array('ipi', 'IPI'), 'opcaoICMS' => array('icms', 'ICMS'), 'opcaoST' => array('st', 'ST'), 'opcaoPISCofins' => array('pisCofins', 'PIS/COFINS'), 'opcaoNCM' => array('ncm', 'NCM'), 'opcaoUnidade' => array('unidade', 'Unidade de medida'), 'opcaoPeso' => array('peso', 'Peso do produto'));
		foreach ($camposAtualizacao as $nome => $dados) {
			$marcado = !isset($opcoesSelecionadas[$dados[0]]) || $opcoesSelecionadas[$dados[0]] !== false;
			$formArray['campos']['geral'][$nome] = array('name' => $nome, 'label' => $dados[1], 'type' => 'checkbox', 'checked' => $marcado ? 'checked' : false, 'reject' => true);
		}
		$formArray['campos']['geral']['opcoes'] = array('name' => 'opcoes', 'type' => 'hidden', 'reject' => true);
		$formArray['campos']['geral']['est016FormAtualizarCFOPRegras'] = array('name' => 'est016FormAtualizarCFOPRegras', 'type'=>'button', 'reload' => false, 'value' => 'Aplicar regras CFOP', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$form->definirFormularioArray($formArray);
		$opcoesAtualizacao = json_encode(json_decode($r_opcoes, true));
		if ($opcoesAtualizacao === 'null' || $opcoesAtualizacao === false) {
			$opcoesAtualizacao = '{}';
		}

		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			var opcoesAtualizacao = {$opcoesAtualizacao};
			function est016OpcoesAtualizacaoCFOP() {
				return {
					ipi: $('#opcaoIPI').is(':checked'),
					icms: $('#opcaoICMS').is(':checked'),
					st: $('#opcaoST').is(':checked'),
					pisCofins: $('#opcaoPISCofins').is(':checked'),
					ncm: $('#opcaoNCM').is(':checked'),
					unidade: $('#opcaoUnidade').is(':checked'),
					peso: $('#opcaoPeso').is(':checked')
				};
			}
			$('#est016FormAtualizarCFOP_btn').unbind('click.opcoesAtualizacao').bind('click.opcoesAtualizacao', function() {
				$('#opcoes').val(JSON.stringify(est016OpcoesAtualizacaoCFOP()));
			});
			$('#est016FormAtualizarCFOPRegras').unbind('click').bind('click', function(data) {
				idProgressBar = dialogAlert('Processando CFOP e alíquotas',"<span id='progressBar'><b><br>0% Processando CFOP</b></span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");
				$.getJSON('/est/est016/form_func-ajax/atualizarCFOP/?ajax=true&T224_Id=' + encodeURIComponent("{$r_T224_Id}") + '&opcoes=' + encodeURIComponent(JSON.stringify(opcoesAtualizacao)) + '&callback=?', function(request) {
					if (request.code) {
						T224_Id = {$r_T224_Id};
						opcoesAtualizacao = est016OpcoesAtualizacaoCFOP();
						$('#progressBar').html('<br><b><font color="green">100% CFOP processada &#10004;</font></b></br>' + '<b>0% Processando alíquotas</b></br>');
						est016AtualizarAliquotasCFOPGeralLoop(idProgressBar,T224_Id,1,opcoesAtualizacao);

						function est016AtualizarAliquotasCFOPGeralLoop(idProgressBar, T224_Id, inicio, opcoes){
				                $.getJSON('/est/est016/form_func-ajax/atualizarAliquota/?ajax=true&T224_Id=' + T224_Id + '&extra=' + encodeURIComponent("{$extra_}") + '&inicio=' + inicio + '&opcoes=' + encodeURIComponent(JSON.stringify(opcoes || {})) + '&callback=?', function(request) {
			                    if (request.concluido == 0) {
			                    	$('#progressBar').html('<br><b><font color="green">100% CFOP processada &#10004;</font></b></br><b>' + request.percentual + '<b>% Processando alíquotas</b>' + '<br/><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
				                    est016AtualizarAliquotasCFOPGeralLoop(idProgressBar,T224_Id,0,opcoes);
			                    }else{
			                    	if (request.code) {
			                    		$('#dc-' + idProgressBar).dialog("destroy").remove();
			                    		if(request.erro == 0){
											dialogConfirm('CFOP e alíquotas processadas','<br><b><font color="green">100% CFOP processada &#10004;</font></b></br><b><font color="green">' + request.percentual + '<b>%</b><b> Alíquotas processadas &#10004;' + '</font><br/><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');	
			                    		}else{
			                    			dialogConfirm('CFOP e alíquotas processadas','<br><b><font color="green">100% CFOP processada &#10004;</font></b></br><b><font color="red">' + request.percentual + '<b>%</b><b> Alíquotas processadas &#10006;' + '</font><br/><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');	
			                    		} 
			                    		divRefresh('{$g['divIdParent']}', true);  
			                        } else {
			                        	$('#dc-' + idProgressBar).dialog("destroy").remove();
			                            dialogConfirm('Erro!', request.data);
			                            divRefresh('{$g['divIdParent']}', true);
			                        }
			                    } 
			                });	                    
				      	}
					} else {
						$('#dc-' + idProgressBar).dialog("destroy").remove();
						var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
						dialogConfirm('Erro', request.data, \$buttons);
					}
					$('#{$g['divId']}').hideLoading();
					$('#{$g['divId']}').closest('.ui-dialog-content').dialog('close');
				});
			});
			} </script></div>
EOT;
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
				$D006_Id = $form->campoValorEnviado('D006_Id');
				echo <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						T224_Id = {$r_T224_Id};	
						idProgressBar = dialogAlert('Processando CFOP e alíquotas',"<span id='progressBar'><b><br>0% Processando CFOP</b></span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");

						$.getJSON('/est/est016/form_func-ajax/atualizarCFOPManual/?ajax=true&T224_Id=' + T224_Id + '&D006_Id=' + {$D006_Id} + '&opcoes=' + encodeURIComponent(JSON.stringify(opcoesAtualizacao)) + '&callback=?', function(request) {
							if(request.code){
								opcoesAtualizacao = est016OpcoesAtualizacaoCFOP();
								$('#progressBar').html('<br><b><font color="green">100% CFOP processada &#10004;</font></b></br>' + '<b>0% Processando alíquotas</b></br>');
								est016AtualizarAliquotaCFOPLoop(idProgressBar,T224_Id, 1, opcoesAtualizacao);
							}else{
								$(this).dialog("destroy").remove(); 
			                    dialogConfirm('Erro!', request.data);
							}
						});

						function est016AtualizarAliquotaCFOPLoop(idProgressBar, T224_Id, inicio, opcoes){
			                $.getJSON('/est/est016/form_func-ajax/atualizarAliquota/?ajax=true&T224_Id=' + T224_Id + '&extra=' + encodeURIComponent("{$extra_}") + '&inicio=' + inicio + '&opcoes=' + encodeURIComponent(JSON.stringify(opcoes || {})) + '&callback=?', function(request) {
			                    if (request.concluido == 0) {
			                    	$('#progressBar').html('<br><b><font color="green">100% CFOP processada &#10004;</font></b></br><b>' + request.percentual + '<b>% Processando alíquotas</b>' + '<br/><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
				                    est016AtualizarAliquotaCFOPLoop(idProgressBar,T224_Id,0,opcoes);
			                    }else{
			                    	if(request.code){
			                   			$('#dc-' + idProgressBar).dialog("destroy").remove();
			                    		if(request.erro == 0){
											dialogConfirm('CFOP e alíquotas processadas','<br><b><font color="green">100% CFOP atualizada &#10004;</font></b></br><b><font color="green">' + request.percentual + '<b>%</b><b> Alíquotas processadas &#10004;' + '</font><br/><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');	
			                    		}else{
			                    			dialogConfirm('CFOP e alíquotas processadas','<br><b><font color="green">100% CFOP atualizada &#10004;</font></b></br><b><font color="red">' + request.percentual + '<b>%</b><b> Alíquotas processadas &#10006;' + '</font><br/><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');	
			                    		} 
			                    		divRefresh('{$g['divIdParent']}', true);  
			                        } else {
			                        	$(this).dialog("destroy").remove(); 
			                            dialogConfirm('Erro!', request.data);
			                            divRefresh('{$g['divIdParent']}', true); 
			                        }
			                    } 
			                });	                    
				      	}
						$('#{$g['divId']}').hideLoading();
						$('#{$g['divId']}').closest('.ui-dialog-content').dialog('close');
					} </script></div>
EOT;
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
	
		$botoesDir = '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	/**
	 * Form Principal
	 * Chamado em: est016tab02
	 */
	case 'est016FormNaoLiberar':
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
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'T224';
		$form_campo_pk = 'T224_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est016FormNaoLiberarBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est016/form/est016FormNaoLiberar/';
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T224_Liberou_Alcada_Observacao_Nao_Liberado' => array('name' => 'T224_Liberou_Alcada_Observacao_Nao_Liberado', 'label' => '* Motivo', 'type' => 'textarea', 'length' => array(3,50)),
					'T224_Id' => array('name' => 'T224_Id', 'value' => @$r_T224_Id, 'label' => 'Observação', 'type' => 'hidden', 'length' => array(3,50)),
				)
			)
		);
        $form = new Formulario('ve013FormNaoLiberar');
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

				$retorno = $EST016->est016NaoLiberarPedido($form);
				if ($retorno !== true) {
					echo gGeraAlertMsg('Erro', $retorno);	
				} else {
					echo gGeraAlertMsg('Operação OK', "Ordem de compra negada com Sucesso");	
					echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										divRefresh('{$g['divIdParent']}', true);
									}, 150);
  								});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
							} </script></div>
EOT;
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
		$gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk,"Você deseja negar este ordem de compra?");

		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('form.tpl');

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="est016FormNaoLiberarBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	/**
	 * Form Principal
	 * Chamado em: est016tab02
	 */
	case 'est016FormNaoLiberarTelaAnalise':
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
			$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
		} else {
			$formId = uniqid();
		}
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'T224';
		$form_campo_pk = 'T224_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est016FormNaoLiberarBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est016/form/est016FormNaoLiberarTelaAnalise/';
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T224_Liberou_Alcada_Observacao_Nao_Liberado' => array('name' => 'T224_Liberou_Alcada_Observacao_Nao_Liberado', 'label' => '* Motivo', 'type' => 'textarea', 'length' => array(14,50)),
					'T224_Id' => array('name' => 'T224_Id', 'value' => @$r_T224_Id, 'label' => 'Observação', 'type' => 'hidden', 'length' => array(50,50)),
					'divReload' => array('name' => 'divReload', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
				)
			)
		);
        $form = new Formulario('ve013FormNaoLiberar');
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

				$retorno = $EST016->est016NaoLiberarPedido($form);
				if ($retorno !== true) {
					echo gGeraAlertMsg('Erro', $retorno);	
				} else {
					//echo gGeraAlertMsg('Operação OK', "Ordem de compra negada com Sucesso");
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							var buttons = {
								"Fechar": function() {
									$(this).dialog("destroy").remove();
									fecharJanela('{$g['divId']}');
									fecharJanela('{$g['divIdParent']}');
									divRefresh('{$form->campoValorEnviado('divReload')}');
								}
							}
                            dialogConfirm('Análise de Ordem de Compra', 'Ordem de compra negada com sucesso!', buttons);
						} </script></div>
EOT;
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
		$gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk,"Você deseja negar este ordem de compra?");

		/**
		 * PARTE 5: Exibir o formulário
		 */
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('form.tpl');

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = $botoesEsq = $botoesDir = '';
		$botoesDir .= '<button type="submit" id="est016FormNaoLiberarBtn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	 case 'est016FormCancelarOCObs':

        /**
        * PARTE 1: Variavéis envidas para o formulário
        */
        // Variável indica qual tab do form será exibida
        $tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
        // Utilizada em UPDATE, é o ID do registro que está sendo editado
        $acaoId = (isset($r_acaoId)) ? $r_acaoId : false;
        
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

		$form_editor = array(
			'geral'=>'{"T224_Observacao_Pedido_Perdido":{"top":20,"left":4,"width":137}}'
		);

        // A qual tabela este form está relacionada, e a chave primária da tabela
        $form_tabela = 'T224';
        $form_campo_pk = 'T224_Id';
        // ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
        $enviar_btnId = 'est016FormCancelarOCObs_Btn';
        // Form que será executado ao clicar no botão
        $enviar_acaoHref = '/est/est016/form/est016FormCancelarOCObs/';
        // Array de definição do formulário
        //$r_T224_Id = isset($r_est016FormDocumentos) ? $r_est016FormDocumentos['T227_T224_Id'] : $r_T224_Id;
                                                                   
        ///$tmp = gGeraSelect('D047', 'D047_Id', 'D047_Descricao_Motivo', "order by D047_Descricao_Motivo");
//        foreach($tmp as &$value) { $value['selected'] = true; }
        // Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
        $form = new Formulario('est016FormCancelarPedidoForm');
        //$formArray['campos']['geral']['T224_D047_Id']     				= 
        $formArray['campos']['geral']['T224_Observacao_Pedido_Perdido'] = array('name' => 'T224_Observacao_Pedido_Perdido', 'label'=>'Observação', 'type' => 'textarea');
        $formArray['campos']['geral']['T224_Id'] = array('name' => 'T224_Id', 'label'=>'Observação', 'type' => 'hidden', 'value'=>$acaoId);
        //$formArray['campos']['geral']['divReload']     = array('name' => 'divReload', 'label'=>'', 'type' => 'hidden','value' => @$r_divReload, 'reject' => true);
        
        $form->definirFormularioArray($formArray);
        
        /**
        * PARTE 3: Processamento do formulário (se foi enviado)
        *            E carregamento dos valores (se não foi enviado e possui acaoId)
        */
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
            // Form com erros
            if ($formCodigo == 1) {
                echo gGeraAlertMsgErroForm($form);
            // Form sem erros
            } else if ($formCodigo == 2) {
                // Insert ou Update?
                if (!empty($r_T224_Id)) {
                    // Update
                    if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
                        // Imprime uma mensagem e fecha a janela
                        $alert=gGeraAlertMsg("Operação OK", "Observação de cancelamento gravada com sucesso!", false);
                        echo <<<EOT
                                <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                                    if (typeof jQuery != 'undefined') {
                                    	{$alert}
                                        divRefresh("{$g['divId']}", true);
                                        divRefresh('{$form->campoValorEnviado('divReload')}', true);
                                        divRefresh('{$g['divId']}', true);
                                        $("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
                                    }
                                } </script></div>
EOT;
                    }
                } else {

                    //$retorno = $EST016->est016VerificaCancelarPedido($form);
                    
                    if ($retorno === true) {
                        $alert .= gGeraAlertMsg('Operação OK', 'Pedido Cancelado com sucesso!', false);
                        echo <<<EOT
                            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                                divRefresh('{$g['divIdParent']}', true);
                                if (typeof jQuery != 'undefined') {
                                    $("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
                                        setTimeout(function() {
                                    {$alert}
                                }, 150);
                                });
                                $("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
                                }
                            } </script></div>
EOT;
                    }else{
                       $alert = gGeraAlertMsg('ERRO', "{$retorno}", false);
                        echo <<<EOT
                            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                                if (typeof jQuery != 'undefined') {
                                    {$alert}
                                }
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
                        echo  $processado;
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
        $botoesEsq = '';
        $botoesDir = '';
        $botoesDir .= '<button type="submit" id="est016FormCancelarOCObs_Btn">Enviar</button>';
        $botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
        $g['smarty']->assign('botoesId', $g['divId']);
        $g['smarty']->assign('botoesEsq', $botoesEsq);
        $g['smarty']->assign('botoesDir', $botoesDir);
        echo $g['smarty']->fetch('buttons-floatBottom.tpl');
        // Ações dos botões
        // A ação do botão Enviar é definido dentro do formulário
        echo <<<EOT
            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
            } </script></div>
EOT;
    break;

    case 'GerarOT':
  		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;

		$extraC004_Id = "";
		if ($r_C004_Id){
			$extraC004_Id = " and C004_Id = {$r_C004_Id}";
			$gerarOTM5 = "S";
		} else {
			$gerarOTM5 = "N";
		}
		
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
		$form_tabela = 'T224';
		$form_campo_pk = 'T224_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est016GerarOT';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est016/form/GerarOT/';

		//form editor
		$form_editor = array(
			'geral' => '{"Count":{"top":7,"left":75,"width":null,"hidden":0},"Empresa":{"top":30,"left":24,"width":431,"hidden":0},"Empresa2":{"top":53,"left":22,"width":424,"hidden":0}}'
		);
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					//'T224_Id' => array('name' => 'T218_Id', 'label' => 'T218_Id', 'type' => 'hidden', 'length' => array(40, 80), 'reject' => true, 'value' => $r_T218_Id),
     				'Count' => array('name' => 'Count', 'label' => '', 'type' => 'comentario', 'value' => $r_count > 1 ? "<b>Deseja gerar uma Ordem de Transferência para os {$r_count} itens selecionados?</b>" : "Deseja gerar uma Ordem de Transferência para {$r_count} item selecionado?"),
     				'Empresa' => array('name' => 'Empresa', 'reject' => true, 'label' => 'Origem', 'type' => 'select', 
     					'select' => gGeraSelect('C004','C004_Id','C004_Nome_Abreviado',"JOIN D024 ON C004_D024_Id=D024_Id WHERE C004_Flag_Ativo='S' and C004_Id != '{$g['empresaAtual']}' $extraC004_Id")
     				),
     				'Empresa2' => array('name' => 'Empresa2', 'reject' => true, 'label' => 'Destino', 'disabled' => true, 'value' => $g['C004']['C004_Nome_Abreviado'])
				)
			)
		);
        $form = new Formulario('form-est016GerarOT');
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
				$sql = preg_replace('#LIMIT 0,100$#', '', urldecode($r_sql));
				// log("origem no form= ".$r_origem);
				if($r_origem === "estoque"){
					$origem = 1;
				}else if($r_origem === "transferencia"){
					$origem = 2;
				}
				log("origem= ".$origem);
				$sql = base64_encode($sql);
				$T224_Id = false;
				$C004_Id=$form->campoValorEnviado('Empresa');
				log("C004_Id no form= ".$C004_Id);
				//$retorno = $EST016->gerarOT($form->campoValorEnviado('Empresa'), $D049s);
				echo<<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					var origem = "{$origem}";
					idProgressBar = dialogAlert('Processando CFOP e alíquotas',"<div style='border:none;width:auto;height:200px;overflow:auto'><span id='progressBar'><b><br>0% Gerando ordem de transferência</b></span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;'/></div>");

					var dadosFiltro = 'ajax=true'+'&C004_Id='+encodeURIComponent('{$C004_Id}')+'&extra='+encodeURIComponent('{$sql}')+'&origem= '+encodeURIComponent(origem)+'&gerarOTM5='+encodeURIComponent('{$gerarOTM5}')+'&callback=?';
					$.ajax({
						url: '/est/est016/form_func-ajax/gerarOT/',
						type: 'POST',
						dataType: 'jsonp',
						data: dadosFiltro,
					}).done(function(request, temp1, temp2){
						if(request.code){
							T224_Id = request.T224_Id;
							$('#progressBar').html('<br><b><font color="green">100% Ordem de transferência gerada &#10004;</font></b></br>' + '<b>0% Processando CFOP</b></br>');	
							$.getJSON('/est/est016/form_func-ajax/atualizarCFOP/?ajax=true&T224_Id=' +encodeURIComponent(T224_Id) +'&callback=?', function(request) {
								if(request.code){
						        	$('#progressBar').html('<br><b><font color="green">100% Ordem de transferência gerada &#10004;</font></b></br>' + '<font color="green"><b>100% CFOP processada &#10004;</font></b><b><br>0% Processando Alíquotas</br></b>');
                                    est016AtualizarAliquotasLoop(idProgressBar,T224_Id, 1);

                                  	function est016AtualizarAliquotasLoop(idProgressBar, T224_Id, inicio){
										$.getJSON('/est/est016/form_func-ajax/atualizarAliquota/?ajax=true&T224_Id=' + encodeURIComponent(T224_Id) + '&extra=' + encodeURIComponent("{$extra_}") + '&inicio=' + inicio + '&callback=?', function(request) {
						                    if (request.concluido == 0) {
						                        $('#progressBar').html('<br><b><font color="green">100% Ordem de Transferência gerada &#10004;</font></b>'+'<br><b><font color="green">100% CFOP processada &#10004;</font></b></br><b>' + request.percentual + '<b>% Processando alíquotas</b>' + '<br/><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
						                        est016AtualizarAliquotasLoop(idProgressBar,T224_Id,0);
						                    }else{
						                        if (request.code){    
						                       		$.getJSON('/est/est016/form_func-ajax/desmarcarCheckbox/?ajax=true&T224_Id=' + encodeURIComponent(T224_Id) + '&extra=' + encodeURIComponent("{$sql}") +'&callback=?', function(request) {
							                       		if(request.code){ 
						                                    $('#dc-' + idProgressBar).dialog("destroy").remove();
                                                        	divRefresh('{$g['divIdParent']}', true);
                                                        	dialogConfirm('Processamento concluído','<br><b><font color="green">100% Ordem de Transferência gerada &#10004;</font></b>'+'<br><b><font color="green">100% CFOP processada &#10004;</font></b></br><b><font color="green">100% Alíquotas processadas &#10004;</font></b>');
															$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
																setTimeout(function() {
																	abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}' ,unique(), '', 'Editar Ordem de Compra', '/est/est016/content/est016content02/', '&acaoId=' + encodeURIComponent(T224_Id) + '&tabela=T224', [990,605]);  //950 615
																}, 150);
															});
															$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
						                                }else{
						                                	$('#dc-' + idProgressBar).dialog("destroy").remove();
															dialogConfirm('Erro ao limpar os checkbox', request.data);	
						                                } 
						                            });  
						                        }else{
						                            $('#dc-' + idProgressBar).dialog("destroy").remove();
						                            divRefresh('{$g['divIdParent']}', true);
													dialogConfirm('Erro ao processar Alíquotas', '<div style=\'border:none;width:auto;height:200px;overflow:auto\'><b>Os produtos foram incluídos na ordem de compra!</b>'+'<br><br>No entanto houve um erro ao atualizar alíquotas<b><font color="red"><br>Favor realizar o processo de atualizar alíquotas manualmente</font></b>'+'<br><br>Erro(s) encontrado(s):<br>'+request.erros +'<br>' +request.data +'</div>');
													$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
														setTimeout(function() {
															abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}' ,unique(), '', 'Editar Ordem de Compra', '/est/est016/content/est016content02/', '&acaoId=' + encodeURIComponent(T224_Id) + '&tabela=T224', [990,605]);  //950 615
														}, 150);
													});
													$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
						                        }           
						                    } 
						                });                     
                                  	}
								}else{ 
 									$('#dc-' + idProgressBar).dialog("destroy").remove();
 									divRefresh('{$g['divIdParent']}', true);
									dialogConfirm('Erro ao processar CFOP', '<div style=\'border:none;width:auto;height:200px;overflow:auto\'><b>Os produtos foram incluídos na ordem de compra!</b>'+'<br><br>No entanto houve um erro ao atualizar CFOP<b><font color="red"><br>Favor realizar o processo de atualizar CFOP e alíquotas manualmente</font></b>'+'<br><br>Erro(s) encontrado(s):<br>'+request.data+'</div>');
									$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
										setTimeout(function() {
											abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}' ,unique(), '', 'Editar Ordem de Compra', '/est/est016/content/est016content02/', '&acaoId=' + encodeURIComponent(T224_Id) + '&tabela=T224', [990,605]);  //950 615
										}, 150);
									});
									$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								}
							});
						}else{
							$('#dc-' + idProgressBar).dialog("destroy").remove();
							dialogConfirm('Erro ao gerar OT', request.data);
						}
						
					});
				} </script></div>
EOT;
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
		$botoesMid = '';
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="est016GerarOT">Gerar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'est016FormConfiguracoes':
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

		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'T224';
		$form_campo_pk = 'T224_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est016FormConfiguracoesBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est016/form/est016FormConfiguracoes/';
		
		$form_editor = array('geral' => '{"T224_Flag_Calcular_ST":{"top":7,"left":32.421875,"width":64,"hidden":0},"est015FormConfiguracoesBtn":{"top":97,"left":160,"width":null,"hidden":0},"T224_Flag_Calcular_IPI":{"top":30,"left":33.03125,"width":64,"hidden":0},"T224_Flag_Calcular_ICMS":{"top":53,"left":18.96875,"width":64,"hidden":0}}');

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T224_Flag_Calcular_ST' => array('name' => 'T224_Flag_Calcular_ST', 'label' => 'Calcular ST','type' => 'select','select' => 
						array(
							array('title' => 'SIM', 'value' => 'S'),
							array('title' => 'NÃO', 'value' => 'N'),
						)
					),
					'T224_Flag_Calcular_IPI' => array('name' => 'T224_Flag_Calcular_IPI', 'label' => 'Calcular IPI','type' => 'select','select' => 
						array(
							array('title' => 'SIM', 'value' => 'S'),
							array('title' => 'NÃO', 'value' => 'N'),
						)
					),
					'T224_Flag_Calcular_ICMS' => array('name' => 'T224_Flag_Calcular_ICMS', 'label' => 'Calcular ICMS','type' => 'select','select' => 
						array(
							array('title' => 'SIM', 'value' => 'S'),
							array('title' => 'NÃO', 'value' => 'N'),
						)
					),
				)
			)
		);
		// Botão Limpar / Enviar
		if(!empty($r_acaoId)){
			$formArray['campos']['geral']['est016FormConfiguracoesBtn'] = array('name' => 'est016FormConfiguracoesBtn', 'type'=>'button', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		}
		$form = new Formulario('est016FormConfiguracoes');
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
   						echo gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!');
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								//divRefresh('{$r_divIdMain}', true);
								divRefresh($('.contentCabecalhoLayout').parent().attr('id'));
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
	break;

	case 'est016FormIncluirOC':
		
		/**
			* PARTE 1: Variavéis envidas para o formulário
			*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$r_T224_Id = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;

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
		
		// busca o ID do cliente pra carregar os contatos, caso seja edição
		$idCliente = $r_idCliente ? $r_idCliente : '99999999999';
		if ($acaoId>0){
			$sql = "SELECT T224_D024_Id,D020_D022_Id,D022_D036_Id, D024_D022_Id, D024_D020_Id FROM T224 LEFT JOIN D024 ON D024_Id=T224_D024_Id LEFT JOIN D020 ON D020_Id=D024_D020_Id LEFT JOIN D022 ON D022_Id=D020_D022_Id LEFT JOIN D036 ON D036_Id=D022_D036_Id WHERE T224_Id = '{$acaoId}'";
			$result = mysql_query($sql);
			$aD024 = mysql_fetch_assoc($result);		
			$idTransportadoraCidade=$aD024['D020_D022_Id'];
			$idCliente=$aD024['T224_D024_Id'];
			$idTransportadora=$aD024['D024_D022_Id'];
            $idTipoTransp=$aD024['D022_D036_Id'];
		} 		

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'T224';
		$form_campo_pk = 'T224_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est016FormCabecalhoBtnNew';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est016/form/est016FormIncluirOC/';

		if($g['C031']['habilitaComprasImportacao'] == 'S'){
			$meusCampos = ', "T224A_Flag_Importacao":{"top":220,"left":21,"width":212,"hidden":1}, "T224A_Dolar_Cambial":{"top":240,"left":21,"width":212,"hidden":1}, "T224A_Numero_Processo_Importacao":{"top":260,"left":21,"width":212,"hidden":1}, "T224A_D024_Id":{"top":280,"left":21,"width":212,"hidden":1}'; 
		}
		
		$form_editor = array(
			'geral' => '{"T224_D024_Id":{"top":7,"left":67.953125,"width":267,"hidden":0},"T224_D013_Id":{"top":7,"left":483.765625,"width":287,"hidden":0},"est016FormContatos":{"top":5,"left":832,"width":null,"hidden":0},"T224_D006_Id":{"top":7,"left":927,"width":351,"hidden":1},"T224_C007_Id_Vendedor_Interno":{"top":30,"left":67.671875,"width":287,"hidden":0},"T224_Data_Emissao":{"top":30,"left":480.4375,"width":280,"hidden":0},"T224_D116_Id":{"top":30,"left":966,"width":42,"hidden":1},"T224_Flag_ACP":{"top":53,"left":34.34375,"width":288,"hidden":0},"T224_D163_Id":{"top":53,"left":490.703125,"width":287,"hidden":1},"T224_Nome_Transportadora":{"top":53,"left":932,"width":120,"hidden":1},"T224_Prazo_Entrega":{"top":76,"left":39.53125,"width":280,"hidden":0},"T224_Data_Entrega":{"top":76,"left":417,"width":280,"hidden":0},"T224_Prazos_Pagamentos":{"top":76,"left":417,"width":280,"hidden":1},"T224_Flag_Dividido":{"top":76,"left":997,"width":59,"hidden":1},"T224_Observacao":{"top":99,"left":66.953125,"height":62,"width":280,"hidden":0},"T224_Observacao_Comercial":{"top":99,"left":436.015625,"height":62,"width":280,"hidden":0},"T224_Pedido_Ecommerce":{"top":99,"left":964,"width":120,"hidden":1},"T224_Codigo_Rastreio":{"top":122,"left":943,"width":120,"hidden":1},"T224_C007_Id_Embalagem":{"top":145,"left":969,"width":185,"hidden":1},"T224_Observacao_Nota_Fiscal":{"top":168,"left":67.359375,"height":62,"width":280,"hidden":0},"T224_Observacao_1":{"top":168,"left":456.84375,"height":62,"width":280,"hidden":0},"T224_Flag_Totalizar_Peso":{"top":168,"left":967,"width":59,"hidden":1},"T224_Quantidade_Volumes":{"top":191,"left":993,"width":127,"hidden":1},"T224_Valor_Despesas_Acessorias":{"top":237,"left":17.421875,"width":280,"hidden":0},"T224_Flag_Transporte_Coleta":{"top":237,"left":468.09375,"width":288,"hidden":0},"T224_Peso_Liquido":{"top":237,"left":973,"width":127,"hidden":1},"T224_Flag_Frete":{"top":260,"left":97.796875,"width":288,"hidden":0},"T224_D109_Id":{"top":260,"left":475.328125,"width":287,"hidden":0},"T224_Custo_Frete":{"top":260,"left":978,"width":80,"hidden":1},"T224_Valor_Frete":{"top":283,"left":69.671875,"width":280,"hidden":0},"T224_Hora_Coleta":{"top":283,"left":464.765625,"width":280,"hidden":0},"T224_Percentual_Comissao":{"top":283,"left":974,"width":104,"hidden":1},"T224_D036_Id":{"top":306,"left":100.921875,"width":289,"hidden":0},"T224_D022_Id":{"top":329,"left":47.234375,"width":265,"hidden":0},"button":{"top":326.984375,"left":416,"width":null,"hidden":0},"T224_Ordem_Compra":{"top":328.984375,"left":947,"width":280,"hidden":1},"TransportadoraPadrao":{"top":352,"left":9.671875,"width":288,"hidden":0},"T224_Peso_Bruto":{"top":351.984375,"left":981,"width":127,"hidden":1},"T224_Especie":{"top":374.96875,"left":996,"width":127,"hidden":1},"T224_Flag_Expedicao":{"top":397.96875,"left":984,"width":103,"hidden":1}' . $meusCampos . '}'
		);

		// novo formulário
		$form = new Formulario('form_est016_form1');

		$exibirImp   = ($g['C031']['habilitaComprasImportacao'] == 'S') ? 'select' : 'hidden';
		$exibirMoeda = ($g['C031']['habilitaComprasImportacao'] == 'S') ? 'moeda' : 'hidden';
		$exibirText  = ($g['C031']['habilitaComprasImportacao'] == 'S') ? 'text' : 'hidden';
		$exibirPesq  = ($g['C031']['habilitaComprasImportacao'] == 'S') ? 'pesquisa' : 'hidden';
		//$habilitar = ($g['C031']['habilitaComprasImportacao'] == 'S') ? false : true;

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T224_D024_Id' => array('name' => 'T224_D024_Id','label'=>'Fornecedor', 'type' => 'pesquisa','extra' => "onchange=\"divSetarAcaoId('{$g['divId']}', \$('#T224_D024_Id').val(), 'idCliente'); carregaCFOP_Contatos('{$g['divId']}',this);\"", 'pesquisa' => array('D024','D024_Id','D024_Id,D024_Nome_Empresa,D020_Nome_Cidade,D018_UF,D024_Cpf,D024_Cnpj','left join D020 on D020_Id=D024_D020_Id left join D018 on D018_Id=D024_D018_Id WHERE D024_Flag_Fornecedor="S" and D024_Flag_Ativo="S" order by D024_Nome_Empresa','(%1) %2 [%3 - %4]'), 'length' => array(50,30)),
					'T224A_D024_Id' => array('name' => 'T224A_D024_Id','label'=>'Pessoa Importação', 'type' => $exibirPesq, 'pesquisa' => array('D024', 'D024_Id', 'D024_Nome_Empresa', "where D024_Flag_Pessoa_Fisica_Juridica = 'I'")),
					'T224_D013_Id' => array('name' => 'T224_D013_Id', 'label' => 'Contato', 'type' => 'select',
						'select' => gGeraSelect('D013','D013_Id','D013_Nome_Contato,D013_DDD_Telefone_1,D013_Telefone_1,D013_E_Mail',"where D013_D024_Id=$idCliente AND D013_Flag_Ativo = 'S'",true),
						'selectRefresh' => array('D013','D013_Id','D013_Nome_Contato,D013_DDD_Telefone_1,D013_Telefone_1,D013_E_Mail',"where D013_D024_Id=$idCliente AND D013_Flag_Ativo = 'S'",true),
					),
					'est016FormContatos' => array('name' => 'est016FormContatos', 'type'=>'button', 'reload' => false, 'value' => 'Incluir', 'reject' => true),
					'T224_D006_Id' => array('name' => 'T224_D006_Id', 'label'=>'CFOP','type' => 'select', 'select' => 
        				gGeraSelect('D006','D006_Id','D006_Codigo_CFOP,D006_Descricao',' WHERE D006_Flag_Inativo!="S" AND D006_Flag_Departamento="COMERCIAL" ORDER BY D006_Codigo_CFOP',true),
					),											
					'T224A_Flag_Importacao' => array('name' => 'T224A_Flag_Importacao', 'label'=>'Importação?', 'type' => $exibirImp, 'select' => 
						array(
							array('title' => 'Não', 'value' => 'N'),
							array('title' => 'Sim', 'value' => 'S')
						),
					),											
					'T224A_Numero_Processo_Importacao' => array('name' => 'T224A_Numero_Processo_Importacao','label'=>'Nr. PI','type' => 'text', 'length' => array(10,10),'type' => $exibirText ),
					'T224_Data_Emissao' => array('name' => 'T224_Data_Emissao','label'=>'Emissão','type' => 'data', 'length' => array(20,10), 'value'=>date('d/m/Y')),
					'T224_Data_Entrega' => array('name' => 'T224_Data_Entrega','label'=>'Data de Entrega','type' => 'data', 'length' => array(20,10)),
					'T224_C007_Id_Vendedor_Interno' => array('name' => 'T224_C007_Id_Vendedor_Interno', 'label'=>'Comprador','type' => 'select', 'select' => 
						gGeraSelect('C007','C007_Id','C007_Login','WHERE C007_Flag_Vendedor!=""',true, $g['usuarioAtual']),
					),
					'T224_Percentual_Comissao' => array('name' => 'T224_Percentual_Comissao', 'label'=>'Comissão %','value'=>'0,00','type' => 'moeda', 'length' => array(20,10)),
					'T224A_Dolar_Cambial' => array('name' => 'T224A_Dolar_Cambial', 'label'=>'Cotação Dólar Cambial','type' => $exibirMoeda, 'length' => array(20,10)),
					'T224_Flag_ACP' => array('name' => 'T224_Flag_ACP', 'label'=>'Tipo Documento','type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => 0),
							array('title' => 'NF', 'value' => 1),
							array('title' => 'Romaneio', 'value' => 2),
							array('title' => 'ACP', 'value' => 3),
							array('title' => 'Cupom Fiscal', 'value' => 4),
							array('title' => 'NF D1', 'value' => 5),
							array('title' => 'NFe', 'value' => 6, 'selected' => true),
							array('title' => 'NFSe', 'value' => 7),							
						)
					),
					'T224_Prazo_Entrega' => array('name' => 'T224_Prazo_Entrega', 'label'=>'Prazo de Entrega','type' => 'text', 'length' => array(53,40)),
					'T224_Prazos_Pagamentos' => array('name' => 'T224_Prazos_Entregas', 'label'=>'Forma de Pagamento','type' => 'text', 'length' => array(53,40)),
					'T224_D116_Id' => array('name' => 'T224_D116_Id', 'label' => 'Veículo','type' => 'select',
						'select'=> gGeraSelect('D116', 'D116_Id', 'D116_Placa,D133_Modelo,D134_Cor', "LEFT JOIN D133 on D133_Id=D116_D133_Id LEFT JOIN D134 on D134_Id=D116_D134_Id WHERE D116_D024_Id=$idCliente", true),
						'selectRefresh' => array('D116', 'D116_Id', 'D116_Placa,D133_Modelo,D134_Cor', "LEFT JOIN D133 on D133_Id=D116_D133_Id LEFT JOIN D134 on D134_Id=D116_D134_Id WHERE D116_D024_Id=$idCliente", true)
					),
					'T224_Ordem_Compra' => array('name' => 'T224_Ordem_Compra', 'label'=>'Ordem de Compra','type' => 'text', 'length' => array(53,30)),
					//'tituloObservacoes' => array('name' => 'tituloObservacoes', 'label'=>'Observações','type' => 'titulo', 'reject' => true),
					'T224_Observacao' => array('name' => 'T224_Observacao', 'label'=>'Fornecedor','type' => 'textarea','extra' => 'title="Esta observação será visualizada pelo cliente (Não será impressa na DANFE)"', 'length' => array(3,50)),
					'T224_Observacao_Comercial' => array('name' => 'T224_Observacao_Comercial', 'label'=>'Comercial Interna','type' => 'textarea','extra' => 'title="Observação de uso interno da área comercial (Não será impressa na DANFE)"', 'length' => array(3,50)),
					'T224_Observacao_Nota_Fiscal' => array('name' => 'T224_Observacao_Nota_Fiscal', 'label'=>'Nota Fiscal','type' => 'textarea','extra' => 'title="Esta observação será impressa na DANFE"', 'length' => array(3,50)),
					'T224_Observacao_1' => array('name' => 'T224_Observacao_1', 'label'=>'Almox.:','type' => 'textarea','extra' => 'title="Observação de uso interno da área de estoque (Não será impressa na DANFE)"', 'length' => array(3,50)),
					'T224_Flag_Frete' => array('name' => 'T224_Flag_Frete','label'=>'Frete', 'type' => 'select',
						'select' => array(
							array('title' => '0-EMITENTE', 'value' => '0'),
							array('title' => '1-DESTINATÁRIO', 'value' => '1'),
							array('title' => '2-TERCEIROS', 'value' => '2'),
							array('title' => '9-SEM FRETE', 'value' => '9'),
						),
					),
					'T224_Valor_Frete' => array('name' => 'T224_Valor_Frete', 'label'=>'Valor Frete','value'=>'0,00','type' => 'moeda', 'length' => array(20,20)),
					'T224_D022_Id' => array('name' => 'T224_D022_Id', 'label' => 'Transportadora', 'type' => 'pesquisa', 'pesquisa' => array('D022','D022_Id','D022_Nome_Empresa,D020_Nome_Cidade,D018_UF','left join D020 on D020_Id=D022_D020_Id left join D018 on D018_Id=D022_D018_Id'),'extra' => "onchange=\"carregaTipoTransp(this, '{$g['divId']}')\"", 'length' => array(50,90)),
					'T224_D036_Id' => array('name' => 'T224_D036_Id', 'label' => 'Tipo', 'type' => 'select', 'select' =>
						gGeraSelect('D036','D036_Id','D036_Tipo_Transporte','',true),
					),
					'TransportadoraPadrao' => array('name' => 'TransportadoraPadrao', 'label' => 'Transportadora Padrão', 'type' => 'select','reject' =>true ,'disabled' => true,
						'select' => gGeraSelect('D022','D022_Id','D022_Nome_Empresa,D020_Nome_Cidade,D018_UF',"left join D020 on D020_Id=D022_D020_Id left join D018 on D018_Id=D022_D018_Id WHERE D022_Id='{$idTransportadora}' OR D022_Id = '{$idTransportadoraCidade}'",false), 
					),
					'button' => array('name' => 'button', 'type' => 'button','value' =>'Usar Padrão','reload' => false,'extra' => "onclick='copiaTransportadora({$idTipoTransp})'",'reject'=>true),
					'nada' => array('name' => 'nada', 'label' => 'Transportadora', 'type' => 'hidden', 'reject'=>true, 'length' => array(30,30)),
					'T224_Flag_Revenda_Consumidor' => array('name' => 'T224_Flag_Revenda_Consumidor', 'label'=>'T224_Flag_Revenda_Consumidor','type' => 'hidden'
						//'select' => 
							//array(
								//array('title' => '...', 'value' => 0, 'selected' => true),
								//array('title' => 'Revenda', 'value' => 1),
								//array('title' => 'Consumidor atacado', 'value' => 2),
								//array('title' => 'Consumidor varejo', 'value' => 3),
							//)
					),
					'T224_Flag_Expedicao' => array('name' => 'T224_Flag_Expedicao', 'label'=>'Expedição','type' => 'select','select' => 
						array(
							array('title' => '...', 'value' => 0),
							array('title' => 'Comercial', 'value' => 1),
							array('title' => 'Balcão', 'value' => 2),
						)
					),
					'T224_Quantidade_Volumes' => array('name' => 'T224_Quantidade_Volumes', 'label' => 'Volumes', 'type' => 'text'),
					'T224_Especie' => array('name' => 'T224_Especie', 'label' => 'Espécie', 'type' => 'text'),
					'T224_Peso_Liquido' => array('name' => 'T224_Peso_Liquido', 'label' => 'Peso Liquido', 'type' => 'text'),
					'T224_Peso_Bruto' => array('name' => 'T224_Peso_Bruto', 'label' => 'Peso Bruto', 'type' => 'text'),
					'T224_Nome_Transportadora' => array('name' => 'T224_Nome_Transportadora', 'label' => 'Nome transportadora', 'type' => 'text'),
					'T224_Flag_Dividido' => array('name' => 'T224_Flag_Dividido', 'label'=>'Dividido','type' => 'select','select' => 
						array(
							array('title' => 'Não', 'value' => 'N'),
							array('title' => 'Sim', 'value' => 'S'),
						)
					),
					'T224_Pedido_Ecommerce' => array('name' => 'T224_Pedido_Ecommerce', 'label' => 'N° Ecommerce', 'type' => 'text'),
					'T224_Codigo_Rastreio' => array('name' => 'T224_Codigo_Rastreio', 'label' => 'Codigo de Rastreio', 'type' => 'text'),
					'T224_C007_Id_Embalagem' => array('name' => 'T224_C007_Id_Embalagem', 'label' => 'Embalado por', 'type' => 'select', 'select' =>
						gGeraSelect('C007', 'C007_Id', 'C007_Primeiro_Nome', '', true)
					),
					'T224_Custo_Frete' => array('name' => 'T224_Custo_Frete', 'label' => 'Custo Frete', 'type' => 'moeda'),
					'T224_Flag_Totalizar_Peso' => array('name' => 'T224_Flag_Totalizar_Peso', 'label'=>'Totalizar Peso','type' => 'select','select' => 
						array(
							array('title' => 'Não', 'value' => 'N'),
							array('title' => 'Sim', 'value' => 'S'),
						)
					),
					'T224_D109_Id' => array('name' => 'T224_D109_Id', 'label'=>'Motorista','type' => 'select','select' => gGeraSelect('D109', 'D109_Id', 'D109_Nome', "WHERE D109_Funcao = 'MOTORISTA'",true)),
					'T224_Hora_Coleta' => array('name' => 'T224_Hora_Coleta', 'label' => 'Hora Coleta', 'type' => 'tempo'),
					'T224_Flag_Transporte_Coleta' => array('name' => 'T224_Flag_Transporte_Coleta', 'label' => 'Tipo coleta', 'type' => 'select', 'select' => 
						array(
							array('title' => '...', 'value' => ''),
							array('title' => 'MOTO', 'value' => '1'),
							array('title' => 'CARRO', 'value' => '2')
						)
					),
					'T224_D163_Id' => array('name' => 'T224_D163_Id', 'label' => 'Status', 'type' => 'select', 'select' =>
						gGeraSelect('D163','D163_Id','D163_Status','',true),
					),
					'T224_Valor_Despesas_Acessorias' => array('name' => 'T224_Valor_Despesas_Acessorias', 'label'=>'Valor desp. acessoria','value'=>'0,00','type' => 'moeda', 'length' => array(20,20)),
					'T224_C004_Id' => array('name' => 'T224_C004_Id', 'label' => 'N° Ecommerce', 'type' => 'hidden', 'value'=>$g['empresaAtual']),
				)
			)
		);
		/** Botão Limpar / Novo Registro
		if (!empty($acaoId)) {
			$formArray['campos']['geral']['est016FormCabecalhoBtnNew'] = array('name' => 'est016FormCabecalhoBtnNew', 'type'=>'submit', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right; margin-top:20px;', 'reject' => true);
		}
		*/
		$form->definirFormularioArray($formArray);
		
		$sql = "SELECT T224_D024_Id FROM T224 WHERE T224_Id = '{$r_T224_Id}';";
		$resultado = mysql_query($sql);
		$row = mysql_fetch_array($resultado);
		echo <<<EOT
		    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		    	$('#est016FormContatos').unbind('click');
				$('#est016FormContatos').unbind('click').bind('click', function(data) {
					var id = '{$row['T224_D024_Id']}';
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Cliente/Fornecedor - ID: ' + id, '/cad/cad001/content/cad001contentCadastro/', '&acaoId=' + encodeURIComponent(id) + '&tabela=D024' + '&tab=contatos', [950,553]);
					return false;
				});
		    } </script></div>
EOT;

		/**
			* Na maioria dos casos não será necessário alterar nada daqui para baixo
			* Somente em casos especiais, para alterar pequenos detalhes ou modificações do visual
			*/


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
						
						$T224 = mysql_query("SELECT T224_Flag_Lista_Preco, D022_Nome_Empresa FROM T224 LEFT JOIN D022 ON D022_Id=T224_D022_Id WHERE T224_Id = '{$acaoId}'");
 						$rT224 = mysql_fetch_array($T224);
						// Imprime uma mensagem e fecha a janela
						mysql_query("UPDATE T224 SET T224_Nome_Transportadora='{$rT224['D022_Nome_Empresa']}' WHERE T224_Id = '{$acaoId}'");
						echo gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!');
						$lista = $form->campoValorEnviado('T224_Flag_Lista_Preco');
						//Verifica se a lista de preço foi alterada
						if(($rT224['T224_Flag_Lista_Preco'] != $lista) && !empty($lista)){
							$EST016->alteraLista($acaoId, $lista);
							echo gGeraAlertMsg('Atenção', 'A lista de preço foi alterada, os valores de tabela dos produtos serão modificados.');	
						}					
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								//divRefresh('{$r_divIdMain}', true);
								divRefresh($('.contentCabecalhoLayout').parent().attr('id'));
							} </script></div>
EOT;
					}
				} else {
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						// Imprime uma mensagem e fecha a janela
						$acaoId = $g['mysqlLastId'];
						if($g['C031']['habilitaComprasImportacao'] == 'S'){
							$dadosT224 = mysql_query("select T224A_T224_Id from T224A where T224A_Id = '{$acaoId}'");
							$varT224 = mysql_fetch_array($dadosT224);
							$acaoId = $varT224['T224A_T224_Id'];
						}

						$result = mysql_query("SELECT D024_Id,
												      D024_Flag_Contribuinte,
												      D024_Pgto_Primeiro_Vcto_Tipo,
												      D024_Prazo_Parcela,
												      D024_Pgto_Dias_Vcto_Tipo,
												      D024_Dias_Vencimento,
												      D024A_D031_Id,
												      D024_Cobrar_Frete_Primeira_Parcela,
												      D024_Cobrar_ST_Primeira_Parcela,
												      D024_Cobrar_IPI_Primeira_Parcela,
												      D024_Quantidade_Parcelas 
											     FROM D024 
										    LEFT JOIN D024A ON D024_Id = D024A_D024_Id
											    WHERE D024_Id='{$form->campoValorEnviado('T224_D024_Id')}'");

						$D024 = mysql_fetch_assoc($result);	
						$updateT224 = [];

						if(!empty($D024['D024_Pgto_Primeiro_Vcto_Tipo'])){
							$updateT224[]=" T224_Pgto_Primeiro_Vcto_Tipo='{$D024['D024_Pgto_Primeiro_Vcto_Tipo']}'";
						}
						if(!empty($D024['D024_Prazo_Parcela'])){
							$updateT224[]=" T224_Pgto_Primeiro_Vcto_Valor='{$D024['D024_Prazo_Parcela']}'";
						}
						if(!empty($D024['D024_Pgto_Dias_Vcto_Tipo'])){
							$updateT224[]=" T224_Pgto_Dias_Vcto_Tipo='{$D024['D024_Pgto_Dias_Vcto_Tipo']}'";
						}
						if(!empty($D024['D024_Dias_Vencimento'])){
							$updateT224[]=" T224_Pgto_Dias_Vcto_Valor='{$D024['D024_Dias_Vencimento']}'";
						}
						if(!empty($D024['D024A_D031_Id'])){
							$updateT224[]=" T224_Pgto_Portador='{$D024['D024A_D031_Id']}'";
						}
						if(!empty($D024['D024_Quantidade_Parcelas'])){
							$updateT224[]=" T224_Pgto_Parcelas='{$D024['D024_Quantidade_Parcelas']}'";
						}

						if (!empty($updateT224)) {
							$updateQuery = "UPDATE T224 SET " . implode(', ', $updateT224) . " WHERE T224_Id = '{$acaoId}'";
							mysql_query($updateQuery);
						} 

						//die($AbrirEdicaoClienteAposInsercao);
						$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
								setTimeout(function() {
									abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}' ,unique(), '', 'Editar Ordem de Compra', '/est/est016/content/est016content02/', '&acaoId=' + encodeURIComponent('{$acaoId}') + '&tabela={$from}', [990,605]);  //950 615
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
		$botoesMid = '';
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="est016FormCabecalhoBtnNew">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'est016FormEmpresaDividirOrdemCompra':
		/**
         * PARTE 1: Variavéis envidas para o formulário
         */
        // Variável indica qual tab do form será exibida
        $tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;

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
        $form_tabela = '';
        $form_campo_pk = '';
        // ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
        $enviar_btnId = 'est016FormEmpresaDividirOrdemCompra_btn';
        // Form que será executado ao clicar no botão
        $enviar_acaoHref = '/est/est016/form/est016FormEmpresaDividirOrdemCompra/';

        // novo formulário
        $form = new Formulario('est016FormEmpresaDividirOrdemCompra');
        // Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
        $formArray = array(
            'campos' => array(
                'geral' => array(
                    'C004_Id_Novo' => array('name' => 'C004_Id_Novo','label'=>'Empresa', 'type' => 'select','extra' => 'style="width:300px;"' ,
                        'select' => gGeraSelect('C004','C004_Id','C004_Nome_Empresa',"where C004_Flag_Ativo='S'", false, $g['empresaAtual'])
                    ),
                    'T224_Id_Antigo' => array('name' => 'T224_Id_Antigo', 'label' => 'T224_Id_Antigo', 'type' => 'hidden','value' => @$r_T224_Id),
                )
            )
        );

        $form->definirFormularioArray($formArray);

        /**
         * PARTE 3: Processamento do formulário (se foi enviado)
         *            E carregamento dos valores (se não foi enviado e possui acaoId)
         */
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
            // Form com erros
            if ($formCodigo == 1) {
                echo gGeraAlertMsgErroForm($form);
                // Form sem erros
            } else if ($formCodigo == 2) {
                $C004_Id_Novo = $form->campoValorEnviado('C004_Id_Novo');
                $T224_Id_Antigo = $form->campoValorEnviado('T224_Id_Antigo');
				$r_sql = urldecode($r_sql);
				$r_sql = base64_encode($r_sql);
				echo <<<EOT
                    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                        idProgressBar = dialogAlert('Processando alíquotas',"<span id='progressBar'><b><br>0% Dividindo produtos da ordem de compra</b></span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");

                        $.getJSON('/est/est016/form_func-ajax/dividirOrdemCompra/?ajax=true&T224_Id=' + encodeURIComponent("{$T224_Id_Antigo}") +'&C004_Id='+  encodeURIComponent("{$C004_Id_Novo}")+'&extra1= '+ encodeURIComponent("{$r_sql}") +'&extra=' + encodeURIComponent("{$r_extra}") + '&callback=?', function(request) {
                        	if(request.code){
                                var T224_Id_Antigo = request.T224_Id_Antigo;
                        		var T224_Id = request.T224_Id;
                        		var sqlGrid = request.sqlGrid;
								$('#progressBar').html('<br><b><font color="green">100% Produtos divididos &#10004;</font></b></br>' + '<b>0% Reprocessando histórico</b></br>');
	                            est016ReprocessaHistórico(idProgressBar,1);

		                        function est016ReprocessaHistórico(idProgressBar, inicio){
					                $.getJSON('/est/est016/form_func-ajax/reprocessaHistorico/?ajax=true&T224_Id=' + encodeURIComponent(T224_Id)+'&T224_Id_Antigo='+ encodeURIComponent(T224_Id_Antigo) + '&extra=' + encodeURIComponent("{$r_extra}") + '&inicio=' + inicio + '&callback=?', function(request) {
					                    if (request.concluido == 0) {
					                    	$('#progressBar').html('<br><b><font color="green">100% Produtos divididos &#10004;</font></b></br>' + '<b>'+ request.percentual +'% Reprocessando histórico</b></br>'+'</b><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
					                    	est016ReprocessaHistórico(idProgressBar,0);
					                    }else{
					                    	if (request.code) {
					                    		var T224_Id = request.T224_Id;
					                    		$('#progressBar').html('<br><b><font color="green">100% Produtos divididos &#10004;</font></b></br>' + '<b><font color="green">100% Histórico processado &#10004;</font></b></br>' + '<b>0% Processando alíquotas</b></br>');
	                            				est016AtualizarAliquotasPedidos(idProgressBar,1);

	                            				function est016AtualizarAliquotasPedidos(idProgressBar, inicio){
                                                    $.getJSON('/est/est016/form_func-ajax/atualizarAliquota/?ajax=true&T224_Id=' + encodeURIComponent(T224_Id) + '&extra=' + encodeURIComponent("{$extra_}") + '&inicio=' + inicio + '&callback=?', function(request) {
                                                        if (request.concluido == 0) {
                                                            $('#progressBar').html('<br><b><font color="green">100% Produtos adicionados na cotação &#10004;</font></b>'+'<br><b><font color="green">100% Histórico processado &#10004;</font></b></br><b>' + request.percentual + '<b>% Processando alíquotas</b>' + '<br/><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
                                                            est016AtualizarAliquotasPedidos(idProgressBar, 0);
                                                        }else{
                                                            if (request.code) {
                                                            	var T224_Id = request.T224_Id;
                                                            	$('#progressBar').html('<br><b><font color="green">100% Produtos divididos &#10004;</font></b></br>' + '<b><font color="green">100% Histórico processado &#10004;</font></b></br>' + '<font color="green"><b>100% Alíquotas processadas</b></br></font>' + '<b>0% Totalizando produtos</b>');
                                                            	$.getJSON('/est/est016/form_func-ajax/totalizarOrdemCompra/?ajax=true&T224_Id=' + encodeURIComponent(T224_Id) + '&callback=?', function(request) {
	                                                        		if(request.code){
	                                                        			divRefresh('{$g['divId']}');
	                                                        			var T224_Id = request.T224_Id;
		                                                            	$('#dc-' + idProgressBar).dialog("destroy").remove();
		                                                            	divRefresh('{$g['divIdParent']}', true);
		                                                            	dialogConfirm('Processamento concluído','<br><b><font color="green">100% Produtos divididos &#10004;</font></b>'+'<br><b><font color="green">100% Histórico processado &#10004;</font></b></br><b><font color="green">100% Alíquotas processadas &#10004;</font></b>' + '<br><b><font color="green">100% Produtos totalizados &#10004;</font></b>');
																		$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
																			setTimeout(function() {
																				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}' ,unique(), '', 'Editar Ordem de Compra', '/est/est016/content/est016content02/', '&acaoId=' + encodeURIComponent(T224_Id) + '&tabela=T224', [990,605]);  //950 615
																			}, 150);
																		});
																		$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
																	}else{
																		divRefresh('{$g['divIdParent']}', true);
																		$('#dc-' + idProgressBar).dialog("destroy").remove();
	                                                                	dialogConfirm('Erro ao totalizar produtos', request.data);	    	
																	}
																});
                                                            }else{
                                                            	divRefresh('{$g['divId']}');
                                                            	var T224_Id = request.T224_Id;
                                                                $('#dc-' + idProgressBar).dialog("destroy").remove();
                                                                dialogConfirm('Erro ao processar Alíquotas', '<div style=\'border:none;width:auto;height:200px;overflow:auto\'><b>Os produtos foram divididos!</b>'+'<br><br>No entanto houve um erro ao atualizar alíquotas<b><font color="red"><br>Favor realizar o processo de atualizar alíquotas manualmente</font></b>'+'<br><br>Erro(s) encontrado(s):<br>'+request.erros +'<br>' +request.data +'</div>'); 
                                                                $("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
																	setTimeout(function() {
																		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}' ,unique(), '', 'Editar Ordem de Compra', '/est/est016/content/est016content02/', '&acaoId=' + encodeURIComponent(T224_Id) + '&tabela=T224', [990,605]);  //950 615
																	}, 150);
																});
																$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close'); 
                                                            }           
                                                        } 
                                                    });                         
                                                }	
					                        } else {
					                        	divRefresh('{$g['divId']}');
					                        	var T224_Id = request.T224_Id;
					                        	$('#dc-' + idProgressBar).dialog("destroy").remove();
					                        	$(this).dialog("destroy").remove(); 
					                           	dialogConfirm('Erro ao reprocessar histórico', '<div style=\'border:none;width:auto;height:200px;overflow:auto\'><b>Os produtos foram divididos!</b>'+'<br><br>No entanto houve um erro ao reprocessar histórico<b><font color="red"><br>Favor realizar o processo de atualizar alíquotas manualmente</font></b>'+'<br><br>Erro(s) encontrado(s):<br>'+request.erros +'<br>' +request.data +'</div>'); 
				                           	 	$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
													setTimeout(function() {
														abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}' ,unique(), '', 'Editar Ordem de Compra', '/est/est016/content/est016content02/', '&acaoId=' + encodeURIComponent(T224_Id) + '&tabela=T224', [990,605]);  //950 615
													}, 150);
												});
												$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close'); 
					                        }
					                    } 
					                });	                  
		        				}
	        				}else{
	        				    $('#dc-' + idProgressBar).dialog("destroy").remove();
                                dialogConfirm('Erro ao dividir ordem de compra', request.data);	
	        				}
	        			});	
                    } </script></div>
EOT;
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
        $g['smarty']->display('form.tpl');
	break;

	case 'est016FormAplicarDescontoValorTotal':
		/**
		 * PARTE 1: Variavéis envidas para o formulário
		 */
		// Variável indica qual tab do form será exibida
		$tab = (isset($r_tab)) ? $r_tab : false;
		$acaoId = (isset($r_acaoId)) ? $r_acaoId : false;

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

		$form_editor = array(
			'geral' => '{"T224_Valor_Desconto_Manual":{"top":6.98529052734375,"left":12.3621826171875,"width":184.039216,"hidden":0}}'
		);
		
		// A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
		$form_tabela = 'T224';
		$form_campo_pk = 'T224_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'est016AplicarDescontoTotalBtnExecutar';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/est/est016/form/est016FormAplicarDescontoValorTotal/';

		$form = new Formulario('form-est016FormAplicarDescontoValorTotal');
        $formArray = array(
            'campos' => array(
                'geral' => array(
                    'T224_Valor_Desconto_Manual' => array('name' => 'T224_Valor_Desconto_Manual', 'label' => 'Desconto R$', 'type' => 'moeda'),
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
				// Update
				 if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
					// Imprime uma mensagem e fecha a janela
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
							divRefresh('{$g['divIdParent']}');
						} </script></div>
EOT;
				}
			}
		} else {
			// Carrega os valores caso seja um update
			if (!empty($acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $acaoId);
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
		$botoesMid = '';
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="est016AplicarDescontoTotalBtnExecutar">Executar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
}
