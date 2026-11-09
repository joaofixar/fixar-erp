<?php
/**
 * personalizacoes.php
 *
 * Versão legível de sistema-legado/sistema/personalizacoes.php.
 * O original foi ofuscado com desvios; aqui o fluxo foi reconstruído
 * em if/else/while/foreach, no mesmo comportamento.
 * Este arquivo é referência de leitura e não é carregado pelo sistema.
 */
namespace hardness;

class Personalizacoes
{
    public static $rootDir = null;

    public static function getDirRoot()
    {
        global $g;
        return self::$rootDir ? self::$rootDir : $g['pathDados'];
    }

    public static function getDirPersonalizacoesGrid()
    {
        global $g;
        return self::getDirRoot() . "personalizacaoGrid/";
    }

    public static function getDirGrids()
    {
        global $g;
        return self::getDirRoot() . "personalizacaoGrid/grids/";
    }

    public static function getDirPersonalizacoesTab()
    {
        global $g;
        return self::getDirRoot() . "personalizacaoTab/";
    }

    public static function getDirTabs()
    {
        global $g;
        return self::getDirRoot() . "personalizacaoTab/tabs/";
    }

    public static function getDirPersonalizacoesBotoes()
    {
        global $g;
        return self::getDirRoot() . "personalizacaoBotoes/";
    }

    public static function getDirPersonalizacoesContent()
    {
        global $g;
        return self::getDirRoot() . "personalizacaoContent/";
    }

    public static function getDirContents()
    {
        global $g;
        return self::getDirRoot() . "personalizacaoContent/contents/";
    }

    public static function getDirPersonalizacoesForm()
    {
        global $g;
        return self::getDirRoot() . "personalizacaoForm/";
    }

    public static function getDirForms()
    {
        global $g;
        return self::getDirRoot() . "personalizacaoForm/forms/";
    }

    public static function getDirPersonalizacoesOutro()
    {
        global $g;
        return self::getDirRoot() . "personalizacaoOutro/";
    }

    public static function getDirOutros()
    {
        global $g;
        return self::getDirRoot() . "personalizacaoOutro/outros/";
    }

    public static function getDirPersonalizacoesCodigo()
    {
        global $g;
        return self::getDirRoot() . "personalizacaoCodigo/";
    }

    public static function getDirCodigos()
    {
        global $g;
        return self::getDirRoot() . "personalizacaoCodigo/codigos/";
    }

    public static function getDirSistema()
    {
        global $g;
        return self::getDirRoot() . "sistema/";
    }

    public static function getDirLogs()
    {
        global $g;
        return $g['pathDados'] . "logs/";
    }

    public static function getDirLogsCrontab()
    {
        global $g;
        return $g['pathDados'] . "logs/crontab/";
    }

    public static function getDirPersonalizacoesConf()
    {
        global $g;
        return self::getDirRoot() . "personalizacaoConf/";
    }

    public static function getDirPersonalizacoesTela()
    {
        global $g;
        return self::getDirRoot() . "personalizacaoTela/";
    }

    public static function getDirTelas()
    {
        global $g;
        return self::getDirRoot() . "personalizacaoTela/telas/";
    }

    public static function getDirPersonalizacoesExcel()
    {
        global $g;
        return self::getDirRoot() . "personalizacaoExcel/";
    }

    public static function getDirExcels()
    {
        global $g;
        return self::getDirRoot() . "personalizacaoExcel/excels/";
    }

    public static function getDirPersonalizacoesBI()
    {
        global $g;
        return self::getDirRoot() . "personalizacaoBI/";
    }

    public static function getDirBIs()
    {
        global $g;
        return self::getDirRoot() . "personalizacaoBI/BIs/";
    }

    public static function getDirPersonalizacoesIndicador()
    {
        global $g;
        return self::getDirRoot() . "personalizacaoIndicador/";
    }

    public static function getDirIndicadores()
    {
        global $g;
        return self::getDirRoot() . "personalizacaoIndicador/indicadores/";
    }

    public static function getDirPersonalizacoesDashboard()
    {
        global $g;
        return self::getDirRoot() . "personalizacaoDashboard/";
    }

    public static function getDirDashboards()
    {
        global $g;
        return self::getDirRoot() . "personalizacaoDashboard/dashboards/";
    }

    public static function getDirPersonalizacoesFormula()
    {
        global $g;
        return self::getDirRoot() == 'formulas/' ? self::getDirRoot() : self::getDirRoot() . "personalizacaoFormula/";
    }

    public static function getDirPersonalizacoesBanco()
    {
        global $g;
        return self::getDirRoot() . "personalizacaoBanco/";
    }

    public static function getDirPersonalizacoesBancoCodigos()
    {
        global $g;
        return self::getDirRoot() . "personalizacaoBanco/codigos/";
    }

    public static function criarDiretorios()
    {
        global $g;
        $dir = self::getDirPersonalizacoesGrid();
        is_dir($dir) or mkdir($dir, 0777, true);
        $dir = self::getDirGrids();
        is_dir($dir) or mkdir($dir, 0777, true);
        $dir = self::getDirPersonalizacoesTab();
        is_dir($dir) or mkdir($dir, 0777, true);
        $dir = self::getDirTabs();
        is_dir($dir) or mkdir($dir, 0777, true);
        $dir = self::getDirPersonalizacoesBotoes();
        is_dir($dir) or mkdir($dir, 0777, true);
        $dir = self::getDirPersonalizacoesContent();
        is_dir($dir) or mkdir($dir, 0777, true);
        $dir = self::getDirContents();
        is_dir($dir) or mkdir($dir, 0777, true);
        $dir = self::getDirPersonalizacoesForm();
        is_dir($dir) or mkdir($dir, 0777, true);
        $dir = self::getDirForms();
        is_dir($dir) or mkdir($dir, 0777, true);
        $dir = self::getDirPersonalizacoesOutro();
        is_dir($dir) or mkdir($dir, 0777, true);
        $dir = self::getDirOutros();
        is_dir($dir) or mkdir($dir, 0777, true);
        $dir = self::getDirPersonalizacoesCodigo();
        is_dir($dir) or mkdir($dir, 0777, true);
        $dir = self::getDirCodigos();
        is_dir($dir) or mkdir($dir, 0777, true);
        $dir = self::getDirSistema();
        is_dir($dir) or mkdir($dir, 0777, true);
        $dir = self::getDirLogs();
        is_dir($dir) or mkdir($dir, 0777, true);
        $dir = self::getDirLogsCrontab();
        is_dir($dir) or mkdir($dir, 0777, true);
        $dir = self::getDirPersonalizacoesConf();
        is_dir($dir) or mkdir($dir, 0777, true);
        $dir = self::getDirPersonalizacoesTela();
        is_dir($dir) or mkdir($dir, 0777, true);
        $dir = self::getDirTelas();
        is_dir($dir) or mkdir($dir, 0777, true);
        $dir = self::getDirPersonalizacoesExcel();
        is_dir($dir) or mkdir($dir, 0777, true);
        $dir = self::getDirExcels();
        is_dir($dir) or mkdir($dir, 0777, true);
        $dir = self::getDirPersonalizacoesBI();
        is_dir($dir) or mkdir($dir, 0777, true);
        $dir = self::getDirBIs();
        is_dir($dir) or mkdir($dir, 0777, true);
        $dir = self::getDirPersonalizacoesIndicador();
        is_dir($dir) or mkdir($dir, 0777, true);
        $dir = self::getDirIndicadores();
        is_dir($dir) or mkdir($dir, 0777, true);
        $dir = self::getDirPersonalizacoesDashboard();
        is_dir($dir) or mkdir($dir, 0777, true);
        $dir = self::getDirDashboards();
        is_dir($dir) or mkdir($dir, 0777, true);
        $dir = self::getDirPersonalizacoesFormula();
        is_dir($dir) or mkdir($dir, 0777, true);
        $dir = self::getDirPersonalizacoesBanco();
        is_dir($dir) or mkdir($dir, 0777, true);
        $dir = self::getDirPersonalizacoesBancoCodigos();
        is_dir($dir) or mkdir($dir, 0777, true);
    }

