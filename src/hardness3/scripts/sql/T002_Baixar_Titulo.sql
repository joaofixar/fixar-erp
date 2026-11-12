CREATE PROCEDURE `T002_Baixar_Titulo`(xT002_Id int(11),xData date)
BEGIN
      DECLARE xT002_Valor_Saldo decimal(10,2);
      DECLARE xT002_Data_Recebimento date;

      UPDATE T002
         SET T002_Valor_Juros    = (select sum(T020_Valor) from T020 where T020_T002_Id=T002_Id and T020_Flag_Deb_Cred='C' group by T020_T002_Id),
             T002_Valor_Desconto = (select sum(T020_Valor) from T020 where T020_T002_Id=T002_Id and T020_Flag_Deb_Cred='D' group by T020_T002_Id),
             T002_Valor_Recebido = (select sum(T019_Valor_Lancamento) from T019 where T019_T002_Id=T002_Id and T019_Flag_Excluido!='S'),
             T002_Valor_Total    = ifnull(T002_Valor_Duplicata,0)+ifnull(T002_Valor_Juros,0)-ifnull(T002_Valor_Desconto,0),
             T002_Valor_Saldo    = ifnull(T002_Valor_Duplicata,0)+ifnull(T002_Valor_Juros,0)-ifnull(T002_Valor_Desconto,0)-ifnull(T002_Valor_Recebido,0)
       WHERE T002_Id=xT002_Id;

      SELECT T002_Valor_Saldo,
             T002_Data_Recebimento
        INTO xT002_Valor_Saldo,
             xT002_Data_Recebimento
        FROM T002
       WHERE T002_Id=xT002_Id;

      IF xT002_Valor_Saldo<=0 THEN
         IF xT002_Data_Recebimento='0000-00-00' THEN
            SELECT T019_Data_Lancamento 
              INTO xT002_Data_Recebimento 
              FROM T019 
             WHERE T019_T002_Id=xT002_Id 
          ORDER BY T019_Id DESC LIMIT 1;

            IF (xT002_Data_Recebimento = '0000-00-00') THEN
               SET xT002_Data_Recebimento = xData;
            END IF; 

            UPDATE T002
               SET T002_Data_Recebimento = xT002_Data_Recebimento,
                   T002_Data_Baixa       = xData
             WHERE T002_Id=xT002_Id; 

         END IF;  
      ELSE      
         UPDATE T002
            SET T002_Data_Recebimento = '0000-00-00',
                T002_Data_Baixa       = '0000-00-00'
          WHERE T002_Id=xT002_Id;

      END IF;
END;
