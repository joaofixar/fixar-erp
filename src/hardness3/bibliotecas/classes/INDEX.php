<?php
namespace hardness;
definirClasse('INDEX');
class INDEX_
{

	/**
     * Carrega variáveis globais para uso no crontab
     * 
     * @param 
     * @return 
     */    
    public function carragaVARSGlobal()
    { 
        global $g, $confUsuario;

        /*
        * TABELAS DE CONFIGURAÇÕES
        * Caso os dados não estejam no cache, os dados são recuperados direto do banco
        */

        $D021 = mysql_query("SELECT *
                               from C007
                          left join C019 on C019_C007_Id=C007_Id and C019_C004_Id='{$g['empresaAtual']}'
                          left join C021 on C021_Id=C019_C021_Id
                              where C007_Id='{$g['usuarioAtual']}'");
        $mD021 = mysql_fetch_assoc($D021);

        if($g['c020']['C020_Id']<=0){
            $g['c020'] = $g['C020'] = mysql_fetch_assoc(mysql_query("SELECT * FROM C020 WHERE C020_C004_Id = '{$g['empresaAtual']}';"));
        }

        if($g['c004']['C004_Id']<=0){
            $g['c004'] = $g['C004'] = mysql_fetch_assoc(mysql_query("SELECT * FROM C004 LEFT JOIN D020 ON D020_Id=C004_D020_Id LEFT JOIN D018 on D018_Id=C004_D018_Id WHERE C004_Id='{$g['empresaAtual']}';"));
        }

        if($g['c007']['C007_Id']<=0){
            $g['c007'] = $g['C007'] = mysql_fetch_assoc(mysql_query("SELECT * FROM C007 WHERE C007_Id='{$g['usuarioAtual']}';"));
        }

        if(!isset($g['c019'])){
            $g['c019'] = $g['C019'] = array();
            $tmp = mysql_query("SELECT * FROM C019 WHERE C019_C007_Id='{$g['usuarioAtual']}';");
            if($tmp) {
                while($row = mysql_fetch_assoc($tmp)){
                    $g['C019'][$row['C019_C004_Id']] = true;
                }
            }
        }

        if($g['c021']['C021_Id']<=0){
            $g['c021'] = $g['C021'] = mysql_fetch_assoc(mysql_query("SELECT * FROM C021 WHERE C021_Id='{$g['perfilUsuario']}';"));
        }

        if(!isset($g['c029Ids'])){
            $g['c029Ids'] = array();
            $tmp = mysql_query("SELECT C030_C029_Id FROM C030 WHERE C030_C021_Id='{$g['perfilUsuario']}' AND C030_Acesso_Liberado = 'S';");
            if ($tmp) {
                while ($row = mysql_fetch_row($tmp)) {
                    $g['c029Ids'][$row[0]] = true;
                }
            }
        }

        if(!isset($g['c031']) || !isset($g['c031']) || !isset($g['c031_global'])){
            $sqlC031 = mysql_query("SELECT * FROM C031 WHERE C031_C004_Id = '{$g['empresaAtual']}';");
            while($rowC031 = mysql_fetch_assoc($sqlC031)){
                $g['C031'][$rowC031['C031_Campo']] = $rowC031['C031_Valor'];
            }

            $sqlC031 = mysql_query("SELECT C031_C004_Id, C031_Campo, C031_Valor FROM C031 WHERE C031_Campo IN 
                                        ('mensagemLiberacaoDeCredito', 'desabilitarCotacaoEmergencia', 'desabilitarCompraEmergencia', 'desabilitarMontagemEmergencia', 'desabilitarServicoEmergencia', 'desabilitarProducaoEmergencia')
                                    ORDER BY C031_C004_Id;");
            while($rowC031 = mysql_fetch_assoc($sqlC031)){
                $g['C031_global'][$rowC031['C031_C004_Id']][$rowC031['C031_Campo']] = $rowC031['C031_Valor'];
            }
        }

        if(!empty($g['C004']['C004_Fuso_Horario'])){
            date_default_timezone_set($g['C004']['C004_Fuso_Horario']);
        }

        // C007_Id = 63 (root) sempre tem acesso a Usuários/Perfil (C029_Id = 84)
        if ($g['c007']['C007_Id'] == 63) {
            $g['isRoot'] = true;
            $g['c029Ids'][53] = true;
            $g['c029Ids'][54] = true;
            $g['c029Ids'][84] = true;
        }

        // nivelAlcada
        $g['nivelAlcada'] = 1;
        switch ($g['C007']['C007_Flag_Vendedor']) {
            case 'S':
                $g['nivelAlcada'] = 2;
                break;
            case 'D':
                $g['nivelAlcada'] = 4;
                break;
        }

        $g['nivelAlcadaUsuarioSistema'] = $_SESSION['nivelAlcadaUsuarioSistema'];

        /**
        * Para todas as requisições é enviado junto 3 parametros para o controle de janelas/divs do sistema
        */
        $g['divIdRoot'] = isset($r_divIdRoot) ? $r_divIdRoot : '';				// a qual botão a requisição está associada
        $g['divIdParent'] = isset($r_divIdParent) ? $r_divIdParent : '';		// de qual div esta requisição foi aberta
        $g['divId'] = isset($r_divId) ? $r_divId : 'erro-divIdNaoEnviado';		// o div da requisição
        
    }


}