    public static function getColunasOcultasUsuario($gridId, $usuario = null)
    {
        global $g;
        $retorno = array();
        $usuario = $usuario ? $usuario : $g['usuarioAtual'];
        $dir = self::getDirPersonalizacoesGrid();
        $arquivo = $dir . $gridId . '-ocultar-' . $usuario;
        if (!(!file_exists($arquivo))) {
            $ocultar = file($arquivo);
            foreach ($ocultar as $linha) {
                $linha = trim($linha);
                $linha = explode("\t|\t", $linha);
                $retorno[$linha[1]] = $linha[0];
            }
        }
        return $retorno;
    }

    public static function salvarColunasOcultasUsuario($gridId, $colunasOcultasUsuario, $usuario = null)
    {
        global $g;
        $usuario = $usuario ? $usuario : $g['usuarioAtual'];
        $dir = self::getDirPersonalizacoesGrid();
        $arquivo = $dir . $gridId . '-ocultar-' . $usuario;
        $texto = '';
        foreach ($colunasOcultasUsuario as $key => $value) {
            $texto .= $texto ? "\n" : '';
            $texto .= $key . "\t|\t" . $value;
        }
        file_put_contents($arquivo, $texto);
        return true;
    }

    public static function getGrid($gridId)
    {
        $retorno = array();
        $gridPHP = apc_fetch("grid-php-{$gridId}");
        if ($gridPHP) {
            $retorno = unserialize($gridPHP);
        }
        return $retorno;
    }

    public static function getPersonalizacao($itemId, $pre = '', $pos = '', $tipo = 'grid', $modoDesenvolvedor = false)
    {
        global $g;
        $rootDir = Personalizacoes::$rootDir;
        if ($modoDesenvolvedor == 'IDE') {
            Personalizacoes::$rootDir = 'ide/';
        } else {
            if ($modoDesenvolvedor == 'APPS') {
                $rootDir = Personalizacoes::$rootDir;
                Personalizacoes::$rootDir = Apps::getDirAtivos();
            } else {
                if ($modoDesenvolvedor == 'APPS-EDITOR') {
                    $rootDir = Personalizacoes::$rootDir;
                    Personalizacoes::$rootDir = Apps::getDirEditor();
                } else {
                    if ($tipo == 'formula' && $modoDesenvolvedor == 'PHP') {
                        Personalizacoes::$rootDir = 'formulas/';
                    } else {
                        if ($modoDesenvolvedor == 'GLOBAL') {
                            Personalizacoes::$rootDir = $g['pathDados'];
                        }
                    }
                }
            }
        }
        $retorno = array();
        if ($tipo == 'content') {
            $dir = self::getDirPersonalizacoesContent();
        } else {
            if ($tipo == 'tabs') {
                $dir = self::getDirPersonalizacoesTab();
            } else {
                if ($tipo == 'form') {
                    $dir = self::getDirPersonalizacoesForm();
                } else {
                    if ($tipo == 'grid') {
                        $dir = self::getDirPersonalizacoesGrid();
                    } else {
                        if ($tipo == 'formula') {
                            $dir = self::getDirPersonalizacoesFormula();
                        } else {
                            die("Por favor, entre em contado com o suporte técnico. Tipo de personalização inválida: {$tipo}");
                        }
                    }
                }
            }
        }
        $arquivo = $dir . $pre . $itemId . $pos;
        if (!(!file_exists($arquivo))) {
            $personalizacao = file_get_contents($arquivo);
            $personalizacao = gSubstituirVariaveisParametros($personalizacao);
            $personalizacao = preg_replace('/\\-\\-\\-$/', '', trim($personalizacao));
            $personalizacao .= "\n---";
            preg_match_all("/(.+)---/msU", $personalizacao, $matches);
            foreach ($matches[0] as $stringItem) {
                $item = array();
                preg_match_all("/([a-z0-9]+) ?\\[(.*)\\][\n\r]/msUi", $stringItem, $matches2);
                foreach ($matches2[1] as $key => $value) {
                    $item[$value] = trim($matches2[2][$key]);
                    if (stripos($item[$value], 'array') !== false) {
                        eval('$item[$value] = ' . $item[$value] . ';');
                    }
                    if ($tipo == 'form' && $value == 'length') {
                        $item[$value] = explode(',', $item[$value]);
                    }
                }
                if ($tipo == 'form') {
                    $retorno[$item['tab']][] = $item;
                } else {
                    $retorno[] = $item;
                }
            }
        }
        if ($modoDesenvolvedor) {
            Personalizacoes::$rootDir = $rootDir;
        }
        return $retorno;
    }

    public static function salvarPersonalizacao($gridId, $conteudo, $pre = '', $pos = '')
    {
        $dir = self::getDirPersonalizacoesGrid();
        $arquivo = $dir . $pre . $gridId . $pos;
        if (empty(trim($conteudo))) {
            unlink($arquivo);
        } else {
            file_put_contents($arquivo, $conteudo);
        }
    }

    public static function salvarPersonalizacaoFormula($tabela, $conteudo)
    {
        $arquivo = self::getDirPersonalizacoesFormula() . $tabela;
        if (empty(trim($conteudo))) {
            unlink($arquivo);
        } else {
            file_put_contents($arquivo, $conteudo);
        }
    }

    public static function getInfoPath($path)
    {
        $path_ = explode('/', trim($path, '/'));
        return array("partes" => $path_, "php" => "prog/{$path_[0]}/{$path_[1]}/{$path_[2]}.php");
    }

