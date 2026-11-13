# Task 74 — schema mínimo consolidado para lote GNRE real

## Escopo

Esta task consolida somente a persistência futura de lote físico em `GNRE010` e `GNRE011`. Não gera XML, não chama o Portal, não transmite, não consulta recibo/resultado, não trata PDF, não cria worker/fila de lote e não altera a Fila sequencial do MVP. `GNRE004`, `GNRE005` e `GNRE008` continuam exclusivamente no fluxo individual já operacional.

O ambiente continua vindo do `GNRE007` imutável. O serviço de preparação exige empresa, ambiente, UF, snapshot e layout homogêneos; a proteção transacional contra participação concorrente agora abrange todo lote ativo (`PREPARADO`, `ENVIANDO`, `RECEBIDO`, `PENDENTE` ou `INCONCLUSIVO`), e não apenas a coorte local `PREPARADO`. Não foi introduzida coluna mutável em `GNRE001`.

## Schema para instalações novas

O `CREATE TABLE` em `scripts/atualizacaoBase.php` passa a criar as duas tabelas já consolidadas. A preparação local existente continua inserindo somente os campos originais e deixa todos os campos futuros de transporte/consulta nulos.

### `GNRE010`: cabeçalho de lote físico

Além dos campos originais de empresa, snapshot, UF, ambiente, layout, versão do fingerprint, situação e criação, foram adicionados:

| Campo | Finalidade futura |
| --- | --- |
| `GNRE010_Chave_Idempotencia` | Chave persistida antes da chamada física; única por empresa quando preenchida. |
| `GNRE010_XML_SHA256`, `GNRE010_XML_Fisico` | Evidência congelada do XML físico. |
| `GNRE010_Data_Hora_Envio`, `GNRE010_C007_Id_Solicitante_Envio` | Auditoria da solicitação de envio. |
| `GNRE010_HTTP_Recepcao_Status`, `GNRE010_SOAP_Recepcao`, `GNRE010_Mensagem_Tecnica` | Evidência técnica da recepção, sem credenciais. |
| `GNRE010_Recibo`, `GNRE010_Data_Hora_Recibo` | Recibo único compartilhado e seu horário oficial preservado. |
| `GNRE010_Proxima_Tentativa_Consulta`, `GNRE010_Consulta_Lock_Token`, `GNRE010_Consulta_Lock_Ate` | Agenda e lease futuros da consulta única do lote. |
| `GNRE010_Ultima_Consulta_SOAP`, `GNRE010_Data_Hora_Ultima_Consulta` | Última evidência de consulta futura. |

`GNRE010_Situacao` permite exatamente `PREPARADO`, `ENVIANDO`, `RECEBIDO`, `PENDENTE`, `FINALIZADO`, `INCONCLUSIVO`, `REJEITADO` e `CANCELADO`. `UQ_GNRE010_Empresa_Idempotencia` protege `(empresa, chave de idempotência)`. Não há coluna nem binário para PDF coletivo.

### `GNRE011`: participante imutável

Foram mantidos lote, guia, fragmento congelado, hashes/fingerprint, decisão e candidatos de correlação e criação. Foram adicionados:

| Campo | Finalidade futura |
| --- | --- |
| `GNRE011_Ordinal_Envio` | Ordem auditável da composição; é expressamente proibido usá-la para correlação. |
| `GNRE011_Situacao` | Ciclo individual futuro: `PREPARADO`, `PENDENTE`, `FINALIZADO`, `INCONCLUSIVO` ou `REJEITADO`. |
| `GNRE011_Situacao_Guia` | `situacaoGuia` oficial individual quando recebido; somente `0` persistido e inequivocamente correlacionado confirma autorização. |
| `GNRE011_Resultado_Individual`, `GNRE011_Motivos_Rejeicao` | Evidências individuais futuras. |
| `GNRE011_Data_Hora_Decisao` | Data/hora da decisão individual persistida. |

Permanecem a unicidade `(GNRE011_GNRE010_Id, GNRE011_GNRE001_Id)`, índice por guia, índice por lote e o novo índice `(lote, situação)`. Fragmento, hashes, fingerprint e identidade da participante não são reescritos pelo modelo de resultado futuro.

## Atualização manual idempotente para base local existente

Não executei esta atualização. Ela é necessária apenas para uma base que já recebeu as versões preparatórias de `GNRE010`/`GNRE011` da Task 66.

