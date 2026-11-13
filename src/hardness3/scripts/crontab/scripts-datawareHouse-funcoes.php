<?php
namespace hardness;

/**
* Funções!
*/

/**
* Apaga e recria as tabelas
*/
function carregaTabelas() {
    global $tables, $confUsuarioClickHouse, $g;

    verificarConexaoClickhouse();

    $sql = "SHOW DATABASES LIKE " . $confUsuarioClickHouse['dbDatabase'];
    $result = mysqli_query_clickHoue($sql);
    if (empty($result) || !$result)
    {
        //Conexão do banco da ClickHouse
        $conClickHouse=mysqli_connect($confUsuarioClickHouse['dbHost'], $confUsuarioClickHouse['dbUser'], $confUsuarioClickHouse['dbPass'], 'e998' , $confUsuarioClickHouse['dbPort']);
        $g['conexaoBancoClickHouse'] = $conClickHouse;

        $sql = "CREATE DATABASE IF NOT EXISTS " . $confUsuarioClickHouse['dbDatabase'];
        $result = mysqli_query_clickHoue($sql);
        if (!$result) { die('Invalid query: ' . mysqli_error_clickHouse() . "\n<br/>SQL: " . $sql); }

        //Conexão do banco da ClickHouse
        $conClickHouse=mysqli_connect($confUsuarioClickHouse['dbHost'], $confUsuarioClickHouse['dbUser'], $confUsuarioClickHouse['dbPass'], $confUsuarioClickHouse['dbDatabase'], $confUsuarioClickHouse['dbPort']);
        $g['conexaoBancoClickHouse'] = $conClickHouse;

    }

    echo "Removendo tabelas...\n<br />";

    foreach ($tables as $table) {
        $sql = "DROP TABLE IF EXISTS {$table};";
        $result = mysqli_query_clickHoue($sql);
        if (!$result) { die('Invalid query: ' . mysqli_error_clickHouse() . "\n<br/>SQL: " . $sql); }
    }
    echo "Criando tabelas...\n<br />";

    $sql = <<<EOT
CREATE TABLE FAC001 (
  FAC001_Id UInt64,
  FAC001_DIM001_Id UInt64,
  FAC001_DIM002_Id UInt64,
  FAC001_DIM003_Id UInt64,
  FAC001_DIM004_Id_Vendedor UInt64,
  FAC001_DIM004_Id_Supervisor UInt64,
  FAC001_DIM005_Id UInt64,
  FAC001_DIM006_Id UInt64,
  FAC001_DIM007_Id UInt64,
  FAC001_DIM008_Id UInt64,
  FAC001_DIM009_Id UInt64,
  FAC001_DIM010_Id UInt64,
  FAC001_DIM011_Id UInt64,
  FAC001_DIM012_Id UInt64,
  FAC001_DIM013_Id UInt64,
  FAC001_FLAG_CANCELADO FixedString(1),
  FAC001_FLAG_SALDO FixedString(1),
  FAC001_NF_Quantidade DECIMAL(20,5),
  FAC001_NF_Valor_Total_Preco DECIMAL(20,5),
  FAC001_NF_Valor_Total_Custo DECIMAL(20,5),
  FAC001_NF_Produtos_Quantidade_Itens DECIMAL(20,5),
  FAC001_NF_Produtos_Quantidade DECIMAL(20,5),
  FAC001_NF_ICMS DECIMAL(20,5),
  FAC001_NF_ICMS_ST DECIMAL(20,5),
  FAC001_NF_IPI DECIMAL(20,5),
  FAC001_NF_PIS DECIMAL(20,5),
  FAC001_NF_COFINS DECIMAL(20,5),
  FAC001_Orcamento_Valor_Total_Preco DECIMAL(20,5),
  FAC001_CR_Valor_Duplicata DECIMAL(20,5),
  FAC001_CP_Valor_Duplicata DECIMAL(20,5),
  FAC001_LB_Valor_Lancamento DECIMAL(20,5),
  FAC001_LB_Valor_Lancamento_Pago DECIMAL(20,5),
  FAC001_LB_Valor_Lancamento_Recebido DECIMAL(20,5),
  FAC001_LB_Valor_Saldo DECIMAL(20,5),
  FAC001_LB_Valor_Saldo_Inicial DECIMAL(20,5),
  FAC001_Pedidos_Valor_Total_Preco DECIMAL(20,5),
  FAC001_NFR_Valor_Total_Custo DECIMAL(20,5),
  FAC001_NFR_Produtos_Quantidade_Itens DECIMAL(20,5),
  FAC001_NFR_Produtos_Quantidade DECIMAL(20,5),
  FAC001_NFR_Quantidade DECIMAL(20,5),
  FAC001_NFR_ICMS_ST DECIMAL(20,5),
  FAC001_NFR_ICMS_IPI DECIMAL(20,5),
  FAC001_NFR_ICMS_PIS DECIMAL(20,5),
  FAC001_NFR_ICMS_COFINS DECIMAL(20,5),
  FAC001_NFR_ICMS DECIMAL(20,5),
  FAC001_COT_Quantidade DECIMAL(20,5),
  FAC001_COT_Valor_Total_Preco DECIMAL(20,5),
  FAC001_COT_Produtos_Quantidade_Itens DECIMAL(20,5),
  FAC001_COT_Produtos_Quantidade DECIMAL(20,5),
  FAC001_ORDCOMP_Quantidade DECIMAL(20,5),
  FAC001_ORDCOMP_Valor_Total_Preco DECIMAL(20,5),
  FAC001_ORDCOMP_Produtos_Quantidade_Itens DECIMAL(20,5),
  FAC001_ORDCOMP_Produtos_Quantidade DECIMAL(20,5),
  FAC001_PROJ_Valor_Projetado DECIMAL(20,5),
  FAC001_PROJ_Valor_Saldo DECIMAL(20,5),
  FAC001_HIST_Valor_Preco DECIMAL(20,5),
  FAC001_HIST_Quantidade_Entrada DECIMAL(20,5),
  FAC001_HIST_Valor_Entrada DECIMAL(20,5),
  FAC001_HIST_Quantidade_Saida DECIMAL(20,5),
  FAC001_HIST_Valor_Saida DECIMAL(20,5),
  FAC001_HIST_Quantidade_Saldo DECIMAL(20,5),
  FAC001_HIST_Quantidade_Saldo_Inicial DECIMAL(20,5),
  FAC001_HIST_Valor_Saldo DECIMAL(20,5),
  FAC001_HIST_Valor_Saldo_Inicial DECIMAL(20,5),
  FAC001_HIST_Quantidade DECIMAL(20,5))
ENGINE = MergeTree
ORDER BY FAC001_Id
EOT;
    $result = mysqli_query_clickHoue($sql);
    if (!$result) { die('Invalid query: ' . mysqli_error_clickHouse() . "\n<br/>SQL: " . $sql); }
    $sql = <<<EOT
CREATE TABLE DIM001 (
  DIM001_Id UInt64,
  DIM001_Data Date,
  DIM001_Dia FixedString(2),
  DIM001_Dia_Da_Semana UInt64,
  DIM001_Mes UInt64,
  DIM001_Ano UInt64)
ENGINE = MergeTree
ORDER BY DIM001_Id

EOT;
    $result = mysqli_query_clickHoue($sql);
    if (!$result) { die('Invalid query: ' . mysqli_error_clickHouse() . "\n<br/>SQL: " . $sql); }
    $sql = <<<EOT
CREATE TABLE DIM002 (
  DIM002_Id UInt64,
  DIM002_Md5 FixedString(32),
  DIM002_Nome_Empresa String,
  DIM002_Nome_Fantasia String,
  DIM002_E_Mail String)
ENGINE = MergeTree
ORDER BY DIM002_Id
EOT;
    $result = mysqli_query_clickHoue($sql);
    if (!$result) { die('Invalid query: ' . mysqli_error_clickHouse() . "\n<br/>SQL: " . $sql); }
    $sql = <<<EOT
CREATE TABLE DIM003 (
  DIM003_Id UInt64,
  DIM003_Md5 FixedString(32),
  DIM003_Endereco String,
  DIM003_Cep FixedString(20),
  DIM003_Numero String,
  DIM003_Bairro String,
  DIM003_Cidade String,
  DIM003_Estado FixedString(2))
ENGINE = MergeTree
ORDER BY DIM003_Id
EOT;
    $result = mysqli_query_clickHoue($sql);
    if (!$result) { die('Invalid query: ' . mysqli_error_clickHouse() . "\n<br/>SQL: " . $sql); }
    $sql = <<<EOT
CREATE TABLE DIM004 (
  DIM004_Id UInt64,
  DIM004_Md5 FixedString(32),
  DIM004_Login FixedString(30),
  DIM004_Email String,
  DIM004_Nome String,
  DIM004_Primeiro_Nome String,
  DIM004_C007_Id FixedString(255))
ENGINE = MergeTree
ORDER BY DIM004_Id
EOT;
    $result = mysqli_query_clickHoue($sql);
    if (!$result) { die('Invalid query: ' . mysqli_error_clickHouse() . "\n<br/>SQL: " . $sql); }
    $sql = <<<EOT
CREATE TABLE DIM005 (
  DIM005_Id UInt64,
  DIM005_Data Date,
  DIM005_Dia UInt64,
  DIM005_Dia_Da_Semana UInt64,
  DIM005_Mes UInt64,
  DIM005_Ano UInt64)
ENGINE = MergeTree
ORDER BY DIM005_Id
EOT;
    $result = mysqli_query_clickHoue($sql);
    if (!$result) { die('Invalid query: ' . mysqli_error_clickHouse() . "\n<br/>SQL: " . $sql); }
    $sql = <<<EOT
CREATE TABLE DIM006 (
  DIM006_Id UInt64,
  DIM006_Data Date,
  DIM006_Dia UInt64,
  DIM006_Dia_Da_Semana UInt64,
  DIM006_Mes UInt64,
  DIM006_Ano UInt64)
ENGINE = MergeTree
ORDER BY DIM006_Id
EOT;
    $result = mysqli_query_clickHoue($sql);
    if (!$result) { die('Invalid query: ' . mysqli_error_clickHouse() . "\n<br/>SQL: " . $sql); }
    $sql = <<<EOT
CREATE TABLE DIM007 (
  DIM007_Id UInt64,
  DIM007_Conta FixedString(50),
  DIM007_Flag_Deb_Crd FixedString(1) DEFAULT '')
ENGINE MergeTree
ORDER BY DIM007_Id
EOT;
    $result = mysqli_query_clickHoue($sql);
    if (!$result) { die('Invalid query: ' . mysqli_error_clickHouse() . "\n<br/>SQL: " . $sql); }
    $sql = <<<EOT
CREATE TABLE DIM008 (
  DIM008_Id UInt64,
  DIM008_Md5 FixedString(32),
  DIM008_D006_Descricao String,
  DIM008_D006_Flag_Entrada_Saida FixedString(1))
ENGINE = MergeTree
ORDER BY DIM008_Id
EOT;
    $result = mysqli_query_clickHoue($sql);
    if (!$result) { die('Invalid query: ' . mysqli_error_clickHouse() . "\n<br/>SQL: " . $sql); }
    $sql = <<<EOT
CREATE TABLE DIM009 (
  DIM009_Id UInt64,
  DIM009_Md5 FixedString(32),
  DIM009_D001_Descricao_Produto String,
  DIM009_D082_Marca FixedString(100),
  DIM009_C008_Tipo FixedString(100),
  DIM009_D003_Nome_Linha FixedString(100),
  DIM009_D001_Codigo_Produto FixedString(100))
ENGINE MergeTree
ORDER BY DIM009_Id
EOT;
    $result = mysqli_query_clickHoue($sql);
    if (!$result) { die('Invalid query: ' . mysqli_error_clickHouse() . "\n<br/>SQL: " . $sql); }
    $sql = <<<EOT
CREATE TABLE DIM010 (
  DIM010_Id UInt64,
  DIM010_Md5 FixedString(32),
  DIM010_D006_Codigo_CFOP_e_Descricao String)
ENGINE = MergeTree
ORDER BY DIM010_Id
EOT;
    $result = mysqli_query_clickHoue($sql);
    if (!$result) { die('Invalid query: ' . mysqli_error_clickHouse() . "\n<br/>SQL: " . $sql); }
    $sql = <<<EOT
CREATE TABLE DIM011 (
  DIM011_Id UInt64,
  DIM011_Md5 FixedString(32),
  DIM011_Nome_Empresa String)
ENGINE = MergeTree
ORDER BY DIM011_Id
EOT;
    $result = mysqli_query_clickHoue($sql);
    if (!$result) { die('Invalid query: ' . mysqli_error_clickHouse() . "\n<br/>SQL: " . $sql); }
    $sql = <<<EOT
CREATE TABLE DIM012 (
  DIM012_Id UInt64,
  DIM012_Nome_Empresa String,
  DIM012_Nome_Abreviado String)
ENGINE = MergeTree
ORDER BY DIM012_Id
EOT;
    $result = mysqli_query_clickHoue($sql);
    if (!$result) { die('Invalid query: ' . mysqli_error_clickHouse() . "\n<br/>SQL: " . $sql); }
    $sql = <<<EOT
CREATE TABLE DIM013 (
  DIM013_Id UInt64,
  DIM013_Nome_Caixa String,
  DIM013_Nome_Banco String)
ENGINE = MergeTree
ORDER BY DIM013_Id
EOT;
    $result = mysqli_query_clickHoue($sql);
    if (!$result) { die('Invalid query: ' . mysqli_error_clickHouse() . "\n<br/>SQL: " . $sql); }
}

