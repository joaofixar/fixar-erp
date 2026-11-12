<?php
namespace hardness;

/**
 * Traduz o retorno técnico da rejeição 225 da NF-e para uma orientação
 * compreensível, sem alterar o retorno original persistido pela fila.
 */
class NFeRejeicao225
{
    /**
     * @param string $retornoTecnico xMotivo/detalhe devolvido pela SEFAZ
     * @param string $xml             XML enviado, quando disponível
     * @return array
     */
    public static function interpretar($retornoTecnico, $xml = '')
    {
        $retornoTecnico = trim((string) $retornoTecnico);
        $catalogo = self::catalogo();
        $tag = self::extrairTag($retornoTecnico, array_keys($catalogo));
        // Alguns retornos SAX apontam a última tag processada, mas informam o
        // valor que realmente violou o Schema. Quando esse valor identifica
        // de forma inequívoca outro campo no XML, ele é uma evidência mais
        // precisa que a tag citada no fim da mensagem técnica.
        $tagPeloValor = self::extrairTagPeloValorXml($xml, $retornoTecnico, array_keys($catalogo));
        if ($tagPeloValor !== '') {
            $tag = $tagPeloValor;
        }
        if ($tag === '') {
            $tag = self::extrairTagPelaPosicaoXml($xml, $retornoTecnico, array_keys($catalogo));
        }
        $contexto = self::localizarContextoXml($xml, $tag, $retornoTecnico);
        $grupo = isset($contexto['grupo']) ? $contexto['grupo'] : '';

        $dados = isset($catalogo[$tag]) ? $catalogo[$tag] : array(
            'titulo' => $tag !== ''
                ? "Campo {$tag} fora do formato esperado"
                : 'Não foi possível identificar automaticamente o campo',
            'onde' => 'Dados da NF-e e cadastros utilizados na emissão',
            'orientacao' => 'Revise os dados informados, gere novamente o XML e tente retransmitir a nota.',
        );

        $dados = self::aplicarContexto($tag, $grupo, $dados);
        $item = isset($contexto['item']) ? $contexto['item'] : '';
        $resumo = $dados['titulo'];
        if ($item !== '') {
            $resumo .= ' — item ' . $item;
        }

        return array(
            'titulo' => $dados['titulo'],
            'resumo' => $resumo,
            'tag' => $tag,
            'grupo' => $grupo,
            'caminho' => isset($contexto['caminho']) ? $contexto['caminho'] : '',
            'item' => $item,
            'valorInvalido' => self::extrairValorInvalido($retornoTecnico, $tag),
            'ondeVerificar' => $dados['onde'],
            'orientacao' => $dados['orientacao'],
            'retornoTecnico' => $retornoTecnico,
        );
    }

