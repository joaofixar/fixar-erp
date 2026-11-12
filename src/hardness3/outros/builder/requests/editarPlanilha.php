<?php
namespace hardness;
require_once('../conf.php');
if (empty($g['projeto'])) die('Erro ao carregar projeto.');

function removeBomUtf8($s) {
    if (substr($s,0,3)==chr(hexdec('EF')).chr(hexdec('BB')).chr(hexdec('BF'))) {
        return substr($s,3);
    } else {
        return $s;
    }
}

$getLinhaHandle = false;
$getLinhaDelimiter = ';';
function getLinha($arquivo) {
    global $getLinhaHandle, $getLinhaDelimiter;
    if (!$getLinhaHandle) {
        $getLinhaHandle = fopen($arquivo, "r");
        $campos = fgetcsv($getLinhaHandle, 0, $getLinhaDelimiter);
        if (count($campos) == 1 && strpos($campos[0], ',') !== false) {
            fclose($getLinhaHandle);
            $getLinhaHandle = fopen($arquivo, "r");
            $getLinhaDelimiter = ',';
            $campos = fgetcsv($getLinhaHandle, 0, $getLinhaDelimiter);
        }
        // Remove o BOM UTF8 gerado pelo Excel no inicio do arquivo e limpa acentos
        foreach ($campos as $key => $campo) {
            $campos[$key] = trim($campo);
            $campos[$key] = removeBomUtf8($campos[$key]);
            $campos[$key] = gLimpaAcentosPuro($campos[$key]);
        }
        return $campos;
    } else {
        $campos = fgetcsv($getLinhaHandle, 0, $getLinhaDelimiter);
        return $campos;
    }
}

// Definições da planilha
if ($_POST["planilha"]) {
    $planilha = $g['projeto']['planilhas'][$_POST["planilha"]];
    if (!$planilha) { die('Planilha não encontrada.'); }
    $nomeArquivo = $planilha['nomeArquivo'];
    $nomePlanilha = preg_replace("/\.csv$/i", "", $nomeArquivo);
    $arquivoPlanilha = $g['pathDadosArquivos'] . '/' . $planilha['arquivoId'];
    $campos = getLinha($arquivoPlanilha);

    // Ao abrir uma planilha para edição, tentamos associar automaticamente (pelo nome) todos os campos que não tenham definição de entidade
    foreach ($campos as $campo) {
        if (!empty($planilha['campos'][$campo]['entidade'])) { continue; } // campo já definido manualmente
        foreach ($g['projeto']['entidades'] as $entidade) {
            if (empty($entidade['tabela'])) { continue; } // somente entidade já processado
            if (preg_replace('/s$/i', '', Util::padronizarNome($entidade['nome'])) == preg_replace('/s$/i', '', Util::padronizarNome($campo))) {
                // Procurar campo chave do relacionamento
                $campoTabela = "{$entidade['tabela']}_Id";
                foreach (mysqli_fetch_all(mysqli_query("SHOW COLUMNS FROM {$entidade['tabela']}"), MYSQLI_ASSOC) as $campoRelacionamento) {
                    if (preg_replace('/s$/i', '', Util::padronizarNome($campoRelacionamento['Field'], $entidade['tabela'])) == preg_replace('/s$/i', '', Util::padronizarNome($campo))) {
                        $campoTabela = $campoRelacionamento['Field'];
                    }
                }

                $g['projeto']['planilhas'][$_POST["planilha"]]['campos'][$campo] = array(
                    'entidade' => $entidade['id'],
                    'tabela' => $entidade['tabela'],
                    'campoTabela' => $campoTabela
                );
            }
        }
    }
    Util::salvarProjeto();
    $planilha = $g['projeto']['planilhas'][$_POST["planilha"]];
}

// Identifica o tipo de um campo com base no seu texto
function identificarTipo($texto) {
    $texto = strtolower(trim($texto));
    if (empty($texto) || $texto == 'null') {
        return array('null', 0);
    }
    if (
        preg_match('/^[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{2,4}$/', $texto)
        || preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $texto)
    ) {
        return array('date', strlen($texto));
    } else if (preg_match('/^[0-9]+$/', $texto)) {
        return array('int', strlen($texto));
    } else if (preg_match('/^([0-9]|,|\.)+$/', $texto)) {
        if (strpos($texto, ',') !== false && strpos($texto, '.') !== false) {
            $texto = str_replace(".", "", $texto);
        }
        $texto = str_replace(",", ".", $texto);
        $texto = explode('.', $texto);
        return array('decimal', strlen($texto[1]));
    } else if (strlen($texto) <= 50000) {
        return array('varchar', strlen($texto));
    } else {
        return array('text');
    }
}

function getTipoMysql($tipo) {
    if ($tipo[0] == 'varchar') {
        return "varchar({$tipo[1]})";
    } else if ($tipo[0] == 'decimal') {
        return "decimal(20,{$tipo[1]})";
    }
    return $tipo[0];
}

// Processar planilha
if ($_GET["processar"]) {
    $log = ['Processamento iniciado.'];

    if ($_SESSION['modoDesenvolvedorApp'] && empty($g['app']['prefixoTabela'])) {
        echo json_encode([
            "erro" => true,
            "msg" => 'Não foi definido um prefixo de tabela para este App.',
        ]);
        die();
    }

    file_put_contents('/tmp/editarPlanilha.txt', $log[count($log)-1] . "\n", FILE_APPEND);
    global $getLinhaHandle;
    $dataRaw = file_get_contents('php://input');
    $data = json_decode($dataRaw, true);

    $arquivoPlanilha = $data['arquivoPlanilha'];

    // Identificação automática dos tipos dos campos
    $camposTipo = array();
    $getLinhaHandle = false;
    // Ignorar primeira linha - é cabeçalho
    getLinha($arquivoPlanilha);
    $totalLinhas = 0;
    while (($camposItem = getLinha($arquivoPlanilha)) !== false) {
//        print_r($camposItem);
//        print_r($camposTipo);
        $totalLinhas++;
        foreach ($camposItem as $key => $value) {
            $tipo = identificarTipo($value);
            if (!$camposTipo[$key]) {
                $camposTipo[$key] = $tipo;
            } else {
                if ($tipo[0] == 'null') {
                    continue;
                } else if ($tipo[0] != $camposTipo[$key][0]) {
                    $camposTipo[$key][0] = $tipo[0] == 'text' ? 'text' : 'varchar';
                }
                if ($tipo != 'text' && $tipo[1] > $camposTipo[$key][1]) {
                    $camposTipo[$key][1] = $tipo[1];
                }
            }
        }
//        print_r($camposTipo);
    }
    if ($totalLinhas == 0) {
        echo json_encode([
            "erro" => true,
            "msg" => 'O arquivo possui apenas uma linha (título), é necessário possuir registros para identificação automática dos campos.',
        ]);
        die();
    }
    foreach ($camposTipo as &$campoTipo) {
        if ($campoTipo[0] == 'null') {
            $campoTipo = array('varchar', 255);
        } else if ($campoTipo[0] == 'varchar') {
            $campoTipo[1] = round($campoTipo[1] * 1.3);
        }
    }

    
    // Agrupar campos por Entidade (primárias e secundárias)
    $tabelas = [
        "p" => [
            $data['entidadePrimaria'] => []
        ],
        "s" => []
    ];
    foreach ($data["planilha"] as $key => $campo) {
        $campo['tipo'] = $camposTipo[$key] ? $camposTipo[$key] : array('varchar', 255);
        $campo["importKey"] = $key;

        // Flag: altera os campos que tenham a palavra "Flag" no nome
        if (stripos($campo['campo'], 'Flag') !== false) {
            $campo['campo'] = trim(str_ireplace('Flag', '', $campo['campo']), "\n _");
            $campo['flag'] = true;
        }

        if ($campo["entidade"] == $data['entidadePrimaria']) {
            $tabelas["p"][$campo["entidade"]][] = $campo;
        } else {
            $tabelas["s"][$campo["entidade"]][] = $campo;
        }
    }

    // Inicio montagem grid principal
    $grid = [
        "nome" => $g['projeto']['entidades'][$data['entidadePrimaria']]['nome'],
        "grid" => [],
        "tabelas" => [],
        "tabelasJoin" => [],
    ];

    mysqli_query('SET AUTOCOMMIT=0');
    mysqli_query('START TRANSACTION');

    // Criar tabelas e grid principal
    $chavesSecundarias = [];
    $insertIdSecundarias = [];
    foreach (array_merge($tabelas["s"], $tabelas["p"]) as $key => $tabela) {
        // Verifica se a entidade já está ligada a uma tabela
        $entidadeTabelaExistente = $tabela[0]['entidadeTabela'];
        if (!empty($entidadeTabelaExistente)) {
            $log[] = "Consultando registros em {$g['projeto']['entidades'][$key]['nome']} ({$entidadeTabelaExistente}).";
            file_put_contents('/tmp/editarPlanilha.txt', $log[count($log)-1] . "\n", FILE_APPEND);

            $grid["tabelas"][] = $entidadeTabelaExistente;
            foreach ($tabela as $campo) {
                $nomeCampo = str_replace(" ", "_", trim($campo["campo"]));
                $grid["grid"][$campo['importKey']] = [
                    "titulo" => trim($campo["campo"]),
                    "campo" => "{$campo['campoEntidade']}",
                    "tipo" => $campo["tipo"],
                    "tabela" => $entidadeTabelaExistente,
                    "flag" => $campo["flag"]
                ];
            }

            // Vira chave secundária na tabela primária
            $chavesSecundarias[] = $entidadeTabelaExistente;

            // Localiza registros na tabela já existente
            $getLinhaHandle = false;
            // Ignorar primeira linha - é cabeçalho
            getLinha($arquivoPlanilha);

            $count = 0;
            while (($campos = getLinha($arquivoPlanilha)) !== false) {

                $sql = "SELECT {$entidadeTabelaExistente}_Id FROM {$entidadeTabelaExistente} WHERE 1=1";
                foreach ($tabela as $campo) {
                    $value = trim($campos[$campo['importKey']]);

                    // Tratamento por tipo de campo
                    switch ($campo["tipo"][0]) {
                        case 'varchar':
                        case 'text':
                            $value = $value;
                            $value = mysql_real_escape_string($value);
                            break;
                        case 'date':
                            $value = str_replace("/", "-", $value);
                            $value = strtotime($value);
                            $value = date("Y-m-d", $value);
                            break;
                        case 'datetime':
                            $value = str_replace("/", "-", $value);
                            $value = strtotime($value);
                            $value = date("Y-m-d H:i:s", $value);
                            break;
                        case 'decimal':
                            if (strpos($value, ',') !== false && strpos($value, '.') !== false) {
                                $value = str_replace(".", "", $value);
                            }
                            $value = str_replace(",", ".", $value);
                            $value = mysql_real_escape_string($value);
                            break;
                        case 'int':
                            $value = intval($value);
                            break;
                    }

                    $sql .= " AND {$campo['campoEntidade']} = '{$value}'";
                }
                $consultaRegistro = mysqli_query($sql);
                $error = mysqli_error();
                if ($error) {
                    mysqli_query('ROLLBACK');
                    // CREATE TABLE não é suportado em TRANSACTION
                    foreach ($grid["tabelas"] as $tabela) {
                        mysqli_query("DROP TABLE {$tabela};");
                    }
                    echo json_encode([
                        "erro" => true,
                        "msg" => utf8_encode($error),
                        "log" => implode("<br >\n", $log),
                    ]);
                    die();
                }
                $consultaRegistro = mysqli_fetch_row($consultaRegistro);
                if (empty($consultaRegistro)) {
                    $insertIdSecundarias[$entidadeTabelaExistente][$count] = null;
                    $sql_ = explode('1=1 AND', $sql);
                    $sql_ = trim($sql_[1]);
                    $log[] = "<span style='color: red;'>Não econtrado: {$sql_}.</span>";
                    file_put_contents('/tmp/editarPlanilha.txt', $log[count($log)-1] . "\n", FILE_APPEND);
                } else {
                    $insertIdSecundarias[$entidadeTabelaExistente][$count] = $consultaRegistro[0];
                }
                $count++;
            }
            continue;
        }

        // Verificar próximo nome de tabela disponível
        $prefixoTabela = 'P';
        if ($_SESSION['modoDesenvolvedor']) {
            $prefixoTabela = $sys['prefixoTabelas'];
        } else if ($_SESSION['modoDesenvolvedorApp']) {
            $prefixoTabela = $g['app']['prefixoTabela'];
        }
        $query = "SHOW TABLES LIKE '{$prefixoTabela}%';";
        $tabelasPx = array_map(function ($a) { return $a[0]; }, mysqli_fetch_all(mysqli_query($query)));
        if (empty($tabelasPx)) {
            $nomeTabela = "{$prefixoTabela}001";
        } else {
            sort($tabelasPx);
            $nomeTabela = end($tabelasPx);
            $nomeTabela = $prefixoTabela . str_pad(intval(preg_replace('/[a-z]/i', '', $nomeTabela))+1, 3, "0", STR_PAD_LEFT);
        }
        $grid["tabelas"][] = $nomeTabela;

        $campos = [
            "\t{$nomeTabela}_Id int unsigned auto_increment primary key"
        ];
        $camposInsert = [];
        if ($key != $data['entidadePrimaria']) {
            $insertIdSecundarias[$nomeTabela] = [];
            $chavesSecundarias[] = $nomeTabela;
        } else {
            foreach ($chavesSecundarias as $fk) {
                $comment = '';
                foreach ($g['projeto']['entidades'] as $entidade_) {
                    if ($entidade_['tabela'] == $fk) {
                        $comment = mysql_real_escape_string($entidade_['nome']);
                    }
                }
                $campos[] = "\t{$nomeTabela}_{$fk}_Id int unsigned COMMENT '$comment'";
            }
        }
        foreach ($tabela as $campo) {
            $nomeCampo = str_replace(" ", "_", trim($campo["campo"]));
            $camposInsert[] = "{$nomeTabela}_{$nomeCampo}";
            $comment = mysql_real_escape_string(trim($campo["campo"]));
            $campos[] = "\t{$nomeTabela}_{$nomeCampo} " . getTipoMysql($campo["tipo"]) . " not null COMMENT '{$comment}'";
            $grid["grid"][$campo['importKey']] = [
                "titulo" => trim($campo["campo"]),
                "campo" => "{$nomeTabela}_{$nomeCampo}",
                "tipo" => $campo["tipo"],
                "tabela" => $nomeTabela,
                "flag" => $campo["flag"]
            ];
        }
        $campos_ = implode(",\n", $campos);

        // $data
        $comment = mysql_real_escape_string(trim($g['projeto']['entidades'][$key]['nome']));
        $log[] = "Criando Tabela {$nomeTabela} para Entidade {$g['projeto']['entidades'][$key]['nome']}.";
        file_put_contents('/tmp/editarPlanilha.txt', $log[count($log)-1] . "\n", FILE_APPEND);
        $sql = <<<EOT
CREATE TABLE {$nomeTabela}
(
$campos_
) DEFAULT CHARSET=latin1 COMMENT='{$comment}';
EOT;
        //echo $sql . "\n";
        mysqli_query($sql);
        // Auditoria
        if ($g['projeto']['entidades'][$key]['auditoria'] == 'N') {
            $tabelasAuditoria = Personalizacoes::tabelasAuditoria();
            $tabelasAuditoria[strtoupper($nomeTabela)] = 'N';
            Personalizacoes::tabelasAuditoria($tabelasAuditoria);
            $g['tabelasAuditoria'] = $tabelasAuditoria;
        }
        //
        $error = mysqli_error();
        if ($error) {
            mysqli_query('ROLLBACK');
            // CREATE TABLE não é suportado em TRANSACTION
            foreach ($grid["tabelas"] as $tabela) {
                mysqli_query("DROP TABLE {$tabela};");
            }
            echo json_encode([
                "erro" => true,
                "msg" => utf8_encode($error),
                "log" => implode("<br >\n", $log)
            ]);
            die();
        }

        // Associar tabela a entidade
        $g['projeto']['entidades'][$key]['tabela'] = $nomeTabela;

        // Insere registros
        $getLinhaHandle = false;
        // Ignorar primeira linha - é cabeçalho
        getLinha($arquivoPlanilha);

        $count = 0;
        $logInsert = [];
        $log[] = "Inserindo registros em {$g['projeto']['entidades'][$key]['nome']} ({$g['projeto']['entidades'][$key]['tabela']}).";
        file_put_contents('/tmp/editarPlanilha.txt', $log[count($log)-1] . "\n", FILE_APPEND);
        while (($campos = getLinha($arquivoPlanilha)) !== false) {
            $camposInsertPrimaria = $camposInsert;
            $values = [];
            foreach ($tabela as $campo) {
                $value = trim($campos[$campo['importKey']]);

                // Tratamento por tipo de campo
                switch ($campo["tipo"][0]) {
                    case 'text':
                    case 'varchar':
                        $value = $value;
                        $value = mysql_real_escape_string($value);
                        break;
                    case 'date':
                        if (empty($value) || $value == 'null') {
                            $value = '0000-00-00';
                        } else {
                            $value = str_replace("/", "-", $value);
                            $value = strtotime($value);
                            $value = date("Y-m-d", $value);
                        }
                        break;
                    case 'datetime':
                        $value = str_replace("/", "-", $value);
                        $value = strtotime($value);
                        $value = date("Y-m-d H:i:s", $value);
                        break;
                    case 'decimal':
                        if (strpos($value, ',') !== false && strpos($value, '.') !== false) {
                            $value = str_replace(".", "", $value);
                        }
                        $value = str_replace(",", ".", $value);
                        $value = mysql_real_escape_string($value);
                        break;
                    case 'int':
                        $value = intval($value);
                        break;
                }

                $values[] = "'" . $value . "'";
            }
            $skipInsert = false;
            // Incluir FKs para a entidade primária
            if ($key == $data['entidadePrimaria']) {
                foreach ($chavesSecundarias as $fk) {
                    $camposInsertPrimaria[] = "{$nomeTabela}_{$fk}_Id";
                    if ($insertIdSecundarias[$fk][$count]) {
                        $values[] = "'" . $insertIdSecundarias[$fk][$count] . "'";
                    } else {
                        $values[] = "null";
                    }
                }
            } else {
                // Verifica se já não foi inserido um registro igual
                $valuesImploded = implode("-", $values);
                if (isset($logInsert[$valuesImploded])) {
                    $skipInsert = true;
                }
            }
            $insertId = 0;
            if (!$skipInsert) {
                $sql = "INSERT INTO {$nomeTabela} (" . join(',', $camposInsertPrimaria) . ") VALUES (" . join(',', $values) . ")";
                //echo $sql . "\n";
                mysqli_query($sql);
                $error = mysqli_error();
                if ($error) {
                    mysqli_query('ROLLBACK');
                    // CREATE TABLE não é suportado em TRANSACTION
                    foreach ($grid["tabelas"] as $tabela) {
                        mysqli_query("DROP TABLE {$tabela};");
                    }
                    echo json_encode([
                        "erro" => true,
                        "msg" => utf8_encode($error),
                        "log" => implode("<br >\n", $log),
                    ]);
                    die();
                }
                $insertId = mysqli_insert_id();
                // Auditoria
                $arrayAlterados = mysqli_real_escape_string(serialize([]));
                $sql = "INSERT INTO T162 (T162_C007_Id, T162_Acao, T162_Query, T162_DataHora, T162_Tabela, T162_TempoExecucao, T162_Registro_Id, T162_Registro, T162_IP) VALUES ('{$_SESSION['Usuario_Atual']}', 'I', 'hardness3/outros/builder/requests/editarPlanilha.php', '" . date('Y-m-d H:i:s') . "', '{$nomeTabela}', '0', '{$insertId}', COMPRESS('{$arrayAlterados}'), '{$_SERVER['REMOTE_ADDR']}');";
                if ($g['projeto']['entidades'][$key]['auditoria'] != 'N') {
                    mysqli_query($sql);
                }
            }
            // Guarda os ids inseridos nas tabelas secundárias
            if ($key != $data['entidadePrimaria']) {
                if ($skipInsert) {
                    $insertIdSecundarias[$nomeTabela][$count] = $logInsert[$valuesImploded];
                } else {
                    $mysqlInsertId = $insertId;
                    $insertIdSecundarias[$nomeTabela][$count] = $mysqlInsertId;
                    $logInsert[$valuesImploded] = $mysqlInsertId;
                }
            }
            $count++;
        }
        $log[] = "Registros processados: {$count}.";
        file_put_contents('/tmp/editarPlanilha.txt', $log[count($log)-1] . "\n", FILE_APPEND);
    }

    // Manter o grid principal na mesma ordem que veio no arquivo CSV
    ksort($grid['grid']);

    mysqli_query('COMMIT');

    // Salva o grid principal
    $log[] = "Criando Grid: {$grid['nome']}.";
    file_put_contents('/tmp/editarPlanilha.txt', $log[count($log)-1] . "\n", FILE_APPEND);
    // Primeiro é criado as tabelas secundárias, depois a primária - por isso precisamos inverter
    $grid["tabelas"] = array_reverse($grid["tabelas"]);
    // Os join são somente de 1 nível (todas as secundárias se ligam pela primária)
    $tabelaPrimaria = '';
    foreach ($grid["tabelas"] as $tabela) {
        if (!$tabelaPrimaria) {
            $tabelaPrimaria = $tabela;
            $grid["tabelasJoin"][] = null;
        } else {
            $grid["tabelasJoin"][] = $tabelaPrimaria;
        }
    }

    $id = uniqid();
    $gridIdPrincipal = $id;
    $grid["id"] = $id;
    $g['projeto']['grids'][$id] = $grid;

    // Criação grids secundários
    $gridsCriados = [ $id ];
    foreach ($tabelas["s"] as $tabela) {
        $entidadeTabelaExistente = $tabela[0]['entidadeTabela'];
        if (!empty($entidadeTabelaExistente)) {
            continue;
        }
        $entidade = $tabela[0]['entidade'];
        $entidadeTabela = $g['projeto']['entidades'][$entidade]['tabela'];
        // Inicio montagem grid
        $grid = [
            "nome" => $g['projeto']['entidades'][$entidade]['nome'],
            "grid" => [],
            "tabelas" => [$entidadeTabela],
            "tabelasJoin" => [null],
        ];
        $log[] = "Criando Grid: {$grid['nome']}.";
        file_put_contents('/tmp/editarPlanilha.txt', $log[count($log)-1] . "\n", FILE_APPEND);
        foreach ($tabela as $campo) {
            $nomeCampo = str_replace(" ", "_", trim($campo["campo"]));
            $campos[] = "\t{$entidadeTabela}_{$nomeCampo} " . getTipoMysql($campo["tipo"]) . " not null";
            $grid["grid"][] = [
                "titulo" => trim($campo["campo"]),
                "campo" => "{$entidadeTabela}_{$nomeCampo}",
                "tipo" => $campo["tipo"],
                "tabela" => $entidadeTabela,
                "flag" => $campo['flag']
            ];
        }
        $id = uniqid();
        $grid["id"] = $id;
        $g['projeto']['grids'][$id] = $grid;
        $gridsCriados[] = $id;
    }

    // Montagem dos formulário com base nos grids
    foreach ($g['projeto']['grids'] as $keyGrid => $grid) {
        // Somente para grids criados neste processo
        if (!in_array($keyGrid, $gridsCriados)) continue;
        // Utilizo o primeiro campo da tabela secundária
        $cacheCampoTabelaSecundaria = [];
        // Novo form
        $form = [
            "nome" => $grid["nome"],
            "tabela" => $grid["tabelas"][0],
            "form" => []
        ];
        $log[] = "Criando Form: {$form['nome']}.";
        file_put_contents('/tmp/editarPlanilha.txt', $log[count($log)-1] . "\n", FILE_APPEND);
        foreach ($grid['grid'] as $campo) {
            $campoForm = [
                "titulo" => $campo["titulo"],
                "campo" => $campo["campo"],
                "tabela" => $campo["tabela"],
                "tipo" => $campo["tipo"]
            ];
            if ($form["tabela"] == $campo["tabela"]) {
                // É campo da tabela principal
                switch ($campo["tipo"][0]) {
                    case 'text':
                    case 'varchar':
                    case 'date':
                        break;
                    case 'datetime':
                        $campoForm["tipo"] = "Data";
                        break;
                    case 'decimal':
                        $campoForm["tipo"] = "Moeda";
                        $campoForm["opcoes"] = [ [ "nome" => "casasDecimais", "valor" => $campo["tipo"][1] ] ];
                        break;
                    case 'int':
                        $campoForm["tipo"] = "Moeda";
                        $campoForm["opcoes"] = [ [ "nome" => "casasDecimais", "valor" => 0 ] ];
                        break;
                }
                if ($campo['flag']) {
                    $campoForm['tipo'] = 'Flag';
                }
                $form["form"][] = $campoForm;
            } else {
                // É tabela secundária
                foreach ($g['projeto']["entidades"] as $entidade) {
                    if ($entidade["tabela"] == $campoForm["tabela"]) {
                        $campoForm["titulo"] = $entidade["nome"];
                    }
                }
                $campoForm["tipo"] = "Select";
                // Verifica se já existe um campo adicionado para essa tabela
                $encontrou = false;
                foreach ($form["form"] as &$campoForm_) {
                    if ($campoForm_["tabela"] == $campo["tabela"]) {
                        $encontrou = true;
                        $campoForm_["campo"][] = $campo["campo"];
                    }
                }
                if (!$encontrou) {
                    $campoForm["campo"] = [ $campo["campo"] ];
                    $form["form"][] = $campoForm;
                }
            }
        }
        $id = uniqid();
        $form["id"] = $id;
        $g['projeto']['forms'][$id] = $form;

        // Informa o grid sobre esse form
        $g['projeto']['grids'][$keyGrid]["form"] = $id;
    }

//    print_r($g['projeto']['forms']);
//    // CREATE TABLE não é suportado em TRANSACTION
//    foreach ($g['projeto']['grids']["tabelas"] as $tabela) {
//        mysqli_query("DROP TABLE {$tabela};");
//    }
//    die();

    // Remover planilha
    unset($g['projeto']['planilhas'][$data['idPlanilha']]);

    Util::salvarProjeto();

    // Retorna grid principal
    echo json_encode([
        "erro" => false,
        "gridId" => $gridIdPrincipal,
        "log" => implode("<br >\n", $log),
    ]);
    die();
}

