<?php
namespace hardness;
global $g;

// Classes
require_once("bibliotecas/classes/FIN001.php");
$FIN001 = new FIN001();

// Carrega funções do ajax
require_once("prog/fin/fin001/form_func-js.php");

/**
* Ações
*/
switch ($g['r'][3]) {
	/**
	* Form Principal
	* Chamado em: fin001tab02
	*/
	case 'fin001form01':		
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
		$formMultiTab = true;
		if ($formMultiTab) {
			$formId = $r_formId;
		} else {
			$formId = uniqid();
		}
				
		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'T002';
		$form_campo_pk = 'T002_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'fin001_content_form1_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fin/fin001/form/fin001form01/';

		//form editor
		$form_editor = array(
			'cadastro_titulo' => '{"T002_D024_Id":{"top":7,"left":51.109375,"width":423,"hidden":0},"T002_Numero_Cheque":{"top":7,"left":603.4375,"width":136,"hidden":0},"T002_D032_Id":{"top":30,"left":60.875,"width":112,"hidden":0},"T002_D014_Id":{"top":30,"left":317.53125,"width":192,"hidden":0},"T002_Data_Cheque":{"top":30,"left":619.921875,"width":136,"hidden":0},"T002_Nome_Cliente":{"top":53,"left":21.5625,"width":440,"hidden":0},"T002_Agencia_Cheque":{"top":53,"left":642.953125,"width":136,"hidden":0},"T002_Data_Emissao":{"top":76,"left":22.953125,"width":104,"hidden":0},"T002_Data_Vencimento":{"top":76,"left":340,"width":104,"hidden":0},"T002_Conta_Corrente_Cheque":{"top":76,"left":608.703125,"width":136,"hidden":0},"T002_Numero_Documento":{"top":99,"left":18.4375,"width":104,"hidden":0},"T002_Numero_Duplicata":{"top":99,"left":362.21875,"width":104,"hidden":0},"T002_Banco_Cheque":{"top":99,"left":651.671875,"width":280,"hidden":0},"T002_Flag_Status":{"top":122,"left":59.171875,"width":112,"hidden":0},"T002_Nome_Cheque":{"top":122,"left":653.015625,"width":280,"hidden":0},"T002_D027_Id":{"top":145,"left":47.703125,"width":112,"hidden":0},"T002_Cidade_Cheque":{"top":145,"left":648.5625,"width":280,"hidden":0},"T002_Valor_Duplicata":{"top":168,"left":16.375,"width":104,"hidden":0},"T002_Valor_Juros":{"top":168,"left":368.0625,"width":104,"hidden":0},"T002_Cheque_Repassado":{"top":168,"left":628.046875,"width":280,"hidden":0},"T002_Valor_Desconto":{"top":191,"left":15.90625,"width":104,"hidden":0},"T002_Valor_Recebido":{"top":191,"left":350.25,"width":104,"hidden":0},"T002_Observacao_Cheque":{"top":191,"left":584.390625,"height":56,"width":280,"hidden":0},"T002_Valor_Total":{"top":214,"left":38.140625,"width":104,"hidden":0},"T002_Valor_Taxas":{"top":214,"left":371.796875,"width":104,"hidden":0},"T002_Observacao":{"top":237,"left":32.3125,"height":83,"width":440,"hidden":0},"T002_Observacao_2":{"top":283,"left":596,"height":56,"width":280,"hidden":1},"T002_Valor_Original":{"top":328.984375,"left":26.25,"width":104,"hidden":0},"JurosCalculado":{"top":328.984375,"left":347.421875,"width":104,"hidden":0},"DiasProrrogados":{"top":351.984375,"left":5.609375,"width":104,"hidden":0},"T002_Data_Vencimento_Original":{"top":351.984375,"left":329.265625,"width":104,"hidden":0},"T002_Valor_Saldo":{"top":375,"left":591,"width":100,"hidden":1},"T002_T135_Id":{"top":375,"left":848,"width":32,"hidden":1}}', 
			'cheque'		  => '{"T002_Numero_Cheque":{"top":4,"left":40,"width":125},"T002_Data_Cheque":{"top":26,"left":55,"width":125},"T002_Agencia_Cheque":{"top":48,"left":37,"width":125},"T002_Conta_Corrente_Cheque":{"top":70,"left":4,"width":125},"T002_Banco_Cheque":{"top":92,"left":46,"width":275},"T002_Nome_Cheque":{"top":114,"left":50,"width":275},"T002_Cidade_Cheque":{"top":136,"left":44,"width":275}}',
			'remessa'		  => '{"T002A_Percentual_Comissao_Supervisor":{"top":122,"left":258.546875,"width":40,"hidden":0},"T002_T035_Id":{"top":7,"left":36.3125,"width":72,"hidden":0},"T002_Valor_Comissao":{"top":30,"left":2.375,"width":72,"hidden":0},"T002A_Valor_Total_Comissao_Supervisor":{"top":122,"left":34.46875,"width":72,"hidden":0},"T002_Percentual_Comissao":{"top":30,"left":258.546875,"width":40,"hidden":0},"T002_Valor_Total_Comissao_Externo":{"top":53,"left":-0.265625,"width":72,"hidden":0},"T002_Percentual_Comissao_Externo":{"top":53,"left":258.546875,"width":40,"hidden":0},"T002_Valor_Comissao_Representante":{"top":76,"left":15.671875,"width":72,"hidden":0},"T002_Percentual_Comissao_Representante":{"top":76,"left":258.546875,"width":40,"hidden":0},"T002_Valor_Total_Comissao_Usuario":{"top":99,"left":49.015625,"width":72,"hidden":0},"T002_Percentual_Comissao_Usuario":{"top":99,"left":258.546875,"width":40,"hidden":0}}'
		);
		
        //Calcula dias prorrogados
        $SQLdias = mysql_query("SELECT datediff(if(T002_Data_Recebimento='0000-00-00',T002_Data_Vencimento,T002_Data_Recebimento),T002_Data_Vencimento_Original) as Atraso, T002_Valor_Duplicata, T002_D032_Id FROM T002 WHERE T002_Id='{$acaoId}'");
        $rowSQLdias = mysql_fetch_array($SQLdias);
        $diasProrrogados = $rowSQLdias['Atraso'];
        $D032_Id = (!empty($rowSQLdias['T002_D032_Id'])) ? $rowSQLdias['T002_D032_Id'] : 0;
        
        //calcula valor do juros
        $valor = $rowSQLdias['T002_Valor_Duplicata'];
        $taxa = $g['c004']['C004_Taxa_Juros'];
        $valorJuros = gJurosCompostosTitulos($valor, $taxa, $diasProrrogados);
        $valorJuros = gCorrigeNumero($valorJuros - $valor);

        $restricaoEditarComissao = (isset($g['c029Ids'][192])) ? false : true;


		$C004=mysql_query("select * from C004 where C004_Id='{$g['empresaAtual']}'");
		$mC004=mysql_fetch_array($C004);
		$CNPJ_Empresa_Corrente = substr($mC004['C004_Cnpj'], 0, -8);
        
        // Verifica se é uma inserção ou update para habilitar os campos
        if (empty($acaoId)){
			$readonly = false;
		}else{
			$readonly = true;
		}

		$clientePorEmpresa = '';
		if($g['C031']['separarClientePorEmpresa'] == 'S'){
			$clientePorEmpresa = "WHERE (IFNULL(D024_C004_Id,0) = '{$g['empresaAtual']}' OR IFNULL(D024_C004_Id,0) = 0)";
		}

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$form = new Formulario('fin001FormTituloReceber');  
		$formArray = array(
			'campos' => array(
				'cadastro_titulo' => array(
					'T002_D024_Id' => array('name' => 'Consulta_T002_D024_Id', 'label'=>'Clientes', 'type' => 'pesquisa', 'pesquisa' => array('D024','D024_Id','D024_Id,D024_Nome_Empresa,D020_Nome_Cidade,D018_UF,D024_Cpf,D024_Cnpj,D024_DDD_Telefone_1,D024_Telefone_1','left join D020 on D020_Id=D024_D020_Id left join D018 on D018_Id=D024_D018_Id '.$clientePorEmpresa.' order by D024_Nome_Empresa','(%1) %2 [%3/%4] - %5%6 Tel:(%7)%8'),'length' => array(85,30)),
					//'T002_D024_Id' => array('name' => 'T002_D024_Id', 'align' => 'right', 'style'=> 'float: right', 'type' => 'text', 'value'=>$_REQUEST['T002_Id'], 'length' => array(10,5)),
					//'T002_D014_Id' => array('name' => 'Consulta_T002_D014_Id','label'=>'Contas', 'type' => 'pesquisa','pesquisa'=>array('D014','D014_Id','D014_SubConta,D014_Grupo','where D014_Flag_Plano="M"'), 'length' => array(67,30)),
					
					'T002_D032_Id' => array('name' => 'T002_D032_Id', 'label' => 'Grupo', 'type' => 'select','extra' => "onchange=\"alteraConta(this, '{$g['divId']}')\"", 'select' => 
						gGeraSelect('D032', 'D032_Id', 'D032_Descricao','WHERE D032_Flag_Ativo="S" ORDER BY D032_Descricao', true)
					),
					'T002_D014_Id' => array('name' => 'T002_D014_Id', 'label' => 'Conta', 	'type' => 'select', 'align'=>'right', 'style'=>'float:right', 'select' => 
						gGeraSelect('D014', 'D014_Id', 'IF(D154_Id>0[[virgula]]CONCAT(D154_Descricao[[virgula]]" - "[[virgula]]D014_SubConta)[[virgula]]D014_SubConta) as D014_SubConta',"LEFT JOIN D154 ON D154_Id=D014_D154_Id WHERE D014_D032_Id = '{$D032_Id}' AND D014_Flag_Ativo='S' ORDER BY D014_SubConta", true)
					),
					
					//'T002_D014_Id' => array('name' => 'T002_D014_Id', 'align' => 'right', 'style' => 'float:right', 'type' => 'text', 'length' => array(10,5)),
					'T002_Nome_Cliente' => array('name' => 'T002_Nome_Cliente', 'label' => 'Complemento', 'type' => 'text', 'length' => array(100,100)),				
					'T002_Data_Emissao' => array('name' => 'T002_Data_Emissao', 'label'=>'Data Emissão','type' => 'data', 'length' => array(15,10)),
					'T002_Data_Vencimento' => array('name' => 'T002_Data_Vencimento','label'=>'Data Vencimento ','extra' => "onchange=\"calculaJuros(this, '{$g['divId']}', {$acaoId})\"", 'align'=>'right','style'=>'float:right','type' => 'data', 'length' => array(15,10)),
					'T002_Numero_Documento' => array('name' => 'T002_Numero_Documento', 'label'=>'Nº Documento','type' => 'text'),
					'T002_Numero_Duplicata' => array('name' => 'T002_Numero_Duplicata', 'label'=>'Nº Duplicata ','align'=>'right','style'=>'float:right','type' => 'text'),		
					'T002_Flag_Status' => array('name' => 'T002_Flag_Status', 'label'=>'Status', 'type' => 'select', 'select' => 
						array(
							array('title' => 'Normal', 'value' => 1, 'selected' => true),
                            array('title' => 'Cobrança', 'value' => 3),
							array('title' => 'Perdido', 'value' => 2),
						)
					),					
					'T002_D027_Id' => array('name' => 'T002_D027_Id', 'label' => 'Portador', 'type' => 'select','select' => 
						gGeraSelect('D027', 'D027_Id', 'D027_Portador','LEFT JOIN D007 ON D007_Id = D027_D007_Id LEFT JOIN C004 ON C004_Id = D007_C004_Id WHERE C004_Cnpj LIKE "%'.$CNPJ_Empresa_Corrente.'%" OR ifnull(D027_D007_Id,0) = 0 order by D027_Portador', true)
					),
					'T002_Valor_Duplicata' => array('name' => 'T002_Valor_Duplicata', 'label'=>'Valor Duplicata','value' =>'0,00', 'type' => 'moeda', 'length' => array(15,10)),
					'T002_Valor_Juros' => array('name' => 'T002_Valor_Juros', 'label'=>'Valor Juros ','value' =>'0,00','readonly' => $readonly, 'align'=>'right','style'=>'float:right', 'type' => 'moeda', 'length' => array(15,10)),
					'T002_Valor_Desconto' => array('name' => 'T002_Valor_Desconto', 'label'=>'Valor Desconto','value' =>'0,00','readonly' => $readonly, 'type' => 'moeda', 'length' => array(15,10)),
					'T002_Valor_Recebido' => array('name' => 'T002_Valor_Recebido', 'label'=>'Valor Recebido ','value' =>'0,00','readonly' => true,'align'=>'right', 'style'=>'float:right', 'type' => 'moeda', 'length' => array(15,10)),
					'T002_Valor_Taxas' => array('name' => 'T002_Valor_Taxas', 'label'=>'Valor taxas','value' =>'0,00','readonly' => $readonly, 'type' => 'moeda', 'length' => array(15,10)),
					'T002_Valor_Total' => array('name' => 'T002_Valor_Total', 'label'=>'Valor Total', 'type' => 'moeda', 'value' =>'0,00', 'readonly' => true, 'style'=>'font-weight:bold'),
					'T002_Valor_Saldo' => array('name' => 'T002_Valor_Saldo', 'label'=>'Saldo', 'type' => 'moeda', 'value' =>'0,00', 'readonly' => true, 'style'=>'font-weight:bold'),
					'T002_T135_Id' => array('name' => 'T002_T135_Id', 'label'=>'Contrato', 'type' => 'select', 'style' => 'float:right', 'align'=>'right','select' => 					
						gGeraSelect('T135','T135_Id','T135_Tipo','',true),                                                
					),					
					'T002_Observacao' => array('name' => 'T002_Observacao', 'label'=>'Observação', 'type' => 'textarea', 'length' => array(4,81)),
					'T002_Observacao_2' => array('name' => 'T002_Observacao_2', 'label'=>'Observação 2', 'type' => 'textarea', 'length' => array(4,81)),
					'T002_Valor_Original' => array('name' => 'T002_Valor_Original','label'=>'Valor original', 'readonly' => true,'type' => 'moeda', 'length' => array(15,10)),
					'T002_Data_Vencimento_Original' => array('name' => 'T002_Data_Vencimento_Original', 'label'=>'Vencimento original', 'align'=>'right', 'style'=>'float:right','type' => 'data', 'disabled' => true, 'length' => array(15,10)),
					
					'T002_C004_Id' => array('name' => 'T002_C004_Id','type' => 'hidden','value'=>$g['empresaAtual']),
                    'DiasProrrogados' => array('name' => 'DiasProrrogados', 'label'=>'Dias Prorrogados','value' =>$diasProrrogados, 'readonly' => true, 'type' => 'moeda', 'reject' => true, 'length' => array(15,10)),
                    'JurosCalculado' => array('name' => 'JurosCalculado', 'label'=>'Juros Calculado','value' =>$valorJuros,'readonly' => true, 'align'=>'right','style'=>'float:right', 'type' => 'text','reject' => true, 'length' => array(15,10)),
					'T002_Numero_Cheque' => array('name' => 'T002_Numero_Cheque', 'label' => 'Número Cheque', 'type' => 'text', 'length' => array(20, 120)),
					'T002_Data_Cheque' => array('name' => 'T002_Data_Cheque', 'label' => 'Data Cheque', 'type' => 'data', 'length' => array(20, 120)),					
					'T002_Agencia_Cheque' => array('name' => 'T002_Agencia_Cheque', 'label' => 'Agência', 'type' => 'text', 'length' => array(20, 120)),
					'T002_Conta_Corrente_Cheque' => array('name' => 'T002_Conta_Corrente_Cheque', 'label' => 'Conta Corrente', 'type' => 'text', 'length' => array(20, 120)),					
					'T002_Banco_Cheque' => array('name' => 'T002_Banco_Cheque', 'label' => 'Banco', 'type' => 'text', 'length' => array(50, 120)),									
					'T002_Nome_Cheque' => array('name' => 'T002_Nome_Cheque', 'label' => 'Nome', 'type' => 'text', 'length' => array(50, 120)),					
					'T002_Cidade_Cheque' => array('name' => 'T002_Cidade_Cheque', 'label' => 'Cidade', 'type' => 'text', 'length' => array(50, 120)),
					'T002_Observacao_Cheque' => array('name' => 'T002_Observacao_Cheque', 'label' => 'Observação Cheque', 'type' => 'textarea', 'length' => array(50, 120)),
					'T002_Cheque_Repassado' => array('name' => 'T002_Cheque_Repassado', 'label' => 'Repassado', 'type' => 'text', 'length' => array(50, 120)),
				),
				'remessa' => array(
					'T002_T035_Id' => array('name' => 'T002_T035_Id', 'label' => 'Número da Remessa', 'type' => 'text', 'length' => array(20, 120)),
				)								
			)
		);

		if($g['nivelAlcada'] == 4){
			$formArray['campos']['remessa']['T002_Percentual_Comissao'] = array('name' => 'T002_Percentual_Comissao', 'label' => '%', 'type' => 'moeda', 'align' => 'right', 'length' => array(10,10), 'readonly' => $restricaoEditarComissao);
	    	$formArray['campos']['remessa']['T002_Valor_Comissao'] = array('name' => 'T002_Valor_Comissao', 'label' => 'Comissão Vendedor Interno', 'type' => 'moeda','align' => 'right', 'length' => array(10,10), 'readonly' => $restricaoEditarComissao);
	    	$formArray['campos']['remessa']['T002_Percentual_Comissao_Representante'] = array('name' => 'T002_Percentual_Comissao_Representante', 'label' => '%', 'type' => 'moeda','align' => 'right', 'length' => array(10,10), 'readonly' => $restricaoEditarComissao);
	    	$formArray['campos']['remessa']['T002_Valor_Comissao_Representante'] = array('name' => 'T002_Valor_Comissao_Representante', 'label' => 'Comissão Representante', 'type' => 'moeda','align' => 'right', 'length' => array(10,10), 'readonly' => true);
	    	$formArray['campos']['remessa']['T002_Percentual_Comissao_Externo'] = array('name' => 'T002_Percentual_Comissao_Externo', 'label' => '%', 'type' => 'moeda','align' => 'right', 'length' => array(10,10), 'readonly' => $restricaoEditarComissao);
	    	$formArray['campos']['remessa']['T002_Valor_Total_Comissao_Externo'] = array('name' => 'T002_Valor_Total_Comissao_Externo', 'label' => 'Comissão Vendedor Externo', 'type' => 'moeda','align' => 'right', 'length' => array(10,10), 'readonly' => true);
	    	$formArray['campos']['remessa']['T002_Percentual_Comissao_Usuario'] = array('name' => 'T002_Percentual_Comissao_Usuario', 'label' => '%', 'type' => 'moeda','align' => 'right', 'length' => array(10,10), 'readonly' => $restricaoEditarComissao);
	    	$formArray['campos']['remessa']['T002_Valor_Total_Comissao_Usuario'] = array('name' => 'T002_Valor_Total_Comissao_Usuario', 'label' => 'Comissão Usuário', 'type' => 'moeda','align' => 'right', 'length' => array(10,10), 'readonly' => true);
			//$formArray['campos']['remessa']['T002A_Percentual_Comissao_Supervisor'] = array('name' => 'T002A_Percentual_Comissao_Supervisor', 'label' => '%', 'type' => 'moeda','align' => 'right', 'length' => array(10,10), 'readonly' => $restricaoEditarComissao);
	    	//$formArray['campos']['remessa']['T002A_Valor_Total_Comissao_Supervisor'] = array('name' => 'T002A_Valor_Total_Comissao_Supervisor', 'label' => 'Comissão Supervisor', 'type' => 'moeda','align' => 'right', 'length' => array(10,10), 'readonly' => true);
		}
		
		
		/* So exibe o campo para o Numero de Parcelas, se for um Incluir */
		if( empty($r_acaoId) )
			$formArray['campos']['cadastro_titulo']['NumeroParcelas'] = array('name' => 'NumeroParcelas', 'label'=>'Numero de Parcelas','reject' => true,'type' => 'moeda', 'casasDecimais' => 0);
		/*
			Exibe a opcao de parcela, apenas na 1 parcela, ou na inclusao
			
		$sql = mysql_query("SELECT * FROM T015 WHERE T015_Id = '{$r_acaoId}' AND T015_Numero_Parcela = '1'");
		if( mysql_num_rows($sql) > 0 || empty($r_acaoId) ){
			$formArray['campos']['cadastro_titulo']['NumeroParcelas'] = array('name' => 'NumeroParcelas', 'label'=>'Numero de Parcelas','reject' => true,'type' => 'text');
		} else{
			$formArray['campos']['cadastro_titulo']['NumeroParcelas'] = array('name' => 'NumeroParcelas', 'label'=>'Numero de Parcelas','reject' => true,'type' => 'text', 'disabled' => true);
		}*/
		
		$form->definirFormularioArray($formArray);
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#T002_D014_Id_refreshBtn').bind('click', function(){
					alteraConta($('#T002_D032_Id'), '{$g['divId']}');
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
				$T002_Id = false;
				// Insert ou Update?
				if (!empty($acaoId)) {
					// Update

					$validaDataEmissao = true;
					$validacao = '';

					//Não inclui títulos onde a Data de Vencimento é anterior a Emissão
					if($g['C031']['aceitarVencimentoAnteriorEmissaoPagar'] == 'N'){
					$T002_Data_Vencimento = gCorrigeDataInverte($form->campoValorEnviado('T002_Data_Vencimento'));
					$T002_Data_Emissao = gCorrigeDataInverte($form->campoValorEnviado('T002_Data_Emissao'));
					$sqlEmissao = mysql_query("SELECT DATEDIFF('{$T002_Data_Emissao}','{$T002_Data_Vencimento}')");
					$diferencaEmissao = mysql_fetch_array($sqlEmissao);	
						if($diferencaEmissao[0] > 0){
							$validaDataEmissao = false;
							$validacao = 'A Data de Vencimento não pode ser anterior a Data de Emissão! <br />';
						}
					}

					if (!empty($validacao)){
						echo gGeraAlertMsg('Operação Inválida',$validacao);
					}

					//Se validaData for true o processo continua normalmente
					if($validaDataEmissao){

						if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
							
							$valorNumeroParcelas = $form->campoValorEnviado('NumeroParcelas');
							if( !empty($valorNumeroParcelas) && $valorNumeroParcelas !=0 )
							{
								$retorno=$FIN001->duplicarRegistrosUpdate($acaoId, $valorNumeroParcelas);
							}
							
							$T002_Id = $r_acaoId;
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
					}
				} else {
					// Insert
					$validaData = true;
					$validaDataEmissao = true;
					$validacao = '';

					//Não inclui títulos onde a Data de Vencimento é anterior à Emissão
					if($g['C031']['aceitarVencimentoAnteriorEmissaoPagar'] == 'N'){
					$T002_Data_Vencimento = gCorrigeDataInverte($form->campoValorEnviado('T002_Data_Vencimento'));
					$T002_Data_Emissao = gCorrigeDataInverte($form->campoValorEnviado('T002_Data_Emissao'));
					$sqlEmissao = mysql_query("SELECT DATEDIFF('{$T002_Data_Emissao}','{$T002_Data_Vencimento}')");
					$diferencaEmissao = mysql_fetch_array($sqlEmissao);	
						if($diferencaEmissao[0] > 0){
							$validaDataEmissao = false;
							$validacao = 'A Data de Vencimento não pode ser anterior a Data de Emissão! <br />';
						}
					}

					//Define se é para rejeitar inclusão de titulos a pagar atrasados
					if($g['C031']['aceitarTitulosReceberAtrasados'] == 'N'){
						log('Result ='.$g['C031']['aceitarTitulosReceberAtrasados']);
						$T002_Data_Vencimento = gCorrigeDataInverte($form->campoValorEnviado('T002_Data_Vencimento'));
						$sql = mysql_query("SELECT DATEDIFF(CURDATE(),'{$T002_Data_Vencimento}')");
						$diferenca = mysql_fetch_array($sql);					
						log("Resultado= ". $diferenca[0]);
						if($diferenca[0] > 0){
							$validaData = false;
							$validacao .='A Data de Vencimento do Título está atrasada!';
						} else {
							//a Data de Vencimento não está atrasada
							$validaData = true;
						}
					}

					if (!empty($validacao)){
						echo gGeraAlertMsg('Operação Inválida',$validacao);
					}


					//Se validaData for true, ou , a opção Aceitar Titulos Atrasados estiver como SIM, o processo continua normalmente
					if($validaData && $validaDataEmissao){
						log('Result ='.$g['C031']['aceitarTitulosReceberAtrasados']);
						if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
							$T002_Id = $g['mysqlLastId'];
	                        $valorOriginal = gCorrigeNumeroInverte($form->campoValorEnviado('T002_Valor_Duplicata'));
	                        $vencOriginal = gCorrigeDataInverte($form->campoValorEnviado('T002_Data_Vencimento'));
	                        mysql_query("UPDATE T002 SET T002_Valor_Original='{$valorOriginal}',T002_Data_Vencimento_Original='{$vencOriginal}' WHERE T002_Id='{$T002_Id}'");
	                        
	                        $valorNumeroParcelas = $form->campoValorEnviado('NumeroParcelas');
							if( !empty($valorNumeroParcelas) && $valorNumeroParcelas !=0 )
							{
								$retorno=$FIN001->duplicarRegistros($T002_Id, $valorNumeroParcelas);
							}
	                        
	                        // Insere os Acrésciomos e Descontos na tabela T020
							$juros = gCorrigeNumeroInverte($form->campoValorEnviado('T002_Valor_Juros'));
							$taxas = gCorrigeNumeroInverte($form->campoValorEnviado('T002_Valor_Taxas'));
							$desconto = gCorrigeNumeroInverte($form->campoValorEnviado('T002_Valor_Desconto'));
							
							if($juros > 0){
								mysql_query("INSERT INTO T020 (
									                     T020_T002_Id,
														 T020_C004_Id,
														 T020_Data_Lancamento,
														 T020_Historico,
														 T020_Valor,
														 T020_Flag_Deb_Cred
											    ) VALUES (
														 '$T002_Id',
														 '{$g['empresaAtual']}',
														 current_date(),
														 'JUROS',
														 '$juros',
														 'C') 
										    ");
								$cErro = mysql_error();
						        if (!empty($cErro)) {
						            mysql_query("ROLLBACK");
						            return "Erro: $cErro";
						        }
							}if($desconto > 0){
								mysql_query("INSERT INTO T020 (
									                     T020_T002_Id,
														 T020_C004_Id,
														 T020_Data_Lancamento,
														 T020_Historico,
														 T020_Valor,
														 T020_Flag_Deb_Cred
												) VALUES (
														 '$T002_Id',
														 '{$g['empresaAtual']}',
														 current_date(),
														 'DESCONTO',
														 '$desconto',
														 'D') 
										    ");
								$cErro = mysql_error();
						        if (!empty($cErro)) {
						            mysql_query("ROLLBACK");
						            return "Erro: $cErro";
						        }
							}if($taxas > 0){
								mysql_query("INSERT INTO T020 (
														 T020_T002_Id,
													   	 T020_C004_Id,
													     T020_Data_Lancamento,
													     T020_Historico,
													     T020_Valor,
													     T020_Flag_Deb_Cred
											    ) VALUES (
												        '$T002_Id',
													    '{$g['empresaAtual']}',
													    current_date(),
												        'TAXAS',
													    '$taxas',
													    'T') 
										    ");
								$cErro = mysql_error();
						        if (!empty($cErro)) {
						            mysql_query("ROLLBACK");
						            return "Erro: $cErro";
						        }
							}

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
				if ($T002_Id) {
					mysql_query("call T002_Gravar_Totalizacao('$T002_Id')");	
	    			mysql_query("call T002_Baixar_Titulo('$T002_Id','".date('Y-m-d')."')");
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
	
	/**
	* Form Principal - Acrescimos e Descontos
	* Chamado em: fin001tab02
	*/
	case 'fin001form02':
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
		$form_tabela = 'T020';
		$form_campo_pk = 'T020_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'form2_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fin/fin001/form/fin001form02/';

		//form editor
		$form_editor = array('acrescimos_descontos' =>'{"T020_Historico":{"top":7,"left":8,"width":125,"hidden":0},"T020_Valor":{"top":7,"left":199,"width":60,"hidden":0},"T020_Flag_Deb_Cred":{"top":7,"left":315,"width":112,"hidden":0},"form2_btn":{"top":5,"left":480,"width":null,"hidden":0}}' , );
				
		// novo formulário
		$form = new Formulario('form_fin001_form2');

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'acrescimos_descontos' => array(
					'T020_Historico' => array('name' => 'T020_Historico', 'label'=>'Histórico','reload'=>false, 'type' => 'text', 'length' => array(20,30)),
					'T020_Valor' => array('name' => 'T020_Valor', 'label'=>'Valor ','align' => 'right','reload'=>false, 'type' => 'moeda','style'=>'margin-left:5px','length' => array(7,10)),					
					'T020_Flag_Deb_Cred' => array('name' => 'T020_Flag_Deb_Cred','label'=>'Tipo ','align' => 'right', 'type' => 'select', 'style'=>'margin-left:5px;','select' => 
						array(
							array('title' => '...', 'value' => ''),
							array('title' => 'Debito', 'value' => 'D'),
							array('title' => 'Credito', 'value' => 'C'),
							array('title' => 'Taxa debito', 'value' => 'T'),
						)
					),								
					'form2_btn' => array('name' => 'form2_btn', 'type'=>'submit','reload'=>false,'value'=>'Incluir','align' => 'right', 'style'=> 'float:right;','extra'=>'style="margin-left:5px;padding-left:5px;padding-right:5px;padding-top:0px;height:20px;"', 'reject'=>true),
					'T020_Data_Lancamento' => array('name' => 'T020_Data_Lancamento','type' => 'hidden','value'=>date("Y-m-d"),'align' => 'right'),
					'T020_T002_Id' => array('name' => 'T020_T002_Id','type' => 'hidden','value'=>$_REQUEST['acaoId'],'align' => 'right'),		
					'T020_C004_Id' => array('name' => 'T020_C004_Id','type' => 'hidden','value'=>$g['empresaAtual'],'align' => 'right'),					
				)	
			)
		);
		
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
					// Insert
					if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
						// Imprime uma mensagem e fecha a janela
						//$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						$T020_Id = $g['mysqlLastId'];
						if ($T020_Id) {
							// FUNCAO PERSONALIZADO NO APP FINANCEIRO LANCAMENTO ACRESCIMOS/DESCONTOS EM BANCOS E CAIXAS
							require_once('bibliotecas/classes/FIN001.php');
							$FIN001 = new FIN001;
							$FIN001->vinculaContaGrupoT020($T020_Id, $form, false, false);							
							mysql_query("call T002_Gravar_Totalizacao('{$_REQUEST['acaoId']}')");
						}

						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								divRefresh('{$g['divId']}', true);
								divRefresh('{$r_divRefresh}', true);
							} </script></div>
EOT;
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
		//echo '<div style="padding-top:5px;padding-bottom:5px">Inclusão de Acréscimos e Descontos</div>';
		echo $gerado['pagina'];				
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');
	break;
		
	/**
	* Form Principal - Recebimentos
	* Chamado em: fin001tab02
	*/
	case 'fin001form03':
	
		// acrescenta o div com o grid
		$gridId = uniqid();		
		echo '<div id="'.$gridId.'"></div>';		
		
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($gridId, '/fin/fin001/grid/fin001grid03/', '&acaoId=' . urlencode($_REQUEST['acaoId'])),
		);
		echo gProcessaAcoes($actions);
				
	break;	
	
	/**
	* Form Principal - Agrupamentos
	* Chamado em: fin001tab02
	*/
	case 'fin001form04':
		// acrescenta o div com o grid
		$gridId = uniqid();		
		echo '<div id="'.$gridId.'"></div>';		
		
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($gridId, '/fin/fin001/grid/fin001grid04/', '&acaoId=' . urlencode($_REQUEST['acaoId'])),
		);
		echo gProcessaAcoes($actions);
				
	break;		
	
	/**
	* Comentário do Estorno
	* Chamado em: grid_func-js - fin001ValidaCampoRmEstorno()
	*/
	case 'fin001form05':

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
		$form_tabela = 'TXXX';
		$form_campo_pk = 'TXXX';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'content_form5_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fin/fin001/form/fin001form05/';
				
		// novo formulário
		$form = new Formulario('form_fin001_form5');
		$sql = "select 
					D007_Nome_Banco
				from 
					T002 
				left join D007 on T002_D007_Id = D007_Id 
				where 
					T002_Id = '{$acaoId}'";
		$conjuntoDados1 = mysql_query($sql);
		$Var = mysql_fetch_assoc($conjuntoDados1);
		$nomeBanco = $Var['D007_Nome_Banco'];
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		if($g['C031']['HabilitarDataAtualEstornoReceber'] == 'S'){
			$formArray = array(
				'campos' => array(
					'estorno' => array(
						'T019_Obs_Estorno' => array('name' => 'T019_Obs_Estorno', 'label'=>'Observação', 'type' => 'textarea', 'length' => array(8,63)),				
						'acaoId' => array('name' => 'acaoId','type' => 'hidden','reject'=>true, 'value'=>$acaoId),
					)	
				)
			);
        }else{
			/***************************** CARLOS CARVALHO **********************************************/
			$sql = "select 
					D007_Nome_Banco
				from T019 
					left join D007 on T019_D007_Id = D007_Id 
				where 
					T019_T002_Id = '{$acaoId}'";
			$conjuntoDados1 = mysql_query($sql);
			$Var = mysql_fetch_assoc($conjuntoDados1);
			$nomeBanco = $Var['D007_Nome_Banco'];
			$formArray = array(
				'campos' => array(
					'estorno' => array(
						'T019_Obs_Estorno'       => array('name' => 'T019_Obs_Estorno', 'label'=>'Observação', 'type' => 'textarea', 'length' => array(8,63)),
						'D007_Nome_Banco'   => array('name' => 'D007_Nome_Banco', 'label'=> 'Banco', 'type' => 'text', 'length' => array(63,160), 'reject'=>true, 'value' => $nomeBanco, 'disabled' => true, 'readonly' => true),
						'T019_Data_Lancamento'   => array('name' => 'T019_Data_Lancamento', 'label'=>'Data', 'type' => 'data', 'length' => array(8,63)),
						'acaoId' => array('name' => 'acaoId','type' => 'hidden','reject'=>true, 'value'=>$acaoId),
					)	
				)
			);
		}
		
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
				if($g['C031']['HabilitarDataAtualEstornoReceber'] == 'S'){
					$retorno = $FIN001->estornar( $form->campoValorEnviado('acaoId'), $form->campoValorEnviado('T019_Obs_Estorno'));
				}else{
					$retorno = $FIN001->estornar( $form->campoValorEnviado('acaoId'), $form->campoValorEnviado('T019_Obs_Estorno'), gCorrigeDataInverte($form->campoValorEnviado('T019_Data_Lancamento')));
				}
				$retorno = ($retorno === true) ? 'Titulo estornado!' : $retorno;
				// Mensagem
				$alert = gGeraAlertMsg('Operação de Estorno', $retorno, false);
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
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk, "Confirma o estorno?");
		
		
		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());		
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		//echo '<div style="padding:5px">Estorno de título - ID: '.$acaoId.'</div>';
		echo $gerado['pagina'];				
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('form.tpl');

		echo <<<JS
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {				
				const table = $("#{$g['divId']}").find('form.formFormulario > table').first();
				table.css('background', 'transparent'); // Removendo a cor de fundo
			} </script></div>
