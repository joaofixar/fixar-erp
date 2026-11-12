<?php
namespace hardness;

include('conf.php');
include('inc.conf.php');

//Parametro de Pesquisa e Coluna da Pesquisa selecionada em requisições

$arqFiltro = $g['pathDadosUsuario'] . 'tabletFiltro/clientesLista';
if (!file_exists($arqFiltro)) {
    $pesquisa = array('busca' => '', 'ordem' => "{$g['C031']['definirOrdemDeOrdenacao']}", 'colunaBusca' => '', 'tipoOrdenacao' => '', 'coluna' => '');
    file_put_contents($arqFiltro, serialize($pesquisa));
}

$pesquisa = unserialize(file_get_contents($arqFiltro));
if (isset($_REQUEST['pesquisa'])) {
    $pesquisa['busca'] = $_REQUEST['pesquisa'];
    file_put_contents($arqFiltro, serialize($pesquisa));
}
if (isset($_REQUEST['colunaPesquisa'])) {
    $pesquisa['colunaBusca'] = $_REQUEST['colunaPesquisa'];
    file_put_contents($arqFiltro, serialize($pesquisa));
}
if (isset($_REQUEST['coluna'])) {
    $pesquisa['tipoOrdenacao'] = $_REQUEST['tipoOrdenacao'];
    $pesquisa['coluna'] = $_REQUEST['coluna'];
    $pesquisa['ordem'] = $_REQUEST['coluna'] . isset($_REQUEST['tipoOrdenacao']) ? ' ' . $_REQUEST['coluna'] . ' ' . $_REQUEST['tipoOrdenacao'] : ' DESC';
    file_put_contents($arqFiltro, serialize($pesquisa));
}
//Gera o Combo para pesquisa
$comboPesquisa = '';
$comboPesquisa .= '<option value=""'.(($pesquisa['colunaBusca'] == '') ? ' selected="selected"' : '').'> Geral </option>';
$comboPesquisa .= '<option value="D024_Nome_Fantasia"'.(($pesquisa['colunaBusca'] == 'D024_Nome_Fantasia') ? ' selected="selected"' : '').'> Cliente </option>';
$comboPesquisa .= '<option value="D024_Id"'.(($pesquisa['colunaBusca'] == 'D024_Id') ? ' selected="selected"' : '').'> Código </option>';
//$comboPesquisa .= '<option value="D116_Modelo"'.(($pesquisa['colunaBusca'] == 'D116_Modelo') ? ' selected="selected"' : '').'> Veículo </option>';
//$comboPesquisa .= '<option value="D024_Cpf"'.(($pesquisa['colunaBusca'] == 'D024_Cpf') ? ' selected="selected"' : '').'> CPF/CNPJ </option>';

include('inc.header.php');
echo <<<EOT
    <div data-role="page" id="pageClientesProcurar">
EOT;
$etapa = 5;
include('inc.header.bar.php');
echo <<<EOT
            <div data-role="content">
                <div class="ui-grid-a" style="margin-top: -10px; margin-bottom: 0px;">
                    <div class="ui-block-a" style="width: 20%;">
                    </div>
                    <div class="ui-block-b" style="width: 60%;">
                            <div style="width: 48%; float:left; margin-top: 8px; margin-bottom: 5px;">
                                <input placeholder="Procurar Cliente" data-type="search" class="ui-input-text ui-body-c" value="{$pesquisa['busca']}" id="procurarCliente">
                            </div>
                            <div style="float:left;">
                                <select name="colunaPesquisa" id="colunaPesquisa" data-inline="true">
                                    {$comboPesquisa}
                               </select>
                            </div>
                            <div style="float:left;">
                                <a data-role="button" data-transition="slide" data-icon="search" data-iconpos="left" onclick="\$.mobile.changePage( './clientesLista.php', {transition: 'fade', type:'post', data: 'colunaPesquisa=' + encodeURIComponent($('#colunaPesquisa').val()) + '&pesquisa=' + encodeURIComponent($('#procurarCliente').val())} )" >
                                    Procurar
                                </a>
                            </div>
                    </div>
                </div>
                <ul data-role="listview" data-divider-theme="b" data-inset="true" style="margin-top: 1px; clear:both;">