if ($_GET["salvarCampo"]) {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!$g['projeto']['planilhas'][$data['planilha']]) {
        echo json_encode([
            "erro" => "Planilha não encontrada."
        ]);
        die();
    }

//    if ($data['entidade']) {
    $campo = &$g['projeto']['planilhas'][$data['planilha']]['campos'][$data['campo']];
    $campo['entidade'] = $data['entidade'];
    $campo['tabela'] = $data['tabela'];
    $campo['campoTabela'] = $data['campoTabela'];
//    } else {
//        print_r($g['projeto']['planilhas'][$data['planilha']]);
//        unset($g['projeto']['planilhas'][$data['planilha']]['campos'][$data['campo']]);
//    }

    Util::salvarProjeto();

    echo json_encode([
        "erro" => false
    ]);
    die();
}

if ($_GET["salvarCampoTipo"]) {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!$g['projeto']['planilhas'][$data['planilha']]) {
        echo json_encode([
            "erro" => "Planilha não encontrada."
        ]);
        die();
    }

    $campo = &$g['projeto']['planilhas'][$data['planilha']]['campos'][$data['campo']];
    $campo['tipo'] = $data['tipo'];

    Util::salvarProjeto();

    echo json_encode([
        "erro" => false
    ]);
    die();
}
?>

