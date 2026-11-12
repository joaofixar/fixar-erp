<?php
 namespace hardness; if ($g['modoDesenvolvedor']) { goto OPcCM; } if (!$g['modoDesenvolvedorApp']) { goto FScFY; } Personalizacoes::$rootDir = Apps::getDirEditor(); FScFY: goto EOswz; OPcCM: Personalizacoes::$rootDir = 'ide/'; EOswz: ini_set('mysqli.reconnect', '1'); set_time_limit(0); $debug = false; $estrutura = BI::estruturaDeDados(); switch ($g['r'][3]) { case 'bi': goto pGizK; OGBwN: die; goto sn3mX; DoXBX: $incluirGraficoLabel_ = !isset($r_incluirGraficoLabel) || $r_incluirGraficoLabel != 'false' ? ' checked="checked"' : ''; goto n4aaQ; I_3Rl: $curvaABC[$row[0]] = 'A'; goto Ibwyq; C7MZR: $var = 'r_tipodataa' . $i; goto crB6N; Y64Eb: pdkwW: goto RW29z; QL_iG: $tipodata = 'tipodata' . $id; goto aC3Mw; nZ6qe: $numColsInicial++; goto tUelh; BW8P4: if (!(strpos($r_dimensao1, '_Mes') != false)) { goto N5yKR; } goto C2DKY; aylpO: SfrOE: goto Cp1pP; jou5y: $filtroAtual = mysql_real_escape_string(file_get_contents($file)); goto YULMc; NrI3V: ob_end_clean(); goto qIaEB; R51Tz: if (strpos($r_dimensao1, 'CONCAT') !== false) { goto kQ5CB; } goto evfV7; wliCc: $nova[] = $row[0]; goto ncTHj; to1GE: $row[0] = gCorrigeData($row[0]); goto PxUUV; hPeqR: $grafico['nrColunas'] = count($grafico['colunaTotal']); goto n0aEV; aQAug: QevRd: goto mpph0; AgNG3: oFPcB: goto p1mP4; Mkhi9: $tmp++; goto I7AlS; fKXVS: goto VYWZb; goto rTF_a; r1uK4: $curvaABCTotal['C'][0] = number_format($curvaABCTotal['C'][0], 0, '', '.'); goto sKgJC; O5m5E: $medida = 'medida' . $id; goto y2tY1; i6JKH: $r_dimensao1 = $json['linha']['campo']; goto fa01B; Bukmv: $result = mysql_query($sql); goto xqqpd; UHoKT: K83jd: goto pmvAb; q1JBr: $fato2_ = 'r_' . $fato2; goto uqQ5E; z7Rmw: $fato2 = 'fato2' . $id; goto vEFGh; mmjNz: goto PscfW; goto hGj9j; bJ9hc: if ($i === 0 && !$g['modoDesenvolvedor']) { goto m7gPa; } goto yb4KE; UfgmI: $fato = 'fato' . $id; goto pNGcs; OL68O: if (!$debug) { goto oFPcB; } goto JjqtH; i3tGr: $ocultarSempreColunas_ = $r_ocultarSempreColunas != 'false' ? ' checked="checked"' : ''; goto rb4Br; Z4BQK: TP2zj: goto A5H02; ftJjV: foreach ($geradoDados as &$valor) { goto Rpy84; lukOP: $i = 0; goto eHDvz; XBbUG: if (!($i < $countI)) { goto tzp0N; } goto QeGvv; eHDvz: BhOyF: goto XBbUG; A76jh: $valor[$i] = '-'; goto V1Qls; LGvlQ: xqbGl: goto D3bC8; FfpQb: tzp0N: goto LGvlQ; QeGvv: if (isset($valor[$i])) { goto ooosC; } goto A76jh; wJCBZ: if (!($count == 1)) { goto qlM1h; } goto pE_Gx; pE_Gx: goto xqbGl; goto dLuRu; yek3k: goto BhOyF; goto FfpQb; dLuRu: qlM1h: goto lukOP; V1Qls: ooosC: goto aLYqa; Rpy84: $count++; goto wJCBZ; aLYqa: eXQMd: goto DuqAs; DuqAs: $i++; goto yek3k; D3bC8: } goto RIAu7; ksMOK: lWp1B: goto mPDKT; sKgJC: $curvaABCTotal['C'][1] = number_format($curvaABCTotal['C'][1], 2, ',', '.'); goto NZ2Lu; fSwg7: $dir = $g['pathDadosUsuario'] . 'gridFiltro/'; goto s7IrT; bVTWb: if (!empty($ocultarTotais_)) { goto a7OH0; } goto C_fWP; II3al: if (!isset(${$var})) { goto SfrOE; } goto og6xQ; GULUQ: wKnJR: goto ann9c; LulIy: $fato = 'fato' . $id; goto RXDYU; peVk2: if ($json['tipo'] == 'grafico') { goto kwVA0; } goto t_fAI; hqmtY: dLtil: goto XScXx; p03Sx: E28Ko: goto nW747; wzAbp: UFvtP: goto LgEMd; PxUUV: m7Riy: goto wliCc; RIAu7: e0ae8: goto zQM7S; jMOyz: xiibs: goto QSxxq; Skxh2: if (!empty($ocultarPorcentagem_)) { goto hQhyl; } goto XueyR; D7WYu: goto qKGYB; goto BsEpq; m7PYD: $fato2_ = isset(${$fato2_}) ? ${$fato2_} : false; goto LulIy; y2Gyb: $g['smarty']->display('gridHTML.tpl'); goto jbI1U; bxBE_: if (!empty($ocultarTotais_)) { goto pWNpE; } goto lqR88; iv2ys: UU6pb: goto fdu1p; tHNwm: goto fNX1Y; goto p03Sx; z7MSR: $tmp = explode('/', $colunaTitulo); goto SdJFG; Qf6pr: unset($cache); goto uU39O; JaE0j: M9Vi2: goto ICO8p; dBRPC: if ($totalTmp <= $curvaB) { goto xx1rv; } goto IiH5j; Ua0Rz: if (!empty($ocultarPorcentagem_)) { goto NdWRs; } goto ZUzfO; gB2tR: $countI = count($geradoDados[0]); goto ftJjV; GGAGp: cSbdc: goto uv8Fx; GklBe: boSBc: goto z9Qqx; R33sh: g8iPc: goto Qj2EZ; IOJRd: $fato_ = 'r_' . $fato; goto ZYiaU; vLj3R: if (!($grafico['nrColunas'] <= 0 && in_array($r_exibir, array('barra')))) { goto HRx5E; } goto hPeqR; h9u3K: $fato_ = isset(${$fato_}) ? ${$fato_} : false; goto KjUs7; L8cJA: $id = $i == 0 ? '' : 'a' . $i; goto EVb6n; kcJ01: $i = 1; goto Jomm_; f0Pc3: $geradoDadosItem = array(); goto yb0ZO; DwH25: foreach ($json['dados'] as $key => $value) { goto lnTJZ; f9MSq: $varTipoData = $key > 0 ? "r_tipodata{$key}" : 'r_tipodata'; goto tgjdH; HFlO1: ICRPo: goto xI7e7; b3OEG: $varMedida = $key > 0 ? "r_medidaa{$key}" : 'r_medida'; goto jmAja; jmAja: if ($value['agregador'] && $value['agregador'] != 'sum') { goto ICRPo; } goto hKIg9; lnh4B: $campoTmp = explode('->', $value['campo']); goto KGUxT; KGUxT: ${$varFato2} = $campoTmp[0]; goto b3OEG; YnBYt: goto i76IH; goto HFlO1; lnTJZ: $varFato = $key > 0 ? "r_fatoa{$key}" : 'r_fato'; goto HKRc_; tgjdH: ${$varTipoData} = $value['dimensaoData'] ? $value['dimensaoData'] : 'padrao'; goto yig9A; xI7e7: ${$varMedida} = $value['agregador']; goto s313I; hKIg9: ${$varMedida} = 'sum'; goto YnBYt; yig9A: OtK01: goto TJihS; HKRc_: $fato = $value['campo']; goto C9Nwc; s313I: i76IH: goto f9MSq; C9Nwc: ${$varFato} = $fato; goto CPkaf; CPkaf: $varFato2 = $key > 0 ? "r_fato2a{$key}" : 'r_fato2'; goto lnh4B; TJihS: } goto Y3B9s; YNw_R: $nova[0] = formataDiaMes($tmp[0]) . '/' . $tmp[1]; goto iLi_a; fALZ_: $numColsPorFato++; goto pHvw5; rK0a7: $fato2_ = 'r_' . $fato2; goto ra3xz; YEZju: end($geradoDados); goto L82YB; CVoxU: QK4Z0: goto aWnKo; i0sSW: $numColsPorFato++; goto P55ov; YULMc: if (isset($r_FiltroUsuario)) { goto XUm4_; } goto fUMVq; UXX_N: y2rRq: goto a3vwz; QJRBI: aIlT4: goto J43ao; WdxQi: $geradoSql = addslashes(str_replace("\"", "'", str_replace("\r", ' ', str_replace("\n", ' ', $sql)))); goto Bukmv; Ibwyq: xlojn: goto NZ7nD; IHPkq: goto KSWcI; goto OZKvp; YEniT: $curvaABC[$row[0]] = 'B'; goto UaHke; brLdd: goto t4wFY; goto uw0lo; XK0xH: if (strpos($r_dimensao2, '_Mes') != false) { goto x2o8c; } goto GikaV; tUelh: a05pR: goto WPicg; K7XFa: $ocultarPorcentagem_ = ''; goto pjAIE; NGa22: if ($json) { goto ZYZdb; } goto gRARc; QXC0_: EoGoR: goto KwONT; Mo2IL: if (!$debug) { goto m4g41; } goto d96ih; evfV7: if (!(strpos($r_dimensao1, '_Mes') !== false)) { goto zGj7c; } goto WSkuC; bLgnw: echo <<<EOT
\t\t\t\t\t<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
\t\t\t\t\t\t// Click na linha
\t\t\t\t\t\t\$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
                            if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
                            var \$tr = \$(this).closest('tr');
                            var valor = \$tr.attr('id');
                            if (window.atob(valor).match(/Linhas.*Colunas/)) { return true; }
                            var dim = \$(this).find('td[dim]').attr('dim');
                            var formEditor = \$(this).closest('.formEditor').attr('id');
                            if (\$tr.hasClass('linhaNegrito')) {
                                \$tr.removeClass('linhaNegrito');
                            } else {
                                \$tr.parent().find('tr').removeClass('linhaNegrito');
                                \$tr.addClass('linhaNegrito');
                            }
                            \$('#' + '{$g['divId']}').showLoading();
                            \$.getJSON('/etc/etc011/grid_func-ajax/etc011AdicionarFiltroGlobal/?ajax=true&tab=' + encodeURIComponent('{$r_tab}') + '&valor=' + encodeURIComponent(valor) + '&dim=' + encodeURIComponent(dim) + '&callback=?', function(request) {
                                \$.getJSON('/etc/etc011/grid_func-ajax/etc011ReprocessarCache/?ajax=true&callback=?', function(request) {
                                    \$('#' + '{$g['divId']}').hideLoading();
//                                    divRefresh(\$('#' + '{$g['divId']}').closest('.ui-tabs-panel').attr('id'), true);
                                    // Refresh de todos os outros grids
                                    \$('#' + '{$g['divId']}').closest('.formFormulario').find('.formEditor').each(function() {
                                        if (\$(this).attr('id') != formEditor) {
                                            divRefresh(\$(this).attr('id'), false, false, 'manual');
                                        }
                                    });
                                    // \$('span[title="Recarregar"]').each(function() {
                                    //     var \$this = \$(this);
                                    //     if (\$this.closest('.formEditor').attr('id') != formEditor) {
                                    //         \$this.trigger('click');
                                    //     }
                                    // });
                                    // Recarregar cards
                                    \$('#' + '{$g['divId']}').closest('.formFormulario').find('.bi-indicador').each(function() {
                                        divRefresh(\$(this).parent().attr('id'), false, false, 'manual');
                                    });
                                });
                            });
                        });
