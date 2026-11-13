<?php
/**
* triggers
* Execução de código PHP antes/após INSERT/UPDATE/DELETE
* A chave primária sempre estará disponível (exceto em beforeInsert) pela variavél: $chaveValor 
* Coloque um '\' na função mysql_query: mysqli_query() para utilizar a original do mysql
*/
$conf['triggers']['T014']['insertBefore'] = <<<'EOT'
	// Código PHP
EOT;

$conf['triggers']['T014']['insertAfter'] = <<<'EOT'
    // Recalcula o custo do item e de todos os duplicados relativo a ele
    $sql = "SELECT T014_Id_Duplicado FROM T014 WHERE T014_Id='{$chaveValor}'";
    $res = mysqli_query($sql);
    $row = mysql_fetch_assoc($res);    

    $sql = "SELECT T014_Id 
              FROM T014 
             WHERE T014_Id='{$chaveValor}' 
                OR T014_Id_Duplicado='{$chaveValor}' ";

    if($row['T014_Id_Duplicado'] > 0){
        $sql .= " OR T014_Id='{$row['T014_Id_Duplicado']}' 
                  OR T014_Id_Duplicado='{$row['T014_Id_Duplicado']}'";
    }  
   
    $res = mysqli_query($sql);
    while ($row = mysql_fetch_assoc($res)) {
        mysqli_query("call T014_Gravar_Totalizacao_Custo('{$row['T014_Id']}')");
    }
    // fim custo
    mysqli_query("call T014_Gravar_Totalizacao('$chaveValor')");
    $T014=mysqli_query("select T014_T013_Id, T014_D006_Id from T014 where T014_Id='$chaveValor'");
    $mT014=mysql_fetch_array($T014);
    mysqli_query("UPDATE T013 SET T013_D006_Id='{$mT014['T014_D006_Id']}' WHERE T013_Id = '{$mT014['T014_T013_Id']}' AND (T013_D006_Id IS NULL OR T013_D006_Id <= 0)");
    mysqli_query("call T013_Gravar_Totalizacao('$mT014[T014_T013_Id]')");

    //require_once('bibliotecas/classes/NOTIFICACOES.php');
    //$NOTIFICACOES = new NOTIFICACOES();
    //$NOTIFICACOES->atualizarNotificacoesT014();
EOT;

$conf['triggers']['T014']['updateBefore'] = <<<'EOT'
EOT;

$conf['triggers']['T014']['updateAfter'] = <<<'EOT'
    // Recalcula o custo do item e de todos os duplicados relativo a ele
    $sql = "SELECT T014_Id_Duplicado FROM T014 WHERE T014_Id='{$chaveValor}'";
    $res = mysqli_query($sql);
    $row = mysql_fetch_assoc($res);
    
    $sql = "SELECT T014_Id 
              FROM T014 
             WHERE T014_Id='{$chaveValor}' 
                OR T014_Id_Duplicado='{$chaveValor}' ";

    if($row['T014_Id_Duplicado'] > 0){
        $sql .= " OR T014_Id='{$row['T014_Id_Duplicado']}' 
                  OR T014_Id_Duplicado='{$row['T014_Id_Duplicado']}'";
    }
                  
    $res = mysqli_query($sql);
    while ($row = mysql_fetch_assoc($res)) {
        mysqli_query("call T014_Gravar_Totalizacao_Custo('{$row['T014_Id']}')");
    }
    // fim custo
    mysqli_query("call T014_Gravar_Totalizacao('$chaveValor')");
    $T014=mysqli_query("select T014_T013_Id, T014_D006_Id from T014 where T014_Id='$chaveValor'");
    $mT014=mysql_fetch_array($T014);
    mysqli_query("UPDATE T013 SET T013_D006_Id='{$mT014['T014_D006_Id']}' WHERE T013_Id = '{$mT014['T014_T013_Id']}' AND (T013_D006_Id IS NULL OR T013_D006_Id <= 0)");
    mysqli_query("call T013_Gravar_Totalizacao('$mT014[T014_T013_Id]')");

    //require_once('bibliotecas/classes/NOTIFICACOES.php');
    //$NOTIFICACOES = new NOTIFICACOES();
    //$NOTIFICACOES->atualizarNotificacoesT014();
EOT;

$conf['triggers']['T014']['deleteBefore'] = <<<'EOT'
EOT;

$conf['triggers']['T014']['deleteAfter'] = <<<'EOT'
EOT;


/**
* restricaoTravar
* Código PHP para validar se uma tabela pode: ser editada, incluida ou removida
* Para ver variáveis e funções disponíveis dentro do código, veja:
*    http://hardness/wiki/index.php?title=Restri%C3%A7%C3%B5es_de_Campos
*/
$conf['restricaoTravar']['T014'] = <<<'EOT'
    if(!isset($g['c029Ids'][189])){
        $retorno = "Não possui permissão.";
        return false;
    }
    $retorno = true;
    if($operacao == 'alterar' || $operacao == 'excluir') {
        $sql = mysql_query("SELECT T013_Flag_Estoque_Finalizado FROM T014 LEFT JOIN T013 ON T013_Id = T014_T013_Id WHERE T014_Id = '{$valorPk}' AND T013_Flag_Travar_Nota = 'S'");
        if (mysql_num_rows($sql) > 0) {
            $retorno = 'Esta nota já foi finalizada';
        }
        $mT014=mysql_fetch_array($sql);
        if ($mT014['T013_Flag_Estoque_Finalizado']!='S'){
            if ($campo == 'T225_Flag_Autorizado') { $retorno = true; }
            if ($campo == 'T014_Flag_Conferencia_Cega') { $retorno = true; }
            if ($campo == 'T014_D006_Id') { $retorno = true; }
            if ($campo == 'T014_D004_Id') { $retorno = true; }
        }
        if ($mT014['T013_Flag_Financeiro_Finalizado']!='S'){
            if ($campo == 'T014_D006_Id') { $retorno = true; }
        }
    } else {
        $retorno = true;
    }
    if ($campo == 'T014_Quantidade_Devolucao') {
        $retorno = true;    
    }
EOT;
