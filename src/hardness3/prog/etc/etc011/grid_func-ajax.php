<?php
namespace hardness;
$resposta = array('code' => true, 'data' => array());

/**
 * Ações
 */
switch($g['r'][3]) {
    case 'etc011ReprocessarCache':
	    // TODO: ao filtrar, atualizar somente o dashboard atual (não todos)
        if ($r_todosUsuarios) {
            $sql = "UPDATE D182 SET D182_Grid_Salvo = NULL";
        } else {
            $sql = "UPDATE D182 SET D182_Grid_Salvo = NULL WHERE D182_C007_Id = '{$g['usuarioAtual']}'";
        }
		mysql_query($sql);
        /*

        if ($r_todosUsuarios) {
            $usuarios = array();
            $sqlUsuario = "SELECT C007_Id FROM C007 
                        LEFT JOIN D182 ON D182_C007_Id=C007_Id
                            WHERE C007_Ativo='S'
                              AND D182_Grid_Salvo IS NULL
                         GROUP BY C007_Id";
            $resUsuario = mysql_query($sqlUsuario);
            while($rowUsuario = mysql_fetch_assoc($resUsuario)) {
                $usuarios[] = $rowUsuario['C007_Id'];
            }
        } else {
            $usuarios = array($g['usuarioAtual']);
        }

        foreach ($usuarios as $usuario) {
            $sql = "SELECT D132_Id, D132_Grupo FROM D132";
            $res = mysql_query($sql);
            while ($row = mysql_fetch_assoc($res)) {
                $tab = '';
                if ($row['D132_Grupo'] == 1) {
                    $tab = 'etc011FormDashVendas';
                } else if ($row['D132_Grupo'] == 2) {
                    $tab = 'etc011FormDashOrcamento';
                } else if ($row['D132_Grupo'] == 3) {
                    $tab = 'etc011FormDashPainel';
                } else if ($row['D132_Grupo'] == 4) {
                    $tab = 'etc011FormDashFinanceiro';
                }
                $uniqid = uniqid();
                $url = "{$confUsuario['urlRaiz']}etc/etc011/grid/etc011GridPivot/?ajax=true&bi=visu&D132_Id={$row['D132_Id']}&noEmp=true&p={$g['empresaAtual']}&u={$usuario}&divId={$uniqid}&divIdRoot=B.I.&tab={$tab}";
                file_get_contents($url);
            }
        }
        */
	break;

	case 'etc011ReprocessarDataWarehouse':
		mysql_query("INSERT INTO T265 (T265_Data_Inicio_Log, T265_Referencia, T265_Descricao) VALUES (CURRENT_TIMESTAMP(), 'Manual', 'Atualizando base de dados do B.I.')");
		$idUltimoScript = $g['mysqlLastId'];
		$time = microtime(true);
		include('scripts/crontab/scripts-datawareHouse.php');
		mysql_query("UPDATE T265 SET T265_Data_Fim_Log = CURRENT_TIMESTAMP() WHERE T265_Id = '{$idUltimoScript}'");

		die();
	break;

    case 'etc011AdicionarFiltroGlobal':
        // Salva um filtro individual no global por dashboard

        $r_valor = base64_decode($r_valor);
        $r_valor = str_replace('dim01-', '', $r_valor);
        if ($r_dim == 'DIM002_Nome_Empresa' || $r_dim == 'DIM002_Nome_Fantasia') {
            $r_dim = 'DIM002_Nome_Empresa, DIM002_Nome_Fantasia';
        }
        if (preg_match('/_Dia_Da_Semana$/', $r_dim)) {
            $r_valor = formataDiaSemanaReverso($r_valor);
        }
        if (preg_match('/_Mes$/', $r_dim)) {
            $r_valor = formataMesReverso($r_valor);
        }

        $dir = $g['pathDadosUsuario'] . 'gridFiltro/';
        $fileGlobal = $dir . md5("bi-{$r_tab}");
        if (file_exists($fileGlobal)) {
            $gridFiltroGlobal = unserialize(file_get_contents($fileGlobal));
        } else {
            $gridFiltroInterno = array('campos' => array(), 'extra' => '', 'busca' => array(), 'ordem' => '');
        }

        if (isset($gridFiltroGlobal['campos'][$r_dim])) {
            if (is_array($gridFiltroGlobal['campos'][$r_dim]['busca'])) {
                if ($gridFiltroGlobal['campos'][$r_dim]['busca'][0] == $r_valor) {
                    $gridFiltroGlobal['campos'][$r_dim]['busca'] = array();
                } else {
                    $gridFiltroGlobal['campos'][$r_dim]['busca'][0] = $r_valor;
                    $gridFiltroGlobal['campos'][$r_dim]['busca'][1] = $r_valor;
                }
            } else {
                if ($gridFiltroGlobal['campos'][$r_dim]['busca'] == '=' . $r_valor) {
                    $gridFiltroGlobal['campos'][$r_dim]['busca'] = '';
                } else {
                    $gridFiltroGlobal['campos'][$r_dim]['busca'] = '=' . $r_valor;
                }
            }
        } else {
            if (preg_match('/_Data$/', $r_dim)) {
                $gridFiltroGlobal['campos'][$r_dim] = array(
                    'busca' => array($r_valor, $r_valor)
                );
            } else {
                $gridFiltroGlobal['campos'][$r_dim] = array(
                    'busca' => '=' . $r_valor
                );
            }
        }
        file_put_contents($fileGlobal, serialize($gridFiltroGlobal));
    break;
}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";

function formataDiaSemanaReverso($valor) {
    switch($valor) {
        case '1 - Segunda-feira':
            $valor = 1;
            break;
        case '2 - Terça-feira':
            $valor = 2;
            break;
        case '3 - Quarta-feira':
            $valor = 3;
            break;
        case '4 - Quinta-feira':
            $valor = 4;
            break;
        case '5 - Sexta-feira':
            $valor = 5;
            break;
        case '6 - Sábado':
            $valor = 6;
            break;
        case '7 - Domingo':
            $valor = 7;
            break;
    }
    return $valor;
}

function formataMesReverso($valor) {
    switch($valor) {
        case'01 - Janeiro':
            $valor = '1';
            break;
        case'02 - Fevereiro':
            $valor = '2';
            break;
        case'03 - Março':
            $valor = '3';
            break;
        case'04 - Abril':
            $valor = '4';
            break;
        case'05 - Maio':
            $valor = '5';
            break;
        case'06 - Junho':
            $valor = '6';
            break;
        case'07 - Julho':
            $valor = '7';
            break;
        case'08 - Agosto':
            $valor = '8';
            break;
        case'09 - Setembro':
            $valor = '9';
            break;
        case '10 - Outubro':
            $valor = '10';
            break;
        case '11 - Novembro':
            $valor = '11';
            break;
        case '12 - Dezembro':
            $valor = '12';
            break;
    }
    return $valor;
}