\t\t\t\t\t} </script></div>
EOT;
goto SyG9a; yb0ZO: $countPersonalizado = 0; goto KwEuG; ako4Z: $nova[1] = formataDiaSemana($nova[1]); goto p7GxB; fnCXN: $i = 0; goto AtGjL; pZDFF: $grid = array(); goto k8LVf; CBTOa: file_put_contents($gridFiltroUsuario, $grafico); goto nX6Zu; y1uUD: $g['smarty']->assign('removerScrollLoading', true); goto G8gow; i02op: $r_incluirGraficoLegend = isset($json['grid']['incluirGraficoLegend']) && !$json['grid']['incluirGraficoLegend'] ? 'false' : 'true'; goto wbk7j; DREIU: $var = 'r_fato2a' . $i; goto e2GM4; UA2bG: if (isset($r_ocultarSempreColunas)) { goto PMYOU; } goto Vsthf; hm9aV: $keysAdicionados[$row[0]] = true; goto nlF9V; sA2JI: XaTZY: goto g2Wb8; AoXn3: $g['smarty']->assign('tfoot', $gridTabela['tfoot']); goto I8kq5; pnyZT: goto slmXv; goto dBT27; e_rGy: $dir = "{$g['pathDados']}personalizacaoGrid/"; goto tK1V_; KYnzH: if ($r_exibir != 'grid') { goto UEIj9; } goto RurSI; KiT_T: goto CAK4b; goto w8MNM; g2Wb8: goto AcmGs; goto DmUgH; tqIzI: m4g41: goto vtTQB; kG2P4: list($geradoSql_, $geradoDados_) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra); goto pZDFF; lEuuD: YHzAR: goto bUAw5; Q0J3a: $geradoDados[$key][] = $totalPorLinha; goto E7nDn; DQ7EE: $nova[0] = formataDiaMes($tmp[0]) . '/' . $tmp[1]; goto LCsZe; wUaww: CgO8V: goto fe2_x; BqBzH: $funcao = 'grafico' . $r_exibir; goto rCylT; YeRau: $i++; goto SrOY1; ShiY6: $r_ocultarTotais = $json['grid']['ocultarTotais'] ? 'true' : 'false'; goto yTxrg; RkEql: goto SVzFW; goto ATL13; gueSe: JBsYI: goto eDdDm; uk3HY: $colunaTitulo = formataDiaMes($colunaTitulo); goto jFT2T; haqHa: iiVVz: goto oGmE3; tK1V_: is_dir($dir) or mkdir($dir, 0777, true); goto bH4cS; XblQi: foreach ($json['filtros'] as $filtro) { goto idZHr; idZHr: $campo = $filtro['campo']; goto fyn0y; dHMOh: MzO4x: goto Wc5oC; fyn0y: $gridFiltro[$campo] = $filtro; goto dHMOh; Wc5oC: } goto GTaUv; cQe06: log('(time) 2 DIM - 2.5.1: ' . round(microtime(true) - $time_start, 4)); goto zRxf1; HbFik: goto J4B7w; goto Fe8lj; taXHO: $curvaABCTotal[$curvaABC[$row[0]]][1] += $row[1]; goto zlGdv; CopKA: $fato_ = isset(${$fato_}) ? ${$fato_} : false; goto K9VhH; bkeDp: $medida_ = isset(${$medida_}) ? ${$medida_} : false; goto igtF3; G0NsX: $thStyle = 'background-color: silver; cursor: auto; font-size:11px; white-space: nowrap; padding: 5px 5px;'; goto ZAe2m; Bs7qM: q_1im: goto Iao6U; KjUs7: $medida = 'medida' . $id; goto i0oU8; VkARD: if (!empty($ocultarTotais_)) { goto NFecj; } goto GW010; J0Oxc: goto E28Ko; goto HHoz5; dBT27: Rn0YY: goto GqRRX; jq033: if (!empty($ocultarPorcentagem_)) { goto l7RRe; } goto qF7KA; jbI1U: return true; goto Z4BQK; hl9qZ: $geradoDados[] = $nova; goto AWXc1; daX0R: ksB0d: goto t83VI; KMDdP: ob_start(); goto y2Gyb; VMgeZ: if (!empty($ocultarPorcentagem_)) { goto qQ1Ml; } goto fALZ_; wZOHw: $geradoDadosItem[$nova[-1][0]] = key($geradoDados); goto RkEql; PMu2o: NE7Gy: goto Mkhi9; FEAVn: if (empty($ocultarPorcentagem_) && empty($ocultarTotais_)) { goto fbpMg; } goto y1oPE; QnERF: $curvaABCTotal['A'][1] = number_format($curvaABCTotal['A'][1], 2, ',', '.'); goto mHD2T; AjsML: if (!empty($ocultarPorcentagem_)) { goto EoGoR; } goto G3xm0; vtDSI: $dim1Total = array(); goto AjsML; bKEwp: HwkKz: goto RhC81; PAN5x: $grafico = json_encode($grafico, true); goto kJDmG; pFJRN: foreach ($grid as $key => $value) { goto v1ZqO; rNotF: $grafico['colunas'][] = $value['colunaTituloLegenda']; goto Y6YaU; v1ZqO: if (!($key < 3)) { goto Wh2mB; } goto Z2a5I; Z2a5I: goto wXUo6; goto rFaBl; Y6YaU: wXUo6: goto NSvZ4; rFaBl: Wh2mB: goto rNotF; NSvZ4: } goto IyJ2a; p0myB: goto lkcHp; goto Yo3I2; JyM5y: VzSuP: goto Zzv2p; vHdrX: $row[0] = gCorrigeData($row[0]); goto ABO0P; r1fPB: echo '<!-- INICIO-FILTRO --><div class="dashboardFiltro">'; goto hv1Rm; S6Uql: $nova[1] = formataDiaMes($nova[1]); goto EBaEj; Qm2hA: Y35zm: goto D_LWk; wD3kr: baLvc: goto YdwSr; fOhDQ: PSzVm: goto LmLJ1; wbk7j: $r_numMetricas = count($json['dados']); goto i6JKH; Iao6U: $json = json_decode($bi['codigo'], JSON_OBJECT_AS_ARRAY); goto HbFik; zQM7S: if (!($r_mostrarCurvaABC != 'false')) { goto oITga; } goto g281Y; oMFWY: $extraVazioColuna = $r_incluirColunaVazia == 'false' ? 'HAVING (Valor != 0)' : ''; goto SGkAR; gbEdX: $cache = array(); goto wzAbp; Cp1pP: glsfF: goto hdkyb; psf1k: goto XaTZY; goto GGAGp; bH4cS: $gridFiltroUsuario = $dir . "relatorioBI_{$r_D132_Id}-{$g['usuarioAtual']}"; goto CBTOa; TY70l: $json = json_decode($json, JSON_OBJECT_AS_ARRAY); goto NGa22; EwbTV: $id = $i == 0 ? '' : 'a' . $i; goto z7Rmw; g93IY: cAshG: goto XzZ9K; jVT_P: $colunaTitulo = gCorrigeData($colunaTitulo); goto Witjc; uv8Fx: $tmp = explode('/', $nova[1]); goto Es2kt; Ny0Re: if ($bi) { goto q_1im; } goto bJ9hc; qH0GK: if (isset($r_ocultarColunasDashboard)) { goto iYNNb; } goto Zqbh3; wWsvd: $fato = 'fato' . $id; goto IOJRd; tkIFq: $fato2_ = isset(${$fato2_}) ? ${$fato2_} : false; goto e9w4b; XScXx: $row = mysql_fetch_row($result); goto Jw8_o; mMOVZ: $i = $i + 0 * $r_numMetricas; goto mmjNz; vEahp: $multiplicador = $i * 2; goto wUaww; L07O9: $bi = Personalizacoes::getBINovo($id); goto Ny0Re; RXDYU: $fato_ = 'r_' . $fato; goto BreR3; xNaxV: $fato_ = 'r_fato'; goto CopKA; W4p6P: $curvaABCTotal['B'][1] = number_format($curvaABCTotal['B'][1], 2, ',', '.'); goto r1uK4; IiFGI: $medida_ = 'r_' . $medida; goto bkeDp; iBcVl: $tipodata_ = 'r_' . $tipodata; goto KLCNW; uQrfP: $grafico = array('nrColunas' => 0, 'colunas' => array(), 'colunaTotal' => array(), 'colunaColunas' => array()); goto Q27h3; IZD3v: $result = mysql_query($sql); goto HTXFH; XKedh: $g['smarty']->display('grid.tpl'); goto zZQzW; SPflP: $fato_ = 'r_' . $fato; goto h9u3K; p9S5P: $nova[-1] = array($row[0], !$i ? $row[1] : 0); goto Wm0gM; eDdDm: if (strpos($r_dimensao1, '_Dia_Da_Semana') != false) { goto XWh3c; } goto MxgaX; NZ2Lu: $stringCurvaABC = <<<EOT
\t\t\t\t<div style="margin: 5px;"><span style="color: green; font-weight: bold;">A (80%)</span> : {$curvaABCTotal['A'][0]} - {$curvaABCTotal['A'][1]} &nbsp;&nbsp;|&nbsp;&nbsp; <span style="color: blue; font-weight: bold;">B (10%)</span> : {$curvaABCTotal['B'][0]} - {$curvaABCTotal['B'][1]} &nbsp;&nbsp;|&nbsp;&nbsp; <span style="color: var(--BGVermelho); font-weight: bold;">C (10%)</span> : {$curvaABCTotal['C'][0]} - {$curvaABCTotal['C'][1]}</div>
EOT;
goto ybyan; bO1qI: if (strpos($r_dimensao1, 'CONCAT') !== false) { goto Qk4Iv; } goto BW8P4; vtTQB: if ($resultDim2Ind) { goto LpcNm; } goto LIJgl; uqQ5E: $fato2_ = isset(${$fato2_}) ? ${$fato2_} : false; goto wWsvd; n4aaQ: $incluirGraficoLegend_ = !isset($r_incluirGraficoLegend) || $r_incluirGraficoLegend != 'false' ? ' checked="checked"' : ''; goto VA7hX; wPI2X: $medida_ = isset(${$medida_}) ? ${$medida_} : false; goto JNT2H; PDfRe: echo '</div><!-- FIM-FILTRO --->'; goto rhjbm; a19HU: Wo_Dy: goto k3UN8; OiACN: if (!(!isset($r_dimensao1) || empty($r_dimensao1))) { goto LvVG2; } goto OGBwN; I8kq5: $g['smarty']->assign('gridId', md5($g['r_'])); goto y1uUD; i_tkI: $md5 = md5($g['r_']); goto TZ0nG; O192Y: goto CgO8V; goto aKyBe; hGj9j: IO0Fw: goto s3Y0i; X8V54: pWNpE: goto jq033; qQtuI: $grid[] = array('colunaValor' => $r_dimensao1, 'colunaTitulo' => $estrutura[$r_dimensao1]['nome'], 'colunaAlinhar' => 'left', 'chavePrimaria' => true, 'chavePrimariaBase64' => true); goto fnCXN; Jw8_o: $dim1Total[$i] = isset($row[0]) ? $row[0] : 0; goto TnZME; vLwf4: $tmp = explode('/', $nova[0]); goto YNw_R; QeFPU: $tmp = count($geradoDados[$key]); goto KG9a_; qIaEB: echo $output; goto J0Oxc; D5fcC: $r_incluirLinhaVazia = $json['grid']['incluirLinhaVazia'] ? 'true' : 'false'; goto Yt38f; mNynj: echo <<<EOT
\t\t\t\t<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
\t\t\t\t\t/**
\t\t\t\t\t* Monta o gráfico de barra
\t\t\t\t\t*/
\t\t\t\t\tfunction g{$g['divId']}graficobarra() {
\t\t\t\t\t\t\$('#grafico{$g['divId']}').html('');
\t\t\t\t\t\t(function basic_bars(container, horizontal) {
\t\t\t\t\t\t    var horizontal = horizontal ? true : false, dims = [], dim = [], point, i, xAxisTexto1 = [], xAxisTexto2 = [], grafico, i = 0, colunas = 0, variacao;
\t\t\t\t\t\t\tgrafico = JSON.parse(\$('#{$g['divId']}').data('grafico'));
\t\t\t\t\t\t\t// tamanho de cada coluna
\t\t\t\t\t\t\tvariacao = 0.9 / grafico['nrColunas'];
\t\t\t\t\t\t\t// Calcula xAxisTexto1 (para fazer o tickFormatter do xaxis)
\t\t\t\t\t\t\ti = 0;
\t\t\t\t\t\t\t\$.each(grafico['colunaColunas'], function(index, value) {
\t\t\t\t\t\t\t\txAxisTexto1[i] = index;
\t\t\t\t\t\t\t\ti++;
\t\t\t\t\t\t\t});
\t\t\t\t\t\t    // Adiciona os dados de cada coluna
\t\t\t\t\t\t    for (colunas; colunas < grafico['nrColunas']; colunas++) {
\t\t\t\t\t    \t\tdim = [];
\t\t\t\t\t    \t\ti = 0;
\t\t\t\t\t\t\t    \$.each(grafico['colunaColunas'], function(index, value) {
\t\t\t\t    \t\t\t\ti++;
\t\t\t\t\t\t\t\t\tpoint = [i + (variacao * colunas), parseFloat(value[colunas][0])];
\t\t\t\t\t\t\t\t\tdim.push(point);
\t\t\t\t\t\t\t\t\t//xAxisTexto1[i + (variacao * colunas)] = index;
\t\t\t\t\t\t\t\t\txAxisTexto2[i + (variacao * colunas)] = value[colunas][1];
\t\t\t\t\t\t\t\t});
\t\t\t\t\t\t\t\tdims.push({data: dim, label: grafico['colunas'][colunas]});
\t\t\t\t\t\t\t}
\t\t\t\t\t\t    Flotr.draw(container, dims, {
\t\t\t\t\t\t    \tgrid: {
\t\t\t\t\t\t            outlineWidth: 0,
\t\t\t\t\t\t        },
\t\t\t\t\t\t        bars: {
\t\t\t\t\t\t            show: true,
\t\t\t\t\t\t            horizontal: horizontal,
\t\t\t\t\t\t            shadowSize: 0,
\t\t\t\t\t\t            barWidth: variacao
\t\t\t\t\t\t        },
\t\t\t\t\t\t        mouse: {
\t\t\t\t\t\t            track: true,
\t\t\t\t\t\t            relative: true,
\t\t\t\t\t\t            trackFormatter: function(x) {
\t\t\t\t\t            \t\treturn xAxisTexto2[x.nearest.x] + ' - ' + number_format(x.y, 2, ',', '.');
\t\t\t\t\t\t            }
\t\t\t\t\t\t        },
\t\t\t\t\t\t        yaxis: {
\t\t\t\t\t\t            min: 0,
\t\t\t\t\t\t            autoscaleMargin: 1
\t\t\t\t\t\t        },
\t\t\t\t\t\t        xaxis: {
\t\t\t\t\t\t        \tshowLabels: {$incluirGraficoLabel_},
\t\t\t\t\t\t            noTicks: xAxisTexto1.length,
\t\t\t\t\t\t            tickFormatter: function(x) {
\t\t\t\t\t\t                var x = x.split('.');
\t\t\t\t\t\t                if (parseInt(x[1]) == 0 ){ 
\t\t\t\t\t\t                \treturn xAxisTexto1[parseInt(x[0])-1];
\t\t\t\t\t\t                } else {
\t\t\t\t\t\t                \treturn '';
\t\t\t\t\t\t                }
\t\t\t\t\t\t            }
\t\t\t\t\t\t        },
\t\t\t\t\t\t        legend: {
\t\t\t\t\t\t        \tshow: {$incluirGraficoLegend_},
\t\t\t\t\t\t            position: "ne",
\t\t\t\t\t\t            backgroundColor: "#D2E8FF"
\t\t\t\t\t\t        }
\t\t\t\t\t\t    });
\t\t\t\t\t\t})(document.getElementById("grafico{$g['divId']}"));
\t\t\t\t\t}
\t\t\t\t\tfunction g{$g['divId']}graficopizza() {
\t\t\t\t\t\t\$('#grafico{$g['divId']}').html('');
\t\t\t\t\t\t(function basic_pie(container) {
\t\t\t\t\t\t\tvar data = [], dim, grafico, graph, i = 0, xAxisTexto = [];
\t\t\t\t\t\t\tgrafico = JSON.parse(\$('#{$g['divId']}').data('grafico'));
\t\t\t\t\t\t\t\$.each(grafico['colunaTotal'], function(index, value) {
\t\t\t\t\t\t\t\ti++;
\t\t\t\t\t\t\t\tdim = {
\t\t\t\t\t\t\t\t\tdata: [[i, parseFloat(value)]],
\t\t\t\t\t\t\t\t\tlabel: index
\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t\tdata.push(dim);
\t\t\t\t\t\t\t\txAxisTexto.push(index);
\t\t\t\t\t\t\t});
\t\t\t\t\t\t    graph = Flotr.draw(container, data, {
\t\t\t\t\t\t        HtmlText: false,
\t\t\t\t\t\t        grid: {
\t\t\t\t\t\t            verticalLines: false,
\t\t\t\t\t\t            horizontalLines: false,
\t\t\t\t\t\t            outlineWidth: 0,
\t\t\t\t\t\t        },
\t\t\t\t\t\t        xaxis: {
\t\t\t\t\t\t            showLabels: false
\t\t\t\t\t\t        },
\t\t\t\t\t\t        yaxis: {
\t\t\t\t\t\t            showLabels: false
\t\t\t\t\t\t        },
\t\t\t\t\t\t        pie: {
\t\t\t\t\t\t        \tshowLabels: {$incluirGraficoLabel_},
\t\t\t\t\t\t            show: true,
\t\t\t\t\t\t            explode: 8,
\t\t\t\t\t\t            shadowSize: 2,
\t\t\t\t\t\t            labelFormatter : function (total, value) {
\t\t\t\t\t\t            \tif ('{$incluirGraficoLabel_}' == 'false') {
\t\t\t\t\t\t            \t\treturn '';
\t\t\t\t\t\t            \t}
\t\t\t\t\t\t            \tvar x = parseFloat((value*100)/total);
\t\t\t\t\t\t            \tif (x < 4) {
\t\t\t\t\t\t            \t\treturn '';
\t\t\t\t\t\t            \t} else {
        \t\t\t\t\t\t\t\t\treturn x.toFixed(2) + '%';
        \t\t\t\t\t\t\t\t}
      \t\t\t\t\t\t\t\t}
\t\t\t\t\t\t        },
\t\t\t\t\t\t        mouse: {
\t\t\t\t\t\t            track: true,
\t\t\t\t\t\t            relative: true,
\t\t\t\t\t\t            trackFormatter: function(x) {
\t\t\t\t\t            \t\treturn xAxisTexto[(x.x - 1)] + ' - ' + number_format(x.y, 2, ',', '.');
\t\t\t\t\t\t            }
\t\t\t\t\t\t        },
\t\t\t\t\t\t        legend: {
\t\t\t\t\t\t        \tshow: {$incluirGraficoLegend_},
\t\t\t\t\t\t            position: "ne",
\t\t\t\t\t\t            backgroundColor: "#D2E8FF"
\t\t\t\t\t\t        }
\t\t\t\t\t\t    });
\t\t\t\t\t\t})(document.getElementById("grafico{$g['divId']}"));
\t\t\t\t\t}
\t\t\t\t\tfunction g{$g['divId']}graficolinha() {
\t\t\t\t\t\t\$('#grafico{$g['divId']}').html('');
\t\t\t\t\t\t(function basic_line(container) {
\t\t\t\t\t\t    var dims = [], dim = [], point, i, xAxisTexto1 = [], xAxisTexto2 = [], grafico, i = 0, colunas = 0, variacao;
\t\t\t\t\t\t\tgrafico = JSON.parse(\$('#{$g['divId']}').data('grafico'));
\t\t\t\t\t\t\t// variacao da linha (para poder identificar cada linha)
\t\t\t\t\t\t\tvariacao = 0.001;
\t\t\t\t\t\t\t// Calcula xAxisTexto1 (para fazer o tickFormatter do xaxis)
\t\t\t\t\t\t\ti = 0;
\t\t\t\t\t\t\t\$.each(grafico['colunaColunas'], function(index, value) {
\t\t\t\t\t\t\t\txAxisTexto1[i] = index;
\t\t\t\t\t\t\t\ti++;
\t\t\t\t\t\t\t});
\t\t\t\t\t\t    // Adiciona os dados de cada coluna
\t\t\t\t\t\t    for (colunas; colunas < grafico['nrColunas']; colunas++) {
\t\t\t\t\t    \t\tdim = [];
\t\t\t\t\t    \t\ti = 0;
\t\t\t\t\t\t\t    \$.each(grafico['colunaColunas'], function(index, value) {
\t\t\t\t    \t\t\t\ti++;
\t\t\t\t\t\t\t\t\tpoint = [i + (variacao * colunas), parseFloat(value[colunas][0])];
\t\t\t\t\t\t\t\t\tdim.push(point);
\t\t\t\t\t\t\t\t\t//xAxisTexto1[i + (variacao * colunas)] = index;
\t\t\t\t\t\t\t\t\txAxisTexto2[i + (variacao * colunas)] = value[colunas][1];
\t\t\t\t\t\t\t\t});
\t\t\t\t\t\t\t\tdims.push({data: dim, label: grafico['colunas'][colunas]});
\t\t\t\t\t\t\t}
\t\t\t\t\t\t    Flotr.draw(container, dims, {
\t\t\t\t\t\t    \tgrid: {
\t\t\t\t\t\t            outlineWidth: 0,
\t\t\t\t\t\t        },
\t\t\t\t\t\t        mouse: {
\t\t\t\t\t\t            track: true,
\t\t\t\t\t\t            relative: true,
\t\t\t\t\t\t            trackFormatter: function(x) {
\t\t\t\t\t            \t\treturn xAxisTexto2[x.nearest.x] + ' - ' + number_format(x.y, 2, ',', '.');
\t\t\t\t\t\t            }
\t\t\t\t\t\t        },
\t\t\t\t\t\t        yaxis: {
\t\t\t\t\t\t            min: 0,
\t\t\t\t\t\t            autoscaleMargin: 1
\t\t\t\t\t\t        },
\t\t\t\t\t\t        xaxis: {
\t\t\t\t\t\t        \tshowLabels: {$incluirGraficoLabel_},
\t\t\t\t\t\t            noTicks: xAxisTexto1.length,
\t\t\t\t\t\t            tickFormatter: function(x) {
\t\t\t\t\t\t                var x = x.split('.');
\t\t\t\t\t\t                if (parseInt(x[1]) == 0 ){ 
\t\t\t\t\t\t                \treturn xAxisTexto1[parseInt(x[0])-1];
\t\t\t\t\t\t                } else {
\t\t\t\t\t\t                \treturn '';
\t\t\t\t\t\t                }
\t\t\t\t\t\t            }
\t\t\t\t\t\t        },
\t\t\t\t\t\t        lines: {
\t\t\t\t\t\t            show: true,
\t\t\t\t\t\t            shadowSize: 0,
\t\t\t\t\t\t        },
\t\t\t\t\t\t        points: {
\t\t\t\t\t\t            show: true
\t\t\t\t\t\t        },
\t\t\t\t\t\t        legend: {
\t\t\t\t\t\t        \tshow: {$incluirGraficoLegend_},
\t\t\t\t\t\t            position: "ne",
\t\t\t\t\t\t            backgroundColor: "#D2E8FF"
\t\t\t\t\t\t        },
\t\t\t\t\t\t    });
\t\t\t\t\t\t})(document.getElementById("grafico{$g['divId']}"));
\t\t\t\t\t}
\t\t\t\t\tfunction g{$g['divId']}graficoarea() {
\t\t\t\t\t\t\$('#grafico{$g['divId']}').html('');
\t\t\t\t\t\t(function basic_area(container) {
\t\t\t\t\t\t\tvar dims = [], dim = [], point, i, xAxisTexto1 = [], xAxisTexto2 = [], grafico, i = 0, colunas = 0, variacao;
\t\t\t\t\t\t\tgrafico = JSON.parse(\$('#{$g['divId']}').data('grafico'));
\t\t\t\t\t\t\t// variacao da linha (para poder identificar cada linha)
\t\t\t\t\t\t\tvariacao = 0.001;
\t\t\t\t\t\t\t// Calcula xAxisTexto1 (para fazer o tickFormatter do xaxis)
\t\t\t\t\t\t\ti = 0;
\t\t\t\t\t\t\t\$.each(grafico['colunaColunas'], function(index, value) {
\t\t\t\t\t\t\t\txAxisTexto1[i] = index;
\t\t\t\t\t\t\t\ti++;
\t\t\t\t\t\t\t});
\t\t\t\t\t\t    // Adiciona os dados de cada coluna
\t\t\t\t\t\t    for (colunas; colunas < grafico['nrColunas']; colunas++) {
\t\t\t\t\t    \t\tdim = [];
\t\t\t\t\t    \t\ti = 0;
\t\t\t\t\t\t\t    \$.each(grafico['colunaColunas'], function(index, value) {
\t\t\t\t    \t\t\t\ti++;
\t\t\t\t\t\t\t\t\tpoint = [i + (variacao * colunas), parseFloat(value[colunas][0])];
\t\t\t\t\t\t\t\t\tdim.push(point);
\t\t\t\t\t\t\t\t\t//xAxisTexto1[i + (variacao * colunas)] = value[colunas][1];
\t\t\t\t\t\t\t\t\txAxisTexto2[i + (variacao * colunas)] = value[colunas][1];
\t\t\t\t\t\t\t\t});
\t\t\t\t\t\t\t\tdims.push({data: dim, label: grafico['colunas'][colunas]});
\t\t\t\t\t\t\t}
\t\t\t\t\t\t    Flotr.draw(container, dims, {
\t\t\t\t\t\t    \tgrid: {
\t\t\t\t\t\t            outlineWidth: 0,
\t\t\t\t\t\t        },
\t\t\t\t\t\t        mouse: {
\t\t\t\t\t\t            track: true,
\t\t\t\t\t\t            relative: true,
\t\t\t\t\t\t            trackFormatter: function(x) {
\t\t\t\t\t            \t\treturn xAxisTexto2[x.nearest.x] + ' - ' + number_format(x.y, 2, ',', '.');
\t\t\t\t\t\t            }
\t\t\t\t\t\t        },
\t\t\t\t\t\t        yaxis: {
\t\t\t\t\t\t            min: 0,
\t\t\t\t\t\t            autoscaleMargin: 1,
\t\t\t\t\t\t            tickFormatter: function(x) {
\t\t\t\t\t\t\t\t\t\tif (x>=1000000){
\t\t\t\t\t\t\t\t\t\t\tx = x / 1000000;
\t\t\t\t\t\t\t\t\t\t\treturn "<font style='font-size:12px'>" + number_format(x, 2, '.', '.') + "MI </font>";
\t\t\t\t\t\t\t\t\t\t} else if (x>1000){
\t\t\t\t\t\t\t\t\t\t\tx = x / 1000;
\t\t\t\t\t\t\t\t\t\t\treturn "<font style='font-size:12px'>" + number_format(x, 0, '.', '.') + "K </font>";
\t\t\t\t\t\t\t\t\t\t} else {
\t\t\t\t\t\t\t\t\t\t\treturn "<font style='font-size:12px'>" + number_format(x, 0, ',', '.') + "</font>";
\t\t\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t            }
\t\t\t\t\t\t        },
\t\t\t\t\t\t        xaxis: {
\t\t\t\t\t\t        \tshowLabels: {$incluirGraficoLabel_},
\t\t\t\t\t\t            noTicks: xAxisTexto1.length,
\t\t\t\t\t\t            tickFormatter: function(x) {
\t\t\t\t\t\t                var x = x.split('.');
\t\t\t\t\t\t                if (parseInt(x[1]) == 0 ){ 
\t\t\t\t\t\t                \treturn xAxisTexto1[parseInt(x[0])-1];
\t\t\t\t\t\t                } else {
\t\t\t\t\t\t                \treturn '';
\t\t\t\t\t\t                }
\t\t\t\t\t\t            }
\t\t\t\t\t\t        },
\t\t\t\t\t\t        lines: {
\t\t\t\t\t\t            fill: true,
\t\t\t\t\t\t            show: true,
\t\t\t\t\t\t            shadowSize: 1,
\t\t\t\t\t\t        },
\t\t\t\t\t\t        points: {
\t\t\t\t\t\t            show: true
\t\t\t\t\t\t        },
\t\t\t\t\t\t        legend: {
\t\t\t\t\t\t        \tshow: {$incluirGraficoLegend_},
\t\t\t\t\t\t            position: "ne",
\t\t\t\t\t\t            backgroundColor: "#D2E8FF"
\t\t\t\t\t\t        },
\t\t\t\t\t\t        label: {
\t\t\t\t\t\t        \tshow: true,
\t\t\t\t\t\t            position: "ne",
\t\t\t\t\t\t            backgroundColor: "#D2E8FF"
\t\t\t\t\t\t        }
\t\t\t\t\t\t    });
\t\t\t\t\t\t})(document.getElementById("grafico{$g['divId']}"));
\t\t\t\t\t}
\t\t\t\t\tfunction g{$g['divId']}graficoradar() {
\t\t\t\t\t\t\$('#grafico{$g['divId']}').html('');
\t\t\t\t\t\t(function basic_area(container) {
\t\t\t\t\t\t\tvar dims = [], dim = [], point, i, xAxisTexto1 = [], xAxisTexto2 = [], grafico, i = 0, colunas = 0, variacao;
\t\t\t\t\t\t\tgrafico = JSON.parse(\$('#{$g['divId']}').data('grafico'));
\t\t\t\t\t\t\t// variacao da linha (para poder identificar cada linha)
\t\t\t\t\t\t\tvariacao = 0.001;
\t\t\t\t\t\t\t// Calcula xAxisTexto1 (para fazer o tickFormatter do xaxis)
\t\t\t\t\t\t\ti = 0;
\t\t\t\t\t\t\t\$.each(grafico['colunaColunas'], function(index, value) {
\t\t\t\t\t\t\t\txAxisTexto1[i] = index;
\t\t\t\t\t\t\t\ti++;
\t\t\t\t\t\t\t});
\t\t\t\t\t\t    // Adiciona os dados de cada coluna
\t\t\t\t\t\t    for (colunas; colunas < grafico['nrColunas']; colunas++) {
\t\t\t\t\t    \t\tdim = [];
\t\t\t\t\t    \t\ti = 0;
\t\t\t\t\t\t\t    \$.each(grafico['colunaColunas'], function(index, value) {
\t\t\t\t    \t\t\t\ti++;
\t\t\t\t\t\t\t\t\tpoint = [i + (variacao * colunas), parseFloat(value[colunas][0])];
\t\t\t\t\t\t\t\t\tdim.push(point);
\t\t\t\t\t\t\t\t\t//xAxisTexto1[i + (variacao * colunas)] = index;
\t\t\t\t\t\t\t\t\txAxisTexto2[i + (variacao * colunas)] = value[colunas][1];
\t\t\t\t\t\t\t\t});
\t\t\t\t\t\t\t\tdims.push({data: dim, label: grafico['colunas'][colunas]});
\t\t\t\t\t\t\t}
\t\t\t\t\t\t    Flotr.draw(container, dims, {
\t\t\t\t\t\t    \tgrid: {
\t\t\t\t\t\t            outlineWidth: 0,
\t\t\t\t\t\t        },
\t\t\t\t\t    \t\tradar: {
\t\t\t\t\t\t            show: true,
\t\t\t\t\t\t            shadowSize: 0,
\t\t\t\t\t\t        },
\t\t\t\t\t\t        grid: {
\t\t\t\t\t\t            circular: true,
\t\t\t\t\t\t            minorHorizontalLines: true
\t\t\t\t\t\t        },
\t\t\t\t\t\t        mouse: {
\t\t\t\t\t\t            track: true,
\t\t\t\t\t\t            relative: true,
\t\t\t\t\t\t            trackFormatter: function(x) {
\t\t\t\t\t            \t\treturn xAxisTexto2[x.nearest.x] + ' - ' + number_format(x.y, 2, ',', '.');
\t\t\t\t\t\t            }
\t\t\t\t\t\t        },
\t\t\t\t\t\t        yaxis: {
\t\t\t\t\t\t\t\t\tmin: 0,
\t\t\t\t\t\t            autoscaleMargin: 1
\t\t\t\t\t\t        },
\t\t\t\t\t\t        xaxis: {
\t\t\t\t\t\t        \tshowLabels: {$incluirGraficoLabel_},
\t\t\t\t\t\t            noTicks: xAxisTexto1.length,
\t\t\t\t\t\t            tickFormatter: function(x) {
\t\t\t\t\t\t                var x = x.split('.');
\t\t\t\t\t\t                if (parseInt(x[1]) == 0 ){ 
\t\t\t\t\t\t                \treturn xAxisTexto1[parseInt(x[0])-1];
\t\t\t\t\t\t                } else {
\t\t\t\t\t\t                \treturn '';
\t\t\t\t\t\t                }
\t\t\t\t\t\t            }
\t\t\t\t\t\t        },
\t\t\t\t\t\t        legend: {
\t\t\t\t\t\t        \tshow: {$incluirGraficoLegend_},
\t\t\t\t\t    \t\t\tposition: "ne",
\t\t\t\t\t        \t\tbackgroundColor: "#D2E8FF"
\t\t\t\t\t\t        }
\t\t\t\t\t\t    });
\t\t\t\t\t\t})(document.getElementById("grafico{$g['divId']}"));
\t\t\t\t\t}
\t\t\t\t} </script></div>
EOT;
goto BqBzH; Es2kt: $nova[1] = formataDiaMes($tmp[0]) . '/' . $tmp[1]; goto sA2JI; EwWm0: goto qMZX7; goto LKLoj; G3xm0: $i = 0; goto v3HNS; SrN2D: $numColsInicial = 2; goto rVntN; fdAbh: $thClass = 'ui-widget-header ui-corner-all'; goto cSw2d; o6Giq: $r_exibir = $json['grafico']['tipo'] ? $json['grafico']['tipo'] : 'barra'; goto SBZsa; i7MVw: if ($totalTmp <= $curvaA) { goto M4N7M; } goto dBRPC; n0nt_: zGj7c: goto xIQDF; HHoz5: Gc92k: goto NRA42; yIfr1: $curvaABCTotal = array('A' => array(0, 0), 'B' => array(0, 0), 'C' => array(0, 0)); goto dueUC; yE_Zi: $array = array('fato' => $r_fato, 'fato2' => $r_fato2, 'dimensao1' => $r_dimensao1, 'dimensao1ord' => $r_dimensao1ord, 'dimensao2' => $r_dimensao2, 'dimensao2ord' => $r_dimensao2ord, 'medida' => $r_medida, 'incluirLinhaVazia' => $r_incluirLinhaVazia, 'incluirColunaVazia' => $r_incluirColunaVazia, 'mostrarCurvaABC' => $r_mostrarCurvaABC, 'ocultarColunasDashboard' => $r_ocultarColunasDashboard, 'tipodata' => $r_tipodata, 'exibir' => $r_exibir, 'incluirGraficoLabel' => $r_incluirGraficoLabel, 'incluirGraficoLegend' => $r_incluirGraficoLegend, 'numMetricas' => $r_numMetricas, 'ocultarSempreColunas' => $r_ocultarSempreColunas, 'ocultarPorcentagem' => $r_ocultarPorcentagem, 'ocultarTotais' => $r_ocultarTotais); goto NWKDD; TAUt3: M8qsU: goto WGShV; TZ0nG: $filtroGlobal = false; goto dwt33; Vsthf: $ocultarSempreColunas_ = ''; goto D7WYu; UaHke: YEbtu: goto gq4V8; Y3B9s: kfpXy: goto G4SV7; hciXe: fvWKO: goto svWZX; Qn9RG: $totalValor += $row[1]; goto fKXVS; GQTpU: foreach ($geradoDados as $key => $geradoDadosItem) { goto M7mwD; iB3QA: Lmf0b: goto ybHv7; OKt5P: FEc47: goto yxriS; yxriS: $geradoDados[$key][] = $cache[$geradoDadosItem[-1][0]]; goto c0SZe; c0SZe: U9dEA: goto iB3QA; M7mwD: if (isset($cache[$geradoDadosItem[-1][0]])) { goto FEc47; } goto aIakP; aIakP: $geradoDados[$key][] = 0; goto AikVX; AikVX: goto U9dEA; goto OKt5P; ybHv7: } goto AjvUp; b1Wsn: Migm0: goto VP4Bs; ser1B: $g['ignorarGerarSQLGrid'] = true; goto IAU3r; BTNIS: if (empty($json['filtros'])) { goto PqZs1; } goto XblQi; Yh9Xt: if ($r_mostrarCurvaABC != 'false') { goto JBsYI; } goto bMC0B; Zzv2p: $extra = preg_split('/GROUP BY/i', $extra); goto DbQZB; oGmE3: if ($result) { goto UU6pb; } goto iv2ys; wrJWT: hBWU3: goto n0jl9; zZQzW: goto TP2zj; goto Y64Eb; igtF3: $campo = "{$medida_}({$fato_}) AS Valor"; goto lLzPX; BjPwF: $result = mysql_query($sql); goto OL68O; DbQZB: $extra = $extra[0]; goto HgmTZ; C5vT6: $r_ocultarColunasDashboard = $json['grid']['ocultarColunasDashboard'] ? 'true' : 'false'; goto IXCEj; LCsZe: oW9g1: goto brLdd; E7nDn: wqipS: goto sN6wh; SqaC2: PbNzG: goto Bs7qM; Kesm8: x1Ea9: goto w48ab; lfTx2: $fato2_ = 'r_' . $fato2; goto tkIFq; vxoq_: $grid[$i]['classTH'] = "hideDashBoard"; goto PNQHo; vHhiO: if (!($r_mostrarCurvaABC != 'false')) { goto NVJGg; } goto jtNwj; Alwph: s4vGV: goto BlL8P; dueUC: $curvaABC = array(); goto NY3tI; R3vnN: if (!empty($ocultarPorcentagem_)) { goto s4vGV; } goto kIkSb; F_05q: RjPAX: goto VZDYI; pNGcs: $fato_ = 'r_' . $fato; goto SVZJP; Q27h3: $tmpKey = 0; goto xjDZk; hv1Rm: echo $g['ui']->gridFiltroPrint($gridFiltro, $md5); goto PDfRe; MiWPf: $r_dimensao1ord = isset($json['linha']['ordenacao']) ? $json['linha']['ordenacao'] : 'asc'; goto d0hVW; sn3mX: LvVG2: goto v5I12; xIQDF: goto oW9g1; goto lU3J5; pf5r2: if (!($row = mysql_fetch_array($result))) { goto RDpXc; } goto i7MVw; coPhl: foreach ($geradoDados as $key => $value) { goto V4oq0; yJraC: $geradoDados[$key][] = '-'; goto IaOZ4; goRom: Q9Eo5: goto vcp8h; FoPpE: if (isset($value[-1])) { goto LvnHH; } goto fq1Ys; jFgQ1: od6vg: goto R_z7p; XIkMl: $numCols = 3 + $i * $x; goto OYBIS; Bp3Td: if (!empty($ocultarPorcentagem_)) { goto od6vg; } goto qyQn2; TCrLC: goto Pyua8; goto Cmdgr; vcp8h: if (!empty($ocultarTotais_)) { goto rjI64; } goto yJraC; Cmdgr: XoUXV: goto FoPpE; R_z7p: WW0qw: goto QiZF3; N4bNX: aj5iT: goto NkDe8; V4oq0: if (!($key == 0)) { goto XoUXV; } goto TCrLC; OYBIS: $tmp = count($geradoDados[$key]); goto Zoc6K; IaOZ4: rjI64: goto Bp3Td; HVNFU: if (!($tmp < $numCols)) { goto Q9Eo5; } goto VqUvg; fq1Ys: goto Pyua8; goto MY7To; qyQn2: $geradoDados[$key][] = '-'; goto jFgQ1; NkDe8: $tmp++; goto H0nou; VqUvg: $geradoDados[$key][] = 0; goto N4bNX; H0nou: goto mk0Lm; goto goRom; QiZF3: Pyua8: goto Wpbgk; zkMu2: $x = empty($ocultarPorcentagem_) && empty($ocultarTotais_) ? 2 : 0; goto XIkMl; Zoc6K: mk0Lm: goto HVNFU; MY7To: LvnHH: goto zQqiz; zQqiz: if (!(count($keysAdicionados) > 0 && !isset($keysAdicionados[$value[-1][0]]))) { goto WW0qw; } goto zkMu2; Wpbgk: } goto Qm2hA; DaygQ: if (!($r_dimensao1 == 'DDDMS')) { goto m7Riy; } goto to1GE; y2tY1: $medida_ = 'r_' . $medida; goto wPI2X; Qj2EZ: $i++; goto MhXqA; kOs2H: hQhyl: goto w6MMg; NXSgu: log('6: ' . $sql); goto MRKCu; KwONT: $geradoDados = array(); goto f0Pc3; L7zvQ: if (empty($ocultarPorcentagem_) && empty($ocultarTotais_)) { goto H6nyY; } goto lDjfQ; Kurfj: $filtroAtual = file_get_contents($file); goto OiACN; ra3xz: $fato2_ = isset(${$fato2_}) ? ${$fato2_} : false; goto UfgmI; DMwMp: goto UFvtP; goto RCemY; fUMVq: $graficoSerialize = mysql_real_escape_string(serialize($grafico)); goto fCOmb; AWXc1: end($geradoDados); goto wZOHw; p_Iys: M4N7M: goto I_3Rl; TnZME: qaQ6r: goto Mn5W8; Zqbh3: $ocultarColunasDashboard_ = ''; goto y1KjO; wv5NJ: $g['smarty']->assign('thead', $gridTabela['thead']); goto LH5D7; YdwSr: goto lWp1B; goto GklBe; RzC84: $fato2 = 'fato2' . $id; goto q1JBr; TfOzx: dDqAw: goto FUePa; rb4Br: qKGYB: goto MuQRI; NY3tI: $curvaA = round($totalValor * 0.8, 2); goto J5_d6; fuMJm: $colunaTitulo = $rowDim2[0]; goto QCMql; UmgMz: if (!($rowDim2 = mysql_fetch_row($resultDim2))) { goto g9N1u; } goto fuMJm; Jg7L9: goto u1fvm; goto Ep3Xt; IiH5j: $curvaABC[$row[0]] = 'C'; goto wji2u; UraxL: goto Gc92k; goto g1bFa; ZRjGV: foreach ($grafico['colunaTotal'] as $key => $value) { goto kvJaa; hWbiB: $grafico['colunaColunas']['-'][] = array($value, $key); goto Bz96y; Bz96y: Vtfl_: goto K1Ozk; kvJaa: $grafico['colunas'][] = $key; goto hWbiB; K1Ozk: } goto PaFFb; crB6N: $array['tipodataa' . $i] = ${$var}; goto Kr8LJ; E9dLj: $var = 'r_fatoa' . $i; goto II3al; nGWgs: ZYZdb: goto o1zaq; svWZX: echo <<<EOT
\t           \t\t<li><a href="#" onclick="divSetarAcaoId('{$g['divId']}', 'comfiltro', 'novavisao'); divRefresh('{$g['divId']}');">Exibir Filtros</a></li>
\t           \t\t<li><a href="#" onclick="abrirJanela(false, divIdRootAberto(), '{$g['divId']}' , unique(), '', 'Visualizar Relatório', '/etc/etc011/content/etc011ContentPivotRelatorio/', '&acaoId=' + encodeURIComponent('') + '&D132_Id=' + encodeURIComponent('{$r_D132_Id}') + '&bi=conf&grid=true&tab=' + encodeURIComponent('{$r_tab}'), [1200,600]);">Configurar</a></li>
EOT;
goto YeQL0; LDawc: fygkE: goto dmnWB; I7AlS: goto M5sz8; goto JaE0j; IyJ2a: dy4Xa: goto vLj3R; L82YB: $geradoDadosItem[$nova[-1][0]] = key($geradoDados); goto BroP4; VfiQT: if ($resultDim2) { goto GP6Ps; } goto R6NDm; BlShz: $ocultarTotais_ = ''; goto pnyZT; HgmTZ: $sql = <<<EOT
            /* ClickHouse */
\t\t\tSELECT
\t\t\t\t{$r_dimensao2},
\t\t\t\t{$r_medida}({$r_fato}) AS Valor
\t\t\tFROM BIH001
\t\t\t{$extra}
\t\t\tGROUP BY
\t\t\t\t{$r_dimensao2}
\t\t\t{$extraVazioColuna}
\t\t\tORDER BY
\t\t\t\t{$r_dimensao2} {$r_dimensao2ord}
EOT;
goto QViL6; v3HNS: PRkai: goto u56SS; XCyL8: goto LUUj1; goto L7Gcj; SyG9a: R4JrS: goto ser1B; aWnKo: goto feVwQ; goto Q_2vB; SCj1Z: $output = ob_get_contents(); goto NrI3V; m5_OH: $i; goto Kesm8; QIWOd: x2o8c: goto uk3HY; xOKVn: VzkIA: goto L7zvQ; d96ih: log('8: ' . $sql); goto tqIzI; pTMCw: JsRVV: goto UTsso; hIPVw: if (!empty($ocultarTotais_)) { goto PLe0H; } goto i0sSW; jtNwj: $sql = <<<EOT
\t\t\t\t/* ClickHouse */
\t\t\t\tSELECT
\t\t\t\t\t{$r_dimensao1},
\t\t\t\t\t{$r_medida}({$r_fato}) AS Valor
\t\t\t\tFROM BIH001
\t\t\t\tGROUP BY
\t\t\t\t\t{$r_dimensao1}
\t\t\t\t{$extraVazioLinha}
\t\t\t\tORDER BY
\t\t\t\t\tValor DESC
EOT;
goto IZD3v; i0oU8: $medida_ = 'r_' . $medida; goto VMeWy; ncTHj: if (!empty($ocultarPorcentagem_) && !empty($ocultarTotais_)) { goto zoqqj; } goto FEAVn; gq4V8: goto xlojn; goto p_Iys; e2GM4: $array['fato2a' . $i] = ${$var}; goto C7MZR; PwW0k: ej3jx: goto bVTWb; SGkAR: $thStyle = ''; goto fdAbh; VZDYI: if (!($i != 0)) { goto OFg14; } goto coPhl; bLsri: $arrayCamposBI = serialize($array); goto fSwg7; DgeQP: if (!isset($r_novavisao)) { goto Migm0; } goto u5b_A; y1oPE: $multiplicador = $i; goto O192Y; KLCNW: $tipodata_ = isset(${$tipodata_}) ? ${$tipodata_} : false; goto O5m5E; UTsso: J4B7w: goto i_tkI; SKWzH: $json = apc_fetch("bi-pre-visualizar-{$r_biPreVisualizar}"); goto TY70l; LgEMd: if (!($rowDim2Ind = mysql_fetch_row($resultDim2Ind))) { goto Ko37H; } goto QjFQT; gRARc: die('<div style="margin: 4px;">Erro ao processar JSON: ' . json_last_error_msg() . '</div>'); goto nGWgs; ta358: $r_ocultarPorcentagem = $json['grid']['ocultarPorcentagem'] ? 'true' : 'false'; goto ShiY6; OmHOK: $id = $g['rOriginal'][3]; goto XSYaB; QjFQT: $cache[$rowDim2Ind[0]] = $rowDim2Ind[1]; goto DMwMp; LIJgl: LpcNm: goto A0pEn; EDyGM: goto baLvc; goto zhsHH; ZUzfO: $nova[] = $porcentagem; goto hcABF; yb4KE: if ($i === 1 && !$g['modoDesenvolvedor']) { goto FhHVB; } goto Icimi; eV3s7: $tmp = explode('/', $nova[0]); goto DQ7EE; kJDmG: echo <<<EOT
\t\t\t<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
\t\t\t\t//\$('#{$g['divId']} .gridOptTop').remove();
\t\t\t\t\$('#{$g['divId']}').data('grafico', '{$grafico}');
\t\t\t} </script></div>
EOT;
goto ieLXQ; aRwUm: if (!$debug) { goto s6O90; } goto NXSgu; LKLoj: FhHVB: goto NQCQY; fe2_x: goto FuPT4; goto oaMCY; fa01B: $r_dimensao2 = $json['coluna']['campo']; goto MiWPf; hdkyb: $i++; goto XCyL8; G2yUS: $incluirColunaVazia_ = isset($r_incluirColunaVazia) && $r_incluirColunaVazia != 'false' ? ' checked="checked"' : ''; goto DoXBX; VMeWy: $medida_ = isset(${$medida_}) ? ${$medida_} : false; goto bxBE_; d8O3D: LUUj1: goto GBn9J; XzZ9K: if (!($row = mysql_fetch_row($result))) { goto RjPAX; } goto xBzA4; LH5D7: $g['smarty']->assign('tbody', $gridTabela['tbody']); goto AoXn3; ZAe2m: $thClass = ''; goto uS0Ou; kkOKS: goto GvOTZ; goto G3Irp; g1bFa: qMZX7: goto Vv7Gp; w07Wx: jFVdn: goto ymUJa; PNQHo: zw7R3: goto YeRau; Icimi: die('Caminho personalizado não encontrado.'); goto EwWm0; k_hF7: HRx5E: goto PAN5x; oWVou: fNX1Y: goto M1hGi; PQUEA: CAK4b: goto pf5r2; B_WeY: $totalRegistros++; goto Qn9RG; ABO0P: pj6rc: goto QEkAH; thsGT: $geradoDados[$key][] = $porcentagem; goto bKEwp; ybyan: echo $stringCurvaABC; goto CrItq; GzuC8: goto sm1k8; goto mM3rR; zB2pX: oyaU6: goto UA2bG; EBaEj: UIMuK: goto psf1k; RCemY: Ko37H: goto GQTpU; XSYaB: if (!empty($r_biPreVisualizar)) { goto wRS1C; } goto L07O9; XueyR: $nova[] = $porcentagem; goto kOs2H; uw0lo: EaTEn: goto pYsNQ; CrItq: oITga: goto uQrfP; XdTPk: $grid[] = array('colunaValor' => "{$r_dimensao2}-{$colunaTituloSanitizado}-{$fato_}-{$medida_}", 'colunaTitulo' => $colunaTitulo, 'colunaTituloLegenda' => $colunaTitulo, 'colunaAlinhar' => 'right', 'extraTH' => 'colspan="1"', 'styleTH' => 'text-align: center;', 'callback' => 'biFormatarValor'); goto siMrb; RW29z: ob_end_clean(); goto KMDdP; lHhSt: echo <<<EOT
\t\t\t\t\t<div id="grafico{$g['divId']}" style="margin: 5px; width: 95%; height: 95%;"></div>
\t\t\t\t\t<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
\t\t\t\t\t\t// chama o gráfico
\t\t\t\t\t\tsetTimeout(function() {
\t\t\t\t\t\t\tg{$g['divId']}{$funcao}();
\t\t\t\t\t\t}, 100);
\t\t\t\t\t} </script></div>
EOT;
goto JgFgH; w8IhN: $i = 2; goto xOKVn; WA9BQ: @mysql_data_seek($result, 0); goto PQUEA; p1mP4: if ($result) { goto dLtil; } goto hqmtY; M1hGi: if (!($i < 3)) { goto E28Ko; } goto OmHOK; HTXFH: if (!$debug) { goto iiVVz; } goto ysrFf; d0hVW: $r_dimensao2ord = isset($json['coluna']['ordenacao']) ? $json['coluna']['ordenacao'] : 'asc'; goto DwH25; oo2oJ: WqPI3: goto vcEW4; SVZJP: $fato_ = isset(${$fato_}) ? ${$fato_} : false; goto QL_iG; xtY0P: a7OH0: goto Skxh2; Wm0gM: if (!($i == 0 && $r_mostrarCurvaABC != 'false')) { goto xiibs; } goto azTr0; t1RKG: echo <<<EOT
\t           \t\t<li><a href="#" onclick="divSetarAcaoId('{$g['divId']}', 'semfiltro', 'novavisao'); divRefresh('{$g['divId']}');">Ocultar Filtros</a></li>
\t           \t\t<li><a href="#" onclick="abrirJanela(false, divIdRootAberto(), '{$g['divId']}' , unique(), '', 'Visualizar Relatório', '/etc/etc011/content/etc011ContentPivotRelatorio/', '&acaoId=' + encodeURIComponent('') + '&D132_Id=' + encodeURIComponent('{$r_D132_Id}') + '&bi=conf&grid=true&tab=' + encodeURIComponent('{$r_tab}'), [1200,600]);">Configurar</a></li>
EOT;
goto I2l_E; mwY0a: $gridTabela = $g['ui']->grid($grid, $geradoDados); goto wv5NJ; v5I12: $extraVazioLinha = $r_incluirLinhaVazia == 'false' ? 'HAVING (Valor != 0)' : ''; goto oMFWY; vdxBb: goto Fs7UZ; goto QIWOd; LKi3a: $id = $i == 0 ? '' : 'a' . $i; goto RzC84; pYsNQ: $nova[0] = formataDiaSemana($nova[0]); goto pyKBz; J5_d6: $curvaB = round($curvaA + $totalValor * 0.1, 2); goto pKrcj; btaWb: my9FD: goto qQtuI; RurSI: echo <<<EOT
            \t\t<li><a href="#" onclick="acoesGrid(this, false, true, false, false); return false;" class="acao-grid-csv">Gerar CSV (Excel)</a></li>
