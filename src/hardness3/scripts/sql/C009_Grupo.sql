CREATE FUNCTION `C009_Grupo`(xGrupo varchar(10)) RETURNS varchar(30) CHARSET utf8
BEGIN
    DECLARE xDescricao VARCHAR(25);
       IF xGrupo=99 THEN
          SET xDescricao = 'Acessos diversos';
       END IF;
       IF xGrupo=2 THEN
          SET xDescricao = 'Cadastros';
       END IF;
       IF xGrupo=3 THEN
          SET xDescricao = 'Vendas';
       END IF;
       IF xGrupo=4 THEN
          SET xDescricao = 'Financeiro';
       END IF;
       IF xGrupo=5 THEN
          SET xDescricao = 'Estoque';
       END IF;
       IF xGrupo=6 THEN
          SET xDescricao = 'Relatórios';
       END IF;
       IF xGrupo=7 THEN
          SET xDescricao = 'Fiscal';
       END IF;
       IF xGrupo=8 THEN
          SET xDescricao = 'Configurações';
       END IF;
       IF xGrupo=9 THEN
          SET xDescricao = 'Site';
       END IF;
       IF xGrupo=10 THEN
          SET xDescricao = 'NF-e';
       END IF;
       IF xGrupo=11 THEN
          SET xDescricao = 'Importação';
       END IF;
    RETURN xDescricao;
END;

