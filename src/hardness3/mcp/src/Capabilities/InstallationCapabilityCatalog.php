<?php

declare(strict_types=1);

namespace HardnessMcp\Capabilities;

use PDO;

/** Code-owned optional features. New capabilities must be explicit and tested. */
final class InstallationCapabilityCatalog
{
    public static function create(): CapabilityRegistry
    {
        return new CapabilityRegistry([
            new CapabilityDefinition('logistics.loads', [
                new RequiredRecord('C029', 'C029_Id', 286),
                new RequiredColumns('SMT071', ['SMT071_Id', 'SMT071_C004_Id', 'SMT071_Descricao', 'SMT071_Flag_Status', 'SMT071_Data_Criacao', 'SMT071_Data_Previsao', 'SMT071_Hora_Previsao', 'SMT071_D109_Id', 'SMT071_D022_Id', 'SMT071_D170_Id', 'SMT071_Valor_Total', 'SMT071_Peso_Total', 'SMT071_Valor_Frete_Incluso', 'SMT071_Valor_Fechado', 'SMT071_Km_Rodado']),
                new RequiredColumns('SMT072', ['SMT072_Id', 'SMT072_SMT071_Id', 'SMT072_T005_Id', 'SMT072_Sequecia_Entrega', 'SMT072_Valor_Pedido', 'SMT072_Peso_Pedido', 'SMT072_Guia_St', 'SMT072_Contrata_Auxiliar_Descarga', 'SMT072_Valor_Axiliar_Descarga']),
            ]),
            new CapabilityDefinition('logistics.load.order_context', [
                new RequiredColumns('T005', ['T005_Id', 'T005_C004_Id', 'T005_D024_Id', 'T005_D148_Id_Entrega', 'T005_Data_Emissao', 'T005_Data_Entrega', 'T005_Valor_Frete', 'T005_Prazos_Pagamentos', 'T005_Nome_Status']),
                new RequiredColumns('T005A', ['T005A_T005_Id', 'T005A_D148_Id_Redespacho', 'T005A_Valor_Frete_Itens']),
                new RequiredColumns('D024', ['D024_Id', 'D024_C004_Id', 'D024_D018_Id', 'D024_D020_Id', 'D024_Nome_Empresa', 'D024_Nome_Fantasia', 'D024_Cidade', 'D024_Estado', 'D024_Endereco', 'D024_Numero', 'D024_Bairro', 'D024_Cep']),
                new RequiredColumns('D148', ['D148_Id', 'D148_D018_Id', 'D148_D020_Id', 'D148_Rua', 'D148_Numero', 'D148_Bairro', 'D148_Cep']),
                new RequiredColumns('D018', ['D018_Id', 'D018_UF']),
                new RequiredColumns('D020', ['D020_Id', 'D020_D084_Id', 'D020_Nome_Cidade']),
                new RequiredColumns('D084', ['D084_Id', 'D084_Regiao']),
                new RequiredColumns('T007', ['T007_Id', 'T007_T005_Id', 'T007_C004_Id', 'T007_Numero_Nota_Fiscal', 'T007_Serie', 'T007_Data_Emissao', 'T007_Valor_Total', 'T007_Chave_Acesso_Nfe', 'T007_Flag_Cancelada', 'T007_Cliente_Razao', 'T007_Cliente_Cidade', 'T007_Cliente_Estado', 'T007_Cliente_Endereco', 'T007_Cliente_Endereco_Numero', 'T007_Cliente_Bairro', 'T007_Cliente_CEP']),
            ]),
            new CapabilityDefinition('logistics.load.resources', [
                new RequiredColumns('D109', ['D109_Id', 'D109_Nome']),
                new RequiredColumns('D022', ['D022_Id', 'D022_Nome_Empresa', 'D022_Nome_Fantasia']),
                new RequiredColumns('D170', ['D170_Id', 'D170_Identificacao_Veiculo', 'D170_Placa', 'D170_Capacidade_Kg', 'D170_Capacidade_M3']),
                new RequiredColumns('D170A', ['D170A_D170_Id', 'D170A_Comprimento_Carroceria']),
                new RequiredColumns('SMT171', ['SMT171_Id', 'SMT171_SMT071_Id', 'SMT171_C004_Id', 'SMT171_Data']),
            ]),
            // This is deliberately both profile- and schema-bound. A database
            // which happens to have similarly named tables must not publish a
            // customer-specific workflow.
            new CapabilityDefinition('customer.inecel.certificate_trace', [
                new RequiredInstallationProfile('inecel-process-analysis-v1'),
                new RequiredColumns('T007', ['T007_Id', 'T007_C004_Id', 'T007_Numero_Nota_Fiscal', 'T007_Chave_Acesso_Nfe']),
                new RequiredColumns('T008', ['T008_Id', 'T008_T007_Id', 'T008_D009_Id', 'T008_C004_Id', 'T008_T014_Id']),
                new RequiredColumns('T001', ['T001_Id', 'T001_T007_Id', 'T001_T008_Id', 'T001_T013_Id', 'T001_T014_Id', 'T001_D009_Id', 'T001_C004_Id']),
                new RequiredColumns('T001A', ['T001A_Id', 'T001A_T001_Id', 'T001A_Lote', 'T001A_T255_Id']),
                new RequiredColumns('T255', ['T255_Id', 'T255_C004_Id']),
                new RequiredColumns('T264', ['T264_Id', 'T264_T255_Id', 'T264_D009_Id']),
                new RequiredColumns('T013', ['T013_Id', 'T013_C004_Id', 'T013_Numero_Nota_Fiscal', 'T013_Data_Entrada', 'T013_Chave_Acesso_Nfe']),
                new RequiredColumns('T014', ['T014_Id', 'T014_T013_Id', 'T014_D009_Id', 'T014_C004_Id', 'T014_Codigo_Produto', 'T014_Descricao_Produto']),
                new RequiredColumns('SMT205', ['SMT205_Id', 'SMT205_T013_Id', 'SMT205_C007_Id', 'SMT205_Nome_Arquivo', 'SMT205_Descricao', 'SMT205_Data_Hora_Upload']),
            ]),
        ]);
    }

    public static function resolve(PDO $pdo, ?string $installationProfile = null): CapabilitySet
    {
        return self::create()->resolve(new PdoCapabilityProbe($pdo, $installationProfile));
    }
}