EOT;
goto BA9rm; ymUJa: $i++; goto W6PS8; EZM0F: $incluirLinhaVazia_ = isset($r_incluirLinhaVazia) && $r_incluirLinhaVazia != 'false' ? ' checked="checked"' : ''; goto G2yUS; YKoRp: PqZs1: goto EZM0F; ZYiaU: $fato_ = isset(${$fato_}) ? ${$fato_} : false; goto KHV3v; AjvUp: ROx5A: goto Qf6pr; fCOmb: mysql_query("INSERT INTO D182 (D182_D132_Id, D182_C007_Id, D182_Grid_Salvo) VALUES('{$r_D132_Id}', '{$g['usuarioAtual']}', '{$graficoSerialize}') ON DUPLICATE KEY UPDATE D182_Grid_Salvo='{$graficoSerialize}'"); goto Jg7L9; q60IJ: if ($r_novavisao == 'semfiltro') { goto fvWKO; } goto t1RKG; pGizK: $i = 0; goto oWVou; FCj89: $incluirGraficoLabel_ = empty($incluirGraficoLabel_) ? 'false' : 'true'; goto vjH2i; nN3pm: $dir = $g['pathDadosUsuario'] . 'gridFiltro/'; goto q447k; FUePa: $geradoDados[] = $nova; goto YEZju; tr_E4: r79nk: goto PrYW9; QCMql: if (strpos($r_dimensao2, '_Dia_Da_Semana') != false) { goto boSBc; } goto trW9B; d8057: if (!($r_bi == 'visu' && isset($r_tab))) { goto R4JrS; } goto bLgnw; P55ov: PLe0H: goto wb82O; NQCQY: Personalizacoes::$rootDir = 'ide/'; goto UraxL; aKyBe: fbpMg: goto vEahp; u56SS: if (!($i < $r_numMetricas)) { goto wkk1j; } goto LKi3a; J2gbf: BtLWZ: goto R33sh; BsEpq: PMYOU: goto i3tGr; cySuJ: $tmp = 0; goto LDawc; WSkuC: $nova[0] = formataDiaMes($nova[0]); goto n0nt_; XrGbO: echo Botoes::processar($botoes); goto xhOHL; gdpEr: $extra = <<<EOT
-- WHERE
-- GROUP BY
GROUP BY {$r_dimensao1}
-- HAVING
{$extraVazioLinha}
-- ORDER BY
ORDER BY Valor {$r_dimensao1ord}
EOT;
goto o8_Vx; Q04ti: hJLkW: goto cmZwc; h9nET: $pivotD2Count = mysql_numrows($resultDim2); goto fVZwx; DUCWr: echo <<<EOT
\t           \t\t<li><a href="#" onclick="abrirJanela(false, divIdRootAberto(), '{$g['divId']}' , unique(), '', 'Visualizar Relatório', '/etc/etc011/content/etc011ContentPivotRelatorio/', '&acaoId=' + encodeURIComponent('') + '&D132_Id=' + encodeURIComponent('{$r_D132_Id}') + '&bi=visu&grid=true&tab=' + encodeURIComponent('{$r_tab}'), [1200,600]);">Visualizar como Grid</a></li>
EOT;
goto fOhDQ; cqCun: $grid[] = array('colunaValor' => "ABC", 'colunaTitulo' => 'ABC', 'colunaAlinhar' => 'center', 'style' => 'width: 1%;', 'callback' => 'biCurvaABC'); goto btaWb; ecb0x: $r_exibir = 'grid'; goto DUvQ9; ann9c: foreach ($geradoDados as $key_ => $geradoDadosItem) { $geradoDados[$key_][] = 0; F7Ywc: } goto l4f60; KHV3v: $tipodata = 'tipodata' . $id; goto iBcVl; vEuZM: iYNNb: goto EtGTl; NDFY_: $medida = 'medida' . $id; goto IpZoF; l0OpT: if (!($i < $r_numMetricas)) { goto WYZ1M; } goto L8cJA; lDjfQ: if (empty($ocultarPorcentagem_) || empty($ocultarTotais_)) { goto IO0Fw; } goto mMOVZ; yTxrg: $r_incluirGraficoLabel = isset($json['grid']['incluirGraficoLabel']) && !$json['grid']['incluirGraficoLabel'] ? 'false' : 'true'; goto i02op; iq3ir: $tipodata_ = isset(${$tipodata_}) ? ${$tipodata_} : false; goto DRzf0; diIrz: $r_clearFILTER = 'continuar'; goto pTMCw; K9VhH: $botoes = array(); goto XrGbO; R9g8u: $keysAdicionados[$row[0]] = true; goto THBbz; LmLJ1: echo <<<EOT
        </ul>
    </div>