/**
* Adiciona um registro na dimensão DATA
* 
* @param int $data
* @param string $tipo	DIM001 / DIM005 / DIM006
* @return int DIM001_Id / DIM005_Id / DIM006_Id
*/
function addData($data, $tipo) {
	global $cache;
	Date_default_timezone_set('America/Sao_Paulo');
	if (empty($data)) { return false; }
	if (isset($cache['data'][$data.$tipo])) {
		return $cache['data'][$data.$tipo];
	}
	$sql = "SELECT {$tipo}_Id FROM {$tipo} WHERE {$tipo}_Data = '{$data}';";
	$resultData = mysqli_query_clickHoue($sql);
	if (!$resultData) { die('Invalid query: ' . mysqli_error_clickHouse() . "\n<br/>SQL: " . $sql); }
	if (mysqli_num_rows_clickHouse($resultData) > 0) {
		$rowData = mysql_fetch_assoc($resultData);
		$cache['data'][$data.$tipo] = $rowData[$tipo.'_Id'];
		return $rowData[$tipo.'_Id'];
	} else {
		$cache['insert_id']+=1;
		$data_ = explode('-', $data);
		$data_ = mktime(0, 0, 0, $data_[1], $data_[2], $data_[0]);
		$sql = "INSERT INTO {$tipo} ({$tipo}_Id, {$tipo}_Data, {$tipo}_Dia, {$tipo}_Dia_Da_Semana, {$tipo}_Mes, {$tipo}_Ano) VALUES ({$cache['insert_id']}, '{$data}', '".str_pad(Date('d', $data_), 2, "0", STR_PAD_LEFT)."', ".Date('N', $data_).", ".Date('m', $data_).", ".Date('Y', $data_).");";
		$resultData = mysqli_query_clickHoue($sql);
		if (!$resultData) { die('Invalid query: ' . mysqli_error_clickHouse() . "\n<br/>SQL: " . $sql); }
		return $cache['insert_id'];
	}
}

