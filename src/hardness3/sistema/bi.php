<?php
namespace hardness;

class BI
{
    public static $bulkRegistros = [];

    // Estrutura de dados do B.I.
    // Alterações aqui se refletem em todos os lugares
    public static function estruturaDeDados() {
        return [
            // Notas Fiscais
            'T008_Valor_Total_Preco' => ['campo' => 'T008_Valor_Total_Preco', 'tipo' => 'DECIMAL(20,5)', 'aspas' => false, 'nome' => 'Valor Total Preço',],
            'T008_Quantidade' => ['campo' => 'T008_Quantidade', 'tipo' => 'DECIMAL(20,5)', 'aspas' => false, 'nome' => 'Quantidade',],
            'T008_Valor_ICMS' => ['campo' => 'T008_Valor_ICMS', 'tipo' => 'DECIMAL(20,5)', 'aspas' => false, 'nome' => 'Valor ICMS',],
            'T008_Valor_ICMS_Substituicao_Retencao' => ['campo' => 'T008_Valor_ICMS_Substituicao_Retencao', 'tipo' => 'DECIMAL(20,5)', 'aspas' => false, 'nome' => 'Valor ICMS SR',],
            'T008_Valor_IPI' => ['campo' => 'T008_Valor_IPI', 'tipo' => 'DECIMAL(20,5)', 'aspas' => false, 'nome' => 'Valor IPI',],
            'T008_Valor_PIS' => ['campo' => 'T008_Valor_PIS', 'tipo' => 'DECIMAL(20,5)', 'aspas' => false, 'nome' => 'Valor PIS',],
            'T008_Valor_COFINS' => ['campo' => 'T008_Valor_COFINS', 'tipo' => 'DECIMAL(20,5)', 'aspas' => false, 'nome' => 'Valor COFINS',],
            'T007_Id' => ['campo' => 'T007_Id', 'tipo' => 'UInt64', 'aspas' => false, 'nome' => 'T007_Id',],
            'T008_Id' => ['campo' => 'T008_Id', 'tipo' => 'UInt64', 'aspas' => false, 'nome' => 'T008_Id',],
            'T007_Flag_Expedicao' => ['campo' => 'T007_Flag_Expedicao', 'tipo' => 'FixedString(1)', 'aspas' => true, 'nome' => 'Flag Expedição',],
            'T007_Flag_Cancelada' => ['campo' => 'T007_Flag_Cancelada', 'tipo' => 'FixedString(1)', 'aspas' => true, 'nome' => 'Flag Cancelada',],
            // Data
            'Data' => ['campo' => 'Data', 'tipo' => 'Date', 'aspas' => true, 'nome' => 'Data',],
            'Data_Dia' => ['campo' => 'Data_Dia', 'tipo' => 'FixedString(2)', 'aspas' => true, 'nome' => 'Dia',],
            'Data_Dia_Da_Semana' => ['campo' => 'Data_Dia_Da_Semana', 'tipo' => 'UInt64', 'aspas' => false, 'nome' => 'Dia da Semana',],
            'Data_Mes' => ['campo' => 'Data_Mes', 'tipo' => 'UInt64', 'aspas' => false, 'nome' => 'Mes',],
            'Data_Mes_Texto' => ['campo' => 'Data_Mes_Texto', 'tipo' => 'String', 'aspas' => true, 'nome' => 'Mes',],
            'Data_Ano' => ['campo' => 'Data_Ano', 'tipo' => 'UInt64', 'aspas' => false, 'nome' => 'Ano',],
            'Data_Mes_Ano' => ['campo' => 'Data_Mes_Ano', 'tipo' => 'FixedString(7)', 'aspas' => true, 'nome' => 'Mes/Ano',],
            // D024+
            'D024_Id' => ['campo' => 'D024_Id', 'tipo' => 'UInt64', 'aspas' => false, 'nome' => 'D024_Id',],
            'D024_Nome_Empresa' => ['campo' => 'D024_Nome_Empresa', 'tipo' => 'String', 'aspas' => true, 'nome' => 'Nome_Empresa',],
            'D024_Nome_Fantasia' => ['campo' => 'D024_Nome_Fantasia', 'tipo' => 'String', 'aspas' => true, 'nome' => 'Nome_Fantasia',],
            'D024_Endereco' => ['campo' => 'D024_Endereco', 'tipo' => 'String', 'aspas' => true, 'nome' => 'Endereço',],
            'D024_Cep' => ['campo' => 'D024_Cep', 'tipo' => 'FixedString(20)', 'aspas' => true, 'nome' => 'Cep',],
            'D024_Bairro' => ['campo' => 'D024_Bairro', 'tipo' => 'String', 'aspas' => true, 'nome' => 'Bairro',],
            'D024_Cidade' => ['campo' => 'D024_Cidade', 'tipo' => 'String', 'aspas' => true, 'nome' => 'Cidade',],
            'D018_UF' => ['campo' => 'D018_UF', 'tipo' => 'FixedString(2)', 'aspas' => true, 'nome' => 'UF',],
            'D020_Nome_Cidade' => ['campo' => 'D020_Nome_Cidade', 'tipo' => 'String', 'aspas' => true, 'nome' => 'Cidade',],
            // C007
            'Vendedor_C007_Login' => ['campo' => 'Vendedor_C007_Login', 'tipo' => 'String', 'aspas' => true, 'nome' => 'Vendedor Login',],
            'Vendedor_C007_Email' => ['campo' => 'Vendedor_C007_Email', 'tipo' => 'String', 'aspas' => true, 'nome' => 'Vendedor Email',],
            'Vendedor_C007_Nome' => ['campo' => 'Vendedor_C007_Nome', 'tipo' => 'String', 'aspas' => true, 'nome' => 'Vendedor Nome',],
            'Vendedor_C007_Primeiro_Nome' => ['campo' => 'Vendedor_C007_Primeiro_Nome', 'tipo' => 'String', 'aspas' => true, 'nome' => 'Vendedor Primeiro Nome',],
            'Vendedor_C007_Id' => ['campo' => 'Vendedor_C007_Id', 'tipo' => 'UInt64', 'aspas' => false, 'nome' => 'Vendedor C007_Id',],
            'Supervisor_C007_Login' => ['campo' => 'Supervisor_C007_Login', 'tipo' => 'String', 'aspas' => true, 'nome' => 'Supervisor Login',],
            'Supervisor_C007_Email' => ['campo' => 'Supervisor_C007_Email', 'tipo' => 'String', 'aspas' => true, 'nome' => 'Supervisor Email',],
            'Supervisor_C007_Nome' => ['campo' => 'Supervisor_C007_Nome', 'tipo' => 'String', 'aspas' => true, 'nome' => 'Supervisor Nome',],
            'Supervisor_C007_Primeiro_Nome' => ['campo' => 'Supervisor_C007_Primeiro_Nome', 'tipo' => 'String', 'aspas' => true, 'nome' => 'Supervisor Primeiro Nome',],
            'Supervisor_C007_Id' => ['campo' => 'Supervisor_C007_Id', 'tipo' => 'UInt64', 'aspas' => false, 'nome' => 'Supervisor C007_Id',],
            // Produto
            'D009_Id' => ['campo' => 'D009_Id', 'tipo' => 'UInt64', 'aspas' => false, 'nome' => 'D009_Id',],
            'D001_Descricao_Produto' => ['campo' => 'D001_Descricao_Produto', 'tipo' => 'String', 'aspas' => true, 'nome' => 'Descrição Produto',],
            'D082_Marca' => ['campo' => 'D082_Marca', 'tipo' => 'String', 'aspas' => true, 'nome' => 'Marca',],
            'C008_Tipo' => ['campo' => 'C008_Tipo', 'tipo' => 'String', 'aspas' => true, 'nome' => 'Departamento',],
            'D060_Descricao_Grupo' => ['campo' => 'D060_Descricao_Grupo', 'tipo' => 'String', 'aspas' => true, 'nome' => 'Setor',],
            'D003_Nome_Linha' => ['campo' => 'D003_Nome_Linha', 'tipo' => 'String', 'aspas' => true, 'nome' => 'Grupo',],
            'D002_Descricao_Produto' => ['campo' => 'D002_Descricao_Produto', 'tipo' => 'String', 'aspas' => true, 'nome' => 'Sub-Grupo',],
            'D001_Codigo_Produto' => ['campo' => 'D001_Codigo_Produto', 'tipo' => 'String', 'aspas' => true, 'nome' => 'Código Produto',],
            // CFOP
            'D006_Id' => ['campo' => 'D006_Id', 'tipo' => 'UInt64', 'aspas' => false, 'nome' => 'D006_Id',],
            'D006_Codigo_CFOP' => ['campo' => 'D006_Codigo_CFOP', 'tipo' => 'String', 'aspas' => true, 'nome' => 'Código CFOP',],
            'D006_Descricao' => ['campo' => 'D006_Descricao', 'tipo' => 'String', 'aspas' => true, 'nome' => 'Descrição CFOP',],
            'D006_Flag_Entrada_Saida' => ['campo' => 'D006_Flag_Entrada_Saida', 'tipo' => 'FixedString(1)', 'aspas' => true, 'nome' => 'CFOP Flag Entrada Saida',],
            'D006_Flag_Devolucao' => ['campo' => 'D006_Flag_Devolucao', 'tipo' => 'FixedString(1)', 'aspas' => true, 'nome' => 'CFOP Flag Devolução',],
            'D006_Flag_Venda_Compra_Outros' => ['campo' => 'D006_Flag_Venda_Compra_Outros', 'tipo' => 'FixedString(2)', 'aspas' => true, 'nome' => 'CFOP Flag Tipo CFOP',],
            'D006_Flag_Gera_Contas' => ['campo' => 'D006_Flag_Gera_Contas', 'tipo' => 'FixedString(1)', 'aspas' => true, 'nome' => 'CFOP Flag Gera Contas',],
            'D006_Flag_Estoque' => ['campo' => 'D006_Flag_Estoque', 'tipo' => 'FixedString(1)', 'aspas' => true, 'nome' => 'CFOP Flag Gera Estoque',],
            // Representante
            'D021_Nome_Empresa' => ['campo' => 'D021_Nome_Empresa', 'tipo' => 'String', 'aspas' => true, 'nome' => 'Representante',],
            // C004
            'C004_Id' => ['campo' => 'C004_Id', 'tipo' => 'UInt64', 'aspas' => false, 'nome' => 'C004_Id',],
            'C004_Nome_Empresa' => ['campo' => 'C004_Nome_Empresa', 'tipo' => 'String', 'aspas' => true, 'nome' => 'Nome Empresa',],
            'C004_Nome_Abreviado' => ['campo' => 'C004_Nome_Abreviado', 'tipo' => 'String', 'aspas' => true, 'nome' => 'Nome Empresa Abreviado',],
        ];
    }

