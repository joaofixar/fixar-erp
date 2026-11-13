# Task 00 — Fundação documental e contrato oficial

**Situação:** concluída em 28/10/2026.

## Objetivo

Criar a fonte de verdade do módulo GNRE, consolidar as decisões de arquitetura
e preservar o contrato técnico oficial usado nas tarefas futuras. Esta tarefa
não cria banco, interface, cliente SOAP, fila ou transmissão.

## Escopo realizado

- Criado `docs/PROJECT_STATUS.md` com objetivo do MVP, decisões, tarefas,
  critérios de continuidade e a vedação explícita de dependência do código de
  NF-e.
- Movido o pacote fornecido pelo usuário para
  `docs/referencias/gnre-v2.17/`, mantendo o PDF e os 16 XSDs originais.
- Criado o inventário verificável do pacote em `docs/referencias/README.md`.
- Revisado o manual v2.17: SOAP 1.2, Document/Literal, TLS 1.2 com autenticação
  mútua, recepção assíncrona, recibo, consulta posterior, HTTP 429/`Retry-After`
  e limite de 500 requisições/minuto/IP (120 ms entre chamadas).

## Decisões confirmadas

1. A aplicação será independente em `aplicacoes/fiscal/gnre`; código da NF-e nunca
   será chamado ou incluído.
2. A experiência será cadastrar e transmitir sob ação do usuário. O ciclo
   posterior de recibo exige fila própria persistente, implementada apenas
   quando a transmissão existir.
3. O banco será criado em etapas: Task 02 cria `GNRE001` e `GNRE002`; Task 04
   cria `GNRE003` para os itens indispensáveis ao XML; Task 05 criará a tabela
   de tentativas e Task 06 a fila. Não existe tabela reservada para
   configuração futura neste MVP.
4. A primeira transmissão real será em homologação, com CNPJ habilitado no
   Portal e certificado disponível.

## Validações executadas

- Listagem conferida: PDF do manual e 16 XSDs presentes.
- PDF conferido: versão 2.17, setembro de 2026, data de revisão 03/09/2026.
- Conferidos endpoints de homologação para `GnreLoteRecepcao`,
  `GnreResultadoLote`, `GnreLoteRecepcaoConsulta`, `GnreResultadoLoteConsulta`
  e `GnreConfigUF`.
- Calculados SHA-256 de todos os artefatos; ver inventário.
- Nenhuma migration, tabela, código executável ou configuração de produção foi
  criada nesta tarefa.

## Como verificar

1. Abra `docs/PROJECT_STATUS.md` e confirme o objetivo e a sequência das
   tarefas.
2. Abra `docs/referencias/README.md` e compare os hashes se quiser confirmar
   que o material é o pacote original entregue.
3. Confirme que não existem arquivos em `aplicacoes/fiscal/gnre` fora de `docs/`.

## Pendências para a Task 01

- Definir a permissão ERP específica a ser criada/reutilizada e o ponto de menu
  na tela inicial.
- Levantar o padrão de bootstrap de aplicação isolada adequado à sessão ERP.
- O WSDL não veio no pacote. A Task 03 deverá capturá-lo diretamente dos
  endpoints de homologação, registrando data e hash antes de codificar SOAP.
