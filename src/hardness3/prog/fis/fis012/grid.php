<?php
namespace hardness;
require_once('prog/fis/fis012/grid_func-callback.php');

switch($g['r'][3]){
	/**
	* @author Ricardo Norio Miyata
	*/
	case 'fis012GridMDFe':
		global $g;

		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'excluirMDFe', 'colunaTitulo' => 'E', 'colunaAlinhar' => 'center', 'style' => 'font-weight:bold;width:1%'),
			array('colunaValor' => 'T276_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T276_Flag_Cancelada', 'colunaTitulo' => 'Cancelar', 'callback' => 'validaCampoCancelar'),
			array('colunaValor' => 'T276_Numero_MDFe', 'colunaTitulo' => 'MDF-e', 'style' => 'font-weight:bold;','colunaAlinhar' => 'center'),
			array('colunaValor' => 'T276_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'font-weight:bold;width:1%'),
			array('colunaValor' => 'descarregamentoD018.D018_UF descarregamentoUF', 'colunaTitulo' => 'Descarreg.'),
			array('colunaValor' => 'T276_Quantidade_NFe', 'colunaTitulo' => 'Qtd Notas'),
			array('colunaValor' => 'T276_Quantidade_CTe', 'colunaTitulo' => 'Qtd Conhecimentos'),
			array('colunaValor' => '(GROUP_CONCAT(DISTINCT CONCAT(D109_Nome) SEPARATOR "<br>")) as colunaMotoristas', 'colunaTitulo' => 'Motoristas', 'colunaAlinhar' => 'left', 'style' => 'font-size:11px;width:1%;', 'callback' => 'colunaMotoristas'),
            array('colunaValor' => 'Damdfe', 'colunaTitulo' => 'DAMDFe', 'colunaTipo' => 'livre', 'callback' => 'ValidaCampoDamdfe'),
			array('colunaValor' => 'XML', 'colunaTitulo' => 'XML', 'callback' => 'validaCampoXML', 'colunaTipo' => 'livre'),
            array('colunaValor' => 'T276_Observacao_Cancelamento', 'colunaTitulo' => 'T276_Observacao_Cancelamento', 'colunaExibir' => false),
			array('colunaValor' => 'Retorno Sefaz', 'colunaTipo' => 'livre', 'colunaTitulo' => 'Retorno SEFAZ', 'callback' => 'chamaCampo'),
			array('colunaValor' => 'T276_Numero_Protocolo_MDFe', 'colunaTitulo' => 'Protocolo MDF-e'),
			array('colunaValor' => 'T276_Numero_Protocolo_Cancelamento_MDFe', 'colunaTitulo' => 'Prot. Cancelamento', 'callback' => 'validaCampoXMLCancelado'),
			array('colunaValor' => 'T276_Chave_Acesso_MDFe', 'colunaTitulo' => 'Chave de Acesso'/*,  'colunaTipo' => 'livre', 'callback' => 'ValidaChaveAcesso'*/),
			array('colunaValor' => 'T284_Numero_NFe', 'colunaTitulo' => 'Número NF-e', 'colunaExibir' => false),
			array('colunaValor' => 'T276_C004_Id', 'colunaTitulo' => 'Id Empresa Emitente', 'colunaExibir' => false),
			array('colunaValor' => 'T276_TX_Resposta_Codigo', 'colunaTitulo' => 'Flag Transmissão', 'colunaExibir' => false),
			array('colunaValor' => 'T276_Flag_Encerrada', 'colunaTitulo' => 'Flag Encerrado', 'colunaExibir' => false),
			array('colunaValor' => 'carregamentoD018.D018_UF carregamentoD018', 'colunaTitulo' => 'UF Carregamento', 'colunaExibir' => false),
        );

        /**
		* Definição da SQL
		*/
		$from = 'T276';
		$extra = <<<EOT
			LEFT JOIN D018 as descarregamentoD018 on descarregamentoD018.D018_Id=T276_D018_Id_Descarregamento
			LEFT JOIN D018 as carregamentoD018 on carregamentoD018.D018_Id=T276_D018_Id_Carregamento
			LEFT JOIN T284 ON T276_Id = T284_T276_Id
			LEFT JOIN T278 ON T276_Id = T278_T276_Id			
			LEFT JOIN D109 ON D109_Id = T278_D109_Id			
			WHERE T276_C004_Id = '{$g['empresaAtual']}'
			GROUP BY T276_Id
EOT;

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="fis012GridMdfeBtnIncluir">Incluir</button>
			</div>

			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#fis012GridMdfeBtnIncluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir MDF-e', '/fis/fis012/content/fis012MdfeCadastro/', '&tab=geral&acao=incluir',  [940,750]);
				});
			} </script></div>