/**
* Adiciona um registro na dimensão EMPRESA
* 
* @param int $D024_Id
* @return int DIM002_Id
*/
function addClienteFornecedor($D024_Id) {
	global $cache;
	if (empty($D024_Id)) { return false; }
	// Busca empresa
	if (isset($cache['clienteFornecedor'][$D024_Id])) {
		$row = array();
		$row['D024_Nome_Empresa'] = $cache['clienteFornecedor'][$D024_Id][0];
		$row['D024_Nome_Fantasia'] = $cache['clienteFornecedor'][$D024_Id][1];
		$row['D024_E_Mail'] = $cache['clienteFornecedor'][$D024_Id][2];
		$md5 = md5($row['D024_Nome_Empresa'] . $row['D024_Nome_Fantasia'] . $row['D024_E_Mail']);
	} else {
		$sql = "SELECT D024_Nome_Empresa, D024_Nome_Fantasia, D024_E_Mail FROM D024 WHERE D024_Id = '{$D024_Id}';";
		$result = mysqli_query($sql);
		if (!$result) { die('Invalid query: ' . mysqli_error() . "\n<br/>SQL: " . $sql); }
		if (mysqli_num_rows($result) == 0) { return false; }
		$row = mysql_fetch_assoc($result);
		$md5 = md5($row['D024_Nome_Empresa'] . $row['D024_Nome_Fantasia'] . $row['D024_E_Mail']);
		$cache['clienteFornecedor'][$D024_Id] = array($row['D024_Nome_Empresa'], $row['D024_Nome_Fantasia'], $row['D024_E_Mail']);
	}
	if (isset($cache['clienteFornecedorMD5'][$md5])) {
		return $cache['clienteFornecedorMD5'][$md5];
	}
	// Verifica se já exista na dimensão
	$sql = "SELECT DIM002_Id FROM DIM002 WHERE DIM002_Md5 = '{$md5}';";
	$resultEmp = mysqli_query_clickHoue($sql);
	if (!$resultEmp) { die('Invalid query: ' . mysqli_error_clickHouse() . "\n<br/>SQL: " . $sql); }
	if (mysqli_num_rows_clickHouse($resultEmp) > 0) {
		$rowEmp = mysql_fetch_assoc($resultEmp);
		$cache['clienteFornecedorMD5'][$md5] = $rowEmp['DIM002_Id'];
		return $rowEmp['DIM002_Id'];
	} else {
		$row['D024_Nome_Empresa'] = mysql_real_escape_string($row['D024_Nome_Empresa']);
		$row['D024_Nome_Fantasia'] = mysql_real_escape_string($row['D024_Nome_Fantasia']);
		$row['D024_E_Mail'] = mysql_real_escape_string($row['D024_E_Mail']);
		$md5 = mysql_real_escape_string($md5);
		$cache['insert_id']+=1;
		$sql = "INSERT INTO DIM002 (DIM002_Id,DIM002_Md5, DIM002_Nome_Empresa, DIM002_Nome_Fantasia, DIM002_E_Mail) VALUES ({$cache['insert_id']},'{$md5}', '{$row['D024_Nome_Empresa']}', '{$row['D024_Nome_Fantasia']}', '{$row['D024_E_Mail']}');";
		$resultEmp = mysqli_query_clickHoue($sql);
		if (!$resultEmp) { die('Invalid query: ' . mysqli_error_clickHouse() . "\n<br/>SQL: " . $sql); }
		return $cache['insert_id'];
	}
}

