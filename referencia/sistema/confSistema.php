<?php
/**
 * confSistema.php
 *
 * Versão legível de sistema-legado/sistema/confSistema.php.
 * O original foi ofuscado com desvios; aqui o fluxo foi reconstruído
 * em if/else/while/foreach, no mesmo comportamento.
 * Este arquivo é referência de leitura e não é carregado pelo sistema.
 */
namespace hardness;

class confSistema
{
    public $conf = array('restricaoIncluirUpdate' => array(), 'ignorarRelacionamentoExcluir' => array(), 'restricaoExcluir' => array(), 'camposNaoUsar' => array(), 'camposValorPadrao' => array(), 'restricaoTravar' => array(), 'camposLabelForm' => array(), 'camposTipo' => array(), 'altText' => array(), 'triggers' => array(), 'ini' => array(), 'tabsNaoUsar' => array(), 'auditoriaCampos' => array(), 'trocarEmpresa' => array('exibir' => '', 'tabelaFilho' => '', 'executarNoFinal' => ''));

    private $carregado = array();

    function carregaArquivoConf($campo, $tipoConf)
    {
        global $g, $confUsuario;
        $conf = &$this->conf;
        $campo_ = explode('_', $campo);
        if (count($campo_) > 1) {
            $tabela = $campo_[0];
        } else {
            $tabela = '_GLOBAL';
        }
        $arquivo = "{$g['pathRaiz']}configuracoes/{$tabela}.php";
        if (!(isset($this->carregado[$arquivo]))) {
            if (file_exists($arquivo)) {
                require_once $arquivo;
                $this->carregado[$arquivo] = true;
            } else {
                $this->carregado[$arquivo] = false;
            }
        }
        $arquivoUser = "{$g['pathRaiz']}configuracoes/configuracoesPorBanco/{$confUsuario['dbDatabase']}/{$tabela}.php";
        if (!(isset($this->carregado[$arquivoUser]))) {
            if (file_exists($arquivoUser)) {
                require_once $arquivoUser;
                $this->carregado[$arquivoUser] = true;
            } else {
                $this->carregado[$arquivoUser] = false;
            }
        }
        $arquivoUserEmp = "{$g['pathRaiz']}configuracoes/configuracoesPorBanco/{$confUsuario['dbDatabase']}/{$g['empresaAtual']}/{$tabela}.php";
        if (!(isset($this->carregado[$arquivoUserEmp]))) {
            if (file_exists($arquivoUserEmp)) {
                require_once $arquivoUserEmp;
                $this->carregado[$arquivoUserEmp] = true;
            } else {
                $this->carregado[$arquivoUserEmp] = false;
            }
        }
        $dir = "ide/personalizacaoConf/";
        $dirApps = Apps::getDirAtivos() . "personalizacaoConf/";
        $dirAppsEditor = Apps::getDirEditor() . "personalizacaoConf/";
        $dirGlobal = "{$g['pathDados']}personalizacaoConf/";
        if ($tabela == '_GLOBAL') {
            $tabelas = array('GLOBAL', 'CAMPOS_LIVRE');
        } else {
            $tabelas = array($tabela);
        }
        $novoArquivoBanco = false;
        foreach ($tabelas as $tabela_) {
            foreach ($conf as $key => $value) {
                if (!($key == 'altText')) {
                    if ($key == 'tabsNaoUsar') {
                        if (!isset($this->carregado[$tabela_ . '-' . $key])) {
                            $arquivoTmp = "ide/personalizacaoTab/tabsNaoUsar.serialized";
                            $arquivoTmpGlobal = Apps::getDirAtivos() . "personalizacaoTab/tabsNaoUsar.serialized";
                            if (!$g['modoDesenvolvedor'] && file_exists($arquivoTmpGlobal)) {
                                $arquivoTmp = $arquivoTmpGlobal;
                            }
                            $arquivoTmpGlobal = Apps::getDirEditor() . "personalizacaoTab/tabsNaoUsar.serialized";
                            if ($g['modoDesenvolvedorApp'] && file_exists($arquivoTmpGlobal)) {
                                $arquivoTmp = $arquivoTmpGlobal;
                            }
                            $arquivoTmpGlobal = "{$g['pathDados']}personalizacaoTab/tabsNaoUsar.serialized";
                            if (!$g['modoDesenvolvedor'] && !$g['modoDesenvolvedorApp'] && file_exists($arquivoTmpGlobal)) {
                                $arquivoTmp = $arquivoTmpGlobal;
                            }
                            if (file_exists($arquivoTmp)) {
                                $conf['tabsNaoUsar'] = array_merge($conf['tabsNaoUsar'], unserialize(file_get_contents($arquivoTmp)));
                                $this->carregado[$tabela_ . '-' . $key] = true;
                                $novoArquivoBanco = true;
                            } else {
                                $this->carregado[$tabela_ . '-' . $key] = false;
                            }
                        } else {
                            if (isset($this->carregado[$tabela_ . '-' . $key])) {
                                $novoArquivoBanco = $this->carregado[$tabela_ . '-' . $key];
                            } else {
                                $this->carregado[$tabela_ . '-' . $key] = false;
                            }
                        }
                    } else {
                        if (!isset($this->carregado[$tabela_ . '-' . $key])) {
                            $arquivoTmp = $dir . $tabela_ . '-' . $key . '.php';
                            $arquivoTmpGlobal = $dirApps . $tabela_ . '-' . $key . '.php';
                            if (!$g['modoDesenvolvedor'] && file_exists($arquivoTmpGlobal)) {
                                $arquivoTmp = $arquivoTmpGlobal;
                            }
                            $arquivoTmpGlobal = $dirAppsEditor . $tabela_ . '-' . $key . '.php';
                            if ($g['modoDesenvolvedorApp'] && file_exists($arquivoTmpGlobal)) {
                                $arquivoTmp = $arquivoTmpGlobal;
                            }
                            $arquivoTmpGlobal = $dirGlobal . $tabela_ . '-' . $key . '.php';
                            if (!$g['modoDesenvolvedor'] && !$g['modoDesenvolvedorApp'] && file_exists($arquivoTmpGlobal)) {
                                $arquivoTmp = $arquivoTmpGlobal;
                            }
                            if (file_exists($arquivoTmp)) {
                                require_once $arquivoTmp;
                                $this->carregado[$tabela_ . '-' . $key] = true;
                                $novoArquivoBanco = true;
                            } else {
                                $this->carregado[$tabela_ . '-' . $key] = false;
                            }
                        } else {
                            if (isset($this->carregado[$tabela_ . '-' . $key])) {
                                $novoArquivoBanco = $this->carregado[$tabela_ . '-' . $key];
                            } else {
                                $this->carregado[$tabela_ . '-' . $key] = false;
                            }
                        }
                    }
                }
            }
        }
        $dir = "ide/{$g['pathDados']}personalizacaoConf/";
        $dirGlobal = "{$g['pathDados']}personalizacaoConf/";
        if ($tabela == '_GLOBAL') {
            $tabelas = array($g['empresaAtual'] . 'GLOBAL', $g['empresaAtual'] . 'CAMPOS_LIVRE');
        } else {
            $tabelas = array($g['empresaAtual'] . $tabela);
        }
        $novoArquivoBancoEmpresa = false;
        foreach ($tabelas as $tabela_) {
            foreach ($conf as $key => $value) {
                if (!($key == 'altText')) {
                    if ($key == 'tabsNaoUsar') {
                        if (!isset($this->carregado[$tabela_ . '-' . $key])) {
                            $arquivoTmp = "ide/personalizacaoTab/tabsNaoUsar.serialized";
                            $arquivoTmpGlobal = Apps::getDirAtivos() . "personalizacaoTab/tabsNaoUsar.serialized";
                            if (!$g['modoDesenvolvedor'] && file_exists($arquivoTmpGlobal)) {
                                $arquivoTmp = $arquivoTmpGlobal;
                            }
                            $arquivoTmpGlobal = Apps::getDirEditor() . "personalizacaoTab/tabsNaoUsar.serialized";
                            if ($g['modoDesenvolvedorApp'] && file_exists($arquivoTmpGlobal)) {
                                $arquivoTmp = $arquivoTmpGlobal;
                            }
                            $arquivoTmpGlobal = "{$g['pathDados']}personalizacaoTab/tabsNaoUsar.serialized";
                            if (!$g['modoDesenvolvedor'] && !$g['modoDesenvolvedorApp'] && file_exists($arquivoTmpGlobal)) {
                                $arquivoTmp = $arquivoTmpGlobal;
                            }
                            if (file_exists($arquivoTmp)) {
                                $conf['tabsNaoUsar'] = array_merge($conf['tabsNaoUsar'], unserialize(file_get_contents($arquivoTmp)));
                                $this->carregado[$tabela_ . '-' . $key] = true;
                                $novoArquivoBancoEmpresa = true;
                            } else {
                                $this->carregado[$tabela_ . '-' . $key] = false;
                            }
                        } else {
                            if (isset($this->carregado[$tabela_ . '-' . $key])) {
                                $novoArquivoBancoEmpresa = $this->carregado[$tabela_ . '-' . $key];
                            } else {
                                $this->carregado[$tabela_ . '-' . $key] = false;
                            }
                        }
                    } else {
                        if (!isset($this->carregado[$tabela_ . '-' . $key])) {
                            $arquivoTmp = $dir . $tabela_ . '-' . $key . '.php';
                            $arquivoTmpGlobal = $dirApps . $tabela_ . '-' . $key . '.php';
                            if (!$g['modoDesenvolvedor'] && file_exists($arquivoTmpGlobal)) {
                                $arquivoTmp = $arquivoTmpGlobal;
                            }
                            $arquivoTmpGlobal = $dirAppsEditor . $tabela_ . '-' . $key . '.php';
                            if ($g['modoDesenvolvedorApp'] && file_exists($arquivoTmpGlobal)) {
                                $arquivoTmp = $arquivoTmpGlobal;
                            }
                            $arquivoTmpGlobal = $dirGlobal . $tabela_ . '-' . $key . '.php';
                            if (!$g['modoDesenvolvedor'] && !$g['modoDesenvolvedorApp'] && file_exists($arquivoTmpGlobal)) {
                                $arquivoTmp = $arquivoTmpGlobal;
                            }
                            if (file_exists($arquivoTmp)) {
                                require_once $arquivoTmp;
                                $this->carregado[$tabela_ . '-' . $key] = true;
                                $novoArquivoBancoEmpresa = true;
                            } else {
                                $this->carregado[$tabela_ . '-' . $key] = false;
                            }
                        } else {
                            if (isset($this->carregado[$tabela_ . '-' . $key])) {
                                $novoArquivoBancoEmpresa = $this->carregado[$tabela_ . '-' . $key];
                            } else {
                                $this->carregado[$tabela_ . '-' . $key] = false;
                            }
                        }
                    }
                }
            }
        }
        $arquivo = 'ide/personalizacaoForm/' . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $campo . '-' . $tipoConf;
        $arquivoGlobal = Apps::getDirAtivos() . "personalizacaoForm/" . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $campo . '-' . $tipoConf;
        if (!$g['modoDesenvolvedor'] && file_exists($arquivoGlobal)) {
            $arquivo = $arquivoGlobal;
        }
        $arquivoGlobal = Apps::getDirEditor() . "personalizacaoForm/" . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $campo . '-' . $tipoConf;
        if ($g['modoDesenvolvedorApp'] && file_exists($arquivoGlobal)) {
            $arquivo = $arquivoGlobal;
        }
        $arquivoGlobal = "{$g['pathDados']}personalizacaoForm/" . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $campo . '-' . $tipoConf;
        if (!$g['modoDesenvolvedor'] && !$g['modoDesenvolvedorApp'] && file_exists($arquivoGlobal)) {
            $arquivo = $arquivoGlobal;
        }
        if (!(isset($this->carregado[$arquivo]))) {
            if (file_exists($arquivo)) {
                require_once $arquivo;
                $this->carregado[$arquivo] = true;
            } else {
                $this->carregado[$arquivo] = false;
            }
        }
        if (!(!$this->carregado[$arquivo] && !$this->carregado[$arquivoUser] && !$this->carregado[$arquivoUserEmp] && !$novoArquivoBanco && $novoArquivoBancoEmpresa && !$this->carregado[$arquivoUserEmpForm])) {
            return true;
        } else {
            return false;
        }
    }