EOT;
		
		/**
		* Pode ser definido uma classe para cada linha do grid
		*/
		function gridLinha($array) {
			if($array['todosCampos']['T276_Flag_Cancelada'] == 'S'){
				return 'linhaVermelho';
			}else if($array['todosCampos']['T276_TX_Resposta_Codigo'] == 15){
				return 'linhaVerde';
			}else if($array['todosCampos']['T276_TX_Resposta_Codigo'] == 9){
				return 'linhaFundoAmareloClaro';
			}
		}

		/**
		* Filtro
		*/
		$gridFiltro = array(
			'T276_Numero_MDFe' => array('titulo' => 'Número MDF-e', 'tipo' => 'texto'),
			'T284_Numero_NFe' => array('titulo' => 'Número NF-e', 'tipo' => 'texto'),
			'T276_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
			'descarregamentoD018.D018_UF' => array('titulo' => 'UF Descarregamento', 'tipo' => 'texto'),
			'carregamentoD018.D018_UF' => array('titulo' => 'UF Carregamento', 'tipo' => 'texto'),
			'T276_Valor_Carga' => array('titulo' => 'Valor', 'tipo' => 'moeda'),
			'T276_Flag_Cancelada' => array('titulo' => 'Cancelado', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todos', 'value' => ''),
                array('title' => 'Sim', 'value' => 'S'),
                array('title' => 'Não', 'value' => 'N'),
              ),
              'naoGerarExtra' => ''),	
            'T276_Flag_Encerrada' => array('titulo' => 'Encerrado', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todos', 'value' => ''),
                array('title' => 'Sim', 'value' => 'S'),
                array('title' => 'Não', 'value' => 'N'),
              ),
              'naoGerarExtra' => ''),
            'D109_Nome' => array('titulo' => 'Motorista', 'tipo' => 'texto'),   
            'T276_Data_Inicio_Viagem' => array('titulo' => 'Início Viagem', 'tipo' => 'data'),	
		);

		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

		// Imprime o filtro do GRID (noTabelaPrecolmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		// Totalizador de registros
		$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
		
		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Registros<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;

		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'T276_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Alterar MDF-e: ' + acaoId, '/fis/fis012/content/fis012MdfeCadastro/', '&acaoId=' + encodeURIComponent(acaoId) + '&T276_Id=' + encodeURIComponent(acaoId) + '&acao=alterar', [940,750]);
		        });
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} </script></div>
EOT;

		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;

	/**
	* @author Raquel Pazini
	*/
	case 'fis012GridEnviarMDFe':
		global $g;

		//Verifica as numerações dos mdfes
		$sqlMdfes = "SELECT T276_Numero_MDFe, T276_Serie 
		               FROM T276
					  WHERE T276_C004_Id = '{$g['empresaAtual']}' 
					    AND T276_Serie < 900
				   ORDER BY T276_Serie DESC,floor(T276_Numero_MDFe) DESC LIMIT 20";
		$query = mysql_query($sqlMdfes);
		$mdfeAnterior = 0;
		while($row = mysql_fetch_array($query)){
	         if ($mdfeAnterior>0){
	        	$diferenca = $mdfeAnterior - $row['T276_Numero_MDFe']; 
	        	if ($diferenca == 2){
	        		$mensagemErro .= "Falta o Mdf-e " . ($mdfeAnterior-1) . '<br/>';
	        	} else if ($diferenca > 2){
	        		$mensagemErro .= "Faltam os Mdf-e " . ($row['T276_Numero_MDFe'] + 1) . " a " . ($mdfeAnterior - 1) . '<br/>';
	        	}
	         }
	         $mdfeAnterior = $row['T276_Numero_MDFe'];
		}

		if (!empty($mensagemErro)){
			/*echo "<br><br><p align=center><font style='color: var(--BGVermelho);font-weight:bold;font-size:18px'>ATENÇÃO!!<br/>" . $mensagemErro . " Favor verificar as numerações que estão faltando!!</font></p>";
			die();*/
		}

		$cert = '';
		// EXIBE VENCIMENTO DO CERTIFICADO DIGITAL
		$cSql_Empresa=@mysql_query("SELECT C004_Certificado_Digital, C004_Senha_Certificados, C004_Localizacao_Certificados FROM C004 LEFT JOIN D018 ON D018_Id=C004_D018_Id WHERE C004_Id='{$g['empresaAtual']}'");
		$mEmpresa=@mysql_fetch_array($cSql_Empresa);
		if ($g['c020']['C020_NFe_Tipo_Certificado']=='A1' and $g['c020']['C020_NFe_Habilitada']=='S') {
            if (!empty($mEmpresa['C004_Certificado_Digital'])) {	
	            $mkt_Hoje=time();
	            $mktVencimentoCertificado=pegaValidadeCertificado(base64_decode($mEmpresa['C004_Certificado_Digital']),$mEmpresa['C004_Senha_Certificados'],'S');
            } elseif (!empty($mEmpresa['C004_Localizacao_Certificados'])) {	
                $cArquivo_Certificado_Digital = $mEmpresa['C004_Localizacao_Certificados']."{$g['empresaAtual']}.pfx";
	            if (file_exists($cArquivo_Certificado_Digital)) {
	                $mkt_Hoje=time();
	                $mktVencimentoCertificado=pegaValidadeCertificadoA1($cArquivo_Certificado_Digital,$mEmpresa['C004_Senha_Certificados']);
	            }
            }	  
               
            if (empty($mktVencimentoCertificado)) {
            	$cert = "<span style='color: var(--BGVermelho);font-size:18px;'>Não foi possivel ler o certificado digital</span>";
            } else {
                $VencimentoCertificado=date("d/m/Y  h:i:s",$mktVencimentoCertificado);
                if ($mktVencimentoCertificado>$mkt_Hoje) {
                    $isCertificadoValido=true; 
                    $cert = <<<EOT
                    	<font style='font-size:11px;'><b>Seu certificado digital vence em:</b><BR></font>
						<font style='color:blue;font-size:18px;'><b>{$VencimentoCertificado}</b></font>
EOT;
                } else {
                   	$cert = <<<EOT
						<span style='color: var(--BGVermelho);font-size:11px;'><b>Certificado digital vencido em:</b><BR></span>
						<span style='color: var(--BGVermelho);font-size:18px;'><b>{$VencimentoCertificado}</b></span>
EOT;
                }
            }
        }

		$grid = array(
 			array('colunaValor' => 'T276_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false, 'colunaAlinhar' => 'center', 'style' => 'width:1%;font-size:12px'),
			array('colunaValor' => 'T276_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaDimensao' => 'S', 'colunaAlinhar' => 'center', 'colunaAlinhar' => 'center', 'style' => 'width: 1%;vertical-align: top;font-weight:bold;'),
			array('colunaValor' => 'T276_Numero_MDFe', 'colunaTitulo' => 'Número MDF-e', 'colunaAlinhar' => 'center', 'style' => 'width:1%;font-size:12px;font-weight:bold;'),
			array('colunaValor' => 'T276_Valor_Carga', 'colunaTitulo' => 'Valor', 'colunaCasasDecimais' => 2, 'colunaAlinhar' => 'center', 'style' => 'width: 1%;vertical-align: center;font-weight:bold;'),
			array('colunaValor' => 'carregamentoD018.D018_UF carregamentoD018', 'colunaTitulo' => 'UF Carregamento', 'colunaDimensao' => 'S', 'colunaAlinhar' => 'center', 'style' => 'width: 1%;vertical-align: center;font-weight:bold;'),
			array('colunaValor' => 'carregamentoD020.D020_Nome_Cidade', 'colunaTitulo' => 'Cidade Carregamento', 'colunaDimensao' => 'S', 'colunaAlinhar' => 'center', 'style' => 'width: 1%;vertical-align: center;font-weight:bold;'),
			array('colunaValor' => 'descarregamentoD018.D018_UF descarregamentoUF', 'colunaTitulo' => 'UF Descarregamento', 'colunaDimensao' => 'S','colunaAlinhar' => 'center', 'style' => 'width: 1%;vertical-align: center;font-weight:bold;'),
			array('colunaValor' => 'T276_Ambiente_MDFe', 'colunaTitulo' => 'Ambiente MDF-e', 'colunaExibir' => false),
			array('colunaValor' => 'txSefazMDFe', 'colunaTitulo' => 'Transmissão MDF-e Sefaz', 'callback' => 'txSefazMDFe', 'colunaTipo' => 'livre', 'style' => 'width: 50%; vertical-align: top;', 'callbackParameter' => array('imprimirAutomatico' => true,'encerrar' =>'N','encerrado' => 'N')),			
		);
		
		function gridLinha($array) {
			global $g;

			if ($array['todosCampos']['T276_TX_Resposta_Codigo'] < 100) {
				if ($array['todosCampos']['T276_TX_Resposta_Codigo'] == 9) {
					return 'linhaSuccess';
				} 
			} else {
				return 'linhaErrorNoColor';
			}
		}

		/**
		 * Definição da SQL
		 */
		$from = "T276";
		
		$extra = <<<EOT
			LEFT JOIN D018 as descarregamentoD018 on descarregamentoD018.D018_Id=T276_D018_Id_Descarregamento
			LEFT JOIN D018 as carregamentoD018 on carregamentoD018.D018_Id=T276_D018_Id_Carregamento
			LEFT JOIN D020 as carregamentoD020 on carregamentoD020.D020_Id=T276_D020_Id_Carregamento
			LEFT JOIN T278 ON T278_T276_Id=T276_Id			
			WHERE T276_C004_Id='{$g['empresaAtual']}' 
			AND T276_TX_Flag_Finalizada!=1
			AND IFNULL(T276_Numero_Protocolo_MDFe, '')=''
			GROUP BY T276_Id
			ORDER BY T276_Numero_MDFe
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
            'T276_Numero_MDFe' => array('titulo' => 'MDF-e', 'tipo' => 'numero'),
            'T276_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
            'carregamentoD018.D018_UF' => array('titulo' => 'UF Carregamento', 'tipo' => 'texto'),
            'carregamentoD020.D020_Nome_Cidade' => array('titulo' => 'Cidade Carregamento', 'tipo' => 'texto'),
            'descarregamentoD018.D018_UF' => array('titulo' => 'UF Descarregamento', 'tipo' => 'texto'),
            'T276_Flag_Travar_MDFe' => array('titulo' => 'Finalizada?(S/N)', 'tipo' => 'texto'),
            'T278_Nome' => array('titulo' => 'Motorista', 'tipo' => 'texto'),
            'T276_Data_Inicio_Viagem' => array('titulo' => 'Início Viagem', 'tipo' => 'data'),
        );
		

		//Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
			
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		//$sqlGridFiltro = base64_encode($geradoSql);
			
		/**
		* Totais (Versão Acima do GRID)
		*/
		$numeroRegistros = $g['sqlAuto']->pegarSqlCampo("count(T276_Id)", $from, str_replace('GROUP BY T276_Id', '', $extra));
		$valorTotal = $g['sqlAuto']->pegarSqlCampo("sum(T276_Valor_Carga)", $from, str_replace('GROUP BY T276_Id', '', $extra));

		if (!personalizacaoTotais()) {
			echo <<<EOT
				<div class="buttonsBottomTop">
					<span class="item" style="text-align:right">MDF-e<br><input type="text" class="whiteBg" id="inputValorTotal" size="11" value="{$numeroRegistros}" readonly></span>
					<span class="item" style="text-align:right">Valor Total<br><input type="text" class="whiteBg" id="inputValorTotal" size="11" value="{$valorTotal}" readonly></span>
					<div style="clear:both;"></div>
				</div>
EOT;
		}	

		/**
		* Geração: Grid
		*/
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }

		//define o clique da linha
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
                    var id = $(this).closest('tr').attr('id').split('|');
                    var T276_Id = id[0].replace(/T276_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', menuId('MDFe'), '', 'Editar MDFe - ID: ' + T276_Id, '/fis/fis012/content/fis012MdfeCadastro/', '&acaoId=' + encodeURIComponent(T276_Id) + '&tabela={$from}', [990,605]);
				});
			} </script></div>
EOT;
  	break;


    /**
	* @author Alexa Mahara
	*/
	case 'fis012GridLacres':
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$campoParent = 'T276'; 
		$valorParent = $acaoId;

		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T280_Id', $g['divId'], $campoParent, $valorParent)),
			array('colunaValor' => 'T280_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'T280_T276_Id', 'colunaTitulo' => 'Número MDF-e', 'colunaExibir' => false),
			array('colunaValor' => 'D171_Descricao', 'colunaTitulo' => 'Descrição'),
			array('colunaValor' => 'D171_Numero_Lacre', 'colunaTitulo' => 'Número Lacre'),
		);
			
		/**
		*
		* Definição da SQL
		*/
		$from = 'T280';

		$extra = <<<EOT
			LEFT JOIN T276 ON T276_Id = T280_T276_Id
			LEFT JOIN D171 ON D171_Id = T280_D171_Id
			WHERE T280_T276_Id = {$r_acaoId}
EOT;

		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		
		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); } 
    break;

    /**
	* @author Alexa Mahara
	*/
	case 'fis012GridLacresModalRodoviario':
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$campoParent = 'T276'; 
		$valorParent = $acaoId;

		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T286_Id', $g['divId'], $campoParent, $valorParent)),
			array('colunaValor' => 'T286_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'D171_Numero_Lacre', 'colunaTitulo' => 'Número Lacre'),
			array('colunaValor' => 'D171_Descricao', 'colunaTitulo' => 'Descrição'),
		);
			
		/**
		*
		* Definição da SQL
		*/
		$from = 'T286';

		$extra = <<<EOT
			LEFT JOIN D171 ON D171_Id = T286_D171_Id
			WHERE T286_T276_Id = {$acaoId}
