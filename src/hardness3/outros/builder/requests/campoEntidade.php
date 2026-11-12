<?php
namespace hardness;
// Pode ser incluido diretamente via include()
if (file_exists('../conf.php')) {
    require_once('../conf.php');
    if (empty($g['projeto'])) die('Erro ao carregar projeto.');
}

$id = $_POST['id'];
$nomeTabela = $_POST['tabela'];
$_campoTabela = $_POST['campoTabela'];

// Campos de uma tabela
$_query = <<<EOT
select * from information_schema.columns
where table_schema = '{$confUsuario["dbDatabase"]}' AND table_name = '{$nomeTabela}'
order by table_name,COLUMN_NAME;
EOT;
$_campos = [];
foreach (mysqli_fetch_all(mysqli_query($_query), MYSQLI_ASSOC) as $_campo) {
    $ehChavePrimaria = preg_match("/^[^_]+_Id$/", $_campo["COLUMN_NAME"], $matches) ? true : false;
    $ehChaveEstrangeira = !$ehChavePrimaria && preg_match("/_Id$/U", $_campo["COLUMN_NAME"]);
    $chaveEstrangeiraTabela = null;
    if ($ehChaveEstrangeira) {
        preg_match("/^.*_(?<tabela>.+)_Id$/", $_campo["COLUMN_NAME"], $matches);
        $chaveEstrangeiraTabela = $matches["tabela"];
    }
    if ($ehChaveEstrangeira) continue; // $ehChavePrimaria ||
    $_campos[] = new Campo(
        $_campo["COLUMN_NAME"],
        Campo::tipoMysqlCampo($_campo["COLUMN_TYPE"]),
        false,
        false,
        false
    );
}
?>

<div id="<?=$id?>" class="ui search clearable selection dropdown dropdownEntidadeCampo" style="width: 100%; border: 0;">
    <i class="dropdown icon"></i>
    <div class="default text">Selecione...</div>
    <div class="menu">
        <?php foreach ($_campos as $_campo) { ?>
            <div class="item" data-value="<?=$_campo->nome?>"><?=$_campo->nome?></div>
        <?php } ?>
    </div>
    <input type="hidden" name="campo" value="<?=$_campoTabela?>">
</div