</div>
<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
\t\$('#{$g['divId']} .dropdown-toggle').dropdown();
} </script></div>
EOT;
goto b1Wsn; NZ7nD: $curvaABCTotal[$curvaABC[$row[0]]][0] += 1; goto taXHO; KwEuG: $i = 0; goto oo2oJ; jHyCJ: gF9Ds: goto dzsiw; oaMCY: zoqqj: goto Rys2F; EVb6n: $fato2 = 'fato2' . $id; goto lfTx2; uvhl2: wkk1j: goto QXC0_; GTaUv: gLVZT: goto YKoRp; OZKvp: kwVA0: goto o6Giq; mz2j4: goto PRkai; goto uvhl2; pHvw5: qQ1Ml: goto hIPVw; trW9B: if (strpos($r_dimensao2, 'CONCAT') !== false) { goto B_TAI; } goto XK0xH; t_fAI: die("<div style='margin: 4px;'>json->tipo inválido: {$json['tipo']}</div>"); goto IHPkq; Oczt_: $tipodata = 'tipodata' . $id; goto CvvBj; pyPxh: if (!($tmp < $numCols)) { goto M9Vi2; } goto ifct8; SBZsa: KSWcI: goto p0myB; Q_2vB: Tsxxo: goto d8057; nX6Zu: u1fvm: goto a19HU; CvvBj: $tipodata_ = 'r_' . $tipodata; goto iq3ir; JjqtH: log('2: ' . $sql); goto AgNG3; ILben: N5yKR: goto GzuC8; lLzPX: $sql = <<<EOT
                    /* ClickHouse */
                    SELECT
                        {$r_dimensao1},
                        {$campo}
                    FROM BIH001
                    {$extra}