<h2><?=$nomeArquivo?></h2>

<h3>Colunas e Dados</h3>

<table class="ui compact celled definition table editarPlanilha">
    <thead class="full-width">
    <tr class="center aligned">
        <th>Campo</th>
<!--        <th>Tipo</th>-->
        <th>Entidade</th>
        <th>Campo Entidade</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($campos as $campo) {
        $tipo = isset($planilha['campos'][$campo]['tipo']) ? $planilha['campos'][$campo]['tipo'] : 'Texto';
        ?>
        <tr data-campo="<?=$campo?>">
            <td data-label="Campo">
                <?= $campo ?>
            </td>
<!--            <td data-label="Tipo" style="padding: 0;">-->
<!--                <div class="ui search selection dropdown dropdownTipo" style="width: 100%; border: 0;">-->
<!--                    <input type="hidden" name="tipo" value="--><?//=$tipo?><!--">-->
<!--                    <i class="dropdown icon"></i>-->
<!--                    <div class="default text">Tipo</div>-->
<!--                    <div class="menu">-->
<!--                        <div class="item" data-value="Texto" selected>Texto</div>-->
<!--                        <div class="item" data-value="Inteiro">Inteiro</div>-->
<!--                        <div class="item" data-value="Decimal">Decimal</div>-->
<!--                        <div class="item" data-value="Data">Data</div>-->
<!--                        <div class="item" data-value="DataHora">DataHora</div>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </td>-->
            <td data-label="Tabela" style="padding: 0;">
                <?php
                    $entidade = isset($planilha['campos'][$campo]['entidade']) ? $planilha['campos'][$campo]['entidade'] : $planilha['entidade'];
                    echo Util::dropdownEntidade('dropdownPlanilhaTabela', $entidade, 'width: 100%; border: 0;', $planilha['entidade']);
                ?>
            </td>
            <td data-label="CampoTabela" class="campoEntidade" style="padding: 0;">
                <?php
                if ($planilha['campos'][$campo]['tabela']) {
                    $_POST['id'] = $id = uniqid();
                    $_POST['tabela'] = $planilha['campos'][$campo]['tabela'];
                    $_POST['campoTabela'] = $planilha['campos'][$campo]['campoTabela'];
                    include("outros/builder/requests/campoEntidade.php");
                    ?>
                    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                        $('#<?=$id?>').dropdown({
                            forceSelection: false,
                            onChange: function (value, text, $choice_) {
                                const entidade = '<?=$planilha['campos'][$campo]['entidade']?>';
                                const campo = '<?=$campo?>';
                                const tabela = '<?=$planilha['campos'][$campo]['tabela']?>';
                                // Salva a seleção
                                const campoTabela = $choice_ ? $choice_.data('value') : '';
                                $.ajax({
                                    url: '/hardness3/outros/builder/requests/editarPlanilha.php?salvarCampo=true',
                                    dataType: 'json',
                                    type: 'POST',
                                    contentType : 'application/json',
                                    data: JSON.stringify({
                                        entidade: entidade,
                                        campo: campo,
                                        tabela: tabela,
                                        campoTabela: campoTabela,
                                        planilha: '<?=$planilha['id']?>'
                                    }),
                                    success: function (data) {
                                        if (data["erro"]) {
                                            alert(data["msg"]);
                                        }
                                    }
                                });
                            }
                        });
                    } </script></div>
                    <?php
                }
                ?>
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>

