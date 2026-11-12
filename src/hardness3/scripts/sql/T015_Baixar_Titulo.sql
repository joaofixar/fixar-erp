CREATE PROCEDURE `T015_Baixar_Titulo`(xT015_Id int(11),xData date)
BEGIN
      DECLARE xT015_Valor_Saldo decimal(10,2);
      DECLARE xT015_Data_Pagamento date;

      UPDATE T015
         SET T015_Valor_Comissao = T015_Valor_Base_Comissao*(T015_Percentual_Comissao/100),
             T015_Valor_Juros    = (select sum(T033_Valor) from T033 where T033_T015_Id=T015_Id and T033_Flag_Deb_Cred='C' group by T033_T015_Id),
             T015_Valor_Desconto = (select sum(T033_Valor) from T033 where T033_T015_Id=T015_Id and T033_Flag_Deb_Cred='D' group by T033_T015_Id),
             T015_Valor_Pago     = (select sum(T019_Valor_Lancamento*-1) from T019 where T019_T015_Id=T015_Id and T019_Flag_Excluido!='S'),
             T015_Valor_Total    = ifnull(T015_Valor_Duplicata,0)+ifnull(T015_Valor_Juros,0)-ifnull(T015_Valor_Desconto,0),
             T015_Valor_Saldo    = ifnull(T015_Valor_Duplicata,0)+ifnull(T015_Valor_Juros,0)-ifnull(T015_Valor_Desconto,0)-ifnull(T015_Valor_Pago,0)
       WHERE T015_Id = xT015_Id;

      SELECT T015_Valor_Saldo,
             T015_Data_Pagamento
        INTO xT015_Valor_Saldo,
             xT015_Data_Pagamento
        FROM T015
       WHERE T015_Id = xT015_Id;

      IF xT015_Valor_Saldo <= 0 THEN
         IF xT015_Data_Pagamento = '0000-00-00' THEN
            SELECT T019_Data_Lancamento 
              INTO xT015_Data_Pagamento 
              FROM T019 
             WHERE T019_T015_Id = xT015_Id 
          ORDER BY T019_Id DESC LIMIT 1;

            IF (xT015_Data_Pagamento = '0000-00-00') THEN
               SET xT015_Data_Pagamento = xData;
            END IF;   

            UPDATE T015
               SET T015_Data_Pagamento = xT015_Data_Pagamento,
                   T015_Data_Baixa     = xData
             WHERE T015_Id = xT015_Id; 
         END IF;
      ELSE
         UPDATE T015
            SET T015_Data_Pagamento = '0000-00-00',
                T015_Data_Baixa     = '0000-00-00'
          WHERE T015_Id = xT015_Id;
      END IF;
END;