EOT;

		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		
		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); } 
    break;

	/**
	* @author Ricardo Norio Miyata
	*/
	case 'fis012GridIncluirNfe':
		global $g;
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$T276_Id = (isset($_REQUEST['T276_Id'])) ? $_REQUEST['T276_Id'] : false;

		$campoParent = 'T276'; 
		$valorParent = $T276_Id;
		/*$parametro = array('T284', $acaoId, 'T276', $T276_Id);
		echo $parametro*/
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T284_Id', $g['divId'], $campoParent, $valorParent), 'colunaExibir' => true),
            array('colunaValor' => 'T284_Id', 'colunaTitulo' => 'Id', 'colunaAlinhar' => 'center', 'chavePrimaria' => true),
            array('colunaValor' => 'T284_T007_Id', 'colunaTitulo' => 'Tipo', 'colunaExibir'=> false),
            array('colunaValor' => 'T284_Flag_Tipo_Documento', 'colunaTitulo' => 'Tipo', 'callback' => 'validaTipo'),
            array('colunaValor' => 'T284_Numero_NFe', 'colunaTitulo' => 'NF-e', 'colunaAlinhar' => 'left','style' => 'font-weight:bold', 'callback' => 'abrirJanela', 'callbackParameter' => array('Editar Nota Fiscal', '/ven/ven003/content/ven003contentIncluirNotaFiscal/', 'T284_T007_Id', '1000,583')),
            array('colunaValor' => 'T284_Destinatario_Nome', 'colunaTitulo' => 'Destinatário'),
            array('colunaValor' => 'T284_Valor_Carga', 'colunaTitulo' => 'Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2','style' => 'font-weight:bold;background:var(--celula-amarelo-claro)'),
            array('colunaValor' => 'T284_Peso_Bruto_Total', 'colunaTitulo' => 'Peso', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3', 'colunaEditar' =>true),
            array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF Descarregamento','style' => 'width:1%;','colunaAlinhar' => 'center'),
            array('colunaValor' => 'T284_Cidade_Descarregamento', 'colunaTitulo' => 'Cidade Descarregamento'),
		);

        /**
		* Definição da SQL
		*/
		$from = 'T284';
		$sql_ = urlencode(base64_encode($geradoSql));
		$extra = <<<EOT
			LEFT JOIN T007 ON T007_Id = T284_T007_Id
			LEFT JOIN D024 ON D024_Id = T007_D024_Id
			LEFT JOIN D018 ON D018_Id = T284_D018_Id
			WHERE T284_T276_Id = '{$acaoId}' AND T284_Flag_Tipo_Documento = '2'
			ORDER BY T284_Id
EOT;

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="fis012GridMdfeBtnIncluirNotaFiscal">Incluir NF-e</button>
				<button type="button" id="fis012GridMdfeBtnAlterarDescarregamento">Alterar Descarregamento</button>
			</div>

			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#fis012GridMdfeBtnIncluirNotaFiscal').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir de NF-e', '/fis/fis012/content/fis012ContentCadastroNfeMdfe/', '&tab=geral' + '&T276_Id=' + encodeURIComponent('{$acaoId}'),  [600,300]);
				});
				$('#fis012GridMdfeBtnAlterarDescarregamento').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Alterar Descarregamento', '/fis/fis012/content/fis012ContentAlterarDescarregamento/', '' + '&T276_Id=' + encodeURIComponent('{$acaoId}'),  [400,200]);
				});
			} </script></div>
EOT;
		/**
		* Filtro
		*/

		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

		// Imprime o filtro do GRID (noTabelaPrecolmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		// Totalizador de registros
		$qntNfe = $g['sqlAuto']->pegarSqlCampo("count(T284_Id)", $from, $extra);
		$pesoTotal = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T284_Peso_Bruto_Total)", $from, $extra));
		$valorTotal = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T284_Valor_Carga)", $from, $extra));

		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Quantidade NF-e<br><input type="text" value="{$qntNfe}" class="whiteBg" id="" size="11" readonly></span>
				<span class="item" style="text-align:right">Valor Total<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<span class="item" style="text-align:right">Peso Total<br><input type="text" value="{$pesoTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;

		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'T284_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Alterar NF-e | Incluir Tipo Transporte: ' + acaoId, '/fis/fis012/content/fis012ContentAlterarNfeMdfe/', '&T284_Id=' + encodeURIComponent(acaoId) + '&acaoId=' + encodeURIComponent(acaoId) + '&T276_Id=' + encodeURIComponent({$T276_Id}), [750,650]);
		        });
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} </script></div>
EOT;

		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;

	/**
	* @author Lígia Nakazato
	*/
	case 'fis012GridEncerrarMDFe':
		//Verifica as numerações dos mdfes
		$sqlMdfes = "SELECT T276_Numero_MDFe, T276_Serie 
		               FROM T276
					  WHERE T276_C004_Id = '{$g['empresaAtual']}' 
					    AND T276_Serie < 900
				   ORDER BY T276_Serie DESC,floor(T276_Numero_MDFe) DESC LIMIT 20";
		$query = mysql_query($sqlMdfes);
		$mdfeAnterior = 0;
		while($row = mysql_fetch_array($query)){
	         if ($mdfeAnterior>0){
	        	$diferenca = $mdfeAnterior - $row['T276_Numero_MDFe']; 
	        	if ($diferenca == 2){
	        		$mensagemErro .= "Falta o Mdf-e " . ($mdfeAnterior-1) . '<br/>';
	        	} else if ($diferenca > 2){
	        		$mensagemErro .= "Faltam os Mdf-e " . ($row['T276_Numero_MDFe'] + 1) . " a " . ($mdfeAnterior - 1) . '<br/>';
	        	}
	         }
	         $mdfeAnterior = $row['T276_Numero_MDFe'];
		}

		/*if (!empty($mensagemErro)){
			echo "<br><br><p align=center><font style='color: var(--BGVermelho);font-weight:bold;font-size:18px'>ATENÇÃO!!<br/>" . $mensagemErro . " Favor verificar as numerações que estão faltando!!</font></p>";
			die();
		}*/

		$cert = '';
		// EXIBE VENCIMENTO DO CERTIFICADO DIGITAL
		$cSql_Empresa=@mysql_query("SELECT * FROM C004 LEFT JOIN D018 ON D018_Id=C004_D018_Id WHERE C004_Id='{$g['empresaAtual']}'");
		$mEmpresa=@mysql_fetch_array($cSql_Empresa);
		if ($g['c020']['C020_NFe_Tipo_Certificado']=='A1' and $g['c020']['C020_NFe_Habilitada']=='S') {
            if (!empty($mEmpresa['C004_Certificado_Digital'])) {	
	            $mkt_Hoje=time();
	            $mktVencimentoCertificado=pegaValidadeCertificado(base64_decode($mEmpresa['C004_Certificado_Digital']),$mEmpresa['C004_Senha_Certificados'],'S');
            } elseif (!empty($mEmpresa['C004_Localizacao_Certificados'])) {	
                $cArquivo_Certificado_Digital = $mEmpresa['C004_Localizacao_Certificados']."{$g['empresaAtual']}.pfx";
	            if (file_exists($cArquivo_Certificado_Digital)) {
	                $mkt_Hoje=time();
	                $mktVencimentoCertificado=pegaValidadeCertificadoA1($cArquivo_Certificado_Digital,$mEmpresa['C004_Senha_Certificados']);
	            }
            }	  
               
            if (empty($mktVencimentoCertificado)) {
            	$cert = "<span style='color: var(--BGVermelho);font-size:18px;'>Não foi possivel ler o certificado digital</span>";
            } else {
                $VencimentoCertificado=date("d/m/Y  h:i:s",$mktVencimentoCertificado);
                if ($mktVencimentoCertificado>$mkt_Hoje) {
                    $isCertificadoValido=true; 
                    $cert = <<<EOT
                    	<font style='font-size:11px;'><b>Seu certificado digital vence em:</b><BR></font>
						<font style='color:blue;font-size:18px;'><b>{$VencimentoCertificado}</b></font>
EOT;
                } else {
                   	$cert = <<<EOT
						<span style='color: var(--BGVermelho);font-size:11px;'><b>Certificado digital vencido em:</b><BR></span>
						<span style='color: var(--BGVermelho);font-size:18px;'><b>{$VencimentoCertificado}</b></span>
EOT;
                }
            }
        }

        $status = explode('|', trim(file_get_contents('/tmp/status-sefaz')));
		$normal = ($status[0]) ? 'semaforo_verde.png' : 'semaforo_vermelho.png';
		//$scan = ($status[1]) ? 'semaforo_verde.png' : 'semaforo_vermelho.png';
		$mensagemCert = <<<EOT
			<div style='margin:0px 0 0 800px;width:175px;background-color:$backgroundCert;align:center;padding:7px'>
				<!--<h2 style="float: left; margin: 9px 5px 0 5px;">Sefaz:</h2><img src="/hardness3/static/img/{$normal}" style="float: left; margin-top: 7px"></img>-->
				<!--<h2 style="float: left; margin: 9px 5px 0 5px;">SVC:</h2><img src="/hardness3/static/img/{$scan}" style="float: left; margin-top: 7px"></img>-->
					{$cert}
			</div>
