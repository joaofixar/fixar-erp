<?php 

namespace hardness;

$resposta = array('code' => true, 'data' => array());

    case 'codigoBarra':

        global $g;
        
        echo <<<EOT

            <style>
                
                .header-movimentacao {width: 100%; height: 30px; text-align: center; background: #E3E1E1; padding: 10px 0 0 10px; font-size: 1.5em; color: green; font-weight: bold; border-bottom: 1px solid #BABABA;}
                .conteudo-movimentacao {float: right; font-weight: bold; margin: -3px 36px 0 0; font-size: 1.3em; color: blue;}
                .nav-info { width: 100%; height: 110px; display: flex; justify-content: center;}
                .nav-item { gap: 10px; width: 100%; display: flex; max-width: 250px; font-weight: bold; align-items: center; justify-content: center; text-transform: uppercase;}
                .nav-item p { background: black; color: white; padding: 6px;}
                .nav-item h5 { font-size: 16px;}
                .bottom-container { width: 100%; height: 40px; display: flex; justify-content: center; position: absolute; bottom: 15px;}
                .bottom-container button { font-weight: bold !important; background: rgba(0, 128, 0, 0.418) !important; max-width: 180px !important; color: #ffffff !important; width: 100% !important; border: none !important; transition: 0.3s !important;}
                .bottom-container button:hover { transition: 0.3s !important; background: rgba(0, 128, 0, 0.671) !important;}
                .produto-container { display: flex; flex-direction: column-reverse;}
                .produto-item { cursor: default; height: 60px; width: 100%; display: flex; max-width: 100%; position: relative; align-items: center; justify-content: center;}
                .produto-item input { font-weight: bold; font-size: 16px; position: absolute; right: 20px; font-size: 16px; display: flex; justify-content: center; align-items: center; text-align: center; height: 25px; width: 40px; border-radius: 5px; border: none; outline: none;}
                .produto-item button { background-color: #000; border-radius: 20px; position: absolute; font-weight: bold; color: #ffffff; font-size: 12px; cursor: pointer; outline: none; height: 20px; border: none; width: 20px; left: 20px;}
                .produto-item section { width: 50px; left: 180px; height: 50px; object-fit:cover; position: absolute; margin-top: 10px !important;}
                .produto-item h5 {color: black; font-size: 18px; font-weight: bold;}
                .produto-item img {border-radius: 5px !important; cursor: pointer;}
                .produto-item p {margin: 0 !important; font-weight: bold; font-size: 14px;}
                .prod-sku {background: rgb(37 107 37 / 22%); justify-content: center;flex-direction: column;align-items: center;position: absolute;display: flex;width: 100px;height: 100%;left: 60px;gap: 2px;}
                .prod-ean-forn {background: rgb(37 107 37 / 22%); justify-content: center;flex-direction: column;align-items: center;position: absolute;display: flex;width: 190px;height: 100%;left: 250px;gap: 2px;}
                .prod-qtd-estoque {background: rgb(37 107 37 / 22%); justify-content: center;flex-direction: column; align-items: center; height: 100%; width: 80px; position: absolute; display: flex; right: 100px;}
                .prod-cod-fornecedor {background: rgb(37 107 37 / 22%); justify-content: center;flex-direction: column; align-items: center; height: 100%; width: 200px; position: absolute; display: flex; right: 210px;}

            </style>

            <main class="nav-info">
                <div class="nav-item"><h5>Origem:</h5><p id="origem-txt">Indefinida</p></div>
                <div class="nav-item"><h5>Destino:</h5><p id="destino-txt">Indefinido</p></div>
            </main>

            <div class="header-movimentacao">
                <span class="cabecalho">Leia a Origem...</span>
            </div>

            <div class="corpo" style="width: 100%; height: 100%; overflow-y: scroll;">
                <div id="produtos-txt" class="produto-container"></div>
                <div id="produtos-lista" style="display:none;"></div>
                <p id="tp-locacao-destino" style="display:none;"></p>

                <div class="bottom-container">
                    <button type="button" id="btnConcluir">
                        Concluir
                    </button>
                </div>
            </div>
EOT;
        echo <<<EOT

            <div class="divHidden"><script type="text/javascript"> 
            
            if (typeof jQuery != 'undefined') {
            
                // Elementos
                var cabecalho = $('#{$g['divId']} .cabecalho');
                var corpo = $('#{$g['divId']} .corpo');
                var rodape = $('#{$g['divId']} .rodapeCodBarra');

                // Ajustes do elementos
                $('#{$g['divId']}').parent().css({'padding': '0'});
                cabecalho.width(cabecalho.width() - 10);
                corpo.height(corpo.height() - (41 * 2));
                rodape.width(rodape.width() - 10);

                // COD BARRAS
                codBarraLastTime = 0;
                codBarraScan = '';

                // MONITORANDO CONFERENCIA
                var tipoLocacaoOrigem = '';
                var colorState = false;
                var quantidade = [];
                var origemValue = 0;
                var origemId = 0;
                var qtdTotal = 0;
                var tpd = '';

                function itemColor () {
                    if (!colorState) { colorState = true; return "background: rgb(58 117 58 / 60%);";} 
                    else { colorState = false; return "background: rgba(0, 128, 0, 0.418);";}
                }

                $(document).unbind('keydown').bind('keydown', function(e) {
                    // 46 - delete
                    if ( e.which == 46 ) {
                        corpo.find('.item:first').remove();
                        verCodBarraAjustesUI();
                    }
                });

                function listaProd() {
                    let produtosTxt = quantidade.map(function(item) { return item.item;}).join("");
                    $('#produtos-lista').text(produtosTxt);
                }

                function atualizaQtdTotal() {
                    qtdTotal = 0;
                    quantidade.forEach(item => {
                        let parts = item.item.split('Quantidade | ');
                        let qtd = parseInt(parts[1]);
                        qtdTotal += qtd;
                    });
                }

                function ErrorMessage(message) {
                    cabecalho.css('color', 'red');
                    cabecalho.html(message);
                    reproduzirSomErro();
                }

                $(document).unbind('keypress').bind('keypress', function(e) {
                    // não faz nada se tiver o focus no input
                    if ($(':focus').is('input')) { return true; }
                    // 
                    var agora = new Date().getTime();
                    if ((agora - codBarraLastTime) > 500 && !codBarraLendo) {
                        codBarraScan = '';
                    }
                    // 42 = barra de espaço
                    if ( e.which == 42 ) {
                        codBarraScan = '';
                        cabecalho.css('color', 'green');
                        cabecalho.html('Lendo Codigo: ');
                        codBarraLendo = true;
                    }
                    // 8 - backspace
                    if ( e.which == 8 ) {
                        if (codBarraScan != '' && codBarraScan != '*') {
                            codBarraScan = codBarraScan.substr(0,codBarraScan.length-1);
                            cabecalho.html('Lendo código de: ' + codBarraScan);
                        }
                        return false;
                    }
                    
                    // 13 - enter
                    if ( e.which == 13 ) {
                        if (codBarraScan != '') {

                            cabecalho.css('color', 'blue');
                            cabecalho.html('Procurando origem: ' + codBarraScan + ' ...');
                            if (codBarraLendo) { codBarraScan = codBarraScan.substr(1);}
                            
                            $.ajax({
                                type: 'post',
                                dataType: 'json',
                                url: '/etc/etc022/ajax/etc022CodigoOrigem/',
                                data: 'ajax=true&codbarra=' + encodeURIComponent(codBarraScan) + '&originValue=' + encodeURIComponent(origemValue) + '&origemId=' + encodeURIComponent(origemId) +'&callback=?',
                                success: function(request) {
                                    if (!request.code) {
                                        
                                        // ITEM NAO ENCONTRADO 
                                        
                                        if (request.status === "2") { ErrorMessage(request.info);} else {cabecalho.css('color', 'green');}
                                        
                                        //INSERE NOVA ORIGEM E DESTINO
                                        
                                        if ($('#origem-txt').text().toUpperCase() === 'INDEFINIDA') {
                                            $('#origem-txt').html(request.locacao);
                                            origemId = request.locacaoId;
                                            cabecalho.html(request.info);
                                            origemValue = request.status;
                                            tipoLocacaoOrigem = request.locacaoTipo;
                                        } else {
                                            $('#destino-txt').html(request.locacao);
                                            $('#tp-locacao-destino').html(request.locacaoTipo);
                                            cabecalho.html(request.info);
                                        }

                                        // INSERE NOVOS PRODUTOS
                                        if (request.produtos) {
                                            if (request.produtos.QTDEST > 0) {

                                                let i = quantidade.length;
                                                let qtdEstoque = Math.floor(request.produtos.QTDEST);
                                                let qtdFormatada =  qtdEstoque < 10 ? '0' + qtdEstoque : qtdEstoque;
                                                
                                                let totalInserido = quantidade.reduce((ind, item) => {
                                                    
                                                    let fragProduto = item.item.split(" | ");
                                                    
                                                    if (fragProduto[0] === request.produtos.SKU) {
                                                        
                                                        let qtdItem = parseInt(fragProduto[6], 10);
                                                        if (!isNaN(qtdItem)) {
                                                            ind += qtdItem; 
                                                        }
                                                    }
                                                    return ind;
                                                }, 1);

                                                if (totalInserido <= qtdEstoque) {
                                                        
                                                    function recalculateTotalInserted() {
                                                        let totalInserido = quantidade.reduce((acc, item) => {
                                                            let itemDetails = item.item.split(" | ");
                                                            if (itemDetails[0] === request.produtos.SKU) {
                                                                let itemQuantity = parseInt(itemDetails[6], 10);
                                                                if (!isNaN(itemQuantity)) {
                                                                    acc += itemQuantity;
                                                                }
                                                            }
                                                            return acc;
                                                        }, 1);
                                                    }

                                                    var ProdItem = $('<div>').attr('style', itemColor()).addClass('produto-item').append(
                                                        $('<button>').text('X').bind('click', function() {
                                                            $(this).closest('div').remove();

                                                            let indexToRemove = quantidade.findIndex(item => {
                                                                let itemDetails = item.item.split(" | ");
                                                                return itemDetails[0] === request.produtos.SKU && itemDetails[2] === $(this).closest('div').find('h5').text();
                                                            });
                                                            if (indexToRemove !== -1) {
                                                                quantidade.splice(indexToRemove, 1);
                                                            }
                                                            recalculateTotalInserted();
                                                        }),

                                                        $('<div>').addClass("prod-sku").append( 
                                                            $('<p>').text("SKU"), 
                                                            $('<p>').text(request.produtos.SKU)
                                                        ),
                                                        $('<section>').html(request.img),
                                                        $('<div>').addClass("prod-ean-forn").append( 
                                                            $('<p>').text("EAN DO FORNECEDOR"), 
                                                            $('<p>').text(request.produtos.EANFORN)
                                                        ),
                                                        $('<h5>').text(request.produtos.DESCRICAO),
                                                        $('<div>').addClass("prod-qtd-estoque").append( 
                                                            $('<p>').text("ESTOQUE"), 
                                                            $('<p>').text(qtdFormatada)
                                                        ),
                                                        $('<div>').addClass("prod-cod-fornecedor").append( 
                                                            $('<p>').text("CODIGO DO FORNECEDOR"), 
                                                            $('<p>').text(request.produtos.CODFORN)
                                                        ),
                                                        $('<input>').attr({'placeholder': "01",'data-index': i}).bind('input', function() {

                                                            var newQtd = parseInt($(this).val(), 10);
                                                            if (newQtd > qtdEstoque) {
                                                                ErrorMessage("Quantidade solicitada excede o estoque disponível!");
                                                                $(this).val(1);
                                                                return;
                                                            }

                                                            if (quantidade[i]) {
                                                                quantidade[i].item = 
                                                                    request.produtos.SKU     + " | " + request.produtos.EANFORN + " | " + request.produtos.DESCRICAO + " | " + 
                                                                    request.produtos.MARCA   + " | " + request.produtos.QTDEST  + " | " + 'Quantidade | ' + newQtd   + " | " + 
                                                                    request.produtos.EAN     + " | " + request.produtos.CODFORN + " | " + request.produtos.D001_Id   + " | " +
                                                                    request.produtos.T066_Id + " | " + request.produtos.D009_Id
                                                                ;
                                                                atualizaQtdTotal();
                                                            }
                                                        })
                                                    );
                                                    $('#produtos-txt').append(ProdItem);

                                                    quantidade.push({ item: 
                                                        request.produtos.SKU     + " | " + request.produtos.EANFORN + " | " + request.produtos.DESCRICAO + " | " + 
                                                        request.produtos.MARCA   + " | " + request.produtos.QTDEST  + " | " + 'Quantidade | ' + 1        + " | " + 
                                                        request.produtos.EAN     + " | " + request.produtos.CODFORN + " | " + request.produtos.D001_Id   + " | " +
                                                        request.produtos.T066_Id + " | " + request.produtos.D009_Id
                                                    });
                                                    cabecalho.html(request.info);
                                                } else {
                                                    ErrorMessage("Quantidade excedida " + request.produtos.DESCRICAO);
                                                }
                                            } else {
                                                ErrorMessage("Produto " + request.produtos.DESCRICAO + " Sem estoque!!");
                                            }
                                        }
                                    } else {
                                        ErrorMessage("Item inexistente, de outra locação ou empresa!!");
                                    }
                                }
                            });

                            codBarraScan = '';
                        } 

                        codBarraLendo = false;
                    } else {
                        codBarraLastTime = agora;
                        codBarraScan = codBarraScan + String.fromCharCode(e.which);
                        if (codBarraLendo) {
                            cabecalho.css('color', 'blue');
                            cabecalho.html('Lendo codigo: ' + codBarraScan);
                        }
                    }
            
                });
EOT;
        echo <<<EOT

            $('#btnConcluir').bind('click', function(e) {

                listaProd();
                atualizaQtdTotal(); 

                let result = agregaQuantidades(quantidade);
                if (!result.flagEstoque) {return;}

                let objQtd = JSON.stringify(result.items);
                
                origem  = document.getElementById('origem-txt').innerText;
                destino = document.getElementById('destino-txt').innerText;
                tpd = document.getElementById('tp-locacao-destino').innerText;
                                
                $.ajax({
                    type: 'post',
                    dataType: 'json',
                    url: '/etc/etc022/ajax/etc022InsertMovimentacao/',
                    data: 'ajax=true&origem=' + encodeURIComponent(origem) + '&produtos=' + encodeURIComponent(objQtd) + '&destino=' + encodeURIComponent(destino) + '&qtdTotal=' + encodeURIComponent(qtdTotal) + '&tipoOrigem=' + encodeURIComponent(tipoLocacaoOrigem) + '&tipoDestino=' + encodeURIComponent(tpd) + '&callback=?',
                    success: function(request) {
                        if (request.code) {
                            cabecalho.css('color', 'green');
                            cabecalho.html(request.data);
                            setTimeout(function() { 
                                fecharJanela('{$g['divId']}');
                                divRefresh('{$g['divIdParent']}', true);
                            }, 500);                            
                        } else {
                            ErrorMessage(request.error);
                        }
                    }
                });

            });

            function agregaQuantidades(itens) {

                let agregado = {};
                let flagEstoque = true;

                itens.forEach(item => {

                    let partes = item.item.split('|');                    
                    let produto = partes[2].trim();
                    let descricao = partes.slice(0, 4).join('|').trim();
                    let estoque = parseFloat(partes[4].trim());

                    let quantidade = parseInt(partes[partes.length - 6].trim());      
                    let detalhesFinais = partes.slice(-5).join('|').trim();

                    let chave = descricao + ' | ' + estoque + ' | ' + detalhesFinais;

                    if (agregado[chave]) {
                        if (agregado[chave].quantidade + quantidade > estoque) {
                            ErrorMessage("Quantidade excedida " + produto);
                            flagEstoque = false;
                        } else {
                            agregado[chave].quantidade += quantidade;
                        }
                    } else {
                        if (quantidade > estoque) {
                            flagEstoque = false;
                            ErrorMessage("Quantidade excedida " + produto);
                        } else {
                            agregado[chave] = {
                                quantidade: quantidade,
                                detalhesFinais: detalhesFinais
                            };
                        }
                    }
                });

                return { items: Object.keys(agregado).map(chave => {
                    let item = agregado[chave];
                    return { item: chave.split('|').slice(0, 5).join('|') + ' | Quantidade | ' + item.quantidade + ' | ' + item.detalhesFinais };
                }), flagEstoque: flagEstoque };
            }

            function reproduzirSomErro(){ 
                var som = new Audio("data:audio/wav;base64,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qJrx4KOQjahZxWbcZgztj2c49nKmkId44S71j0c8eV9yDK6uPRzx5X18eDvjvQ6yKo9ZSS6l//8elePK/Lf//IInrOF/FvDoADYAGBMGb7FtErm5MXMlmPAJQVgWta7Zx2go+8xJ0UiCb8LHHdftWyLJE0QIAIsI+UbXu67dZMjmgDGCGl1H+vpF4NSDckSIkk7Vd+sxEhBQMRU8j/12UIRhzSaUdQ+rQU5kGeFxm+hb1oh6pWWmv3uvmReDl0UnvtapVaIzo1jZbf/pD6ElLqSX+rUmOQNpJFa/r+sa4e/pBlAABoAAAAA3CUgShLdGIxsY7AUABPRrgCABdDuQ5GC7DqPQCgbbJUAoRSUj+NIEig0YfyWUho1VBBBA//uQZB4ABZx5zfMakeAAAAmwAAAAF5F3P0w9GtAAACfAAAAAwLhMDmAYWMgVEG1U0FIGCBgXBXAtfMH10000EEEEEECUBYln03TTTdNBDZopopYvrTTdNa325mImNg3TTPV9q3pmY0xoO6bv3r00y+IDGid/9aaaZTGMuj9mpu9Mpio1dXrr5HERTZSmqU36A3CumzN/9Robv/Xx4v9ijkSRSNLQhAWumap82WRSBUqXStV/YcS+XVLnSS+WLDroqArFkMEsAS+eWmrUzrO0oEmE40RlMZ5+ODIkAyKAGUwZ3mVKmcamcJnMW26MRPgUw6j+LkhyHGVGYjSUUKNpuJUQoOIAyDvEyG8S5yfK6dhZc0Tx1KI/gviKL6qvvFs1+bWtaz58uUNnryq6kt5RzOCkPWlVqVX2a/EEBUdU1KrXLf40GoiiFXK///qpoiDXrOgqDR38JB0bw7SoL+ZB9o1RCkQjQ2CBYZKd/+VJxZRRZlqSkKiws0WFxUyCwsKiMy7hUVFhIaCrNQsKkTIsLivwKKigsj8XYlwt/WKi2N4d//uQRCSAAjURNIHpMZBGYiaQPSYyAAABLAAAAAAAACWAAAAApUF/Mg+0aohSIRobBAsMlO//Kk4soosy1JSFRYWaLC4qZBYWFRGZdwqKiwkNBVmoWFSJkWFxX4FFRQWR+LsS4W/rFRb/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////VEFHAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAU291bmRib3kuZGUAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAMjAwNGh0dHA6Ly93d3cuc291bmRib3kuZGUAAAAAAAAAACU=");  
                setTimeout(function(){ som.play(); },500);	
                som.play();
            }}

        </script></div>
EOT;

    break;

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ").";