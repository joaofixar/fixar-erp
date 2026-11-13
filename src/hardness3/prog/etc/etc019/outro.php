<?php
namespace hardness;

if (empty($sys['wikiDB'])) {
    die('wikiDB não configurando em sysConfig.php');
}
$con = @mysqli_connect($sys['wikiDB']['host'], $sys['wikiDB']['user'], $sys['wikiDB']['pass'], $sys['wikiDB']['db']);
$g['conexaoBanco'] = $con;

/**
 * Ações
 */
switch ($g['r'][3]) {
    case 'etc019OutroAjuda':
        $todos = array();
        if ($r_acaoId) {
            // Navegação pela interface do wiki
            $pai = $r_acaoId;
        } else {
            // Chamada via botão 'Ajuda' dentro do sistema
            // Cria os itens em W001, caso não exista
            $itens = array(
                'janela' => array('tipo' => $r_janelaTipo, 'href' => $r_janelaHref, 'titulo' => $r_janelaTitulo),
                'item' => array('tipo' => $r_itemTipo, 'href' => $r_itemHref, 'titulo' => $r_itemTitulo),
                'subitem' => array('tipo' => $r_subitemTipo, 'href' => $r_subitemHref, 'titulo' => $r_subitemTitulo),
            );
            $pai = null;
            foreach ($itens as $itemKey => $item) {
                if (empty($item['href'])) {
                    continue;
                }
                $md5 = md5($item['href']);
                $titulo = explode('||', $item['titulo']);
                $titulo = $titulo[1] ? $titulo[1] : $titulo[0];
                $titulo = trim(str_replace(array('.:', ':.'), '', $titulo));
                $query = "SELECT W001_Id FROM W001 WHERE W001_Md5 = '{$md5}'";
                $W001 = mysql_fetch_assoc(mysql_query($query));
                if ($W001) {
                    $pai = $W001['W001_Id'];
                } else {
                    $paiId = $pai ? $pai : 'NULL';
                    $query = "
                    INSERT INTO W001
                        (W001_W001_Id, W001_Tipo, W001_Url, W001_Md5, W001_Nome)
                    VALUES
                        ({$paiId}, '{$item['tipo']}', '" . mysql_real_escape_string($item['href']) . "', '{$md5}', '" . mysql_real_escape_string($titulo) . "') 
                ";
                    mysql_query($query);
                    $pai = $g['mysqlLastId'];
                }
            }
        }

        // Navega pela hierarquia encontrado todos os itens
        $W001_Id = $pai;
        while ($W001_Id && $W001_Id != 'NULL') {
            $query = "SELECT * FROM W001 WHERE W001_Id = '{$W001_Id}'";
            $W001 = mysql_fetch_assoc(mysql_query($query));
            $todos[] = $W001;
            $W001_Id = $W001['W001_W001_Id'];
        }
        $todos = array_reverse($todos);

        // Breadcrumb
        $breadcrumb = '';
        $W001 = null;
        foreach ($todos as $key => $item) {
            $tipo = ucfirst(strtolower($item['W001_Tipo']));
            if ($key == (count($todos)-1)) {
                $breadcrumb .= "<li class='breadcrumb-item active'>{$tipo} ({$item['W001_Nome']})</li>\n";
            } else {
                $breadcrumb .= "<li class='breadcrumb-item active'><a href='#' onclick=\"divSetarAcaoId('{$g['divId']}', '{$item['W001_Id']}'); divRefresh('{$g['divId']}');\">{$tipo} ({$item['W001_Nome']})</a></li>\n";
            }
            $W001 = $item;
        }

        $modoEdicao = $r_modoEdicao ? '' : 'true';
        $modoEdicaoOnOff = $r_modoEdicao ? 'ON' : 'OFF';
        $modoEdicaoBtn = $r_modoEdicao ? 'btn-success' : 'btn-secondary';
        echo <<<EOT
<div class="bootstrap" style="background-color: white; min-width: 100%; min-height: 100%; padding: 8px 16px;">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#" onclick="divSetarAcaoId('{$g['divId']}', 'NULL'); divRefresh('{$g['divId']}');">Sistema</a></li>
            {$breadcrumb}
            <button type="button" class="btn {$modoEdicaoBtn} btn-sm" style="padding: 0; margin: 0; float: right; font-size: 13px; height: 25px; line-height: 25px;" onclick="divSetarAcaoId('{$g['divId']}', '{$modoEdicao}', 'modoEdicao'); divRefresh('{$g['divId']}');">Modo Edição: {$modoEdicaoOnOff}</button>
        </ol>
    </nav>
EOT;

        // Dados e Registros (não é mostrado para o 'Sistema')
        if (!empty($W001)) {
            if ($r_modoEdicao) {
                $identificador = preg_match('/\/.*\/.*\/.*\//', $W001['W001_Url']) ? $W001['W001_Url'] : base64_decode($W001['W001_Url']);
                echo <<<EOT
    <h4 style="margin-top: 28px;">Dados</h4>
    <div class="card">
        <div class="card-body" style="padding: 12px 12px 4px 12px">
            <form>
              <div class="row">
                <label class="col-sm-1 col-form-label">Nome</label>
                <div class="col-sm-11">
                  <input type="text" class="form-control form-control-sm" id="wikiCabecalhoNome" value="{$W001['W001_Nome']}" style="width: 550px; text-transform: none;">
                </div>
              </div>
              <div class="row">
                <label class="col-sm-1 col-form-label">Identificador</label>
                <div class="col-sm-11">
                  <input type="text" readonly class="form-control form-control-sm" id="wikiCabecalhoUrl" value="{$identificador}" style="width: 400px; text-transform: none;">
                </div>
              </div>
              <div class="row">
                <label class="col-sm-1 col-form-label">Descrição</label>
                <div class="col-sm-11">
                  <textarea id="wikiCabecalhoDescricao" class="form-control" style="width: 700px; height: 150px; text-transform: none;">{$W001['W001_Descricao']}</textarea>
                </div>
              </div>
              <div style="text-align: center;">
                <a class="btn btn-success btn-sm" href="#" role="button" style="margin-bottom: 4px;" onclick="wikiEditarCabecalho();">Salvar</a>
              </div>
            </form>
        </div>
    </div>
EOT;
            } else {
                if (!empty($W001['W001_Descricao'])) {
                    echo "<p>{$W001['W001_Descricao']}</p>";
                }
            }

            if ($r_modoEdicao) {
                echo <<<EOT
    <h4 style="margin-top: 28px;">Tópicos</h4>
    <a class="btn btn-primary btn-sm" href="#" role="button" style="margin-bottom: 4px;" onclick="wikiAbrirRegistro('{$g['divId']}', 'TOPICO', '{$r_modoEdicao}', '{$W001['W001_Id']}', '0')">Incluir</a>
EOT;
            }
            echo <<<EOT
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>Tópico</th>
                <th>Última Alteração</th>
            </tr>
        </thead>
EOT;

            $query = "SELECT * FROM W002 WHERE W002_Tipo = 'TOPICO' AND W002_W001_Id = '{$W001['W001_Id']}' AND W002_Ativo = 'S'";
            $res = mysql_query($query);
            if (mysql_num_rows($res) == 0) {
                echo <<<EOT
        <tr>
            <td colspan="2" style="opacity: 0.8;">Nenhum tópico registrado ainda para este item.</td>        
        </tr>
EOT;
            } else {
                while ($registro = mysql_fetch_assoc($res)) {
                    $data = date('d/m/Y', strtotime($registro['W002_Criado_Em']));
                    echo <<<EOT
            <tr style="cursor: pointer;" onclick="wikiAbrirRegistro('{$g['divId']}', 'TOPICO', '{$r_modoEdicao}', '{$registro['W002_W001_Id']}', '{$registro['W002_Id']}'); divRefresh('{$g['divId']}');">
                <td style="font-size: 1.1em;">{$registro['W002_Titulo']}</td>
                <td>{$data}</td>
            </tr>
EOT;
                }
            }

            echo <<<EOT
    </table>
EOT;
        }

        // Comentários
        echo <<<EOT
    <h5 style="margin-top: 30px;">Comentários</h5>
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Usuário</th>
                <th>Última Alteração</th>
            </tr>
        </thead>
EOT;

        $query = "SELECT *, IF(W002_Usuario_Id = 63, 1, 2) as OrdenacaoUsuario FROM W002 WHERE W002_W001_Id = '{$W001['W001_Id']}' AND W002_W002_Id IS NULL AND W002_Tipo = 'COMENTARIO' AND W002_Empresa_Db = '{$confUsuario['dbDatabase']}' AND W002_Ativo = 'S' ORDER BY OrdenacaoUsuario ASC, W002_Id ASC";
        $res = mysql_query($query);
        if (mysql_num_rows($res) == 0) {
            echo <<<EOT
        <tr>
            <td colspan="3" style="opacity: 0.8;">Nenhum comentário registrado ainda para este item.</td>        
        </tr>
EOT;
        } else {
            while ($registro = mysql_fetch_assoc($res)) {
                $data = date('d/m/Y', strtotime($registro['W002_Criado_Em']));
                echo <<<EOT
            <tr style="cursor: pointer;" onclick="wikiAbrirRegistro('{$g['divId']}', 'COMENTARIO', '{$r_modoEdicao}', '{$registro['W002_W001_Id']}', '', '{$registro['W002_Id']}'); divRefresh('{$g['divId']}');">
                <td style="font-size: 1.0em;">{$registro['W002_Titulo']}</td>
                <td>{$registro['W002_Usuario_Login']}</td>
                <td>{$data}</td>
            </tr>
EOT;
            }
        }

        echo <<<EOT
    </table>
EOT;

        echo <<<EOT
    <a class="btn btn-primary btn-sm" href="#" role="button" style="margin-bottom: 8px;" onclick="wikiAbrirRegistro('{$g['divId']}', 'COMENTARIO', 'true', '{$W001['W001_Id']}', '')">Incluir Comentário</a>
EOT;

        // Itens Relacionados
        $query = empty($W001) ? "SELECT * FROM W001 LEFT JOIN W002 ON W002_W001_Id=W001_Id AND W002_Ativo='S' WHERE W001_W001_Id IS NULL" : "SELECT * FROM W001 LEFT JOIN W002 ON W002_W001_Id=W001_Id AND W002_Ativo='S' WHERE W001_W001_Id = '{$W001['W001_Id']}'";
        $res = mysql_query($query);
        if (mysql_num_rows($res) != 0) {
            echo <<<EOT
    <h5 style="margin-top: 30px;">Itens Relacionados</h5>
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th style="width: 5%;">Tipo</th>
                <th>Nome</th>
            </tr>
        </thead>
EOT;

            while ($filho = mysql_fetch_assoc($res)) {
                $tipo = ucfirst(strtolower($filho['W001_Tipo']));
                if ($filho['W001_Tem_Topico']=='N') {
                    $filho['W001_Tem_Topico'] = buscaTopicosRecursive($filho['W001_Id']);
                }
                if ($filho['W001_Tem_Topico']=='S') {
                    $style = 'color:#000000;';
                } else {
                    $style = 'color:#6d6c6c;';
                }
                echo <<<EOT
            <tr style="cursor: pointer;$style" onclick="divSetarAcaoId('{$g['divId']}', '{$filho['W001_Id']}'); divRefresh('{$g['divId']}');">
                <td>{$tipo}</td>
                <td>{$filho['W001_Nome']}</td>
            </tr>
EOT;
            }

            echo <<<EOT
    </table>
</div>
EOT;
        }

        echo <<<EOT
<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {	
    function wikiAbrirRegistro(divId, tipo, modoEdicao, W001_Id, W002_Id, W002_Comentario_Id) {
        W002_Comentario_Id = typeof(W002_Comentario_Id) == 'undefined' ? '' : W002_Comentario_Id;
        \$elem = $(`#\${divId}`);
        if (tipo == 'TOPICO') {
            abrirJanela(false, \$elem.data('janelaIdRoot'), divId, unique(), '', 'Ajuda - Tópico', '/etc/etc019/outro/etc019OutroRegistro/', '&tipo=' + tipo + '&modoEdicao=' + modoEdicao + '&W001_Id=' + encodeURIComponent(W001_Id) + '&W002_Id=' + encodeURIComponent(W002_Id), [900,700], false, 'center');
        } else {
            abrirJanela(false, \$elem.data('janelaIdRoot'), divId, unique(), '', 'Ajuda - Comentário', '/etc/etc019/outro/etc019OutroRegistro/', '&tipo=' + tipo + '&modoEdicao=' + modoEdicao + '&W001_Id=' + encodeURIComponent(W001_Id) + '&W002_Id=' + encodeURIComponent(W002_Id) + '&W002_Comentario_Id=' + encodeURIComponent(W002_Comentario_Id), [800,600], false, 'center');
        }
    }
    function wikiEditarCabecalho() {
        $('#{$g['divId']}').showLoading();
        $.ajax({
            url: '/etc/etc019/outro/etc019OutroCabecalhoSalvar/',
            dataType: 'json',
            data: 'ajax=true&W001_Id=' + encodeURIComponent('{$W001['W001_Id']}') + '&nome=' + encodeURIComponent($('#wikiCabecalhoNome').val()) + '&descricao=' + encodeURIComponent($('#wikiCabecalhoDescricao').val()),
            type: 'post',
            success: function(data) {
                $('#{$g['divId']}').hideLoading();
                if (data.erro) {
                    alert(data.erro);
                } else {
                    divRefresh('{$g['divId']}');
                }
            }
        });
    }
} </script></div>
EOT;

    break;

    case 'etc019OutroRegistro':
        $W002_Id = $r_tipo == 'TOPICO' ? $r_W002_Id : $r_W002_Comentario_Id;
        $height = $r_tipo == 'TOPICO' ? '550px' : '450px';
        $nomeBtn = $r_tipo == 'TOPICO' ? 'Editar' : 'Salvar';
        $btnExcluir = '';
        if ($W002_Id) {
            $query = "SELECT * FROM W002 WHERE W002_Id = '{$W002_Id}'";
            $W002 = mysql_fetch_assoc(mysql_query($query));
            if ($r_tipo == 'COMENTARIO') {
                $btnExcluir = '<button type="button" class="btn btn-danger btn-sm" onclick="wikiExcluirRegistro(\'' . $W002['W002_Id'] . '\');">Excluir</button>';
            }
        }
        // O comentário sempre abre em modo de edição para o usuário que criou ele
        if ($r_tipo == 'COMENTARIO' && $W002['W002_Empresa_Db'] == $confUsuario['dbDatabase'] && $W002['W002_Usuario_Id'] == $g['C007']['C007_Id']) {
            $r_modoEdicao = true;
        }
        if ($r_modoEdicao) {
            echo <<<EOT
<div class="bootstrap" style="background-color: white; min-width: 100%; min-height: 100%; padding: 8px 16px;">
    <form>
      <div style="display: flex; flex-direction: column; height: 100%;">
          <div class="" style="margin: 0;">
            <label for="wikiRegistroNome">Nome</label>
            <input type="text" class="form-control form-control-sm" id="wikiRegistroNome" aria-describedby="wikiRegistroNome" style="width: 600px; text-transform: none;" value="{$W002['W002_Titulo']}">
          </div>
          <textarea id="wikiRegistroTextarea" type="text" style="width: 100%; height: {$height}; text-transform: none;">{$W002['W002_Corpo']}</textarea>
          <div style="text-align: center; margin-top: 12px;">
            <button type="button" class="btn btn-success btn-sm" onclick="wikiEditarRegistro();">{$nomeBtn}</button>
            {$btnExcluir}
          </div>
      </div>
    </form>
</div>

<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {	
    tinymce.remove();
    tinymce.init({
        selector: `#wikiRegistroTextarea`,
        plugins: 'print preview paste importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor insertdatetime advlist lists wordcount textpattern noneditable help charmap quickbars emoticons',
        menubar: 'edit insert format table',
        toolbar: 'undo redo | bold italic underline strikethrough | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media template link anchor codesample | ltr rtl',
        quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quicktable',
        noneditable_noneditable_class: 'mceNonEditable',
        fontsize_formats: "8px 9px 10px 11px 12px 14px 18px 24px 30px 36px 48px 60px 72px 96px",
        setup:function(ed) {
           ed.on('change', function(e) {
               $('#wikiRegistroTextarea').val(ed.getContent());
           });
        },
        statusbar: false,
    });
    
    function wikiEditarRegistro() {
        $('#{$g['divId']}').showLoading();
        $.ajax({
            url: '/etc/etc019/outro/etc019OutroTopicoSalvar/',
            dataType: 'json',
            data: 'ajax=true&tipo={$r_tipo}&W001_Id=' + encodeURIComponent('{$r_W001_Id}') + '&W002_Id=' + encodeURIComponent('{$r_W002_Id}') + '&W002_Comentario_Id=' + encodeURIComponent('{$r_W002_Comentario_Id}') + '&nome=' + encodeURIComponent($('#wikiRegistroNome').val()) + '&corpo=' + encodeURIComponent($('#wikiRegistroTextarea').val()),
            type: 'post',
            success: function(data) {
                $('#{$g['divId']}').hideLoading();
                if (data.erro) {
                    alert(data.erro);
                } else {
                    fecharJanela('{$g['divId']}');
                    divRefresh('{$g['divIdParent']}');
                }
            }
        });
    }
    
    function wikiExcluirRegistro(W002_Id) {
        $('#{$g['divId']}').showLoading();
        $.ajax({
            url: '/etc/etc019/outro/etc019OutroTopicoExcluir/',
            dataType: 'json',
            data: 'ajax=true&W002_Id=' + encodeURIComponent(W002_Id),
            type: 'post',
            success: function(data) {
                $('#{$g['divId']}').hideLoading();
                if (data.erro) {
                    alert(data.erro);
                } else {
                    fecharJanela('{$g['divId']}');
                    divRefresh('{$g['divIdParent']}');
                }
            }
        });
    }
} </script></div>
EOT;
        } else {
            echo <<<EOT
<div class="bootstrap" style="background-color: white; min-width: 100%; min-height: 100%; padding: 8px 16px;">
    <h4>{$W002['W002_Titulo']}</h4>
    <p>
        {$W002['W002_Corpo']}
    </p>
EOT;
            if ($r_tipo == 'TOPICO') {
                echo <<<EOT
    <h4 style="margin-top: 24px;">Comentários</h4>
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Usuário</th>
                <th>Última Alteração</th>
            </tr>
        </thead>
EOT;

                $query = "SELECT *, IF(W002_Usuario_Id = 63, 1, 2) as OrdenacaoUsuario FROM W002 WHERE W002_W002_Id = '{$W002['W002_Id']}' AND W002_Tipo = 'COMENTARIO' AND W002_Empresa_Db = '{$confUsuario['dbDatabase']}' AND W002_Ativo = 'S' ORDER BY OrdenacaoUsuario ASC, W002_Id ASC";
                $res = mysql_query($query);
                if (mysql_num_rows($res) == 0) {
                    echo <<<EOT
        <tr>
            <td colspan="3" style="opacity: 0.8;">Nenhum comentário registrado ainda para este item.</td>        
        </tr>
EOT;
                } else {
                    while ($registro = mysql_fetch_assoc($res)) {
                        $data = date('d/m/Y', strtotime($registro['W002_Criado_Em']));
                        echo <<<EOT
            <tr style="cursor: pointer;" onclick="wikiAbrirRegistro('{$g['divId']}', 'COMENTARIO', '{$r_modoEdicao}', '{$registro['W002_W001_Id']}', '', '{$registro['W002_Id']}'); divRefresh('{$g['divId']}');">
                <td style="font-size: 1.0em;">{$registro['W002_Titulo']}</td>
                <td>{$registro['W002_Usuario_Login']}</td>
                <td>{$data}</td>
            </tr>
EOT;
                    }
                }

                echo <<<EOT
    </table>
EOT;

                echo <<<EOT
    <a class="btn btn-primary btn-sm" href="#" role="button" style="margin-bottom: 8px;" onclick="wikiAbrirRegistro('{$g['divId']}', 'COMENTARIO', 'true', '{$W002['W002_W001_Id']}', '{$W002['W002_Id']}')">Incluir Comentário</a>
EOT;
            }
            echo <<<EOT
</div>
EOT;
        }
    break;

    case 'etc019OutroCabecalhoSalvar':
        $resposta = array();

        $nome = trim($r_nome);
        $descricao = trim($r_descricao);
        if (empty($nome)) {
            $resposta['erro'] = 'Nome é obrigatório.';
        } else if (empty($r_W001_Id)) {
            $resposta['erro'] = 'Id é obrigatório.';
        } else {
            $query = "
                UPDATE W001 SET W001_Nome = '" . mysql_real_escape_string($nome) . "', W001_Descricao = '" . mysql_real_escape_string($descricao) . "' 
                WHERE W001_Id = '" . mysql_real_escape_string($r_W001_Id) . "'
            ";
            mysql_query($query);
        }
        echo json_encode($resposta);
    break;

    case 'etc019OutroTopicoSalvar':
        $resposta = array();

        $nome = trim($r_nome);
        $corpo = trim($r_corpo);
        if (empty($nome) || empty($corpo)) {
            $resposta['erro'] = 'Nome e texto são obrigatórios.';
        } else {
            if ($r_tipo == 'COMENTARIO' && !empty($r_W002_Comentario_Id)) {
                $query = "
                UPDATE W002 SET
                    W002_Titulo = '" . mysql_real_escape_string($nome) . "',
                    W002_Corpo = '" . mysql_real_escape_string($corpo) . "'
                WHERE
                    W002_Id = '" . mysql_real_escape_string($r_W002_Comentario_Id) . "'
                ";
                mysql_query($query);
            } else {
                // Registro está 'nascendo' aprovado (W002_Ativo = 'S') - mudar depois que tiver esquema de aprovação
                $W002_Id = $r_W002_Id ? "'{$r_W002_Id}'" : 'NULL';
                $query = "
                INSERT INTO W002
                    (W002_W001_Id, W002_W002_Id, W002_Titulo, W002_Corpo, W002_Empresa_Db, W002_Empresa_Url, W002_Empresa_Nome, W002_Usuario_Id, W002_Usuario_Login, W002_Ativo, W002_Tipo)
                VALUES
                    (
                        {$r_W001_Id},
                        {$W002_Id},
                        '" . mysql_real_escape_string($nome) . "',
                        '" . mysql_real_escape_string($corpo) . "',
                        '" . mysql_real_escape_string($confUsuario['dbDatabase']) . "',
                        '" . mysql_real_escape_string($confUsuario['urlRaiz']) . "',
                        '" . mysql_real_escape_string($g['C004']['C004_Nome_Empresa']) . "',
                        '" . mysql_real_escape_string($g['C007']['C007_Id']) . "',
                        '" . mysql_real_escape_string($g['C007']['C007_Login']) . "',
                        'S',
                        '" . mysql_real_escape_string($r_tipo) . "'
                    )
                ";
                mysql_query($query);
                // Atualizar comentários para o novo W002_Id
                if ($r_tipo == 'TOPICO') {
                    $query = "UPDATE W002 SET W002_W002_Id = '{$g['mysqlLastId']}' WHERE W002_W002_Id = '{$r_W002_Id}' AND W002_Tipo = 'COMENTARIO'";
                    mysql_query($query);
                }
            }

            // Desabilitar todas as versões antigas desse registro
            if ($r_tipo == 'TOPICO') {
                $W002_Id = $r_W002_Id;
                while ($W002_Id) {
                    $query = "UPDATE W002 SET W002_Ativo = 'N' WHERE W002_Id = '{$W002_Id}' AND W002_Tipo = 'TOPICO'";
                    mysql_query($query);
                    $query = "SELECT W002_W002_Id FROM W002 WHERE W002_Id = '{$W002_Id}' AND W002_Tipo = 'TOPICO'";
                    $W002 = mysql_fetch_assoc(mysql_query($query));
                    $W002_Id = $W002['W002_W002_Id'];
                }
            }
        }
        echo json_encode($resposta);
    break;

    case 'etc019OutroTopicoExcluir':
        $resposta = array();

        $query = "DELETE FROM W002 WHERE W002_Id = '" . mysql_real_escape_string($r_W002_Id) . "'";
        mysql_query($query);

        echo json_encode($resposta);
    break;
}


function buscaTopicosRecursive($W001_Id) {
    $temTopico = 'N';
    while (true) {
        $W002 = mysql_query("SELECT W002_Id FROM W002 WHERE W002_W001_Id='{$W001_Id}'");
        if (mysql_num_rows($W002)>0){
            mysql_query("UPDATE W001 SET W001_Tem_Topico='S' WHERE W001_Id='{$W001_Id}'");
            $temTopico = 'S';
            break;
        }
        $W001 = mysql_query("SELECT W001_Id, W001_Tem_Topico FROM W001 WHERE W001_W001_Id='{$W001_Id}'");
        $mW001 = mysql_fetch_assoc($W001);
        if ($mW001['W001_Tem_Topico']=='S') {
            $temTopico = 'S';
            break;
        } else if ($mW001['W001_Id']) {
            $W001_Id = $mW001['W001_Id'];
        } else {
            break;
        }
    }
    return $temTopico;
}