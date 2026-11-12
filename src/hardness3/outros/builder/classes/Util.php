<?php
namespace hardness;

class Util {
    // $permitirExistente = true, false ou id da entidade
    public static function dropdownEntidade($nomeClass, $nomeEntidadeOuTabela = false, $style = '', $permitirExistente = false) {
        global $g;
        $retorno = <<<EOT
<div class="ui search clearable selection dropdown {$nomeClass}" style="{$style}">
    <i class="dropdown icon"></i>
    <div class="default text">Selecione...</div>
    <div class="menu">

EOT;
        $entidadeValor = '';
        $entidades = $g['projeto']['entidades'];
        usort($entidades, function ($e1, $e2) {
            if (!$e1['tabela'] && $e2['tabela']) {
                return -1;
            } else if ($e1['tabela'] && !$e2['tabela']) {
                return 1;
            } else {
                return strcmp($e1['nome'], $e2['nome']);
            }
        });
        foreach ($entidades as $entidade) {
            $disabled = '';
            $nome = $entidade['nome'];
            if ($nomeEntidadeOuTabela && ($entidade['id'] == $nomeEntidadeOuTabela || $entidade['tabela'] == $nomeEntidadeOuTabela)) {
                $entidadeValor = $entidade['id'];
            } else {
                if ($entidade['tabela']) {
                    $nome .= " ({$entidade['tabela']})";
                    if (!$permitirExistente) {
                        $disabled = 'disabled';
                    }
                } else {
                    // Verifica se é entidade primária de alguma planilha
                    foreach ($g['projeto']['planilhas'] as $planilha) {
                        if ($planilha['entidade'] && $planilha['entidade'] == $entidade['id']) {
                            if ($permitirExistente != $entidade['id']) {
                                $nome .= " ({$planilha['nomeArquivo']})";
                                $disabled = 'disabled';
                                break 1;
                            }
                        }
                        // Verifica se é entidade secundária de alguma planilha
                        foreach ($planilha['campos'] as $campo) {
                            if ($entidade['id'] == $campo['entidade']) {
                                $nome .= " ({$planilha['nomeArquivo']})";
                                $disabled = 'disabled';
                                break 2;
                            }
                        }
                    }
                }
            }

            $retorno .= "<div class=\"item {$disabled}\" data-value=\"{$entidade['id']}\" data-tabela=\"{$entidade['tabela']}\">{$nome}</div>\n";
        }
        $retorno .= <<<EOT
    </div>
    <input type="hidden" name="tabela" value="{$entidadeValor}">
</div>

EOT;
        return $retorno;
    }

    public static function salvarProjeto() {
        global $g;
        file_put_contents($g['projetoPath'], json_encode($g['projeto']));
    }

    public static function padronizarNome($nome, $tabela = false) {
        if ($tabela) {
            $nome = str_replace($tabela, '', $nome);
        }
        $nome = preg_replace('/[ _]+/', ' ', $nome);
        $nome = trim($nome);
        $nome = ucwords($nome);
        return $nome;
    }
}