EOT;
goto WdxQi; NWKDD: $i = 1; goto d8O3D; l4f60: d2381: goto GDJUi; GZxrs: if (!($row = mysql_fetch_array($result))) { goto TKMZ4; } goto B_WeY; xhOHL: if (!(!empty($gridFiltro) && !$r_pularFiltro)) { goto tVetf; } goto r1fPB; fVZwx: OXKIj: goto UmgMz; q447k: $file = $dir . $md5; goto jou5y; pmvAb: $ocultarPorcentagem_ = $r_ocultarPorcentagem != 'false' ? ' checked="checked"' : ''; goto wrJWT; zlGdv: $totalTmp += $row[1]; goto KiT_T; QViL6: $resultDim2 = mysql_query($sql); goto aRwUm; JgFgH: goto QK4Z0; goto UXX_N; w48ab: if (!($i <= $countGrid)) { goto hJLkW; } goto vxoq_; Q7siH: xx1rv: goto YEniT; Yo3I2: hTUhL: goto ecb0x; wAwiA: H6nyY: goto mREBA; CSZbB: goto WqPI3; goto JyM5y; dmnWB: if (!($tmp < $multiplicador)) { goto ej3jx; } goto TpAy7; ta5eb: goto Gc92k; goto SqaC2; xBzA4: $totalPorLinha = $row[1]; goto R3vnN; Jomm_: if (!($r_mostrarCurvaABC != 'false')) { goto VzkIA; } goto w8IhN; z9Qqx: $colunaTitulo = formataDiaSemana($colunaTitulo); goto ksMOK; Fe8lj: wRS1C: goto xehNZ; e9w4b: $fato = 'fato' . $id; goto SPflP; YeQL0: U8Jc9: goto KYnzH; MuQRI: if (isset($r_ocultarPorcentagem)) { goto K83jd; } goto K7XFa; DRzf0: $medida = 'medida' . $id; goto IiFGI; o1zaq: if ($r_gridFiltrado) { goto JsRVV; } goto diIrz; ukthm: if (isset($r_generateHTML)) { goto pdkwW; } goto XKedh; xqqpd: $keysAdicionados = array(); goto g93IY; nW747: goto UTXa1; goto FqRVP; Eno70: $mostrarCurvaABC_ = $r_mostrarCurvaABC != 'false' ? ' checked="checked"' : ''; goto B4FBy; MhXqA: goto aIlT4; goto jHyCJ; PaFFb: O_Off: goto k_hF7; VA7hX: if (isset($r_mostrarCurvaABC)) { goto CWE0H; } goto LbwFt; rTF_a: TKMZ4: goto yIfr1; xehNZ: echo <<<EOT
\t\t\t<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                // Para corrigir um bug onde o editor "se movimenta" ao atualizar a tela
                setTimeout(function() {
                    \$('.formEditor-CodigoJsonBI').css('left', '16px');
                }, 10);                