    // Limpa e recria a estrutura
    public static function carregaEstrutura() {
        global $confUsuarioClickHouse, $g;
        $sql = "SHOW DATABASES LIKE " . $confUsuarioClickHouse['dbDatabase'];
        $result = mysqli_query_clickHouse($sql);
        if (empty($result))
        {
            // Reconecta utilizando um banco que sabemos que existe (e998)
            $g['conexaoBancoClickHouse'] = mysqli_connect($confUsuarioClickHouse['dbHost'], $confUsuarioClickHouse['dbUser'], $confUsuarioClickHouse['dbPass'], 'e998' , $confUsuarioClickHouse['dbPort']);

            // Cria o novo banco
            $sql = "CREATE DATABASE IF NOT EXISTS " . $confUsuarioClickHouse['dbDatabase'];
            $result = mysqli_query_clickHouse($sql);
            if (!$result) { die('Invalid query: ' . mysqli_error_clickHouse() . "\n<br/>SQL: " . $sql); }

            // Conexão com o novo banco
            $g['conexaoBancoClickHouse'] = mysqli_connect($confUsuarioClickHouse['dbHost'], $confUsuarioClickHouse['dbUser'], $confUsuarioClickHouse['dbPass'], $confUsuarioClickHouse['dbDatabase'], $confUsuarioClickHouse['dbPort']);
        }

        echo "Removendo tabela master...\n<br />";

        $sql = "DROP TABLE IF EXISTS BIH001;";
        $result = mysqli_query_clickHouse($sql);
        if (!$result) { die('Invalid query: ' . mysqli_error_clickHouse() . "\n<br/>SQL: " . $sql); }

        echo "Criando tabela master...\n<br />";

        $sql = <<<EOT
CREATE TABLE BIH001 (
EOT;
        $primeiroRegistro = true;
        foreach (self::estruturaDeDados() as $item) {
            $sql .= $primeiroRegistro ? '' : ', ';
            $sql .= "{$item['campo']} {$item['tipo']}";
            $primeiroRegistro = false;
        }
        $sql .= <<<EOT
)
ENGINE = MergeTree
ORDER BY Data
EOT;
        $result = mysqli_query_clickHouse($sql);
        if (!$result) { die('Invalid query: ' . mysqli_error_clickHouse() . "\n<br/>SQL: " . $sql); }
    }