    private static function catalogo()
    {
        return array(
            'dhEmi' => array(
                'titulo' => 'Data e hora de emissão fora do formato esperado',
                'onde' => 'Data de emissão da NF-e',
                'orientacao' => 'Informe data, hora, segundos e fuso, por exemplo 2026-07-16T16:00:00-03:00.',
            ),
            'dhSaiEnt' => array(
                'titulo' => 'Data e hora de saída/entrada fora do formato esperado',
                'onde' => 'Data de saída ou entrada da NF-e',
                'orientacao' => 'Informe a data e a hora completas com o fuso. Se não houver essa informação, não gere a tag vazia.',
            ),
            'tpImp' => array(
                'titulo' => 'Formato de impressão do DANFE inválido',
                'onde' => 'Modelo e formato de impressão da NF-e',
                'orientacao' => 'Confira o formato do DANFE. No leiaute 4.00, utilize um código permitido pelo Schema, de 0 a 5.',
            ),
            'finNFe' => array(
                'titulo' => 'Finalidade da NF-e inválida',
                'onde' => 'Finalidade da nota fiscal',
                'orientacao' => 'Confira se a nota é normal, complementar, de ajuste, devolução/retorno ou outra finalidade prevista no Schema vigente.',
            ),
            'refNFe' => array(
                'titulo' => 'Chave da NF-e referenciada inválida',
                'onde' => 'Notas fiscais referenciadas',
                'orientacao' => 'Confira a chave de acesso referenciada, seu tamanho e os caracteres permitidos pelo Schema vigente.',
            ),
            'refCTe' => array(
                'titulo' => 'Chave do CT-e referenciado inválida',
                'onde' => 'Documentos fiscais referenciados',
                'orientacao' => 'Confira a chave do CT-e e remova espaços ou pontuação.',
            ),
            'nECF' => array(
                'titulo' => 'Número do ECF referenciado inválido',
                'onde' => 'Cupom fiscal referenciado',
                'orientacao' => 'Confira o número do ECF e a quantidade de posições permitida.',
            ),
            'nCOO' => array(
                'titulo' => 'Número do COO referenciado inválido',
                'onde' => 'Cupom fiscal referenciado',
                'orientacao' => 'Confira o número do contador do cupom fiscal e remova formatação indevida.',
            ),
            'indIntermed' => array(
                'titulo' => 'Intermediador da transação não informado ou inválido',
                'onde' => 'Vendas → Notas Fiscais → Cabeçalho → Intermediador Transação',
                'orientacao' => 'Não deixe o campo em branco. Selecione 0 - Operação sem intermediador ou 1 - Operação em site ou plataforma de terceiros, conforme a venda. Depois gere novamente o XML.',
            ),
            'CNPJ' => array(
                'titulo' => 'CNPJ fora do formato esperado',
                'onde' => 'Cadastro relacionado ao grupo indicado no XML',
                'orientacao' => 'Confira o CNPJ sem máscara, espaços ou valor vazio, conforme o Schema vigente.',
            ),
            'CPF' => array(
                'titulo' => 'CPF fora do formato esperado',
                'onde' => 'Cadastro relacionado ao grupo indicado no XML',
                'orientacao' => 'Informe 11 algarismos, sem pontos, espaços ou traço, e confira os dígitos verificadores.',
            ),
            'IE' => array(
                'titulo' => 'Inscrição Estadual fora do formato esperado',
                'onde' => 'Cadastro fiscal do emitente, destinatário ou transportador',
                'orientacao' => 'Confira a IE e a UF, remova pontos e traços e use ISENTO somente quando permitido.',
            ),
            'cMun' => array(
                'titulo' => 'Código IBGE do município inválido',
                'onde' => 'Município do endereço indicado no XML',
                'orientacao' => 'Confira o código IBGE do município e se ele pertence à UF informada.',
            ),
            'CEP' => array(
                'titulo' => 'CEP fora do formato esperado',
                'onde' => 'Endereço indicado no XML',
                'orientacao' => 'Informe 8 algarismos, sem hífen ou espaços.',
            ),
            'nro' => array(
                'titulo' => 'Número do endereço não informado ou inválido',
                'onde' => 'Endereço indicado no XML',
                'orientacao' => 'Preencha o número do endereço. Quando não houver número, utilize o padrão cadastral adotado pelo sistema, como S/N ou 0.',
            ),
            'IM' => array(
                'titulo' => 'Inscrição Municipal fora do formato esperado',
                'onde' => 'Cadastro municipal do emitente',
                'orientacao' => 'Confira a Inscrição Municipal. Quando houver CNAE no XML, a IM deve ser gerada antes dele.',
            ),
            'CNAE' => array(
                'titulo' => 'CNAE fora do formato esperado',
                'onde' => 'Cadastro fiscal do emitente',
                'orientacao' => 'Confira os 7 algarismos do CNAE e a existência da Inscrição Municipal no XML.',
            ),
            'NCM' => array(
                'titulo' => 'NCM fora do formato esperado',
                'onde' => 'Fiscal → NCM e Cadastros → Produtos → dados fiscais do produto',
                'orientacao' => 'Localize o produto do item indicado, confira o NCM vinculado e informe 8 algarismos, sem pontos ou espaços. O valor 00 somente se aplica aos casos específicos previstos pela NF-e. Depois gere novamente o XML.',
            ),
            'CEST' => array(
                'titulo' => 'CEST fora do formato esperado',
                'onde' => 'Fiscal → NCM e Cadastros → Produtos → dados fiscais do produto',
                'orientacao' => 'Confira o CEST vinculado ao NCM/produto e informe 7 algarismos, sem pontos, traços ou espaços. Depois gere novamente o XML.',
            ),
            'CFOP' => array(
                'titulo' => 'CFOP fora do formato esperado',
                'onde' => 'Fiscal → CFOP e CFOP selecionado no item da nota fiscal',
                'orientacao' => 'Confira o cadastro do CFOP usado na operação e informe um código válido com 4 algarismos. Depois gere novamente o XML.',
            ),
            'cEAN' => array(
                'titulo' => 'Código de barras comercial fora do formato esperado',
                'onde' => 'Cadastros → Produtos → código de barras',
                'orientacao' => 'Confira o GTIN e seu dígito verificador. Quando aplicável, utilize SEM GTIN.',
            ),
            'cEANTrib' => array(
                'titulo' => 'Código de barras tributável fora do formato esperado',
                'onde' => 'Cadastros → Produtos → código de barras tributável',
                'orientacao' => 'Confira o GTIN tributável e seu dígito verificador. Quando aplicável, utilize SEM GTIN.',
            ),
            'uCom' => array(
                'titulo' => 'Unidade comercial fora do formato esperado',
                'onde' => 'Cadastros → Produtos → unidade comercial',
                'orientacao' => 'Utilize texto simples e respeite o limite do Schema, por exemplo UN ou M3.',
            ),
            'uTrib' => array(
                'titulo' => 'Unidade tributável fora do formato esperado',
                'onde' => 'Cadastros → Produtos → unidade tributável',
                'orientacao' => 'Utilize texto simples e respeite o limite do Schema, por exemplo UN ou M3.',
            ),
            'qCom' => array(
                'titulo' => 'Quantidade comercial fora do formato esperado',
                'onde' => 'Quantidade do item',
                'orientacao' => 'Use ponto como separador decimal, sem separador de milhar, e respeite as casas decimais permitidas.',
            ),
            'vUnCom' => array(
                'titulo' => 'Valor unitário comercial fora do formato esperado',
                'onde' => 'Valor unitário do item',
                'orientacao' => 'Use ponto como separador decimal, sem separador de milhar, e respeite as casas decimais permitidas.',
            ),
            'pICMS' => array(
                'titulo' => 'Alíquota de ICMS fora do formato esperado',
                'onde' => 'Fiscal → CFOP → tributação de ICMS e dados fiscais do item',
                'orientacao' => 'Use ponto como separador decimal e confira a quantidade de casas permitida.',
            ),
            'vICMS' => array(
                'titulo' => 'Valor de ICMS fora do formato esperado',
                'onde' => 'Fiscal → CFOP → tributação de ICMS e dados fiscais do item',
                'orientacao' => 'Use ponto como separador decimal e confira a quantidade de casas permitida.',
            ),
            'CST' => array(
                'titulo' => 'CST inválido ou no grupo tributário incorreto',
                'onde' => 'Fiscal → CFOP → tributação do item',
                'orientacao' => 'Confira o CST, o imposto indicado pelo grupo XML e o regime tributário da empresa.',
            ),
            'CSOSN' => array(
                'titulo' => 'CSOSN inválido ou no grupo de ICMS incorreto',
                'onde' => 'Fiscal → CFOP → tributação de ICMS do item',
                'orientacao' => 'Confira o CSOSN e o regime tributário da empresa.',
            ),
            'IPI' => array(
                'titulo' => 'Estrutura do IPI inválida',
                'onde' => 'Tributação de IPI do item',
                'orientacao' => 'Confira o enquadramento e se foi gerado somente o grupo tributado ou não tributado permitido.',
            ),
            'IPITrib' => array(
                'titulo' => 'Grupo tributado do IPI inválido',
                'onde' => 'Tributação de IPI do item',
                'orientacao' => 'Confira o CST, a alíquota, os valores e a ordem das tags do IPI tributado.',
            ),
            'IPINT' => array(
                'titulo' => 'Grupo não tributado do IPI inválido',
                'onde' => 'Tributação de IPI do item',
                'orientacao' => 'Confira o CST e as tags permitidas para o IPI não tributado.',
            ),
            'cEnq' => array(
                'titulo' => 'Enquadramento legal do IPI inválido',
                'onde' => 'Tributação de IPI do item',
                'orientacao' => 'Confira o código de enquadramento legal do IPI.',
            ),
            'modFrete' => array(
                'titulo' => 'Modalidade do frete inválida',
                'onde' => 'Vendas → Notas Fiscais → transporte/frete',
                'orientacao' => 'Utilize somente 0, 1, 2, 3, 4 ou 9.',
            ),
            'placa' => array(
                'titulo' => 'Placa do veículo fora do formato esperado',
                'onde' => 'Cadastros → Transportadoras e transporte da nota fiscal',
                'orientacao' => 'Informe a placa sem hífen ou espaços e confira o padrão antigo ou Mercosul.',
            ),
            'infCpl' => array(
                'titulo' => 'Informações complementares fora do formato esperado',
                'onde' => 'Vendas → Notas Fiscais → informações complementares',
                'orientacao' => 'Revise o tamanho, quebras de linha e caracteres de controle. Os caracteres &, < e > devem ser escapados corretamente pelo gerador XML.',
            ),
            'UFSaidaPais' => array(
                'titulo' => 'UF de saída do país inválida',
                'onde' => 'Dados de exportação',
                'orientacao' => 'Confira a sigla da UF de embarque ou de transposição de fronteira.',
            ),
            'xLocExporta' => array(
                'titulo' => 'Local de exportação fora do formato esperado',
                'onde' => 'Dados de exportação',
                'orientacao' => 'Preencha o local de exportação e respeite o limite de tamanho do Schema.',
            ),
            'xLocDespacho' => array(
                'titulo' => 'Local de despacho fora do formato esperado',
                'onde' => 'Dados de exportação',
                'orientacao' => 'Revise o local de despacho. Se não houver informação, não gere a tag vazia.',
            ),
            'cExportador' => array(
                'titulo' => 'Código do exportador fora do formato esperado',
                'onde' => 'Detalhes de exportação do item',
                'orientacao' => 'Confira o código do exportador e respeite o limite de tamanho do Schema.',
            ),
            'xNEmp' => array(
                'titulo' => 'Nota de empenho fora do formato esperado',
                'onde' => 'Informações de compra da NF-e',
                'orientacao' => 'Confira a nota de empenho e respeite o limite de 22 caracteres.',
            ),
            'xPed' => array(
                'titulo' => 'Número do pedido fora do formato esperado',
                'onde' => 'Pedido de compra da NF-e ou do item',
                'orientacao' => 'Confira o pedido, remova espaços nas extremidades e respeite o limite do grupo XML.',
            ),
            'nItemPed' => array(
                'titulo' => 'Número do item do pedido inválido',
                'onde' => 'Pedido de compra do item da NF-e',
                'orientacao' => 'Informe de 1 a 6 algarismos, sem símbolos ou espaços.',
            ),
            'chassi' => array(
                'titulo' => 'Chassi fora do formato esperado',
                'onde' => 'Detalhes específicos do veículo',
                'orientacao' => 'Confira os 17 caracteres do chassi, sem espaços ou símbolos indevidos.',
            ),
            'nLote' => array(
                'titulo' => 'Número do lote fora do formato esperado',
                'onde' => 'Rastreabilidade do produto ou medicamento',
                'orientacao' => 'Não envie a tag vazia; remova espaços nas extremidades e respeite o limite do Schema.',
            ),
            'vPMC' => array(
                'titulo' => 'Preço Máximo ao Consumidor fora do formato esperado',
                'onde' => 'Detalhes do medicamento',
                'orientacao' => 'Use ponto como separador decimal e no máximo duas casas decimais.',
            ),
            'nSerie' => array(
                'titulo' => 'Número de série fora do formato esperado',
                'onde' => 'Detalhamento específico do produto',
                'orientacao' => 'Confira o número de série e o limite de tamanho do grupo XML.',
            ),
            'cProdANP' => array(
                'titulo' => 'Código de produto da ANP fora do formato esperado',
                'onde' => 'Detalhes do combustível',
                'orientacao' => 'Informe 9 algarismos e confira o código na tabela vigente da ANP.',
            ),
            'nDup' => array(
                'titulo' => 'Número da duplicata fora do formato esperado',
                'onde' => 'Parcelas e duplicatas da NF-e',
                'orientacao' => 'Remova espaços nas extremidades e respeite o limite do Schema.',
            ),
            'dVenc' => array(
                'titulo' => 'Data de vencimento da duplicata inválida',
                'onde' => 'Parcelas e duplicatas da NF-e',
                'orientacao' => 'Informe uma data real no formato AAAA-MM-DD.',
            ),
            'vDup' => array(
                'titulo' => 'Valor da duplicata fora do formato esperado',
                'onde' => 'Parcelas e duplicatas da NF-e',
                'orientacao' => 'Use ponto como separador decimal, sem separador de milhar e com no máximo duas casas decimais.',
            ),
            'tPag' => array(
                'titulo' => 'Forma de pagamento inválida',
                'onde' => 'Detalhamento do pagamento da NF-e',
                'orientacao' => 'Confira o código de dois algarismos na tabela de formas de pagamento vigente.',
            ),
            'tpIntegra' => array(
                'titulo' => 'Tipo de integração do pagamento inválido',
                'onde' => 'Dados do cartão ou pagamento eletrônico',
                'orientacao' => 'Utilize 1 para pagamento integrado ou 2 para não integrado.',
            ),
            'xNome' => array(
                'titulo' => 'Nome ou razão social fora do formato esperado',
                'onde' => 'Cadastro indicado pelo grupo XML',
                'orientacao' => 'Revise o nome, remova espaços nas extremidades e respeite o limite de tamanho.',
            ),
            'xBairro' => array(
                'titulo' => 'Bairro não informado ou fora do formato esperado',
                'onde' => 'Endereço indicado pelo grupo XML',
                'orientacao' => 'Preencha o bairro e respeite o tamanho permitido pelo Schema.',
            ),
            'cPais' => array(
                'titulo' => 'Código do país fora do formato esperado',
                'onde' => 'País do endereço indicado no XML',
                'orientacao' => 'Confira o código oficial do país. Para o Brasil, normalmente é utilizado 1058.',
            ),
            'idCadIntTran' => array(
                'titulo' => 'Identificador do intermediador fora do formato esperado',
                'onde' => 'Cadastro do marketplace, site ou intermediador',
                'orientacao' => 'Confira o identificador cadastrado na plataforma, sem espaços nas extremidades, e respeite o limite do Schema.',
            ),
        );
    }

