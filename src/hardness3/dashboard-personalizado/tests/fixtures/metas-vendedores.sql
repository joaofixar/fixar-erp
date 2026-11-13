SELECT m.SMT120_C007_Id AS vendedorId, m.SMT120_Data_Inicial AS inicio, m.SMT120_Data_Final AS fim,
m.SMT120_Valor_Meta AS meta, m.SMT120_Valor_Atingido AS atingido, m.SMT120_Percentual_Atingido AS percentual
FROM SMT120 m
WHERE m.SMT120_Data_Inicial = :metaInicio AND m.SMT120_Data_Final = :metaFim
AND (:vendedor = '' OR m.SMT120_C007_Id = :vendedor)
AND EXISTS (SELECT 1 FROM C019 vinculo WHERE vinculo.C019_C007_Id = m.SMT120_C007_Id AND vinculo.C019_C004_Id = :empresa)