JS;

		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="content_form5_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		//$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
				
	break;			
	
	/**
	* Remessa
	* Chamado em: fin001tab03
	*/
	case 'fin001form06':
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
		$form_tabela = 'D007';
		$form_campo_pk = 'D007_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'form_remessa_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fin/fin001/form/fin001form06/';

		//form editor
		$form_editor = array(
			'remessa' => '{"Conta":{"top":30,"left":12.53125,"width":351,"hidden":0}}'
		);

		// novo formulário
		$form = new Formulario('form_fin001_remessa');
		
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'remessa' => array(
					'Conta' => array(
						'name' => 'Conta', 
						'label' => 'Conta', 
						'type' => 'select', 
						'reject' => true, 'select' =>
						gGeraSelect('D007', 'D007_Id', 'D007_Nome_Banco, D007_Numero_Agencia, D007_Numero_Conta, D007_Tipo_Registro(D007_Flag_Tipo_Registro)', "where D007_Flag_Layout='1' and D007_Flag_Ativo='S' order by D007_Nome_Banco")
					)
				)
			)
		);
		$form->definirFormularioArray($formArray);

		// Quantidade de registros para gerar remessa
		$qteTitulos = (isset($_REQUEST['qteTitulos'])) ? $_REQUEST['qteTitulos'] : 0;
		echo "<br /><spam style='margin-left:10px;font-size:14px;font-weight:bold'>Total de titulos marcados para remessa: <font style='color: var(--BGVermelho);font-weight:bold;font-size:16px'>" . $qteTitulos . "</font></spam>";
		
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
				$last = $FIN001->gerarRemessaBancaria($form->campoValorEnviado('Conta'));
				if (is_int($last)) {
					$alert = gGeraAlertMsg('Remessa Bancária', "Remessa gerada com sucesso!<br/><a href=\"/fin/fin001/grid_func-ajax/downloadRemessa/?T002_T035_Id={$last}&ajax=true\">Abrir Arquivo</a>", false);
				echo <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
							setTimeout(function() {
								{$alert}
							}, 150);
  						});
						$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
						//divRefresh('{$g['divIdParent']}', true);
					} </script></div>
