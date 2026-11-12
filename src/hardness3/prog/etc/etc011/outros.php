<?php
namespace hardness;

switch ($g['r'][3]) {
	case 'etc011DashboardPersonalizado':
		require_once 'dashboard-personalizado/config.php';
		require_once 'dashboard-personalizado/access.php';
		if (!dashboardPersonalizadoHabilitado($g['empresaAtual'])) {
			echo gGeraAlertMsg('Dashboard indisponível', 'Não existe uma personalização ativa para a empresa atual.');
			break;
		}
		if (!dashboardPersonalizadoPodeAcessar(array('usuario' => (int) $g['usuarioAtual'], 'perfil' => (int) $g['perfilUsuario'], 'root' => !empty($g['isRoot']), 'diretor' => isset($g['nivelAlcada']) && (int) $g['nivelAlcada'] === 4), $g['empresaAtual'])) {
			echo gGeraAlertMsg('Dashboard indisponível', 'Seu usuário ainda não possui indicadores liberados neste dashboard.');
			break;
		}
		$dashboardPersonalizadoToken = dashboardPersonalizadoCriarTokenSessao();
		if ($dashboardPersonalizadoToken === false) {
			echo gGeraAlertMsg('Dashboard indisponível', 'Não foi possível autorizar a nova janela. Atualize a página e tente novamente.');
			break;
		}
		$dashboardPersonalizadoUrl = json_encode('/hardness3/dashboard-personalizado/#session=' . $dashboardPersonalizadoToken);
		echo "<script>window.open({$dashboardPersonalizadoUrl}, '_blank', 'noopener,noreferrer');</script>";
	break;

	case 'etc011OutrosGrafico':
		echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" onclick="graficoBarra(); return false;">Barra</button>
				<button type="button" onclick="graficoPizza(); return false;">Pizza</button>
				<button type="button" onclick="graficoLinha(); return false;">Linha</button>
				<button type="button" onclick="graficoArea(); return false;">Area</button>
				<button type="button" onclick="graficoRadar(); return false;">Radar</button>
			</div>
			<div id="grafico" style="width: 100%; height: 90%;"></div>
EOT;
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				/**
				* Monta o gráfico de barra
				*/
				function graficoBarra() {
					$('#grafico').html('');
					(function basic_bars(container, horizontal) {
					    var horizontal = horizontal ? true : false, dims = [], dim = [], point, i, xAxisTexto1 = [], xAxisTexto2 = [], grafico, i = 0, colunas = 0, variacao;
						grafico = JSON.parse($('#{$g['divIdParent']}').data('grafico'));
						// tamanho de cada coluna
						variacao = 0.9 / grafico['nrColunas'];
					    // Adiciona os dados de cada coluna
					    for (colunas; colunas < grafico['nrColunas']; colunas++) {
					    	dim = [];
					    	i = 0;
						    $.each(grafico['colunaColunas'], function(index, value) {
				    			i++;
								point = [i + (variacao * colunas), parseFloat(value[colunas][0])];
								dim.push(point);
								xAxisTexto1[i + (variacao * colunas)] = index;
								xAxisTexto2[i + (variacao * colunas)] = value[colunas][1];
							});
							dims.push({data: dim, label: grafico['colunas'][colunas]});
						}
					    Flotr.draw(container, dims, {
				    		title: '.: Título do Gráfico :.',
					        bars: {
					            show: true,
					            horizontal: horizontal,
					            shadowSize: 0,
					            barWidth: variacao
					        },
					        mouse: {
					            track: true,
					            relative: true,
					            trackFormatter: function(x) {
					            	return xAxisTexto2[x.nearest.x] + ' - ' + x.y;
					            }
					        },
					        yaxis: {
					            min: 0,
					            autoscaleMargin: 1
					        },
					        xaxis: {
					            noTicks: xAxisTexto1.length,
					            tickFormatter: function(x) {
					                var x = parseInt(x);
					                return xAxisTexto1[x];
					            }
					        },
					        legend: {
					            position: "ne",
					            backgroundColor: "#D2E8FF"
					        }
					    });
					})(document.getElementById("grafico"));
				}
				function graficoPizza() {
					$('#grafico').html('');
					(function basic_pie(container) {
						var data = [], dim, grafico, graph, i = 0, xAxisTexto = [];
						grafico = JSON.parse($('#{$g['divIdParent']}').data('grafico'));
						$.each(grafico['colunaTotal'], function(index, value) {
							i++;
							dim = {
								data: [[i, parseFloat(value)]],
								label: index
							}
							data.push(dim);
							xAxisTexto.push(index);
						});
					    graph = Flotr.draw(container, data, {
					        HtmlText: false,
					        grid: {
					            verticalLines: false,
					            horizontalLines: false
					        },
					        xaxis: {
					            showLabels: false
					        },
					        yaxis: {
					            showLabels: false
					        },
					        pie: {
					            show: true,
					            explode: 20
					        },
					        mouse: {
					            track: true,
					            relative: true,
					            trackFormatter: function(x) {
					            	return xAxisTexto[(x.x - 1)] + ' - ' + x.y;
					            }
					        },
					        legend: {
					            position: "ne",
					            backgroundColor: "#D2E8FF"
					        }
					    });
					})(document.getElementById("grafico"));
				}
				function graficoLinha() {
					$('#grafico').html('');
					(function basic_line(container) {
					    var dims = [], dim = [], point, i, xAxisTexto1 = [], xAxisTexto2 = [], grafico, i = 0, colunas = 0, variacao;
						grafico = JSON.parse($('#{$g['divIdParent']}').data('grafico'));
						// variacao da linha (para poder identificar cada linha)
						variacao = 0.001;
					    // Adiciona os dados de cada coluna
					    for (colunas; colunas < grafico['nrColunas']; colunas++) {
					    	dim = [];
					    	i = 0;
						    $.each(grafico['colunaColunas'], function(index, value) {
				    			i++;
								point = [i + (variacao * colunas), parseFloat(value[colunas][0])];
								dim.push(point);
								xAxisTexto1[i + (variacao * colunas)] = index;
								xAxisTexto2[i + (variacao * colunas)] = value[colunas][1];
							});
							dims.push({data: dim, label: grafico['colunas'][colunas]});
						}
					    Flotr.draw(container, dims, {
					        mouse: {
					            track: true,
					            relative: true,
					            trackFormatter: function(x) {
					            	return xAxisTexto2[x.nearest.x] + ' - ' + x.y;
					            }
					        },
					        yaxis: {
					            min: 0,
					            autoscaleMargin: 1
					        },
					        xaxis: {
					            noTicks: xAxisTexto1.length,
					            tickFormatter: function(x) {
					                var x = parseInt(x);
					                return xAxisTexto1[x];
					            }
					        },
					        lines: {
					            show: true
					        },
					        points: {
					            show: false
					        },
					        legend: {
					            position: "ne",
					            backgroundColor: "#D2E8FF"
					        },
					    });
					})(document.getElementById("grafico"));
				}
				function graficoArea() {
					$('#grafico').html('');
					(function basic_area(container) {
						var dims = [], dim = [], point, i, xAxisTexto1 = [], xAxisTexto2 = [], grafico, i = 0, colunas = 0, variacao;
						grafico = JSON.parse($('#{$g['divIdParent']}').data('grafico'));
						// variacao da linha (para poder identificar cada linha)
						variacao = 0.001;
					    // Adiciona os dados de cada coluna
					    for (colunas; colunas < grafico['nrColunas']; colunas++) {
					    	dim = [];
					    	i = 0;
						    $.each(grafico['colunaColunas'], function(index, value) {
				    			i++;
								point = [i + (variacao * colunas), parseFloat(value[colunas][0])];
								dim.push(point);
								xAxisTexto1[i + (variacao * colunas)] = index;
								xAxisTexto2[i + (variacao * colunas)] = value[colunas][1];
							});
							dims.push({data: dim, label: grafico['colunas'][colunas]});
						}
					    Flotr.draw(container, dims, {
					        mouse: {
					            track: true,
					            relative: true,
					            trackFormatter: function(x) {
					            	return xAxisTexto2[x.nearest.x] + ' - ' + x.y;
					            }
					        },
					        yaxis: {
					            min: 0,
					            autoscaleMargin: 1
					        },
					        xaxis: {
					            noTicks: xAxisTexto1.length,
					            tickFormatter: function(x) {
					                var x = parseInt(x);
					                return xAxisTexto1[x];
					            }
					        },
					        lines: {
					            fill: true,
					            show: true
					        },
					        points: {
					            show: false
					        },
					        legend: {
					            position: "ne",
					            backgroundColor: "#D2E8FF"
					        }
					    });
					})(document.getElementById("grafico"));
				}
				function graficoRadar() {
					$('#grafico').html('');
					(function basic_area(container) {
						var dims = [], dim = [], point, i, xAxisTexto1 = [], xAxisTexto2 = [], grafico, i = 0, colunas = 0, variacao;
						grafico = JSON.parse($('#{$g['divIdParent']}').data('grafico'));
						// variacao da linha (para poder identificar cada linha)
						variacao = 0.001;
					    // Adiciona os dados de cada coluna
					    for (colunas; colunas < grafico['nrColunas']; colunas++) {
					    	dim = [];
					    	i = 0;
						    $.each(grafico['colunaColunas'], function(index, value) {
				    			i++;
								point = [i + (variacao * colunas), parseFloat(value[colunas][0])];
								dim.push(point);
								xAxisTexto1[i + (variacao * colunas)] = index;
								xAxisTexto2[i + (variacao * colunas)] = value[colunas][1];
							});
							dims.push({data: dim, label: grafico['colunas'][colunas]});
						}
					    Flotr.draw(container, dims, {
					    	radar: {
					            show: true
					        },
					        grid: {
					            circular: true,
					            minorHorizontalLines: true
					        },
					        mouse: {
					            track: true,
					            relative: true,
					            trackFormatter: function(x) {
					            	return xAxisTexto2[x.nearest.x] + ' - ' + x.y;
					            }
					        },
					        yaxis: {
								min: 0,
					            autoscaleMargin: 1
					        },
					        xaxis: {
					            noTicks: xAxisTexto1.length,
					            tickFormatter: function(x) {
					                var x = parseInt(x);
					                return xAxisTexto1[x];
					            }
					        },
					        legend: {
					    		position: "ne",
					        	backgroundColor: "#D2E8FF"
					        }
					    });
					})(document.getElementById("grafico"));
				}
				
				graficoBarra();
			} </script></div>