\t\t\t} </script></div>
EOT;
goto SKWzH; GikaV: if (!($r_dimensao2 == 'DDDMS')) { goto yKXu6; } goto jVT_P; kIkSb: $porcentagem = round($totalPorLinha * 100 / $dim1Total[$i], 2); goto Alwph; mHD2T: $curvaABCTotal['B'][0] = number_format($curvaABCTotal['B'][0], 0, '', '.'); goto W4p6P; tF8jK: $totalValor = 0; goto hzaW2; xu6px: UEIj9: goto DUCWr; rux4z: $countGrid = count($grid); goto kcJ01; jFT2T: Fs7UZ: goto EDyGM; WPicg: $numCols = $numColsInicial + $i * $numColsPorFato; goto QeFPU; QFdo8: GP6Ps: goto h9nET; IpZoF: $medida_ = 'r_' . $medida; goto pQZY_; QEkAH: $nova[] = $row[0]; goto VkARD; PrYW9: $i++; goto CSZbB; G4SV7: $gridFiltro = array(); goto BTNIS; VP4Bs: if ($r_exibir == 'grid') { goto Tsxxo; } goto FCj89; MxgaX: if (strpos($r_dimensao1, 'CONCAT') !== false) { goto cSbdc; } goto Y9W16; cmZwc: zFuj0: goto DgeQP; uS0Ou: qXJYR: goto gdpEr; B4FBy: GvOTZ: goto qH0GK; Yt38f: $r_incluirColunaVazia = $json['grid']['incluirColunaVazia'] ? 'true' : 'false'; goto MQgfz; Luu2q: hiuAr: goto hm9aV; A0pEn: if (mysql_num_rows($resultDim2Ind) == 0) { goto wKnJR; } goto gbEdX; qF7KA: $grid[] = array('colunaValor' => "{$medida_}({$fato_})-porcentagem", 'colunaTitulo' => '%', 'colunaAlinhar' => 'right', 'style' => 'width: 1%;', 'extraTH' => 'colspan="1"', 'styleTH' => 'text-align: center;', 'callback' => 'biPorcentagem'); goto NbM3C; vcEW4: if (!($i < $r_numMetricas)) { goto VzSuP; } goto EwbTV; mREBA: $i = $i + 2 * $r_numMetricas; goto aQAug; k3UN8: if ($r_bi == 'visu') { goto y2rRq; } goto lHhSt; pyKBz: t4wFY: goto hl9qZ; RhC81: SVzFW: goto TAUt3; vEFGh: $fato2_ = 'r_' . $fato2; goto m7PYD; hzaW2: VYWZb: goto GZxrs; TpAy7: $nova[] = 0; goto daX0R; mM3rR: Qk4Iv: goto vLwf4; YkMC2: goto QevRd; goto wAwiA; BroP4: goto M8qsU; goto Luu2q; mpph0: $countGrid = $countGrid - 1; goto m5_OH; NbM3C: l7RRe: goto w07Wx; pjAIE: goto hBWU3; goto UHoKT; n0jl9: if (isset($r_ocultarTotais)) { goto Rn0YY; } goto BlShz; o8_Vx: $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra, $md5, false, false, true, false, false, $r_tab ? md5("bi-{$r_tab}") : false, false, $filtroGlobal); goto vEB16; lU3J5: kQ5CB: goto eV3s7; JqrT4: NFecj: goto Ua0Rz; t83VI: $tmp++; goto i1w1u; iLi_a: sm1k8: goto GZuCB; wji2u: goto YEbtu; goto Q7siH; hcABF: NdWRs: goto Yh9Xt; R6NDm: die('Invalid query: ' . mysql_error() . "\n<br/>SQL: " . $sql); goto QFdo8; aC3Mw: $tipodata_ = 'r_' . $tipodata; goto uvBcW; AtGjL: mgWGf: goto l0OpT; rhjbm: tVetf: goto yE_Zi; uU39O: goto BtLWZ; goto GULUQ; mPDKT: $i = 0; goto QJRBI; wb82O: if (!($r_mostrarCurvaABC != 'false')) { goto a05pR; } goto nZ6qe; Ep3Xt: XUm4_: goto e_rGy; lmLUE: $id = $i == 0 ? '' : 'a' . $i; goto XJi75; eapjN: g9N1u: goto Pb0qs; EtGTl: $ocultarColunasDashboard_ = $r_ocultarColunasDashboard != 'false' ? ' checked="checked"' : ''; goto zB2pX; XZYYp: goto dDqAw; goto gueSe; JNT2H: $sql = <<<EOT
                /* ClickHouse */
