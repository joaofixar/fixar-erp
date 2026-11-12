<?php
namespace hardness;

use Error;

//Classe
require_once("bibliotecas/classes/CAD030.php");
require_once("bibliotecas/segurancaSenha.php");
$CAD030 = new CAD030();

/**
* Ações
*/
switch ($g['r'][3]) {
	case 'cad030FormCadastroUsuario':
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
		$form_tabela = 'C007';
		$form_campo_pk = 'C007_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad030FormCadastroUsuario_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad030/form/cad030FormCadastroUsuario/';
		$form_editor = array(
			'geral' => '{"C007_Flag_Departamento":{"top":0,"left":0,"width":184.14378399999998,"hidden":1},"C007_Nome":{"top":6.969970703125,"left":85.55453491210938,"width":312.039216,"hidden":0},"C007_Cargo":{"top":6.969970703125,"left":589.3995361328125,"width":184.039216,"hidden":0},"C007_Primeiro_Nome":{"top":29.978546142578125,"left":41.48284912109375,"width":232.039216,"hidden":0},"C007_Flag_Vendedor":{"top":29.978546142578125,"left":589.6752319335938,"width":191,"hidden":0},"C007_Cpf":{"top":52.95648193359375,"left":94.96017456054688,"width":232.039216,"hidden":0},"C007_Ativo":{"top":52.9718017578125,"left":593.7346801757812,"width":64,"hidden":0},"C007_Flag_Ausente":{"top":52.9718017578125,"left":706.4337768554688,"width":64,"hidden":0},"C007_Cnpj":{"top":75.96505737304688,"left":88.20465087890625,"width":232.039216,"hidden":0},"C007_Login":{"top":75.98037719726562,"left":591.4828491210938,"width":184.039216,"hidden":0},"C007_Senha":{"top":73.9736328125,"left":815.9773559570312,"width":null,"hidden":0},"C007_Crc":{"top":98.9736328125,"left":101.501220703125,"width":232.039216,"hidden":0},"C007_Cep":{"top":98.9736328125,"left":598.253662109375,"width":184.039216,"hidden":0},"buttonSearchCep1":{"top":96.98220825195312,"left":815.9773559570312,"width":null,"hidden":0},"C007_Endereco":{"top":121.96688842773438,"left":76.7769775390625,"width":312.039216,"hidden":0},"C007_Numero":{"top":121.96688842773438,"left":579.335205078125,"width":184.039216,"hidden":0},"C007_Complemento":{"top":144.9754638671875,"left":53.569244384765625,"width":312.039216,"hidden":0},"C007_Bairro":{"top":144.9754638671875,"left":493.4742431640625,"width":280.039216,"hidden":0},"C007_DDD_Telefone":{"top":167.96875,"left":80.25430297851562,"width":41.039215999999996,"hidden":0},"C007_Telefone":{"top":167.96875,"left":175.98040771484375,"width":184.039216,"hidden":0},"C007_D020_Id":{"top":167.96875,"left":488.5570068359375,"width":174,"hidden":0},"C007_D018_Id":{"top":167.96875,"left":717.4019165039062,"width":79,"hidden":0},"C007A_Ramal":{"top":190.97732543945312,"left":91.54412841796875,"width":232.039216,"hidden":0},"C007_Id_Vendedor_Substituto":{"top":190.99264526367188,"left":518.8878784179688,"width":191,"hidden":0},"C007_C007_Id":{"top":213.9705810546875,"left":61.6268310546875,"width":239,"hidden":0},"C007_C007_Id_2":{"top":213.9705810546875,"left":557.628662109375,"width":191,"hidden":0},"C007_Codigo_Barras":{"top":236.97915649414062,"left":38.664215087890625,"width":232.039216,"hidden":0},"C007_Flag_Expedicao":{"top":236.97915649414062,"left":568.030029296875,"width":191,"hidden":0},"C007_IP_Acesso":{"top":259.9724426269531,"left":44.883575439453125,"width":232.039216,"hidden":0},"C007_Dispositivo_Acesso":{"top":259.9724426269531,"left":520.7261352539062,"width":191,"hidden":0},"C007_Acesso_Hora_Inicio":{"top":282.9656677246094,"left":57.07720947265625,"width":72.03921600000001,"hidden":0},"C007_Acesso_Hora_Fim":{"top":282.9656677246094,"left":225.12255859375,"width":72.03921600000001,"hidden":0},"C007_D206_Id":{"top":282.9656677246094,"left":546.6605224609375,"width":191,"hidden":0},"C007_Acesso_Dias_Nao_Uteis":{"top":305.9895935058594,"left":15.379913330078125,"width":80,"hidden":0},"C007_IPV_1":{"top":305.9895935058594,"left":568.1066284179688,"width":184.039216,"hidden":0},"tituloEmail":{"top":325.9650573730469,"left":400.98040771484375,"width":null,"hidden":0},"C007_Email":{"top":351.9914245605469,"left":92.3406982421875,"width":270.039216,"hidden":0},"C007_Email_SMTP_Servidor":{"top":351.9914245605469,"left":450.04595947265625,"width":248.039216,"hidden":0},"C007_Email_SMTP_Usuario":{"top":374.9846496582031,"left":85.094970703125,"width":270.039216,"hidden":0},"C007_Email_SMTP_Porta":{"top":374.9846496582031,"left":463.710205078125,"width":104.03921600000001,"hidden":0},"C007_Email_SMTP_Autenticacao":{"top":374.9846496582031,"left":649.938720703125,"width":56.039215999999996,"hidden":0},"C007_Email_SMTP_Senha":{"top":397.9779357910156,"left":92.126220703125,"width":270.039216,"hidden":0},"C007_Email_SMTP_Seguranca":{"top":397.9779357910156,"left":438.54168701171875,"width":255,"hidden":0},"C007_Email_Assinatura_":{"top":420.9711608886719,"left":25.0919189453125,"height":110.03921600000001,"width":648.0392159999999,"hidden":0}}'
		);
		if ($g['C031']['recebimentoEmail'] == 'S') {
			$form_editor['geral'] = '{"C007_Flag_Departamento":{"top":0,"left":0,"width":176.42132,"hidden":1},"C007_Nome":{"top":6.993133544921875,"left":85.53265380859375,"width":311.80068000000006,"hidden":0},"C007_Cargo":{"top":6.993133544921875,"left":589.3814697265625,"width":151.80068,"hidden":0},"C007_Ativo":{"top":6.993133544921875,"left":817.71484375,"width":64,"hidden":0},"C007_Primeiro_Nome":{"top":30,"left":41.4261474609375,"width":231.80068,"hidden":0},"C007_Flag_Vendedor":{"top":30,"left":589.6563720703125,"width":160,"hidden":0},"C007_Flag_Ausente":{"top":30,"left":802.405517578125,"width":64,"hidden":0},"C007_Cpf":{"top":52.972503662109375,"left":94.948486328125,"width":231.80068,"hidden":0},"C007_Login":{"top":52.972503662109375,"left":591.4605712890625,"width":151.80068,"hidden":0},"C007_Cnpj":{"top":75.97940063476562,"left":88.19586181640625,"width":231.80068,"hidden":0},"C007_Senha":{"top":75.97940063476562,"left":588.1099853515625,"width":151.80068,"hidden":0},"C007_Crc":{"top":98.98626708984375,"left":101.494873046875,"width":231.80068,"hidden":0},"C007_Cep":{"top":98.98626708984375,"left":598.2303466796875,"width":151.80068,"hidden":0},"buttonSearchCep1":{"top":96.99313354492188,"left":799.98291015625,"width":null,"hidden":0},"C007_Endereco":{"top":121.99313354492188,"left":76.7525634765625,"width":311.80068000000006,"hidden":0},"C007_Numero":{"top":121.99313354492188,"left":579.312744140625,"width":151.80068,"hidden":0},"C007_Complemento":{"top":145.00003051757812,"left":53.5223388671875,"width":311.80068000000006,"hidden":0},"C007_Bairro":{"top":145.00003051757812,"left":493.4708251953125,"width":247.80068,"hidden":0},"C007_DDD_Telefone":{"top":167.9725341796875,"left":80.223388671875,"width":40.80068,"hidden":0},"C007_Telefone":{"top":167.9725341796875,"left":175.9793701171875,"width":183.80068,"hidden":0},"C007_D020_Id":{"top":167.9725341796875,"left":488.53955078125,"width":174,"hidden":0},"C007_D018_Id":{"top":167.9725341796875,"left":717.388427734375,"width":47,"hidden":0},"C007A_Ramal":{"top":190.99658203125,"left":91.52923583984375,"width":231.80068,"hidden":0},"C007_Id_Vendedor_Substituto":{"top":190.97940063476562,"left":518.814453125,"width":160,"hidden":0},"C007_C007_Id":{"top":213.98626708984375,"left":61.58074951171875,"width":239,"hidden":0},"C007_C007_Id_2":{"top":213.98626708984375,"left":557.577392578125,"width":159,"hidden":0},"C007_Codigo_Barras":{"top":236.9931640625,"left":38.62542724609375,"width":231.80068,"hidden":0},"C007_Flag_Expedicao":{"top":236.9931640625,"left":567.98974609375,"width":160,"hidden":0},"C007_IP_Acesso":{"top":260.0000305175781,"left":44.82818603515625,"width":231.80068,"hidden":0},"C007_Dispositivo_Acesso":{"top":260.0000305175781,"left":520.6529541015625,"width":160,"hidden":0},"C007_Acesso_Hora_Inicio":{"top":282.9725646972656,"left":57.044677734375,"width":71.80068,"hidden":0},"C007_Acesso_Hora_Fim":{"top":282.9725646972656,"left":225.0859375,"width":71.80068,"hidden":0},"C007_D206_Id":{"top":282.9725646972656,"left":546.5980224609375,"width":159,"hidden":0},"C007_Acesso_Dias_Nao_Uteis":{"top":305.9794006347656,"left":15.32647705078125,"width":80,"hidden":0},"C007_IPV_1":{"top":305.9794006347656,"left":648.07568359375,"width":71.80068,"hidden":0},"tituloEmail":{"top":325.9794006347656,"left":400.97943115234375,"width":null,"hidden":0},"C007_Email":{"top":351.9931945800781,"left":92.319580078125,"width":269.80068000000006,"hidden":0},"C007_Email_SMTP_Servidor":{"top":351.9931945800781,"left":450.00006103515625,"width":247.80068,"hidden":0},"C007_Email_SMTP_Usuario":{"top":375.0000305175781,"left":85.0687255859375,"width":269.80068000000006,"hidden":0},"C007_Email_SMTP_Porta":{"top":375.0000305175781,"left":463.677001953125,"width":103.80068,"hidden":0},"C007_Email_SMTP_Autenticacao":{"top":375.0000305175781,"left":649.89697265625,"width":55.80068,"hidden":0},"C007_Email_SMTP_Senha":{"top":397.9725646972656,"left":92.1134033203125,"width":269.80068000000006,"hidden":0},"C007_Email_SMTP_Seguranca":{"top":397.9725646972656,"left":438.488037109375,"width":255,"hidden":0},"C007_Email_POP_Servidor":{"top":420.9966125488281,"left":52.680419921875,"width":269.80068000000006,"hidden":0},"C007_Email_POP_Porta":{"top":420.9794006347656,"left":466.3402099609375,"width":103.80068,"hidden":0},"C007_Email_Assinatura_":{"top":443.9862976074219,"left":24.98284912109375,"height":109.80068,"width":647.80068,"hidden":0}}';
		}

/*		if(($g['divIdRoot'] == 'cad030b') && $acaoId!=""){
			$disabled = true;
		}else{
			$disabled=false;
		}*/
		// novo formulário
		$form = new Formulario('cad030FormCadastroUsuario');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'C007_Flag_Vendedor' => array('name' => 'C007_Flag_Vendedor','label'=>'Tipo','disabled' => $disabled, 'nivel' => 4, 'type' => 'select',
						'select' => array(
							array('title' => 'Normal', 'value' => 'N', 'selected' => true),
							array('title' => 'Vendedor', 'value' => 'V'),
							array('title' => 'Comprador', 'value' => 'C'),
							array('title' => 'Supervisor', 'value' => 'S'),
							array('title' => 'Diretor', 'value' => 'D')
						)
					),
					'C007_Flag_Departamento' => array('name' => 'C007_Flag_Departamento','label'=>'Departamento','disabled' => $disabled, 'nivel' => 4, 'type' => 'select',
						'select' => array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'GERAL', 'value' => 'G'),
							array('title' => 'COMPRAS', 'value' => 'C'),
							array('title' => 'ESTOQUE', 'value' => 'E'),
							array('title' => 'VENDAS', 'value' => 'V'),
							array('title' => 'FINANCEIRO', 'value' => 'F')
						)
					),
					'C007_Ativo' => array('name' => 'C007_Ativo','label'=>'Ativo', 'disabled' => $disabled, 'align'=>'right', 'type' => 'select', 'nivel' => 4,
						'select' => array(
							array('title' => 'Sim', 'value' => 'S', 'selected' => true),
							array('title' => 'Não', 'value' => 'N'),
							)
					),
					'C007_Login' => array('name' => 'C007_Login','label'=>'Login', 'type' => 'text', 'length' => array(33,60), 'uppercase' => false, 'nivel' => 4),
					'C007_Senha' => array('name' => 'C007_Senha','label'=>'Senha', 'type' => 'password', 'length' => array(33,60), 'uppercase' => false, 'nivel' => 4),
					'C007_Primeiro_Nome' => array('name' => 'C007_Primeiro_Nome','label'=>'Primeiro Nome', 'type' => 'text', 'length' => array(33,100), 'nivel' => 4),
					'C007_Nome' => array('name' => 'C007_Nome','label'=>'Nome', 'type' => 'text', 'length' => array(33,60), 'nivel' => 4),
					'C007_DDD_Telefone' => array('name' => 'C007_DDD_Telefone','label'=>'Telefone', 'type' => 'text', 'length' => array(33,60), 'nivel' => 4),
					'C007_Telefone' => array('name' => 'C007_Telefone', 'type' => 'text', 'length' => array(33,60), 'nivel' => 4),
					'C007A_Ramal' => array('name' => 'C007A_Ramal', 'label'=>'Ramal', 'type' => 'text', 'nivel' => 4),
					'C007_Cargo' => array('name' => 'C007_Cargo','label'=>'Cargo', 'type' => 'text', 'length' => array(33,60), 'nivel' => 4),
					'C007_IP_Acesso' => array('name' => 'C007_IP_Acesso','label'=>'Faixa IP Acesso', 'type' => 'text', 'length' => array(50,60), 'nivel' => 4),
					'C007_Acesso_Hora_Inicio' => array('name' => 'C007_Acesso_Hora_Inicio','label'=>'Inicio Acesso', 'type' => 'time', 'length' => array(50,60), 'nivel' => 4),
					'C007_Acesso_Hora_Fim' => array('name' => 'C007_Acesso_Hora_Fim','label'=>'Fim Acesso', 'type' => 'time', 'length' => array(50,60), 'nivel' => 4),
					'C007_Acesso_Dias_Nao_Uteis' => array('name' => 'C007_Acesso_Dias_Nao_Uteis','label'=>'Acesso dias não úteis', 'align'=>'right', 'type' => 'select', 'nivel' => 4,
						'select' => array(
							array('title' => 'Sim', 'value' => 'S', 'selected' => true),
							array('title' => 'Não', 'value' => 'N'),
							)
					),
					'C007_D206_Id' => array('name' => 'C007_D206_Id', 'label' => 'Tabela Feriado', 'type' => 'select','align'=>'left', 'style'=>'float:left', 'select' => 
						gGeraSelect('D206', 'D206_Id', 'D206_descricao','', true)
					),


					'C007_Email' => array('name' => 'C007_Email','label'=>'E-mail', 'type' => 'text', 'length' => array(50,60), 'uppercase' => false, 'nivel' => 4),
					'C007_Email_SMTP_Servidor' => array('name' => 'C007_Email_SMTP_Servidor','label'=>'Servidor SMTP', 'type' => 'text', 'length' => array(50,60), 'uppercase' => false, 'nivel' => 4),
					'C007_Email_SMTP_Porta' => array('name' => 'C007_Email_SMTP_Porta','label'=>'Porta SMTP', 'type' => 'text', 'length' => array(50,60), 'nivel' => 4),
					'C007_Email_SMTP_Autenticacao' => array('name' => 'C007_Email_SMTP_Autenticacao','label'=>'Autenticação', 'type' => 'text', 'length' => array(50,60), 'uppercase' => false, 'nivel' => 4),

					// CAMPOS OBSOLETOS NÃO TEREMOS MAIS INBOX NO SISTEMA 05/04/2023
					//'C007_Email_POP_Servidor' => array('name' => 'C007_Email_POP_Servidor','label'=>'Servidor IMAP', 'type' => 'text', 'length' => array(50,60), 'uppercase' => false, 'nivel' => 4),
					//'C007_Email_POP_Porta' => array('name' => 'C007_Email_POP_Porta','label'=>'Porta IMAP', 'type' => 'numero', 'length' => 6, 'value' => 993, 'nivel' => 4),
					'C007_Email_SMTP_Usuario' => array('name' => 'C007_Email_SMTP_Usuario','label'=>'Usuário', 'type' => 'text', 'length' => array(50,60), 'uppercase' => false, 'nivel' => 4),
					'C007_Email_SMTP_Senha' => array('name' => 'C007_Email_SMTP_Senha','label'=>'Senha', 'type' => 'password', 'length' => array(50,60), 'uppercase' => false, 'nivel' => 4),
					'C007_Data_Alteracao_Senha' => array('name' => 'C007_Data_Alteracao_Senha','label'=>'Senha', 'type' => 'hidden', 'value'=>date('Y-m-d'), 'length' => array(50,60), 'uppercase' => false, 'nivel' => 4),
					'C007_Codigo_Barras' => array('name' => 'C007_Codigo_Barras','label'=>'Codigo de Barras', 'type' => 'text', 'length' => array(50,60), 'nivel' => 4),
					'C007_Cpf' => array('name' => 'C007_Cpf','label'=>'CPF', 'type' => 'text', 'length' => array(33,20), 'nivel' => 4),
					'C007_Cnpj' => array('name' => 'C007_Cnpj','label'=>'CNPJ', 'type' => 'text', 'length' => array(33,20), 'nivel' => 4),
					'C007_Crc' => array('name' => 'C007_Crc','label'=>'CRC', 'type' => 'text', 'length' => array(33,20), 'nivel' => 4),
					'C007_D018_Id' => array('name' => 'C007_D018_Id', 'label' => 'UF',     'type' => 'select','align'=>'left', 'style'=>'float:left', 'extra' => "onchange=\"alteraEstado(this, '{$g['divId']}')\"", 'select' => 
						gGeraSelect('D018', 'D018_Id', 'D018_UF','', true)
					),
					'C007_D020_Id' => array('name' => 'C007_D020_Id', 'label' => 'Cidade', 	'type' => 'select', 'select' => 
						gGeraSelect('D020', 'D020_Id', 'D020_Nome_Cidade', $WHERE_D020, true)
					),
					'C007_Cep' => 	array('name' => 'C007_Cep',	'label' => 'CEP', 'type' => 'cep', 'length' => array(10,15)),
					'buttonSearchCep1' => 	array('name' => 'buttonSearchCep1',	'label' => '', 'type' => 'button', 'extra' => "style='padding: 0; heigth: 20px; width: 50px;' onclick='pesquisaCep(false,\"{$g['divId']}\", \"{$g['divIdRoot']}\",\"C007_Cep\", {\"EnderecoDb\": \"C007_Endereco\", \"Cep\": \"C007_Cep\", \"BairroDb\": \"C007_Bairro\", \"UfDb\": \"C007_D018_Id\",\"CidadeDb\": \"C007_D020_Id\", \"PaisDb\": \"\"})'", 'align'=>'left', 'value'=>'Buscar', 'reject'=>true, 'reload' => false),
					'C007_Bairro' => array('name' => 'C007_Bairro', 'label' => 'Bairro', 'type' => 'text', 'length' => array(25,30)),

					'C007_Endereco' => array('name' => 'C007_Endereco','label'=>'Endereço', 'type' => 'text', 'length' => array(33, 60), 'nivel' => 4),
					'C007_Numero' => array('name' => 'C007_Numero','label'=>'Número', 'type' => 'text', 'length' => array(33,20), 'nivel' => 4),
					'C007_Complemento' => array('name' => 'C007_Complemento','label'=>'Complemento', 'type' => 'text', 'length' => array(33,20), 'nivel' => 4),
					'C007_Flag_Expedicao' => array('name' => 'C007_Flag_Expedicao','label'=>'Expedição', 'type' => 'select', 'nivel' => 4,
						'select' => array(
							array('title' => '...', 'value' => ''),
							array('title' => 'Comercial', 'value' => '1'),
							array('title' => 'Balcão', 'value' => '2'),
						)
					),
					'C007_Id_Vendedor_Substituto' => array('name' => 'C007_Id_Vendedor_Substituto', 'label' => 'Vendedor Substituto', 'type' => 'select', 'nivel' => 4, 
						'select' => gGeraSelect('C007', 'C007_Id', 'C007_Primeiro_Nome', 'where C007_Ativo="S" order by C007_Primeiro_Nome', true)
					),
					'C007_Flag_Ausente' => array('name' => 'C007_Flag_Ausente', 'label'=>'Ausente', 'align'=>'right', 'type' => 'select', 'nivel' => 2,
						'select' => array(
							array('title' => 'Sim', 'value' => 'S', 'selected' => true),
							array('title' => 'Não', 'value' => 'N'),
							)
					),
					'C007_Email_Assinatura_' => array('name' => 'C007_Email_Assinatura_', 'type' => 'textarea', 'label'=>'Assinatura do Email', 'uppercase' => false, 'length' => array(7,65), 'reject' => true, 'nivel' => 4),
					'C007_Email_Assinatura' => array('name' => 'C007_Email_Assinatura', 'type' => 'hidden', 'uppercase' => false, 'nivel' => 4),
					'tituloEmail' => array('name' => 'tituloEmail', 'label'=>'Dados de Email','type' => 'titulo', 'reject' => true, 'nivel' => 4),
					'C007_C007_Id' => array('name' => 'C007_C007_Id', 'label' => 'Supervisor 1', 'type' => 'select', 'nivel' => 4,
						'select' => gGeraSelect('C007', 'C007_Id', 'C007_Primeiro_Nome', 'where C007_Ativo="S" order by C007_Primeiro_Nome', true)
					),
					'C007_C007_Id_2' => array('name' => 'C007_C007_Id_2', 'label' => 'Supervisor 2', 'type' => 'select', 'nivel' => 4,
						'select' => gGeraSelect('C007', 'C007_Id', 'C007_Primeiro_Nome', 'where C007_Ativo="S" order by C007_Primeiro_Nome', true)
					),
					'C007_Dispositivo_Acesso' => array('name' => 'C007_Dispositivo_Acesso', 'label' => 'Acesso dispositivos', 'type' => 'select', 'nivel' => 4,
						'select' => array(
							array('title' => 'TODOS', 'value' => '0'),
							array('title' => 'DESKTOP', 'value' => '1'),
							array('title' => 'MOBILE', 'value' => '2'),
						)
					),
					'C007_Email_SMTP_Seguranca' => array('name' => 'C007_Email_SMTP_Seguranca', 'label' => 'Segurança SMTP', 'type' => 'select',
						'select' => array(
							array('title' => 'Automático', 'value' => '0'),
							array('title' => 'SSL', 'value' => '1'),
							array('title' => 'TLS', 'value' => '2'),
						)
					),
					'C007_IPV_1' => array('name' => 'C007_IPV_1', 'label' => 'IPV Venda', 'type'=>'moeda', 'casasDecimais' => 4, 'nivel' => 4),
				)
			)
		);
		// Em alterações, a senha nunca é exibida nem é reenviada pelo formulário.
		// A redefinição administrativa terá fluxo próprio; a criação já grava hash seguro.
		if (!empty($acaoId)) {
			$senhaTokenAdministrativo = bin2hex(random_bytes(32));
			$_SESSION['senha_admin_troca'][(int) $acaoId] = array('token' => $senhaTokenAdministrativo, 'criado_em' => time());
			$senhaBotaoExtra = "onclick=\"if (typeof hardnessAbrirDialogoSenhaAdministrativo == 'function') { hardnessAbrirDialogoSenhaAdministrativo(" . (int) $acaoId . ", '" . $senhaTokenAdministrativo . "'); } else { dialogConfirm('Aguarde', 'A tela ainda está sendo preparada. Aguarde alguns segundos e tente novamente.'); } return false;\"";
			$formArray['campos']['geral']['C007_Senha'] = array('name' => 'C007_Senha', 'label' => '', 'type' => 'button', 'value' => 'Alterar senha', 'style' => 'width:auto !important; min-width:98px;', 'extra' => $senhaBotaoExtra, 'reject' => true, 'nivel' => 4);
			$formArray['campos']['geral']['C007_Data_Alteracao_Senha']['reject'] = true;
		}
		if ($g['C031']['recebimentoEmail'] == 'S') {
			$formArray['campos']['geral']['C007_Email_POP_Servidor'] = array('name' => 'C007_Email_POP_Servidor','label'=>'Servidor IMAP', 'type' => 'text', 'length' => array(50,60), 'uppercase' => false, 'nivel' => 4);
			$formArray['campos']['geral']['C007_Email_POP_Porta'] = array('name' => 'C007_Email_POP_Porta','label'=>'Porta IMAP', 'type' => 'numero', 'length' => 6, 'value' => 993, 'nivel' => 4);
		}
		$form->definirFormularioArray($formArray);
		isset($r_acaoId) or ($r_acaoId = false);
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var myEditor = new nicEditor({
					iconsPath : '/hardness3/static/img/nicEditorIcons.gif', 
					buttonList: ['fontSize','bold','italic','underline','left','center','right','justify','strikeThrough','subscript','superscript','hr','html','forecolor','upload']
				}).panelInstance('C007_Email_Assinatura_');
				myEditor.addEvent('blur', function() {
					if (typeof(myEditor.instanceById('C007_Email_Assinatura_')) != 'undefined') {
						$('#C007_Email_Assinatura').val(myEditor.instanceById('C007_Email_Assinatura_').getContent());
					}
				});
				setTimeout(function() {
					$('.nicEdit-panelContain').css({'border': '1px solid #4297D7', 'background': 'url("/hardness3/static/img/jquery.ui/ui-bg_gloss-wave_55_5c9ccc_500x100.png") repeat-x scroll 50% 50% #5C9CCC'});
					$('.nicEdit-main').height(70).css('overflow', 'auto');
					if (myEditor.instanceById('C007_Email_Assinatura_') != 'undefined') {
						myEditor.instanceById('C007_Email_Assinatura_').setContent($('#C007_Email_Assinatura').val());
					}
				}, 100);
				$('#{$g['divId']} #cad030TestarEmail').click(function(data) {
					if (data && typeof data.preventDefault == 'function') {
						data.preventDefault();
					}
					$('#{$g['divId']}').showLoading();
					var url = '/cad/cad030/form_func-ajax/testarEmail/?ajax=true&C007_Id=' + encodeURIComponent('{$r_acaoId}');
					if ('{$g['C031']['recebimentoEmail']}' == 'N') {
						var emailDestinoPadrao = $.trim($('#{$g['divId']} #C007_Email').val());
						if (emailDestinoPadrao == '') {
							emailDestinoPadrao = $.trim($('#{$g['divId']} #C007_Email_SMTP_Usuario').val());
						}
						var emailDestino = prompt('Informe o e-mail que deve receber o teste de envio SMTP.', emailDestinoPadrao);
						if (emailDestino === null) {
							$('#{$g['divId']}').hideLoading();
							return false;
						}
						emailDestino = $.trim(emailDestino);
						if (emailDestino == '') {
							$('#{$g['divId']}').hideLoading();
							dialogConfirm('Erro', 'Informe um e-mail de destino para o teste.');
							return false;
						}
						url += '&emailDestino=' + encodeURIComponent(emailDestino);
					}
    				$.getJSON(url + '&callback=?', function(request) {
    					$('#{$g['divId']}').hideLoading();
						if (request.code) {
							if ('{$g['C031']['recebimentoEmail']}' == 'N') {
								dialogConfirm('Ok', request.data);
							} else {
								abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Definir Pastas de E-mail: ' + '{$r_acaoId}', '/cad/cad030/content/cad030ContentPastasEmail/', '&acaoId=' + encodeURIComponent('{$r_acaoId}'), [350,150]);
							}
						} else {
							dialogConfirm('Erro', request.data);
						}
					});
					return false;
				});
				$('#cad030RecriarSenhaBanco').click(function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Recriar Senha Banco - ID: ' + '{$acaoId}', '/cad/cad030/content/cad030ContentRecriarSenha/', '&tab=geral&C007_Id=' + encodeURIComponent('{$acaoId}'), [300,200]);
				});		
		    } </script></div>