    // Incluir um registro para inclusão (via bulk) na estrutura do clickhouse
    public static function incluirRegistro($registro) {
        self::$bulkRegistros[] = $registro;
        if (count(self::$bulkRegistros) > 1000) {
            self::processarIncluirRegistro();
        }
    }

    // Processa os registros pendentes de inclusão - incluídos via incluirRegistro()
    public static function processarIncluirRegistro() {
        $campos = '';
        foreach (self::estruturaDeDados() as $item) {
            $campos .= $campos ? ", `{$item['campo']}`" : "`{$item['campo']}`";
        }

        $sql = "INSERT INTO BIH001 ({$campos}) VALUES ";
        $primeiroRegistro = true;
        foreach (self::$bulkRegistros as $bulkRegistro) {
            $valores = '';
            foreach (self::estruturaDeDados() as $item) {
                $valores .= $valores ? ', ' : '';
                $valores .= $bulkRegistro[$item['campo']] ? ($item['aspas'] ? "'" . mysql_real_escape_string($bulkRegistro[$item['campo']]) . "'" : $bulkRegistro[$item['campo']]) : 'NULL';
            }
            $sql .= $primeiroRegistro ? "({$valores})" : ", ({$valores})";
            $primeiroRegistro = false;
        }

        $result = mysqli_query_clickHouse($sql);
        if (!$result) { die('Invalid query: ' . mysqli_error_clickHouse() . "\n<br/>SQL: " . $sql); }

        self::$bulkRegistros = [];
    }