    private static function extrairTag($retornoTecnico, $tagsConhecidas)
    {
        $encontradas = array();
        foreach ($tagsConhecidas as $tag) {
            $padrao = '/(?<![A-Za-z0-9_])' . preg_quote($tag, '/') . '(?![A-Za-z0-9_])/i';
            if (preg_match($padrao, $retornoTecnico, $match, PREG_OFFSET_CAPTURE)) {
                $encontradas[] = array('tag' => $tag, 'posicao' => $match[0][1]);
            }
        }

        if (!empty($encontradas)) {
            usort($encontradas, function ($a, $b) {
                return $a['posicao'] - $b['posicao'];
            });
            return $encontradas[0]['tag'];
        }

        // Alguns validadores da SEFAZ não devolvem a tag isolada. Exemplo real:
        // #AnonType_NCMproddetinfNFeTNFe. Nesse formato, o nome da tag aparece
        // como prefixo do tipo anônimo concatenado ao caminho do Schema.
        if (preg_match_all('/#AnonType_([A-Za-z0-9_]+)/i', $retornoTecnico, $tiposAnonimos)) {
            usort($tagsConhecidas, function ($a, $b) {
                return strlen($b) - strlen($a);
            });
            foreach ($tiposAnonimos[1] as $tipoAnonimo) {
                foreach ($tagsConhecidas as $tag) {
                    if (stripos($tipoAnonimo, $tag) === 0) {
                        return $tag;
                    }
                }
            }
        }

        $padroes = array(
            '/Element\s+[\'\"]\{[^}]+\}([A-Za-z_][A-Za-z0-9_]*)[\'\"]/i',
            '/(?:Element|Elemento)\s+[\'\"](?:[^:\'\"]+:)?([A-Za-z_][A-Za-z0-9_]*)[\'\"]/i',
            '/<([A-Za-z_][A-Za-z0-9_]*)>/i',
        );
        foreach ($padroes as $padrao) {
            if (preg_match($padrao, $retornoTecnico, $match)) {
                return $match[1];
            }
        }

        return '';
    }