EOT;
				}else{
					echo gGeraAlertMsg('Remessa Bancária com Erro', $last);
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


		// acrescenta o div com o grid
		$gridId = uniqid();
		echo '<div id="'.$gridId.'"></div>';		
		
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';		
		$botoesDir .= '<button type="button" id="form_remessa_btn">Gerar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
	
	/**
	* Agrupar
	* Chamado em: fin001tab04
	*/
	case 'fin001form07':
		/*
		// acrescenta o div com o grid
		$gridId = uniqid();		
		echo '<div id="'.$gridId.'"></div>';		
		
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($gridId, '/fin/fin001/grid/fin001grid05/','&extra='.urlencode($r_extra)),
		);
		echo gProcessaAcoes($actions);
		
		// espaços depois do grid
		echo '<br /><br />';
		*/
		
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/		
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;	
		$extra = isset($r_fin001form07) ? $r_fin001form07['extra'] : $r_extra;	

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
		$form_tabela = 'T002';
		$form_campo_pk = 'T002_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'content_agrupar_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fin/fin001/form/fin001form07/';
				
		// novo formulário
		$form = new Formulario('form_fin001_agrupar');

		// Editor
		$form_editor = array(
			'agrupar' => '{"T002_D027_Id":{"top":30,"left":74,"width":175,"hidden":0},"agruparNovoVencimento":{"top":30,"left":332,"width":72,"hidden":0},"aviso":{"top":53,"left":53,"width":null,"hidden":0}}',
		);


		$C004=mysql_query("select * from C004 where C004_Id='{$g['empresaAtual']}'");
		$mC004=mysql_fetch_array($C004);
		$CNPJ_Empresa_Corrente = substr($mC004['C004_Cnpj'], 0, -8);
		
		$formArray = array(
			'campos' => array(
				'agrupar' => array(
					'T002_D027_Id' => array('name' => 'T002_D027_Id', 'label' => 'Portador', 'type' => 'select','select' => 
						gGeraSelect('D027', 'D027_Id', 'D027_Portador','LEFT JOIN D007 ON D007_Id = D027_D007_Id LEFT JOIN C004 ON C004_Id = D007_C004_Id WHERE C004_Cnpj LIKE "%'.$CNPJ_Empresa_Corrente.'%" OR ifnull(D027_D007_Id,0) = 0 order by D027_Portador', true)
					),
					'agruparNovoVencimento' => array('name' => 'agruparNovoVencimento','label'=>'Novo Vencimento ', 'align'=>'right','type' => 'data', 'length' => array(15,10)),
					'extra' => array('name' => 'extra', 'type' => 'hidden', 'value' => @$r_extra, 'reject' => true),
					'aviso' => array('name' => 'aviso', 'type' => 'comentario', 'value' => "<div class='msgAviso'>Atenção!!! Esse processo só poderá ser feito uma vez, tenha certeza de informar corretamente os dados acima.</div>", 'reject' => true),
				)	
			)
		);	

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
				// Mantém compatibilidade com métodos agrupar personalizados que retornam apenas true.
				$g['FIN001TituloAgrupado'] = 0;
				$retorno = $FIN001->agrupar($form->campoValorEnviado('T002_D027_Id'), $form->campoValorEnviado('agruparNovoVencimento'),$form->campoValorEnviado('extra'));
				$idTituloAgrupado = !empty($g['FIN001TituloAgrupado']) ? (int) $g['FIN001TituloAgrupado'] : 0;
                if ($retorno === true) {
					// Quando foi possível identificar o título criado, a própria confirmação
					// substitui o alerta simples de sucesso.
					$alert = $idTituloAgrupado ? '' : gGeraAlertMsg('Agrupar', 'Agrupado!', false);
				} else {
					$alert = gGeraAlertMsg('Agrupar', $retorno, false);
				}
				echo <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
							setTimeout(function() {
								{$alert}
							}, 150);
						});
						$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
						divRefresh('{$g['divIdParent']}', true);
						if ({$idTituloAgrupado}) {
							var buttonsAbrirTituloAgrupado = {
								"Sim": function() {
									$(this).dialog("destroy").remove();
									abrirJanela(false, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Título a Receber - ID: {$idTituloAgrupado}', '/fin/fin001/content/fin001content02/', '&acaoId={$idTituloAgrupado}&tabela=T002', [1050,540]);
								},
								"Não": function() { $(this).dialog("destroy").remove(); }
							};
							setTimeout(function() {
								dialogConfirm('Agrupar', 'Agrupado com sucesso! Deseja abrir o novo título?', buttonsAbrirTituloAgrupado);
							}, 150);
						}
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
		$g['smarty']->assign('tableAlign','align="center"');
		$g['smarty']->assign('table',$gerado['table']);
		
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');		
		
		//echo '<div class="msgAviso">Atenção!!! Esse processo só poderá ser feito uma vez, tenha certeza de informar corretamente os dados acima.</div>';

	break;
	
	/**
	* Baixar
	* Chamado em: fin001tab05
	*/
	case 'fin001form08':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$extra = isset($r_extra) ? $r_extra : false;

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
		$form_tabela = 'T000';
		$form_campo_pk = 'T000_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$usarBaixaEmEtapas = ($g['C031']['habilitarBaixaEmEtapasContasReceber'] == 'S');
		$enviar_btnId = 'content_baixar_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fin/fin001/form/fin001form08/';

		//form editor
		$camposForm = '';

		if ($g['C031']['utilizaTrocoBaixa'] === 'S') {
			$camposForm = ',"ValorRecebido":{"top":76,"left":592.984375,"width":136,"hidden":0},"ValorTotal":{"top":99,"left":594.296875,"width":136,"hidden":0},"ValorTroco":{"top":122,"left":629.765625,"width":136,"height":25,"hidden":0}';
		}

		$form_editor = array(
			'baixar' => '{"D007_Id":{"top":7,"left":667.671875,"width":143,"hidden":0},"dataBaixa":{"top":30,"left":646.84375,"width":136,"hidden":0},"dataRecebimento":{"top":53,"left":611.9375,"width":136,"hidden":0}'. $camposForm .'}', 
		);

		// novo formulário
		$form = new Formulario('form_fin001_baixar');

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$dataLancamento = ($g['c029Ids'][267]) ? date("d/m/Y") : "";
		$permiteEditar  = ($g['c029Ids'][267]) ? true : false;
		$bancoUsuario   = ($g['c029Ids'][267]) ? " and D007_Id = (SELECT C007_D007_Id from C007 where C007_Id='{$g['usuarioAtual']}') " : "";
		
		$formArray = array(
			'campos' => array(
				'baixar' => array(
					'D007_Id' => array('name' => 'D007_Id', 'label' => 'Banco', 'type' => 'select', 'disabled' => $permiteEditar,'select' => 
						gGeraSelect('D007','D007_Id','D007_Nome_Banco', 'where D007_Flag_Ativo="S"' . $bancoUsuario,''),
					),
					'empty' => array('name' => 'empty', 'label' => '', 'type' => 'hidden'),
					'dataBaixa' => array('name' => 'dataBaixa','label'=>'Data baixa','type'=>'data', 'restricao'=>'globalData', 'length' => array(15,10), 'disabled' => $permiteEditar, 'value' => $dataLancamento),
					'dataRecebimento' => array('name' => 'dataRecebimento','label'=>'Data recebimento','type'=>'data', 'restricao'=>'globalData', 'length' => array(15,10), 'disabled' => $permiteEditar, 'value' => $dataLancamento),
					'extra' => array('name' => 'extra', 'type' => 'hidden', 'value' => @$r_extra, 'reject' => true),
				)
			)
		);

		if ($g['C031']['utilizaTrocoBaixa'] === 'S') {
			$formArray['campos']['baixar']['ValorRecebido'] = array('name' => 'ValorRecebido', 'label' => 'Valor recebido cliente',  'type' => 'moeda', 'reject' => true, 'extra' => "autocomplete=\"off\" oninput=\"calcularValorTroco('{$g['divId']}', '{$r_extra}');\"", 'style' => 'text-align: right;');
			$formArray['campos']['baixar']['ValorTotal']    = array('name' => 'ValorTotal',    'label' => 'Valor total dos títulos', 'type' => 'moeda', 'reject' => true, 'extra' => 'readonly', 'style' => 'text-align: right; background-color: #E8E8E8;');
			$formArray['campos']['baixar']['ValorTroco']    = array('name' => 'ValorTroco',    'label' => 'Valor do troco',          'type' => 'moeda', 'reject' => true, 'extra' => 'readonly', 'style' => 'text-align: right; background-color: #E8E8E8; font-size: 20px;');
		}

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
				if ($usarBaixaEmEtapas) {
					$dadosBaixaLote = array(
						'D007_Id'         => $form->campoValorEnviado('D007_Id'),
						'dataBaixa'       => $form->campoValorEnviado('dataBaixa'),
						'dataRecebimento' => $form->campoValorEnviado('dataRecebimento'),
						'extra'           => $form->campoValorEnviado('extra'),
					);
					
					$dadosBaixaLoteJson = json_encode($dadosBaixaLote);
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							var fin001BaixaLoteDados = {$dadosBaixaLoteJson};
							var fin001BaixaLoteLimit = 10;
							var fin001BaixaProgressId = dialogAlert(
								'Processando baixa',
								"<div style='overflow:hidden;'>" +
									"<div id='fin001BaixaProgress' style='line-height:16px;'>Preparando lote...</div>" +
									"<div style='margin-top:6px; text-align:center;'>" +
										"<img src='/hardness3/static/img/loading.gif' style='width:24px; height:24px;' />" +
									"</div>" +
								"</div>"
							);
							$('#dc-' + fin001BaixaProgressId).parent().css('width', '280px');

							function fin001BaixaAtualizarStatus(data) {
								$('#fin001BaixaProgress').html(
									'<div><b>Processando baixa em lotes</b></div>' +
									'<div>Processados: <b>' + data.processado + '</b> de <b>' + data.total + '</b></div>' +
									'<div>Restantes: <b>' + data.restante + '</b></div>'
								);
							}

							function fin001BaixaFinalizar(data) {
								$('#{$g['divId']}').hideLoading();
								$('#dc-' + fin001BaixaProgressId).dialog("destroy").remove();
								fin001BaixaAtualizarStatus(data);
								dialogConfirm('Operação de Baixa', 'Baixa realizada com sucesso!<br /><br />Processados: <b>' + data.processado + '</b> de <b>' + data.total + '</b>');
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								divRefresh('{$g['divIdParent']}', true);
							}

							function fin001BaixaErro(msg) {
								$('#{$g['divId']}').hideLoading();
								$('#dc-' + fin001BaixaProgressId).dialog("destroy").remove();
								dialogConfirm('Operação de Baixa', msg);
							}

							function fin001ExecutarProximoLote(snapshotIds, T045_Id, processadoAtual) {
								var idsLote = snapshotIds.slice(processadoAtual, processadoAtual + fin001BaixaLoteLimit);
								if (idsLote.length <= 0) {
									fin001BaixaFinalizar({ processado: processadoAtual, total: snapshotIds.length, restante: 0 });
									return;
								}

								$.ajax({
									url: '/fin/fin001/form_func-ajax/fin001BaixaLoteProcessar/?ajax=true',
									type: 'POST',
									dataType: 'json',
									data: {
										D007_Id: fin001BaixaLoteDados.D007_Id,
										dataBaixa: fin001BaixaLoteDados.dataBaixa,
										dataRecebimento: fin001BaixaLoteDados.dataRecebimento,
										extra: fin001BaixaLoteDados.extra,
										T045_Id: T045_Id,
										processadoAtual: processadoAtual,
										'snapshotIds[]': snapshotIds,
										'idsLote[]': idsLote
									},
									success: function(request) {
										if (request.code) {
											fin001BaixaAtualizarStatus(request.data);
											if (request.data.done) {
												fin001BaixaFinalizar(request.data);
												return;
											}
											fin001ExecutarProximoLote(snapshotIds, T045_Id, request.data.processado);
										} else {
											fin001BaixaErro(request.data);
										}
									},
									error: function() {
										fin001BaixaErro('Falha de comunicacao ao processar o lote da baixa.');
									}
								});
							}

							$('#{$g['divId']}').showLoading();
							$.ajax({
								url: '/fin/fin001/form_func-ajax/fin001BaixaLotePreparar/?ajax=true',
								type: 'POST',
								dataType: 'json',
								data: fin001BaixaLoteDados,
								success: function(request) {
									if (request.code) {
										fin001BaixaAtualizarStatus(request.data);
										fin001ExecutarProximoLote(request.data.ids, request.data.T045_Id, 0);
									} else {
										fin001BaixaErro(request.data);
									}
								},
								error: function() {
									fin001BaixaErro('Falha de comunicacao ao iniciar a baixa em lotes.');
								}
							});
						} </script></div>
EOT;
					break;
				}


				// Carlos Carvalho - Calcula troco

				// baixa o Título CARLOS CARVALHO 
				if ($g['C031']['baixaTitulos'] == 'S') {
					$ValorTotalRecebido = $form->campoValorEnviado('ValorTotalRecebido');
					$valorRecebidoCliente = $form->campoValorEnviado('T002A_Valor_Recebido_Cliente');
					$Troco = $form->campoValorEnviado('T002A_Troco');
					$VAR = $FIN001->trocoSmart($ValorTotalRecebido, $valorRecebidoCliente, $Troco);
					$retorno = $FIN001->baixarSmart($form->campoValorEnviado('D007_Id'),gCorrigeDataInverte($form->campoValorEnviado('dataBaixa')),$form->campoValorEnviado('extra'), gCorrigeDataInverte($form->campoValorEnviado('dataRecebimento')));
				}else{
					$retorno = $FIN001->baixar($form->campoValorEnviado('D007_Id'),gCorrigeDataInverte($form->campoValorEnviado('dataBaixa')),$form->campoValorEnviado('extra'), gCorrigeDataInverte($form->campoValorEnviado('dataRecebimento')));
				}
				
				if ($retorno === true){
				$alert = gGeraAlertMsg('Operação de Baixa', 'Baixa realizada com sucesso!', false);
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
				}else{
					echo gGeraAlertMsg('Operação de Baixa', $retorno);
				}
			}
		}	
		
		/**
		* PARTE 4: Gerar o formulário
		*/
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk, 'Tem certeza? Os dados informados acima estao corretos?');
		
		// acrescenta o div com o grid
		//$gridId = uniqid();		
//		echo '<div id="'.$gridId.'"></div>';		
//		
//		$actions = array( 
			// identificador, url, paramentros get (opcional)
//			array($gridId, '/fin/fin001/grid/fin001grid07/','&extra='.urlencode($r_extra)),
//		);		
				
		echo '<br /><hr />';
		echo '<p align="center" style="font-size:14px;font-weight:bold;"><b>BAIXAR</b></p>';
		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		// alinhando a table do form
		$g['smarty']->assign('tableAlign','align="center"');		
		$g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
		//echo '<div style="padding-top:5px;padding-bottom:5px">Inclusão de Acréscimos e Descontos</div>';
		echo $gerado['pagina'];				
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');

		//echo gProcessaAcoes($actions);

	break;
	
	/**
	* Reparcelar
	* Chamado em: fin001tab06
	*/
	case 'fin001form09':
		
		// acrescenta o div com o grid
		$gridId = uniqid();		
		echo '<div id="'.$gridId.'"></div>';		
		
		$actions = array(
			// identificador, url, paramentros get (opcional)
			array($gridId, '/fin/fin001/grid/fin001grid06/'),
		);
		echo gProcessaAcoes($actions);
		
		// espaços depois do grid
		echo '<br /><br />';
		
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
		$form_tabela = 'T002';
		$form_campo_pk = 'T002_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'content_reparcelar_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fin/fin001/form/fin001form09/';
				
		// novo formulário
		$form = new Formulario('form_fin001_reparcelar');		
		
		$formArray = array(
			'campos' => array(
				'reparcelar' => array(
					'numeroDuplicata' => array('name' => 'numeroDuplicata','type'=>'text', 'label'=>'Novo Número', 'length' => array(15,10)),
					'dataEmissao' => array('name' => 'dataEmissao', 'label' => 'Nova Emissão&nbsp;&nbsp;&nbsp;', 'align' => 'right', 'style' => 'float:right;margin-left:30px;', 'type' => 'data', 'length' => array(15,10), 'restricao' => 'globalDataMaiorIgualAtual'),
					'valor1' => array('name' => 'valor1', 'label' => 'Valor 1', 'type' => 'moeda', 'length' => array(15,10), 'restricao' => 'globalNaoVazio'),
					'vencimento1' => array('name' => 'vencimento1', 'label' => 'Vencimento 1&nbsp;&nbsp;&nbsp;', 'align' => 'right', 'style' => 'margin-left:30px;', 'type' => 'data', 'length' => array(15,10), 'restricao' => 'globalDataMaiorIgualAtual'),
				)	
			)
		);
		// Processa os vários campos extras (campos adicionados dinamicamente)
		if (isset($_REQUEST['form_fin001_reparcelar'])) {
			foreach ($_REQUEST['form_fin001_reparcelar'] as $campo => $valor) {
				if (strpos($campo, 'valor') === false && strpos($campo, 'vencimento') === false) { continue; }
				if (strpos($campo, 'valor') !== false) {
					$nr = str_replace('valor', '', $campo);
					if ($nr == 1) { continue; }
					$formArray['campos']['reparcelar'][$campo] = array('name' => $campo, 'label' => "Valor {$nr}", 'type' => 'moeda', 'length' => array(15,10), 'restricao' => 'globalNaoVazio');
				} else {
					$nr = str_replace('vencimento', '', $campo);
					if ($nr == 1) { continue; }
					$formArray['campos']['reparcelar'][$campo] = array('name' => $campo, 'label' => "Vencimento {$nr}&nbsp;&nbsp;&nbsp;", 'align'=>'right','style'=>'margin-left:30px;','type' => 'data', 'length' => array(15,10), 'restricao' => 'globalDataMaiorIgualAtual');
				}
			}
		}
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
				$retorno = $FIN001->reparcelar($form);
				if ($retorno === true) {
			    	$alert = gGeraAlertMsg('Sucesso!', 'Reparcelamento efetuado com sucesso!', false);
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
								divRefresh('{$g['divIdParent']}', true);
								setTimeout(function() {
									{$alert}
								}, 150);
	  						});
							$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
						} </script></div>