EOT;
	break;

	case 'etc011Avisos':
		global $g, $sys, $confUsuario;

		$html = "";
		
		$servidor = $sys['urlEmpresa'];

		$html .= <<<EOT
			<section id="divBoleto" style="border: 0px; width: 97%; margin: 0px; margin-left: 8px; margin-right:5px; display: flex; flex-wrap: wrap">
EOT;

		// Imprime os boletos para os parceiros hardness
		echo getBoletosParceiroFwNovo();
		
		// * AVISOS GERAIS =====================================================================================
		// Inicia uma conexão com o servidor central do parceiro no modelo sistema antigo
 		$con = @mysqli_connect($sys['dbHost'], $sys['dbUser'], $sys['dbPass'],$sys['dbDatabase']);
		$g['conexaoBanco'] = $con; 

		$C032 = mysql_query("SELECT C032_Aviso, 
									C032_Imagem 
							   FROM C032 
							  WHERE C032_Flag_Ativo='S'");
				
		while($mC032 = mysql_fetch_array($C032)) {
			// Tratar caracteres especiais
			$mC032['C032_Aviso'] = utf8_encode($mC032['C032_Aviso']);

			$mC032['C032_Aviso'] = str_replace(array('size="1"','size="2"','size="3"','size="4"','size="5"','size="6"'), 'style="font-size:13px;"', $mC032['C032_Aviso']);

			$imagemAviso = "{$sys['urlEmpresa']}{$g['pathWeb']}dados_usuarios/{$sys['dbDatabase']}/imagens/avisos/{$mC032['C032_Imagem']}";
			if(strpos($confUsuario['urlRaiz'], 'https://') !== false && isset($sys['urlEmpresaHTTPS'])){
				// Se o cliente usa link HTTPS, verifica se a empresa tem link HTPPS também para carregar a imagem
				$imagemAviso = "{$sys['urlEmpresaHTTPS']}{$g['pathWeb']}dados_usuarios/{$sys['dbDatabase']}/imagens/avisos/{$mC032['C032_Imagem']}";
			}

			// impressão dos avisos cadastrados 
			$html .= <<<EOT
			<div style='border: 0px; width: 610px; max-width: 610px; margin: 5px; margin-left: 6px; margin-bottom:0px; height: 120px; padding: 10px; background: white; border-radius: 10px; display: grid; grid-template-columns: 1flr; background-image: url("{$imagemAviso}");background-repeat: no-repeat;background-size: 630px 140px;'>
				<div style='width:100%;'>
					{$mC032['C032_Aviso']}
				</div>
			</div>
EOT;
		}
		
		// * BOLETOS CLIENTE ===================================================================================
		if((isset($g['c029Ids'][35]) && $g['c029Ids'][35]) || (isset($g['c029Ids'][36]) && $g['c029Ids'][36]) || (isset($g['c029Ids'][37]) && $g['c029Ids'][37]) && !empty($g['usuarioAtual']) && ($g['nivelAlcada']==4)) {
			// Se o servidor for do tipo novo, criamos os boletos na tela no formato do app de boletos do fw novo
			if (isset($sys['dbHostFwNovo'])) {
				echo getBoletosFwNovo();
			} else {
				echo getBoletos();
			}
		}

		// * AVISOS LOCAIS E ÚLTIMAS ATUALIZAÇÕES DO SISTEMA ===================================================

		//-- CONEXAO DIRETA COM O BANCO DE DADOS
		$con =@mysqli_connect($confUsuario['dbHost'], $confUsuario['dbUser'], $confUsuario['dbPass'],$confUsuario['dbDatabase']);
		// $dbs =@mysql_select_db($sys['dbDatabase'], $con) or die (mysql_error());
		$g['conexaoBanco'] = $con;
		
		if ($confUsuario['dbDatabase']!='e001') {

			$C032 = mysql_query("SELECT C032_Aviso, 
										C032_Imagem,
										C007_Nome
								   FROM C032 
							  LEFT JOIN C036 ON C036_C032_Id=C032_Id
							  LEFT JOIN C007 ON C007_Id='{$g['usuarioAtual']}'
								  WHERE C032_Flag_Ativo='S'
									AND (C036_C021_Id='{$g['perfilUsuario']}' OR IFNULL(C036_C021_Id, 0) = 0) 
									AND (C036_C004_Id='{$g['empresaAtual']}' OR IFNULL(C036_C004_Id, 0) = 0) 
									AND (C036_C007_Id='{$g['usuarioAtual']}' OR IFNULL(C036_C007_Id, 0) = 0)
									AND (curdate() >= C036_Data_Inicial OR IFNULL(C036_Data_Inicial,'0000-00-00') = '0000-00-00')
									AND (curdate() <= C036_Data_Final OR IFNULL(C036_Data_Final,'0000-00-00') = '0000-00-00')
						  	   GROUP BY C032_Id");

			while($mC032 = mysql_fetch_array($C032)) {
				// Tratar caracteres especiais
				$mC032['C032_Aviso'] = utf8_encode($mC032['C032_Aviso']);

				$mC032['C032_Aviso'] = str_replace(array('size="2"','size="3"','size="4"','size="5"','size="6"'), 'style="font-size:13px;"', $mC032['C032_Aviso']);

				$mC032['C032_Aviso'] = str_replace('[NOME_USUARIO]', $mC032['C007_Nome'], $mC032['C032_Aviso']);
			
				// impressão dos avisos cadastrados 
				$html .= <<<EOT
				<div style='border: 0px; width: 610px; max-width: 610px; margin: 5px; margin-left: 6px; margin-bottom:0px; height: 120px; padding: 10px; background: white; border-radius: 10px; display: grid; grid-template-columns: 1flr; background-image: url("{$confUsuario['urlRaiz']}{$g['pathWeb']}dados_usuarios/{$confUsuario['dbDatabase']}/imagens/avisos/{$mC032['C032_Imagem']}");background-repeat: no-repeat;background-size: 630px 140px;'>
						{$mC032['C032_Aviso']}			
				</div>
EOT;
			}
		}

		require_once 'dashboard-personalizado/config.php';
		require_once 'dashboard-personalizado/access.php';
			$exibirAtalhoIntegradores = !empty($g['c029Ids'][304]);
			$exibirAtalhoMcp = !empty($g['c029Ids'][306]) && isset($g['nivelAlcada']) && (int) $g['nivelAlcada'] === 4;
			$exibirAtalhoWebhooks = !empty($g['c029Ids'][307]);
		$html .= <<<HTML
			<div id="atalhosAcessoInicio">
				<style>
					#atalhosAcessoInicio { --atalhos-acesso-direita: 32px; --atalhos-acesso-inferior: 32px; --atalhos-acesso-espacamento: 16px; position: fixed; right: var(--atalhos-acesso-direita); bottom: var(--atalhos-acesso-inferior); z-index: 1000000; display: flex; flex-direction: column; align-items: flex-end; gap: var(--atalhos-acesso-espacamento); }
				</style>
HTML;
		if ($exibirAtalhoIntegradores || $exibirAtalhoMcp || $exibirAtalhoWebhooks) {
			$html .= <<<HTML
				<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,400,0,0">
HTML;
		}
		if (dashboardPersonalizadoHabilitado($g['empresaAtual']) && dashboardPersonalizadoPodeAcessar(array('usuario' => (int) $g['usuarioAtual'], 'perfil' => (int) $g['perfilUsuario'], 'root' => !empty($g['isRoot']), 'diretor' => isset($g['nivelAlcada']) && (int) $g['nivelAlcada'] === 4), $g['empresaAtual'])) {
			$dashboardPersonalizadoToken = dashboardPersonalizadoCriarTokenSessao();
			if ($dashboardPersonalizadoToken !== false) {
				$dashboardPersonalizadoUrl = htmlspecialchars('/hardness3/dashboard-personalizado/#session=' . $dashboardPersonalizadoToken, ENT_QUOTES, 'UTF-8');
				$html .= <<<HTML
					<div id="dashboardPersonalizadoAcessoInicio">
						<a class="dashboard-personalizado-atalho" href="#" role="button" title="Acesse Dashboard" aria-label="Acesse Dashboard" data-dashboard-url="{$dashboardPersonalizadoUrl}" onclick="window.open(this.getAttribute('data-dashboard-url'), '_blank', 'noopener,noreferrer'); return false;" style="display: inline-flex; min-width: 260px; padding: 18px 21px; border: 1px solid #2860dc; border-radius: 12px; background: #fff; box-shadow: 0 12px 30px rgba(25,55,100,.16); color: #07224d !important; align-items: center; gap: 14px; font: 800 19px/1.1 Arial, sans-serif; text-align: left; white-space: nowrap; cursor: pointer; text-shadow: none !important; transition: transform .18s ease, box-shadow .18s ease, background-color .18s ease;" onmouseover="this.style.transform='translateY(-2px)'; this.style.backgroundColor='#edf3ff'; this.style.boxShadow='0 16px 34px rgba(25,55,100,.22)';" onmouseout="this.style.transform=''; this.style.backgroundColor='#fff'; this.style.boxShadow='0 12px 30px rgba(25,55,100,.16)';">
							<span class="material-icons dashboard-personalizado-atalho__icone" aria-hidden="true" style="display: flex; align-items: center; justify-content: center; flex: 0 0 43px; width: 43px; height: 43px; border-radius: 10px; background: #eaf0ff; color: #2860dc !important; font-size: 25px; font-weight: 400; line-height: 1;">assessment</span>
							<span class="dashboard-personalizado-atalho__texto" style="display: flex; flex: 1; flex-direction: column; gap: 2px;"><span style="color: #07224d !important; font-weight: 800; line-height: 1.1;">Dashboard</span><small style="color: #617495 !important; font-size: 12px; font-weight: 600; line-height: 1.1;">Painel</small></span>
							<span class="material-icons dashboard-personalizado-atalho__seta" aria-hidden="true" style="color: #2860dc !important; font-size: 23px; font-weight: 400; line-height: 1;">open_in_new</span>
						</a>
					</div>
					<style>
						#dashboardPersonalizadoAcessoInicio .dashboard-personalizado-atalho { display: flex !important; flex-direction: row !important; flex-wrap: nowrap !important; align-items: center !important; justify-content: flex-start !important; text-decoration: none !important; }
						#dashboardPersonalizadoAcessoInicio .dashboard-personalizado-atalho__icone { display: flex !important; flex: 0 0 43px !important; align-items: center !important; justify-content: center !important; }
						#dashboardPersonalizadoAcessoInicio .dashboard-personalizado-atalho__texto { display: flex !important; flex: 1 1 auto !important; flex-direction: column !important; align-items: flex-start !important; justify-content: center !important; min-width: 0; }
						#dashboardPersonalizadoAcessoInicio .dashboard-personalizado-atalho__texto > span, #dashboardPersonalizadoAcessoInicio .dashboard-personalizado-atalho__texto > small { display: block !important; width: auto !important; text-align: left !important; }
						#dashboardPersonalizadoAcessoInicio .dashboard-personalizado-atalho__seta { display: block !important; flex: 0 0 auto !important; margin-left: auto !important; }
					</style>
				HTML;
			}
		}

		require_once 'painel-personalizado/atalho.php';
		$html .= painelPersonalizadoAtalhoInicio();

		if ($exibirAtalhoIntegradores) {
			$html .= <<<HTML
				<a class="atalho-integradores" href="/hardness3/integradores/" target="_blank" rel="noopener noreferrer" title="Abrir painel de integrações em uma nova aba">
					<span class="atalho-integradores__icone material-symbols-outlined" aria-hidden="true">hub</span>
					<span class="atalho-integradores__texto"><strong>Integrações</strong><small>Painel</small></span>
					<span class="atalho-integradores__seta material-symbols-outlined" aria-hidden="true">open_in_new</span>
				</a>
				<style>
					.atalho-integradores { display: inline-flex; align-items: center; gap: 14px; min-width: 260px; padding: 18px 21px; border: 1px solid #2860dc; border-radius: 12px; background: #fff; box-shadow: 0 12px 30px rgba(25,55,100,.16); color: #07224d; text-decoration: none; transition: transform .18s ease, box-shadow .18s ease, background-color .18s ease; }
					.atalho-integradores:hover { transform: translateY(-2px); background: #edf3ff; box-shadow: 0 16px 34px rgba(25,55,100,.22); color: #07224d; }
					.atalho-integradores__icone { display: flex; align-items: center; justify-content: center; width: 43px; height: 43px; border-radius: 10px; background: #eaf0ff; color: #2860dc; font-size: 24px; }
					.atalho-integradores__texto { display: flex; flex: 1; flex-direction: column; gap: 2px; }
					.atalho-integradores__texto strong { color: #07224d; font-size: 19px; font-weight: 800; letter-spacing: .01em; line-height: 1.1; }
					.atalho-integradores__texto small { color: #617495; font-size: 12px; font-weight: 600; }
					.atalho-integradores__seta { color: #2860dc; font-size: 23px; line-height: 1; }
				</style>
			HTML;
		}

		if ($exibirAtalhoMcp) {
			$html .= <<<HTML
				<a class="atalho-mcp" href="/gerencial/mcp/content/McpAdminStandalone/?ajax=true" target="_blank" rel="noopener noreferrer" title="Abrir administração de conexões MCP em uma nova aba">
					<span class="atalho-mcp__icone material-symbols-outlined" aria-hidden="true">account_tree</span>
					<span class="atalho-mcp__texto"><strong>Conexões MCP</strong><small>Administração</small></span>
					<span class="atalho-mcp__seta material-symbols-outlined" aria-hidden="true">open_in_new</span>
				</a>
				<style>
					.atalho-mcp { display: inline-flex; align-items: center; gap: 14px; min-width: 260px; padding: 18px 21px; border: 1px solid #2860dc; border-radius: 12px; background: #fff; box-shadow: 0 12px 30px rgba(25,55,100,.16); color: #07224d; text-decoration: none; transition: transform .18s ease, box-shadow .18s ease, background-color .18s ease; }
					.atalho-mcp:hover { transform: translateY(-2px); background: #edf3ff; box-shadow: 0 16px 34px rgba(25,55,100,.22); color: #07224d; }
					.atalho-mcp:focus { outline: 3px solid rgba(40,96,220,.28); outline-offset: 3px; }
					.atalho-mcp__icone { display: flex; align-items: center; justify-content: center; width: 43px; height: 43px; border-radius: 10px; background: #eaf0ff; color: #2860dc; font-size: 24px; }
					.atalho-mcp__texto { display: flex; flex: 1; flex-direction: column; gap: 2px; }
					.atalho-mcp__texto strong { color: #07224d; font-size: 19px; font-weight: 800; letter-spacing: .01em; line-height: 1.1; }
					.atalho-mcp__texto small { color: #617495; font-size: 12px; font-weight: 600; }
					.atalho-mcp__seta { color: #2860dc; font-size: 23px; line-height: 1; }
				</style>
			HTML;
		}

		if ($exibirAtalhoWebhooks) {
			$html .= <<<HTML
				<a class="atalho-webhooks" href="/gerencial/webhooks/content/WebhooksAdminContent/?ajax=true" target="_blank" rel="noopener noreferrer" title="Abrir administração de Webhooks em uma nova aba">
					<span class="atalho-webhooks__icone material-symbols-outlined" aria-hidden="true">description</span>
					<span class="atalho-webhooks__texto"><strong>Webhooks</strong><small>Administração</small></span>
					<span class="atalho-webhooks__seta material-symbols-outlined" aria-hidden="true">open_in_new</span>
				</a>
				<style>
					.atalho-webhooks { display: inline-flex; align-items: center; gap: 14px; min-width: 260px; padding: 18px 21px; border: 1px solid #2860dc; border-radius: 12px; background: #fff; box-shadow: 0 12px 30px rgba(25,55,100,.16); color: #07224d; text-decoration: none; transition: transform .18s ease, box-shadow .18s ease, background-color .18s ease; }
					.atalho-webhooks:hover { transform: translateY(-2px); background: #edf3ff; box-shadow: 0 16px 34px rgba(25,55,100,.22); color: #07224d; }
					.atalho-webhooks:focus { outline: 3px solid rgba(40,96,220,.28); outline-offset: 3px; }
					.atalho-webhooks__icone { display: flex; align-items: center; justify-content: center; width: 43px; height: 43px; border-radius: 10px; background: #eaf0ff; color: #2860dc; font-size: 24px; }
					.atalho-webhooks__texto { display: flex; flex: 1; flex-direction: column; gap: 2px; }
					.atalho-webhooks__texto strong { color: #07224d; font-size: 19px; font-weight: 800; letter-spacing: .01em; line-height: 1.1; }
					.atalho-webhooks__texto small { color: #617495; font-size: 12px; font-weight: 600; }
					.atalho-webhooks__seta { color: #2860dc; font-size: 23px; line-height: 1; }
				</style>
			HTML;
		}
		$html .= '</div>';

        $html .= <<<EOT
			</section>
EOT;
 		$pastaVideoMelhorias = "static/melhorias/";
		if(!empty($sys['pastaVideoMelhorias'])){
			// Caso o parceiro queira gravar os proprios vídeos e não listar os nossos da hardness
			// Precisa criar no sysConfig o elemento pastaVideoMelhorias no array e criar uma pasta com o nome do pastaVideoMelhorias
			$pastaVideoMelhorias = "static/melhorias/{$sys['pastaVideoMelhorias']}/";
		}
		$exibirListaVideosAtualizacoes = true;
		if (array_key_exists('exibirListaVideosAtualizacoes', $sys)) {
			$valorExibirListaVideos = strtolower(trim((string) $sys['exibirListaVideosAtualizacoes']));
			$exibirListaVideosAtualizacoes = !in_array($valorExibirListaVideos, array('', '0', 'n', 'nao', 'não', 'false'), true);
		}

		//-- Apresenta vídeo de melhoria do sistema
		if (array_key_exists('C007_Id_Melhoria_Notificada', $g['C007'])) {
			//$idMelhoria = $g['C007']['C007_Id_Melhoria_Notificada'] + 1;
			// conta quantidade de arquivos que tem na pasta
			$diretorio 		   = scandir($pastaVideoMelhorias);
			$qtdArquivos  	   = count($diretorio) - 2; 
			$idMelhoria        = $qtdArquivos;
			$idMelhoriaArquivo = str_pad($idMelhoria, 5, "0", STR_PAD_LEFT);
			$arquivo 		   = "{$pastaVideoMelhorias}notificacao_{$idMelhoriaArquivo}.php";
			if (file_exists($arquivo) && ($g['C007']['C007_Id_Melhoria_Notificada'] != $idMelhoria)) {
				//require_once($arquivo);
				mysql_query("UPDATE C007 SET C007_Id_Melhoria_Notificada = '{$idMelhoria}' WHERE C007_Id = '{$g['usuarioAtual']}'");
			} 

/* 			não vai mais deixar o vídeo aberto de melhorias não vizualizadas
			echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#melhoriaMinimizar$idMelhoria').unbind('click').unbind('click').bind('click', function(data) {
						$('#melhoriaVideo').toggle();
						if ($('#melhoriaMinimizar$idMelhoria').html()=='-') {
							$('#melhoriaMinimizar$idMelhoria').html('+');
						} else {
							$('#melhoriaMinimizar$idMelhoria').html('-');
						}                
					});
				} </script></div>
EOT; */
		}

		echo $html;

		//-- Logo de fundo da tela inicial
		if (!empty($sys['logoFundo'])) {
			echo "<img src='{$sys['logoFundo']}' alt='logoHardnessBranca' class='logo-fundo-inicio' style='right: 100px; top: 100px;'>";
		}

		if ($exibirListaVideosAtualizacoes) {
			//-- Mostra as últimas 10 notificações de melhoria
			echo "<div class='ultimasAttTitulo'>Últimas atualizações:</div>";

			//$idMelhoria=$g['C007']['C007_Id_Melhoria_Notificada'];
			$countMelhoria=0;
			for ($idMelhoria;$idMelhoria>0 || $countMelhoria<=10;$idMelhoria--)
			{
				$countMelhoria++;
				$idMelhoriaArquivo = str_pad($idMelhoria, 5, "0", STR_PAD_LEFT);
				$arquivoMelhoria = "{$pastaVideoMelhorias}notificacao_{$idMelhoriaArquivo}.php";
				if (file_exists($arquivoMelhoria)) {
					require_once($arquivoMelhoria);
				} else {
					continue;
				}
				echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#melhoriaVideo$idMelhoria').toggle();
					$('#melhoriaMinimizar$idMelhoria').html('+');
					$('#melhoriaMinimizar$idMelhoria').unbind('click').unbind('click').bind('click', function(data) {
						$('#melhoriaVideo$idMelhoria').toggle();
						if ($('#melhoriaMinimizar$idMelhoria').html()=='-') {
							$('#melhoriaMinimizar$idMelhoria').html('+');
						} else {
							$('#melhoriaMinimizar$idMelhoria').html('-');
						}                
					});
				} </script></div>
EOT;
			}
		}

	break;
}

/**
 * Pega o HTML que mostra os boletos disponíveis para os parceiros no FW novo
 * @return string
 */
function getBoletosParceiroFwNovo(): string
{
	global $g, $sys, $confUsuario;

	//-- CONEXAO DIRETA COM O BANCO DE DADOS
	$con = @mysqli_connect($sys['dbHostH'], $sys['dbUserH'], $sys['dbPassH'],$sys['dbDatabaseH']);
	// $dbs =@mysql_select_db($sys['dbDatabase'], $con) or die (mysql_error());
	$g['conexaoBanco'] = $con;

	$html = "";

	// Somente usuário root e parceiros que tem dbCNPJ configurado podem ver seus boletos pendentes nos servidores
	if ($g["usuarioAtual"] != 63 || !isset($sys["dbCNPJ"])) {
		return "";
	}

	$cnpjEmpresaUsuaria = md5($sys['dbCNPJ']);

	if (strlen($cnpjEmpresaUsuaria)==14) {
		$cnpjEmpresaUsuaria=md5($cnpjEmpresaUsuaria);
	}

	if (empty($cnpjEmpresaUsuaria)) {
		return "";
	}

	// Pega as informações do parceiro
	$qPES001 = "SELECT PES001_Id,
					   PES001_Razao_Social
				  FROM PES001
				 WHERE MD5(PES001_CNPJ) = '{$cnpjEmpresaUsuaria}'";
	$PES001 = mysql_query($qPES001);
	if ($erro = mysql_error()) {
		$html .= "Erro select boletos(1):" . $erro;
		return $html;
	}
	$mPES001 = mysql_fetch_assoc($PES001);

	// Pega as informações da empresa mantenedora do sistema
	$qC001 = "SELECT PES001_Nome_Fantasia,
					 PES004_DDD,
					 PES004_Telefone,
					 PES004_Email
				FROM C001 
		   LEFT JOIN PES001 ON C001_PES001_Id = PES001_Id
		   LEFT JOIN PES004 ON PES004_PES001_Id = PES001_Id
			   WHERE C001_Id = 1
			   LIMIT 1";
	$C001 = mysql_query($qC001);
	if ($erro = mysql_error()) {
		$html .= "Erro select boletos(2):" . $erro;
		return $html;
	}
	$mC001 = mysql_fetch_assoc($C001);

	// * Verifica boletos para clientes normais ====================================================================
	$qBOL001 = "SELECT BOL001_Id,
					   PES001_Id,
					   BOL001_Data_Emissao,
					   BOL001_Data_Vencimento,
					   BOL001_Valor_Total,
					   BOL001_Nosso_Numero,
					   BOL001_Flag->>'$.Cancelado',
					   BOL001_Data_Vencimento,
					   DATEDIFF(BOL001_Data_Vencimento,CURRENT_DATE()) AS Dias_Vencer,
					   DAYNAME(BOL001_Data_Vencimento) AS DiaSemanaNome,
					   DATE_FORMAT(BOL001_Data_Vencimento,'%w') AS DiaSemana,
					   DATE_FORMAT(CURRENT_DATE(),'%w') AS DiaSemanaHoje,
					   DATEDIFF(CURRENT_DATE(),BOL001_Data_Vencimento) AS Dias_Vencido
					FROM BOL001
				LEFT JOIN BOL006 ON BOL001_BOL006_Id = BOL006_Id
				LEFT JOIN PES001 ON BOL006_PES001_Id = PES001_Id
				LEFT JOIN BOL005 ON BOL001_BOL005_Id = BOL005_Id
				LEFT JOIN CBC002 ON BOL005_CBC002_Id = CBC002_Id
				LEFT JOIN CBC001 ON CBC002_CBC001_Id = CBC001_Id
					WHERE MD5(PES001_CNPJ) = '{$cnpjEmpresaUsuaria}'
					  AND IFNULL(BOL001_Flag->>'$.Cancelado', 'N') = 'N'
					  AND BOL001_Data_Pagamento = '0000-00-00'
				 ORDER BY BOL001_Data_Vencimento";
	$BOL001 = mysql_query($qBOL001);
	if ($erro = mysql_error()) {
		$html .= "Erro select boletos(3):" . $erro;
		return $html;
	}

	if (mysql_num_rows($BOL001) > 0) {
		//Impressão dos boletos do cliente
		while ($mBOL001 = mysql_fetch_assoc($BOL001)) {
			$vencimentoBoleto = gCorrigeData($mBOL001['BOL001_Data_Vencimento']);
			// Usamos urlEmpresaH porque precisamos pegar o boleto do servidor da hardness, não do parceiro
			$idOfuscado = md5($mBOL001["BOL001_Id"]."-".$mBOL001["PES001_Id"]);
			$urlBoleto = $sys["urlEmpresaH"] . "/hardness/integrator/boleto/gerar_boleto_parceiro.php?id={$idOfuscado}";
			
			//Caso Dias_Vencido<=0
			//Hoje seja segunda  e DiasSemana do vencimento for Sab/Dom)
			if(($mBOL001['Dias_Vencido']<=0) || (($mBOL001['DiaSemanaHoje']==1) && ($mBOL001['DiaSemana']==0 || $mBOL001['DiaSemana']==6))){
				if ($mBOL001['Dias_Vencer'] == 0) {
					$dias_vencimento = " (hoje)";
				} elseif ($mBOL001['Dias_Vencer'] == 1) {
					$dias_vencimento = " (amanhã) ";
				} elseif ($mBOL001['Dias_Vencer'] > 2) {
					$dias_vencimento = " (daqui a {$mBOL001['Dias_Vencer']} dias)";
				}

				$html .= <<<EOT
				<div id="divBoleto" class="boletoNotificacao">
					<div style='margin-top:10px; margin-left: 20px; width: 615px;'>
						Olá Parceiro! Já está disponível o boleto para pagamento.
					</div>
					<div style='display:grid; grid-template-columns: 80px 520px;margin:5px;margin-left:15px;'>
						<div style='margin:5px'>
							<a target="_blank" href="{$urlBoleto}">
								<img src="{$confUsuario['urlEmpresa']}/hardness3/static/img/boleto.png" border=0>
							</a>
						</div>
						<div style='margin:5px'>
							Empresa: <b>{$mPES001["PES001_Razao_Social"]}</b><br>
							Vencimento em <b>{$vencimentoBoleto} {$dias_vencimento} </b><br>
							<a target="_blank" href="{$urlBoleto}">
								<font style="font-size:16px;color:green;font-weight:bold">Clique aqui para imprimir o boleto</font>
							</a>
						</div>
					</div>
					<div style='margin-bottom: 10px; margin-left: 20px'>
						O pagamento pode ser realizado pela internet ou em qualquer agência bancária até vencimento.
					</div>
				</div>
EOT;
			} else {
				//Caso Dias_Vencido > 0
				//Caso Dias_Vencido>2 e DiasSemana vencimento igual Sab/Dom
				if($mBOL001['Dias_Vencer'] < 0) {
					$dias_vencimento = " (".abs($mBOL001['Dias_Vencer'])." dias atrás)";
				} else {
					$dias_vencimento = "";
				}
				$dias = 30;
				$html .= <<<EOT
				<div id="divBoleto" class="boletoNotificacao">
					<font style='margin-top:10px; margin-left: 20px; width: 615px; color: var(--BGVermelho);'>
						Olá Parceiro! Consta em nossa base a(s) pendendência(s) abaixo relacionada(s)
					</font>
					<div style='display:grid; grid-template-columns: 80px 520px;margin:5px;margin-left:15px;'>
						<div style='margin:5px'>
							<a target="_blank" href="{$urlBoleto}">
								<img src="{$confUsuario['urlEmpresa']}/hardness3/static/img/boleto.png" border=0>
							</a>
						</div>
						<div style='margin:5px'>
							Empresa:<b> {$mPES001["PES001_Razao_Social"]}</b><br>
							Vencimento em <b>{$vencimentoBoleto}<font style='color: var(--BGVermelho);'>{$dias_vencimento}</font></b><br>
							<a target='_blank' href="{$urlBoleto}">
								<font style='color:green;font-size:16px;font-weight:bold'>Clique aqui para imprimir o boleto</font>
							</a>
						</div>
					</div>
EOT;
				if(abs($mBOL001['Dias_Vencido']) >= ($dias/2)) {
					$prazoSuspensao = $dias-abs($mBOL001['Dias_Vencido']);
					$prazoSuspensao .= ($prazoSuspensao > 1) ? ' dias' : ' dia';
					$html .= <<<EOT
						<div style='margin-bottom: 10px; margin-left: 20px; font-weight: bold;color: var(--BGVermelho)'>
							ATENÇÃO! O SUPORTE E O ACESSO AO SISTEMA SERÃO SUSPENSOS DENTRO DE {$prazoSuspensao}.
						</div>
					</div>
EOT;
				} else {
					$html .= <<<EOT
						<div style='margin-bottom: 10px; margin-left: 20px'>
							Desconsiderar caso a(s) pendência(s) já tenha(m) sido quitada(s), assim que for acusado em nosso sistema a(s) mesma(s) será(ão) baixada(s).
						</div>
					</div>
EOT;
				}
			}
		}
	}

	return $html;
	
	// ! CÓDIGO COMENTADO PORQUE PROVAVELMENTE TEM HAVER COM OS CLIENTES DE NFe
	// if ($temBoleto == 'S') {
	// 	//-- Encontra todos os a vencer
	// 	$cClausula  = " select ";
	// 	$cClausula .= " * ";
	// 	$cClausula .= " from T135 ";
	// 	$cClausula .= " left join D024 on D024_Id=T135_D024_Id ";
	// 	$cClausula .= " where md5(D024_Cnpj)='".$cnpjEmpresaUsuaria."' ";
	// 	$cClausula .= "   and T135_Data_Termino<=(Curdate() + interval 30 day) ";
	// 	$cClausula .= "   and T135_Flag_Renovado!='S' ";
	// 	$cClausula .= "   and T135_Flag_Renovado!='C' ";
	// 	$cSql_T135=mysql_query($cClausula);
	// 	$erroMysql = mysql_error();
	// 	if(!empty($erroMysql)){
	// 		$html .= "Erro select boletos(4):".$erroMysql;
	// 		return $html;
	// 	}

	// 	if (mysql_num_rows($cSql_T135) > 0) {
	// 		$mT135=mysql_fetch_array($cSql_T135);
	// 		$html .= <<<EOT
	// 			<table width=500 border=0 cellpadding=5 cellspacing=0 bgcolor=white style='border: 1px dashed green; border-top:0px'>";
	// 				<tr>
	// 					<td colspan=2 style='border-bottom:0px solid silver'>
	// 						<font style='font-family:arial,verdana;font-size:12px;'>
	// 							{$mT135['D024_Nome_Empresa']}, <b>agradecemos à confiança e parceria</b> durante estes últimos 12 meses! A Hardness Sistemas vem trabalhando constantemente para ofereca você, serviços e produtos que atendam melhor as suas necessidades e expectativas!<br>
	// 					</td>
	// 				</tr>
	// 				<tr>
	// 					<td class=fonte style='font-size:12px;border-right:0px solid silver'>
	// 						Caso a sua empresa não queira renovar os serviços, basta nos enviar um e-mail pedindo o <font style='color: var(--BGVermelho)'>cancelamento</font> para {$mC001['PES004_Email']}.<br>
	// 					</td>
	// 				</tr>
	// 				<tr>
	// 					<td width=10 class=fonte colspan=2 style='font-size:12px;border-top:0px solid silver''>
	// 						<b>Obrigado!</b>
	// 						<BR>
	// 						<BR>
	// 						Equipe {$mC001['PES001_Nome_Fantasia']}
	// 					</td>
	// 				</tr>
	// 			</table>
	// EOT;
	// 	}
	// } else {
	// 	//-- Encontra todos os a vencer
	// 	$cClausula  = " select ";
	// 	$cClausula .= " * ";
	// 	$cClausula .= " from T135 ";
	// 	$cClausula .= " left join D024 on D024_Id=T135_D024_Id ";
	// 	$cClausula .= " where md5(D024_Cnpj)='".$cnpjEmpresaUsuaria."' ";
	// 	$cClausula .= "   and T135_Data_Termino>Curdate() ";
	// 	$cClausula .= "   and T135_Data_Termino<=(Curdate() + interval 30 day) ";
	// 	$cClausula .= "   and T135_Flag_Renovado!='S' ";
	// 	$cClausula .= "   and T135_Flag_Renovado!='C' ";
	// 	$cSql_T135=mysql_query($cClausula);
	// 	$erroMysql = mysql_error();
	// 	if(!empty($erroMysql)){
	// 		$html .= "Erro select boletos(5):".$erroMysql;
	// 		return $html;
	// 	}

	// 	if(mysql_num_rows($cSql_T135) > 0) {
	// 		$mT135=mysql_fetch_array($cSql_T135);
	// 		$html .= <<<EOT
	// 			<table width=500 border=0 cellpadding=5 cellspacing=0 bgcolor=#EAF7EA style='border: 2px solid green;'>
	// 				<tr>
	// 					<td colspan=2 style='border-bottom:0px solid silver' bgcolor=green>
	// 						<font style='font-family:arial,verdana;font-size:12px;'>
	// 						<font style='color:white;font-weight:bold;font-size:13px'>RENOVAÇÃO ANUAL</font>
	// 					</td>
	// 				</tr>
	// 				<tr>
	// 					<td colspan=2 style='border-bottom:0px solid silver'>
	// 						<font style='font-family:arial,verdana;font-size:12px;'>
	// 							{$mT135['D024_Nome_Empresa']}, <b>agradecemos à confiança e parceria</b> durante estes últimos 12 meses! A {$mC001['PES001_Nome_Fantasia']} vem trabalhando constantemente para oferecer a você, serviços e produtos que atendam melhor as suas necessidades e expectativas!<br>
	// 					</td>
	// 				</tr>
	// 				<tr>
	// 					<td class=fonte style='font-size:12px;border-right:0px solid silver'>
	// 						Caso a sua empresa não queira renovar os serviços, basta nos enviar um e-mail pedindo o <font style='color: var(--BGVermelho)'>cancelamento</font> para {$mC001['PES001_Email']}.<br>
	// 					</td>
	// 				</tr>
	// 				<tr>
	// 					<td width=10 class=fonte colspan=2 style='font-size:12px;border-top:0px solid silver''>
	// 						<b>Obrigado!</b>
	// 						<BR>
	// 						<BR>
	// 						Equipe {$mC001['PES001_Nome_Fantasia']}
	// 					</td>
	// 				</tr>
	// 			</table>
	// EOT;
	// 	}
	// }
}

 /**
 * Pega o HTML que mostra os boletos disponíveis para os clientes no modelo Fw Novo
 * @return string
 */
function getBoletosFwNovo(): string
{
	global $g, $sys, $confUsuario;

	// Inicia uma conexão com o servidor central do parceiro no modelo sistema novo
	$con = @mysqli_connect($sys['dbHostFwNovo'], $sys['dbUserFwNovo'], $sys['dbPassFwNovo'],$sys['dbDatabaseFwNovo']);
	$g['conexaoBanco'] = $con;

	$cnpjEmpresaUsuaria = md5($g['C004']['C004_Cnpj']);

	$html = "";

	$gestao=true;
	
	if (strlen($cnpjEmpresaUsuaria)==14) {
		$cnpjEmpresaUsuaria=md5($cnpjEmpresaUsuaria);
	}


	if (empty($cnpjEmpresaUsuaria)) {
		return "";
	}

	// Pega as informações da empresa mantenedora do sistema
	$qC001 = "SELECT PES001_Nome_Fantasia,
					 PES004_DDD,
					 PES004_Telefone,
					 PES004_Email
				FROM C001 
		   LEFT JOIN PES001 ON C001_PES001_Id = PES001_Id
		   LEFT JOIN PES004 ON PES004_PES001_Id = PES001_Id
			   WHERE C001_Id = 1
			   LIMIT 1";
	$C001 = mysql_query($qC001);
	if ($erro = mysql_error()) {
		$html .= "Erro select boletos(1):" . $erro;
		return $html;
	}
	$mC001 = mysql_fetch_assoc($C001);

	// * Verifica boletos para clientes NFe ========================================================================
	$qBOL001 = "SELECT BOL001_Id,
					   BOL001_Data_Vencimento,
					   PES001_Flag->>'$.Tipo_Contrato' AS PES001_Flag_Tipo_Contrato,
					   DATEDIFF(CURRENT_DATE(),BOL001_Data_Vencimento) dias
					 FROM BOL001
				LEFT JOIN BOL006 ON BOL001_BOL006_Id = BOL006_Id
				LEFT JOIN PES001 ON BOL006_PES001_Id = PES001_Id
					WHERE MD5(PES001_CNPJ) = '{$cnpjEmpresaUsuaria}'
					 AND IFNULL(BOL001_Flag->>'$.Cancelado', 'N') = 'N'
				ORDER BY BOL001_Data_Vencimento DESC";
	$BOL001 = mysql_query($qBOL001);
	if ($erro = mysql_error()) {
		$html .= "Erro select boletos(2):" . $erro;
		return $html;
	}
	$mBOL001 = mysql_fetch_assoc($BOL001);

	//Verifica se os clientes tipo "NF-E" não possuem boleto ou tem boletos vencidos a mais de 366 dias
	if ((mysql_num_rows($BOL001) <= 0 || $mBOL001['dias'] > 366) && ($mBOL001['PES001_Flag_Tipo_Contrato'] == 'A')) {
		$html .= "<br><br><br><font style='font-size:18;font-weight:bold;color: var(--BGVermelho);background:white'>EXISTEM PENDENCIAS EM SEU CADASTRO, FAVOR ENTRAR EM CONTATO COM NOSSO SUPORTE PELO TELEFONE {$mC001['PES004_DDD']} {$mC001['PES004_Telefone']}.</font>";
		return $html;
	}

	// * Verifica boletos para clientes normais ====================================================================
	$qBOL001 = "SELECT BOL001_Id,
					   PES001_Id,
					   BOL001_Data_Emissao,
					   BOL001_Data_Vencimento,
					   BOL001_Valor_Total,
					   BOL001_Nosso_Numero,
					   BOL001_Flag->>'$.Cancelado',
					   BOL001_Data_Vencimento,
					   DATEDIFF(BOL001_Data_Vencimento,CURRENT_DATE()) AS Dias_Vencer,
					   DAYNAME(BOL001_Data_Vencimento) AS DiaSemanaNome,
					   DATE_FORMAT(BOL001_Data_Vencimento,'%w') AS DiaSemana,
					   DATE_FORMAT(CURRENT_DATE(),'%w') AS DiaSemanaHoje,
					   DATEDIFF(CURRENT_DATE(),BOL001_Data_Vencimento) AS Dias_Vencido
					FROM BOL001
				LEFT JOIN BOL006 ON BOL001_BOL006_Id = BOL006_Id
				LEFT JOIN PES001 ON BOL006_PES001_Id = PES001_Id
				LEFT JOIN BOL005 ON BOL001_BOL005_Id = BOL005_Id
				LEFT JOIN CBC002 ON BOL005_CBC002_Id = CBC002_Id
				LEFT JOIN CBC001 ON CBC002_CBC001_Id = CBC001_Id
					WHERE MD5(PES001_CNPJ) = '{$cnpjEmpresaUsuaria}'
					  AND IFNULL(BOL001_Flag->>'$.Cancelado', 'N') = 'N'
					  AND BOL001_Data_Pagamento = '0000-00-00'
				 ORDER BY BOL001_Data_Vencimento";
	$BOL001 = mysql_query($qBOL001);
	if ($erro = mysql_error()) {
		$html .= "Erro select boletos(3):" . $erro;
		return $html;
	}

	if (mysql_num_rows($BOL001) > 0) {
		//Impressão dos boletos do cliente
		while ($mBOL001 = mysql_fetch_assoc($BOL001)) {
			$vencimentoBoleto = gCorrigeData($mBOL001['BOL001_Data_Vencimento']);

			$idOfuscado = md5($mBOL001["BOL001_Id"]."-".$mBOL001["PES001_Id"]);
			$urlBoleto = $sys["urlEmpresaFwNovo"] . "/hardness/integrator/boleto/gerar_boleto_parceiro.php?id={$idOfuscado}";

			//Caso Dias_Vencido<=0
			//Hoje seja segunda  e DiasSemana do vencimento for Sab/Dom)
			if(($mBOL001['Dias_Vencido']<=0) || (($mBOL001['DiaSemanaHoje']==1) && ($mBOL001['DiaSemana']==0 || $mBOL001['DiaSemana']==6))){
				if ($mBOL001['Dias_Vencer'] == 0) {
					$dias_vencimento = " (hoje)";
				} elseif ($mBOL001['Dias_Vencer'] == 1) {
					$dias_vencimento = " (amanhã) ";
				} elseif ($mBOL001['Dias_Vencer'] > 2) {
					$dias_vencimento = " (daqui a {$mBOL001['Dias_Vencer']} dias)";
				}

				$html .= <<<EOT
				<div id="divBoleto" class="boletoNotificacao">
					<div style='margin-top:10px; margin-left: 20px; width: 615px;'>
						Olá! Já está disponível o boleto para pagamento.
					</div>
					<div style='display:grid; grid-template-columns: 80px 520px;margin:5px;margin-left:15px;'>
						<div style='margin:5px'>
							<a target="_blank" href="{$urlBoleto}">
								<img src="{$confUsuario['urlEmpresa']}/hardness3/static/img/boleto.png" border=0>
							</a>
						</div>
						<div style='margin:5px'>
							Empresa: <b>{$g['C004']['C004_Nome_Abreviado']}</b><br>
							Vencimento em <b>{$vencimentoBoleto} {$dias_vencimento} </b><br>
							<a target="_blank" href="{$urlBoleto}">
								<font style="font-size:16px;color:green;font-weight:bold">Clique aqui para imprimir o boleto</font>
							</a>
						</div>
					</div>
					<div style='margin-bottom: 10px; margin-left: 20px'>
						O pagamento pode ser realizado pela internet ou em qualquer agência bancária até vencimento.
					</div>
				</div>
EOT;
			} else {
				//Caso Dias_Vencido > 0
				//Caso Dias_Vencido>2 e DiasSemana vencimento igual Sab/Dom
				if($mBOL001['Dias_Vencer'] < 0) {
					$dias_vencimento = " (".abs($mBOL001['Dias_Vencer'])." dias atrás)";
				} else {
					$dias_vencimento = "";
				}
				$dias = 10;
				if (isset($gestao)) {
					$dias = 15;
				}
				$html .= <<<EOT
				<div id="divBoleto" class="boletoNotificacao">
					<font style='margin-top:10px; margin-left: 20px; width: 615px; color: var(--BGVermelho);'>
						Consta em nossa base a(s) pendendência(s) abaixo relacionada(s)
					</font>
					<div style='display:grid; grid-template-columns: 80px 520px;margin:5px;margin-left:15px;'>
						<div style='margin:5px'>
							<a target="_blank" href="{$urlBoleto}">
								<img src="{$confUsuario['urlEmpresa']}/hardness3/static/img/boleto.png" border=0>
							</a>
						</div>
						<div style='margin:5px'>
							Empresa:<b> {$g['C004']['C004_Nome_Abreviado']}</b><br>
							Vencimento em <b>{$vencimentoBoleto}<font style='color: var(--BGVermelho);'>{$dias_vencimento}</font></b><br>
							<a target='_blank' href="{$urlBoleto}">
								<font style='color:green;font-size:16px;font-weight:bold'>Clique aqui para imprimir o boleto</font>
							</a>
						</div>
					</div>
EOT;
				if(abs($mBOL001['Dias_Vencido']) >= ($dias/2)) {
					$prazoSuspensao = $dias-abs($mBOL001['Dias_Vencido']);
					$prazoSuspensao .= ($prazoSuspensao > 1) ? ' dias' : ' dia';
					$html .= <<<EOT
						<div style='margin-bottom: 10px; margin-left: 20px; font-weight: bold;color: var(--BGVermelho)'>
							ATENÇÃO! O ACESSO AO SISTEMA SERÁ SUSPENSO DENTRO DE {$prazoSuspensao}.
						</div>
					</div>
EOT;
				} else {
					$html .= <<<EOT
						<div style='margin-bottom: 10px; margin-left: 20px'>
							Desconsiderar caso a(s) pendência(s) já tenha(m) sido quitada(s), assim que for acusado em nosso sistema a(s) mesma(s) será(ão) baixada(s).
						</div>
					</div>
EOT;
				}
			}
		}
	}

	// Inicia uma conexão com o servidor central do parceiro no modelo sistema antigo
	$con = @mysqli_connect($sys['dbHost'], $sys['dbUser'], $sys['dbPass'],$sys['dbDatabase']);
	$g['conexaoBanco'] = $con; 

	return $html;
	
	// ! CÓDIGO COMENTADO PORQUE PROVAVELMENTE TEM HAVER COM OS CLIENTES DE NFe
	// if ($temBoleto == 'S') {
	// 	//-- Encontra todos os a vencer
	// 	$cClausula  = " select ";
	// 	$cClausula .= " * ";
	// 	$cClausula .= " from T135 ";
	// 	$cClausula .= " left join D024 on D024_Id=T135_D024_Id ";
	// 	$cClausula .= " where md5(D024_Cnpj)='".$cnpjEmpresaUsuaria."' ";
	// 	$cClausula .= "   and T135_Data_Termino<=(Curdate() + interval 30 day) ";
	// 	$cClausula .= "   and T135_Flag_Renovado!='S' ";
	// 	$cClausula .= "   and T135_Flag_Renovado!='C' ";
	// 	$cSql_T135=mysql_query($cClausula);
	// 	$erroMysql = mysql_error();
	// 	if(!empty($erroMysql)){
	// 		$html .= "Erro select boletos(4):".$erroMysql;
	// 		return $html;
	// 	}

	// 	if (mysql_num_rows($cSql_T135) > 0) {
	// 		$mT135=mysql_fetch_array($cSql_T135);
	// 		$html .= <<<EOT
	// 			<table width=500 border=0 cellpadding=5 cellspacing=0 bgcolor=white style='border: 1px dashed green; border-top:0px'>";
	// 				<tr>
	// 					<td colspan=2 style='border-bottom:0px solid silver'>
	// 						<font style='font-family:arial,verdana;font-size:12px;'>
	// 							{$mT135['D024_Nome_Empresa']}, <b>agradecemos à confiança e parceria</b> durante estes últimos 12 meses! A Hardness Sistemas vem trabalhando constantemente para ofereca você, serviços e produtos que atendam melhor as suas necessidades e expectativas!<br>
	// 					</td>
	// 				</tr>
	// 				<tr>
	// 					<td class=fonte style='font-size:12px;border-right:0px solid silver'>
	// 						Caso a sua empresa não queira renovar os serviços, basta nos enviar um e-mail pedindo o <font style='color: var(--BGVermelho)'>cancelamento</font> para {$mC001['PES004_Email']}.<br>
	// 					</td>
	// 				</tr>
	// 				<tr>
	// 					<td width=10 class=fonte colspan=2 style='font-size:12px;border-top:0px solid silver''>
	// 						<b>Obrigado!</b>
	// 						<BR>
	// 						<BR>
	// 						Equipe {$mC001['PES001_Nome_Fantasia']}
	// 					</td>
	// 				</tr>
	// 			</table>
	// EOT;
	// 	}
	// } else {
	// 	//-- Encontra todos os a vencer
	// 	$cClausula  = " select ";
	// 	$cClausula .= " * ";
	// 	$cClausula .= " from T135 ";
	// 	$cClausula .= " left join D024 on D024_Id=T135_D024_Id ";
	// 	$cClausula .= " where md5(D024_Cnpj)='".$cnpjEmpresaUsuaria."' ";
	// 	$cClausula .= "   and T135_Data_Termino>Curdate() ";
	// 	$cClausula .= "   and T135_Data_Termino<=(Curdate() + interval 30 day) ";
	// 	$cClausula .= "   and T135_Flag_Renovado!='S' ";
	// 	$cClausula .= "   and T135_Flag_Renovado!='C' ";
	// 	$cSql_T135=mysql_query($cClausula);
	// 	$erroMysql = mysql_error();
	// 	if(!empty($erroMysql)){
	// 		$html .= "Erro select boletos(5):".$erroMysql;
	// 		return $html;
	// 	}

	// 	if(mysql_num_rows($cSql_T135) > 0) {
	// 		$mT135=mysql_fetch_array($cSql_T135);
	// 		$html .= <<<EOT
	// 			<table width=500 border=0 cellpadding=5 cellspacing=0 bgcolor=#EAF7EA style='border: 2px solid green;'>
	// 				<tr>
	// 					<td colspan=2 style='border-bottom:0px solid silver' bgcolor=green>
	// 						<font style='font-family:arial,verdana;font-size:12px;'>
	// 						<font style='color:white;font-weight:bold;font-size:13px'>RENOVAÇÃO ANUAL</font>
	// 					</td>
	// 				</tr>
	// 				<tr>
	// 					<td colspan=2 style='border-bottom:0px solid silver'>
	// 						<font style='font-family:arial,verdana;font-size:12px;'>
	// 							{$mT135['D024_Nome_Empresa']}, <b>agradecemos à confiança e parceria</b> durante estes últimos 12 meses! A {$mC001['PES001_Nome_Fantasia']} vem trabalhando constantemente para oferecer a você, serviços e produtos que atendam melhor as suas necessidades e expectativas!<br>
	// 					</td>
	// 				</tr>
	// 				<tr>
	// 					<td class=fonte style='font-size:12px;border-right:0px solid silver'>
	// 						Caso a sua empresa não queira renovar os serviços, basta nos enviar um e-mail pedindo o <font style='color: var(--BGVermelho)'>cancelamento</font> para {$mC001['PES001_Email']}.<br>
	// 					</td>
	// 				</tr>
	// 				<tr>
	// 					<td width=10 class=fonte colspan=2 style='font-size:12px;border-top:0px solid silver''>
	// 						<b>Obrigado!</b>
	// 						<BR>
	// 						<BR>
	// 						Equipe {$mC001['PES001_Nome_Fantasia']}
	// 					</td>
	// 				</tr>
	// 			</table>
	// EOT;
	// 	}
	// }
}

function getBoletos(): string
{
	global $g, $confUsuario, $sys;

	$html = "";

	$Cnpj_Empresa_Usuaria = md5($g['C004']['C004_Cnpj']);

	$Local = 'S';
	$Usuario_Atual=63;
	$Gestao=true;


	if (strlen($Cnpj_Empresa_Usuaria)==14)
	{
		$Cnpj_Empresa_Usuaria=md5($Cnpj_Empresa_Usuaria);
	}

	$D024 = mysql_fetch_assoc(mysql_query("SELECT D024_Id FROM D024 WHERE md5(D024_Cnpj) = '{$Cnpj_Empresa_Usuaria}'"));
	$D024_Id = md5($D024['D024_Id']);

	$tem_boleto = 'N';
	if (!empty($Cnpj_Empresa_Usuaria)) {
		$sqlC004 = mysql_query("SELECT * FROM C004 WHERE C004_Id = '1'");
		$rowC004 = mysql_fetch_assoc($sqlC004);

		//Select dos boletos do cliente
		$cClausula  = " select ";
		$cClausula .= " T117_Id, ";
		$cClausula .= " T117_Data_Vencimento, ";
		$cClausula .= " D024_D046_Id, ";
		$cClausula .= " datediff(current_date(),T117_Data_Vencimento) dias ";
		$cClausula .= " from T117 ";
		$cClausula .= " left join D024 on D024_Id=T117_D024_Id ";
		$cClausula .= " left join T002 on T002_T117_Id=T117_Id ";
		$cClausula .= " left join D007 on D007_Id=T117_D007_Id ";
		$cClausula .= " where md5(D024_Cnpj)='".$Cnpj_Empresa_Usuaria."' ";
		$cClausula .= " and T117_Flag_Cancelado!='S'";
		$cClausula .= " AND (T002_T002_Id_Agrupado<=0 OR T002_T002_Id_Agrupado IS NULL)";
		$cClausula .= " order by T117_Data_Vencimento desc";

		$T117=mysql_query($cClausula);
		$mT117 = mysql_fetch_array($T117);
		$erroMysql = mysql_error();
		if(!empty($erroMysql)){
			$html .= "Erro select boletos(1):".$erroMysql;
			return $html;
		}

		//Verifica se os clientes tipo "NF-E" não possuem boleto ou tem boletos vencidos a mais de 366 dias
		if ((mysql_num_rows($T117) <= 0 || $mT117['dias'] > 366) && ($mT117['D024_D046_Id'] == '2')) {
			$html .= "<br><br><br><font style='font-size:18;font-weight:bold;color: var(--BGVermelho);background:white'>EXISTEM PENDENCIAS EM SEU CADASTRO, FAVOR ENTRAR EM CONTATO COM NOSSO SUPORTE PELO TELEFONE {$rowC004['C004_DDD_Telefone']} {$rowC004['C004_Telefone']}.</font>";
			return $html;
		}

		//Select dos boletos do cliente em abertos (vencidos e a vencer)
		$cClausula  = " SELECT ";
		$cClausula .= " T117_Id, ";
		$cClausula .= " T117_D024_Id, ";
		$cClausula .= " T117_Data_Emissao, ";
		$cClausula .= " T117_Data_Vencimento, ";
		$cClausula .= " T117_Valor_Total, ";
		$cClausula .= " T117_Nosso_Numero,";
		$cClausula .= " T117_Flag_Cancelado,";
		$cClausula .= " T002_Data_Vencimento, ";
		$cClausula .= " D007_Nome_Banco, ";
		$cClausula .= " DATEDIFF(T117_Data_Vencimento,CURRENT_DATE()) AS Dias_Vencer, ";
		$cClausula .= " DAYNAME(T117_Data_Vencimento) AS DiaSemanaNome, ";
		$cClausula .= " DATE_FORMAT(T117_Data_Vencimento,'%w') AS DiaSemana, ";
		$cClausula .= " DATE_FORMAT(CURRENT_DATE(),'%w') AS DiaSemanaHoje, ";
		$cClausula .= " DATEDIFF(CURRENT_DATE(),T117_Data_Vencimento) AS Dias_Vencido, ";
		$cClausula .= " DATEDIFF(T117_Data_Vencimento,T002_Data_Vencimento) AS Dias_Prorrogado ";
		$cClausula .= " FROM T117 ";
		$cClausula .= " LEFT JOIN D024 ON D024_Id=T117_D024_Id ";
		$cClausula .= " LEFT JOIN T002 ON T002_T117_Id=T117_Id ";
		$cClausula .= " LEFT JOIN D007 ON D007_Id=T117_D007_Id ";
		$cClausula .= " WHERE md5(D024_Cnpj)='".$Cnpj_Empresa_Usuaria."' ";
		$cClausula .= " AND T117_Flag_Cancelado='N' ";
		$cClausula .= " AND T002_Data_Recebimento='0000-00-00' ";
		$cClausula .= " AND (T002_T002_Id_Agrupado<=0 OR T002_T002_Id_Agrupado IS NULL)";
		$cClausula .= " ORDER BY T117_Data_Vencimento";
		$cSql_T117=mysql_query($cClausula);
		$erroMysql = mysql_error();
		if(!empty($erroMysql)){
			$html .= "Erro select boletos(2):".$erroMysql;
			return $html;
		}

		if (mysql_num_rows($cSql_T117) > 0){
			//Impressão dos boletos do cliente
			while ($mT117 = mysql_fetch_array($cSql_T117)) {
				//Caso Dias_Vencido<=0
				//Hoje seja segunda  e DiasSemana do vencimento for Sab/Dom)
				if(($mT117['Dias_Vencido']<=0) || (($mT117['DiaSemanaHoje']==1) && ($mT117['DiaSemana']==0 || $mT117['DiaSemana']==6))){
					if ($mT117['Dias_Vencer'] == 0) {
						$dias_vencimento = " (hoje)";
					} elseif ($mT117['Dias_Vencer'] == 1) {
						$dias_vencimento = " (amanhã) ";
					} elseif ($mT117['Dias_Vencer'] > 2) {
						$dias_vencimento = " (daqui a {$mT117['Dias_Vencer']} dias)";
					}
					$path = base64_encode($sys['urlEmpresa'] . $g['pathWeb'] . "dados_usuarios/{$sys['dbDatabase']}/");
					$md5BoletoCliente = md5($mT117['T117_Id'].$mT117['T117_D024_Id']);
					// $vencimentoBoleto = troca_data_dma($mT117['T117_Data_Vencimento']);
					$vencimentoBoleto = gCorrigeData($mT117['T117_Data_Vencimento']);
					$urlBoleto = $sys['urlEmpresa']."/hardness3/prog/externo/cobranca/gerarBoletoEmpresa.php?ajax=true&cQuery=".$md5BoletoCliente."&path=".$path."&buscarBoletoBanco=suporte";
					$html .= <<<EOT
					<div id="divBoleto" class="boletoNotificacao">
						<div style='margin-top:10px; margin-left: 20px; width: 615px;'>
							Olá! Já está disponível o boleto para pagamento.
						</div>
						<div style='display:grid; grid-template-columns: 80px 520px;margin:5px;margin-left:15px;'>
							<div style='margin:5px'>
								<a target="_blank" href="{$urlBoleto}">
									<img src="{$confUsuario['urlEmpresa']}/hardness3/static/img/boleto.png" border=0>
								</a>
							</div>
							<div style='margin:5px'>
								Empresa: <b>{$g['C004']['C004_Nome_Abreviado']}</b><br>
								Vencimento em <b>{$vencimentoBoleto} {$dias_vencimento} </b><br>
								<a target="_blank" href="{$urlBoleto}">
									<font style="font-size:16px;color:green;font-weight:bold">Clique aqui para imprimir o boleto</font>
								</a>
							</div>
						</div>
						<div style='margin-bottom: 10px; margin-left: 20px'>
							O pagamento pode ser realizado pela internet ou em qualquer agência bancária até vencimento.
						</div>
					</div>
EOT;
				} else {
					//Caso Dias_Vencido > 0
					//Caso Dias_Vencido>2 e DiasSemana vencimento igual Sab/Dom
					if($mT117['Dias_Vencer'] < 0) {
						$dias_vencimento = " (".abs($mT117['Dias_Vencer'])." dias atrás)";
					} else {
						$dias_vencimento = "";
					}
					$dias = 10;
					if (isset($Gestao)) {
						$dias = 15;
					}
					$path = base64_encode($sys['urlEmpresa'] . $g['pathWeb'] . "dados_usuarios/{$sys['dbDatabase']}/");
					$md5BoletoCliente = md5($mT117['T117_Id'].$mT117['T117_D024_Id']);
					$vencimentoBoleto = gCorrigeData($mT117['T117_Data_Vencimento']);
					$urlBoleto = $sys['urlEmpresa']."/hardness3/prog/externo/cobranca/gerarBoletoEmpresa.php?ajax=true&cQuery=".$md5BoletoCliente."&path=".$path."&buscarBoletoBanco=suporte";
					$html .= <<<EOT
					<div id="divBoleto" class="boletoNotificacao">
						<font style='margin-top:10px; margin-left: 20px; width: 615px; color: var(--BGVermelho);'>
						Consta em nossa base a(s) pendendência(s) abaixo relacionada(s)
						</font>
						<div style='display:grid; grid-template-columns: 80px 520px;margin:5px;margin-left:15px;'>
							<div style='margin:5px'>
								<a target="_blank" href="{$urlBoleto}">
									<img src="{$confUsuario['urlEmpresa']}/hardness3/static/img/boleto.png" border=0>
								</a>
							</div>
							<div style='margin:5px'>
								Empresa:<b> {$g['C004']['C004_Nome_Abreviado']}</b><br>
								Vencimento em <b>{$vencimentoBoleto}<font style='color: var(--BGVermelho);'>{$dias_vencimento}</font></b><br>
								<a target='_blank' href="{$urlBoleto}">
									<font style='color:green;font-size:16px;font-weight:bold'>Clique aqui para imprimir o boleto</font>
								</a>
							</div>
						</div>
EOT;
					if(abs($mT117['Dias_Vencido']) >= ($dias/2)) {
						$prazoSuspensao = $dias-abs($mT117['Dias_Vencido']);
						$prazoSuspensao .= ($prazoSuspensao > 1) ? ' dias' : ' dia';
						$html .= <<<EOT
							<div style='margin-bottom: 10px; margin-left: 20px; font-weight: bold;color: var(--BGVermelho)'>
								ATENÇÃO! O ACESSO AO SISTEMA SERÁ SUSPENSO DENTRO DE {$prazoSuspensao}.
							</div>
						</div>
EOT;
					} else {
					
						$html .= <<<EOT
							<div style='margin-bottom: 10px; margin-left: 20px'>
								Desconsiderar caso a(s) pendência(s) já tenha(m) sido quitada(s), assim que for acusado em nosso sistema a(s) mesma(s) será(ão) baixada(s).
							</div>
						</div>
EOT;
					}
				}
			}
			$tem_boleto = 'S';
		}

		if ($tem_boleto == 'S') {
			//-- Encontra todos os a vencer
			$cClausula  = " select ";
			$cClausula .= " * ";
			$cClausula .= " from T135 ";
			$cClausula .= " left join D024 on D024_Id=T135_D024_Id ";
			$cClausula .= " where md5(D024_Cnpj)='".$Cnpj_Empresa_Usuaria."' ";
			$cClausula .= "   and T135_Data_Termino<=(Curdate() + interval 30 day) ";
			$cClausula .= "   and T135_Flag_Renovado!='S' ";
			$cClausula .= "   and T135_Flag_Renovado!='C' ";
			$cSql_T135=mysql_query($cClausula);
			$erroMysql = mysql_error();
			if(!empty($erroMysql)){
				$html .= "Erro select boletos(4):".$erroMysql;
				return $html;
			}

			if (mysql_num_rows($cSql_T135) > 0) {
				$mT135=mysql_fetch_array($cSql_T135);
				$html .= <<<EOT
					<table width=500 border=0 cellpadding=5 cellspacing=0 bgcolor=white style='border: 1px dashed green; border-top:0px'>";
						<tr>
							<td colspan=2 style='border-bottom:0px solid silver'>
								<font style='font-family:arial,verdana;font-size:12px;'>
									{$mT135['D024_Nome_Empresa']}, <b>agradecemos à confiança e parceria</b> durante estes últimos 12 meses! A Hardness Sistemas vem trabalhando constantemente para ofereca você, serviços e produtos que atendam melhor as suas necessidades e expectativas!<br>
							</td>
						</tr>
						<tr>
							<td class=fonte style='font-size:12px;border-right:0px solid silver'>
								Caso a sua empresa não queira renovar os serviços, basta nos enviar um e-mail pedindo o <font style='color: var(--BGVermelho)'>cancelamento</font> para {$rowC004['C004_E_Mail']}.<br>
							</td>
						</tr>
						<tr>
							<td width=10 class=fonte colspan=2 style='font-size:12px;border-top:0px solid silver''>
								<b>Obrigado!</b>
								<BR>
								<BR>
								Equipe {$rowC004['C004_Nome_Abreviado']}
							</td>
						</tr>
					</table>
EOT;
			}
		} else {
			//-- Encontra todos os a vencer
			$cClausula  = " select ";
			$cClausula .= " * ";
			$cClausula .= " from T135 ";
			$cClausula .= " left join D024 on D024_Id=T135_D024_Id ";
			$cClausula .= " where md5(D024_Cnpj)='".$Cnpj_Empresa_Usuaria."' ";
			$cClausula .= "   and T135_Data_Termino>Curdate() ";
			$cClausula .= "   and T135_Data_Termino<=(Curdate() + interval 30 day) ";
			$cClausula .= "   and T135_Flag_Renovado!='S' ";
			$cClausula .= "   and T135_Flag_Renovado!='C' ";
			$cSql_T135=mysql_query($cClausula);
			$erroMysql = mysql_error();
			if(!empty($erroMysql)){
				$html .= "Erro select boletos(5):".$erroMysql;
				return $html;
			}

			if(mysql_num_rows($cSql_T135) > 0) {
				$mT135=mysql_fetch_array($cSql_T135);
				$html .= <<<EOT
					<table width=500 border=0 cellpadding=5 cellspacing=0 bgcolor=#EAF7EA style='border: 2px solid green;'>
						<tr>
							<td colspan=2 style='border-bottom:0px solid silver' bgcolor=green>
								<font style='font-family:arial,verdana;font-size:12px;'>
								<font style='color:white;font-weight:bold;font-size:13px'>RENOVAÇÃO ANUAL</font>
							</td>
						</tr>
						<tr>
							<td colspan=2 style='border-bottom:0px solid silver'>
								<font style='font-family:arial,verdana;font-size:12px;'>
									{$mT135['D024_Nome_Empresa']}, <b>agradecemos à confiança e parceria</b> durante estes últimos 12 meses! A {$rowC004['C004_Nome_Abreviado']} vem trabalhando constantemente para oferecer a você, serviços e produtos que atendam melhor as suas necessidades e expectativas!<br>
							</td>
						</tr>
						<tr>
							<td class=fonte style='font-size:12px;border-right:0px solid silver'>
								Caso a sua empresa não queira renovar os serviços, basta nos enviar um e-mail pedindo o <font style='color: var(--BGVermelho)'>cancelamento</font> para {$rowC004['C004_E_Mail']}.<br>
							</td>
						</tr>
						<tr>
							<td width=10 class=fonte colspan=2 style='font-size:12px;border-top:0px solid silver''>
								<b>Obrigado!</b>
								<BR>
								<BR>
								Equipe {$rowC004['C004_Nome_Abreviado']}
							</td>
						</tr>
					</table>
EOT;
			}
		}
	}

	return $html;
}