    // Preencher os campos de DATA do registro
    public static function preencherRegistroData($data, &$registro) {
        date_default_timezone_set('America/Sao_Paulo');
        if (empty($data)) { return false; }
        $data_ = explode('-', $data);
        $data_ = mktime(0, 0, 0, $data_[1], $data_[2], $data_[0]);

        $registro['Data'] = $data;
        $registro['Data_Dia'] = str_pad(Date('d', $data_), 2, "0", STR_PAD_LEFT);
        $registro['Data_Dia_Da_Semana'] = Date('N', $data_);
        $registro['Data_Mes'] = Date('m', $data_);
        $registro['Data_Ano'] = Date('Y', $data_);
        $registro['Data_Mes_Ano'] = Date('m', $data_) . '/' . Date('Y', $data_);

        $Mes_Texto = array('01' => 'Janeiro',
                           '02' => 'Fevereiro',
                           '03' => 'Março',
                           '04' => 'Abril',
                           '05' => 'Maio',
                           '06' => 'Junho',
                           '07' => 'Julho',
                           '08' => 'Agosto',
                           '09' => 'Setembro',
                           '10' => 'Outubro',
                           '11' => 'Novembro',
                           '12' => 'Dezembro');

        $Mes = Date('m', $data_);
        $registro['Data_Mes_Texto'] = $Mes_Texto[$Mes];
    }
}