    public static function getGridFuncCallbackList($path)
    {
        $retorno = array();
        if (file_exists($path)) {
            $codigoPHP = file_get_contents($path);
            preg_match_all('/require_once\\([\'"](?<path>.*)[\'"]\\);/', $codigoPHP, $matches);
            foreach ($matches['path'] as $valor) {
                if (strpos($valor, 'grid_func-callback') !== false) {
                    $retorno[] = $valor;
                }
            }
            return $retorno;
        } else {
            return $retorno;
        }
    }

    public static function getFunctionCodeList($path)
    {
        $retorno = array();
        if (file_exists($path)) {
            $codigoPHP = file_get_contents($path);
            $codigoPHP = preg_replace('/\\?>$/m', '', $codigoPHP);
            $codigoPHP .= "\nfunction vazio() {}";
            preg_match_all('/function [^\\(]+\\(.+(?=function [^\\(]+\\()/Ums', $codigoPHP, $matches);
            foreach ($matches[0] as $key => $match) {
                $codigo = trim($match);
                preg_match('/function (?<nome>.*) ?\\(/U', $codigo, $matchNome);
                $retorno[$matchNome['nome']] = $codigo;
            }
            return $retorno;
        } else {
            return $retorno;
        }
    }

    public static function getGridLinha($gridId)
    {
        $gridLinha = apc_fetch("grid-gridLinha-{$gridId}");
        return $gridLinha ? : '';
    }

    public static function getPersonalizacaoGridLinha($gridId, $pre = '')
    {
        $retorno = '';
        $dir = self::getDirPersonalizacoesGrid();
        $arquivo = $dir . $pre . $gridId . '-gridLinha';
        if (!(!file_exists($arquivo))) {
            $retorno = file_get_contents($arquivo);
        }
        return $retorno;
    }

    public static function salvarPersonalizacaoGridLinha($gridId, $codigo, $pre = '')
    {
        $dir = self::getDirPersonalizacoesGrid();
        $arquivo = $dir . $pre . $gridId . '-gridLinha';
        if (empty($codigo)) {
            if (!(!file_exists($arquivo))) {
                unlink($arquivo);
            }
        } else {
            file_put_contents($arquivo, $codigo);
        }
        return true;
    }

    public static function getGridTotais($gridId)
    {
        $retorno = array();
        $gridTotais = apc_fetch("grid-totais-{$gridId}");
        if ($gridTotais) {
            $retorno = unserialize($gridTotais);
        }
        return $retorno;
    }

    public static function converterPersonalizacao($array)
    {
        $retorno = '';
        foreach ($array as $item) {
            foreach ($item as $key => $value) {
                if (!($key == 'nivel' || $key == 'niveis')) {
                    if (is_array($value)) {
                        $value = var_export($value, true);
                    } else {
                        if ($value === true) {
                            $value = 'S';
                        }
                    }
                    $retorno .= "{$key} [{$value}]\n";
                }
            }
            $retorno .= "---\n";
        }
        return trim($retorno);
    }

    public static function converterForm($form)
    {
        $retorno = '';
        foreach ($form['campos'] as $tabKey => $tab) {
            foreach ($tab as $field => $fieldArray) {
                $retorno .= "tab [{$tabKey}]\n";
                $retorno .= "field [{$field}]\n";
                foreach ($fieldArray as $key => $value) {
                    if (!in_array($key, ['tab', 'field'])) {
                        if (is_array($value)) {
                            if ($key == 'length') {
                                $value = implode($value, ', ');
                            } else {
                                $value = var_export($value, true);
                                $value = preg_replace('/[0-9]+ \\=\\> /', '', $value);
                            }
                        } else {
                            if ($value === true) {
                                $value = 'S';
                            }
                        }
                        $retorno .= "{$key} [{$value}]\n";
                    }
                }
                $retorno .= "---\n";
            }
        }
        return trim($retorno);
    }

    public static function processarTotais($gridId, $totais, $incluir = array('desenvolvedor', 'apps', 'global', 'perfil', 'usuario'), $perfil = false)
    {
        global $g;
        foreach ($totais as &$total) {
            $total['nivel'] = 'Sistema';
        }
        $perfil = $perfil ? $perfil : $g['perfilUsuario'];
        $totaisPersonalizado = array(array('desenvolvedor', Personalizacoes::getPersonalizacao($gridId, '', '-totais-novo', 'grid', 'IDE')), array('apps', Personalizacoes::getPersonalizacao($gridId, '', '-totais-novo', 'grid', 'APPS')), array('apps-editor', Personalizacoes::getPersonalizacao($gridId, '', '-totais-novo', 'grid', 'APPS-EDITOR')), array('global', Personalizacoes::getPersonalizacao($gridId, '', '-totais-novo', 'grid', 'GLOBAL')), array('perfil', Personalizacoes::getPersonalizacao($gridId, "{$perfil}-", '-totais-novo', 'grid', 'GLOBAL')), array('usuario', Personalizacoes::getPersonalizacao($gridId, "u{$g['usuarioAtual']}-", '-totais-novo', 'grid', 'GLOBAL')));
        foreach ($totaisPersonalizado as $totais_) {
            if (in_array($totais_[0], $incluir)) {
                foreach ($totais_[1] as $itemPersonalizado) {
                    $itemPersonalizado['nivel'] = $totais_[0];
                    $todos = array();
                    foreach ($totais as &$item) {
                        $todos[] = $item['nome'];
                        if ($item['nome'] == $itemPersonalizado['nome']) {
                            $item = array_merge($item, $itemPersonalizado);
                        }
                    }
                    if (!(in_array($itemPersonalizado['nome'], $todos))) {
                        $totais[] = $itemPersonalizado;
                    }
                }
            }
        }
        return $totais;
    }

    public static function processarFormulas($tabela, $incluir = array('php', 'desenvolvedor', 'apps', 'global'))
    {
        global $g;
        $formulas = array();
        $formulasPersonalizado = array(array('php', Personalizacoes::getPersonalizacao($tabela, '', '', 'formula', 'PHP')), array('desenvolvedor', Personalizacoes::getPersonalizacao($tabela, '', '', 'formula', 'IDE')), array('apps', Personalizacoes::getPersonalizacao($tabela, '', '', 'formula', 'APPS')), array('apps-editor', Personalizacoes::getPersonalizacao($tabela, '', '', 'formula', 'APPS-EDITOR')), array('global', Personalizacoes::getPersonalizacao($tabela, '', '', 'formula', 'GLOBAL')));
        foreach ($formulasPersonalizado as $formula_) {
            if (in_array($formula_[0], $incluir)) {
                foreach ($formula_[1] as $itemPersonalizado) {
                    $itemPersonalizado['nivel'] = $formula_[0];
                    $itemPersonalizado['niveis'] = 1;
                    $todos = array();
                    foreach ($formulas as &$item) {
                        $todos[] = $item['updateId'];
                        if ($item['updateId'] == $itemPersonalizado['updateId']) {
                            $itemPersonalizado['niveis'] = $item['niveis'] + 1;
                            $item = array_merge($item, $itemPersonalizado);
                        }
                    }
                    if (!(in_array($itemPersonalizado['updateId'], $todos))) {
                        $formulas[$itemPersonalizado['updateId']] = $itemPersonalizado;
                    }
                }
            }
        }
        return $formulas;
    }

