<?php

$cCampo = $_GET['Campo'];
$nValor = $_GET['Valor'];

if ($cCampo=='D024_Id_Principal')
{
    echo "<script>parent.location.replace('principal.php?D024_Id=$nValor');</script>";
}
else
{
    echo "<script>parent.location.replace('formulario_gerar_boleto.php?D024_Id=$nValor');</script>";
}

echo "$cCampo";
echo "<BR>$nValor";

echo "<script>parent.document.getElementById('DivLoadingSC').style.display = 'none';</script>";





?>