EOT;

		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		*/

		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			$form->campoSetarValor('C007_Email', trim($form->campoValorEnviado('C007_Email')), true);
			$form->campoSetarValor('C007_Email_SMTP_Usuario', trim($form->campoValorEnviado('C007_Email_SMTP_Usuario')), true);
			$form->campoSetarValor('C007_Email_SMTP_Senha', trim($form->campoValorEnviado('C007_Email_SMTP_Senha')), true);
			$form->campoSetarValor('C007_Email_SMTP_Servidor', trim($form->campoValorEnviado('C007_Email_SMTP_Servidor')), true);
			$form->campoSetarValor('C007_Email_POP_Servidor', trim($form->campoValorEnviado('C007_Email_POP_Servidor')), true);

			// Form com erros
			if ($formCodigo == 1) {
				echo gGeraAlertMsgErroForm($form);
				// Form sem erros
			} else if ($formCodigo == 2) {
				if (empty($acaoId)) {
					$errosSenha = SegurancaSenha::validarNovaSenha($form->campoValorEnviado('C007_Senha'));
					if (!empty($errosSenha)) {
						echo gGeraAlertMsg('Erro', implode('<br>', $errosSenha), false);
						break;
					}
					$form->campoSetarValor('C007_Senha', SegurancaSenha::gerarHash($form->campoValorEnviado('C007_Senha')), true);
				}
				$C007_Login = $form->campoValorEnviado('C007_Login');
				$sqlC007 = mysql_query("SELECT CONCAT(C007_Id,' - ',C007_Primeiro_Nome) as usuario FROM C007 WHERE C007_Login='{$C007_Login}' AND C007_Id!='$acaoId' AND C007_Ativo='S'");
				
				if(mysql_num_rows($sqlC007)>0){
					$C007_Usuario = array();
					while($C007 = mysql_fetch_array($sqlC007)){
						$C007_Usuario[]=$C007['usuario'];
					}
					$usuarios= implode("<br/>", $C007_Usuario);
					$alert = gGeraAlertMsg('Erro', "Esse login já está sendo utilizado pelos usuários abaixo:<br/>{$usuarios}", false);
					echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								setTimeout(function() {
									{$alert}
								}, 150);
							divRefresh('{$g['divIdParent']}', true);
						} </script></div>
EOT;
				}else{
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
							$acaoId = $g['mysqlLastId'];
							$alert = gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
							echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Editar Cadastro - ID: ' + '{$acaoId}', '/cad/cad030/content/cad030contentCadastroUsuario/', '&tab=geral&acaoId=' + encodeURIComponent('{$acaoId}'), [800,600]);
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
			}
		} else {
			// Carrega os valores caso seja um update
			if (!empty($acaoId)) {
				$processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $acaoId);
				$form->campoSetarValor('C007_Email_Assinatura', htmlentities($form->campoValor('C007_Email_Assinatura'), ENT_COMPAT, 'UTF-8'));
				$form->campoSetarValor('C007_Email', trim($form->campoValor('C007_Email')));
				$form->campoSetarValor('C007_Email_SMTP_Usuario', trim($form->campoValor('C007_Email_SMTP_Usuario')));
				$form->campoSetarValor('C007_Email_SMTP_Senha', trim($form->campoValor('C007_Email_SMTP_Senha')));
				$form->campoSetarValor('C007_Email_SMTP_Servidor', trim($form->campoValor('C007_Email_SMTP_Servidor')));
				$form->campoSetarValor('C007_Email_POP_Servidor', trim($form->campoValor('C007_Email_POP_Servidor')));
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
		$botoesDir = $botoesEsq = '';
		$botoesDir .= '<button type="submit" id="cad030FormCadastroUsuario_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		if (isset($r_acaoId) && !empty($r_acaoId)) {
			$botoesEsq .= '<button id="cad030TestarEmail">Testar E-mail</button>';
		}
		if ($g['usuarioAtual'] == 63) {
			$botoesEsq .= '<button id="cad030RecriarSenhaBanco">Recriar Senha Banco</button>';	
		}
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'cad030FormPerfilUsuario':
		/**
		* PARTE 1: Variavéis envidas para o formulário
		*/
		// Variável indica qual tab do form será exibida
		$tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
		// Utilizada em UPDATE, é o ID do registro que está sendo editado
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$C007_Id = (isset($r_cad030FormPerfilUsuario)) ? $r_cad030FormPerfilUsuario['C007_Id'] : $r_C007_Id;

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
		$form_tabela = 'C019';
		$form_campo_pk = 'C019_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('cad030FormPerfilUsuarioAdd', 'cad030FormPerfilUsuarioCln', 'cad030FormPerfilUsuarioNew');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad030/form/cad030FormPerfilUsuario/';

		// novo formulário
		$form = new Formulario('cad030FormPerfilUsuario');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
		'campos' => array(
			'geral' => array(
				'C019_C007_Id' => array('name' => 'C019_C007_Id','label'=>'Usuário','extra' =>'style="width:200px"','disabled' => true, 'value' => $C007_Id, 'type' => 'select',
					'select' => gGeraSelect('C007','C007_Id','C007_Primeiro_Nome,C007_Id', "WHERE C007_Id = '{$C007_Id}'")
					),
				'C019_C004_Id' => array('name' => 'C019_C004_Id','label'=>'Empresa','extra' =>'style="width:200px"', 'type' => 'select',
					'select' => gGeraSelect('C004','C004_Id','C004_Nome_Abreviado,C004_Id',"where C004_Flag_Ativo='S'",true)
					),
				'C019_C021_Id' => array('name' => 'C019_C021_Id','label'=>'Perfil','extra' =>'style="width:200px"', 'type' => 'select',
					'select' => gGeraSelect('C021','C021_Id','C021_Nome_Perfil,C021_Id','',true)
					),
				'divReload' => array('name' => 'divReload', 'label' => '', 'type' => 'hidden', 'value' => @$r_divReload, 'reject' => true),
				'C007_Id' => array('name' => 'C007_Id', 'label' => '', 'type' => 'hidden', 'value' => $C007_Id, 'reject' => true),
				)
			)
		);
		// Botão Limpar / Novo Registro
		if (!empty($acaoId)) {
			$formArray['campos']['geral']['cad030FormPerfilUsuarioNew'] = array('name' => 'cad030FormPerfilUsuarioNew', 'type'=>'submit', 'reload' => false, 'value' => 'Novo Registro', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		}
		$formArray['campos']['geral']['cad030FormPerfilUsuarioCln'] = array('name' => 'cad030FormPerfilUsuarioCln', 'type'=>'button', 'reload' => false, 'value' => 'Limpar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$formArray['campos']['geral']['cad030FormPerfilUsuarioAdd'] = array('name' => 'cad030FormPerfilUsuarioAdd', 'type'=>'button', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		$form->definirFormularioArray($formArray);

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
					$retorno = $CAD030->verificaDuplicidade($form);  
					if ($retorno!==true){							
						echo gGeraAlertMsg('Erro', $retorno);
					} else {																				
						if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
							echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								divRefresh('{$form->campoValorEnviado('divReload')}', true);
								divRefresh('{$g['divId']}', true);
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
		$botoesDir = '';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'cad030FormUsuariosApi':
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
		$form_tabela = 'D178';
		$form_campo_pk = 'D178_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('cad030FormUsuariosApiBtnAdd', 'cad030FormUsuariosApiBtnNew');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad030/form/cad030FormUsuariosApi/';

		// Editor
		$form_editor = array(
			'geral' => '{"D178_C004_Id":{"top":145,"left":29.859375,"width":623,"hidden":0},"D178_Nome_Usuario":{"top":7,"left":37.203125,"width":616,"hidden":0},"D178_Permissao_Estoque_Disponibilidade":{"top":214,"left":177.40625,"width":64,"hidden":0},"D178_Key":{"top":30,"left":47.59375,"width":616,"hidden":0},"D178_Permissao_Estoque_Quantidade":{"top":214,"left":387.75,"width":64,"hidden":0},"D178_Token":{"top":53,"left":37.1875,"width":616,"hidden":0},"D178_Descricao_Usuario":{"top":76,"left":24.96875,"height":62,"width":616,"hidden":0},"D178_Flag_Ativo":{"top":168,"left":49.421875,"width":128,"hidden":0},"tituloPermissoes":{"top":189,"left":298,"width":null,"hidden":1},"D178_Permissao_Produto":{"top":214,"left":29.859375,"width":64,"hidden":0},"cad030FormUsuariosApiBtnAdd":{"top":258,"left":608,"width":null,"hidden":0}}'
		);

		// Novo Formulário
		$form = new Formulario('cad030FormUsuariosApi');

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D178_Key' => array('name' => 'D178_Key','label'=>'Key', 'type' => 'text', 'length' => array(60,60)),
					'D178_Token' => array('name' => 'D178_Token','label'=>'Token', 'type' => 'text', 'length' => array(60,60)),
					'D178_Descricao_Usuario' => array('name' => 'D178_Descricao_Usuario', 'type' => 'textarea', 'label'=>'Descrição'),
					'D178_Nome_Usuario' => array('name' => 'D178_Nome_Usuario', 'type' => 'text','label'=>'Nome', 'length' => array(60,60)),
					'D178_Flag_Ativo' => array('name' => 'D178_Flag_Ativo', 'label'=>'Ativo', 'type' => 'select','select' => 
						array(
							array('title' => 'SIM', 'value' => 'S'),
							array('title' => 'NÃO', 'value' => 'N'),	
						)
					),
					'D178_C004_Id' => array('name' => 'D178_C004_Id','label'=>'Empresa','extra' =>'style="width:200px"', 'type' => 'select',
						'select' => gGeraSelect('C004','C004_Id','C004_Nome_Abreviado,C004_Id',"where C004_Flag_Ativo='S'",true)
					),
					'tituloPermissoes' => array('name' => 'tituloPermissoes', 'label'=>'Permissões','type' => 'titulo', 'reject' => true),
					'D178_Permissao_Produto' => array('name' => 'D178_Permissao_Produto', 'label'=>'Produtos', 'type' => 'select','select' => 
						array(
							array('title' => 'NÃO', 'value' => 'N'),
							array('title' => 'SIM', 'value' => 'S'),
						)
					),
					'D178_Permissao_Estoque_Disponibilidade' => array('name' => 'D178_Permissao_Estoque_Disponibilidade', 'label'=>'Estoque(disponibilidade)', 'type' => 'select','select' => 
						array(
							array('title' => 'NÃO', 'value' => 'N'),
							array('title' => 'SIM', 'value' => 'S'),
						)
					),
					'D178_Permissao_Estoque_Quantidade' => array('name' => 'D178_Permissao_Estoque_Quantidade', 'label'=>'Estoque(quantidade)', 'type' => 'select','select' => 
						array(
							array('title' => 'NÃO', 'value' => 'N'),
							array('title' => 'SIM', 'value' => 'S'),
						)
					),
				),
			)
		);

		$formArray['campos']['geral']['cad030FormUsuariosApiBtnAdd'] = array('name' => 'cad030FormUsuariosApiBtnAdd', 'type'=>'button', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		
		$form->definirFormularioArray($formArray);

		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		*/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				$g['conteudo']['pagina'] = gGeraAlertMsgErroForm($form);
				// Form sem erros
			} else if ($formCodigo == 2) {
				// Insert ou Update?
				if (!empty($acaoId)) {
					// Update
					$form->campoSetarValor('D024_Cep', str_replace('-', '', $form->campoValorEnviado('D024_Cep')), true);
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
						$acaoId = $g['mysqlLastId'];
						//die($AbrirEdicaoClienteAposInsercao);
						$alert = gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
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
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		echo  $g['smarty']->fetch('formEditor.tpl');

		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo  $g['smarty']->fetch('buttons-floatBottom.tpl');
		// Ações dos botões
		// A ação do botão Enviar é definido dentro do formulário

	break;

	case 'cad030FormUsuariosB2B':
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
		$form_tabela = 'D179';
		$form_campo_pk = 'D179_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = array('cad030FormUsuariosB2BBtnAdd');
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad030/form/cad030FormUsuariosB2B/';

		// Editor
		$form_editor = array(
			'geral' => '{"D179_C007_Vendedor_Externo_Id":{"top":99,"left":33.8125,"width":287,"hidden":0},"D179_Login":{"top":7,"left":87.03125,"width":376,"hidden":0},"D179_Senha":{"top":30,"left":82.734375,"width":152,"hidden":0},"D179_D021_Id":{"top":53,"left":49.71875,"width":287,"hidden":0},"D179_C007_Id":{"top":76,"left":37.484375,"width":287,"hidden":0},"cad030FormUsuariosB2BBtnAdd":{"top":143,"left":400,"width":null,"hidden":0}}'
		);

		// Novo Formulário
		$form = new Formulario('cad030FormUsuariosB2B');

		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D179_Login' => array('name' => 'D179_Login','label'=>'Email', 'type' => 'text', 'length' => array(60,60)),
					'D179_Senha' => array('name' => 'D179_Senha','label'=>'Senha','type' => 'password', 'uppercase' => false, 'length' => array(60,60)),
					'D179_C007_Id' => array('name' => 'D179_C007_Id', 'label' => 'Vendedor Interno', 'type' => 'select',
						'select' => gGeraSelect('C007', 'C007_Id', 'C007_Primeiro_Nome', 'where C007_Ativo="S" order by C007_Primeiro_Nome', true)
					),
					'D179_C007_Vendedor_Externo_Id' => array('name' => 'D179_C007_Vendedor_Externo_Id', 'label' => 'Vendedor Externo', 'type' => 'select',
						'select' => gGeraSelect('C007', 'C007_Id', 'C007_Primeiro_Nome', 'where C007_Ativo="S" order by C007_Primeiro_Nome', true)
					),
					'D179_D021_Id' => array('name' => 'D179_D021_Id', 'label' => 'Representante', 'type' => 'select',
						'select' => gGeraSelect('D021', 'D021_Id', 'D021_Nome_Empresa', 'order by D021_Nome_Empresa', true)
					),
				),
			)
		);

		$formArray['campos']['geral']['cad030FormUsuariosB2BBtnAdd'] = array('name' => 'cad030FormUsuariosB2BBtnAdd', 'type'=>'button', 'reload' => false, 'value' => empty($acaoId) ? 'Incluir' : 'Alterar', 'align' => 'right', 'style' => 'float:right;', 'reject' => true);
		
		$form->definirFormularioArray($formArray);

		/**
		* PARTE 3: Processamento do formulário (se foi enviado)
		* 		   E carregamento dos valores (se não foi enviado e possui acaoId)
		*/
		if ($formCodigo = $form->processarFormulario($form_tabela)) {
			// Form com erros
			if ($formCodigo == 1) {
				$g['conteudo']['pagina'] = gGeraAlertMsgErroForm($form);
				// Form sem erros
			} else if ($formCodigo == 2) {

				$D179_D021_Id = $form->campoValorEnviado('D179_D021_Id');
				$D179_C007_Id = $form->campoValorEnviado('D179_C007_Id');
				$D179_C007_Vendedor_Externo_Id = $form->campoValorEnviado('D179_C007_Vendedor_Externo_Id');
				
				if(($D179_D021_Id>0) && ($D179_C007_Id<=0)){
						$alert = gGeraAlertMsg('Erro', 'Para cadastrar um login vinculado a um representante é necessário vincular um vendedor interno.', false);
							echo <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									{$alert}
								} </script></div>
EOT;
				}else if(($D179_C007_Vendedor_Externo_Id>0) && ($D179_C007_Id>0)){
					$alert = gGeraAlertMsg('Erro', 'Só é permitido vincular um tipo de vendedor ao login.', false);
							echo <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									{$alert}
								} </script></div>
EOT;
				}else{
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
							$acaoId = $g['mysqlLastId'];
							//die($AbrirEdicaoClienteAposInsercao);
							$alert = gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
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
		echo  $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		echo  $g['smarty']->fetch('formEditor.tpl');

		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = '';
		$botoesDir = '';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		echo  $g['smarty']->fetch('buttons-floatBottom.tpl');
		// Ações dos botões
		// A ação do botão Enviar é definido dentro do formulário

	break;
		
	case 'cad030FormPastasEmail':
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
		
		// Pastas
		$sql = "SELECT C007_Email_Pastas FROM C007 WHERE C007_Id = '{$acaoId}';";
		$resultado = mysql_query($sql);
		$C007 = mysql_fetch_assoc($resultado);
		$C007['C007_Email_Pastas'] = unserialize($C007['C007_Email_Pastas']);

		// A qual tabela este form está relacionada, e a chave primária da tabela
		$form_tabela = 'C007';
		$form_campo_pk = 'C007_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad030FormPastasEmail_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad030/form/cad030FormPastasEmail/';

		// novo formulário
		$form = new Formulario('cad030FormPastasEmail');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
		'campos' => array(
			'geral' => array(
				'C007_Email_Pasta_Entrada' => array('name' => 'C007_Email_Pasta_Entrada','label'=>'Pasta de Entrada', 'uppercase' => false, 'type' => 'select',
					'select' => $C007['C007_Email_Pastas']
				),
				'C007_Email_Pasta_Saida' => array('name' => 'C007_Email_Pasta_Saida','label'=>'Pasta de Saída', 'uppercase' => false, 'type' => 'select',
					'select' => $C007['C007_Email_Pastas']
				),
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
						$alert = gGeraAlertMsg('Operação OK', 'O seu e-mail está configurado e pronto para uso!', false);
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
		$botoesDir = $botoesEsq = '';
		$botoesDir .= '<button type="submit" id="cad030FormPastasEmail_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;	

	case 'cad030FormCadastroUsuarioSimples':
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
		$form_tabela = 'C007';
		$form_campo_pk = 'C007_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad030FormCadastroUsuario_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad030/form/cad030FormCadastroUsuarioSimples/';
		$form_editor = array(
			'geral' => '{"C007_Login":{"top":6.969970703125,"left":79.48835802078247,"width":104.03921600000001,"hidden":0},"C007_Senha":{"top":4.993873596191406,"left":224.00428819656372,"width":null,"hidden":0},"C007_Nome":{"top":29.993881225585938,"left":69.56189012527466,"width":488.039216,"hidden":0},"C007_Endereco":{"top":52.98713684082031,"left":60.78431749343872,"width":488.039216,"hidden":0},"C007_Numero":{"top":75.99571228027344,"left":67.3406834602356,"width":72.03921600000001,"hidden":0},"C007_Complemento":{"top":75.99571228027344,"left":213.57229661941528,"width":312.039216,"hidden":0},"C007_Cpf":{"top":98.97364807128906,"left":78.96752214431763,"width":185.039216,"hidden":0},"C007_Cnpj":{"top":98.97364807128906,"left":328.21691942214966,"width":232.039216,"hidden":0},"C007_DDD_Telefone":{"top":121.99755859375,"left":64.26164293289185,"width":24.039216000000003,"hidden":0},"C007_Telefone":{"top":121.99755859375,"left":143.9951024055481,"width":88.03921600000001,"hidden":0},"tituloEmail":{"top":163.9705810546875,"left":262.99020433425903,"width":null,"hidden":0},"C007_Email":{"top":190.99264526367188,"left":79.38112688064575,"width":200.039216,"hidden":0},"C007_Email_SMTP_Servidor":{"top":190.99264526367188,"left":338.05148363113403,"width":184.039216,"hidden":0},"C007_Email_SMTP_Usuario":{"top":213.9705810546875,"left":69.10232591629028,"width":200.039216,"hidden":0},"C007_Email_SMTP_Porta":{"top":213.9705810546875,"left":351.71569871902466,"width":56.039215999999996,"hidden":0},"C007_Email_SMTP_Autenticacao":{"top":213.9705810546875,"left":489.93566942214966,"width":40.039215999999996,"hidden":0},"C007_Email_SMTP_Senha":{"top":236.97915649414062,"left":76.13357591629028,"width":200.039216,"hidden":0},"C007_Email_SMTP_Seguranca":{"top":236.97915649414062,"left":326.5471806526184,"width":191,"hidden":0},"C007_Email_Assinatura_":{"top":282.996337890625,"left":9.09926462173462,"height":112.03921600000001,"width":488.039216,"hidden":0}}'
		);

		if ($g['C031']['recebimentoEmail'] == 'S') {
			$form_editor['geral'] = '{"C007_Login":{"top":6.993133544921875,"left":79.46734619140625,"width":103.80068,"hidden":0},"C007_Senha":{"top":6.993133544921875,"left":268.1099853515625,"width":295.80068000000006,"hidden":0},"C007_Nome":{"top":29.982818603515625,"left":69.53607177734375,"width":487.80068000000006,"hidden":0},"C007_Endereco":{"top":52.98968505859375,"left":60.75604248046875,"width":487.80068000000006,"hidden":0},"C007_Numero":{"top":75.99658203125,"left":67.319580078125,"width":71.80068,"hidden":0},"C007_Complemento":{"top":75.99658203125,"left":213.5223388671875,"width":311.80068000000006,"hidden":0},"C007_Cpf":{"top":98.98626708984375,"left":78.951904296875,"width":184.80068,"hidden":0},"C007_Cnpj":{"top":98.98626708984375,"left":328.19586181640625,"width":231.80068,"hidden":0},"C007_DDD_Telefone":{"top":121.99313354492188,"left":64.226806640625,"width":23.80068,"hidden":0},"C007_Telefone":{"top":121.99313354492188,"left":143.98626708984375,"width":87.80068,"hidden":0},"tituloEmail":{"top":163.98626708984375,"left":262.98974609375,"width":null,"hidden":0},"C007_Email":{"top":190.99658203125,"left":79.3642578125,"width":199.80068,"hidden":0},"C007_Email_SMTP_Servidor":{"top":190.99658203125,"left":338.00689697265625,"width":183.80068,"hidden":0},"C007_Email_SMTP_Usuario":{"top":213.98626708984375,"left":69.07220458984375,"width":199.80068,"hidden":0},"C007_Email_SMTP_Porta":{"top":213.98626708984375,"left":351.68389892578125,"width":55.80068,"hidden":0},"C007_Email_SMTP_Autenticacao":{"top":213.98626708984375,"left":489.87982177734375,"width":39.80068,"hidden":0},"C007_Email_SMTP_Senha":{"top":236.99313354492188,"left":76.1168212890625,"width":199.80068,"hidden":0},"C007_Email_SMTP_Seguranca":{"top":236.99313354492188,"left":326.49493408203125,"width":191,"hidden":0},"C007_Email_POP_Servidor":{"top":259.9828186035156,"left":36.683837890625,"width":199.80068,"hidden":0},"C007_Email_POP_Porta":{"top":259.9828186035156,"left":354.34710693359375,"width":55.80068,"hidden":0},"C007_Email_Assinatura_":{"top":282.9897155761719,"left":9.0721435546875,"height":111.80068,"width":487.80068000000006,"hidden":0}}';
		}
		
		if(($g['divIdRoot'] == 'cad030b') && $acaoId!=""){
			$disabled = true;
		}else{
			$disabled=false;
		}
		// novo formulário
		$form = new Formulario('cad030FormCadastroUsuarioSimples');
		$senhaBotaoMinhaSenha = "onclick=\"if (typeof hardnessAbrirDialogoSenha == 'function') { hardnessAbrirDialogoSenha(); } else { dialogConfirm('Aguarde', 'A tela ainda está sendo preparada. Aguarde alguns segundos e tente novamente.'); } return false;\"";
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'C007_Login' => array('name' => 'C007_Login','label'=>'Login', 'type' => 'text', 'length' => array(33,60), 'uppercase' => false, 'disabled' => true),
					'C007_Senha' => array('name' => 'C007_Senha', 'label' => '', 'type' => 'button', 'value' => 'Alterar senha', 'style' => 'width:auto !important; min-width:98px;', 'extra' => $senhaBotaoMinhaSenha, 'reject' => true),
					'C007_Nome' => array('name' => 'C007_Nome','label'=>'Nome', 'type' => 'text', 'length' => array(33,60)),
					'C007_DDD_Telefone' => array('name' => 'C007_DDD_Telefone','label'=>'Telefone', 'type' => 'text', 'length' => array(33,60)),
					'C007_Telefone' => array('name' => 'C007_Telefone', 'type' => 'text', 'length' => array(33,60)),
					//'C007_Email' => array('name' => 'C007_Email','label'=>'E-mail', 'type' => 'text', 'length' => array(50,60), 'uppercase' => false),
					'C007_Email_SMTP_Servidor' => array('name' => 'C007_Email_SMTP_Servidor','label'=>'Servidor SMTP', 'type' => 'text', 'length' => array(50,60), 'uppercase' => false),
					'C007_Email_SMTP_Porta' => array('name' => 'C007_Email_SMTP_Porta','label'=>'Porta SMTP', 'type' => 'text', 'length' => array(50,60)),
					'C007_Email_SMTP_Autenticacao' => array('name' => 'C007_Email_SMTP_Autenticacao','label'=>'Autenticação', 'type' => 'text', 'length' => array(50,60), 'uppercase' => false),
					'C007_Email' => array('name' => 'C007_Email','label'=>'Email', 'type' => 'text', 'length' => array(50,60), 'uppercase' => false),
					'C007_Email_SMTP_Usuario' => array('name' => 'C007_Email_SMTP_Usuario','label'=>'Usuário', 'type' => 'text', 'length' => array(50,60), 'uppercase' => false),
					'C007_Email_SMTP_Senha' => array('name' => 'C007_Email_SMTP_Senha','label'=>'Senha', 'type' => 'text', 'length' => array(50,60), 'uppercase' => false),
					'C007_Data_Alteracao_Senha' => array('name' => 'C007_Data_Alteracao_Senha','label'=>'Senha', 'type' => 'hidden', 'value'=>date('Y-m-d'), 'length' => array(50,60), 'uppercase' => false, 'reject' => true),
					'C007_Cpf' => array('name' => 'C007_Cpf','label'=>'CPF', 'type' => 'text', 'length' => array(33,20)),
					'C007_Cnpj' => array('name' => 'C007_Cnpj','label'=>'CNPJ', 'type' => 'text', 'length' => array(33,20)),
					'C007_Endereco' => array('name' => 'C007_Endereco','label'=>'Endereço', 'type' => 'text', 'length' => array(33, 60)),
					'C007_Numero' => array('name' => 'C007_Numero','label'=>'Número', 'type' => 'text', 'length' => array(33,20)),
					'C007_Complemento' => array('name' => 'C007_Complemento','label'=>'Complemento', 'type' => 'text', 'length' => array(33,20)),
					'C007_Email_Assinatura_' => array('name' => 'C007_Email_Assinatura_', 'type' => 'textarea', 'label'=>'Assinatura do Email', 'uppercase' => false, 'length' => array(7,65), 'reject' => true),
					'C007_Email_Assinatura' => array('name' => 'C007_Email_Assinatura', 'type' => 'hidden', 'uppercase' => false),
					'tituloEmail' => array('name' => 'tituloEmail', 'label'=>'Dados de Email','type' => 'titulo', 'reject' => true),
					'C007_Email_SMTP_Seguranca' => array('name' => 'C007_Email_SMTP_Seguranca', 'label' => 'Segurança SMTP', 'type' => 'select',
						'select' => array(
							array('title' => 'Automático', 'value' => '0'),
							array('title' => 'SSL', 'value' => '1'),
							array('title' => 'TLS', 'value' => '2'),
						)
					),
					// CAMPOS OBSOLETOS NÃO TEREMOS MAIS INBOX NO SISTEMA 05/04/2023
					//'C007_Email_POP_Servidor' => array('name' => 'C007_Email_POP_Servidor','label'=>'Servidor IMAP', 'type' => 'text', 'length' => array(50,60), 'uppercase' => false),
					//'C007_Email_POP_Porta' => array('name' => 'C007_Email_POP_Porta','label'=>'Porta IMAP', 'type' => 'numero', 'length' => 6, 'value' => 993),
				)
			)
		);
		if ($g['C031']['recebimentoEmail'] == 'S') {
			$formArray['campos']['geral']['C007_Email_POP_Servidor'] = array('name' => 'C007_Email_POP_Servidor','label'=>'Servidor IMAP', 'type' => 'text', 'length' => array(50,60), 'uppercase' => false);
			$formArray['campos']['geral']['C007_Email_POP_Porta'] = array('name' => 'C007_Email_POP_Porta','label'=>'Porta IMAP', 'type' => 'numero', 'length' => 6, 'value' => 993);
		}
		$form->definirFormularioArray($formArray);
		
		isset($r_acaoId) or ($r_acaoId = false);
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var myEditor = new nicEditor({
					iconsPath : '/hardness3/static/img/nicEditorIcons.gif', 
					buttonList: ['fontSize','bold','italic','underline','left','center','right','justify','strikeThrough','subscript','superscript','hr','html','forecolor','upload']
				}).panelInstance('C007_Email_Assinatura_');
				myEditor.addEvent('blur', function() {
					if (typeof(myEditor.instanceById('C007_Email_Assinatura_')) != 'undefined') {
						$('#C007_Email_Assinatura').val(myEditor.instanceById('C007_Email_Assinatura_').getContent());
					}
				});
				setTimeout(function() {
					$('.nicEdit-panelContain').css({'border': '1px solid #4297D7', 'background': 'url("/hardness3/static/img/jquery.ui/ui-bg_gloss-wave_55_5c9ccc_500x100.png") repeat-x scroll 50% 50% #5C9CCC'});
					$('.nicEdit-main').height(70).css('overflow', 'auto');
					if (myEditor.instanceById('C007_Email_Assinatura_') != 'undefined') {
						myEditor.instanceById('C007_Email_Assinatura_').setContent($('#C007_Email_Assinatura').val());
					}
				}, 100);
				$('#{$g['divId']} #cad030TestarEmail').click(function(data) {
					if (data && typeof data.preventDefault == 'function') {
						data.preventDefault();
					}
					$('#{$g['divId']}').showLoading();
					var url = '/cad/cad030/form_func-ajax/testarEmail/?ajax=true&C007_Id=' + encodeURIComponent('{$r_acaoId}');
					if ('{$g['C031']['recebimentoEmail']}' == 'N') {
						var emailDestinoPadrao = $.trim($('#{$g['divId']} #C007_Email').val());
						if (emailDestinoPadrao == '') {
							emailDestinoPadrao = $.trim($('#{$g['divId']} #C007_Email_SMTP_Usuario').val());
						}
						var emailDestino = prompt('Informe o e-mail que deve receber o teste de envio SMTP.', emailDestinoPadrao);
						if (emailDestino === null) {
							$('#{$g['divId']}').hideLoading();
							return false;
						}
						emailDestino = $.trim(emailDestino);
						if (emailDestino == '') {
							$('#{$g['divId']}').hideLoading();
							dialogConfirm('Erro', 'Informe um e-mail de destino para o teste.');
							return false;
						}
						url += '&emailDestino=' + encodeURIComponent(emailDestino);
					}
    				$.getJSON(url + '&callback=?', function(request) {
    					$('#{$g['divId']}').hideLoading();
						if (request.code) {
							if ('{$g['C031']['recebimentoEmail']}' == 'N') {
								dialogConfirm('Ok', request.data);
							} else {
								abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Definir Pastas de E-mail: ' + '{$r_acaoId}', '/cad/cad030/content/cad030ContentPastasEmail/', '&acaoId=' + encodeURIComponent('{$r_acaoId}'), [350,150]);
							}
						} else {
							dialogConfirm('Erro', request.data);
						}
					});
					return false;
				});
				$('#{$g['divId']} #cad030AlterarMinhaSenha').click(function(data) {
					if (data && typeof data.preventDefault == 'function') data.preventDefault();
					if (typeof hardnessAbrirDialogoSenha == 'function') {
						hardnessAbrirDialogoSenha();
					} else {
						dialogConfirm('Aguarde', 'A tela ainda está sendo preparada. Aguarde alguns segundos e tente novamente.');
					}
					return false;
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
						$acaoId = $g['mysqlLastId'];
						$alert = gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						echo <<<EOT
						<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
							$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
								setTimeout(function() {
									abrirJanela(data, '{$g['divIdRoot']}', '{$g['divIdParent']}', unique(), '', 'Editar Cadastro - ID: ' + '{$acaoId}', '/cad/cad030/content/cad030contentCadastroUsuario/', '&tab=geral&acaoId=' + encodeURIComponent('{$acaoId}'), [500,510]);
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
				$form->campoSetarValor('C007_Email_Assinatura', htmlentities($form->campoValor('C007_Email_Assinatura'), ENT_COMPAT, 'UTF-8'));
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
		$botoesDir = $botoesEsq = '';
		$botoesDir .= '<button type="submit" id="cad030FormCadastroUsuario_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		if (isset($r_acaoId) && !empty($r_acaoId)) {
			$botoesEsq .= '<button id="cad030TestarEmail">Testar E-mail</button>';
		}
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'cad030FormParametrosUsuario':
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
		$form_tabela = 'C007';
		$form_campo_pk = 'C007_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad030FormParametrosUsuario_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad030/form/cad030FormParametrosUsuario/';

		$form_editor = array(
			'geral' => '{"observacaoOC":{"top":4,"left":632,"width":null,"hidden":1},"C007_Programacao_Vendas":{"top":7,"left":2.484375,"height":200,"width":254,"hidden":0},"C007_Alcada_OC_Empresa_Grupo":{"top":30,"left":442.84375,"width":96,"hidden":0},"C007_Valor_Maximo_OC":{"top":53,"left":542.8125,"width":87,"hidden":0},"C007_Valor_Maximo_OC_Dia":{"top":76,"left":555.53125,"width":87,"hidden":0},"C007_Valor_Maximo_OC_Mes":{"top":99,"left":549.25,"width":87,"hidden":0},"C007_Valor_Maximo_OC_Fornecedor":{"top":122,"left":477.828125,"width":87,"hidden":0},"observacaoPO":{"top":165,"left":603,"width":null,"hidden":1},"C007_Valor_Maximo_PO":{"top":191,"left":614.6875,"width":89,"hidden":0},"C007_Cota_Vendas_Diaria":{"top":214,"left":14.625,"width":136,"hidden":0},"C007_Valor_Maximo_PO_Dia":{"top":214,"left":555.53125,"width":89,"hidden":0},"C007_Cota_Vendas_Mensal":{"top":237,"left":7.046875,"width":136,"hidden":0},"C007_Valor_Maximo_PO_Mes":{"top":237,"left":549.25,"width":89,"hidden":0},"C007_Percentual_Comissao":{"top":260,"left":18.25,"width":136,"hidden":0},"C007_Valor_Maximo_PO_Fornecedor":{"top":260,"left":477.828125,"width":89,"hidden":0},"C007_Nome_Vendedor_Ecommerce":{"top":283,"left":29.078125,"width":248,"hidden":0},"C007_Usuario_Acessos":{"top":306,"left":452.421875,"width":55,"hidden":0},"C007_Flag_Tipo_Etiqueta_Transporte":{"top":306,"left":2.203125,"width":255,"hidden":0},"C007_D007_Id":{"top":329,"left":54.109375,"width":255,"hidden":0}}'
		);

		// novo formulário
		$form = new Formulario('cad030FormParametrosUsuario');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'C007_Programacao_Vendas' => array('name' => 'C007_Programacao_Vendas', 'label'=>'Programação de Vendas', 'type' => 'textarea', 'nivel' => 4),
					'C007_Cota_Vendas_Mensal' => array('name' => 'C007_Cota_Vendas_Mensal','label'=>'Cota de Vendas Mensal', 'type' => 'moeda', 'nivel' => 4),
					'C007_Cota_Vendas_Diaria' => array('name' => 'C007_Cota_Vendas_Diaria','label'=>'Cota de Vendas Diaria', 'type' => 'moeda', 'nivel' => 4),
					'C007_Valor_Maximo_OC' => array('name' => 'C007_Valor_Maximo_OC','label'=>'Valor máximo da ordem de compra', 'type' => 'moeda', 'nivel' => 4),
					'C007_Valor_Maximo_OC_Dia' => array('name' => 'C007_Valor_Maximo_OC_Dia','label'=>'Valor máximo de compra por dia', 'type' => 'moeda', 'nivel' => 4),
					'C007_Valor_Maximo_OC_Mes' => array('name' => 'C007_Valor_Maximo_OC_Mes','label'=>'Valor máximo de compra por mês', 'type' => 'moeda', 'nivel' => 4),
					'C007_Valor_Maximo_OC_Fornecedor' => array('name' => 'C007_Valor_Maximo_OC_Fornecedor','label'=>'Valor máximo de compra do mês por fornecedor', 'type' => 'moeda', 'nivel' => 4),
					'C007_Percentual_Comissao' => array('name' => 'C007_Percentual_Comissao','label' => 'Percentual Comissão', 'type' => 'moeda', 'nivel' => 4),
					'C007_Alcada_OC_Empresa_Grupo' => array('name' => 'C007_Alcada_OC_Empresa_Grupo','label' => 'Verifica alçada ordem de compra por empresa ou grupo', 'nivel' => 4 , 'type' => 'select', 
						'select' => array(
							array('title' => 'EMPRESA', 'value' => 'E'),
							array('title' => 'GRUPO', 'value' => 'G', 'selected' => true)
						)
					),
					'C007_Valor_Maximo_PO' => array('name' => 'C007_Valor_Maximo_PO','label'=>'Valor máximo da PO', 'type' => 'moeda', 'nivel' => 4),
					'C007_Valor_Maximo_PO_Dia' => array('name' => 'C007_Valor_Maximo_PO_Dia','label'=>'Valor máximo de compra por dia', 'type' => 'moeda', 'nivel' => 4),
					'C007_Valor_Maximo_PO_Mes' => array('name' => 'C007_Valor_Maximo_PO_Mes','label'=>'Valor máximo de compra por mês', 'type' => 'moeda', 'nivel' => 4),
					'C007_Valor_Maximo_PO_Fornecedor' => array('name' => 'C007_Valor_Maximo_PO_Fornecedor','label'=>'Valor máximo de compra do mês por fornecedor', 'type' => 'moeda', 'nivel' => 4),
					'observacaoOC' => array('name' => 'observacaoOC', 'label'=>'Limites para compra nacional','type' => 'titulo', 'reject' => true),
					'observacaoPO' => array('name' => 'observacaoOC', 'label'=>'Limites para compra internacional','type' => 'titulo', 'reject' => true),
					'C007_Nome_Vendedor_Ecommerce' => array('name' => 'C007_Nome_Vendedor_Ecommerce', 'label' => 'Nome e-commerce', 'type' => 'text', 'uppercase' => false, 'nivel' => 4),
					'C007_Flag_Tipo_Etiqueta_Transporte' => array('name' => 'C007_Flag_Tipo_Etiqueta_Transporte', 'label'=>'Tipo Etiqueta Transporte', 'type' => 'select', 
						'select' => array(
							array('title' => '...', 'value' => '', 'selected' => true),
							array('title' => 'Padrão do sistema', 'value' => '1'),
							array('title' => 'Controle Interno', 'value' => '2'),
							array('title' => 'Mercado Livre', 'value' => '3'),
							array('title' => 'Definido pela transportadora', 'value' => 'T'),
						)
					),
					'C007_Usuario_Acessos' => array('name' => 'C007_Usuario_Acessos', 'label'=>'Permite ativar cliente inativo na carteira do próprio vendedor', 'type' => 'select', 
						'select' => array(
							array('title' => 'Sim', 'value' => 'S'),
							array('title' => 'Não', 'value' => 'N'),
						),
						'extra' => "title='" . "CRM->Vendas->Clientes->Inativos: Se estiver marcado como *NÃO* e o usuário já for o vendedor do cliente, não será possível ativar na carteira" . "'"
					),
					'C007_D007_Id' => array('name' => 'C007_D007_Id', 'label'=>'Banco Padrão', 'type' => 'select', 
						'select' => gGeraSelect('D007', 'D007_Id', 'D007_Nome_Banco', '', true)
					),
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
						$alert = gGeraAlertMsg('Operação OK', 'Registro alterado com sucesso!', false);
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
		$botoesDir = $botoesEsq = '';
		$botoesDir .= '<button type="submit" id="cad030FormParametrosUsuario_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'cad030FormIntegracao':
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
		$form_tabela = 'C007';
		$form_campo_pk = 'C007_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad030FormIntegracao_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad030/form/cad030FormIntegracao/';

		$form_editor = array(
			'geral' => '{"C007_Key_Trello":{"top":7,"left":44.03125,"width":600,"hidden":0},"C007_Token_Trello":{"top":30,"left":33.625,"width":600,"hidden":0},"C007_Id_Usuario_Trello":{"top":53,"left":14.671875,"width":600,"hidden":0}}'
		);

		// novo formulário
		$form = new Formulario('cad030FormIntegracao');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'C007_Key_Trello' => array('name' => 'C007_Key_Trello', 'label'=>'Key trello', 'uppercase' => false, 'type' => 'text', 'length' => array(100,100)),
					'C007_Token_Trello' => array('name' => 'C007_Token_Trello', 'label'=>'Token trello', 'uppercase' => false, 'type' => 'text', 'length' => array(100,100)),
					'C007_Id_Usuario_Trello' => array('name' => 'C007_Id_Usuario_Trello', 'label'=>'Id usuario trello', 'uppercase' => false, 'type' => 'text', 'length' => array(100,100)),
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
						$alert = gGeraAlertMsg('Operação OK', 'Registro alterado com sucesso!', false);
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
		$botoesDir = $botoesEsq = '';
		$botoesDir .= '<button type="submit" id="cad030FormIntegracao_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'cad030FormRecriarSenha':
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
		$form_tabela = 'C007';
		$form_campo_pk = 'C007_Id';
		// ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
		$enviar_btnId = 'cad030FormRecriarSenha_btn';
		// Form que será executado ao clicar no botão
		$enviar_acaoHref = '/cad/cad030/form/cad030FormRecriarSenha/';
		$form_editor = array(
			'geral' => '{"RecriarSenha_Login":{"top":30,"left":50,"width":152,"hidden":0},"RecriarSenha_Senha":{"top":53,"left":50,"width":152,"hidden":0}}'
		);

/*		if(($g['divIdRoot'] == 'cad030b') && $acaoId!=""){
			$disabled = true;
		}else{
			$disabled=false;
		}*/
		// novo formulário
		$form = new Formulario('cad030FormRecriarSenha');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'RecriarSenha_Login' => array('name' => 'RecriarSenha_Login','label'=>'Login', 'type' => 'text', 'length' => array(33,60), 'uppercase' => false,'colunaTipo' => 'livre'),
					'RecriarSenha_Senha' => array('name' => 'RecriarSenha_Senha','label'=>'Senha', 'type' => 'password', 'length' => array(33,60), 'uppercase' => false,'colunaTipo' => 'livre'),
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
				$RecriarSenha_Login = $form->campoValorEnviado('RecriarSenha_Login');
				$RecriarSenha_Senha = $form->campoValorEnviado('RecriarSenha_Senha');
				$alert = gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
				echo <<<EOT
					<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
						$.getJSON('/cad/cad030/form_func-ajax/recriarSenha/?ajax=true&C007_Id=' + encodeURIComponent('{$r_C007_Id}') + '&RecriarSenha_Login=' + encodeURIComponent('{$RecriarSenha_Login}') + '&RecriarSenha_Senha=' + encodeURIComponent('{$RecriarSenha_Senha}') +'&callback=?', function(request) {
							if(request.code) {
								dialogConfirm('Ok', 'Processo concluído.');
							}else{
								dialogConfirm('Erro', request.data);	
							}				      						
						});						
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
		echo $gerado['pagina'];
		if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
		$g['smarty']->display('formEditor.tpl');

		/**
		* Botões
		*/
		// Definição dos botões
		$botoesDir = $botoesEsq = '';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$botoesDir .= '<button id="cad030FormRecriarSenha_btn">Recriar Senha Banco</button>';	

		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'cad030PerfilEmailForm':
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
		$form_tabela     = 'D202';
		$form_campo_pk   = 'D202_Id';
		$enviar_btnId    = 'cad030PerfilEmailForm_btn';
		$enviar_acaoHref = '/cad/cad030/form/cad030PerfilEmailForm/';

		$form_editor = array('geral' => '{"D202_C044_Id":{"top":7,"left":31.390625,"width":340,"hidden":0},"D202_C021_Id":{"top":30,"left":33.421875,"width":340,"hidden":0},"D202_C007_Id":{"top":53,"left":21.078125,"width":340,"hidden":0}}');

		// novo formulário
		$form = new Formulario('cad030PerfilEmailForm');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D202_C044_Id' => array('name' => 'D202_C044_Id', 'label' => 'Email', 'type' => 'select', 'select' => gGeraSelect('C044', 'C044_Id', 'C044_Descricao', '', true)),
					'D202_C021_Id' => array('name' => 'D202_C021_Id', 'label' => 'Perfil', 'type' => 'select', 'select' => gGeraSelect('C021', 'C021_Id', 'C021_Nome_Perfil', 'ORDER BY C021_Nome_Perfil', true)),
					'D202_C007_Id' => array('name' => 'D202_C007_Id', 'label' => 'Usuário', 'type' => 'select', 'select' => gGeraSelect('C007', 'C007_Id', 'C007_Nome', 'ORDER BY C007_Nome', true))
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
				$mensagem     = '';
				$D202_C044_Id = $form->campoValorEnviado('D202_C044_Id');
				$D202_C021_Id = $form->campoValorEnviado('D202_C021_Id');
				$D202_C007_Id = $form->campoValorEnviado('D202_C007_Id');

				if ($D202_C044_Id == 0) {
					$mensagem .= '<br>Nenhum perfil de email selecionado.';
				}

				if (($D202_C021_Id == 0) && ($D202_C007_Id == 0)) {
					$mensagem .= '<br>Nenhum perfil ou usuário selecionado.';
				}

				if (($D202_C021_Id > 0) && ($D202_C007_Id > 0)) {
					$mensagem .= '<br>Não é possivel escolher um perfil e um usuário ao mesmo tempo, selecione apenas uma opção.';
				}

				if (empty($mensagem)) {
					// Insert ou Update?
					if (!empty($acaoId)) {
						// Update
						if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
							// Imprime uma mensagem e fecha a janela
							$alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
						}
					} else {
						// Insert
						if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
							// Imprime uma mensagem e fecha a janela
							$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						}
					}
					echo <<<EOT
					<div class="divHidden"><script type="text/javascript">
						if (typeof jQuery != 'undefined') {
							$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
								setTimeout(function() {
									{$alert}
								}, 150);
							});
							$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
							divRefresh('{$g['divIdParent']}', true);
						}
					</script></div>
