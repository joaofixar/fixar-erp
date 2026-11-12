<?php
 namespace hardness; if ($g['modoDesenvolvedor']) { goto Do6wm; } if (!$g['modoDesenvolvedorApp']) { goto jVCsk; } Personalizacoes::$rootDir = Apps::getDirEditor(); jVCsk: goto D2C4P; Do6wm: Personalizacoes::$rootDir = 'ide/'; D2C4P: switch ($g['r'][3]) { case 'editorTelaMenu': echo <<<EOT
<div id="{$r_areaId}-menu" style="width: 100%; height: 100%; background-color: rgb(230, 235, 238);">
    <div style="height: 6px;"></div>
    <div style="display: flex; justify-content: center; align-items: center;">
        <select id="{$r_tipo}editorTelaMenuAddItemOpcao" class="itemForm formInput ui-state-default ui-corner-all" style="display: table; margin: 0 auto; width: 150px;">
            <option value="editor">Editor</option>
            <option value="texto">Texto</option>
            <option value="quadro">Quadro</option>
            <option value="imagem">Imagem</option>
            <option value="barcode">Código de Barras (JS)</option>
            <option value="barcode2">Código de Barras (PHP)</option>
            <option value="qrCode">QR Code</option>
            <optgroup label="――――"></optgroup>
            <option value="grid">Grid</option>
        </select>
        <button type="submit" value="" class="ui-icon ui-icon-plusthick ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" title="Adicionar elemento" style="background-position: -30px -126px; float: left; width: 23px; height: 22px; margin-left: 4px;" role="button" onclick="{$r_tipo}editorTelaMenuAddItem();"><span class="ui-button-text"></span></button>
    </div>
    <div style="border-bottom: 1px solid rgba(207, 221, 232, 1.0); height: 1px; margin: 10px;"></div>
    <form class="formFormulario">
        <div style="flex-direction: column; margin-left: 10px; display: none;" class="{$r_tipo}editorTelaMenuItem {$r_tipo}editorTelaMenuItemTextoTitulo">
            <span>Texto:</span>
            <textarea type="text" style="width: 360px; height: 80px; text-transform: none;" onkeyup="{$r_tipo}editorTelaMenuItemTextoTitulo(\$(this));"></textarea>
        </div>
        
        <div style="flex-direction: column; margin-left: 10px; display: none;" class="{$r_tipo}editorTelaMenuItem {$r_tipo}editorTelaMenuItemEditor">
            <span>Editor:</span>
            <textarea type="text" style="width: 360px; height: 300px; text-transform: none;"></textarea>
        </div>
        
        <div style="flex-direction: column; margin-left: 10px; display: none;" class="{$r_tipo}editorTelaMenuItem {$r_tipo}editorTelaMenuItemUrl">
            <span>Grid URL:</span>
            <textarea type="text" style="width: 360px; height: 100px; text-transform: none;" onkeyup="{$r_tipo}editorTelaMenuItemUrl(\$(this));"></textarea>
        </div>
        
        <div style="flex-direction: column; margin-left: 10px; margin-top: 8px; display: none;" class="{$r_tipo}editorTelaMenuItem {$r_tipo}editorTelaMenuItemCss">
            <span>CSS:</span>
            <textarea style="width: 360px; height: 150px; text-transform: none;" onkeyup="{$r_tipo}editorTelaMenuItemCss(\$(this));"></textarea>
        </div>
        
        <div style="flex-direction: column; margin-left: 10px; margin-top: 8px; display: none;" class="{$r_tipo}editorTelaMenuItem {$r_tipo}editorTelaMenuItemCssInternal">
            <span>CSS:</span>
            <textarea style="width: 360px; height: 150px; text-transform: none;" onkeyup="{$r_tipo}editorTelaMenuItemCssInternal(\$(this));"></textarea>
        </div>
        
        <div style="flex-direction: column; margin-left: 10px; margin-top: 8px; display: none;" class="{$r_tipo}editorTelaMenuItem {$r_tipo}editorTelaMenuItemBarcode">
            <span><a href="https://github.com/lindell/JsBarcode/wiki/Options" target="_blank">Opções<a>:</span>
            <textarea style="width: 360px; height: 150px; text-transform: none;" onkeyup="{$r_tipo}editorTelaMenuItemBarcode(\$(this));"></textarea>
        </div>
        
        <div style="flex-direction: column; margin-left: 10px; margin-top: 8px; display: none;" class="{$r_tipo}editorTelaMenuItem {$r_tipo}editorTelaMenuItemBarcode2">
            <span><a href="https://github.com/picqer/php-barcode-generator#usage" target="_blank">Opções<a>:</span>
            <textarea style="width: 360px; height: 150px; text-transform: none;" onkeyup="{$r_tipo}editorTelaMenuItemBarcode2(\$(this));"></textarea>
        </div>

        <div style="flex-direction: column; margin-left: 10px; margin-top: 8px; display: none;" class="{$r_tipo}editorTelaMenuItem {$r_tipo}editorTelaUploadImagemLink">
            <span>Src:</span>
            <textarea style="width: 360px; height: 100px; text-transform: none;" onkeyup="{$r_tipo}editorTelaUploadImagemLink(\$(this));"></textarea>
        </div>
        
        <div style="align-items: center; justify-content: center; margin-top: 16px; display: none;" class="{$r_tipo}editorTelaMenuItem {$r_tipo}editorTelaUploadImagem">
            <button onclick="{$r_tipo}editorTelaUploadImagem(); return false;">Upload</button>
        </div>
        
        <div style="align-items: center; justify-content: center; margin-top: 16px; display: none;" class="{$r_tipo}editorTelaMenuItem {$r_tipo}editorTelaMenuItemRemover">
            <button onclick="{$r_tipo}editorTelaMenuItemRemover(); return false;">Remover</button>
        </div>
        
        <div style="align-items: center; justify-content: center; margin-top: 16px; display: none;" class="{$r_tipo}editorTelaMenuItem {$r_tipo}editorTelaMenuItemRemover">
            <button type="submit" value="" style="width: 25px;" onclick="{$r_tipo}editorTelaCimaBaixo('cima'); return false;">
                <span class="ui-icon ui-icon-arrow-1-n" title="Cima" style="margin-left: -9px; margin-top: -2px;"></span>
            </button>
            <button type="submit" value="" style="width: 25px;" onclick="{$r_tipo}editorTelaCimaBaixo('baixo'); return false;">
                <span class="ui-icon ui-icon-arrow-1-s" title="Baixo" style="margin-left: -9px; margin-top: -2px;"></span>
            </button>
        </div>
    </form>
</div>
<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
    function {$r_tipo}editorTelaMenuAddItem() {
        const opcao = \$('#{$r_tipo}editorTelaMenuAddItemOpcao').val();
        const uniq = unique();
        if (opcao == 'texto') {
            \$('#{$r_areaId} .telaItemGrid').remove();
            \$('<div class="telaItem telaItemTexto"><span class="texto">TEXTO</span></div>')
                .addClass(`telaItem-\${uniq}`)
                .css({
                    position: 'absolute',
                    top: '23px',
                    left: '32px',
                })
                .attr('unique', uniq)
                .appendTo('#{$r_areaId}');
        } else if (opcao == 'quadro') {
            \$('#{$r_areaId} .telaItemGrid').remove();
            \$('<div class="telaItem telaItemQuadro"></div>')
                .addClass(`telaItem-\${uniq}`)
                .css({
                    position: 'absolute',
                    top: '23px',
                    left: '32px',
                    width: '100px',
                    height: '100px',
                    border: '1px solid #7e7e7e',
                    'border-radius': '5px',
                })
                .attr('unique', uniq)
                .attr('caminho-url', '')
                .appendTo('#{$r_areaId}');
        } else if (opcao == 'grid') {
            \$('#{$r_areaId} .telaItem').remove();
            \$('<div class="telaItem telaItemGrid">GRID<input type="hidden" class="css" /></div>')
                .addClass(`telaItem-\${uniq}`)
                .css({
                    position: 'absolute',
                    top: '23px',
                    left: '8px',
                    width: '49px',
                    height: '50px',
                    border: '1px solid #7e7e7e',
                    'border-radius': '5px',
                })
                .attr('unique', uniq)
                .appendTo('#{$r_areaId}');
        } else if (opcao == 'imagem') {
            \$('#{$r_areaId} .telaItemGrid').remove();
            \$('<div class="telaItem telaItemImagem"><img class="imagem" style="width: 100%; height: 100%;" src="data:image/png;base64, iVBORw0KGgoAAAANSUhEUgAAAAIAAAACCAYAAABytg0kAAAAFElEQVQIW2MMDQ39z8DAwMAIYwAAKgMD/9AXrvgAAAAASUVORK5CYII="></div>')
                .addClass(`telaItem-\${uniq}`)
                .css({
                    position: 'absolute',
                    top: '23px',
                    left: '32px',
                    width: '100px',
                    height: '100px',
                })
                .attr('unique', uniq)
                .appendTo('#{$r_areaId}');
        } else if (opcao == 'barcode') {
            \$('#{$r_areaId} .telaItemGrid').remove();
            \$('<div class="telaItem telaItemBarcode"><span class="texto">COD. BARRA (JS)</span><input type="hidden" class="options" value="{\\n    width: 1,\\n    fontSize: 14,\\n}" /></div>')
                .addClass(`telaItem-\${uniq}`)
                .css({
                    position: 'absolute',
                    top: '23px',
                    left: '8px',
                    height: '50px',
                    width: '100px',
                    // ignorados
                    border: '1px solid #7e7e7e',
                    'border-radius': '5px',
                })
                .attr('unique', uniq)
                .appendTo('#{$r_areaId}');
        } else if (opcao == 'barcode2') {
            \$('#{$r_areaId} .telaItemGrid').remove();
            \$('<div class="telaItem telaItemBarcode2"><span class="texto">COD. BARRA (PHP)</span><input type="hidden" class="options" value="{\\n    widthFactor: 1\\n}" /></div>')
                .addClass(`telaItem-\${uniq}`)
                .css({
                    position: 'absolute',
                    top: '23px',
                    left: '8px',
                    height: '50px',
                    width: '100px',
                    // ignorados
                    border: '1px solid #7e7e7e',
                    'border-radius': '5px',
                })
                .attr('unique', uniq)
                .appendTo('#{$r_areaId}');

        } else if (opcao == 'qrCode') {

            \$('#{$r_areaId} .telaItemGrid').remove();
            //\$('<div class="telaItem telaItemBarcode2"><span class="texto">COD. BARRA (PHP)</span><input type="hidden" class="options" value="{\\n    widthFactor: 1\\n}" /></div>')
            \$('<div class="telaItem telaItemQrCode"><span class="texto">QR CODE</span><input type="hidden" class="options" value="{\\n    widthFactor: 1\\n}" /></div>')

                .addClass(`telaItem-\${uniq}`)
                .css({
                    position: 'absolute',
                    top: '23px',
                    left: '8px',
                    height: '50px',
                    width: '100px',
                    // ignorados
                    border: '1px solid #7e7e7e',
                    'border-radius': '5px',
                })
                .attr('unique', uniq)
                .appendTo('#{$r_areaId}');

        } else if (opcao == 'editor') {
            \$('#{$r_areaId} .telaItemGrid').remove();
            \$('<div class="telaItem telaItemEditor"><div class="editor" style="min-width: 4px; min-height: 4px;">EDITOR</div></div>')
                .addClass(`telaItem-\${uniq}`)
                .css({
                    position: 'absolute',
                    top: '23px',
                    left: '32px',
                    width: '100px',
                })
                .attr('unique', uniq)
                .appendTo('#{$r_areaId}');
        }
        {$r_tipo}editorTela('{$r_areaId}');
        setTimeout(function() { 
            \$(`.telaItem-\${uniq} .moveHandle`).trigger('click');
        }, 100);
    }
    function {$r_tipo}editorTelaMenuItemCss(\$elem) {
        if (!{$r_tipo}editorTelaItemSelecionado) { return; }
        let style = \$(`.telaItem-\${{$r_tipo}editorTelaItemSelecionado}`).attr('style').split(';');
        style = style.filter((item) => {
            item = item.split(':');
            item[0] = item[0].trim();
            return item[0] && (item[0] == 'position' || item[0] == 'cursor'); // || item[0] == 'top' || item[0] == 'left'
        });
        \$(`.telaItem-\${{$r_tipo}editorTelaItemSelecionado}`).attr('style', style.join(';') + ';' + \$elem.val());
    }
    function {$r_tipo}editorTelaMenuItemCssInternal(\$elem) {
        if (!{$r_tipo}editorTelaItemSelecionado) { return; }
        let style = \$(`.telaItem-\${{$r_tipo}editorTelaItemSelecionado}`).find('.css').val(\$elem.val());
    }
    function {$r_tipo}editorTelaMenuItemBarcode(\$elem) {
        if (!{$r_tipo}editorTelaItemSelecionado) { return; }
        let style = \$(`.telaItem-\${{$r_tipo}editorTelaItemSelecionado}`).find('.options').val(\$elem.val());
    }
    function {$r_tipo}editorTelaMenuItemBarcode2(\$elem) {
        if (!{$r_tipo}editorTelaItemSelecionado) { return; }
        let style = \$(`.telaItem-\${{$r_tipo}editorTelaItemSelecionado}`).find('.options').val(\$elem.val());
    }
    function {$r_tipo}editorTelaMenuItemTextoTitulo(\$elem) {
        if (!{$r_tipo}editorTelaItemSelecionado) { return; }
        \$(`.telaItem-\${{$r_tipo}editorTelaItemSelecionado} span.texto`).html(\$elem.val());
    }
    function {$r_tipo}editorTelaMenuItemUrl(\$elem) {
        if (!{$r_tipo}editorTelaItemSelecionado) { return; }
        \$(`.telaItem-\${{$r_tipo}editorTelaItemSelecionado}`).attr('caminho-url', \$elem.val());
    }
    function {$r_tipo}editorTelaMenuItemRemover() {
        if (!{$r_tipo}editorTelaItemSelecionado) { return; }
        \$(`.telaItem-\${{$r_tipo}editorTelaItemSelecionado}`).remove();
        {$r_tipo}editorTelaItemSelecionado = false;
        \$('.{$r_tipo}editorTelaMenuItem').hide();
    }
    function {$r_tipo}editorTelaUploadImagem() {
        if (!{$r_tipo}editorTelaItemSelecionado) { return; }
        abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Enviar Imagem', '/sistema/personalizacaoLowCode/content/editarTelaUploadImagem/', '&item=' + encodeURIComponent(`.telaItem-\${{$r_tipo}editorTelaItemSelecionado}`), [400,150])
    }
    function {$r_tipo}editorTelaUploadImagemLink(\$elem) {
        if (!{$r_tipo}editorTelaItemSelecionado) { return; }
        let style = \$(`.telaItem-\${{$r_tipo}editorTelaItemSelecionado}`).find('.imagem').attr('src', \$elem.val());
    }
    function {$r_tipo}editorTelaCimaBaixo(opcao) {
        if (!{$r_tipo}editorTelaItemSelecionado) { return; }
        const \$item = \$(`.telaItem-\${{$r_tipo}editorTelaItemSelecionado}`);
        \$item.detach();
        if (opcao == 'baixo') {
            \$item.prependTo('#{$r_areaId}');
        } else {
            \$item.appendTo('#{$r_areaId}');        
        }
    }
    function {$r_tipo}editorTelaMenuItemCssBarcode(\$elem) {
        if (!{$r_tipo}editorTelaItemSelecionado) { return; }
        let style = \$(`.telaItem-\${{$r_tipo}editorTelaItemSelecionado}`).find('.options').val(\$elem.val());
    }
} </script></div>
EOT;
goto qgOwt; case 'editorTelaArea': goto jGDWy; bTqZ2: $html = $tela['html' . ucfirst($r_tipo)]; goto VivcY; sBQYd: $pdfAltura = $tela['pdfAltura'] ? $tela['pdfAltura'] : 297; goto HU_zV; bCbKc: $pdfLarguraPx = $pdfLargura * $mapeamentoPxMm * 1.4; goto WJssG; VivcY: $unique = uniqid(); goto ZRPEQ; WJssG: $pdfAlturaPx = $pdfAltura * $mapeamentoPxMm * 1.4; goto Tlt_C; jGDWy: $tela = Personalizacoes::getTelaNova($r_acaoId); goto bTqZ2; HU_zV: $mapeamentoPxMm = 2.74; goto bCbKc; NjldV: goto qgOwt; goto oV7lT; ZRPEQ: $pdfLargura = $tela['pdfLargura'] ? $tela['pdfLargura'] : 210; goto sBQYd; Tlt_C: echo <<<EOT
<!-- Hack para funcionar CSS dentro do FW -->
<style>
    /* CSS telaEditorHtml (está em mais lugares) */
    .telaEditorHtml body {
        font-family: Roboto,sans-serif;
        font-size:14px;
        margin: 0 auto;
    }
    .telaEditorHtml p {
        margin: 0;
    }
    .telaEditorHtml{$unique} * {
        {$tela['css']}
    }