    /**
     * Quando a SEFAZ omite o nome da tag, usa a linha e a coluna informadas
     * pelo SAX para localizar o elemento conhecido mais próximo no XML enviado.
     * Sem posição confiável, só identifica quando existe uma única tag
     * conhecida vazia; assim uma enumeração genérica não vira indIntermed por
     * suposição.
     */
    private static function extrairTagPelaPosicaoXml($xml, $retornoTecnico, $tagsConhecidas)
    {
        $xml = (string) $xml;
        if (trim($xml) === '') {
            return '';
        }

        $mapaTags = array();
        foreach ($tagsConhecidas as $tagConhecida) {
            $mapaTags[strtolower($tagConhecida)] = $tagConhecida;
        }

        // Para erro de valor vazio, uma única tag conhecida vazia no XML é
        // evidência mais forte que a coluna do SAX. O validador pode comunicar
        // o erro somente ao fechar o grupo pai, algumas tags depois do campo.
        if (strpos($retornoTecnico, "Value ''") !== false || strpos($retornoTecnico, 'Value ""') !== false) {
            $tagsVazias = array();
            foreach ($tagsConhecidas as $tagConhecida) {
                $tagEscapada = preg_quote($tagConhecida, '/');
                $prefixo = '(?:[A-Za-z_][A-Za-z0-9_.-]*:)?';
                $padraoVazia = '/<' . $prefixo . $tagEscapada . '\b[^>]*(?:\/\s*>|>\s*<\/' . $prefixo . $tagEscapada . '\s*>)/i';
                if (preg_match($padraoVazia, $xml)) {
                    $tagsVazias[] = $tagConhecida;
                }
            }
            if (count($tagsVazias) === 1) {
                return $tagsVazias[0];
            }
        }

        if (preg_match('/lineNumber:\s*(\d+)\s*;\s*columnNumber:\s*(\d+)/i', $retornoTecnico, $posicao)) {
            $linhas = preg_split('/\r\n|\r|\n/', $xml);
            $numeroLinha = (int) $posicao[1];
            $numeroColuna = max(1, (int) $posicao[2]);
            if (isset($linhas[$numeroLinha - 1])) {
                $linha = $linhas[$numeroLinha - 1];
                $posicoesByte = array($numeroColuna - 1);
                if (function_exists('mb_substr')) {
                    $prefixo = mb_substr($linha, 0, $numeroColuna - 1, 'UTF-8');
                    $posicoesByte[] = strlen($prefixo);
                }

                $maisProximas = array();
                if (preg_match_all('/<\/?(?:[A-Za-z_][A-Za-z0-9_.-]*:)?([A-Za-z_][A-Za-z0-9_.-]*)\b[^>]*>/i', $linha, $elementos, PREG_OFFSET_CAPTURE)) {
                    foreach ($elementos[1] as $indice => $elemento) {
                        $nomeNormalizado = strtolower($elemento[0]);
                        if (!isset($mapaTags[$nomeNormalizado])) {
                            continue;
                        }

                        $inicio = $elementos[0][$indice][1];
                        $fim = $inicio + strlen($elementos[0][$indice][0]);
                        $distancia = null;
                        foreach ($posicoesByte as $posicaoByte) {
                            if ($posicaoByte >= $inicio && $posicaoByte <= $fim) {
                                $distanciaAtual = 0;
                            } else {
                                $distanciaAtual = min(abs($posicaoByte - $inicio), abs($posicaoByte - $fim));
                            }
                            if ($distancia === null || $distanciaAtual < $distancia) {
                                $distancia = $distanciaAtual;
                            }
                        }

                        $tagConhecida = $mapaTags[$nomeNormalizado];
                        if (!isset($maisProximas[$tagConhecida]) || $distancia < $maisProximas[$tagConhecida]) {
                            $maisProximas[$tagConhecida] = $distancia;
                        }
                    }
                }

                if (!empty($maisProximas)) {
                    asort($maisProximas, SORT_NUMERIC);
                    $tagMaisProxima = key($maisProximas);
                    if (current($maisProximas) <= 120) {
                        return $tagMaisProxima;
                    }
                }
            }
        }

        return '';
    }

