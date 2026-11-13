# Tarefa 11 — Flag de disponibilidade de processo no Apontamento PCP

**Situação:** Concluída em 16/10/2026.

`SMT006_Flag_Apontamento` é a única regra de disponibilidade do catálogo para
o Apontamento PCP. É `enum('S','N')`, padrão `N`; não consulta `SMT084` e não
altera estruturas ou dados de engenharia, OP ou PCP existentes.

O terminal lista e inicia somente processos com a flag `S`, PCP006 ativo e
PCP004 ativo. A mesma validação ocorre dentro da transação de início/entrada,
portanto desabilitar o processo bloqueia inclusive novo participante em um
grupo já aberto. PCP004 existente é preservado; a configuração o mantém visível
e mostra em vermelho “Processo desabilitado no apontamento” quando estiver ativo.

O formulário de vínculo oferece somente processos liberados e o backend também
recusa tentativa direta de criar/reativar vínculo para processo não liberado.
Configurações → Processos permite editar a flag e filtra Todos, Liberados e
Não liberados.

Para instalação existente, `scripts/atualizacaoBase.php` chama `Criar_Campo`.
Migração opcional, somente para este cliente que queira aproveitar o cadastro
IDE atual (não é dependência do módulo):

```sql
UPDATE SMT006
INNER JOIN SMT084 ON SMT084.SMT084_SMT006_Id = SMT006.SMT006_Id
SET SMT006.SMT006_Flag_Apontamento = 'S';
```

`tests/task11-integration.php` cobre processo liberado/não liberado, vínculo
existente preservado e bloqueio do backend para nova entrada.
