<?php
/**
 * apps.php
 *
 * Versão legível de sistema-legado/sistema/apps.php.
 * O original foi ofuscado com desvios; aqui o fluxo foi reconstruído
 * em if/else/while/foreach, no mesmo comportamento.
 * Este arquivo é referência de leitura e não é carregado pelo sistema.
 */
namespace hardness;

class Apps
{
    public static function getDirEditor($appId = false)
    {
        global $g;
        if (!$appId) {
            $appId = $g['modoDesenvolvedorApp'];
        }
        if (!$appId) {
            return false;
        } else {
            return "apps/desenvolvedor/u{$g['usuarioAtual']}-{$appId}/";
        }
    }

    public static function getDirAtivos()
    {
        global $confUsuario;
        return "apps/ativos/{$confUsuario['dbDatabase']}/";
    }

    public static function getDirLoja()
    {
        return "apps/loja/";
    }

    public static function criarDiretorios()
    {
        global $g;
        $dir = self::getDirAtivos();
        is_dir($dir) or mkdir($dir, 0777, true);
        $dir = self::getDirEditor();
        if ($dir) {
            is_dir($dir) or mkdir($dir, 0777, true);
        }
        $dir = "apps/desenvolvedor/";
        is_dir($dir) or mkdir($dir, 0777, true);
        $dir = self::getDirLoja();
        is_dir($dir) or mkdir($dir, 0777, true);
    }

    public static function listarApps()
    {
        $apps = array();
        $appsMeta = array_filter(scandir(self::getDirLoja()), function($valor) {
            return $valor != '.' && $valor != '..' && $valor != '.gitignore' && preg_match('/\\.json$/', $valor);
        });
        foreach ($appsMeta as $appId) {
            $appId = preg_replace('/\\.json$/', '', $appId);
            $apps[] = self::getApp($appId);
        }
        return $apps;
    }

    public static function salvarApp($app)
    {
        $appMeta = self::getDirLoja() . $app['id'] . '.json';
        file_put_contents($appMeta, json_encode($app, JSON_PRETTY_PRINT));
    }

    public static function getApp($id)
    {
        $appMeta = self::getDirLoja() . $id . '.json';
        return json_decode(file_get_contents($appMeta), true);
    }