</style>
<div style="width: 200%; height: 26px;">
    <div class="editorTelaReguaHorizontal" style="width: 5000px; padding-top: 12px; padding-left: 46px;">
        <div class="cm" style="height: 14px; border-left: 1px solid gray; float: left; position: relative; margin-right: {$mapeamentoPxMm}px; display: none;">
            <div class="text" style="position: absolute; top: -12px; left: -1px;">1</div>
        </div>
        <div class="mm" style="height: 8px; margin-right: {$mapeamentoPxMm}px; border-left: 1px solid gray; float: left; display: none;"></div> 
    </div>
</div>
<div id="{$r_areaId}" class="{$r_tipo}editorTelaArea telaEditorHtml telaEditorHtml{$unique}" data-tipo="{$r_tipo}" data-areaid="{$r_areaId}" style="width: {$pdfLarguraPx}px; height: {$pdfAlturaPx}px; position: relative; left: 46px;">
    <div style="width: 46px; height: 200%; overflow: hidden; position: absolute; left: -46px;">
        <div class="editorTelaReguaVertical" style="height: 5000px;">
            <div class="cm" style="width: 14px; border-top: 1px solid gray; float: left; position: relative; margin-bottom: {$mapeamentoPxMm}px; clear: left; left: 31px; display: none;">
                <div class="text" style="position: absolute; left: -22px; top: -3px; font-size: 11px;">1</div>
            </div>
            <div class="mm" style="width: 8px; margin-bottom: {$mapeamentoPxMm}px; border-top: 1px solid gray; float: left; clear: left; position: relative; left: 31px; display: none;"></div> 
        </div>
    </div>
    {$html}
