<?php
namespace hardness;

/* INTEGRAÇÃO E-COMMERCE TRAY
 *
 * URL's da API utilizadas
 * https://api.commerce.tray.com.br/categories - POST - Adicionar categorias
 * https://api.commerce.tray.com.br/categories/{id} - PUT - Atualizar categorias
 */

definirClasse('ENVIOCATEGORIASTRAY');
class EnvioCategoriasTray_ {

    public function __construct() {
    }

    public function executar() {

        require_once('bibliotecas/webservices/tray/conexaoTray.php');
        $conexaoTray = new ConexaoTray();

        if ($conexaoTray->isTudoOk()) {
            $this->enviarCategorias($conexaoTray);
            $this->enviarSubCategorias($conexaoTray);
        }
    }

    private function enviarCategorias($conexaoTray) {
        
        $categoriasTray = $this->getCategorias();

        foreach($categoriasTray as $categoriaTray) {

            $envioTray = array('Category' => $categoriaTray['paraTray']);

            $D003_Id_Integracao_Tray = $categoriaTray['IDs']["D003_Id_Integracao_Tray"];

            if (isset($D003_Id_Integracao_Tray) && !empty($D003_Id_Integracao_Tray) && $D003_Id_Integracao_Tray > 0) {
                $retornoTray = $conexaoTray->execPut("categories/{$D003_Id_Integracao_Tray}", $envioTray, true);

                if ($retornoTray['code'] == "200") {
                    $categoriaTray['deTray'] = $retornoTray;
                    $this->setCategoriaIntegrada($categoriaTray, false);
                }
            } else {
                $retornoTray = $conexaoTray->execPost("categories", $envioTray, true);

                if ($retornoTray['code'] == "201") {
                    $categoriaTray['deTray'] = $retornoTray;
                    $this->setCategoriaIntegrada($categoriaTray, true);
                }
            }

        }
    }

    private function getCategorias() {

        $categoriasTray = array();

        $sql = " SELECT D003_Id, D003_Nome_Linha, D003_Id_Integracao_Tray 
                 FROM D003 
                 WHERE D003_Integrado_Tray = 'N' ";
                 // AND D003_Flag_Visualizar_Site = 'S' -- ???

        $query = mysql_query($sql);

        while ($categoriaERP = mysql_fetch_array($query)) {

            $categoriaTray = array('IDs' => array(), 'paraTray' => array(), 'deTray' => array());

            $categoriaTray['IDs']["D003_Id"]                     = $categoriaERP['D003_Id'];
            $categoriaTray['IDs']["D003_Id_Integracao_Tray"]     = $categoriaERP['D003_Id_Integracao_Tray'];

            $categoriaTray['paraTray']['name']                   = $categoriaERP['D003_Nome_Linha']; // String (255) - Nome da categoria
            $categoriaTray['paraTray']['title']                  = $categoriaERP['D003_Nome_Linha']; // String (255) - Title da pagina de categorias
            $categoriaTray['paraTray']['small_description']      = $categoriaERP['D003_Nome_Linha']; // String - Descrição detalhada da Categoria
            $categoriaTray['paraTray']['has_acceptance_term']    = 0; // Number - Categoria com termo de aceitação ( 0 / 1)

            $categoriaTray['paraTray']['Metatag']                = array(); // Object - Meta Tag
            $categoriaTray['paraTray']['Metatag']['keywords']    = $categoriaERP['D003_Nome_Linha']; // String (256) - Palavras-Chaves de Meta Tag
            $categoriaTray['paraTray']['Metatag']['description'] = $categoriaERP['D003_Nome_Linha']; // String (256) - Descrição de Meta Tag

            // $categoriaTray['paraTray']['slug']                   = $categoriaERP['']; // String (255) - Final da URL da categoria
            // $categoriaTray['paraTray']['order']                  = $categoriaERP['']; // Number - Posição na Ordenação das Categorias
            // $categoriaTray['paraTray']['acceptance_term']        = $categoriaERP['']; // String - Texto com os termos a ser aceito na categoria
            // $categoriaTray['paraTray']['property']               = $categoriaERP['']; // String - Características

            $categoriasTray[] = $categoriaTray;
        }

        return $categoriasTray;
    }

    private function setCategoriaIntegrada($categoriaTray, $updIdTray) {

        $D003_Id = $categoriaTray['IDs']['D003_Id'];

        $sqlD003  = " UPDATE D003 SET D003_Integrado_Tray = 'S' ";

        if ($updIdTray) {
            $sqlD003 .= "  ,D003_Id_Integracao_Tray = {$categoriaTray['deTray']['id']} ";
        }

        $sqlD003 .= " WHERE D003_Id = {$D003_Id} ";

        mysql_query($sqlD003);
    }