\t\t\t\tSELECT
\t\t\t\t    {$medida_}({$fato_}) AS Valor
\t\t\t\tFROM BIH001
\t\t\t\t{$extra}
EOT;
goto BjPwF; Y9W16: if (!(strpos($r_dimensao1, '_Mes') != false)) { goto UIMuK; } goto S6Uql; dzsiw: goto OXKIj; goto eapjN; KG9a_: M5sz8: goto pyPxh; xjDZk: foreach ($geradoDados as $key => $value) { goto BCqTH; uF21c: fuIYw: goto TORD_; mCIzu: $grafico['colunaTotal'][$tmpKey] = empty($colunaTotal) ? 0 : str_replace("'", '', $colunaTotal); goto PkG51; sF7_1: if (!($key == 0)) { goto s6o71; } goto c2Y1l; PkG51: foreach ($value as $colunaKey => $colunaValue) { goto Bummx; W3tUa: goto vzFT5; goto NsXn1; ACJ23: $grafico['colunaColunas']["{$tmpKey} "][] = array(empty($coluna) ? 0 : str_replace("'", '', $coluna), str_replace("'", '', strip_tags($value[0])) . '/' . @$grid[$colunaKey]['colunaTituloLegenda']); goto I9Osw; NsXn1: klIXV: goto xfvz4; xfvz4: $coluna = gCorrigeNumeroInverte(strip_tags($colunaValue)); goto ACJ23; Bummx: if (!($colunaKey < 3)) { goto klIXV; } goto W3tUa; I9Osw: vzFT5: goto dUwqY; dUwqY: } goto TJEcj; xs4Xg: $tmpKey++; goto uF21c; c2Y1l: goto fuIYw; goto U1qRX; TJEcj: Er4MR: goto xs4Xg; U1qRX: s6o71: goto ZXTF9; scclm: $colunaTotal = gCorrigeNumeroInverte(strip_tags($value[1])); goto mCIzu; dnsiT: CxgfE: goto scclm; PEEbr: $grafico['nrColunas'] = count($value) - 4; goto dnsiT; BCqTH: $tmpKey = str_replace("'", '', strip_tags($value[0])); goto sF7_1; ZXTF9: if (!($grafico['nrColunas'] == 0)) { goto CxgfE; } goto PEEbr; TORD_: } goto DomVB; IAU3r: list($geradoSql_, $geradoDados_) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra); goto mwY0a; I2l_E: goto U8Jc9; goto hciXe; s3Y0i: $i = $i + 1 * $r_numMetricas; goto BJBWA; Eu8kE: slmXv: goto xNaxV; vEB16: $grid = array(); goto SYWFW; D_LWk: OFg14: goto tr_E4; Mn5W8: $i++; goto mz2j4; ATL13: WUM_S: goto YMFVB; WGShV: goto cAshG; goto F_05q; fdu1p: $totalRegistros = mysql_numrows($result); goto tF8jK; A5H02: feVwQ: goto SCj1Z; SrOY1: goto x1Ea9; goto Q04ti; Kr8LJ: $var = 'r_medidaa' . $i; goto LJuOP; sN6wh: if (!empty($ocultarPorcentagem_)) { goto HwkKz; } goto thsGT; Rys2F: $multiplicador = 0; goto gXICt; SdJFG: $colunaTitulo = formataDiaMes($tmp[0]) . '/' . $tmp[1]; goto wD3kr; MRKCu: s6O90: goto VfiQT; G3Irp: CWE0H: goto Eno70; F8kLm: $sql = <<<EOT
                    /* ClickHouse */
