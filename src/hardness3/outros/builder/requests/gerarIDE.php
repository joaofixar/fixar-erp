<?php
namespace hardness;
require_once('../conf.php');
if (empty($g['projeto'])) die('Erro ao carregar projeto.');

if (empty($g['projeto']['forms']) && empty($g['projeto']['grids'])) {
    echo json_encode([
        "erro" => "Nenhum Grid ou Form para ser gerado."
    ]);
}

$menu = '';

// FORM
foreach ($g['projeto']['forms'] as $form) {
    // Definições
    $md5 = md5(uniqid());
    $g['projeto']['forms'][$form['id']]['md5'] = $md5; // salva para uso futuro no grid
    $formNovo = array(
        'nome' => null,
        'tabela' => $form["tabela"],
        'botoes' => true,
        'md5' => md5("sistema/personalizacao/form/{$md5}/"),
    );

    // Nome - encontra próximo
    $nome = strtolower(preg_replace('/[[:space:]]+/', ' ', gLimpaAcentosPuro($form["nome"])));
    $formNovo['nome'] = $nome;
    $count = 1;
    $forms = Personalizacoes::listarFormsNovos();
    foreach ($forms as $nomeArquivo) {
        $form_ = Personalizacoes::getFormNovo($nomeArquivo);
        $nome_ = preg_replace('/\-[0-9]+$/', '', $form_['nome']);
        //if (stripos($nome_, $nome) !== false) {
        if (strtolower(trim($nome_)) == strtolower(trim($nome))) {
            $count++;
        }
    }
    if ($count > 1) {
        $formNovo['nome'] = $nome . '-' . $count;
    }

    // Código
    $codigo = '';
    foreach ($form["form"] as $campo) {
        switch ($campo['tipo']) {
            case "Select":
                $codigo .= <<<EOT
tab [geral]
field [{$form["tabela"]}_{$campo["tabela"]}_Id]
name [{$form["tabela"]}_{$campo["tabela"]}_Id]
label [{$campo["titulo"]}]
type [select]
EOT;
                // Utilizado callbackPreencherSelect
                $dir = Personalizacoes::getDirPersonalizacoesForm();
                $tabela = str_replace('/', '-', trim("/sistema/personalizacao/form/{$md5}/", '/'));
                $arquivo = $dir . $tabela . '-' . "{$form["tabela"]}_{$campo["tabela"]}_Id" . '-callbackPreencherSelect';
                $campos = implode(",", $campo["campo"]);
                $codigoCallback = <<<EOT
<?php
/**
 * Código executado para preencher a opção "select" de campos "type [select]" 
 * Variáveis:
 *      \$array['campoAtual'], \$array['todosCampos'], \$g, \$array['{parametro}']
 */
return gGeraSelect('{$campo["tabela"]}', '{$campo["tabela"]}_Id', '{$campos}', '', true);
EOT;
                file_put_contents($arquivo, $codigoCallback);
                break;
            case "Flag":
                $codigo .= <<<EOT
tab [geral]
field [{$campo['campo']}]
name [{$campo['campo']}]
label [{$campo["titulo"]}]
type [select]
EOT;
                // Utilizado callbackPreencherSelect
                $dir = Personalizacoes::getDirPersonalizacoesForm();
                $tabela = str_replace('/', '-', trim("/sistema/personalizacao/form/{$md5}/", '/'));
                $arquivo = $dir . $tabela . '-' . "{$campo['campo']}" . '-callbackPreencherSelect';
                $campos = implode(",", $campo["campo"]);

                // Valores únicos do campo tipo Flag
                $arrayRetorno = "array('title' => '...', 'value' => ''),";
                $query = "SELECT DISTINCT {$campo['campo']} FROM {$form["tabela"]} ORDER BY 1 ASC;";
                $resultado = mysqli_fetch_all(mysqli_query($query));
                foreach ($resultado as $valor) {
                    $valor = $valor[0];
                    if (empty($valor)) { continue; }
                    $nome = $valor;
                    if ($nome == 'S') { $nome = 'Sim'; }
                    if ($nome == 'N') { $nome = 'Não'; }
                    $arrayRetorno .= "\n\tarray('title' => '{$nome}', 'value' => '{$valor}'),";
                }

                $codigoCallback = <<<EOT
<?php
/**
 * Código executado para preencher a opção "select" de campos "type [select]" 
 * Variáveis:
 *      \$array['campoAtual'], \$array['todosCampos'], \$g, \$array['{parametro}']
 */
return array(
    {$arrayRetorno}
);
EOT;
                file_put_contents($arquivo, $codigoCallback);
                break;
            case "Moeda":
                $codigo .= <<<EOT
tab [geral]
field [{$campo['campo']}]
name [{$campo['campo']}]
label [{$campo["titulo"]}]
type [moeda]
EOT;
                break;
            case "Data":
                $codigo .= <<<EOT
tab [geral]
field [{$campo['campo']}]
name [{$campo['campo']}]
label [{$campo["titulo"]}]
type [data]
EOT;
                break;
            default:
                $codigo .= <<<EOT
tab [geral]
field [{$campo['campo']}]
name [{$campo['campo']}]
label [{$campo["titulo"]}]
type [text]
EOT;
                break;
        }
        foreach ($campo["opcoes"] as $opcao) {
            if ($opcao["nome"] == "casasDecimais") {
                $codigo .= "\ncasasDecimais [{$opcao["valor"]}]";
            }
        }
        $codigo .= "\n---\n";
    }

    // Salvar
    $formNovo['nome'] = ucwords($formNovo['nome']);
    $arquivo = Personalizacoes::getDirForms() . $md5;
    file_put_contents($arquivo, serialize($formNovo));
    // Personalização
    Personalizacoes::salvarPersonalizacaoForm($formNovo['md5'], $codigo);
}

