CREATE FUNCTION `T005_Status_Pedido`(xStatus int(2),xTipo int(1)) RETURNS varchar(30)
BEGIN
    DECLARE xDescricao VARCHAR(25);
    SET xDescricao = '';
    IF xTipo=1 THEN
       IF xStatus=1 THEN
          SET xDescricao = 'Imprimir';
       END IF;
       IF xStatus=13 THEN
          SET xDescricao = 'Analise Com.';
       END IF;
        IF xStatus=14 THEN
          SET xDescricao = 'Aprovado';
       END IF;
        IF xStatus=15 THEN
          SET xDescricao = 'Negado';
       END IF;
       IF xStatus=2 THEN
          SET xDescricao = 'Separando';
       END IF;
       IF xStatus=24 THEN
          SET xDescricao = 'Etiquetas';
       END IF;
       IF xStatus=25 THEN
          SET xDescricao = 'NF CANCELADA';
       END IF;
       IF xStatus=10 THEN
          SET xDescricao = 'Conferindo';
       END IF;
       IF xStatus=11 THEN
          SET xDescricao = 'Pago';
       END IF;
       IF xStatus=3 THEN
          SET xDescricao = 'Emitir NF';
       END IF;
       IF xStatus=12 THEN
          SET xDescricao = 'Aguard.Entrega';
       END IF;
       IF xStatus=4 THEN
          SET xDescricao = 'Aguard.coleta';
       END IF;
       IF xStatus=5 THEN
          SET xDescricao = 'Coletado';
       END IF;
       IF xStatus=6 THEN
          SET xDescricao = 'Financeiro';
       END IF;
       IF xStatus=7 THEN
          SET xDescricao = 'Romaneio';
       END IF;
       IF xStatus=8 THEN
          SET xDescricao = 'Cancelado';
       END IF;
       IF xStatus=9 THEN
          SET xDescricao = 'Nao Liberado';
       END IF;
       IF xStatus=20 THEN
          SET xDescricao = 'Aguardando Liberacao';
       END IF;
       IF xStatus=21 THEN
          SET xDescricao = 'Aguardando Peças';
       END IF;
       IF xStatus=22 THEN
          SET xDescricao = 'Disponivel p/Faturar';
       END IF;
       IF xStatus=23 THEN
          SET xDescricao = 'Aguard.Prod/Compra';
       END IF;
       IF xStatus=26 THEN
          SET xDescricao = 'Vencido';
       END IF;
       IF xStatus=27 THEN
          SET xDescricao = 'Emitir CF';
       END IF;
       IF xStatus=28 THEN
          SET xDescricao = 'Venda Futura';
       END IF;
       IF xStatus=29 THEN
          SET xDescricao = 'Entrega Parcial';
       END IF;
       IF xStatus=30 THEN
          SET xDescricao = 'Aguard. Retorno Material';
       END IF;
    END IF;
    IF xTipo=2 THEN
       IF xStatus=1 THEN
          SET xDescricao = 'Imprima';
       END IF;
        IF xStatus=13 THEN
          SET xDescricao = 'Análise';
       END IF;
        IF xStatus=14 THEN
          SET xDescricao = 'Aprovado';
       END IF;
        IF xStatus=15 THEN
          SET xDescricao = 'Negado';
       END IF;
       IF xStatus=2 THEN
          SET xDescricao = 'Separando';
       END IF;
       IF xStatus=24 THEN
          SET xDescricao = 'Etiquetas';
       END IF;
       IF xStatus=25 THEN
          SET xDescricao = 'NF CANCELADA';
       END IF;
       IF xStatus=10 THEN
          SET xDescricao = 'Conferência';
       END IF;
       IF xStatus=11 THEN
          SET xDescricao = 'Pago';
       END IF;
       IF xStatus=3 THEN
          SET xDescricao = 'Emitir NF';
       END IF;
       IF xStatus=12 THEN
          SET xDescricao = 'Aguard.Entrega';
       END IF;
       IF xStatus=4 THEN
          SET xDescricao = 'Aguard.coleta';
       END IF;
       IF xStatus=5 THEN
          SET xDescricao = 'Finalizado';
       END IF;
       IF xStatus=6 THEN
          SET xDescricao = 'Financeiro';
       END IF;
       IF xStatus=7 THEN
          SET xDescricao = 'Romaneio';
       END IF;
       IF xStatus=8 THEN
          SET xDescricao = 'Cancelado';
       END IF;
       IF xStatus=9 THEN
          SET xDescricao = 'Nao Liberado';
       END IF;
       IF xStatus=20 THEN
          SET xDescricao = 'Aguardando Liberacao';
       END IF;
       IF xStatus=21 THEN
          SET xDescricao = 'Aguardando Pecas';
       END IF;
       IF xStatus=22 THEN
          SET xDescricao = 'Disponivel p/Faturar';
       END IF;
       IF xStatus=23 THEN
          SET xDescricao = 'Aguard.Prod/Compra';
       END IF;
       IF xStatus=26 THEN
          SET xDescricao = 'Vencido';
       END IF;
       IF xStatus=27 THEN
          SET xDescricao = 'Emitir CF';
       END IF;
       IF xStatus=28 THEN
          SET xDescricao = 'Venda Futura';
       END IF;
       IF xStatus=29 THEN
          SET xDescricao = 'Entrega Parcial';
       END IF;
    END IF;
    IF xDescricao = '' THEN
       SET xDescricao = 'Pendente';
    END IF;
    RETURN xDescricao;
END;