No atualizador do projeto, após carregar `scripts/atualizacaoBase-funcoes.php`, execute uma vez o bloco abaixo. `Criar_Campo` e `Criar_Indice` verificam a existência antes de emitir `ALTER TABLE`; `Alterar_Tipo_Campo` amplia o enum de situação sem apagar linhas. O índice único é criado pelo helper com o nome idempotente `IDX_GNRE010_C004_Id_GNRE010_Chave_Idempotencia` nesta base já existente (o `CREATE TABLE` de instalações novas usa `UQ_GNRE010_Empresa_Idempotencia`).

```php
Alterar_Tipo_Campo('GNRE010_Situacao', "enum('PREPARADO','ENVIANDO','RECEBIDO','PENDENTE','FINALIZADO','INCONCLUSIVO','REJEITADO','CANCELADO') NOT NULL DEFAULT 'PREPARADO'");
Criar_Campo('GNRE010_Chave_Idempotencia', 'varchar(64) DEFAULT NULL');
Criar_Campo('GNRE010_XML_SHA256', 'char(64) DEFAULT NULL');
Criar_Campo('GNRE010_XML_Fisico', 'mediumtext DEFAULT NULL');
Criar_Campo('GNRE010_Data_Hora_Envio', 'datetime DEFAULT NULL');
Criar_Campo('GNRE010_C007_Id_Solicitante_Envio', 'int DEFAULT NULL');
Criar_Campo('GNRE010_HTTP_Recepcao_Status', 'smallint unsigned DEFAULT NULL');
Criar_Campo('GNRE010_SOAP_Recepcao', 'mediumtext DEFAULT NULL');
Criar_Campo('GNRE010_Mensagem_Tecnica', 'text DEFAULT NULL');
Criar_Campo('GNRE010_Recibo', 'varchar(20) DEFAULT NULL');
Criar_Campo('GNRE010_Data_Hora_Recibo', 'varchar(40) DEFAULT NULL');
Criar_Campo('GNRE010_Proxima_Tentativa_Consulta', 'datetime DEFAULT NULL');
Criar_Campo('GNRE010_Consulta_Lock_Token', 'char(64) DEFAULT NULL');
Criar_Campo('GNRE010_Consulta_Lock_Ate', 'datetime DEFAULT NULL');
Criar_Campo('GNRE010_Ultima_Consulta_SOAP', 'mediumtext DEFAULT NULL');
Criar_Campo('GNRE010_Data_Hora_Ultima_Consulta', 'datetime DEFAULT NULL');
Criar_Indice('GNRE010_C004_Id,,GNRE010_Chave_Idempotencia', 'UNIQUE');

Criar_Campo('GNRE011_Ordinal_Envio', 'smallint unsigned DEFAULT NULL');
Criar_Campo('GNRE011_Situacao', "enum('PREPARADO','PENDENTE','FINALIZADO','INCONCLUSIVO','REJEITADO') NOT NULL DEFAULT 'PREPARADO'");
Criar_Campo('GNRE011_Situacao_Guia', 'char(1) DEFAULT NULL');
Criar_Campo('GNRE011_Resultado_Individual', 'mediumtext DEFAULT NULL');
Criar_Campo('GNRE011_Motivos_Rejeicao', 'mediumtext DEFAULT NULL');
Criar_Campo('GNRE011_Data_Hora_Decisao', 'datetime DEFAULT NULL');
Criar_Indice('GNRE011_GNRE010_Id,,GNRE011_Situacao');
```

Não há backfill. Coortes existentes continuam `PREPARADO` ou `CANCELADO`; os campos novos ficam `NULL`. Nenhuma coorte exige ajuste manual para permanecer válida. O operador só deve executar o bloco antes da futura task que venha a usar campos de envio/consulta.

## Garantias preservadas

- Resultado ausente, parcial, ambíguo ou inconclusivo nunca libera retransmissão; `INCONCLUSIVO` continua sendo lote ativo.
- Não há associação por posição/ordinal e não há conversão de PDF coletivo em `GNRE008` individual.
- Homologação e Produção compartilham o schema; a separação vem sempre do ambiente imutável do snapshot, sem lote misto.
- Esta consolidação não habilita fluxo algum de Portal nem altera a interface da Fila sequencial.

## Validação

Foram previstos lint dos PHPs alterados, o teste sintético novo da Task 74 e as regressões 63, 65, 66, 67, 68 e 71, além de `git diff --check`.
