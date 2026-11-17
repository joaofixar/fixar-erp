#!/bin/bash
# Sobe o MariaDB. Se a empresa ainda não está no volume, carrega a
# fotografia e em seguida as rotinas que a lista do Hardness pede.
set -euo pipefail

marcar="/tmp/rotinas-aplicadas"
pid=0
banco="${MARIADB_DATABASE:-fixar}"

encerrar() {
    if [ "$pid" -ne 0 ] && kill -0 "$pid" 2>/dev/null; then
        kill -TERM "$pid" 2>/dev/null || true
        wait "$pid" 2>/dev/null || true
    fi
    exit 0
}
trap encerrar TERM INT

/usr/local/bin/docker-entrypoint.sh "$@" &
pid=$!

servidor_final() {
    local cmd
    cmd=$(tr '\0' ' ' < "/proc/${pid}/cmdline" 2>/dev/null || true)
    case "$cmd" in
        *mariadbd*|*mysqld*) return 0 ;;
    esac
    return 1
}

pronto=0
while kill -0 "$pid" 2>/dev/null; do
    if servidor_final && mariadb --protocol=socket -uroot -hlocalhost -p"${MARIADB_ROOT_PASSWORD:-}" -e "SELECT 1" >/dev/null 2>&1; then
        pronto=1
        break
    fi
    sleep 2
done

if [ "$pronto" -eq 1 ]; then
    existe=$(mariadb --protocol=socket -uroot -hlocalhost -p"${MARIADB_ROOT_PASSWORD:-}" -N -B -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='${banco}' AND table_name='C004'")
    existe=$(printf '%s' "$existe" | tr -d '[:space:]')
    if [ "$existe" = "0" ]; then
        echo "C004 ausente; carregando a fotografia"
        mariadb --protocol=socket -uroot -hlocalhost -p"${MARIADB_ROOT_PASSWORD:-}" "$banco" < /semente/fotografia.sql
    else
        echo "C004 presente; a fotografia fica como está"
    fi
    echo "Aplicando rotinas ausentes"
    bash /usr/local/bin/aplicar-rotinas-ausentes.sh
    touch "$marcar"
fi

wait "$pid"
