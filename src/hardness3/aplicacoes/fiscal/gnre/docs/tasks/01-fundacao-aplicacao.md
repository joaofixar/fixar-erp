# Task 01 — Fundação da aplicação e acesso ERP

**Situação:** concluída em 28/10/2026.

## Objetivo

Criar a aplicação isolada `aplicacoes/fiscal/gnre`, acessível pela sessão já
autenticada no ERP, sem criar tabelas GNRE e sem depender de código da NF-e.

## Implementado

- `bootstrap/app.php`: bootstrap independente, conexão ERP, contexto de
  usuário/empresa, CSRF e autorização por perfil. Não inclui nem chama classes
  de NF-e.
- `config/module.php`: metadados do módulo e permissão de acesso.
- `public/index.php` e `public/.htaccess`: entrada direta, sem rewrite global,
  listagem de diretório ou login alternativo.
- `scripts/atualizacaoBase.php`: registra `C029_Id=310`, **Acessar GNRE**, no
  grupo Fiscal. Esta é uma permissão ERP; não cria tabela GNRE.

## Decisões

- URL inicial: `/hardness3/aplicacoes/fiscal/gnre/public/index.php`. A tela
  inicial/menu do ERP deve apontar diretamente para ela, como ocorre no MVP do
  Apontamento PCP. Não foi alterado menu legado nesta tarefa.
- Toda requisição valida novamente `Usuario_Atual`, `Empresa_Atual`, C007
  ativa, C004 ativa, vínculo C019 e liberação C030 da permissão 310. A empresa
  e o perfil não vêm de parâmetros HTTP.
- Não há login próprio, persistência GNRE ou dependência de NF-e.

## Banco e implantação

Execute a atualização-base usual do ERP para inserir a permissão C029 310.
Depois, conceda **Acessar GNRE** ao perfil desejado para cada empresa em
`C030` e cadastre o atalho para a URL acima na tela inicial. Não há migration
de tabela nesta task.

## Como testar

1. Atualize a base e libere a permissão 310 a um perfil com acesso à empresa.
2. Entre no ERP com um usuário desse perfil, selecione a empresa e abra a URL.
3. A tela deve mostrar empresa e usuário atuais.
4. Remova a permissão, troque para empresa sem o vínculo ou acesse sem sessão:
   o módulo deve retornar HTTP 403 e não exibir dados.

## Validações executadas

- Lint PHP dos três arquivos PHP novos.
- Conferência de que não há tabelas GNRE, includes de NF-e ou alterações no
  código de NF-e.
- `git diff --check`.

## Pendências para a Task 02

- Aplicar a atualização-base e liberar C029 310 no ambiente de teste.
- Definir o atalho da tela inicial para a URL pública do módulo.
- A Task 02 criará somente `GNRE001` e `GNRE002`, junto com o cadastro manual.
