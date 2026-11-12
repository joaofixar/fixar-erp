<?php
namespace hardness;

use Error;
use Exception;

definirClasse('SMTPRINT');
class SMTPRINT_{

    /**
     * Realiza a impressão em PDF do relatório do sistema
     *
     * @relatorio  -> Obrigatório     : String
     * @parametros -> Não Obrigatório : String
     * 
     */
    public function gerarPDF($relatorio, $parametros) {
        global $g, $confUsuario;

        try {
            $param        = empty($parametros) ? "" : '&' . $parametros;
            $urlRelatorio = $relatorio . '?ajax=true' . $param;
            $urlRelatorio = str_replace(' ', '+', $urlRelatorio);

            $diretorio = $this->verificarDiretorio();
            $nome      = md5($urlRelatorio . time()) . '.pdf';
            $arquivo   = $g['pathRaiz'] . '/' .  $diretorio . $nome;

            $comando = "xvfb-run --server-num " . mt_rand(0, 98) . " -s \"-screen 0 640x480x16\" wkhtmltopdf-novo --quiet --margin-left 1 --margin-right 1 --margin-top 1 --margin-bottom 1 --page-width 210 --page-height 297 --dpi 300 --disable-smart-shrinking \"{$g['urlRaiz']}{$urlRelatorio}&noEmp=true&p={$g['empresaAtual']}&u={$g['usuarioAtual']}&db={$confUsuario['dbDatabase']}\" \"{$arquivo}\"";
            shell_exec($comando);
            log($comando);

            return $arquivo;
        } catch(Exception $expt) {
            log('SMTPRINT->gerarPDF - Erro :' . $expt);
            return "";
        }
    }

    private function verificarDiretorio() {
        global $g;

        $diretorio = $g['pathDados'] . 'pdf/';

        is_dir($diretorio) or mkdir($diretorio, 0777, true);

        return $diretorio;
    }

}