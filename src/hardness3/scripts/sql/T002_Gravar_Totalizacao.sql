CREATE PROCEDURE `T002_Gravar_Totalizacao`(xT002_Id int(11))
BEGIN
     UPDATE T002
        SET T002_Valor_Juros=(select sum(T020_Valor) from T020 where T020_T002_Id=T002_Id and T020_Flag_Deb_Cred='C' group by T020_T002_Id),
            T002_Valor_Desconto=(select sum(T020_Valor) from T020 where T020_T002_Id=T002_Id and T020_Flag_Deb_Cred='D' group by T020_T002_Id),
            T002_Valor_Taxas=(select sum(T020_Valor) from T020 where T020_T002_Id=T002_Id and T020_Flag_Deb_Cred='T' group by T020_T002_Id),
            T002_Valor_Recebido=(select sum(T019_Valor_Lancamento) from T019 where T019_T002_Id=T002_Id),
            T002_Valor_Total=ifnull(T002_Valor_Duplicata,0)+ifnull(T002_Valor_Juros,0)-ifnull(T002_Valor_Desconto,0)-ifnull(T002_Valor_Taxas,0),
            T002_Valor_Saldo=ifnull(T002_Valor_Duplicata,0)+ifnull(T002_Valor_Juros,0)-ifnull(T002_Valor_Desconto,0)-ifnull(T002_Valor_Taxas,0)-ifnull(T002_Valor_Recebido,0),
            T002_Valor_Total_Cheques=(select sum(T246_Valor) from T246 Where T246_T002_Id=T002_Id)
      WHERE T002_Id=xT002_Id;
END;