EOT;
		        } else {
		        	echo gGeraAlertMsg("Erro!", $retorno);
				}
			}
		}
		
		/**
		* PARTE 4: Gerar o formulário
		*/
		$gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk, 'Tem certeza? Os dados informados acima estao corretos?');
		
		/**
		* PARTE 5: Exibir o formulário
		*/
		$g['smarty']->assign('formularioID', $form->formularioID());
		$g['smarty']->assign('tableAlign','align="center"');	
		$g['smarty']->assign('table',$gerado['table']);
		
		// acrescentar o botão +
		$botaoMais = "<a href=\"#\"><span class=\"ui-icon ui-icon-plusthick\" title=\"Adicionar Parcela\" onClick=\"fin001AdicionarParcela('{$g['divId']}');return false;\">Adicionar</span></a>";
		$g['smarty']->assign('tableExtra','<tr><td></td><td></td><td>'.$botaoMais.'</td></tr>');
		// adicionar o botão X
		echo "<div class=\"divHidden\"><script type=\"text/javascript\"> if (typeof jQuery != 'undefined') {fin001FixBotaoX('{$g['divId']}');} </script></div>";
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('form.tpl');
		echo '<div class="msgAviso">Atenção!!! Esse processo só poderá ser feito uma vez, tenha certeza de informar corretamente os dados acima.</div>';
		
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';		
		$botoesDir .= '<button type="submit" id="content_reparcelar_btn">Reparcelar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';		
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

	break;
	
	case 'fin001formReparcelamento':
		global $g;
		
		$T166_Id = (isset($r_acaoId)) ? $r_acaoId : false;
		
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
		$form_tabela = 'T166';
		$form_campo_pk = 'T166_Id';
		// ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'etc010FormUsuariosClientesBtn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/etc/etc010/form/etc010FormUsuariosClientes/';
		
		// Definição do Formulário
		$formArray = array(
			'campos' => array(
				'geral' => array(
					// Campo normal (do banco)
					'T166_D024_Id' => array('name' => 'T166_D024_Id', 'label' => 'Cliente/Fornecedor', 'extra'=>'style="width:350px;"', 'disabled'=>true, 'type' => 'select', 
						'select' =>  gGeraSelect('D024', 'D024_Id','D024_Nome_Empresa',"" ),
					),
					'T166_C007_Id' => array('name' => 'T166_C007_Id', 'label' => 'Usuário', 'extra'=>'style="width:350px;"', 'disabled'=>true, 'type' => 'select', 
						'select' =>  gGeraSelect('C007', 'C007_Id','C007_Primeiro_Nome', ''),
					),
					'T166_Tipo' => array('name' => 'T166_Tipo', 'label' => 'Tipo', 'type' => 'select', 'extra'=>'style="width:350px;"', 'disabled'=>true, 'select' => 
						array(
							array('title' => '...', 'value' => ''),
							array('title' => 'Reparcelamento', 'value' => 'R'),
							array('title' => 'Agrupamento', 'value' => 'A'), 
						)
					),
					'T166_Data_Emissao' => array('name' => 'T166_Data_Emissao', 'label' => 'Data','disabled'=>true, 'type' => 'data', 'length' => array(10,15)),
					'T166_Valor_Total' => array('name' => 'T166_Valor_Total', 'label' => 'Valor Total','disabled'=>true, 'type' => 'moeda', 'length' => array(10,30)),
					//'T166_Titulos_Geradores' => array('name' => 'T166_Titulos_Geradores', 'label' => 'Títulos Geradores','disabled'=>true, 'type' => 'textarea', 'length' => array(4,60)),
					//'T166_Titulos_Gerados' => array('name' => 'T166_Titulos_Gerados', 'label' => 'Títulos Gerados','disabled'=>true, 'type' => 'textarea', 'length' => array(4,60)),
				)
			)
		);
        $form = new Formulario('form-fin001FormPrincipalReparcelamento');
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
					if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $r_acaoId, $form)) {
						// Fecha a janela, recarrega o parent (grid) e emite uma mensagem de alerta
						//$alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
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
						// Fecha a janela, recarrega o parent (grid), emite uma mensagem de alerta e abre o registro para alteração (o que acabou de ser incluido)
						$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								// Fecha a janela, recarrega o grid e abre a alteração
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
		$g['smarty']->display('form.tpl');

	break;
	
	case 'fin001FormPagamento':
			$acaoId = (isset($r_acaoId)) ? $r_acaoId : false; 
			$T005_Id = (isset($r_T005_Id)) ? $r_T005_Id : false;			
			
			//echo $T005_Id;
			//('qqqq');
			/**
			* PARTE 1: Variavéis envidas para o formulário
			*/
			// Variável indica qual tab do form será exibida
			$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
			$r_divReload = isset($r_ven002FormPagamento) ? $r_ven002FormPagamento['divReload'] : $r_divReload;	
			$extra = isset($r_fin001FormPagamento) ? $r_fin001FormPagamento['extra'] : $r_extra;
		
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
			$form_tabela = '';
			$form_campo_pk = '';
			// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
			$enviar_btnId = 'fin001FormPagamentoReceber_btn';
			// Form que será executado ao clicar no botão
			$enviar_acaoHref = '/fin/fin001/form/fin001FormPagamento/';

			//form editor
			$form_editor = array(
				'geral' => '{"Total_Parcelas":{"top":6.98529052734375,"left":64.21568965911865,"width":164,"hidden":0},"selecionaPrimeiroVencimento":{"top":6.98529052734375,"left":311.3204565048218,"width":112,"hidden":0},"primeiroVencimentoData":{"top":6.98529052734375,"left":527.9871129989624,"width":72.03921600000001,"hidden":0},"primeiroVencimentoNum":{"top":6.98529052734375,"left":527.9871129989624,"width":72.03921600000001,"hidden":0},"valorAcrescimo":{"top":6.98529052734375,"left":646.2163000106812,"width":104.03921600000001,"hidden":0},"fin001FormAdicionarParcela_btn":{"top":50.9957275390625,"left":911.9944982528687,"width":null,"hidden":0},"Portador_D027_Id":{"top":29.993896484375,"left":56.249999046325684,"width":165,"hidden":0},"selecionaDias":{"top":29.993896484375,"left":312.49999141693115,"width":112,"hidden":0},"IntervalorDias":{"top":29.993896484375,"left":527.9871129989624,"width":72.03921600000001,"hidden":0},"diaFixo":{"top":29.993896484375,"left":527.9871129989624,"width":72.03921600000001,"hidden":0},"valorDesconto":{"top":29.993896484375,"left":652.0374059677124,"width":104.03921600000001,"hidden":0},"Ndcto":{"top":52.987152099609375,"left":8.042279243469238,"width":152.039216,"hidden":0},"Emissao":{"top":52.987152099609375,"left":326.9607763290405,"width":104.03921600000001,"hidden":0},"fin001FormPagamentoReceber_btn":{"top":50.9957275390625,"left":815.9926671981812,"width":null,"hidden":0}}',

			);
			
			$queryT002 = <<<EOT
			SELECT 
			T002_Numero_Duplicata, 
			T002_Data_Emissao, 
			D024_Nome_Empresa,
			D024_Id, 
			T002_Data_Vencimento, 
			T002_Valor_Duplicata, 
			T002_Valor_Saldo
			FROM T002 
			left join D024 on D024_Id=T002_D024_Id 
			left join D027 on D027_Id=T002_D027_Id 
			left join T117 on T117_Id=T002_T117_Id 
			left join D014 on D014_Id=T002_D014_Id 
			left join D046 on D046_Id=D024_D046_Id 
			left join T007 on T007_Id=T002_T007_Id 
			left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno 
			left join T005 on T005_Id=T007_T005_Id
			WHERE 			 		
EOT;
			$queryT002 .= gPegarParteWhere(gInsertExtraWhere(base64_decode($extra), "(T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null) AND ifnull(T002_T035_Id,0)<=0 AND T002_Flag_Reparcelar='S' AND T002_Data_Recebimento='0000-00-00' AND T002_C004_Id='{$g['empresaAtual']}' "));
			$queryT002 .= " ORDER BY T002_Data_Vencimento ";
			$T002 = mysql_query($queryT002);
			
			$valorSomado = 0;
			while($aT002 = mysql_fetch_array($T002)){
				$D024_Id = $aT002['D024_Id'];
				$valorSomado = ($valorSomado + $aT002['T002_Valor_Saldo']);
			}
			
			// echo "D024_Id: ".$D024_Id."<br />Total: ".$valorSomado;
			// die(); 
			
			

			// novo formulário			
			$form = new Formulario('fin001FormPagamento');			
			
			$select = array();
			$select[] = array('value' => '0', 'title' => 'A COMBINAR');
			for ($i=1; $i<=$g['C031']['numeroMaxParcelas']; $i++) {
				$valorParcela=$FIN001->fin001calcularParcelaCombo($i,$valorSomado);
				if(@$r_simular == true){
					if($i=='1'){
						$select[] = array('value' => $i, 'title' => str_pad($i,2,"0",STR_PAD_LEFT) . ($i == 1 ? " Parcela&nbsp;&nbsp;" : " Parcelas"), 'selected'=>true);
					}else{
						$select[] = array('value' => $i, 'title' => str_pad($i,2,"0",STR_PAD_LEFT) . ($i == 1 ? " Parcela&nbsp;&nbsp;" : " Parcelas"));
					}
				} else {
					if($i=='1'){
						$select[] = array('value' => $i, 'title' => str_pad($i,2,"0",STR_PAD_LEFT) . ($i == 1 ? " Parcela&nbsp;&nbsp;" : " Parcelas") . " - " .$valorParcela, 'selected'=>true);
					}else{
						$select[] = array('value' => $i, 'title' => str_pad($i,2,"0",STR_PAD_LEFT) . ($i == 1 ? " Parcela&nbsp;&nbsp;" : " Parcelas") . " - " .$valorParcela);
					}
				}
			}


			$C004=mysql_query("select * from C004 where C004_Id='{$g['empresaAtual']}'");
			$mC004=mysql_fetch_array($C004);
			$CNPJ_Empresa_Corrente = substr($mC004['C004_Cnpj'], 0, -8);
			// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
			$formArray = array(
				'campos' => array(
					'geral' => array(
						'Total_Parcelas' => array('name' => 'Total_Parcelas', 'label' => 'Parcelas', 'type' => 'select', 'select' => $select, 'extra' => 'style="width:164px;"', 'reject' => true),
						//'valorParcelaSimulacao' => array('name' => 'valorParcelaSimulacao', 'label' => '', 'type' => 'text','extra'=>'style="background:#E8E8E8"','reject' => true, 'length' => array(3,6), 'align' => 'right', 'readonly'=>'readonly'),
						'valorAcrescimo' => array('name' => 'valorAcrescimo', 'label' => 'Acréscimo', 'type' => 'moeda', 'reject' => true, 'length' => array(26,12)),
						'valorDesconto' => array('name' => 'valorDesconto', 'label' => 'Desconto', 'type' => 'moeda', 'reject' => true, 'length' => array(26,12)),
						'selecionaPrimeiroVencimento' => array('name' => 'selecionaPrimeiroVencimento', 'label'=>'Primeira Parcela.', 'reject'=>true, 'type' => 'select','select' => 
						array(
							array('title' => 'Dias', 'value' => 'Dias'),
							array('title' => 'Vencimento', 'value' => 'Data', 'selected' => true),
						)),
						'primeiroVencimentoNum' => array('name' => 'primeiroVencimentoNum', 'label' => '', 'type' => 'text', 'reject' => true, 'length' => array(3,6), 'align' => 'right'),
						'primeiroVencimentoData' => array('name' => 'primeiroVencimentoData', 'label' => '', 'type' => 'data', 'reject' => true, 'align' => 'right'),
						'selecionaDias' => array('name' => 'selecionaDias', 'label'=>'Demais Parcelas', 'type' => 'select', 'reject'=>true,'select' => 
						array(
							array('title' => 'Intervalo', 'value' => 'I', 'selected' => true),
							array('title' => 'Dia Fixo', 'value' => 'F'),
						)),			
						'IntervalorDias' => array('name' => 'IntervalorDias', 'label' => '', 'type' => 'text', 'reject' => true, 'length' => array(3,6), 'align' => 'right'),
						'diaFixo' => array('name' => 'diaFixo', 'label' => '', 'type' => 'text', 'reject' => true, 'length' => array(3,2), 'align' => 'right'),												
						'Portador_D027_Id' => array('name' => 'D027_Id','label'=>'Portador', 'extra'=>'style="width:165px;"', 'reject'=>true, 'type' => 'select', 'select' =>
							gGeraSelect('D027', 'D027_Id', 'D027_Portador','LEFT JOIN D007 ON D007_Id = D027_D007_Id LEFT JOIN C004 ON C004_Id = D007_C004_Id WHERE C004_Cnpj LIKE "%'.$CNPJ_Empresa_Corrente.'%" OR ifnull(D027_D007_Id,0) = 0 order by D027_Portador', true)
						),
						'Ndcto' => array('name' => 'Ndcto', 'label' => 'Número Documento', 'type' => 'text', 'reject' => true, 'length' => array(27,100)),
						'Emissao' => array('name' => 'Emissao', 'label' => 'Data Emissão', 'type' => 'data', 'value'=>date("d/m/Y"), 'reject' => true, 'length' => array(10,100)),
						'fin001FormPagamentoReceber_btn' => array('name' => 'fin001FormPagamentoReceber_btn', 'type'=>'submit','reload'=>false,'value'=>'Gerar Parcelas','align' => 'right', 'reject'=>true),
						'fin001FormAdicionarParcela_btn' => array('name' => 'fin001FormAdicionarParcela_btn', 'type'=>'submit', 'reload' => false, 'value' => 'Adicionar Parcela', 'align' => 'right', 'style' => 'float:right;', 'reject' => true),
						// 'T005_Observacao_Pagamento' => array('name' => 'T005_Observacao_Pagamento', 'label' => 'Observação', 'type' => 'textarea', 'length' => array(1,23)),
						// 'ven002SalvarObservacao_btn' => array('name' => 'ven002SalvarObservacao_btn', 'type'=>'submit', 'reload' => false, 'value' => 'Salvar Observação', 'align' => 'right', 'style' => 'float:right; margin-right:120px; margin-top:15px;', 'reject' => true),						'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
						'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
						'D024_Id' => array('name' => 'D024_Id', 'label' => '', 'type' => 'hidden', 'value' => @$D024_Id, 'reject' => true),
						'valorSomado' => array('name' => 'valorSomado', 'label' => '', 'type' => 'hidden', 'value' => @$valorSomado, 'reject' => true),
						'extra' => array('name' => 'extra', 'type' => 'hidden', 'value' => @$r_extra, 'reject' => true),
					)
				)
			);
		
		if(@$r_simular == true){
			$formArray['campos']['geral']['ValorSomado'] = array('name' => 'ValorSomado', 'label' => 'Saldo Devedor', 'type' => 'moeda', 'value' => gCorrigeNumero($valorSomado), 'reject' => true, 'disabled' => true);
			$formArray['campos']['geral']['ValorCompra'] = array('name' => 'ValorCompra', 'label' => 'Valor da Compra', 'type' => 'moeda', 'reject' => true);
		}
 
		$form->definirFormularioArray($formArray);
		
		echo <<<EOT
		    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		    	$('#fin001FormAdicionarParcela_btn').unbind('click');
				$('#fin001FormAdicionarParcela_btn').unbind('click').bind('click', function(data) {
					var id = '{$row['T005_D024_Id']}';
						$.getJSON('/fin/fin001/form_func-ajax/inserirParcela/?ajax=true=' + '&' + $("#{$formId}").serialize() + '&callback=?', function(request) {
							if (request.code) {															
								divRefresh("{$r_divReload}");
							} else {
								var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } };
    							dialogConfirm("Erro!", request.data, buttons)
							}
						});
					return false;
				});
				$('#ven002SalvarObservacao_btn').unbind('click').bind('click', function(data) {
					$.getJSON('/ven/ven002/form_func-ajax/salvarObservacao/?ajax=true=' + '&' + $("#{$formId}").serialize() + '&T005_Id=' + encodeURIComponent('{$T005_Id}') + '&callback=?', function(request) {
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
					$.getJSON('/fin/fin001/form_func-ajax/fin001calcularParcelaSimulacao/?ajax=true=' + '&' + $("#{$formId}").serialize() + '&callback=?', function(request) {
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
					$retorno = $FIN001->fin001atualizarParcelas($form);
					if ($retorno === true) {
						$alert = gGeraAlertMsg('Operação OK', 'Parcelas cadastradas com sucesso.', false);
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									if (typeof jQuery != 'undefined') {
                                       //divRefresh("{$g['divId']}", true);
                                       divRefresh('{$form->campoValorEnviado('divReload')}');
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
	
	case 'fin001FormDetalhesCheque':
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
		$form_tabela = 'T180';
		$form_campo_pk = 'T180_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'fin001FormDetalhesCheque_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fin/fin001/form/fin001FormDetalhesCheque/';
				
		// novo formulário
		$form = new Formulario('fin001ContentDetalhesCheque');

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T180_Numero_Cheque' => array('name' => 'T180_Numero_Cheque', 'label' => 'Número', 'type' => 'text', 'length' => array(20, 120)),
					'T180_Data_Cheque' => array('name' => 'T180_Data_Cheque', 'label' => 'Data', 'type' => 'data', 'length' => array(20, 120)),					
					'T180_Agencia_Cheque' => array('name' => 'T180_Agencia_Cheque', 'label' => 'Agência', 'type' => 'text', 'length' => array(20, 120)),
					'T180_Conta_Corrente_Cheque' => array('name' => 'T180_Conta_Corrente_Cheque', 'label' => 'Conta Corrente', 'type' => 'text', 'length' => array(20, 120)),					
					'T180_Banco_Cheque' => array('name' => 'T180_Banco_Cheque', 'label' => 'Banco', 'type' => 'text', 'length' => array(50, 120)),									
					'T180_Nome_Cheque' => array('name' => 'T180_Nome_Cheque', 'label' => 'Nome', 'type' => 'text', 'length' => array(50, 120)),					
					'T180_Cidade_Cheque' => array('name' => 'T180_Cidade_Cheque', 'label' => 'Cidade', 'type' => 'text', 'length' => array(50, 120)),
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

	case 'fin001FormRetornoRemessas':
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
		$form_tabela = 'T000';
		$form_campo_pk = 'T000_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'fin001FormRetornoRemessas_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fin/fin001/form/fin001FormRetornoRemessas/';
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('fin001FormRetornoRemessas');
		$formArray = array(
			'campos' => array(
				'geral' => array(
				)
			)
		);
		$formArray['campos']['geral']['uploadArquivoTXT'] 			= array('name' => 'uploadArquivoTXT', 'label' => 'Selecione o TXT', 'type' => 'file', 'length' => array(24, 24), 'reject' => true);
		$formArray['campos']['geral']['fin001FormRetornoRemessas_btn'] 		= array('name' => 'ven003FormImportarTXT_btn', 'type'=>'submit', 'reload' => false, 'value' => 'Enviar', 'align' => 'right', 'style' => 'float:right;', 'extra' => 'style="margin-right:10px;margin-top:0px;padding-left:10px;padding-right:10px;padding-top:0px;height:20px;"', 'reject'=>true);
		//$formArray['campos']['geral']['divReload'] 						= array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true);
		
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

				$arquivo = $form->campoValorEnviado('uploadArquivoTXT');
				//Identifica no arquivo de retorno o banco								
				if(empty($arquivo['tmp_name'])){					
					echo gGeraAlertMsg('Erro', 'Selecione um arquivo para importar');
				} else {
					$arrayArquivo            = file($arquivo['tmp_name']);
					// Numero do banco pelo CNAB400
					$codigoBanco    	     = substr($arrayArquivo[0],76,3);

					// Numero do banco pelo CNAB200 (No momento BB e Viacredi possuem esse layout)
					$codigoBancoCNAB240 = substr($arrayArquivo[0],0,3);
					$CNAB240 		    = false;

					if($codigoBancoCNAB240 === '085'){
						// Viacredi não precisa tratar se for CNAB240 porque só foi desenvolvido nesse layout
						$codigoBanco = $codigoBancoCNAB240;
					} elseif ($codigoBancoCNAB240 === '001') {
						// Banco do brasil precisa tratar se for CNAB240 porque tem os dois layouts desenvolvidos
						$CNAB240     = true;
						$codigoBanco = $codigoBancoCNAB240;
					} elseif ($codigoBancoCNAB240 === '756') {
						// Sicoob precisa tratar se for CNAB240 porque tem os dois layouts desenvolvidos
						$CNAB240     = true;
						$codigoBanco = $codigoBancoCNAB240;
					}	

					if(empty($codigoBanco)){
						echo gGeraAlertMsg('Erro', 'Código do banco não localizado');
					} else {
						//Verifica o codigo do banco, para o Banco do Brasil, verifica se o codigo, agencia, e digitos verificadores
						if($codigoBanco == '001'){
							
							if(!$CNAB240){
								$dadoEmpresa = '';
								$dadoRetorno = substr($arrayArquivo[0],26,14);								
								$sqlD007     = mysql_query("SELECT CONCAT(LPAD(D007_Numero_Agencia, 4, '0'), D007_Digito_Verificador_Agencia, LPAD(D007_Numero_Conta, 8, '0'), D007_Digito_Verificador_Conta) as dadoEmpresa FROM D007 WHERE CONCAT(LPAD(D007_Numero_Agencia, 4, '0'), D007_Digito_Verificador_Agencia, LPAD(D007_Numero_Conta, 8, '0'), D007_Digito_Verificador_Conta) = '{$dadoRetorno}' AND D007_C004_Id = '{$g['empresaAtual']}'");
								if(mysql_num_rows($sqlD007) == 1){
									$D007 = mysql_fetch_array($sqlD007);
									$dadoEmpresa = $D007['dadoEmpresa'];
								}
							} else {								
								$C004 		 = mysql_query("SELECT * FROM C004 WHERE C004_Id = '{$g['empresaAtual']}'");
								$C004		 = mysql_fetch_array($C004);
								$dadoEmpresa = substr(str_replace(array('-','.','/',' '), '', $C004['C004_Cnpj']),0,8);
								$dadoRetorno = substr(substr($arrayArquivo[0], 18, 14),0,8);
							}
							//Verifica o codigo do banco, para o Sicoob, verifica se o codigo, agencia, e digitos verificadores
						} else if($codigoBanco == '756'){

								if(!$CNAB240){
									$C004 = mysql_query("SELECT * FROM C004 WHERE C004_Id = '{$g['empresaAtual']}'");
									$C004 = mysql_fetch_array($C004);
									$dadoEmpresa = substr(str_replace(array('-','.','/',' '), '', $C004['C004_Cnpj']),0,8);
									$dadoRetorno = substr(substr($arrayArquivo[1], 3, 14),0,8);
									
									if($dadoEmpresa != $dadoRetorno){
										$sqlD007 = mysql_query("SELECT SUBSTR(REPLACE(REPLACE(REPLACE(D007_CNPJ,'-',''),'/',''),'.',''),1,8) as D007_CNPJ FROM D007 LEFT JOIN D062 ON D062_Id=D007_D062_Id WHERE SUBSTR(REPLACE(REPLACE(REPLACE(D007_CNPJ,'-',''),'/',''),'.',''),1,8) = '{$dadoRetorno}' AND SUBSTR(REPLACE(REPLACE(REPLACE(D007_CNPJ,'-',''),'/',''),'.',''),1,8) ='{$dadoEmpresa}' AND D062_Numero_Banco = '{$codigoBanco}'");
										if(mysql_num_rows($sqlD007) > 0){
											$rowD007 = mysql_fetch_array($sqlD007);
											$dadoEmpresa = substr($rowD007['D007_CNPJ'],0,8);
										}
									}
								} else {								
									$C004 		 = mysql_query("SELECT * FROM C004 WHERE C004_Id = '{$g['empresaAtual']}'");
									$C004		 = mysql_fetch_array($C004);
									$dadoEmpresa = substr(str_replace(array('-','.','/',' '), '', $C004['C004_Cnpj']),0,8);
									$dadoRetorno = substr(substr($arrayArquivo[0], 18, 14),0,8);
								}

						// Se o código do banco é SICREDI também faz 
						} else if($codigoBanco == '748'){
							$C004 = mysql_query("SELECT * FROM C004 WHERE C004_Id = '{$g['empresaAtual']}'");
							$C004 = mysql_fetch_array($C004);
							$dadoEmpresa = substr(str_replace(array('-','.','/',' '), '', $C004['C004_Cnpj']),0,8);
							$dadoRetorno = substr(substr($arrayArquivo[0], 31, 45),0,8);
							
							if($dadoEmpresa != $dadoRetorno){
								$sqlD007 = mysql_query("SELECT SUBSTR(REPLACE(REPLACE(REPLACE(D007_CNPJ,'-',''),'/',''),'.',''),1,8) as D007_CNPJ FROM D007 LEFT JOIN D062 ON D062_Id=D007_D062_Id WHERE SUBSTR(REPLACE(REPLACE(REPLACE(D007_CNPJ,'-',''),'/',''),'.',''),1,8) = '{$dadoRetorno}' AND SUBSTR(REPLACE(REPLACE(REPLACE(D007_CNPJ,'-',''),'/',''),'.',''),1,8) ='{$dadoEmpresa}' AND D062_Numero_Banco = '{$codigoBanco}'");
								if(mysql_num_rows($sqlD007) > 0){
									$rowD007 = mysql_fetch_array($sqlD007);
									$dadoEmpresa = substr($rowD007['D007_CNPJ'],0,8);
								}
							}
						//Se o código do banco é VIACREDI	
						}else if($codigoBanco == '085'){

							$C004 = mysql_query("SELECT * FROM C004 WHERE C004_Id = '{$g['empresaAtual']}'");
							$C004 = mysql_fetch_array($C004);
							$dadoEmpresa = substr(str_replace(array('-','.','/',' '), '', $C004['C004_Cnpj']),0,8);
							$dadoRetorno = substr(substr($arrayArquivo[0], 18, 14),0,8);
						//Se o código do banco é SICOOB
						} else if($codigoBanco == '756'){

							$C004 = mysql_query("SELECT * FROM C004 WHERE C004_Id = '{$g['empresaAtual']}'");
							$C004 = mysql_fetch_array($C004);
							$dadoEmpresa = substr(str_replace(array('-','.','/',' '), '', $C004['C004_Cnpj']),0,8);
							$dadoRetorno = substr(substr($arrayArquivo[0], 18, 14),0,8);
						//Para os demais bancos, verifica se o CNPJ da empresa
						} else {

							$C004 = mysql_query("SELECT * FROM C004 WHERE C004_Id = '{$g['empresaAtual']}'");
							$C004 = mysql_fetch_array($C004);
							$dadoEmpresa = substr(str_replace(array('-','.','/',' '), '', $C004['C004_Cnpj']),0,8);
							$dadoRetorno = substr(substr($arrayArquivo[1], 3, 14),0,8);
							
							if($dadoEmpresa != $dadoRetorno){
								$sqlD007 = mysql_query("SELECT SUBSTR(REPLACE(REPLACE(REPLACE(D007_CNPJ,'-',''),'/',''),'.',''),1,8) as D007_CNPJ FROM D007 LEFT JOIN D062 ON D062_Id=D007_D062_Id WHERE SUBSTR(REPLACE(REPLACE(REPLACE(D007_CNPJ,'-',''),'/',''),'.',''),1,8) = '{$dadoRetorno}' AND SUBSTR(REPLACE(REPLACE(REPLACE(D007_CNPJ,'-',''),'/',''),'.',''),1,8) ='{$dadoEmpresa}' AND D062_Numero_Banco = '{$codigoBanco}'");
								if(mysql_num_rows($sqlD007) > 0){
									$rowD007 = mysql_fetch_array($sqlD007);
									$dadoEmpresa = substr($rowD007['D007_CNPJ'],0,8);
								}
							}
						}
						if($dadoEmpresa != $dadoRetorno){
							echo gGeraAlertMsg('Erro', "O retorno selecionado não é desta empresa ou o arquivo não está no formato correto! Favor verificar. (banco $codigoBanco). $dadoEmpresa != $dadoRetorno");
						} else {
							
							if($CNAB240 === true){
								require_once("bibliotecas/layouts_remessa/" . $codigoBanco . "/retorno_". $codigoBanco . "_CNAB240.php");
							}elseif(substr($arrayArquivo[1], 0, 1) == '1' && $codigoBanco == '001'){
								//Validação para layout CBR 643 do Banco do Brasil (Convenio de 6 posições)
								require_once("bibliotecas/layouts_remessa/001/retorno_001_convenio_6.php");
							} else {
								require_once("bibliotecas/layouts_remessa/" . $codigoBanco . "/retorno_". $codigoBanco . ".php");
							}

							$RETORNO_REMESSA = new RETORNO_REMESSA();				
							$retorno = $RETORNO_REMESSA->importarRetorno($arquivo);
							if($retorno !== true){
								echo gGeraAlertMsg('Erro', $retorno);
							}else{
								// Imprime uma mensagem
								echo gGeraAlertMsg('Operação OK', 'Arquivo importado com Sucesso!');	
								echo <<<EOT
									<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
										//divRefresh('{$form->campoValorEnviado('divReload')}', true);
										divRefresh('{$r_divGridId}', true);
									} </script></div>
EOT;
							}
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
		$g['smarty']->display('form.tpl');

	break;

	case 'fin001FormVencimento':

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
		$form_tabela = '';
		$form_campo_pk = '';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'fin001FormVencimento_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fin/fin001/form/fin001FormVencimento/';
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('fin001FormVencimento');
		$formArray = array(
			'campos' => array(
				'geral' => array(
				)
			)
		);
		$formArray['campos']['geral']['data_vencimento'] = array('name' => 'data_vencimento', 'label' => 'Data', 'type' => 'data', 'length' => array(24, 24), 'reject' => true);
		//$formArray['campos']['geral']['divReload'] 						= array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true);
		
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
				$r_extra = urldecode($r_extra);
				$data = $form->campoValorEnviado('data_vencimento');			
				$retorno = $FIN001->FIN001AlterarDataVencimento($data, $r_extra);
				if($retorno !== true){
					echo gGeraAlertMsg('Erro', $retorno);
				}else{
					// Imprime uma mensagem
					echo gGeraAlertMsg('Operação OK', 'Operação concluída com sucesso!');	
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							divRefresh('{$g['divIdParent']}');
							$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
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
		$botoesDir .= '<button type="submit" id="fin001FormVencimento_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

	break;

	case 'fin001FormCarta':

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
		$form_tabela = 'T002';
		$form_campo_pk = 'T002_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'fin001FormConta_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fin/fin001/form/fin001FormCarta/';
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('fin001FormCarta');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T002_D007_Id' => array('name' => 'T002_D007_Id', 'label' => 'Conta', 'type' => 'select', 'select' => 
						gGeraSelect('D007', 'D007_Id', 'D007_Nome_Banco','')
					),
					'operacao' => array('name' => 'operacao', 'reject' => true, 'type' => 'hidden')
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
				$operacao = $form->campoValorEnviado('operacao');
				if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
					if($operacao == 'imprimir'){
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								nova_janela('/fin/fin001/outro/fin001CartaPDF/?ajax=true&T002_Id={$acaoId}',0,0,800,600,'yes','no','no','no','yes','no','Impressao');
								$('#operacao').val('');
							} </script></div>
EOT;
					} else {
						echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Carta', '/etc/etc006/content/etc006ContentEmailEnviar/', '&T002_Id={$acaoId}', [900,550]);
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
		$botoesEsq .= '<button type="submit" id="fin001FormConta_btn">Email</button>';
		$botoesEsq .= '<button type="submit" id="fin001FormConta_imprimir">Imprimir</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');

		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#fin001FormConta_imprimir').unbind('click').bind('click', function(){
					$('#operacao').val('imprimir');
					$('#fin001FormConta_btn').trigger('click');
				});
			} </script></div>
EOT;
	break;

	case 'fin001DuplicarTitulos':

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
		$form_tabela = '';
		$form_campo_pk = '';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'fin001DuplicarTitulos_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fin/fin001/form/fin001DuplicarTitulos/';

		$form_editor = array(
			'geral' => '{"DiasDuplicar":{"top":7,"left":5,"width":200,"hidden":0}}'
		);
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponivéis (automacaoFormulario.php)
		$form = new Formulario('fin001DuplicarTitulos');
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'DiasDuplicar' => array('name' => 'DiasDuplicar', 'label' => 'Dias', 'type' => 'text'),
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
				$dias = $form->campoValorEnviado('DiasDuplicar');
				$retorno = $FIN001->duplicarTitulos($dias, $r_extra);
				if($retorno === true){
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							divRefresh('{$g['divIdParent']}');
							fecharJanela('{$g['divId']}');
						} </script></div>
EOT;
				} else {
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    	dialogConfirm('Erro', "{$retorno}", \$buttons);
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
		$botoesDir .= '<button type="submit" id="fin001DuplicarTitulos_btn">Duplicar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'fin001CancelarTitulos':
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
		$form_tabela = 'T002';
		$form_campo_pk = 'T002_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'fin001CancelarTitulos_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fin/fin001/form/fin001CancelarTitulos/';
				
		// novo formulário
		$form = new Formulario('fin001CancelarTitulos');

		$form_editor = array(
			'geral' => '{"T002_Motivo_Cancelamento":{"top":7,"left":16,"height":140,"width":359,"hidden":0}}'
		);

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T002_Motivo_Cancelamento' => array('name' => 'T002_Motivo_Cancelamento', 'label' => 'Motivo', 'type' => 'textarea', 'length' => array(20, 120)),
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
					$retorno = $FIN001->cancelarTitulo($acaoId);
					if($retorno === true){
						$alert = gGeraAlertMsg('Operação OK', 'Título cancelado com sucesso!', false);
					} else {
						$alert = gGeraAlertMsg('Erro', $retorno, false);
					}
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
		$g['smarty']->display('formEditor.tpl');

		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="fin001CancelarTitulos_btn">Cancelar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
		//$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
				
	break;

	case 'fin001FormSerasaRelatoNormal':
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
		$form_tabela = 'X000';
		$form_campo_pk = 'X000_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'form_relato_normal_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fin/fin001/form/fin001FormSerasaRelatoNormal/';

		//form editor
		$form_editor = array('geral' => '{"periodo":{"top":7,"left":23,"width":112,"hidden":0},"dataInicio":{"top":30,"left":40,"width":72,"hidden":0},"dataFim":{"top":53,"left":49,"width":72,"hidden":0},"form_relato_normal_btn":{"top":85,"left":90,"width":null,"hidden":0}}');
				
		// novo formulário
		$form = new Formulario('form_fin001_form2');

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'periodo' => array('name' => 'periodo', 'label' => 'Periodicidade ', 'type' => 'select', 'reject' => true, 'select' => 
						array(
							array('title' => 'Diário', 'value' => 'D'),
							array('title' => 'Semanal', 'value' => 'S'),
							array('title' => 'Mensal', 'value' => 'M'),
							array('title' => 'Quinzenal', 'value' => 'Q'),
						)
					),								
					'dataInicio' => array('name' => 'dataInicio', 'label' => 'Data Início', 'type' => 'data', 'reject' => true),
					'dataFim' => array('name' => 'dataFim', 'label' => 'Data Fim', 'type' => 'data', 'reject' => true),
					'form_relato_normal_btn' => array('name' => 'form_relato_normal_btn', 'type'=>'button', 'reload' => false, 'value' => 'Gerar', 'reject' => true)
				)	
			)
		);
		
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
				$dataInicio = substr($form->campoValorEnviado('dataInicio'),0,10);
				$dataFim    = substr($form->campoValorEnviado('dataFim'),0,10);
				$retorno = $FIN001->serasaRelatoNormal($dataInicio, $dataFim, $form->campoValorEnviado('periodo'));
				if (is_array($retorno)) {
					echo gGeraAlertMsg('Ocorreu um erro!', $retorno[0]);
				} else {
					$dir = $g['pathDados'] . '/tmp/';
					is_dir($dir) or mkdir($dir, 0777, true);
					$arquivo = str_replace('/', '', gCorrigeDataInverte($dataInicio)) . '-' . str_replace('/', '', gCorrigeDataInverte($dataFim)) . '-' . $form->campoValorEnviado('periodo') . '.txt';
					file_put_contents($dir . $arquivo, $retorno);
					echo gGeraAlertMsg('Processo concluído!', "Arquivo gerado com sucesso!");
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							document.location = '/fin/fin001/form_func-ajax/downloadArquivo/?ajax=true&arquivo={$arquivo}';
						} </script></div>
EOT;
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
		//echo '<div style="padding-top:5px;padding-bottom:5px">Inclusão de Acréscimos e Descontos</div>';
		echo $gerado['pagina'];				
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');
	break;

	case 'fin001FormSerasaRelatoConciliacao':
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
		$form_tabela = 'X000';
		$form_campo_pk = 'X000_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'form_relato_concil_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fin/fin001/form/fin001FormSerasaRelatoConciliacao/';

		//form editor
		$form_editor = array('geral' => '{"uploadArquivoConciliacao":{"top":7,"left":11,"width":297,"hidden":0},"form_relato_concil_btn":{"top":41,"left":120,"width":null,"hidden":0}}');
				
		// novo formulário
		$form = new Formulario('form_fin001_form2');

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'uploadArquivoConciliacao' => array('name' => 'uploadArquivoConciliacao', 'reject'=>true, 'label' => 'Arquivo de Conciliação', 'type' => 'file'),
					'form_relato_concil_btn' => array('name' => 'form_relato_concil_btn', 'type'=>'button', 'reload' => false, 'value' => 'Gerar', 'reject' => true)
				)	
			)
		);
		
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
				$arquivo = $form->campoValorEnviado('uploadArquivoConciliacao');
				if (!empty($arquivo['name']) && $arquivo['error'] == UPLOAD_ERR_OK) {
					$retorno = $FIN001->serasaRelatoConciliacao(file_get_contents($arquivo['tmp_name']));
					$dir = $g['pathDados'] . '/tmp/';
					is_dir($dir) or mkdir($dir, 0777, true);
					$arquivo = 'CONCILIADO-' . $arquivo['name'];
					file_put_contents($dir . $arquivo, $retorno);
					echo gGeraAlertMsg('Processo concluído!', "Arquivo gerado com sucesso!");
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							document.location = '/fin/fin001/form_func-ajax/downloadArquivo/?ajax=true&arquivo={$arquivo}';
						} </script></div>
