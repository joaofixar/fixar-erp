CREATE TRIGGER `T238A_trigger_apos_alteracao` AFTER UPDATE ON `T238A` FOR EACH ROW
BEGIN
  UPDATE T006A
    LEFT JOIN T006             ON (T006_Id          = T006A_T006_Id)
    LEFT JOIN T238 AS T238_NEW ON (T238_NEW.T238_Id = NEW.T238A_T238_Id)
  SET
    T006A_Quantidade_Conversao_Separacao = (SELECT SUM(T238A.T238A_Quantidade_Conversao_Separacao)
                                              FROM T238A, T238
                                             WHERE T238.T238_Id      = T238A.T238A_T238_Id
                                               AND T238.T238_T006_Id = T238_NEW.T238_T006_Id)
  WHERE T006_Id = T238_NEW.T238_T006_Id;
END;