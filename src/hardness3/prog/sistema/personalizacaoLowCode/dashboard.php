<?php
 namespace hardness; if ($g['modoDesenvolvedor']) { goto Bol2K; } if (!$g['modoDesenvolvedorApp']) { goto xMuWz; } Personalizacoes::$rootDir = Apps::getDirEditor(); xMuWz: goto yRhKe; Bol2K: Personalizacoes::$rootDir = 'ide/'; yRhKe: set_time_limit(0); switch ($g['r'][3]) { case 'dashboard': goto HISky; XMorS: cfTw3: goto xrPUo; Ilu14: echo $gerado['pagina']; goto A_2OQ; suzYD: GTeTW: goto Y2O7Z; FaJHc: if ($i === 1 && !$g['modoDesenvolvedor']) { goto PYQQY; } goto cpnPg; tpcj5: T1Ajw: goto bzmCA; ONCat: goto rm13l; goto waI0y; MvIfb: goto rF30L; goto HFBCx; TUyFF: $form_campo_pk = 'X000_Id'; goto MwPPe; m4Eg8: gJygM: goto EAmwY; aaiD9: goto NpMTf; goto ens2m; RznyY: Personalizacoes::$rootDir = Apps::getDirAtivos(); goto GDW05; b8P6V: $dashboardParametros = []; goto PZUTM; YYQeJ: if ($i === 0 && !$g['modoDesenvolvedor']) { goto HdbFO; } goto FaJHc; pituR: $tab = 'geral'; goto mAv8j; VIeCo: $g['smarty']->assign('table', $gerado['table']); goto z7wxZ; mWjXF: goto cfTw3; goto xhYEs; A_2OQ: if ($formMultiTab) { goto aky5A; } goto Dr5vA; ens2m: yBW8t: goto jrSX0; FLV3_: echo <<<EOT
\t\t\t<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                // Para corrigir um bug onde o editor "se movimenta" ao atualizar a tela
                setTimeout(function() {
                    \$('.formEditor-CodigoJsonBI').css('left', '16px');
                }, 10);                
\t\t\t} </script></div>
EOT;
goto BMEOs; mAv8j: $acaoId = false; goto MOG6t; EAmwY: $resposta .= '<div style="height: 90%; display: flex; align-items: center; justify-content: center;"><div style="width: 311px; height: 65px;">'; goto gBnVi; HFBCx: PYQQY: goto dkGni; bzmCA: if (!empty($r_dashboardPreVisualizar)) { goto ki4WK; } goto taCLP; P8LOq: $id = $g['rOriginal'][3]; goto tPmdH; lmJO2: $qtd = 0; goto Mo5rp; Xbu5E: goto YdkPT; goto N6U5v; f_Y3p: ki4WK: goto FLV3_; Ccyh7: if ($json) { goto gJygM; } goto tSPjk; qmcPV: YdkPT: goto suzYD; V0a3g: $form_tabela = 'X000'; goto TUyFF; u1pV1: $form = new Formulario("dashboardForm-{$id}"); goto w0SIg; tPmdH: $resposta = ''; goto NUU5W; tSPjk: die('<div style="margin: 4px;">Erro ao processar JSON: ' . json_last_error_msg() . '</div>'); goto m4Eg8; Mo5rp: $primeiroItem = true; goto XzjAK; CTd1r: goto yjqEu; goto tpcj5; NUU5W: if ($id == 'editor') { goto T1Ajw; } goto HJFpe; FiGI6: $enviar_acaoHref = false; goto u1pV1; SSTWl: usYsK: goto xue9K; F0eKo: foreach ($json['itens'] as $item) { goto kAb4D; l_ZFM: $primeiroItem_ = '0'; goto zEstS; tQtEW: l0A1o: goto l_ZFM; dL_2Y: $dashboardUrls[] = $item['url']; goto bkaW5; bkaW5: $dashboardParametros[] = $item['parametros']; goto ah0LR; PksRq: echo <<<EOT
                        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                            setTimeout(function() {
                                biDashboardAdicionarItem('{$g['divId']}', '{$item['url']}', '{$tab}', '{$hideLoading}', '{$tipo}', '{$primeiroItem_}');
                            }, 50);
                        } </script></div>