EOT;

		$grid = array(
 			array('colunaValor' => 'T276_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false ),
			array('colunaValor' => 'T276_Numero_MDFe', 'colunaTitulo' => 'Número MDF-e', 'colunaAlinhar' => 'center', 'style' => 'width: 1%;vertical-align: center;font-weight:bold;'),
			array('colunaValor' => 'T276_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaDimensao' => 'S', 'colunaAlinhar' => 'center', 'colunaAlinhar' => 'center', 'style' => 'width: 1%;vertical-align: top;font-weight:bold;'),
			array('colunaValor' => 'T276_Flag_Cancelada', 'colunaTitulo' => 'Cancelar', 'callback' => 'validaCampoCancelar'),
			array('colunaValor' => 'T276_Valor_Carga', 'colunaTitulo' => 'Valor', 'colunaCasasDecimais' => 2, 'colunaAlinhar' => 'center', 'style' => 'width: 1%;vertical-align: top;font-weight:bold;'),
			array('colunaValor' => 'carregamentoD018.D018_UF', 'colunaTitulo' => 'UF Carregamento', 'colunaDimensao' => 'S', 'colunaAlinhar' => 'center', 'style' => 'width: 1%;vertical-align: center;font-weight:bold;'),
			array('colunaValor' => 'carregamentoD020.D020_Nome_Cidade', 'colunaTitulo' => 'Cidade Carregamento', 'colunaDimensao' => 'S', 'colunaAlinhar' => 'left', 'style' => 'width: 1%;vertical-align: center;font-weight:bold;'),
			array('colunaValor' => 'descarregamentoD018.D018_UF', 'colunaTitulo' => 'UF Descarregamento', 'colunaDimensao' => 'S', 'colunaAlinhar' => 'center', 'style' => 'width: 1%;vertical-align: center;font-weight:bold;'),
			array('colunaValor' => "IFNULL(descarregamentoD020.D020_Nome_Cidade, '<span style=\"color: var(--BGVermelho);font-weight:bold;\">PREENCHA A CIDADE')", 'colunaTitulo' => 'Cidade Descarregamento', 'colunaDimensao' => 'S', 'colunaAlinhar' => 'left', 'style' => $style),
			array('colunaValor' => 'T276_C004_Id', 'colunaTitulo' => 'Id empresa', 'colunaExibir' => false ),			
			array('colunaValor' => 'txSefazMDFe', 'colunaTitulo' => 'Transmissão MDF-e Sefaz', 'callback' => 'txSefazMDFe', 'colunaTipo' => 'livre', 'style' => 'width: 50%; vertical-align: top;', 'callbackParameter' => array('imprimirAutomatico' => true, 'encerrar' =>'S','encerrado' => 'N')),			
			
		);

		
		/**
		 * Definição da SQL
		 */
		$from = "T276";
		
		$extra = <<<EOT
			LEFT JOIN D020 as descarregamentoD020 on descarregamentoD020.D020_Id=T276_D020_Id_Descarregamento
			LEFT JOIN D020 as carregamentoD020 on carregamentoD020.D020_Id=T276_D020_Id_Carregamento
			LEFT JOIN D018 as descarregamentoD018 on descarregamentoD018.D018_Id=T276_D018_Id_Descarregamento
			LEFT JOIN D018 as carregamentoD018 on carregamentoD018.D018_Id=T276_D018_Id_Carregamento
			LEFT JOIN T278 ON T278_T276_Id=T276_Id 			
			WHERE T276_C004_Id='{$g['empresaAtual']}' 
			AND T276_TX_Flag_Finalizada!=1
			AND IFNULL(T276_Numero_Protocolo_MDFe,'')!=''
			AND IFNULL(T276_Numero_Protocolo_Cancelamento_MDFe,'') =''
			AND T276_Flag_Encerrada!='S'
			GROUP BY T276_Id
			ORDER BY T276_Numero_MDFe
EOT;

		/**
		* Filtro
		*/
		$gridFiltro = array(
            'T276_Numero_MDFe' => array('titulo' => 'MDF-e', 'tipo' => 'numero'),
            'T276_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
            'carregamentoD018.D018_UF' => array('titulo' => 'UF Carregamento', 'tipo' => 'texto'),
            'carregamentoD020.D020_Nome_Cidade' => array('titulo' => 'Cidade Carregamento', 'tipo' => 'texto'),
            'descarregamentoD018.D018_UF' => array('titulo' => 'UF Descarregamento', 'tipo' => 'texto'),
            'T276_Flag_Travar_MDFe' => array('titulo' => 'Finalizada?(S/N)', 'tipo' => 'texto'),
            'T278_Nome' => array('titulo' => 'Motorista', 'tipo' => 'texto'),
            'T276_Data_Inicio_Viagem' => array('titulo' => 'Início Viagem', 'tipo' => 'data'),
        );

		function gridLinha($array) {
			global $g;
		
			if ($array['todosCampos']['T276_TX_Resposta_Codigo'] < 100) {
				if ($array['todosCampos']['T276_TX_Resposta_Codigo'] == 15) {
					return 'linhaSuccess';
				} else {
					return '';
				}
			} else {
				return 'linhaErrorNoColor';
			}
		}

		//Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
			
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		$sqlGridFiltro = base64_encode($geradoSql);
			
		/**
		* Botões (Versão Acima do GRID)
		*/
		$numeroRegistros = $g['sqlAuto']->pegarSqlCampo("count(T276_Id)", $from, str_replace('GROUP BY T276_Id', '', $extra));
		$valorTotal = $g['sqlAuto']->pegarSqlCampo("sum(T276_Valor_Carga)", $from, str_replace('GROUP BY T276_Id', '', $extra));

		if (!personalizacaoTotais()) {
			echo <<<EOT
				<div class="buttonsBottomTop">
					<span class="item" style="text-align:right">MDF-e<br><input type="text" class="whiteBg" id="inputValorTotal" size="11" value="{$numeroRegistros}" readonly></span>
					<span class="item" style="text-align:right">Valor Total<br><input type="text" class="whiteBg" id="inputValorTotal" size="11" value="{$valorTotal}" readonly></span>
					<div style="clear:both;"></div>
				</div>
EOT;
		}

		//define o clique da linha
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
                    var id = $(this).closest('tr').attr('id').split('|');
                    var T276_Id = id[0].replace(/T276_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', menuId('MDFe'), '', 'Editar MDFe - ID: ' + T276_Id, '/fis/fis012/content/fis012MdfeCadastro/', '&acaoId=' + encodeURIComponent(T276_Id) + '&tabela={$from}', [990,605]);
				});
				if (typeof(outtime) != 'undefined') {
					clearInterval(outtime);
				}
				outtime = setTimeout(function() {
					divRefresh('{$g['divId']}');
				}, 300000);
			} </script></div>
EOT;

		/**
		* Geração: Grid
		*/
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
  	break;

  	/**
	* @author Lígia Nakazato
	*/
	case 'fis012GridEncerradosMDFe':
		//Verifica as numerações dos mdfes
		$sqlMdfes = "SELECT T276_Numero_MDFe, T276_Serie 
		               FROM T276
					  WHERE T276_C004_Id = '{$g['empresaAtual']}' 
					    AND T276_Serie < 900
				   ORDER BY T276_Serie DESC,floor(T276_Numero_MDFe) DESC LIMIT 20";
		$query = mysql_query($sqlMdfes);
		$mdfeAnterior = 0;
		while($row = mysql_fetch_array($query)){
	         if ($mdfeAnterior>0){
	        	$diferenca = $mdfeAnterior - $row['T276_Numero_MDFe']; 
	        	if ($diferenca == 2){
	        		$mensagemErro .= "Falta o Mdf-e " . ($mdfeAnterior-1) . '<br/>';
	        	} else if ($diferenca > 2){
	        		$mensagemErro .= "Faltam os Mdf-e " . ($row['T276_Numero_MDFe'] + 1) . " a " . ($mdfeAnterior - 1) . '<br/>';
	        	}
	         }
	         $mdfeAnterior = $row['T276_Numero_MDFe'];
		}

		if (!empty($mensagemErro)){
			/*echo "<br><br><p align=center><font style='color: var(--BGVermelho);font-weight:bold;font-size:18px'>ATENÇÃO!!<br/>" . $mensagemErro . " Favor verificar as numerações que estão faltando!!</font></p>";
			die();*/
		}

		$cert = '';
		// EXIBE VENCIMENTO DO CERTIFICADO DIGITAL
		$cSql_Empresa=@mysql_query("SELECT * FROM C004 LEFT JOIN D018 ON D018_Id=C004_D018_Id WHERE C004_Id='{$g['empresaAtual']}'");
		$mEmpresa=@mysql_fetch_array($cSql_Empresa);
		if ($g['c020']['C020_NFe_Tipo_Certificado']=='A1' and $g['c020']['C020_NFe_Habilitada']=='S') {
            if (!empty($mEmpresa['C004_Certificado_Digital'])) {	
	            $mkt_Hoje=time();
	            $mktVencimentoCertificado=pegaValidadeCertificado(base64_decode($mEmpresa['C004_Certificado_Digital']),$mEmpresa['C004_Senha_Certificados'],'S');
            } elseif (!empty($mEmpresa['C004_Localizacao_Certificados'])) {	
                $cArquivo_Certificado_Digital = $mEmpresa['C004_Localizacao_Certificados']."{$g['empresaAtual']}.pfx";
	            if (file_exists($cArquivo_Certificado_Digital)) {
	                $mkt_Hoje=time();
	                $mktVencimentoCertificado=pegaValidadeCertificadoA1($cArquivo_Certificado_Digital,$mEmpresa['C004_Senha_Certificados']);
	            }
            }	  
               
            if (empty($mktVencimentoCertificado)) {
            	$cert = "<span style='color: var(--BGVermelho);font-size:18px;'>Não foi possivel ler o certificado digital</span>";
            } else {
                $VencimentoCertificado=date("d/m/Y  h:i:s",$mktVencimentoCertificado);
                if ($mktVencimentoCertificado>$mkt_Hoje) {
                    $isCertificadoValido=true; 
                    $cert = <<<EOT
                    	<font style='font-size:11px;'><b>Seu certificado digital vence em:</b><BR></font>
						<font style='color:blue;font-size:18px;'><b>{$VencimentoCertificado}</b></font>
EOT;
                } else {
                   	$cert = <<<EOT
						<span style='color: var(--BGVermelho);font-size:11px;'><b>Certificado digital vencido em:</b><BR></span>
						<span style='color: var(--BGVermelho);font-size:18px;'><b>{$VencimentoCertificado}</b></span>
EOT;
                }
            }
        }

        $status = explode('|', trim(file_get_contents('/tmp/status-sefaz')));
		$normal = ($status[0]) ? 'semaforo_verde.png' : 'semaforo_vermelho.png';
		$scan = ($status[1]) ? 'semaforo_verde.png' : 'semaforo_vermelho.png';
		$mensagemCert = <<<EOT
			<div style='margin:0px 0 0 800px;width:175px;background-color:$backgroundCert;align:center;padding:7px'>
				<!--<h2 style="float: left; margin: 9px 5px 0 5px;">Sefaz:</h2><img src="/hardness3/static/img/{$normal}" style="float: left; margin-top: 7px"></img>-->
				<!--<h2 style="float: left; margin: 9px 5px 0 5px;">SVC:</h2><img src="/hardness3/static/img/{$scan}" style="float: left; margin-top: 7px"></img>-->
					{$cert}
			</div>