/**
* Adiciona um registro na dimensão REPRESENTANTE
* 
* @param int $D021_Id
* @return int DIM011_Id
*/
function addRepresentante($D021_Id) {
	global $cache;
	if (empty($D021_Id)) { return false; }
	// Busca empresa
	if (isset($cache['representante'][$D021_Id])) {
		$row = array();
		$row['D021_Nome_Empresa'] = $cache['representante'][$D021_Id][0];
		$md5 = md5($row['D021_Nome_Empresa']);
	} else {
		$sql = "SELECT D021_Nome_Empresa FROM D021 WHERE D021_Id = '{$D021_Id}';";
		$result = mysqli_query($sql);
		if (!$result) { die('Invalid query: ' . mysqli_error() . "\n<br/>SQL: " . $sql); }
		if (mysqli_num_rows($result) == 0) { return false; }
		$row = mysql_fetch_assoc($result);
		$md5 = md5($row['D021_Nome_Empresa']);
		$cache['representante'][$D021_Id] = array($row['D021_Nome_Empresa']);
	}
	if (isset($cache['representanteMD5'][$md5])) {
		return $cache['representanteMD5'][$md5];
	}
	// Verifica se já exista na dimensão
	$sql = "SELECT DIM011_Id FROM DIM011 WHERE DIM011_Md5 = '{$md5}';";
	$resultEmp = mysqli_query_clickHoue($sql);
	if (!$resultEmp) { die('Invalid query: ' . mysqli_error_clickHouse() . "\n<br/>SQL: " . $sql); }
	if (mysqli_num_rows_clickHouse($resultEmp) > 0) {
		$rowEmp = mysql_fetch_assoc($resultEmp);
		$cache['representanteMD5'][$md5] = $rowEmp['DIM011_Id'];
		return $rowEmp['DIM011_Id'];
	} else {
		$row['D021_Nome_Empresa'] = mysql_real_escape_string($row['D021_Nome_Empresa']);
		$md5 = mysql_real_escape_string($md5);
		$cache['insert_id']+=1;
		$sql = "INSERT INTO DIM011 (DIM011_Id, DIM011_Md5, DIM011_Nome_Empresa) VALUES ({$cache['insert_id']},'{$md5}', '{$row['D021_Nome_Empresa']}');";
		$resultEmp = mysqli_query_clickHoue($sql);
		if (!$resultEmp) { die('Invalid query: ' . mysqli_error_clickHouse() . "\n<br/>SQL: " . $sql); }
		return $cache['insert_id'];
	}
}

/**
* Adiciona um registro na dimensão USUÁRIO
* 
* @param int $D024_Id
* @return int DIM002_Id
*/
function addUsuario($C007_Id) {
	global $cache;
	if (empty($C007_Id)) { return false; }
	// Busca empresa
	if (isset($cache['usuario'][$C007_Id])) {
		$row = array();
		$row['C007_Login'] = $cache['usuario'][$C007_Id][0];
		$row['C007_Email'] = $cache['usuario'][$C007_Id][1];
		$row['C007_Nome'] = $cache['usuario'][$C007_Id][2];
		$row['C007_Primeiro_Nome'] = $cache['usuario'][$C007_Id][3];
        $row['C007_Id'] = $cache['usuario'][$C007_Id][4];
		$md5 = md5($row['C007_Login'] . $row['C007_Email'] . $row['C007_Nome'] . $row['C007_Primeiro_Nome'] . $row['C007_Id']);
	} else {
		$sql = "SELECT C007_Id, C007_Login, C007_Email, C007_Nome, C007_Primeiro_Nome FROM C007 WHERE C007_Id = '{$C007_Id}';";
		$result = mysqli_query($sql);
		if (!$result) { die('Invalid query: ' . mysqli_error() . "\n<br/>SQL: " . $sql); }
		if (mysqli_num_rows($result) == 0) { return false; }
		$row = mysql_fetch_assoc($result);
		$md5 = md5($row['C007_Login'] . $row['C007_Email'] . $row['C007_Nome'] . $row['C007_Primeiro_Nome'] . $row['C007_Id']);
		$cache['usuario'][$C007_Id] = array($row['C007_Login'], $row['C007_Email'], $row['C007_Nome'], $row['C007_Primeiro_Nome'], $row['C007_Id']);
	}
	// Verifica se já exista na dimensão
	if (isset($cache['usuarioMD5'][$md5])) {
		return $cache['usuarioMD5'][$md5];
	}
	$sql = "SELECT DIM004_Id FROM DIM004 WHERE DIM004_Md5 = '{$md5}';";
	$resultUsr = mysqli_query_clickHoue($sql);
	if (!$resultUsr) { die('Invalid query: ' . mysqli_error_clickHouse() . "\n<br/>SQL: " . $sql); }
	if (mysqli_num_rows_clickHouse($resultUsr) > 0) {
		$rowUsr = mysql_fetch_assoc($resultUsr);
		$cache['usuarioMD5'][$md5] = $rowUsr['DIM004_Id'];
		return $rowUsr['DIM004_Id'];
	} else {
		$row['C007_Login'] = mysql_real_escape_string($row['C007_Login']);
		$row['C007_Email'] = mysql_real_escape_string($row['C007_Email']);
		$row['C007_Nome'] = mysql_real_escape_string($row['C007_Nome']);
		$row['C007_Primeiro_Nome'] = mysql_real_escape_string($row['C007_Primeiro_Nome']);
        $row['C007_Id'] = mysql_real_escape_string($row['C007_Id']);
		$md5 = mysql_real_escape_string($md5);
		$cache['insert_id']+=1;		
		$sql = "INSERT INTO DIM004 (DIM004_Id, DIM004_Md5, DIM004_Login, DIM004_Email, DIM004_Nome, DIM004_Primeiro_Nome, DIM004_C007_Id) VALUES ({$cache['insert_id']},'{$md5}', '{$row['C007_Login']}', '{$row['C007_Email']}', '{$row['C007_Nome']}', '{$row['C007_Primeiro_Nome']}', '{$row['C007_Id']}');";
		$resultUsr = mysqli_query_clickHoue($sql);
		if (!$resultUsr) { die('Invalid query: ' . mysqli_error_clickHouse() . "\n<br/>SQL: " . $sql); }
		return $cache['insert_id'];
	}
}