    /**
     * Expõe o valor rejeitado apenas para campos operacionais não sensíveis.
     * CPF, CNPJ e chaves fiscais continuam somente no detalhe técnico restrito.
     */
    private static function extrairValorInvalido($retornoTecnico, $tag)
    {
        $tagsPermitidas = array(
            'NCM', 'CEST', 'CFOP', 'cEAN', 'cEANTrib', 'uCom', 'uTrib',
            'qCom', 'vUnCom', 'pICMS', 'vICMS', 'CST', 'CSOSN', 'cEnq',
            'modFrete', 'placa', 'tPag', 'tpIntegra', 'tpImp', 'finNFe',
        );
        if (!in_array($tag, $tagsPermitidas, true)) {
            return '';
        }

        $padroes = array(
            '/\bValue\s+[\'"]([^\'"]*)[\'"]/i',
            '/\bThe value\s+[\'"]([^\'"]*)[\'"]/i',
            '/\bvalor\s+[\'"]([^\'"]*)[\'"]/i',
        );
        foreach ($padroes as $padrao) {
            if (preg_match($padrao, $retornoTecnico, $match)) {
                return trim($match[1]);
            }
        }

        return '';
    }

    /**
     * Localiza no XML a tag cujo conteúdo é o valor recusado pela SEFAZ.
     * Só retorna uma tag quando todos os nós encontrados têm o mesmo nome;
     * assim não troca o diagnóstico em caso de valor repetido em campos
     * diferentes.
     */
    private static function extrairTagPeloValorXml($xml, $retornoTecnico, $tagsConhecidas)
    {
        $xml = (string) $xml;
        if (trim($xml) === '') {
            return '';
        }

        $valor = '';
        $padroes = array(
            '/\bValue\s+[\'\"]([^\'\"]+)[\'\"]/i',
            '/\bThe value\s+[\'\"]([^\'\"]+)[\'\"]/i',
            '/\bvalor\s+[\'\"]([^\'\"]+)[\'\"]/i',
        );
        foreach ($padroes as $padrao) {
            if (preg_match($padrao, $retornoTecnico, $match)) {
                $valor = trim(html_entity_decode($match[1], ENT_QUOTES, 'UTF-8'));
                break;
            }
        }
        if ($valor === '') {
            return '';
        }

        $mapaTags = array();
        foreach ($tagsConhecidas as $tagConhecida) {
            $mapaTags[strtolower($tagConhecida)] = $tagConhecida;
        }
        $encontradas = array();

        if (class_exists('\\DOMDocument')) {
            $anterior = libxml_use_internal_errors(true);
            $dom = new \DOMDocument();
            $carregado = $dom->loadXML($xml, LIBXML_NONET | LIBXML_NOBLANKS);
            libxml_clear_errors();
            libxml_use_internal_errors($anterior);
            if ($carregado) {
                foreach ($mapaTags as $nomeNormalizado => $tagConhecida) {
                    $nos = $dom->getElementsByTagName($tagConhecida);
                    for ($i = 0; $i < $nos->length; $i++) {
                        if (trim($nos->item($i)->textContent) === $valor) {
                            $encontradas[$tagConhecida] = true;
                        }
                    }
                }
            }
        }

        // O ERP também roda em instalações sem a extensão DOM. Neste caso,
        // basta ler as tags conhecidas do XML já produzido para preservar a
        // mesma decisão do caminho principal.
        if (empty($encontradas)) {
            foreach ($tagsConhecidas as $tagConhecida) {
                $tagEscapada = preg_quote($tagConhecida, '/');
                $prefixo = '(?:[A-Za-z_][A-Za-z0-9_.-]*:)?';
                $padraoTag = '/<' . $prefixo . $tagEscapada . '\\b[^>]*>(.*?)<\\/' . $prefixo . $tagEscapada . '\\s*>/si';
                if (!preg_match_all($padraoTag, $xml, $ocorrencias)) {
                    continue;
                }
                foreach ($ocorrencias[1] as $conteudo) {
                    $valorXml = trim(html_entity_decode(strip_tags($conteudo), ENT_QUOTES, 'UTF-8'));
                    if ($valorXml === $valor) {
                        $encontradas[$tagConhecida] = true;
                    }
                }
            }
        }

        if (count($encontradas) === 1) {
            return key($encontradas);
        }

        return '';
    }

    private static function localizarContextoXml($xml, $tag, $retornoTecnico)
    {
        $contexto = array('grupo' => '', 'caminho' => '', 'item' => '');
        if ($tag === '' || trim((string) $xml) === '') {
            return $contexto;
        }

        if (!class_exists('\\DOMDocument')) {
            return self::localizarContextoTextoXml($xml, $tag, $retornoTecnico);
        }

        $anterior = libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $carregado = $dom->loadXML($xml, LIBXML_NONET | LIBXML_NOBLANKS);
        libxml_clear_errors();
        libxml_use_internal_errors($anterior);
        if (!$carregado) {
            return $contexto;
        }

        $nos = $dom->getElementsByTagName($tag);
        if ($nos->length === 0) {
            return $contexto;
        }

        $noEscolhido = $nos->item(0);
        for ($i = 0; $i < $nos->length; $i++) {
            $valor = trim($nos->item($i)->textContent);
            if ($valor !== '' && strpos($retornoTecnico, $valor) !== false) {
                $noEscolhido = $nos->item($i);
                break;
            }
        }

        $partes = array();
        $grupos = array('ide', 'emit', 'dest', 'enderEmit', 'enderDest', 'retirada', 'entrega', 'prod', 'ICMS', 'IPI', 'PIS', 'COFINS', 'transp', 'transporta', 'veicTransp', 'exporta', 'detExport', 'compra', 'rastro', 'med', 'arma', 'comb', 'cobr', 'dup', 'pag', 'detPag', 'card', 'infIntermed', 'infAdic', 'veicProd');
        $atual = $noEscolhido;
        while ($atual instanceof \DOMElement) {
            array_unshift($partes, $atual->localName);
            if ($contexto['grupo'] === '' && in_array($atual->localName, $grupos, true) && $atual->localName !== $tag) {
                $contexto['grupo'] = $atual->localName;
            }
            if ($atual->localName === 'det' && $atual->hasAttribute('nItem')) {
                $contexto['item'] = $atual->getAttribute('nItem');
            }
            $atual = $atual->parentNode;
        }
        $contexto['caminho'] = implode('/', $partes);

        return $contexto;
    }