    public static function modoDesenvolvedor($app)
    {
        global $g, $confUsuario;
        if (!empty($app['id'])) {
            $dirAtivo = "apps/desenvolvedor/u{$g['usuarioAtual']}-{$app['id']}/";
            is_dir($dirAtivo) or mkdir($dirAtivo, 0777, true);
            $metaAppDir = "{$dirAtivo}app.json";
            $appVersaoModoEdicao = false;
            if (!(!file_exists($metaAppDir))) {
                $appVersaoModoEdicao = json_decode(file_get_contents($metaAppDir), true);
            }
            file_put_contents($metaAppDir, json_encode($app, JSON_PRETTY_PRINT));
            if ($app['versao'] != 'v0.0.0') {
                $dirAppVersao = self::getDirLoja() . $app['id'] . '/' . $app['versao'] . '/';
                foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dirAppVersao)) as $arquivo) {
                    if (!$arquivo->isDir() && $arquivo->getFilename() != 'app.json') {
                        $novoDir = $dirAtivo . str_replace($dirAppVersao, '', str_replace('/' . $arquivo->getFilename(), '', $arquivo->getPath())) . '/';
                        is_dir($novoDir) or mkdir($novoDir, 0777, true);
                        $novoArquivo = $novoDir . $arquivo->getFilename();
                        if (file_exists($novoArquivo) && $appVersaoModoEdicao) {
                            $dirVersaoModoEdicao = self::getDirLoja() . $appVersaoModoEdicao['id'] . '/' . $appVersaoModoEdicao['versao'] . '/';
                            $dirVersaoModoEdicao = $dirVersaoModoEdicao . str_replace($dirAppVersao, '', str_replace('/' . $arquivo->getFilename(), '', $arquivo->getPath())) . '/';
                            if (!(md5_file($dirVersaoModoEdicao . $arquivo->getFilename()) != md5_file($novoArquivo))) {
                            }
                        }
                        copy($arquivo->getRealPath(), $novoArquivo);
                    }
                }
            }
            $g['modoDesenvolvedorApp'] = $app['id'];
            $g['atualizacaoBaseSomenteApps'] = true;
            include 'scripts/atualizacaoBaseIde.php';
        } else {
            return false;
        }
    }

    public static function publicarApp($app, $dados)
    {
        $dirDesenvolvedor = self::getDirEditor($app['id']);
        $dirLojaApp = self::getDirLoja() . $app['id'] . '/';
        is_dir($dirLojaApp) or mkdir($dirLojaApp, 0777, true);
        if (!(isset($app['versoes']))) {
            $app['versoes'] = array();
        }
        $app['versoes'][$dados['versao']] = array('versao' => $dados['versao'], 'notas' => $dados['notas'], 'producao' => $dados['producao']);
        $app['versao'] = $dados['versao'];
        self::salvarApp($app);
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dirDesenvolvedor)) as $arquivo) {
            if ($arquivo->isDir()) {
                if (count(glob("{$arquivo->getRealPath()}/*")) === 0) {
                    rmdir($arquivo->getRealPath());
                }
            } else {
                if (strpos($arquivo->getRealPath(), 'builder/arquivos/') !== false) {
                    unlink($arquivo->getRealPath());
                }
            }
        }
        rename($dirDesenvolvedor, $dirLojaApp . $dados['versao']);
        Apps::syncLocalParaApps();
    }

    public static function listarAtivos($tipo = 'apps')
    {
        $metaAtivosDir = self::getDirAtivos() . "ativos.json";
        if (!(file_exists($metaAtivosDir))) {
            file_put_contents($metaAtivosDir, json_encode(array(), JSON_PRETTY_PRINT));
        }
        $ativos = json_decode(file_get_contents($metaAtivosDir), true);
        if (!(!isset($ativos[$tipo]) || $tipo == 'todos')) {
            return $ativos[$tipo];
        } else {
            return $ativos;
        }
    }

    public static function salvarAtivos($metaAtivos)
    {
        $metaAtivosDis = self::getDirAtivos() . "ativos.json";
        file_put_contents($metaAtivosDis, json_encode($metaAtivos, JSON_PRETTY_PRINT));
        return true;
    }

    public static function mergeApps($apps, $pathDestino)
    {
        global $conflitosMerge, $conflitosMergeArquivos, $arquivosMerge;
        $conflitosMerge = array();
        $conflitosMergeArquivos = array();
        $incluirConflito = function($path, $id, $versao, $appNome, $mensagem) {
            global $conflitosMerge, $conflitosMergeArquivos;
            $conflitosMerge[$path][] = array('appId' => $id, 'appVersao' => $versao, 'appNome' => $versao, 'arquivo' => $path, 'conflito' => $mensagem, 'conflitoApp' => $conflitosMergeArquivos[$path]);
        };
        foreach ($apps as $app) {
            $arquivos = self::listarArquivosApp($app['id'], $app['versao']);
            foreach ($arquivos as $arquivo) {
                $arquivosMerge[$arquivo['path']] = $app['id'];
                $novoDir = $pathDestino . $arquivo['pathDir'];
                is_dir($novoDir) or mkdir($novoDir, 0777, true);
                $novoArquivo = $pathDestino . $arquivo['path'];
                if (!file_exists($novoArquivo)) {
                    copy($arquivo['realPath'], $novoArquivo);
                } else {
                    if ($arquivo['formato'] == 'php-serializado') {
                        if (in_array($arquivo['tipo'], array('botoes', 'grid-tamanho-personalizado'))) {
                            $arquivoOrigem = unserialize(file_get_contents($arquivo['realPath']));
                            $arquivoDestino = unserialize(file_get_contents($novoArquivo));
                            $merge = $arquivoDestino;
                            foreach ($arquivoOrigem as $itemOrigem) {
                                $encontrou = false;
                                foreach ($arquivoDestino as $key => $itemDestino) {
                                    if ($itemOrigem['id'] == $itemDestino['id']) {
                                        $encontrou = true;
                                        $merge[$key] = $itemOrigem;
                                        $incluirConflito($arquivo['path'], $app['id'], $app['versao'], $app['nome'], "Botão '{$itemOrigem['titulo']}' ({$itemOrigem['id']}) já foi modificado por um App, será substituído.");
                                    }
                                }
                                if (!$encontrou) {
                                    $merge[] = $itemOrigem;
                                }
                            }
                            unlink($novoArquivo);
                            file_put_contents($novoArquivo, serialize($merge));
                        } else {
                            $incluirConflito($arquivo['path'], $app['id'], $app['versao'], $app['nome'], 'Tipo não suporta merge, o arquivo será sobreposto.');
                            unlink($novoArquivo);
                            copy($arquivo['realPath'], $novoArquivo);
                        }
                    } else {
                        if ($arquivo['formato'] == 'php') {
                            if (in_array($arquivo['tipo'], array('restricao-incluir-update', 'alteracoes-textos', 'coluna-editar-restricoes', 'variaveis-sistema', 'menu-fastcrud'))) {
                                $arquivoOrigem = file_get_contents($arquivo['realPath']);
                                $arquivoOrigem = str_replace('<?php', '', $arquivoOrigem);
                                $arquivoOrigem = str_replace('namespace hardness;', '', $arquivoOrigem);
                                $arquivoDestino = file_get_contents($novoArquivo);
                                $merge = $arquivoDestino . "\n\n" . $arquivoOrigem;
                                unlink($novoArquivo);
                                file_put_contents($novoArquivo, $merge);
                            } else {
                                if (in_array($arquivo['tipo'], array('php-classe'))) {
                                    $merge = file_get_contents($novoArquivo);
                                    $mergeSerialized = @unserialize($merge);
                                    if (!$mergeSerialized) {
                                        $mergeSerialized = array($merge);
                                    }
                                    $mergeSerialized[] = file_get_contents($arquivo['realPath']);
                                    unlink($novoArquivo);
                                    file_put_contents($novoArquivo, serialize($mergeSerialized));
                                } else {
                                    $incluirConflito($arquivo['path'], $app['id'], $app['versao'], $app['nome'], 'Tipo não suporta merge, o arquivo será sobreposto.');
                                    unlink($novoArquivo);
                                    copy($arquivo['realPath'], $novoArquivo);
                                }
                            }
                        } else {
                            if ($arquivo['formato'] == 'arquivo-xlsx') {
                                $incluirConflito($arquivo['path'], $app['id'], $app['versao'], $app['nome'], 'Tipo não suporta merge, o arquivo será sobreposto.');
                                unlink($novoArquivo);
                                copy($arquivo['realPath'], $novoArquivo);
                            } else {
                                if ($arquivo['formato'] == 'texto') {
                                    if (in_array($arquivo['tipo'], array('grid-personalizado', 'grid-filtros-personalizado'))) {
                                        $tipo = 'grid';
                                    } else {
                                        if ($arquivo['tipo'] == 'form-personalizado') {
                                            $tipo = 'form';
                                        } else {
                                            if ($arquivo['tipo'] == 'grid-totais-personalizado') {
                                                $tipo = 'totais';
                                            } else {
                                                if ($arquivo['tipo'] == 'tab-personalizado') {
                                                    $tipo = 'tab';
                                                } else {
                                                    if ($arquivo['tipo'] == 'banco') {
                                                        $arquivoOrigem = file_get_contents($arquivo['realPath']);
                                                        $arquivoDestino = file_get_contents($novoArquivo);
                                                        unlink($novoArquivo);
                                                        file_put_contents($novoArquivo, $arquivoOrigem . $arquivoDestino);
                                                        $conflitosMergeArquivos[$arquivo['path']] = array('appId' => $app['id'], 'appVersao' => $app['versao'], 'appNome' => $app['nome']);
                                                    } else {
                                                        if ($arquivo['tipo'] == 'grid-extra') {
                                                            $arquivoOrigem = file_get_contents($arquivo['realPath']);
                                                            $arquivoDestino = file_get_contents($novoArquivo);
                                                            unlink($novoArquivo);
                                                            file_put_contents($novoArquivo, Personalizacoes::extraMerge($arquivoOrigem, $arquivoDestino));
                                                        } else {
                                                            if ($arquivo['tipo'] == 'formulas') {
                                                                $tipo = 'formulas';
                                                            } else {
                                                                $incluirConflito($arquivo['path'], $app['id'], $app['versao'], $app['nome'], 'Tipo não suporta merge, o arquivo será sobreposto.');
                                                                unlink($novoArquivo);
                                                                copy($arquivo['realPath'], $novoArquivo);
                                                                $conflitosMergeArquivos[$arquivo['path']] = array('appId' => $app['id'], 'appVersao' => $app['versao'], 'appNome' => $app['nome']);
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    $arquivoOrigem = file_get_contents($arquivo['realPath']);
                                    $arrayOrigem = self::lerTipoTexto($arquivoOrigem, $tipo);
                                    $arquivoDestino = file_get_contents($novoArquivo);
                                    $arrayDestino = self::lerTipoTexto($arquivoDestino, $tipo);
                                    foreach ($arrayOrigem as $key => $value) {
                                        if (!isset($arrayDestino[$key])) {
                                            $arrayDestino[$key] = $value;
                                        } else {
                                            foreach ($value as $key2 => $value2) {
                                                if (!($value2 == $key)) {
                                                    if (!($key2 == 'tab')) {
                                                        if (!(!isset($arrayDestino[$key][$key2]))) {
                                                            $incluirConflito($arquivo['path'], $app['id'], $app['versao'], $app['nome'], "Valor [{$key}][{$key2}] já foi modificado por um App, será substituído.");
                                                        }
                                                        $arrayDestino[$key][$key2] = $value2;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    unlink($novoArquivo);
                                    file_put_contents($novoArquivo, self::converterTipoTexto($arrayDestino));
                                } else {
                                    if ($arquivo['formato'] == 'json') {
                                        $incluirConflito($arquivo['path'], $app['id'], $app['versao'], $app['nome'], 'Tipo não suporta merge, o arquivo será sobreposto.');
                                        unlink($novoArquivo);
                                        copy($arquivo['realPath'], $novoArquivo);
                                    } else {
                                        $incluirConflito($arquivo['path'], $app['id'], $app['versao'], $app['nome'], 'Tipo não suporta merge, o arquivo será sobreposto.');
                                        unlink($novoArquivo);
                                        copy($arquivo['realPath'], $novoArquivo);
                                    }
                                }
                            }
                        }
                    }
                }
                $conflitosMergeArquivos[$arquivo['path']] = array('appId' => $app['id'], 'appVersao' => $app['versao'], 'appNome' => $app['nome']);
            }
        }
        return $conflitosMerge;
    }

    public static function listarArquivosApp($appId, $versao = 'desenvolvedor')
    {
        $arquivos = array();
        $lojaApp = self::getApp($appId);
        if ($versao == 'desenvolvedor') {
            $dir = self::getDirEditor($appId);
        } else {
            if ($versao == 'ultima') {
                $dir = self::getDirLoja() . $appId . '/' . $lojaApp['versao'];
            } else {
                $dir = self::getDirLoja() . $appId . '/' . $versao;
            }
        }
        if (file_exists($dir)) {
            foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir)) as $arquivo) {
                if (!$arquivo->isDir()) {
                    $arquivo = array('path' => ltrim(str_replace($dir, '', $arquivo->getPathname()), '/'), 'pathDir' => ltrim(str_replace($dir, '', $arquivo->getPath()), '/'), 'realPath' => $arquivo->getRealPath(), 'tipo' => '', 'formato' => '');
                    if ($arquivo['path'] == 'app.json') {
                    } else {
                        if (preg_match('/^personalizacaoBotoes\\/[0-9a-z]+$/', $arquivo['path'])) {
                            $arquivo['tipo'] = 'botoes';
                            $arquivo['formato'] = 'php-serializado';
                        } else {
                            if (preg_match('/^personalizacaoCodigo\\/codigos\\/[0-9a-z]+$/', $arquivo['path'])) {
                                $arquivo['tipo'] = 'codigo';
                                $arquivo['formato'] = 'php-serializado';
                            } else {
                                if (preg_match('/^personalizacaoCodigo\\/codigos\\/[0-9a-z]+\\-metodos\\/[0-9a-z]+$/', $arquivo['path'])) {
                                    $arquivo['tipo'] = 'codigos-metodos';
                                    $arquivo['formato'] = 'php-serializado';
                                } else {
                                    if (preg_match('/^personalizacaoConf\\/CAMPOS_LIVRE\\-restricaoIncluirUpdate\\.php$/', $arquivo['path'])) {
                                        $arquivo['tipo'] = 'restricao-incluir-update';
                                        $arquivo['formato'] = 'php';
                                    } else {
                                        if (preg_match('/^personalizacaoConf\\/GLOBAL\\-altText\\.php$/', $arquivo['path'])) {
                                            $arquivo['tipo'] = 'alteracoes-textos';
                                            $arquivo['formato'] = 'php';
                                        } else {
                                            if (preg_match('/^personalizacaoConf\\/GLOBAL\\-colunaEditarRestricoes\\.php$/', $arquivo['path'])) {
                                                $arquivo['tipo'] = 'coluna-editar-restricoes';
                                                $arquivo['formato'] = 'php';
                                            } else {
                                                if (preg_match('/^personalizacaoConf\\/GLOBAL\\-ini\\.php$/', $arquivo['path'])) {
                                                    $arquivo['tipo'] = 'variaveis-sistema';
                                                    $arquivo['formato'] = 'php';
                                                } else {
                                                    if (preg_match('/^personalizacaoConf\\/GLOBAL\\-menuBuilder\\.php$/', $arquivo['path'])) {
                                                        $arquivo['tipo'] = 'menu-fastcrud';
                                                        $arquivo['formato'] = 'php';
                                                    } else {
                                                        if (preg_match('/^personalizacaoContent\\/[0-9a-z]+$/', $arquivo['path'])) {
                                                            $arquivo['tipo'] = 'content-personalizado';
                                                            $arquivo['formato'] = 'texto';
                                                        } else {
                                                            if (preg_match('/^personalizacaoContent\\/contents\\/[0-9a-z]+$/', $arquivo['path'])) {
                                                                $arquivo['tipo'] = 'content';
                                                                $arquivo['formato'] = 'php-serializado';
                                                            } else {
                                                                if (preg_match('/^personalizacaoExcel\\/excels\\/[0-9a-z]+$/', $arquivo['path'])) {
                                                                    $arquivo['tipo'] = 'excel';
                                                                    $arquivo['formato'] = 'php-serializado';
                                                                } else {
                                                                    if (preg_match('/^personalizacaoExcel\\/excels\\/[0-9a-z]+\\.excel$/', $arquivo['path'])) {
                                                                        $arquivo['tipo'] = 'excel-arquivo';
                                                                        $arquivo['formato'] = 'arquivo-xlsx';
                                                                    } else {
                                                                        if (preg_match('/^personalizacaoForm\\/[0-9a-z]+$/', $arquivo['path'])) {
                                                                            $arquivo['tipo'] = 'form-personalizado';
                                                                            $arquivo['formato'] = 'texto';
                                                                        } else {
                                                                            if (preg_match('/^personalizacaoForm\\/.*\\-callbackAoAlterar$/', $arquivo['path'])) {
                                                                                $arquivo['tipo'] = 'form-campo-callback-ao-alterar';
                                                                                $arquivo['formato'] = 'php';
                                                                            } else {
                                                                                if (preg_match('/^personalizacaoForm\\/.*\\-callbackAoSair$/', $arquivo['path'])) {
                                                                                    $arquivo['tipo'] = 'form-campo-callback-ao-sair';
                                                                                    $arquivo['formato'] = 'php';
                                                                                } else {
                                                                                    if (preg_match('/^personalizacaoForm\\/.*\\-callbackPreencherValor$/', $arquivo['path'])) {
                                                                                        $arquivo['tipo'] = 'form-campo-callback-preencher-valor';
                                                                                        $arquivo['formato'] = 'php';
                                                                                    } else {
                                                                                        if (preg_match('/^personalizacaoForm\\/.*\\-callbackPreencherSelect$/', $arquivo['path'])) {
                                                                                            $arquivo['tipo'] = 'form-campo-callback-preencher-select';
                                                                                            $arquivo['formato'] = 'php';
                                                                                        } else {
                                                                                            if (preg_match('/^personalizacaoForm\\/.*\\-callbackRestricao$/', $arquivo['path'])) {
                                                                                                $arquivo['tipo'] = 'form-campo-callback-restricao';
                                                                                                $arquivo['formato'] = 'php';
                                                                                            } else {
                                                                                                if (preg_match('/^personalizacaoForm\\/formEditorData\\-.*$/', $arquivo['path'])) {
                                                                                                    $arquivo['tipo'] = 'form-editor';
                                                                                                    $arquivo['formato'] = 'json';
                                                                                                } else {
                                                                                                    if (preg_match('/^personalizacaoForm\\/forms\\/[0-9a-z]+$/', $arquivo['path'])) {
                                                                                                        $arquivo['tipo'] = 'form';
                                                                                                        $arquivo['formato'] = 'php-serializado';
                                                                                                    } else {
                                                                                                        if (preg_match('/^personalizacaoGrid\\/grids\\/[0-9a-z]+$/', $arquivo['path'])) {
                                                                                                            $arquivo['tipo'] = 'grid';
                                                                                                            $arquivo['formato'] = 'php-serializado';
                                                                                                        } else {
                                                                                                            if (preg_match('/^personalizacaoGrid\\/[0-9a-z]+$/', $arquivo['path'])) {
                                                                                                                $arquivo['tipo'] = 'grid-personalizado';
                                                                                                                $arquivo['formato'] = 'texto';
                                                                                                            } else {
                                                                                                                if (preg_match('/^personalizacaoGrid\\/[0-9a-z]+\\-callback\\-.+(\\-personalizado)?\\-[0-9a-z]+$/i', $arquivo['path'])) {
                                                                                                                    $arquivo['tipo'] = 'grid-coluna-callback';
                                                                                                                    $arquivo['formato'] = 'php';
                                                                                                                } else {
                                                                                                                    if (preg_match('/^personalizacaoGrid\\/[0-9a-z]+\\-filtros$/', $arquivo['path'])) {
                                                                                                                        $arquivo['tipo'] = 'grid-filtros-personalizado';
                                                                                                                        $arquivo['formato'] = 'texto';
                                                                                                                    } else {
                                                                                                                        if (preg_match('/^personalizacaoGrid\\/[0-9a-z]+\\-ordem$/', $arquivo['path'])) {
                                                                                                                            $arquivo['tipo'] = 'grid-ordem-personalizado';
                                                                                                                            $arquivo['formato'] = 'texto';
                                                                                                                        } else {
                                                                                                                            if (preg_match('/^personalizacaoGrid\\/[0-9a-z]+\\-totais\\-novo$/', $arquivo['path'])) {
                                                                                                                                $arquivo['tipo'] = 'grid-totais-personalizado';
                                                                                                                                $arquivo['formato'] = 'texto';
                                                                                                                            } else {
                                                                                                                                if (preg_match('/^personalizacaoGrid\\/[0-9a-z]+\\-width$/', $arquivo['path'])) {
                                                                                                                                    $arquivo['tipo'] = 'grid-tamanho-personalizado';
                                                                                                                                    $arquivo['formato'] = 'php-serializado';
                                                                                                                                } else {
                                                                                                                                    if (preg_match('/^personalizacaoOutro\\/[0-9a-z]+$/', $arquivo['path'])) {
                                                                                                                                        $arquivo['tipo'] = 'outro-personalizado';
                                                                                                                                        $arquivo['formato'] = 'php';
                                                                                                                                    } else {
                                                                                                                                        if (preg_match('/^personalizacaoOutro\\/outros\\/[0-9a-z]+$/', $arquivo['path'])) {
                                                                                                                                            $arquivo['tipo'] = 'outro';
                                                                                                                                            $arquivo['formato'] = 'php-serializado';
                                                                                                                                        } else {
                                                                                                                                            if (preg_match('/^personalizacaoTab\\/[0-9a-z]+$/', $arquivo['path'])) {
                                                                                                                                                $arquivo['tipo'] = 'tab-personalizado';
                                                                                                                                                $arquivo['formato'] = 'texto';
                                                                                                                                            } else {
                                                                                                                                                if (preg_match('/^personalizacaoTab\\/tabs\\/[0-9a-z]+$/', $arquivo['path'])) {
                                                                                                                                                    $arquivo['tipo'] = 'tab';
                                                                                                                                                    $arquivo['formato'] = 'php-serializado';
                                                                                                                                                } else {
                                                                                                                                                    if (preg_match('/^personalizacaoBanco\\/atualizacaoBaseIde\\.txt$/', $arquivo['path'])) {
                                                                                                                                                        $arquivo['tipo'] = 'banco';
                                                                                                                                                        $arquivo['formato'] = 'texto';
                                                                                                                                                    } else {
                                                                                                                                                        if (preg_match('/^personalizacaoBanco\\/atualizacaoExcluir\\.txt$/', $arquivo['path'])) {
                                                                                                                                                            $arquivo['tipo'] = 'banco-excluir';
                                                                                                                                                            $arquivo['formato'] = 'texto';
                                                                                                                                                        } else {
                                                                                                                                                            if (preg_match('/^personalizacaoBanco\\/codigos\\/FUNCTION\\-.*\\.sql$/', $arquivo['path'])) {
                                                                                                                                                                $arquivo['tipo'] = 'banco-function';
                                                                                                                                                                $arquivo['formato'] = 'texto';
                                                                                                                                                            } else {
                                                                                                                                                                if (preg_match('/^personalizacaoBanco\\/codigos\\/PROCEDURE\\-.*\\.sql$/', $arquivo['path'])) {
                                                                                                                                                                    $arquivo['tipo'] = 'banco-procedure';
                                                                                                                                                                    $arquivo['formato'] = 'texto';
                                                                                                                                                                } else {
                                                                                                                                                                    if (preg_match('/^personalizacaoBanco\\/codigos\\/TRIGGER\\-.*\\.sql$/', $arquivo['path'])) {
                                                                                                                                                                        $arquivo['tipo'] = 'banco-trigger';
                                                                                                                                                                        $arquivo['formato'] = 'texto';
                                                                                                                                                                    } else {
                                                                                                                                                                        if (preg_match('/^personalizacaoOutro\\/grids\\/[0-9a-z]+$/', $arquivo['path'])) {
                                                                                                                                                                            $arquivo['tipo'] = 'grid';
                                                                                                                                                                            $arquivo['formato'] = 'php-serializado';
                                                                                                                                                                        } else {
                                                                                                                                                                            if (preg_match('/^personalizacaoConf\\/.*\\-alterarMetodos\\.php$/', $arquivo['path'])) {
                                                                                                                                                                                $arquivo['tipo'] = 'php-classe';
                                                                                                                                                                                $arquivo['formato'] = 'php';
                                                                                                                                                                            } else {
                                                                                                                                                                                if (preg_match('/^personalizacaoGrid\\/[0-9a-z]+\\-extra$/', $arquivo['path'])) {
                                                                                                                                                                                    $arquivo['tipo'] = 'grid-extra';
                                                                                                                                                                                    $arquivo['formato'] = 'texto';
                                                                                                                                                                                } else {
                                                                                                                                                                                    if (preg_match('/^builder\\/[0-9a-z]+\\.json$/', $arquivo['path'])) {
                                                                                                                                                                                        $arquivo['tipo'] = 'builder-projeto';
                                                                                                                                                                                        $arquivo['formato'] = 'texto';
                                                                                                                                                                                    } else {
                                                                                                                                                                                        if (preg_match('/^builder\\/arquivos\\/[0-9a-z]+$/', $arquivo['path'])) {
                                                                                                                                                                                            $arquivo['tipo'] = 'builder-arquivo';
                                                                                                                                                                                            $arquivo['formato'] = 'binario';
                                                                                                                                                                                        } else {
                                                                                                                                                                                            if (preg_match('/^personalizacaoFormula\\/[0-9a-z]+$/i', $arquivo['path'])) {
                                                                                                                                                                                                $arquivo['tipo'] = 'formulas';
                                                                                                                                                                                                $arquivo['formato'] = 'texto';
                                                                                                                                                                                            } else {
                                                                                                                                                                                                $arquivo['tipo'] = 'desconhecido';
                                                                                                                                                                                                $arquivo['formato'] = 'texto';
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
                                }
                            }
                        }
                        if ($arquivo['tipo'] != 'builder-arquivo') {
                            $arquivos[$arquivo['path']] = $arquivo;
                        }
                    }
                }
            }
            return $arquivos;
        } else {
            return $arquivos;
        }
    }

    public static function lerTipoTexto($conteudo, $tipo = 'grid')
    {
        $retorno = array();
        $personalizacao = $conteudo;
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
            if ($tipo == 'grid') {
                $retorno[$item['colunaValor']] = $item;
            } else {
                if ($tipo == 'form') {
                    $retorno[$item['field']] = $item;
                } else {
                    if ($tipo == 'totais') {
                        $retorno[$item['nome']] = $item;
                    } else {
                        if ($tipo == 'tab') {
                            $retorno[$item['url']] = $item;
                        } else {
                            if ($tipo == 'formulas') {
                                $retorno[$item['updateId']] = $item;
                            } else {
                                $retorno[] = $item;
                            }
                        }
                    }
                }
            }
        }
        return $retorno;
    }

    public static function converterTipoTexto($array)
    {
        $retorno = '';
        foreach ($array as $keyNivelCampo => $valueNivelCampo) {
            foreach ($valueNivelCampo as $key => $value) {
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
            $retorno .= "---\n";
        }
        return $retorno;
    }

    public static function syncAppsParaLocal($ignorarErro = false)
    {
        global $g;
        chmod("{$g['pathRaiz']}outros/apps/apps.key", 0600);
        $comando = "export HOME=/tmp ; /usr/bin/rsync -aq --timeout=30 --delete -e \"/usr/bin/ssh -o StrictHostKeyChecking=no -i {$g['pathRaiz']}outros/apps/apps.key -o UserKnownHostsFile=/tmp/known_hosts\" apps@apps.hardness.com.br:producao/ {$g['pathRaiz']}apps/loja/ 2>&1";
        exec($comando, $output, $result_code);
        if (!($result_code !== 0 && !$ignorarErro)) {
        } else {
            die("Erro de comunicação com a Loja de Apps, por favor, entre em contato com o suporte técnico:\n<br/>" . implode("\n<br/>", $output));
        }
    }

    public static function syncLocalParaApps()
    {
        global $g;
        chmod("{$g['pathRaiz']}outros/apps/apps.key", 0600);
        $comando = "export HOME=/tmp ; /usr/bin/rsync -aq --timeout=30 -e \"/usr/bin/ssh -o StrictHostKeyChecking=no -i {$g['pathRaiz']}outros/apps/apps.key -o UserKnownHostsFile=/tmp/known_hosts\" {$g['pathRaiz']}apps/loja/ apps@apps.hardness.com.br:producao/ 2>&1";
        exec($comando, $output, $result_code);
        if (!($result_code !== 0)) {
        } else {
            die("Erro de comunicação com a Loja de Apps, por favor, entre em contato com o suporte técnico:\n<br/>" . implode("\n<br/>", $output));
        }
    }

    public static function removerApp($appId)
    {
        global $g, $confUsuario;
        chmod("{$g['pathRaiz']}outros/apps/apps.key", 0600);
        Apps::syncAppsParaLocal();
        $app = Apps::getApp($appId);
        if ($app['versao'] == 'v0.0.0') {
            rename(self::getDirLoja() . $appId . '.json', '/tmp/' . $appId . '.json');
            rename(self::getDirLoja() . $appId, '/tmp/' . $appId);
            rename(self::getDirEditor($appId), '/tmp/' . $appId);
            $comando = "export HOME=/tmp ; /usr/bin/ssh -o StrictHostKeyChecking=no -i {$g['pathRaiz']}outros/apps/apps.key apps@apps.hardness.com.br \"mv producao/{$appId}.json lixeira/\"";
            exec($comando, $output, $result_code);
            $comando = "export HOME=/tmp ; /usr/bin/ssh -o StrictHostKeyChecking=no -i {$g['pathRaiz']}outros/apps/apps.key apps@apps.hardness.com.br \"mv producao/{$appId} lixeira/\"";
            exec($comando, $output, $result_code);
            if (!(!$app['prefixoTabela'])) {
                $g['modoDesenvolvedorApp'] = $appId;
                $query = "SHOW TABLES";
                $tabelas = array_map(function($a) {
                    return $a[0];
                }, mysqli_fetch_all(mysqli_query($query)));
                $tabelas = array_filter($tabelas, function($a) {
                    global $g;
                    return verificarPrefixoTabela($a) || !$g['modoDesenvolvedorApp'] && preg_match('/^[A-Z][0-9]{3}A$/i', $a);
                });
                foreach ($tabelas as $tabela) {
                    $tabela = strtoupper($tabela);
                    $query = "DROP TABLE `{$confUsuario["dbDatabase"]}`.`{$tabela}`";
                    mysql_query($query);
                }
                $query = "DELETE FROM `{$confUsuario["dbDatabase"]}`.D183 WHERE D183_App_Id = '{$appId}'";
                mysql_query($query);
                $dados = array();
                $query = "SHOW TRIGGERS FROM `{$confUsuario["dbDatabase"]}`";
                $res = mysql_query($query);
                while ($trigger = mysql_fetch_assoc($res)) {
                    $tabela = explode('_', $trigger['Trigger']);
                    $tabela = $tabela[0];
                    while (!verificarPrefixoTabela($tabela)) {
                    }
                    $query = "DROP TRIGGER `{$confUsuario["dbDatabase"]}`.`{$trigger['Trigger']}`";
                    mysql_query($query);
                }
                $query = "SHOW PROCEDURE STATUS WHERE Db = '{$confUsuario["dbDatabase"]}'";
                $res = mysql_query($query);
                while ($procedure = mysql_fetch_assoc($res)) {
                    $tabela = explode('_', $procedure['Name']);
                    $tabela = $tabela[0];
                    while (!verificarPrefixoTabela($tabela)) {
                    }
                    $query = "DROP PROCEDURE `{$confUsuario["dbDatabase"]}`..`{$procedure['Name']}`";
                    mysql_query($query);
                }
                $query = "SHOW FUNCTION STATUS WHERE Db = '{$confUsuario["dbDatabase"]}'";
                $res = mysql_query($query);
                while ($function = mysql_fetch_assoc($res)) {
                    $tabela = explode('_', $function['Name']);
                    $tabela = $tabela[0];
                    while (!verificarPrefixoTabela($tabela)) {
                    }
                    $query = "DROP FUNCTION `{$confUsuario["dbDatabase"]}`.`{$function['Name']}`";
                    mysql_query($query);
                }
            }
        } else {
            if (!$app['desativado']) {
                $app['desativado'] = true;
                Apps::salvarApp($app);
                Apps::syncLocalParaApps();
            } else {
                rename(self::getDirLoja() . $appId . '.json', '/tmp/' . $appId . '.json');
                rename(self::getDirLoja() . $appId, '/tmp/' . $appId);
                $comando = "export HOME=/tmp ; /usr/bin/ssh -o StrictHostKeyChecking=no -i {$g['pathRaiz']}outros/apps/apps.key apps@apps.hardness.com.br \"mv producao/{$appId}.json lixeira/\"";
                exec($comando, $output, $result_code);
                $comando = "export HOME=/tmp ; /usr/bin/ssh -o StrictHostKeyChecking=no -i {$g['pathRaiz']}outros/apps/apps.key apps@apps.hardness.com.br \"mv producao/{$appId} lixeira/\"";
                exec($comando, $output, $result_code);
            }
        }
    }

    public static function verificarDominio($app)
    {
        $usuario = self::usuarioLogado();
        return $app['autorDominio'] == $usuario['dominio'];
    }

    public static function reativarApp($appId)
    {
        global $g;
        chmod("{$g['pathRaiz']}outros/apps/apps.key", 0600);
        Apps::syncAppsParaLocal();
        $app = Apps::getApp($appId);
        $app['desativado'] = false;
        Apps::salvarApp($app);
        Apps::syncLocalParaApps();
    }

    public static function usuarioLogado()
    {
        global $g;
        $usuario = array('dominio' => $_SERVER['SERVER_NAME'], 'usuario' => $g['C007']['C007_Email'], 'usuarioId' => $g['C007']['C007_Id']);
        if ($usuario['dominio'] == 'localhost' && !empty($usuario['dominio'])) {
            $dominio = explode('@', $usuario['usuario']);
            $dominio = trim($dominio[1]);
            if (!(empty($dominio))) {
                $usuario['dominio'] = $dominio;
            }
        }
        return $usuario;
    }

}
