CREATE PROCEDURE `T008_Gravar_Totalizacao_4_Reforma`(xT008_Id int(11))
BEGIN
    /* Variáveis do script
    =========================================== */
    DECLARE xT007_Id INT(11) DEFAULT 0;
    DECLARE xC004_Id int(10);
    DECLARE xD006_Deduz_ICMS_Desonerado_Valor_Total VARCHAR (1) DEFAULT 'S';
    DECLARE xD006_Flag_Nao_Somar_IBS_CBS_Valor_Total_Reforma VARCHAR (1) DEFAULT 'S';
    DECLARE xD006_Flag_Deduz_PIS_Base_IBS_CBS VARCHAR(1) DEFAULT 'S';
    DECLARE xD006_Flag_Deduz_COFINS_Base_IBS_CBS VARCHAR(1) DEFAULT 'S';
    DECLARE xD006_Flag_Deduz_ICMS_Base_IBS_CBS VARCHAR(1) DEFAULT 'S';
    DECLARE xD006_Flag_Deduz_DIFAL_Base_IBS_CBS VARCHAR(1) DEFAULT 'S';
    DECLARE xD006_Flag_Deduz_FCP_Base_IBS_CBS VARCHAR(1) DEFAULT 'S';
    DECLARE xD006_Flag_Deduz_FCP_Dest_Base_IBS_CBS VARCHAR(1) DEFAULT 'S';
    DECLARE xD006_Flag_Devolucao varchar(1) default 'N';
    DECLARE xC031_Atualizar_Aliquotas_Devolucao varchar(1);
    DECLARE xT008_Flag_Calculo_Manual varchar(1) default 'N';
    DECLARE xT007A_Flag_Calcular_Reforma_Tributaria_Nota_Devolucao varchar(1) default 'N';    

    /* SELECTS
    ================================================== */
    -- Informações gerais do T008 (Item NFe), T007 (Cabeçalho NFe) e D006 (CFOP do item da NFe)
    SELECT T008_T007_Id,
           T007_C004_Id,
           D006_Deduz_ICMS_Desonerado_Valor_Total,
           D006_Flag_Nao_Somar_IBS_CBS_Valor_Total_Reforma,
           D006_Flag_Deduz_PIS_Base_IBS_CBS,
           D006_Flag_Deduz_COFINS_Base_IBS_CBS,
           D006_Flag_Deduz_ICMS_Base_IBS_CBS,
           D006_Flag_Deduz_DIFAL_Base_IBS_CBS,
           D006_Flag_Deduz_FCP_Base_IBS_CBS,
           D006_Flag_Deduz_FCP_Dest_Base_IBS_CBS,
           D006_Flag_Devolucao,
           T008_Flag_Calculo_Manual,
           T007A_Flag_Calcular_Reforma_Tributaria_Nota_Devolucao
      INTO xT007_Id,
           xC004_Id,
           xD006_Deduz_ICMS_Desonerado_Valor_Total,
           xD006_Flag_Nao_Somar_IBS_CBS_Valor_Total_Reforma,
           xD006_Flag_Deduz_PIS_Base_IBS_CBS,
           xD006_Flag_Deduz_COFINS_Base_IBS_CBS,
           xD006_Flag_Deduz_ICMS_Base_IBS_CBS,
           xD006_Flag_Deduz_DIFAL_Base_IBS_CBS,
           xD006_Flag_Deduz_FCP_Base_IBS_CBS,
           xD006_Flag_Deduz_FCP_Dest_Base_IBS_CBS,
           xD006_Flag_Devolucao,
           xT008_Flag_Calculo_Manual,
           xT007A_Flag_Calcular_Reforma_Tributaria_Nota_Devolucao
      FROM T008
 LEFT JOIN T007 ON T007_Id=T008_T007_Id
 LEFT JOIN D006 ON D006_Id=T008_D006_Id
 LEFT JOIN T007A ON T007_Id = T007A_T007_Id
     WHERE T008_Id=xT008_Id
     LIMIT 1;

    -- Seleciona a configuração de atualizarAliquotasDevolucao do cliente
    SELECT C031_Valor INTO xC031_Atualizar_Aliquotas_Devolucao FROM C031 WHERE C031_Campo = 'atualizarAliquotasDevolucao' AND C031_C004_Id = xC004_Id;

    /* ! ATENÇÃO: Precisa desse [BEFORE]/[AFTER] cálculo manual porque existem coisas que precisam ser calculadas antes do calculo manual (alíquotas por exemplo), e coisas que precisam ser calculadas depois (valor total por exemplo)
    [BEFORE Cálculo manual] Fórmulas essenciais dos campos da reforma 
    1. Devem ser calculados independente se o item é cálculo manual ou devolução
    2. São dispensáveis da aba 'Cálculos' porque não fazem sentido calcular se o sistema já teria tudo pronto para calcular eles
    =================================================== */
    UPDATE T008A
    LEFT JOIN T008 ON T008A_T008_Id = T008_Id
    LEFT JOIN T007 ON T008_T007_Id = T007_Id
    SET
    -- Cálculo das aliquotas efetivas (alíquota + redução aplicada)
    T008A_Aliquota_CBS_Efetiva              = Calcular_T008A_Aliquota_CBS_Efetiva(T008_Id, T008A_Id, T008A_Aliquota_CBS, T008A_Percentual_Reducao_CBS),
    T008A_Aliquota_IBS_UF_Efetiva           = Calcular_T008A_Aliquota_IBS_UF_Efetiva(T008_Id, T008A_Id, T008A_Aliquota_IBS_UF, T008A_Percentual_Reducao_IBS_UF),
    T008A_Aliquota_IBS_Municipal_Efetiva    = Calcular_T008A_Aliquota_IBS_Municipal_Efetiva(T008_Id, T008A_Id, T008A_Aliquota_IBS_Municipal, T008A_Percentual_Reducao_IBS_Municipal),

    -- Cálculo das aliquotas efetivas regular (alíquota + redução aplicada) usamos a mesma função que a alíquota efetiva normal porque o cálculo é o mesmo
    T008A_Aliquota_CBS_Efetiva_Regular              = Calcular_T008A_Aliquota_CBS_Efetiva(T008_Id, T008A_Id, T008A_Aliquota_CBS_Regular, T008A_Percentual_Reducao_CBS_Regular),
    T008A_Aliquota_IBS_UF_Efetiva_Regular           = Calcular_T008A_Aliquota_IBS_UF_Efetiva(T008_Id, T008A_Id, T008A_Aliquota_IBS_UF_Regular, T008A_Percentual_Reducao_IBS_UF_Regular),
    T008A_Aliquota_IBS_Municipal_Efetiva_Regular    = Calcular_T008A_Aliquota_IBS_Municipal_Efetiva(T008_Id, T008A_Id, T008A_Aliquota_IBS_Municipal_Regular, T008A_Percentual_Reducao_IBS_Municipal_Regular)
    WHERE T008_Id=xT008_Id AND ifnull(T007_Numero_Protocolo_Nfe,'')='';

    /* Fórmulas relativas dos campos da reforma (São calculados somente se cálculo automático e não é devolução) (É assim porque esses campos podem ser setados na aba 'Cálculos')
    ================================================== */
    IF (xT008_Flag_Calculo_Manual != 'S' or xT007A_Flag_Calcular_Reforma_Tributaria_Nota_Devolucao = 'S') THEN
        UPDATE T008A
        LEFT JOIN T008 ON T008A_T008_Id = T008_Id
        LEFT JOIN T007 ON T008_T007_Id = T007_Id
        SET 
        -- As opções da CFOP afetam somente as deduções desta base; apenas N desativa.
        -- Calculo da base de CBS e IBS
        T008A_Valor_Base_CBS_IBS                = Calcular_T008A_Valor_Base_CBS_IBS(
            T008_Id, 
            T008A_Id,
            T008_Valor_Total_Preco_Sem_Desconto,
            T008_Valor_Total_Frete,
            T008_Valor_Total_Seguro,
            T008_Valor_Total_Despesas_Acessorias,
            T008_Valor_Total_Imposto_Importacao,
            T008_Valor_Total_Desconto,
            IF(IFNULL(xD006_Flag_Deduz_PIS_Base_IBS_CBS, 'S') = 'N', 0, T008_Valor_PIS),
            IF(IFNULL(xD006_Flag_Deduz_COFINS_Base_IBS_CBS, 'S') = 'N', 0, T008_Valor_COFINS),
            IF(IFNULL(xD006_Flag_Deduz_ICMS_Base_IBS_CBS, 'S') = 'N', 0, T008_Valor_ICMS),
            IF(IFNULL(xD006_Flag_Deduz_DIFAL_Base_IBS_CBS, 'S') = 'N', 0, T008_Valor_Partilha_ICMS_Destino),
            IF(IFNULL(xD006_Flag_Deduz_FCP_Base_IBS_CBS, 'S') = 'N', 0, T008_Valor_FCP),
            IF(IFNULL(xD006_Flag_Deduz_FCP_Dest_Base_IBS_CBS, 'S') = 'N', 0, T008_Valor_FCP_Partilha),
            T008A_Aliquota_CBS,
            T008A_Aliquota_IBS_Municipal,
            T008A_Aliquota_IBS_UF,
            T008A_Aliquota_CBS_Regular,
            T008A_Aliquota_IBS_Municipal_Regular,
            T008A_Aliquota_IBS_UF_Regular
        ), 

        -- Cálculo dos valores dos diferimentos de CBS e IBS (usa alíquota efetiva porque tem redução aplicada)
        T008A_Valor_Diferimento_CBS             = Calcular_T008A_Valor_Diferimento_CBS(T008_Id, T008A_Id, T008A_Valor_Base_CBS_IBS, T008A_Aliquota_CBS_Efetiva, T008A_Percentual_Diferimento_CBS),
        T008A_Valor_Diferimento_IBS_UF          = Calcular_T008A_Valor_Diferimento_IBS_UF(T008_Id, T008A_Id, T008A_Valor_Base_CBS_IBS, T008A_Aliquota_IBS_UF_Efetiva, T008A_Percentual_Diferimento_IBS_UF),
        T008A_Valor_Diferimento_IBS_Municipal   = Calcular_T008A_Valor_Diferimento_IBS_Municipal(T008_Id, T008A_Id, T008A_Valor_Base_CBS_IBS, T008A_Aliquota_IBS_Municipal_Efetiva, T008A_Percentual_Diferimento_IBS_Municipal),

        -- Aqui mandamos a aliquota efetiva porque ela é a que tem a redução aplicada (ou seja, se redução for zero, a aliquota normal será usada)
        T008A_Valor_CBS                         = Calcular_T008A_Valor_CBS(T008_Id, T008A_Id, T008A_Valor_Base_CBS_IBS, T008A_Aliquota_CBS_Efetiva, T008A_Valor_Diferimento_CBS),
        T008A_Valor_IBS_UF                      = Calcular_T008A_Valor_IBS_UF(T008_Id, T008A_Id, T008A_Valor_Base_CBS_IBS, T008A_Aliquota_IBS_UF_Efetiva, T008A_Valor_Diferimento_IBS_UF),
        T008A_Valor_IBS_Municipal               = Calcular_T008A_Valor_IBS_Municipal(T008_Id, T008A_Id, T008A_Valor_Base_CBS_IBS, T008A_Aliquota_IBS_Municipal_Efetiva, T008A_Valor_Diferimento_IBS_Municipal),

        -- Aqui cálculamos os valores da tributação regular, usamos o cacular dos impostos normais porque o cálculo é o mesmo, exceto que mandamos 0 para o diferimento porque tributação regular não possui diferimento
        T008A_Valor_CBS_Regular                 = Calcular_T008A_Valor_CBS(T008_Id, T008A_Id, T008A_Valor_Base_CBS_IBS, T008A_Aliquota_CBS_Efetiva_Regular, 0),
        T008A_Valor_IBS_UF_Regular              = Calcular_T008A_Valor_IBS_UF(T008_Id, T008A_Id, T008A_Valor_Base_CBS_IBS, T008A_Aliquota_IBS_UF_Efetiva_Regular, 0),
        T008A_Valor_IBS_Municipal_Regular       = Calcular_T008A_Valor_IBS_Municipal(T008_Id, T008A_Id, T008A_Valor_Base_CBS_IBS, T008A_Aliquota_IBS_Municipal_Efetiva_Regular, 0)

        WHERE T008_Id=xT008_Id and T007_Flag_Calcular='S' and (T007_Flag_Finalidade_NFe IN ('5','6') or xD006_Flag_Devolucao = 'N' or xC031_Atualizar_Aliquotas_Devolucao = 'S' or xT007A_Flag_Calcular_Reforma_Tributaria_Nota_Devolucao = 'S') AND ifnull(T007_Numero_Protocolo_Nfe,'')='';
    END IF;

    /* NT 2025.002 v1.51, UB13: CST 800/811 usam gTransfCred/gAjusteCompet,
       não gIBSCBS. Limpar resíduos de tributação comum em todo recálculo,
       inclusive manual, sem alterar os valores especiais informados.
       Isso NÃO implica vItem=0: o total continua na fórmula comum (VB01-10). */
    UPDATE T008A
    INNER JOIN T008 ON T008A_T008_Id = T008_Id
    INNER JOIN T007 ON T008_T007_Id = T007_Id
    SET T008A_Valor_Base_CBS_IBS = 0,
        T008A_Valor_CBS = 0,
        T008A_Valor_IBS_UF = 0,
        T008A_Valor_IBS_Municipal = 0,
        T008A_Valor_IBS = 0,
        T008A_Valor_Diferimento_CBS = 0,
        T008A_Valor_Diferimento_IBS_UF = 0,
        T008A_Valor_Diferimento_IBS_Municipal = 0,
        T008A_Valor_CBS_Regular = 0,
        T008A_Valor_IBS_UF_Regular = 0,
        T008A_Valor_IBS_Municipal_Regular = 0
    WHERE T008_Id = xT008_Id
      AND T008A_Situacao_Tributaria_CBS_IBS IN ('800', '811')
      AND T007_Flag_Finalidade_NFe IN ('5', '6')
      AND IFNULL(T007_Numero_Protocolo_Nfe, '') = ''
      AND IFNULL(T007_Flag_Cancelada, 'N') != 'S'
      AND IFNULL(T007_Flag_Inutilizado, 'N') != 'S';

    /* ! ATENÇÃO: Precisa desse [BEFORE]/[AFTER] cálculo manual porque existem coisas que precisam ser calculadas antes do calculo manual (alíquotas por exemplo), e coisas que precisam ser calculadas depois (valor total por exemplo)
    [AFTER Cálculo manual] Fórmulas essenciais dos campos da reforma 
    1. Devem ser calculados independente se o item é cálculo manual ou devolução
    2. São dispensáveis da aba 'Cálculos' porque não fazem sentido calcular se o sistema já teria tudo pronto para calcular eles
    =================================================== */
    UPDATE T008A
    LEFT JOIN T008 ON T008A_T008_Id = T008_Id
    LEFT JOIN T007 ON T008_T007_Id = T007_Id
    SET
    -- Cálculo IBS UF + IBS Mun
    T008A_Valor_IBS                         = Calcular_T008A_Valor_IBS(T008_Id, T008A_Id, T008A_Valor_IBS_Municipal, T008A_Valor_IBS_UF),

    -- Cálcula o valor total da reforma
    T008A_Valor_Total_Reforma               = Calcular_T008A_Valor_Total_Reforma(
        T008_Id, 
        T008A_Id,
        T008_Valor_Total_Preco_Sem_Desconto,
        T008_Valor_Total_Desconto,
        xD006_Deduz_ICMS_Desonerado_Valor_Total,
        T008_Valor_ICMS_Desonerado,
        T008_Valor_ICMS_Substituicao_Retencao,
        T008_Valor_FCP_ST,
        T008_Valor_Total_Frete,
        T008_Valor_Total_Seguro,
        T008_Valor_Total_Despesas_Acessorias,
        T008_Valor_Total_Imposto_Importacao,
        T008_Valor_IPI,
        T008_Valor_IPI_Devolucao,
        T008_Valor_PIS,
        T008_Valor_COFINS,
        xD006_Flag_Nao_Somar_IBS_CBS_Valor_Total_Reforma,
        T008A_Valor_IBS,
        T008A_Valor_CBS
    )
    WHERE T008_Id=xT008_Id AND ifnull(T007_Numero_Protocolo_Nfe,'')='';
END;
