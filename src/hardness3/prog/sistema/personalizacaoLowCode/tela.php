<?php
 namespace hardness; $generator = new \Picqer\Barcode\BarcodeGeneratorPNG(); include_once getcwd() . '/hardness/phpqrcode/phpqrcode.php'; if ($g['modoDesenvolvedor']) { goto sVNtZ; } if (!$g['modoDesenvolvedorApp']) { goto biK8N; } Personalizacoes::$rootDir = Apps::getDirEditor(); biK8N: goto mow3C; sVNtZ: Personalizacoes::$rootDir = 'ide/'; mow3C: function processarBarcode($html) { goto nzY8N; wxUA2: return $html; goto mJkCC; VqBnb: preg_match_all('/<div class="telaItem telaItemBarcode[^2].*<\\/div>/Uims', $html, $matches); goto JJjmo; rWGow: $count = 0; goto Tz6M_; hRo2n: ei_Tk: goto wxUA2; IAVo8: foreach ($matches[0] as $item) { goto ngoXD; zfTkk: $count++; goto KuxnS; qfTYX: preg_match('/<span class="texto">(.*)<\\/span>/Uims', $item, $matchText); goto O1lsu; Vm4Sf: $barcode = <<<EOT
<img class="telaItem" id="telaItem-{$matchUnique[1]}{$count}" style="{$matchStyle[1]}" />
<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
    JsBarcode("#telaItem-{$matchUnique[1]}{$count}", "{$matchText[1]}", {$matchOptions[1]});
} </script></div>
EOT;
goto zfTkk; hmArK: Yk8uO: goto UuNoP; KuxnS: $html = str_replace($item, $barcode, $html); goto hmArK; O1lsu: preg_match('/<input.*class="options.*value="(.*)">/Uims', $item, $matchOptions); goto mP6L7; ngoXD: preg_match('/unique="(.*)"/U', $item, $matchUnique); goto qfTYX; dGnHy: $matchStyle[1] = str_replace('border: 1px solid rgb(126, 126, 126); border-radius: 5px;', '', $matchStyle[1]); goto TFnVE; TFnVE: $matchOptions[1] = preg_replace('/\\{/', "{ height: {$matchStyleHeight[1]},", $matchOptions[1], 1); goto Vm4Sf; wGwki: preg_match('/height: (.*)px;/U', $matchStyle[1], $matchStyleHeight); goto dGnHy; mP6L7: preg_match('/style="(.*)"/U', $item, $matchStyle); goto wGwki; UuNoP: } goto hRo2n; vQem1: preg_match_all('/<div class="telaItem telaItemBarcode2.*<\\/div>/Uims', $html, $matches); goto rWGow; JJjmo: $count = 0; goto IAVo8; nzY8N: global $generator; goto vQem1; LSvQk: ht7_k: goto VqBnb; Tz6M_: foreach ($matches[0] as $item) { goto YRUR0; PIr1A: wyhCh: goto ZIJX0; wg_ox: $count++; goto Bf2jq; L6aD4: preg_match('/<input.*class="options.*value="(.*)">/Uims', $item, $matchOptions); goto cXYRi; BR1TX: $barcode = <<<EOT
<img class="telaItem" id="telaItem-{$matchUnique[1]}{$count}" style="{$matchStyle[1]}" src="data:image/png;base64,{$barcodeImage}" />
EOT;
goto wg_ox; JENPg: preg_match('/<span class="texto">(.*)<\\/span>/Uims', $item, $matchText); goto L6aD4; YRUR0: preg_match('/unique="(.*)"/U', $item, $matchUnique); goto JENPg; Bf2jq: $html = str_replace($item, $barcode, $html); goto PIr1A; Yqu0s: $barcodeImage = base64_encode($generator->getBarcode($matchText[1], $generator::TYPE_CODE_128, $widthFactor, $matchStyleHeight[1])); goto BR1TX; Nn_NP: $widthFactor = isset($options['widthFactor']) ? $options['widthFactor'] : 1; goto Yqu0s; G1Y8V: $options = json_decode(preg_replace('/\\b\\w+(?=:)/', '"$0"', $matchOptions[1]), true); goto Nn_NP; O9eNN: $matchOptions[1] = str_replace('\\n', '', $matchOptions[1]); goto G1Y8V; Ug5wt: $matchStyle[1] = str_replace('border: 1px solid rgb(126, 126, 126); border-radius: 5px;', '', $matchStyle[1]); goto O9eNN; ljDqc: preg_match('/height: (.*)px;/U', $matchStyle[1], $matchStyleHeight); goto Ug5wt; cXYRi: preg_match('/style="(.*)"/U', $item, $matchStyle); goto ljDqc; ZIJX0: } goto LSvQk; mJkCC: } function processarQrCode($html) { goto mFzEM; mFzEM: preg_match_all('/<div class="telaItem telaItemQrCode.*<\\/div>/Uims', $html, $matches); goto NJysn; J4FXs: A8Eyc: goto yM_Y5; yM_Y5: return $html; goto R9Yzl; g2GO0: foreach ($matches[0] as $item) { goto c27TP; HAbwN: a9NMQ: goto aDgZn; Zjcyf: \QRcode::png($matchText[1], null, QR_ECLEVEL_M, 4, 1); goto N3dOO; N3dOO: $qrCodeImage = base64_encode(ob_get_clean()); goto ebI63; SV5BW: preg_match('/<span class="texto">(.*)<\\/span>/Uims', $item, $matchText); goto TSI_D; TSI_D: preg_match('/<input.*class="options.*value="(.*)">/Uims', $item, $matchOptions); goto p6T7B; KR7zG: $matchOptions[1] = str_replace('\\n', '', $matchOptions[1]); goto xSFIW; aXhkT: $count++; goto idRPP; eH4kK: preg_match('/height: (.*)px;/U', $matchStyle[1], $matchStyleHeight); goto UqmnQ; c27TP: preg_match('/unique="(.*)"/U', $item, $matchUnique); goto SV5BW; p6T7B: preg_match('/style="(.*)"/U', $item, $matchStyle); goto eH4kK; xSFIW: ob_start(); goto Zjcyf; UqmnQ: $matchStyle[1] = str_replace('border: 1px solid rgb(126, 126, 126); border-radius: 5px;', '', $matchStyle[1]); goto KR7zG; ebI63: $qrCode = <<<EOT
            <img class="telaItem" id="telaItem-{$matchUnique[1]}{$count}" style="{$matchStyle[1]}" src="data:image/png;base64,{$qrCodeImage}" alt="QR Code" />
EOT;
goto aXhkT; idRPP: $html = str_replace($item, $qrCode, $html); goto HAbwN; aDgZn: } goto J4FXs; NJysn: $count = 0; goto g2GO0; R9Yzl: } switch ($g['r'][3]) { case 'tela': goto gaRV_; aeXny: if (!$matchStyleInterno) { goto KILtX; } goto AKjme; QYFhZ: $css = str_replace(' !important', '', $css); goto ZJ_zW; R7VD6: p0eXS: goto wb1Ph; UDZCn: $htmlCabecalho = $r_visualizar ? $tela['htmlCabecalhoVisualizar'] : $tela['htmlCabecalho']; goto W4KtG; pWajI: goto WGG4r; goto ExMnU; uK3pu: Personalizacoes::$rootDir = 'ide/'; goto fEZdT; hXt6V: if (!$htmlCabecalho) { goto c7jD_; } goto YghK5; O95Gl: die('Caminho personalizado não encontrado.'); goto PSDb1; PAef4: die; goto DD1wF; a1_XQ: $nomeArquivo = $r_nomeArquivo ? $r_nomeArquivo : $tela['nome'] . '.pdf'; goto rpZcy; jqL2I: Dnzcc: goto NLOdn; gOvx2: $id = $g['rOriginal'][3]; goto wSenY; pPItf: $htmlRodape = processarQrCode($htmlRodape); goto aAJ6E; jU5Wu: $mapeamentoPxMm = 3.7; goto VMMN9; Bpavt: preg_match('/top: (.*)px; left: (.*)px; width: (.*)px;/U', $matchStyle[1], $matchStyleInterno); goto NCN62; LLJVY: goto CGuZ0; goto gCexz; qIL3E: KA8JY: goto U5aUS; ljatE: echo <<<EOT
</div>
EOT;
goto Oln7q; ZRjyX: header('Content-Disposition: attachment; filename="' . $nomeArquivo . '"'); goto LWKzP; sFp1G: $tela = unserialize(file_get_contents($arquivo)); goto ofY6D; VMMN9: echo <<<EOT
<html>
\t<head>
\t\t<title>Relatório - {$tela['nome']}</title>
\t\t<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
\t\t<style>
\t\t    /* CSS telaEditorHtml (está em mais lugares) */
            .telaEditorHtml body {
                font-family: Roboto,sans-serif;
                font-size:14px;
                margin: 0 auto;
            }
            .telaEditorHtml p {
                margin: 0;
            }
            /*.telaEditorHtml hr {*/
            /*    margin-block-start: 3px; margin-block-end: 3px;*/
            /*}*/
            
            .new-page {
                page-break-before: always;
            }
            
\t\t    {$css}
\t\t    {$cssCorpo}
        </style>
        <link rel=stylesheet href="/hardness3/static/css/temas/padrao/jqueryui.css" type="text/css">
        <link rel=stylesheet href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" type="text/css">
        <link rel=stylesheet href="https://fonts.googleapis.com/icon?family=Material+Icons" type="text/css">
        <link rel=stylesheet href="/hardness3/static/css/temas/tema_{$confUsuario['dbDatabase']}_cache.css" type="text/css">
        <script src="/hardness3/static/js/jquery-1.6.2.min.js"></script>
        <script src="/hardness3/static/js/JsBarcode.all.min.js"></script>
\t\t<link rel="preconnect" href="https://fonts.gstatic.com">
\t\t<link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
        <script>
            \$(document).ready(function() {
                // Cabecalho
                var heightCabecalho = 0;
                \$('#htmlCabecalho .telaItem').each(function() {
                   \$item = \$(this);
                   // Na geração do PDF "\$item.getOuterHeight()" não funciona, por isso usamos "parseInt(window.getComputedStyle(\$item.get(0)).fontSize, 10);"
                   const height = \$item.position().top + (\$item.hasClass('telaItemTexto') ? parseInt(window.getComputedStyle(\$item.get(0)).fontSize, 10) : \$item.outerHeight());
                   // console.log(parseInt(window.getComputedStyle(\$item.get(0)).fontSize, 10));
                   // console.log(\$item.outerHeight());
                   // console.log(height);
                   // console.log('-----');
                   heightCabecalho = height > heightCabecalho ? height : heightCabecalho;
                });
                if (heightCabecalho > 0) {
                    if ('{$quebraPagina}' == 'N' && '{$loopPorPagina}' == 'S') {
                        heightCabecalho = {$pdfAltura} * {$mapeamentoPxMm};
                    }
                    \$('#htmlCabecalho').height(heightCabecalho);
                }
                
                // Corpo
                
                // Corrige a posição dos itens no loop
                // Exemplo: o top do segundo item é o top + height do item anterior, e assim consecutivamente
                // var loopHeightAtual = 0;
                // var loopHeightAnterior = 0;
                // var countItem = 0;
                // \$('.corpoItemLoop').each(function() {
                //     loopHeightAtual = 0;
                //     countItem++;
                //     \$(this).find('.telaItem').each(function() {
                //         \$item = \$(this);
                //         const height = \$item.position().top + (\$item.hasClass('telaItemTexto') ? parseInt(window.getComputedStyle(\$item.get(0)).fontSize, 10) : \$item.outerHeight());
                //         var top = parseInt(\$(this).css('top'), 10);
                //         if (countItem > 1) {
                //             \$item.css('top', top + loopHeightAnterior + 'px');                            
                //         }
                //         loopHeightAtual = height > loopHeightAtual ? height : loopHeightAtual;
                //     });
                //     loopHeightAnterior += loopHeightAtual;
                // });
                
                // Corrige a altura dos corpoItemLoop 
                \$('.corpoItemLoop').each(function() {
                    var heightCorpoItemLoop = 0;
                    \$(this).find('.telaItem').each(function() {
                       \$item = \$(this);
                       const height = \$item.position().top + (\$item.hasClass('telaItemTexto') ? parseInt(window.getComputedStyle(\$item.get(0)).fontSize, 10) : \$item.outerHeight());
                       heightCorpoItemLoop = height > heightCorpoItemLoop ? height : heightCorpoItemLoop;
                    });
                    if (heightCorpoItemLoop > 0) {
                        if ('{$quebraPagina}' == 'N' && '{$loopPorPagina}' == 'S') {
                            heightCorpoItemLoop = {$pdfAltura} * {$mapeamentoPxMm};
                        }
                        \$(this).height(heightCorpoItemLoop);
                    }
                });
                
                //
                var heightCorpo = 0;
                \$('#htmlCorpo .telaItem').each(function() {
                   \$item = \$(this);
                   const height = \$item.position().top + (\$item.hasClass('telaItemTexto') ? parseInt(window.getComputedStyle(\$item.get(0)).fontSize, 10) : \$item.outerHeight());
                   heightCorpo = height > heightCorpo ? height : heightCorpo;
                });
                \$('#htmlCorpo .corpoItemLoop').each(function() {
                   \$item = \$(this);
                   const height = \$item.position().top + (\$item.hasClass('telaItemTexto') ? parseInt(window.getComputedStyle(\$item.get(0)).fontSize, 10) : \$item.outerHeight());
                   heightCorpo = height > heightCorpo ? height : heightCorpo;
                });
                if (heightCorpo > 0) {
                    \$('#htmlCorpo').height(heightCorpo);
                }
                
                // Rodape
                var heightRodape = 0;
                \$('#htmlRodape .telaItem').each(function() {
                   \$item = \$(this);
                   const height = \$item.position().top + (\$item.hasClass('telaItemTexto') ? parseInt(window.getComputedStyle(\$item.get(0)).fontSize, 10) : \$item.outerHeight());
                   heightRodape = height > heightRodape ? height : heightRodape;
                });
                if (heightRodape > 0) {
                    if ('{$quebraPagina}' == 'N' && '{$loopPorPagina}' == 'S') {
                        heightRodape = {$pdfAltura} * {$mapeamentoPxMm};
                    }
                    \$('#htmlRodape').height(heightRodape);
                }
            });
        </script>
</head>
<body class="telaEditorHtml" style="background: none; background-color: white; color: black; width: 100%; height: 100%; font-size: 11px;">
EOT;
goto hXt6V; wrvKG: aiCuW: goto WH7a9; EKpmw: foreach ($retorno['loop'] as $key => $item) { goto CbpCU; BS9bp: e1CvV: goto ZLddi; CbpCU: $htmlCorpoTmp = ''; goto rqNlO; MJSKI: foreach ($item as $key => $value) { $htmlCorpoTmp = str_replace("[{$key}]", $value, $htmlCorpoTmp) . "\n"; DojGw: } goto KVxTw; q9D_x: $htmlCorpo .= $htmlCorpoTmp; goto BS9bp; e4i3N: if (!($loopPorPagina == 'S' && $quebraPagina == 'S' && $key < count($retorno['loop']))) { goto KpCXK; } goto BiwS7; BiwS7: $htmlCorpoTmp .= '<div class="new-page"></div>' . "\n"; goto myvRR; oMYeZ: $htmlCorpoTmp .= '</div>' . "\n"; goto e4i3N; myvRR: KpCXK: goto q9D_x; GN4Pj: $htmlCorpoTmp .= $htmlCorpoOriginal; goto MJSKI; rqNlO: $htmlCorpoTmp .= '<div class="corpoItemLoop" style="position: relative;">' . "\n"; goto GN4Pj; KVxTw: gS_mF: goto oMYeZ; ZLddi: } goto jqL2I; LWKzP: $url = gGerarUrlBackend('/sistema/personalizacaoLowCode/grid_func-ajax/telaGerarPdf/', '&acaoId=' . urlencode($id) . '&link=' . urlencode("/sistema/personalizacao/tela/{$id}/?ajax=true{$repassarParametros}") . '&inline=true'); goto xvuYO; ztZJr: if (!$htmlRodape) { goto BJwc8; } goto CmQK6; VzEGf: vZVmS: goto oSHJp; hOGRV: $htmlRodape = $r_visualizar ? $tela['htmlRodapeVisualizar'] : $tela['htmlRodape']; goto tjng9; RrPvv: zGh9E: goto sVMJD; rRNha: file_get_contents($url); goto CD1oM; ZumXL: if (!($loopPorPagina == 'S' && $quebraPagina == 'S')) { goto GCNlT; } goto ZA8nW; U5aUS: if (!$r_visualizar) { goto x1mLO; } goto oRTL8; YghK5: echo <<<EOT
<div id="htmlCabecalho" style="width: 100%; position: relative;">
    {$htmlCabecalho}
</div>
EOT;
goto ZumXL; aAJ6E: $pdfLargura = $tela['pdfLargura'] ? $tela['pdfLargura'] : 210; goto v62kb; jWke8: if ($r_salvarNoArquivo) { goto aiCuW; } goto a1_XQ; ztVyl: if ($tela) { goto U5_DN; } goto pnJGL; UbI6C: $htmlCabecalho = processarBarcode($htmlCabecalho); goto fieu8; kFpOt: goto kaF6O; goto kjwJU; ZFhvn: $cssCorpo = ''; goto Jvgf1; BgkJA: WGG4r: goto rFBHf; FJbuu: echo <<<EOT
<div id="htmlCorpo" style="width: 100%; position: relative;">
    {$htmlCorpo}
</div>
EOT;
goto kFpOt; pnJGL: if ($i === 0 && !$g['modoDesenvolvedor']) { goto TLxKQ; } goto oigWz; pNg2u: echo '</body>'; goto pWajI; W4KtG: $htmlCorpo = $r_visualizar ? $tela['htmlCorpoVisualizar'] : $tela['htmlCorpo']; goto hOGRV; sVMJD: $css = $tela['css']; goto QYFhZ; TTXHm: if (!($matchStyleInterno[3] > 100)) { goto qkoPE; } goto wzEm7; ExMnU: CGuZ0: goto G3qMU; kjwJU: cSN8U: goto R1lVc; wSenY: $tela = Personalizacoes::getTelaNova($id); goto ztVyl; B5db5: $cssCorpo = str_replace(' !important', '', $cssCorpo); goto czSnC; G3qMU: $i++; goto xIzNM; ofY6D: x1mLO: goto UDZCn; gaRV_: $i = 0; goto y3HE5; wb1Ph: o22dF: goto zgMKF; Wy3Bq: c7jD_: goto dMw70; czSnC: $cssCorpo = preg_replace('/([a-z]+:.*);/Uims', '$1 !important;', $cssCorpo); goto LQhz3; T72_f: Personalizacoes::$rootDir = Apps::getDirAtivos(); goto LLJVY; NLOdn: SOyY2: goto RrPvv; y3HE5: B1vpp: goto wqNPz; SMVMZ: GCNlT: goto Wy3Bq; tx45B: $htmlCorpo = ''; goto EKpmw; EsIsO: $codigo = explode('/', trim($tela['codigo'], '/')); goto klhQb; QMf9e: $htmlCorpo = processarQrCode($htmlCorpo); goto pPItf; rpZcy: header('Content-type: application/pdf'); goto ZRjyX; v62kb: $pdfAltura = $tela['pdfAltura'] ? $tela['pdfAltura'] : 297; goto jU5Wu; vsqxw: qkoPE: goto DF0uI; PSDb1: goto hFG5l; goto lECuw; xIzNM: goto B1vpp; goto BgkJA; wqNPz: if (!($i < 3)) { goto WGG4r; } goto gOvx2; Oln7q: cj4u7: goto fWIso; fieu8: $htmlCorpo = processarBarcode($htmlCorpo); goto wxaAi; fEZdT: goto CGuZ0; goto Gl3zb; P8mn2: $htmlCabecalho = processarQrCode($htmlCabecalho); goto QMf9e; zgMKF: if (!$retorno['loop']) { goto SOyY2; } goto XjCJ4; DF0uI: echo <<<EOT
<div id="htmlCorpo" style="{$style}" class="telaItem-{$matchUnique[1]}">
EOT;
goto ipSbn; HYzYW: foreach ($retorno['valores'] as $array) { goto Z7_Rt; wt23r: TlNB5: goto gT0U8; GHxMA: $keys = array_keys($array[0]); goto wt23r; Z7_Rt: if (!(count($array) > 0)) { goto TlNB5; } goto GHxMA; FswUR: s9GSm: goto dFjZH; r1xdW: YDzgs: goto VhbfH; gT0U8: foreach ($array as $item) { goto nvy0q; nvy0q: foreach ($item as $key => $value) { goto BiR15; XBgtR: if (!($pos !== false)) { goto tXPG1; } goto NdyQv; NdyQv: $htmlCorpo = substr_replace($htmlCorpo, $value, $pos, strlen("[{$key}]")); goto ZlsRj; ZlsRj: tXPG1: goto cMzz9; cMzz9: v07da: goto xHt1K; BiR15: $pos = strpos($htmlCorpo, "[{$key}]"); goto XBgtR; xHt1K: } goto WqpNp; ZS60g: pazlI: goto WuYw8; WqpNp: lWAJG: goto ZS60g; WuYw8: } goto r1xdW; VhbfH: foreach ($keys as $key) { $htmlCorpo = str_replace("[{$key}]", "", $htmlCorpo); Z5grX: } goto RtV6Z; RtV6Z: oIwG1: goto FswUR; dFjZH: } goto R7VD6; AKjme: $cssCorpo = $matchStyleInterno[2]; goto GYhvI; fWIso: ly8Dh: goto ztZJr; FFjhP: BJwc8: goto pNg2u; GYhvI: $cssCorpo = preg_replace('/(.*)({.*})/Umsi', ".telaItem-{$matchStyleInterno[1]} \$1 \$2\n", $cssCorpo); goto B5db5; kLtWG: if (!$retorno['valores']) { goto o22dF; } goto HYzYW; tjng9: $quebraPagina = $tela['quebraPagina'] ? $tela['quebraPagina'] : 'S'; goto uTrtv; BMWZK: if (!$tela['codigo']) { goto zGh9E; } goto EsIsO; oRTL8: $arquivo = "/tmp/{$id}"; goto sFp1G; NCN62: $style = "margin-top: {$matchStyleInterno[1]}px; margin-left: {$matchStyleInterno[2]}px;"; goto TTXHm; dMw70: if (!$htmlCorpo) { goto ly8Dh; } goto idYxk; wxaAi: $htmlRodape = processarBarcode($htmlRodape); goto P8mn2; CmQK6: echo <<<EOT
<div id="htmlRodape" style="width: 100%; position: relative;">
    {$htmlRodape}
</div>
EOT;
goto FFjhP; ctEaq: preg_match('/unique="(.*)"/U', $htmlCorpo, $matchUnique); goto IbLRE; jJ7o7: $repassarParametros = gRepassarParametros(false, array('pdf')); goto jWke8; rvjCD: goto Ilt9a; goto xVshz; oigWz: if ($i === 1 && !$g['modoDesenvolvedor']) { goto fjQkV; } goto O95Gl; ipSbn: echo file_get_contents($caminhoUrl); goto ljatE; Gl3zb: hFG5l: goto rvjCD; IbLRE: $caminhoUrl = $matchCaminho[1]; goto YV4Pb; Jvgf1: preg_match('/telaItemGrid.*unique="(.*)".*<input.*class="css.*value="(.*)">/Uims', $htmlCorpo, $matchStyleInterno); goto aeXny; j5TZ6: preg_match('/caminho-url="(.*)"/U', $htmlCorpo, $matchCaminho); goto E_csI; wzEm7: $style .= " width: {$matchStyleInterno[3]}px; overflow: hidden;"; goto vsqxw; YVoFu: if ($matches) { goto vZVmS; } goto UsR3B; Pr9hx: aXBO1: goto kLtWG; E_csI: preg_match('/style="(.*)"/U', $htmlCorpo, $matchStyle); goto ctEaq; UsR3B: if ($loopPorPagina == 'S') { goto cSN8U; } goto FJbuu; oSHJp: $repassarParametros = gRepassarParametros(false, array('pdf', 'titulo', 'noEmp', 'p', 'u', 'db', 'sessionCookie')); goto j5TZ6; Th5pK: foreach ($retorno as $key => $value) { goto A2ETe; I3Y8i: $htmlRodape = str_replace("[{$key}]", $value, $htmlRodape); goto q4vqn; Cl7cd: $htmlCabecalho = str_replace("[{$key}]", $value, $htmlCabecalho); goto HeFkI; M3Yfg: goto LrMWR; goto adWHn; HeFkI: $htmlCorpo = str_replace("[{$key}]", $value, $htmlCorpo); goto I3Y8i; adWHn: QsLQY: goto Cl7cd; q4vqn: LrMWR: goto L_X9t; A2ETe: if (!is_array($retorno[$key])) { goto QsLQY; } goto M3Yfg; L_X9t: } goto Pr9hx; lECuw: fjQkV: goto uK3pu; SK9T4: if (!$r_pdf) { goto KA8JY; } goto jJ7o7; klhQb: $codigo = $codigo[count($codigo) - 1]; goto wVEOG; rFBHf: goto egemx; goto bAreB; ZJ_zW: $css = preg_replace('/([a-z]+:.*);/Uims', '$1 !important;', $css); goto ZFhvn; gCexz: Ilt9a: goto GgYaP; FZwPo: kaF6O: goto kAvaU; CD1oM: gcnnm: goto qIL3E; kAvaU: goto cj4u7; goto VzEGf; tiPvD: $caminhoUrl = "{$confUsuario['urlRaiz']}/{$caminhoUrl}?ajax=true&generateHTML=true&generateHTMLTodos=true&generateHTMLIgnorarCabecalho=true&noEmp=true&p={$g['empresaAtual']}&u={$g['usuarioAtual']}&db={$confUsuario['dbDatabase']}{$repassarParametros}"; goto Bpavt; LQhz3: KILtX: goto UbI6C; DD1wF: goto gcnnm; goto wrvKG; YV4Pb: $caminhoUrl = html_entity_decode($caminhoUrl); goto tiPvD; XjCJ4: $htmlCorpoOriginal = $htmlCorpo; goto tx45B; R1lVc: echo $htmlCorpo; goto FZwPo; ZA8nW: echo '<div class="new-page"></div>' . "\n"; goto SMVMZ; GgYaP: U5_DN: goto SK9T4; xvuYO: echo file_get_contents($url); goto PAef4; xVshz: TLxKQ: goto T72_f; uTrtv: $loopPorPagina = $tela['loopPorPagina'] ? $tela['loopPorPagina'] : 'N'; goto BMWZK; idYxk: preg_match('/telaItemGrid.*caminho-url="(.*)"/U', $htmlCorpo, $matches); goto YVoFu; WH7a9: $url = gGerarUrlBackend('/sistema/personalizacaoLowCode/grid_func-ajax/telaGerarPdf/', '&acaoId=' . urlencode($id) . '&link=' . urlencode("/sistema/personalizacao/tela/{$id}/?ajax=true{$repassarParametros}") . '&salvarNoArquivo=' . urlencode($r_salvarNoArquivo)); goto rRNha; wVEOG: $retorno = executarCodigo($codigo); goto Th5pK; bAreB: } uFkof: egemx: