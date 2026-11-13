<?php

declare(strict_types=1);

namespace HardnessMcp\Scopes;

use RuntimeException;

/** Server-side catalogue: only scopes backed by an existing MCP Tool belong here. */
final class ScopeCatalog
{
    /** @return array<int,array{id:string,name:string,description:string,tools:array<int,string>,active:bool}> */
    public function all(): array
    {
        return [[
            'id' => 'company:read',
            'group' => 'Empresa',
            'subgroup' => 'Dados básicos',
            'order' => 10,
            'name' => 'Consultar dados da empresa',
            'description' => 'Permite consultar os dados básicos da empresa vinculada à conexão MCP.',
            'tools' => ['hardness.company.get_current', 'hardness.system.context_pilot'],
            'active' => true,
        ], [
            'id' => 'product:prices:history',
            'group' => 'Produtos', 'subgroup' => 'Preços', 'order' => 24,
            'name' => 'Consultar histórico de preços',
            'description' => 'Permite consultar os lotes de atualização e reversão de preços da empresa.',
            'tools' => ['hardness.product.prices.history'], 'active' => true,
        ], [
            'id' => 'product:prices:export',
            'group' => 'Produtos', 'subgroup' => 'Preços', 'order' => 26,
            'name' => 'Exportar relatório de preços',
            'description' => 'Gera planilhas XLSX de conferência a partir do histórico de alterações de preços.',
            'tools' => ['hardness.product.prices.export'], 'active' => true,
        ], [
            'id' => 'product:prices:reverse',
            'group' => 'Produtos', 'subgroup' => 'Preços', 'order' => 25,
            'name' => 'Reverter atualização de preços aprovada',
            'description' => 'Prepara e aplica a reversão de preços de um lote anterior, após aprovação explícita.',
            'tools' => ['hardness.product.prices.reversal_prepare','hardness.product.prices.reversal_apply'], 'active' => true,
        ], [
            'id' => 'partner:search',
            'group' => 'Clientes e fornecedores',
            'subgroup' => 'Dados básicos',
            'order' => 15,
            'name' => 'Pesquisar clientes e fornecedores',
            'description' => 'Permite pesquisar, desambiguar e consultar os dados cadastrais básicos de parceiros selecionados. Indicada para uso interno, como funcionários utilizando assistentes de IA.',
            'tools' => ['hardness.partner.search', 'hardness.partner.get'],
            'active' => true,
        ], [
            'id' => 'partner:lookup',
            'group' => 'Clientes e fornecedores',
            'subgroup' => 'Dados básicos',
            'order' => 16,
            'name' => 'Localizar parceiro por CPF/CNPJ conhecido',
            'description' => 'Localiza somente pelo CPF/CNPJ informado, sem permitir pesquisa por nome ou descoberta de outros cadastros. Indicada para integrações e atendimentos externos, como WhatsApp, quando o cliente informa o próprio documento.',
            'tools' => ['hardness.partner.get_by_document'],
            'active' => true,
        ], [
            'id' => 'product:prices:apply',
            'group' => 'Produtos', 'subgroup' => 'Preços', 'order' => 23,
            'name' => 'Aplicar atualização de preços aprovada',
            'description' => 'Atualiza os preços 1 a 5 de um lote previamente preparado e aprovado, com histórico e controle de conflitos.',
            'tools' => ['hardness.product.prices.apply'], 'active' => true,
        ], [
            'id' => 'product:prices:prepare',
            'group' => 'Produtos', 'subgroup' => 'Preços', 'order' => 22,
            'name' => 'Consultar preços e preparar atualização',
            'description' => 'Consulta os preços 1 a 5 e grava uma prévia de atualização, sem alterar os preços do cadastro.',
            'tools' => ['hardness.product.prices.prepare'], 'active' => true,
        ], [
            'id' => 'product:read',
            'group' => 'Produtos',
            'subgroup' => 'Dados básicos',
            'order' => 20,
            'name' => 'Consultar produtos',
            'description' => 'Permite pesquisar os produtos vinculados à empresa da conexão MCP.',
            'tools' => ['hardness.product.search'],
            'active' => true,
        ], [
            'id' => 'product:partner-reference:lookup',
            'group' => 'Produtos',
            'subgroup' => 'Referências de parceiros',
            'order' => 21,
            'name' => 'Localizar produto por referência de parceiro',
            'description' => 'Localiza produtos pelo código de referência utilizado por um cliente ou fornecedor específico. Use quando o código informado pertence ao parceiro, e não ao cadastro interno do produto no ERP.',
            'tools' => ['hardness.product.get_by_partner_reference'],
            'active' => true,
        ], [
            'id' => 'quote:read',
            'group' => 'Orçamentos',
            'subgroup' => 'Consultas',
            'order' => 30,
            'name' => 'Consultar orçamentos',
            'description' => 'Permite pesquisar orçamentos e consultar os detalhes de um orçamento selecionado.',
            'tools' => ['hardness.quote.search', 'hardness.quote.get_by_id'],
            'active' => true,
        ], [
            'id' => 'quote:report:read',
            'group' => 'Relatórios',
            'subgroup' => 'Orçamentos',
            'order' => 35,
            'name' => 'Consultar relatórios de orçamentos',
            'description' => 'Permite consultar orçamentos individuais e também totais, gráficos e rankings de valores orçados, com análises históricas de até 5 anos.',
            'tools' => ['hardness.quote.summary'],
            'active' => true,
        ], [
            'id' => 'quote:item-report:read',
            'group' => 'Relatórios', 'subgroup' => 'Itens de orçamentos', 'order' => 35,
            'name' => 'Consultar relatórios de itens de orçamentos',
            'description' => 'Permite consultar ranking de produtos orçados, quantidade, preço líquido, custo, lucro e margem por período.',
            'tools' => ['hardness.quote.item_summary'], 'active' => true,
        ], [
            'id' => 'sales:read',
            'group' => 'Relatórios',
            'subgroup' => 'Vendas',
            'order' => 36,
            'name' => 'Consultar vendas faturadas por NF-e',
            'description' => 'Permite consultar NF-e individuais e também totais e gráficos de vendas faturadas, com análises históricas de até 5 anos.',
            'tools' => ['hardness.sales.summary'],
            'active' => true,
        ], [
            'id' => 'sales:item-report:read',
            'group' => 'Relatórios', 'subgroup' => 'Itens de notas fiscais', 'order' => 36,
            'name' => 'Consultar relatórios de itens faturados',
            'description' => 'Permite consultar ranking de produtos faturados, quantidade, preço líquido, custo, lucro e margem por período.',
            'tools' => ['hardness.sales.item_summary'], 'active' => true,
        ], [
            'id' => 'order:report:read',
            'group' => 'Relatórios',
            'subgroup' => 'Pedidos',
            'order' => 37,
            'name' => 'Consultar relatórios de pedidos',
            'description' => 'Permite consultar pedidos individuais e também totais, gráficos e rankings, com análises históricas de até 5 anos.',
            'tools' => ['hardness.order.summary'],
            'active' => true,
        ], [
            'id' => 'order:item-report:read',
            'group' => 'Relatórios', 'subgroup' => 'Itens de pedidos', 'order' => 37,
            'name' => 'Consultar relatórios de itens de pedidos',
            'description' => 'Permite consultar ranking de produtos pedidos, quantidade, preço líquido, custo, lucro e margem por período.',
            'tools' => ['hardness.order.item_summary'], 'active' => true,
        ], [
            'id' => 'product:conversion-report:read',
            'group' => 'Relatórios', 'subgroup' => 'Funil de produtos', 'order' => 38,
            'name' => 'Consultar funil de conversão por produto',
            'description' => 'Permite acompanhar a conversão de quantidades entre orçamento, pedido e nota fiscal, com receita, custo, lucro e margem faturados.',
            'tools' => ['hardness.product.conversion_summary'], 'active' => true,
        ], [
            'id' => 'commercial:opportunities:read',
            'group' => 'Relatórios', 'subgroup' => 'Oportunidades comerciais', 'order' => 39,
            'name' => 'Consultar oportunidades comerciais',
            'description' => 'Permite listar orçamentos sem conversão em pedido e pedidos sem faturamento, priorizados por valor pendente e tempo em aberto.',
            'tools' => ['hardness.commercial.opportunities'], 'active' => true,
        ], [
            'id' => 'finance:receivables:read',
            'group' => 'Relatórios', 'subgroup' => 'Financeiro', 'order' => 40,
            'name' => 'Consultar contas a receber',
            'description' => 'Permite consultar totais, vencimentos, situação, clientes, portadores e origem de contas a receber.',
            'tools' => ['hardness.finance.receivables_summary', 'hardness.finance.cash_flow_summary'], 'active' => true,
        ], [
            'id' => 'finance:payables:read',
            'group' => 'Relatórios', 'subgroup' => 'Financeiro', 'order' => 41,
            'name' => 'Consultar contas a pagar',
            'description' => 'Permite consultar totais, vencimentos, situação, fornecedores, portadores e origem de contas a pagar.',
            'tools' => ['hardness.finance.payables_summary', 'hardness.finance.cash_flow_summary'], 'active' => true,
        ], [
            'id' => 'quote:write',
            'group' => 'Orçamentos',
            'subgroup' => 'Criação',
            'order' => 40,
            'name' => 'Criar orçamentos',
            'description' => 'Permite criar um orçamento básico para a empresa vinculada à conexão MCP.',
            'tools' => ['hardness.quote.create', 'hardness.quote.generate_pdf_link'],
            'active' => true,
        ], [
            'id' => 'quote:item:add',
            'group' => 'Orçamentos', 'subgroup' => 'Itens', 'order' => 41,
            'name' => 'Incluir itens de orçamento',
            'description' => 'Permite incluir uma lista de produtos em um único orçamento pendente escolhido explicitamente.',
            'tools' => ['hardness.quote_item.add'], 'active' => true,
        ], [
            'id' => 'quote:item:update',
            'group' => 'Orçamentos', 'subgroup' => 'Itens', 'order' => 42,
            'name' => 'Alterar item de orçamento',
            'description' => 'Permite alterar somente campos controlados de um item de orçamento pendente.',
            'tools' => ['hardness.quote_item.update'], 'active' => true,
        ], [
            'id' => 'quote:item:delete',
            'group' => 'Orçamentos', 'subgroup' => 'Itens', 'order' => 43,
            'name' => 'Excluir item de orçamento',
            'description' => 'Permite excluir um único item de orçamento pendente.',
            'tools' => ['hardness.quote_item.delete'], 'active' => true,
        ], [
            'id' => 'ide_smt:quote:order:generate',
            'group' => 'Orçamentos', 'subgroup' => 'Geração de pedidos', 'order' => 44,
            'name' => 'Gerar pedido: PDV, Expedição, Caderneta Balcão ou Caderneta Expedição',
            'description' => 'Permite transformar um orçamento em pedido escolhendo um dos quatro destinos. O recurso também depende da configuração global da empresa.',
            'tools' => ['hardness.ide_smt.quote.generate_order_by_destination'], 'active' => true,
        ]];
    }

    /** @param mixed $scopes @return string[] */
    public function normalize($scopes): array
    {
        if (!is_array($scopes) || ($scopes !== [] && array_keys($scopes) !== range(0, count($scopes) - 1))) {
            throw new RuntimeException('Invalid scopes payload.');
        }
        $available = [];
        foreach ($this->all() as $scope) { if ($scope['active']) { $available[$scope['id']] = true; } }
        $normalized = [];
        foreach ($scopes as $scope) {
            if (!is_string($scope) || !isset($available[$scope])) { throw new RuntimeException('Unknown MCP scope.'); }
            $normalized[$scope] = true;
        }
        $result = array_keys($normalized);
        sort($result, SORT_STRING);
        return $result;
    }

    /** @param string[] $scopes */
    public function requiresErpActor(array $scopes): bool
    {
        return array_intersect($scopes, ['quote:write', 'quote:item:add', 'quote:item:update', 'quote:item:delete', 'ide_smt:quote:order:generate', 'product:prices:prepare', 'product:prices:apply', 'product:prices:reverse', 'product:prices:export']) !== [];
    }
}