EOT;
        //Verifica Ordenação da Coluna pelo clique do usuário DESC ou ASC
        $D024_Id = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'D024_Id' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
        $D024_Nome_Fantasia = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'D024_Nome_Fantasia' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
        $D020_Nome_Cidade = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'D020_Nome_Cidade' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';
        $D018_UF = (isset($_REQUEST['coluna']) && $_REQUEST['coluna'] == 'D018_UF' && $_REQUEST['tipoOrdenacao'] == 'DESC') ? 'ASC' : 'DESC';

        //Define Icone de Ordenação na coluna do Grid
        //Define a imagem
        if(isset($pesquisa['coluna']) && $pesquisa['tipoOrdenacao'] == 'DESC'){
            $iconeOrdenacao = "<img src='/hardness3/mobile/tablet/jquery.mobile-1.0.1/images/arrow-down-3.png'></img>";
        }else if(isset($pesquisa['coluna']) && $pesquisa['tipoOrdenacao'] == 'ASC'){
            $iconeOrdenacao = "<img src='/hardness3/mobile/tablet/jquery.mobile-1.0.1/images/arrow-up-3.png'></img>";
        }else{
            $iconeOrdenacao = '';
        }
        //Verifica qual coluna foi selecionada para mostrar o icone apenas na coluna selecionada
        $D024_Id_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'D024_Id') ? $iconeOrdenacao : '';
        $D024_Nome_Fantasia_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'D024_Nome_Fantasia') ? $iconeOrdenacao : '';
        $D020_Nome_Cidade_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'D020_Nome_Cidade') ? $iconeOrdenacao : '';
        $D018_UF_Icone = (isset($pesquisa['coluna']) && $pesquisa['coluna'] == 'D018_UF') ? $iconeOrdenacao : '';

echo <<<EOT
                    <li data-role="list-divider" role="heading">
                        <table style="width: 100%";>
                            <tr>
                                <td style="width: 5%"; align="center"><a href="clientesLista.php?coluna=D024_Id&tipoOrdenacao={$D024_Id}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Código{$D024_Id_Icone}</a></td>
                                <td style="width: 33%";><a href="clientesLista.php?coluna=D024_Nome_Fantasia&tipoOrdenacao={$D024_Nome_Fantasia}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Cliente{$D024_Nome_Fantasia_Icone}</a></td>
                                <td style="width: 10%";>Telefone</td>
                                <td style="width: 20%";><a href="clientesLista.php?coluna=D020_Nome_Cidade&tipoOrdenacao={$D020_Nome_Cidade}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">Cidade{$D020_Nome_Cidade_Icone}</a></td>
                                <td style="width: 5%"; align="center"><a href="clientesLista.php?coluna=D018_UF&tipoOrdenacao={$D018_UF}&pesquisa={$pesquisa['busca']}&colunaPesquisa={$pesquisa['colunaBusca']}" style="color:#FFF; text-decoration:none;" data-transition="fade">UF{$D018_UF_Icone}</a></td>
                                <td style="width: 12%"; align="center">CPF/CNPJ</td>
                                <td style="width: 15%"; align="center">Vendedor</td>
                            </tr>
                        </table>
                    </li>
EOT;