// GRIDS
foreach ($g['projeto']['grids'] as $grid) {
    // Definições
    $md5 = md5(uniqid());
    $gridNovo = array(
        'nome' => null,
        'md5' => md5("sistema/personalizacao/grid/{$md5}/"),
        'from' => $grid["tabelas"][0],
        'extra' => null,
        'grid' => null,
        'botoes' => null,
        'gridFiltro' => null,
        'js' => null,
        'criadoViaSQL' => true,
        'sql' => '',
    );
    $sqlCampos = [];

    // Nome - encontra próximo
    $nome = strtolower(preg_replace('/[[:space:]]+/', ' ', gLimpaAcentosPuro($grid["nome"])));
    $gridNovo['nome'] = $nome;
    $count = 1;
    $grids = Personalizacoes::listarGridsNovos();
    foreach ($grids as $nomeArquivo) {
        $grid_ = Personalizacoes::getGridNovo($nomeArquivo);
        $nome_ = preg_replace('/\-[0-9]+$/', '', $grid_['nome']);
        //if (stripos($nome_, $nome) !== false) {
        if (strtolower(trim($nome_)) == strtolower(trim($nome))) {
            $count++;
        }
    }
    if ($count > 1) {
        $gridNovo['nome'] = $nome . '-' . $count;
    }

    // Grid
    $grid_ = array(
        array('colunaValor' => "{$grid["tabelas"][0]}_Id", 'colunaTitulo' => 'ID', 'chavePrimaria' => true, 'colunaExibir' => false),
    );
    $sqlCampos[] = "{$grid["tabelas"][0]}_Id as `ID`";
    // Campos
    foreach ($grid["grid"] as $campo) {
        $campoNovo = array('colunaValor' => $campo["campo"], 'colunaTitulo' => $campo["titulo"]);
        foreach (explode(',', $campo["opcoes"]) as $opcao) {
            if ($opcao == "colunaEditar") {
                $campoNovo['colunaEditar'] = true;
            } else if ($opcao == "colunaExibirTitulo") {
                $campoNovo['colunaExibirTitulo'] = true;
            } else if ($opcao == "colunaExibir") {
                $campoNovo['colunaExibir'] = false;
            }
        }
        if ($campo["tipo"] == "Data" || $campo["tipo"] == "DataHora") {
            $campoNovo['callback'] = 'gCorrigeData';
        }
        $grid_[] = $campoNovo;
        $sqlCampos[] = "{$campo["campo"]} as `{$campo["titulo"]}`";
    }
    $gridNovo['grid'] = $grid_;

    // Extra
    $extra = '';
    $ultimaTabela = '';
    foreach ($grid["tabelas"] as $key => $tabela) {
        if (!$ultimaTabela) {
            $ultimaTabela = $tabela;
        } else {
            $tabelaJoin = $grid["tabelasJoin"][$key] ? $grid["tabelasJoin"][$key] : $ultimaTabela;
            $extra .= "LEFT JOIN {$tabela} ON {$tabela}_Id={$tabelaJoin}_{$tabela}_Id\n";
        }
    }
    if (!empty($extra)) {
        $extra = "-- JOIN\n{$extra}";
    }
    $gridNovo['extra'] = $extra;

    // Filtros
    $gridFiltro = array();
    foreach ($grid["grid"] as $item) {
        if (in_array('filtro', explode(',', $item["opcoes"]))) {
            $tipo = "texto";
            switch ($item["titulo"]) {
                case "Data":
                case "DataHora":
                    $tipo = "data";
                    break;
                case "Inteiro":
                case "Decimal":
                    $tipo = "numero";
                    break;
            }
            $gridFiltro[$item["campo"]] = array('titulo' => $item["titulo"], 'tipo' => $tipo);
        }
    }
    $gridNovo['gridFiltro'] = $gridFiltro;

    // Botões
    if ($grid['form']) {
        $botoes = array(
            array('titulo' => 'Incluir', 'id' => $grid['form'], 'janelaUrl' => "/sistema/personalizacao/content/{$g['projeto']['forms'][$grid['form']]['md5']}/", 'janelaTamanho' => '[800,550]'),
        );
        $gridNovo['botoes'] = $botoes;
    }

    // JS
    $gridNovo['js'] = <<<EOT
acaoClickLinha('[[divId]]', function(data, array) {
    // array['todosCampos'], array['divId'], array['divIdRoot'], array['divIdParent']
    abrirJanela(data, array['divIdRoot'], array['divId'], unique(), '', 'Alterar {$gridNovo['nome']}: ' + array['todosCampos']['{$grid["tabelas"][0]}_Id'], '/sistema/personalizacao/content/{$g['projeto']['forms'][$grid['form']]['md5']}/', '&acao=alterar&acaoId=' + encodeURIComponent(array['todosCampos']['{$grid["tabelas"][0]}_Id']), [600,300]);
});
EOT;

    // SQL
    $gridNovo['sql'] = 'SELECT ' . implode(', ', $sqlCampos) . " FROM {$gridNovo['from']}";
    if (!empty($gridNovo['extra'])) {
        $gridNovo['sql'] .= "\n{$gridNovo['extra']}";
    }

    // Salvar
    $gridNovo['nome'] = ucwords($gridNovo['nome']);
    $arquivo = Personalizacoes::getDirGrids() . $md5;
    file_put_contents($arquivo, serialize($gridNovo));

    // Menu
    $menu .= <<<EOT
// \$menu['{$g['projeto']["nome"]}']['{$gridNovo['nome']}'] = "abrirJanela(event, divIdRootAberto(), '', menuId('{$md5}'), 'Grid: {$gridNovo['nome']}', '{$gridNovo['nome']}', '/sistema/personalizacao/content/{$md5}/', '', 'auto', false, 'center');";

EOT;

//    if ($grid['form']) {
//        $case .= <<<'EOT2'
//            echo <<<EOT
//                <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
//                    var dbclickPrevent = false;
//                    $('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
//                        // Previne duplo click (abrir duas janelas)
//                        if (dbclickPrevent) { return false; }
//                        dbclickPrevent = true;
//                        setTimeout(function() { dbclickPrevent = false; }, 500);
//                        // (substituir pelo mesmo ID da chave primária do grid)
//                        var chavePrimaria = '***TABELA_PRIMARIA***_Id';
//                        if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
//                        var id = $(this).closest('tr').attr('id').split('|');
//                        var acaoId = id[0].replace(chavePrimaria + '-', '');
//                        // Ação: abrir nova janela
//                        abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Alterar ***NOME***: ' + acaoId, '/per/***DIR***/content/***CONTENT_FORM***/', '&acao=alterar&acaoId=' + encodeURIComponent(acaoId), [600,300]);
//                    });
//                    $('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
//                } </script></div>
//EOT;
//
//
//EOT2;
//
//    }

}

// Menu
if ($_SESSION['modoDesenvolvedor']) {
    mkdir("ide/personalizacaoConf");
    $arquivoMenu = "ide/personalizacaoConf/GLOBAL-menuBuilder.php";
} else if ($_SESSION['modoDesenvolvedorApp']) {
    mkdir(Apps::getDirEditor() . "personalizacaoConf");
    $arquivoMenu = Apps::getDirEditor() . "personalizacaoConf/GLOBAL-menuBuilder.php";
} else {
    mkdir("{$g['pathDados']}/personalizacaoConf");
    $arquivoMenu = "{$g['pathDados']}/personalizacaoConf/GLOBAL-menuBuilder.php";
}
if (!file_exists($arquivoMenu)) {
    $tmp = <<<'DEFAULT'
<?php
namespace hardness;
/**
* Alterações do menu, os itens aqui são incluídos automaticamente pelo Fast Crud
*/

DEFAULT;
    file_put_contents($arquivoMenu, $tmp);
}
file_put_contents($arquivoMenu, "\n" . $menu, FILE_APPEND);

echo json_encode([
    "erro" => false,
]);
die();