    public static function listarGridsNovos()
    {
        return array_filter(scandir(self::getDirGrids()), function($valor) {
            return $valor != '.' && $valor != '..' && $valor != '.gitignore';
        });
    }

    public static function listarTabsNovas()
    {
        return array_filter(scandir(self::getDirTabs()), function($valor) {
            return $valor != '.' && $valor != '..' && $valor != '.gitignore';
        });
    }

    public static function listarContentsNovos()
    {
        return array_filter(scandir(self::getDirContents()), function($valor) {
            return $valor != '.' && $valor != '..' && $valor != '.gitignore';
        });
    }

    public static function listarFormsNovos()
    {
        return array_filter(scandir(self::getDirForms()), function($valor) {
            return $valor != '.' && $valor != '..' && $valor != '.gitignore';
        });
    }

    public static function listarOutrosNovos()
    {
        return array_filter(scandir(self::getDirOutros()), function($valor) {
            return $valor != '.' && $valor != '..' && $valor != '.gitignore';
        });
    }

    public static function listarCodigosNovos()
    {
        return array_filter(scandir(self::getDirCodigos()), function($valor) {
            return $valor != '.' && $valor != '..' && $valor != '.gitignore' && !is_dir(self::getDirCodigos() . $valor);
        });
    }

    public static function listarMetodosNovos($id)
    {
        return array_filter(scandir(self::getDirCodigos() . '/' . $id . '-metodos/'), function($valor) {
            return $valor != '.' && $valor != '..' && $valor != '.gitignore';
        });
    }

    public static function listarTelasNovas()
    {
        return array_filter(scandir(self::getDirTelas()), function($valor) {
            return $valor != '.' && $valor != '..' && $valor != '.gitignore' && !is_dir(self::getDirTelas() . $valor);
        });
    }

    public static function listarExcelsNovos()
    {
        return array_filter(scandir(self::getDirExcels()), function($valor) {
            return $valor != '.' && $valor != '..' && $valor != '.gitignore' && !is_dir(self::getDirExcels() . $valor) && !preg_match('/\\.excel$/', $valor);
        });
    }

    public static function listarBIsNovos()
    {
        return array_filter(scandir(self::getDirBIs()), function($valor) {
            return $valor != '.' && $valor != '..' && $valor != '.gitignore' && !is_dir(self::getDirBIs() . $valor);
        });
    }

    public static function listarIndicadoresNovos()
    {
        return array_filter(scandir(self::getDirIndicadores()), function($valor) {
            return $valor != '.' && $valor != '..' && $valor != '.gitignore' && !is_dir(self::getDirIndicadores() . $valor);
        });
    }

    public static function listarDashboardsNovos()
    {
        return array_filter(scandir(self::getDirDashboards()), function($valor) {
            return $valor != '.' && $valor != '..' && $valor != '.gitignore' && !is_dir(self::getDirDashboards() . $valor);
        });
    }

    public static function getGridNovo($id)
    {
        $arquivo = self::getDirGrids() . $id;
        if (file_exists($arquivo)) {
            return unserialize(file_get_contents($arquivo));
        } else {
            return false;
        }
    }

    public static function getTabNova($id)
    {
        $arquivo = self::getDirTabs() . $id;
        if (file_exists($arquivo)) {
            return unserialize(file_get_contents($arquivo));
        } else {
            return false;
        }
    }

    public static function getContentNovo($id)
    {
        $arquivo = self::getDirContents() . $id;
        if (file_exists($arquivo)) {
            return unserialize(file_get_contents($arquivo));
        } else {
            return false;
        }
    }

    public static function getFormNovo($id)
    {
        $arquivo = self::getDirForms() . $id;
        if (file_exists($arquivo)) {
            return unserialize(file_get_contents($arquivo));
        } else {
            return false;
        }
    }

    public static function getOutroNovo($id)
    {
        $arquivo = self::getDirOutros() . $id;
        if (file_exists($arquivo)) {
            return unserialize(file_get_contents($arquivo));
        } else {
            return false;
        }
    }

    public static function getCodigoNovo($id)
    {
        $arquivo = self::getDirCodigos() . $id;
        if (file_exists($arquivo)) {
            return unserialize(file_get_contents($arquivo));
        } else {
            return false;
        }
    }

    public static function getMetodoNovo($idCodigo, $idMetodo)
    {
        $arquivo = self::getDirCodigos() . $idCodigo . '-metodos/' . $idMetodo;
        if (file_exists($arquivo)) {
            return unserialize(file_get_contents($arquivo));
        } else {
            return false;
        }
    }

    public static function getTelaNova($id)
    {
        $arquivo = self::getDirTelas() . $id;
        if (file_exists($arquivo)) {
            return unserialize(file_get_contents($arquivo));
        } else {
            return false;
        }
    }

    public static function getExcelNovo($id)
    {
        $arquivo = self::getDirExcels() . $id;
        if (file_exists($arquivo)) {
            return unserialize(file_get_contents($arquivo));
        } else {
            return false;
        }
    }

    public static function getBINovo($id)
    {
        $arquivo = self::getDirBIs() . $id;
        if (file_exists($arquivo)) {
            return unserialize(file_get_contents($arquivo));
        } else {
            return false;
        }
    }

    public static function getIndicadorNovo($id)
    {
        $arquivo = self::getDirIndicadores() . $id;
        if (file_exists($arquivo)) {
            return unserialize(file_get_contents($arquivo));
        } else {
            return false;
        }
    }

    public static function getDashboardNovo($id)
    {
        $arquivo = self::getDirDashboards() . $id;
        if (file_exists($arquivo)) {
            return unserialize(file_get_contents($arquivo));
        } else {
            return false;
        }
    }

    public static function getTabs($tabId)
    {
        $retorno = array();
        $tabs = apc_fetch("tabs-{$tabId}");
        if ($tabs) {
            $retorno = unserialize($tabs);
        }
        return $retorno;
    }

    public static function getContent($contentId)
    {
        $retorno = array();
        $tabs = apc_fetch("content-{$contentId}");
        if ($tabs) {
            $retorno = unserialize($tabs);
        }
        return $retorno;
    }