$filtro = '';
if($C007['C007_Flag_Vendedor'] == 'V'){
	//$filtro = "AND Vendedor.C007_Id='{$g['usuarioAtual']}'";} else if($C007['C007_Flag_Vendedor'] == 'S'){
	//$filtro = "AND (Supervisor.C007_Id='{$g['usuarioAtual']}' OR Vendedor.C007_Id='{$g['usuarioAtual']}')";}
}
// Produtos
$sql = <<<EOT
        SELECT D024_Id, D024_Nome_Fantasia, D024_Bairro, D020_Nome_Cidade, D018_UF, D024_CNPJ, D024_CPF, D024_RG, D024_DDD_Telefone_1, D024_Telefone_1, (select D013_DDD_Telefone_1 from D013 where D013_D024_Id=D024_Id and D013_Telefone_1 != '' and D013_Telefone_1 is not null ORDER BY D013_Id ASC LIMIT 1) as D013_DDD_Telefone_1,(select D013_Telefone_1 from D013 where D013_D024_Id=D024_Id and D013_Telefone_1 != '' and D013_Telefone_1 is not null ORDER BY D013_Id ASC LIMIT 1) as D013_Telefone_1, Vendedor.C007_Primeiro_Nome as C007_Primeiro_Nome FROM D024 
        left join D020 on D020_Id=D024_D020_Id
        left join D018 on D018_Id=D024_D018_Id
        left join D084 on D084_Id=D020_D084_Id
        left join D085 on D085_Id=D024_D085_Id
        left join D046 on D046_Id=D024_D046_Id
        left join D021 on D021_Id=D024_D021_Id
        left join D116 on D024_Id=D116_D024_Id
    	left join C007 as Vendedor on Vendedor.C007_Id=D024_C007_Vendedor_Interno
    	left join C007 as Externo on Externo.C007_Id=D024_C007_Vendedor_Externo
    	left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id
    	left join C007 as Supervisor2 on Supervisor2.C007_Id=Vendedor.C007_C007_Id_2
    	left join C007 as Substituto on Substituto.C007_Id=Vendedor.C007_Id_Vendedor_Substituto
	WHERE ((1=1)
EOT;
    //Verifica Se foi requisitada a pesquisa
    if(isset($pesquisa['busca']) && $pesquisa['busca']!='') {
        //Se a Coluna foi Selecionada faz pesquisa pela coluna, senão faz pesquisa geral
        if(isset($pesquisa['colunaBusca']) && $pesquisa['colunaBusca']!='') {
            //Se a Coluna Escolhida = Data
            if($pesquisa['colunaBusca']=='T005_Data_Emissao') {
                $sql .= "AND date_format({$pesquisa['colunaBusca']},'%d/%m/%Y') like '%{$pesquisa['busca']}%'";
            } else if($pesquisa['colunaBusca']=='D024_Nome_Fantasia'){
                $sql .= " AND ((D024_Id LIKE '%{$pesquisa['busca']}%') OR (D024_Nome_Empresa LIKE '%{$pesquisa['busca']}%') OR (D018_UF LIKE '%{$pesquisa['busca']}%') OR (D024_Bairro LIKE '%{$pesquisa['busca']}%') OR (D020_Nome_Cidade LIKE '%{$pesquisa['busca']}%') OR ( D024_Nome_Fantasia LIKE '%{$pesquisa['busca']}%') OR (D116_Modelo LIKE '%{$pesquisa['busca']}%') OR ( REPLACE(D116_Placa, '-', '') LIKE '%{$pesquisa['busca']}%') OR (REPLACE(REPLACE(D024_Cpf, '.', ''), '-', '') LIKE REPLACE(REPLACE('%{$pesquisa['busca']}%', '.', ''), '-', '') OR REPLACE(REPLACE(REPLACE(D024_Cnpj, '.', ''), '-', ''), '/', '') LIKE REPLACE(REPLACE(REPLACE('%{$pesquisa['busca']}%', '.', ''), '-', ''), '/', '')) OR (D116_Placa LIKE '%{$pesquisa['busca']}%' OR REPLACE(REPLACE(D116_Placa, '-', ''), ' ', '') LIKE REPLACE(REPLACE('%{$pesquisa['busca']}%', '-', ''), ' ', '')))"; 
            } else if($pesquisa['colunaBusca']=='D116_Modelo') {
                $sql .= " AND ({$pesquisa['colunaBusca']} LIKE '%{$pesquisa['busca']}%' OR (D116_Placa LIKE '%{$pesquisa['busca']}%' OR REPLACE(REPLACE(D116_Placa, '-', ''), ' ', '') LIKE REPLACE(REPLACE('%{$pesquisa['busca']}%', '-', ''), ' ', '')))";
            } else if($pesquisa['colunaBusca']=='D024_Cpf') {
                $sql .= " AND (REPLACE(REPLACE({$pesquisa['colunaBusca']}, '.', ''), '-', '') LIKE REPLACE(REPLACE('%{$pesquisa['busca']}%', '.', ''), '-', '') OR REPLACE(REPLACE(REPLACE(D024_Cnpj, '.', ''), '-', ''), '/', '') LIKE REPLACE(REPLACE(REPLACE('%{$pesquisa['busca']}%', '.', ''), '-', ''), '/', ''))";
            } else{
                $sql .= " AND {$pesquisa['colunaBusca']} LIKE '%{$pesquisa['busca']}%'";
            }
        }else{
            $sql .= " AND ((D024_Nome_Empresa LIKE '%{$pesquisa['busca']}%') OR ( D024_Nome_Fantasia LIKE '%{$pesquisa['busca']}%') OR (D116_Modelo LIKE '%{$pesquisa['busca']}%') OR ( REPLACE(D116_Placa, '-', '') LIKE '%{$pesquisa['busca']}%') OR (REPLACE(REPLACE(D024_Cpf, '.', ''), '-', '') LIKE REPLACE(REPLACE('%{$pesquisa['busca']}%', '.', ''), '-', '') OR REPLACE(REPLACE(REPLACE(D024_Cnpj, '.', ''), '-', ''), '/', '') LIKE REPLACE(REPLACE(REPLACE('%{$pesquisa['busca']}%', '.', ''), '-', ''), '/', '')) OR (D116_Placa LIKE '%{$pesquisa['busca']}%' OR REPLACE(REPLACE(D116_Placa, '-', ''), ' ', '') LIKE REPLACE(REPLACE('%{$pesquisa['busca']}%', '-', ''), ' ', '')))";     
        }
    }
$sql .= <<<EOT
    AND D024_Flag_Ativo='S' AND (D024_C004_Id='{$g['empresaAtual']}' OR IFNULL(D024_C004_Id,0)=0))
    {$filtro}
	{$g['conf']->ini('extraSqlPadraoVisualizacaoClientes')}
	ORDER BY {$g['C031']['definirOrdemDeOrdenacao']}
    LIMIT 0,100
