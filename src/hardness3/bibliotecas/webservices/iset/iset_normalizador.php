<?php
namespace hardness;

require_once __DIR__ . '/iset_sku.php';

/** Converte exclusivamente o contrato externo da ISET no contrato da integração. */
class iset_normalizador_
{
    private function valor($dados, $caminho, $padrao = '')
    {
        foreach (explode('.', $caminho) as $chave) {
            if (!is_array($dados) || !array_key_exists($chave, $dados)) {
                return $padrao;
            }
            $dados = $dados[$chave];
        }
        return $dados === null ? $padrao : $dados;
    }

    public function normalizar(array $original)
    {
        $order = isset($original['order']) && is_array($original['order']) ? $original['order'] : array();
        $totais = isset($order['totals']) && is_array($order['totals']) ? $order['totals'] : array();
        $desconto = 0;
        $percentualDescontoOperacoes = 0;
        $produtos = 0;
        foreach ($totais as $total) {
            if (!isset($total['code'], $total['value'])) {
                continue;
            }
            if ($total['code'] === 'ot_discount') {
                $desconto += abs((float) $total['value']);
            }
            if ($total['code'] === 'ot_subtotal') {
                $produtos += (float) $total['value'];
            }

            // A ISET registra descontos percentuais de operações separadamente
            // do desconto em valor. A totalização do pedido Hardness precisa do
            // percentual para distribuir o desconto entre os itens T006.
            if (isset($total['action']) && $total['action'] === 'rem_p') {
                $percentualDescontoOperacoes += abs((float) $total['value']);
            }
        }
        if ($percentualDescontoOperacoes > 0 && $produtos > 0) {
            $desconto += round($produtos * ($percentualDescontoOperacoes / 100), 2);
        }

        $percentualDesconto = $produtos > 0 ? ($desconto / $produtos) * 100 : 0;
        $tipoTransacao = $this->valor($order, 'payment.transaction.paymentType');
        if (trim((string) $tipoTransacao) === '') {
            // Alguns provedores retornam paymentType; o PagHiper PIX retorna
            // type. Ambos representam a mesma informação para o D027.
            $tipoTransacao = $this->valor($order, 'payment.transaction.type');
        }

        $itens = array();
        foreach ((array) $this->valor($order, 'products', array()) as $item) {
            if (!is_array($item)) {
                continue;
            }
            $itens[] = array(
                'id_externo' => $this->valor($item, 'id'),
                'codigo_referencia' => (new ISET_SKU())->paraHardness($this->valor($item, 'cod_ref')),
                'nome' => $this->valor($item, 'name'),
                'quantidade' => $this->valor($item, 'quantity', 0),
                'valor_unitario' => $this->valor($item, 'price_unit', 0),
                'valor_total' => $this->valor($item, 'price_final', 0),
                'raw' => $item,
            );
        }
        return array(
            'pedido' => array(
                'id_externo'   => $this->valor($original, 'orderId'),
                'status_id'    => $this->valor($order, 'statusId'),
                'status'       => $this->valor($order, 'statusDescription'),
                'data_compra'  => $this->valor($order, 'datePurchased'),
                'observacao'   => $this->valor($order, 'note'),
            ),
            'cliente' => array(
                'nome'      => $this->valor($order, 'customer.name'),
                'tipo'      => $this->valor($order, 'customer.type'),
                'documento' => $this->valor($order, 'customer.cpf_cnpj'),
                'email'     => $this->valor($order, 'customer.email'),
                'telefone'  => trim($this->valor($order, 'customer.phones.primary.ddd') . ' ' . $this->valor($order, 'customer.phones.primary.number')),
                'celular'   => trim($this->valor($order, 'customer.phones.cellphone.ddd') . ' ' . $this->valor($order, 'customer.phones.cellphone.number')),
            ),
            'endereco' => array(
                'nome'        => $this->valor($order, 'shipping.address.name'),
                'rua'         => $this->valor($order, 'shipping.address.street_address'),
                'numero'      => $this->valor($order, 'shipping.address.street_number'),
                'complemento' => $this->valor($order, 'shipping.address.street_complemento'),
                'bairro'      => $this->valor($order, 'shipping.address.suburb'),
                'cidade'      => $this->valor($order, 'shipping.address.city'),
                'uf'          => $this->valor($order, 'shipping.address.state_code'),
                'cep'         => $this->valor($order, 'shipping.address.postcode'),
            ),
            'pagamento' => array(
                'metodo'             => $this->valor($order, 'payment.method'),
                'titulo'             => $this->valor($order, 'payment.title'),
                'parcelas'           => $this->valor($order, 'payment.installments'),
                'tipo_transacao'     => $tipoTransacao,
            ),
            'totais' => array(
                'produtos'     => $produtos,
                // O custo ausente precisa continuar distinto de frete grátis.
                // A validação decide se o pedido pode ser importado antes de
                // converter esse valor para número.
                'frete'        => $this->valor($order, 'shipping.cost', null),
                'desconto'     => $desconto,
                'percentual_desconto' => $percentualDesconto,
                'total'        => $this->valor($original, 'totalValue', 0),
                'frete_titulo' => $this->valor($order, 'shipping.title'),
                'frete_metodo' => $this->valor($order, 'shipping.method'),
            ),
            'itens' => $itens,
            'raw' => $original,
        );
    }
}

if (function_exists(__NAMESPACE__ . '\\definirClasse')) {
    definirClasse('ISET_NORMALIZADOR');
}

if (!class_exists(__NAMESPACE__ . '\\iset_normalizador', false)) {
    class_alias(__NAMESPACE__ . '\\iset_normalizador_', __NAMESPACE__ . '\\iset_normalizador');
}