    public static function salvarPersonalizacaoTabs($tabId, $codigo, $pre = '')
    {
        $dir = self::getDirPersonalizacoesTab();
        $arquivo = $dir . $pre . $tabId;
        if (empty($codigo)) {
            if (!(!file_exists($arquivo))) {
                unlink($arquivo);
            }
        } else {
            file_put_contents($arquivo, $codigo);
        }
        return true;
    }

    public static function salvarPersonalizacaoContent($contentId, $codigo, $pre = '')
    {
        $dir = self::getDirPersonalizacoesContent();
        $arquivo = $dir . $pre . $contentId;
        if (empty($codigo)) {
            if (!(!file_exists($arquivo))) {
                unlink($arquivo);
            }
        } else {
            file_put_contents($arquivo, $codigo);
        }
        return true;
    }

    public static function salvarPersonalizacaoForm($formId, $codigo, $pre = '')
    {
        $dir = self::getDirPersonalizacoesForm();
        $arquivo = $dir . $pre . $formId;
        if (empty($codigo)) {
            if (!(!file_exists($arquivo))) {
                unlink($arquivo);
            }
        } else {
            file_put_contents($arquivo, $codigo);
        }
        return true;
    }

    public static function salvarPersonalizacaoOutro($formId, $codigo, $pre = '')
    {
        $dir = self::getDirPersonalizacoesOutro();
        $arquivo = $dir . $pre . $formId;
        if (empty($codigo)) {
            if (!(!file_exists($arquivo))) {
                unlink($arquivo);
            }
        } else {
            file_put_contents($arquivo, $codigo);
        }
        return true;
    }

    public static function converterTabs($tabs)
    {
        $retorno = '';
        foreach ($tabs as $tab) {
            $retorno .= "url [{$tab[1]}]\n";
            $retorno .= "titulo [{$tab[2]}]\n";
            $retorno .= "---\n";
        }
        return trim($retorno);
    }

    public static function converterContent($content)
    {
        $retorno = '';
        foreach (array('url', 'url2', 'tipo', 'tamanho', 'parametros', 'parametros2') as $campo) {
            if (isset($content[$campo]) && $content[$campo] !== '') {
                $retorno .= "{$campo} [{$content[$campo]}]\n";
            }
        }
        $retorno .= "---";
        return trim($retorno);
    }

    public static function processarTabs($tabId, $incluir = array('desenvolvedor', 'apps', 'global', 'perfil', 'usuario'), $perfil = false)
    {
        global $g;
        $tabs = array();
        $perfil = $perfil ? $perfil : $g['perfilUsuario'];
        $tabsPersonalizado = array(array('desenvolvedor', Personalizacoes::getPersonalizacao($tabId, '', '', 'tabs', 'IDE')), array('apps', Personalizacoes::getPersonalizacao($tabId, '', '', 'tabs', 'APPS')), array('apps-editor', Personalizacoes::getPersonalizacao($tabId, '', '', 'tabs', 'APPS-EDITOR')), array('global', Personalizacoes::getPersonalizacao($tabId, '', '', 'tabs', 'GLOBAL')), array('perfil', Personalizacoes::getPersonalizacao($tabId, "{$perfil}-", '', 'tabs', 'GLOBAL')), array('usuario', Personalizacoes::getPersonalizacao($tabId, "u{$g['usuarioAtual']}-", '', 'tabs', 'GLOBAL')));
        foreach ($tabsPersonalizado as $tabs_) {
            if (in_array($tabs_[0], $incluir)) {
                foreach ($tabs_[1] as $itemPersonalizado) {
                    $itemPersonalizado['nivel'] = $tabs_[0];
                    $todos = array();
                    foreach ($tabs as &$item) {
                        $todos[] = $item['url'];
                        if ($item['url'] == $itemPersonalizado['url']) {
                            $item = array_merge($item, $itemPersonalizado);
                        }
                    }
                    if (!(in_array($itemPersonalizado['url'], $todos))) {
                        $tabs[] = $itemPersonalizado;
                    }
                }
            }
        }
        return $tabs;
    }

    public static function processarContent($contentId, $incluir = array('desenvolvedor', 'apps', 'global', 'perfil', 'usuario'), $perfil = false)
    {
        global $g;
        $content = array();
        $perfil = $perfil ? $perfil : $g['perfilUsuario'];
        $contentPersonalizado = array(array('desenvolvedor', Personalizacoes::getPersonalizacao($contentId, '', '', 'content', 'IDE')), array('apps', Personalizacoes::getPersonalizacao($contentId, '', '', 'content', 'APPS')), array('apps-editor', Personalizacoes::getPersonalizacao($contentId, '', '', 'content', 'APPS-EDITOR')), array('global', Personalizacoes::getPersonalizacao($contentId, '', '', 'content', 'GLOBAL')), array('perfil', Personalizacoes::getPersonalizacao($contentId, "{$perfil}-", '', 'content', 'GLOBAL')), array('usuario', Personalizacoes::getPersonalizacao($contentId, "u{$g['usuarioAtual']}-", '', 'content', 'GLOBAL')));
        foreach ($contentPersonalizado as $content_) {
            if (in_array($content_[0], $incluir)) {
                if (!(empty($content_[1]))) {
                    $content = array_merge($content, $content_[1][0]);
                }
            }
        }
        return $content;
    }

    public static function processarForm($formId, $incluir = array('desenvolvedor', 'apps', 'global', 'perfil', 'usuario'), $perfil = false)
    {
        global $g;
        $form = array();
        $perfil = $perfil ? $perfil : $g['perfilUsuario'];
        $formPersonalizado = array(array('desenvolvedor', Personalizacoes::getPersonalizacao($formId, '', '', 'form', 'IDE')), array('apps', Personalizacoes::getPersonalizacao($formId, '', '', 'form', 'APPS')), array('global', Personalizacoes::getPersonalizacao($formId, '', '', 'form', 'GLOBAL')), array('apps-editor', Personalizacoes::getPersonalizacao($formId, '', '', 'form', 'APPS-EDITOR')), array('perfil', Personalizacoes::getPersonalizacao($formId, "{$perfil}-", '', 'form', 'GLOBAL')), array('usuario', Personalizacoes::getPersonalizacao($formId, "u{$g['usuarioAtual']}-", '', 'form', 'GLOBAL')));
        foreach ($formPersonalizado as $form_) {
            if (in_array($form_[0], $incluir)) {
                foreach ($form_[1] as $itemTab => $camposTab) {
                    foreach ($camposTab as $itemPersonalizado) {
                        if (!(isset($form[$itemTab]))) {
                            $form[$itemTab] = array();
                        }
                        $itemPersonalizado['nivel'] = $form_[0];
                        $todos = array();
                        foreach ($form[$itemTab] as &$item) {
                            $todos[] = $item['field'];
                            if ($item['field'] == $itemPersonalizado['field']) {
                                $item = array_merge($item, $itemPersonalizado);
                            }
                        }
                        if (!(in_array($itemPersonalizado['field'], $todos))) {
                            $form[$itemTab][] = $itemPersonalizado;
                        }
                    }
                }
            }
        }
        return $form;
    }

