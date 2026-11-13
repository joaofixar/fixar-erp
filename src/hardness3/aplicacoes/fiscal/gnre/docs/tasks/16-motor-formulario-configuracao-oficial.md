# Task 16 — Motor de formulário por configuração oficial GNRE

## Resultado

Concluída em 29/10/2026. A Task 15 permanece em validação manual e não é a
interface final. Nenhum arquivo do fluxo, layout, CSS ou JavaScript dela foi
alterado. A Task 17 deverá construir a nova jornada visual consumindo este
contrato, sem reproduzir regras no navegador.

## Correção pendente de aprovação

Esta task continua **aguardando validação**; a Task 17 permanece bloqueada.
Esta correção altera somente o contrato e seus testes, sem tocar na interface
congelada da Task 15, persistência ou protocolo.

## Arquitetura e contrato

`src/FormDefinitionBuilder.php` expõe `buildGuide($rules, $type, $context)` e
`buildItem($rules, $type, $receipt, $context)`. Ambos recebem o resultado já
interpretado de `GnreConfigUfRules::fromXml()`; portanto não consultam Portal,
não persistem e não calculam tributos. Retornam arrays serializáveis em JSON:
`version`, `level`, `uf`, `gnre_type`, `receipt` e `fields`.

Todo campo possui `id`, `level` (`guide`, `item`, `value` ou `field`), `label`, `help`,
`visible`, `state` (`S`, `O`, `N` ou `unavailable`), `component`, `options`,
`source`, `unavailable_reason`, `clear_rule`, `children` e `meta`. Grupos têm
filhos explícitos e seus filhos carregam a regra de limpeza; `N` não é renderizado;
`unavailable` também não deve ganhar input e informa por que a resposta oficial
não permite uma experiência válida. A UI deve limpar valores conforme
`clear_rule`, principalmente em `clear_on_receipt_change`.

## Mapeamento

As listas de receita, tipo, detalhamento, produto, período e tipo documental
viram `selection`. `exigePeriodoReferencia` produz o grupo/mês/ano de
referência, enquanto `exigePeriodoApuracao` produz `assessment_period`; são
campos independentes. Documento, destinatário e emitente são grupos de
identificação. Vencimento/pagamento são datas. Campos adicionais usam código,
título, tipo, tamanho e decimais de `camposAdicionais`, não JSON para o usuário.

`valorExigido` projeta 11 (principal), 21 (total), ambos, opcionais ou nenhum
para `N`; FCP usa 12/22 e a semântica N/S/O já confirmada na Task 14. Convênio
e FCP com `O` são obrigatórios, conforme aquela regra universal. Tipo 0/1/2 e
interseções UF/receita são rejeitados claramente antes de retornar item.

O grupo de emitente descreve a decisão de inscrição na UF favorecida e os
ramos inscrito/não inscrito, identificação, razão social e endereço. Documento
de origem descreve tipo oficial, número, descrição local e vínculo `GNRE002`.
Destinatário descreve situação de inscrição, tipo/identificação, razão social
e município IBGE. Em todos os casos cada filho recebe estado próprio; grupo
obrigatório nunca é devolvido sem filhos renderizáveis.

Convênio e FCP usam uma única semântica no builder: `N` e `S` são opcionais e
visíveis; `O` é obrigatório. Assim, o motor não oculta um valor que a proteção
fiscal da Task 14 ainda permite. FCP conserva os metadados de pares 12→11 e
22→21.

Regras universais permanecem identificadas como `universal`/`Task 14`; a UF
continua sendo a única fonte das exigências por receita. Não há condição por
UF, inclusive no cenário de aceitação PR/100102 (representado por configuração
equivalente, sem qualquer exceção no serviço).

## Cobertura e testes

`tests/fixtures/task16-config-uf-minimal.xml` é validada contra o XSD local;
`tests/task16-form-definition-synthetic.php` cria fixtures locais de TConfigUf
para receita simples, completa, documento/destinatário/campos adicionais,
`valorExigido=N`, configuração insuficiente, tipos 0/1/2 e cenário PR. Ele
instancia o builder, confirma contrato consumível pelo servidor e futura tela,
limpeza, campos proibidos, indisponibilidade, grupos/filhos obrigatórios e
separação dos períodos. O teste de completude também bloqueia seleção
obrigatória sem opções, obrigatório sem componente e grupo obrigatório sem
filho preenchível.

Não há uma cópia bruta sanitizável da resposta real PR/100102 no repositório
ou nos artefatos desta task. O cenário sintético anterior permanece útil apenas
como interpretação genérica e está marcado **a confirmar em homologação**;
não foi promovido a regra estadual nem declarado como resposta oficial. Para
encerrar essa lacuna, a validação precisa fornecer a resposta `TConfigUf`
obtida no teste manual (com dados sensíveis removidos), que poderá ser
preservada integralmente em fixture sem chamada ao Portal.

Em 29/10/2026 foi verificado também o cache/sessões locais já existentes, sem
nova chamada ao Portal. O único XML PR encontrado tem SHA-256
`c4f74c020ae9e829275127816f70ac4b0ea851754899568005103b742253f72d`, mas é
um envelope SOAP com situação `102` (CNPJ não habilitado), contém dado sensível
na mensagem e não inclui receitas, tipos, listas ou `valorExigido`. Ele foi
deliberadamente descartado: não é uma `TConfigUf` válida nem uma base segura
para fixture de aceitação.

## Limitações e handoff para Task 17

O motor descreve apenas a definição; não substitui `GnreConfigUfRules` nem
`GnreXmlGenerator`, que são a proteção final. A Task 17 deve chamar o builder
após a resposta oficial carregada e usar exclusivamente `fields` para render,
validar e limpar o estado visual. Não deve modificar persistência, XML,
transmissão, consulta, fila, certificado ou Portal sem nova autorização.