EOT;

		$grid = array(
 			array('colunaValor' => 'T276_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false ),
			array('colunaValor' => 'T276_Data_Emissao', 'colunaTitulo' => 'Emissão', 'callback' => 'gCorrigeData', 'colunaDimensao' => 'S', 'colunaAlinhar' => 'center', 'colunaAlinhar' => 'center', 'style' => 'width: 1%;vertical-align: top;font-weight:bold;'),
			array('colunaValor' => 'T276_Numero_MDFe', 'colunaTitulo' => 'Número MDF-e', 'colunaAlinhar' => 'center', 'style' => 'width: 1%;vertical-align: top;font-weight:bold;'),
			array('colunaValor' => 'T276_Valor_Carga', 'colunaTitulo' => 'Valor', 'colunaCasasDecimais' => 2, 'colunaAlinhar' => 'center', 'style' => 'width: 1%;vertical-align: top;font-weight:bold;'),
			array('colunaValor' => 'carregamentoD018.D018_UF', 'colunaTitulo' => 'UF Carregamento', 'colunaDimensao' => 'S', 'colunaAlinhar' => 'center', 'style' => 'width: 1%;vertical-align: top;font-weight:bold;'),
			array('colunaValor' => 'carregamentoD020.D020_Nome_Cidade', 'colunaTitulo' => 'Cidade Carregamento', 'colunaDimensao' => 'S', 'style' => 'width: 1%;vertical-align: top;font-weight:bold;'),
			array('colunaValor' => 'descarregamentoD018.D018_UF', 'colunaTitulo' => 'UF Descarregamento', 'colunaDimensao' => 'S','colunaAlinhar' => 'center', 'style' => 'width: 1%;vertical-align: top;font-weight:bold;'),
			array('colunaValor' => 'descarregamentoD020.D020_Nome_Cidade', 'colunaTitulo' => 'Cidade Descarregamento', 'colunaDimensao' => 'S', 'style' => 'width: 1%;vertical-align: top;font-weight:bold;'),
			array('colunaValor' => 'T276_C004_Id', 'colunaTitulo' => 'Id empresa', 'colunaExibir' => false ),			
			array('colunaValor' => 'txSefazMDFe', 'colunaTitulo' => 'Transmissão MDF-e Sefaz', 'callback' => 'txSefazMDFe', 'colunaTipo' => 'livre', 'style' => 'width: 50%;', 'callbackParameter' => array('imprimirAutomatico' => true, 'encerrar' =>'N', 'encerrado' => 'S')),			
		);
		
		/**
		 * Definição da SQL
		 */
		$from = "T276";
		
		$extra = <<<EOT
			LEFT JOIN D020 as descarregamentoD020 on descarregamentoD020.D020_Id=T276_D020_Id_Descarregamento
			LEFT JOIN D020 as carregamentoD020 on carregamentoD020.D020_Id=T276_D020_Id_Carregamento
			LEFT JOIN D018 as descarregamentoD018 on descarregamentoD018.D018_Id=T276_D018_Id_Descarregamento
			LEFT JOIN D018 as carregamentoD018 on carregamentoD018.D018_Id=T276_D018_Id_Carregamento
			LEFT JOIN T278 ON T278_T276_Id=T276_Id 			
			WHERE T276_C004_Id='{$g['empresaAtual']}' 
		    AND T276_TX_Flag_Finalizada!=1
		    AND T276_Flag_Encerrada='S'
			GROUP BY T276_Id
			ORDER BY T276_Numero_MDFe

EOT;
			//esse era o extra utilizado na nota
			// WHERE T276_C004_Id='{$g['empresaAtual']}' 
			// AND IFNULL(T276_Protocolo_Inutilizacao,'')=''
		 //    AND T276_TX_Flag_Finalizada!=1
			// AND IFNULL(T276_Protocolo_Inutilizacao,'') = ''
		 //    AND IFNULL(T276_Numero_Protocolo_Nfe,'') = ''
			// ORDER BY T276_Numero_MDFe
		function gridLinha($array) {
			global $g;

			if ($array['todosCampos']['T276_TX_Resposta_Codigo'] < 100) {
				if ($array['todosCampos']['T276_TX_Resposta_Codigo'] == 9) {
					return 'linhaSuccess';
				} else {
					return '';
				}
			} else {
				return 'linhaErrorNoColor';
			}
		}

		/**
		* Filtro
		*/
		$gridFiltro = array(
            'T276_Numero_MDFe' => array('titulo' => 'MDF-e', 'tipo' => 'numero'),
            'T276_Data_Emissao' => array('titulo' => 'Emissão', 'tipo' => 'data'),
            'carregamentoD018.D018_UF' => array('titulo' => 'UF Carregamento', 'tipo' => 'texto'),
            'carregamentoD020.D020_Nome_Cidade' => array('titulo' => 'Cidade Carregamento', 'tipo' => 'texto'),
            'descarregamentoD018.D018_UF' => array('titulo' => 'UF Descarregamento', 'tipo' => 'texto'),
            'descarregamentoD020.D020_Nome_Cidade' => array('titulo' => 'Cidade Descarregamento', 'tipo' => 'texto'),
            'T278_Nome' => array('titulo' => 'Motorista', 'tipo' => 'texto'),
            'T276_Data_Inicio_Viagem' => array('titulo' => 'Início Viagem', 'tipo' => 'data'),
        );
		
		//Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
			
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		$sqlGridFiltro = base64_encode($geradoSql);
			
		/**
		* Botões (Versão Acima do GRID)
		*/
		$numeroRegistros = $g['sqlAuto']->pegarSqlCampo("count(T276_Id)", $from, str_replace('GROUP BY T276_Id', '', $extra));
		$valorTotal = $g['sqlAuto']->pegarSqlCampo("sum(T276_Valor_Carga)", $from, str_replace('GROUP BY T276_Id', '', $extra));

		if (!personalizacaoTotais()) {
			echo <<<EOT
				<div class="buttonsBottomTop">
					<span class="item" style="text-align:right">MDF-e<br><input type="text" class="whiteBg" id="inputValorTotal" size="11" value="{$numeroRegistros}" readonly></span>
					<span class="item" style="text-align:right">Valor Total<br><input type="text" class="whiteBg" id="inputValorTotal" size="11" value="{$valorTotal}" readonly></span>
					<div style="clear:both;"></div>
				</div>
EOT;
		}

		/**
		* Geração: Grid
		*/
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }

		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
                var id = $(this).closest('tr').attr('id').split('|');
                var T276_Id = id[0].replace(/T276_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', menuId('MDFe'), '', 'Editar MDFe - ID: ' + T276_Id, '/fis/fis012/content/fis012MdfeCadastro/', '&acaoId=' + encodeURIComponent(T276_Id) + '&tabela={$from}', [990,605]);
			});
			if (typeof(outtime) != 'undefined') {
				clearInterval(outtime);
			}
			outtime = setTimeout(function() {
				divRefresh('{$g['divId']}');
			}, 300000);
		} </script></div>
EOT;
  	break;
  	
	/**
	* @author Ricardo Norio Miyata
	*/
	case 'fis012GridCadastrarMotoristaMdfe':
		$T276_Id = (isset($_REQUEST['T276_Id'])) ? $_REQUEST['T276_Id'] : false;
		$campoParent='T276_Id'; 
		$valorParent=$T276_Id;

		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
            array('colunaValor' => 'livre', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T278_Id', $g['divId'], $campoParent, $valorParent)),
            array('colunaValor' => 'T278_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T278_Nome', 'colunaTitulo' => 'Nome'),
            array('colunaValor' => 'T278_CPF', 'colunaTitulo' => 'CPF'),
        );

		/**
		* Definição da SQL
		*/
		$from = 'T278';

		$extra = <<<EOT
			LEFT JOIN D109 ON D109_Id = T278_D109_Id
			WHERE T278_T276_Id = '{$T276_Id}'
			ORDER BY T278_Id
