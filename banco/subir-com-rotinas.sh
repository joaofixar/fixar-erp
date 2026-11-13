#!/bin/bash
# Sobe o MariaDB oficial. Volume vazio recebe as rotinas pelo
# docker-entrypoint-initdb.d. Volume que já tem dados não passa por essa
# pasta, então o mesmo passo roda aqui, depois que o servidor responde.
set -euo pipefail

marcar="/tmp/rotinas-aplicadas"
pid=0

encerrar() {
    if [ "$pid" -ne 0 ] && kill -0 "$pid" 2>/dev/null; then
        kill -TERM "$pid" 2>/dev/null || true
        wait "$pid" 2>/dev/null || true
    fi
    exit 0
}
trap encerrar TERM INT

ja_tinha_dados=0
if [ -d /var/lib/mysql/mysql ]; then
    ja_tinha_dados=1
fi

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
    if [ "$ja_tinha_dados" -eq 1 ]; then
        echo "Volume já tinha dados; aplicando rotinas ausentes"
        bash /docker-entrypoint-initdb.d/zz-rotinas.sh || true
    else
        echo "Volume novo; as rotinas entram pela inicialização"
    fi
    touch "$marcar"
fi

wait "$pid"
