CREATE PROCEDURE `T007_Gravar_Totalizacao_4_Reforma`(xT007_Id int(11))
BEGIN
    /* Variáveis do script
    =========================================== */
    DECLARE xT007A_Valor_Total_Base_CBS_IBS decimal(15, 2);
    DECLARE xT007A_Valor_Total_CBS decimal(15, 2);
    DECLARE xT007A_Valor_Total_IBS decimal(15, 2);
    DECLARE xT007A_Valor_Total_IBS_Municipal decimal(15, 2);
    DECLARE xT007A_Valor_Total_IBS_UF decimal(15, 2);
    DECLARE xT007A_Valor_Total_Reforma decimal(15, 2);
    DECLARE xT007A_Valor_Total_Diferimento_CBS decimal(15, 2);
    DECLARE xT007A_Valor_Total_Diferimento_IBS_UF decimal(15, 2);
    DECLARE xT007A_Valor_Total_Diferimento_IBS_Municipal decimal(15, 2);
    DECLARE xT007A_Valor_Total_IBS_Transferido decimal(15, 2);
    DECLARE xT007A_Valor_Total_CBS_Transferido decimal(15, 2);
    DECLARE xT007A_Valor_Total_IBS_Ajuste_Competencia decimal(15, 2);
    DECLARE xT007A_Valor_Total_CBS_Ajuste_Competencia decimal(15, 2);
    DECLARE xT007A_Valor_Total_IBS_Estornado decimal(15, 2);
    DECLARE xT007A_Valor_Total_CBS_Estornado decimal(15, 2);


    /* SELECTS
    ================================================== */
    -- Soma os itens da NFe (T008A) e grava na tabela estendida do cabeçalho (T007A)
    SELECT SUM(T008A_Valor_Base_CBS_IBS),
           SUM(T008A_Valor_CBS),
           SUM(T008A_Valor_IBS),
           SUM(T008A_Valor_IBS_Municipal),
           SUM(T008A_Valor_IBS_UF),
           SUM(T008A_Valor_Total_Reforma),
           SUM(T008A_Valor_Diferimento_CBS),
           SUM(T008A_Valor_Diferimento_IBS_UF),
           SUM(T008A_Valor_Diferimento_IBS_Municipal),
           IFNULL(SUM(T008A_Valor_IBS_Transferido), 0),
           IFNULL(SUM(T008A_Valor_CBS_Transferido), 0),
           IFNULL(SUM(T008A_Valor_IBS_Ajuste_Competencia), 0),
           IFNULL(SUM(T008A_Valor_CBS_Ajuste_Competencia), 0),
           IFNULL(SUM(T008A_Valor_IBS_Estornado), 0),
           IFNULL(SUM(T008A_Valor_CBS_Estornado), 0)
      INTO xT007A_Valor_Total_Base_CBS_IBS,
           xT007A_Valor_Total_CBS,
           xT007A_Valor_Total_IBS,
           xT007A_Valor_Total_IBS_Municipal,
           xT007A_Valor_Total_IBS_UF,
           xT007A_Valor_Total_Reforma,
           xT007A_Valor_Total_Diferimento_CBS,
           xT007A_Valor_Total_Diferimento_IBS_UF,
           xT007A_Valor_Total_Diferimento_IBS_Municipal,
           xT007A_Valor_Total_IBS_Transferido,
           xT007A_Valor_Total_CBS_Transferido,
           xT007A_Valor_Total_IBS_Ajuste_Competencia,
           xT007A_Valor_Total_CBS_Ajuste_Competencia,
           xT007A_Valor_Total_IBS_Estornado,
           xT007A_Valor_Total_CBS_Estornado
      FROM T008
 LEFT JOIN T008A ON T008A_T008_Id = T008_Id
     WHERE T008_T007_Id = xT007_Id;

    /* UPDATE dos campos da reforma
    ================================================== */
    UPDATE T007A
    LEFT JOIN T007 ON T007A_T007_Id = T007_Id
    SET T007A_Valor_Total_Base_CBS_IBS                  = xT007A_Valor_Total_Base_CBS_IBS,
        T007A_Valor_Total_CBS                           = xT007A_Valor_Total_CBS,
        T007A_Valor_Total_IBS                           = xT007A_Valor_Total_IBS,
        T007A_Valor_Total_IBS_Municipal                 = xT007A_Valor_Total_IBS_Municipal,
        T007A_Valor_Total_IBS_UF                        = xT007A_Valor_Total_IBS_UF,
        T007A_Valor_Total_Reforma                       = xT007A_Valor_Total_Reforma,
        T007A_Valor_Total_Diferimento_CBS              = xT007A_Valor_Total_Diferimento_CBS,
        T007A_Valor_Total_Diferimento_IBS_UF           = xT007A_Valor_Total_Diferimento_IBS_UF,
        T007A_Valor_Total_Diferimento_IBS_Municipal    = xT007A_Valor_Total_Diferimento_IBS_Municipal,
        -- Controles internos separados de gIBSCBS. O XSD não prevê totais de transferência/ajuste.
        T007A_Valor_Total_IBS_Transferido             = xT007A_Valor_Total_IBS_Transferido,
        T007A_Valor_Total_CBS_Transferido             = xT007A_Valor_Total_CBS_Transferido,
        T007A_Valor_Total_IBS_Ajuste_Competencia      = xT007A_Valor_Total_IBS_Ajuste_Competencia,
        T007A_Valor_Total_CBS_Ajuste_Competencia      = xT007A_Valor_Total_CBS_Ajuste_Competencia,
        T007A_Valor_Total_IBS_Estornado               = xT007A_Valor_Total_IBS_Estornado,
        T007A_Valor_Total_CBS_Estornado               = xT007A_Valor_Total_CBS_Estornado
    WHERE T007_Id=xT007_Id and T007_Flag_Calcular='S' AND T007_Flag_Cancelada!='S' AND T007_Flag_Inutilizado!='S' AND ifnull(T007_Numero_Protocolo_Nfe,'')='';
END;