    public static function getBotoes($divId)
    {
        $retorno = array();
        $botoes = apc_fetch("botoes-php-{$divId}");
        if ($botoes) {
            $retorno = unserialize($botoes);
        }
        return $retorno;
    }

    public static function getForm($formId)
    {
        $retorno = array();
        $formPHP = apc_fetch("form-php-{$formId}");
        if ($formPHP) {
            $retorno = unserialize($formPHP);
        }
        return $retorno;
    }

    public static function getSistemaModulos()
    {
        $modulos = array();
        $arquivo = self::getDirSistema() . 'modulos';
        if (!(!file_exists($arquivo))) {
            foreach (file($arquivo) as $linha) {
                $linha = trim($linha);
                if (!empty($linha)) {
                    $modulo = explode(',', $linha);
                    $modulos[$modulo[0]] = array('id' => $modulo[0], 'nome' => $modulo[1], 'descricao' => $modulo[2]);
                }
            }
        }
        return $modulos;
    }

    public static function getSistemaOpcoes()
    {
        $opcoes = array();
        $arquivo = self::getDirSistema() . 'opcoes';
        if (!(!file_exists($arquivo))) {
            foreach (file($arquivo) as $linha) {
                $linha = trim($linha);
                if (!empty($linha)) {
                    $opcao = explode(',', $linha);
                    $opcoes[$opcao[0]] = array('id' => $opcao[0], 'nome' => $opcao[1], 'descricao' => $opcao[2], 'modulo' => $opcao[3]);
                }
            }
        }
        return $opcoes;
    }

    public static function carregarExecutarCallback($callback, $parametros = array())
    {
        $parametros = array_merge(gRepassarParametros(true), $parametros);
        if (!$callback) {
        } else {
            if (file_exists("bibliotecas/funcoesCallback/{$callback}.php")) {
                $callUserFunc = 'hardness\\' . $callback;
                if (!function_exists($callUserFunc)) {
                    require_once "bibliotecas/funcoesCallback/{$callback}.php";
                }
            } else {
                $codigo = $callback;
                if (strpos($codigo, 'namespace hardness') === false) {
                    $codigo = <<<EOT
<?php
namespace hardness;
function callback(\$array) {
    global \$g;
    {$codigo}
}
EOT;
                }
                $codigo = preg_replace('/function[^(]+\\(/', '$executarCallback = function(', $codigo);
                $codigo = str_replace('<?php', '', $codigo);
                $codigo .= ';';
                eval($codigo);
                $callUserFunc = $executarCallback;
            }
            return call_user_func($callUserFunc, $parametros);
        }
    }

    public static function tabelasAuditoria($salvar = null)
    {
        $arquivo = self::getDirPersonalizacoesConf() . 'tabelasAuditoria.serialized';
        if (!$salvar) {
            $tabelasAuditoria = array();
            if (!(!file_exists($arquivo))) {
                $tabelasAuditoria = unserialize(file_get_contents($arquivo));
            }
            return $tabelasAuditoria;
        } else {
            file_put_contents($arquivo, serialize($salvar));
            return true;
        }
    }

