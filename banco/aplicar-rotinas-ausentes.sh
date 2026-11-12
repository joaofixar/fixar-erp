#!/bin/bash
# Cria na base as rotinas da lista do Hardness que ainda não existem.
# Não recria as que a fotografia já trouxe. Arquivo que falha fica listado
# e o passo segue. Roda de novo no fim para rotina que dependia de outra.

aplicar_rotinas_ausentes() {
    local lista="${ROTINA_LISTA:-/rotinas/lista.php}"
    local pasta="${ROTINA_SQL:-/rotinas/sql}"
    local banco="${MARIADB_DATABASE:-fixar}"
    local usuario="${MARIADB_ROTINA_USUARIO:-root}"
    local senha="${MARIADB_ROOT_PASSWORD:-}"
    local host="${MARIADB_HOST:-}"
    local host_arg=""
    local senha_arg=""
    local criadas=0
    local puladas=0
    local nomes=()
    local tipos=()
    local falhas=()
    local i nome tipo arquivo tmp status existe

    if [ ! -f "$lista" ] || [ ! -d "$pasta" ]; then
        echo "lista ou pasta de rotinas ausente (${lista}, ${pasta})" >&2
        return 1
    fi

    if [ -n "$host" ]; then
        host_arg="-h${host}"
    fi

    if [ -n "$senha" ] && mariadb -u"$usuario" $host_arg -p"$senha" "$banco" -e "SELECT 1" >/dev/null 2>&1; then
        senha_arg="-p${senha}"
    elif mariadb -u"$usuario" $host_arg "$banco" -e "SELECT 1" >/dev/null 2>&1; then
        senha_arg=""
    else
        echo "sem conexao com o banco ${banco}" >&2
        return 1
    fi

    # As rotinas do Hardness não declaram DETERMINISTIC. Com log binário o
    # MariaDB recusa a criação sem esta variável. Ela só importa na criação.
    mariadb -u"$usuario" $host_arg $senha_arg "$banco" -e "SET GLOBAL log_bin_trust_function_creators = 1" >/dev/null 2>&1 || true

    while IFS= read -r linha; do
        tipo=$(printf '%s' "$linha" | sed -n 's/.*Atualizar_Rotina([[:space:]]*"\([^"]*\)".*/\1/p')
        nome=$(printf '%s' "$linha" | sed -n 's/.*Atualizar_Rotina([[:space:]]*"[^"]*"[[:space:]]*,[[:space:]]*"\([^"]*\)".*/\1/p')
        if [ -z "$nome" ]; then
            continue
        fi
        case "$nome" in
            *[!A-Za-z0-9_]*) continue ;;
        esac
        nomes+=("$nome")
        tipos+=("$tipo")
    done <<EOF
$(grep -E 'Atualizar_Rotina\(' "$lista")
EOF

    aplicar_uma() {
        nome="$1"
        tipo="$2"
        if [ "$tipo" = "TRIGGER" ]; then
            existe=$(mariadb -u"$usuario" $host_arg $senha_arg "$banco" -N -B -e "SELECT COUNT(*) FROM information_schema.TRIGGERS WHERE TRIGGER_SCHEMA='${banco}' AND TRIGGER_NAME='${nome}'" 2>/dev/null) || existe=""
        else
            existe=$(mariadb -u"$usuario" $host_arg $senha_arg "$banco" -N -B -e "SELECT COUNT(*) FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA='${banco}' AND ROUTINE_NAME='${nome}'" 2>/dev/null) || existe=""
        fi
        if [ -z "$existe" ]; then
            falhas+=("${nome}: consulta de existencia falhou")
            return 1
        fi
        existe=$(printf '%s' "$existe" | tr -d '[:space:]')
        if [ "$existe" != "0" ]; then
            puladas=$((puladas + 1))
            return 0
        fi
        arquivo="${pasta}/${nome}.sql"
        if [ ! -f "$arquivo" ]; then
            falhas+=("${nome} (arquivo ausente)")
            return 1
        fi
        tmp=$(mktemp)
        {
            printf 'DELIMITER //\n'
            cat "$arquivo"
            printf '\n//\n'
        } > "$tmp"
        if mariadb -u"$usuario" $host_arg $senha_arg "$banco" < "$tmp" >/dev/null 2>"${tmp}.err"; then
            criadas=$((criadas + 1))
            rm -f "$tmp" "${tmp}.err"
            return 0
        fi
        status=$(tr '\n' ' ' < "${tmp}.err")
        falhas+=("${nome}: ${status}")
        rm -f "$tmp" "${tmp}.err"
        return 1
    }

    for i in "${!nomes[@]}"; do
        aplicar_uma "${nomes[$i]}" "${tipos[$i]}" || true
    done

    if [ "${#falhas[@]}" -gt 0 ]; then
        local primeira=("${falhas[@]}")
        local tentar item
        falhas=()
        for i in "${!nomes[@]}"; do
            tentar=0
            for item in "${primeira[@]}"; do
                case "$item" in
                    "${nomes[$i]}"|"${nomes[$i]}:"*|"${nomes[$i]} "*) tentar=1 ;;
                esac
            done
            if [ "$tentar" -eq 1 ]; then
                aplicar_uma "${nomes[$i]}" "${tipos[$i]}" || true
            fi
        done
    fi

    echo "criadas=${criadas} puladas=${puladas} falhas=${#falhas[@]}"
    if [ "${#falhas[@]}" -gt 0 ]; then
        printf '%s\n' "${falhas[@]}"
    fi
}

aplicar_rotinas_ausentes