/**
* Adiciona um registro na dimensão LOCAL
* 
* @param int $D024_Id
* @return int DIM002_Id
*/
function addClienteFornecedorLocal($D024_Id) {
	global $cache;
	if (empty($D024_Id)) { return false; }
	// Busca empresa
	if (isset($cache['clienteFornecedorLocal'][$D024_Id])) {
		$row = array();
		$row['D024_Endereco'] = $cache['clienteFornecedorLocal'][$D024_Id][0];
		$row['D024_Cep'] = $cache['clienteFornecedorLocal'][$D024_Id][1];
		$row['D024_Numero'] = $cache['clienteFornecedorLocal'][$D024_Id][2];
		$row['D024_Bairro'] = $cache['clienteFornecedorLocal'][$D024_Id][3];
		$row['D020_Nome_Cidade'] = $cache['clienteFornecedorLocal'][$D024_Id][4];
		$row['D018_UF'] = $cache['clienteFornecedorLocal'][$D024_Id][5];
		$md5 = md5($row['D024_Endereco'] . $row['D024_Cep'] . $row['D024_Numero'] . $row['D024_Bairro'] . $row['D020_Nome_Cidade'] . $row['D018_UF']);
	} else {
		$sql = "SELECT D024_Id, D024_Endereco, D024_Cep, D024_Numero, D024_Bairro, D024_Cidade, D018_UF, D020_Nome_Cidade FROM D024 LEFT JOIN D018 ON D018_Id=D024_D018_Id LEFT JOIN D020 ON D020_Id=D024_D020_Id WHERE D024_Id = '{$D024_Id}';";
		$result = mysqli_query($sql);
		if (!$result) { die('Invalid query: ' . mysqli_error() . "\n<br/>SQL: " . $sql); }
		if (mysqli_num_rows($result) == 0) { return false; }
		$row = mysql_fetch_assoc($result);
		$md5 = md5($row['D024_Endereco'] . $row['D024_Cep'] . $row['D024_Numero'] . $row['D024_Bairro'] . $row['D020_Nome_Cidade'] . $row['D018_UF']);
		$cache['clienteFornecedorLocal'][$D024_Id] = array($row['D024_Endereco'], $row['D024_Cep'], $row['D024_Numero'], $row['D024_Bairro'], $row['D020_Nome_Cidade'], $row['D018_UF']);
	}
	// Verifica se já exista na dimensão
	if (isset($cache['clienteFornecedorLocalMD5'][$md5])) {
		return $cache['clienteFornecedorLocalMD5'][$md5];
	}
	$sql = "SELECT DIM003_Id FROM DIM003 WHERE DIM003_Md5 = '{$md5}';";
	$resultEmp = mysqli_query_clickHoue($sql);
	if (!$resultEmp) { die('Invalid query: ' . mysqli_error_clickHouse() . "\n<br/>SQL: " . $sql); }
	if (mysqli_num_rows_clickHouse($resultEmp) > 0) {
		$rowEmp = mysql_fetch_assoc($resultEmp);
		$cache['clienteFornecedorLocalMD5'][$md5] = $rowEmp['DIM003_Id'];
		return $rowEmp['DIM003_Id'];
	} else {
		$row['D024_Endereco'] = mysql_real_escape_string($row['D024_Endereco']);
		$row['D024_Cep'] = mysql_real_escape_string($row['D024_Cep']);
		$row['D024_Numero'] = mysql_real_escape_string($row['D024_Numero']);
		$row['D024_Bairro'] = mysql_real_escape_string($row['D024_Bairro']);
		$row['D020_Nome_Cidade'] = mysql_real_escape_string($row['D020_Nome_Cidade']);
		$row['D018_UF'] = mysql_real_escape_string($row['D018_UF']);
		$md5 = mysql_real_escape_string($md5);
		$cache['insert_id']+=1;
		$sql = "INSERT INTO DIM003 (DIM003_Id,DIM003_Md5, DIM003_Endereco, DIM003_Cep, DIM003_Numero, DIM003_Bairro, DIM003_Cidade, DIM003_Estado) VALUES ({$cache['insert_id']},'{$md5}', '{$row['D024_Endereco']}', '{$row['D024_Cep']}', '{$row['D024_Numero']}', '{$row['D024_Bairro']}', '{$row['D020_Nome_Cidade']}', '{$row['D018_UF']}');";
		$resultEmp = mysqli_query_clickHoue($sql);
		if (!$resultEmp) { die('Invalid query: ' . mysqli_error_clickHouse() . "\n<br/>SQL: " . $sql); }
		return $cache['insert_id'];
	}
}

/**
* Adiciona um registro na dimensão CONTA
* 
* @param int $D014_Id
* @return DIM007_Id
*/
function addConta($D014_Id, $Flag_Deb_Crd = '') {
	global $cache;
	// Busca empresa
	if (isset($cache['conta'][$D014_Id])) {
		$row = array();
		$row['D014_SubConta'] = mysql_real_escape_string($cache['conta'][$D014_Id]);
	} else {
		$sql = "SELECT D014_Id, D014_SubConta FROM D014 WHERE D014_Id = '{$D014_Id}';";	
		$result = mysqli_query($sql);
		if (!$result) { die('Invalid query: ' . mysqli_error() . "\n<br/>SQL: " . $sql); }
		if (mysqli_num_rows($result) == 0) { return false; }
		$row = mysql_fetch_assoc($result);
		$row['D014_SubConta'] = mysql_real_escape_string($row['D014_SubConta']);
	}
	// Verifica se já exista na dimensão
	if (isset($cache['contaMD5'][$D014_Id . $Flag_Deb_Crd])) {
		return $cache['contaMD5'][$D014_Id] . $Flag_Deb_Crd;
	}
	if (empty($D014_Id)) { return false; }
	$sql = "SELECT DIM007_Id FROM DIM007 WHERE DIM007_Conta = '{$row['D014_SubConta']}' AND DIM007_Flag_Deb_Crd = '{$Flag_Deb_Crd}';";
	$resultData = mysqli_query_clickHoue($sql);
	if (!$resultData) { die('Invalid query: ' . mysqli_error_clickHouse() . "\n<br/>SQL: " . $sql); }
	if (mysqli_num_rows_clickHouse($resultData) > 0) {
		$rowData = mysql_fetch_assoc($resultData);
		return $cache['contaMD5'][$D014_Id . $Flag_Deb_Crd] = $rowData['DIM007_Id'];
	} else {
		$cache['insert_id']+=1;
		$sql = "INSERT INTO DIM007 (DIM007_Id, DIM007_Conta, DIM007_Flag_Deb_Crd) VALUES ({$cache['insert_id']},'{$row['D014_SubConta']}', '{$Flag_Deb_Crd}');";
		$resultData = mysqli_query_clickHoue($sql);
		if (!$resultData) { die('Invalid query: ' . mysqli_error_clickHouse() . "\n<br/>SQL: " . $sql); }
		return $cache['insert_id'];
	}
}