</div>
<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
    let telaCarregada = null;
    let redimensionando = false;
    
    for (let i = 0; i <= 1000; i++) {
        if (i > {$pdfLargura}) { continue; }
        let \$elem;
        if (i % 10 === 0) {
            \$elem = \$('#{$g['divId']} .editorTelaReguaHorizontal .cm:first').clone();
            const posicao = i/10;
            \$elem.find('.text').html(posicao * 10);
        } else {
            \$elem = \$('#{$g['divId']} .editorTelaReguaHorizontal .mm:first').clone();
        }
        \$elem.show();
        \$elem.appendTo(\$('#{$g['divId']} .editorTelaReguaHorizontal'));
    }
    
    for (let i = 0; i <= 1000; i++) {
        if (i > {$pdfAltura}) { continue; }
        let \$elem;
        if (i % 10 === 0) {
            \$elem = \$('#{$g['divId']} .editorTelaReguaVertical .cm:first').clone();
            const posicao = i/10; 
            \$elem.find('.text').html(posicao * 10);
        } else {
            \$elem = \$('#{$g['divId']} .editorTelaReguaVertical .mm:first').clone();
        }
        \$elem.show();
        \$elem.appendTo(\$('#{$g['divId']} .editorTelaReguaVertical'));
    }
    
    setTimeout(function() {
        {$r_tipo}editorTela('{$r_areaId}');
    }, 200);
    \$('#{$r_areaId}').unbind('click.clickOut').bind('click.clickOut', function(event) {
        const \$target = \$(event.target);
        // Retirar seleção ao clicar fora de um elemento
        if (!\$target.hasClass('moveHandle') && !\$target.hasClass('telaItemWidth') && !\$target.hasClass('telaItemHeight') && !redimensionando) {
            {$r_tipo}editorTelaItemSelecionado = false;
            \$('.{$r_tipo}editorTelaMenuItem').hide();
            \$('.moveHandle').css({ border: 'none' });
        }
    });
    
    function {$r_tipo}editorTela(id) {
        const boxWidth = 16;
\t\tconst boxHeight = 23;

        // Prepara a tela de edição
        \$tela = \$(`#\${id}`);
        if (id !== telaCarregada) {
            \$tela.css({
                'background': 'url("/hardness3/static/img/editorFormGridBackground.gif") repeat scroll 0 0 #EDE7DE',
                //'position': 'fixed',
                'z-index': '9999999'
            });
            telaCarregada = id;
        }
        
        // Função para selecionar um elemento e mostrar os dados na lateral
        const selecionarElemento = function(\$elem, byPass = false) {
            if (!byPass && (!\$elem.attr('unique') || \$elem.attr('unique') == {$r_tipo}editorTelaItemSelecionado)) { return true; }
            \$('.{$r_tipo}editorTelaMenuItem').hide();
            \$('.{$r_tipo}editorTelaMenuItemRemover').css('display', 'flex');
            // CSS
            if (!\$elem.hasClass('telaItemGrid')) {
                \$('.{$r_tipo}editorTelaMenuItemCss').css('display', 'flex');
                let style = \$elem.attr('style').split(';');
                style = style.filter((item) => {
                    item = item.split(':');
                    item[0] = item[0].trim();
                    if (\$elem.hasClass('telaItemBarcode') && item[0] && ['border', 'border-radius'].includes(item[0]) ) {
                        return false;
                    }
                    if (\$elem.hasClass('telaItemBarcode2') && item[0] && ['border', 'border-radius'].includes(item[0]) ) {
                        return false;
                    }
                    return item[0] && item[0] != 'position' && item[0] != 'cursor';
                });
                style = style.map(function(item) { return item.trim() });
                \$(`#\${id}-menu`).find('.{$r_tipo}editorTelaMenuItemCss').find('textarea').val(style.length > 0 ? style.join(';\\n') + ';' : '');
            }
            // Texto
            if (\$elem.hasClass('telaItemTexto')) {
                \$('.{$r_tipo}editorTelaMenuItemTextoTitulo').css('display', 'flex');
                \$(`#\${id}-menu`).find('.{$r_tipo}editorTelaMenuItemTextoTitulo').find('textarea').val(\$elem.find('span.texto').html());
            }
            // Grid
            if (\$elem.hasClass('telaItemGrid')) {
                \$('.{$r_tipo}editorTelaMenuItemUrl').css('display', 'flex');
                \$('.{$r_tipo}editorTelaMenuItemCssInternal').css('display', 'flex');
                \$(`#\${id}-menu`).find('.{$r_tipo}editorTelaMenuItemUrl').find('textarea').val(\$elem.attr('caminho-url'));
                \$(`#\${id}-menu`).find('.{$r_tipo}editorTelaMenuItemCssInternal').find('textarea').val(\$elem.find('.css').val());
            }
            // Imagem
            if (\$elem.hasClass('telaItemImagem')) {
                \$('.{$r_tipo}editorTelaUploadImagem').css('display', 'flex');
                \$('.{$r_tipo}editorTelaUploadImagemLink').css('display', 'flex');
                \$(`#\${id}-menu`).find('.{$r_tipo}editorTelaUploadImagemLink').find('textarea').val(\$elem.find('.imagem').attr('src'));
            }
            // Barcode (JS)
            if (\$elem.hasClass('telaItemBarcode')) {
                \$('.{$r_tipo}editorTelaMenuItemTextoTitulo').css('display', 'flex');
                \$('.{$r_tipo}editorTelaMenuItemBarcode').css('display', 'flex');
                \$(`#\${id}-menu`).find('.{$r_tipo}editorTelaMenuItemTextoTitulo').find('textarea').val(\$elem.find('span.texto').html());
                \$(`#\${id}-menu`).find('.{$r_tipo}editorTelaMenuItemBarcode').find('textarea').val(\$elem.find('.options').val());
            }
            // Barcode (PHP)
            if (\$elem.hasClass('telaItemBarcode2')) {
                \$('.{$r_tipo}editorTelaMenuItemTextoTitulo').css('display', 'flex');
                \$('.{$r_tipo}editorTelaMenuItemBarcode2').css('display', 'flex');
                \$(`#\${id}-menu`).find('.{$r_tipo}editorTelaMenuItemTextoTitulo').find('textarea').val(\$elem.find('span.texto').html());                
                \$(`#\${id}-menu`).find('.{$r_tipo}editorTelaMenuItemBarcode2').find('textarea').val(\$elem.find('.options').val());
            }
            if (\$elem.hasClass('telaItemQrCode')) {
                \$('.{$r_tipo}editorTelaMenuItemTextoTitulo').css('display', 'flex');
                \$(`#\${id}-menu`).find('.{$r_tipo}editorTelaMenuItemTextoTitulo').find('textarea').val(\$elem.find('span.texto').html());                
            }
            // Editor
            if (\$elem.hasClass('telaItemEditor')) {
                \$('.{$r_tipo}editorTelaMenuItemEditor').css('display', 'flex');
                tinymce.remove();
                \$(`#\${id}-menu`).find('.{$r_tipo}editorTelaMenuItemEditor').find('textarea').val(\$elem.find('div.editor').html());
                tinymce.init({
                    selector: `#\${id}-menu .{$r_tipo}editorTelaMenuItemEditor textarea`,
                    plugins: 'print preview paste importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor insertdatetime advlist lists wordcount textpattern noneditable help charmap quickbars emoticons',
                    menubar: 'edit insert format table',
                    toolbar: 'undo redo | bold italic underline strikethrough | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media template link anchor codesample | ltr rtl',
                    quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quicktable',
                    noneditable_noneditable_class: 'mceNonEditable',
                    fontsize_formats: "8px 9px 10px 11px 12px 14px 18px 24px 30px 36px 48px 60px 72px 96px",
                    // O CSS abaixo deve estar na tela.php também
                    content_style: 'body { font-family: Roboto,sans-serif; font-size:14px; margin: 0 auto; } p { margin: 0 }',
                    statusbar: false,
                    setup:function(ed) {
                       ed.on('change', function(e) {
//                           console.log('the event object ', e);
//                           console.log('the editor object ', ed);
//                           console.log('the content ', ed.getContent());
                           if (!{$r_tipo}editorTelaItemSelecionado) { return; }
                           \$(`.telaItem-\${{$r_tipo}editorTelaItemSelecionado} .editor`).html(ed.getContent());
                       });
                   }
                });
            }
            //
            {$r_tipo}editorTelaItemSelecionado = \$elem.attr('unique');
            \$('.moveHandle').css({ border: 'none' });
            \$elem.find('.moveHandle').css({ border: '1px dashed silver' });
        };
        
        \$tela.find('.telaItem').each(function() {
            // Mover item pela tela
            if (\$(this).data('itemCarregado')) { return; }
            \$('<div>')
\t\t\t\t.addClass('moveHandle')
\t\t\t\t.css({'width': '100%', 'height': '100%', 'position': 'absolute', 'top': '0'})
\t\t\t\t.appendTo(\$(this));
\t\t\t\$(this).css({'cursor': 'move'});
            \$(this).bind('click', function() {
                selecionarElemento(\$(this));
            });
\t\t\t// http://www.myphpetc.com/2009/11/jquery-ui-multiple-draggable-plugin.html
\t\t\t\$(this).multidraggable({
\t\t\t\t'handle': '.moveHandle',
\t\t\t\tstart: function(event, ui) {
                    selecionarElemento(\$(this));
\t\t\t\t\t//\$(this).css({'border': '1px dotted black'});
\t\t\t\t\t\$(this).addClass('movingItem');
\t\t\t\t},
\t\t\t\tstop: function(event, ui) {
\t\t\t\t\t\$(this).removeClass('movingItem');
\t\t\t\t\t\$elem = \$(this);
\t\t\t\t\tvar posDiv = \$(this).position();
\t\t\t\t\tvar posElem = \$elem.position();
                    
                    posTopMod = posDiv.top;
                    posLeftMod = posDiv.left;
                    
                    \$(this).css({'top': posTopMod + 'px', 'left': posLeftMod + 'px'});
                    selecionarElemento(\$(this), true);
\t\t\t\t}
\t\t\t});
            \$(this).data('itemCarregado', true);
            
            // Redimensionar
            if (\$(this).hasClass('telaItemQuadro') || \$(this).hasClass('telaItemImagem') || \$(this).hasClass('telaItemGrid') || \$(this).hasClass('telaItemEditor') || \$(this).hasClass('telaItemBarcode') || \$(this).hasClass('telaItemBarcode2') || \$(this).hasClass('telaItemQrCode')) {
                var \$eResize = \$('<div>')
                    .addClass('telaItemWidth ui-icon ui-icon-arrow-2-e-w')
                    .css({'width': '16px', 'height': '16px', 'position': 'absolute', 'top': '0', 'right': '-16px', 'cursor': 'e-resize', 'overflow': 'visible'})
                    .data('originalWidth', \$(this).width())
                    .appendTo(\$(this))
                    .draggable({ 
                        'axis': 'x',
                        'drag': function(event, ui) {
                            redimensionando = true;
                            var diff = ui.position.left - ui.originalPosition.left;
                            \$(this).parent().width(\$(this).data('originalWidth') + diff);
                        },
                        'stop': function(event, ui) {
                            var width = \$(this).parent().width();
                            \$(this).parent().width(width);
                            \$(this).data('originalWidth', width);
                            setTimeout(function() {
                                redimensionando = false;
                            }, 100);
                        }
                    });
            }
            if (\$(this).hasClass('telaItemQuadro') || \$(this).hasClass('telaItemImagem') || \$(this).hasClass('telaItemBarcode') || \$(this).hasClass('telaItemBarcode2') || \$(this).hasClass('telaItemQrCode')) {
                 var \$eResize = \$('<div>')
                     .addClass('telaItemHeight ui-icon ui-icon-arrow-2-n-s')
                     .css({'height': '16px', 'height': '16px', 'position': 'absolute', 'bottom': '0', 'right': '-16px', 'cursor': 'n-resize', 'overflow': 'visible'})
                     .data('originalHeight', \$(this).height())
                     .appendTo(\$(this))
                     .draggable({
                         'axis': 'y',
                         'drag': function(event, ui) {
                             redimensionando = true;
                              var diff = ui.position.top - ui.originalPosition.top;
                              \$(this).parent().height(\$(this).data('originalHeight') + diff);
                         },
                         'stop': function(event, ui) {
                             var height = \$(this).parent().height();
                             \$(this).parent().height(height);
                             \$(this).data('originalHeight', height);
                             setTimeout(function() {
                                redimensionando = false;
                            }, 100);
                         }
                     });
            }
        });
    }
} </script></div>
EOT;
goto NjldV; oV7lT: case 'appsVisualizarArquivo': goto sDmpY; KlQBZ: $arquivo = $acaoId[0]; goto axhTW; en3PR: echo <<<EOT
<pre style="background-color: white; width: 100%; height: 100%; margin: 0; padding: 0; border: 0; white-space: normal; overflow: auto;">
    {$conteudo}
