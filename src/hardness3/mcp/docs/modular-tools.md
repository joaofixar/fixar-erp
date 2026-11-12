# Arquitetura modular das Tools MCP

## Composição em runtime

`ToolRegistry` é somente o índice ordenado de Tools públicas. A composição é
explícita e acontece no Bootstrap:

```text
Bootstrap
  -> ToolCatalogFactory([CompanyToolsModule, ProductToolsModule, QuoteToolsModule])
    -> ToolRegistry
      -> ToolExecutor
        -> JsonRpcServer
```

Não há descoberta por diretório, reflection, container genérico ou geração de
nomes. O Bootstrap conhece módulos de domínio e os componentes de composição,
nunca repositórios, serviços ou Tools concretas.

`ToolCatalogFactory` recebe a lista ordenada de `ToolModuleInterface`, reúne as
Tools e rejeita nomes públicos duplicados. Cada módulo constrói apenas as
dependências de seu domínio. Os módulos ativos são `CompanyToolsModule`,
`ProductToolsModule` e `QuoteToolsModule`.

## Vertical slice Company / GetCurrent

```text
Tools/Company/
  GetCurrent/
    GetCurrentCompanyDefinition.php
    GetCurrentCompanyInput.php
    GetCurrentCompanyHandler.php
    GetCurrentCompanyOutput.php
    GetCurrentCompanyTool.php
  Application/CompanyProfileService.php
  Domain/CompanyRepositoryInterface.php
  Infrastructure/PdoCompanyRepository.php
```

- `GetCurrentCompanyTool` é o adaptador MCP: expõe a definição, cria a entrada
  e delega ao handler.
- `GetCurrentCompanyDefinition` contém exclusivamente o contrato público.
- `GetCurrentCompanyInput` representa o objeto vazio aceito pela Tool.
- `GetCurrentCompanyHandler` coordena o caso de uso com `ExecutionContext`, sem
  conhecer JSON-RPC.
- `GetCurrentCompanyOutput` é uma allow-list da resposta pública e impede o
  retorno de colunas internas.
- Serviço e repositório são componentes compartilháveis somente enquanto houver
  reutilização real. Não existe `AuthorizationPolicy`: autorização por scope é
  centralizada no `ToolExecutor`.

## Convenções públicas estáveis

Nomes de Tool obedecem a:

```text
hardness.<domain>[.<subdomain>].<action>
```

Nomes são em inglês, minúsculos e `snake_case`; domínio é singular e ação fica
no final. Normalmente são três segmentos; quatro são admitidos somente para um
subdomínio real. Não usar tabelas, códigos internos, endpoints, telas,
controllers ou nomes derivados de classes.

Scopes obedecem a:

```text
<domain>[.<subdomain>]:<capability>
```

Use `read` para leitura e `write` para criação/alteração ordinária. Capacidades
sensíveis recebem scope próprio, como `order:cancel` ou `invoice.nfe:issue`.
Várias Tools podem compartilhar um scope e uma Tool só exige múltiplos scopes
quando as capacidades forem de fato independentes.

O catálogo administrativo só pode expor scopes ativos usados por uma Tool real.
`ToolCatalogConsistencyValidator`, coberto pelos testes, verifica nomes,
duplicidade, scopes exigidos, scopes ativos e a relação entre catálogo
administrativo e Tools registradas.

## Como adicionar um domínio ou uma Tool

1. Confirmar necessidade concreta e aprovar domínio, ação, descrição e scope.
2. Criar o módulo do domínio apenas quando existir sua primeira Tool real.
3. Criar o vertical slice do caso de uso: Definition, Input, Handler, Output e
   Tool; Application, Domain e Infrastructure são opcionais e só entram quando
   necessários.
4. Declarar explicitamente a Tool no módulo e o módulo no Bootstrap.
5. Adicionar o scope ativo ao `ScopeCatalog` somente se a Tool real o exigir,
   incluindo a relação administrativa `tools`.
6. Cobrir catálogo, schema, autorização, auditoria, resultado e regressão MCP.

Não criar módulos vazios, Tools fictícias, scopes sem Tool, abstrações vazias ou
nomes calculados a partir de pastas/classes.

## Consultas legadas iniciais

`hardness.product.search` usa `D001` como cadastro mestre e só torna um produto
visível quando existe o vínculo `D049`/`D009` para o `C004_Id` do
`ExecutionContext`. Ela retorna somente id, código, descrição e unidade.

`hardness.quote.search` e `hardness.quote.get_by_id` usam `T003` como limite de
empresa (`T003_C004_Id`). O detalhe lê `T004` somente com o mesmo `C004_Id` e
preserva a descrição histórica do item. Nenhuma dessas Tools aceita empresa como
argumento, nem retorna custo, margem, estoque, tributos ou observações livres.

## Checklist de revisão

- Nome público segue a convenção e não duplica intenção existente.
- Domínio, ação, descrição e contrato são de negócio e estáveis.
- Schema de entrada é mínimo, fechado e suportado pelo validador.
- Saída é explicitamente allow-listed e não vaza dados internos.
- Scope aplica menor privilégio; ações sensíveis têm capacidade específica.
- Scope existe, está ativo e referencia a Tool registrada.
- Tool, catálogo e scope passam pelas validações de nomenclatura e consistência.
- Tenant e `ExecutionContext` são a única fonte de contexto de empresa.
- Auditoria guarda evento seguro, sem argumentos ou dados empresariais.
- Alterações incompatíveis recebem nova Tool/versão e período de depreciação;
  uma Tool publicada não é renomeada nem reutilizada com nova semântica.