    public static function gerarScriptBanco()
    {
        global $confUsuario, $g;
        $dir = !$g['modoDesenvolvedorApp'] ? 'ide/' : Apps::getDirEditor();
        $resultado = '';
        $query = "SHOW TABLES";
        $tabelas = array_map(function($a) {
            return $a[0];
        }, mysqli_fetch_all(mysqli_query($query)));
        $tabelas = array_filter($tabelas, function($a) {
            global $g;
            return verificarPrefixoTabela($a) || !$g['modoDesenvolvedorApp'] && preg_match('/^[A-Z][0-9]{3}A$/i', $a);
        });
        $listaIndices = [];
        $listaIndicesExcluir = [];
        foreach ($tabelas as $tabela) {
            $tabela = strtoupper($tabela);
            $query = "SHOW FULL COLUMNS FROM `{$confUsuario["dbDatabase"]}`.`{$tabela}`";
            $campos = mysqli_fetch_all(mysqli_query($query), MYSQLI_ASSOC);
            $query = <<<EOT
SELECT table_comment FROM INFORMATION_SCHEMA.TABLES 
WHERE table_schema = '{$confUsuario["dbDatabase"]}' AND table_name = '{$tabela}'
EOT;
            $comment = mysqli_fetch_array(mysqli_query($query));
            $comment = trim($comment[0]);
            $query = "CREATE TABLE {$tabela} ( {$tabela}_Id int unsigned auto_increment, constraint {$tabela}_pk primary key ({$tabela}_Id) ) COMMENT='{$comment}'";
            $resultado .= "TABELA\t{$tabela}\t{$query}\n";
            foreach ($campos as $campo) {
                if (!($campo['Key'] == 'PRI')) {
                    $tipo = $campo['Type'];
                    if ($campo['Null'] == 'NO') {
                        $tipo .= ' not null';
                    }
                    if (strlen($campo['Default']) != 0) {
                        $default = "'{$campo['Default']}'";
                        if ($campo['Default'] == 'CURRENT_TIMESTAMP') {
                            $default = "{$campo['Default']}";
                        }
                        $tipo .= " default {$default}";
                    }
                    $query = "{$tipo} COMMENT '{$campo['Comment']}'";
                    $resultado .= "CAMPO\t{$tabela}\t{$campo['Field']}\t{$query}\n";
                }
            }
            $query = "SHOW INDEX FROM `{$confUsuario["dbDatabase"]}`.{$tabela}";
            $indices = [];
            $res = mysqli_query($query);
            while ($index = mysqli_fetch_assoc($res)) {
                if ($index['Key_name'] == 'PRIMARY') {
                }
                while ($indices[$index['Key_name']]) {
                    $indices[$index['Key_name']]['campos'][] = $index['Column_name'];
                }
                $indices[$index['Key_name']] = array('nome' => $index['Key_name'], 'campos' => array(), 'unique' => $index['Non_unique'] == 0);
            }
            foreach ($indices as $index) {
                $unique = $index['unique'] ? 'UNIQUE' : '';
                $campos = implode(', ', $index['campos']);
                $resultado .= "INDEX\t{$tabela}\t{$unique}\t{$index['nome']}\t{$campos}\n";
                $listaIndices[$index['nome']] = true;
            }
            $atualizacaoExcluir = file("{$dir}personalizacaoBanco/atualizacaoExcluir.txt");
            foreach ($atualizacaoExcluir as $linha) {
                $linha = trim($linha);
                $item = explode("\t", $linha);
                if ($item[0] == 'INDEX' && !$listaIndices[$item[3]] && !$listaIndicesExcluir[$item[3]]) {
                    $resultado .= "{$linha}\n";
                    $listaIndicesExcluir[$item[3]] = true;
                }
            }
        }
        mysql_query("UPDATE D183 SET D183_Uuid = UUID() WHERE D183_Uuid = '' OR D183_Uuid IS NULL");
        $sql = "SELECT * FROM D183";
        if (!(!$g['modoDesenvolvedorApp'])) {
            $sql .= " WHERE D183_App_Id = '{$g['modoDesenvolvedorApp']}'";
        }
        $res = mysql_query($sql);
        while ($D183 = mysql_fetch_assoc($res)) {
            $D183['D183_Desenvolvimento'] = 0;
            $resultado .= "INSERT\tD183\t" . base64_encode(serialize($D183)) . "\n";
        }
        if (!empty($resultado)) {
            file_put_contents("{$dir}personalizacaoBanco/atualizacaoBaseIde.txt", $resultado);
        } else {
            if (!(!file_exists("{$dir}personalizacaoBanco/atualizacaoBaseIde.txt"))) {
                unlink("{$dir}personalizacaoBanco/atualizacaoBaseIde.txt");
            }
        }
        $dados = array();
        $query = "SHOW TRIGGERS FROM `{$confUsuario["dbDatabase"]}`";
        $res = mysql_query($query);
        while ($trigger = mysql_fetch_assoc($res)) {
            $tabela = explode('_', $trigger['Trigger']);
            $tabela = $tabela[0];
            while (!verificarPrefixoTabela($tabela)) {
            }
            $query = "SHOW TRIGGERS FROM `{$confUsuario["dbDatabase"]}` WHERE `Trigger` = '{$trigger['Trigger']}'";
            $trigger = mysql_fetch_assoc(mysql_query($query));
            $table = strtoupper($trigger['Table']);
            $codigo = "CREATE TRIGGER `{$trigger['Trigger']}` {$trigger['Timing']} {$trigger['Event']} ON `{$table}` FOR EACH ROW " . $trigger['Statement'];
            $dados["TRIGGER-{$trigger['Trigger']}"] = $codigo;
        }
        $query = "SHOW PROCEDURE STATUS WHERE Db = '{$confUsuario["dbDatabase"]}'";
        $res = mysql_query($query);
        while ($procedure = mysql_fetch_assoc($res)) {
            $tabela = explode('_', $procedure['Name']);
            $tabela = $tabela[0];
            while (!verificarPrefixoTabela($tabela)) {
            }
            $query = "SHOW CREATE PROCEDURE `{$procedure['Name']}`";
            $procedure = mysql_fetch_assoc(mysql_query($query));
            $codigo = $procedure['Create Procedure'];
            $dados["PROCEDURE-{$procedure['Procedure']}"] = $codigo;
        }
        $query = "SHOW FUNCTION STATUS WHERE Db = '{$confUsuario["dbDatabase"]}'";
        $res = mysql_query($query);
        while ($function = mysql_fetch_assoc($res)) {
            $tabela = explode('_', $function['Name']);
            $tabela = $tabela[0];
            while (!verificarPrefixoTabela($tabela)) {
            }
            $query = "SHOW CREATE FUNCTION `{$function['Name']}`";
            $function = mysql_fetch_assoc(mysql_query($query));
            $codigo = $function['Create Function'];
            $dados["FUNCTION-{$function['Function']}"] = $codigo;
        }
        array_map('\\unlink', glob("{$dir}personalizacaoBanco/codigos/*.*"));
        foreach ($dados as $key => $value) {
            file_put_contents("{$dir}personalizacaoBanco/codigos/{$key}.sql", $value);
        }
    }