    /**
     * Fallback para processos PHP sem a extensão DOM. O XML já foi gerado pelo
     * sistema; aqui ele é apenas lido, nunca alterado nem renderizado.
     */
    private static function localizarContextoTextoXml($xml, $tag, $retornoTecnico)
    {
        $contexto = array('grupo' => '', 'caminho' => '', 'item' => '');
        $tagEscapada = preg_quote($tag, '/');
        $prefixo = '(?:[A-Za-z_][A-Za-z0-9_.-]*:)?';
        $padraoTag = '/<' . $prefixo . $tagEscapada . '\\b[^>]*>(.*?)<\\/' . $prefixo . $tagEscapada . '\\s*>/si';
        if (!preg_match_all($padraoTag, $xml, $ocorrencias, PREG_OFFSET_CAPTURE)) {
            return $contexto;
        }

        $indiceEscolhido = 0;
        foreach ($ocorrencias[1] as $indice => $ocorrencia) {
            $valor = trim(html_entity_decode(strip_tags($ocorrencia[0]), ENT_QUOTES, 'UTF-8'));
            if ($valor !== '' && (
                strpos($retornoTecnico, "'{$valor}'") !== false
                || strpos($retornoTecnico, '"' . $valor . '"') !== false
                || strpos($retornoTecnico, $valor) !== false
            )) {
                $indiceEscolhido = $indice;
                break;
            }
        }

        $posicaoTag = $ocorrencias[0][$indiceEscolhido][1];
        $antesDaTag = substr($xml, 0, $posicaoTag);
        $grupos = array('ide', 'emit', 'dest', 'enderEmit', 'enderDest', 'retirada', 'entrega', 'prod', 'ICMS', 'IPI', 'PIS', 'COFINS', 'transp', 'transporta', 'veicTransp', 'exporta', 'detExport', 'compra', 'rastro', 'med', 'arma', 'comb', 'cobr', 'dup', 'pag', 'detPag', 'card', 'infIntermed', 'infAdic', 'veicProd');
        $grupoMaisProximo = array('nome' => '', 'posicao' => -1);
        foreach ($grupos as $grupo) {
            $abertura = self::ultimaPosicaoTagAbertura($antesDaTag, $grupo);
            $fechamento = self::ultimaPosicaoTagFechamento($antesDaTag, $grupo);
            if ($abertura !== false && ($fechamento === false || $abertura > $fechamento) && $abertura > $grupoMaisProximo['posicao']) {
                $grupoMaisProximo = array('nome' => $grupo, 'posicao' => $abertura);
            }
        }
        $contexto['grupo'] = $grupoMaisProximo['nome'];

        $posicaoDet = self::ultimaPosicaoTagAbertura($antesDaTag, 'det');
        $fechamentoDet = self::ultimaPosicaoTagFechamento($antesDaTag, 'det');
        if ($posicaoDet !== false && ($fechamentoDet === false || $posicaoDet > $fechamentoDet)) {
            $aberturaDet = substr($antesDaTag, $posicaoDet, strpos($antesDaTag, '>', $posicaoDet) - $posicaoDet + 1);
            if (preg_match('/\\bnItem=[\'\"]([^\'\"]+)[\'\"]/i', $aberturaDet, $item)) {
                $contexto['item'] = $item[1];
            }
        }

        $partes = array();
        if ($contexto['item'] !== '') {
            $partes[] = 'det[@nItem=' . $contexto['item'] . ']';
        }
        if ($contexto['grupo'] !== '') {
            $partes[] = $contexto['grupo'];
        }
        $partes[] = $tag;
        $contexto['caminho'] = implode('/', $partes);

        return $contexto;
    }

    private static function ultimaPosicaoTagAbertura($xml, $tag)
    {
        $padrao = '/<(?:[A-Za-z_][A-Za-z0-9_.-]*:)?' . preg_quote($tag, '/') . '\\b[^>]*>/i';
        if (!preg_match_all($padrao, $xml, $matches, PREG_OFFSET_CAPTURE)) {
            return false;
        }
        $ultima = end($matches[0]);
        return $ultima[1];
    }

    private static function ultimaPosicaoTagFechamento($xml, $tag)
    {
        $padrao = '/<\\/(?:[A-Za-z_][A-Za-z0-9_.-]*:)?' . preg_quote($tag, '/') . '\\s*>/i';
        if (!preg_match_all($padrao, $xml, $matches, PREG_OFFSET_CAPTURE)) {
            return false;
        }
        $ultima = end($matches[0]);
        return $ultima[1];
    }