EOT;
goto dL_2Y; Pp5IY: $primeiroItem = false; goto tQtEW; iJpuA: $hideLoading = $qtd == count($json['itens']) ? 'S' : 'N'; goto U03xU; csjV5: $primeiroItem = true; goto wrXYM; U03xU: $tipo = strpos($item['url'], '/indicador/') !== false ? '2' : '1'; goto gANPE; gANPE: if (!($primeiroItem && $tipo == 2)) { goto l0A1o; } goto Pp5IY; wrXYM: $primeiroItem_ = '1'; goto ni1bi; ah0LR: $dashboardTipos[] = $tipo; goto FUeeK; kAb4D: $qtd++; goto iJpuA; ni1bi: TSob0: goto PksRq; zEstS: if (!(!$primeiroItem && $tipo == 1)) { goto TSob0; } goto csjV5; FUeeK: RZzwg: goto T01RG; T01RG: } goto Qgli6; HJFpe: $dashboard = Personalizacoes::getDashboardNovo($id); goto ni31b; Qgli6: Wk4gj: goto GrNzi; z7wxZ: $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : ''); goto Ilu14; xhYEs: NpMTf: goto ONCat; XzjAK: $dashboardUrls = []; goto b8P6V; cpnPg: die('Caminho personalizado não encontrado.'); goto MvIfb; eaLzQ: echo <<<EOT
                <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                    \$('#FiltroGlobal_{$id}').unbind('click').bind('click', function(data) {
                        abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Filtro Global', '/etc/etc011/content/etc011FiltroGlobal/', '&D132_Id=' + '&tab=' + encodeURIComponent('{$g['r'][3]}') + '&divIdReload=' + encodeURIComponent('{$g['divId']}'), [1200,350]);
                    });
                    \$('#Refresh_{$id}').unbind('click').bind('click', function(data) {
                        divRefresh('{$g['divId']}', true);
                    });
                    setTimeout(function() {
                        \$('#{$g['divId']} .formFormatoEditor').css('display','flex');
                        \$('#{$g['divId']} .formFormatoEditor').css('flex-wrap','wrap');
                        \$('#{$g['divId']} .formFormatoEditor').css('margin-left','5px');
                        \$('#{$g['divId']} .formFormatoEditor').css('margin-bottom','45px');
                        \$('#{$g['divId']} .formFormatoEditor').css('max-width','1300px');
                    }, 50);
                } </script></div>
EOT;
goto H9GIX; H9GIX: if (!(count($json['itens']) > 0)) { goto usYsK; } goto lmJO2; MOG6t: $formId = uniqid(); goto V0a3g; taCLP: die; goto f_Y3p; xue9K: echo <<<'EOT'
                <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                    // Adiciona um item do dashboard, mais não carrega
                    function biDashboardAdicionarItem(divId, itemUrl, tab, removeLoading, tipo, primeiroItem, left, top, width, height) {
                        var dashboardId = itemUrl.replaceAll('/', '');
                        if (tipo == 1) {
                            var width = 635;
                            var height = 420;
                        } else {
                            var width = 311;
                            var height = 65;
                        }
                    
                        var $div = $('<div id="' + tab + dashboardId + '" formEditorHidden="0" formEditorID="' + dashboardId + '" formEditorDIV="1" class="formEditor formEditor-' + dashboardId + ' formEditorBI"></div>')
                        .css({
                            'position' : 'relative',
                            'top' : '35px',
                            'width': width + 'px',
                            'max-width': width + 'px',
                            'height': height + 'px',
                            'margin' : '5px',
                            'overflow': 'auto',
                            'border-radius': '6px',
                        })
                        .appendTo($('#' + divId + ' .formFormatoEditor'));
                        // Show loading do sistema sempre faz um overlay fixo por cima (não funciona no scroll), vamos usar diferente aqui
                        //$('#' + tab + dashboardId).showLoading();
                        $('#' + tab + dashboardId).append(
                            $('<div class="loading-indicator-overlay-manual" style="width: 100%; height: 100%;"><div class="loading-indicator-manual" style="margin: auto; height: 100%;"></div></div>')
                        );
                    }
                    
                    // Faz o loading do item, um a um
                    function biDashboardCarregarItem(divId, dashboardUrls, dashboardParametros, tab, removeLoading, dashboardTipos, left, top, width, height) {
                        var itemUrl = dashboardUrls.shift();
                        var itemParametros = dashboardParametros.shift();
                        if (itemParametros && itemParametros != null && itemParametros[0] != '&') {
                            itemParametros = '&' + itemParametros;
                        }
                        var dashboardId = itemUrl.replaceAll('/', '');
                        var itemTipo = dashboardTipos.shift();
                        var $div = $('#' + tab + dashboardId);
                        $.ajax({
                            url: itemUrl,
                            dataType: 'html',
                            data: 'ajax=true&pularFiltro=true&divId=' + tab + dashboardId + itemParametros,
                            success: function (data) {
                                $div.html(data);
                                $('#' + tab + dashboardId).data({
                                    'divId': tab + dashboardId,
                                    'divIdRoot': '',
                                    'divIdParent': '',
                                    'vars': '',
                                    'href': itemUrl
                                });
                                // Chama o próximo
                                if (dashboardUrls.length > 0) {
                                    biDashboardCarregarItem(divId, dashboardUrls, dashboardParametros, tab, removeLoading, dashboardTipos, left, top, width, height);
                                }
                                return false;
                            }
                        });
                    }
                } </script></div>