</pre>
EOT;
goto wNk8n; axhTW: if (!(strpos($arquivo, 'apps/') === false)) { goto JVSXZ; } goto Vul6V; zTdUN: JVSXZ: goto osek3; sDmpY: $acaoId = unserialize(base64_decode($r_acaoId)); goto KlQBZ; osek3: $conteudo = str_replace("\n", "<br/>", htmlentities(file_get_contents($arquivo))); goto en3PR; wNk8n: goto qgOwt; goto kWbh7; Vul6V: die('Acesso negado.'); goto zTdUN; kWbh7: case 'appsVisualizarConflito': goto VaiJC; VaiJC: $acaoId = unserialize(base64_decode($r_acaoId)); goto URE2M; ct1lm: if (!$conflito) { goto UJCmy; } goto yNuul; dJ1ni: $conteudo = str_replace(" ", '&nbsp;', $conteudo); goto E1TC1; E1TC1: goto Uxgym; goto yjxPr; Xyu5c: $conteudo = 'Sem nenhum conflito.'; goto ppX13; DgLix: echo <<<EOT
<pre style="background-color: white; width: 100%; height: 100%; margin: 0; padding: 0; border: 0; white-space: normal; overflow: auto;">
    {$conteudo}
</pre>
EOT;
goto g1psC; ppX13: Uxgym: goto DgLix; URE2M: $conflito = $acaoId[1]; goto ct1lm; yjxPr: UJCmy: goto Xyu5c; g1psC: goto qgOwt; goto fjF_9; yNuul: $conteudo = str_replace("\n", "<br/>", htmlentities(var_export($conflito, true))); goto dJ1ni; fjF_9: case 'appsInstalarVersao': goto XImr7; Cx1nO: $conteudo .= '</table></div>'; goto SOs1f; dH2h0: $conteudo = '<div style="width: 100%; height: 100%; font-size: 18px; background-color: var(--fundo-notificacao); color: var(--letra2); overflow: auto;"><table style="width: 100%;" cellpadding="4">'; goto oY2P7; XImr7: $app = Apps::getApp($r_acaoId); goto boI2m; Jcbxc: $conteudo .= <<<EOT
<tr>
    <td colspan="2">
        <iframe id='melhoriaVideo14' width="100%" height="400" src="{$app['versoes'][$r_versao]['video']}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    </td>
</tr>
EOT;
goto uUMq1; oY2P7: $conteudo .= <<<EOT
<tr>
    <td style="vertical-align: top; opacity: 0.8; width: 5%;">Nome:</td>
    <td>{$app['nome']}</td>
</tr>
<tr>
    <td style="vertical-align: top; opacity: 0.8;">Versão:</td>
    <td>{$r_versao}</td>
</tr>
<tr>
    <td style="vertical-align: top; opacity: 0.8;">Notas:</td>
    <td><div>{$notas}</div></td>
</tr>
EOT;
goto HXt6H; SOs1f: echo $conteudo; goto uRIJE; uUMq1: eZIGU: goto Cx1nO; boI2m: $notas = str_replace("\n", '<br/>', $app['versoes'][$r_versao]['notas']); goto dH2h0; uRIJE: goto qgOwt; goto XXF00; HXt6H: if (!$app['versoes'][$r_versao]['video']) { goto eZIGU; } goto Jcbxc; XXF00: case 'formulaMostrarOriginal': goto ayf3d; nfy3H: echo '</div>'; goto sNEx_; Jds11: ble7p: goto nfy3H; chte7: foreach (array('php', 'desenvolvedor', 'apps', 'global') as $nivel) { goto gj9QH; McinV: echo "<h3>{$nivel}:</h3>"; goto DKKaG; DKKaG: echo '<textarea style="width: 90%; height: 50px;">' . htmlentities($formulas[$r_acaoId]['updateValor']) . '</textarea>'; goto PTCRU; PTCRU: echo "<br/><br/>"; goto D3EbE; XnSx8: if (!$formulas[$r_acaoId]) { goto DFawc; } goto T_6F7; T_6F7: $nivel = strtoupper($nivel); goto McinV; gj9QH: $formulas = Personalizacoes::processarFormulas($r_tabela, array($nivel)); goto XnSx8; e6rX_: S53Ii: goto OsI1s; D3EbE: DFawc: goto e6rX_; OsI1s: } goto Jds11; sNEx_: goto qgOwt; goto JJVWD; ayf3d: echo '<div style="width: 100; height: 100%; padding: 8px 0 8px 16px;">'; goto chte7; JJVWD: case 'appsInstalarAtualizarCliente': goto l6Rhi; tLMN2: ttdJb: goto x5TU_; t22DW: $atualizar = true; goto EUkbv; x5TU_: echo <<<EOT
            </div>
