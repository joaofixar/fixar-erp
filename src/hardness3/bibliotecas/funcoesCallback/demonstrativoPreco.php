<?php
namespace hardness;

use function hardness\montarDemonstrativo as HardnessMontarDemonstrativo;

function demonstrativoPreco($array = array(), $param = array()){
	global $g;

	$titulo = isset($param[0]) ? $param[0] : '';
	$demonstrativo = isset($param[1]) ? $param[1] : '';
	$local = isset($param[2]) ? $param[2] : 'right';
	if (isset($param[3]) && $param[3]) {
		$array['campoAtual'] = gCorrigeNumero($array['campoAtual'], $param[3]);
	}
	$extra = isset($param[4]) ? $param[4] : '';
	if (isset($param[5]) && $param[5]) {
		$array['campoAtual'] = $param[5];
	}
	$width = (isset($param[6]) && $param[6]) ? $param[6] : '236'; //236 é o width default do popover
	$center = (isset($param[7]) && $param[7]) ? $param[7] : 'N';

	if($width == '-1'){
		$width = $array['todosCampos']['width'];
	}
	$id = 'popover-' . uniqid();

	$notificacao = base64_encode(montarDemonstrativo(unserialize($array['todosCampos']["$demonstrativo"])));

	log($notificacao);

	return "<a id=\"{$id}\" href=\"#\" style='text-decoration: none;' onclick=\"demonstrativoPreco('{$id}', '{$titulo}', '{$notificacao}', '{$local}', '{$extra}', '{$width}', '{$center}', '{$g['divId']}', '{$g['divIdParent']}', '{$g['divIdRoot']}');\"><u>{$array['campoAtual']}</u></a>";
}

function montarDemonstrativo($demonstrativo){
    $html = <<<EOT
        <script>
            $(function() {
                $('#divDemonstrativo h4').click(function() {
                    if($(this).prev().hasClass('cabDemonstrativo') || $('.popover').find('.arrow').offset() == null){
                        return false;
                    }
                    heightInicial = parseFloat($('.popover').css('height'));
                    if ($('.popover').find('.arrow').offset() != null) {
                        arrowInicial = $('.popover').find('.arrow').offset().top;
                    }
                    $(this).prev().toggle();
                    heightFinal = parseFloat($('.popover').css('height'));
                    var popoverHeight2 = parseFloat($('.popover').css('height'));
                    var contentHeight2 = parseFloat($('.popover').closest('.ui-tabs-panel').css('height'));

                    var novoTop = $('.popover').position().top - ((heightFinal-heightInicial)/2);
                    var topUsado = '1 ' + novoTop;
                    if((novoTop + heightFinal) > contentHeight2){
                        $('.popover').data($(this).attr('id'), ((novoTop + heightFinal) - contentHeight2));
                        novoTop = novoTop - ((novoTop + heightFinal) - contentHeight2);
                        topUsado = '2 ' + novoTop;
                    } else if(heightInicial > heightFinal){
                        novoTop = $('.popover').position().top + ((heightInicial-heightFinal)/2);
                        if($('.popover').data($(this).attr('id')) > 0){
                            novoTop = novoTop + $('.popover').data($(this).attr('id'));
                        }
                        topUsado = '3 ' + novoTop;
                    }
                    $('.popover').css('top', novoTop);
                    arrowFinal = $('.popover').find('.arrow').offset().top;

                    if (typeof($('.popover').find('.arrow').offset()) != null) {
                        $('.popover').find('.arrow').css('top', $('.popover').find('.arrow').position().top + (arrowInicial - arrowFinal));
                    }

                    return false;
                });
                $('.contentCollapse').click(function() {
                    $(this).next().trigger('click');
                });
            });
        </script>
        <div id="divDemonstrativo" style="color: #000; margin-bottom: 2px;">
EOT;
    $tagAberta = false;
    foreach ($demonstrativo as $campo) {
        if ($campo['operacao'] == 'cab') {
            $html .= <<<EOT
                <div class="cabDemonstrativo" style="width: 100%; float: left; font-size:11px; font-weight: bold; {$campo['css']}" onclick="{$campo['onclick']}">
                    {$campo['valor']}
                </div>
EOT;
            continue;
        }
        $campo['css'] = ($campo['exibir'] == 'N') ? 'color: gray;' : $campo['css'];

        if($campo['tipo'] == 'total' || $campo['tipo'] == 'fixo'){
            if($tagAberta == true){
                $html .= '</div><h4 id="h4-'.uniqid().'" style="border: 0px; font-weight: bold; margin: 0px;">';
            } else {
                $html .= '<h4 id="h4-'.uniqid().'" style="border: 0px; font-weight: bold; margin: 0px;">';
            }
            $tagAberta = true;
        }

        $campo['operacao'] = (empty($campo['operacao'])) ? '&nbsp;' : $campo['operacao'];
        $html .= <<<EOT
                <div style="width:11px; float: left; font-size:11px; padding: 2px 0px; {$campo['css']}">
                    {$campo['operacao']}
                </div>
                <div style="width:120px; float: left; font-size:11px; padding: 2px 0px; {$campo['css']}">
                    {$campo['campo']}
                </div>
                <div style='width:75px; float: left; font-size:11px; padding: 2px 0px; text-align: right; {$campo['css']}'>
                    {$campo['valor']}
                </div>
EOT;
        if($campo['tipo'] == 'total' || $campo['tipo'] == 'fixo'){
            $html .= '</h4><div class="contentCollapse" style="border: 0; background: none; color: #000; padding: 0px; width: 100%; float: left;">';
        }
    }
    return $html;
}