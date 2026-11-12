CREATE PROCEDURE `T234_Gravar_Totalizacao`(xT234_Id int(11))
BEGIN
    DECLARE xT234_Horas_Trabalho time;
    DECLARE xT234_Horas_ReTrabalho time;

    SELECT time_format( SEC_TO_TIME( SUM( TIME_TO_SEC( T274_Total_Horas_Trabalhadas ) ) ),'%H:%i:%s') as Soma_Horas
      INTO xT234_Horas_Trabalho
      FROM T274
    WHERE T274_T234_Id=xT234_Id;
    IF xT234_Horas_Trabalho IS NULL THEN
       SET xT234_Horas_Trabalho = "00:00:00";
    END IF;

   SELECT time_format( SEC_TO_TIME( SUM( TIME_TO_SEC( T234_Horas_Trabalho ) ) ),'%H:%i:%s') as Soma_Horas_Retrabalho
      INTO xT234_Horas_ReTrabalho
      FROM T234
    WHERE T234_Chamado_Origem_Id=xT234_Id;
    IF xT234_Horas_ReTrabalho IS NULL THEN
       SET xT234_Horas_ReTrabalho = "00:00:00";
    END IF;

    UPDATE T234 SET T234_Horas_Trabalho = xT234_Horas_Trabalho, T234_Horas_ReTrabalho = xT234_Horas_ReTrabalho WHERE T234_Id=xT234_Id;
END