    public function extraMerge($extra1, $extra2)
    {
        if (preg_match('/\\-\\- (JOIN|WHERE|GROUP BY|HAVING|ORDER BY)( (\\(REPLACE\\)|\\(OR\\)))*/i', $extra1)) {
            if (preg_match('/\\-\\- (JOIN|WHERE|GROUP BY|HAVING|ORDER BY)( (\\(REPLACE\\)|\\(OR\\)))*/i', $extra2)) {
                $extra1 = str_replace("\t", '', trim($extra1));
                $extra2 = str_replace("\t", '', trim($extra2));
                $quebrarExtra = function($extra) {
                    $retorno = array('JOIN' => array('codigo' => '', 'modo' => 'ADD', 'extra' => ''), 'WHERE' => array('codigo' => '', 'modo' => 'ADD', 'extra' => ''), 'GROUP BY' => array('codigo' => '', 'modo' => 'ADD', 'extra' => ''), 'HAVING' => array('codigo' => '', 'modo' => 'ADD', 'extra' => ''), 'ORDER BY' => array('codigo' => '', 'modo' => 'ADD', 'extra' => ''));
                    foreach (array('JOIN', 'WHERE', 'GROUP BY', 'HAVING', 'ORDER BY') as $item) {
                        preg_match("/\\-\\- {$item}( (\\(REPLACE\\)|\\(OR\\)))*/i", $extra, $modificadores);
                        if (stripos($modificadores[0], '(REPLACE)') !== false) {
                            $retorno[$item]['modo'] = 'REPLACE';
                        }
                        if (stripos($modificadores[0], '(OR)') !== false) {
                            $retorno[$item]['extra'] = 'OR';
                        }
                        $split = preg_split("/\\-\\- {$item}( (\\(REPLACE\\)|\\(OR\\)))*/i", $extra);
                        if (count($split) > 1) {
                            $split = preg_split('/\\-\\- (JOIN|WHERE|GROUP BY|HAVING|ORDER BY)( (\\(REPLACE\\)|\\(OR\\)))*/i', $split[1]);
                            $retorno[$item]['codigo'] = trim($split[0]);
                            if ($item != 'JOIN') {
                                $retorno[$item]['codigo'] = trim(preg_replace("/^[[:blank:]]*{$item}/i", '', $retorno[$item]['codigo']));
                            }
                        }
                    }
                    return $retorno;
                };
                $extra1 = $quebrarExtra($extra1);
                $extra2 = $quebrarExtra($extra2);
                $extraNovo = '';
                foreach (array('JOIN', 'WHERE', 'GROUP BY', 'HAVING', 'ORDER BY') as $item) {
                    $extraNovo .= "-- {$item}\n";
                    $pre = $item != 'JOIN' ? "{$item} " : '';
                    if (empty($extra1[$item]['codigo'])) {
                        if (!(empty($extra2[$item]['codigo']))) {
                            $extraNovo .= "{$pre}{$extra2[$item]['codigo']}\n";
                        }
                    } else {
                        if (empty($extra2[$item]['codigo'])) {
                            $extraNovo .= "{$pre}{$extra1[$item]['codigo']}\n";
                        } else {
                            if ($extra2[$item]['modo'] == 'REPLACE') {
                                $extraNovo .= "{$pre}{$extra2[$item]['codigo']}\n";
                            } else {
                                if ($item == 'JOIN') {
                                    $extraNovo .= "{$extra1[$item]['codigo']}\n{$extra2[$item]['codigo']}\n";
                                } else {
                                    if (in_array($item, array('WHERE', 'HAVING'))) {
                                        $wherePrefixo = $extra2[$item]['extra'] == 'OR' ? 'OR' : 'AND';
                                        $extraNovo .= "{$item} ({$extra1[$item]['codigo']})\n{$wherePrefixo} ({$extra2[$item]['codigo']})\n";
                                    } else {
                                        if (!(!in_array($item, array('GROUP BY', 'ORDER BY')))) {
                                            $extraNovo .= "{$item} {$extra1[$item]['codigo']}, {$extra2[$item]['codigo']}\n";
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                return $extraNovo;
            } else {
                return $extra2;
            }
        } else {
            return $extra2;
        }
    }

    public static function formulasListarTabelas()
    {
        global $g;
        $diretorios = array('php' => 'formulas/', 'ide' => "ide/personalizacaoFormula/", 'apps' => Apps::getDirAtivos() ? Apps::getDirAtivos() . 'personalizacaoFormula/' : '', 'apps-editor' => Apps::getDirEditor() . 'personalizacaoFormula/', 'global' => "{$g['pathDados']}personalizacaoFormula/");
        $tabelas = array();
        foreach ($diretorios as $tipo => $diretorio) {
            if (!(empty($diretorio) || !file_exists($diretorio))) {
                $arquivos = array_filter(scandir($diretorio), function($valor) {
                    return $valor != '.' && $valor != '..' && $valor != '.gitignore' && $valor != 'README.txt';
                });
                $tabelas = array_merge($tabelas, $arquivos);
            }
        }
        return array_unique($tabelas);
    }

    public static function formulasTabelasPai($tabela)
    {
        global $g;
        $tabela = strtoupper($tabela);
        $regexFilho = '/^[^(]+\\((?<tabela>[^_]+)_[a-z0-9_]+\\)$/i';
        $niveisAtual = array('php', 'desenvolvedor', 'apps', 'global');
        if ($g['modoDesenvolvedor']) {
            $niveisAtual = array('php', 'desenvolvedor');
        } else {
            if (!(!$g['modoDesenvolvedorApp'])) {
                $niveisAtual = array('php', 'desenvolvedor', 'apps', 'apps-editor');
            }
        }
        $tabelasPai = array();
        $todasTabelas = self::formulasListarTabelas();
        foreach ($todasTabelas as $tabelaBusca) {
            $tabelaBusca = strtoupper($tabelaBusca);
            if (!($tabelaBusca == $tabela)) {
                $formulasBusca = Personalizacoes::processarFormulas($tabelaBusca, $niveisAtual);
                foreach ($formulasBusca as $item) {
                    if (preg_match($regexFilho, $item['updateValor'], $matches) && $tabela == strtoupper($matches['tabela'])) {
                        $tabelasPai[] = $tabelaBusca;
                    }
                }
            }
        }
        $tabelasPai = array_unique($tabelasPai);
        return $tabelasPai;
    }

    public static function formulasGerarProcedure($tabela)
    {
        global $g;
        $declares = '';
        $tabela = strtoupper($tabela);
        $regexFilho = '/^[^(]+\\((?<tabela>[^_]+)_[a-z0-9_]+\\)$/i';
        $niveisAtual = array('php', 'desenvolvedor', 'apps', 'global');
        if ($g['modoDesenvolvedor']) {
            $niveisAtual = array('php', 'desenvolvedor');
        } else {
            if (!(!$g['modoDesenvolvedorApp'])) {
                $niveisAtual = array('php', 'desenvolvedor', 'apps', 'apps-editor');
            }
        }
        $formulas = Personalizacoes::processarFormulas($tabela, $niveisAtual);
        if (!empty($formulas)) {
            $tabelasPai = self::formulasTabelasPai($tabela);
            $calls = '';
            foreach ($tabelasPai as $tabelaPai) {
                $declare = "DECLARE x{$tabelaPai}_Id int(11);";
                $declares .= $declares ? "\n\t{$declare}" : $declare;
                $call = <<<EOT
SELECT {$tabela}_{$tabelaPai}_Id INTO x{$tabelaPai}_Id FROM {$tabela} WHERE {$tabela}_Id = x{$tabela}_Id;
\tCALL {$tabelaPai}_Formulas(x{$tabelaPai}_Id);
EOT;
                $calls .= $calls ? "\n\t{$call}" : $call;
            }
            $campos = array();
            foreach ($formulas as $formula) {
                if (preg_match($regexFilho, $formula['updateValor'], $matches) && $tabela != strtoupper($matches['tabela'])) {
                    $tabelaFilha = strtoupper($matches['tabela']);
                    $campos[] = "{$formula['updateId']} = (SELECT {$formula['updateValor']} FROM {$tabelaFilha} WHERE {$tabelaFilha}_{$tabela}_Id = {$tabela}_Id)";
                } else {
                    $campos[] = "{$formula['updateId']} = {$formula['updateValor']}";
                }
            }
            $campos = implode(",\n\t\t\t", $campos);
            $procedure = <<<EOT
CREATE PROCEDURE `{$tabela}_Formulas` (x{$tabela}_Id int(11))
BEGIN
    {$declare}
    UPDATE {$tabela}
        SET
            {$campos}
        WHERE
            {$tabela}_Id = x{$tabela}_Id;
    {$calls}
END;
EOT;
            return $procedure;
        } else {
            return '';
        }
    }

    public static function arquivoCSS($tema)
    {
        global $g;
        $tema_ = strtoupper($tema) . '.CSS-alterarCSS.css';
        if ($g['modoDesenvolvedor']) {
            $arquivos = array('ide/' . "personalizacaoConf/{$tema_}");
        } else {
            if ($g['modoDesenvolvedorApp']) {
                $arquivos = array(Apps::getDirEditor() . "personalizacaoConf/{$tema_}", Apps::getDirAtivos() . "personalizacaoConf/{$tema_}", 'ide/' . "personalizacaoConf/{$tema_}");
            } else {
                $arquivos = array($g['pathDados'] . "personalizacaoConf/{$tema_}", Apps::getDirAtivos() . "personalizacaoConf/{$tema_}", 'ide/' . "personalizacaoConf/{$tema_}");
            }
        }
        foreach ($arquivos as $arquivo) {
            if (!file_exists($arquivo)) {
            } else {
                return $arquivo;
            }
        }
    }

}
