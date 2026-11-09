<?php
/**
 * automacaoFormulario.php
 *
 * Versão legível de sistema-legado/sistema/automacaoFormulario.php.
 * O original foi ofuscado com desvios; aqui o fluxo foi reconstruído
 * em if/else/while/foreach, no mesmo comportamento.
 * Este arquivo é referência de leitura e não é carregado pelo sistema.
 */
namespace hardness;

class Formulario
{
    public $formNome = 'form_padrao';

    public $formArray = array();

    public $formArrayValues = array();

    private $formTabId = array();

    public $acaoId = false;

    private $formEditorCache = array();

    public $tabela;

    public $formArrayOriginal = array();

    public $formArrayValuesOriginal = array();

    public $formArrayTodosCampos = array();

    function __construct($formNome = '')
    {
        global $r_acaoId;
        $this->acaoId = $r_acaoId;
        $this->formNome = !empty($formNome) ? $formNome : $this->formNome;
    }

    public function definirFormularioArray($formArray)
    {
        global $g, $r_pegarForm;
        $formId = md5(trim($g['r_'], '/') . '/');
        apc_store("form-php-{$formId}", serialize($formArray), 3600);
        $this->formArray = $formArray;
        $arquivoIde = 'ide/personalizacaoForm/' . str_replace('/', '-', trim(@$g['r_'], '/')) . '-novoCampo';
        $arquivoApps = Apps::getDirAtivos() . "personalizacaoForm/" . str_replace('/', '-', trim(@$g['r_'], '/')) . '-novoCampo';
        $arquivoAppsEditor = Apps::getDirEditor() . "personalizacaoForm/" . str_replace('/', '-', trim(@$g['r_'], '/')) . '-novoCampo';
        $arquivoGlobal = "{$g['pathDados']}personalizacaoForm/" . str_replace('/', '-', trim(@$g['r_'], '/')) . '-novoCampo';
        $arquivos = [];
        if ($g['modoDesenvolvedor']) {
            $arquivos[] = $r_pegarForm ? null : $arquivoIde;
        } else {
            if ($g['modoDesenvolvedorApp']) {
                $arquivos[] = $arquivoIde;
                $arquivos[] = $arquivoApps;
                $arquivos[] = $r_pegarForm ? null : $arquivoAppsEditor;
            } else {
                $arquivos[] = $arquivoIde;
                $arquivos[] = $arquivoApps;
                $arquivos[] = $r_pegarForm ? null : $arquivoGlobal;
            }
        }
        $arquivos = array_filter($arquivos);
        foreach ($arquivos as $arquivoPersonalizacaoNovoCampo) {
            if (!(!file_exists($arquivoPersonalizacaoNovoCampo))) {
                include $arquivoPersonalizacaoNovoCampo;
                $this->formArray = array_replace_recursive($this->formArray, $formArray);
            }
        }
        if ($g['modoDesenvolvedor']) {
            if (!$r_pegarForm) {
                $personalizado = Personalizacoes::processarForm($formId, array('desenvolvedor'));
            }
        } else {
            if ($g['modoDesenvolvedorApp']) {
                $personalizado = Personalizacoes::processarForm($formId, $r_pegarForm ? array('desenvolvedor', 'apps') : array('desenvolvedor', 'apps', 'apps-editor'));
            } else {
                $personalizado = Personalizacoes::processarForm($formId, $r_pegarForm ? array('desenvolvedor', 'apps') : array('desenvolvedor', 'apps', 'global', 'perfil', 'usuario'));
            }
        }
        foreach ($personalizado as $tab => $tabCampos) {
            foreach ($tabCampos as $item) {
                $id = $item['field'];
                if (isset($this->formArray['campos'][$tab][$id])) {
                    $this->formArray['campos'][$tab][$id] = array_merge($this->formArray['campos'][$tab][$id], $item);
                } else {
                    $this->formArray['campos'][$tab][$id] = $item;
                }
            }
        }
        foreach ($this->formArray['campos'] as $tab => &$tabCampos) {
            foreach ($tabCampos as $id => &$item) {
                $arquivoIde = 'ide/personalizacaoForm/' . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $id . '-codigoFonte';
                $arquivoApps = Apps::getDirAtivos() . 'personalizacaoForm/' . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $id . '-codigoFonte';
                $arquivoAppsEditor = Apps::getDirEditor() . 'personalizacaoForm/' . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $id . '-codigoFonte';
                $arquivoGlobal = "{$g['pathDados']}personalizacaoForm/" . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $id . '-codigoFonte';
                $arquivo = $arquivoIde;
                if (!$g['modoDesenvolvedor'] && file_exists($arquivoApps)) {
                    $arquivo = $arquivoApps;
                }
                if ($g['modoDesenvolvedorApp'] && file_exists($arquivoAppsEditor)) {
                    $arquivo = $arquivoAppsEditor;
                }
                if (!$g['modoDesenvolvedor'] && !$g['modoDesenvolvedorApp'] && file_exists($arquivoGlobal)) {
                    $arquivo = $arquivoGlobal;
                }
                if (!(!file_exists($arquivo))) {
                    eval("namespace hardness;\n" . str_replace('<?php', '', file_get_contents($arquivo)));
                    $item = $codigo;
                }
                $arquivoIde = 'ide/personalizacaoForm/' . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $id . '-callbackPreencherValor';
                $arquivoApps = Apps::getDirAtivos() . 'personalizacaoForm/' . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $id . '-callbackPreencherValor';
                $arquivoAppsEditor = Apps::getDirEditor() . 'personalizacaoForm/' . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $id . '-callbackPreencherValor';
                $arquivoGlobal = "{$g['pathDados']}personalizacaoForm/" . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $id . '-callbackPreencherValor';
                $arquivo = $arquivoIde;
                if (!$g['modoDesenvolvedor'] && file_exists($arquivoApps)) {
                    $arquivo = $arquivoApps;
                }
                if ($g['modoDesenvolvedorApp'] && file_exists($arquivoAppsEditor)) {
                    $arquivo = $arquivoAppsEditor;
                }
                if (!$g['modoDesenvolvedor'] && !$g['modoDesenvolvedorApp'] && file_exists($arquivoGlobal)) {
                    $arquivo = $arquivoGlobal;
                }
                if (!(!file_exists($arquivo))) {
                    $item['callbackPreencherValor'] = file_get_contents($arquivo);
                }
                if (!(!$item['callbackPreencherValor'])) {
                    $carregarParametros = array('todosCampos' => $this->formArrayTodosCampos);
                    if (!(!empty($carregarParametros['todosCampos']))) {
                        $carregarParametros['todosCampos'] = $_REQUEST[$this->formNome];
                        $carregarParametros[$this->formNome] = false;
                    }
                    $item['value'] = Personalizacoes::carregarExecutarCallback($item['callbackPreencherValor'], $carregarParametros);
                }
                if ($item['type'] == 'select') {
                    $arquivoIde = 'ide/personalizacaoForm/' . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $id . '-callbackPreencherSelect';
                    $arquivoApps = Apps::getDirAtivos() . 'personalizacaoForm/' . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $id . '-callbackPreencherSelect';
                    $arquivoAppsEditor = Apps::getDirEditor() . 'personalizacaoForm/' . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $id . '-callbackPreencherSelect';
                    $arquivoGlobal = "{$g['pathDados']}personalizacaoForm/" . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $id . '-callbackPreencherSelect';
                    $arquivo = $arquivoIde;
                    if (!$g['modoDesenvolvedor'] && file_exists($arquivoApps)) {
                        $arquivo = $arquivoApps;
                    }
                    if ($g['modoDesenvolvedorApp'] && file_exists($arquivoAppsEditor)) {
                        $arquivo = $arquivoAppsEditor;
                    }
                    if (!$g['modoDesenvolvedor'] && !$g['modoDesenvolvedorApp'] && file_exists($arquivoGlobal)) {
                        $arquivo = $arquivoGlobal;
                    }
                    if (!(!file_exists($arquivo))) {
                        $item['callbackPreencherSelect'] = file_get_contents($arquivo);
                    }
                    if (!(!$item['callbackPreencherSelect'])) {
                        $carregarParametros = array('campoAtual' => $item['value'], 'todosCampos' => $this->formArrayTodosCampos);
                        if (!(!empty($carregarParametros['todosCampos']))) {
                            $carregarParametros['todosCampos'] = $_REQUEST[$this->formNome];
                            $carregarParametros[$this->formNome] = false;
                        }
                        $item['select'] = Personalizacoes::carregarExecutarCallback($item['callbackPreencherSelect'], $carregarParametros);
                    }
                }
                if ($g['conf']->camposNaoUsar($id)) {
                    $this->formTabId[$id] = $tab;
                    if ($item['type'] == 'select') {
                        $valorSelect = $g['conf']->camposValorPadrao($id);
                        if (strlen($item['value']) > 0) {
                            $valorSelect = $item['value'];
                        }
                        $this->formArrayValues[$id] = $valorSelect;
                        if (strlen($valorSelect) > 0) {
                            foreach ($item['select'] as &$itemSelect) {
                                if ($itemSelect['value'] == $valorSelect) {
                                    $itemSelect['selected'] = true;
                                } else {
                                    $itemSelect['selected'] = false;
                                }
                            }
                            unset($itemSelect);
                        }
                    } else {
                        if ($item['type'] == 'pesquisa') {
                            $valorSelect = $g['conf']->camposValorPadrao($id);
                            if ($item['value']) {
                                $valorSelect = $item['value'];
                            } else {
                                $item['value'] = $valorSelect;
                            }
                            $sql = "select {$item['pesquisa'][2]} from {$item['pesquisa'][0]} {$item['pesquisa'][3]} where {$item['pesquisa'][1]} = '{$valorSelect}'";
                            foreach ($this->formArrayTodosCampos as $key => $value) {
                                $sql = str_replace("[[{$key}]]", $value, $sql);
                            }
                            $campoPadraoPesquisa = mysql_query($sql);
                            $campoPadraoPesquisa = mysql_fetch_array($campoPadraoPesquisa);
                            $item['value_Pesquisa'] = implode(' ', $campoPadraoPesquisa);
                        } else {
                            $item['value'] = isset($item['value']) ? $item['value'] : $g['conf']->camposValorPadrao($id);
                            $this->formArrayValues[$id] = $item['value'];
                        }
                    }
                    $type = $g['conf']->camposTipo($id);
                    if ($type) {
                        switch ($type[0]) {
                            case 'numero':
                                $item['type'] = 'moeda';
                                if (!(isset($item['casasDecimais']))) {
                                    $item['casasDecimais'] = !isset($type[1]) ? 2 : $type[1];
                                }
                                break;
                            case 'data':
                                $item['type'] = "data";
                                break;
                            case 'dataHora':
                            case 'datetime':
                                $item['type'] = "dataHora";
                                break;
                            case 'tempo':
                                $item['type'] = "tempo";
                                break;
                            case 'cnpj':
                                $item['type'] = "cnpj";
                                break;
                            case 'cpf':
                                $item['type'] = "cpf";
                                break;
                            case 'cep':
                                $item['type'] = "cep";
                                break;
                            case 'texto':
                                foreach ($type['form'] as $key => $value) {
                                    $item[$key] = $value;
                                }
                                break;
                        }
                    }
                    if (!(isset($item['type']))) {
                        $item['type'] = 'text';
                    }
                    if (($item['type'] == 'moeda')) {
                        $item['length'] = isset($item['length']) ? $item['length'] : array(12, 50);
                    } elseif (($item['type'] == 'data')) {
                        $item['length'] = isset($item['length']) ? $item['length'] : array(10, 10);
                    } elseif (($item['type'] == 'dataHora')) {
                        $item['length'] = isset($item['length']) ? $item['length'] : array(19, 19);
                    } elseif (($item['type'] == 'tempo')) {
                        $item['length'] = isset($item['length']) ? $item['length'] : array(12, 10);
                    } elseif (($item['type'] == 'cnpj')) {
                        $item['length'] = isset($item['length']) ? $item['length'] : array(14, 18);
                    } elseif (($item['type'] == 'cpf')) {
                        $item['length'] = isset($item['length']) ? $item['length'] : array(20, 14);
                    } elseif (($item['type'] == 'cep')) {
                        $item['length'] = isset($item['length']) ? $item['length'] : array(10, 15);
                    }
                } else {
                    unset($this->formArray['campos'][$tab][$id]);
                }
                $arquivoIde = isset($r_pegarForm) && $g['modoDesenvolvedor'] ? null : 'ide/personalizacaoForm/' . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $id . '-callbackAoSair';
                $arquivoApps = isset($r_pegarForm) && $g['modoDesenvolvedor'] ? null : Apps::getDirAtivos() . 'personalizacaoForm/' . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $id . '-callbackAoSair';
                $arquivoAppsEditor = isset($r_pegarForm) && ($g['modoDesenvolvedor'] || $g['modoDesenvolvedorApp']) ? null : Apps::getDirEditor() . 'personalizacaoForm/' . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $id . '-callbackAoSair';
                $arquivoGlobal = isset($r_pegarForm) ? null : "{$g['pathDados']}personalizacaoForm/" . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $id . '-callbackAoSair';
                $arquivo = $arquivoIde;
                if (!$g['modoDesenvolvedor'] && $arquivoApps && file_exists($arquivoApps)) {
                    $arquivo = $arquivoApps;
                }
                if ($g['modoDesenvolvedorApp'] && $arquivoAppsEditor && file_exists($arquivoAppsEditor)) {
                    $arquivo = $arquivoAppsEditor;
                }
                if (!$g['modoDesenvolvedor'] && !$g['modoDesenvolvedorApp'] && $arquivoGlobal && file_exists($arquivoGlobal)) {
                    $arquivo = $arquivoGlobal;
                }
                if ($arquivo && file_exists($arquivo)) {
                    $item['callbackAoSair'] = file_get_contents($arquivo);
                }
                $arquivoIde = isset($r_pegarForm) && $g['modoDesenvolvedor'] ? null : 'ide/personalizacaoForm/' . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $id . '-callbackAoAlterar';
                $arquivoApps = isset($r_pegarForm) && $g['modoDesenvolvedor'] ? null : Apps::getDirAtivos() . 'personalizacaoForm/' . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $id . '-callbackAoAlterar';
                $arquivoAppsEditor = isset($r_pegarForm) && ($g['modoDesenvolvedor'] || $g['modoDesenvolvedorApp']) ? null : Apps::getDirEditor() . 'personalizacaoForm/' . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $id . '-callbackAoAlterar';
                $arquivoGlobal = isset($r_pegarForm) ? null : "{$g['pathDados']}personalizacaoForm/" . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $id . '-callbackAoAlterar';
                $arquivo = $arquivoIde;
                if (!$g['modoDesenvolvedor'] && $arquivoApps && file_exists($arquivoApps)) {
                    $arquivo = $arquivoApps;
                }
                if ($g['modoDesenvolvedorApp'] && $arquivoAppsEditor && file_exists($arquivoAppsEditor)) {
                    $arquivo = $arquivoAppsEditor;
                }
                if (!$g['modoDesenvolvedor'] && !$g['modoDesenvolvedorApp'] && $arquivoGlobal && file_exists($arquivoGlobal)) {
                    $arquivo = $arquivoGlobal;
                }
                if ($arquivo && file_exists($arquivo)) {
                    $item['callbackAoAlterar'] = file_get_contents($arquivo);
                }
                $arquivoIde = isset($r_pegarForm) && $g['modoDesenvolvedor'] ? null : 'ide/personalizacaoForm/' . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $id . '-callbackRestricao';
                $arquivoApps = isset($r_pegarForm) && $g['modoDesenvolvedor'] ? null : Apps::getDirAtivos() . 'personalizacaoForm/' . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $id . '-callbackRestricao';
                $arquivoAppsEditor = isset($r_pegarForm) && ($g['modoDesenvolvedor'] || $g['modoDesenvolvedorApp']) ? null : Apps::getDirEditor() . 'personalizacaoForm/' . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $id . '-callbackRestricao';
                $arquivoGlobal = isset($r_pegarForm) ? null : "{$g['pathDados']}personalizacaoForm/" . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $id . '-callbackRestricao';
                $arquivo = $arquivoIde;
                if (!$g['modoDesenvolvedor'] && $arquivoApps && file_exists($arquivoApps)) {
                    $arquivo = $arquivoApps;
                }
                if ($g['modoDesenvolvedorApp'] && $arquivoAppsEditor && file_exists($arquivoAppsEditor)) {
                    $arquivo = $arquivoAppsEditor;
                }
                if (!$g['modoDesenvolvedor'] && !$g['modoDesenvolvedorApp'] && $arquivoGlobal && file_exists($arquivoGlobal)) {
                    $arquivo = $arquivoGlobal;
                }
                if ($arquivo && file_exists($arquivo)) {
                    $item['callbackRestricao'] = file_get_contents($arquivo);
                }
            }
        }
        $this->formArrayOriginal = $this->formArray;
        $this->formArrayValuesOriginal = $this->formArrayValues;
        if (!$r_pegarForm) {
            if (empty($g['nivelAlcada']) && $g['usuarioAtual'] == '63') {
                $g['nivelAlcada'] = 4;
            }
            foreach ($this->formArray['campos'] as $tab => &$tabCampos) {
                foreach ($tabCampos as $id => &$item) {
                    $nivel = 1;
                    if (!(!isset($item['nivel']))) {
                        $nivel = $item['nivel'];
                    }
                    if ($nivel > $g['nivelAlcada']) {
                        if ($key = 'C007_Email_Assinatura_') {
                            $item['readonly'] = true;
                        } else {
                            $item['disabled'] = true;
                        }
                    }
                }
            }
            return null;
        } else {
            apc_store("form-php-{$formId}-pegarForm", serialize($this->formArray), 3600);
            die("form-php-{$formId}-pegarForm");
        }
    }