EOT;
goto abYTU; MYiW8: foreach ($versoes as $versao) { goto tGS6L; NJMGU: gxi6R: goto BH2h6; va84H: echo <<<EOT
                <div class="versao">{$versaoTxt}</div>
                <div class="versaoDetalhes">{$versao['notas']}</div>
EOT;
goto NJMGU; SlksY: if (!($versaoAtiva == $versao['versao'])) { goto ilsSy; } goto BkfPb; BkfPb: $versaoTxt = "<strong>{$versao['versao']} <span style='opacity: 0.9;'>[INSTALADO]</span></strong>"; goto uvOar; uvOar: EOT; goto DAtzG; tGS6L: $versaoTxt = $versao['versao']; goto SlksY; DAtzG: ilsSy: goto va84H; BH2h6: } goto tLMN2; VNMQE: $versoes = $app['versoes']; goto O4jrT; m36ts: $btnTexto = $btnTexto == 'Instalar' ? 'Obter' : $btnTexto; goto aluca; M1drl: echo <<<EOT
            <div class="titulo">{$app['nome']}</div>
            <div class="descricao">{$app['descricao']}</div>
            <div class="download">
                <span class="btn {$btnClass}" onclick="{$btnOnclick}">{$btnTexto}</span>
            </div>
            {$btnDesinstalar}
            <div>&nbsp;</div>
            <div class="historico">
                <div class="header">Histórico de Alterações</div>
                <hr class="linha" />