EOT;
		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;

	/**
	* @author Ricardo Norio Miyata
	*/
	case 'fis012GridCadastrarTipoTransporte':
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$T284_Id = (isset($_REQUEST['T284_Id'])) ? $_REQUEST['T284_Id'] : false;
		$T276_Id = (isset($_REQUEST['T276_Id'])) ? $_REQUEST['T276_Id'] : false;

		// Campos passados para o callback excluir linha para fazer a restrição.
		$campoParent = 'T276'; 
		$valorParent = $T276_Id;
		log("T276_Id no fis012GridCadastrarTipoTransporte: ".$T276_Id);

		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
			array('colunaValor' => 'livre', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T281_Id', $g['divId'], $campoParent, $valorParent)),
			array('colunaValor' => 'T281_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'T281_Flag_Unidade_Transporte', 'colunaTitulo' => 'Tipo de Transporte', 'callback' => 'tipoTransporte'),
			array('colunaValor' => 'T281_Quantidade_Rateada', 'colunaTitulo' => 'Quantidade Rateada'),
			array('colunaValor' => 'T281_Identificacao_Unidade_Transporte', 'colunaTitulo' => 'Identificação'),
			array('colunaValor' => '(SELECT GROUP_CONCAT(DISTINCT CONCAT(T282_Identificacao_Unidade_Carga) SEPARATOR "<br>") FROM T282 WHERE T282_T281_Id = T281_Id) as colunaCargas', 'colunaTitulo' => 'Identificação da Carga', 'colunaAlinhar' => 'left', 'style' => 'font-size:11px;width:1%;', 'callback' => 'colunaCargas'),
			array('colunaValor' => '(SELECT GROUP_CONCAT(DISTINCT CONCAT(D171_Numero_Lacre) SEPARATOR "<br>") FROM D171 LEFT JOIN T287 ON T287_D171_Id = D171_Id WHERE T287_T281_Id = T281_Id) as colunaTransporteLacres', 'colunaTitulo' => 'Número Lacre', 'colunaAlinhar' => 'left', 'style' => 'font-size:11px;width:1%;', 'callback' => 'colunaTipoTransporteLacres'),
        );

		/**
		* Definição da SQL
		*/
		$from = 'T281';
		$extra = <<<EOT
			LEFT JOIN T284 ON T284_Id = T281_T284_Id
			WHERE T284_T276_Id = '{$T276_Id}'
			AND T284_Id = '{$T284_Id}'
			ORDER BY T281_Id
EOT;

		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T281_Id-/, '');
				divSetarAcaoId('{$r_divReload}', id);
				divRefresh('{$r_divReload}');
			});
			$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) {
				return false;
			});
		} </script></div>
EOT;
		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }

        /**
		 * Botões
		 */
		// Definição dos botões
		$botoesDir = '';
		$botoesMid = '';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->display('buttons-floatBottom.tpl');
		// Ações dos botões
		$novoIdTelaForm = uniqid();
	break;

	case 'fis012GridRodoviarioVeiculo':
		$T276_Id = (isset($_REQUEST['T276_Id'])) ? $_REQUEST['T276_Id'] : false;
		$campoParent = 'T276_Id'; 
		$valorParent = $T276_Id;

		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
			array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T283_Id', $g['divId'], $campoParent, $valorParent)),
			array('colunaValor' => 'T283_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T283_Identificacao_Veiculo', 'colunaTitulo' => 'Identificação Veículo'),
			array('colunaValor' => 'T283_Placa', 'colunaTitulo' => 'Placa'),
            array('colunaValor' => 'T283_Renavam', 'colunaTitulo' => 'Renavam'),
            array('colunaValor' => 'T283_Tara', 'colunaTitulo' => 'Tara'),
            array('colunaValor' => 'T283_Capacidade_Kg', 'colunaTitulo' => 'Capacidade(kg)'),
            array('colunaValor' => 'T283_Capacidade_M3', 'colunaTitulo' => 'Capacidade(m³)'),
            array('colunaValor' => 'T283_Flag_Rodado', 'colunaTitulo' => 'Tipo Rodado','callback' => 'validaCampoTipoRodado'),
            array('colunaValor' => 'T283_Flag_Carroceria', 'colunaTitulo' => 'Tipo Carroceria', 'callback' => 'validaCampoCarroceria'),
            array('colunaValor' => 'T283_UF_Licenciamento', 'colunaTitulo' => 'UF Licenciamento'),
            array('colunaValor' => 'T283_D170_Id', 'colunaTitulo' => 'ID Veículo','colunaExibir' => false),
            array('colunaValor' => 'T283_UF_Licenciamento', 'colunaTitulo' => 'ID UF Licenciamento','colunaExibir' => false),
            array('colunaValor' => 'T283_Flag_Carro_Proprio', 'colunaTitulo' => 'Carro Próprio','callback' => 'validaCarroProprio'),
            array('colunaValor' => 'T283_Categoria_Veiculo', 'colunaTitulo' => 'Categoria','callback' => 'validaCampoCategoria'),
            array('colunaValor' => 'T283_CPF_Proprietario', 'colunaTitulo' => 'CPF Proprietário', 'colunaExibir' => false),
            array('colunaValor' => 'T283_CNPJ_Proprietario', 'colunaTitulo' => 'CNPJ Proprietário', 'colunaExibir' => false),
            array('colunaValor' => 'T283_RNTRC', 'colunaTitulo' => 'RNTRC', 'colunaExibir' => false),
            array('colunaValor' => 'T283_Proprietario_Veiculo', 'colunaTitulo' => 'Proprietário do Veículo', 'colunaExibir' => false),
            array('colunaValor' => 'T283_Inscricao_Estadual_Proprietario', 'colunaTitulo' => 'IE', 'colunaExibir' => false),
        );

        /**
		* Definição da SQL
		*/
		$from = 'T283';
		$extra = <<<EOT
			LEFT JOIN D170 ON D170_Id=T283_D170_Id 
			WHERE T283_T276_Id='{$r_T276_Id}'
EOT;

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="fis012GridRodoviarioVeiculobtn">Incluir</button>
			</div>
			
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#fis012GridRodoviarioVeiculobtn').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastrar Veículo', '/fis/fis012/content/fis012ContentIncluirVeiculoModal/', '&tab=geral&T276_Id= '+encodeURIComponent({$r_T276_Id}),  [630,550]);
				});
			} </script></div>
EOT;
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
/* 		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'T283_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Alterar Veículo: ' + acaoId, '/fis/fis012/content/fis012ContentIncluirVeiculoModal/', '&acaoId=' + encodeURIComponent(acaoId) + '&T276_Id= '+encodeURIComponent({$r_T276_Id}), [630,550]);
		        });
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} </script></div>
EOT; */

        echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {            
            acaoClickLinha('{$g['divId']}', function(data, array) {
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Alterar Veículo: ' + encodeURIComponent(array['todosCampos']['T283_Id']), '/fis/fis012/content/fis012ContentIncluirVeiculoModal/', '&acaoId=' + encodeURIComponent(array['todosCampos']['T283_Id']) + '&T276_Id= '+encodeURIComponent({$r_T276_Id}) + '&T283_Flag_Carro_Proprio=' + encodeURIComponent(array['todosCampos']['T283_Flag_Carro_Proprio']) , [630,550]);

            });
		} </script></div>
EOT;

		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;

	/**
	* @author Alexa Mahara
	*/
	case 'fis012GridTecnico':

		$acao_Id = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$grid = array(
            array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T285_Id', $g['divId']), 'colunaExibirNivel' => 4),
			array('colunaValor' => 'T285_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'exibir' => false),
			array('colunaValor' => 'T285_Data_Transmicao', 'colunaTitulo' => 'Data', 'callback' => 'gCorrigeData'),
			array('colunaValor' => 'T285_Retorno_Sefaz', 'colunaTitulo' => 'Log'),
			array('colunaValor' => 'T285_Chave_Acesso_MDFe', 'colunaTitulo' => 'Chave acesso'),
			array('colunaValor' => 'T285_Numero_Recibo_MDFe', 'colunaTitulo' => 'Recibo'),
		);
		
		/**
		 * Definição da SQL
		 */
		$from = "T285";
		
		$sT276 = mysql_query("select T276_Numero_MDFe,T276_C004_Id from T276 where T276_Id={$acao_Id}");
		$mT276=mysql_fetch_array($sT276);
		
		$extra = <<<EOT
					where T285_T276_Id= '$acao_Id'
                    order by T285_Id desc,T285_Data_Transmicao desc
EOT;

		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		//echo $g['ui']->gridFiltroPrint($grid);
		
		
		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);		
		
		$novoIdTelaForm = uniqid();

		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
        if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }	
	
	break;

	/**
	* @author Alexa Mahara
	*/
	case 'fis012GridCadastrarSeguradora':
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$T279_Id = (isset($_REQUEST['T279_Id'])) ? $_REQUEST['T279_Id'] : false;
		$T276_Id = (isset($_REQUEST['T276_Id'])) ? $_REQUEST['T276_Id'] : false;

		$campoParent = 'T276_Id'; 
		$valorParent = $T276_Id;


		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
			array('colunaValor' => 'livre', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T279_Id', $g['divId'], $campoParent, $valorParent)),
			array('colunaValor' => 'T279_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
			array('colunaValor' => 'T279_Seguradora_Nome', 'colunaTitulo' => 'Nome Seguradora'),
			array('colunaValor' => 'T279_Seguradora_CNPJ', 'colunaTitulo' => 'CNPJ'),
			array('colunaValor' => 'T279_Numero_Apolice', 'colunaTitulo' => 'Número de Apólice', 'colunaEditar' => true),
			array('colunaValor' => '(SELECT GROUP_CONCAT(D174_Numero_Averbacao, "<br>") FROM D174 WHERE D174_T279_Id = T279_Id) as colunaSeguradora', 'colunaTitulo' => 'Averbação', 'callback' => 'colunaAverbacao'),
        );

		/**
		* Definição da SQL
		*/
		$from = 'T279';
		$extra = <<<EOT
			WHERE T279_T276_Id = '{$acaoId}'
			ORDER BY T279_Id
EOT;

		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
		$sqlGridFiltro = base64_encode($geradoSql);
		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }

        /**
		 * Botões
		 */
		// Definição dos botões
		$botoesDir = '';
		$botoesMid = '';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->display('buttons-floatBottom.tpl');
		// Ações dos botões
		$novoIdTelaForm = uniqid();
	break;

	/**
	* @author Ricardo Norio Miyata
	*/
	case 'fis012GridCadastrarTipoTransporteCargas':
		$T281_Id = (isset($_REQUEST['T281_Id'])) ? $_REQUEST['T281_Id'] : false;
		// Campos passados para o callback excluir linha para fazer a restrição.
		
		$T276_Id = (isset($_REQUEST['T276_Id'])) ? $_REQUEST['T276_Id'] : false;
		$campoParent = 'T276'; 
		$valorParent = $T276_Id;

		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
            array('colunaValor' => 'livre', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T282_Id', $g['divId'], $campoParent, $valorParent)),
            array('colunaValor' => 'T282_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T282_Identificacao_Unidade_Carga', 'colunaTitulo' => 'Identificação'),
            array('colunaValor' => 'T282_Flag_Unidade_Carga', 'colunaTitulo' => 'Tipo de Carga', 'callback' => 'tipoCarga'),
            array('colunaValor' => 'T282_Quantidade_Rateada', 'colunaTitulo' => 'Quantidade Rateada'),
			array('colunaValor' => '(SELECT GROUP_CONCAT(DISTINCT CONCAT(D171_Numero_Lacre) SEPARATOR "<br>") FROM T288 LEFT JOIN D171 ON D171_Id = T288_D171_Id WHERE T288_T282_Id = T282_Id) as colunaCargaLacres', 'colunaTitulo' => 'Número Lacre', 'colunaAlinhar' => 'left', 'style' => 'font-size:11px;width:1%;', 'callback' => 'colunaTipoTransporteCargaLacres'),
        );

		/**
		* Definição da SQL
		*/
		$from = 'T282';

		$extra = <<<EOT
			LEFT JOIN T281 ON T281_Id = T282_T281_Id
			LEFT JOIN T284 ON T284_Id = T281_T284_Id
			WHERE T284_T276_Id = {$T276_Id}
			AND T282_T281_Id = {$T281_Id}
			ORDER BY T282_Id
