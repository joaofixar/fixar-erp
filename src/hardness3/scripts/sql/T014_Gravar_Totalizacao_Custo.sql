CREATE PROCEDURE `T014_Gravar_Totalizacao_Custo`(xT014_Id int(11))
BEGIN
    DECLARE xFlag_Substituicao_Tributaria VARCHAR(1);
    DECLARE xFlag_Deb_Crd_ICMS VARCHAR(1);
    DECLARE xFlag_Credita_IPI VARCHAR(1);
    DECLARE xT014_Quantidade DECIMAL(10,5);
    DECLARE xT014_Id_Duplicado INT(11);
    DECLARE xAliquota_ICMS_Interna DECIMAL(5,2);
    DECLARE xT014_Aliquota_ICMS DECIMAL(5,2) default '0.00';
    DECLARE xValorDIFAL DECIMAL(15,5) default '0.00000';
    DECLARE xAliquotaDIFAL DECIMAL(5,2);
    DECLARE xT014_Valor_Custo_Unitario DECIMAL(20,10);
    DECLARE xT014_Valor_Base_ICMS DECIMAL(10,2);
    DECLARE xD006_Flag_Somar_DIFAL_Custo VARCHAR(1);
    DECLARE xFlag_Deb_Crd_PIS VARCHAR(1);
    DECLARE xFlag_Deb_Crd_COFINS VARCHAR(1);
    DECLARE xT014A_Valor_Acrescimo_Custo DECIMAL(20,10);

    
    SELECT D006_Flag_Substituicao_Tributaria,
           D006_Flag_Deb_Crd_ICMS,
           D006_Flag_Credita_IPI,
           T014_Id_Duplicado,
           D006_Flag_Somar_DIFAL_Custo,
           T014_ICMS_Inter_Estadual, 
           T014_Aliquota_ICMS,
           T014_Valor_Custo_Unitario,
           T014_Valor_Base_ICMS,
           D006_Flag_Deb_Crd_PIS,
           D006_Flag_Deb_Crd_COFINS,
           IFNULL(T014A_Valor_Acrescimo_Custo,0)
      INTO xFlag_Substituicao_Tributaria,
           xFlag_Deb_Crd_ICMS,
           xFlag_Credita_IPI,
           xT014_Id_Duplicado,
           xD006_Flag_Somar_DIFAL_Custo,
           xAliquota_ICMS_Interna,
           xT014_Aliquota_ICMS,
           xT014_Valor_Custo_Unitario,
           xT014_Valor_Base_ICMS,
           xFlag_Deb_Crd_PIS,
           xFlag_Deb_Crd_COFINS,
           xT014A_Valor_Acrescimo_Custo
      FROM T014
 LEFT JOIN D006 ON D006_Id=T014_D006_Id
 LEFT JOIN T013 ON T013_Id=T014_T013_Id     
 LEFT JOIN D024 ON D024_Id=T013_D024_Id  
 LEFT JOIN T014A ON T014_Id = T014A_T014_Id 
     WHERE T014_Id=xT014_Id;

    -- O T014 pode ser duplicado (é usado para fazer a associação com as Ordem de Compras), neste caso, o calculo do T014_Valor_Custo_Final_Unitario deve ser a soma de todas as quantidades duplicadas (esta soma é a quantidade que chegou na nota)
    SELECT 
        SUM(T014_Quantidade) INTO xT014_Quantidade
    FROM 
        T014 
    WHERE 
        T014_Id=xT014_Id OR T014_Id_Duplicado=xT014_Id OR T014_Id=xT014_Id_Duplicado OR T014_Id_Duplicado=xT014_Id_Duplicado;

    /*SELECT D009_IPV_ICMS(T014_C004_Id, T013_D024_Id, T014_D009_Id, T014_D006_Id, D024_D018_Id, D024_Flag_Revenda_Consumidor, 0.00, 6, 2) AS Aliquota_ICMS, 
            T014_Aliquota_ICMS,
            T014_Valor_Custo_Unitario,
            T014_Valor_Base_ICMS
       INTO xAliquota_ICMS_Interna,
            xT014_Aliquota_ICMS,
            xT014_Valor_Custo_Unitario,
            xT014_Valor_Base_ICMS
       FROM T014
  LEFT JOIN T013 ON T013_Id=T014_T013_Id     
  LEFT JOIN D024 ON D024_Id=T013_D024_Id
      WHERE T014_Id=xT014_Id;*/
     
    UPDATE T014 SET 
           T014_Aliquota_ICMS_Diferenca_Aliquotas = IF(xD006_Flag_Somar_DIFAL_Custo='S', (T014_Aliquota_ICMS_Interno - (T014_Aliquota_ICMS_Interno * T014_Percentual_Reducao_ICMS/100)) - T014_Aliquota_ICMS, 0),                                             
           T014_Aliquota_ICMS_Diferenca_Aliquotas = IF(T014_Aliquota_ICMS_Diferenca_Aliquotas > 0, T014_Aliquota_ICMS_Diferenca_Aliquotas, 0),
           T014_Valor_ICMS_Diferenca_Aliquotas    = (T014_Aliquota_ICMS_Diferenca_Aliquotas/100) * xT014_Valor_Base_ICMS,
           T014_Valor_Custo_Final_Unitario        = ((((xT014_Quantidade*T014_Valor_Custo_Unitario) + 
                                                       IF(xFlag_Substituicao_Tributaria='S',T014_Valor_ICMS_Substituicao,0) + 
                                                       T014_Valor_Frete + 
                                                       IF(xFlag_Credita_IPI='N',T014_Valor_IPI,0) + 
                                                       T014_Valor_Seguro + 
                                                       T014_Valor_Despesas_Acessorias + 
                                                       T014_Valor_ICMS_Diferenca_Aliquotas) - 
                                                       T014_Valor_Total_Desconto) / xT014_Quantidade) +
                                                       xT014A_Valor_Acrescimo_Custo,
           T014_Valor_Total_Custo_Final           = T014_Valor_Custo_Final_Unitario * T014_Quantidade
    WHERE T014_Id=xT014_Id;

   UPDATE T014A
LEFT JOIN T014 ON T014_Id = T014A_T014_Id 
      SET T014A_Valor_Custo_Final_Liquido_Unitario = (T014_Valor_Total_Custo_Final - IF(xFlag_Deb_Crd_ICMS='S', T014_Valor_ICMS,0) - IF(xFlag_Deb_Crd_PIS = 'S', T014_Valor_PIS, 0) - IF(xFlag_Deb_Crd_COFINS = 'S', T014_Valor_COFINS, 0)) / xT014_Quantidade                                             
    WHERE T014A_T014_Id=xT014_Id;

END;

