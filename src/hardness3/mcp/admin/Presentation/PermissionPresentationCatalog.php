<?php

declare(strict_types=1);

namespace HardnessMcp\AdminUi\Presentation;

/**
 * Administrative copy only. It never participates in scope validation,
 * authorization, MCP Tool definitions or public OAuth/MCP descriptions.
 */
final class PermissionPresentationCatalog
{
    /** @return array{allows?:string,limitations?:array<int,string>,recommended_use?:string,usage?:string} */
    public function forScope(string $scopeId): array
    {
        $catalog = [
            'company:read' => [
                'allows' => 'Permite consultar os dados básicos da empresa vinculada à conexão, como identificação, CNPJ, cidade, estado e situação cadastral.',
                'limitations' => ['Não permite alterar dados da empresa.', 'Não permite consultar dados de outra empresa.'],
            ],
            'partner:search' => [
                'allows' => 'Permite pesquisar clientes e fornecedores pelo nome, nome fantasia ou CPF/CNPJ e consultar os dados cadastrais básicos do cadastro selecionado.',
                'limitations' => ['Não permite criar, alterar ou excluir clientes e fornecedores.', 'Quando houver mais de um resultado, o parceiro precisa ser escolhido antes da consulta detalhada.'],
                'recommended_use' => 'Indicada para uso interno, quando funcionários usam um assistente de IA para localizar um parceiro mesmo sem conhecer seu CPF/CNPJ.',
                'usage' => 'internal',
            ],
            'partner:lookup' => [
                'allows' => 'Permite localizar um cliente ou fornecedor somente pelo CPF ou CNPJ informado e consultar os dados cadastrais básicos do cadastro encontrado.',
                'limitations' => ['Não permite pesquisar por nome ou nome fantasia.', 'Não permite criar, alterar ou excluir cadastros.'],
                'recommended_use' => 'Indicada para integrações externas, como atendimento por WhatsApp, quando a própria pessoa informa seu documento.',
                'usage' => 'external',
            ],
            'product:read' => [
                'allows' => 'Permite pesquisar os produtos disponíveis para a empresa vinculada pela descrição ou código e consultar seus dados básicos.',
                'limitations' => ['Não permite criar, alterar ou excluir produtos.', 'Não permite consultar produtos fora da empresa vinculada à conexão.'],
            ],
            'product:partner-reference:lookup' => [
                'allows' => 'Permite localizar um produto pelo código de referência usado por um cliente ou fornecedor específico.',
                'limitations' => ['Não pesquisa por código interno, descrição, código de barras ou outros identificadores.', 'Não permite criar, alterar ou excluir produtos.'],
            ],
            'quote:read' => [
                'summary' => 'Permite pesquisar orçamentos e consultar os detalhes de um orçamento selecionado.',
                'allows' => 'Permite pesquisar os orçamentos da empresa vinculada e consultar um orçamento selecionado com seus itens.',
                'limitations' => ['Não permite criar, alterar ou excluir orçamentos ou itens.', 'Não permite consultar orçamentos de outra empresa.', 'Não permite consultar totais, gráficos ou rankings gerenciais; para isso, habilite Consultar relatórios de orçamentos.'],
            ],
            'quote:report:read' => [
                'summary' => 'Permite consultar orçamentos individuais, totais, gráficos e rankings de valores orçados.',
                'allows' => 'Permite listar orçamentos com cliente, grupo, cliente da proposta quando disponível, status e total. Também permite agrupar valores por dia, semana, mês, cliente, vendedor interno, supervisor ou status.',
                'limitations' => ['Resumos por mês, cliente, vendedor interno, supervisor ou status aceitam até 5 anos.', 'O agrupamento semanal e a listagem de documentos aceitam até 12 meses; o agrupamento diário aceita até 90 dias.', 'Rankings e listagens de documentos retornam no máximo 50 posições.', 'Não retorna itens nem análise por produto.', 'Não permite dividir períodos maiores em consultas sequenciais.'],
                'recommended_use' => 'Indicada para gestão comercial, acompanhamento de valores orçados e consulta dos documentos que compõem o período.',
            ],
            'quote:item-report:read' => [
                'summary' => 'Permite analisar os produtos dos orçamentos, inclusive custo e margem.',
                'allows' => 'Permite consultar ranking de produtos por período, com código, descrição, quantidade, preço líquido médio, receita líquida, custo, lucro bruto e margem.',
                'limitations' => ['Não retorna orçamentos individuais.', 'O período máximo é de 12 meses e o ranking retorna no máximo 50 produtos.', 'Inclui custo e margem; conceda somente a pessoas autorizadas.'],
                'recommended_use' => 'Indicada para acompanhar produtos mais orçados e a rentabilidade potencial da carteira comercial.',
            ],
            'sales:read' => [
                'summary' => 'Permite consultar NF-e individuais, totais e gráficos de vendas faturadas.',
                'allows' => 'Permite listar NF-e com número, ID, cliente, grupo, cliente da proposta quando disponível e total. Também permite agrupar o faturamento por dia, semana, mês, cliente ou vendedor interno. Considera NF-e de saída não canceladas e com protocolo.',
                'limitations' => ['Resumos por mês, cliente ou vendedor interno aceitam até 5 anos.', 'O agrupamento semanal e a listagem de documentos aceitam até 12 meses; o agrupamento diário aceita até 90 dias.', 'Rankings e listagens de documentos retornam no máximo 50 posições.', 'Não permite análise por produto.', 'Não permite dividir períodos maiores em consultas sequenciais.'],
                'recommended_use' => 'Indicada para acompanhar faturamento, evolução de vendas, rankings comerciais e as NF-e que compõem o período.',
            ],
            'sales:item-report:read' => [
                'summary' => 'Permite analisar os produtos faturados por NF-e, inclusive custo e margem.',
                'allows' => 'Permite consultar ranking de produtos faturados por período, com código, descrição, quantidade, preço líquido médio, receita líquida, custo, lucro bruto e margem.',
                'limitations' => ['Considera somente NF-e de saída autorizadas e não canceladas.', 'Não retorna NF-e individuais.', 'O período máximo é de 12 meses e o ranking retorna no máximo 50 produtos.'],
                'recommended_use' => 'Indicada para identificar produtos líderes de faturamento e margem realizada.',
            ],
            'order:report:read' => [
                'summary' => 'Permite consultar pedidos individuais, totais, gráficos e rankings.',
                'allows' => 'Permite listar pedidos com cliente, grupo, cliente da proposta quando disponível, status e total. Também permite agrupar valores por dia, semana, mês, cliente, vendedor interno ou status.',
                'limitations' => ['Inclui todos os status por padrão; use o filtro de status quando precisar de um recorte específico.', 'Resumos por mês, cliente, vendedor interno ou status aceitam até 5 anos.', 'O agrupamento semanal e a listagem de documentos aceitam até 12 meses; o agrupamento diário aceita até 90 dias.', 'Rankings e listagens de documentos retornam no máximo 50 posições.', 'Não retorna itens nem análise por produto.', 'Não permite dividir períodos maiores em consultas sequenciais.'],
                'recommended_use' => 'Indicada para acompanhamento comercial dos pedidos, sua distribuição por status e os documentos que compõem o período.',
            ],
            'order:item-report:read' => [
                'summary' => 'Permite analisar os produtos dos pedidos, inclusive custo e margem.',
                'allows' => 'Permite consultar ranking de produtos pedidos por período, com código, descrição, quantidade, preço líquido médio, receita líquida, custo, lucro bruto e margem.',
                'limitations' => ['Não retorna pedidos individuais.', 'O período máximo é de 12 meses e o ranking retorna no máximo 50 produtos.', 'Inclui custo e margem; conceda somente a pessoas autorizadas.'],
                'recommended_use' => 'Indicada para acompanhar demanda por produto antes do faturamento.',
            ],
            'product:conversion-report:read' => [
                'summary' => 'Permite acompanhar a conversão de produtos entre orçamento, pedido e NF-e.',
                'allows' => 'Permite consultar quantidades orçadas, pedidas e faturadas, percentuais de conversão, saldos pendentes e resultado financeiro faturado por produto.',
                'limitations' => ['O período se refere à emissão do orçamento; pedidos e NF-e vinculados podem ser posteriores.', 'Não retorna documentos individuais.', 'Inclui custo, lucro e margem; conceda somente a pessoas autorizadas.'],
                'recommended_use' => 'Indicada para identificar gargalos comerciais e operacionais entre cotação, pedido e faturamento.',
            ],
            'commercial:opportunities:read' => [
                'summary' => 'Permite obter uma lista priorizada de oportunidades para ação do time comercial.',
                'allows' => 'Permite listar orçamentos ainda não convertidos em pedido e pedidos com valor ainda não faturado, com cliente, vendedor, idade e valor pendente estimado.',
                'limitations' => ['Não infere último contato, motivo de perda ou recompra sem registro padronizado no ERP.', 'O valor pendente é calculado a partir dos itens do documento.', 'Não permite alterar documentos ou enviar comunicações.'],
                'recommended_use' => 'Indicada para definir uma fila diária de follow-up comercial e de acompanhamento de faturamento.',
            ],
            'quote:write' => [
                'allows' => 'Permite criar um orçamento básico para a empresa vinculada e obter o link do PDF quando disponível.',
                'limitations' => ['Não permite alterar ou excluir um orçamento já existente.', 'Não permite incluir, alterar ou excluir itens por esta permissão.'],
            ],
            'quote:item:add' => [
                'summary' => 'Permite incluir um ou vários produtos em um único orçamento pendente escolhido explicitamente.',
                'allows' => 'Permite incluir um ou vários produtos em um único orçamento pendente escolhido explicitamente pelo usuário.',
                'limitations' => ['Não escolhe orçamento automaticamente nem distribui itens entre orçamentos.', 'Os preços são calculados pelo ERP; não permite informar preço ou desconto manual.', 'Não permite alterar ou excluir itens.'],
            ],
            'quote:item:update' => [
                'allows' => 'Permite alterar um campo controlado de um item de orçamento pendente: quantidade, desconto, preço unitário ou ordem do item. Uma atualização coletiva só pode atingir itens de um único orçamento escolhido explicitamente.',
                'limitations' => ['Não permite alterar itens de mais de um orçamento na mesma solicitação.', 'Não escolhe orçamento, item, campo ou valor em nome do usuário.', 'Não permite incluir ou excluir itens.'],
            ],
            'quote:item:delete' => [
                'allows' => 'Permite excluir um único item específico de um orçamento pendente, após a confirmação explícita do usuário.',
                'limitations' => ['Não permite excluir vários itens, todos os itens ou itens de mais de um orçamento na mesma solicitação.', 'Cada exclusão exige uma nova solicitação e uma nova confirmação.', 'Não permite incluir ou alterar itens.'],
            ],
        ];

        return $catalog[$scopeId] ?? [];
    }
}