    function restricaoIncluirUpdate($campo, $valor, $valores = false, $acaoId = false)
    {
        global $g;
        $tabela = explode('_', $campo);
        $tabela = $tabela[0];
        $sql = "SHOW COLUMNS FROM {$tabela} WHERE FIELD = '{$campo}'";
        $resShow = mysqli_query($sql);
        if (mysql_num_rows($resShow) == 1) {
            $rowShow = mysql_fetch_assoc($resShow);
            if (!(!preg_match('#^(varchar|char|int)\\(([0-9]+)\\)( unsigned)?$#', $rowShow['Type'], $matches))) {
                if (!($valor != '...' && strlen($valor) > $matches[2])) {
                } else {
                    log($sql);
                    return "Tamanho máximo é de {$matches[2]}. Tamanho digitado: " . strlen($valor);
                }
            }
            if (!(!preg_match('#^(decimal)\\(([0-9]+),([0-9]+)\\)$#', $rowShow['Type'], $matches))) {
                $valor = str_replace('.', '', $valor);
                $valor = str_replace(',', '.', $valor);
                $tmp = explode('.', $valor);
                $numero = $tmp[0];
                $casas = isset($tmp[1]) ? $tmp[1] : '';
                if (!(strlen($numero) > $matches[2] || strlen($casas) > $matches[3])) {
                } else {
                    log($sql);
                    return "Tamanho máximo é de {$matches[2]} com {$matches[3]} casas decimais. Tamanho digitado: " . strlen($numero) . " com " . strlen($casas) . " casas decimais";
                }
            }
        }
        $conf = &$this->conf;
        if (!$valores) {
            $valores = array();
        }
        if ($this->carregaArquivoConf($campo, 'restricaoIncluirUpdate')) {
            if (isset($this->conf['restricaoIncluirUpdate'][$campo])) {
                $retorno = true;
                eval("namespace hardness;\n" . $this->conf['restricaoIncluirUpdate'][$campo]);
                return $retorno;
            } else {
                return true;
            }
        } else {
            return true;
        }
    }