EOT;

		/**
		 * Define ação do click de abertura (ao clicar em uma linha do grid)
		 */
		$novoIdTelaForm = uniqid();
		echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T282_Id-/, '');
				divSetarAcaoId('{$r_divReload}', id);
				divRefresh('{$r_divReload}');
			});
			$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) {
				return false;
			});
		} </script></div>
EOT;
		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }

        /**
		 * Botões
		 */
		// Definição dos botões
		$botoesDir = '';
		$botoesMid = '';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->display('buttons-floatBottom.tpl');
		// Ações dos botões
		$novoIdTelaForm = uniqid();
	break;

	/**
	* @author Alexa Mahara
	*/
	case 'fis012GridCadastrarAverbacaoSeguradora':
		$T279_Id = (isset($_REQUEST['T279_Id'])) ? $_REQUEST['T279_Id'] : false;
		$campoParent='T279_Id'; 
		$valorParent=$T279_Id;

		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
            array('colunaValor' => 'livre', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D174_Id', $g['divId'], $campoParent, $valorParent)),
            array('colunaValor' => 'D174_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T279_Seguradora_Nome', 'colunaTitulo' => 'Seguradora'),
            array('colunaValor' => 'D174_Numero_Averbacao', 'colunaTitulo' => 'Número averbação', 'colunaEditar' => true),
        );

		/**
		* Definição da SQL
		*/
		$from = 'D174';

		$extra = <<<EOT
			left join T279 on T279_Id=D174_T279_Id 
			WHERE D174_T279_Id = '{$T279_Id}'
			ORDER BY D174_Id
EOT;
		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }

        /**
		 * Botões
		 */
		// Definição dos botões
		$botoesDir = '';
		$botoesMid = '';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->display('buttons-floatBottom.tpl');
		// Ações dos botões
		$novoIdTelaForm = uniqid();
	break;

	/**
	* @author Ricardo Norio Miyata
	*/
	case 'fis012GridCadastrarTipoTransporteCargaLacre':
		$T282_Id = (isset($_REQUEST['T282_Id'])) ? $_REQUEST['T282_Id'] : false;
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$T276_Id = (isset($_REQUEST['T276_Id'])) ? $_REQUEST['T276_Id'] : false;
		
		$campoParent = 'T276'; 
		$valorParent = $T276_Id;

		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
            array('colunaValor' => 'livre', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T288_Id', $g['divId'], $campoParent, $valorParent)),
            array('colunaValor' => 'T288_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T282_Identificacao_Unidade_Carga', 'colunaTitulo' => 'Identificador da Carga'),
            array('colunaValor' => 'D171_Numero_Lacre', 'colunaTitulo' => 'Número Lacre')
        );

		/**
		* Definição da SQL
		*/
		$from = 'T288';

		$extra = <<<EOT
			LEFT JOIN T282 ON T282_Id = T288_T282_Id
			LEFT JOIN T281 ON T281_Id = T282_T281_Id
			LEFT JOIN T284 ON T284_Id = T281_T284_Id
			LEFT JOIN D171 ON D171_Id = T288_D171_Id
			WHERE T288_T282_Id = '{$T282_Id}'
			AND T284_T276_Id = '{$T276_Id}'
			ORDER BY T288_Id
EOT;

		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;

	/**
	* @author Ricardo Norio Miyata
	*/
	case 'fis012GridCadastrarTipoTransporteLacre':
		$T281_Id = (isset($_REQUEST['T281_Id'])) ? $_REQUEST['T281_Id'] : false;
		$T276_Id = (isset($_REQUEST['T276_Id'])) ? $_REQUEST['T276_Id'] : false;

		$campoParent = 'T276'; 
		$valorParent = $T276_Id;
		/**
		 * Definição do Grid (Tabela)
		 */
		$grid = array(
            array('colunaValor' => 'livre', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T287_Id', $g['divId'], $campoParent, $valorParent)),
            array('colunaValor' => 'T287_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T281_Identificacao_Unidade_Transporte', 'colunaTitulo' => 'Identificador do Transporte'),
            array('colunaValor' => 'D171_Numero_Lacre', 'colunaTitulo' => 'Número Lacre')
        );

		/**
		* Definição da SQL
		*/
		$from = 'T287';

		$extra = <<<EOT
			LEFT JOIN T281 ON T281_Id = T287_T281_Id
			LEFT JOIN T284 ON T284_Id = T281_T284_Id
			LEFT JOIN D171 ON D171_Id = T287_D171_Id
			WHERE T287_T281_Id = '{$T281_Id}'
			AND T284_T276_Id = '{$T276_Id}'
			ORDER BY T287_Id
EOT;

		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $grid, $extra);
		// Imprime o filtro do GRID (normalmente executado acima do GRID)

		/**
		 * Geração: Monta o SQL e retorna o dados
		 */
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }

        /**
		 * Botões
		 */
		// Definição dos botões
		$botoesDir = '';
		$botoesMid = '';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->display('buttons-floatBottom.tpl');
		// Ações dos botões
		$novoIdTelaForm = uniqid();
	break;

	case 'fis012GridIncluirCte':
		global $g;
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$T276_Id = (isset($_REQUEST['T276_Id'])) ? $_REQUEST['T276_Id'] : false;		
		
		$campoParent = 'T276_Id'; 
		$valorParent = $T276_Id;

		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(			
			array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T284_Id', $g['divId'], $campoParent, $valorParent)),
            array('colunaValor' => 'T284_Id', 'colunaTitulo' => 'Id', 'colunaAlinhar' => 'center', 'chavePrimaria' => true),
            array('colunaValor' => 'T284_Flag_Tipo_Documento', 'colunaTitulo' => 'Tipo', 'callback' => 'validaTipo'),
            array('colunaValor' => 'Numero_CTe', 'colunaTipo' => 'livre', 'colunaTitulo' => 'CT-e', 'callback' => 'retornaNumeroPelaChaveAcesso',  'callbackParameter' => array('T284_Chave_De_Acesso_CTe')),
            array('colunaValor' => 'T284_Destinatario_Nome', 'colunaTitulo' => 'Destinatário'),
            array('colunaValor' => 'T284_Valor_Carga', 'colunaTitulo' => 'Total', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2','style' => 'font-weight:bold;background:var(--celula-amarelo-claro)'),
            array('colunaValor' => 'T284_Peso_Bruto_Total', 'colunaTitulo' => 'Peso', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '3', 'colunaEditar' =>true),
            array('colunaValor' => 'D018_UF', 'colunaTitulo' => 'UF Descarregamento','style' => 'width:1%;','colunaAlinhar' => 'center'),
            array('colunaValor' => 'T284_Cidade_Descarregamento', 'colunaTitulo' => 'Cidade Descarregamento'),
            array('colunaValor' => 'T284_Chave_De_Acesso_CTe', 'colunaTitulo' => 'Chave de Acesso', 'colunaExibir'=> false),
		);

        /**
		* Definição da SQL
		*/
		$from = 'T284';
		$sql_ = urlencode(base64_encode($geradoSql));
		$extra = <<<EOT
			LEFT JOIN T007 ON T007_Id = T284_T007_Id
			LEFT JOIN D024 ON D024_Id = T007_D024_Id
			LEFT JOIN D018 ON D018_Id = T284_D018_Id
			WHERE T284_T276_Id = '{$acaoId}' AND T284_Flag_Tipo_Documento = '1'
			ORDER BY T284_Id
EOT;

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="fis012GridMdfeBtnIncluirConhecimento">Incluir CT-e</button>
				<button type="button" id="fis012GridMdfeBtnAlterarDescarregamentoCTe">Alterar Descarregamento</button>
			</div>

			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#fis012GridMdfeBtnIncluirConhecimento').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir de CT-e', '/fis/fis012/content/fis012ContentCadastroCTeMdfe/', '&tab=geral' + '&T276_Id=' + encodeURIComponent('{$acaoId}'),  [600,300]);
				});
				$('#fis012GridMdfeBtnAlterarDescarregamentoCTe').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Alterar Descarregamento', '/fis/fis012/content/fis012ContentAlterarDescarregamento/', '' + '&T276_Id=' + encodeURIComponent('{$acaoId}'),  [400,200]);
				});
			} </script></div>
