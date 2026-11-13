# Task 19 — preenchimento e pré-validação cadastral

> Reaberta em 02/11/2026 exclusivamente para investigar a divergência de
> município da homologação da guia #13. Não houve transmissão, consulta,
> alteração de estado ou escrita nas guias #8, #9 ou #13 nesta reabertura.

## Reabertura — município aceito pelo Portal

### Evidência observada

A guia #13 foi recebida em homologação (código 100, recibo 26000000045924),
mas o resultado individual retornou 247 para `contribuinteEmitente/municipio`
e 248 para `item/contribuinteDestinatario/municipio` na UF favorecida PR. Ambos
os elementos continham `41069`, derivado localmente de `D020_Codigo_IBGE=4106902`
(Curitiba/PR). A rejeição 235 deixou de ocorrer após a normalização da razão
social, portanto é evidência independente da divergência municipal.

### Fatos confirmados

- O XSD local `tiposBasicoGNRE_v1.00.xsd` define `TCodMunIBGE` como exatamente
  cinco algarismos. `dados_gnre_v2.00.xsd` usa esse tipo tanto no emitente como
  no destinatário.
- O Manual GNRE v2.17 (p. 14 da paginação do PDF / mensagens de validação)
  descreve município como “código do IBGE com 5 caracteres numéricos, sem o
  código da UF”. O mesmo manual lista 247 como município do emitente não
  cadastrado e 248 como município do destinatário não cadastrado para a UF
  favorecida.
- O manual também afirma, na seção de consulta de configuração, que `TConfigUf`
  retorna regras e receitas, mas não as regras específicas verificadas pelo
  serviço de cada UF. Nem o XSD/WSDL de configuração local contém uma lista de
  municípios. Logo GNRE007 não é fonte de cadastro/aceitação municipal.
- `D020_Codigo_IBGE` é o identificador interno do ERP. O valor de cinco posições
  produzido dele satisfaz somente formato/XML; a rejeição da #13 prova que isso
  não é prova de cadastro nem de aceitação pelo Portal/UF.

### Conclusão e estratégia recomendada

Não foi encontrada fonte oficial automatizável, local ou no resultado de
configuração, que associe município GNRE aceito à UF favorecida. Não é seguro
inventar outro truncamento, usar sete dígitos, criar fallback por UF ou tabela
de mapeamento. A seleção D018/D020 permanece apenas como auxílio cadastral e o
aviso deve deixar explícita esta limitação.

O menor fluxo manual controlado recomendado, antes de qualquer implementação,
é: obter junto à SEFAZ/Portal da UF favorecida a lista ou confirmação oficial do
código GNRE para a combinação município/UF; registrar a fonte, URL ou protocolo,
data de vigência, UF, município ERP, código de cinco dígitos confirmado e quem
validou; submeter essa proposta para aprovação fiscal. Só então uma tarefa
específica poderá decidir onde persistir/auditar essa confirmação. Esta Task não
cria cadastro manual nem alteração de modelo.

### Próximo teste permitido, após confirmação oficial

1. Criar uma nova guia de homologação, nunca retransmitir a #13.
2. Usar o código de cinco dígitos confirmado para PR tanto no emitente quanto no
   destinatário aplicável, mantendo a evidência anexada ao roteiro fiscal.
3. Validar XSD local e transmitir somente após autorização explícita.
4. Registrar o resultado individual; 247/248 ausentes confirmam apenas aquela
   combinação e ambiente, não uma regra geral.

Concluída localmente em 02/11/2026, sem chamada ao Portal, transmissão ou acesso
a `GNRE004`/`GNRE005`. A Task 18 também fica encerrada: em homologação as guias
#8 e #9 foram recebidas pelo Portal e rejeitadas individualmente por cadastro:
#8/247 (código do município do emitente não cadastrado), #9/235 (razão social do
destinatário não informada) e #9/236 (código do município do destinatário não
informado). As guias não foram modificadas nesta task.

## Implementação

- `GnreCompanyEmitter` lê a cadeia `C004.C004_D020_Id → D020 → D018`, preserva
  o código ERP bruto e apresenta UF, cidade, código XML e código D020 no bloco
  Emitente. Não atualiza C004, D018 nem D020.
- O XSD v2.00 confirma `municipio` como `TCodMunIBGE`; o tipo básico local fixa
  cinco dígitos. `GnreMunicipality` conserva `41069` como cinco dígitos e não o
  preenche/converte para sete. Quando D020 contém o formato ERP de sete dígitos,
  usa os cinco primeiros para o contrato GNRE.
- Destinatário agora seleciona UF de D018 e cidade D020 filtrada por ela; persiste
  o código formatado para o XSD em `GNRE003_Destinatario_Codigo_Municipio`, sem
  alegar compatibilidade/cadastro no Portal.
  A origem da UF pode ser reconstruída pelo código persistido, sem tabela GNRE.
- `GnreSemanticValidator` é deliberadamente separado: XSD mantém estrutura,
  `TConfigUf` mantém aplicabilidade/listas, e a camada nova verifica somente
  município de cinco dígitos e, quando destinatário é aplicável e identificado,
  razão social e município. Não há regra fixa por UF.

## Evidência e limites

O retorno do Portal confirma as três rejeições, mas não oferece nesta evidência
uma lista pública de municípios cadastrados por UF. Portanto o erro 247 é
diagnosticado contra a cadeia ERP e formato do XML, sem alegar que D020 cadastra
automaticamente o município no Portal; a limitação é exibida por não haver fonte
oficial local para antecipá-la. A inscrição estadual/“inscrito na UF favorecida”
continua lacuna separada: o XSD aceita IE na identificação, mas não foi criado
campo ou serialização sem contrato fiscal específico.

## Testes

`php tests/task19-cadastro-semantico-synthetic.php` cobre resolução D018/D020,
filtro e reabertura por código, destinatário sem razão social/município e código
municipal inválido. A preservação de #8/#9 é por escopo: o teste não abre banco,
e a implementação não escreve GNRE001/GNRE004/GNRE005 fora da gravação normal de
um rascunho explicitamente editado.