/**
* Adiciona um registro na dimensão FILTROS
* 
* @param int $D006_Id
* @return DIM008_Id
*/
function addFiltros($D006_Id = false) {
	global $cache;
	if (!empty($D006_Id)) {
		// NF
		if (isset($cache['filtro'][$D006_Id])) {
			$row = array();
			$row['D006_Descricao'] = $cache['filtro'][$D006_Id][0];
			$row['D006_Flag_Entrada_Saida'] = $cache['filtro'][$D006_Id][1];
			$md5 = md5($row['D006_Descricao'] . $row['D006_Flag_Entrada_Saida']);
		} else {
			$sql = "SELECT D006_Id, D006_Descricao, D006_Flag_Entrada_Saida FROM D006 WHERE D006_Id = '{$D006_Id}';";
			$result = mysqli_query($sql);
			if (!$result) { die('Invalid query: ' . mysqli_error() . "\n<br/>SQL: " . $sql); }
			if (mysqli_num_rows($result) == 0) { return false; }
			$row = mysql_fetch_assoc($result);
			$md5 = md5($row['D006_Descricao'] . $row['D006_Flag_Entrada_Saida']);
			$cache['filtro'][$D006_Id] = array($row['D006_Descricao'], $row['D006_Flag_Entrada_Saida']);
		}
		// Verifica se já exista na dimensão
		if (isset($cache['filtroMD5'][$md5])) {
			return $cache['filtroMD5'][$md5];
		}
		$sql = "SELECT DIM008_Id FROM DIM008 WHERE DIM008_Md5 = '{$md5}';";
		$resultEmp = mysqli_query_clickHouse($sql);
		if (!$resultEmp) { die('Invalid query: ' . mysqli_error_clickHouse() . "\n<br/>SQL: " . $sql); }
		if (mysqli_num_rows_clickHouse($resultEmp) > 0) {
			$rowEmp = mysql_fetch_assoc($resultEmp);
			$cache['filtroMD5'][$md5] = $rowEmp['DIM008_Id'];
			return $rowEmp['DIM008_Id'];
		} else {
			$row['D006_Descricao'] = mysql_real_escape_string($row['D006_Descricao']);
			$row['D006_Flag_Entrada_Saida'] = mysql_real_escape_string($row['D006_Flag_Entrada_Saida']);
			$md5 = mysql_real_escape_string($md5);
			$cache['insert_id']+=1;
			$sql = "INSERT INTO DIM008 (DIM008_Id, DIM008_Md5, DIM008_D006_Descricao, DIM008_D006_Flag_Entrada_Saida) VALUES ({$cache['insert_id']}, '{$md5}', '{$row['D006_Descricao']}', '{$row['D006_Flag_Entrada_Saida']}');";
			$resultEmp = mysqli_query_clickHoue($sql);
			if (!$resultEmp) { die('Invalid query: ' . mysqli_error_clickHouse() . "\n<br/>SQL: " . $sql); }
			return $cache['insert_id'];
		}
	}	
}

/**
* Adiciona um registro na dimensão PRODUTOS
* 
* @param int D009_Id
* @return DIM009_Id
*/
function addProduto($D009_Id = false) {
	global $cache;
	if (!empty($D009_Id)) {
		// NF
		if (isset($cache['produto'][$D009_Id])) {
			$row = array();
			$row['D001_Descricao_Produto'] = $cache['produto'][$D009_Id][0];
			$row['D082_Marca'] = $cache['produto'][$D009_Id][1];
			$row['C008_Tipo'] = $cache['produto'][$D009_Id][2];
			$row['D003_Nome_Linha'] = $cache['produto'][$D009_Id][3];
			$row['D001_Codigo_Produto'] = $cache['produto'][$D009_Id][4];
			$md5 = md5($row['D001_Descricao_Produto'] . $row['D082_Marca'] . $row['C008_Tipo'] . $row['D003_Nome_Linha'] . $row['D001_Codigo_Produto']);
		} else {
			$sql = <<<EOT
				SELECT 
					D009_Id, 
					D001_Codigo_Produto,
					D001_Descricao_Produto, 
					D082_Marca, 
					C008_Tipo,
					D003_Nome_Linha
				FROM 
					D009 
				LEFT JOIN D049 ON D049_Id=D009_D049_Id 
				LEFT JOIN D001 ON D001_Id=D049_D001_Id 
				LEFT JOIN C008 ON C008_Id=D001_C008_Id 
				LEFT JOIN D082 ON D082_Id=D049_D082_Id 
				LEFT JOIN D003 ON D003_Id=D001_D003_Id
				WHERE 
					D009_Id = '{$D009_Id}';
EOT;
			$result = mysqli_query($sql);
			if (!$result) { die('Invalid query: ' . mysqli_error() . "\n<br/>SQL: " . $sql); }
			if (mysqli_num_rows($result) == 0) { return false; }
			$row = mysql_fetch_assoc($result);
			$md5 = md5($row['D001_Descricao_Produto'] . $row['D082_Marca'] . $row['C008_Tipo'] . $row['D003_Nome_Linha'] . $row['D001_Codigo_Produto']);
			$cache['produto'][$D009_Id] = array($row['D001_Descricao_Produto'], $row['D082_Marca'], $row['C008_Tipo'], $row['D003_Nome_Linha'], $row['D001_Codigo_Produto']);
		}
		// Verifica se já exista na dimensão
		if (isset($cache['produtoMD5'][$md5])) {
			return $cache['produtoMD5'][$md5];
		}
		$sql = "SELECT DIM009_Id FROM DIM009 WHERE DIM009_Md5 = '{$md5}';";
		$resultEmp = mysqli_query_clickHoue($sql);
		if (!$resultEmp) { die('Invalid query: ' . mysqli_error_clickHouse() . "\n<br/>SQL: " . $sql); }
		if (mysqli_num_rows_clickHouse($resultEmp) > 0) {
			$rowEmp = mysql_fetch_assoc($resultEmp);
			$cache['produtoMD5'][$md5] = $rowEmp['DIM009_Id'];
			return $rowEmp['DIM009_Id'];
		} else {
			$row['D001_Descricao_Produto'] = mysql_real_escape_string($row['D001_Codigo_Produto'].' - '.$row['D001_Descricao_Produto']);
			$row['D082_Marca'] = mysql_real_escape_string($row['D082_Marca']);
			$row['C008_Tipo'] = mysql_real_escape_string($row['C008_Tipo']);
			$row['D003_Nome_Linha'] = mysql_real_escape_string($row['D003_Nome_Linha']);
			$row['D001_Codigo_Produto'] = mysql_real_escape_string($row['D001_Codigo_Produto']);
			$md5 = mysql_real_escape_string($md5);
			$cache['insert_id']+=1;
			$sql = "INSERT INTO DIM009 (DIM009_Id, DIM009_Md5, DIM009_D001_Descricao_Produto, DIM009_D082_Marca, DIM009_C008_Tipo, DIM009_D003_Nome_Linha, DIM009_D001_Codigo_Produto) VALUES ({$cache['insert_id']},'{$md5}', '{$row['D001_Descricao_Produto']}', '{$row['D082_Marca']}', '{$row['C008_Tipo']}', '{$row['D003_Nome_Linha']}', '{$row['D001_Codigo_Produto']}');";
			$resultEmp = mysqli_query_clickHoue($sql);
			if (!$resultEmp) { die('Invalid query: ' . mysqli_error_clickHouse() . "\n<br/>SQL: " . $sql); }
			return $cache['insert_id'];
		}
	}
}

