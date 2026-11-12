<?php
namespace hardness;
include 'prog/sistema/funcoes/pesquisaGridCallback.php';

/**
* Grid de pesquisa para o campo do formulário tipo "pesquisaGrid"
*/
switch($g['r'][3]){
    case 'verificaCep':
        require_once('bibliotecas/classes/CAD001.php');
        $CAD001 = new CAD001();
        $resposta = array('code' => true, 'data' => array());

        $cep = isset($r_CEP) ? $r_CEP : false;
        $W004_Id = isset($r_idCep) ? $r_idCep : false;
        $whereW004 = ($W004_Id>0 ? " WHERE W004_Id ='{$W004_Id}'" : " WHERE W004_Cep ='{$cep}'");

        $sql = mysql_query("SELECT * FROM global.W004 {$whereW004}");
        $result = mysql_fetch_array($sql);
        
        $sql2 = mysql_query("SELECT D020_Id FROM D020 WHERE D020_Codigo_IBGE ='{$result['W004_IBGE_Cidade']}'");
        $result2 = mysql_fetch_array($sql2);
        
        $sql3 = mysql_query("SELECT D018_Id FROM D018 WHERE D018_Codigo_IBGE ='{$result['W004_IBGE_UF']}'");
        $result3 = mysql_fetch_array($sql3);
        
        $sql4 = mysql_query("SELECT D030_Id FROM D030 WHERE D030_Nome_Pais LIKE '%BRASIL%'");
        $result4 = mysql_fetch_array($sql4);

        
        // Se não encontra faz as buscas nos webservices
        if(mysql_num_rows($sql) <= 0){
            $retorno = $CAD001->pesquisaTerceirosByjg($cep);
            if($retorno['Cep']>0){
                
                $retorno['Logradouro'] = explode('-', $retorno['Logradouro']);
                $retorno['Logradouro'] = $retorno['Logradouro'][0];

                // Se encontra atualiza a tabela W004 da base global
                $insW004=mysql_query("INSERT INTO global.W004 (
                    W004_Logradouro,
                    W004_Bairro,
                    W004_Cep,
                    W004_Cidade,
                    W004_UF,
                    W004_IBGE_Cidade,
                    W004_IBGE_UF
                ) VALUES (
                    '{$retorno['Logradouro']}',
                    '{$retorno['Bairro']}',
                    '{$retorno['Cep']}',
                    '{$retorno['Cidade']}',
                    '{$retorno['UF']}',
                    '{$retorno['IBGE_Cidade']}',
                    '{$retorno['IBGE_UF']}'
                );");

                $sql2 = mysql_query("SELECT D020_Id FROM D020 WHERE D020_Codigo_IBGE ='{$retorno['IBGE_Cidade']}'");
                $result2 = mysql_fetch_array($sql2);
                
                $sql3 = mysql_query("SELECT D018_Id FROM D018 WHERE D018_Codigo_IBGE ='{$retorno['IBGE_UF']}'");
                $result3 = mysql_fetch_array($sql3);
                
                $sql4 = mysql_query("SELECT D030_Id FROM D030 WHERE D030_Nome_Pais LIKE '%BRASIL%'");
                $result4 = mysql_fetch_array($sql4);

                $result['W004_Logradouro'] = $retorno['Logradouro'];
                $result['W004_Cep'] = $cep;
                $result['W004_Bairro'] = $retorno['Bairro'];

            } else {
                $consultaCep = $CAD001->consultaCep($cep);
                log("consultaCep: ".json_encode($consultaCep));

                if ($consultaCep[0] === true) {
                    $insW004=mysql_query("INSERT INTO global.W004 (
                        W004_Logradouro,
                        W004_Bairro,
                        W004_Cep,
                        W004_Cidade,
                        W004_UF,
                        W004_IBGE_Cidade,
                        W004_IBGE_UF
                    ) VALUES (
                        '{$consultaCep[1]['logradouro']}',
                        '{$consultaCep[1]['bairro']}',
                        '{$consultaCep[1]['cep']}',
                        '{$consultaCep[1]['localidade']}',
                        '{$consultaCep[1]['uf']}',
                        '{$consultaCep[1]['ibge']}',
                        '{$consultaCep[1]['ddd']}'
                    );");

                    $result2 = mysql_fetch_assoc(mysql_query("SELECT D020_Id FROM D020 WHERE D020_Codigo_IBGE ='{$consultaCep[1]['ibge']}' AND D020_D018_Id	> 0"));
                    $result3 = mysql_fetch_assoc(mysql_query("SELECT D018_Id FROM D018 WHERE D018_UF ='{$consultaCep[1]['uf']}'"));
                    $result4 = mysql_fetch_assoc(mysql_query("SELECT D030_Id FROM D030 WHERE D030_Nome_Pais LIKE '%BRASIL%'"));

                    $result['W004_Logradouro'] = $consultaCep[1]['logradouro'];
                    $result['W004_Cep'] = $cep;
                    $result['W004_Bairro'] = $consultaCep[1]['bairro'];
                }
            }
        }

        if((mysql_num_rows($sql) > 0) || ($retorno['Cep'] > 0 || $consultaCep[0] === true)){
            $resposta['code'] = true;
            $resposta['data']['EnderecoDb'] = $result['W004_Logradouro'];
            $resposta['data']['Cep'] = $result['W004_Cep'];
            $resposta['data']['BairroDb'] = $result['W004_Bairro'];
            //$resposta['data']['CidadeDb'] = $result2['D020_Id'];
            $resposta['data']['UfDb'] = $result3['D018_Id'];
            $resposta['data']['PaisDb'] = $result4['D030_Id'];
            $resposta['data']['CidadeDb'] = gProcessaSelect(gGeraSelect('D020', 'D020_Id', 'D020_Nome_Cidade', 'WHERE D020_D018_Id = '.$result3['D018_Id'].' AND D020_Id='.$result2['D020_Id'].' ORDER BY D020_Nome_Cidade'));
        } else {
            $resposta['code'] = false;
            $resposta['msg'] = $consultaCep[1];
        }

        echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
        
	break;
    
    case 'content':
        
        $content1 = uniqid();
        $g['smarty']->assign('content1', $content1);
        $g['smarty']->assign('tipo', 'unico');
        // O que será carregado em cada conteúdo?
        $actions = array(
        // identificador, url, paramentros get (opcional)
        array($content1, '/sistema/funcoes/pesquisaCep/tab/', '&CEP=' . $r_CEP . '&camposAtualizar=' . $r_camposAtualizar)
        );
        echo gProcessaAcoes($actions);

    break;
    
    case 'tab':
             
        // Form (caso seja o mesmo form em multiplas tabs)
        //$formId = uniqid();
        //$g['smarty']->assign('formId', $formId);

        // Definição das Tabs
        $tabs = array();
        $tabsList = array(
        // Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
        array(uniqid(), '/sistema/funcoes/pesquisaCep/grid/', 'Busca Endereços', '&CEP=' . $r_CEP . '&camposAtualizar=' . $r_camposAtualizar),
        );

        // Processa Tabs
        echo gProcessaTabs($tabsList, $tabs);

        // Carrega Template
        $g['smarty']->assign('tabs', $tabs);
        $g['smarty']->display('tab.tpl');
    break;
    
    case 'grid':

        /**
         * Definição do Grid (Tabela)
         */        

        $grid = array(
            //array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('W004_Id', $g['divId'])),
            array('colunaValor'=>'W004_Id', 'colunaTitulo'=>'Id', 'colunaExibir' => false, 'chavePrimaria'=>true, 'colunaAlinhar' => 'center'),
            array('colunaValor'=>'W004_Cep', 'colunaTitulo'=>'Cep', 'colunaAlinhar' => 'center'),
            array('colunaValor'=>'W004_Logradouro', 'colunaTitulo'=>'Endereço'),
            array('colunaValor'=>'W004_Bairro', 'colunaTitulo'=>'Bairro'),
            array('colunaValor'=>'W004_Cidade', 'colunaTitulo'=>'Cidade'),
            array('colunaValor'=>'W004_UF', 'colunaTitulo'=>'UF', 'colunaAlinhar' => 'center'),            
        );
        
        /**
        * Definição da SQL
        */
        $from = 'global.W004';
        $extra = <<<EOT
            ORDER BY W004_Cep

EOT;

        /**
        * Botões (Versão Acima do GRID)
        
        echo <<<EOT
            <div class="buttonsBottomTop">
                <button type="button" id="cad001Incluir">Incluir</button>
            </div>
            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {        
                $('#cad001Incluir').unbind('click').bind('click', function(data) {
                    abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Clientes/Fornecedores', '/cad/cad001/content/cad001contentCadastro/','', [950,550]);
                });
            } </script></div>
EOT;
        */
        /**
        * Filtro
        */
        $gridFiltro = array(
            'W004_Cep' => array('titulo' => 'Cep', 'tipo' => 'texto', 'valor' => isset($r_CEP) && $r_CEP != 'filtro' ? $r_CEP : ''),
            'W004_Logradouro' => array('titulo' => 'Endereço', 'tipo' => 'texto'),
            'W004_Bairro' => array('titulo' => 'Bairro', 'tipo' => 'texto'),
            'W004_Cidade' => array('titulo' => 'Cidade', 'tipo' => 'texto'),
            'W004_UF' => array('titulo' => 'UF', 'tipo' => 'texto'),
            
        );
        if (isset($r_CEP) && $r_CEP != 'filtro') {
            $g['ui']->gridFiltroLimpar();
        }
        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro);
        
        /**
         * Geração: Monta o SQL e retorna o dados
         */
        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
        

        /**
        * Botões (Versão Acima do GRID)
        
        *$valorTotal = $g['sqlAuto']->pegarSqlCampo("count(*)", $from, $extra);
        
        echo <<<EOT
            <div class="buttonsBottomTop">
                <span class="item" style="text-align:right">Total Empresas<br><input type="text" value="{$valorTotal}" class="whiteBg" id="" size="11" readonly></span>
                <div style="clear:both;"></div>
            </div>
EOT;
        */
        
        /**
         * Define ação do click de abertura (ao clicar em uma linha do grid)
         */
        $novoIdTelaForm = uniqid();
        echo <<<EOT
        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
            $('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
                if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
                var id = $(this).closest('tr').attr('id').replace(/W004_Id-/, '');
                pesquisaCep(id,'{$g['divIdParent']}','{$g['divIdRoot']}',false,'{$r_camposAtualizar}');
                $("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
            });
            divSetarAcaoId('{$g['divId']}', 'filtro', 'CEP');
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
}
?>