EOT;
				} else {
					if (!empty($arquivo['name'])) {
						echo gGeraAlertMsg('Erro', 'Nenhum arquivo foi selecionado.');
					} else {
						echo gGeraAlertMsg('Erro', 'Ocorreu um erro ao tentar enviar o arquivo. Por favor, tente novamente.');
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
		//echo '<div style="padding-top:5px;padding-bottom:5px">Inclusão de Acréscimos e Descontos</div>';
		echo $gerado['pagina'];				
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');
	break;

	case 'fin001FormDocumentos':
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
		$form_tabela = 'T242';
		$form_campo_pk = 'T242_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'fin001FormDocumentosBtnAdd';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fin/fin001/form/fin001FormDocumentos/';
		
		//Form Editor
		$form_editor = array(
			'geral' => '{"uploadArquivo":{"top":4,"left":12,"width":243,"hidden":0},"fin001FormDocumentosBtnAdd":{"top":5,"left":416,"width":null,"hidden":0}}'
		);
		
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'T242_T002_Id' => array('name' => 'T242_T002_Id', 'type' => 'hidden', 'value' => $r_T002_Id),
					'T242_Data_Hora_Upload' => array('name' => 'T242_Data_Hora_Upload', 'type' => 'hidden', 'value' => date("Y-m-d H:i:s")),
					'divReload' => array('name' => 'divReload', 'label'=>'', 'type' => 'hidden','value' => @$r_divReload, 'reject' => true),
					'uploadArquivo' => array('name' => 'uploadArquivo', 'label' => 'Arquivo documento', 'type' => 'file', 'length' => array(24, 24), 'reject' => true, 'uppercase' => false),
					'fin001FormDocumentosBtnAdd' => array('name' => 'fin001FormDocumentosBtnAdd', 'type' => 'submit', 'reload' => false, 'value' => 'Incluir', 'align' => 'right', 'style' => 'float:right;', 'reject' => true),
					'T242_Nome_Arquivo' => array('name' => 'T242_Nome_Arquivo', 'type' => 'hidden', 'uppercase' => false),
				)
			)
		);
		
		$form = new Formulario('fin001FormDocumentos');
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
						$form->campoSetarValor('T242_Nome_Arquivo', $arquivo['name'], true);
						if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
							// Gravar o arquivo no disco
							$pathinfo = pathinfo($arquivo['name']);
							$pathinfo['extension'] = empty($pathinfo['extension']) ? '.tmp' : $pathinfo['extension'];
							$dir = "{$g['pathDados']}documentos/contas_receber";
							//$dir = "{$g['pathDados']}di";
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
    break;

    case 'fin001FormIncluirCheque':
			/**
			* PARTE 1: Variavéis envidas para o formulário
			*/
			// Variável indica qual tab do form será exibida
			$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
			// Utilizada em UPDATE, é o ID do registro que está sendo editado
			$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
			$T002_Id = (isset($r_T002_Id)) ? $r_T002_Id : false;

			/**
			* PARTE 2: Definição do formulário
			*/
			// Este mesmo form será exibido em multiplas tabs ou não?
			$formMultiTab = true;
			if ($formMultiTab) {
				$formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
			} else {
				$formId = uniqid();
			}

			// A qual tabela este form está relacionada, e a chave primária da tabela
			$form_tabela = 'T246';
			$form_campo_pk = 'T246_Id';
			// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
			$enviar_btnId = 'cad001FormCheque_btn';
			// Form que será executado ao clicar no botão
			$enviar_acaoHref = '/fin/fin001/form/fin001FormIncluirCheque/';

			// novo formulário
			$form = new Formulario('fin001FormCheque');
			$form_editor = array('geral' => '{"T246_D007_Id":{"top":122,"left":319,"width":175,"hidden":0},"T246_Banco_Cheque":{"top":7,"left":75,"width":168,"hidden":0},"T246_Agencia_Cheque":{"top":7,"left":355,"width":168,"hidden":0},"T246_Conta_Corrente_Cheque":{"top":30,"left":33,"width":168,"hidden":0},"T246_Numero_Cheque":{"top":30,"left":356,"width":168,"hidden":0},"T246_Nome_Cheque":{"top":53,"left":77,"width":168,"hidden":0},"T246_Data_Cheque":{"top":53,"left":352,"width":168,"hidden":0},"T246_Data_Vencimento":{"top":76,"left":24,"width":168,"hidden":0},"T246_Valor":{"top":76,"left":369,"width":168,"hidden":0},"T246_Cidade_Cheque":{"top":99,"left":72,"width":168,"hidden":0},"T246_Cheque_Repassado":{"top":99,"left":338,"width":168,"hidden":0},"T246_Data_Deposito":{"top":122,"left":39,"width":168,"hidden":0},"T246_Cpf_Cnpj":{"top":145,"left":64,"width":168,"hidden":0},"T246_Observacao_Cheque":{"top":168,"left":6,"height":39,"width":456,"hidden":0}}');

			// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
			$formArray = array(
				'campos' => array(
					'geral' => array(
						'T246_Id' => array('name' => 'T246_Id','label'=>'Id', 'type' => 'hidden'),
						'T246_Numero_Cheque' => array('name' => 'T246_Numero_Cheque','label'=>'Cheque', 'type' => 'text'),
						'T246_Data_Cheque' => array('name' => 'T246_Data_Cheque','label'=>'Emissão', 'type' => 'data'),
						'T246_Agencia_Cheque' => array('name' => 'T246_Agencia_Cheque','label'=> 'Agência', 'type' => 'text'),
						'T246_Conta_Corrente_Cheque' => array('name' => 'T246_Conta_Corrente_Cheque','label'=>'Conta corrente', 'type' => 'text'),
						'T246_Banco_Cheque' => array('name' => 'T246_Banco_Cheque','label'=>'Banco', 'type' => 'text'),
						'T246_Nome_Cheque' => array('name' => 'T246_Nome_Cheque','label'=>'Nome', 'type' => 'text'),
						'T246_Cidade_Cheque' => array('name' => 'T246_Cidade_Cheque','label'=>'Cidade', 'type' => 'text'),
						'T246_Cheque_Repassado' => array('name' => 'T246_Cheque_Repassado','label'=>'Repassado', 'type' => 'text'),
						'T246_Observacao_Cheque' => array('name' => 'T246_Observacao_Cheque','label'=>'Observação cheque', 'type' => 'textarea'),
						'T246_T002_Id' => array('name' => 'T246_T002_Id','label'=>'Id', 'type' => 'hidden', 'value'=>$T002_Id),
						'T246_Valor' => array('name' => 'T246_Valor','label'=>'Valor', 'type' => 'moeda'),
						'T246_Data_Vencimento' => array('name' => 'T246_Data_Vencimento','label'=>'Data vencimento', 'type' => 'data'),
						'T246_Data_Deposito' => array('name' => 'T246_Data_Deposito','label'=>'Data deposito', 'type' => 'data'),
						'T246_D007_Id' => array('name' => 'T246_D007_Id', 'label' => 'Banco deposito', 	'type' => 'select', 'select' => 
						gGeraSelect('D007', 'D007_Id', 'D007_Nome_Banco','', true)
					),
						'T246_Cpf_Cnpj' => array('name' => 'T246_Cpf_Cnpj','label'=>'Cpf/Cnpj', 'type' => 'text'),
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
				} else {
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
			echo  $gerado['pagina'];
			if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
			$g['smarty']->display('formEditor.tpl');

			/**
			* Botões
			*/
			// Definição dos botões
			$botoesMid = '';
			$botoesDir = '';
			$botoesDir .= '<button type="submit" id="cad001FormCheque_btn">Enviar</button>';
			$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
			$g['smarty']->assign('botoesId', $g['divId']);
			$g['smarty']->assign('botoesEsq', $botoesEsq);
			$g['smarty']->assign('botoesMid', $botoesMid);
			$g['smarty']->assign('botoesDir', $botoesDir);
			$g['smarty']->display('buttons-floatBottom.tpl');

		break;

	case 'fin001FormImportarCSV':
	
		/**
		* PARTE 1: Variáveis envidas para o formulário
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
		$form_tabela = 'T002';
		$form_campo_pk = 'T002';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'fin001FormImportarCSV_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/fin/fin001/form/fin001FormImportarCSV/';
		// Array de definição do formulário
		// Veja documentação ou exemplos para entender todas as variavéis de definição disponíveis (automacaoFormulario.php)
		$form = new Formulario('fin001FormImportarCSV');
		$formArray = array(
			'campos' => array(
				'geral' => array(
				)
			)
		);
		$formArray['campos']['geral']['uploadArquivoCSVFIN001'] 		= array('name' => 'uploadArquivoCSVFIN001', 'label' => 'Selecione o CSV', 'type' => 'file', 'length' => array(24, 24), 'reject' => true);
		$formArray['campos']['geral']['fin001FormImportarCSV_btn'] 		= array('name' => 'fin001FormImportarCSV_btn', 'type'=>'submit', 'reload' => false, 'value' => 'Enviar', 'align' => 'right', 'style' => 'float:right;', 'extra' => 'style="margin-right:10px;margin-top:0px;padding-left:10px;padding-right:10px;padding-top:0px;height:20px;"', 'reject'=>true);
		$formArray['campos']['geral']['T002_Arquivo_Csv'] 				= array('name' => 'T002_Arquivo_Csv', 'type' => 'hidden');
		$formArray['campos']['geral']['divReload'] 						= array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true);
		
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
				$arquivo = $form->campoValorEnviado('uploadArquivoCSVFIN001');
				$retorno = $FIN001->importarCSV($arquivo);
				
				if($retorno !== true){
					//$retorno = htmlentities(utf8_decode($retorno));
					$retorno = htmlentities($retorno);
					echo gGeraAlertMsg('Relatório de Importação', '<textarea style=" background-color: #FFF; width:900px; height:400px; text-transform: none;">' . $retorno . '</textarea>');
				}else{
					// Imprime uma mensagem
					echo gGeraAlertMsg('Operação OK', 'CSV Importado com Sucesso!');
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							divRefresh('{$form->campoValorEnviado('divReload')}', true);
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
	break;

	case 'fin001FormDivisao':
        $acaoId = (isset($r_acaoId)) ? $r_acaoId : false; 
        $T005_Id = (isset($r_T005_Id)) ? $r_T005_Id : false;            
        
        //echo $T005_Id;
        //('qqqq');
        /**
        * PARTE 1: Variavéis envidas para o formulário
        */
        // Variável indica qual tab do form será exibida
        $tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
        $r_divReload = isset($r_ven002FormPagamento) ? $r_ven002FormPagamento['divReload'] : $r_divReload;    
        $extra = isset($r_fin001FormPagamento) ? $r_fin001FormPagamento['extra'] : $r_extra;
        
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
        $form_tabela = '';
        $form_campo_pk = '';
        // ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
        $enviar_btnId = 'fin001FormDivisao_btn';
        // Form que será executado ao clicar no botão
        $enviar_acaoHref = '/fin/fin001/form/fin001FormDivisao/';

        //form editor
		$form_editor = array(
			'geral' => '{"Total_Parcelas":{"top":7,"left":31,"width":208,"hidden":0},"fin001FormDivisao_btn":{"top":5,"left":320,"width":null,"hidden":0},"Emissao":{"top":7,"left":454,"width":72,"hidden":1}}', 
		);
        
        $queryT002 = <<<EOT
	        SELECT 
	        T002_Numero_Duplicata, 
	        T002_Numero_Documento, 
	        T002_Data_Emissao, 
	        D024_Nome_Empresa,
	        D024_Id, 
	        T002_Data_Vencimento, 
	        T002_Valor_Duplicata, 
	        T002_Valor_Saldo, 
	        T002_D027_Id
	        FROM T002 
	        LEFT JOIN D024 ON D024_Id=T002_D024_Id
			LEFT JOIN D027 ON D027_Id=T002_D027_Id
			LEFT JOIN D014 ON D014_Id=T002_D014_Id
			LEFT JOIN D032 ON D032_Id=D014_D032_Id 
			LEFT JOIN D154 ON D154_Id=D014_D154_Id
			LEFT JOIN T007 ON T007_Id=T002_T007_Id
			LEFT JOIN C004 ON C004_Id=T002_C004_Id
			LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno
	        WHERE                      
EOT;
	    //Ao clicar em "gerar parcelas" o form atualiza e o extra retorna com os caracteres '+' e '=' codificados em ASCII, é preciso fazer um replace para que funcione   	
        $extra = str_replace("%3D", "=", $extra);
        $extra = str_replace("%2B", "+", $extra);
        $queryT002 .= gPegarParteWhere(gInsertExtraWhere(base64_decode($extra), "(T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null) AND T002_Flag_Reparcelar='S' AND T002_Data_Recebimento='0000-00-00'"));
        $queryT002 .= "ORDER BY T002_Data_Vencimento DESC";
        $T002 = mysql_query($queryT002);
        
        $valorSomado = 0;
        while($mT002 = mysql_fetch_array($T002)){
            $valorSomado = ($valorSomado + $mT002['T002_Valor_Saldo']);
            $registroT002 = $mT002;
        }

        $form = new Formulario('fin001FormDivisao');            
        
        $select = array();
        $select[] = array('value' => '0', 'title' => 'A COMBINAR');
        for ($i=1; $i<=60; $i++) {
            $valorParcela = gCorrigeNumero($valorSomado/$i);
            if($i=='1'){
                $select[] = array('value' => $i, 'title' => str_pad($i,2,"0",STR_PAD_LEFT) . " PARCELA&nbsp;&nbsp; - " .$valorParcela, 'selected'=>true);
            }else{
                $select[] = array('value' => $i, 'title' => str_pad($i,2,"0",STR_PAD_LEFT) . " PARCELAS - " .$valorParcela);
            }
        }
        // Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
        $formArray = array(
            'campos' => array(
                'geral' => array(
                    'Total_Parcelas' => array('name' => 'Total_Parcelas', 'label' => 'Parcelas', 'type' => 'select', 'select' => $select, 'reject' => true),
                    'fin001FormDivisao_btn' => array('name' => 'fin001FormDivisao_btn', 'type'=>'submit','reload'=>false,'value'=>'Gerar Parcelas','align' => 'right', 'reject'=>true),
                    'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
                    'valorSomado' => array('name' => 'valorSomado', 'label' => '', 'type' => 'hidden', 'value' => @$valorSomado, 'reject' => true),
                    'extra' => array('name' => 'extra', 'type' => 'hidden', 'value' => $r_extra, 'reject' => true),
                    'registroT002' => array('name' => 'registroT002', 'type' => 'hidden', 'value' => serialize($registroT002), 'reject' => true),
                )
            )
        );
 
        $form->definirFormularioArray($formArray);     
             
        /**
         * PARTE 3: Processamento do formulário (se foi efnviado)
         *            E carregamento dos valores (se não foi enviado e possui acaoId)
         */
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
            // Form com erros
            if ($formCodigo == 1) {
                echo gGeraAlertMsgErroForm($form);
                // Form sem erros
            } else if ($formCodigo == 2) {
                // Gerar Boleto            
                $retorno = $FIN001->gerarParcelasDivisao($form);
                if ($retorno === true) {
                    $alert = gGeraAlertMsg('Operação OK', 'Parcelas geradas com sucesso.', false);
                    echo <<<EOT
                        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                                if (typeof jQuery != 'undefined') {
                                   //divRefresh("{$g['divId']}", true);
                                   divRefresh('{$form->campoValorEnviado('divReload')}');
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
}