/**
* Adiciona um registro na dimensão CFOP
* 
* @param int D006_Id
* @return DIM010_Id
*/
function addCFOP($D006_Id = false) {
	global $cache;
	if (!empty($D006_Id)) {
		if (isset($cache['cfop'][$D006_Id])) {
			$row = array();
			$row['D006_Codigo_CFOP'] = $cache['cfop'][$D006_Id][0];
			$row['D006_Descricao'] = $cache['cfop'][$D006_Id][1];
			$md5 = md5($row['D006_Codigo_CFOP'] . $row['D006_Descricao']);
		} else {
			$sql = <<<EOT
				SELECT 
					D006_Id, 
					D006_Codigo_CFOP, 
					D006_Descricao
				FROM 
					D006 
				WHERE 
					D006_Id = '{$D006_Id}';
EOT;
			$result = mysqli_query($sql);
			if (!$result) { die('Invalid query: ' . mysqli_error() . "\n<br/>SQL: " . $sql); }
			if (mysqli_num_rows($result) == 0) { return false; }
			$row = mysql_fetch_assoc($result);
			$md5 = md5($row['D006_Codigo_CFOP'] . $row['D006_Descricao']);
			$cache['cfop'][$D006_Id] = array($row['D006_Codigo_CFOP'], $row['D006_Descricao']);
		}
		// Verifica se já exista na dimensão
		if (isset($cache['cfopMD5'][$md5])) {
			return $cache['cfopMD5'][$md5];
		}
		$sql = "SELECT DIM010_Id FROM DIM010 WHERE DIM010_Md5 = '{$md5}';";
		$resultEmp = mysqli_query_clickHoue($sql);
		if (!$resultEmp) { die('Invalid query: ' . mysqli_error_clickHouse() . "\n<br/>SQL: " . $sql); }
		if (mysqli_num_rows_clickHouse($resultEmp) > 0) {
			$rowEmp = mysql_fetch_assoc($resultEmp);
			$cache['cfopMD5'][$md5] = $rowEmp['DIM010_Id'];
			return $rowEmp['DIM010_Id'];
		} else {
			$row['DIM010_D006_Codigo_CFOP_e_Descricao'] = mysql_real_escape_string($row['D006_Codigo_CFOP']) . ' - ' . mysql_real_escape_string($row['D006_Descricao']);
			$md5 = mysql_real_escape_string($md5);
			$cache['insert_id']+=1;
			$sql = "INSERT INTO DIM010 (DIM010_Id, DIM010_Md5, DIM010_D006_Codigo_CFOP_e_Descricao) VALUES ({$cache['insert_id']},'{$md5}', '{$row['DIM010_D006_Codigo_CFOP_e_Descricao']}');";
			$resultEmp = mysqli_query_clickHoue($sql);
			if (!$resultEmp) { die('Invalid query: ' . mysqli_error_clickHouse() . "\n<br/>SQL: " . $sql); }
			return $cache['insert_id'];
		}
	}
}

/**
* Adiciona um registro na dimensão EMPRESA
* 
* @param int $C004_Id
* @return DIM012_Id
*/
function addEmpresa($C004_Id) {
	global $cache;
	// Busca empresa
	if (isset($cache['empresa'][$C004_Id])) {
		$row = array();
		$row['C004_Nome_Abreviado'] = mysql_real_escape_string($cache['empresa'][$C004_Id]);
	} else {
		$sql = "SELECT C004_Id, C004_Nome_Empresa, C004_Nome_Abreviado FROM C004 WHERE C004_Id = '{$C004_Id}';";	
		$result = mysqli_query($sql);
		if (!$result) { die('Invalid query: ' . mysqli_error() . "\n<br/>SQL: " . $sql); }
		if (mysql_num_rows($result) == 0) { return false; }
		$row = mysql_fetch_assoc($result);
		$row['C004_Nome_Abreviado'] = mysql_real_escape_string($row['C004_Nome_Abreviado']);
	}
	// Verifica se já exista na dimensão
	if (isset($cache['empresaMD5'][$C004_Id])) {
		return $cache['empresaMD5'][$C004_Id];
	}
	if (empty($C004_Id)) { return false; }
	$sql = "SELECT DIM012_Id FROM DIM012 WHERE DIM012_Nome_Abreviado = '{$row['C004_Nome_Abreviado']}';";
	$resultData = mysqli_query_clickHoue($sql);
	if (!$resultData) { die('Invalid query: ' . mysqli_error_clickHouse() . "\n<br/>SQL: " . $sql); }
	if (mysqli_num_rows_clickHouse($resultData) > 0) {
		$rowData = mysql_fetch_assoc($resultData);
		return $cache['empresaMD5'][$C004_Id] = $rowData['DIM012_Id'];
	} else {
		$cache['insert_id']+=1;
		$sql = "INSERT INTO DIM012 (DIM012_Id, DIM012_Nome_Empresa, DIM012_Nome_Abreviado) VALUES ({$cache['insert_id']},'{$row['C004_Nome_Empresa']}', '{$row['C004_Nome_Abreviado']}');";
		$resultData = mysqli_query_clickHoue($sql);
		if (!$resultData) { die('Invalid query: ' . mysqli_error_clickHouse() . "\n<br/>SQL: " . $sql); }
		return $cache['insert_id'];
	}
}

/**
* Adiciona um registro na dimensão CAIXA/BANCO
* 
* @param int $D007_Id
* @return DIM013_Id
*/
function addCaixaBanco($D007_Id) {
	global $cache;
	// Busca caixa/banco
	if (isset($cache['caixaBanco'][$D007_Id])) {
		$row = array();
		$row['D007_Nome_Banco'] = mysql_real_escape_string($cache['caixaBanco'][$D007_Id]);
	} else {
		$sql = "SELECT D007_Id, D007_Nome_Banco, D062_Nome_Banco FROM D007 LEFT JOIN D062 ON D062_Id=D007_D062_Id WHERE D007_Id = '{$D007_Id}';";	
		$result = mysqli_query($sql);
		if (!$result) { die('Invalid query: ' . mysqli_error() . "\n<br/>SQL: " . $sql); }
		if (mysql_num_rows($result) == 0) { return false; }
		$row = mysql_fetch_assoc($result);
		$row['D007_Nome_Banco'] = mysql_real_escape_string($row['D007_Nome_Banco']);
	}
	// Verifica se já exista na dimensão
	if (isset($cache['caixaBancoMD5'][$D007_Id])) {
		return $cache['caixaBancoMD5'][$D007_Id];
	}
	if (empty($D007_Id)) { return false; }
	$sql = "SELECT DIM013_Id FROM DIM013 WHERE DIM013_Nome_Caixa = '{$row['D007_Nome_Banco']}';";
	$resultData = mysqli_query_clickHoue($sql);
	if (!$resultData) { die('Invalid query: ' . mysqli_error_clickHouse() . "\n<br/>SQL: " . $sql); }
	if (mysqli_num_rows_clickHouse($resultData) > 0) {
		$rowData = mysql_fetch_assoc($resultData);
		return $cache['caixaBancoMD5'][$D007_Id] = $rowData['DIM013_Id'];
	} else {
		$cache['insert_id']+=1;
		$sql = "INSERT INTO DIM013 (DIM013_Id, DIM013_Nome_Caixa, DIM013_Nome_Banco) VALUES ({$cache['insert_id']},'{$row['D007_Nome_Banco']}', '{$row['D062_Nome_Banco']}');";
		$resultData = mysqli_query_clickHoue($sql);
		if (!$resultData) { die('Invalid query: ' . mysqli_error_clickHouse() . "\n<br/>SQL: " . $sql); }
		return $cache['insert_id'];
	}
}