    function restricaoExcluir($campo, $valor, $space = '')
    {
        global $g;
        $conf = &$this->conf;
        $this->carregaArquivoConf($campo, 'restricaoExcluir');
        $tabela = explode('_', $campo);
        $tabela = $tabela[0];
        $relacionamentos = gPegaRelacionamento2($tabela);
        $campos = array();
        foreach ($relacionamentos as $fk) {
            if (!(isset($this->conf['ignorarRelacionamentoExcluir']) && in_array($fk, $this->conf['ignorarRelacionamentoExcluir']))) {
                $tabela = explode('_', $fk);
                $tabela = $tabela[0];
                $sql = "SELECT {$tabela}_Id, {$fk} FROM {$tabela} WHERE {$fk} = '{$valor}'";
                $result = mysql_query($sql);
                if (!mysql_error()) {
                    if (mysql_num_rows($result) > 0) {
                        if (!(in_array($fk, $campos))) {
                            $campos[] = $fk;
                        }
                    }
                }
            }
        }
        if (!(empty($campos))) {
            $g['restricaoExcluir'] = $campos;
        }
        return true;
    }

    function restricaoVerificaIncluirUpdate($campo)
    {
        global $g;
        $conf = &$this->conf;
        $this->carregaArquivoConf($campo, 'restricaoIncluirUpdate');
        $this->carregaArquivoConf('GLOBAL', 'GLOBAL');
        if (isset($this->conf['restricaoIncluirUpdate'][$campo])) {
            return true;
        } else {
            return false;
        }
    }