EOT;
goto MYiW8; VJBq5: $btnOnclick = $atualizar ? "appsDownloadCliente('{$app['id']}', '{$versoes[0]['versao']}', 'atualizado')" : ''; goto QqWz4; O4jrT: $versoes = array_filter($versoes, function ($valor) { return $valor['producao'] == 'S'; }); goto IttWa; yty60: IfSMa: goto M1drl; LypnX: $atualizar = false; goto HCUC_; BkdWa: $btnTexto = ucfirst($btnClass); goto m36ts; tR0St: $btnClass = 'instalar'; goto t0MGx; abYTU: echo <<<EOT
\t\t\t<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
               function appsDownloadCliente(appId, versao, texto) {
                    \$('#{$g['divId']}').showLoading();
                    \$.getJSON('/sistema/personalizacaoLowCode/grid_func-ajax/appsDownload/?ajax=true&appId=' + encodeURIComponent(appId) + '&versao=' + encodeURIComponent(versao) + '&callback=?', function(request) {
                        \$('#{$g['divId']}').hideLoading();
                        \$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
                            divRefresh('{$g['divIdParent']}');
                            setTimeout(function() {
                                dialogConfirm('Loja de Aplicativos', `Aplicativo \${texto} com sucesso.`);
                            }, 150);
                            });
                        \$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
                    });
               }
