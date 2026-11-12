$path = 'inecel/coletor/movimentacao_estoque_ajax.php'
$lines = [System.Collections.Generic.List[string]](Get-Content -Path $path)
$start = -1
$end = -1
for ($i = 0; $i -lt $lines.Count; $i++) {
    if ($lines[$i].Contains('$summaryAtualizado') -or $lines[$i].Contains('\$summaryAtualizado')) {
        $start = $i
        break
    }
}
if ($start -lt 0) { throw 'Inicio do bloco summary nao encontrado.' }
for ($i = $start; $i -lt $lines.Count; $i++) {
    if ($lines[$i].Trim() -eq '];') {
        $end = $i
        break
    }
}
if ($end -lt 0) { throw 'Fim do bloco summary nao encontrado.' }
for ($i = $end; $i -ge $start; $i--) {
    $lines.RemoveAt($i)
}
$replacement = [string[]]@(
    '$summaryAtualizado = [',
    "    'total_fisico' => `$rowAtualizada['D009_Quantidade_Estoque'] ?? 0,",
    "    'total_liquido' => `$rowAtualizada['D009_Quantidade_Estoque_Liquido'] ?? 0,",
    "    'reserva' => `$rowAtualizada['reserva'] ?? 0,",
    "    'total_fisico_formatado' => `$rowAtualizada['D009_Quantidade_Estoque_formatado'] ?? inecel_format_number(0, 2),",
    "    'total_liquido_formatado' => `$rowAtualizada['D009_Quantidade_Estoque_Liquido_formatado'] ?? inecel_format_number(0, 2),",
    "    'reserva_formatado' => `$rowAtualizada['reserva_formatado'] ?? inecel_format_number(0, 2),",
    '];'
)
for ($i = $replacement.Count - 1; $i -ge 0; $i--) {
    $lines.Insert($start, $replacement[$i])
}
$utf8NoBom = New-Object System.Text.UTF8Encoding($false)
[System.IO.File]::WriteAllLines((Join-Path (Get-Location) $path), $lines, $utf8NoBom)
