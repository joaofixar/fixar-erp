# Task 18 — interface progressiva por snapshot oficial

Implementada e validada em 02/11/2026. A criação começa por UF e consulta apenas
`GNRE007` local; a consulta ao Portal permanece uma ação explícita. A guia criada
persiste o snapshot escolhido em `GNRE001_GNRE007_Id` e o tipo oficial.

A edição resolve obrigatoriamente esse vínculo imutável. O formulário de item é
construído com `GnreFormDefinitionBuilder`: estados `N`, `S`, `O` e `unavailable`,
grupos, rótulos, listas e restrições de tamanho vêm da definição. Tipos 0, 1 e 2
são apresentados por nome de negócio. No tipo 0, o documento é informado no item
e persistido internamente em `GNRE002`, sem painel técnico de documentos.

Valores usam nomes de negócio (`Valor principal`, `Valor total` e FCP) e são
persistidos em `GNRE006`; a validação fiscal/XML final continua na Task 14.
Configuração incompatível bloqueia a gravação e dados existentes não são apagados
automaticamente. Guias sem vínculo ficam em consulta segura, sem seleção automática.

## Fixture e validação

`tests/fixtures/task18-pr-snapshot-sanitized.xml` veio exclusivamente de
`GNRE007_Id=1` local (PR/homologação), sem Portal. Origem, data e SHA-256 estão
registrados no arquivo `.md` ao lado. O teste `task18-progressive-interface-synthetic.php`
cobre tipo 0, receitas 100102/100110, extra 66, lista obrigatória ausente e uso do
snapshot vinculado sem sessão/Portal.

Limitação conhecida: a verificação visual manual no navegador ainda deve confirmar
o layout responsivo; nenhuma transmissão, fila, NF-e, pagamento ou schema foi alterado.

## Lacuna documentada — emitente favorecido

O XSD e o gerador atual serializam `contribuinteEmitente` a partir do cadastro da
empresa (`C004` e endereço), com CNPJ, razão social e endereço quando a configuração
exige. A definição também conhece a opção de inscrição na UF favorecida/IE, mas não
há coluna em `GNRE001` nem serialização correspondente no XML atual. Portanto a
interface mostra apenas a prévia efetivamente usada no XML e pendências do cadastro;
ela não expõe campos sem persistência. Caso a UF passe a exigir esses dados no XML,
a proposta mínima é validar primeiro o mapeamento oficial e a serialização; qualquer
persistência por guia depende de aprovação explícita e de uma task de modelo, sem
inventar migration nesta task.