    function camposNaoUsar($campo)
    {
        global $g;
        $conf = &$this->conf;
        if ($this->carregaArquivoConf($campo, 'camposNaoUsar')) {
            if (in_array($campo, $this->conf['camposNaoUsar'])) {
                return false;
            } else {
                return true;
            }
        } else {
            return true;
        }
    }

    function tabsNaoUsar($tab, $nome)
    {
        global $g;
        $conf = &$this->conf;
        if ($this->carregaArquivoConf($nome, 'tabsNaoUsar')) {
            if (isset($this->conf['tabsNaoUsar'][$g['perfilUsuario'] . '-' . $tab]) && count($this->conf['tabsNaoUsar'][$g['perfilUsuario'] . '-' . $tab]) > 0) {
                $tab = $g['perfilUsuario'] . '-' . $tab;
            }
            if (isset($this->conf['tabsNaoUsar'][$tab])) {
                if (in_array($nome, $this->conf['tabsNaoUsar'][$tab])) {
                    return false;
                } else {
                    return true;
                }
            } else {
                return true;
            }
        } else {
            return true;
        }
    }

    function camposValorPadrao($campo)
    {
        global $g;
        $conf = &$this->conf;
        if ($this->carregaArquivoConf($campo, 'camposValorPadrao')) {
            if (isset($this->conf['camposValorPadrao'][$campo])) {
                return $this->conf['camposValorPadrao'][$campo];
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    function restricaoTravar($operacao, $campoPk, $valorPk, $campoPkParent = null, $valorPkParent = null, $campo = null, $valor = null)
    {
        global $g;
        $conf = &$this->conf;
        if ($this->carregaArquivoConf($campoPk, 'restricaoTravar')) {
            $campo_ = explode('_', $campoPk);
            if (count($campo_) > 1) {
                $tabela = $campo_[0];
            } else {
                $tabela = '_GLOBAL';
            }
            if (isset($this->conf['restricaoTravar'][$tabela])) {
                $retorno = true;
                log("restricaoTravar Tabela: " . $tabela);
                eval("namespace hardness;\n" . $this->conf['restricaoTravar'][$tabela]);
                return $retorno;
            } else {
                return true;
            }
        } else {
            return true;
        }
    }

    function camposLabelForm($campo)
    {
        global $g;
        $conf = &$this->conf;
        if ($this->carregaArquivoConf($campo, 'camposLabelForm')) {
            if (isset($this->conf['camposLabelForm'][$campo])) {
                return $this->conf['camposLabelForm'][$campo];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    function camposTipo($campo)
    {
        global $g;
        $conf = &$this->conf;
        if ($this->carregaArquivoConf($campo, 'camposTipo')) {
            if (isset($this->conf['camposTipo'][$campo])) {
                return $this->conf['camposTipo'][$campo];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    function carregaAltText()
    {
        global $g, $confUsuario;
        $conf = &$this->conf;
        $arquivo = "{$g['pathRaiz']}configuracoes/_alteracoesTextuais.php";
        if (!isset($this->carregado[$arquivo]) && file_exists($arquivo)) {
            require_once $arquivo;
            $this->carregado[$arquivo] = true;
        }
        $arquivoUser = "{$g['pathRaiz']}configuracoes/configuracoesPorBanco/{$confUsuario['dbDatabase']}/_alteracoesTextuais.php";
        if (!isset($this->carregado[$arquivoUser]) && file_exists($arquivoUser)) {
            require_once $arquivoUser;
            $this->carregado[$arquivoUser] = true;
        }
        $arquivoTmp = "ide/personalizacaoConf/GLOBAL-altText.php";
        $arquivoTmpGlobal = Apps::getDirAtivos() . "personalizacaoConf/GLOBAL-altText.php";
        if (!$g['modoDesenvolvedor'] && file_exists($arquivoTmpGlobal)) {
            $arquivoTmp = $arquivoTmpGlobal;
        }
        $arquivoTmpGlobal = Apps::getDirEditor() . "personalizacaoConf/GLOBAL-altText.php";
        if ($g['modoDesenvolvedorApp'] && file_exists($arquivoTmpGlobal)) {
            $arquivoTmp = $arquivoTmpGlobal;
        }
        $arquivoTmpGlobal = "{$g['pathDados']}personalizacaoConf/GLOBAL-altText.php";
        if (!$g['modoDesenvolvedor'] && !$g['modoDesenvolvedorApp'] && file_exists($arquivoTmpGlobal)) {
            $arquivoTmp = $arquivoTmpGlobal;
        }
        if (!isset($this->carregado[$arquivoTmp]) && file_exists($arquivoTmp)) {
            require_once $arquivoTmp;
            $this->carregado[$arquivoTmp] = true;
        }
        return null;
    }

    function altText($id)
    {
        if (isset($this->conf['altText'][$id])) {
            return $this->conf['altText'][$id];
        } else {
            return $id;
        }
    }

    function triggers($tabela, $acao, $chaveValor = false)
    {
        global $g;
        $conf = &$this->conf;
        if ($this->carregaArquivoConf($tabela . '_Id', 'triggers')) {
            if (isset($this->conf['triggers'][$tabela])) {
                if (isset($this->conf['triggers'][$tabela][$acao]) && !empty($this->conf['triggers'][$tabela][$acao])) {
                    log("Trigger: {$tabela} - Acao: {$acao}");
                    eval("namespace hardness;\n" . $this->conf['triggers'][$tabela][$acao]);
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    function ini($campo)
    {
        global $g;
        $conf = &$this->conf;
        if ($this->carregaArquivoConf($campo, 'ini')) {
            if (isset($this->conf['ini'][$campo])) {
                return $this->conf['ini'][$campo];
            } else {
                return '.ini não encontrado (verifique: _GLOBAL.php)';
            }
        } else {
            return false;
        }
    }

    function temAlteracoes($tabela, $key = false)
    {
        global $g;
        $tem = false;
        $dir = "{$g['pathDados']}personalizacaoConf/";
        if ($g['modoDesenvolvedor']) {
            $dir = "ide/personalizacaoConf/";
        } else {
            if (!(!$g['modoDesenvolvedorApp'])) {
                $dir = Apps::getDirEditor() . "personalizacaoConf/";
            }
        }
        if ($key) {
            $arquivo = $dir . $tabela . '-' . $key . '.php';
            if (!(!file_exists($arquivo))) {
                $tem = true;
            }
        } else {
            foreach ($this->conf as $key => $value) {
                $arquivo = $dir . $tabela . '-' . $key . '.php';
                if (!(!file_exists($arquivo))) {
                    $tem = true;
                }
            }
        }
        return $tem;
    }

    function auditoriaCampos($campo)
    {
        global $g;
        $conf = &$this->conf;
        $this->carregaArquivoConf($campo, 'auditoriaCampos');
        return true;
    }

    public static function pegarUrlFrameworkNovo() : string
    {
        global $g;
        $urlFrameworkNovo = $g['C031']['urlFrameworkNovo'];
        $urlFrameworkNovo = str_replace(["http://", "https://"], '', $urlFrameworkNovo);
        $url = "http://" . $urlFrameworkNovo . '/erp/';
        $login = $g['C007']['C007_Login'];
        $senha = sha1('HARDNESS' . base64_decode($g['C007']['C007_Senha']));
        $authToken = md5("{$login}:{$senha}");
        $url = "{$url}?authToken={$authToken}&currentFirm={$g['empresaAtual']}";
        return $url;
    }

}