\t\t\t} </script></div>
EOT;
goto jNcTA; t0MGx: $btnOnclick = "appsDownloadCliente('{$app['id']}', '{$versoes[0]['versao']}', 'instalado')"; goto sXAtV; QqWz4: tnKmc: goto BkdWa; Tw7RG: $btnClass = $atualizar ? 'atualizar' : 'instalado'; goto VJBq5; GT4P2: $versaoAtiva = $ativos[$app['id']]['versao']; goto PTohU; EUkbv: J5bGf: goto tR0St; Y6FvL: $app = Apps::getApp($r_acaoId); goto VNMQE; sXAtV: $btnDesinstalar = ''; goto nCB8Y; IMdF6: goto qgOwt; goto FsW6V; Pm1tg: $btnDesinstalar = "<div class='desinstalar'><a href='#' onclick=\"appsDownloadCliente('{$app['id']}', false, 'desinstalado');\">Desinstalar</a></div>"; goto Tw7RG; l6Rhi: $ativos = Apps::listarAtivos(); goto Y6FvL; nCB8Y: if (!$versaoAtiva) { goto tnKmc; } goto Pm1tg; aluca: echo "<div style='padding: 4px; overflow: auto; height: 100%;'><div class='detalhesApp'>"; goto TNj7L; PTohU: $versaoDiponivel = $versoes[0]['versao']; goto LypnX; IttWa: usort($versoes, function ($a, $b) { goto QLB0z; QLB0z: $a['versao'] = ltrim(str_replace(array('v', '.'), '', $a['versao']), '0'); goto wKxyS; wKxyS: $b['versao'] = ltrim(str_replace(array('v', '.'), '', $b['versao']), '0'); goto VtB43; VtB43: return $a['versao'] < $b['versao']; goto BfqLZ; BfqLZ: }); goto GT4P2; HCUC_: if (!(!empty($versaoAtiva) && $versaoAtiva != $versaoDiponivel)) { goto J5bGf; } goto t22DW; OfYpx: echo <<<EOT
            <div class="video">
                <iframe id='{$r_acaoId}' width="832" height="400" src="{$app['video']}" title="{$app['nome']}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div>
EOT;
goto yty60; TNj7L: if (!$app['video']) { goto IfSMa; } goto OfYpx; jNcTA: echo "</div></div>"; goto IMdF6; FsW6V: case 'outro': goto AIjhC; u3iQQ: $id = $g['rOriginal'][3]; goto YATvC; SNHpQ: GSRSW: goto HsEkG; PRY08: goto qblId; goto SNHpQ; WPeAE: x49ge: goto XMO8A; o5p1V: goto OcxDK; goto P0OPz; I5VCq: die('Caminho personalizado não encontrado.'); goto o5p1V; ggUhr: KD_zk: goto Q9NMY; lbuSd: goto qgOwt; goto KX3co; AIjhC: $i = 0; goto WPeAE; NxM8K: $codigo = file_get_contents($arquivo); goto czcQx; G1qwH: e7ECY: goto EvvIf; niA3Y: $arquivo = Personalizacoes::getDirPersonalizacoesOutro() . $outro['md5']; goto NxM8K; HsEkG: $i++; goto qRHNs; XhYqt: if ($i === 0 && !$g['modoDesenvolvedor']) { goto e7ECY; } goto KTqR8; tf7RE: goto GSRSW; goto V09pO; XMO8A: if (!($i < 3)) { goto qblId; } goto u3iQQ; EvvIf: Personalizacoes::$rootDir = Apps::getDirAtivos(); goto E_6P7; Q9NMY: yfR6b: goto niA3Y; czcQx: $codigo = str_replace('<?php', '', $codigo); goto KqJTE; cA0gZ: qblId: goto lbuSd; E_6P7: goto GSRSW; goto ggUhr; KqJTE: echo eval($codigo); goto PRY08; MvvaW: if ($outro) { goto yfR6b; } goto XhYqt; TG1Qa: Personalizacoes::$rootDir = 'ide/'; goto tf7RE; P0OPz: dd9r3: goto TG1Qa; YATvC: $outro = Personalizacoes::getOutroNovo($id); goto MvvaW; KTqR8: if ($i === 1 && !$g['modoDesenvolvedor']) { goto dd9r3; } goto I5VCq; qRHNs: goto x49ge; goto cA0gZ; V09pO: OcxDK: goto W785d; W785d: goto KD_zk; goto G1qwH; KX3co: } aLrt0: qgOwt: