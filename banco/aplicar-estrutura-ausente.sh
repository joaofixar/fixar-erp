#!/bin/bash
# Cria colunas e índices que o atualizacaoBase.php pede e a base ainda não tem.
# Não grava configuração e não executa o resto desse script. Tabela que não
# existe fica de fora. Uma coluna que falha fica listada e o passo segue.
#
# ESTRUTURA_LISTAR=1 imprime os pedidos e não fala com o banco.

aplicar_estrutura_ausente() {
    local arquivo="${ESTRUTURA_ARQUIVO:-/rotinas/atualizacaoBase.php}"
    local leitor="${ESTRUTURA_LEITOR:-}"
    local banco="${MARIADB_DATABASE:-fixar}"
    local usuario="${MARIADB_ROTINA_USUARIO:-root}"
    local senha="${MARIADB_ROOT_PASSWORD:-}"
    local host="${MARIADB_HOST:-}"
    local host_arg=""
    local senha_arg=""
    local lista_tmp
    local criadas=0
    local indices_criados=0
    local puladas=0
    local sem_tabela=0
    local falhas=()

    if [ -z "$leitor" ]; then
        leitor="$(cd "$(dirname "$0")" && pwd)/ler-estrutura.awk"
    fi
    if [ ! -f "$arquivo" ] || [ ! -f "$leitor" ]; then
        echo "script de estrutura ausente (${arquivo}, ${leitor})" >&2
        return 1
    fi
    case "$banco" in
        *[!A-Za-z0-9_]*)
            echo "nome de banco recusado" >&2
            return 1
            ;;
    esac

    lista_tmp=$(mktemp)
    if ! awk -f "$leitor" "$arquivo" >"$lista_tmp"; then
        rm -f "$lista_tmp"
        echo "não leu a estrutura" >&2
        return 1
    fi
    if [ "${ESTRUTURA_LISTAR:-}" = "1" ]; then
        cat "$lista_tmp"
        rm -f "$lista_tmp"
        return 0
    fi

    if [ -n "$host" ]; then
        host_arg="-h${host}"
    fi
    if [ -n "$senha" ] && mariadb -u"$usuario" $host_arg -p"$senha" "$banco" -e "SELECT 1" >/dev/null 2>&1; then
        senha_arg="-p${senha}"
    elif mariadb -u"$usuario" $host_arg "$banco" -e "SELECT 1" >/dev/null 2>&1; then
        senha_arg=""
    else
        rm -f "$lista_tmp"
        echo "sem conexao com o banco ${banco}" >&2
        return 1
    fi

    declare -A tem_tabela=()
    declare -A tem_coluna=()
    declare -A tem_indice=()
    local tabela coluna
    while IFS=$'\t' read -r tabela coluna; do
        [ -n "$tabela" ] || continue
        tem_tabela["$tabela"]=1
        tem_coluna["${tabela}.${coluna}"]=1
    done < <(mariadb -u"$usuario" $host_arg $senha_arg "$banco" -N -B -e "SELECT TABLE_NAME, COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA='${banco}'")
    while IFS=$'\t' read -r tabela coluna; do
        [ -n "$tabela" ] || continue
        tem_indice["${tabela}.${coluna}"]=1
    done < <(mariadb -u"$usuario" $host_arg $senha_arg "$banco" -N -B -e "SELECT TABLE_NAME, INDEX_NAME FROM information_schema.STATISTICS WHERE TABLE_SCHEMA='${banco}'")

    local tipo nome campos modo sql tmp erro falta resto lista campo
    while IFS=$'\t' read -r tipo tabela nome campos modo; do
        [ -n "$tipo" ] || continue
        if [ -z "${tem_tabela[$tabela]:-}" ]; then
            sem_tabela=$((sem_tabela + 1))
            continue
        fi
        if [ "$tipo" = "coluna" ]; then
            # No pedido de coluna, o tipo ocupa o quarto campo.
            sql=$campos
            if [ -n "${tem_coluna[${tabela}.${nome}]:-}" ]; then
                puladas=$((puladas + 1))
                continue
            fi
            tmp=$(mktemp)
            printf 'ALTER TABLE `%s` ADD `%s` %s;\n' "$tabela" "$nome" "$sql" >"$tmp"
            if mariadb -u"$usuario" $host_arg $senha_arg "$banco" <"$tmp" >/dev/null 2>"${tmp}.err"; then
                criadas=$((criadas + 1))
                tem_coluna["${tabela}.${nome}"]=1
                rm -f "$tmp" "${tmp}.err"
            else
                erro=$(tr '\n' ' ' <"${tmp}.err")
                falhas+=("${tabela}.${nome}: ${erro}")
                rm -f "$tmp" "${tmp}.err"
            fi
        elif [ "$tipo" = "indice" ]; then
            if [ -n "${tem_indice[${tabela}.${nome}]:-}" ]; then
                puladas=$((puladas + 1))
                continue
            fi
            falta=0
            resto=$campos
            lista=""
            while [ -n "$resto" ]; do
                campo=${resto%%,*}
                resto=${resto#"$campo"}
                resto=${resto#,}
                if [ -z "${tem_coluna[${tabela}.${campo}]:-}" ]; then
                    falta=1
                fi
                if [ -n "$lista" ]; then
                    lista="${lista}, "
                fi
                lista="${lista}\`${campo}\`"
            done
            if [ "$falta" -eq 1 ]; then
                falhas+=("${tabela}.${nome}: coluna do indice ausente")
                continue
            fi
            tmp=$(mktemp)
            if [ "$modo" = "UNIQUE" ]; then
                printf 'ALTER TABLE `%s` ADD UNIQUE INDEX `%s` (%s);\n' "$tabela" "$nome" "$lista" >"$tmp"
            else
                printf 'ALTER TABLE `%s` ADD INDEX `%s` (%s);\n' "$tabela" "$nome" "$lista" >"$tmp"
            fi
            if mariadb -u"$usuario" $host_arg $senha_arg "$banco" <"$tmp" >/dev/null 2>"${tmp}.err"; then
                indices_criados=$((indices_criados + 1))
                tem_indice["${tabela}.${nome}"]=1
                rm -f "$tmp" "${tmp}.err"
            else
                erro=$(tr '\n' ' ' <"${tmp}.err")
                falhas+=("${tabela}.${nome}: ${erro}")
                rm -f "$tmp" "${tmp}.err"
            fi
        fi
    done <"$lista_tmp"
    rm -f "$lista_tmp"

    echo "colunas=${criadas} indices=${indices_criados} puladas=${puladas} sem_tabela=${sem_tabela} falhas=${#falhas[@]}"
    if [ "${#falhas[@]}" -gt 0 ]; then
        printf '%s\n' "${falhas[@]}"
    fi
}

aplicar_estrutura_ausente