    public function campoCodigoHTML($id, $disabled = false, $alterado = false, $enviar_btnId = false)
    {
        global $g;
        global $r_modoFormLeve, $r_acaoId, $r_campoRefreshVars;
        $item = $this->formArray['campos'][$this->formTabId[$id]][$id];
        if (!(!isset($item['value']))) {
            $item['value'] = str_replace('"', "&quot;", $item['value']);
        }
        $options = '';
        $tagId = $id;
        if (!is_array($enviar_btnId) && $item['type'] == 'enviarForm') {
            $tagId = $enviar_btnId;
        }
        if (isset($item['disabled']) && $item['disabled']) {
            $options .= isset($item['id_show']) && !$item['id_show'] ? '' : "id=\"{$tagId}_disabled\" ";
            $options .= !isset($item['array_group']) || $item['array_group'] ? "name=\"{$this->formNome}[{$item['name']}]\" " : "name=\"{$item['name']}_disabled\" ";
        } else {
            $options .= !isset($item['array_group']) || $item['array_group'] ? "name=\"{$this->formNome}[{$item['name']}]\" " : "name=\"{$item['name']}\" ";
            $options .= isset($item['id_show']) && !$item['id_show'] ? '' : "id=\"{$tagId}\" ";
        }
        if ($item['type'] == 'enviarForm') {
            $item['type'] = 'submit';
        }
        $options .= $item['type'] != 'textarea' && $item['type'] != 'select' && $item['type'] != 'pesquisa' && $item['type'] != 'pesquisaGrid' && $item['type'] != 'moeda' && $item['type'] != 'tempo' && $item['type'] != 'data' && $item['type'] != 'dataHora' ? "type=\"{$item['type']}\" " : '';
        $options .= isset($item['length'][0]) && $item['type'] == 'textarea' ? "rows=\"{$item['length'][0]}\" " : '';
        $options .= isset($item['length'][1]) && $item['type'] == 'textarea' ? "cols=\"{$item['length'][1]}\" " : '';
        $options .= isset($item['length'][0]) && $item['type'] != 'textarea' ? "size=\"{$item['length'][0]}\" " : '';
        $options .= isset($item['length'][1]) && $item['type'] != 'textarea' ? "maxlength=\"{$item['length'][1]}\" " : '';
        $options .= isset($item['value']) && $item['type'] != 'textarea' && $item['type'] != 'pesquisa' && $item['type'] != 'pesquisaGrid' ? "value=\"{$item['value']}\" " : '';
        $options .= isset($item['extra']) ? $item['extra'] . ' ' : '';
        $options .= isset($item['checked']) && $item['checked'] ? 'checked="checked" ' : '';
        $options .= $disabled || isset($item['disabled']) && $item['disabled'] ? 'disabled="disabled" tabindex="-1" ' : '';
        $options .= isset($item['readonly']) && $item['readonly'] ? 'readonly="readonly" tabindex="-1" ' : '';
        if ($_SESSION['device'] == 'mobile') {
            $item['style'] .= ' font-size: 16px !important;';
            if ($item['type'] == 'select') {
                $item['style'] .= ' height: 39px; overflow: hidden;';
            }
            if (in_array($item['type'], ['button', 'submit', 'enviarForm'])) {
                $item['class'] .= ' tw-form-button';
                $item['style'] .= ' width: 100% !important;';
            } else {
                $item['class'] .= ' tw-form-field';
            }
        }
        if (isset($item['style']) && !empty($item['style'])) {
            $uppercase = isset($item['uppercase']) && !$item['uppercase'] ? 'text-transform: none;' : '';
            $options .= " style=\"{$uppercase}{$item['style']}\"";
        } else {
            $options .= isset($item['uppercase']) && !$item['uppercase'] ? ' style="text-transform: none;"' : '';
        }
        if ($alterado) {
            $options .= 'alterado="true"';
        }
        $options .= isset($item['displayType']) ? "display-type=\"{$item['displayType']}\" " : '';
        $options = trim($options);
        if ($item['type'] == 'textarea') {
            $tmpValue = isset($item['value']) ? $item['value'] : '';
            $output = "<textarea {$options} class=\"itemForm itemForm-{$item['name']} {$item['class']}\">{$tmpValue}</textarea>";
            if (!(empty($item['code']))) {
                $options = '';
                $options .= "overviewRulerLanes: 0,\n";
                $options .= "folding: false,\n";
                $options .= "minimap: { enabled: false },\n";
                if (isset($item['readonly']) && $item['readonly']) {
                    $options .= "readOnly: true,\n";
                }
                if (!(!$item['codeModoSimples'])) {
                    $options .= "lineNumbers: 'off',\n";
                    $options .= "glyphMargin: false,\n";
                }
                $output .= <<<EOT
\t\t\t\t<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') { 
\t\t\t\t    var gerarEditor = function() {
\t\t\t\t\t\tvar \$textarea = \$('#{$g['divId']} #{$id}');
\t\t\t\t\t\t\$('<div id="{$id}Code">').appendTo(\$textarea.parent());
\t\t\t\t\t\t\$item = \$('#{$g['divId']} #{$id}Code');
\t\t\t\t\t\t\$item.width(\$textarea.width());
\t\t\t\t\t\t\$item.height(\$textarea.height());
\t\t\t\t\t\t\$textarea.css('display', 'none');
EOT;
                if ($item['codeSchema'] == 'bi') {
                    $listaCampos = array('dimensoes' => '', 'fatos' => '', 'todos' => '');
                    foreach (BI::estruturaDeDados() as $item) {
                        $listaCampos['todos'] .= $listaCampos['todos'] ? ', ' : '';
                        $listaCampos['todos'] .= "'{$item['campo']}'";
                        if (strpos($item['campo'], 'FAC_') !== false) {
                            $listaCampos['fatos'] .= $listaCampos['fatos'] ? ', ' : '';
                            $listaCampos['fatos'] .= "'{$item['campo']}'";
                        } else {
                            $listaCampos['dimensoes'] .= $listaCampos['dimensoes'] ? ', ' : '';
                            $listaCampos['dimensoes'] .= "'{$item['campo']}'";
                        }
                    }
                    $options .= "model: model,\n";
                    $output .= <<<EOT
                        // Documentos úteis utilizado para desenvolver este Schema
                        // https://github.com/Microsoft/monaco-editor/issues/38
                        // https://microsoft.github.io/monaco-editor/playground.html#extending-language-services-configure-json-defaults
                        // https://simonplend.com/wp-content/uploads/2020/12/JSON-Schema-Cheat-Sheet-v1.1.pdf
                        var jsonCode = \$textarea.val();
                        monaco.editor.getModels().forEach(model => model.dispose());
\t\t\t\t\t\tvar modelUri = monaco.Uri.parse('a://b/foo.json'); // a made up unique URI for our model
\t\t\t\t\t\tvar model = monaco.editor.createModel(jsonCode, 'json', modelUri);
                        // configure the JSON language support with schemas and schema associations
\t\t\t\t\t\tmonaco.languages.json.jsonDefaults.setDiagnosticsOptions({
\t\t\t\t\t\t\tvalidate: true,
\t\t\t\t\t\t\tschemas: [
\t\t\t\t\t\t\t\t{
\t\t\t\t\t\t\t\t\turi: 'http://myserver/bi-schema.json', // id of the first schema
\t\t\t\t\t\t\t\t\tfileMatch: [modelUri.toString()], // associate with our model
\t\t\t\t\t\t\t\t\tschema: {
\t\t\t\t\t\t\t\t\t\ttype: 'object',
\t\t\t\t\t\t\t\t\t\trequired: ['linha', 'dados'],
\t\t\t\t\t\t\t\t\t\tproperties: {
\t\t\t\t\t\t\t\t\t\t\ttipo: {
                                                description: 'Tipo do relátorio B.I. (padrão: grid)',
                                                type: 'string',
                                                default: 'grid',
\t\t\t\t\t\t\t\t\t\t\t\tenum: ['grid', 'grafico']
\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\tgrid: {
                                                description: 'Propriedades que modificam um relátorio tipo GRID',
\t\t\t\t\t\t\t\t\t\t\t\t\$ref: 'http://myserver/bi-grid-schema.json'
\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\tgrafico: {
                                                description: 'Propriedades que definem e modificam um gráfico',
                                                \$ref: 'http://myserver/bi-grafico-schema.json'
\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\tlinha: {
                                                description: 'Definição da Dimensão que será utilizada na linhas do relatório',
                                                \$ref: 'http://myserver/bi-linha-schema.json'
\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\tcoluna: {
                                                description: 'Definição da Dimensão que será utilizada para a geração de colunas do relatório',
                                                \$ref: 'http://myserver/bi-linha-schema.json'
\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\tdados: {
                                                description: 'Inclusão de Fatos, os seja, os campos de valores numéricos que serão mostrados no relátorio',
                                                \$ref: 'http://myserver/bi-dados-schema.json'
\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\tfiltros: {
\t\t\t\t\t\t\t\t\t\t\t\tdescription: 'Monta os filtros que serão exibidos',
                                                \$ref: 'http://myserver/bi-filtros-schema.json'
\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t{
\t\t\t\t\t\t\t\t\turi: 'http://myserver/bi-grid-schema.json',
\t\t\t\t\t\t\t\t\tschema: {
\t\t\t\t\t\t\t\t\t\ttype: 'object',
\t\t\t\t\t\t\t\t\t\tproperties: {
\t\t\t\t\t\t\t\t\t\t\tmostrarCurvaABC: {
                                                description: 'Padrão: false',
                                                type: 'boolean',
                                                default: false,
\t\t\t\t\t\t\t\t\t\t\t\tenum: [true, false]
\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\tocultarColunasDashboard: {
                                                description: 'Padrão: false',
                                                type: 'boolean',
                                                default: false,
\t\t\t\t\t\t\t\t\t\t\t\tenum: [true, false]
\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\tocultarSempreColunas: {
                                                description: 'Padrão: false',
                                                type: 'boolean',
                                                default: false,
\t\t\t\t\t\t\t\t\t\t\t\tenum: [true, false]
\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\tocultarPorcentagem: {
                                                description: 'Padrão: false',
                                                type: 'boolean',
                                                default: false,
\t\t\t\t\t\t\t\t\t\t\t\tenum: [true, false]
\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\tocultarTotais: {
                                                description: 'Padrão: false',
                                                type: 'boolean',
                                                default: false,
\t\t\t\t\t\t\t\t\t\t\t\tenum: [true, false]
\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\tincluirLinhaVazia: {
                                                description: 'Padrão: false',
                                                type: 'boolean',
                                                default: false,
\t\t\t\t\t\t\t\t\t\t\t\tenum: [true, false]
\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\tincluirColunaVazia: {
                                                description: 'Padrão: false',
                                                type: 'boolean',
                                                default: false,
\t\t\t\t\t\t\t\t\t\t\t\tenum: [true, false]
\t\t\t\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t{
\t\t\t\t\t\t\t\t\turi: 'http://myserver/bi-grafico-schema.json',
\t\t\t\t\t\t\t\t\tschema: {
\t\t\t\t\t\t\t\t\t\ttype: 'object',
\t\t\t\t\t\t\t\t\t\tproperties: {
\t\t\t\t\t\t\t\t\t\t\ttipo: {
                                                description: 'Padrão: barra',
                                                type: 'string',
                                                default: 'barra',
\t\t\t\t\t\t\t\t\t\t\t\tenum: ['barra', 'pizza', 'linha', 'area', 'radar', 'grid']
\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\tincluirGraficoLabel: {
                                                description: 'Padrão: true',
                                                type: 'boolean',
                                                default: true,
\t\t\t\t\t\t\t\t\t\t\t\tenum: [true, false]
\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\tincluirGraficoLegend: {
                                                description: 'Padrão: true',
                                                type: 'boolean',
                                                default: true,
\t\t\t\t\t\t\t\t\t\t\t\tenum: [true, false]
\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t{
\t\t\t\t\t\t\t\t\turi: 'http://myserver/bi-linha-schema.json',
\t\t\t\t\t\t\t\t\tschema: {
\t\t\t\t\t\t\t\t\t\ttype: 'object',
\t\t\t\t\t\t\t\t\t\trequired: ['campo'],
\t\t\t\t\t\t\t\t\t\tproperties: {
\t\t\t\t\t\t\t\t\t\t\tcampo: {
                                                description: 'Formato: {Dimensão}->{Campo}',
                                                type: 'string',
\t\t\t\t\t\t\t\t\t\t\t\tenum: [
                                                    {$listaCampos['dimensoes']}
\t\t\t\t\t\t\t\t\t\t\t\t]
\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\tordenacao: {
                                                description: 'Ordenação (padrão: asc)',
                                                type: 'string',
\t\t\t\t\t\t\t\t\t\t\t\tenum: ['desc', 'asc', 'total_desc', 'total_asc']
\t\t\t\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t{
\t\t\t\t\t\t\t\t\turi: 'http://myserver/bi-dados-schema.json',
\t\t\t\t\t\t\t\t\tschema: {
\t\t\t\t\t\t\t\t\t\ttype: 'array',
\t\t\t\t\t\t\t\t\t\titems: {
                                            type: 'object',
                                            required: ['campo'],
\t\t\t\t\t\t\t\t\t\t\tproperties: {
\t\t\t\t\t\t\t\t\t\t\t\tcampo: {
\t\t\t\t\t\t\t\t\t\t\t\t\tdescription: 'Formato: {Entidade}->{Campo}',
\t\t\t\t\t\t\t\t\t\t\t\t\ttype: 'string',
\t\t\t\t\t\t\t\t\t\t\t\t\tenum: [
\t\t\t\t\t\t\t\t\t\t\t\t\t\t{$listaCampos['fatos']}
\t\t\t\t\t\t\t\t\t\t\t\t\t]
\t\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\t\tagregador: {
\t\t\t\t\t\t\t\t\t\t\t\t\tdescription: 'Agregadores: https://clickhouse.com/docs/en/sql-reference/aggregate-functions/reference/',
\t\t\t\t\t\t\t\t\t\t\t\t\ttype: 'string',
                                                \tdefault: 'sum',
\t\t\t\t\t\t\t\t\t\t\t\t\tenum: ['sum', 'avg', 'any', 'count', 'min', 'max']
\t\t\t\t\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t{
\t\t\t\t\t\t\t\t\turi: 'http://myserver/bi-filtros-schema.json',
\t\t\t\t\t\t\t\t\tschema: {
\t\t\t\t\t\t\t\t\t\ttype: 'array',
\t\t\t\t\t\t\t\t\t\titems: {
\t\t\t\t\t\t\t\t\t\t\ttype: 'object',
\t\t\t\t\t\t\t\t\t\t\trequired: ['campo', 'titulo'],
\t\t\t\t\t\t\t\t\t\t\tproperties: {
\t\t\t\t\t\t\t\t\t\t\t\tcampo: {
\t\t\t\t\t\t\t\t\t\t\t\t\tanyOf: [
\t\t\t\t\t\t\t\t\t\t\t\t\t\t{
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\tdescription: 'Nome do campo para aplicar o filtro',
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\ttype: 'string',
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\tenum: [
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t{$listaCampos['todos']}
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t]
\t\t\t\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\t\t\t\t{
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\ttype: "string"
\t\t\t\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\t\t\t],
\t\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\t\ttitulo: {
\t\t\t\t\t\t\t\t\t\t\t\t\tdescription: 'Título do filtro',
\t\t\t\t\t\t\t\t\t\t\t\t\ttype: 'string'
\t\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\t\texibir: {
\t\t\t\t\t\t\t\t\t\t\t\t\tdescription: 'Exibir ou não o filtro',
\t\t\t\t\t\t\t\t\t\t\t\t\ttype: 'boolean',
\t\t\t\t\t\t\t\t\t\t\t\t\tdefault: true,
\t\t\t\t\t\t\t\t\t\t\t\t\tenum: [true, false]
\t\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\t\ttipo: {
\t\t\t\t\t\t\t\t\t\t\t\t\tdescription: 'Tipo do campo',
\t\t\t\t\t\t\t\t\t\t\t\t\ttype: 'string',
\t\t\t\t\t\t\t\t\t\t\t\t\tdefault: 'texto',
\t\t\t\t\t\t\t\t\t\t\t\t\tenum: ['texto', 'numero', 'data', 'select']
\t\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\t\tvalor: {
\t\t\t\t\t\t\t\t\t\t\t\t\tdescription: 'Trazer o filtro já preenchido com um valor filtrado',
\t\t\t\t\t\t\t\t\t\t\t\t\ttype: 'string'
\t\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\t\tselect: {
\t\t\t\t\t\t\t\t\t\t\t\t\ttype: 'array',
\t\t\t\t\t\t\t\t\t\t\t\t\titems: {
\t\t\t\t\t\t\t\t\t\t\t\t\t\ttype: 'object',
\t\t\t\t\t\t\t\t\t\t\t\t\t\trequired: ['title', 'value'],
\t\t\t\t\t\t\t\t\t\t\t\t\t\tproperties: {
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\ttitle: {
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\tdescription: 'Título',
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\ttype: 'string'
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\tvalue: {
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\tdescription: 'Valor',
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\ttype: 'string'
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t]
\t\t\t\t\t\t});
EOT;
                } else {
                    if ($item['codeSchema'] == 'indicador') {
                        $options .= "model: model,\n";
                        $output .= <<<EOT
                        var jsonCode = \$textarea.val();
                        monaco.editor.getModels().forEach(model => model.dispose());
\t\t\t\t\t\tvar modelUri = monaco.Uri.parse('a://b/foo.json'); // a made up unique URI for our model
\t\t\t\t\t\tvar model = monaco.editor.createModel(jsonCode, 'json', modelUri);
                        // configure the JSON language support with schemas and schema associations
\t\t\t\t\t\tmonaco.languages.json.jsonDefaults.setDiagnosticsOptions({
\t\t\t\t\t\t\tvalidate: true,
\t\t\t\t\t\t\tschemas: [
\t\t\t\t\t\t\t\t{
\t\t\t\t\t\t\t\t\turi: 'http://myserver/indicador-schema.json',
\t\t\t\t\t\t\t\t\tfileMatch: [modelUri.toString()],
\t\t\t\t\t\t\t\t\tschema: {
\t\t\t\t\t\t\t\t\t\ttype: 'object',
\t\t\t\t\t\t\t\t\t\trequired: ['nome', 'valor'],
\t\t\t\t\t\t\t\t\t\tproperties: {
\t\t\t\t\t\t\t\t\t\t\tnome: {
                                                description: 'Nome do indicador.',
                                                type: 'string',
\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\tvalor: {
\t\t\t\t\t\t\t\t\t\t\t\tdescription: 'Valor do indicador. Caso utilizado o campo "sql", deverá ser utilizado a variável "[[valor]]" para substituição.',
\t\t\t\t\t\t\t\t\t\t\t\ttype: 'string',
\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\tclasse: {
\t\t\t\t\t\t\t\t\t\t\t\tdescription: 'Classe CSS.',
\t\t\t\t\t\t\t\t\t\t\t\ttype: 'string',
                                                default: 'bi-indicador-padrao',
\t\t\t\t\t\t\t\t\t\t\t\tenum: ['bi-indicador-padrao'],
\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\tsql: {
\t\t\t\t\t\t\t\t\t\t\t\tdescription: 'Query SQL para ser executada no servidor ClickHouse, retornando o valor do Indicador. Variáveis disponíveis: [[extra]].',
\t\t\t\t\t\t\t\t\t\t\t\ttype: 'string',
\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\tdecimais: {
\t\t\t\t\t\t\t\t\t\t\t\tdescription: 'Utilizado para a formatação de casas decimais do valor retornado pelo "sql".',
\t\t\t\t\t\t\t\t\t\t\t\ttype: 'number',
\t\t\t\t\t\t\t\t\t\t\t\tdefault: 2,
\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t]
\t\t\t\t\t\t});
EOT;
                    } else {
                        if ($item['codeSchema'] == 'dashboard') {
                            $options .= "model: model,\n";
                            $output .= <<<EOT
                        var jsonCode = \$textarea.val();
                        monaco.editor.getModels().forEach(model => model.dispose());
\t\t\t\t\t\tvar modelUri = monaco.Uri.parse('a://b/foo.json'); // a made up unique URI for our model
\t\t\t\t\t\tvar model = monaco.editor.createModel(jsonCode, 'json', modelUri);
                        // configure the JSON language support with schemas and schema associations
\t\t\t\t\t\tmonaco.languages.json.jsonDefaults.setDiagnosticsOptions({
\t\t\t\t\t\t\tvalidate: true,
\t\t\t\t\t\t\tschemas: [
\t\t\t\t\t\t\t\t{
\t\t\t\t\t\t\t\t\turi: 'http://myserver/dashboard-schema.json',
\t\t\t\t\t\t\t\t\tfileMatch: [modelUri.toString()],
\t\t\t\t\t\t\t\t\tschema: {
\t\t\t\t\t\t\t\t\t\ttype: 'object',
\t\t\t\t\t\t\t\t\t\trequired: ['itens'],
\t\t\t\t\t\t\t\t\t\tproperties: {
\t\t\t\t\t\t\t\t\t\t\titens: {
                                                description: 'Relatórios, gráficos e indicadores',
\t\t\t\t\t\t\t\t\t\t\t\t\$ref: 'http://myserver/dashboard-itens-schema.json'
\t\t\t\t\t\t\t\t\t\t\t},\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t{
\t\t\t\t\t\t\t\t\turi: 'http://myserver/dashboard-itens-schema.json',
\t\t\t\t\t\t\t\t\tschema: {
\t\t\t\t\t\t\t\t\t\ttype: 'array',
\t\t\t\t\t\t\t\t\t\titems: {
                                            type: 'object',
                                            required: ['url'],
\t\t\t\t\t\t\t\t\t\t\tproperties: {
\t\t\t\t\t\t\t\t\t\t\t\turl: {
\t\t\t\t\t\t\t\t\t\t\t\t\tdescription: 'URL do item a ser carregada.',
\t\t\t\t\t\t\t\t\t\t\t\t\ttype: 'string',
\t\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\t\tparametros: {
\t\t\t\t\t\t\t\t\t\t\t\t\tdescription: 'Parâmetros opcionais',
\t\t\t\t\t\t\t\t\t\t\t\t\ttype: 'string',
\t\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t]
\t\t\t\t\t\t});
EOT;
                        }
                    }
                }
                $output .= <<<EOT
\t\t\t\t\t\tvar monEditor = monaco.editor.create(document.getElementById("{$id}Code"), {
\t\t\t\t\t\t\tvalue: \$textarea.val(),
\t\t\t\t\t\t\tlanguage: "{$item['code']}",
\t\t\t\t\t\t\ttheme: "vs-dark",
\t\t\t\t\t\t\t{$options}
\t\t\t\t\t\t});
\t\t\t\t\t\tmonEditor.getModel().onDidChangeContent(function (e) {
\t\t\t\t\t\t\t\$textarea.val(monEditor.getValue());
\t\t\t\t\t\t})
\t\t\t\t\t\t\$item.closest('.ui-widget-content').css('overflow', 'hidden');
\t\t\t\t\t\t\$textarea.bind('setEditorValue', function(event, value) {
\t\t\t\t\t\t    monEditor.setValue(value);
\t\t\t\t\t\t});
                        
\t\t\t\t\t}
\t\t\t\t\t// Tem alguns textarea que são maximizados no JS, para esses casos aguardar 100ms
\t\t\t\t\tvar \$textarea = \$('#{$g['divId']} #{$id}');
\t\t\t\t\tif (\$textarea.width() > 150) {
\t\t\t\t\t    gerarEditor();
\t\t\t\t\t} else {
\t\t\t\t\t    \$textarea.bind('gerarEditor', gerarEditor);
\t\t\t\t\t}
\t\t\t\t} </script></div>
EOT;
            }
        } else {
            if ($item['type'] == 'select') {
                $lastSelectValue = 0;
                if (count($item['select']) == 1) {
                    $lastSelectValue = $item['select'][0]['value'];
                }
                $selectOptions = '';
                foreach ($item['select'] as $option) {
                    $tmp = '';
                    if (!(!isset($option['disabled']))) {
                        $tmp .= 'disabled="disabled" ';
                    }
                    $tmp .= isset($option['value']) ? "value=\"{$option['value']}\" " : '';
                    if (isset($option['selected']) && $option['selected']) {
                        $tmp .= 'selected="selected" ';
                        $lastSelectValue = $option['value'];
                        $selectOptions .= '<option ' . trim($tmp) . " {$option['extra']}>{$option['title']}</option>";
                    } else {
                        if (isset($r_modoFormLeve) && $r_modoFormLeve && $r_acaoId) {
                        } else {
                            if ($option['title'] == 'optgroup') {
                                $selectOptions .= '<optgroup label="' . $option['value'] . '"></optgroup>';
                            } else {
                                $selectOptions .= '<option ' . trim($tmp) . " {$option['extra']}>{$option['title']}</option>";
                            }
                        }
                    }
                }
                $output = '';
                if (!isset($item['selectRefresh']) || isset($item['selectRefresh']) && $item['selectRefresh']) {
                    if (!(!isset($item['select']))) {
                        reset($item['select']);
                        if ($_SESSION['device'] == 'mobile') {
                            $selectIconStyle = <<<EOT
\tstyle="position: absolute; right: 0; top: -28px;";
EOT;
                        } else {
                            $selectIconStyle = <<<EOT
\tstyle="display: inline-block; margin-left: 1px; cursor: pointer; float: right;";
EOT;
                        }
                        $output .= '<span id=\'' . $item['name'] . '_refreshBtn' . '\' class="ui-icon ui-icon-refresh" ' . $selectIconStyle . ' onclick="recarregarCampo(\'' . $g['divId'] . '\', \'' . $item['name'] . '\', \'' . $r_campoRefreshVars . '\');">recarregar opções</span>';
                    }
                }
                $output .= "<select {$options} class=\"itemForm itemForm-{$item['name']} {$item['class']}\">{$selectOptions}</select>";
            } else {
                if ($item['type'] == 'pesquisa') {
                    $options_ = str_replace($id, "{$id}_Pesquisa", $options);
                    $value_Pesquisa = isset($item['value_Pesquisa']) ? "value=\"{$item['value_Pesquisa']}\" " : '';
                    $value_Pesquisa = isset($item['value']) && empty($value_Pesquisa) ? "value=\"{$item['value']}\" " : $value_Pesquisa;
                    $options_ .= $value_Pesquisa;
                    $output = "<input type=\"text\" {$options_} class=\"campo_input_pesquisa itemForm itemForm-{$item['name']}_Pesquisa {$item['class']}\" />";
                    $options_ = $options;
                    $options_ .= isset($item['value']) ? "value=\"{$item['value']}\" " : '';
                    $output .= "<input type=\"hidden\" {$options_} class=\"itemForm itemForm-{$item['name']} {$item['class']}\" />";
                    $item['pesquisa'][3] = !isset($item['pesquisa'][3]) ? '' : $item['pesquisa'][3];
                    $item['pesquisa'][4] = !isset($item['pesquisa'][4]) ? '' : $item['pesquisa'][4];
                    $pesquisaSelect = <<<EOT
\t\t\t\tselect: function(event, ui) { 
\t\t\t\t\t// Ao selecionar, seta o campo hidden ID
\t\t\t\t\tvar id = \$(this).attr('id').replace(/\\_Pesquisa\$/, '');
\t\t\t\t\tif (ui.item.value != 0) {
\t\t\t\t\t\t\$(this).val(ui.item.label);
\t\t\t\t\t\t\$('#' + CSS.escape(id)).val(ui.item.value);
\t\t\t\t\t\t\$(this).data('selectLabel', ui.item.label);
\t\t\t\t\t\t\$(this).data('selectValue', ui.item.value);
\t\t\t\t\t} else {
\t\t\t\t\t\t\$(this).val('');
\t\t\t\t\t\t\$('#' + CSS.escape(id)).val('');
\t\t\t\t\t}
\t\t\t\t\t\$('#' + CSS.escape(id)).trigger('change');
\t\t\t\t\treturn false;
\t\t\t\t}
EOT;
                    $pesquisaChange = <<<EOT
\t\t\t\tchange: function(event, ui) {
\t\t\t\t\t// Usuário não pode mudar o campo, apenas selecionar um valor da pesquisa
\t\t\t\t\tvar id = \$(this).attr('id').replace(/\\_Pesquisa\$/, '');
\t\t\t\t\tif (isempty(\$(this).val())) {
\t\t\t\t\t\t\$(this).val('');
\t\t\t\t\t\t\$('#' + CSS.escape(id)).val('');
\t\t\t\t\t\t\$(this).data('selectLabel', '');
\t\t\t\t\t\t\$(this).data('selectValue', '');
\t\t\t\t\t} else if (\$(this).val() != \$(this).data('selectLabel')) {
\t\t\t\t\t\t\$(this).val(\$(this).data('selectLabel'));
\t\t\t\t\t\t\$('#' + CSS.escape(id)).val(\$(this).data('selectValue'));
\t\t\t\t\t}
\t\t\t\t\treturn false;
\t\t\t\t}
EOT;
                    if (!(!isset($item['pesquisaSelect']))) {
                        $pesquisaSelect = $item['pesquisaSelect'];
                    }
                    if (!(!isset($item['pesquisaChange']))) {
                        $pesquisaChange = $item['pesquisaChange'];
                    }
                    $item['pesquisa'][3] = str_replace("'", "\\'", $item['pesquisa'][3]);
                    $output .= <<<EOT
<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
\$(`#{$g['divId']} #\${CSS.escape('{$id}')}_Pesquisa`).autocomplete({
\tsource: function( request, response ) {
        // todosCampos
        let todosCampos = {};
        \$('#{$g['divId']} .formFormulario:first .itemForm').each(function() {
            todosCampos[\$(this).attr('id')] = \$(this).val(); 
        });
\t\t\$.ajax({
\t\t\turl: "/sistema/funcoes/pesquisa/campoPesquisa/",
\t\t\ttype: 'POST',
\t\t\tdataType: "jsonp",
\t\t\tdata: 'ajax=true&tabela=' + encodeURIComponent('{$item['pesquisa'][0]}') + 
\t\t\t\t'&campoId=' + encodeURIComponent('{$item['pesquisa'][1]}') +
\t\t\t\t'&campoTxt=' + encodeURIComponent('{$item['pesquisa'][2]}') +
\t\t\t\t'&sqlExtra=' + encodeURIComponent('{$item['pesquisa'][3]}') +
\t\t\t\t'&formato=' + encodeURIComponent('{$item['pesquisa'][4]}') +
\t\t\t\t'&pesquisa=' + encodeURIComponent(request.term) +
\t\t\t\t'&todosCampos=' + encodeURIComponent(JSON.stringify(todosCampos)),
\t\t\tsuccess: function( data ) {
\t\t\t\tif (isempty(data)) {
\t\t\t\t\tdata = [{
\t\t\t\t\t\ttitle: 'Não encontrado! Pesquise novamente.',
\t\t\t\t\t\tvalue: 0
\t\t\t\t\t}];
\t\t\t\t}
\t\t\t\tresponse( \$.map( data, function( item ) {
\t\t\t\t\treturn {
\t\t\t\t\t\tlabel: item.title,
\t\t\t\t\t\tvalue: item.value
\t\t\t\t\t}
\t\t\t\t}));
\t\t\t}
\t\t});
\t},
\tdelay: 200,
\t{$pesquisaSelect},
\t{$pesquisaChange},
\tfocus: function(event, ui) { 
\t\t\$(this).val(ui.item.label);
\t\treturn false;
\t}
}).data('selectLabel', '').data('selectValue', 0);
} </script></div>
EOT;
                } else {
                    if ($item['type'] == 'pesquisaGrid') {
                        $pesquisaGridId = md5($g['r_'] . $id);
                        $options_ = str_replace($id, "{$id}_Pesquisa", $options);
                        $value_Pesquisa = isset($item['value_Pesquisa']) ? "value=\"{$item['value_Pesquisa']}\" " : '';
                        $value_Pesquisa = isset($item['value']) ? "value=\"{$item['value']}\" " : $value_Pesquisa;
                        $options_ .= $value_Pesquisa;
                        $output = "<input type=\"text\" enterkeyhint=\"enter\" {$options_} class=\"campo_input_pesquisa_grid itemForm itemForm-{$item['name']}_Pesquisa {$item['class']}\" />";
                        $options_ = $options;
                        $options_ .= isset($item['value']) ? "value=\"{$item['value']}\" " : '';
                        $output .= "<input type=\"hidden\" {$options_} class=\"itemForm itemForm-{$item['name']} {$item['class']}\" />";
                        $item['pesquisaGrid'][3] = !isset($item['pesquisaGrid'][3]) ? '' : $item['pesquisaGrid'][3];
                        $item['pesquisaGrid'][3] = str_replace("\n", ' ', $item['pesquisaGrid'][3]);
                        $item['pesquisaGrid'][3] = str_replace("\r", ' ', $item['pesquisaGrid'][3]);
                        $item['pesquisaGrid'][3] = str_replace("'", "\\'", $item['pesquisaGrid'][3]);
                        if (isset($item['pesquisaGrid'][8])) {
                            if (!(is_array($item['pesquisaGrid'][8]))) {
                                $item['pesquisaGrid'][8] = array($item['pesquisaGrid'][8]);
                            }
                            $item['pesquisaGrid'][8] = serialize($item['pesquisaGrid'][8]);
                        } else {
                            $item['pesquisaGrid'][8] = serialize(array());
                        }
                        if (isset($item['pesquisaGrid'][9])) {
                            $item['pesquisaGrid'][9] = serialize($item['pesquisaGrid'][9]);
                        } else {
                            $item['pesquisaGrid'][9] = serialize(array());
                        }
                        $item['pesquisaGrid'][10] = isset($item['pesquisaGrid'][10]) ? $item['pesquisaGrid'][10] : '';
                        $item['pesquisaGrid'][11] = isset($item['pesquisaGrid'][11]) ? $item['pesquisaGrid'][11] : '[800,400]';
                        $item['pesquisaGrid'][12] = isset($item['pesquisaGrid'][12]) ? $item['pesquisaGrid'][12] : '';
                        $item['pesquisaGrid'][13] = isset($item['pesquisaGrid'][13]) ? serialize($item['pesquisaGrid'][13]) : '';
                        $item['pesquisaGrid'][14] = isset($item['pesquisaGrid'][14]) ? $item['pesquisaGrid'][14] : '%-%';
                        $item['pesquisaGrid'][15] = isset($item['pesquisaGrid'][15]) ? $item['pesquisaGrid'][15] : 'false';
                        $item['pesquisaGrid'][16] = isset($item['pesquisaGrid'][16]) ? $item['pesquisaGrid'][16] : '';
                        $arquivoIde = 'ide/personalizacaoForm/' . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $id . '-callbackJS';
                        $arquivoApps = Apps::getDirAtivos() . 'personalizacaoForm/' . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $id . '-callbackJS';
                        $arquivoAppsEditor = Apps::getDirEditor() . 'personalizacaoForm/' . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $id . '-callbackJS';
                        $arquivoGlobal = "{$g['pathDados']}personalizacaoForm/" . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $id . '-callbackJS';
                        $arquivo = $arquivoIde;
                        if (!$g['modoDesenvolvedor'] && file_exists($arquivoApps)) {
                            $arquivo = $arquivoApps;
                        }
                        if ($g['modoDesenvolvedorApp'] && file_exists($arquivoAppsEditor)) {
                            $arquivo = $arquivoAppsEditor;
                        }
                        if (!$g['modoDesenvolvedor'] && !$g['modoDesenvolvedorApp'] && file_exists($arquivoGlobal)) {
                            $arquivo = $arquivoGlobal;
                        }
                        if (!(!file_exists($arquivo))) {
                            $item['pesquisaGridCallbackJS'] = file_get_contents($arquivo);
                        }
                        $pesquisaGridCallbackJS = '';
                        if (!(!$item['pesquisaGridCallbackJS'])) {
                            $item['pesquisaGrid'][6] = 'func' . uniqid();
                            $pks = explode(',', $item['pesquisaGrid'][1]);
                            $pesquisaGridCallbackJS = <<<EOT
function {$item['pesquisaGrid'][6]}(...args) {
\tlet array = {};
\tarray['divId'] = args[0];
\tarray['{$pks[0]}'] = args[1];
\tarray['{$pks[1]}'] = args[2];
\tarray['{$pks[2]}'] = args[3];
\tarray['{$pks[3]}'] = args[4];
\t{$item['pesquisaGridCallbackJS']}
}
EOT;
                        }
                        $output .= <<<EOT
\t\t\t<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                {$pesquisaGridCallbackJS}
\t\t\t\t\$(`#{$g['divId']} #\${CSS.escape('{$id}')}_Pesquisa`).bind('keypress', function(e) {
                    const divId = '{$item['pesquisaGrid'][7]}' ? '{$item['pesquisaGrid'][7]}' : '{$g['divId']}'; 
\t\t\t\t\tif (e.keyCode == 13) {
                        // todosCampos
\t\t\t\t\t\tlet todosCampos = {};
\t\t\t\t\t\t\$('#{$g['divId']} .formFormulario:first .itemForm').each(function() {
\t\t\t\t\t\t\ttodosCampos[\$(this).attr('id')] = \$(this).val(); 
\t\t\t\t\t\t});
\t\t\t\t\t\tvar pesquisa = \$(`#{$g['divId']} #\${CSS.escape('{$id}')}_Pesquisa`).val();
\t\t\t\t\t\tif ({$item['pesquisaGrid'][15]}) {
\t\t\t\t\t\t\t// Pesquisa se existe apenas um resultado para pesquisa
\t\t\t\t\t\t\t\$('#{$g['divId']}').showLoading();
\t\t\t\t\t\t\t\$.getJSON('/sistema/funcoes/pesquisaGrid/pesquisa/?ajax=true' + '&pesquisaGridId=' + encodeURIComponent('{$pesquisaGridId}') + '&pesquisa=' + encodeURIComponent(pesquisa) + '&campo1=' + encodeURIComponent('{$item['pesquisaGrid'][0]}') + '&campo2=' + encodeURIComponent('{$item['pesquisaGrid'][1]}') + '&campo3=' + encodeURIComponent('{$item['pesquisaGrid'][2]}') + '&campo4=' + encodeURIComponent('{$item['pesquisaGrid'][3]}') + '&campo5=' + encodeURIComponent('{$item['pesquisaGrid'][4]}') + '&campo6=' + encodeURIComponent('{$item['pesquisaGrid'][5]}') + '&campo7=' + encodeURIComponent('{$item['pesquisaGrid'][6]}') + '&campo8=' + encodeURIComponent(divId) + '&campo9=' + encodeURIComponent('{$item['pesquisaGrid'][8]}') + '&campo10=' + encodeURIComponent('{$item['pesquisaGrid'][9]}') + '&campo11=' + encodeURIComponent('{$item['pesquisaGrid'][10]}') + '&campo12=' + encodeURIComponent('{$item['pesquisaGrid'][11]}') + '&campo13=' + encodeURIComponent('{$item['pesquisaGrid'][12]}') + '&campo14=' + encodeURIComponent('{$item['pesquisaGrid'][13]}') + '&campo15=' + encodeURIComponent('{$item['pesquisaGrid'][14]}') + '&campoPesquisaOrigem=' + encodeURIComponent('{$id}_Pesquisa') + '&todosCampos=' + encodeURIComponent(JSON.stringify(todosCampos)) + '&callback=?', function(request) {
\t\t\t\t\t\t\t\tif (request.code) {
\t\t\t\t\t\t\t\t\t\$('#{$g['divId']}').hideLoading();
\t\t\t\t\t\t\t\t\t{$item['pesquisaGrid'][6]}(divId, request.id1, request.id2, request.id3, request.id4);
\t\t\t\t\t\t\t\t} else if (request.erro) {
\t\t\t\t\t\t\t\t\t\$('#{$g['divId']}').hideLoading();
\t\t\t\t\t\t\t\t\tdialogConfirm('Erro', 'Registro não encontrado');
\t\t\t\t\t\t\t\t} else {
\t\t\t\t\t\t\t\t\t\$('#{$g['divId']}').hideLoading();
\t\t\t\t\t\t\t\t\tabrirJanela(e, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', '{$item['pesquisaGrid'][4]}', '/sistema/funcoes/pesquisaGrid/content/', '&pesquisaGridId=' + encodeURIComponent('{$pesquisaGridId}') + '&pesquisa=' + encodeURIComponent(pesquisa) + '&campo1=' + encodeURIComponent('{$item['pesquisaGrid'][0]}') + '&campo2=' + encodeURIComponent('{$item['pesquisaGrid'][1]}') + '&campo3=' + encodeURIComponent('{$item['pesquisaGrid'][2]}') + '&campo4=' + encodeURIComponent('{$item['pesquisaGrid'][3]}') + '&campo5=' + encodeURIComponent('{$item['pesquisaGrid'][4]}') + '&campo6=' + encodeURIComponent('{$item['pesquisaGrid'][5]}') + '&campo7=' + encodeURIComponent('{$item['pesquisaGrid'][6]}') + '&campo8=' + encodeURIComponent(divId) + '&campo9=' + encodeURIComponent('{$item['pesquisaGrid'][8]}') + '&campo10=' + encodeURIComponent('{$item['pesquisaGrid'][9]}') + '&campo11=' + encodeURIComponent('{$item['pesquisaGrid'][10]}') + '&campo12=' + encodeURIComponent('{$item['pesquisaGrid'][11]}') + '&campo13=' + encodeURIComponent('{$item['pesquisaGrid'][12]}') + '&campo14=' + encodeURIComponent('{$item['pesquisaGrid'][13]}') + '&campo15=' + encodeURIComponent('{$item['pesquisaGrid'][14]}') + '&campoPesquisaOrigem=' + encodeURIComponent('{$id}_Pesquisa') + '&todosCampos=' + encodeURIComponent(JSON.stringify(todosCampos)), {$item['pesquisaGrid'][11]});
\t\t\t\t\t\t\t\t\treturn false;
\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t});
\t\t\t\t\t\t} else {
\t\t\t\t\t\t\tabrirJanela(e, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', '{$item['pesquisaGrid'][4]}', '/sistema/funcoes/pesquisaGrid/content/', '&pesquisaGridId=' + encodeURIComponent('{$pesquisaGridId}') + '&pesquisa=' + encodeURIComponent(pesquisa) + '&campo1=' + encodeURIComponent('{$item['pesquisaGrid'][0]}') + '&campo2=' + encodeURIComponent('{$item['pesquisaGrid'][1]}') + '&campo3=' + encodeURIComponent('{$item['pesquisaGrid'][2]}') + '&campo4=' + encodeURIComponent('{$item['pesquisaGrid'][3]}') + '&campo5=' + encodeURIComponent('{$item['pesquisaGrid'][4]}') + '&campo6=' + encodeURIComponent('{$item['pesquisaGrid'][5]}') + '&campo7=' + encodeURIComponent('{$item['pesquisaGrid'][6]}') + '&campo8=' + encodeURIComponent(divId) + '&campo9=' + encodeURIComponent('{$item['pesquisaGrid'][8]}') + '&campo10=' + encodeURIComponent('{$item['pesquisaGrid'][9]}') + '&campo11=' + encodeURIComponent('{$item['pesquisaGrid'][10]}') + '&campo12=' + encodeURIComponent('{$item['pesquisaGrid'][11]}') + '&campo13=' + encodeURIComponent('{$item['pesquisaGrid'][12]}') + '&campo14=' + encodeURIComponent('{$item['pesquisaGrid'][13]}') + '&campo15=' + encodeURIComponent('{$item['pesquisaGrid'][14]}') + '&campo16=' + encodeURIComponent('{$item['pesquisaGrid'][16]}') +'&campoPesquisaOrigem=' + encodeURIComponent('{$id}_Pesquisa') + '&todosCampos=' + encodeURIComponent(JSON.stringify(todosCampos)), {$item['pesquisaGrid'][11]});
\t\t\t\t\t\t\treturn false;
\t\t\t\t\t\t}
\t\t\t\t\t}
\t\t\t\t});
\t\t\t} </script></div>
EOT;
                    } else {
                        if ($item['type'] == 'data') {
                            $output = "<input type=\"text\" {$options} class=\"campo_input_data itemForm itemForm-{$item['name']} {$item['class']}\" onkeypress=\"return(Formatar_Data('0',this,event,'/'));\" />";
                        } else {
                            if ($item['type'] == 'dataHora') {
                                $output = "<input type=\"text\" {$options} class=\"campo_input_datahora itemForm itemForm-{$item['name']} {$item['class']}\" />";
                            } else {
                                if ($item['type'] == 'moeda') {
                                    $casas = isset($item['casasDecimais']) ? $item['casasDecimais'] : 2;
                                    $output = "<input type=\"text\" {$options} class=\"campo_input_moeda itemForm itemForm-{$item['name']} {$item['class']}\" onkeypress=\"return(Verifica_Caracter(event, this));\" onchange=\"return(Corrige_Valor(event, this,{$casas}));\" />";
                                } else {
                                    if ($item['type'] == 'tempo') {
                                        $output = "<input type=\"text\" {$options} class=\"campo_input_tempo itemForm itemForm-{$item['name']} {$item['class']}\" onkeyup=\"return(Formatar_Tempo(this));\" maxlength=\"6\" size=\"5\" value=\"00:00\"/>";
                                    } else {
                                        if ($item['type'] == 'titulo') {
                                            $output = "";
                                        } else {
                                            if ($item['type'] == 'comentario') {
                                                $output = "<span {$options}>{$item['value']}</span>";
                                            } else {
                                                if ($item['type'] == 'cnpj') {
                                                    $output = "<input type=\"text\" {$options} class=\"campo_input_cnpj itemForm itemForm-{$item['name']} {$item['class']}\" onkeypress=\"return(Formatar_Cnpj('7',this,event));\" maxlength=\"18\" size=\"18\"/>";
                                                } else {
                                                    if ($item['type'] == 'cpf') {
                                                        $output = "<input type=\"text\" {$options} class=\"campo_input_cpf itemForm itemForm-{$item['name']} {$item['class']}\" onkeypress=\"return(Formatar_Cpf('8',this,event));\" maxlength=\"14\" size=\"14\"/>";
                                                    } else {
                                                        if ($item['type'] == 'cep') {
                                                            $output = "<input type=\"text\" {$options} class=\"campo_input_cep itemForm itemForm-{$item['name']} {$item['class']}\" maxlength=\"10\" size=\"15\"/>";
                                                            $output .= <<<EOT
                <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                    \$('#{$g['divId']} .itemForm-{$item['name']}').mask('99999-999');
                } </script></div>
EOT;
                                                        } else {
                                                            $output = "<input {$options} class=\"itemForm itemForm-{$item['name']} {$item['class']}\" />";
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        if (isset($item['disabled']) && $item['disabled']) {
            isset($item['value']) or $item['value'] = '';
            if ($item['type'] == 'select') {
                $item['value'] = $lastSelectValue;
            }
            $options_ = '';
            $options_ .= isset($item['id_show']) && !$item['id_show'] ? '' : "id=\"{$id}\" ";
            $options_ .= !isset($item['array_group']) || $item['array_group'] ? "name=\"{$this->formNome}[{$item['name']}]\" " : "name=\"{$item['name']}\" ";
            $output .= "<input type=\"hidden\" {$options_} value=\"{$item['value']}\" class=\"itemForm itemForm-{$item['name']} {$item['class']}\">";
        }
        if (isset($item['restricao']) && !$item['restricao']) {
            $output .= '';
        }
        if (!(!$item['abrirJanela'])) {
            $titulo = $item['abrirJanelaTitulo'] ? $item['abrirJanelaTitulo'] : 'Janela';
            $tamanho = $item['abrirJanelaTamanho'] ? $item['abrirJanelaTamanho'] : 'auto';
            $parametros = $item['abrirJanelaParametros'];
            if ($tamanho == 'auto') {
                $tamanho = "'auto'";
            }
            $evento = $item['type'] == 'button' ? 'click' : 'keypress';
            $output .= <<<EOT
\t\t\t\t<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
\t\t\t\t    var \$campo = \$('#{$g['divId']} #{$id}');
\t\t\t\t    \$campo.addClass('temAcaoEnter');
\t\t\t\t\t\$campo.bind('{$evento}', function(e) {
\t\t\t\t\t\tif ('{$evento}' == 'click' || e.keyCode == 13) {
\t\t\t\t\t\t\tvar valor = \$campo.val();
                            // Parametros extras
                            var parametrosExtras = '{$parametros}';
                            if (parametrosExtras) {
                                // Faz a substituição de variáveis com campos do form
                                var \$form = \$campo.closest('.formFormulario');
                                for (var item of \$form.serializeArray()) {
\t\t\t\t\t\t\t\t\tvar itemName = item.name.replace(\$form.data('formNome') + '[', '');
\t\t\t\t\t\t\t\t\tvar itemValue = item.value;
\t\t\t\t\t\t\t\t\titemName = itemName.replace(/\\]\$/, '').trim();
                                    parametrosExtras = parametrosExtras.replaceAll('{' + itemName + '}', encodeURIComponent(itemValue));
\t\t\t\t\t\t\t\t}
                            }
\t\t\t\t\t\t\tabrirJanela(e, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', '{$titulo}', '{$item['abrirJanela']}', '&campoAtual=' + encodeURIComponent(valor) + '{$g['repassarParametros']}' + parametrosExtras, {$tamanho});
\t\t\t\t\t\t\treturn false;
\t\t\t\t\t\t}
\t\t\t\t\t});
\t\t\t\t} </script></div>
EOT;
        }
        if (!(!$item['callbackAoSair'])) {
            $formId = md5(trim($g['r_'], '/') . '/');
            $campoId = md5($id);
            $cacheBase = md5($g['pathDados']);
            apc_store("callbackAoSair-{$cacheBase}-{$formId}-{$campoId}", $item['callbackAoSair'], 3600);
            $evento = $item['type'] == 'button' ? 'click' : 'blur';
            $output .= <<<EOT
\t\t\t\t<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
\t\t\t\t\t\$('#{$g['divId']} #{$id}').bind('{$evento}', function(e) {
\t\t\t\t\t\tvar valor = \$('#{$g['divId']} #{$id}').val();
                        var \$form = \$('#{$g['divId']} #{$id}').closest('.formFormulario');
\t\t\t\t\t\t\$.ajax({
\t\t\t\t\t\t\turl: '/sistema/personalizacaoForm/form_func-ajax/callbackAoSair/',
\t\t\t\t\t\t\tdataType: 'html',
\t\t\t\t\t\t\ttype: 'POST',
\t\t\t\t\t\t\tdata: 'ajax=true&divIdParent={$g['divIdParent']}&divId={$g['divId']}&campoId={$campoId}&formId={$formId}&acaoId={$r_acaoId}&campoAtual=' + encodeURIComponent(valor) + '&formNome=' + \$form.data('formNome') + '&' + \$form.serialize(),
\t\t\t\t\t\t\tsuccess: function(data) {
\t\t\t\t\t\t\t\t\$('#{$g['divId']} #{$id}').closest('.formFormulario').append(data);
\t\t\t\t\t\t\t\treturn false;
\t\t\t\t\t\t\t}
\t\t\t\t\t\t});
\t\t\t\t\t});
\t\t\t\t} </script></div>
EOT;
        }
        if (!(!$item['callbackAoAlterar'])) {
            $formId = md5(trim($g['r_'], '/') . '/');
            $campoId = md5($id);
            $cacheBase = md5($g['pathDados']);
            apc_store("callbackAoAlterar-{$cacheBase}-{$formId}-{$campoId}", $item['callbackAoAlterar'], 3600);
            $output .= <<<EOT
\t\t\t\t<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
\t\t\t\t\t\$('#{$g['divId']} #{$id}').bind('change', function(e) {
\t\t\t\t\t\tvar valor = \$('#{$g['divId']} #{$id}').val();
                        var \$form = \$('#{$g['divId']} #{$id}').closest('.formFormulario');
\t\t\t\t\t\t\$.ajax({
\t\t\t\t\t\t\turl: '/sistema/personalizacaoForm/form_func-ajax/callbackAoAlterar/',
\t\t\t\t\t\t\tdataType: 'html',
\t\t\t\t\t\t\ttype: 'POST',
\t\t\t\t\t\t\tdata: 'ajax=true&divIdParent={$g['divIdParent']}&divId={$g['divId']}&campoId={$campoId}&formId={$formId}&acaoId={$r_acaoId}&campoAtual=' + encodeURIComponent(valor) + '&formNome=' + \$form.data('formNome') + '&' + \$form.serialize(),
\t\t\t\t\t\t\tsuccess: function(data) {
\t\t\t\t\t\t\t\t\$form.append(data);
\t\t\t\t\t\t\t\treturn false;
\t\t\t\t\t\t\t}
\t\t\t\t\t\t});
\t\t\t\t\t});
\t\t\t\t} </script></div>
EOT;
        }
        return $output;
    }

    public function formularioID()
    {
        $code = '<input type="hidden" name="' . $this->formNome . '[internal_sent]" value="1">';
        return $code;
    }

    public function processarFormulario($tabela = '', $camposIndividuais = false)
    {
        global $g, $form_editor;
        $this->tabela = $tabela;
        if (!$g['debug']) {
            $this->excluirCamposQueNaoExistem($tabela);
        } else {
            $this->verificarCamposQueNaoExistem($tabela);
        }
        if ($this->verificaSeFoiEnviado()) {
            $tmpValidation = 2;
            $sent = $_REQUEST[$this->formNome];
            $camposValidos = array();
            foreach ($this->formArray['campos'] as $tab => $tabCampos) {
                foreach ($tabCampos as $id => $item) {
                    $this->formArrayValues[$id] = isset($sent[$item['name']]) ? $sent[$item['name']] : null;
                    if ($camposIndividuais && $sent[$item['name']] == '1' && substr($item['name'], 0, 2) == '__') {
                        $camposValidos[substr($item['name'], 2)] = true;
                    }
                }
            }
            if ($camposIndividuais && count($camposValidos) <= 0) {
                $tmpValidation = 3;
            }
            foreach ($this->formArray['campos'] as $tab => $tabCampos) {
                if (!(isset($this->formEditorCache[$tab]))) {
                    $arquivoIde = 'ide/personalizacaoForm/' . 'formEditorData-' . str_replace('/', '-', trim($g['r_'], '/')) . "-{$tab}";
                    $arquivoApps = Apps::getDirAtivos() . 'personalizacaoForm/' . 'formEditorData-' . str_replace('/', '-', trim($g['r_'], '/')) . "-{$tab}";
                    $arquivoAppsEditor = Apps::getDirEditor() . 'personalizacaoForm/' . 'formEditorData-' . str_replace('/', '-', trim($g['r_'], '/')) . "-{$tab}";
                    $arquivoGlobal = "{$g['pathDados']}personalizacaoForm/" . 'formEditorData-' . str_replace('/', '-', trim($g['r_'], '/')) . "-{$tab}";
                    $arquivo = $arquivoIde;
                    if (!$g['modoDesenvolvedor'] && file_exists($arquivoApps)) {
                        $arquivo = $arquivoApps;
                    }
                    if ($g['modoDesenvolvedorApp'] && file_exists($arquivoAppsEditor)) {
                        $arquivo = $arquivoAppsEditor;
                    }
                    if (!$g['modoDesenvolvedor'] && !$g['modoDesenvolvedorApp'] && file_exists($arquivoGlobal)) {
                        $arquivo = $arquivoGlobal;
                    }
                    if (strpos($arquivo, "sistema-personalizacaoGrid-form") !== false) {
                        $arquivo = '';
                    }
                    $formEditorData = array();
                    $formEditorUtilizado = false;
                    if (!(!file_exists($arquivo))) {
                        $formEditorData = json_decode(file_get_contents($arquivo), true);
                        $formEditorUtilizado = true;
                    }
                    if (isset($form_editor) && isset($form_editor[$tab]) && !empty($form_editor[$tab])) {
                        if (!empty($formEditorData)) {
                            $formEditorData_ = json_decode($form_editor[$tab], true);
                            $formEditorData = array_merge($formEditorData_, $formEditorData);
                        } else {
                            $formEditorData = json_decode($form_editor[$tab], true);
                        }
                        $formEditorUtilizado = true;
                    }
                    if ($formEditorUtilizado) {
                        $this->formEditorCache[$tab] = $formEditorData;
                    } else {
                        $this->formEditorCache[$tab] = false;
                    }
                }
                foreach ($tabCampos as $id => $item) {
                    if (!($item['type'] != 'file' && !isset($sent[$item['name']]) && $item['type'] != 'button' && $item['type'] != 'submit' && $item['type'] != 'enviarForm' || $camposIndividuais == true && !isset($camposValidos[$item['name']]))) {
                        if (!($item['type'] != 'hidden' && $this->formEditorCache[$tab] && (!isset($this->formEditorCache[$tab][$id]) || isset($this->formEditorCache[$tab][$id]['hidden']) && $this->formEditorCache[$tab][$id]['hidden']))) {
                            $this->formArray['campos'][$tab][$id]['processed'] = true;
                            $this->formArray['campos'][$tab][$id]['processed_value'] = isset($sent[$item['name']]) ? $sent[$item['name']] : null;
                            switch ($item['type']) {
                                case 'radio':
                                    if (!isset($item['reload']) || $item['reload']) {
                                        $this->formArray['campos'][$tab][$id]['checked'] = $sent[$item['name']] == $item['value'] ? true : false;
                                    }
                                    break;
                                case 'checkbox':
                                    if (!isset($item['reload']) || $item['reload']) {
                                        $this->formArray['campos'][$tab][$id]['checked'] = isset($sent[$item['name']]) && $sent[$item['name']] == 'on' ? true : false;
                                    }
                                    break;
                                case 'select':
                                    if (!isset($item['reload']) || $item['reload']) {
                                        foreach ($item['select'] as $optionKey => $option) {
                                            $this->formArray['campos'][$tab][$id]['select'][$optionKey]['selected'] = isset($sent[$item['name']]) && $sent[$item['name']] == (isset($option['value']) ? $option['value'] : false) ? true : false;
                                        }
                                    }
                                    @($this->formArray['campos'][$tab][$id]['value'] = !isset($item['reload']) || $item['reload'] ? $sent[$item['name']] : (isset($item['value']) ? $item['value'] : ''));
                                    break;
                                case 'file':
                                    $error_msg = '';
                                    if (!isset($_FILES[$this->formNome])) {
                                    } else {
                                        if ($_FILES[$this->formNome]['error'][$item['name']] > 0) {
                                            if (($_FILES[$this->formNome]['error'][$item['name']] == UPLOAD_ERR_INI_SIZE) || ($_FILES[$this->formNome]['error'][$item['name']] == UPLOAD_ERR_FORM_SIZE)) {
                                                $error_msg = 'Arquivo é maior que o limite permitido.';
                                            } elseif (($_FILES[$this->formNome]['error'][$item['name']] == UPLOAD_ERR_PARTIAL)) {
                                                $error_msg = 'Erro ao enviar o arquivo. Por favor, tente novamente.';
                                            } elseif (($_FILES[$this->formNome]['error'][$item['name']] == UPLOAD_ERR_NO_FILE)) {
                                            } elseif (($_FILES[$this->formNome]['error'][$item['name']] == UPLOAD_ERR_INI_SIZE)) {
                                                $error_msg = 'Arquivo é maior que o limite permitido.';
                                            } elseif (($_FILES[$this->formNome]['error'][$item['name']] == UPLOAD_ERR_NO_TMP_DIR)) {
                                                $error_msg = 'Erro interno. Por favor, contate o suporte. Erro: Diretório temporário não encontrado.';
                                            } elseif (($_FILES[$this->formNome]['error'][$item['name']] == UPLOAD_ERR_CANT_WRITE)) {
                                                $error_msg = 'Erro interno. Por favor, contate o suporte. Erro: Não foi possivel gravar no diretório temporário.';
                                            } elseif (($_FILES[$this->formNome]['error'][$item['name']] == UPLOAD_ERR_EXTENSION)) {
                                                $error_msg = 'Erro interno. Por favor, contate o suporte. Erro: UPLOAD_ERR_EXTENSION';
                                            }
                                        }
                                    }
                                    $this->formArray['campos'][$tab][$id]['processed_value'] = array('name' => isset($_FILES[$this->formNome]) ? $_FILES[$this->formNome]['name'][$item['name']] : '', 'type' => isset($_FILES[$this->formNome]) ? $_FILES[$this->formNome]['type'][$item['name']] : '', 'tmp_name' => isset($_FILES[$this->formNome]) ? $_FILES[$this->formNome]['tmp_name'][$item['name']] : '', 'error' => empty($error_msg) ? false : (isset($_FILES[$this->formNome]) ? $_FILES[$this->formNome]['error'][$item['name']] : UPLOAD_ERR_NO_FILE), 'error_msg' => $error_msg, 'size' => isset($_FILES[$this->formNome]) ? $_FILES[$this->formNome]['size'][$item['name']] : 0);
                                    break;
                                case 'pesquisa':
                                    $tmp_tabela = $this->formArray['campos'][$tab][$id]['pesquisa'][0];
                                    $tmp_campo = $this->formArray['campos'][$tab][$id]['pesquisa'][1];
                                    $tmp_valor = $this->formArray['campos'][$tab][$id]['pesquisa'][2];
                                    $extra = isset($this->formArray['campos'][$tab][$id]['pesquisa'][3]) ? $this->formArray['campos'][$tab][$id]['pesquisa'][3] : 'WHERE (1=1)';
                                    $sqlExtra = "({$tmp_campo} = '{$this->formArray['campos'][$tab][$id]['processed_value']}')";
                                    $extra = gInsertExtraWhere($extra, $sqlExtra);
                                    $retorno = gGeraSelect($tmp_tabela, $tmp_campo, $tmp_valor, $extra);
                                    $retorno = isset($retorno[0]) ? $retorno[0] : array('title' => '', 'value' => '');
                                    $this->formArray['campos'][$tab][$id]['value'] = $retorno['value'];
                                    $this->formArray['campos'][$tab][$id]['value_Pesquisa'] = $retorno['title'];
                                    break;
                                case 'pesquisaGrid':
                                    $tmp_tabela = $this->formArray['campos'][$tab][$id]['pesquisaGrid'][0];
                                    $tmp_campo = $this->formArray['campos'][$tab][$id]['pesquisaGrid'][1];
                                    $extra = $this->formArray['campos'][$tab][$id]['pesquisaGrid'][3];
                                    $sqlExtra = gInsertExtraWhere($extra, "{$tmp_campo} = '{$this->formArray['campos'][$tab][$id]['processed_value']}'");
                                    $retorno = gGeraSelect($tmp_tabela, $tmp_campo, $tmp_campo, $sqlExtra);
                                    $retorno = isset($retorno[0]) ? $retorno[0] : array('title' => '', 'value' => '');
                                    $this->formArray['campos'][$tab][$id]['value'] = $retorno['value'];
                                    $this->formArray['campos'][$tab][$id]['value_Pesquisa'] = $retorno['title'];
                                    break;
                                case 'button':
                                    break;
                                case 'submit':
                                case 'enviarForm':
                                    break;
                                default:
                                    isset($sent[$item['name']]) or $sent[$item['name']] = '';
                                    $this->formArray['campos'][$tab][$id]['value'] = !isset($item['reload']) || $item['reload'] ? $sent[$item['name']] : (isset($item['value']) ? $item['value'] : '');
                                    break;
                            }
                            $toCheck = $id;
                            if (isset($item['restricao']) && $item['restricao']) {
                                $toCheck = $item['restricao'];
                            }
                            if (isset($item['restricao']) && !$item['restricao']) {
                                $restricao = true;
                            } else {
                                $restricao = $g['conf']->restricaoIncluirUpdate($toCheck, $this->campoValorEnviado($id), $this->formArrayValues, $this->acaoId);
                            }
                            if ($item['callbackRestricao']) {
                                $callbackRestricaoRetorno = Personalizacoes::carregarExecutarCallback($item['callbackRestricao'], array('campoAtual' => $this->campoValorEnviado($id), 'todosCampos' => $this->formArrayValues));
                                if ($callbackRestricaoRetorno && $callbackRestricaoRetorno !== true) {
                                    $this->formArray['campos'][$tab][$id]['validation'] = false;
                                    $this->formArray['error_msg'][$id] = $callbackRestricaoRetorno;
                                } else {
                                    $this->formArray['campos'][$tab][$id]['validation'] = true;
                                }
                            } else {
                                if ($restricao === true) {
                                    $this->formArray['campos'][$tab][$id]['validation'] = true;
                                } else {
                                    $this->formArray['campos'][$tab][$id]['validation'] = false;
                                    $this->formArray['error_msg'][$id] = $restricao;
                                }
                            }
                            if ($item['type'] == 'file') {
                                $this->formArray['campos'][$tab][$id]['validation'] = empty($this->formArray['campos'][$tab][$id]['processed_value']['error']) ? true : false;
                                $this->formArray['error_msg'][$id] = $this->formArray['campos'][$tab][$id]['processed_value']['error_msg'];
                            }
                            $tmpValidation = !$this->formArray['campos'][$tab][$id]['validation'] ? 1 : $tmpValidation;
                        }
                    } else {
                        unset($this->formArray['campos'][$tab][$id]);
                    }
                }
            }
            return $tmpValidation;
        } else {
            return false;
        }
    }

    public function carregarValoresCampos($table, $campoPk, $campoValor, $sqlExtra = '', $apenasTodosCampos = false)
    {
        global $g, $r_todosCampos;
        if (!($apenasTodosCampos && $r_todosCampos)) {
            $sql = "DESC {$table};";
            $result = mysql_query($sql);
            if (!mysql_error()) {
                $fields = array();
                while ($row = mysql_fetch_assoc($result)) {
                    $fields[$row['Field']] = 1;
                }
                $sql = "DESC {$table}A;";
                $result = mysql_query($sql);
                if (!(mysql_error())) {
                    while ($row = mysql_fetch_assoc($result)) {
                        $fields[$row['Field']] = 1;
                    }
                }
                $sql = "SHOW TABLES LIKE '{$table}A'";
                $tabelaIrma = mysql_fetch_array(mysql_query($sql));
                $tabelaIrma = $tabelaIrma ? "LEFT JOIN {$table}A ON {$table}A_{$table}_Id = {$table}_Id" : '';
                $sql = <<<EOT
SELECT * FROM {$table} 
{$tabelaIrma}
WHERE {$campoPk} = '{$campoValor}' {$sqlExtra} 
EOT;
                $result = mysql_query($sql);
                if (!mysql_error()) {
                    $row = mysql_fetch_assoc($result);
                    foreach ($row as $key => $value) {
                        if (preg_match('/^\\{.*\\}$/mi', $value)) {
                            $valueJson = json_decode($value);
                            if (is_object($valueJson)) {
                                foreach ($valueJson as $key2 => $value2) {
                                    $row["{$key}->{$key2}"] = $value2;
                                }
                            }
                        }
                    }
                    if (!$apenasTodosCampos) {
                        $erro = '';
                        foreach ($this->formArray['campos'] as $tab => $tabCampos) {
                            foreach ($tabCampos as $id => $item) {
                                $idCampoJson = false;
                                if (strpos($id, '->') !== false) {
                                    $idCampoJson = explode('->', $id);
                                    $idCampoJson = trim($idCampoJson[0]);
                                }
                                if (!isset($fields[$idCampoJson ? $idCampoJson : $id]) && !isset($this->formArray['campos'][$tab][$id]['reject'])) {
                                    $erro .= "<br/>Campo {$id} não existe na tabela {$table}.<br/>";
                                } else {
                                    if (!(isset($this->formArray['campos'][$tab][$id]['reject']) && $this->formArray['campos'][$tab][$id]['reject'])) {
                                        if ($this->formArray['campos'][$tab][$id]['type'] == 'pesquisa') {
                                            $tmp_tabela = $this->formArray['campos'][$tab][$id]['pesquisa'][0];
                                            $tmp_campo = $this->formArray['campos'][$tab][$id]['pesquisa'][1];
                                            $tmp_valor = $this->formArray['campos'][$tab][$id]['pesquisa'][2];
                                            $extra = isset($this->formArray['campos'][$tab][$id]['pesquisa'][3]) ? $this->formArray['campos'][$tab][$id]['pesquisa'][3] : 'WHERE (1=1)';
                                            $formato = isset($this->formArray['campos'][$tab][$id]['pesquisa'][4]) ? $this->formArray['campos'][$tab][$id]['pesquisa'][4] : false;
                                            $sqlExtra = "({$tmp_campo} = '{$row[$id]}')";
                                            $extra = gInsertExtraWhere($extra, $sqlExtra);
                                            foreach ($this->formArrayTodosCampos as $key => $value) {
                                                $extra = str_replace("[[{$key}]]", $value, $extra);
                                            }
                                            $retorno = gGeraSelect($tmp_tabela, $tmp_campo, $tmp_valor, $extra, false, false, $formato);
                                            $retorno = isset($retorno[0]) ? $retorno[0] : array('title' => '', 'value' => '');
                                            $this->formArray['campos'][$tab][$id]['value'] = $retorno['value'];
                                            $this->formArray['campos'][$tab][$id]['value_Pesquisa'] = $retorno['title'];
                                        } else {
                                            if ($this->formArray['campos'][$tab][$id]['type'] == 'pesquisaGrid') {
                                                $tmp_tabela = $this->formArray['campos'][$tab][$id]['pesquisaGrid'][0];
                                                $tmp_campo = $this->formArray['campos'][$tab][$id]['pesquisaGrid'][1];
                                                $extra = $this->formArray['campos'][$tab][$id]['pesquisaGrid'][3];
                                                $sqlExtra = gInsertExtraWhere($extra, "{$tmp_campo} = '{$row[$id]}'");
                                                $retorno = gGeraSelect($tmp_tabela, $tmp_campo, $tmp_campo, $sqlExtra);
                                                $retorno = isset($retorno[0]) ? $retorno[0] : array('title' => '', 'value' => '');
                                                $this->formArray['campos'][$tab][$id]['value'] = $retorno['value'];
                                                $this->formArray['campos'][$tab][$id]['value_Pesquisa'] = $retorno['title'];
                                            } else {
                                                if ($this->formArray['campos'][$tab][$id]['type'] == 'select') {
                                                    foreach ($this->formArray['campos'][$tab][$id]['select'] as &$option) {
                                                        if (isset($option['value']) && $option['value'] == $row[$id]) {
                                                            $found = true;
                                                            $option['selected'] = true;
                                                        } else {
                                                            unset($option['selected']);
                                                        }
                                                    }
                                                    if (!(!isset($row[$id]))) {
                                                        $this->formArray['campos'][$tab][$id]['value'] = $row[$id];
                                                    }
                                                } else {
                                                    if ($this->formArray['campos'][$tab][$id]['type'] == 'data') {
                                                        $data = explode(' ', $row[$id]);
                                                        $data = explode('-', $data[0]);
                                                        @($this->formArray['campos'][$tab][$id]['value'] = "{$data[2]}/{$data[1]}/{$data[0]}");
                                                    } else {
                                                        if ($this->formArray['campos'][$tab][$id]['type'] == 'dataHora') {
                                                            $dataHora = explode(' ', $row[$id]);
                                                            $data = explode('-', $dataHora[0]);
                                                            $hora = isset($dataHora[1]) ? $dataHora[1] : '00:00:00';
                                                            @($this->formArray['campos'][$tab][$id]['value'] = "{$data[2]}/{$data[1]}/{$data[0]} {$hora}");
                                                        } else {
                                                            if ($this->formArray['campos'][$tab][$id]['type'] == 'moeda') {
                                                                $casas = isset($this->formArray['campos'][$tab][$id]['casasDecimais']) ? $this->formArray['campos'][$tab][$id]['casasDecimais'] : 2;
                                                                $casasConf = $g['conf']->camposTipo($id);
                                                                if ($casasConf) {
                                                                    $casas = $casasConf[1];
                                                                }
                                                                $this->formArray['campos'][$tab][$id]['value'] = @number_format($row[$id], $casas, ',', '.');
                                                            } else {
                                                                if ($this->formArray['campos'][$tab][$id]['type'] == 'tempo') {
                                                                    $row[$id] = trim($row[$id]);
                                                                    if (!(empty($row[$id]))) {
                                                                        $temp = explode(':', $row[$id]);
                                                                        $this->formArray['campos'][$tab][$id]['value'] = $temp[0] . ':' . $temp[1];
                                                                    }
                                                                } else {
                                                                    if (!(!isset($row[$id]))) {
                                                                        $this->formArray['campos'][$tab][$id]['value'] = $row[$id];
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        if (!empty($erro)) {
                            return gGeraAlertMsg('$form->carregarValoresCampos()', $erro);
                        } else {
                            return true;
                        }
                    } else {
                        $this->formArrayTodosCampos = $row;
                        return;
                    }
                } else {
                    return gGeraAlertMsg('Erro em operação no banco de dados', "Por favor, contate o suporte técnico.<br/><br/>Erro:<br/>" . mysql_error() . "<br/><br/>SQL:<br/>{$sql}");
                }
            } else {
                return gGeraAlertMsg('Erro em operação no banco de dados', "Por favor, contate o suporte técnico.<br/><br/>Erro:<br/>" . mysql_error() . "<br/><br/>SQL:<br/>{$sql}");
            }
        } else {
            $this->formArrayTodosCampos = $r_todosCampos;
            return;
        }
    }

    public function excluirCamposQueNaoExistem($table)
    {
        $sql = "DESC {$table};";
        $result = mysql_query($sql);
        if (!mysql_error()) {
            $fields = array();
            while ($row = mysql_fetch_assoc($result)) {
                $fields[$row['Field']] = 1;
            }
            $erro = '';
            foreach ($this->formArray['campos'] as $tab => $tabCampos) {
                foreach ($tabCampos as $id => $item) {
                    if (!isset($fields[$id]) && !isset($this->formArray['campos'][$tab][$id]['reject'])) {
                        unset($this->formArray['campos'][$tab][$id]);
                    }
                }
            }
        } else {
            return gGeraAlertMsg('Erro em operação no banco de dados', "Por favor, contate o suporte técnico.<br/><br/>Erro:<br/>" . mysql_error() . "<br/><br/>SQL:<br/>{$sql}");
        }
    }

    public function verificarCamposQueNaoExistem($table)
    {
        if (!empty($table)) {
            $sql = "DESC {$table};";
            $result = mysql_query($sql);
            if (!mysql_error()) {
                $fields = array();
                while ($row = mysql_fetch_assoc($result)) {
                    $fields[$row['Field']] = 1;
                }
                $erro = '';
                foreach ($this->formArray['campos'] as $tab => $tabCampos) {
                    foreach ($tabCampos as $id => $item) {
                        if (!isset($fields[$id]) && !isset($this->formArray['campos'][$tab][$id]['reject'])) {
                            $erro .= "<br/>Campo {$id} não existe na tabela {$table}.<br/>";
                        }
                    }
                }
                if (!empty($erro)) {
                    return gGeraAlertMsg('$form->verificarCamposQueNaoExistem()', $erro);
                } else {
                    return true;
                }
            } else {
                return gGeraAlertMsg('Erro em operação no banco de dados', "Por favor, contate o suporte técnico.<br/><br/>Erro:<br/>" . mysql_error() . "<br/><br/>SQL:<br/>{$sql}");
            }
        } else {
            return false;
        }
    }

    public function temErro($id = false)
    {
        if ($this->verificaSeFoiEnviado()) {
            if (empty($id)) {
                foreach ($this->formArray['campos'] as $tab => $tabCampos) {
                    foreach ($tabCampos as $id => $item) {
                        if (!($item['type'] != 'hidden' && $this->formEditorCache[$tab] && (!isset($this->formEditorCache[$tab][$id]) || isset($this->formEditorCache[$tab][$id]['hidden']) && $this->formEditorCache[$tab][$id]['hidden']))) {
                            if ($item['validation']) {
                            } else {
                                return true;
                            }
                        }
                    }
                }
            } else {
                return isset($this->formArray['campos'][$this->formTabId[$id]][$id]['validation']) && !$this->formArray['campos'][$this->formTabId[$id]][$id]['validation'] ? true : false;
            }
        } else {
            return false;
        }
    }

    public function pegarErro($id)
    {
        if ($this->verificaSeFoiEnviado()) {
            return isset($this->formArray['campos'][$this->formTabId[$id]][$id]['validation']) && !$this->formArray['campos'][$this->formTabId[$id]][$id]['validation'] ? $this->formArray['error_msg'][$id] : '';
        } else {
            return false;
        }
    }

    public function setarErro($id)
    {
        if ($this->verificaSeFoiEnviado()) {
            $this->formArray['campos'][$this->formTabId[$id]][$id]['validation'] = false;
            return true;
        } else {
            return false;
        }
    }

    public function campoSetarSelect($id, $value)
    {
        if (isset($this->formTabId[$id])) {
            $this->formArray['campos'][$this->formTabId[$id]][$id]['select'] = $value;
            return true;
        } else {
            return false;
        }
    }

    public function campoSetarValor($id, $value, $proccessed = false)
    {
        if (isset($this->formTabId[$id])) {
            if (isset($this->formArray['campos'][$this->formTabId[$id]][$id]['select'])) {
                foreach ($this->formArray['campos'][$this->formTabId[$id]][$id]['select'] as &$itemSelect) {
                    if ($itemSelect['value'] == $value) {
                        $itemSelect['selected'] = true;
                    } else {
                        $itemSelect['selected'] = false;
                    }
                }
            } else {
                $this->formArray['campos'][$this->formTabId[$id]][$id]['value'] = $value;
            }
            if ($proccessed) {
                $this->formArray['campos'][$this->formTabId[$id]][$id]['processed_value'] = $value;
            }
            return true;
        } else {
            return false;
        }
    }

    public function campoSetarValorPesquisa($id, $value)
    {
        if (isset($this->formTabId[$id])) {
            $this->formArray['campos'][$this->formTabId[$id]][$id]['value_Pesquisa'] = $value;
            return true;
        } else {
            return false;
        }
    }

    public function campoValorEnviado($id)
    {
        if (isset($this->formTabId[$id])) {
            return isset($this->formArray['campos'][$this->formTabId[$id]][$id]['processed_value']) ? $this->formArray['campos'][$this->formTabId[$id]][$id]['processed_value'] : false;
        } else {
            return isset($_REQUEST[$this->formNome][$id]) ? $_REQUEST[$this->formNome][$id] : false;
        }
    }

    public function campoValor($id)
    {
        if (isset($this->formTabId[$id])) {
            return isset($this->formArray['campos'][$this->formTabId[$id]][$id]['value']) ? $this->formArray['campos'][$this->formTabId[$id]][$id]['value'] : null;
        } else {
            return false;
        }
    }

    public function isChecked($id)
    {
        return!isset($this->formArray['campos'][$this->formTabId[$id]][$id]['checked']) || !$this->formArray['campos'][$this->formTabId[$id]][$id]['checked'] ? false : true;
    }

    public function checkedIt($id)
    {
        $this->formArray['campos'][$this->formTabId[$id]][$id]['checked'] = true;
        return true;
    }

    public function isDisabled($id)
    {
        return isset($this->formArray['campos'][$this->formTabId[$id]][$id]['disabled']) && $this->formArray['campos'][$this->formTabId[$id]][$id]['disabled'] ? true : false;
    }

    public function verificaSeFoiEnviado()
    {
        return empty($_REQUEST[$this->formNome]['internal_sent']) ? false : true;
    }

    public function limpar()
    {
        $this->formArray = $this->formArrayOriginal;
        $this->formArrayValues = $this->formArrayValuesOriginal;
        unset($_REQUEST[$this->formNome]);
        $this->processarFormulario($this->tabela);
    }

}