EOT;
				} else {
					echo gGeraAlertMsg('Erro!', 'Não é possível salvar o registro.<br>' . $mensagem);
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
		$botoesDir .= '<button type="submit" id="cad030PerfilEmailForm_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'cad030FeriadosForm':
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
		$form_tabela     = 'D206';
		$form_campo_pk   = 'D206_Id';
		$enviar_btnId    = 'cad030FeriadosForm_btn';
		$enviar_acaoHref = '/cad/cad030/form/cad030FeriadosForm/';

		$form_editor = array('geral' => '{"D206_Descricao":{"top":7,"left":9.71875,"width":200,"hidden":0},"D206_Flag_Ativo":{"top":30,"left":33.734375,"width":55,"hidden":0}}');

		// novo formulário
		$form = new Formulario('cad030FeriadosForm');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D206_Descricao' => array('name' => 'D206_Descricao', 'label' => 'Descrição', 'type' => 'text'),
					'D206_Flag_Ativo' => array('name' => 'D206_Flag_Ativo', 'label'=>'Ativo', 'type' => 'select','select' => 
						array(
							array('title' => 'SIM', 'value' => 'S'),
							array('title' => 'NÃO', 'value' => 'N'),	
						)
					),
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
						}
					} else {
						// Insert
						if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
							// Imprime uma mensagem e fecha a janela
							$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						}
					}
					echo <<<EOT
					<div class="divHidden"><script type="text/javascript">
						if (typeof jQuery != 'undefined') {
							$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
								setTimeout(function() {
									{$alert}
								}, 150);
							});
							$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
							divRefresh('{$g['divIdParent']}', true);
						}
					</script></div>
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

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="cad030FeriadosForm_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

	case 'cad030DataFeriadosForm':
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
		$form_tabela     = 'D207';
		$form_campo_pk   = 'D207_Id';
		$enviar_btnId    = 'cad030DataFeriadosForm_btn';
		$enviar_acaoHref = '/cad/cad030/form/cad030DataFeriadosForm/';

		$form_editor = array('geral' => '{"D207_D206_Id":{"top":53,"left":36,"width":137,"hidden":0},"D207_Descricao_Feriado":{"top":7,"left":21.953125,"width":280,"hidden":0},"D207_Data":{"top":30,"left":35.828125,"width":88,"hidden":0}}');

		// novo formulário
		$form = new Formulario('cad030DataFeriadosForm');
		// Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
		$formArray = array(
			'campos' => array(
				'geral' => array(
					'D207_D206_Id' => array('name' => 'D207_D206_Id', 'label' => 'D207_D206_Id', 'type' => 'hidden', 'value' => $r_D206_Id),
					'D207_Descricao_Feriado' => array('name' => 'D207_Descricao_Feriado', 'label' => 'Feriado', 'type' => 'text'),
					'D207_Data' => array('name' => 'D207_Data', 'label' => 'Data', 'type' => 'data')
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
						}
					} else {
						// Insert
						if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
							// Imprime uma mensagem e fecha a janela
							$alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
						}
					}
					echo <<<EOT
					<div class="divHidden"><script type="text/javascript">
						if (typeof jQuery != 'undefined') {
							$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
								setTimeout(function() {
									{$alert}
								}, 150);
							});
							$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
							divRefresh('{$g['divIdParent']}', true);
						}
					</script></div>
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

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesDir = '';
		$botoesDir .= '<button type="submit" id="cad030DataFeriadosForm_btn">Enviar</button>';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
}