    private function enviarSubCategorias($conexaoTray) {

        $subCategoriasTray = $this->getSubCategorias();

        foreach($subCategoriasTray as $subCategoriaTray) {

            $envioTray = array('Category' => $subCategoriaTray['paraTray']);

            $D002_Id_Integracao_Tray = $subCategoriaTray['IDs']["D002_Id_Integracao_Tray"];

            if (isset($D002_Id_Integracao_Tray) && !empty($D002_Id_Integracao_Tray) && $D002_Id_Integracao_Tray > 0) {
                $retornoTray = $conexaoTray->execPut("categories/{$D002_Id_Integracao_Tray}", $envioTray, true);

                if ($retornoTray['code'] == "200") {
                    $subCategoriaTray['deTray'] = $retornoTray;
                    $this->setSubCategoriaIntegrada($subCategoriaTray, false);
                }
            } else {
                $retornoTray = $conexaoTray->execPost("categories", $envioTray, true);

                if ($retornoTray['code'] == "201") {
                    $subCategoriaTray['deTray'] = $retornoTray;
                    $this->setSubCategoriaIntegrada($subCategoriaTray, true);
                }
            }

        }
    }

    private function getSubCategorias() {

        $subCategoriasTray = array();

        $sql = " SELECT D002_Id, D002_Descricao_Produto, D003_Id_Integracao_Tray, D002_Id_Integracao_Tray 
                 FROM D002
                 JOIN D003 ON D002_D003_Id = D003_Id
                 WHERE D002_Integrado_Tray = 'N'
                 AND   COALESCE(D003_Id_Integracao_Tray, 0) > 0 ";

                 // AND D002_Flag_Visualizar_Site = 'S' -- ???

        $query = mysql_query($sql);

        while ($subCategoriaERP = mysql_fetch_array($query)) {

            $subCategoriaTray = array('IDs' => array(), 'paraTray' => array(), 'deTray' => array());

            $subCategoriaTray['IDs']["D002_Id"]                     = $subCategoriaERP['D002_Id'];
            $subCategoriaTray['IDs']["D002_Id_Integracao_Tray"]     = $subCategoriaERP['D002_Id_Integracao_Tray'];

            $subCategoriaTray['paraTray']['name']                   = $subCategoriaERP['D002_Descricao_Produto']; // String (255) - Nome da categoria
            $subCategoriaTray['paraTray']['title']                  = $subCategoriaERP['D002_Descricao_Produto']; // String (255) - Title da pagina de categorias
            $subCategoriaTray['paraTray']['small_description']      = $subCategoriaERP['D002_Descricao_Produto']; // String - Descrição detalhada da Categoria
            $subCategoriaTray['paraTray']['has_acceptance_term']    = 0; // Number - Categoria com termo de aceitação ( 0 / 1)

            $subCategoriaTray['paraTray']['Metatag']                = array(); // Object - Meta Tag
            $subCategoriaTray['paraTray']['Metatag']['keywords']    = $subCategoriaERP['D002_Descricao_Produto']; // String (256) - Palavras-Chaves de Meta Tag
            $subCategoriaTray['paraTray']['Metatag']['description'] = $subCategoriaERP['D002_Descricao_Produto']; // String (256) - Descrição de Meta Tag

            $subCategoriaTray['paraTray']["parent_id"]              = $subCategoriaERP['D003_Id_Integracao_Tray']; // Id da categoria pai

            // $subCategoriaTray['paraTray']['slug']                   = $subCategoriaERP['']; // String (255) - Final da URL da categoria
            // $subCategoriaTray['paraTray']['order']                  = $subCategoriaERP['']; // Number - Posição na Ordenação das Categorias
            // $subCategoriaTray['paraTray']['acceptance_term']        = $subCategoriaERP['']; // String - Texto com os termos a ser aceito na categoria
            // $subCategoriaTray['paraTray']['property']               = $subCategoriaERP['']; // String - Características

            $subCategoriasTray[] = $subCategoriaTray;
        }

        return $subCategoriasTray;
    }

    private function setSubCategoriaIntegrada($subCategoriaTray, $updIdTray) {

        $D002_Id = $subCategoriaTray['IDs']['D002_Id'];

        $sqlD002  = " UPDATE D002 SET D002_Integrado_Tray = 'S' ";

        if ($updIdTray) {
            $sqlD002 .= " ,D002_Id_Integracao_Tray = {$subCategoriaTray['deTray']['id']} ";
        }

        $sqlD002 .= " WHERE D002_Id = {$D002_Id} ";

        mysql_query($sqlD002);
    }

}