EOT;

$res = mysql_query($sql);
while ($row = mysql_fetch_assoc($res)) {
    if(empty($row['D024_CNPJ'])){
        $documentoCliente = $row['D024_CPF'];
    }else{
        $documentoCliente = $row['D024_CNPJ'];
    }
    //$row['D024_Nome_Fantasia'] = substr($row['D024_Nome_Fantasia'], 0, 30);
    $row['D020_Nome_Cidade'] = substr($row['D020_Nome_Cidade'], 0, 20);
    $D013_DDD_Telefone_1 = $row['D013_DDD_Telefone_1'];
    $D013_Telefone_1 = $row['D013_Telefone_1'];
    if(!empty($D013_DDD_Telefone_1)){
        $telefoneCliente = "({$D013_DDD_Telefone_1}) {$D013_Telefone_1}";
    }else{
        $telefoneCliente = "{$D013_Telefone_1}";
    }
    if(empty($telefoneCliente)){
        $telefoneCliente = $row['D024_DDD_Telefone_1'] . ' ' . $row['D024_Telefone_1'];
    }
    echo <<<EOT
        <li data-theme="c" data-icon="false">
            <a href="./clientesEditar.php?D024_Id={$row['D024_Id']}&update=true" data-ajax="false">
                <table style="width: 100%";>
                    <tr>
                        <td style="width: 5%;" align="center">{$row['D024_Id']}</td>
                        <td style="width: 33%;">{$row['D024_Nome_Fantasia']}</td>
                        <td style="width: 10%;">{$telefoneCliente}</td>
                        <td style="width: 20%;">{$row['D020_Nome_Cidade']}</td>
                        <td style="width: 5%;" align="center">{$row['D018_UF']}</td>
                        <td style="width: 12%;" align="center">{$documentoCliente}</td>
                        <td style="width: 15%;" align="center">{$row['C007_Primeiro_Nome']}</td>
                    </tr>
                </table>
            </a>
        </li>
EOT;
}

echo <<<EOT
                </ul>
            </div>
EOT;
include('inc.footer.php');
echo <<<EOT
    </div>
    <script>
        $( document ).undelegate("#pageClientesProcurar", "pageinit");
        $( document ).delegate("#pageClientesProcurar", "pageinit", function() {
EOT;
include('inc.swipe.events.php');
echo <<<EOT
        });
    </script>
EOT;