\t\t\t\t\tSELECT
\t\t\t\t\t\t{$r_dimensao1},
\t\t\t\t\t    {$medida_}({$fato_}) AS Valor
\t\t\t\t\tFROM BIH001
\t\t\t\t\t{$extra}
\t\t\t\t\tAND ({$r_dimensao2} = '{$key}')
\t\t\t\t\tGROUP BY
\t\t\t\t\t    {$r_dimensao1}
\t\t\t\t\tORDER BY
\t\t\t\t\t\t{$r_dimensao1} ASC
EOT;
goto c27kD; J43ao: if (!($i < $r_numMetricas)) { goto gF9Ds; } goto lmLUE; a3vwz: echo <<<EOT
\t\t\t\t\t<div id="grafico{$g['divId']}" style="margin: 5px; width: 100%; height: 100%;"></div>
\t\t\t\t\t<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
\t\t\t\t\t\t// corrige o tamanho do DIV para ocupar tudo
\t\t\t\t\t\t\$('#grafico{$g['divId']}')
\t\t\t\t\t\t\t.width(\$('#{$g['divId']}').width() - 20)
\t\t\t\t\t\t\t.height(\$('#{$g['divId']}').height() - 40);
\t\t\t\t\t\t// chama o gráfico
\t\t\t\t\t\tsetTimeout(function() {
\t\t\t\t\t\t\tg{$g['divId']}{$funcao}();
\t\t\t\t\t\t}, 100);
\t\t\t\t\t} </script></div>
EOT;
goto CVoxU; KNaYx: Personalizacoes::$rootDir = Apps::getDirAtivos(); goto ta5eb; siMrb: $key = mysql_real_escape_string($rowDim2[0]); goto F8kLm; ojCy1: CoTNb: goto XZYYp; ICO8p: if (!empty($ocultarTotais_)) { goto wqipS; } goto Q0J3a; n0aEV: $grafico['colunaColunas']['-'] = array(); goto ZRjGV; nlF9V: if (isset($geradoDadosItem[$row[0]])) { goto WUM_S; } goto DaygQ; rCylT: if (!(isset($r_D132_Id) && $r_D132_Id > 0 && $r_Filtro_D132_Id != 1 && $r_bi == 'visu')) { goto Wo_Dy; } goto nN3pm; pKrcj: $totalTmp = 0; goto WA9BQ; p7GxB: AcmGs: goto TfOzx; BA9rm: goto PSzVm; goto xu6px; i1w1u: goto fygkE; goto PwW0k; bUAw5: $nova[0] = formataDiaSemana($nova[0]); goto ojCy1; og6xQ: $array['fatoa' . $i] = ${$var}; goto DREIU; GBn9J: if (!($i < 10)) { goto djaus; } goto E9dLj; GqRRX: $ocultarTotais_ = $r_ocultarTotais != 'false' ? ' checked="checked"' : ''; goto Eu8kE; LbwFt: $mostrarCurvaABC_ = ''; goto kkOKS; GZuCB: goto CoTNb; goto lEuuD; Vv7Gp: goto PbNzG; goto KI8ss; dwt33: if (empty($json['tipo']) || $json['tipo'] == 'grid') { goto hTUhL; } goto peVk2; C_fWP: $nova[] = $totalPorLinha; goto xtY0P; THBbz: if (!($r_dimensao1 == 'DDDMS')) { goto pj6rc; } goto vHdrX; u5b_A: echo <<<EOT
<div class="gridOptTop {$class}" style="margin-top: -25px; position: absolute; right: 4px;">
    <div class="btn-group">
        <button class="btn btn-mini" onclick="gridRefresh(this); return false;">Atualizar</button>
        <button data-toggle="dropdown" class="btn btn-mini dropdown-toggle" onmouseover="if (!\$(this).closest('div').hasClass('open')) { \$(this).trigger('click'); } "><span class="caret"></span></button>
        <ul class="dropdown-menu pull-left" style="margin-left: -80px; margin-top: -5px;">
EOT;
goto q60IJ; LJuOP: $array['medidaa' . $i] = ${$var}; goto aylpO; ieLXQ: if (!(!empty($ocultarSempreColunas_) || !empty($ocultarColunasDashboard_) && $r_bi == 'visu' && (!isset($r_grid) || empty($r_grid)))) { goto zFuj0; } goto rux4z; GDJUi: if (!$debug) { goto vu2Jd; } goto cQe06; gXICt: FuPT4: goto cySuJ; Witjc: yKXu6: goto vdxBb; W6PS8: goto mgWGf; goto DZ_J9; ifct8: $geradoDados[$key][] = 0; goto PMu2o; DUvQ9: lkcHp: goto D5fcC; ysrFf: log('1: ' . $sql); goto haqHa; k8LVf: if (!($r_mostrarCurvaABC != 'false')) { goto my9FD; } goto cqCun; Pb0qs: $count = 0; goto gB2tR; G8gow: $g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT)); goto ukthm; g281Y: $curvaABCTotal['A'][0] = number_format($curvaABCTotal['A'][0], 0, '', '.'); goto QnERF; GW010: $nova[] = $totalPorLinha; goto JqrT4; DomVB: Tp4CP: goto pFJRN; cSw2d: if (!isset($r_generateHTML)) { goto qXJYR; } goto G0NsX; GTVnk: NVJGg: goto vtDSI; XJi75: $fato2 = 'fato2' . $id; goto rK0a7; y1KjO: goto oyaU6; goto vEuZM; KI8ss: m7gPa: goto KNaYx; tO7Dn: $colunaTituloSanitizado = trim(preg_replace('/[^a-z0-9]+/i', '-', $colunaTitulo), '-'); goto XdTPk; BlL8P: $nova = array(); goto p9S5P; SYWFW: $g['ignorarGerarSQLGrid'] = true; goto kG2P4; bMC0B: if (strpos($r_dimensao1, '_Dia_Da_Semana') != false) { goto YHzAR; } goto bO1qI; DZ_J9: WYZ1M: goto vHhiO; NRA42: $i++; goto tHNwm; uvBcW: $tipodata_ = isset(${$tipodata_}) ? ${$tipodata_} : false; goto NDFY_; w8MNM: RDpXc: goto GTVnk; pQZY_: $medida_ = isset(${$medida_}) ? ${$medida_} : false; goto tO7Dn; c27kD: $resultDim2Ind = mysql_query($sql); goto Mo2IL; zRxf1: vu2Jd: goto J2gbf; rVntN: $numColsPorFato = 0; goto VMgeZ; vjH2i: $incluirGraficoLegend_ = empty($incluirGraficoLegend_) ? 'false' : 'true'; goto mNynj; zhsHH: B_TAI: goto z7MSR; s7IrT: $file = $dir . $md5; goto Kurfj; L7Gcj: djaus: goto bLsri; azTr0: $nova[] = $curvaABC[$row[0]]; goto jMOyz; C2DKY: $nova[0] = formataDiaMes($nova[0]); goto ILben; BreR3: $fato_ = isset(${$fato_}) ? ${$fato_} : false; goto Oczt_; DmUgH: XWh3c: goto ako4Z; MQgfz: $r_mostrarCurvaABC = $json['grid']['mostrarCurvaABC'] ? 'true' : 'false'; goto C5vT6; BJBWA: PscfW: goto YkMC2; IXCEj: $r_ocultarSempreColunas = $json['grid']['ocultarSempreColunas'] ? 'true' : 'false'; goto ta358; QSxxq: if ($i != 0) { goto hiuAr; } goto R9g8u; w6MMg: if (strpos($r_dimensao1, '_Dia_Da_Semana') !== false) { goto EaTEn; } goto R51Tz; lqR88: $grid[] = array('colunaValor' => "{$medida_}({$fato_})", 'colunaTitulo' => $estrutura[$fato_]['nome'], 'colunaAlinhar' => 'right', 'style' => 'width: 1%; font-weight: bold;', 'extraTH' => 'colspan="1"', 'styleTH' => 'text-align: center;', 'callback' => 'biFormatarValor'); goto X8V54; YMFVB: $key = $geradoDadosItem[$row[0]]; goto SrN2D; FqRVP: } wnERm: UTXa1: function formataDiaSemana($valor) { goto wWAuD; mQhjO: return $valor; goto av24e; wWAuD: switch ($valor) { case 1: $valor = '1 - Segunda-feira'; goto IlD5Q; case 2: $valor = '2 - Terça-feira'; goto IlD5Q; case 3: $valor = '3 - Quarta-feira'; goto IlD5Q; case 4: $valor = '4 - Quinta-feira'; goto IlD5Q; case 5: $valor = '5 - Sexta-feira'; goto IlD5Q; case 6: $valor = '6 - Sábado'; goto IlD5Q; case 7: $valor = '7 - Domingo'; goto IlD5Q; } goto W0ZbJ; IsrRQ: IlD5Q: goto mQhjO; W0ZbJ: geqJI: goto IsrRQ; av24e: }