$files = @(
    'inecel/coletor/pedidos.php',
    'inecel/coletor/pedidos_separacao.php',
    'inecel/coletor/volumes.php',
    'inecel/coletor/movimentacao_estoque.php',
    'inecel/coletor/separacao.php'
)

$bindFunction = @"
    function bindCollectorInput(input, autoFocus) {
        var shouldAutoFocus = autoFocus !== false;
        var form = input ? input.closest('form') : null;
        var manualMode = false;

        if (!input || input.disabled || !form) {
            return;
        }

        function focusInput() {
            input.focus({ preventScroll: true });
            var valueLength = input.value.length;
            if (typeof input.setSelectionRange === 'function') {
                input.setSelectionRange(valueLength, valueLength);
            }
        }

        function lockScannerMode() {
            manualMode = false;
            input.readOnly = false;
            input.setAttribute('inputmode', 'none');
            input.setAttribute('virtualkeyboardpolicy', 'manual');
        }

        function unlockKeyboardMode() {
            manualMode = true;
            input.setAttribute('inputmode', 'text');
            input.setAttribute('virtualkeyboardpolicy', 'auto');
            focusInput();
        }

        lockScannerMode();

        if (shouldAutoFocus) {
            setTimeout(function () {
                focusInput();
            }, 120);
        }

        input.addEventListener('dblclick', function (event) {
            event.preventDefault();
            unlockKeyboardMode();
        });

        input.addEventListener('blur', function () {
            if (manualMode) {
                lockScannerMode();
            }
        });

        input.addEventListener('keypress', function (event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                if (typeof form.requestSubmit === 'function') {
                    form.requestSubmit();
                } else {
                    form.submit();
                }
            }
        });
    }
"@

foreach ($file in $files) {
    $content = Get-Content -Path $file -Raw

    $content = [regex]::Replace(
        $content,
        'function bindCollectorInput\(input\) \{[\s\S]*?\n\s*\}\n\s*bindCollectorInput',
        $bindFunction + "`r`n    bindCollectorInput",
        1
    )

    switch ($file) {
        'inecel/coletor/pedidos.php' {
            $content = $content.Replace(
                '<input name="q" value="<?php echo inecel_h($search); ?>" autofocus inputmode="text" virtualkeyboardpolicy="manual" data-unlock-dblclick="true" readonly placeholder="Buscar por pedido, pessoa ou status">',
                '<input name="q" value="<?php echo inecel_h($search); ?>" autofocus inputmode="none" virtualkeyboardpolicy="manual" data-unlock-dblclick="true" placeholder="Buscar por pedido, pessoa ou status">'
            )
        }
        'inecel/coletor/pedidos_separacao.php' {
            $content = $content.Replace(
                '<input name="q" value="<?php echo inecel_h($search); ?>" autofocus inputmode="text" virtualkeyboardpolicy="manual" data-unlock-dblclick="true" readonly placeholder="Buscar por pedido, pessoa ou status">',
                '<input name="q" value="<?php echo inecel_h($search); ?>" autofocus inputmode="none" virtualkeyboardpolicy="manual" data-unlock-dblclick="true" placeholder="Buscar por pedido, pessoa ou status">'
            )
        }
        'inecel/coletor/volumes.php' {
            $content = $content.Replace(
                '<input id="codigo" name="codigo" type="text" inputmode="text" virtualkeyboardpolicy="manual" data-unlock-dblclick="true" autofocus placeholder="Leia ou digite o codigo do volume">',
                '<input id="codigo" name="codigo" type="text" inputmode="none" virtualkeyboardpolicy="manual" data-unlock-dblclick="true" autofocus placeholder="Leia ou digite o codigo do volume">'
            )
        }
        'inecel/coletor/movimentacao_estoque.php' {
            $content = $content.Replace(
                '<input type="text" name="codigo" value="<?php echo inecel_h($search); ?>" autofocus inputmode="text" virtualkeyboardpolicy="manual" data-unlock-dblclick="true" readonly placeholder="Leia ou digite o codigo do produto">',
                '<input type="text" name="codigo" value="<?php echo inecel_h($search); ?>" autofocus inputmode="none" virtualkeyboardpolicy="manual" data-unlock-dblclick="true" placeholder="Leia ou digite o codigo do produto">'
            )
        }
        'inecel/coletor/separacao.php' {
            $content = $content.Replace(
                '<input type="text" id="scannerCodigo" inputmode="text" virtualkeyboardpolicy="manual" data-unlock-dblclick="true" placeholder="Leia ou digite o codigo de barras do produto" <?php echo $semLocacaoSeparadoEmpresa ? ''disabled'' : ''''; ?>>',
                '<input type="text" id="scannerCodigo" inputmode="none" virtualkeyboardpolicy="manual" data-unlock-dblclick="true" placeholder="Leia ou digite o codigo de barras do produto" <?php echo $semLocacaoSeparadoEmpresa ? ''disabled'' : ''''; ?>>'
            )
            $content = $content.Replace(
                '<input type="text" id="locationCodigo" inputmode="text" virtualkeyboardpolicy="manual" data-unlock-dblclick="true" placeholder="Leia ou digite o codigo da locacao">',
                '<input type="text" id="locationCodigo" inputmode="none" virtualkeyboardpolicy="manual" data-unlock-dblclick="true" placeholder="Leia ou digite o codigo da locacao">'
            )
            $content = $content.Replace('    bindCollectorInput(locationCodigo);', '    bindCollectorInput(locationCodigo, false);')
        }
    }

    Set-Content -Path $file -Value $content -Encoding UTF8
}
