<?php
namespace hardness;

include 'bibliotecas/open-flash-chart-2/php-ofc-library/open-flash-chart.php';

switch ($g['r'][3]) {                 
	case 'etc012GraphFluxoCaixa':
		// Geração dos dados
		if (!isset($r_graph)) {			
			$r_data = $g['r'][4];
			$data = isset($r_data) ? $r_data : date('Y-m-d');
			$data = explode('-', $data);
			
			// Pega infos do banco
			$line_T002 = array();
			$line_T015 = array();
			$line_Max = 10;
			$eixoX = array();
			for ($i = 1; $i <= 31; $i++) {
				$i_ = ($i < 10) ? '0' . $i : $i;
				$eixoX[] = (string) $i_;
				$data_ = "{$data[0]}-{$data[1]}-{$i_}";
				$sql = "SELECT SUM(T002_Valor_Duplicata) as Total FROM T002 WHERE T002_Data_Recebimento = '{$data_}';";
				$res = mysql_query($sql);
				if (mysql_numrows($res) == 0) {
					$line_T002[] = 0;
				} else {
					$row = mysql_fetch_row($res);
					$line_T002[] = (int) $row[0];
					$line_Max = ($line_Max < $row[0]) ? $row[0] : $line_Max;
				}
				$sql = "SELECT SUM(T015_Valor_Duplicata) as Total FROM T015 WHERE T015_Data_Pagamento = '{$data_}';";
				$res = mysql_query($sql);
				if (mysql_numrows($res) == 0) {
					$line_T015[] = 0;
				} else {
					$row = mysql_fetch_row($res);
					$line_T015[] = (int) $row[0];
					$line_Max = ($line_Max < $row[0]) ? $row[0] : $line_Max;
				}
			}
			
			// Grafico
			$chart = new \open_flash_chart();
			$chart->set_bg_colour( '#FFFFFF' );
			
			// Titulo
			$title = new \title("Movimentação Fluxo Caixa - {$data[1]}/{$data[0]}");
			$chart->set_title( $title );
			
			// Linhas
			$line1 = new \line();
			$line1->set_values( $line_T002 );
			$line1->set_colour( '#1475BA' );
			$line2 = new \line();
			$line2->set_values( $line_T015 );
			$line2->set_colour( '#FC0000' );
			$chart->add_element( $line1 );
			$chart->add_element( $line2 );
			
			// Eixo Y
			$y = new \y_axis();
			$line_Max += ($line_Max > 0) ? ($line_Max * 0.05) : 0;
			$line_Max = round($line_Max);
			$y->set_range(0, $line_Max, 10);
			$y->set_colours( '#D3D3C2', '#D3D3C2' );
			$chart->set_y_axis($y);
			// Eixo Y Label
			$y_legend = new \y_legend( 'Valor' );
			$y_legend->set_style( '{font-size: 14px; font-weight: bold;}' );
			$chart->set_y_legend( $y_legend );
			
			// Eixo X
			$x = new \x_axis();
			$x->set_colours( '#D3D3C2', '#D3D3C2' );
			$x->set_labels_from_array( $eixoX );
			$x->offset(false);
			$chart->set_x_axis( $x );
			// Eixo X Label
			$x_legend = new \x_legend( 'Recebido x Pago' );
			$x_legend->set_style( '{font-size: 12px; font-weight: bold;}' );
			$chart->set_x_legend( $x_legend );
			
			echo $chart->toPrettyString();			
		} else {
			echo <<<EOT
				<div id="{$g['divId']}_graph" style="width: 100%; height: 100%;"></div>
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				swfobject.embedSWF(
					"/hardness3/static/swf/open-flash-chart.swf", "{$g['divId']}_graph", "100%", "100%", "9.0.0", "expressInstall.swf", { "data-file":"/etc/etc012/graph/etc012GraphFluxoCaixa/{$r_data}/?ajax=true" }
				);
				} </script></div>
EOT;
		}
	break;
}
?>
