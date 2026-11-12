CREATE TRIGGER T020_trigger_atualizar_extras AFTER INSERT ON `T020` FOR EACH ROW 
BEGIN 
  DECLARE xT020_Valor_Juros DECIMAL(19,2); 
  DECLARE xT020_Valor_Desconto DECIMAL(19,2); 
  SELECT SUM(T020_Valor) INTO xT020_Valor_Juros FROM T020 WHERE T020_T002_Id=NEW.T020_T002_Id AND T020_Flag_Deb_Cred='C'; 
  SELECT SUM(T020_Valor) INTO xT020_Valor_Desconto FROM T020 WHERE T020_T002_Id=NEW.T020_T002_Id AND (T020_Flag_Deb_Cred='D' OR T020_Flag_Deb_Cred='T'); 
  IF xT020_Valor_Juros IS NULL THEN 
     SET xT020_Valor_Juros=0; 
  END IF; 
  IF xT020_Valor_Desconto IS NULL THEN 
     SET xT020_Valor_Desconto=0; 
  END IF; 
  UPDATE T002 SET T002_Valor_Juros=xT020_Valor_Juros, 
                  T002_Valor_Desconto=xT020_Valor_Desconto, 
                  T002_Valor_Total=T002_Valor_Duplicata+xT020_Valor_Juros-xT020_Valor_Desconto 
         WHERE T002_Id=NEW.T020_T002_Id; 
END;
