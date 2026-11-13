# Task 09 — Configuração oficial da UF e emitente

**Situação:** concluída e validada manualmente em homologação em 29/10/2026.

## Entrega

- `GnreConfigUfRules` transforma `TConfigUf` em modelo estruturado, sem regras fixas por UF: receitas, detalhamentos, produtos, períodos, parcela, documentos, tipos de GNRE, destinatário, valores, campos adicionais, XML permitido, limites, PIX e indicadores de emitente/endereço.
- A consulta continua sendo ação explícita da tela. O XML bruto é mostrado para auditoria e as regras são mantidas apenas na sessão da empresa, sem tabela/cache de banco. Antes de transmitir, a própria ação explícita de transmissão consulta a configuração novamente e valida antes de iniciar a transação ou criar `GNRE004`.
- Falta de configuração, SOAP/XML inválido, UF/receita ausente, lista incompatível e requisito oficial desconhecido impedem geração/transmissão localmente. Campos exigidos pelo contrato mas ainda sem representação no cadastro manual (data de pagamento e convênio) também falham fechados.
- Os campos de receita, detalhamento, produto, período e tipo de documento usam listas oficiais na tela quando carregadas; o servidor revalida as listas, portanto uma alteração do navegador não as contorna.
- `GnreCompanyEmitter` é independente de NF-e e lê `C004`, `D020` e `D018`. Mapeamento: CNPJ `C004_Cnpj`; razão social `C004_Nome_Empresa`; endereço `C004_Logradouro`, `C004_Numero`, `C004_Bairro`; município `D020_Codigo_IBGE`; UF `D018_UF`; CEP `C004_Cep`; telefone `C004_DDD_Telefone` + `C004_Telefone`. O código IBGE de sete posições é formatado nos cinco exigidos pelo XSD GNRE local. Esta adequação de formato não prova cadastro/aceitação no Portal; a homologação da Task 19 registrou a limitação. Dados faltantes bloqueiam com o campo cadastral apontado.
- O XML acrescenta `contribuinteEmitente` somente quando a receita permite/exige; endereço, município, UF, CEP e telefone somente quando `enderecoObr/emitCnpj` assim determina.
- Correção progressiva: `public/task09-progressive.js` recebe somente as regras já presentes na sessão, recalcula a tela a cada troca de receita, troca combos oficiais, limpa valores incompatíveis, oculta campos proibidos e identifica obrigatório/opcional. Ao proibir documento ou destinatário, limpa `document_id`, tipo de documento, tipo/identificação/razão social/município do destinatário e remove a obrigatoriedade antes do POST. Ao proibir campos adicionais limpa o JSON; quando permitidos, preserva somente códigos pertencentes à nova receita. O tipo GNRE permitido é mostrado por receita; o tipo efetivo continua corretamente calculado a partir dos itens/documentos, e não é um valor livre do navegador.
- Campos adicionais são apresentados orientados por título, tipo/tamanho e obrigatoriedade retornados; a edição sincroniza o JSON já existente. O bloco **Emitente** aparece quando aplicável, expõe os dados do cadastro e cada pendência cadastral antes da geração.

## Fontes oficiais

- Manual de Integração GNRE v2.17 e XSDs versionados em `docs/referencias/gnre-v2.17/`.
- Em especial `config_uf_v1.00.xsd` (contrato `TConfigUf`) e `dados_gnre_v2.00.xsd` (emitente e leiaute do lote).

## Arquivos e banco

- Alterados: `src/ConfigUfRules.php`, `src/CompanyEmitter.php`, `src/ConfigUfClient.php`, `src/XmlGenerator.php`, `public/index.php`, teste e documentação.
- Não há migration nem mudança em GNRE001–GNRE005. A configuração e o emitente não são duplicados em tabelas GNRE.

## Testes

- Fixture sintética: `tests/task09-config-uf-synthetic.php`, cobrindo receitas/listas distintas, receita proibida, obrigatório de emitente, emitente incompleto e XML com emitente.
- Teste de interface sem navegador: `tests/task09-progressive-ui.js`, cobrindo troca de receita, limpeza de valores incompatíveis — inclusive documento e todos os campos do destinatário ocultados — estados obrigatório/opcional/proibido, tipos permitidos e emitente exigido.
- Executado no PHP 7 do ERP: `task09 synthetic: OK`.
- Executados lint PHP e `git diff --check`.
- Nenhuma consulta de configuração, transmissão ou consulta de resultado foi feita ao Portal nesta task.

## Validação manual em homologação

- Consulta explícita de configuração para a UF **BA**, em homologação, retornou
  código oficial **450** — “Consulta da Configuração efetuada com sucesso”.
- Foram retornadas **13 receitas**. A inspeção do XML confirmou regras por
  receita para emitente, detalhamento, produto, período, documento,
  destinatário, vencimento, pagamento, convênio, campos adicionais, tipos
  GNRE e PIX.
- A validação foi somente de configuração: nenhuma guia foi criada,
  transmitida ou consultada.

## Roteiro manual

1. Abra a GNRE e use **Carregar/atualizar requisitos** para UF/receita desejada.
2. Verifique XML bruto e listas oficiais; grave guia/item apenas com valores oferecidos.
3. Gere o XML: teste intencionalmente produto/detalhamento não permitido e cadastro de empresa incompleto; ambos devem falhar localmente.
4. Com cadastro completo e receita que exige emitente, gere XML e confirme `contribuinteEmitente`.
5. Antes de qualquer transmissão homologada, confira que a configuração é renovada e que uma falha não cria `GNRE004`/`GNRE005`.

## Limitações

- O cadastro manual atual não possui data de pagamento ou convênio; se a UF os exigir, a guia é bloqueada com mensagem explícita. Não foi criada persistência especulativa para esses campos.
- A guia #4 não foi tocada: seu resultado final rejeitado continua sem retransmissão ou alteração.
