<?php
 namespace hardness; class Formulario { public $formNome = 'form_padrao'; public $formArray = array(); public $formArrayValues = array(); private $formTabId = array(); public $acaoId = false; private $formEditorCache = array(); public $tabela; public $formArrayOriginal = array(); public $formArrayValuesOriginal = array(); public $formArrayTodosCampos = array(); function __construct($formNome = '') { goto WNzts; m_XVi: $this->acaoId = $r_acaoId; goto PZs_N; PZs_N: $this->formNome = !empty($formNome) ? $formNome : $this->formNome; goto VxNvK; WNzts: global $r_acaoId; goto m_XVi; VxNvK: } public function definirFormularioArray($formArray) { goto jFl0M; nFXvc: lci2Y: goto ihSOa; YGZ0U: $arquivos[] = $arquivoApps; goto kwv1B; TNLd0: dEUZv: goto ecvQ7; nUYVx: myFyz: goto aqrhI; gU1Oy: SXacc: goto ezOPJ; TiRb_: Vrr1E: goto UHIQG; xCHow: $arquivoApps = Apps::getDirAtivos() . "personalizacaoForm/" . str_replace('/', '-', trim(@$g['r_'], '/')) . '-novoCampo'; goto pStzH; k2uOI: apc_store("form-php-{$formId}", serialize($formArray), 3600); goto OyuqQ; ihYVW: if (!(empty($g['nivelAlcada']) && $g['usuarioAtual'] == '63')) { goto myFyz; } goto TEnIz; U70ej: $arquivos[] = $r_pegarForm ? null : $arquivoGlobal; goto uuTUD; aqrhI: foreach ($this->formArray['campos'] as $tab => &$tabCampos) { goto Ka10C; QQ6w_: GdsRZ: goto lHd3N; tVcrj: yZvxz: goto QQ6w_; Ka10C: foreach ($tabCampos as $id => &$item) { goto T4cSC; pD01A: $item['readonly'] = true; goto ei0Ya; T4cSC: $nivel = 1; goto xiUug; chqPO: if (!($nivel > $g['nivelAlcada'])) { goto VuhGX; } goto jOeig; hOexT: WmRlb: goto iuhDl; ei0Ya: Wf1eY: goto MaBvo; MaBvo: VuhGX: goto hOexT; Mfksu: $item['disabled'] = true; goto B4ztW; ygI6l: o2FvK: goto pD01A; B4ztW: goto Wf1eY; goto ygI6l; UB5fb: NS9bq: goto chqPO; xiUug: if (!isset($item['nivel'])) { goto NS9bq; } goto detpY; jOeig: if ($key = 'C007_Email_Assinatura_') { goto o2FvK; } goto Mfksu; detpY: $nivel = $item['nivel']; goto UB5fb; iuhDl: } goto tVcrj; lHd3N: } goto xmle2; ihSOa: $arquivos = array_filter($arquivos); goto XQNg8; E3jie: if ($g['modoDesenvolvedorApp']) { goto Vrr1E; } goto Jscbq; TNAeN: S5Vb0: goto F3hx8; eFYH2: if ($r_pegarForm) { goto b0Icg; } goto NWEgn; CSzow: qQ9Mb: goto MHpD2; TZIHq: if ($g['modoDesenvolvedor']) { goto mNoO0; } goto OBJuE; jFl0M: global $g, $r_pegarForm; goto SzyGv; tPG__: $this->formArrayValuesOriginal = $this->formArrayValues; goto kYfn6; PHS6n: EUT4R: goto ihYVW; F3hx8: goto l6ci2; goto fADnR; ecvQ7: foreach ($this->formArray['campos'] as $tab => &$tabCampos) { goto ZCvho; ZCvho: foreach ($tabCampos as $id => &$item) { goto iNbhV; GQwqI: if (!($g['modoDesenvolvedorApp'] && $arquivoAppsEditor && file_exists($arquivoAppsEditor))) { goto VgwSr; } goto JawK9; EP0gG: if (!($g['modoDesenvolvedorApp'] && file_exists($arquivoAppsEditor))) { goto vhlwY; } goto JE2lM; ib8NA: if (isset($item['type'])) { goto mbBbT; } goto KEhvF; nwBr_: VNEdh: goto TZ0eo; YHxlw: $arquivoApps = Apps::getDirAtivos() . 'personalizacaoForm/' . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $id . '-callbackPreencherValor'; goto WKgtR; W6ZEB: QT6Py: goto Yvpuu; Li5yD: $arquivoAppsEditor = isset($r_pegarForm) && ($g['modoDesenvolvedor'] || $g['modoDesenvolvedorApp']) ? null : Apps::getDirEditor() . 'personalizacaoForm/' . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $id . '-callbackAoAlterar'; goto kix0l; s7Bs9: $item['callbackRestricao'] = file_get_contents($arquivo); goto zNA_k; fxXba: XuNSj: goto ib8NA; IHiUx: k8HQF: goto fxXba; rLAqc: if (!($g['modoDesenvolvedorApp'] && file_exists($arquivoAppsEditor))) { goto XAke4; } goto TegrV; tSayF: VgwSr: goto jOtrD; EpnNL: $carregarParametros['todosCampos'] = $_REQUEST[$this->formNome]; goto wyzrf; znuO1: $arquivoApps = isset($r_pegarForm) && $g['modoDesenvolvedor'] ? null : Apps::getDirAtivos() . 'personalizacaoForm/' . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $id . '-callbackAoAlterar'; goto Li5yD; fkmsd: switch ($type[0]) { case 'numero': goto M9E72; ZeR3o: goto k8HQF; goto Vhw36; NuWTC: A9Bfg: goto ZeR3o; M9E72: $item['type'] = 'moeda'; goto ckRZk; ckRZk: if (isset($item['casasDecimais'])) { goto A9Bfg; } goto dKNW1; dKNW1: $item['casasDecimais'] = !isset($type[1]) ? 2 : $type[1]; goto NuWTC; Vhw36: case 'data': $item['type'] = "data"; goto k8HQF; case 'dataHora': case 'datetime': $item['type'] = "dataHora"; goto k8HQF; case 'tempo': $item['type'] = "tempo"; goto k8HQF; case 'cnpj': $item['type'] = "cnpj"; goto k8HQF; case 'cpf': $item['type'] = "cpf"; goto k8HQF; case 'cep': $item['type'] = "cep"; goto k8HQF; case 'texto': goto MhzR2; MhzR2: foreach ($type['form'] as $key => $value) { $item[$key] = $value; TUTsK: } goto wDnEx; wDnEx: J9tJj: goto bjc_4; bjc_4: goto k8HQF; goto RneY0; RneY0: } goto vqIqi; fOgT5: hkvxP: goto P76JM; RGOov: $arquivoIde = 'ide/personalizacaoForm/' . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $id . '-callbackPreencherSelect'; goto t_pUs; uYCHL: $valorSelect = $item['value']; goto wBjh2; Wx9kV: if (!(strlen($valorSelect) > 0)) { goto OVkhQ; } goto mmk0S; Las0_: $arquivoAppsEditor = isset($r_pegarForm) && ($g['modoDesenvolvedor'] || $g['modoDesenvolvedorApp']) ? null : Apps::getDirEditor() . 'personalizacaoForm/' . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $id . '-callbackAoSair'; goto H1fMa; CDKqt: $arquivo = $arquivoIde; goto iGHah; M8MpV: if (!$type) { goto XuNSj; } goto fkmsd; X14i4: $arquivo = $arquivoGlobal; goto TmKWW; TegrV: $arquivo = $arquivoAppsEditor; goto yOVnh; qhE2d: $item['value'] = $valorSelect; goto WFbiT; vLB1l: ww83R: goto FXlKl; b7Phz: $arquivo = $arquivoApps; goto p9QHb; YrTA2: tVxBp: goto HX0oo; i5uH3: $carregarParametros[$this->formNome] = false; goto ozZ47; VlFHj: if (!empty($carregarParametros['todosCampos'])) { goto gF92y; } goto b_6M4; QlLHT: $arquivo = $arquivoGlobal; goto LqRPi; ozZ47: gF92y: goto wyJso; e5fVr: if ($item['type'] == 'pesquisa') { goto nCtph; } goto SNgwr; v8VqY: i_8M7: goto bjQrH; lUFCq: $campoPadraoPesquisa = mysql_fetch_array($campoPadraoPesquisa); goto F1gtq; PkPtu: QQqT9: goto u8_DB; LVJ2n: $this->formTabId[$id] = $tab; goto e9tby; F1gtq: $item['value_Pesquisa'] = implode(' ', is_array($campoPadraoPesquisa) ? $campoPadraoPesquisa : array()); goto idnHN; KnY8l: if (!file_exists($arquivo)) { goto ABEhI; } goto Hw1NC; YAcsW: jH0px: goto G8Qx6; Pkj3i: $arquivo = $arquivoGlobal; goto v8VqY; CczyY: $arquivo = $arquivoApps; goto Q5Jbl; G8Qx6: $this->formArrayValues[$id] = $valorSelect; goto Wx9kV; zqKlk: $arquivoGlobal = "{$g['pathDados']}personalizacaoForm/" . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $id . '-callbackPreencherValor'; goto C0d7j; tj9oV: $arquivo = $arquivoGlobal; goto Ftv2w; LqRPi: UeT3D: goto KnY8l; DUJmQ: $arquivo = $arquivoAppsEditor; goto wYl_Z; JT3CE: $carregarParametros = array('campoAtual' => $item['value'], 'todosCampos' => $this->formArrayTodosCampos); goto VlFHj; TmKWW: auOF1: goto wYe9t; PNpz3: if (!(!$g['modoDesenvolvedor'] && $arquivoApps && file_exists($arquivoApps))) { goto KgAIg; } goto CczyY; iGHah: if (!(!$g['modoDesenvolvedor'] && $arquivoApps && file_exists($arquivoApps))) { goto a1r31; } goto y_aB3; aW_NK: if (!(!$g['modoDesenvolvedor'] && !$g['modoDesenvolvedorApp'] && $arquivoGlobal && file_exists($arquivoGlobal))) { goto i_8M7; } goto Pkj3i; imWA2: $arquivoGlobal = "{$g['pathDados']}personalizacaoForm/" . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $id . '-codigoFonte'; goto Gtw0l; J6lI3: if (!($g['modoDesenvolvedorApp'] && file_exists($arquivoAppsEditor))) { goto h5MjH; } goto DUJmQ; AG91d: $carregarParametros = array('todosCampos' => $this->formArrayTodosCampos); goto cDbfd; n1g0l: eval("namespace hardness;\n" . str_replace('<?php', '', file_get_contents($arquivo))); goto k3NJI; wLd7E: nCtph: goto LzXLH; kjEAJ: $arquivo = $arquivoApps; goto KoAcy; Q5Jbl: KgAIg: goto BxCEt; PC086: sWGAc: goto W8CEg; mmk0S: foreach ($item['select'] as &$itemSelect) { goto yVlLv; TuheZ: $itemSelect['selected'] = false; goto L5wVK; L5wVK: goto RWnZm; goto JN7jU; SyKUp: $itemSelect['selected'] = true; goto OJCPA; yVlLv: if ($itemSelect['value'] == $valorSelect) { goto Ot_8h; } goto TuheZ; JN7jU: Ot_8h: goto SyKUp; JTWrq: edoz5: goto V92uc; OJCPA: RWnZm: goto JTWrq; V92uc: } goto nwBr_; H9MVM: OVkhQ: goto W6ZEB; nV_CV: $arquivo = $arquivoGlobal; goto zYRbe; LzXLH: $valorSelect = $g['conf']->camposValorPadrao($id); goto pvt9E; BxCEt: if (!($g['modoDesenvolvedorApp'] && $arquivoAppsEditor && file_exists($arquivoAppsEditor))) { goto KqL5E; } goto bAXYG; XIyMw: if (!($item['type'] == 'select')) { goto n5q33; } goto RGOov; b_6M4: $carregarParametros['todosCampos'] = $_REQUEST[$this->formNome]; goto i5uH3; kix0l: $arquivoGlobal = isset($r_pegarForm) ? null : "{$g['pathDados']}personalizacaoForm/" . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $id . '-callbackAoAlterar'; goto Lv7qC; bAXYG: $arquivo = $arquivoAppsEditor; goto houka; v70zF: if ($g['conf']->camposNaoUsar($id)) { goto hxwQ2; } goto R4PeQ; cwCat: $sql = "select {$item['pesquisa'][2]} from {$item['pesquisa'][0]} {$item['pesquisa'][3]} where {$item['pesquisa'][1]} = '{$valorSelect}'"; goto HrfVJ; pvt9E: if ($item['value']) { goto RQkmt; } goto qhE2d; VbAfN: a1r31: goto a8C2o; SL_J_: $arquivo = $arquivoIde; goto eQyBj; QKWfV: if (!file_exists($arquivo)) { goto nROHw; } goto AwMdf; wfKuH: $item['callbackAoSair'] = file_get_contents($arquivo); goto tJWqa; Hw1NC: $item['callbackPreencherValor'] = file_get_contents($arquivo); goto N0dKE; zfWrR: V7bvm: goto WiTeZ; nGI2x: goto QT6Py; goto fUIdo; zYRbe: J5RaX: goto fWDPM; rwIj1: $arquivoIde = isset($r_pegarForm) && $g['modoDesenvolvedor'] ? null : 'ide/personalizacaoForm/' . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $id . '-callbackRestricao'; goto oPuNx; Lv7qC: $arquivo = $arquivoIde; goto PNpz3; iNbhV: $arquivoIde = 'ide/personalizacaoForm/' . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $id . '-codigoFonte'; goto WiHyh; fWDPM: if (!($arquivo && file_exists($arquivo))) { goto cGF2P; } goto vdTls; q95lK: GbdMP: goto rLAqc; v_4fv: cGF2P: goto rwIj1; Kasui: if (!(!$g['modoDesenvolvedor'] && !$g['modoDesenvolvedorApp'] && $arquivoGlobal && file_exists($arquivoGlobal))) { goto J5RaX; } goto nV_CV; KEhvF: $item['type'] = 'text'; goto UHUVc; FXlKl: $item['value'] = Personalizacoes::carregarExecutarCallback($item['callbackPreencherValor'], $carregarParametros); goto b1C65; yOVnh: XAke4: goto LeNVp; Gtw0l: $arquivo = $arquivoIde; goto kOnw4; WiTeZ: $arquivoIde = 'ide/personalizacaoForm/' . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $id . '-callbackPreencherValor'; goto YHxlw; SNgwr: $item['value'] = isset($item['value']) ? $item['value'] : $g['conf']->camposValorPadrao($id); goto KIiTq; WFbiT: goto nY2fZ; goto JEBe7; Yvpuu: $type = $g['conf']->camposTipo($id); goto M8MpV; TTSBD: goto tVxBp; goto xeTSS; p9QHb: md1_Y: goto GQwqI; KAbBz: $arquivo = $arquivoIde; goto niNOY; nsKUV: gdHTv: goto J6lI3; b1C65: FlBe7: goto XIyMw; a8C2o: if (!($g['modoDesenvolvedorApp'] && $arquivoAppsEditor && file_exists($arquivoAppsEditor))) { goto gOoX5; } goto EM2Lc; YqrGj: if (!(!$g['modoDesenvolvedor'] && !$g['modoDesenvolvedorApp'] && file_exists($arquivoGlobal))) { goto YSUD7; } goto tj9oV; xeTSS: hxwQ2: goto LVJ2n; tJWqa: atUm2: goto OSt9C; AwMdf: $item['callbackPreencherSelect'] = file_get_contents($arquivo); goto l6mNC; vdTls: $item['callbackAoAlterar'] = file_get_contents($arquivo); goto v_4fv; OLuhF: $arquivo = $arquivoApps; goto nsKUV; VS1cM: if (!(strlen($item['value']) > 0)) { goto jH0px; } goto CqUGp; YnfAn: switch ($item['type']) { case 'moeda': $item['length'] = isset($item['length']) ? $item['length'] : array(12, 50); goto z3AUw; case 'data': $item['length'] = isset($item['length']) ? $item['length'] : array(10, 10); goto z3AUw; case 'dataHora': $item['length'] = isset($item['length']) ? $item['length'] : array(19, 19); goto z3AUw; case 'tempo': $item['length'] = isset($item['length']) ? $item['length'] : array(12, 10); goto z3AUw; case 'cnpj': $item['length'] = isset($item['length']) ? $item['length'] : array(14, 18); goto z3AUw; case 'cpf': $item['length'] = isset($item['length']) ? $item['length'] : array(20, 14); goto z3AUw; case 'cep': $item['length'] = isset($item['length']) ? $item['length'] : array(10, 15); goto z3AUw; } goto PkPtu; TZ0eo: unset($itemSelect); goto H9MVM; KIiTq: $this->formArrayValues[$id] = $item['value']; goto mARGM; OSt9C: $arquivoIde = isset($r_pegarForm) && $g['modoDesenvolvedor'] ? null : 'ide/personalizacaoForm/' . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $id . '-callbackAoAlterar'; goto znuO1; PUkKg: $arquivoGlobal = isset($r_pegarForm) ? null : "{$g['pathDados']}personalizacaoForm/" . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $id . '-callbackRestricao'; goto KAbBz; I55_L: n5q33: goto v70zF; N0dKE: ABEhI: goto Ui7la; WKgtR: $arquivoAppsEditor = Apps::getDirEditor() . 'personalizacaoForm/' . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $id . '-callbackPreencherValor'; goto zqKlk; UZ0xh: $valorSelect = $g['conf']->camposValorPadrao($id); goto VS1cM; wyzrf: $carregarParametros[$this->formNome] = false; goto vLB1l; zNA_k: gSSjY: goto fOgT5; e9tby: if ($item['type'] == 'select') { goto XX5Ds; } goto e5fVr; cDbfd: if (!empty($carregarParametros['todosCampos'])) { goto ww83R; } goto EpnNL; JEBe7: RQkmt: goto uYCHL; JawK9: $arquivo = $arquivoAppsEditor; goto tSayF; WiHyh: $arquivoApps = Apps::getDirAtivos() . 'personalizacaoForm/' . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $id . '-codigoFonte'; goto Ivici; HX0oo: $arquivoIde = isset($r_pegarForm) && $g['modoDesenvolvedor'] ? null : 'ide/personalizacaoForm/' . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $id . '-callbackAoSair'; goto LVvUn; tCO0U: FuI_w: goto I55_L; iiNJi: $arquivoAppsEditor = Apps::getDirEditor() . 'personalizacaoForm/' . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $id . '-callbackPreencherSelect'; goto x67yq; QabAx: $arquivo = $arquivoApps; goto q95lK; EM2Lc: $arquivo = $arquivoAppsEditor; goto jtpQ5; iZlfR: if (!(!$g['modoDesenvolvedor'] && file_exists($arquivoApps))) { goto gdHTv; } goto OLuhF; jOtrD: if (!(!$g['modoDesenvolvedor'] && !$g['modoDesenvolvedorApp'] && $arquivoGlobal && file_exists($arquivoGlobal))) { goto sWGAc; } goto sN7d0; R4PeQ: unset($this->formArray['campos'][$tab][$id]); goto TTSBD; sIsNz: CU32Q: goto z315E; H1fMa: $arquivoGlobal = isset($r_pegarForm) ? null : "{$g['pathDados']}personalizacaoForm/" . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $id . '-callbackAoSair'; goto CDKqt; wYe9t: if (!file_exists($arquivo)) { goto V7bvm; } goto n1g0l; k3NJI: $item = $codigo; goto zfWrR; u8_DB: z3AUw: goto YrTA2; oPuNx: $arquivoApps = isset($r_pegarForm) && $g['modoDesenvolvedor'] ? null : Apps::getDirAtivos() . 'personalizacaoForm/' . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $id . '-callbackRestricao'; goto AMO7U; HrfVJ: foreach ($this->formArrayTodosCampos as $key => $value) { $sql = str_replace("[[{$key}]]", $value, $sql); o4gkn: } goto sIsNz; sN7d0: $arquivo = $arquivoGlobal; goto PC086; T1DYP: if (!$item['callbackPreencherSelect']) { goto FuI_w; } goto JT3CE; wBjh2: nY2fZ: goto cwCat; wyJso: $item['select'] = Personalizacoes::carregarExecutarCallback($item['callbackPreencherSelect'], $carregarParametros); goto tCO0U; niNOY: if (!(!$g['modoDesenvolvedor'] && $arquivoApps && file_exists($arquivoApps))) { goto md1_Y; } goto b7Phz; kOnw4: if (!(!$g['modoDesenvolvedor'] && file_exists($arquivoApps))) { goto GbdMP; } goto QabAx; W8CEg: if (!($arquivo && file_exists($arquivo))) { goto gSSjY; } goto s7Bs9; idnHN: Hw65v: goto nGI2x; bjQrH: if (!($arquivo && file_exists($arquivo))) { goto atUm2; } goto wfKuH; fUIdo: XX5Ds: goto UZ0xh; Ui7la: if (!$item['callbackPreencherValor']) { goto FlBe7; } goto AG91d; bY6dC: if (!(!$g['modoDesenvolvedor'] && !$g['modoDesenvolvedorApp'] && file_exists($arquivoGlobal))) { goto UeT3D; } goto QlLHT; jtpQ5: gOoX5: goto aW_NK; l6mNC: nROHw: goto T1DYP; x67yq: $arquivoGlobal = "{$g['pathDados']}personalizacaoForm/" . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $id . '-callbackPreencherSelect'; goto SL_J_; houka: KqL5E: goto Kasui; wYl_Z: h5MjH: goto bY6dC; z0W2o: vhlwY: goto YqrGj; Ivici: $arquivoAppsEditor = Apps::getDirEditor() . 'personalizacaoForm/' . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $id . '-codigoFonte'; goto imWA2; KoAcy: z6NRA: goto EP0gG; LVvUn: $arquivoApps = isset($r_pegarForm) && $g['modoDesenvolvedor'] ? null : Apps::getDirAtivos() . 'personalizacaoForm/' . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $id . '-callbackAoSair'; goto Las0_; vqIqi: EVDC4: goto IHiUx; JE2lM: $arquivo = $arquivoAppsEditor; goto z0W2o; AMO7U: $arquivoAppsEditor = isset($r_pegarForm) && ($g['modoDesenvolvedor'] || $g['modoDesenvolvedorApp']) ? null : Apps::getDirEditor() . 'personalizacaoForm/' . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $id . '-callbackRestricao'; goto PUkKg; Ftv2w: YSUD7: goto QKWfV; CqUGp: $valorSelect = $item['value']; goto YAcsW; y_aB3: $arquivo = $arquivoApps; goto VbAfN; eQyBj: if (!(!$g['modoDesenvolvedor'] && file_exists($arquivoApps))) { goto z6NRA; } goto kjEAJ; LeNVp: if (!(!$g['modoDesenvolvedor'] && !$g['modoDesenvolvedorApp'] && file_exists($arquivoGlobal))) { goto auOF1; } goto X14i4; UHUVc: mbBbT: goto YnfAn; C0d7j: $arquivo = $arquivoIde; goto iZlfR; t_pUs: $arquivoApps = Apps::getDirAtivos() . 'personalizacaoForm/' . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $id . '-callbackPreencherSelect'; goto iiNJi; mARGM: goto Hw65v; goto wLd7E; z315E: $campoPadraoPesquisa = mysql_query($sql); goto lUFCq; P76JM: } goto JZ7Qf; HaEBO: pYRbU: goto sKIhD; JZ7Qf: sKAMt: goto HaEBO; sKIhD: } goto CSzow; vTvU0: if ($g['modoDesenvolvedor']) { goto UP3q7; } goto E3jie; iOYtT: $personalizado = Personalizacoes::processarForm($formId, $r_pegarForm ? array('desenvolvedor', 'apps') : array('desenvolvedor', 'apps', 'global', 'perfil', 'usuario')); goto jmgPV; ezOPJ: goto lci2Y; goto TxEde; LEqAY: apc_store("form-php-{$formId}-pegarForm", serialize($this->formArray), 3600); goto VxdcJ; OyuqQ: $this->formArray = $formArray; goto bKYYH; OBJuE: if ($g['modoDesenvolvedorApp']) { goto q9IgY; } goto iOYtT; tjb8U: $arquivos = []; goto vTvU0; fADnR: mNoO0: goto eFYH2; TEnIz: $g['nivelAlcada'] = 4; goto nUYVx; jmgPV: goto S5Vb0; goto I4_ME; Q4Fpy: l6ci2: goto budtI; o5mte: $arquivos[] = $r_pegarForm ? null : $arquivoIde; goto nFXvc; SzyGv: $formId = md5(trim($g['r_'], '/') . '/'); goto k2uOI; MHpD2: $this->formArrayOriginal = $this->formArray; goto tPG__; RtpNH: cY_dN: goto TZIHq; Jscbq: $arquivos[] = $arquivoIde; goto Nvjbb; COxmA: return null; goto UOZ4T; TxEde: UP3q7: goto o5mte; pStzH: $arquivoAppsEditor = Apps::getDirEditor() . "personalizacaoForm/" . str_replace('/', '-', trim(@$g['r_'], '/')) . '-novoCampo'; goto fvcxZ; I4_ME: q9IgY: goto QljLd; XQNg8: foreach ($arquivos as $arquivoPersonalizacaoNovoCampo) { goto OrpDL; P7xpF: rlge5: goto Evfze; btN8p: include $arquivoPersonalizacaoNovoCampo; goto vJNrA; Evfze: TsIe4: goto MVoj0; OrpDL: if (!file_exists($arquivoPersonalizacaoNovoCampo)) { goto rlge5; } goto btN8p; vJNrA: $this->formArray = array_replace_recursive($this->formArray, $formArray); goto P7xpF; MVoj0: } goto RtpNH; UHIQG: $arquivos[] = $arquivoIde; goto YGZ0U; fvcxZ: $arquivoGlobal = "{$g['pathDados']}personalizacaoForm/" . str_replace('/', '-', trim(@$g['r_'], '/')) . '-novoCampo'; goto tjb8U; Nvjbb: $arquivos[] = $arquivoApps; goto U70ej; kteV4: b0Icg: goto Q4Fpy; kYfn6: if (!$r_pegarForm) { goto EUT4R; } goto LEqAY; NWEgn: $personalizado = Personalizacoes::processarForm($formId, array('desenvolvedor')); goto kteV4; uuTUD: goto SXacc; goto TiRb_; VxdcJ: die("form-php-{$formId}-pegarForm"); goto PHS6n; kwv1B: $arquivos[] = $r_pegarForm ? null : $arquivoAppsEditor; goto gU1Oy; QljLd: $personalizado = Personalizacoes::processarForm($formId, $r_pegarForm ? array('desenvolvedor', 'apps') : array('desenvolvedor', 'apps', 'apps-editor')); goto TNAeN; budtI: foreach ($personalizado as $tab => $tabCampos) { goto GOAlk; tNKz9: hfFLM: goto VFXFI; GOAlk: foreach ($tabCampos as $item) { goto eePcw; wh_dE: FeNAD: goto hjP19; y_iT5: $this->formArray['campos'][$tab][$id] = array_merge($this->formArray['campos'][$tab][$id], $item); goto wh_dE; eePcw: $id = $item['field']; goto Nj2ts; hjP19: oErcQ: goto SXs3p; bSlxw: DKnY0: goto y_iT5; Nj2ts: if (isset($this->formArray['campos'][$tab][$id])) { goto DKnY0; } goto vxfI5; PgjHS: goto FeNAD; goto bSlxw; vxfI5: $this->formArray['campos'][$tab][$id] = $item; goto PgjHS; SXs3p: } goto WUTFi; WUTFi: iyWvo: goto tNKz9; VFXFI: } goto TNLd0; xmle2: uFiTM: goto COxmA; bKYYH: $arquivoIde = 'ide/personalizacaoForm/' . str_replace('/', '-', trim(@$g['r_'], '/')) . '-novoCampo'; goto xCHow; UOZ4T: } public function campoCodigoHTML($id, $disabled = false, $alterado = false, $enviar_btnId = false) { goto BXKc3; LBiN2: XAIg_: goto PSOwi; ZNj3u: csjgC: goto fZtVx; AxRYI: goto aRa1Q; goto dhR0E; xsCte: AOukX: goto DKb2L; d4Ua6: $tamanho = $item['abrirJanelaTamanho'] ? $item['abrirJanelaTamanho'] : 'auto'; goto saMJt; pKrVP: $options .= "readOnly: true,\n"; goto LBiN2; EEda5: $output .= <<<EOT
\t\t\t<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                {$pesquisaGridCallbackJS}
\t\t\t\t\$(`#{$g['divId']} #\${CSS.escape('{$id}')}_Pesquisa`).bind('keypress', function(e) {
                    const divId = '{$item['pesquisaGrid'][7]}' ? '{$item['pesquisaGrid'][7]}' : '{$g['divId']}'; 
\t\t\t\t\tif (e.keyCode == 13) {
                        // todosCampos
\t\t\t\t\t\tlet todosCampos = {};
\t\t\t\t\t\t\$('#{$g['divId']} .formFormulario:first .itemForm').each(function() {
\t\t\t\t\t\t\ttodosCampos[\$(this).attr('id')] = \$(this).val(); 
\t\t\t\t\t\t});
\t\t\t\t\t\tvar pesquisa = \$(`#{$g['divId']} #\${CSS.escape('{$id}')}_Pesquisa`).val();
\t\t\t\t\t\tif ({$item['pesquisaGrid'][15]}) {
\t\t\t\t\t\t\t// Pesquisa se existe apenas um resultado para pesquisa
\t\t\t\t\t\t\t\$('#{$g['divId']}').showLoading();
\t\t\t\t\t\t\t\$.getJSON('/sistema/funcoes/pesquisaGrid/pesquisa/?ajax=true' + '&pesquisaGridId=' + encodeURIComponent('{$pesquisaGridId}') + '&pesquisa=' + encodeURIComponent(pesquisa) + '&campo1=' + encodeURIComponent('{$item['pesquisaGrid'][0]}') + '&campo2=' + encodeURIComponent('{$item['pesquisaGrid'][1]}') + '&campo3=' + encodeURIComponent('{$item['pesquisaGrid'][2]}') + '&campo4=' + encodeURIComponent('{$item['pesquisaGrid'][3]}') + '&campo5=' + encodeURIComponent('{$item['pesquisaGrid'][4]}') + '&campo6=' + encodeURIComponent('{$item['pesquisaGrid'][5]}') + '&campo7=' + encodeURIComponent('{$item['pesquisaGrid'][6]}') + '&campo8=' + encodeURIComponent(divId) + '&campo9=' + encodeURIComponent('{$item['pesquisaGrid'][8]}') + '&campo10=' + encodeURIComponent('{$item['pesquisaGrid'][9]}') + '&campo11=' + encodeURIComponent('{$item['pesquisaGrid'][10]}') + '&campo12=' + encodeURIComponent('{$item['pesquisaGrid'][11]}') + '&campo13=' + encodeURIComponent('{$item['pesquisaGrid'][12]}') + '&campo14=' + encodeURIComponent('{$item['pesquisaGrid'][13]}') + '&campo15=' + encodeURIComponent('{$item['pesquisaGrid'][14]}') + '&campoPesquisaOrigem=' + encodeURIComponent('{$id}_Pesquisa') + '&todosCampos=' + encodeURIComponent(JSON.stringify(todosCampos)) + '&callback=?', function(request) {
\t\t\t\t\t\t\t\tif (request.code) {
\t\t\t\t\t\t\t\t\t\$('#{$g['divId']}').hideLoading();
\t\t\t\t\t\t\t\t\t{$item['pesquisaGrid'][6]}(divId, request.id1, request.id2, request.id3, request.id4);
\t\t\t\t\t\t\t\t} else if (request.erro) {
\t\t\t\t\t\t\t\t\t\$('#{$g['divId']}').hideLoading();
\t\t\t\t\t\t\t\t\tdialogConfirm('Erro', 'Registro não encontrado');
\t\t\t\t\t\t\t\t} else {
\t\t\t\t\t\t\t\t\t\$('#{$g['divId']}').hideLoading();
\t\t\t\t\t\t\t\t\tabrirJanela(e, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', '{$item['pesquisaGrid'][4]}', '/sistema/funcoes/pesquisaGrid/content/', '&pesquisaGridId=' + encodeURIComponent('{$pesquisaGridId}') + '&pesquisa=' + encodeURIComponent(pesquisa) + '&campo1=' + encodeURIComponent('{$item['pesquisaGrid'][0]}') + '&campo2=' + encodeURIComponent('{$item['pesquisaGrid'][1]}') + '&campo3=' + encodeURIComponent('{$item['pesquisaGrid'][2]}') + '&campo4=' + encodeURIComponent('{$item['pesquisaGrid'][3]}') + '&campo5=' + encodeURIComponent('{$item['pesquisaGrid'][4]}') + '&campo6=' + encodeURIComponent('{$item['pesquisaGrid'][5]}') + '&campo7=' + encodeURIComponent('{$item['pesquisaGrid'][6]}') + '&campo8=' + encodeURIComponent(divId) + '&campo9=' + encodeURIComponent('{$item['pesquisaGrid'][8]}') + '&campo10=' + encodeURIComponent('{$item['pesquisaGrid'][9]}') + '&campo11=' + encodeURIComponent('{$item['pesquisaGrid'][10]}') + '&campo12=' + encodeURIComponent('{$item['pesquisaGrid'][11]}') + '&campo13=' + encodeURIComponent('{$item['pesquisaGrid'][12]}') + '&campo14=' + encodeURIComponent('{$item['pesquisaGrid'][13]}') + '&campo15=' + encodeURIComponent('{$item['pesquisaGrid'][14]}') + '&campoPesquisaOrigem=' + encodeURIComponent('{$id}_Pesquisa') + '&todosCampos=' + encodeURIComponent(JSON.stringify(todosCampos)), {$item['pesquisaGrid'][11]});
\t\t\t\t\t\t\t\t\treturn false;
\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t});
\t\t\t\t\t\t} else {
\t\t\t\t\t\t\tabrirJanela(e, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', '{$item['pesquisaGrid'][4]}', '/sistema/funcoes/pesquisaGrid/content/', '&pesquisaGridId=' + encodeURIComponent('{$pesquisaGridId}') + '&pesquisa=' + encodeURIComponent(pesquisa) + '&campo1=' + encodeURIComponent('{$item['pesquisaGrid'][0]}') + '&campo2=' + encodeURIComponent('{$item['pesquisaGrid'][1]}') + '&campo3=' + encodeURIComponent('{$item['pesquisaGrid'][2]}') + '&campo4=' + encodeURIComponent('{$item['pesquisaGrid'][3]}') + '&campo5=' + encodeURIComponent('{$item['pesquisaGrid'][4]}') + '&campo6=' + encodeURIComponent('{$item['pesquisaGrid'][5]}') + '&campo7=' + encodeURIComponent('{$item['pesquisaGrid'][6]}') + '&campo8=' + encodeURIComponent(divId) + '&campo9=' + encodeURIComponent('{$item['pesquisaGrid'][8]}') + '&campo10=' + encodeURIComponent('{$item['pesquisaGrid'][9]}') + '&campo11=' + encodeURIComponent('{$item['pesquisaGrid'][10]}') + '&campo12=' + encodeURIComponent('{$item['pesquisaGrid'][11]}') + '&campo13=' + encodeURIComponent('{$item['pesquisaGrid'][12]}') + '&campo14=' + encodeURIComponent('{$item['pesquisaGrid'][13]}') + '&campo15=' + encodeURIComponent('{$item['pesquisaGrid'][14]}') + '&campo16=' + encodeURIComponent('{$item['pesquisaGrid'][16]}') +'&campoPesquisaOrigem=' + encodeURIComponent('{$id}_Pesquisa') + '&todosCampos=' + encodeURIComponent(JSON.stringify(todosCampos)), {$item['pesquisaGrid'][11]});
\t\t\t\t\t\t\treturn false;
\t\t\t\t\t\t}
\t\t\t\t\t}
\t\t\t\t});
\t\t\t} </script></div>
EOT;
goto sPrCv; nzNKu: $item['value'] = str_replace('"', "&quot;", $item['value']); goto SGcAa; SHTQq: dtfiq: goto hOFIi; i6QwM: $options_ .= $value_Pesquisa; goto VGwfX; J_tkC: $output .= <<<EOT
                        var jsonCode = \$textarea.val();
                        monaco.editor.getModels().forEach(model => model.dispose());
\t\t\t\t\t\tvar modelUri = monaco.Uri.parse('a://b/foo.json'); // a made up unique URI for our model
\t\t\t\t\t\tvar model = monaco.editor.createModel(jsonCode, 'json', modelUri);
                        // configure the JSON language support with schemas and schema associations
\t\t\t\t\t\tmonaco.languages.json.jsonDefaults.setDiagnosticsOptions({
\t\t\t\t\t\t\tvalidate: true,
\t\t\t\t\t\t\tschemas: [
\t\t\t\t\t\t\t\t{
\t\t\t\t\t\t\t\t\turi: 'http://myserver/dashboard-schema.json',
\t\t\t\t\t\t\t\t\tfileMatch: [modelUri.toString()],
\t\t\t\t\t\t\t\t\tschema: {
\t\t\t\t\t\t\t\t\t\ttype: 'object',
\t\t\t\t\t\t\t\t\t\trequired: ['itens'],
\t\t\t\t\t\t\t\t\t\tproperties: {
\t\t\t\t\t\t\t\t\t\t\titens: {
                                                description: 'Relatórios, gráficos e indicadores',
\t\t\t\t\t\t\t\t\t\t\t\t\$ref: 'http://myserver/dashboard-itens-schema.json'
\t\t\t\t\t\t\t\t\t\t\t},\t\t\t\t\t\t\t\t\t\t\t
\t\t\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t{
\t\t\t\t\t\t\t\t\turi: 'http://myserver/dashboard-itens-schema.json',
\t\t\t\t\t\t\t\t\tschema: {
\t\t\t\t\t\t\t\t\t\ttype: 'array',
\t\t\t\t\t\t\t\t\t\titems: {
                                            type: 'object',
                                            required: ['url'],
\t\t\t\t\t\t\t\t\t\t\tproperties: {
\t\t\t\t\t\t\t\t\t\t\t\turl: {
\t\t\t\t\t\t\t\t\t\t\t\t\tdescription: 'URL do item a ser carregada.',
\t\t\t\t\t\t\t\t\t\t\t\t\ttype: 'string',
\t\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\t\tparametros: {
\t\t\t\t\t\t\t\t\t\t\t\t\tdescription: 'Parâmetros opcionais',
\t\t\t\t\t\t\t\t\t\t\t\t\ttype: 'string',
\t\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t]
\t\t\t\t\t\t});
EOT;
goto xsCte; XIlfe: FvEW2: goto taMGN; sPrCv: sac2y: goto CCa2I; Lh3e3: $arquivo = $arquivoGlobal; goto M0jo2; nzhtH: return $output; goto PmMhw; WCHsT: goto wU0Ly; goto Vo7oy; FVKzH: $value_Pesquisa = isset($item['value']) && empty($value_Pesquisa) ? "value=\"{$item['value']}\" " : $value_Pesquisa; goto i6QwM; iE1vW: $item['pesquisa'][4] = !isset($item['pesquisa'][4]) ? '' : $item['pesquisa'][4]; goto EFZ34; tQfoB: eOwAO: goto WCHsT; VGwfX: $output = "<input type=\"text\" {$options_} class=\"campo_input_pesquisa itemForm itemForm-{$item['name']}_Pesquisa {$item['class']}\" />"; goto JG1Si; oIkl7: if (!(isset($item['readonly']) && $item['readonly'])) { goto XAIg_; } goto pKrVP; dHqdx: if (!isset($item['select'])) { goto Sglrb; } goto X9B2_; o57Yk: apc_store("callbackAoSair-{$cacheBase}-{$formId}-{$campoId}", $item['callbackAoSair'], 3600); goto PiXe5; ETg_I: $options .= "minimap: { enabled: false },\n"; goto oIkl7; dhR0E: DaVgM: goto wmZxH; MzDSi: goto WLZw_; goto Mrmal; X9B2_: reset($item['select']); goto drDJ7; yegmt: Z_0_y: goto TxRBJ; vFgTA: vd72b: goto Ik2nd; MPrqO: $output .= <<<EOT
                <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                    \$('#{$g['divId']} .itemForm-{$item['name']}').mask('99999-999');
                } </script></div>
EOT;
goto S0FcF; n4ReA: $item['pesquisaGrid'][6] = 'func' . uniqid(); goto QQe16; d5c0I: goto d57cF; goto NMN9z; PwvO6: $item['pesquisaGrid'][3] = !isset($item['pesquisaGrid'][3]) ? '' : $item['pesquisaGrid'][3]; goto KFSRR; NzAcU: $value_Pesquisa = isset($item['value_Pesquisa']) ? "value=\"{$item['value_Pesquisa']}\" " : ''; goto FVKzH; TxRBJ: $options .= $item['type'] != 'textarea' && $item['type'] != 'select' && $item['type'] != 'pesquisa' && $item['type'] != 'pesquisaGrid' && $item['type'] != 'moeda' && $item['type'] != 'tempo' && $item['type'] != 'data' && $item['type'] != 'dataHora' ? "type=\"{$item['type']}\" " : ''; goto txfwV; J_Ra4: $options .= !isset($item['array_group']) || $item['array_group'] ? "name=\"{$this->formNome}[{$item['name']}]\" " : "name=\"{$item['name']}_disabled\" "; goto pbtGG; CK0XK: $item['pesquisaGrid'][8] = serialize($item['pesquisaGrid'][8]); goto nM4RQ; Yuq4z: $item['style'] .= ' font-size: 16px !important;'; goto wxErr; AKrqz: $item['type'] = 'submit'; goto yegmt; txfwV: $options .= isset($item['length'][0]) && $item['type'] == 'textarea' ? "rows=\"{$item['length'][0]}\" " : ''; goto QeM6X; j2w2h: ssTEG: goto RkM2W; Plq4S: $output .= <<<EOT
\t\t\t\t<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
\t\t\t\t\t\$('#{$g['divId']} #{$id}').bind('{$evento}', function(e) {
\t\t\t\t\t\tvar valor = \$('#{$g['divId']} #{$id}').val();
                        var \$form = \$('#{$g['divId']} #{$id}').closest('.formFormulario');
\t\t\t\t\t\t\$.ajax({
\t\t\t\t\t\t\turl: '/sistema/personalizacaoForm/form_func-ajax/callbackAoSair/',
\t\t\t\t\t\t\tdataType: 'html',
\t\t\t\t\t\t\ttype: 'POST',
\t\t\t\t\t\t\tdata: 'ajax=true&divIdParent={$g['divIdParent']}&divId={$g['divId']}&campoId={$campoId}&formId={$formId}&acaoId={$r_acaoId}&campoAtual=' + encodeURIComponent(valor) + '&formNome=' + \$form.data('formNome') + '&' + \$form.serialize(),
\t\t\t\t\t\t\tsuccess: function(data) {
\t\t\t\t\t\t\t\t\$('#{$g['divId']} #{$id}').closest('.formFormulario').append(data);
\t\t\t\t\t\t\t\treturn false;
\t\t\t\t\t\t\t}
\t\t\t\t\t\t});
\t\t\t\t\t});
\t\t\t\t} </script></div>
EOT;
goto tqRKQ; kLmwe: eUzCG: goto Ci3oE; pJFqw: $item['class'] .= ' tw-form-button'; goto TQM9f; HsxzW: $item['pesquisaGrid'][3] = str_replace("\r", ' ', $item['pesquisaGrid'][3]); goto cdK6i; gko55: global $r_modoFormLeve, $r_acaoId, $r_campoRefreshVars; goto U71C8; xLf1K: if (!isset($item['pesquisaSelect'])) { goto vd72b; } goto mFZ48; YZoXr: $options_ = ''; goto NM83v; C1yI1: Bsd7Y: goto JXn6F; DpMF4: if ($item['codeSchema'] == 'bi') { goto Ea8eR; } goto QRCax; Mrmal: dYuT2: goto o2bOU; dLCju: goto Q1Mni; goto lK1Sa; GOKfZ: $arquivoIde = 'ide/personalizacaoForm/' . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $id . '-callbackJS'; goto jqOZd; QSMR7: WLZw_: goto AxRYI; TvwyR: $arquivoAppsEditor = Apps::getDirEditor() . 'personalizacaoForm/' . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $id . '-callbackJS'; goto zM5Sw; oZEM_: wU0Ly: goto Aq0aT; LZSMf: $casas = isset($item['casasDecimais']) ? $item['casasDecimais'] : 2; goto kUov0; JG1Si: $options_ = $options; goto Mu2dJ; MXA4j: if ($item['type'] == 'cpf') { goto csjgC; } goto O6JNF; Wohep: goto eOwAO; goto NNtdk; Livis: $options .= "model: model,\n"; goto J_tkC; BXKc3: global $g; goto gko55; ufIX2: guOR9: goto YZoXr; wxErr: if (!($item['type'] == 'select')) { goto yZaRB; } goto O0kh_; OWrU7: Sglrb: goto XIlfe; O6JNF: if ($item['type'] == 'cep') { goto zRDz1; } goto VKQk6; D5W07: if (!(isset($item['disabled']) && $item['disabled'])) { goto uIoGk; } goto zI5G8; PSOwi: if (!$item['codeModoSimples']) { goto GENPt; } goto xfB0d; J3jaW: $item['pesquisaGrid'][15] = isset($item['pesquisaGrid'][15]) ? $item['pesquisaGrid'][15] : 'false'; goto HSZJw; gHIP8: $options .= "folding: false,\n"; goto ETg_I; C3NMW: $options = ''; goto XDSB8; dvMRb: goto zcqY_; goto V9YZ9; CCa2I: goto FRbH0; goto SiRRG; S7xyY: yZaRB: goto OG1j0; JwAog: $options_ .= $value_Pesquisa; goto klmvC; q32Wq: $output .= "<input type=\"hidden\" {$options_} value=\"{$item['value']}\" class=\"itemForm itemForm-{$item['name']} {$item['class']}\">"; goto EJQ3v; mqqKd: foreach ($item['select'] as $option) { goto SrFoL; Vz0uO: if (!isset($option['disabled'])) { goto fi_pH; } goto q7rN2; UhTPF: goto K1nT6; goto Cbvw7; Cbvw7: ffjEk: goto b3iW6; ErW1v: CT1gr: goto UhTPF; E5zNc: $lastSelectValue = $option['value']; goto XhDjM; TTfp7: $tmp .= isset($option['value']) ? "value=\"{$option['value']}\" " : ''; goto CBT3K; SrFoL: $tmp = ''; goto Vz0uO; qBewU: goto J8OOL; goto m38tu; GC6IY: goto CT1gr; goto pnDUI; CBT3K: if (isset($option['selected']) && $option['selected']) { goto qZHz5; } goto DVUfd; m38tu: qZHz5: goto agQnb; DVUfd: if (isset($r_modoFormLeve) && $r_modoFormLeve && $r_acaoId) { goto ffjEk; } goto NILUM; KCka5: $selectOptions .= '<option ' . trim($tmp) . " {$option['extra']}>{$option['title']}</option>"; goto GC6IY; agQnb: $tmp .= 'selected="selected" '; goto E5zNc; e_gMN: $selectOptions .= '<optgroup label="' . $option['value'] . '"></optgroup>'; goto ErW1v; b3iW6: K1nT6: goto qBewU; H1Uk_: fi_pH: goto TTfp7; pnDUI: AsBqs: goto e_gMN; S81D3: J8OOL: goto Mbfzl; XhDjM: $selectOptions .= '<option ' . trim($tmp) . " {$option['extra']}>{$option['title']}</option>"; goto S81D3; Mbfzl: b0lJ8: goto k2BHX; NILUM: if ($option['title'] == 'optgroup') { goto AsBqs; } goto KCka5; q7rN2: $tmp .= 'disabled="disabled" '; goto H1Uk_; k2BHX: } goto Y6uAj; EJQ3v: uIoGk: goto EbOCD; OG1j0: if (in_array($item['type'], ['button', 'submit', 'enviarForm'])) { goto wdbHV; } goto vYlym; kZw8z: $item['pesquisa'][3] = !isset($item['pesquisa'][3]) ? '' : $item['pesquisa'][3]; goto iE1vW; hrBj0: hMpB3: goto s1aRO; sC4JC: $output = "<textarea {$options} class=\"itemForm itemForm-{$item['name']} {$item['class']}\">{$tmpValue}</textarea>"; goto hxq8j; xOE73: $options .= !isset($item['array_group']) || $item['array_group'] ? "name=\"{$this->formNome}[{$item['name']}]\" " : "name=\"{$item['name']}\" "; goto PM8di; LfLyG: $output .= <<<EOT
<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
\$(`#{$g['divId']} #\${CSS.escape('{$id}')}_Pesquisa`).autocomplete({
\tsource: function( request, response ) {
        // todosCampos
        let todosCampos = {};
        \$('#{$g['divId']} .formFormulario:first .itemForm').each(function() {
            todosCampos[\$(this).attr('id')] = \$(this).val(); 
        });
\t\t\$.ajax({
\t\t\turl: "/sistema/funcoes/pesquisa/campoPesquisa/",
\t\t\ttype: 'POST',
\t\t\tdataType: "jsonp",
\t\t\tdata: 'ajax=true&tabela=' + encodeURIComponent('{$item['pesquisa'][0]}') + 
\t\t\t\t'&campoId=' + encodeURIComponent('{$item['pesquisa'][1]}') +
\t\t\t\t'&campoTxt=' + encodeURIComponent('{$item['pesquisa'][2]}') +
\t\t\t\t'&sqlExtra=' + encodeURIComponent('{$item['pesquisa'][3]}') +
\t\t\t\t'&formato=' + encodeURIComponent('{$item['pesquisa'][4]}') +
\t\t\t\t'&pesquisa=' + encodeURIComponent(request.term) +
\t\t\t\t'&todosCampos=' + encodeURIComponent(JSON.stringify(todosCampos)),
\t\t\tsuccess: function( data ) {
\t\t\t\tif (isempty(data)) {
\t\t\t\t\tdata = [{
\t\t\t\t\t\ttitle: 'Não encontrado! Pesquise novamente.',
\t\t\t\t\t\tvalue: 0
\t\t\t\t\t}];
\t\t\t\t}
\t\t\t\tresponse( \$.map( data, function( item ) {
\t\t\t\t\treturn {
\t\t\t\t\t\tlabel: item.title,
\t\t\t\t\t\tvalue: item.value
\t\t\t\t\t}
\t\t\t\t}));
\t\t\t}
\t\t});
\t},
\tdelay: 200,
\t{$pesquisaSelect},
\t{$pesquisaChange},
\tfocus: function(event, ui) { 
\t\t\$(this).val(ui.item.label);
\t\treturn false;
\t}
}).data('selectLabel', '').data('selectValue', 0);
} </script></div>
EOT;
goto bXd5G; sIHWX: $options .= isset($item['extra']) ? $item['extra'] . ' ' : ''; goto mpoP5; cEeyD: IPL6F: goto YR4Bz; l9gV4: rjx7g: goto hRlIR; kUov0: $output = "<input type=\"text\" {$options} class=\"campo_input_moeda itemForm itemForm-{$item['name']} {$item['class']}\" onkeypress=\"return(Verifica_Caracter(event, this));\" onchange=\"return(Corrige_Valor(event, this,{$casas}));\" />"; goto TIZHW; w15Rf: $output = ""; goto Zs2I4; jtS5K: $lastSelectValue = $item['select'][0]['value']; goto c4IOj; TC3GS: $options .= isset($item['readonly']) && $item['readonly'] ? 'readonly="readonly" tabindex="-1" ' : ''; goto lu9Cl; bVFF3: uB2iv: goto uMBQw; VlqMZ: $titulo = $item['abrirJanelaTitulo'] ? $item['abrirJanelaTitulo'] : 'Janela'; goto d4Ua6; pbtGG: pmDqW: goto aJ34S; qgkC1: $output .= <<<EOT
                        // Documentos úteis utilizado para desenvolver este Schema
                        // https://github.com/Microsoft/monaco-editor/issues/38
                        // https://microsoft.github.io/monaco-editor/playground.html#extending-language-services-configure-json-defaults
                        // https://simonplend.com/wp-content/uploads/2020/12/JSON-Schema-Cheat-Sheet-v1.1.pdf
                        var jsonCode = \$textarea.val();
                        monaco.editor.getModels().forEach(model => model.dispose());
\t\t\t\t\t\tvar modelUri = monaco.Uri.parse('a://b/foo.json'); // a made up unique URI for our model
\t\t\t\t\t\tvar model = monaco.editor.createModel(jsonCode, 'json', modelUri);
                        // configure the JSON language support with schemas and schema associations
\t\t\t\t\t\tmonaco.languages.json.jsonDefaults.setDiagnosticsOptions({
\t\t\t\t\t\t\tvalidate: true,
\t\t\t\t\t\t\tschemas: [
\t\t\t\t\t\t\t\t{
\t\t\t\t\t\t\t\t\turi: 'http://myserver/bi-schema.json', // id of the first schema
\t\t\t\t\t\t\t\t\tfileMatch: [modelUri.toString()], // associate with our model
\t\t\t\t\t\t\t\t\tschema: {
\t\t\t\t\t\t\t\t\t\ttype: 'object',
\t\t\t\t\t\t\t\t\t\trequired: ['linha', 'dados'],
\t\t\t\t\t\t\t\t\t\tproperties: {
\t\t\t\t\t\t\t\t\t\t\ttipo: {
                                                description: 'Tipo do relátorio B.I. (padrão: grid)',
                                                type: 'string',
                                                default: 'grid',
\t\t\t\t\t\t\t\t\t\t\t\tenum: ['grid', 'grafico']
\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\tgrid: {
                                                description: 'Propriedades que modificam um relátorio tipo GRID',
\t\t\t\t\t\t\t\t\t\t\t\t\$ref: 'http://myserver/bi-grid-schema.json'
\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\tgrafico: {
                                                description: 'Propriedades que definem e modificam um gráfico',
                                                \$ref: 'http://myserver/bi-grafico-schema.json'
\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\tlinha: {
                                                description: 'Definição da Dimensão que será utilizada na linhas do relatório',
                                                \$ref: 'http://myserver/bi-linha-schema.json'
\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\tcoluna: {
                                                description: 'Definição da Dimensão que será utilizada para a geração de colunas do relatório',
                                                \$ref: 'http://myserver/bi-linha-schema.json'
\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\tdados: {
                                                description: 'Inclusão de Fatos, os seja, os campos de valores numéricos que serão mostrados no relátorio',
                                                \$ref: 'http://myserver/bi-dados-schema.json'
\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\tfiltros: {
\t\t\t\t\t\t\t\t\t\t\t\tdescription: 'Monta os filtros que serão exibidos',
                                                \$ref: 'http://myserver/bi-filtros-schema.json'
\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t{
\t\t\t\t\t\t\t\t\turi: 'http://myserver/bi-grid-schema.json',
\t\t\t\t\t\t\t\t\tschema: {
\t\t\t\t\t\t\t\t\t\ttype: 'object',
\t\t\t\t\t\t\t\t\t\tproperties: {
\t\t\t\t\t\t\t\t\t\t\tmostrarCurvaABC: {
                                                description: 'Padrão: false',
                                                type: 'boolean',
                                                default: false,
\t\t\t\t\t\t\t\t\t\t\t\tenum: [true, false]
\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\tocultarColunasDashboard: {
                                                description: 'Padrão: false',
                                                type: 'boolean',
                                                default: false,
\t\t\t\t\t\t\t\t\t\t\t\tenum: [true, false]
\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\tocultarSempreColunas: {
                                                description: 'Padrão: false',
                                                type: 'boolean',
                                                default: false,
\t\t\t\t\t\t\t\t\t\t\t\tenum: [true, false]
\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\tocultarPorcentagem: {
                                                description: 'Padrão: false',
                                                type: 'boolean',
                                                default: false,
\t\t\t\t\t\t\t\t\t\t\t\tenum: [true, false]
\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\tocultarTotais: {
                                                description: 'Padrão: false',
                                                type: 'boolean',
                                                default: false,
\t\t\t\t\t\t\t\t\t\t\t\tenum: [true, false]
\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\tincluirLinhaVazia: {
                                                description: 'Padrão: false',
                                                type: 'boolean',
                                                default: false,
\t\t\t\t\t\t\t\t\t\t\t\tenum: [true, false]
\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\tincluirColunaVazia: {
                                                description: 'Padrão: false',
                                                type: 'boolean',
                                                default: false,
\t\t\t\t\t\t\t\t\t\t\t\tenum: [true, false]
\t\t\t\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t{
\t\t\t\t\t\t\t\t\turi: 'http://myserver/bi-grafico-schema.json',
\t\t\t\t\t\t\t\t\tschema: {
\t\t\t\t\t\t\t\t\t\ttype: 'object',
\t\t\t\t\t\t\t\t\t\tproperties: {
\t\t\t\t\t\t\t\t\t\t\ttipo: {
                                                description: 'Padrão: barra',
                                                type: 'string',
                                                default: 'barra',
\t\t\t\t\t\t\t\t\t\t\t\tenum: ['barra', 'pizza', 'linha', 'area', 'radar', 'grid']
\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\tincluirGraficoLabel: {
                                                description: 'Padrão: true',
                                                type: 'boolean',
                                                default: true,
\t\t\t\t\t\t\t\t\t\t\t\tenum: [true, false]
\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\tincluirGraficoLegend: {
                                                description: 'Padrão: true',
                                                type: 'boolean',
                                                default: true,
\t\t\t\t\t\t\t\t\t\t\t\tenum: [true, false]
\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t{
\t\t\t\t\t\t\t\t\turi: 'http://myserver/bi-linha-schema.json',
\t\t\t\t\t\t\t\t\tschema: {
\t\t\t\t\t\t\t\t\t\ttype: 'object',
\t\t\t\t\t\t\t\t\t\trequired: ['campo'],
\t\t\t\t\t\t\t\t\t\tproperties: {
\t\t\t\t\t\t\t\t\t\t\tcampo: {
                                                description: 'Formato: {Dimensão}->{Campo}',
                                                type: 'string',
\t\t\t\t\t\t\t\t\t\t\t\tenum: [
                                                    {$listaCampos['dimensoes']}
\t\t\t\t\t\t\t\t\t\t\t\t]
\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\tordenacao: {
                                                description: 'Ordenação (padrão: asc)',
                                                type: 'string',
\t\t\t\t\t\t\t\t\t\t\t\tenum: ['desc', 'asc', 'total_desc', 'total_asc']
\t\t\t\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t{
\t\t\t\t\t\t\t\t\turi: 'http://myserver/bi-dados-schema.json',
\t\t\t\t\t\t\t\t\tschema: {
\t\t\t\t\t\t\t\t\t\ttype: 'array',
\t\t\t\t\t\t\t\t\t\titems: {
                                            type: 'object',
                                            required: ['campo'],
\t\t\t\t\t\t\t\t\t\t\tproperties: {
\t\t\t\t\t\t\t\t\t\t\t\tcampo: {
\t\t\t\t\t\t\t\t\t\t\t\t\tdescription: 'Formato: {Entidade}->{Campo}',
\t\t\t\t\t\t\t\t\t\t\t\t\ttype: 'string',
\t\t\t\t\t\t\t\t\t\t\t\t\tenum: [
\t\t\t\t\t\t\t\t\t\t\t\t\t\t{$listaCampos['fatos']}
\t\t\t\t\t\t\t\t\t\t\t\t\t]
\t\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\t\tagregador: {
\t\t\t\t\t\t\t\t\t\t\t\t\tdescription: 'Agregadores: https://clickhouse.com/docs/en/sql-reference/aggregate-functions/reference/',
\t\t\t\t\t\t\t\t\t\t\t\t\ttype: 'string',
                                                \tdefault: 'sum',
\t\t\t\t\t\t\t\t\t\t\t\t\tenum: ['sum', 'avg', 'any', 'count', 'min', 'max']
\t\t\t\t\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t{
\t\t\t\t\t\t\t\t\turi: 'http://myserver/bi-filtros-schema.json',
\t\t\t\t\t\t\t\t\tschema: {
\t\t\t\t\t\t\t\t\t\ttype: 'array',
\t\t\t\t\t\t\t\t\t\titems: {
\t\t\t\t\t\t\t\t\t\t\ttype: 'object',
\t\t\t\t\t\t\t\t\t\t\trequired: ['campo', 'titulo'],
\t\t\t\t\t\t\t\t\t\t\tproperties: {
\t\t\t\t\t\t\t\t\t\t\t\tcampo: {
\t\t\t\t\t\t\t\t\t\t\t\t\tanyOf: [
\t\t\t\t\t\t\t\t\t\t\t\t\t\t{
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\tdescription: 'Nome do campo para aplicar o filtro',
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\ttype: 'string',
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\tenum: [
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t{$listaCampos['todos']}
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t]
\t\t\t\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\t\t\t\t{
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\ttype: "string"
\t\t\t\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\t\t\t],
\t\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\t\ttitulo: {
\t\t\t\t\t\t\t\t\t\t\t\t\tdescription: 'Título do filtro',
\t\t\t\t\t\t\t\t\t\t\t\t\ttype: 'string'
\t\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\t\texibir: {
\t\t\t\t\t\t\t\t\t\t\t\t\tdescription: 'Exibir ou não o filtro',
\t\t\t\t\t\t\t\t\t\t\t\t\ttype: 'boolean',
\t\t\t\t\t\t\t\t\t\t\t\t\tdefault: true,
\t\t\t\t\t\t\t\t\t\t\t\t\tenum: [true, false]
\t\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\t\ttipo: {
\t\t\t\t\t\t\t\t\t\t\t\t\tdescription: 'Tipo do campo',
\t\t\t\t\t\t\t\t\t\t\t\t\ttype: 'string',
\t\t\t\t\t\t\t\t\t\t\t\t\tdefault: 'texto',
\t\t\t\t\t\t\t\t\t\t\t\t\tenum: ['texto', 'numero', 'data', 'select']
\t\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\t\tvalor: {
\t\t\t\t\t\t\t\t\t\t\t\t\tdescription: 'Trazer o filtro já preenchido com um valor filtrado',
\t\t\t\t\t\t\t\t\t\t\t\t\ttype: 'string'
\t\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\t\tselect: {
\t\t\t\t\t\t\t\t\t\t\t\t\ttype: 'array',
\t\t\t\t\t\t\t\t\t\t\t\t\titems: {
\t\t\t\t\t\t\t\t\t\t\t\t\t\ttype: 'object',
\t\t\t\t\t\t\t\t\t\t\t\t\t\trequired: ['title', 'value'],
\t\t\t\t\t\t\t\t\t\t\t\t\t\tproperties: {
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\ttitle: {
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\tdescription: 'Título',
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\ttype: 'string'
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\tvalue: {
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\tdescription: 'Valor',
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\ttype: 'string'
\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t]
\t\t\t\t\t\t});
EOT;
goto kLmwe; uMBQw: $pesquisaGridCallbackJS = ''; goto DaLIh; PM8di: $options .= isset($item['id_show']) && !$item['id_show'] ? '' : "id=\"{$tagId}\" "; goto pqlWZ; TkmPQ: if ($item['type'] == 'comentario') { goto cf_4a; } goto RiDQh; vrMMA: $options .= " style=\"{$uppercase}{$item['style']}\""; goto oGzpK; u3qYn: if (!($item['codeSchema'] == 'dashboard')) { goto AOukX; } goto Livis; S0FcF: lYwtg: goto DNXQ9; q7mYQ: $tamanho = "'auto'"; goto XvUKy; hEMw5: $item['pesquisaGrid'][11] = isset($item['pesquisaGrid'][11]) ? $item['pesquisaGrid'][11] : '[800,400]'; goto zKjP3; OhbRP: ajQmG: goto tQfhn; nM4RQ: zcqY_: goto u9vVB; K4nl0: b5mDh: goto KvC8U; OXLtt: robg1: goto hrBj0; Xp7Ml: $options .= 'alterado="true"'; goto SHTQq; z_01X: $formId = md5(trim($g['r_'], '/') . '/'); goto DzdO9; VKQk6: $output = "<input {$options} class=\"itemForm itemForm-{$item['name']} {$item['class']}\" />"; goto Jj3fm; AptOv: $listaCampos = array('dimensoes' => '', 'fatos' => '', 'todos' => ''); goto py_9o; iZxKJ: if (is_array($item['pesquisaGrid'][8])) { goto mF2Lr; } goto S_7pg; SGcAa: T0NPc: goto C3NMW; rfZB9: apc_store("callbackAoAlterar-{$cacheBase}-{$formId}-{$campoId}", $item['callbackAoAlterar'], 3600); goto AAAhr; F3fIJ: $output .= <<<EOT
\t\t\t\t<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') { 
\t\t\t\t    var gerarEditor = function() {
\t\t\t\t\t\tvar \$textarea = \$('#{$g['divId']} #{$id}');
\t\t\t\t\t\t\$('<div id="{$id}Code">').appendTo(\$textarea.parent());
\t\t\t\t\t\t\$item = \$('#{$g['divId']} #{$id}Code');
\t\t\t\t\t\t\$item.width(\$textarea.width());
\t\t\t\t\t\t\$item.height(\$textarea.height());
\t\t\t\t\t\t\$textarea.css('display', 'none');
EOT;
goto DpMF4; V9YZ9: EMScX: goto iZxKJ; q4KUN: N_ChE: goto gVvQU; cudJX: KQcQi: goto AKSin; AAS94: $options .= "overviewRulerLanes: 0,\n"; goto gHIP8; RiDQh: if ($item['type'] == 'cnpj') { goto b5mDh; } goto MXA4j; aJRX5: if (!($g['modoDesenvolvedorApp'] && file_exists($arquivoAppsEditor))) { goto CQgF6; } goto kFc1a; NM83v: $options_ .= isset($item['id_show']) && !$item['id_show'] ? '' : "id=\"{$id}\" "; goto oy1r2; Mu2dJ: $options_ .= isset($item['value']) ? "value=\"{$item['value']}\" " : ''; goto rxkxD; rxkxD: $output .= "<input type=\"hidden\" {$options_} class=\"itemForm itemForm-{$item['name']} {$item['class']}\" />"; goto kZw8z; Ci3oE: $output .= <<<EOT
\t\t\t\t\t\tvar monEditor = monaco.editor.create(document.getElementById("{$id}Code"), {
\t\t\t\t\t\t\tvalue: \$textarea.val(),
\t\t\t\t\t\t\tlanguage: "{$item['code']}",
\t\t\t\t\t\t\ttheme: "vs-dark",
\t\t\t\t\t\t\t{$options}
\t\t\t\t\t\t});
\t\t\t\t\t\tmonEditor.getModel().onDidChangeContent(function (e) {
\t\t\t\t\t\t\t\$textarea.val(monEditor.getValue());
\t\t\t\t\t\t})
\t\t\t\t\t\t\$item.closest('.ui-widget-content').css('overflow', 'hidden');
\t\t\t\t\t\t\$textarea.bind('setEditorValue', function(event, value) {
\t\t\t\t\t\t    monEditor.setValue(value);
\t\t\t\t\t\t});
                        
\t\t\t\t\t}
\t\t\t\t\t// Tem alguns textarea que são maximizados no JS, para esses casos aguardar 100ms
\t\t\t\t\tvar \$textarea = \$('#{$g['divId']} #{$id}');
\t\t\t\t\tif (\$textarea.width() > 150) {
\t\t\t\t\t    gerarEditor();
\t\t\t\t\t} else {
\t\t\t\t\t    \$textarea.bind('gerarEditor', gerarEditor);
\t\t\t\t\t}
\t\t\t\t} </script></div>
EOT;
goto JU7t3; xfB0d: $options .= "lineNumbers: 'off',\n"; goto D8Wtc; vr97v: if (!$item['abrirJanela']) { goto qV_4V; } goto VlqMZ; YkqLR: $selectIconStyle = <<<EOT
\tstyle="display: inline-block; margin-left: 1px; cursor: pointer; float: right;";
EOT;
goto d5c0I; ik393: if ($item['type'] == 'moeda') { goto sOgLB; } goto YsavN; Fseqh: sCOvr: goto aJRX5; Fjw7_: if ($item['type'] == 'dataHora') { goto R2LxL; } goto ik393; A7p3r: if (!isset($item['value'])) { goto T0NPc; } goto nzNKu; iUnCU: $selectIconStyle = <<<EOT
\tstyle="position: absolute; right: 0; top: -28px;";
EOT;
goto JdIgT; z0Fqg: $item['pesquisaGrid'][8] = serialize(array()); goto dvMRb; Jj3fm: goto lYwtg; goto Be_Go; Be_Go: zRDz1: goto VvtoF; RkM2W: goto ajQmG; goto K4nl0; tQfhn: goto gc41g; goto zxbJN; QQe16: $pks = explode(',', $item['pesquisaGrid'][1]); goto x_WK3; b0NFW: $options = trim($options); goto cqSG4; y63lA: dEbfD: goto KDPDm; pQovV: $item['value'] = $lastSelectValue; goto ufIX2; UiXJz: if (!(!$g['modoDesenvolvedor'] && !$g['modoDesenvolvedorApp'] && file_exists($arquivoGlobal))) { goto x8jL6; } goto Lh3e3; f5dEe: $pesquisaChange = $item['pesquisaChange']; goto cEeyD; Y6uAj: Qgfgd: goto cKsM0; q9xe0: $cacheBase = md5($g['pathDados']); goto rfZB9; cqSG4: if ($item['type'] == 'textarea') { goto DaVgM; } goto Vywi4; qsDUD: $cacheBase = md5($g['pathDados']); goto o57Yk; g4xmY: $options .= isset($item['length'][1]) && $item['type'] != 'textarea' ? "maxlength=\"{$item['length'][1]}\" " : ''; goto fivYE; xnyxY: $evento = $item['type'] == 'button' ? 'click' : 'keypress'; goto f240e; fZtVx: $output = "<input type=\"text\" {$options} class=\"campo_input_cpf itemForm itemForm-{$item['name']} {$item['class']}\" onkeypress=\"return(Formatar_Cpf('8',this,event));\" maxlength=\"14\" size=\"14\"/>"; goto j2w2h; taMGN: $output .= "<select {$options} class=\"itemForm itemForm-{$item['name']} {$item['class']}\">{$selectOptions}</select>"; goto QSMR7; TQM9f: $item['style'] .= ' width: 100% !important;'; goto OXLtt; tqRKQ: dArHo: goto wHDe7; hxq8j: if (empty($item['code'])) { goto JUhs6; } goto YHPH6; FFFOG: if (isset($item['disabled']) && $item['disabled']) { goto jk5_z; } goto xOE73; c4IOj: lU6Bp: goto K25HR; hOFIi: $options .= isset($item['displayType']) ? "display-type=\"{$item['displayType']}\" " : ''; goto b0NFW; NNtdk: R2LxL: goto Z_jaG; s1aRO: if (isset($item['style']) && !empty($item['style'])) { goto Bsd7Y; } goto vlSDi; vYlym: $item['class'] .= ' tw-form-field'; goto u_vuf; Ik2nd: if (!isset($item['pesquisaChange'])) { goto IPL6F; } goto f5dEe; sCMO0: if (!file_exists($arquivo)) { goto uB2iv; } goto VcD8I; cKsM0: $output = ''; goto js_lk; dN14l: if ($item['type'] == 'titulo') { goto OHDhl; } goto TkmPQ; kcl_s: if (!(!is_array($enviar_btnId) && $item['type'] == 'enviarForm')) { goto rxw5i; } goto wfbQW; vlSDi: $options .= isset($item['uppercase']) && !$item['uppercase'] ? ' style="text-transform: none;"' : ''; goto OzdXC; Zs2I4: MMX8Y: goto KfKsm; zI5G8: isset($item['value']) or $item['value'] = ''; goto iijJY; T9R7J: $arquivo = $arquivoIde; goto RC4kY; Ye5QI: mF2Lr: goto CK0XK; DNXQ9: goto ssTEG; goto ZNj3u; u_vuf: goto robg1; goto nsuQg; GWZdD: $item['pesquisaGrid'][14] = isset($item['pesquisaGrid'][14]) ? $item['pesquisaGrid'][14] : '%-%'; goto J3jaW; DKb2L: goto dEbfD; goto y54pd; JPuxG: OHDhl: goto w15Rf; tCiId: gc41g: goto QfeDx; jqOZd: $arquivoApps = Apps::getDirAtivos() . 'personalizacaoForm/' . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $id . '-callbackJS'; goto TvwyR; mpoP5: $options .= isset($item['checked']) && $item['checked'] ? 'checked="checked" ' : ''; goto Y6I3b; O0kh_: $item['style'] .= ' height: 39px; overflow: hidden;'; goto S7xyY; XM72c: $options .= isset($item['length'][0]) && $item['type'] != 'textarea' ? "size=\"{$item['length'][0]}\" " : ''; goto g4xmY; wmZxH: $tmpValue = isset($item['value']) ? $item['value'] : ''; goto sC4JC; z7n8e: $campoId = md5($id); goto q9xe0; lu9Cl: if (!($_SESSION['device'] == 'mobile')) { goto hMpB3; } goto Yuq4z; ACn8b: $item['pesquisaGrid'][10] = isset($item['pesquisaGrid'][10]) ? $item['pesquisaGrid'][10] : ''; goto hEMw5; OzdXC: goto o28Jb; goto C1yI1; fivYE: $options .= isset($item['value']) && $item['type'] != 'textarea' && $item['type'] != 'pesquisa' && $item['type'] != 'pesquisaGrid' ? "value=\"{$item['value']}\" " : ''; goto sIHWX; saMJt: $parametros = $item['abrirJanelaParametros']; goto pzYNJ; nsuQg: wdbHV: goto pJFqw; KDPDm: goto eUzCG; goto DXovS; oy1r2: $options_ .= !isset($item['array_group']) || $item['array_group'] ? "name=\"{$this->formNome}[{$item['name']}]\" " : "name=\"{$item['name']}\" "; goto q32Wq; RC4kY: if (!(!$g['modoDesenvolvedor'] && file_exists($arquivoApps))) { goto sCOvr; } goto vNmo6; PiXe5: $evento = $item['type'] == 'button' ? 'click' : 'blur'; goto Plq4S; dQs_B: if (isset($item['pesquisaGrid'][8])) { goto EMScX; } goto z0Fqg; KvC8U: $output = "<input type=\"text\" {$options} class=\"campo_input_cnpj itemForm itemForm-{$item['name']} {$item['class']}\" onkeypress=\"return(Formatar_Cnpj('7',this,event));\" maxlength=\"18\" size=\"18\"/>"; goto OhbRP; kFc1a: $arquivo = $arquivoAppsEditor; goto QCzN2; e4dmY: if (!(count($item['select']) == 1)) { goto lU6Bp; } goto jtS5K; DXovS: Ea8eR: goto AptOv; O0ZfM: aRa1Q: goto D5W07; DJKZY: ylcZ4: goto oFNg3; GjCsB: jk5_z: goto GCSJ1; vNmo6: $arquivo = $arquivoApps; goto Fseqh; QCHbk: goto OYvrA; goto cVrU0; m5DzN: $options_ .= isset($item['value']) ? "value=\"{$item['value']}\" " : ''; goto MxUA1; MxUA1: $output .= "<input type=\"hidden\" {$options_} class=\"itemForm itemForm-{$item['name']} {$item['class']}\" />"; goto PwvO6; y54pd: YoXBl: goto Xvg0B; Y6I3b: $options .= $disabled || isset($item['disabled']) && $item['disabled'] ? 'disabled="disabled" tabindex="-1" ' : ''; goto TC3GS; py_9o: foreach (BI::estruturaDeDados() as $item) { goto XhoOd; XhoOd: $listaCampos['todos'] .= $listaCampos['todos'] ? ', ' : ''; goto c53vn; B2XGo: $listaCampos['fatos'] .= $listaCampos['fatos'] ? ', ' : ''; goto X5993; BQGm7: if (strpos($item['campo'], 'FAC_') !== false) { goto fc5JD; } goto BOE9J; fr7RS: $listaCampos['dimensoes'] .= "'{$item['campo']}'"; goto qobhZ; c53vn: $listaCampos['todos'] .= "'{$item['campo']}'"; goto BQGm7; nUt0q: fc5JD: goto B2XGo; BOE9J: $listaCampos['dimensoes'] .= $listaCampos['dimensoes'] ? ', ' : ''; goto fr7RS; qobhZ: goto JJxFw; goto nUt0q; GAqUk: JJxFw: goto d3moY; X5993: $listaCampos['fatos'] .= "'{$item['campo']}'"; goto GAqUk; d3moY: mvyJT: goto pTBHg; pTBHg: } goto q4KUN; zM5Sw: $arquivoGlobal = "{$g['pathDados']}personalizacaoForm/" . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $id . '-callbackJS'; goto T9R7J; QeM6X: $options .= isset($item['length'][1]) && $item['type'] == 'textarea' ? "cols=\"{$item['length'][1]}\" " : ''; goto XM72c; o2bOU: $lastSelectValue = 0; goto e4dmY; KfKsm: goto rjx7g; goto DJKZY; Qp6S8: $item['pesquisaGrid'][9] = serialize(array()); goto QCHbk; TIZHW: rIic2: goto Wohep; GJ5MI: $output = "<input type=\"text\" {$options} class=\"campo_input_data itemForm itemForm-{$item['name']} {$item['class']}\" onkeypress=\"return(Formatar_Data('0',this,event,'/'));\" />"; goto oZEM_; DzdO9: $campoId = md5($id); goto qsDUD; uTxR_: if ($item['type'] == 'pesquisa') { goto r5QSM; } goto w2EcB; HSZJw: $item['pesquisaGrid'][16] = isset($item['pesquisaGrid'][16]) ? $item['pesquisaGrid'][16] : ''; goto GOKfZ; zKjP3: $item['pesquisaGrid'][12] = isset($item['pesquisaGrid'][12]) ? $item['pesquisaGrid'][12] : ''; goto tjp1L; NMN9z: B_Ebr: goto iUnCU; drDJ7: if ($_SESSION['device'] == 'mobile') { goto B_Ebr; } goto YkqLR; f240e: $output .= <<<EOT
\t\t\t\t<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
\t\t\t\t    var \$campo = \$('#{$g['divId']} #{$id}');
\t\t\t\t    \$campo.addClass('temAcaoEnter');
\t\t\t\t\t\$campo.bind('{$evento}', function(e) {
\t\t\t\t\t\tif ('{$evento}' == 'click' || e.keyCode == 13) {
\t\t\t\t\t\t\tvar valor = \$campo.val();
                            // Parametros extras
                            var parametrosExtras = '{$parametros}';
                            if (parametrosExtras) {
                                // Faz a substituição de variáveis com campos do form
                                var \$form = \$campo.closest('.formFormulario');
                                for (var item of \$form.serializeArray()) {
\t\t\t\t\t\t\t\t\tvar itemName = item.name.replace(\$form.data('formNome') + '[', '');
\t\t\t\t\t\t\t\t\tvar itemValue = item.value;
\t\t\t\t\t\t\t\t\titemName = itemName.replace(/\\]\$/, '').trim();
                                    parametrosExtras = parametrosExtras.replaceAll('{' + itemName + '}', encodeURIComponent(itemValue));
\t\t\t\t\t\t\t\t}
                            }
\t\t\t\t\t\t\tabrirJanela(e, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', '{$titulo}', '{$item['abrirJanela']}', '&campoAtual=' + encodeURIComponent(valor) + '{$g['repassarParametros']}' + parametrosExtras, {$tamanho});
\t\t\t\t\t\t\treturn false;
\t\t\t\t\t\t}
\t\t\t\t\t});
\t\t\t\t} </script></div>
EOT;
goto EUmGP; B2XbX: R5wM1: goto nzhtH; eRmAq: $value_Pesquisa = isset($item['value_Pesquisa']) ? "value=\"{$item['value_Pesquisa']}\" " : ''; goto WSoKH; D8Wtc: $options .= "glyphMargin: false,\n"; goto j3o2j; YHPH6: $options = ''; goto AAS94; lK1Sa: N313b: goto ggZAd; VcD8I: $item['pesquisaGridCallbackJS'] = file_get_contents($arquivo); goto bVFF3; YsavN: if ($item['type'] == 'tempo') { goto ylcZ4; } goto dN14l; eevXk: $output = "<span {$options}>{$item['value']}</span>"; goto tCiId; KFSRR: $item['pesquisaGrid'][3] = str_replace("\n", ' ', $item['pesquisaGrid'][3]); goto HsxzW; K25HR: $selectOptions = ''; goto mqqKd; M1pzJ: if (!$item['callbackAoSair']) { goto dArHo; } goto z_01X; SiRRG: r5QSM: goto sF5i_; f8Obi: if ($item['type'] == 'data') { goto mbzNO; } goto Fjw7_; pzYNJ: if (!($tamanho == 'auto')) { goto yBrtq; } goto q7mYQ; iijJY: if (!($item['type'] == 'select')) { goto guOR9; } goto pQovV; KRREQ: sOgLB: goto LZSMf; cVrU0: g2r_P: goto EziO5; x_WK3: $pesquisaGridCallbackJS = <<<EOT
function {$item['pesquisaGrid'][6]}(...args) {
\tlet array = {};
\tarray['divId'] = args[0];
\tarray['{$pks[0]}'] = args[1];
\tarray['{$pks[1]}'] = args[2];
\tarray['{$pks[2]}'] = args[3];
\tarray['{$pks[3]}'] = args[4];
\t{$item['pesquisaGridCallbackJS']}
}
EOT;
goto WluYR; WluYR: z7uU5: goto EEda5; S_7pg: $item['pesquisaGrid'][8] = array($item['pesquisaGrid'][8]); goto Ye5QI; QPW8a: Q1Mni: goto vr97v; gVvQU: $options .= "model: model,\n"; goto qgkC1; GCSJ1: $options .= isset($item['id_show']) && !$item['id_show'] ? '' : "id=\"{$tagId}_disabled\" "; goto J_Ra4; YR4Bz: $item['pesquisa'][3] = str_replace("'", "\\'", $item['pesquisa'][3]); goto LfLyG; QRCax: if ($item['codeSchema'] == 'indicador') { goto YoXBl; } goto u3qYn; oGzpK: o28Jb: goto t4jTX; QfeDx: goto MMX8Y; goto JPuxG; gFlKS: rxw5i: goto FFFOG; bXd5G: FRbH0: goto MzDSi; EFZ34: $pesquisaSelect = <<<EOT
\t\t\t\tselect: function(event, ui) { 
\t\t\t\t\t// Ao selecionar, seta o campo hidden ID
\t\t\t\t\tvar id = \$(this).attr('id').replace(/\\_Pesquisa\$/, '');
\t\t\t\t\tif (ui.item.value != 0) {
\t\t\t\t\t\t\$(this).val(ui.item.label);
\t\t\t\t\t\t\$('#' + CSS.escape(id)).val(ui.item.value);
\t\t\t\t\t\t\$(this).data('selectLabel', ui.item.label);
\t\t\t\t\t\t\$(this).data('selectValue', ui.item.value);
\t\t\t\t\t} else {
\t\t\t\t\t\t\$(this).val('');
\t\t\t\t\t\t\$('#' + CSS.escape(id)).val('');
\t\t\t\t\t}
\t\t\t\t\t\$('#' + CSS.escape(id)).trigger('change');
\t\t\t\t\treturn false;
\t\t\t\t}
EOT;
goto pbGnH; VBDNn: $formId = md5(trim($g['r_'], '/') . '/'); goto z7n8e; vgYh8: OYvrA: goto ACn8b; zxbJN: cf_4a: goto eevXk; tjp1L: $item['pesquisaGrid'][13] = isset($item['pesquisaGrid'][13]) ? serialize($item['pesquisaGrid'][13]) : ''; goto GWZdD; DaLIh: if (!$item['pesquisaGridCallbackJS']) { goto z7uU5; } goto n4ReA; wHDe7: if (!$item['callbackAoAlterar']) { goto R5wM1; } goto VBDNn; aJ34S: if (!($item['type'] == 'enviarForm')) { goto Z_0_y; } goto AKrqz; h_MRZ: $options_ = $options; goto m5DzN; M0jo2: x8jL6: goto sCMO0; AAAhr: $output .= <<<EOT
\t\t\t\t<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
\t\t\t\t\t\$('#{$g['divId']} #{$id}').bind('change', function(e) {
\t\t\t\t\t\tvar valor = \$('#{$g['divId']} #{$id}').val();
                        var \$form = \$('#{$g['divId']} #{$id}').closest('.formFormulario');
\t\t\t\t\t\t\$.ajax({
\t\t\t\t\t\t\turl: '/sistema/personalizacaoForm/form_func-ajax/callbackAoAlterar/',
\t\t\t\t\t\t\tdataType: 'html',
\t\t\t\t\t\t\ttype: 'POST',
\t\t\t\t\t\t\tdata: 'ajax=true&divIdParent={$g['divIdParent']}&divId={$g['divId']}&campoId={$campoId}&formId={$formId}&acaoId={$r_acaoId}&campoAtual=' + encodeURIComponent(valor) + '&formNome=' + \$form.data('formNome') + '&' + \$form.serialize(),
\t\t\t\t\t\t\tsuccess: function(data) {
\t\t\t\t\t\t\t\t\$form.append(data);
\t\t\t\t\t\t\t\treturn false;
\t\t\t\t\t\t\t}
\t\t\t\t\t\t});
\t\t\t\t\t});
\t\t\t\t} </script></div>
EOT;
goto B2XbX; j3o2j: GENPt: goto F3fIJ; rzDe7: $output .= <<<EOT
                        var jsonCode = \$textarea.val();
                        monaco.editor.getModels().forEach(model => model.dispose());
\t\t\t\t\t\tvar modelUri = monaco.Uri.parse('a://b/foo.json'); // a made up unique URI for our model
\t\t\t\t\t\tvar model = monaco.editor.createModel(jsonCode, 'json', modelUri);
                        // configure the JSON language support with schemas and schema associations
\t\t\t\t\t\tmonaco.languages.json.jsonDefaults.setDiagnosticsOptions({
\t\t\t\t\t\t\tvalidate: true,
\t\t\t\t\t\t\tschemas: [
\t\t\t\t\t\t\t\t{
\t\t\t\t\t\t\t\t\turi: 'http://myserver/indicador-schema.json',
\t\t\t\t\t\t\t\t\tfileMatch: [modelUri.toString()],
\t\t\t\t\t\t\t\t\tschema: {
\t\t\t\t\t\t\t\t\t\ttype: 'object',
\t\t\t\t\t\t\t\t\t\trequired: ['nome', 'valor'],
\t\t\t\t\t\t\t\t\t\tproperties: {
\t\t\t\t\t\t\t\t\t\t\tnome: {
                                                description: 'Nome do indicador.',
                                                type: 'string',
\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\tvalor: {
\t\t\t\t\t\t\t\t\t\t\t\tdescription: 'Valor do indicador. Caso utilizado o campo "sql", deverá ser utilizado a variável "[[valor]]" para substituição.',
\t\t\t\t\t\t\t\t\t\t\t\ttype: 'string',
\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\tclasse: {
\t\t\t\t\t\t\t\t\t\t\t\tdescription: 'Classe CSS.',
\t\t\t\t\t\t\t\t\t\t\t\ttype: 'string',
                                                default: 'bi-indicador-padrao',
\t\t\t\t\t\t\t\t\t\t\t\tenum: ['bi-indicador-padrao'],
\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\tsql: {
\t\t\t\t\t\t\t\t\t\t\t\tdescription: 'Query SQL para ser executada no servidor ClickHouse, retornando o valor do Indicador. Variáveis disponíveis: [[extra]].',
\t\t\t\t\t\t\t\t\t\t\t\ttype: 'string',
\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t\tdecimais: {
\t\t\t\t\t\t\t\t\t\t\t\tdescription: 'Utilizado para a formatação de casas decimais do valor retornado pelo "sql".',
\t\t\t\t\t\t\t\t\t\t\t\ttype: 'number',
\t\t\t\t\t\t\t\t\t\t\t\tdefault: 2,
\t\t\t\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t]
\t\t\t\t\t\t});
EOT;
goto y63lA; u9vVB: if (isset($item['pesquisaGrid'][9])) { goto g2r_P; } goto Qp6S8; EUmGP: qV_4V: goto M1pzJ; wfbQW: $tagId = $enviar_btnId; goto gFlKS; klmvC: $output = "<input type=\"text\" enterkeyhint=\"enter\" {$options_} class=\"campo_input_pesquisa_grid itemForm itemForm-{$item['name']}_Pesquisa {$item['class']}\" />"; goto h_MRZ; mFZ48: $pesquisaSelect = $item['pesquisaSelect']; goto vFgTA; EziO5: $item['pesquisaGrid'][9] = serialize($item['pesquisaGrid'][9]); goto vgYh8; EbOCD: if (isset($item['restricao']) && !$item['restricao']) { goto N313b; } goto dLCju; VvtoF: $output = "<input type=\"text\" {$options} class=\"campo_input_cep itemForm itemForm-{$item['name']} {$item['class']}\" maxlength=\"10\" size=\"15\"/>"; goto MPrqO; U3zTb: $options_ = str_replace($id, "{$id}_Pesquisa", $options); goto eRmAq; Xvg0B: $options .= "model: model,\n"; goto rzDe7; oFNg3: $output = "<input type=\"text\" {$options} class=\"campo_input_tempo itemForm itemForm-{$item['name']} {$item['class']}\" onkeyup=\"return(Formatar_Tempo(this));\" maxlength=\"6\" size=\"5\" value=\"00:00\"/>"; goto l9gV4; JU7t3: JUhs6: goto O0ZfM; js_lk: if (!(!isset($item['selectRefresh']) || isset($item['selectRefresh']) && $item['selectRefresh'])) { goto FvEW2; } goto dHqdx; pqlWZ: goto pmDqW; goto GjCsB; XvUKy: yBrtq: goto xnyxY; Aq0aT: goto sac2y; goto cudJX; cdK6i: $item['pesquisaGrid'][3] = str_replace("'", "\\'", $item['pesquisaGrid'][3]); goto dQs_B; sF5i_: $options_ = str_replace($id, "{$id}_Pesquisa", $options); goto NzAcU; WSoKH: $value_Pesquisa = isset($item['value']) ? "value=\"{$item['value']}\" " : $value_Pesquisa; goto JwAog; QCzN2: CQgF6: goto UiXJz; AKSin: $pesquisaGridId = md5($g['r_'] . $id); goto U3zTb; JdIgT: d57cF: goto rk2HK; XDSB8: $tagId = $id; goto kcl_s; U71C8: $item = $this->formArray['campos'][$this->formTabId[$id]][$id]; goto A7p3r; JXn6F: $uppercase = isset($item['uppercase']) && !$item['uppercase'] ? 'text-transform: none;' : ''; goto vrMMA; pbGnH: $pesquisaChange = <<<EOT
\t\t\t\tchange: function(event, ui) {
\t\t\t\t\t// Usuário não pode mudar o campo, apenas selecionar um valor da pesquisa
\t\t\t\t\tvar id = \$(this).attr('id').replace(/\\_Pesquisa\$/, '');
\t\t\t\t\tif (isempty(\$(this).val())) {
\t\t\t\t\t\t\$(this).val('');
\t\t\t\t\t\t\$('#' + CSS.escape(id)).val('');
\t\t\t\t\t\t\$(this).data('selectLabel', '');
\t\t\t\t\t\t\$(this).data('selectValue', '');
\t\t\t\t\t} else if (\$(this).val() != \$(this).data('selectLabel')) {
\t\t\t\t\t\t\$(this).val(\$(this).data('selectLabel'));
\t\t\t\t\t\t\$('#' + CSS.escape(id)).val(\$(this).data('selectValue'));
\t\t\t\t\t}
\t\t\t\t\treturn false;
\t\t\t\t}
EOT;
goto xLf1K; ggZAd: $output .= ''; goto QPW8a; Vo7oy: mbzNO: goto GJ5MI; Vywi4: if ($item['type'] == 'select') { goto dYuT2; } goto uTxR_; t4jTX: if (!$alterado) { goto dtfiq; } goto Xp7Ml; Z_jaG: $output = "<input type=\"text\" {$options} class=\"campo_input_datahora itemForm itemForm-{$item['name']} {$item['class']}\" />"; goto tQfoB; rk2HK: $output .= '<span id=\'' . $item['name'] . '_refreshBtn' . '\' class="ui-icon ui-icon-refresh" ' . $selectIconStyle . ' onclick="recarregarCampo(\'' . $g['divId'] . '\', \'' . $item['name'] . '\', \'' . $r_campoRefreshVars . '\');">recarregar opções</span>'; goto OWrU7; w2EcB: if ($item['type'] == 'pesquisaGrid') { goto KQcQi; } goto f8Obi; hRlIR: goto rIic2; goto KRREQ; PmMhw: } public function formularioID() { $code = '<input type="hidden" name="' . $this->formNome . '[internal_sent]" value="1">'; return $code; } public function processarFormulario($tabela = '', $camposIndividuais = false) { goto vhGVj; k3DZb: if (!$g['debug']) { goto SSfW9; } goto TBKii; A8_CM: biGhD: goto s7xP_; y1djg: return false; goto UINAA; p0lnT: IIIR3: goto f8cZH; lGKil: SSfW9: goto fUTKO; TBKii: $this->verificarCamposQueNaoExistem($tabela); goto oU1mN; ZXCn_: if (!($camposIndividuais && count($camposValidos) <= 0)) { goto biGhD; } goto T52sa; hHTa1: $tmpValidation = 2; goto Fx0NH; jlhpv: foreach ($this->formArray['campos'] as $tab => $tabCampos) { goto rxJud; FT1Dg: aW7_z: goto GlZPp; rxJud: foreach ($tabCampos as $id => $item) { goto rJdt4; o5wPB: k3g_r: goto EJbjY; EJbjY: VVUf1: goto Fg4z8; rJdt4: $this->formArrayValues[$id] = isset($sent[$item['name']]) ? $sent[$item['name']] : null; goto u29TR; u29TR: if (!($camposIndividuais && $sent[$item['name']] == '1' && substr($item['name'], 0, 2) == '__')) { goto k3g_r; } goto I8_P3; I8_P3: $camposValidos[substr($item['name'], 2)] = true; goto o5wPB; Fg4z8: } goto m02TW; m02TW: CK_o7: goto FT1Dg; GlZPp: } goto PkpQC; wWXgi: $this->tabela = $tabela; goto k3DZb; f8cZH: return $tmpValidation; goto IoiQ7; UINAA: X0zE4: goto hHTa1; vhGVj: global $g, $form_editor; goto wWXgi; s7xP_: foreach ($this->formArray['campos'] as $tab => $tabCampos) { goto jTUpx; Uvyd3: if (!file_exists($arquivo)) { goto BxUgV; } goto yeuvC; kKb0M: $formEditorData = json_decode($form_editor[$tab], true); goto KJL5j; mJOcj: if (!(!$g['modoDesenvolvedor'] && file_exists($arquivoApps))) { goto PnfEV; } goto qFwSj; b3mei: if (!empty($formEditorData)) { goto G7tEX; } goto kKb0M; f50Wl: GKVUJ: goto ziJf0; jTUpx: if (isset($this->formEditorCache[$tab])) { goto LwKan; } goto Brf86; r_uvF: $arquivoAppsEditor = Apps::getDirEditor() . 'personalizacaoForm/' . 'formEditorData-' . str_replace('/', '-', trim($g['r_'], '/')) . "-{$tab}"; goto fl0bt; mvX5V: if (!(strpos($arquivo, "sistema-personalizacaoGrid-form") !== false)) { goto tW9wQ; } goto kzsi3; tC5Zq: LwKan: goto wYoiK; AeIyL: $this->formEditorCache[$tab] = $formEditorData; goto s5h37; oQ8jt: if ($formEditorUtilizado) { goto IwQ98; } goto H9qzN; DCVIB: tW9wQ: goto YmHgH; zdiWf: $formEditorData_ = json_decode($form_editor[$tab], true); goto PtaaW; flxu6: $formEditorUtilizado = false; goto Uvyd3; k3q3j: $arquivoApps = Apps::getDirAtivos() . 'personalizacaoForm/' . 'formEditorData-' . str_replace('/', '-', trim($g['r_'], '/')) . "-{$tab}"; goto r_uvF; yeuvC: $formEditorData = json_decode(file_get_contents($arquivo), true); goto iyLDN; Z0hoT: $arquivo = $arquivoIde; goto mJOcj; Wobdo: G7tEX: goto zdiWf; i6yRf: wMgQD: goto Eb9ng; IAj2U: BxUgV: goto wY90C; frdZE: KpbwH: goto mvX5V; v753g: $arquivo = $arquivoAppsEditor; goto f50Wl; rD3_c: ETc8c: goto bC71I; kzsi3: $arquivo = ''; goto DCVIB; YmHgH: $formEditorData = array(); goto flxu6; bC71I: $formEditorUtilizado = true; goto UGtsH; UD9qx: IwQ98: goto AeIyL; qFwSj: $arquivo = $arquivoApps; goto rGaIg; wYoiK: foreach ($tabCampos as $id => $item) { goto Do_Q4; QZA0l: c5atu: goto aDcr9; tWY2c: TuyAW: goto ldvc3; yxTcW: if ($item['callbackRestricao']) { goto GISfX; } goto MAFcW; VRZZn: VZwbU: goto UEInu; aPTCA: $restricao = $g['conf']->restricaoIncluirUpdate($toCheck, $this->campoValorEnviado($id), $this->formArrayValues, $this->acaoId); goto TAcZq; vEome: RF8df: goto UFE2L; FLhH7: xungE: goto H3sxv; Do_Q4: if (!($item['type'] != 'file' && !isset($sent[$item['name']]) && $item['type'] != 'button' && $item['type'] != 'submit' && $item['type'] != 'enviarForm' || $camposIndividuais == true && !isset($camposValidos[$item['name']]))) { goto duJOo; } goto f_bji; oytuI: duJOo: goto KFLUA; i3G09: HdpJp: goto zHbyX; KFLUA: if (!($item['type'] != 'hidden' && $this->formEditorCache[$tab] && (!isset($this->formEditorCache[$tab][$id]) || isset($this->formEditorCache[$tab][$id]['hidden']) && $this->formEditorCache[$tab][$id]['hidden']))) { goto HdpJp; } goto IRY3W; RU2SF: $this->formArray['campos'][$tab][$id]['validation'] = false; goto zCtl5; MAFcW: if ($restricao === true) { goto Si7PY; } goto RU2SF; UEInu: GNMAf: goto bxzSG; IRY3W: goto xungE; goto i3G09; Ex_r9: $toCheck = $item['restricao']; goto tWY2c; M375F: if ($callbackRestricaoRetorno && $callbackRestricaoRetorno !== true) { goto RF8df; } goto GoGiM; Yq7Wb: if (!($item['type'] == 'file')) { goto ME6nZ; } goto TVt9M; TVt9M: $this->formArray['campos'][$tab][$id]['validation'] = empty($this->formArray['campos'][$tab][$id]['processed_value']['error']) ? true : false; goto zdJDF; GoGiM: $this->formArray['campos'][$tab][$id]['validation'] = true; goto byxHI; APuwp: goto c5atu; goto Dkbmk; WhkcG: GISfX: goto dBaiP; zCtl5: $this->formArray['error_msg'][$id] = $restricao; goto APuwp; K67r2: $tmpValidation = !$this->formArray['campos'][$tab][$id]['validation'] ? 1 : $tmpValidation; goto FLhH7; NYQ3u: eZqzz: goto v29QG; jEpqv: switch ($item['type']) { case 'radio': goto ltCXy; MSQvU: NXplu: goto fAHPi; fAHPi: goto GNMAf; goto Z3NF8; ltCXy: if (!(!isset($item['reload']) || $item['reload'])) { goto NXplu; } goto iRmj9; iRmj9: $this->formArray['campos'][$tab][$id]['checked'] = $sent[$item['name']] == $item['value'] ? true : false; goto MSQvU; Z3NF8: case 'checkbox': goto KCuEm; AuXbx: goto GNMAf; goto BtQ4P; hMmq5: ZgdLK: goto AuXbx; KCuEm: if (!(!isset($item['reload']) || $item['reload'])) { goto ZgdLK; } goto hlC89; hlC89: $this->formArray['campos'][$tab][$id]['checked'] = isset($sent[$item['name']]) && $sent[$item['name']] == 'on' ? true : false; goto hMmq5; BtQ4P: case 'select': goto ry9Se; XyAJf: @($this->formArray['campos'][$tab][$id]['value'] = !isset($item['reload']) || $item['reload'] ? $sent[$item['name']] : (isset($item['value']) ? $item['value'] : '')); goto VvqQZ; bWekR: lMPK6: goto XyAJf; XzEV9: gFhiQ: goto bWekR; ry9Se: if (!(!isset($item['reload']) || $item['reload'])) { goto lMPK6; } goto jrzrS; VvqQZ: goto GNMAf; goto SLD8K; jrzrS: foreach ($item['select'] as $optionKey => $option) { $this->formArray['campos'][$tab][$id]['select'][$optionKey]['selected'] = isset($sent[$item['name']]) && $sent[$item['name']] == (isset($option['value']) ? $option['value'] : false) ? true : false; ld93I: } goto XzEV9; SLD8K: case 'file': goto OwCH1; Sy2Vq: if (!($_FILES[$this->formNome]['error'][$item['name']] > 0)) { goto FG4nQ; } goto OEKJF; OwCH1: $error_msg = ''; goto gRetT; XpvUR: R8kh7: goto dYYdA; IBrKj: bhNkQ: goto jHNGB; CbcaH: FG4nQ: goto OeULq; OeULq: goto bhNkQ; goto ALNTc; dYYdA: t2VxV: goto CbcaH; ALNTc: i98d5: goto IBrKj; OEKJF: switch ($_FILES[$this->formNome]['error'][$item['name']]) { case UPLOAD_ERR_INI_SIZE: case UPLOAD_ERR_FORM_SIZE: $error_msg = 'Arquivo é maior que o limite permitido.'; goto t2VxV; case UPLOAD_ERR_PARTIAL: $error_msg = 'Erro ao enviar o arquivo. Por favor, tente novamente.'; goto t2VxV; case UPLOAD_ERR_NO_FILE: goto t2VxV; case UPLOAD_ERR_INI_SIZE: $error_msg = 'Arquivo é maior que o limite permitido.'; goto t2VxV; case UPLOAD_ERR_NO_TMP_DIR: $error_msg = 'Erro interno. Por favor, contate o suporte. Erro: Diretório temporário não encontrado.'; goto t2VxV; case UPLOAD_ERR_CANT_WRITE: $error_msg = 'Erro interno. Por favor, contate o suporte. Erro: Não foi possivel gravar no diretório temporário.'; goto t2VxV; case UPLOAD_ERR_EXTENSION: $error_msg = 'Erro interno. Por favor, contate o suporte. Erro: UPLOAD_ERR_EXTENSION'; goto t2VxV; } goto XpvUR; gRetT: if (!isset($_FILES[$this->formNome])) { goto i98d5; } goto Sy2Vq; jHNGB: $this->formArray['campos'][$tab][$id]['processed_value'] = array('name' => isset($_FILES[$this->formNome]) ? $_FILES[$this->formNome]['name'][$item['name']] : '', 'type' => isset($_FILES[$this->formNome]) ? $_FILES[$this->formNome]['type'][$item['name']] : '', 'tmp_name' => isset($_FILES[$this->formNome]) ? $_FILES[$this->formNome]['tmp_name'][$item['name']] : '', 'error' => empty($error_msg) ? false : (isset($_FILES[$this->formNome]) ? $_FILES[$this->formNome]['error'][$item['name']] : UPLOAD_ERR_NO_FILE), 'error_msg' => $error_msg, 'size' => isset($_FILES[$this->formNome]) ? $_FILES[$this->formNome]['size'][$item['name']] : 0); goto Prkb4; Prkb4: goto GNMAf; goto hFQRw; hFQRw: case 'pesquisa': goto l3Lgd; cNEMo: $this->formArray['campos'][$tab][$id]['value'] = $retorno['value']; goto uAZJX; YJ04h: $sqlExtra = "({$tmp_campo} = '{$this->formArray['campos'][$tab][$id]['processed_value']}')"; goto HrEf3; T1jzK: $tmp_valor = $this->formArray['campos'][$tab][$id]['pesquisa'][2]; goto cyArl; l3Lgd: $tmp_tabela = $this->formArray['campos'][$tab][$id]['pesquisa'][0]; goto fVz3w; fVz3w: $tmp_campo = $this->formArray['campos'][$tab][$id]['pesquisa'][1]; goto T1jzK; HrEf3: $extra = gInsertExtraWhere($extra, $sqlExtra); goto hAKS1; uAZJX: $this->formArray['campos'][$tab][$id]['value_Pesquisa'] = $retorno['title']; goto qNCgv; hAKS1: $retorno = gGeraSelect($tmp_tabela, $tmp_campo, $tmp_valor, $extra); goto z8sOg; cyArl: $extra = isset($this->formArray['campos'][$tab][$id]['pesquisa'][3]) ? $this->formArray['campos'][$tab][$id]['pesquisa'][3] : 'WHERE (1=1)'; goto YJ04h; qNCgv: goto GNMAf; goto igXwz; z8sOg: $retorno = isset($retorno[0]) ? $retorno[0] : array('title' => '', 'value' => ''); goto cNEMo; igXwz: case 'pesquisaGrid': goto MoOIs; Kdzov: $extra = $this->formArray['campos'][$tab][$id]['pesquisaGrid'][3]; goto rlhL7; gJYE_: goto GNMAf; goto zf0kg; jk48g: $retorno = gGeraSelect($tmp_tabela, $tmp_campo, $tmp_campo, $sqlExtra); goto nKfI2; d7jIP: $tmp_campo = $this->formArray['campos'][$tab][$id]['pesquisaGrid'][1]; goto Kdzov; QoVoD: $this->formArray['campos'][$tab][$id]['value'] = $retorno['value']; goto Fkt8N; MoOIs: $tmp_tabela = $this->formArray['campos'][$tab][$id]['pesquisaGrid'][0]; goto d7jIP; rlhL7: $sqlExtra = gInsertExtraWhere($extra, "{$tmp_campo} = '{$this->formArray['campos'][$tab][$id]['processed_value']}'"); goto jk48g; nKfI2: $retorno = isset($retorno[0]) ? $retorno[0] : array('title' => '', 'value' => ''); goto QoVoD; Fkt8N: $this->formArray['campos'][$tab][$id]['value_Pesquisa'] = $retorno['title']; goto gJYE_; zf0kg: case 'button': goto GNMAf; case 'submit': case 'enviarForm': goto GNMAf; default: goto ApBKP; ApBKP: isset($sent[$item['name']]) or $sent[$item['name']] = ''; goto E6T_B; iDAaJ: goto GNMAf; goto B0mWN; E6T_B: $this->formArray['campos'][$tab][$id]['value'] = !isset($item['reload']) || $item['reload'] ? $sent[$item['name']] : (isset($item['value']) ? $item['value'] : ''); goto iDAaJ; B0mWN: } goto VRZZn; ko2sM: V5xy1: goto Ay1Q7; bxzSG: $toCheck = $id; goto AsliN; zdJDF: $this->formArray['error_msg'][$id] = $this->formArray['campos'][$tab][$id]['processed_value']['error_msg']; goto sjzRS; TAcZq: goto LjdPz; goto ko2sM; Azhhg: $this->formArray['campos'][$tab][$id]['validation'] = true; goto QZA0l; AsliN: if (!(isset($item['restricao']) && $item['restricao'])) { goto TuyAW; } goto Ex_r9; Ys45M: LjdPz: goto yxTcW; dBaiP: $callbackRestricaoRetorno = Personalizacoes::carregarExecutarCallback($item['callbackRestricao'], array('campoAtual' => $this->campoValorEnviado($id), 'todosCampos' => $this->formArrayValues)); goto M375F; POWOC: $this->formArray['campos'][$tab][$id]['processed_value'] = isset($sent[$item['name']]) ? $sent[$item['name']] : null; goto jEpqv; Dkbmk: Si7PY: goto Azhhg; sjzRS: ME6nZ: goto K67r2; f_bji: unset($this->formArray['campos'][$tab][$id]); goto w07OK; UFE2L: $this->formArray['campos'][$tab][$id]['validation'] = false; goto dZsfH; aDcr9: goto RlCX2; goto WhkcG; ldvc3: if (isset($item['restricao']) && !$item['restricao']) { goto V5xy1; } goto aPTCA; v29QG: RlCX2: goto Yq7Wb; dZsfH: $this->formArray['error_msg'][$id] = $callbackRestricaoRetorno; goto NYQ3u; w07OK: goto xungE; goto oytuI; byxHI: goto eZqzz; goto vEome; zHbyX: $this->formArray['campos'][$tab][$id]['processed'] = true; goto POWOC; Ay1Q7: $restricao = true; goto Ys45M; H3sxv: } goto DVLx8; fl0bt: $arquivoGlobal = "{$g['pathDados']}personalizacaoForm/" . 'formEditorData-' . str_replace('/', '-', trim($g['r_'], '/')) . "-{$tab}"; goto Z0hoT; PtaaW: $formEditorData = array_merge($formEditorData_, $formEditorData); goto rD3_c; s5h37: hlwkf: goto tC5Zq; H9qzN: $this->formEditorCache[$tab] = false; goto IKojY; rGaIg: PnfEV: goto RhTTU; IKojY: goto hlwkf; goto UD9qx; KJL5j: goto ETc8c; goto Wobdo; KsyCR: $arquivo = $arquivoGlobal; goto frdZE; Brf86: $arquivoIde = 'ide/personalizacaoForm/' . 'formEditorData-' . str_replace('/', '-', trim($g['r_'], '/')) . "-{$tab}"; goto k3q3j; ziJf0: if (!(!$g['modoDesenvolvedor'] && !$g['modoDesenvolvedorApp'] && file_exists($arquivoGlobal))) { goto KpbwH; } goto KsyCR; DVLx8: yyOKy: goto i6yRf; iyLDN: $formEditorUtilizado = true; goto IAj2U; wY90C: if (!(isset($form_editor) && isset($form_editor[$tab]) && !empty($form_editor[$tab]))) { goto UMeO5; } goto b3mei; RhTTU: if (!($g['modoDesenvolvedorApp'] && file_exists($arquivoAppsEditor))) { goto GKVUJ; } goto v753g; UGtsH: UMeO5: goto oQ8jt; Eb9ng: } goto p0lnT; oU1mN: goto AxG1E; goto lGKil; T52sa: $tmpValidation = 3; goto A8_CM; PkpQC: CoxDZ: goto ZXCn_; fUTKO: $this->excluirCamposQueNaoExistem($tabela); goto gf2R0; Qa2wP: $camposValidos = array(); goto jlhpv; gf2R0: AxG1E: goto P2cq0; P2cq0: if ($this->verificaSeFoiEnviado()) { goto X0zE4; } goto y1djg; Fx0NH: $sent = $_REQUEST[$this->formNome]; goto Qa2wP; IoiQ7: } public function carregarValoresCampos($table, $campoPk, $campoValor, $sqlExtra = '', $apenasTodosCampos = false) { goto fkMll; I10gu: $sql = "DESC {$table}A;"; goto b6e5N; l0XML: $sql = <<<EOT
SELECT * FROM {$table} 
{$tabelaIrma}
WHERE {$campoPk} = '{$campoValor}' {$sqlExtra} 
EOT;
goto gd5PX; Dp6Sr: goto FQWw2; goto EqYPe; ztB_X: $fields = array(); goto u0onm; O90k_: nqXmL: goto s0bk7; spgRg: xAJrZ: goto emyxI; JiD1V: zMF5R: goto d7Nuu; yzzuI: $this->formArrayTodosCampos = $row; goto yoa2e; de100: goto wQRuA; goto M4BKt; E39g5: return gGeraAlertMsg('Erro em operação no banco de dados', "Por favor, contate o suporte técnico.<br/><br/>Erro:<br/>" . mysql_error() . "<br/><br/>SQL:<br/>{$sql}"); goto CcaHl; id9_m: goto xAJrZ; goto B765C; gd5PX: $result = mysql_query($sql); goto nNXGh; y5vEy: return gGeraAlertMsg('$form->carregarValoresCampos()', $erro); goto spgRg; hGV8M: return gGeraAlertMsg('Erro em operação no banco de dados', "Por favor, contate o suporte técnico.<br/><br/>Erro:<br/>" . mysql_error() . "<br/><br/>SQL:<br/>{$sql}"); goto Ia1nk; xZ19l: return true; goto id9_m; CcaHl: oYiXq: goto ztB_X; O4CBJ: HfV4e: goto RkzpN; NDggr: $tabelaIrma = $tabelaIrma ? "LEFT JOIN {$table}A ON {$table}A_{$table}_Id = {$table}_Id" : ''; goto l0XML; d7Nuu: if (!$apenasTodosCampos) { goto nqXmL; } goto yzzuI; LKCUp: if (!($apenasTodosCampos && $r_todosCampos)) { goto UsDSw; } goto mk1UT; pJHkN: foreach ($row as $key => $value) { goto CIpp_; ZhytR: PxkYC: goto vDE1Q; eg1jB: goto PxkYC; goto EApuV; cNe8s: foreach ($valueJson as $key2 => $value2) { $row["{$key}->{$key2}"] = $value2; PmAwy: } goto ZjrSd; CIpp_: if (preg_match('/^\\{.*\\}$/mi', $value)) { goto IsZoG; } goto eg1jB; Fi_y_: if (is_object($valueJson)) { goto uP7Ge; } goto WqrFv; EApuV: IsZoG: goto dFLD_; ZjrSd: nd160: goto ZhytR; WqrFv: goto PxkYC; goto jb6tN; jb6tN: uP7Ge: goto cNe8s; dFLD_: $valueJson = json_decode($value); goto Fi_y_; vDE1Q: } goto JiD1V; eRmya: $row = mysql_fetch_assoc($result); goto pJHkN; M4BKt: Q3Nik: goto I10gu; wJLnh: $fields[$row['Field']] = 1; goto de100; Lf9ON: $fields[$row['Field']] = 1; goto Dp6Sr; DEPgr: fH0r8: goto z73uD; ytvSd: return; goto U9H0H; ro0PR: if (mysql_error()) { goto fH0r8; } goto dwLwo; RkzpN: if (!empty($erro)) { goto Sch0V; } goto xZ19l; b6e5N: $result = mysql_query($sql); goto ro0PR; yoa2e: return; goto O90k_; JGy9t: $sql = "DESC {$table};"; goto WFNZb; u0onm: wQRuA: goto WXaxQ; Cku73: $tabelaIrma = mysql_fetch_array(mysql_query($sql)); goto NDggr; fkMll: global $g, $r_todosCampos; goto LKCUp; JllA_: if (!mysql_error()) { goto oYiXq; } goto E39g5; B765C: Sch0V: goto y5vEy; mk1UT: $this->formArrayTodosCampos = $r_todosCampos; goto ytvSd; hu0EM: if (!($row = mysql_fetch_assoc($result))) { goto uLqQz; } goto Lf9ON; z73uD: $sql = "SHOW TABLES LIKE '{$table}A'"; goto Cku73; s0bk7: $erro = ''; goto hTxRf; EqYPe: uLqQz: goto DEPgr; WFNZb: $result = mysql_query($sql); goto JllA_; Ia1nk: kFFEu: goto eRmya; hTxRf: foreach ($this->formArray['campos'] as $tab => $tabCampos) { goto BX5P_; c1k1n: iyTZT: goto l7_y0; BX5P_: foreach ($tabCampos as $id => $item) { goto E1kW_; QMNtJ: juQCu: goto oZaXz; EPgLI: $this->formArray['campos'][$tab][$id]['value'] = $retorno['value']; goto WFY28; r9cZz: VctHH: goto aKZvq; yue_c: foreach ($this->formArrayTodosCampos as $key => $value) { $extra = str_replace("[[{$key}]]", $value, $extra); qmFDV: } goto IqY5d; UXJxH: $retorno = isset($retorno[0]) ? $retorno[0] : array('title' => '', 'value' => ''); goto EPgLI; lWBnU: x1_5F: goto bbAkf; HepFX: $tmp_campo = $this->formArray['campos'][$tab][$id]['pesquisa'][1]; goto m4Ikp; dopVV: T3uq1: goto YHiPW; pjQts: $this->formArray['campos'][$tab][$id]['value_Pesquisa'] = $retorno['title']; goto Mf4FG; UwjYy: goto Xgo_M; goto lWBnU; meDBs: $data = explode('-', $data[0]); goto BM8VA; N8I_t: SzShL: goto aWd61; p5Gp_: if ($this->formArray['campos'][$tab][$id]['type'] == 'select') { goto qaN6n; } goto Or8AG; IfDAe: $casas = $casasConf[1]; goto ufRuU; ttDVo: if ($this->formArray['campos'][$tab][$id]['type'] == 'dataHora') { goto r2JuL; } goto Yqw8h; hXDnR: foreach ($this->formArray['campos'][$tab][$id]['select'] as &$option) { goto gJl21; ThyV6: IAmwV: goto Gs1GV; Gs1GV: mH0cT: goto ohmJV; aN603: aaPJD: goto QnOWv; cPrdF: goto IAmwV; goto aN603; gJl21: if (isset($option['value']) && $option['value'] == $row[$id]) { goto aaPJD; } goto R__am; R__am: unset($option['selected']); goto cPrdF; qV7UL: $option['selected'] = true; goto ThyV6; QnOWv: $found = true; goto qV7UL; ohmJV: } goto InwRB; uGOXk: $this->formArray['campos'][$tab][$id]['value'] = $row[$id]; goto emtva; kVgvj: auRLT: goto iyP4J; wjf8g: $tmp_tabela = $this->formArray['campos'][$tab][$id]['pesquisa'][0]; goto HepFX; oZaXz: goto Pvy3K; goto T7hSh; Gv_Gd: if (empty($row[$id])) { goto T3uq1; } goto IYXLv; fI3SA: $idCampoJson = explode('->', $id); goto RRbTC; KR6xY: lQnE6: goto H917F; x5J2C: IstR_: goto dpqis; qgJon: if (!isset($fields[$idCampoJson ? $idCampoJson : $id]) && !isset($this->formArray['campos'][$tab][$id]['reject'])) { goto LswhI; } goto as5Ca; emtva: Y9eZG: goto hRdNF; xIXCD: $this->formArray['campos'][$tab][$id]['value'] = @number_format($row[$id], $casas, ',', '.'); goto KR6xY; aOcQy: Hx8PE: goto PTZNk; aUXbe: $retorno = isset($retorno[0]) ? $retorno[0] : array('title' => '', 'value' => ''); goto lxsBw; eChsc: $retorno = gGeraSelect($tmp_tabela, $tmp_campo, $tmp_valor, $extra, false, false, $formato); goto UXJxH; hDq6A: LswhI: goto U6vGY; evXsb: if ($this->formArray['campos'][$tab][$id]['type'] == 'tempo') { goto Hx8PE; } goto TW1qV; OPDmd: $dataHora = explode(' ', $row[$id]); goto pwKIU; WFY28: $this->formArray['campos'][$tab][$id]['value_Pesquisa'] = $retorno['title']; goto x5J2C; qfJ33: goto iSlOj; goto aOcQy; rf4am: @($this->formArray['campos'][$tab][$id]['value'] = "{$data[2]}/{$data[1]}/{$data[0]} {$hora}"); goto BD7mR; ulacu: $extra = gInsertExtraWhere($extra, $sqlExtra); goto yue_c; BM8VA: @($this->formArray['campos'][$tab][$id]['value'] = "{$data[2]}/{$data[1]}/{$data[0]}"); goto QMNtJ; ZjXiL: goto IstR_; goto vyiBs; pwKIU: $data = explode('-', $dataHora[0]); goto g7E5F; PTZNk: $row[$id] = trim($row[$id]); goto Gv_Gd; xXABD: $casasConf = $g['conf']->camposTipo($id); goto icvUY; IMLBb: goto lQnE6; goto kVgvj; hRdNF: Pvy3K: goto bz5Is; Or8AG: if ($this->formArray['campos'][$tab][$id]['type'] == 'data') { goto SzShL; } goto ttDVo; RRbTC: $idCampoJson = trim($idCampoJson[0]); goto y8EKm; R9mJy: goto juQCu; goto N8I_t; U6vGY: $erro .= "<br/>Campo {$id} não existe na tabela {$table}.<br/>"; goto r9cZz; zE40C: $extra = $this->formArray['campos'][$tab][$id]['pesquisaGrid'][3]; goto iRajM; InwRB: rwQMt: goto FWH_M; icvUY: if (!$casasConf) { goto T2MFo; } goto IfDAe; PUmQA: $sqlExtra = "({$tmp_campo} = '{$row[$id]}')"; goto ulacu; lxsBw: $this->formArray['campos'][$tab][$id]['value'] = $retorno['value']; goto pjQts; Zl_od: $extra = isset($this->formArray['campos'][$tab][$id]['pesquisa'][3]) ? $this->formArray['campos'][$tab][$id]['pesquisa'][3] : 'WHERE (1=1)'; goto vDwqq; YEflV: ZQakR: goto qfJ33; T7hSh: qaN6n: goto hXDnR; y8EKm: I4hIp: goto qgJon; m4Ikp: $tmp_valor = $this->formArray['campos'][$tab][$id]['pesquisa'][2]; goto Zl_od; aKZvq: Xgo_M: goto z1qxD; dpqis: goto VctHH; goto hDq6A; G9H4K: H7LcR: goto Q1QSt; wvgy2: if (!(strpos($id, '->') !== false)) { goto I4hIp; } goto fI3SA; iRajM: $sqlExtra = gInsertExtraWhere($extra, "{$tmp_campo} = '{$row[$id]}'"); goto oZP2x; vyiBs: UbsYU: goto wjf8g; iyP4J: $casas = isset($this->formArray['campos'][$tab][$id]['casasDecimais']) ? $this->formArray['campos'][$tab][$id]['casasDecimais'] : 2; goto xXABD; SLWgg: if ($this->formArray['campos'][$tab][$id]['type'] == 'pesquisaGrid') { goto H7LcR; } goto p5Gp_; YHiPW: iSlOj: goto IMLBb; bz5Is: goto FSGZh; goto G9H4K; TW1qV: if (!isset($row[$id])) { goto ZQakR; } goto JHoOm; bbAkf: if ($this->formArray['campos'][$tab][$id]['type'] == 'pesquisa') { goto UbsYU; } goto SLWgg; JHoOm: $this->formArray['campos'][$tab][$id]['value'] = $row[$id]; goto YEflV; FWH_M: if (!isset($row[$id])) { goto Y9eZG; } goto uGOXk; vDwqq: $formato = isset($this->formArray['campos'][$tab][$id]['pesquisa'][4]) ? $this->formArray['campos'][$tab][$id]['pesquisa'][4] : false; goto PUmQA; o3tbP: $this->formArray['campos'][$tab][$id]['value'] = $temp[0] . ':' . $temp[1]; goto dopVV; aWd61: $data = explode(' ', $row[$id]); goto meDBs; H917F: goto k0epP; goto KPjJQ; BD7mR: k0epP: goto R9mJy; Mf4FG: FSGZh: goto ZjXiL; IYXLv: $temp = explode(':', $row[$id]); goto o3tbP; g7E5F: $hora = isset($dataHora[1]) ? $dataHora[1] : '00:00:00'; goto rf4am; Q23vW: $tmp_campo = $this->formArray['campos'][$tab][$id]['pesquisaGrid'][1]; goto zE40C; E1kW_: $idCampoJson = false; goto wvgy2; Yqw8h: if ($this->formArray['campos'][$tab][$id]['type'] == 'moeda') { goto auRLT; } goto evXsb; Q1QSt: $tmp_tabela = $this->formArray['campos'][$tab][$id]['pesquisaGrid'][0]; goto Q23vW; KPjJQ: r2JuL: goto OPDmd; as5Ca: if (!(isset($this->formArray['campos'][$tab][$id]['reject']) && $this->formArray['campos'][$tab][$id]['reject'])) { goto x1_5F; } goto UwjYy; ufRuU: T2MFo: goto xIXCD; IqY5d: NRaNM: goto eChsc; oZP2x: $retorno = gGeraSelect($tmp_tabela, $tmp_campo, $tmp_campo, $sqlExtra); goto aUXbe; z1qxD: } goto c1k1n; l7_y0: n2i2Y: goto Tz_t0; Tz_t0: } goto O4CBJ; WXaxQ: if (!($row = mysql_fetch_assoc($result))) { goto Q3Nik; } goto wJLnh; U9H0H: UsDSw: goto JGy9t; nNXGh: if (!mysql_error()) { goto kFFEu; } goto hGV8M; dwLwo: FQWw2: goto hu0EM; emyxI: } public function excluirCamposQueNaoExistem($table) { goto kHlfK; NMiyZ: $fields[$row['Field']] = 1; goto XRUtP; YDBme: NdxvM: goto X2ggp; dZPdY: $fields = array(); goto YDBme; L5p7w: $erro = ''; goto xj4FC; Fslc8: if (!mysql_error()) { goto bKgsI; } goto hvgMy; X2ggp: if (!($row = mysql_fetch_assoc($result))) { goto COTyd; } goto NMiyZ; xj4FC: foreach ($this->formArray['campos'] as $tab => $tabCampos) { goto vI8rK; uKeg1: ef2o4: goto iQpXE; iQpXE: sp19f: goto Bytv8; vI8rK: foreach ($tabCampos as $id => $item) { goto aIp8q; sUpPU: twfeL: goto cinvl; cinvl: HxLwa: goto NJKKZ; T8Tf2: unset($this->formArray['campos'][$tab][$id]); goto sUpPU; aIp8q: if (!(!isset($fields[$id]) && !isset($this->formArray['campos'][$tab][$id]['reject']))) { goto twfeL; } goto T8Tf2; NJKKZ: } goto uKeg1; Bytv8: } goto OWRoY; IIr9Z: $result = mysql_query($sql); goto Fslc8; hvgMy: return gGeraAlertMsg('Erro em operação no banco de dados', "Por favor, contate o suporte técnico.<br/><br/>Erro:<br/>" . mysql_error() . "<br/><br/>SQL:<br/>{$sql}"); goto xWO5A; XRUtP: goto NdxvM; goto O_HDw; O_HDw: COTyd: goto L5p7w; kHlfK: $sql = "DESC {$table};"; goto IIr9Z; xWO5A: bKgsI: goto dZPdY; OWRoY: eXI_k: goto ywnLZ; ywnLZ: } public function verificarCamposQueNaoExistem($table) { goto sMfc7; ghqQf: return true; goto i6931; i6931: goto hg6B3; goto eqh1l; oWASi: hg6B3: goto EKJYa; aI97C: return gGeraAlertMsg('Erro em operação no banco de dados', "Por favor, contate o suporte técnico.<br/><br/>Erro:<br/>" . mysql_error() . "<br/><br/>SQL:<br/>{$sql}"); goto csPDd; epqT3: WcFez: goto EMIvE; UsRdS: wq0C6: goto KuTyD; eqh1l: ugPEp: goto sNPG4; LGJbu: $result = mysql_query($sql); goto IOrMg; mfH20: $fields = array(); goto kD4K9; T7RCe: if (!($row = mysql_fetch_assoc($result))) { goto WcFez; } goto a73te; jmFqC: goto dre2u; goto epqT3; sMfc7: if (!empty($table)) { goto Qhc5h; } goto l6z0n; kD4K9: dre2u: goto T7RCe; csPDd: Jy9Co: goto mfH20; ao8zE: foreach ($this->formArray['campos'] as $tab => $tabCampos) { goto odAiZ; ouCOr: FRk1t: goto VAltO; rzbIi: gCKW7: goto ouCOr; odAiZ: foreach ($tabCampos as $id => $item) { goto CJq5V; iS064: Mo7wj: goto w_AfP; HbjD6: $erro .= "<br/>Campo {$id} não existe na tabela {$table}.<br/>"; goto FAfz6; CJq5V: if (!(!isset($fields[$id]) && !isset($this->formArray['campos'][$tab][$id]['reject']))) { goto FQijS; } goto HbjD6; FAfz6: FQijS: goto iS064; w_AfP: } goto rzbIi; VAltO: } goto UsRdS; IOrMg: if (!mysql_error()) { goto Jy9Co; } goto aI97C; EMIvE: $erro = ''; goto ao8zE; KuTyD: if (!empty($erro)) { goto ugPEp; } goto ghqQf; a73te: $fields[$row['Field']] = 1; goto jmFqC; dM1wd: Qhc5h: goto lv1qi; l6z0n: return false; goto dM1wd; lv1qi: $sql = "DESC {$table};"; goto LGJbu; sNPG4: return gGeraAlertMsg('$form->verificarCamposQueNaoExistem()', $erro); goto oWASi; EKJYa: } public function temErro($id = false) { goto hcjSD; FRDib: goto CiMRD; goto BxOjU; vgEzJ: if (empty($id)) { goto dE57K; } goto gmv9s; kbqTk: BL37t: goto rdcok; rdcok: CiMRD: goto vaoPh; hcjSD: if ($this->verificaSeFoiEnviado()) { goto BO6ih; } goto CAXLk; BxOjU: dE57K: goto pJ23H; gmv9s: return isset($this->formArray['campos'][$this->formTabId[$id]][$id]['validation']) && !$this->formArray['campos'][$this->formTabId[$id]][$id]['validation'] ? true : false; goto FRDib; CAXLk: return false; goto k2dgY; k2dgY: BO6ih: goto vgEzJ; pJ23H: foreach ($this->formArray['campos'] as $tab => $tabCampos) { goto QJ7bo; by6k0: twCZZ: goto PeCqV; QJ7bo: foreach ($tabCampos as $id => $item) { goto JEwX6; yYmCT: IkeOP: goto sUocn; JEwX6: if (!($item['type'] != 'hidden' && $this->formEditorCache[$tab] && (!isset($this->formEditorCache[$tab][$id]) || isset($this->formEditorCache[$tab][$id]['hidden']) && $this->formEditorCache[$tab][$id]['hidden']))) { goto hosMN; } goto iTSkw; sT4yu: hosMN: goto ajMW5; sUocn: bBTMP: goto o52WQ; ajMW5: if ($item['validation']) { goto IkeOP; } goto BniZN; BniZN: return true; goto yYmCT; iTSkw: goto bBTMP; goto sT4yu; o52WQ: } goto Qp3F4; Qp3F4: R_wRv: goto by6k0; PeCqV: } goto kbqTk; vaoPh: } public function pegarErro($id) { goto lpk8P; L5NVq: return false; goto Cn1is; lpk8P: if ($this->verificaSeFoiEnviado()) { goto us7MV; } goto L5NVq; Cn1is: us7MV: goto tQfKC; tQfKC: return isset($this->formArray['campos'][$this->formTabId[$id]][$id]['validation']) && !$this->formArray['campos'][$this->formTabId[$id]][$id]['validation'] ? $this->formArray['error_msg'][$id] : ''; goto vpitN; vpitN: } public function setarErro($id) { goto SjRST; Z0NCi: return true; goto Ib3j4; JUhMO: JCzmz: goto sX_lv; j0_lM: return false; goto JUhMO; SjRST: if ($this->verificaSeFoiEnviado()) { goto JCzmz; } goto j0_lM; sX_lv: $this->formArray['campos'][$this->formTabId[$id]][$id]['validation'] = false; goto Z0NCi; Ib3j4: } public function campoSetarSelect($id, $value) { goto grPkM; grPkM: if (isset($this->formTabId[$id])) { goto pQjho; } goto AGMgQ; Geur0: return true; goto rdm8z; ERUbx: $this->formArray['campos'][$this->formTabId[$id]][$id]['select'] = $value; goto Geur0; AGMgQ: return false; goto DY6Ne; DY6Ne: pQjho: goto ERUbx; rdm8z: } public function campoSetarValor($id, $value, $proccessed = false) { goto mYR1s; nGw3W: return true; goto nyqug; L1tLS: if (isset($this->formArray['campos'][$this->formTabId[$id]][$id]['select'])) { goto bVqt4; } goto oR1l9; ZlutW: W1nXo: goto L1tLS; YSWPQ: return false; goto ZlutW; oR1l9: $this->formArray['campos'][$this->formTabId[$id]][$id]['value'] = $value; goto Qv1da; X89sH: T51je: goto LOahO; wR5pK: nbZLT: goto X89sH; PRCcl: $this->formArray['campos'][$this->formTabId[$id]][$id]['processed_value'] = $value; goto s_KtM; mYR1s: if (isset($this->formTabId[$id])) { goto W1nXo; } goto YSWPQ; LOahO: if (!$proccessed) { goto hN3MM; } goto PRCcl; s_KtM: hN3MM: goto nGw3W; BJEw6: bVqt4: goto mtiDJ; Qv1da: goto T51je; goto BJEw6; mtiDJ: foreach ($this->formArray['campos'][$this->formTabId[$id]][$id]['select'] as &$itemSelect) { goto oDD1Y; xnNTC: j06G8: goto XAuHy; X76b8: goto j06G8; goto Jx_T2; XAuHy: MzAYf: goto mjDN1; Jx_T2: jU6dm: goto Ir1X_; oDD1Y: if ($itemSelect['value'] == $value) { goto jU6dm; } goto pTard; Ir1X_: $itemSelect['selected'] = true; goto xnNTC; pTard: $itemSelect['selected'] = false; goto X76b8; mjDN1: } goto wR5pK; nyqug: } public function campoSetarValorPesquisa($id, $value) { goto EU3sF; EU3sF: if (isset($this->formTabId[$id])) { goto K4WlY; } goto N2HXy; kaJx2: K4WlY: goto HxcVb; XLYZ4: return true; goto XNRW0; N2HXy: return false; goto kaJx2; HxcVb: $this->formArray['campos'][$this->formTabId[$id]][$id]['value_Pesquisa'] = $value; goto XLYZ4; XNRW0: } public function campoValorEnviado($id) { goto q92gV; Fjt6n: return isset($_REQUEST[$this->formNome][$id]) ? $_REQUEST[$this->formNome][$id] : false; goto sZlBK; q92gV: if (isset($this->formTabId[$id])) { goto P9x2p; } goto Fjt6n; sZlBK: P9x2p: goto P_pVK; P_pVK: return isset($this->formArray['campos'][$this->formTabId[$id]][$id]['processed_value']) ? $this->formArray['campos'][$this->formTabId[$id]][$id]['processed_value'] : false; goto rGv5P; rGv5P: } public function campoValor($id) { goto zgxHJ; erud5: return false; goto QVn41; Is70x: return isset($this->formArray['campos'][$this->formTabId[$id]][$id]['value']) ? $this->formArray['campos'][$this->formTabId[$id]][$id]['value'] : null; goto IOv4C; zgxHJ: if (isset($this->formTabId[$id])) { goto WXeWk; } goto erud5; QVn41: WXeWk: goto Is70x; IOv4C: } public function isChecked($id) { return !isset($this->formArray['campos'][$this->formTabId[$id]][$id]['checked']) || !$this->formArray['campos'][$this->formTabId[$id]][$id]['checked'] ? false : true; } public function checkedIt($id) { $this->formArray['campos'][$this->formTabId[$id]][$id]['checked'] = true; return true; } public function isDisabled($id) { return isset($this->formArray['campos'][$this->formTabId[$id]][$id]['disabled']) && $this->formArray['campos'][$this->formTabId[$id]][$id]['disabled'] ? true : false; } public function verificaSeFoiEnviado() { return empty($_REQUEST[$this->formNome]['internal_sent']) ? false : true; } public function limpar() { goto EJnue; qA2Fh: $this->processarFormulario($this->tabela); goto vCnqP; EJnue: $this->formArray = $this->formArrayOriginal; goto PIjf9; JJHrJ: unset($_REQUEST[$this->formNome]); goto qA2Fh; PIjf9: $this->formArrayValues = $this->formArrayValuesOriginal; goto JJHrJ; vCnqP: } }