EOT;
goto aaiD9; n18eT: $g['smarty']->display('formEditor.tpl'); goto eaLzQ; HISky: $i = 0; goto XMorS; Acn88: echo <<<EOT
                    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                        setTimeout(function() {
                            biDashboardCarregarItem('{$g['divId']}', {$dashboardUrls}, {$dashboardParametros}, '{$tab}', '{$hideLoading}', {$dashboardTipos});
                        }, 50);
                    } </script></div>
EOT;
goto SSTWl; HTxkO: $dashboardParametros = json_encode($dashboardParametros); goto UupKC; GIvV5: $g['smarty']->assign('formularioID', $form->formularioID()); goto VIeCo; PZUTM: $dashboardTipos = []; goto F0eKo; xrPUo: if (!($i < 3)) { goto NpMTf; } goto P8LOq; jrSX0: $i++; goto mWjXF; w0SIg: $formArray = array('campos' => array('geral' => array("Refresh_{$id}" => array('name' => "Refresh_{$id}", 'type' => 'button', 'value' => 'Atualizar', 'reject' => true)))); goto drzf1; QGeTJ: $json = json_decode($json, JSON_OBJECT_AS_ARRAY); goto Ccyh7; UupKC: $dashboardTipos = json_encode($dashboardTipos); goto Acn88; MBJG1: $gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk); goto GIvV5; drzf1: $form_editor = array('geral' => '{"Refresh_' . $id . '":{"top":8,"left":5,"width":null,"hidden":0}}'); goto HVX5z; T8Vat: aky5A: goto n18eT; T4SwZ: goto yBW8t; goto BihyV; gBnVi: yjqEu: goto pituR; GrNzi: $dashboardUrls = json_encode($dashboardUrls); goto HTxkO; Dr5vA: $g["smarty"]->assign("formId", $formId); goto T8Vat; dkGni: Personalizacoes::$rootDir = 'ide/'; goto T4SwZ; ni31b: if ($dashboard) { goto GTeTW; } goto YYQeJ; BihyV: rF30L: goto Xbu5E; Y2O7Z: $json = json_decode($dashboard['codigo'], JSON_OBJECT_AS_ARRAY); goto CTd1r; BMEOs: $json = apc_fetch("dashboard-pre-visualizar-{$r_dashboardPreVisualizar}"); goto QGeTJ; GDW05: goto yBW8t; goto qmcPV; MwPPe: $enviar_btnId = false; goto FiGI6; HVX5z: $form->definirFormularioArray($formArray); goto MBJG1; N6U5v: HdbFO: goto RznyY; waI0y: } UhfDa: rm13l: