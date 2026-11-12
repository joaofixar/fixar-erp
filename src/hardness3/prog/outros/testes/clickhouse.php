<?php
namespace hardness;
require_once("bibliotecas/confUsuarioClickHouse.php");

$con = mysqli_init();
$con->real_connect($confUsuarioClickHouse['dbHost'], $confUsuarioClickHouse['dbUser'], $confUsuarioClickHouse['dbPass'],$confUsuarioClickHouse['dbDatabase'],$confUsuarioClickHouse['dbPort']);
$error = mysqli_connect_error($con);
if (!empty($error)) {
    die("Error: {$error}");
}

$sql = <<<EOT
CREATE TABLE `FAC001` (
  `FAC001_Id` UUID,
  `FAC001_DIM001_Id` UUID,
  `FAC001_FLAG_CANCELADO` FixedString(1) DEFAULT 'N',
  `FAC001_FLAG_SALDO` FixedString(1) DEFAULT 'N',
  `FAC001_NF_Quantidade` Decimal(20,5) DEFAULT '0',
  `FAC001_NF_Valor_Total_Preco` Decimal(20,5) DEFAULT '0'
) ENGINE=MergeTree()
ORDER BY (FAC001_Id);
EOT;
//mysqli_query($sql, $con);

$sql = <<<EOT
CREATE TABLE `DIM001` (
  `DIM001_Id` UUID,
  `DIM001_Data` Date,
  `DIM001_Dia` UInt8,
  `DIM001_Dia_Da_Semana` UInt8,
  `DIM001_Mes` UInt8,
  `DIM001_Ano` UInt16
) ENGINE=MergeTree()
ORDER BY (DIM001_Id);
EOT;
//mysqli_query($sql, $con);

$sql = <<<EOT
INSERT INTO e998.DIM001 (DIM001_Id, DIM001_Data, DIM001_Dia, DIM001_Dia_Da_Semana, DIM001_Mes, DIM001_Ano) VALUES (generateUUIDv4(), '2021-06-01', 1, 4, 6, 2021)
EOT;
//mysqli_query($sql, $con);

$sql = <<<EOT
INSERT INTO e998.FAC001 (FAC001_Id, FAC001_DIM001_Id, FAC001_FLAG_CANCELADO, FAC001_FLAG_SALDO, FAC001_NF_Quantidade, FAC001_NF_Valor_Total_Preco) VALUES (generateUUIDv4(), 'b2080cd2-d0f5-4951-ae59-dc76bd3d17b0', 'N', 'S', 110.00000, 76283.12000)
EOT;
//mysqli_query($sql, $con);

$sql = <<<EOT
SELECT * FROM FAC001
LEFT JOIN DIM001 ON DIM001_Id = FAC001_DIM001_Id
EOT;
$mysql = mysqli_query($sql, $con);
$error = mysqli_error($con);
if (!empty($error)) {
    die("Error: {$error}");
}
$resultado = mysqli_fetch_assoc($mysql);
print_r($resultado);