/**
* Adiciona um Fato
* 
* @param int $FAC001_DIM001_Id
* @param int $FAC001_DIM002_Id
* @param int $FAC001_DIM003_Id
* @param int $FAC001_DIM004_Id_Vendedor
* @param int $FAC001_DIM004_Id_Supervisor
* @param mixed $FAC001_DIM005_Id
* @param mixed $FAC001_DIM006_Id
* @param mixed $FAC001_DIM007_Id
* @param mixed $FAC001_DIM008_Id
* @param mixed $FAC001_DIM009_Id
* @param mixed $FAC001_DIM010_Id
* @param mixed $FAC001_DIM011_Id
* @param mixed $FATO_CAMPO
* @param mixed $FATO_VALOR
*/
function incluirFato($FAC001_DIM001_Id, $FAC001_DIM002_Id, $FAC001_DIM003_Id, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, $FAC001_DIM010_Id, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, $FAC001_FLAG_CANCELADO, $FATO_CAMPO, $FATO_VALOR, $FLAG_SALDO = 'N', $SOMAR_VALORES = true) {
	global $cache;
	// Otimização: Veririca se já existe o mesmo conjuto de dimensões para este fato (isto irá evitar duplicidade de linhas, diminuindo o trabalho do agregador na consulta)
	/*$chave = $FAC001_DIM001_Id . '|' . $FAC001_DIM002_Id . '|' . $FAC001_DIM003_Id . '|' . $FAC001_DIM004_Id_Vendedor . '|' . $FAC001_DIM004_Id_Supervisor . '|' . $FAC001_DIM005_Id . '|' . $FAC001_DIM006_Id . '|' . $FAC001_DIM007_Id . '|' . $FAC001_DIM008_Id . '|' . $FAC001_DIM009_Id . '|' . $FAC001_DIM010_Id . '|' . $FAC001_DIM011_Id . '|' . $FAC001_DIM012_Id . '|' . $FAC001_DIM013_Id . '|' . $FAC001_FLAG_CANCELADO . '|' . $FLAG_SALDO;
	if (isset($cache[$chave]) && $SOMAR_VALORES !== null) {
		if($SOMAR_VALORES === true){
			$sql = "UPDate FAC001 SET `{$FATO_CAMPO}` = (`{$FATO_CAMPO}` + '{$FATO_VALOR}') WHERE FAC001_Id = '{$cache[$chave]}'";
		} else {
			$sql = "UPDate FAC001 SET `{$FATO_CAMPO}` = '{$FATO_VALOR}' WHERE FAC001_Id = '{$cache[$chave]}'";
		}
		$resultFac = mysqli_query_clickHoue($sql);
		if (!$resultFac) { die('Invalid query: ' . mysqli_error_clickHouse() . "\n<br/>SQL: " . $sql); }
	} else {*/
		$cache['insert_id']+=1;
		/*if ($FATO_VALOR<=0){
			$FATO_VALOR = 0.00;
		}*/
        if (strlen($FATO_VALOR) == 0) {
            return false;
        }
		$cache['insertFato'][$FATO_CAMPO][] = "({$cache['insert_id']},$FAC001_DIM001_Id, $FAC001_DIM002_Id, $FAC001_DIM003_Id, $FAC001_DIM004_Id_Vendedor, $FAC001_DIM004_Id_Supervisor, $FAC001_DIM005_Id, $FAC001_DIM006_Id, $FAC001_DIM007_Id, $FAC001_DIM008_Id, $FAC001_DIM009_Id, $FAC001_DIM010_Id, $FAC001_DIM011_Id, $FAC001_DIM012_Id, $FAC001_DIM013_Id, '{$FAC001_FLAG_CANCELADO}', '{$FATO_VALOR}', '{$FLAG_SALDO}')";
		if (count($cache['insertFato'][$FATO_CAMPO]) > 1000) {
            processarIncluirFatos($FATO_CAMPO);
        }
		//$cache[$chave] = mysqli_insert_id_clickHouse();
	//}
	return true;
}

function processarIncluirFatos($FATO_CAMPO) {
    global $cache;
    $sql = <<<EOT
        INSERT INTO FAC001 
			(`FAC001_Id`, `FAC001_DIM001_Id`,`FAC001_DIM002_Id`,`FAC001_DIM003_Id`,`FAC001_DIM004_Id_Vendedor`,`FAC001_DIM004_Id_Supervisor`,`FAC001_DIM005_Id`,`FAC001_DIM006_Id`,`FAC001_DIM007_Id`,`FAC001_DIM008_Id`,`FAC001_DIM009_Id`,`FAC001_DIM010_Id`,`FAC001_DIM011_Id`,`FAC001_DIM012_Id`,`FAC001_DIM013_Id`,`FAC001_FLAG_CANCELADO`,`{$FATO_CAMPO}`,`FAC001_FLAG_SALDO`) 
		VALUES 
EOT;
    $sql .= implode(',', $cache['insertFato'][$FATO_CAMPO]);
    $resultFac = mysqli_query_clickHoue($sql);
    if (!$resultFac) { die('Invalid query: ' . mysqli_error_clickHouse() . "\n<br/>SQL: " . $sql); }
    $cache['insertFato'][$FATO_CAMPO] = array();
}

function processarIncluirFatosEnd() {
    global $cache;
	foreach ($cache['insertFato'] as $FATO_CAMPO => $value) {
		$sql = <<<EOT
			INSERT INTO FAC001 
				(`FAC001_Id`, `FAC001_DIM001_Id`,`FAC001_DIM002_Id`,`FAC001_DIM003_Id`,`FAC001_DIM004_Id_Vendedor`,`FAC001_DIM004_Id_Supervisor`,`FAC001_DIM005_Id`,`FAC001_DIM006_Id`,`FAC001_DIM007_Id`,`FAC001_DIM008_Id`,`FAC001_DIM009_Id`,`FAC001_DIM010_Id`,`FAC001_DIM011_Id`,`FAC001_DIM012_Id`,`FAC001_DIM013_Id`,`FAC001_FLAG_CANCELADO`,`{$FATO_CAMPO}`,`FAC001_FLAG_SALDO`) 
			VALUES 
EOT;
		$sql .= implode(',', $cache['insertFato'][$FATO_CAMPO]);
		$resultFac = mysqli_query_clickHoue($sql);
		if (!$resultFac) { die('Invalid query: ' . mysqli_error_clickHouse() . "\n<br/>SQL: " . $sql); }
		$cache['insertFato'][$FATO_CAMPO] = array();
	}
}