EOT;
		/**
		* Filtro
		*/

		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

		// Imprime o filtro do GRID (noTabelaPrecolmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		// Totalizador de registros
		$qntCTe = $g['sqlAuto']->pegarSqlCampo("count(T284_Id)", $from, $extra);
		$pesoTotal = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T284_Peso_Bruto_Total)", $from, $extra));
		$valorTotal = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("sum(T284_Valor_Carga)", $from, $extra));

		echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Quantidade CT-e<br><input type="text" value="{$qntCTe}" class="whiteBg" id="" size="11" readonly></span>
				<span class="item" style="text-align:right">Valor Total<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
				<span class="item" style="text-align:right">Peso Total<br><input type="text" value="{$pesoTotal}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;


		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'T284_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Alterar CT-e: ' + acaoId, '/fis/fis012/content/fis012ContentAlterarCTeMdfe/', '&T284_Id=' + encodeURIComponent(acaoId) + '&acaoId=' + encodeURIComponent(acaoId) + '&T276_Id=' + encodeURIComponent({$T276_Id}), [750,650]);
		        });
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} </script></div>
EOT;

		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
	break;

	case 'fis012GridInfoPgtoFrete':
		global $g;
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$T276_Id = (isset($_REQUEST['T276_Id'])) ? $_REQUEST['T276_Id'] : false;

		/**
		 * Definição do Grid (Tabela)
		*/
		// Define o callback do grid para traduzir o número do indicador de forma de pagamento
		function getIndicadorFormaPagamento($dados) {
			switch ($dados['todosCampos']['T318_Indicador_Forma_Pagamento']) {
				case '0': return '0 - Pagamento à Vista';
				case '1': return '1 - Pagamento à Prazo';
				default: return '';
			}
		};

		$grid = array(			
			array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T318_Id', $g['divId'], 'T276', $T276_Id), 'colunaExibir' => true),
            array('colunaValor' => 'T318_Id', 'colunaTitulo' => 'Id', 'colunaAlinhar' => 'center', 'chavePrimaria' => true),
			array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Resp. Pgto.', 'colunaAlinhar' => 'left'),
			array('colunaValor' => 'D024_CNPJ', 'colunaTitulo' => 'CNPJ', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'D024_CPF', 'colunaTitulo' => 'CPF', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T318_Valor_Contrato', 'colunaTitulo' => 'Valor Contrato', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'T318_Indicador_Forma_Pagamento', 'colunaTitulo' => 'Forma Pagto', 'colunaAlinhar' => 'center', 'callback' => 'getIndicadorFormaPagamento'),
			array('colunaValor' => 'T318_CIOT', 'colunaTitulo' => 'CIOT', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T318_Codigo_Banco', 'colunaTitulo' => 'Cód. Banco', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T318_Codigo_Agencia', 'colunaTitulo' => 'Cód. Agência', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T318_Chave_Pix', 'colunaTitulo' => 'Chave Pix', 'colunaAlinhar' => 'left'),
			array('colunaValor' => 'T318_CNPJ_Instituicao_Pagamento_Frete', 'colunaTitulo' => 'CNPJ I. Pgto. Frete', 'colunaAlinhar' => 'center'),
		);

        /**
		* Definição da SQL
		*/
		$from = 'T318';
		$sql_ = urlencode(base64_encode($geradoSql));
		$extra = <<<SQL
			LEFT JOIN D024 ON T318_D024_Id = D024_Id
			WHERE T318_T276_Id = '{$acaoId}'
		SQL;

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<JS
			<div class="buttonsBottomTop">
				<button type="button" id="fis012IncluirInfoPgtoFreteBtn">Incluir</button>
			</div>

			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#fis012IncluirInfoPgtoFreteBtn').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Info Pgto. Frete', '/fis/fis012/content/fis012FormInfoPgtoFrete/', '&tab=geral' + '&T276_Id=' + encodeURIComponent('{$acaoId}'),  [348, 278]);
				});
			} </script></div>
		JS;

		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (noTabelaPrecolmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		echo <<<JS
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'T318_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Alterar Info Pgto. Frete: ' + acaoId, '/fis/fis012/content/fis012AlterarInfoPgtoFrete/', '&T318_Id=' + encodeURIComponent(acaoId) + '&acaoId=' + encodeURIComponent(acaoId), [750,650]);
				});
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} </script></div>
		JS;

		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
		break;

	case 'fis012GridInfoPgtoFreteComponente':
		global $g;
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$T318_Id = (isset($_REQUEST['T318_Id'])) ? $_REQUEST['T318_Id'] : false;

		/**
		 * Definição do Grid (Tabela)
		*/
		// Define o callback do grid para traduzir o tipo do componente
		function getTipoComponente($dados) {
			switch ($dados['todosCampos']['T319_Tipo']) {
				case '01': return '01 - Vale Pedágio';
				case '02': return '02 - Impostos, taxas e contribuições';
				case '03': return '03 - Despesas (bancárias, meios de pagamento, outras)';
				case '99': return '99 - Outros';
				default: return '';
			}
		};
		$grid = array(			
			array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T319_Id', $g['divId'], 'T318', $T318_Id), 'colunaExibir' => true),
            array('colunaValor' => 'T319_Id', 'colunaTitulo' => 'Id', 'colunaAlinhar' => 'center', 'chavePrimaria' => true),
			array('colunaValor' => 'T319_Descricao', 'colunaTitulo' => 'Descrição', 'colunaAlinhar' => 'left'),
			array('colunaValor' => 'T319_Valor', 'colunaTitulo' => 'Valor', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'T319_Tipo', 'colunaTitulo' => 'Tipo', 'colunaAlinhar' => 'center', 'callback' => 'getTipoComponente'),
		);

        /**
		* Definição da SQL
		*/
		$from = 'T319';
		$sql_ = urlencode(base64_encode($geradoSql));
		$extra = <<<SQL
			WHERE T319_T318_Id = '{$acaoId}'
		SQL;

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<JS
			<div class="buttonsBottomTop">
				<button type="button" id="fis012IncluirInfoPgtoFreteComponenteBtn">Incluir</button>
			</div>

			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#fis012IncluirInfoPgtoFreteComponenteBtn').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Componente', '/fis/fis012/content/fis012FormInfoPgtoFreteComponente/', '&tab=geral' + '&T318_Id=' + encodeURIComponent('{$acaoId}'),  [478, 186]);
				});
			} </script></div>
		JS;

		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (noTabelaPrecolmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		echo <<<JS
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'T319_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Alterar Componente: ' + acaoId, '/fis/fis012/content/fis012FormInfoPgtoFreteComponente/', '&T319_Id=' + encodeURIComponent(acaoId) + '&acaoId=' + encodeURIComponent(acaoId), [478, 186]);
				});
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} </script></div>
		JS;

		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
		break;

	case 'fis012GridInfoPgtoFreteParcelas':
		global $g;
		$acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
		$T318_Id = (isset($_REQUEST['T318_Id'])) ? $_REQUEST['T318_Id'] : false;

		/**
		 * Definição do Grid (Tabela)
		*/
		// Define o callback do grid para traduzir o tipo do componente

		$grid = array(			
			array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T320_Id', $g['divId'], 'T318', $T318_Id), 'colunaExibir' => true),
            array('colunaValor' => 'T320_Id', 'colunaTitulo' => 'Id', 'colunaAlinhar' => 'center', 'chavePrimaria' => true),
			array('colunaValor' => 'T320_Numero_Parcela', 'colunaTitulo' => 'Nº Parcela', 'colunaAlinhar' => 'center'),
			array('colunaValor' => 'T320_Valor_Parcela', 'colunaTitulo' => 'Valor R$', 'colunaAlinhar' => 'right', 'colunaCasasDecimais' => '2'),
			array('colunaValor' => 'T320_Data_Vencimento', 'callback' => 'gCorrigeData', 'colunaTitulo' => 'Vencimento', 'colunaAlinhar' => 'center'),
		);

        /**
		* Definição da SQL
		*/
		$from = 'T320';
		$sql_ = urlencode(base64_encode($geradoSql));
		$extra = <<<SQL
			WHERE T320_T318_Id = '{$acaoId}'
		SQL;

		/**
		* Botões (Versão Acima do GRID)
		*/
		echo <<<JS
			<div class="buttonsBottomTop">
				<button type="button" id="fis012IncluirInfoPgtoFreteParcelaBtn">Incluir</button>
			</div>

			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
				$('#fis012IncluirInfoPgtoFreteParcelaBtn').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Parcela', '/fis/fis012/content/fis012FormInfoPgtoFreteParcela/', '&tab=geral' + '&T318_Id=' + encodeURIComponent('{$acaoId}'),  [247, 191]);
				});
			} </script></div>
		JS;

		/**
		* Filtro
		*/
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		// Imprime o filtro do GRID (noTabelaPrecolmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);
		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		/**
		* Define ação do click de abertura (ao clicar em uma linha do grid)
		*/
		echo <<<JS
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'T320_Id';	
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Alterar Parcela: ' + acaoId, '/fis/fis012/content/fis012FormInfoPgtoFreteParcela/', '&T320_Id=' + encodeURIComponent(acaoId) + '&acaoId=' + encodeURIComponent(acaoId), [247, 191]);
				});
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} </script></div>
		JS;

		/**
		 * Geração: Grid
		 */
		$gridTabela = $g['ui']->grid($grid, $geradoDados);
		$g['smarty']->assign('thead', $gridTabela['thead']);
		$g['smarty']->assign('tbody', $gridTabela['tbody']);
		$g['smarty']->assign('tfoot', $gridTabela['tfoot']);
		$g['smarty']->assign('gridId', md5($g['r_']));
		$g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
		gBotaoAuditoria($from);
		if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
		break;
}
?>