    private static function aplicarContexto($tag, $grupo, $dados)
    {
        if ($tag === 'xNome') {
            if ($grupo === 'emit') {
                $dados['titulo'] = 'Razão social do emitente fora do formato esperado';
                $dados['onde'] = 'Configuração → dados cadastrais da empresa emitente';
                $dados['orientacao'] = 'Revise a razão social e respeite o limite de 60 caracteres permitido pelo Schema.';
            } elseif ($grupo === 'dest') {
                $dados['titulo'] = 'Nome ou razão social do destinatário fora do formato esperado';
                $dados['onde'] = 'Cadastros → Cliente/Fornecedor → dados cadastrais';
                $dados['orientacao'] = 'Revise o nome ou a razão social do cliente e respeite o limite de 60 caracteres permitido pelo Schema.';
            }
        }

        if ($tag === 'CNPJ') {
            $contextos = array(
                'emit' => array('CNPJ do emitente fora do formato esperado', 'Configuração → dados fiscais da empresa emitente'),
                'dest' => array('CNPJ do destinatário fora do formato esperado', 'Cadastros → Cliente/Fornecedor → dados fiscais'),
                'transporta' => array('CNPJ do transportador fora do formato esperado', 'Cadastros → Transportadoras'),
                'card' => array('CNPJ da instituição de pagamento fora do formato esperado', 'Dados da credenciadora ou instituição de pagamento'),
                'infIntermed' => array('CNPJ do intermediador fora do formato esperado', 'Cadastro do marketplace, site ou intermediador'),
            );
            if (isset($contextos[$grupo])) {
                $dados['titulo'] = $contextos[$grupo][0];
                $dados['onde'] = $contextos[$grupo][1];
            }
        }

        if ($tag === 'CPF' && $grupo === 'dest') {
            $dados['titulo'] = 'CPF do destinatário fora do formato esperado';
            $dados['onde'] = 'Cadastros → Cliente/Fornecedor → dados fiscais';
        }

        if ($tag === 'IE') {
            if ($grupo === 'emit') {
                $dados['onde'] = 'Configuração → dados fiscais da empresa emitente';
            } elseif ($grupo === 'dest') {
                $dados['onde'] = 'Cadastros → Cliente/Fornecedor → dados fiscais';
            } elseif ($grupo === 'transporta') {
                $dados['onde'] = 'Cadastros → Transportadoras';
            }
        }

        if ($tag === 'CST' && in_array($grupo, array('ICMS', 'IPI', 'PIS', 'COFINS'), true)) {
            $dados['titulo'] = "CST de {$grupo} inválido ou no grupo tributário incorreto";
            $dados['onde'] = "Fiscal → CFOP → tributação de {$grupo} do item";
        }

        if ($tag === 'xPed') {
            if ($grupo === 'prod') {
                $dados['onde'] = 'Pedido de compra do item';
                $dados['orientacao'] = 'Confira o número do pedido do item e respeite o limite de 15 caracteres do Schema local.';
            } elseif ($grupo === 'compra') {
                $dados['onde'] = 'Informações gerais de compra da NF-e';
                $dados['orientacao'] = 'Confira o número do pedido e respeite o limite de 60 caracteres do Schema local.';
            }
        }

        if ($tag === 'nSerie') {
            if ($grupo === 'arma') {
                $dados['titulo'] = 'Número de série da arma fora do formato esperado';
                $dados['onde'] = 'Detalhes específicos da arma';
                $dados['orientacao'] = 'Confira o número de série e respeite o limite de 15 caracteres.';
            } elseif ($grupo === 'veicProd') {
                $dados['titulo'] = 'Número de série do veículo fora do formato esperado';
                $dados['onde'] = 'Detalhes específicos do veículo';
                $dados['orientacao'] = 'Confira o número de série e respeite o limite de 9 caracteres.';
            }
        }

        if (in_array($grupo, array('retirada', 'entrega'), true) && in_array($tag, array('xNome', 'xBairro', 'cMun', 'CEP', 'cPais', 'nro'), true)) {
            $local = $grupo === 'retirada' ? 'retirada' : 'entrega';
            $dados['onde'] = 'Vendas → Notas Fiscais → endereço do local de ' . $local;
        }

        $camposEndereco = array('xNome', 'xBairro', 'cMun', 'CEP', 'cPais', 'nro');
        if (in_array($tag, $camposEndereco, true)) {
            if (in_array($grupo, array('emit', 'enderEmit'), true)) {
                $dados['onde'] = 'Configuração → dados cadastrais da empresa emitente';
            } elseif (in_array($grupo, array('dest', 'enderDest'), true)) {
                $dados['onde'] = 'Cadastros → Cliente/Fornecedor → endereço';
            } elseif (in_array($grupo, array('transporta', 'veicTransp'), true)) {
                $dados['onde'] = 'Cadastros → Transportadoras';
            }
        }

        if (in_array($tag, array('dhEmi', 'dhSaiEnt', 'tpImp', 'finNFe', 'refNFe', 'refCTe', 'nECF', 'nCOO'), true)) {
            $dados['onde'] = 'Vendas → Notas Fiscais → dados gerais e documentos referenciados';
        }

        if (in_array($tag, array('nDup', 'dVenc', 'vDup'), true)) {
            $dados['onde'] = 'Vendas → Notas Fiscais → cobrança/duplicatas';
        }

        if (in_array($tag, array('tPag', 'tpIntegra'), true) || $grupo === 'card') {
            $dados['onde'] = 'Vendas → Notas Fiscais → pagamento/cartão';
        }

        if (in_array($tag, array('IPI', 'IPITrib', 'IPINT', 'cEnq'), true)) {
            $dados['onde'] = 'Fiscal → CFOP → tributação de IPI e Fiscal → NCM';
        }

        if (in_array($tag, array('UFSaidaPais', 'xLocExporta', 'xLocDespacho', 'cExportador'), true)) {
            $dados['onde'] = 'Vendas → Notas Fiscais → dados de exportação';
        }

        if (in_array($tag, array('chassi', 'nLote', 'vPMC', 'nSerie', 'cProdANP'), true)) {
            $dados['onde'] = 'Cadastros → Produtos → dados fiscais/específicos e item da nota fiscal';
        }

        return $dados;
    }
}