<div style="text-align: center; padding-top: 32px;">
    <a href="#">
        <div class="ui secondary icon button" onclick="finalizar();">
            Processar
        </div>
    </a>
</div>

<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
    function finalizar() {
        let erro = false;
        const planilha = [];

        //const entidadePrimaria = $('.dropdownEntidadePrimaria').find('.menu .selected').attr("data-value");
        const entidadePrimaria = '<?=$planilha['entidade']?>';
        if (!entidadePrimaria) erro = true;

        $('.editarPlanilha tbody tr').each(function () {
            let entidade = $(this).find('td[data-label="Tabela"]').find('.menu .selected').attr("data-value");
            if (!entidade) erro = true;
            let entidadeTabela = $(this).find('td[data-label="Tabela"]').find('.menu .selected').data('tabela');
            let campoEntidade = '';
            if (entidadeTabela) {
                campoEntidade = $(this).find('td[data-label="CampoTabela"]').find('.menu .selected').data('value');
                if (!campoEntidade) erro = true;
            }
            planilha.push({
                campo: $(this).find('td[data-label="Campo"]').text(),
                tipo: $(this).find('td[data-label="Tipo"]').find('.menu .selected').attr("data-value"),
                entidade: entidade,
                entidadeTabela: entidadeTabela,
                campoEntidade: campoEntidade,
            });
        });

        if (!erro) {
            $('#loader').css('display', 'block');
            $.ajax({
                url: '/hardness3/outros/builder/requests/editarPlanilha.php?processar=true',
                dataType: 'json',
                type: 'POST',
                contentType : 'application/json',
                data: JSON.stringify({
                    nome: '<?= $nomePlanilha ?>',
                    entidadePrimaria: entidadePrimaria,
                    planilha: planilha,
                    arquivoPlanilha: '<?= $arquivoPlanilha ?>',
                    idPlanilha: '<?= $planilha["id"] ?>'
                }),
                success: function (data) {
                    if (data["log"]) {
                        setTimeout(function() {
                            $('#modalProcessamento .content').html(data["log"]);
                            $('#modalProcessamento').modal('show');
                        }, 100);
                    }
                    if (data["erro"]) {
                        alert(data["msg"]);
                    } else {
                        $(window).scrollTop(0);
                        $('#editarPlanilha').html('');
                        carregarListarPlanilhas();
                        carregarNomesGrid();
                        carregarNomesTabelas();
                        carregarNomesEntidades();
                        carregarNomesForms();
                    }
                },
                complete: function() {
                    $('#loader').css('display', 'none');
                },
            });
        } else {
            alert("É necessário preencher a entidade primária e selecionar uma entidade para todos os campos.");
        }
        return false;
    }

    $(".ui.dropdown.dropdownTipo").dropdown({
        onChange: function (value, text, $choice) {
            const campo = $(this).closest('tr').data('campo');
            const tipo = $choice ? $choice.data('value') : '';
            $.ajax({
                url: '/hardness3/outros/builder/requests/editarPlanilha.php?salvarCampoTipo=true',
                dataType: 'json',
                type: 'POST',
                contentType : 'application/json',
                data: JSON.stringify({
                    campo: campo,
                    tipo: tipo,
                    planilha: '<?=$planilha['id']?>'
                }),
                success: function (data) {
                    if (data["erro"]) {
                        alert(data["msg"]);
                    }
                }
            });
        }
    });
    $(".ui.dropdown.dropdownPlanilhaTabela").dropdown({
        forceSelection: false,
        onChange: function (value, text, $choice) {
            const $campoEntidade = $(this).closest('tr').find('td.campoEntidade');
            const entidade = $choice ? $choice.data('value') : '';
            const campo = $(this).closest('tr').data('campo');
            const tabela = $choice ? $choice.data('tabela') : '';
            if (tabela) {
                $.ajax({
                    url: '/hardness3/outros/builder/requests/campoEntidade.php',
                    type: 'POST',
                    data: { tabela: tabela },
                    success: function (data) {
                        $campoEntidade.html(data);
                        $campoEntidade.find('.dropdownEntidadeCampo').dropdown({
                            forceSelection: false,
                            onChange: function (value, text, $choice_) {
                                // Salva a seleção
                                const campoTabela = $choice_ ? $choice_.data('value') : '';
                                $.ajax({
                                    url: '/hardness3/outros/builder/requests/editarPlanilha.php?salvarCampo=true',
                                    dataType: 'json',
                                    type: 'POST',
                                    contentType : 'application/json',
                                    data: JSON.stringify({
                                        entidade: entidade,
                                        campo: campo,
                                        tabela: tabela,
                                        campoTabela: campoTabela,
                                        planilha: '<?=$planilha['id']?>'
                                    }),
                                    success: function (data) {
                                        if (data["erro"]) {
                                            alert(data["msg"]);
                                        }
                                    }
                                });
                            }
                        });
                    }
                });
            } else {
                $campoEntidade.html('');
            }
            // Salva a seleção
            $.ajax({
                url: '/hardness3/outros/builder/requests/editarPlanilha.php?salvarCampo=true',
                dataType: 'json',
                type: 'POST',
                contentType : 'application/json',
                data: JSON.stringify({
                    entidade: entidade,
                    campo: campo,
                    tabela: tabela,
                    campoTabela: '',
                    planilha: '<?=$planilha['id']?>'
                }),
                success: function (data) {
                    if (data["erro"]) {
                        alert(data["msg"]);
                    }
                }
            });
            // http://localhost/hardness3/outros/builder/requests/campoEntidade.php?tabela=P014
        }
    });
    // // Ao selecionar a entidade principal, define ela como o padrão para todos os campos não preenchidos
    // $(".ui.dropdown.dropdownEntidadePrimaria").dropdown({
    //     onChange: (value) => {
    //         $('.dropdownPlanilhaTabela')
    //             .find('input[name="tabela"][value=""]')
    //             .closest('.dropdown')
    //             .dropdown('set selected', value);
    //     }
    // });
} </script></div>
