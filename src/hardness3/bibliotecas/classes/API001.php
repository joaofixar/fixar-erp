<?php
namespace hardness;
require_once("old/lib/funcoes.php");
definirClasse('API001');
class API001_
{

	/**
     * Atualiza Token
     * 
     * @param 
     * @return 
     */    
    public function atualizaToken($token)
    { 
        global $r_C033_Id;

        mysql_query("update C033 set C033_Token='$token' where C033_Id='{$r_C033_Id}'");

        return true;

    }

    /**
     * Retorno 404
     * 
     * @param 
     * @return 
     */    
    public function retorno404(){
		header('HTTP/1.0 404 Not Found');
		$retorno['code'] = 404;
		$retorno['message'] = "Caminho não encontrado!";
		echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
	}

	/**
     * Executa processo
     * 
     * @param 
     * @return 
     */    
    public function executaProcesso($processo, $parametros = array())
    { 
        global $r_C033_Id;

		// Classes
		require_once('bibliotecas/classes/API001.php');
		$API001 = new API001();

		$C033 = mysql_query("SELECT C033_Login,
   							 C033_Senha,
							 C033_Token,
							 C033_Url
							 FROM C033
                             LEFT JOIN C037 ON C037_C033_Id = C033_Id
							 WHERE C037_Id = '{$processo}'");
		$mC033 = mysql_fetch_assoc($C033);

		$login = $mC033['C033_Login'];
		$senha = $mC033['C033_Senha'];
		$token = $mC033['C033_Token'];
		$url   = $mC033['C033_Url'];

        $C037 = mysql_query("SELECT C037_Codigo FROM C037 WHERE C037_Id='{$processo}'");
        $mC037 = mysql_fetch_assoc($C037);

        $codigo = str_replace('<?php','',$mC037['C037_Codigo']);

        $retorno = eval($codigo);

        return $retorno;

    }

	/**
     * reduzJSON
     * 
     * @param 
     * @return 
     */    
    public function reduzJSON($json, $return_array = true)
    { 

        $lines = explode(PHP_EOL, $json);

        $json = "";
        $ultimo = "";
        $loop = '';
        foreach($lines as $line){
            $line = ' ' . $line;
            if (strpos($line,'>>')){
                $cols = explode(">>", $line);
                $loop = trim($cols[1]).'>>';
                $line = ' ' . trim($cols[0]);
            } else if (strpos($line,'<<')){
                $cols = explode("<<", $line);
                $loop = '';
                $line = ' ' . trim($cols[0]);
            }
            if (strpos($line,'=>')){
                if ($ultimo==="ITEM" || $ultimo==="FINAL") {
                    $json .= ',';
                }
                $ultimo = "ITEM";
                $cols = explode("=>", $line);
                if (strpos(" {$cols[0]} ",":")){
                    $chave = explode(":", $cols[0]);
                } else {
                    $chave = array();
                    $chave[0] = $cols[0];
                }
                $chave = trim(str_replace(array('"',"[","{",":"),'', $chave[0]));
                $valor = trim($cols[1]);
                if (strpos(" {$cols[0]} ",":")){
                    $lineJson = '"'.$chave.'":"'.$loop.$valor.'"';
                } else {
                    $lineJson = '"'.$loop.$valor.'"';
                }
                $json .= $lineJson;
            } else {
                $item = explode(":", $line);
                if (!strpos(" ".$item[1]." ",'"')){
                    if (strpos($line,'{') || strpos($line,'[')) {
                        if ($ultimo==="ITEM" || $ultimo==="FINAL") {
                            $json .= ',';
                        }
                        $ultimo = "INICIO";
                        $lineJson = trim($line);
                        $json .= $lineJson;
                    } else if (strpos($line,'}') || strpos($line,']')) {
                        $ultimo = "FINAL";
                        $lineJson = trim(str_replace(',','', $line));
                        $json .= $lineJson;
                    }
                }
            }
        }

        $json = trim($json);
        $array = (array) json_decode($json);
        $array = json_decode(json_encode($array), true);
        $array = $this->arrayCleanRecursive($array);

        if ($return_array===true) {
            return $array;
        } else {
            return $json;
        }
        
    }

	/**
     * arrayCleanRecursive
     * 
     * @param 
     * @return 
     */    
    public function arrayCleanRecursive($array) {
        // Limpa items em branco de um array multidimensional
        $newArray=array();
        foreach ($array as $key => $value) {
            if (is_array($array[$key])) {
                if (count($array[$key])>0){
                    $arrayRecursive = $this->arrayCleanRecursive($array[$key]);
                    if (count($arrayRecursive)>0) {
                        $newArray[$key] = $arrayRecursive;
                    }
                }
            } else {
                if (!empty($value)) {
                    $newArray[$key] = $value;
                }
            }
        }
        return $newArray;
    }    

	/**
     * montaArray
     * 
     * @param 
     * @return 
     */    
    function montaArray($arrayPedido, $arrayCorrelacao, $arrayPK = array(), $newArray = array(), $arrayPedidoOriginal = array(), $arrayValue = array())
    {

        foreach ($arrayCorrelacao as $key => $value) 
        {
            if (is_array($arrayCorrelacao[$key]))
            {
                $array = $this->montaArray($arrayPedido[$key], $arrayCorrelacao[$key], $arrayPK, $newArray, $arrayPedido, $arrayValue);
                $newArray = array_merge($newArray,$array);
            } 
            else 
            {
                if (strpos(' '.$arrayCorrelacao[$key],'>>'))
                {
                    $cols = explode(">>",$arrayCorrelacao[$key]);
                    $arrayCorrelacao[$key] = $cols[1];
                    $loop = $cols[0];
                } else {
                    $loop = "";
                }
                $chaves = explode(',',$arrayCorrelacao[$key]);
                $arrayRegistro = array();
                foreach ($chaves as $chave) {
                    $valores = array();
                    if (!empty($loop)){
                        $valores = $this->filtrarArray($arrayPedidoOriginal,$key);
                    } else {
                        $valores[] = $arrayPedido[$key];
                    }
                    $chave = explode(":", $chave);
                    $callback = $chave[1];
                    $chave = trim($chave[0]);
                    $count = 0;
                    foreach ($valores as $API_ARRAY_CHAVE => $valor) {
                        // Troca o valor do campo caso esteja em $arrayValue
                        if (array_key_exists($chave, $arrayValue)) {
                            $valor = $arrayValue["$chave"];
                        }

                        if (!empty($callback)){ 
                            $valor = $this->callback($callback,$valor,$arrayRegistro,$newArray, $newArray,$API_ARRAY_CHAVE);
                        } else {
                            if (empty($loop)){
                                if (isset($newArray[$chave])){
                                    $newArray[$chave] .= ' ';
                                }
                            }
                        }
                        if (!empty($loop) && ($arrayPK[$loop]['chavePrimaria']===false || (is_array($arrayPK[$loop]['chavePrimaria']) && count($arrayPK[$loop]['chavePrimaria'])>1))) {
                            $newArray[$loop][$count][$chave] .= $valor;
                            $count++;
                        } else {
                            $newArray[$chave] .= $valor;
                        }
                        $arrayRegistro[$chave] = $valor;
                    }
                }
            }
        }
        return $newArray;
    }

	/**
     * Executa filtrarArray
     * 
     * @param 
     * @return 
     */    
    public function filtrarArray($arrayFiltro, $filtro, $newArray = array())
    { 
        foreach ($arrayFiltro as $key => $valor){
            if (is_array($arrayFiltro[$key])){
                $newArray = $this->filtrarArray($arrayFiltro[$key], $filtro, $newArray);
            } else {
                if ($key == $filtro){
                    $newArray[] = $arrayFiltro[$key];
                }
            }
        }
        return $newArray;
    }

	/**
     * Executa montaQuery
     * 
     * Função que monta estrutura para o executaQuery, faz a montagem de
     * insert, where, select(validação caso algum registro ja exista), update(se o registro já existir)
     * 
     * $array -> Array com os campos e dados que deverão ser inseridos
     * $arrayPK -> Define as tabelas, e como elas se correlacionam
     * para inserção ao banco de dados
     * $arrayValue -> Força a troca de campos para valores especificos
     * $targetEmpresa -> Define qual empresa os dados serão inseridos
     * caso as tabelas se correlacionem com "C004_Id"
     * @param 
     * @return 
     */    
    public function montaQuery($array, $arrayPK = array(), $arrayValue = array(), $targetEmpresa = false)
    { 
        global $g;

        // Se nenhuma empresa é especificada, é utilizado $g['empresaAtual'];
        $targetEmpresa = (empty($targetEmpresa) ? $g['empresaAtual'] : $targetEmpresa);

        /* Inicia arrays que serão utilizados */
        $arrayInsert = array(); // Array que salva quais tabelas haverá inserts
        $arrayInsertCampos = array(); // Array que junto com a tabela, indica quais campos haverá insert
        $arrayInsertValores = array(); // Array que junto com a tabela, indica os valores que serão inseridos nos campos

        $arrayUpdate = array();

        $arrayResult = array();

        $arrayWhere = array();

        $arrayRecursive = array();

        /* Passo 1 */

        /* Passa por todos os dados
        repassados no array de dados */
        foreach ($array as $key => $value)
        {
            /* Se uma das chaves conter um array, é necessário tratar
            esse array como um novo set de dados, por isso é utilizado
            função recursiva nesse sistema*/
            if (is_array($value))
            {
                foreach ($value as $keyRecursive => $valueRecursive) 
                {
                    $arrayRecursive["$key"]["$keyRecursive"] = $this->montaQuery($valueRecursive, $arrayPK, $arrayValue);
                }
                continue;
            }

            /* Explode pelos underline por causa de possibilidade
            de tabelas auxiliares*/
            $tabela = explode("_", $key);
            $tabela = $tabela[0];
            
            /* Se caso no $arrayPK que define as tabelas, for definido uma tabela
            livre essa tabela não é inserida no sistema de query, por isso dá a possibilidade
            de tabela XXXX por exemplo na API do Magento 2 */
            if ($arrayPK["$tabela"]['livre'] === true)
            {
                continue;
            }

            // Array que salva as tabelas que serão utilizadas para insert
            $arrayInsert["$tabela"] = $tabela;

            // Troca o valor do campo caso esteja em $arrayValue
            if (array_key_exists($key, $arrayValue)) 
            {
                $value = $arrayValue["$key"];
            }

            /* Se a chave que está no array de dados
            não for a chave primária de sua tabela*/
            if (trim($key) !== (strtoupper(trim($tabela)) . '_Id')) 
            {
                $arrayInsertCampos["$tabela"]["$key"] = $key; // Indica no array qual campo será inserido um dado
                $arrayInsertValores["$tabela"]["$key"] = "'" . $value . "'"; // Indica no array qual dado será inserido no campo
            }

            /* Se uma das chaves do array de dados não for uma chave primaria
            especificada no arrayPK */
            if (!in_array($key, $arrayPK["$tabela"]["chavePrimaria"])) 
            {
                // Adiciona como update
                $arrayUpdate["$tabela"][] = $key . " = '" . $value . "'";
            }

            /* Se uma das chaves do array de dados
            for uma chave primária no arrayPK e o valor dela não estiver
            vazio no array de dados */
            if ((in_array($key, $arrayPK["$tabela"]["chavePrimaria"])) && (!empty(trim($value)))) 
            {
                // Se torna uma clausula where
                $arrayWhere["$tabela"][] = $key . " = '" . $value . "'" ;  
            }
            
            /* Caso a tabela especificada for uma tabela estendida adiciona
            um ((1=1)) no WHERE para criar uma clausula de update no sistema
            do montaQuery */
            if($arrayPK["$tabela"]["forcarUpdate"] === true && !in_array("((1=1))", $arrayWhere["$tabela"]))
            {
                // Se torna uma clausula where
                $arrayWhere["$tabela"][] = "((1=1))"; 
            }
        }

        /* Verifica se as tabelas coletadas no array de dados
        são tabelas registradas pelo ArrayPK */
        foreach ($arrayInsert as $key => $value)
        {
            if (empty($arrayPK["$key"]))
            {
                return "ERRO: Você precisa definir registro de chave primária em arrayPK para tabela {$key}";
            }
        }
        foreach ($arrayPK as $key => $value){
            if (!isset($arrayInsert["$key"])) {
                continue;
            }
            if (count($arrayWhere["$key"])>1){
                $arrayWhere["$key"] = array("(" . implode(" and ", $arrayWhere["$key"]) . ")");
            }
            if ($arrayPK["$key"]["tabelaMestre"]){
                $chavesTabelaMestre = explode(",",$arrayPK["$key"]["tabelaMestre"]);
                foreach ($chavesTabelaMestre as $chaveTabelaMestre) {
                    $arrayInsertCampos["$key"][$key . "_" . $chaveTabelaMestre . "_Id"] = $key . "_" . $chaveTabelaMestre . "_Id";
                    $arrayInsertValores["$key"][$key . "_" . $chaveTabelaMestre . "_Id"] = "'" . '{$arrayId[' . "'" . $chaveTabelaMestre . "']}'";
                    if (!empty($arrayWhere["$key"])) {
                        $arrayWhere["$key"][] = $key . "_" . $chaveTabelaMestre . "_Id = '" . '{$arrayId[' . "'" . $chaveTabelaMestre . "']}'";
                    }
                }
            }
            if ($arrayPK["$key"]["tabelaFK"]){
                $chavesTabelaPK = explode(",",$arrayPK["$key"]["tabelaFK"]);
                foreach ($chavesTabelaPK as $chaveTabelaPK) {
                    if (strpos($chaveTabelaPK,'_Id')) {
                        $chavePK = $chaveTabelaPK;
                        $chaveTabelaPK = explode("_",$chaveTabelaPK);
                        $tabelaPK = $chaveTabelaPK[0];
                    } else {
                        $chavePK = $chaveTabelaPK . "_Id";
                        $tabelaPK = $chaveTabelaPK;
                    }
                    $arrayInsertCampos["$key"][$key . "_" . $chavePK] = $key . "_" . $chavePK;
                    $arrayInsertValores["$key"][$key . "_" . $chavePK] = "'" . '{$arrayId[' . "'" . $tabelaPK . "']}'";
                }
            }
            if ($arrayPK["$key"]["tabelaPrimaria"] === true) {
                if (in_array($key . "_C004_Id", $arrayInsertCampos["$key"])) {
                    $C004 = mysql_query("select C004_Id from C004 where C004_Id=" . $arrayInsertValores["$key"][$key . "_C004_Id"]);
                    if (!mysql_num_rows($C004)) {
                        return "ERRO: " . "select C004_Id from C004 where C004_Id=" . $arrayInsertValores["$key"][$key . "_C004_Id"];
                    }
                } else {
                    $sqlC004 = mysql_query("show columns from {$key} like '%C004_Id'");
                    if (mysql_num_rows($sqlC004)>0){
                        $arrayInsertCampos["$key"][$key . "_C004_Id"] = $key . "_C004_Id";
                        $arrayInsertValores["$key"][$key . "_C004_Id"] = "'" . $targetEmpresa . "'";
                    }
                }
            }
            $arrayResult["$key"]["insert"] = "insert into " . $arrayInsert["$key"] . " (" . implode(",",$arrayInsertCampos["$key"]) . ") values (" . implode(",",$arrayInsertValores["$key"]) . ")";
            $arrayResult["$key"]["where"] = implode(" and ", $arrayWhere["$key"]);
            if ($arrayPK["$key"]["tabelaPrimaria"] === true) {
                $arrayResult["$key"]["update"] = false;    
            } else if (empty($arrayWhere["$key"])) {
                $arrayResult["$key"]["update"] = "";    
            } else if (count($arrayUpdate["$key"])>0) {
                $arrayResult["$key"]["update"] = "update " . $arrayInsert["$key"] . " set " . implode(",", $arrayUpdate["$key"]) . " where " . implode(" and ", $arrayWhere["$key"]);
            } 
            if (!empty($arrayWhere["$key"])) {
                $arrayResult["$key"]["valida"] = "select " . $arrayInsert["$key"] . "_Id from " . $arrayInsert["$key"] . " where " . implode(" and ", $arrayWhere["$key"]);
            }
            if ($arrayPK["$key"]["chavePrimaria"] && empty($arrayResult["$key"]["valida"])){
                $arrayResult["$key"]["erro"] = "Tabela está com chave primária em branco.";
            }
        }
        
        $arrayResult = array_merge($arrayResult,$arrayRecursive);
        $arrayResult = $this->organizaQuery( $arrayResult );
  
        return $arrayResult;
    }

	/**
     * atualizaALiquotaPedido
     * 
     * @param 
     * @return 
     */    
    public function atualizaALiquotaPedido($T005_Id)
    {
        require_once("bibliotecas/classes/VEN002.php");
        $VEN002 = new VEN002();
        require_once("bibliotecas/classes/CAD002.php");
        $CAD002 = new CAD002();
            
        $VEN002->ven002atualizarCFOP($T005_Id);
        $VEN002->atualizarAliquotasPedidos($T005_Id);

        $T006 = mysql_query("select T006_D009_Id from T006 where T006_T005_Id='{$T005_Id}'");
        while ($mT006 = mysql_fetch_array($T006)){
            $CAD002->D001_reprocessa_historico(0, $mT006['T006_D009_Id'], 5);
        }

        mysql_query("COMMIT"); 

        return true;
    }

    /**
     *atualizaParcelas teste Almir
     * 
     * @param 
     * @return 
     */
    public function atualizaParcelas($T005_Id)
    {
        
        require_once("bibliotecas/classes/VEN001.php");
        $VEN001 = new VEN001();

        //ARRAY UTILIZADO NA FUNÇÃO ATUALIZAR PARCELAS
        $dadosTabela = array('tabela' => 'T005', 
                            'tabelaId' => 	$T005_Id,
                            'campos' => 'T005_Pgto_Primeiro_Vcto_Tipo as primeiroVencimentoTipo,
                                T005_Pgto_Primeiro_Vcto_Valor as primeiroVencimentoNum,
                                T005_Pgto_Primeiro_Vcto_Valor_Data as primeiroVencimentoData,
                                T005_Pgto_Dias_Vcto_Tipo as diasVctoTipo,
                                T005_Pgto_Dias_Vcto_Valor as valorIntervaloDiaFixo,
                                T005_D027_Id as D027_Id,
                                T005_Flag_Frete_Primeira_Parcela as campoUsarFreteParcela,
                                T005_Flag_ST_Primeira_Parcela as campoUsarSTParcela,
                                T005_Flag_IPI_Primeira_Parcela as campoUsarIPIParcela,
                                T005_Pgto_Parcelas as Total_Parcelas,
                                T005_Valor_Total_IPI as valorTotalIPI,
                                T005_Valor_Total as valorTotal,
                                T005_Valor_Frete as valorTotalFrete,
                                T005_Valor_ICMS_Substituicao_Retencao as valorTotalST', 
                            'where' => "T005_Id='{$T005_Id}'", 
                            'tabelaPgto' => 'T090', 
                            'camposPgto' => 'T090_Prazos_Dias as prazoDias,T090_D027_Id as D027Pagamento,T090_Numero_Parcela as numeroParcela', 
                            'wherePgto' => "T090_T005_Id='{$T005_Id}'");
        // Gerar Parcelas			
        $retorno = $VEN001->atualizarParcelas($dadosTabela);
        
        if ($retorno==true) {

            return 'Atualizou Parcelas ' . $retorno . '-' . $T005_Id;  
            
        }  else{
            return 'Erro ao gerar Pacelas: ' . $retorno .'-'.$T005_Id;   
        }

    }


    /**
     * Trata a data do callback32 Popular data de entrega
     */

    //FORMATA COMO TIMESTAMP
    function dataToTimestamp($data){
        $ano = substr($data, 6,4);
        $mes = substr($data, 3,2);
        $dia = substr($data, 0,2);
        
    return mktime(0, 0, 0, $mes, $dia, $ano);  
    }

    function Soma1dia($data){
        
        $ano = substr($data, 6,4);
        $mes = substr($data, 3,2);
        $dia = substr($data, 0,2);
        
        return date("d/m/Y", mktime(0, 0, 0, $mes, $dia+1, $ano));
     }

     //LISTA DE FERIADOS NO ANO
    function Feriados($ano,$posicao){
        $dia = 86400;
        $datas = array();
        $datas['pascoa'] = easter_date($ano);
        $datas['sexta_santa'] = $datas['pascoa'] - (2 * $dia);
        $datas['carnaval'] = $datas['pascoa'] - (47 * $dia);
        $datas['corpus_cristi'] = $datas['pascoa'] + (60 * $dia);
        $feriados = array (
        '01/01', // Ano Novo
        date('d/m',$datas['carnaval']),
        date('d/m',$datas['sexta_santa']),
        date('d/m',$datas['pascoa']),
        '21/04',//Tiradentes
        '01/05',//Trabalhador
        date('d/m',$datas['corpus_cristi']),
        '08/08',//Dia dos Pais
        '07/09', // Independência
        '08/09',//Facultativo
        '12/10', //Nossa Sra
        '02/11', //Finados
        '15/11', //Plocamação da Republica
        '25/12', // Natal
        );
            
    return $feriados[$posicao]."/".$ano;
    }

    function SomaDiasUteis($xDataInicial,$xSomarDias){
        for($ii=1; $ii<=$xSomarDias; $ii++){
           
           $xDataInicial=$this->Soma1dia($xDataInicial); //SOMA DIA NORMAL
           
           //VERIFICANDO SE EH DIA DE TRABALHO
           if(date("w", $this->dataToTimestamp($xDataInicial))=="0"){
              //SE DIA FOR DOMINGO OU FERIADO, SOMA +1
              $xDataInicial=$this->Soma1dia($xDataInicial);
             
           }else if(date("w", $this->dataToTimestamp($xDataInicial))=="6"){
              //SE DIA FOR SABADO, SOMA +2
              $xDataInicial=$this->Soma1dia($xDataInicial);
              $xDataInicial=$this->Soma1dia($xDataInicial);
             
           }else{
              //senaum vemos se este dia eh FERIADO
              for($i=0; $i<=12; $i++){
                 if($xDataInicial==$this->Feriados(date("Y"),$i)){
                    $xDataInicial=$this->Soma1dia($xDataInicial);
                 }
              }
           }
        }
     return $xDataInicial;
    }

    function Numero($string){
        
        return preg_replace("/[^0-9]/", "", $string);
     
    }

    function inverteData($data){    
        $parteData = explode("/", $data);    
        $dataInvertida = $parteData[2] . "/" . $parteData[1] . "/" . $parteData[0];
    return $dataInvertida;
    }
     
	/**
     * organizaQuery
     * 
     * @param 
     * @return 
     */    
    public function organizaQuery($arrayQuery)
    {
        $arrayResult = array();
        $count=0;
        foreach ($arrayQuery as $key => $value){
            foreach ($value as $keyRecursive => $valueRecursive){
                if (is_array($valueRecursive)) {
                    $valueRecursive['tabela'] = $key;
                    $arrayResult[] = $valueRecursive[0];
                    $count++;
                } else {
                    $value['tabela'] = $key;
                    $arrayResult[] = $value;
                    break;
                }
            }
        }
        return $arrayResult;
    }

    /**
     * executaQuery
     * 
     * @param 
     * @return 
     */    
    public function executaQuery($arrayQuery)
    {
        global $g;
        if (!is_array($arrayQuery)) {
            return "ERRO: Parametro recebido não é um array";
        }
        $erros = '';
        foreach ($arrayQuery as $key => $value)
        {
            if (!empty($arrayQuery["$key"]["erro"])) {
                $erros .= $arrayQuery["$key"]["tabela"] . " - " . $arrayQuery["$key"]["erro"];
            }
        }
        if (!empty($erros)) {
            return $erros;
        }
        $arrayId = array();
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        foreach ($arrayQuery as $key => $value)
        {
            $tabela = $value['tabela'];
            foreach ($arrayId as $keyTroca => $valueTroca) {
                $value['insert'] =  str_replace('{$arrayId[' . "'" . $keyTroca . "'" . ']}', $valueTroca, $value['insert']);
                $value['valida'] =  str_replace('{$arrayId[' . "'" . $keyTroca . "'" . ']}', $valueTroca, $value['valida']);
                if ($value['update']){
                    $value['update'] =  str_replace('{$arrayId[' . "'" . $keyTroca . "'" . ']}', $valueTroca, $value['update']);
                }
            }
            $sql = mysql_query($value["valida"]);
            if (mysql_num_rows($sql)>0){
                if ($value["update"]===false){
                    mysql_query("ROLLBACK");
                    return "Registro da tabela primária " . $tabela . " já existe";
                }
                $mSql = mysql_fetch_array($sql);
                $arrayId["$tabela"] = $mSql[0];
                if (!empty($value["update"])){
                    mysql_query($value["update"]);
                    $erro = mysql_error();
                    if (!empty($erro)) {
                        mysql_query("ROLLBACK");
                        return "Erro na execução da query " . $value["update"];
                    }
                }
            } else {
                if (!empty($value["insert"])){
                    mysql_query("{$value['insert']}");
                    $erro = mysql_error();
                    if (!empty($erro)) {
                        mysql_query("ROLLBACK");
                        return "Erro na execução da query " . $value["insert"];
                    }
                    $arrayId["$tabela"] = $g['mysqlLastId'];
                }
            }
        }
        mysql_query("COMMIT");
        return $arrayId;
    }

	/**
     * Executa callback
     * 
     * @param 
     * @return 
     */    
    public function callback($callback, $valorCampo, $arrayRegistro = array(), $newArray = array(), &$arrModificar = array(), $API_ARRAY_CHAVE)
    { 
        static $valor;

		// Classes
		require_once('bibliotecas/classes/API001.php');
		$API001 = new API001();

        $callback = explode("=",$callback);
        $callback = $callback[1];

        $C038 = mysql_query("SELECT C038_Codigo FROM C038 WHERE C038_Id='{$callback}'");
        $mC038 = mysql_fetch_assoc($C038);

        if (empty($mC038['C038_Codigo'])) {
            return 'Código do callback não definido, favor informar o suporte técnico';
        }

        $codigo = str_replace('<?php','',$mC038['C038_Codigo']);

        $valor = $valorCampo;

        $retorno = eval($codigo);

        return $retorno;

    }

    /**
     * Executa apiQuery
     * 
     * @param 
     * @return 
     */    
    public function apiQuery($query)
    { 
        $queryAnalise = strtoupper($query);
        if (  strpos(" $query ", 'DROP') || strpos(" $query ", 'DELETE') 
           || strpos(" $query ", 'ALTER TABLE') || strpos(" $query ", 'INSERT')
           || strpos(" $query ", 'UPDATE'))
        {
            return "Comando não permitido";
        }

        $api = mysql_query($query);
        $mApi = mysql_fetch_array($api);

        return $mApi;

    }

    /**
     * API Máscara
     * 
     * @param 
     * @return 
     */    
    public function apiMascara($valor,$mascara)
    { 
        $retorno = '';
        $k = 0;

        for($i=0;$i<strlen($mascara);$i++)
        {  
            if($mascara[$i] == '#') {
                if (isset($valor[$k])) {
                    $retorno .= $valor[$k++];
                }
            } else {
                if(isset($mascara[$i])) {
                    $retorno .= $mascara[$i];
                }
            }
         }

         return $retorno;
    }


    /**
     * exportar dados API CSV
     *
     * @param Form $form
     * @param File $arquivo
     * @return bool
     */    
    public function exportarAPI001($form)
    { 
        global $g;

        $Data_Inicio = $form->campoValorEnviado('Data_Inicio');    
        $Data_Fim = $form->campoValorEnviado('Data_Fim');

        $xData_Inicio = gCorrigeDataInverte($Data_Inicio);
        $xData_Fim    = gCorrigeDataInverte($Data_Fim);

        if (empty($xData_Inicio)) {
            return "Digite a Data Inicial";
        }
        
        if (empty($xData_Fim)) {
            return "Digite a Data Final";
        }
        
        if ($xData_Fim < $xData_Inicio) {
            return "Data final é menor que a data inicial";
        }

        if (!extension_loaded('zip')) {
            return 'Nao esta habilitado php_zip, edite seu php.ini e descomente essa linha, se nao existir basta cria-la: extension=php_zip';
        }

        set_time_limit(0);

        //-- Definindo Variaveis e Constantes
        define(SALTAR_LINHA, chr(13) . chr(10));

        //-- Cria novo arquivo ZIP

        $dir     = "{$g['pathDadosUsuario']}arq_temp";
        is_dir($dir) OR mkdir($dir, 0777, true);
        $cArquivoZIP = "{$g['pathDadosUsuario']}arq_temp/dadosBI.zip";

        $zip = new \ZipArchive();
        if ($zip->open("$cArquivoZIP", \ZIPARCHIVE::CREATE) !== TRUE) {
            return "Impossivel abrir <$cArquivoZIP>";
        }

        chmod($cArquivoZIP, 0777);

        //------------------------------------------------------------------
        // Criar arquivo de clientes
        //------------------------------------------------------------------

        $dir     = "{$g['pathDadosUsuario']}arq_temp";
        is_dir($dir) OR mkdir($dir, 0777, true);
        $cArquivo = "{$g['pathDadosUsuario']}arq_temp/cliente.csv";

        if (file_exists($cArquivo)) {
            unlink($cArquivo);
        }          

        //-- Cria o arquivo
        if (!$handle = fopen($cArquivo, 'x')) {
            return "Erro ao criar o arquivo: <br />$cArquivo";
        }

        chmod($cArquivo, 0777);

        //variavel contadoras de linhas
        $conta_linha = 1;

        //-- Prepara o campo data de gravacao do arquivo
        $Data_Gravacao = date("dmy", time());

        $cAgrega_Dados = '';

        $cAgrega_Dados .= "Codigo_Cliente;";
        $cAgrega_Dados .= "Nome_Cliente;";
        $cAgrega_Dados .= "Bairro_Cliente;";
        $cAgrega_Dados .= "Cidade_Cliente;";
        $cAgrega_Dados .= "UF_Cliente";
        $cAgrega_Dados .= SALTAR_LINHA;

        $cAgrega_Dados .= "000000;";
        $cAgrega_Dados .= "SEM CLIENTE;";
        $cAgrega_Dados .= "SEM BAIRRO;";
        $cAgrega_Dados .= "SEM CIDADE;";
        $cAgrega_Dados .= "SEM UF";
        $cAgrega_Dados .= SALTAR_LINHA;

        // Pesquisando dados dos clientes 
        $D024 = mysql_query("SELECT D024_Id,
                                    D024_Nome_Empresa,
                                    D018_UF,
                                    D020_Nome_Cidade,
                                    D024_Bairro
                               FROM D024
                          LEFT JOIN D018 ON D018_Id = D024_D018_Id
                          LEFT JOIN D020 ON D020_Id = D024_D020_Id
                              WHERE D024_Nome_Empresa!=''
                                AND IFNULL(D024_D020_Id,0)>0
                                AND IFNULL(D024_D018_Id,0)>0
                           ORDER BY D024_Id");
        while($mD024 = mysql_fetch_array($D024)){
            $cAgrega_Dados .= str_pad(trim($mD024['D024_Id']),6,'0',STR_PAD_LEFT).";";
            $cAgrega_Dados .= $this->adicionarEspape(str_replace(';', '', $mD024['D024_Nome_Empresa'])).";";
            $cAgrega_Dados .= trim($mD024['D024_Bairro']).";";
            $cAgrega_Dados .= trim($mD024['D020_Nome_Cidade']).";";
            $cAgrega_Dados .= trim($mD024['D018_UF']);
            $cAgrega_Dados .= SALTAR_LINHA;
        }

        fwrite($handle, $cAgrega_Dados);
        fclose($handle);

        @$zip->addFile($cArquivo, 'cliente.csv');

        //--- Fim arquivo cliente


        //------------------------------------------------------------------
        // Criar arquivo de empresas
        //------------------------------------------------------------------

        $dir     = "{$g['pathDadosUsuario']}arq_temp";
        is_dir($dir) OR mkdir($dir, 0777, true);
        $cArquivo = "{$g['pathDadosUsuario']}arq_temp/empresa.csv";

        if (file_exists($cArquivo)) {
            unlink($cArquivo);
        }          

        //-- Cria o arquivo
        if (!$handle = fopen($cArquivo, 'x')) {
            return "Erro ao criar o arquivo: <br />$cArquivo";
        }

        chmod($cArquivo, 0777);

        //variavel contadoras de linhas
        $conta_linha = 1;

        //-- Prepara o campo data de gravacao do arquivo
        $Data_Gravacao = date("dmy", time());

        $cAgrega_Dados = '';

        $cAgrega_Dados .= "Id_Empresa;";
        $cAgrega_Dados .= "Nome_Empresa;";
        $cAgrega_Dados .= "Cidade_Empresa;";
        $cAgrega_Dados .= "UF_Empresa";
        $cAgrega_Dados .= SALTAR_LINHA;

        $cAgrega_Dados .= "000000;";
        $cAgrega_Dados .= "SEM EMPRESA;";
        $cAgrega_Dados .= "SEM CIDADE;";
        $cAgrega_Dados .= "SEM UF";
        $cAgrega_Dados .= SALTAR_LINHA;

        // Pesquisando dados dos clientes 
        $C004 = mysql_query("SELECT C004_Id,
                                    C004_Nome_Empresa,
                                    D018_UF,
                                    D020_Nome_Cidade
                               FROM C004
                          LEFT JOIN D018 ON D018_Id = C004_D018_Id
                          LEFT JOIN D020 ON D020_Id = C004_D020_Id
                           ORDER BY C004_Id");
        while($mC004 = mysql_fetch_array($C004)){
            $cAgrega_Dados .= str_pad(trim($mC004['C004_Id']),6,'0',STR_PAD_LEFT).";";
            $cAgrega_Dados .= $this->adicionarEspape(str_replace(';', '', $mC004['C004_Nome_Empresa'])).";";
            $cAgrega_Dados .= trim($mC004['D020_Nome_Cidade']).";";
            $cAgrega_Dados .= trim($mC004['D018_UF']);
            $cAgrega_Dados .= SALTAR_LINHA;
        }

        fwrite($handle, $cAgrega_Dados);
        fclose($handle);

        @$zip->addFile($cArquivo, 'empresa.csv');

        //--- Fim arquivo empresa

        //------------------------------------------------------------------
        // Criar arquivo de tipos CFOP
        //------------------------------------------------------------------

        $dir     = "{$g['pathDadosUsuario']}arq_temp";
        is_dir($dir) OR mkdir($dir, 0777, true);
        $cArquivo = "{$g['pathDadosUsuario']}arq_temp/tipo_cfop.csv";

        if (file_exists($cArquivo)) {
            unlink($cArquivo);
        }          

        //-- Cria o arquivo
        if (!$handle = fopen($cArquivo, 'x')) {
            return "Erro ao criar o arquivo: <br />$cArquivo";
        }

        chmod($cArquivo, 0777);

        //variavel contadoras de linhas
        $conta_linha = 1;

        //-- Prepara o campo data de gravacao do arquivo
        $Data_Gravacao = date("dmy", time());

        $cAgrega_Dados = '';

        $cAgrega_Dados .= "tipo_cfop;";
        $cAgrega_Dados .= "Descricao";
        $cAgrega_Dados .= SALTAR_LINHA;

        $cAgrega_Dados .= "XX;";
        $cAgrega_Dados .= "SEM TIPO";
        $cAgrega_Dados .= SALTAR_LINHA;

        $tipoCFOP['C']  = 'Compra';
        $tipoCFOP['DC'] = 'Devolucao Compra';
        $tipoCFOP['DV'] = 'Devolucao Venda';
        $tipoCFOP['O']  = 'Outros';
        $tipoCFOP['T']  = 'Transferencia';
        $tipoCFOP['TR'] = 'Transporte';
        $tipoCFOP['V']  = 'Venda';
 
        // Pesquisando dados dos clientes 
        $D006 = mysql_query("SELECT D006_Flag_Venda_Compra_Outros
                               FROM D006
                              WHERE IFNULL(D006_Flag_Venda_Compra_Outros,'')!=''
                           GROUP BY D006_Flag_Venda_Compra_Outros");

        while($mD006 = mysql_fetch_array($D006)){
            $cAgrega_Dados .= $mD006['D006_Flag_Venda_Compra_Outros'].";";
            $cAgrega_Dados .= $tipoCFOP["{$mD006['D006_Flag_Venda_Compra_Outros']}"];
            $cAgrega_Dados .= SALTAR_LINHA;
        }

        fwrite($handle, $cAgrega_Dados);
        fclose($handle);

        @$zip->addFile($cArquivo, 'tipo_cfop.csv');

        //--- Fim arquivo cliente

        //------------------------------------------------------------------
        // Criar arquivo de usuários
        //------------------------------------------------------------------

        $dir     = "{$g['pathDadosUsuario']}arq_temp";
        is_dir($dir) OR mkdir($dir, 0777, true);
        $cArquivo = "{$g['pathDadosUsuario']}arq_temp/usuarios.csv";

        if (file_exists($cArquivo)) {
            unlink($cArquivo);
        }          

        //-- Cria o arquivo
        if (!$handle = fopen($cArquivo, 'x')) {
            return "Erro ao criar o arquivo: <br />$cArquivo";
        }

        chmod($cArquivo, 0777);

        //variavel contadoras de linhas
        $conta_linha = 1;

        //-- Prepara o campo data de gravacao do arquivo
        $Data_Gravacao = date("dmy", time());

        $cAgrega_Dados = '';

        $cAgrega_Dados .= "Codigo_Usuario;";
        $cAgrega_Dados .= "Nome_Usuario";
        $cAgrega_Dados .= SALTAR_LINHA;

        $cAgrega_Dados .= "000000;";
        $cAgrega_Dados .= "SEM USUARIO";
        $cAgrega_Dados .= SALTAR_LINHA;

        // Pesquisando dados dos clientes 
        $C007 = mysql_query("SELECT C007_Id,
                                    C007_Nome
                               FROM C007
                           ORDER BY C007_Id");
        while($mC007 = mysql_fetch_array($C007)){
            $cAgrega_Dados .= str_pad(trim($mC007['C007_Id']),6,'0',STR_PAD_LEFT).";";
            $cAgrega_Dados .= trim($mC007['C007_Nome']);
            $cAgrega_Dados .= SALTAR_LINHA;
        }

        fwrite($handle, $cAgrega_Dados);
        fclose($handle);

        @$zip->addFile($cArquivo, 'usuarios.csv');

        //--- Fim arquivo usuarios

        //------------------------------------------------------------------
        // Criar arquivo de vendedores internos
        //------------------------------------------------------------------

        $dir     = "{$g['pathDadosUsuario']}arq_temp";
        is_dir($dir) OR mkdir($dir, 0777, true);
        $cArquivo = "{$g['pathDadosUsuario']}arq_temp/vendedor_interno.csv";

        if (file_exists($cArquivo)) {
            unlink($cArquivo);
        }          

        //-- Cria o arquivo
        if (!$handle = fopen($cArquivo, 'x')) {
            return "Erro ao criar o arquivo: <br />$cArquivo";
        }

        chmod($cArquivo, 0777);

        //variavel contadoras de linhas
        $conta_linha = 1;

        //-- Prepara o campo data de gravacao do arquivo
        $Data_Gravacao = date("dmy", time());

        $cAgrega_Dados = '';

        $cAgrega_Dados .= "Codigo_Vendedor_Interno;";
        $cAgrega_Dados .= "Nome_Vendedor";
        $cAgrega_Dados .= SALTAR_LINHA;

        $cAgrega_Dados .= "000000;";
        $cAgrega_Dados .= "SEM USUARIO";
        $cAgrega_Dados .= SALTAR_LINHA;

        // Pesquisando dados dos clientes 
        $C007 = mysql_query("SELECT C007_Id, 
                                    C007_Nome
                               FROM C007
                           ORDER BY C007_Id");
        while($mC007 = mysql_fetch_array($C007)){
            $cAgrega_Dados .= str_pad(trim($mC007['C007_Id']),6,'0',STR_PAD_LEFT).";";
            $cAgrega_Dados .= trim($mC007['C007_Nome']);
            $cAgrega_Dados .= SALTAR_LINHA;
        }

        fwrite($handle, $cAgrega_Dados);
        fclose($handle);

        @$zip->addFile($cArquivo, 'vendedor_interno.csv');

        //--- Fim arquivo vendedor interno

        //------------------------------------------------------------------
        // Criar arquivo de supervisor 
        //------------------------------------------------------------------

        $dir     = "{$g['pathDadosUsuario']}arq_temp";
        is_dir($dir) OR mkdir($dir, 0777, true);
        $cArquivo = "{$g['pathDadosUsuario']}arq_temp/supervisor.csv";

        if (file_exists($cArquivo)) {
            unlink($cArquivo);
        }          

        //-- Cria o arquivo
        if (!$handle = fopen($cArquivo, 'x')) {
            return "Erro ao criar o arquivo: <br />$cArquivo";
        }

        chmod($cArquivo, 0777);

        //variavel contadoras de linhas
        $conta_linha = 1;

        //-- Prepara o campo data de gravacao do arquivo
        $Data_Gravacao = date("dmy", time());

        $cAgrega_Dados = '';

        $cAgrega_Dados .= "Codigo_Supervisor;";
        $cAgrega_Dados .= "Nome_Supervisor";
        $cAgrega_Dados .= SALTAR_LINHA;

        $cAgrega_Dados .= "000000;";
        $cAgrega_Dados .= "SEM SUPERVISOR";
        $cAgrega_Dados .= SALTAR_LINHA;

        // Pesquisando dados dos clientes 
        $C007 = mysql_query("SELECT C007_Id,
                                    C007_Nome
                               FROM C007
                           ORDER BY C007_Id");
        while($mC007 = mysql_fetch_array($C007)){
            $cAgrega_Dados .= str_pad(trim($mC007['C007_Id']),6,'0',STR_PAD_LEFT).";";
            $cAgrega_Dados .= trim($mC007['C007_Nome']);
            $cAgrega_Dados .= SALTAR_LINHA;
        }

        fwrite($handle, $cAgrega_Dados);
        fclose($handle);

        @$zip->addFile($cArquivo, 'supervisor.csv');

        //--- Fim arquivo vendedor supervisor

        //------------------------------------------------------------------
        // Criar arquivo de vendedor_externo 
        //------------------------------------------------------------------

        $dir     = "{$g['pathDadosUsuario']}arq_temp";
        is_dir($dir) OR mkdir($dir, 0777, true);
        $cArquivo = "{$g['pathDadosUsuario']}arq_temp/vendedor_externo.csv";

        if (file_exists($cArquivo)) {
            unlink($cArquivo);
        }          

        //-- Cria o arquivo
        if (!$handle = fopen($cArquivo, 'x')) {
            return "Erro ao criar o arquivo: <br />$cArquivo";
        }

        chmod($cArquivo, 0777);

        //variavel contadoras de linhas
        $conta_linha = 1;

        //-- Prepara o campo data de gravacao do arquivo
        $Data_Gravacao = date("dmy", time());

        $cAgrega_Dados = '';

        $cAgrega_Dados .= "Codigo_vendedor_externo;";
        $cAgrega_Dados .= "Nome_vendedor_externo";
        $cAgrega_Dados .= SALTAR_LINHA;

        $cAgrega_Dados .= "000000;";
        $cAgrega_Dados .= "SEM VENDEDOR";
        $cAgrega_Dados .= SALTAR_LINHA;

        // Pesquisando dados dos clientes 
        $C007 = mysql_query("SELECT C007_Id,
                                    C007_Nome
                               FROM C007
                           ORDER BY C007_Id");
        while($mC007 = mysql_fetch_array($C007)){
            $cAgrega_Dados .= str_pad(trim($mC007['C007_Id']),6,'0',STR_PAD_LEFT).";";
            $cAgrega_Dados .= trim($mC007['C007_Nome']);
            $cAgrega_Dados .= SALTAR_LINHA;
        }

        fwrite($handle, $cAgrega_Dados);
        fclose($handle);

        @$zip->addFile($cArquivo, 'vendedor_externo.csv');

        //--- Fim arquivo vendedor externo

        //------------------------------------------------------------------
        // Criar arquivo de supervisor2 
        //------------------------------------------------------------------

        $dir     = "{$g['pathDadosUsuario']}arq_temp";
        is_dir($dir) OR mkdir($dir, 0777, true);
        $cArquivo = "{$g['pathDadosUsuario']}arq_temp/supervisor2.csv";

        if (file_exists($cArquivo)) {
            unlink($cArquivo);
        }          

        //-- Cria o arquivo
        if (!$handle = fopen($cArquivo, 'x')) {
            return "Erro ao criar o arquivo: <br />$cArquivo";
        }

        chmod($cArquivo, 0777);

        //variavel contadoras de linhas
        $conta_linha = 1;

        //-- Prepara o campo data de gravacao do arquivo
        $Data_Gravacao = date("dmy", time());

        $cAgrega_Dados = '';

        $cAgrega_Dados .= "Codigo_supervisor2;";
        $cAgrega_Dados .= "Nome_supervisor2";
        $cAgrega_Dados .= SALTAR_LINHA;

        $cAgrega_Dados .= "000000;";
        $cAgrega_Dados .= "SEM SUPERVISOR";
        $cAgrega_Dados .= SALTAR_LINHA;

        // Pesquisando dados dos clientes 
        $C007 = mysql_query("SELECT C007_Id,
                                    C007_Nome
                               FROM C007
                           ORDER BY C007_Id");
        while($mC007 = mysql_fetch_array($C007)){
            $cAgrega_Dados .= str_pad(trim($mC007['C007_Id']),6,'0',STR_PAD_LEFT).";";
            $cAgrega_Dados .= trim($mC007['C007_Nome']);
            $cAgrega_Dados .= SALTAR_LINHA;
        }

        fwrite($handle, $cAgrega_Dados);
        fclose($handle);

        @$zip->addFile($cArquivo, 'supervisor2.csv');

        //--- Fim arquivo vendedor supervisor2

        //------------------------------------------------------------------
        // Criar arquivo de Vendedor_Substituto 
        //------------------------------------------------------------------

        $dir     = "{$g['pathDadosUsuario']}arq_temp";
        is_dir($dir) OR mkdir($dir, 0777, true);
        $cArquivo = "{$g['pathDadosUsuario']}arq_temp/Vendedor_substituto.csv";

        if (file_exists($cArquivo)) {
            unlink($cArquivo);
        }          

        //-- Cria o arquivo
        if (!$handle = fopen($cArquivo, 'x')) {
            return "Erro ao criar o arquivo: <br />$cArquivo";
        }

        chmod($cArquivo, 0777);

        //variavel contadoras de linhas
        $conta_linha = 1;

        //-- Prepara o campo data de gravacao do arquivo
        $Data_Gravacao = date("dmy", time());

        $cAgrega_Dados = '';

        $cAgrega_Dados .= "Codigo_Vendedor_Substituto;";
        $cAgrega_Dados .= "Nome_Vendedor_Substituto";
        $cAgrega_Dados .= SALTAR_LINHA;

        $cAgrega_Dados .= "000000;";
        $cAgrega_Dados .= "SEM VENDEDOR SUBSTITUTO";
        $cAgrega_Dados .= SALTAR_LINHA;

        // Pesquisando dados dos clientes 
        $C007 = mysql_query("SELECT C007_Id,
                                    C007_Nome
                               FROM C007
                           ORDER BY C007_Id");
        while($mC007 = mysql_fetch_array($C007)){
            $cAgrega_Dados .= str_pad(trim($mC007['C007_Id']),6,'0',STR_PAD_LEFT).";";
            $cAgrega_Dados .= trim($mC007['C007_Nome']);
            $cAgrega_Dados .= SALTAR_LINHA;
        }

        fwrite($handle, $cAgrega_Dados);
        fclose($handle);

        @$zip->addFile($cArquivo, 'Vendedor_substituto.csv');

        //--- Fim arquivo vendedor Vendedor_Substituto


        //------------------------------------------------------------------
        // Criar arquivo de transportadora
        //------------------------------------------------------------------

        $dir     = "{$g['pathDadosUsuario']}arq_temp";
        is_dir($dir) OR mkdir($dir, 0777, true);
        $cArquivo = "{$g['pathDadosUsuario']}arq_temp/transportadora.csv";

        if (file_exists($cArquivo)) {
            unlink($cArquivo);
        }          

        //-- Cria o arquivo
        if (!$handle = fopen($cArquivo, 'x')) {
            return "Erro ao criar o arquivo: <br />$cArquivo";
        }

        chmod($cArquivo, 0777);

        //variavel contadoras de linhas
        $conta_linha = 1;

        //-- Prepara o campo data de gravacao do arquivo
        $Data_Gravacao = date("dmy", time());

        $cAgrega_Dados = '';

        $cAgrega_Dados .= "Codigo_Transportadora;";
        $cAgrega_Dados .= "Nome_Transportadora;";
        $cAgrega_Dados .= "Cidade_Transportadora;";
        $cAgrega_Dados .= "UF_Transportadora";
        $cAgrega_Dados .= SALTAR_LINHA;

        $cAgrega_Dados .= "000000;";
        $cAgrega_Dados .= "SEM TRANSPORTADORA;";
        $cAgrega_Dados .= "SEM CIDADE;";
        $cAgrega_Dados .= "SEM UF";
        $cAgrega_Dados .= SALTAR_LINHA;

        // Pesquisando dados dos clientes 
        $D022 = mysql_query("SELECT D022_Id,
                                    D022_Nome_Empresa,
                                    D020_Nome_Cidade,
                                    D018_UF
                               FROM D022
                          LEFT JOIN D018 ON D018_Id = D022_D018_Id
                          LEFT JOIN D020 ON D020_Id = D022_D020_Id   
                              WHERE D022_Nome_Empresa!=''
                                AND IFNULL(D022_D020_Id,0)>0
                                AND IFNULL(D022_D018_Id,0)>0
                           ORDER BY D022_Id");
        while($mD022 = mysql_fetch_array($D022)){
            $cAgrega_Dados .= str_pad(trim($mD022['D022_Id']),6,'0',STR_PAD_LEFT).";";
            $cAgrega_Dados .= $this->adicionarEspape(str_replace(';', '', $mD022['D022_Nome_Empresa'])).";";
            $cAgrega_Dados .= trim($mD022['D020_Nome_Cidade']).";";
            $cAgrega_Dados .= trim($mD022['D018_UF']);
            $cAgrega_Dados .= SALTAR_LINHA;
        }

        fwrite($handle, $cAgrega_Dados);
        fclose($handle);

        @$zip->addFile($cArquivo, 'transportadora.csv');

        //--- Fim arquivo transportadora

        //------------------------------------------------------------------
        // Criar arquivo de cidades
        //------------------------------------------------------------------

        /*$dir     = "{$g['pathDadosUsuario']}arq_temp";
        is_dir($dir) OR mkdir($dir, 0777, true);
        $cArquivo = "{$g['pathDadosUsuario']}arq_temp/cidades.csv";

        if (file_exists($cArquivo)) {
            unlink($cArquivo);
        }          

        //-- Cria o arquivo
        if (!$handle = fopen($cArquivo, 'x')) {
            return "Erro ao criar o arquivo: <br />$cArquivo";
        }

        chmod($cArquivo, 0777);

        //variavel contadoras de linhas
        $conta_linha = 1;

        //-- Prepara o campo data de gravacao do arquivo
        $Data_Gravacao = date("dmy", time());

        $cAgrega_Dados = '';

        $cAgrega_Dados  = "Id_Cidade;";
        $cAgrega_Dados .= "Nome_Cidade";
        $cAgrega_Dados .= SALTAR_LINHA;

        $cAgrega_Dados .= "000000";
        $cAgrega_Dados .= "SEM CIDADE";
        $cAgrega_Dados .= SALTAR_LINHA;

        // Pesquisando dados dos clientes 
        $D020 = mysql_query("SELECT D020_Id,
                                    D020_Nome_Cidade
                               FROM D020
                              WHERE D020_Nome_Cidade != ''
                              ORDER BY D020_Id ASC
                            ");
        while($mD020 = mysql_fetch_array($D020)){
            $cAgrega_Dados .= trim($mD020['D020_Id']).";";
            $cAgrega_Dados .= $this->adicionarEspape(str_replace(';', '', $mD020['D020_Nome_Cidade']));
            $cAgrega_Dados .= SALTAR_LINHA;
        }

        fwrite($handle, $cAgrega_Dados);
        fclose($handle);

        @$zip->addFile($cArquivo, 'cidades.csv');*/

        //--- Fim arquivo cidades

        //------------------------------------------------------------------
        // Criar arquivo de estados
        //------------------------------------------------------------------

        /*$dir     = "{$g['pathDadosUsuario']}arq_temp";
        is_dir($dir) OR mkdir($dir, 0777, true);
        $cArquivo = "{$g['pathDadosUsuario']}arq_temp/estados.csv";

        if (file_exists($cArquivo)) {
            unlink($cArquivo);
        }          

        //-- Cria o arquivo
        if (!$handle = fopen($cArquivo, 'x')) {
            return "Erro ao criar o arquivo: <br />$cArquivo";
        }

        chmod($cArquivo, 0777);

        //variavel contadoras de linhas
        $conta_linha = 1;

        //-- Prepara o campo data de gravacao do arquivo
        $Data_Gravacao = date("dmy", time());

        $cAgrega_Dados = '';

        $cAgrega_Dados  = "Id_UF;";
        $cAgrega_Dados .= "UF";
        $cAgrega_Dados .= SALTAR_LINHA;

        $cAgrega_Dados .= "000000";
        $cAgrega_Dados .= "SEM UF";
        $cAgrega_Dados .= SALTAR_LINHA;

        // Pesquisando dados dos clientes 
        $D018 = mysql_query("SELECT D018_Id,
                                    D018_UF
                               FROM D018
                              WHERE D018_UF != ''
                              ORDER BY D018_UF ASC
                            ");
        while($mD018 = mysql_fetch_array($D018)){
            $cAgrega_Dados .= trim($mD018['D018_Id']).";";
            $cAgrega_Dados .= $mD018['D018_UF'];
            $cAgrega_Dados .= SALTAR_LINHA;
        }

        fwrite($handle, $cAgrega_Dados);
        fclose($handle);

        @$zip->addFile($cArquivo, 'estados.csv');

        //--- Fim arquivo estados*/

        //------------------------------------------------------------------
        // Criar arquivo de produtos
        //------------------------------------------------------------------

        $dir     = "{$g['pathDadosUsuario']}arq_temp";
        is_dir($dir) OR mkdir($dir, 0777, true);
        $cArquivo = "{$g['pathDadosUsuario']}arq_temp/produtos.csv";

        if (file_exists($cArquivo)) {
            unlink($cArquivo);
        }          

        //-- Cria o arquivo
        if (!$handle = fopen($cArquivo, 'x')) {
            return "Erro ao criar o arquivo: <br />$cArquivo";
        }

        chmod($cArquivo, 0777);

        //variavel contadoras de linhas
        $conta_linha = 1;

        //-- Prepara o campo data de gravacao do arquivo
        $Data_Gravacao = date("dmy", time());

        $cAgrega_Dados = '';

        $cAgrega_Dados = "Id_Produto;";
        $cAgrega_Dados .= "Codigo_Produto;";
        $cAgrega_Dados .= "Descricao_Produto;";
        $cAgrega_Dados .= "Marca;";
        $cAgrega_Dados .= "Departamento;";
        $cAgrega_Dados .= "Setor;";
        $cAgrega_Dados .= "Grupo;";
        $cAgrega_Dados .= "Sub_Grupo";
        $cAgrega_Dados .= SALTAR_LINHA;

        $cAgrega_Dados .= "000000;";
        $cAgrega_Dados .= "000000;";
        $cAgrega_Dados .= "SEM PRODUTO;";
        $cAgrega_Dados .= "SEM MARCA;";
        $cAgrega_Dados .= "SEM DEPARTAMENTO;";
        $cAgrega_Dados .= "SEM SETOR;";
        $cAgrega_Dados .= "SEM GRUPO;";
        $cAgrega_Dados .= "SEM SUB-GRUPO";
        $cAgrega_Dados .= SALTAR_LINHA;

        // Pesquisando dados dos produtos
        $D001 = mysql_query("SELECT D001_Id,
                                    D001_Codigo_Produto,
                                    D001_Descricao_Produto,
                                    D082_Marca,
                                    C008_Tipo,
                                    D060_Descricao_Grupo, #setor
                                    D003_Nome_Linha, #grupo
                                    D002_Descricao_Produto #Sub-Grupo
                               FROM D001
                          LEFT JOIN D049 ON D049_D001_Id=D001_Id
                          LEFT JOIN D082 ON d082_iD=D049_D082_Id
                          LEFT JOIN C008 ON C008_Id=D001_C008_Id
                          LEFT JOIN D060 ON D060_Id=D001_D060_Id
                          LEFT JOIN D003 ON D003_Id=D001_D003_Id    
                          LEFT JOIN D002 ON D002_Id=D001_D002_Id
                           GROUP BY D001_Codigo_Produto
                           ORDER BY D001_Codigo_Produto");
        while($mD001 = mysql_fetch_array($D001)){
            if (empty($mD001['D001_Codigo_Produto']) || empty($mD001['D001_Descricao_Produto'])) {
                continue;
            }
            $cAgrega_Dados .= str_pad(trim($mD001['D001_Id']),6,'0',STR_PAD_LEFT).";";
            $cAgrega_Dados .= trim($mD001['D001_Codigo_Produto']).";";
            $cAgrega_Dados .= str_replace(';', '', $mD001['D001_Descricao_Produto']).";";
            $cAgrega_Dados .= trim($mD001['D082_Marca']).";";
            $cAgrega_Dados .= trim($mD001['C008_Tipo']).";";
            $cAgrega_Dados .= trim($mD001['D060_Descricao_Grupo']).";";
            $cAgrega_Dados .= trim($mD001['D003_Nome_Linha']).";";
            $cAgrega_Dados .= trim($mD001['D002_Descricao_Produto']);
            $cAgrega_Dados .= SALTAR_LINHA;
        }

        fwrite($handle, $cAgrega_Dados);
        fclose($handle);

        @$zip->addFile($cArquivo, 'produtos.csv');

        //--- Fim arquivo produto

        //------------------------------------------------------------------
        // Criar arquivo de Portadores a Receber
        //------------------------------------------------------------------

        $dir     = "{$g['pathDadosUsuario']}arq_temp";
        is_dir($dir) OR mkdir($dir, 0777, true);
        $cArquivo = "{$g['pathDadosUsuario']}arq_temp/portador_receber.csv";

        if (file_exists($cArquivo)) {
            unlink($cArquivo);
        }          

        //-- Cria o arquivo
        if (!$handle = fopen($cArquivo, 'x')) {
            return "Erro ao criar o arquivo: <br />$cArquivo";
        }

        chmod($cArquivo, 0777);

        //variavel contadoras de linhas
        $conta_linha = 1;

        //-- Prepara o campo data de gravacao do arquivo
        $Data_Gravacao = date("dmy", time());

        $cAgrega_Dados = '';

        $cAgrega_Dados  = "Id_Portador_Receber;";
        $cAgrega_Dados .= "Portador_Receber";
        $cAgrega_Dados .= SALTAR_LINHA;

        $cAgrega_Dados .= "000000";
        $cAgrega_Dados .= "SEM PORTADOR";
        $cAgrega_Dados .= SALTAR_LINHA;

        // Pesquisando dados dos produtos
        $D027 = mysql_query("SELECT D027_Id,
                                    D027_Portador
                               FROM D027
                              WHERE D027_Portador != ''
                           ORDER BY D027_Portador");
        while($mD027 = mysql_fetch_array($D027)){
            $cAgrega_Dados .= str_pad(trim($mD027['D027_Id']),6,'0',STR_PAD_LEFT).";";
            $cAgrega_Dados .= $this->adicionarEspape(str_replace(';', '', $mD027['D027_Portador']));
            $cAgrega_Dados .= SALTAR_LINHA;
        }

        fwrite($handle, $cAgrega_Dados);
        fclose($handle);

        @$zip->addFile($cArquivo, 'portador_receber.csv');

        //--- Fim arquivo PORTADOR RECEBER

        //------------------------------------------------------------------
        // Criar arquivo Contas
        //------------------------------------------------------------------

        $dir     = "{$g['pathDadosUsuario']}arq_temp";
        is_dir($dir) OR mkdir($dir, 0777, true);
        $cArquivo = "{$g['pathDadosUsuario']}arq_temp/contas.csv";

        if (file_exists($cArquivo)) {
            unlink($cArquivo);
        }          

        //-- Cria o arquivo
        if (!$handle = fopen($cArquivo, 'x')) {
            return "Erro ao criar o arquivo: <br />$cArquivo";
        }

        chmod($cArquivo, 0777);

        //variavel contadoras de linhas
        $conta_linha = 1;

        //-- Prepara o campo data de gravacao do arquivo
        $Data_Gravacao = date("dmy", time());

        $cAgrega_Dados = '';

        $cAgrega_Dados  = "Id_Conta;";
        $cAgrega_Dados .= "Conta";
        $cAgrega_Dados .= SALTAR_LINHA;

        $cAgrega_Dados .= "000000";
        $cAgrega_Dados .= "SEM CONTA";
        $cAgrega_Dados .= SALTAR_LINHA;

        // Pesquisando dados dos produtos
        $D014 = mysql_query("SELECT D014_Id,
                                    D014_SubConta
                               FROM D014
                              WHERE D014_SubConta != ''
                           ORDER BY D014_SubConta");
        while($mD014 = mysql_fetch_array($D014)){
            $cAgrega_Dados .= str_pad(trim($mD014['D014_Id']),6,'0',STR_PAD_LEFT).";";
            $cAgrega_Dados .= $this->adicionarEspape(str_replace(';', '', $mD014['D014_SubConta']));
            $cAgrega_Dados .= SALTAR_LINHA;
        }

        fwrite($handle, $cAgrega_Dados);
        fclose($handle);

        @$zip->addFile($cArquivo, 'contas.csv');

        //--- Fim arquivo contas

        //------------------------------------------------------------------
        // Criar arquivo Centro de Custo
        //------------------------------------------------------------------

        $dir     = "{$g['pathDadosUsuario']}arq_temp";
        is_dir($dir) OR mkdir($dir, 0777, true);
        $cArquivo = "{$g['pathDadosUsuario']}arq_temp/centro_custo.csv";

        if (file_exists($cArquivo)) {
            unlink($cArquivo);
        }          

        //-- Cria o arquivo
        if (!$handle = fopen($cArquivo, 'x')) {
            return "Erro ao criar o arquivo: <br />$cArquivo";
        }

        chmod($cArquivo, 0777);

        //variavel contadoras de linhas
        $conta_linha = 1;

        //-- Prepara o campo data de gravacao do arquivo
        $Data_Gravacao = date("dmy", time());

        $cAgrega_Dados = '';

        $cAgrega_Dados  = "Id_Centro_Custo;";
        $cAgrega_Dados .= "Centro_Custo";
        $cAgrega_Dados .= SALTAR_LINHA;

        $cAgrega_Dados .= "000000";
        $cAgrega_Dados .= "SEM CENTRO DE CUSTO";
        $cAgrega_Dados .= SALTAR_LINHA;

        // Pesquisando dados dos produtos
        $D073 = mysql_query("SELECT D073_Id,
                                    D073_Nome
                               FROM D073
                              WHERE D073_Nome != ''
                           ORDER BY D073_Nome");
        while($mD073 = mysql_fetch_array($D073)){
            $cAgrega_Dados .= str_pad(trim($mD073['D073_Id']),6,'0',STR_PAD_LEFT).";";
            $cAgrega_Dados .= $this->adicionarEspape(str_replace(';', '', $mD073['D073_Nome']));
            $cAgrega_Dados .= SALTAR_LINHA;
        }

        fwrite($handle, $cAgrega_Dados);
        fclose($handle);

        @$zip->addFile($cArquivo, 'centro_custo.csv');

        //--- Fim arquivo centro de custos

        //------------------------------------------------------------------
        // Criar arquivo Portador a Pagar
        //------------------------------------------------------------------

        $dir     = "{$g['pathDadosUsuario']}arq_temp";
        is_dir($dir) OR mkdir($dir, 0777, true);
        $cArquivo = "{$g['pathDadosUsuario']}arq_temp/portador_pagar.csv";

        if (file_exists($cArquivo)) {
            unlink($cArquivo);
        }          

        //-- Cria o arquivo
        if (!$handle = fopen($cArquivo, 'x')) {
            return "Erro ao criar o arquivo: <br />$cArquivo";
        }

        chmod($cArquivo, 0777);

        //variavel contadoras de linhas
        $conta_linha = 1;

        //-- Prepara o campo data de gravacao do arquivo
        $Data_Gravacao = date("dmy", time());

        $cAgrega_Dados = '';

        $cAgrega_Dados  = "Id_Portador_Pagar;";
        $cAgrega_Dados .= "Portador_Pagar";
        $cAgrega_Dados .= SALTAR_LINHA;

        $cAgrega_Dados .= "000000";
        $cAgrega_Dados .= "SEM PORTADOR";
        $cAgrega_Dados .= SALTAR_LINHA;

        // Pesquisando dados dos produtos
        $D031 = mysql_query("SELECT D031_Id,
                                    D031_Portador
                               FROM D031
                              WHERE D031_Portador != ''
                           ORDER BY D031_Portador");
        while($mD031 = mysql_fetch_array($D031)){
            $cAgrega_Dados .= str_pad(trim($mD031['D031_Id']),6,'0',STR_PAD_LEFT).";";
            $cAgrega_Dados .= $this->adicionarEspape(str_replace(';', '', $mD031['D031_Portador']));
            $cAgrega_Dados .= SALTAR_LINHA;
        }

        fwrite($handle, $cAgrega_Dados);
        fclose($handle);

        @$zip->addFile($cArquivo, 'portador_pagar.csv');

        //--- Fim arquivo portador pagar


        //------------------------------------------------------------------
        // Criar arquivo de notas fiscais emitidas
        //------------------------------------------------------------------

        $dir     = "{$g['pathDadosUsuario']}arq_temp";
        is_dir($dir) OR mkdir($dir, 0777, true);
        $cArquivo = "{$g['pathDadosUsuario']}arq_temp/notas_emitidas.csv";

        if (file_exists($cArquivo)) {
            unlink($cArquivo);
        }          

        //-- Cria o arquivo
        if (!$handle = fopen($cArquivo, 'x')) {
            return "Erro ao criar o arquivo: <br />$cArquivo";
        }

        chmod($cArquivo, 0777);

        //variavel contadoras de linhas
        $conta_linha = 1;

        //-- Prepara o campo data de gravacao do arquivo
        $Data_Gravacao = date("dmy", time());

        $cAgrega_Dados = '';

        $cAgrega_Dados .= "Id_Empresa;";
        $cAgrega_Dados .= "Id_NF;";
        $cAgrega_Dados .= "Num_NF;";
        $cAgrega_Dados .= "Codigo_Usuario;";
        $cAgrega_Dados .= "Codigo_Vendedor_Interno;";
        $cAgrega_Dados .= "Codigo_Vendedor_Externo;";
        $cAgrega_Dados .= "Codigo_Supervisor;";
        $cAgrega_Dados .= "Codigo_Supervisor2;";
        $cAgrega_Dados .= "Codigo_Vendedor_Substituto;";
        $cAgrega_Dados .= "Codigo_Cliente;";
        $cAgrega_Dados .= "Emissao_NF;";
        $cAgrega_Dados .= "Valor_Total_NF";
        $cAgrega_Dados .= SALTAR_LINHA;

        // Pesquisando dados dos clientes 
        $T007 = mysql_query("SELECT T007_C004_Id,
                                    T007_Id,
                                    T007_Numero_Nota_Fiscal,
                                    usuario.C007_Id as usuario,
                                    vendedor.C007_Id as vendedor,
                                    externo.C007_Id as externo,
                                    supervisor1.C007_Id as supervisor1,
                                    supervisor2.C007_Id as supervisor2,
                                    substituto.C007_Id as substituto,
                                    T007_D024_Id,
                                    T007_Data_Emissao,
                                    T007_Valor_Total
                               FROM T007
                          LEFT JOIN C007 AS usuario ON usuario.C007_Id=T007_C007_Id
                          LEFT JOIN C007 AS vendedor ON vendedor.C007_Id=T007_C007_Id_Vendedor_Interno
                          LEFT JOIN C007 AS supervisor1 ON supervisor1.C007_Id=vendedor.C007_C007_Id
                          LEFT JOIN C007 AS supervisor2 ON supervisor2.C007_Id=vendedor.C007_C007_Id_2
                          LEFT JOIN C007 AS externo ON externo.C007_Id=T007_C007_Id_Vendedor_Externo
                          LEFT JOIN C007 AS substituto ON substituto.C007_Id=vendedor.C007_Id_Vendedor_Substituto
                              WHERE T007_Data_Emissao>='{$xData_Inicio}' 
                                AND T007_Data_Emissao<='{$xData_Fim}'
                                AND T007_Flag_Entrada_Saida = 'S'
                                AND T007_Flag_Cancelada != 'S'
                           ORDER BY T007_Id");

        while($mT007 = mysql_fetch_array($T007)){
            $cAgrega_Dados .= str_pad(trim($mT007['T007_C004_Id']),6,'0',STR_PAD_LEFT).";";
            $cAgrega_Dados .= str_pad(trim($mT007['T007_Id']),6,'0',STR_PAD_LEFT).";";
            $cAgrega_Dados .= trim($mT007['T007_Numero_Nota_Fiscal']).";";
            $cAgrega_Dados .= str_pad(trim($mT007['usuario']),6,'0',STR_PAD_LEFT).";";
            $cAgrega_Dados .= str_pad(trim($mT007['vendedor']),6,'0',STR_PAD_LEFT).";";
            $cAgrega_Dados .= str_pad(trim($mT007['externo']),6,'0',STR_PAD_LEFT).";";
            $cAgrega_Dados .= str_pad(trim($mT007['supervisor1']),6,'0',STR_PAD_LEFT).";";
            $cAgrega_Dados .= str_pad(trim($mT007['supervisor2']),6,'0',STR_PAD_LEFT).";";
            $cAgrega_Dados .= str_pad(trim($mT007['substituto']),6,'0',STR_PAD_LEFT).";";
            $cAgrega_Dados .= str_pad(trim($mT007['T007_D024_Id']),6,'0',STR_PAD_LEFT).";";
            $cAgrega_Dados .= gCorrigeData($mT007['T007_Data_Emissao']).";";
            $cAgrega_Dados .= gCorrigeNumero($mT007['T007_Valor_Total']);
            $cAgrega_Dados .= SALTAR_LINHA;
        }  

        fwrite($handle, $cAgrega_Dados);
        fclose($handle);

        @$zip->addFile($cArquivo, 'notas_emitidas.csv');

        //--- Fim arquivo nota fiscal emitida

        //------------------------------------------------------------------
        // Criar arquivo de produtos notas fiscais emitidas
        //------------------------------------------------------------------

        $dir     = "{$g['pathDadosUsuario']}arq_temp";
        is_dir($dir) OR mkdir($dir, 0777, true);
        $cArquivo = "{$g['pathDadosUsuario']}arq_temp/notas_emitidas_produtos.csv";

        if (file_exists($cArquivo)) {
            unlink($cArquivo);
        }          

        //-- Cria o arquivo
        if (!$handle = fopen($cArquivo, 'x')) {
            return "Erro ao criar o arquivo: <br />$cArquivo";
        }

        chmod($cArquivo, 0777);

        //variavel contadoras de linhas
        $conta_linha = 1;

        //-- Prepara o campo data de gravacao do arquivo
        $Data_Gravacao = date("dmy", time());

        $cAgrega_Dados = '';

        $cAgrega_Dados .= "Id_NF;";
        $cAgrega_Dados .= "Id_Produto;";
        $cAgrega_Dados .= "Qtde_NF;";
        $cAgrega_Dados .= "Preco_Unitario_NF;";
        $cAgrega_Dados .= "Preco_Total_NF;";
        $cAgrega_Dados .= "tipo_cfop";
        $cAgrega_Dados .= SALTAR_LINHA;

        // Pesquisando dados dos clientes 
        $T008 = mysql_query("SELECT T008_T007_Id,
                                    D001_Id,
                                    T008_Quantidade,
                                    T008_Valor_Preco_Unitario,
                                    T008_Valor_Total_Preco,
                                    D006_Flag_Venda_Compra_Outros
                               FROM T008
                          LEFT JOIN T007 ON T007_Id=T008_T007_Id
                          LEFT JOIN D009 ON D009_Id=T008_D009_Id
                          LEFT JOIN D049 ON D049_Id=D009_D049_Id 
                          LEFT JOIN D001 ON D001_Id=D049_D001_Id
                          LEFT JOIN D006 ON D006_Id=T008_D006_Id
                              WHERE T007_Data_Emissao>='{$xData_Inicio}' 
                                AND T007_Data_Emissao<='{$xData_Fim}'
                                AND T007_Flag_Entrada_Saida = 'S'
                                AND T007_Flag_Cancelada != 'S'
                        ORDER BY T008_T007_Id");

        while($mT008 = mysql_fetch_array($T008)){
            $cAgrega_Dados .= str_pad(trim($mT008['T008_T007_Id']),6,'0',STR_PAD_LEFT).";";
            $cAgrega_Dados .= str_pad(trim($mT008['D001_Id']),6,'0',STR_PAD_LEFT).";";
            $cAgrega_Dados .= gCorrigeNumero($mT008['T008_Quantidade']).";";
            $cAgrega_Dados .= gCorrigeNumero($mT008['T008_Valor_Preco_Unitario']).";";
            $cAgrega_Dados .= gCorrigeNumero($mT008['T008_Valor_Total_Preco']).";";
            $cAgrega_Dados .= $mT008['D006_Flag_Venda_Compra_Outros'];
            $cAgrega_Dados .= SALTAR_LINHA;
        }

        fwrite($handle, $cAgrega_Dados);
        fclose($handle);

        @$zip->addFile($cArquivo, 'notas_emitidas_produtos.csv');

        //--- Fim arquivo produto nota fiscal emitida

        //------------------------------------------------------------------
        // Criar arquivo de orçamentos
        //------------------------------------------------------------------

        $dir     = "{$g['pathDadosUsuario']}arq_temp";
        is_dir($dir) OR mkdir($dir, 0777, true);
        $cArquivo = "{$g['pathDadosUsuario']}arq_temp/orcamentos.csv";

        if (file_exists($cArquivo)) {
            unlink($cArquivo);
        }          

        //-- Cria o arquivo
        if (!$handle = fopen($cArquivo, 'x')) {
            return "Erro ao criar o arquivo: <br />$cArquivo";
        }

        chmod($cArquivo, 0777);

        //variavel contadoras de linhas
        $conta_linha = 1;

        //-- Prepara o campo data de gravacao do arquivo
        $Data_Gravacao = date("dmy", time());

        $cAgrega_Dados = '';
        $cAgrega_Dados .= "Num_Orc;";
        $cAgrega_Dados .= "Codigo_Cliente;";
        $cAgrega_Dados .= "Emissao_Orc;";
        $cAgrega_Dados .= "Valor_Total_Orc";
        $cAgrega_Dados .= SALTAR_LINHA;

        // Pesquisando dados dos clientes 
        $T003 = mysql_query("SELECT T003_Id,
                                    T003_D024_Id,
                                    T003_Data_Emissao,
                                    T003_Valor_Total
                               FROM T003
                              WHERE T003_Data_Emissao>='{$xData_Inicio}' 
                                AND T003_Data_Emissao<='{$xData_Fim}'
                            ORDER BY T003_Id");

        while($mT003 = mysql_fetch_array($T003)){
            $cAgrega_Dados .= str_pad(trim($mT003['T003_Id']),6,'0',STR_PAD_LEFT).";";
            $cAgrega_Dados .= str_pad(trim($mT003['T003_D024_Id']),6,'0',STR_PAD_LEFT).";";
            $cAgrega_Dados .= gCorrigeData($mT003['T003_Data_Emissao']).";";
            $cAgrega_Dados .= gCorrigeNumero($mT003['T003_Valor_Total']);
            $cAgrega_Dados .= SALTAR_LINHA;
        }

        fwrite($handle, $cAgrega_Dados);
        fclose($handle);

        @$zip->addFile($cArquivo, 'orcamentos.csv');

        //--- Fim arquivo orçamento

        //------------------------------------------------------------------
        // Criar arquivo de produtos notas fiscais emitidas
        //------------------------------------------------------------------

        $dir     = "{$g['pathDadosUsuario']}arq_temp";
        is_dir($dir) OR mkdir($dir, 0777, true);
        $cArquivo = "{$g['pathDadosUsuario']}arq_temp/orcamentos_produtos.csv";

        if (file_exists($cArquivo)) {
            unlink($cArquivo);
        }          

        //-- Cria o arquivo
        if (!$handle = fopen($cArquivo, 'x')) {
            return "Erro ao criar o arquivo: <br />$cArquivo";
        }

        chmod($cArquivo, 0777);

        //variavel contadoras de linhas
        $conta_linha = 1;

        //-- Prepara o campo data de gravacao do arquivo
        $Data_Gravacao = date("dmy", time());

        $cAgrega_Dados = '';

        $cAgrega_Dados .= "Num_Orc;";
        $cAgrega_Dados .= "Id_Produto;";
        $cAgrega_Dados .= "Qtde_Orc;";
        $cAgrega_Dados .= "Preco_Unitario_Orc;";
        $cAgrega_Dados .= "Preco_Total_Orc";
        $cAgrega_Dados .= SALTAR_LINHA;

        // Pesquisando dados dos clientes 
        $T004 = mysql_query("SELECT T004_T003_Id,
                                    D001_Id,
                                    T004_Quantidade,
                                    T004_Valor_Preco_Unitario,
                                    T004_Valor_Total_Preco
                               FROM T004
                          LEFT JOIN T003 ON T003_Id=T004_T003_Id
                          LEFT JOIN D009 ON D009_Id=T004_D009_Id
                          LEFT JOIN D049 ON D049_Id=D009_D049_Id
                          LEFT JOIN D001 ON D001_Id=D049_D001_Id
                              WHERE T003_Data_Emissao>='{$xData_Inicio}' 
                                AND T003_Data_Emissao<='{$xData_Fim}'
                           ORDER BY T004_T003_Id");

        while($mT004 = mysql_fetch_array($T004)){
            $cAgrega_Dados .= str_pad(trim($mT004['T004_T003_Id']),6,'0',STR_PAD_LEFT).";";
            $cAgrega_Dados .= str_pad(trim($mT004['D001_Id']),6,'0',STR_PAD_LEFT).";";
            $cAgrega_Dados .= gCorrigeNumero($mT004['T004_Quantidade']).";";
            $cAgrega_Dados .= gCorrigeNumero($mT004['T004_Valor_Preco_Unitario']).";";
            $cAgrega_Dados .= gCorrigeNumero($mT004['T004_Valor_Total_Preco']);
            $cAgrega_Dados .= SALTAR_LINHA;
        }

        fwrite($handle, $cAgrega_Dados);
        fclose($handle);

        @$zip->addFile($cArquivo, 'orcamentos_produtos.csv');

        //--- Fim arquivo produto orçamento

        //------------------------------------------------------------------
        // Criar arquivo de pedidos
        //------------------------------------------------------------------

        $dir     = "{$g['pathDadosUsuario']}arq_temp";
        is_dir($dir) OR mkdir($dir, 0777, true);
        $cArquivo = "{$g['pathDadosUsuario']}arq_temp/pedidos.csv";

        if (file_exists($cArquivo)) {
            unlink($cArquivo);
        }          

        //-- Cria o arquivo
        if (!$handle = fopen($cArquivo, 'x')) {
            return "Erro ao criar o arquivo: <br />$cArquivo";
        }

        chmod($cArquivo, 0777);

        //variavel contadoras de linhas
        $conta_linha = 1;

        //-- Prepara o campo data de gravacao do arquivo
        $Data_Gravacao = date("dmy", time());

        $cAgrega_Dados = '';

        $cAgrega_Dados .= "Num_Ped;";
        $cAgrega_Dados .= "Codigo_Cliente;";
        $cAgrega_Dados .= "Emissao_Ped;";
        $cAgrega_Dados .= "Valor_Total_Ped";
        $cAgrega_Dados .= SALTAR_LINHA;

        // Pesquisando dados dos clientes 
        $T005 = mysql_query("SELECT T005_Id,
                                T005_D024_Id,
                                T005_Data_Emissao,
                                T005_Valor_Total
                           FROM T005
                          WHERE T005_Data_Emissao>='{$xData_Inicio}' 
                            AND T005_Data_Emissao<='{$xData_Fim}'
                            AND (T005_T005_Id_Agrupado<=0 OR T005_T005_Id_Agrupado IS NULL)
                       ORDER BY T005_Id");
        
        while($mT005 = mysql_fetch_array($T005)){
            $cAgrega_Dados .= str_pad(trim($mT005['T005_Id']),6,'0',STR_PAD_LEFT).";";
            $cAgrega_Dados .= str_pad(trim($mT005['T005_D024_Id']),6,'0',STR_PAD_LEFT).";";
            $cAgrega_Dados .= gCorrigeData($mT005['T005_Data_Emissao']).";";
            $cAgrega_Dados .= gCorrigeNumero($mT005['T005_Valor_Total']);
            $cAgrega_Dados .= SALTAR_LINHA;
        }

        fwrite($handle, $cAgrega_Dados);
        fclose($handle);

        @$zip->addFile($cArquivo, 'pedidos.csv');

        //--- Fim arquivo pedidos

        //------------------------------------------------------------------
        // Criar arquivo de produtos pedidos
        //------------------------------------------------------------------

        $dir     = "{$g['pathDadosUsuario']}arq_temp";
        is_dir($dir) OR mkdir($dir, 0777, true);
        $cArquivo = "{$g['pathDadosUsuario']}arq_temp/pedidos_produtos.csv";

        if (file_exists($cArquivo)) {
            unlink($cArquivo);
        }          

        //-- Cria o arquivo
        if (!$handle = fopen($cArquivo, 'x')) {
            return "Erro ao criar o arquivo: <br />$cArquivo";
        }

        chmod($cArquivo, 0777);

        //variavel contadoras de linhas
        $conta_linha = 1;

        //-- Prepara o campo data de gravacao do arquivo
        $Data_Gravacao = date("dmy", time());

        $cAgrega_Dados = '';

        $cAgrega_Dados .= "Num_Ped;";
        $cAgrega_Dados .= "Id_Produto;";
        $cAgrega_Dados .= "Qtde_Ped;";
        $cAgrega_Dados .= "Preco_Unitario_Ped;";
        $cAgrega_Dados .= "Preco_Total_Ped";
        $cAgrega_Dados .= SALTAR_LINHA;

        $T006 = mysql_query("SELECT T006_T005_Id,
                                    D001_Id,
                                    T006_Quantidade,
                                    T006_Valor_Preco_Unitario,
                                    T006_Valor_Total_Preco
                               FROM T006
                          LEFT JOIN T005 ON T005_Id=T006_T005_Id
                          LEFT JOIN D009 ON D009_Id=T006_D009_Id
                          LEFT JOIN D049 ON D049_Id=D009_D049_Id
                          LEFT JOIN D001 ON D001_Id=D049_D001_Id
                              WHERE T005_Data_Emissao>='{$xData_Inicio}' 
                                AND T005_Data_Emissao<='{$xData_Fim}'
                           ORDER BY T006_T005_Id");

        while($mT006 = mysql_fetch_array($T006)){
            $cAgrega_Dados .= str_pad(trim($mT006['T006_T005_Id']),6,'0',STR_PAD_LEFT).";";
            $cAgrega_Dados .= str_pad(trim($mT006['D001_Id']),6,'0',STR_PAD_LEFT).";";
            $cAgrega_Dados .= gCorrigeNumero($mT006['T006_Quantidade']).";";
            $cAgrega_Dados .= gCorrigeNumero($mT006['T006_Valor_Preco_Unitario']).";";
            $cAgrega_Dados .= gCorrigeNumero($mT006['T006_Valor_Total_Preco']);
            $cAgrega_Dados .= SALTAR_LINHA;
        }

        fwrite($handle, $cAgrega_Dados);
        fclose($handle);

        @$zip->addFile($cArquivo, 'pedidos_produtos.csv');

        //--- Fim arquivo produto pedidos

        //------------------------------------------------------------------
        // Criar arquivo de cotação
        //------------------------------------------------------------------

        $dir     = "{$g['pathDadosUsuario']}arq_temp";
        is_dir($dir) OR mkdir($dir, 0777, true);
        $cArquivo = "{$g['pathDadosUsuario']}arq_temp/cotacao.csv";

        if (file_exists($cArquivo)) {
            unlink($cArquivo);
        }          

        //-- Cria o arquivo
        if (!$handle = fopen($cArquivo, 'x')) {
            return "Erro ao criar o arquivo: <br />$cArquivo";
        }

        chmod($cArquivo, 0777);

        //variavel contadoras de linhas
        $conta_linha = 1;

        //-- Prepara o campo data de gravacao do arquivo
        $Data_Gravacao = date("dmy", time());

        $cAgrega_Dados = '';

        $cAgrega_Dados .= "Num_Cot;";
        $cAgrega_Dados .= "Codigo_Cliente;";
        $cAgrega_Dados .= "Emissao_Cot;";
        $cAgrega_Dados .= "Valor_Total_Cot";
        $cAgrega_Dados .= SALTAR_LINHA;

        $T218 = mysql_query("SELECT T218_Id,
                                    T218_D024_Id,
                                    T218_Data_Emissao,
                                    T218_Valor_Total
                               FROM T218
                              WHERE T218_Data_Emissao>='{$xData_Inicio}' 
                                AND T218_Data_Emissao<='{$xData_Fim}'
                           ORDER BY T218_Id");

        while($mT218 = mysql_fetch_array($T218)){
            $cAgrega_Dados .= str_pad(trim($mT218['T218_Id']),6,'0',STR_PAD_LEFT).";";
            $cAgrega_Dados .= str_pad(trim($mT218['T218_D024_Id']),6,'0',STR_PAD_LEFT).";";
            $cAgrega_Dados .= gCorrigeData($mT218['T218_Data_Emissao']).";";
            $cAgrega_Dados .= gCorrigeNumero($mT218['T218_Valor_Total']);
            $cAgrega_Dados .= SALTAR_LINHA;
        }

        fwrite($handle, $cAgrega_Dados);
        fclose($handle);

        @$zip->addFile($cArquivo, 'cotacao.csv');

        //--- Fim arquivo cotação

        //------------------------------------------------------------------
        // Criar arquivo de produtos cotacao
        //------------------------------------------------------------------

        $dir     = "{$g['pathDadosUsuario']}arq_temp";
        is_dir($dir) OR mkdir($dir, 0777, true);
        $cArquivo = "{$g['pathDadosUsuario']}arq_temp/cotacao_produtos.csv";

        if (file_exists($cArquivo)) {
            unlink($cArquivo);
        }          

        //-- Cria o arquivo
        if (!$handle = fopen($cArquivo, 'x')) {
            return "Erro ao criar o arquivo: <br />$cArquivo";
        }

        chmod($cArquivo, 0777);

        //variavel contadoras de linhas
        $conta_linha = 1;

        //-- Prepara o campo data de gravacao do arquivo
        $Data_Gravacao = date("dmy", time());

        $cAgrega_Dados = '';

        $cAgrega_Dados .= "Num_Cot;";
        $cAgrega_Dados .= "Num_Cot_Produto;";
        $cAgrega_Dados .= "Id_Produto;";
        $cAgrega_Dados .= "Qtde_Cot;";
        $cAgrega_Dados .= "Preco_Unitario_Cot;";
        $cAgrega_Dados .= "Preco_Total_Cot";
        $cAgrega_Dados .= SALTAR_LINHA;

        // Pesquisando dados dos clientes 
        $T219 = mysql_query("SELECT T219_T218_Id,
                                    T219_Id,
                                    D001_Id,
                                    T219_Quantidade,
                                    T219_Valor_Preco_Unitario,
                                    T219_Valor_Total_Preco
                               FROM T219
                          LEFT JOIN T218 ON T218_Id=T219_T218_Id
                          LEFT JOIN D009 ON D009_Id=T219_D009_Id
                          LEFT JOIN D049 ON D049_Id=D009_D049_Id
                          LEFT JOIN D001 ON D001_Id=D049_D001_Id
                              WHERE T218_Data_Emissao>='{$xData_Inicio}' 
                                AND T218_Data_Emissao<='{$xData_Fim}'
                                AND T219_Flag_Cancelado='N'");

        while($mT219 = mysql_fetch_array($T219)){
            $cAgrega_Dados .= str_pad(trim($mT219['T219_T218_Id']),6,'0',STR_PAD_LEFT).";";
            $cAgrega_Dados .= str_pad(trim($mT219['T219_Id']),6,'0',STR_PAD_LEFT).";";
            $cAgrega_Dados .= str_pad(trim($mT219['D001_Id']),6,'0',STR_PAD_LEFT).";";
            $cAgrega_Dados .= gCorrigeNumero($mT219['T219_Quantidade']).";";
            $cAgrega_Dados .= gCorrigeNumero($mT219['T219_Valor_Preco_Unitario']).";";
            $cAgrega_Dados .= gCorrigeNumero($mT219['T219_Valor_Total_Preco']);
            $cAgrega_Dados .= SALTAR_LINHA;
        }

        fwrite($handle, $cAgrega_Dados);
        fclose($handle);

        @$zip->addFile($cArquivo, 'cotacao_produtos.csv');

        //--- Fim arquivo produto cotacao

        //------------------------------------------------------------------
        // Criar arquivo de ordem de compra
        //------------------------------------------------------------------

        $dir     = "{$g['pathDadosUsuario']}arq_temp";
        is_dir($dir) OR mkdir($dir, 0777, true);
        $cArquivo = "{$g['pathDadosUsuario']}arq_temp/ordem_compra.csv";
        if (file_exists($cArquivo)) {
            unlink($cArquivo);
        }          

        //-- Cria o arquivo
        if (!$handle = fopen($cArquivo, 'x')) {
            return "Erro ao criar o arquivo: <br />$cArquivo";
        }

        chmod($cArquivo, 0777);

        //variavel contadoras de linhas
        $conta_linha = 1;

        //-- Prepara o campo data de gravacao do arquivo
        $Data_Gravacao = date("dmy", time());

        $cAgrega_Dados = '';

        $cAgrega_Dados .= "Num_OC;";
        $cAgrega_Dados .= "Codigo_Cliente;";
        $cAgrega_Dados .= "Emissao_OC;";
        $cAgrega_Dados .= "Valor_Total_OC";
        $cAgrega_Dados .= SALTAR_LINHA;

        $cAgrega_Dados .= "000000";
        $cAgrega_Dados .= "000000";
        $cAgrega_Dados .= "01/01/1200;";
        $cAgrega_Dados .= "0,00";
        $cAgrega_Dados .= SALTAR_LINHA;

        $T224 = mysql_query("SELECT T224_Id,
                                    T224_D024_Id,
                                    T224_Data_Emissao,
                                    T224_Valor_Total
                               FROM T224
                              WHERE T224_Data_Emissao>='{$xData_Inicio}' 
                                AND T224_Data_Emissao<='{$xData_Fim}'
                           ORDER BY T224_Id");

        while($mT224 = mysql_fetch_array($T224)){
            $cAgrega_Dados .= str_pad(trim($mT224['T224_Id']),6,'0',STR_PAD_LEFT).";";
            $cAgrega_Dados .= str_pad(trim($mT224['T224_D024_Id']),6,'0',STR_PAD_LEFT).";";
            $cAgrega_Dados .= gCorrigeData($mT224['T224_Data_Emissao']).";";
            $cAgrega_Dados .= gCorrigeNumero($mT224['T224_Valor_Total']);
            $cAgrega_Dados .= SALTAR_LINHA;
        }

        fwrite($handle, $cAgrega_Dados);
        fclose($handle);

        @$zip->addFile($cArquivo, 'ordem_compra.csv');

        //--- Fim arquivo ordem de compra

        //------------------------------------------------------------------
        // Criar arquivo de produtos ordem de compra
        //------------------------------------------------------------------

        $dir     = "{$g['pathDadosUsuario']}arq_temp";
        is_dir($dir) OR mkdir($dir, 0777, true);
        $cArquivo = "{$g['pathDadosUsuario']}arq_temp/ordem_compra_produtos.csv";

        if (file_exists($cArquivo)) {
            unlink($cArquivo);
        }          

        //-- Cria o arquivo
        if (!$handle = fopen($cArquivo, 'x')) {
            return "Erro ao criar o arquivo: <br />$cArquivo";
        }

        chmod($cArquivo, 0777);

        //variavel contadoras de linhas
        $conta_linha = 1;

        //-- Prepara o campo data de gravacao do arquivo
        $Data_Gravacao = date("dmy", time());

        $cAgrega_Dados = '';

        $cAgrega_Dados .= "Num_OC;";
        $cAgrega_Dados .= "Num_OC_Produto;";
        $cAgrega_Dados .= "Id_Produto;";
        $cAgrega_Dados .= "Qtde_OC;";
        $cAgrega_Dados .= "Preco_Unitario_OC;";
        $cAgrega_Dados .= "Preco_Total_OC";
        $cAgrega_Dados .= SALTAR_LINHA;

        $cAgrega_Dados .= "000000";
        $cAgrega_Dados .= "000000";
        $cAgrega_Dados .= "000000";
        $cAgrega_Dados .= "000000";
        $cAgrega_Dados .= "000000";
        $cAgrega_Dados .= "0";
        $cAgrega_Dados .= SALTAR_LINHA;

        // Pesquisando dados dos clientes 
        $T225 = mysql_query("SELECT T225_T224_Id,
                                    T225_Id,
                                    D001_Id,
                                    T225_Quantidade,
                                    T225_Valor_Preco_Unitario,
                                    T225_Valor_Total_Preco
                               FROM T225
                          LEFT JOIN T224 ON T224_Id=T225_T224_Id
                          LEFT JOIN D009 ON D009_Id=T225_D009_Id
                          LEFT JOIN D049 ON D049_Id=D009_D049_Id 
                          LEFT JOIN D001 ON D001_Id=D049_D001_Id
                              WHERE T224_Data_Emissao>='{$xData_Inicio}' 
                                AND T224_Data_Emissao<='{$xData_Fim}'
                                AND T225_Flag_Cancelada='N'
                           ORDER BY T225_T224_Id");

        while($mT225 = mysql_fetch_array($T225)){
            $cAgrega_Dados .= str_pad(trim($mT225['T225_T224_Id']),6,'0',STR_PAD_LEFT).";";
            $cAgrega_Dados .= str_pad(trim($mT225['T225_Id']),6,'0',STR_PAD_LEFT).";";
            $cAgrega_Dados .= str_pad(trim($mT225['D001_Id']),6,'0',STR_PAD_LEFT).";";
            $cAgrega_Dados .= gCorrigeNumero($mT225['T225_Quantidade']).";";
            $cAgrega_Dados .= gCorrigeNumero($mT225['T225_Valor_Preco_Unitario']).";";
            $cAgrega_Dados .= gCorrigeNumero($mT225['T225_Valor_Total_Preco']);
            $cAgrega_Dados .= SALTAR_LINHA;
        }

        fwrite($handle, $cAgrega_Dados);
        fclose($handle);

        @$zip->addFile($cArquivo, 'ordem_compra_produtos.csv');

        //--- Fim arquivo produto cotacao

        //------------------------------------------------------------------
        // Criar arquivo de conhecimentos
        //------------------------------------------------------------------

        $dir     = "{$g['pathDadosUsuario']}arq_temp";
        is_dir($dir) OR mkdir($dir, 0777, true);
        $cArquivo = "{$g['pathDadosUsuario']}arq_temp/conhecimentos.csv";
        if (file_exists($cArquivo)) {
            unlink($cArquivo);
        }          

        //-- Cria o arquivo
        if (!$handle = fopen($cArquivo, 'x')) {
            return "Erro ao criar o arquivo: <br />$cArquivo";
        }

        chmod($cArquivo, 0777);

        //variavel contadoras de linhas
        $conta_linha = 1;

        //-- Prepara o campo data de gravacao do arquivo
        $Data_Gravacao = date("dmy", time());

        $cAgrega_Dados = '';

        $cAgrega_Dados .= "Id_Conhecimento;";
        $cAgrega_Dados .= "Num_Conhecimento;";
        $cAgrega_Dados .= "Codigo_Cliente;";
        $cAgrega_Dados .= "Codigo_Transportadora;";
        $cAgrega_Dados .= "Emissao_Conhecimento;";
        $cAgrega_Dados .= "Valor_Total_Conhecimento";
        $cAgrega_Dados .= SALTAR_LINHA;

        $T084 = mysql_query("SELECT T084_Id,
                                    T084_Numero_Conhecimento,
                                    T084_D024_Id,
                                    T084_D022_Id,
                                    T084_Data_Emissao,
                                    T084_Valor_Conhecimento
                               FROM T084
                              WHERE T084_Data_Emissao>='{$xData_Inicio}' 
                                AND T084_Data_Emissao<='{$xData_Fim}'");

        while($mT084 = mysql_fetch_array($T084)){
            $cAgrega_Dados .= str_pad(trim($mT084['T084_Id']),6,'0',STR_PAD_LEFT).";";
            $cAgrega_Dados .= trim($mT084['T084_Numero_Conhecimento']).";";
            $cAgrega_Dados .= str_pad(trim($mT084['T084_D024_Id']),6,'0',STR_PAD_LEFT).";";
            $cAgrega_Dados .= str_pad(trim($mT084['T084_D022_Id']),6,'0',STR_PAD_LEFT).";";
            $cAgrega_Dados .= gCorrigeData($mT084['T084_Data_Emissao']).";";
            $cAgrega_Dados .= gCorrigeNumero($mT084['T084_Valor_Conhecimento']);
            $cAgrega_Dados .= SALTAR_LINHA;
        }

        fwrite($handle, $cAgrega_Dados);
        fclose($handle);

        @$zip->addFile($cArquivo, 'conhecimentos.csv');

        //------------------------------------------------------------------
        // Criar arquivo de notas fiscais recebidas
        //------------------------------------------------------------------

        $dir     = "{$g['pathDadosUsuario']}arq_temp";
        is_dir($dir) OR mkdir($dir, 0777, true);
        $cArquivo = "{$g['pathDadosUsuario']}arq_temp/notas_recebidas.csv";

        if (file_exists($cArquivo)) {
            unlink($cArquivo);
        }          

        //-- Cria o arquivo
        if (!$handle = fopen($cArquivo, 'x')) {
            return "Erro ao criar o arquivo: <br />$cArquivo";
        }

        chmod($cArquivo, 0777);

        //variavel contadoras de linhas
        $conta_linha = 1;

        //-- Prepara o campo data de gravacao do arquivo
        $Data_Gravacao = date("dmy", time());

        $cAgrega_Dados = '';

        $cAgrega_Dados .= "Id_NF_Recebida;";
        $cAgrega_Dados .= "Num_NF_Recebida;";
        $cAgrega_Dados .= "Codigo_Cliente;";
        $cAgrega_Dados .= "Emissao_NF_Recebida;";
        $cAgrega_Dados .= "Valor_Total_NF_Recebida";
        $cAgrega_Dados .= SALTAR_LINHA;

        // Pesquisando dados dos clientes 
        $T013 = mysql_query("SELECT T013_Id,
                                    T013_Numero_Nota_Fiscal,
                                    T013_D024_Id,
                                    T013_Data_Emissao,
                                    T013_Valor_Total
                               FROM T013
                              WHERE T013_Data_Emissao>='{$xData_Inicio}' 
                                AND T013_Data_Emissao<='{$xData_Fim}'
                                AND T013_Numero_Nota_Fiscal!=''
                           ORDER BY T013_Id");

        while($mT013 = mysql_fetch_array($T013)){
            $cAgrega_Dados .= str_pad(trim($mT013['T013_Id']),6,'0',STR_PAD_LEFT).";";
            $cAgrega_Dados .= trim($mT013['T013_Numero_Nota_Fiscal']).";";
            $cAgrega_Dados .= str_pad(trim($mT013['T013_D024_Id']),6,'0',STR_PAD_LEFT).";";
            $cAgrega_Dados .= gCorrigeData($mT013['T013_Data_Emissao']).";";
            $cAgrega_Dados .= gCorrigeNumero($mT013['T013_Valor_Total']);
            $cAgrega_Dados .= SALTAR_LINHA;
        }

        fwrite($handle, $cAgrega_Dados);
        fclose($handle);

        @$zip->addFile($cArquivo, 'notas_recebidas.csv');

        //--- Fim arquivo nota fiscal emitida

        //------------------------------------------------------------------
        // Criar arquivo de produtos notas fiscais recebida
        //------------------------------------------------------------------

        $dir     = "{$g['pathDadosUsuario']}arq_temp";
        is_dir($dir) OR mkdir($dir, 0777, true);
        $cArquivo = "{$g['pathDadosUsuario']}arq_temp/notas_recebidas_produtos.csv";

        if (file_exists($cArquivo)) {
            unlink($cArquivo);
        }          

        //-- Cria o arquivo
        if (!$handle = fopen($cArquivo, 'x')) {
            return "Erro ao criar o arquivo: <br />$cArquivo";
        }

        chmod($cArquivo, 0777);

        //variavel contadoras de linhas
        $conta_linha = 1;

        //-- Prepara o campo data de gravacao do arquivo
        $Data_Gravacao = date("dmy", time());

        $cAgrega_Dados = '';

        $cAgrega_Dados .= "Id_NF_Recebida;";
        $cAgrega_Dados .= "Id_NF_Recebida_Produto;";
        $cAgrega_Dados .= "Id_Produto;";
        $cAgrega_Dados .= "Qtde_NF_Recebida;";
        $cAgrega_Dados .= "Preco_Unitario_NF_Recebida;";
        $cAgrega_Dados .= "Preco_Total_NF_Recebida";
        $cAgrega_Dados .= SALTAR_LINHA;

        // Pesquisando dados dos clientes 
        $T014 = mysql_query("SELECT T014_T013_Id,
                                    T014_Id,
                                    D001_Id,
                                    T014_Quantidade,
                                    T014_Valor_Custo_Unitario,
                                    T014_Valor_Total_Custo
                               FROM T014
                          LEFT JOIN T013 ON T013_Id=T014_T013_Id
                          LEFT JOIN D009 ON D009_ID=T014_D009_Id
                          LEFT JOIN D049 ON D049_ID=D009_D049_ID
                          LEFT JOIN D001 ON D001_ID=D049_D001_ID
                              WHERE T013_Data_Emissao>='{$xData_Inicio}' 
                                AND T013_Data_Emissao<='{$xData_Fim}'
                                AND ifnull(D001_D060_Id,0)>0
                                AND ifnull(D001_C008_Id,0)>0
                           ORDER BY T014_T013_Id");

        while($mT014 = mysql_fetch_array($T014)){
            $cAgrega_Dados .= str_pad(trim($mT014['T014_T013_Id']),6,'0',STR_PAD_LEFT).";";
            $cAgrega_Dados .= str_pad(trim($mT014['T014_Id']),6,'0',STR_PAD_LEFT).";";
            $cAgrega_Dados .= str_pad(trim($mT014['D001_Id']),6,'0',STR_PAD_LEFT).";";
            $cAgrega_Dados .= gCorrigeNumero($mT014['T014_Quantidade']).";";
            $cAgrega_Dados .= gCorrigeNumero($mT014['T014_Valor_Custo_Unitario']).";";
            $cAgrega_Dados .= gCorrigeNumero($mT014['T014_Valor_Total_Custo']);
            $cAgrega_Dados .= SALTAR_LINHA;
        }

        fwrite($handle, $cAgrega_Dados);
        fclose($handle);

        @$zip->addFile($cArquivo, 'notas_recebidas_produtos.csv');

        //--- Fim arquivo produto nota fiscal recebida

        //------------------------------------------------------------------
        // Criar arquivo Títulos a Receber
        //------------------------------------------------------------------

        $dir     = "{$g['pathDadosUsuario']}arq_temp";
        is_dir($dir) OR mkdir($dir, 0777, true);
        $cArquivo = "{$g['pathDadosUsuario']}arq_temp/titulos_receber.csv";

        if (file_exists($cArquivo)) {
            unlink($cArquivo);
        }          

        //-- Cria o arquivo
        if (!$handle = fopen($cArquivo, 'x')) {
            return "Erro ao criar o arquivo: <br />$cArquivo";
        }

        chmod($cArquivo, 0777);

        //variavel contadoras de linhas
        $conta_linha = 1;

        //-- Prepara o campo data de gravacao do arquivo
        $Data_Gravacao = date("dmy", time());

        $cAgrega_Dados = '';

        $cAgrega_Dados .= "Id_Titulo_Receber;";//1             
        $cAgrega_Dados .= "Codigo_Cliente;";//2
        $cAgrega_Dados .= "Id_NF;";//2
        $cAgrega_Dados .= "Codigo_Usuario;";//3                
        $cAgrega_Dados .= "Id_Conta;";//4           
        $cAgrega_Dados .= "Id_Portador_Receber;";//5           
        $cAgrega_Dados .= "Num_Duplicata_Receber;";//6
        $cAgrega_Dados .= "Emissao_Titulo_Receber;";//7
        $cAgrega_Dados .= "Vencimento_Titulo_Receber;";//8
        $cAgrega_Dados .= "Recebimento_Titulo_Receber;";//9
        $cAgrega_Dados .= "Valor_Titulo_Receber";//10
        $cAgrega_Dados .= SALTAR_LINHA;

        $cAgrega_Dados .= "000000";//1             
        $cAgrega_Dados .= ";";//2
        $cAgrega_Dados .= ";";//2
        $cAgrega_Dados .= "000000";//3                
        $cAgrega_Dados .= "000000";//4           
        $cAgrega_Dados .= "000000";//5           
        $cAgrega_Dados .= "000000";//6
        $cAgrega_Dados .= "000000";//7
        $cAgrega_Dados .= "000000";//8
        $cAgrega_Dados .= "000000";//9
        $cAgrega_Dados .= "0";//10
        $cAgrega_Dados .= SALTAR_LINHA;

        // Pesquisando dados dos clientes 
        $T002 = mysql_query("SELECT T002_Id,
                                    T002_D024_Id,
                                    T002_T007_Id,
                                    T007_C007_Id_Vendedor_Interno,
                                    T002_D014_Id,
                                    T002_D027_Id,
                                    T002_Numero_Duplicata,
                                    T002_Data_Emissao,
                                    T002_Data_Vencimento,
                                    T002_Data_Recebimento,
                                    T002_Valor_Total
                               FROM T002
                          LEFT JOIN T007 ON T007_Id=T002_T007_Id
                          LEFT JOIN C007 ON C007_Id=T007_C007_Id
                              WHERE T002_Data_Emissao>='{$xData_Inicio}' 
                                AND T002_Data_Emissao<='{$xData_Fim}'
                                AND T002_Flag_Cancelada!='S'
                                ORDER BY T002_Id ASC");

        while($mT002 = mysql_fetch_array($T002)){
            $cAgrega_Dados .= str_pad(trim($mT002['T002_Id']),6,'0',STR_PAD_LEFT).";";//1
            $cAgrega_Dados .= str_pad(trim($mT002['T002_D024_Id']),6,'0',STR_PAD_LEFT).";";//2
            $cAgrega_Dados .= str_pad(trim($mT002['T002_T007_Id']),6,'0',STR_PAD_LEFT).";";//2
            $cAgrega_Dados .= str_pad(trim($mT002['C007_Id']),6,'0',STR_PAD_LEFT).";";//3
            $cAgrega_Dados .= str_pad(trim($mT002['T002_D014_Id']),6,'0',STR_PAD_LEFT).";";//4
            $cAgrega_Dados .= str_pad(trim($mT002['T002_D027_Id']),6,'0',STR_PAD_LEFT).";";//5
            $cAgrega_Dados .= trim($mT002['T002_Numero_Duplicata']).";";//6
            $cAgrega_Dados .= gCorrigeData($mT002['T002_Data_Emissao']).";";//7
            $cAgrega_Dados .= gCorrigeData($mT002['T002_Data_Vencimento']).";";//8
            $cAgrega_Dados .= gCorrigeData($mT002['T002_Data_Recebimento']).";";//9
            $cAgrega_Dados .= gCorrigeNumero($mT002['T002_Valor_Total']);//10
            $cAgrega_Dados .= SALTAR_LINHA;
        }

        fwrite($handle, $cAgrega_Dados);
        fclose($handle);

        @$zip->addFile($cArquivo, 'titulos_receber.csv');

        //--- Fim arquivo Títulos a Receber

        //------------------------------------------------------------------
        // Criar arquivo Títulos a Pagar
        //------------------------------------------------------------------

        $dir     = "{$g['pathDadosUsuario']}arq_temp";
        is_dir($dir) OR mkdir($dir, 0777, true);
        $cArquivo = "{$g['pathDadosUsuario']}arq_temp/titulos_pagar.csv";

        if (file_exists($cArquivo)) {
            unlink($cArquivo);
        }          

        //-- Cria o arquivo
        if (!$handle = fopen($cArquivo, 'x')) {
            return "Erro ao criar o arquivo: <br />$cArquivo";
        }

        chmod($cArquivo, 0777);

        //variavel contadoras de linhas
        $conta_linha = 1;

        //-- Prepara o campo data de gravacao do arquivo
        $Data_Gravacao = date("dmy", time());

        $cAgrega_Dados = '';

        $cAgrega_Dados .= "Id_Titulo_Pagar;";//1             
        $cAgrega_Dados .= "Codigo_Cliente;";//2
        $cAgrega_Dados .= "Id_NF_Recebida;";//2
        $cAgrega_Dados .= "Id_Conta;";//5
        $cAgrega_Dados .= "Id_Portador_Pagar;";//4        
        $cAgrega_Dados .= "Id_Centro_Custo;";//5
        $cAgrega_Dados .= "Num_Duplicata_Pagar;";//6 
        $cAgrega_Dados .= "Emissao_Titulo_Pagar;";//7
        $cAgrega_Dados .= "Vencimento_Titulo_Pagar;";//8
        $cAgrega_Dados .= "Pagamento_Titulo_Pagar;";//9
        $cAgrega_Dados .= "Valor_Titulo_Pagar";//10
        $cAgrega_Dados .= SALTAR_LINHA;

        $cAgrega_Dados .= "000000";//1             
        $cAgrega_Dados .= ";";//2
        $cAgrega_Dados .= ";";//2
        $cAgrega_Dados .= "000000";//3        
        $cAgrega_Dados .= "000000";//4        
        $cAgrega_Dados .= "000000";//5
        $cAgrega_Dados .= "000000";//6 
        $cAgrega_Dados .= "000000";//7
        $cAgrega_Dados .= "000000";//8
        $cAgrega_Dados .= "000000";//9
        $cAgrega_Dados .= "0";//10
        $cAgrega_Dados .= SALTAR_LINHA;

        // Pesquisando dados dos clientes 
        $T015 = mysql_query("SELECT T015_Id,
                                    T015_D024_Id,
                                    T015_T013_Id,
                                    T015_D031_Id,
                                    T015_D014_Id,
                                    T015_D073_Id,
                                    T015_Numero_Duplicata,
                                    T015_Data_Emissao,
                                    T015_Data_Vencimento,
                                    T015_Data_Pagamento,
                                    T015_Valor_Total
                               FROM T015
                              WHERE T015_Data_Emissao>='{$xData_Inicio}' 
                                AND T015_Data_Emissao<='{$xData_Fim}'
                                AND T015_Flag_Cancelada!='S'
                                ORDER BY T015_Id ASC");

        while($mT015 = mysql_fetch_array($T015)){
            $cAgrega_Dados .= str_pad(trim($mT015['T015_Id']),6,'0',STR_PAD_LEFT).";";//1
            $cAgrega_Dados .= str_pad(trim($mT015['T015_D024_Id']),6,'0',STR_PAD_LEFT).";";//2
            $cAgrega_Dados .= str_pad(trim($mT015['T015_T013_Id']),6,'0',STR_PAD_LEFT).";";//2
            $cAgrega_Dados .= str_pad(trim($mT015['T015_D014_Id']),6,'0',STR_PAD_LEFT).";";//4
            $cAgrega_Dados .= str_pad(trim($mT015['T015_D031_Id']),6,'0',STR_PAD_LEFT).";";//4
            $cAgrega_Dados .= str_pad(trim($mT015['T015_D073_Id']),6,'0',STR_PAD_LEFT).";";//5
            $cAgrega_Dados .= trim($mT015['T015_Numero_Duplicata']).";";//6
            $cAgrega_Dados .= gCorrigeData($mT015['T015_Data_Emissao']).";";//7
            $cAgrega_Dados .= gCorrigeData($mT015['T015_Data_Vencimento']).";";//8
            $cAgrega_Dados .= gCorrigeData($mT015['T015_Data_Pagamento']).";";//9
            $cAgrega_Dados .= gCorrigeNumero($mT015['T015_Valor_Total']);//10
            $cAgrega_Dados .= SALTAR_LINHA;
        }

        fwrite($handle, $cAgrega_Dados);
        fclose($handle);

        @$zip->addFile($cArquivo, 'titulos_pagar.csv');

        //--- Fim arquivo Títulos a Pagar

        //------------------------------------------------------------------
        // Criar arquivo lançamento bancario
        //------------------------------------------------------------------

        $dir     = "{$g['pathDadosUsuario']}arq_temp";
        is_dir($dir) OR mkdir($dir, 0777, true);
        $cArquivo = "{$g['pathDadosUsuario']}arq_temp/lancamentos_bancarios.csv";

        if (file_exists($cArquivo)) {
            unlink($cArquivo);
        }          

        //-- Cria o arquivo
        if (!$handle = fopen($cArquivo, 'x')) {
            return "Erro ao criar o arquivo: <br />$cArquivo";
        }

        chmod($cArquivo, 0777);

        //variavel contadoras de linhas
        $conta_linha = 1;

        //-- Prepara o campo data de gravacao do arquivo
        $Data_Gravacao = date("dmy", time());

        $cAgrega_Dados = '';

        $cAgrega_Dados .= "Id_Lancamento;";//1             
        $cAgrega_Dados .= "Codigo_Cliente;";//2
        $cAgrega_Dados .= "Id_Conta;";//3           
        $cAgrega_Dados .= "Id_Portador_Receber;";//4           
        $cAgrega_Dados .= "Id_Portador_Pagar;";//5         
        $cAgrega_Dados .= "Descricao;";//6
        $cAgrega_Dados .= "Complemento;";//7
        $cAgrega_Dados .= "Data lancamento;";//8
        $cAgrega_Dados .= "Valor lancamento;";//9
        $cAgrega_Dados .= "Tipo";//10           
        $cAgrega_Dados .= SALTAR_LINHA;

        $cAgrega_Dados .= ";";//1             
        $cAgrega_Dados .= ";";//2                
        $cAgrega_Dados .= ";";//3           
        $cAgrega_Dados .= ";";//4           
        $cAgrega_Dados .= ";";//5
        $cAgrega_Dados .= ";";//6
        $cAgrega_Dados .= ";";//7
        $cAgrega_Dados .= ";";//8
        $cAgrega_Dados .= "000000";//9
        $cAgrega_Dados .= "";//10
        $cAgrega_Dados .= SALTAR_LINHA;

        // Pesquisando dados dos clientes 
        $T019 = mysql_query("SELECT T019_Id,
                                    T019_D024_Id,
                                    T019_D014_Id,
                                    T019_D027_Id,
                                    T019_D031_Id,
                                    T019_Referente,
                                    T019_Complemento,
                                    T019_Data_Lancamento,
                                    T019_Valor_Lancamento
                               FROM T019
                              WHERE T019_Data_Lancamento>='{$xData_Inicio}' 
                                AND T019_Data_Lancamento<='{$xData_Fim}'
                                ORDER BY T019_Id ASC");

        while($mT019 = mysql_fetch_array($T019)){
            $tipo = ($mT019['T019_Valor_Lancamento']>=0) ? 'Entrada' : 'Saida' ;
            $cAgrega_Dados .= str_pad(trim($mT019['T019_Id']),6,'0',STR_PAD_LEFT).";";//1
            $cAgrega_Dados .= str_pad(trim($mT019['T019_D024_Id']),6,'0',STR_PAD_LEFT).";";//2
            $cAgrega_Dados .= str_pad(trim($mT019['T019_D014_Id']),6,'0',STR_PAD_LEFT).";";//3
            $cAgrega_Dados .= str_pad(trim($mT019['T019_D027_Id']),6,'0',STR_PAD_LEFT).";";//4
            $cAgrega_Dados .= str_pad(trim($mT019['T019_D031_Id']),6,'0',STR_PAD_LEFT).";";//5
            $cAgrega_Dados .= trim($mT019['T019_Referente']).";";//6
            $cAgrega_Dados .= trim($mT019['T019_Complemento']).";";//7
            $cAgrega_Dados .= gCorrigeData($mT019['T019_Data_Lancamento']).";";//8
            $cAgrega_Dados .= gCorrigeNumero($mT019['T019_Valor_Lancamento']).";";//9
            $cAgrega_Dados .= trim($tipo);//10
            $cAgrega_Dados .= SALTAR_LINHA;
        }

        fwrite($handle, $cAgrega_Dados);
        fclose($handle);

        @$zip->addFile($cArquivo, 'lancamentos_bancarios.csv');

        //--- Fim arquivo lancamentos bancarios

        //--- INÍCIO CRIAÇÃO TABELAS DE RELACIONAMENTO TERNÁRIO

        //------------------------------------------------------------------
        // Criar arquivo relação vendas 
        //------------------------------------------------------------------

        /*$dir     = "{$g['pathDadosUsuario']}arq_temp";
        is_dir($dir) OR mkdir($dir, 0777, true);
        $cArquivo = "{$g['pathDadosUsuario']}arq_temp/vendas.csv";

        if (file_exists($cArquivo)) {
            unlink($cArquivo);
        }          

        //-- Cria o arquivo
        if (!$handle = fopen($cArquivo, 'x')) {
            return "Erro ao criar o arquivo: <br />$cArquivo";
        }

        chmod($cArquivo, 0777);

        //variavel contadoras de linhas
        $conta_linha = 1;

        //-- Prepara o campo data de gravacao do arquivo
        $Data_Gravacao = date("dmy", time());

        $cAgrega_Dados = '';

        $cAgrega_Dados .= "Codigo_Cliente;";//1             
        $cAgrega_Dados .= "Num_Orc;";//2
        $cAgrega_Dados .= "Num_Ped;";//3
        $cAgrega_Dados .= "Id_NF;";//4          
        $cAgrega_Dados .= "Valor_Total_Orc;";
        $cAgrega_Dados .= "Valor_Total_Ped;";
        $cAgrega_Dados .= "Valor_Total_NF";
        $cAgrega_Dados .= SALTAR_LINHA;

        // Pesquisando dados dos clientes 
        $relacaoVendas = mysql_query("SELECT D024_Id,
                                             T003_Id,
                                             T005_Id,
                                             T007_Id,
                                             SUM(T004_Valor_Total_Preco) AS T003_Valor_Total,
                                             SUM(T006_Valor_Total_Preco) AS T005_Valor_Total,
                                             SUM(T008_Valor_Total_Preco) AS T007_Valor_Total
                                        FROM T003
                                   LEFT JOIN D024 ON D024_Id=T003_D024_Id
                                   LEFT JOIN T004 ON T004_T003_Id=T003_Id
                                   LEFT JOIN T006 ON T006_T004_Id=T004_Id
                                   LEFT JOIN T008 ON T008_T006_Id=T006_Id   
                                   LEFT JOIN T005 ON T005_Id=T006_T005_Id
                                   LEFT JOIN T007 ON T007_Id=T008_T007_Id
                                       WHERE T003_Data_Emissao>='{$xData_Inicio}' 
                                         AND T003_Data_Emissao<='{$xData_Fim}'
                                         AND (T005_T005_Id_Agrupado<=0 OR T005_T005_Id_Agrupado IS NULL)
                                    GROUP BY T003_Id, T005_Id, T007_Id
                                       UNION
                                      SELECT D024_Id,
                                             T003_Id,
                                             T005_Id,
                                             T007_Id,
                                             SUM(T004_Valor_Total_Preco) AS T003_Valor_Total,
                                             SUM(T006_Valor_Total_Preco) AS T005_Valor_Total,
                                             SUM(T008_Valor_Total_Preco) AS T007_Valor_Total
                                        FROM T005
                                   LEFT JOIN D024 ON D024_Id=T005_D024_Id 
                                   LEFT JOIN T006 ON T006_T005_Id=T005_Id
                                   LEFT JOIN T008 ON T008_T006_Id=T006_Id   
                                   LEFT JOIN T004 ON T004_Id=T006_T004_Id
                                   LEFT JOIN T007 ON T007_Id=T008_T007_Id   
                                   LEFT JOIN T003 ON T003_Id=T004_T003_Id
                                       WHERE T005_Data_Emissao>='{$xData_Inicio}'
                                         AND T005_Data_Emissao<='{$xData_Fim}'
                                         AND (T005_T005_Id_Agrupado<=0 OR T005_T005_Id_Agrupado IS NULL)
                                         AND T003_Id IS NULL
                                    GROUP BY T005_Id, T007_Id
                                       UNION
                                      SELECT D024_Id,
                                             T003_Id,
                                             T005_Id,
                                             T007_Id,
                                             SUM(T004_Valor_Total_Preco) AS T003_Valor_Total,
                                             SUM(T006_Valor_Total_Preco) AS T005_Valor_Total,
                                             SUM(T008_Valor_Total_Preco) AS T007_Valor_Total    
                                        FROM T007
                                   LEFT JOIN D024 ON D024_Id=T007_D024_Id
                                   LEFT JOIN T008 ON T008_T007_Id=T007_Id
                                   LEFT JOIN T006 ON T006_Id=T008_T006_Id 
                                   LEFT JOIN T004 ON T004_Id=T006_T004_Id
                                   LEFT JOIN T005 ON T005_Id=T006_T005_Id
                                   LEFT JOIN T003 ON T003_Id=T004_T003_Id
                                       WHERE T007_Data_Emissao>='{$xData_Inicio}' 
                                         AND T007_Data_Emissao<='{$xData_Fim}'
                                         AND (T005_T005_Id_Agrupado<=0 OR T005_T005_Id_Agrupado IS NULL)
                                         AND T005_Id IS NULL
                                    GROUP BY T007_Id
                                    ORDER BY T007_Id");

        while($mRelacaoVendas = mysql_fetch_array($relacaoVendas)){
            $cAgrega_Dados .= trim($mRelacaoVendas['D024_Id']).";";
            $cAgrega_Dados .= trim($mRelacaoVendas['T003_Id']).";";
            $cAgrega_Dados .= trim($mRelacaoVendas['T005_Id']).";";
            $cAgrega_Dados .= trim($mRelacaoVendas['T007_Id']).";";
            $cAgrega_Dados .= gCorrigeNumero($mRelacaoVendas['T003_Valor_Total']).";";
            $cAgrega_Dados .= gCorrigeNumero($mRelacaoVendas['T005_Valor_Total']).";";
            $cAgrega_Dados .= gCorrigeNumero($mRelacaoVendas['T007_Valor_Total']);
            $cAgrega_Dados .= SALTAR_LINHA;
        }

        fwrite($handle, $cAgrega_Dados);
        fclose($handle);

        @$zip->addFile($cArquivo, 'vendas.csv');

        //--- Fim arquivo relação vendas*/

        //------------------------------------------------------------------
        // Criar arquivo relação compras 
        //------------------------------------------------------------------

        /*$dir     = "{$g['pathDadosUsuario']}arq_temp";
        is_dir($dir) OR mkdir($dir, 0777, true);
        $cArquivo = "{$g['pathDadosUsuario']}arq_temp/compras.csv";

        if (file_exists($cArquivo)) {
            unlink($cArquivo);
        }          

        //-- Cria o arquivo
        if (!$handle = fopen($cArquivo, 'x')) {
            return "Erro ao criar o arquivo: <br />$cArquivo";
        }

        chmod($cArquivo, 0777);

        //variavel contadoras de linhas
        $conta_linha = 1;

        //-- Prepara o campo data de gravacao do arquivo
        $Data_Gravacao = date("dmy", time());

        $cAgrega_Dados = '';

        $cAgrega_Dados .= "Codigo_Cliente;";//1             
        $cAgrega_Dados .= "Num_Cot;";//2
        $cAgrega_Dados .= "Num_OC;";//3
        $cAgrega_Dados .= "Id_NF_Recebida;";//4
        $cAgrega_Dados .= "Id_Conhecimento;";//5
        $cAgrega_Dados .= "Codigo_Transportadora;";//6
        $cAgrega_Dados .= "Valor_Total_Cot;";//7
        $cAgrega_Dados .= "Valor_Total_OC;";//8
        $cAgrega_Dados .= "Valor_Total_NF;";//9
        $cAgrega_Dados .= "Valor_Total_Conhecimento";//10
        $cAgrega_Dados .= SALTAR_LINHA;

        // Pesquisando dados dos clientes 
        $relacaoCompra = mysql_query("SELECT D024_Id,
                                             T218_Id,
                                             T224_Id,
                                             T013_Id,       
                                             T085_T084_Id,
                                             SUM(T219_Valor_Preco_Unitario*IF(T014_Quantidade>0,T014_Quantidade,T219_Quantidade)) AS T218_Valor_Total,
                                             SUM(T225_Valor_Preco_Unitario*IF(T014_Quantidade>0,T014_Quantidade,T225_Quantidade)) AS T224_Valor_Total,
                                             SUM(T014_Valor_Total_Custo) AS T013_Valor_Total,
                                             SUM(T014_Valor_Frete) AS T084_Valor_Total
                                        FROM T218
                                   LEFT JOIN D024 ON D024_Id=T218_D024_Id
                                   LEFT JOIN T219 ON T219_T218_Id=T218_Id
                                   LEFT JOIN T225 ON T225_T219_Id=T219_Id 
                                   LEFT JOIN T014 ON T014_T225_Id=T225_Id 
                                   LEFT JOIN T224 ON T224_Id=T225_T224_Id
                                   LEFT JOIN T013 ON T013_Id=T014_T013_Id
                                   LEFT JOIN T085 ON T085_T013_Id=T013_Id
                                       WHERE T218_Data_Emissao>='{$xData_Inicio}' 
                                         AND T218_Data_Emissao<='{$xData_Fim}'
                                         AND T219_Flag_Cancelado='N'
                                         GROUP BY T218_Id, T224_Id, T013_Id, T085_T084_Id
                                       UNION
                                      SELECT D024_Id,
                                             T218_Id,
                                             T224_Id,
                                             T013_Id,       
                                             T085_T084_Id,
                                             SUM(T219_Valor_Preco_Unitario*IF(T014_Quantidade>0,T014_Quantidade,T219_Quantidade)) AS T218_Valor_Total,    
                                             SUM(T225_Valor_Preco_Unitario*IF(T014_Quantidade>0,T014_Quantidade,T225_Quantidade)) AS T224_Valor_Total,
                                             SUM(T014_Valor_Total_Custo) AS T013_Valor_Total,
                                             SUM(T014_Valor_Frete) AS T084_Valor_Total
                                        FROM T224
                                   LEFT JOIN D024 ON D024_Id=T224_D024_Id 
                                   LEFT JOIN T225 ON T225_T224_Id=T224_Id
                                   LEFT JOIN T219 ON T219_Id=T225_T219_Id
                                   LEFT JOIN T014 ON T014_T225_Id=T225_Id   
                                   LEFT JOIN T218 ON T218_Id=T219_T218_Id
                                   LEFT JOIN T013 ON T013_Id=T014_T013_Id
                                   LEFT JOIN T085 ON T085_T013_Id=T013_Id
                                       WHERE T224_Data_Emissao>='{$xData_Inicio}' 
                                         AND T224_Data_Emissao<='{$xData_Fim}'
                                         AND T225_Flag_Cancelada='N'
                                         AND T218_Id IS NULL
                                    GROUP BY T224_Id, T013_Id, T085_T084_Id
                                       UNION
                                      SELECT D024_Id,
                                             T218_Id,
                                             T224_Id,
                                             T013_Id,       
                                             T085_T084_Id,
                                             SUM(T219_Valor_Preco_Unitario*IF(T014_Quantidade>0,T014_Quantidade,T219_Quantidade)) AS T218_Valor_Total,
                                             SUM(T225_Valor_Preco_Unitario*IF(T014_Quantidade>0,T014_Quantidade,T225_Quantidade)) AS T224_Valor_Total,
                                             SUM(T014_Valor_Total_Custo) AS T013_Valor_Total,
                                             SUM(T014_Valor_Frete) AS T084_Valor_Total
                                        FROM T013
                                   LEFT JOIN D024 ON D024_Id=T013_D024_Id 
                                   LEFT JOIN T014 ON T014_T013_Id=T013_Id 
                                   LEFT JOIN T085 ON T085_T013_Id=T013_Id
                                   LEFT JOIN T225 ON T225_Id=T014_T225_Id
                                   LEFT JOIN T219 ON T219_Id=T225_T219_Id 
                                   LEFT JOIN T224 ON T224_Id=T225_T224_Id   
                                   LEFT JOIN T218 ON T218_Id=T219_T218_Id                                  
                                       WHERE T013_Data_Emissao>='{$xData_Inicio}' 
                                         AND T013_Data_Emissao<='{$xData_Fim}'
                                         AND T224_Id IS NULL           
                                    GROUP BY T013_Id, T085_T084_Id 
                                       UNION
                                      SELECT D024_Id,
                                             T218_Id,
                                             T224_Id,
                                             T013_Id,       
                                             T085_T084_Id,
                                             SUM(T219_Valor_Preco_Unitario*IF(T014_Quantidade>0,T014_Quantidade,T219_Quantidade)) AS T218_Valor_Total,
                                             SUM(T225_Valor_Preco_Unitario*IF(T014_Quantidade>0,T014_Quantidade,T225_Quantidade)) AS T224_Valor_Total,
                                             SUM(T014_Valor_Total_Custo) AS T013_Valor_Total,
                                             SUM(T014_Valor_Frete) AS T084_Valor_Total                                 
                                        FROM T085
                                   LEFT JOIN T084 ON T084_Id=T085_T084_Id
                                   LEFT JOIN D024 ON D024_Id=T084_D024_Id
                                   LEFT JOIN T013 ON T013_Id=T085_T013_Id
                                   LEFT JOIN T014 ON T014_T013_Id=T013_Id
                                   LEFT JOIN T225 ON T225_Id=T014_T225_Id
                                   LEFT JOIN T219 ON T219_Id=T225_T219_Id
                                   LEFT JOIN T224 ON T224_Id=T225_T224_Id 
                                   LEFT JOIN T218 ON T218_Id=T219_T218_Id                                 
                                       WHERE T084_Data_Emissao>='{$xData_Inicio}' 
                                         AND T084_Data_Emissao<='{$xData_Fim}'
                                         AND T013_Id IS NULL   
                                    GROUP BY T085_T084_Id
                                    ORDER BY T218_Id ASC");

        while($mRelacaoCompra = mysql_fetch_array($relacaoCompra)){
            if($mRelacaoCompra['T013_Id'] > 0){
                $T013 = mysql_query("SELECT T013_D022_Id FROM T013 WHERE T013_Id='{$mRelacaoCompra['T013_Id']}'");
                $mT013 = mysql_fetch_array($T013);
                $D022_Id = $mT013['T013_D022_Id'];
            }else if($mRelacaoCompra['T224_Id'] > 0){
                $T224 = mysql_query("SELECT T224_D022_Id FROM T224 WHERE T224_Id='{$mRelacaoCompra['T224_Id']}'");
                $mT224 = mysql_fetch_array($T224);
                $D022_Id = $mT224['T224_D022_Id'];
            }else if($mRelacaoCompra['T218_Id'] > 0){
                $T218 = mysql_query("SELECT T218_D022_Id FROM T218 WHERE T218_Id='{$mRelacaoCompra['T218_Id']}'");
                $mT218 = mysql_fetch_array($T218);
                $D022_Id = $mT218['T218_D022_Id'];
            } 
            $cAgrega_Dados .= trim($mRelacaoCompra['D024_Id']).";";
            $cAgrega_Dados .= trim($mRelacaoCompra['T218_Id']).";";
            $cAgrega_Dados .= trim($mRelacaoCompra['T224_Id']).";";
            $cAgrega_Dados .= trim($mRelacaoCompra['T013_Id']).";";
            $cAgrega_Dados .= trim($mRelacaoCompra['T084_Id'].";");
            $cAgrega_Dados .= $D022_Id.";";
            $cAgrega_Dados .= gCorrigeNumero($mRelacaoCompra['T218_Valor_Total']).";";
            $cAgrega_Dados .= gCorrigeNumero($mRelacaoCompra['T224_Valor_Total']).";";
            $cAgrega_Dados .= gCorrigeNumero($mRelacaoCompra['T013_Valor_Total']).";";
            $cAgrega_Dados .= gCorrigeNumero($mRelacaoCompra['T084_Valor_Total']);
            $cAgrega_Dados .= SALTAR_LINHA;
        }

        fwrite($handle, $cAgrega_Dados);
        fclose($handle);

        @$zip->addFile($cArquivo, 'compras.csv');

        //--- Fim arquivo relação compras*/

        //------------------------------------------------------------------
        // Criar arquivo relação compras produtos 
        //------------------------------------------------------------------

        /*$dir     = "{$g['pathDadosUsuario']}arq_temp";
        is_dir($dir) OR mkdir($dir, 0777, true);
        $cArquivo = "{$g['pathDadosUsuario']}arq_temp/zrelat_com_prod.csv";

        if (file_exists($cArquivo)) {
            unlink($cArquivo);
        }          

        //-- Cria o arquivo
        if (!$handle = fopen($cArquivo, 'x')) {
            return "Erro ao criar o arquivo: <br />$cArquivo";
        }

        chmod($cArquivo, 0777);

        //variavel contadoras de linhas
        $conta_linha = 1;

        //-- Prepara o campo data de gravacao do arquivo
        $Data_Gravacao = date("dmy", time());

        $cAgrega_Dados = '';

        $cAgrega_Dados .= "Id_Produto;";//1
        $cAgrega_Dados .= "Num_Cot_Produto;";//2             
        $cAgrega_Dados .= "Num_OC_Produto;";//3
        $cAgrega_Dados .= "Id_NF_Recebida_Produto";//          
        $cAgrega_Dados .= SALTAR_LINHA;

        // Pesquisando dados dos clientes 
        $relacaoVendas = mysql_query("SELECT D001_Id,
                                             T219_Id,     
                                             T225_Id,
                                             T014_Id
                                        FROM T219
                                   LEFT JOIN T218 ON T218_Id=T219_T218_Id
                                   LEFT JOIN D009 ON D009_Id=T219_D009_Id
                                   LEFT JOIN D049 ON D049_Id=D009_D049_Id
                                   LEFT JOIN D001 ON D001_Id=D049_D001_Id
                                   LEFT JOIN T225 ON T225_T219_Id=T219_Id
                                   LEFT JOIN T014 ON T014_T225_Id=T225_Id
                                       WHERE T218_Data_Emissao >= '{$xData_Inicio}'
                                         AND T218_Data_Emissao <= '{$xData_Fim}'
                                       UNION 
                                      SELECT D001_Id,
                                             T219_Id,       
                                             T225_Id,
                                             T014_Id
                                        FROM T225
                                   LEFT JOIN T224 ON T224_id=T225_T224_Id
                                   LEFT JOIN D009 ON D009_Id=T225_D009_Id
                                   LEFT JOIN D049 ON D049_Id=D009_D049_Id
                                   LEFT JOIN D001 ON D001_Id=D049_D001_Id
                                   LEFT JOIN T219 ON T219_Id=T225_T219_Id
                                   LEFT JOIN T014 ON T014_T225_Id=T225_Id  
                                       WHERE T224_Data_Emissao >= '{$xData_Inicio}'
                                         AND T224_Data_Emissao <= '{$xData_Fim}'
                                       UNION 
                                      SELECT D001_Id,
                                             T219_Id,       
                                             T225_Id,
                                             T014_Id
                                        FROM T014
                                   LEFT JOIN T013 ON T013_Id=T014_T013_Id
                                   LEFT JOIN D009 ON D009_Id=T014_D009_Id
                                   LEFT JOIN D049 ON D049_Id=D009_D049_Id
                                   LEFT JOIN D001 ON D001_Id=D049_D001_Id
                                   LEFT JOIN T225 ON T225_id=T014_T225_Id
                                   LEFT JOIN T219 ON T219_Id=T225_T219_Id
                                       WHERE T013_Data_Emissao >= '{$xData_Inicio}'
                                         AND T013_Data_Emissao <= '{$xData_Fim}'");

        while($mRelacaoVendas = mysql_fetch_array($relacaoVendas)){
            $cAgrega_Dados .= trim($mRelacaoVendas['D001_Id']).";";
            $cAgrega_Dados .= trim($mRelacaoVendas['T219_Id']).";";
            $cAgrega_Dados .= trim($mRelacaoVendas['T225_Id']).";";
            $cAgrega_Dados .= trim($mRelacaoVendas['T014_Id']);
            $cAgrega_Dados .= SALTAR_LINHA;
        }

        fwrite($handle, $cAgrega_Dados);
        fclose($handle);

        @$zip->addFile($cArquivo, 'zrelat_com_prod.csv');*/

        //--- Fim arquivo relação compras produtos 

        //------------------------------------------------------------------
        // Criar arquivo relação títulos
        //------------------------------------------------------------------
        /*
        $dir     = "{$g['pathDadosUsuario']}arq_temp";
        is_dir($dir) OR mkdir($dir, 0777, true);
        $cArquivo = "{$g['pathDadosUsuario']}arq_temp/zrelat_titulos.csv";

        if (file_exists($cArquivo)) {
            unlink($cArquivo);
        }          

        //-- Cria o arquivo
        if (!$handle = fopen($cArquivo, 'x')) {
            return "Erro ao criar o arquivo: <br />$cArquivo";
        }

        chmod($cArquivo, 0777);

        //variavel contadoras de linhas
        $conta_linha = 1;

        //-- Prepara o campo data de gravacao do arquivo
        $Data_Gravacao = date("dmy", time());

        $cAgrega_Dados = '';

        $cAgrega_Dados .= "Codigo_Cliente;";//1
        $cAgrega_Dados .= "Id_Titulo_Receber;";//2             
        $cAgrega_Dados .= "Id_Titulo_Pagar;";//3
        $cAgrega_Dados .= "Id_Conta";//4
        $cAgrega_Dados .= SALTAR_LINHA;

        // Pesquisando dados dos clientes 
        $relacaoTitulos = mysql_query("SELECT D024_Id, 
                                              T002_Id, 
                                              0,
                                              D014_Id
                                         FROM T002
                                    LEFT JOIN D024 ON D024_Id=T002_D024_Id
                                    LEFT JOIN D014 ON D014_Id=T002_D014_Id 
                                        WHERE T002_Data_Emissao>='{$xData_Inicio}'
                                          AND T002_Data_Emissao<='{$xData_Fim}'
                                        UNION 
                                       SELECT D024_Id,
                                              0,
                                              T015_Id,
                                              D014_Id
                                         FROM T015
                                    LEFT JOIN D024 ON D024_Id=T015_D024_Id
                                    LEFT JOIN D014 ON D014_Id=T015_D014_Id
                                        WHERE T015_Data_Emissao>='{$xData_Inicio}'
                                          AND T015_Data_Emissao<='{$xData_Fim}'");

        while($mRelacaoTitulos = mysql_fetch_array($relacaoTitulos)){
            $cAgrega_Dados .= trim($mRelacaoTitulos['D024_Id']).";";
            $cAgrega_Dados .= trim($mRelacaoTitulos['T002_Id']).";";
            $cAgrega_Dados .= trim($mRelacaoTitulos['0']).";";
            $cAgrega_Dados .= trim($mRelacaoTitulos['D014_Id']);
            $cAgrega_Dados .= SALTAR_LINHA;
        }

        fwrite($handle, $cAgrega_Dados);
        fclose($handle);

        @$zip->addFile($cArquivo, 'zrelat_titulos.csv');

        //--- Fim arquivo relação compras produtos 
        
        //------------------------------------------------------------------
        // Criar arquivo relação títulos
        //------------------------------------------------------------------

        $dir     = "{$g['pathDadosUsuario']}arq_temp";
        is_dir($dir) OR mkdir($dir, 0777, true);
        $cArquivo = "{$g['pathDadosUsuario']}arq_temp/zrelat_titulos.csv";

        if (file_exists($cArquivo)) {
            unlink($cArquivo);
        }          

        //-- Cria o arquivo
        if (!$handle = fopen($cArquivo, 'x')) {
            return "Erro ao criar o arquivo: <br />$cArquivo";
        }

        chmod($cArquivo, 0777);

        //variavel contadoras de linhas
        $conta_linha = 1;

        //-- Prepara o campo data de gravacao do arquivo
        $Data_Gravacao = date("dmy", time());

        $cAgrega_Dados = '';

        $cAgrega_Dados .= "Codigo_Cliente;";//1
        $cAgrega_Dados .= "Id_Titulo_Receber;";//2             
        $cAgrega_Dados .= "Id_Titulo_Pagar;";//3
        $cAgrega_Dados .= "Id_Conta";//4
        $cAgrega_Dados .= SALTAR_LINHA;

        // Pesquisando dados dos clientes 
        $relacaoTitulos = mysql_query("SELECT D024_Id, 
                                              T002_Id, 
                                              0,
                                              D014_Id
                                         FROM T002
                                    LEFT JOIN D024 ON D024_Id=T002_D024_Id
                                    LEFT JOIN D014 ON D014_Id=T002_D014_Id 
                                        WHERE T002_Data_Emissao>='{$xData_Inicio}'
                                          AND T002_Data_Emissao<='{$xData_Fim}'
                                        UNION 
                                       SELECT D024_Id,
                                              0,
                                              T015_Id,
                                              D014_Id
                                         FROM T015
                                    LEFT JOIN D024 ON D024_Id=T015_D024_Id
                                    LEFT JOIN D014 ON D014_Id=T015_D014_Id
                                        WHERE T015_Data_Emissao>='{$xData_Inicio}'
                                          AND T015_Data_Emissao<='{$xData_Fim}'");

        while($mRelacaoTitulos = mysql_fetch_array($relacaoTitulos)){
            $cAgrega_Dados .= trim($mRelacaoTitulos['D024_Id']).";";
            $cAgrega_Dados .= trim($mRelacaoTitulos['T002_Id']).";";
            $cAgrega_Dados .= trim($mRelacaoTitulos['0']).";";
            $cAgrega_Dados .= trim($mRelacaoTitulos['D014_Id']);
            $cAgrega_Dados .= SALTAR_LINHA;
        }

        fwrite($handle, $cAgrega_Dados);
        fclose($handle);

        @$zip->addFile($cArquivo, 'zrelat_titulos.csv');

        //------------------------------------------------------------------
        // Criar arquivo relação clientes, transportadoras, cidades e estados
        //------------------------------------------------------------------

        /*$dir     = "{$g['pathDadosUsuario']}arq_temp";
        is_dir($dir) OR mkdir($dir, 0777, true);
        $cArquivo = "{$g['pathDadosUsuario']}arq_temp/zrelat_location.csv";

        if (file_exists($cArquivo)) {
            unlink($cArquivo);
        }          

        //-- Cria o arquivo
        if (!$handle = fopen($cArquivo, 'x')) {
            return "Erro ao criar o arquivo: <br />$cArquivo";
        }

        chmod($cArquivo, 0777);

        //variavel contadoras de linhas
        $conta_linha = 1;

        //-- Prepara o campo data de gravacao do arquivo
        $Data_Gravacao = date("dmy", time());

        $cAgrega_Dados = '';

        $cAgrega_Dados .= "Codigo_Cliente;";//1
        $cAgrega_Dados .= "Codigo_Transportadora;";//2             
        $cAgrega_Dados .= "Id_Cidade;";//3
        $cAgrega_Dados .= "Id_UF";//4
        $cAgrega_Dados .= SALTAR_LINHA;

        // Pesquisando dados dos clientes 
        $relacaoTitulos = mysql_query("SELECT D024_Id AS D024_Id,
                                              0 AS D022_Id, 
                                              D024_D018_Id AS D018_Id,
                                              D024_D020_Id AS D020_Id 
                                         FROM D024
                                        UNION
                                       SELECT 0 AS D024_Id,
                                              D022_Id AS D022_Id, 
                                              D022_D018_Id AS D018_Id,
                                              D022_D020_Id AS D020_Id
                                         FROM D022");

        while($mRelacaoTitulos = mysql_fetch_array($relacaoTitulos)){
            $cAgrega_Dados .= trim($mRelacaoTitulos['D024_Id']).";";
            $cAgrega_Dados .= trim($mRelacaoTitulos['D022_Id']).";";
            $cAgrega_Dados .= trim($mRelacaoTitulos['D020_Id']).";";
            $cAgrega_Dados .= trim($mRelacaoTitulos['D018_Id']);
            $cAgrega_Dados .= SALTAR_LINHA;
        }

        fwrite($handle, $cAgrega_Dados);
        fclose($handle);

        @$zip->addFile($cArquivo, 'zrelat_location.csv');

        //--- Fim arquivo relação clientes, transportadoras, cidades e estados*/

        $zip->close();

        return $cArquivoZIP;

    }

             

    public function adicionarEspape($x)
    {
        $x=trim($x);
        $x = preg_replace("/\s+/", " ",$x);

        ////-- ELIMINA CARACTERES ESPECIAIS
        define( "ACENTOS" , "ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖŒØŠÙÚÛÜÝŽÞàáâãäåæçèéêëìíîïðñòóôõö&œøšùúûüýžþºª&.$" );
        define( "NORMAL"  , "AAAAAAACEEEEIIIIDNOOOOO   UUUUY  aaaaaaaceeeeiiii noooooE   uuuuy  BCED$" );
        
        $x = strtr($x, ACENTOS, NORMAL);
 
        //$x = urldecode($x);
        $novoX = '';
        for ($i = 0; $i < strlen($x); $i++) {
            $code = ord($x[$i]);
            if ($code == 192) { $novoX .= 'A'; continue; }
            if ($code == 193) { $novoX .= 'A'; continue; }
            if ($code == 194) { $novoX .= 'A'; continue; }
            if ($code == 195) { $novoX .= 'A'; continue; }
            if ($code == 196) { $novoX .= 'A'; continue; }
            if ($code == 197) { $novoX .= 'A'; continue; }
            if ($code == 198) { $novoX .= 'A'; continue; }
            if ($code == 199) { $novoX .= 'C'; continue; }
            if ($code == 200) { $novoX .= 'E'; continue; }
            if ($code == 201) { $novoX .= 'E'; continue; }
            if ($code == 202) { $novoX .= 'E'; continue; }
            if ($code == 203) { $novoX .= 'E'; continue; }
            if ($code == 204) { $novoX .= 'I'; continue; }
            if ($code == 205) { $novoX .= 'I'; continue; }
            if ($code == 206) { $novoX .= 'I'; continue; }
            if ($code == 207) { $novoX .= 'I'; continue; }
            if ($code == 210) { $novoX .= 'O'; continue; }
            if ($code == 211) { $novoX .= 'O'; continue; }
            if ($code == 212) { $novoX .= 'O'; continue; }
            if ($code == 213) { $novoX .= 'O'; continue; }
            if ($code == 214) { $novoX .= 'O'; continue; }
            if ($code == 217) { $novoX .= 'U'; continue; }
            if ($code == 218) { $novoX .= 'U'; continue; }
            if ($code == 219) { $novoX .= 'U'; continue; }
            if ($code == 220) { $novoX .= 'U'; continue; }
            if ($code == 224) { $novoX .= 'A'; continue; }
            if ($code == 225) { $novoX .= 'A'; continue; }
            if ($code == 226) { $novoX .= 'A'; continue; }
            if ($code == 227) { $novoX .= 'A'; continue; }
            if ($code == 228) { $novoX .= 'A'; continue; }
            if ($code == 229) { $novoX .= 'A'; continue; }
            if ($code == 231) { $novoX .= 'C'; continue; }
            if ($code == 232) { $novoX .= 'E'; continue; }
            if ($code == 233) { $novoX .= 'E'; continue; }
            if ($code == 234) { $novoX .= 'E'; continue; }
            if ($code == 235) { $novoX .= 'E'; continue; }
            if ($code == 236) { $novoX .= 'I'; continue; }
            if ($code == 237) { $novoX .= 'I'; continue; }
            if ($code == 238) { $novoX .= 'I'; continue; }
            if ($code == 239) { $novoX .= 'I'; continue; }
            if ($code == 242) { $novoX .= 'O'; continue; }
            if ($code == 243) { $novoX .= 'O'; continue; }
            if ($code == 244) { $novoX .= 'O'; continue; }
            if ($code == 245) { $novoX .= 'O'; continue; }
            if ($code == 246) { $novoX .= 'O'; continue; }
            if ($code == 249) { $novoX .= 'U'; continue; }
            if ($code == 250) { $novoX .= 'U'; continue; }
            if ($code == 251) { $novoX .= 'U'; continue; }
            if ($code == 252) { $novoX .= 'U'; continue; }
            $novoX .= $x[$i];
        }
        
        $x = $novoX;

        
        $cCharPermitido='&ABCDEFGHIJLKYMNOPQRSTUVXWZ 1234567890()[]{}.,+_-:/=%?;!@|$"<>';
        for ($n = 0;$n<strlen($x);$n++)
        {
             $c=substr($x,$n,1);
             if (strpos($cCharPermitido,strtoupper($c))!==FALSE)
             {
                $r.=$c;
             }
             else
             {
                $r.="";
             }
        }
        $x=$r;
    
        $x = str_replace(chr(13),"",$x);
        $x = str_replace(chr(10),"",$x);
                
        return "$x";
    }


}