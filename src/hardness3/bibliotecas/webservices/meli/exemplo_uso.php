<?php
/**
 * EXEMPLO DE USO - CONEXAO MELI COM SUPORTE A MÚLTIPLAS EMPRESAS
 * 
 * Este arquivo demonstra como usar a nova implementação da classe ConexaoMeli
 * que suporta automaticamente múltiplas empresas baseado no sellerIdMeli.
 */

require_once 'conexaoMeli.php';

echo "<h2>Exemplo de Uso - ConexaoMeli Multi-Empresas</h2>\n";

// ========================================
// EXEMPLO 1: Consulta automática por userId
// ========================================
echo "<h3>1. Consulta Automática por UserId</h3>\n";

try {
    $meli = new ConexaoMeli();
    
    // O Mercado Livre sempre envia o userId (sellerIdMeli)
    $userId = '123456789'; // Exemplo de sellerIdMeli
    
    echo "Consultando pedido para userId: {$userId}<br>\n";
    
    // A classe automaticamente:
    // 1. Identifica qual empresa possui esse sellerIdMeli
    // 2. Carrega todos os parâmetros dessa empresa
    // 3. Executa a consulta
    $pedido = $meli->consultarPedido('orders/987654321', $userId);
    
    if ($pedido !== false) {
        echo "✅ Pedido consultado com sucesso para empresa ID: " . $meli->getEmpresaId() . "<br>\n";
        echo "✅ Seller ID da empresa: " . $meli->getSellerId() . "<br>\n";
    } else {
        echo "❌ Erro ao consultar pedido: " . $meli->getMsgErro() . "<br>\n";
    }
    
} catch (Exception $e) {
    echo "❌ Exceção: " . $e->getMessage() . "<br>\n";
}

echo "<br>\n";

// ========================================
// EXEMPLO 2: Uso com empresa atual (compatibilidade)
// ========================================
echo "<h3>2. Uso com Empresa Atual (Compatibilidade)</h3>\n";

try {
    $meli = new ConexaoMeli();
    
    // Carrega parâmetros da empresa atual (comportamento antigo)
    if ($meli->carregarEmpresaAtual()) {
        echo "✅ Parâmetros da empresa atual carregados<br>\n";
        echo "✅ Empresa ID: " . $meli->getEmpresaId() . "<br>\n";
        echo "✅ Seller ID: " . $meli->getSellerId() . "<br>\n";
        
        // Agora pode usar consultarPedido sem userId (usa o sellerIdMeli da empresa atual)
        $pedido = $meli->consultarPedido('orders/111222333', $meli->getSellerId());
        
        if ($pedido !== false) {
            echo "✅ Pedido consultado com sucesso<br>\n";
        } else {
            echo "❌ Erro ao consultar pedido: " . $meli->getMsgErro() . "<br>\n";
        }
    } else {
        echo "❌ Erro ao carregar empresa atual: " . $meli->getMsgErro() . "<br>\n";
    }
    
} catch (Exception $e) {
    echo "❌ Exceção: " . $e->getMessage() . "<br>\n";
}

echo "<br>\n";

// ========================================
// EXEMPLO 3: Renovação de token
// ========================================
echo "<h3>3. Renovação de Token</h3>\n";

try {
    $meli = new ConexaoMeli();
    
    // Primeiro precisa carregar parâmetros (empresa atual ou por userId)
    if ($meli->carregarEmpresaAtual()) {
        echo "✅ Parâmetros carregados, renovando token...<br>\n";
        
        $resultado = $meli->atualizaRefreshToken();
        
        if ($resultado !== false) {
            echo "✅ Token renovado com sucesso<br>\n";
            if (is_array($resultado)) {
                echo "✅ Novo access_token: " . substr($resultado['access_token'], 0, 20) . "...<br>\n";
            }
        } else {
            echo "❌ Erro ao renovar token: " . $meli->getMsgErro() . "<br>\n";
        }
    } else {
        echo "❌ Erro ao carregar parâmetros: " . $meli->getMsgErro() . "<br>\n";
    }
    
} catch (Exception $e) {
    echo "❌ Exceção: " . $e->getMessage() . "<br>\n";
}

echo "<br>\n";

// ========================================
// EXEMPLO 4: Múltiplas empresas em sequência
// ========================================
echo "<h3>4. Múltiplas Empresas em Sequência</h3>\n";

try {
    $meli = new ConexaoMeli();
    
    // Lista de userIds para testar
    $userIds = ['seller123', 'seller456', 'seller789'];
    
    foreach ($userIds as $userId) {
        echo "Testando userId: {$userId}<br>\n";
        
        // Cada consulta automaticamente identifica e carrega a empresa correta
        $pedido = $meli->consultarPedido('orders/test', $userId);
        
        if ($pedido !== false) {
            echo "✅ Empresa ID: " . $meli->getEmpresaId() . " | Seller ID: " . $meli->getSellerId() . "<br>\n";
        } else {
            echo "❌ Erro: " . $meli->getMsgErro() . "<br>\n";
        }
        
        echo "<br>\n";
    }
    
} catch (Exception $e) {
    echo "❌ Exceção: " . $e->getMessage() . "<br>\n";
}

echo "<br>\n";

// ========================================
// EXEMPLO 5: Verificação de status
// ========================================
echo "<h3>5. Verificação de Status</h3>\n";

try {
    $meli = new ConexaoMeli();
    
    echo "Status inicial:<br>\n";
    echo "- Parâmetros carregados: " . ($meli->parametrosCarregados() ? 'Sim' : 'Não') . "<br>\n";
    echo "- Tudo OK: " . ($meli->isTudoOk() ? 'Sim' : 'Não') . "<br>\n";
    echo "- Empresa ID: " . ($meli->getEmpresaId() ?: 'Não definido') . "<br>\n";
    echo "- Seller ID: " . ($meli->getSellerId() ?: 'Não definido') . "<br>\n";
    
    // Carregar empresa atual
    $meli->carregarEmpresaAtual();
    
    echo "<br>Status após carregar empresa atual:<br>\n";
    echo "- Parâmetros carregados: " . ($meli->parametrosCarregados() ? 'Sim' : 'Não') . "<br>\n";
    echo "- Tudo OK: " . ($meli->isTudoOk() ? 'Sim' : 'Não') . "<br>\n";
    echo "- Empresa ID: " . ($meli->getEmpresaId() ?: 'Não definido') . "<br>\n";
    echo "- Seller ID: " . ($meli->getSellerId() ?: 'Não definido') . "<br>\n";
    
} catch (Exception $e) {
    echo "❌ Exceção: " . $e->getMessage() . "<br>\n";
}

echo "<br>\n";
echo "<h3>✅ Exemplos concluídos!</h3>\n";
echo "<p>A nova implementação permite trabalhar com múltiplas empresas de forma automática e transparente.</p>\n";
?>
