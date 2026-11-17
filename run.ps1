# Sobe a base local numa máquina Windows nova, sem precisar do Git Bash.
# Faz o mesmo que o run.sh: confere o que falta, instala as bibliotecas do Composer,
# grava a conexão local, constrói a imagem e sobe os contêineres. Pode rodar de novo.
#   powershell -ExecutionPolicy Bypass -File run.ps1
Set-Location $PSScriptRoot

function Falta($oQue) {
    Write-Host "Falta: $oQue" -ForegroundColor Red
    exit 1
}

# Comando nativo que falha não para o script sozinho no PowerShell 5.1.
function Executar {
    & $args[0] @($args | Select-Object -Skip 1)
    if ($LASTEXITCODE -ne 0) { throw "Falhou: $($args -join ' ')" }
}

if (-not $env:DOCKER_HOST) { $env:DOCKER_HOST = 'npipe:////./pipe/kiri' }

if (-not (Get-Command docker -ErrorAction SilentlyContinue)) { Falta 'o CLI do Docker. Instale o Kiri: https://docs.runkiri.com' }
docker version *> $null
if ($LASTEXITCODE -ne 0) { Falta 'o engine do Kiri respondendo. Abra o Kiri e rode de novo.' }
docker compose version *> $null
if ($LASTEXITCODE -ne 0) { Falta 'o docker compose.' }

$python = $null
foreach ($candidato in 'python', 'python3', 'py') {
    if (Get-Command $candidato -ErrorAction SilentlyContinue) {
        & $candidato -c 'import sys' *> $null
        if ($LASTEXITCODE -eq 0) { $python = $candidato; break }
    }
}
if (-not $python) { Falta 'o Python 3.' }

# O vendor/ fica fora do git; sem ele a tela interna responde 500.
$composerDir = Join-Path $PSScriptRoot 'src\hardness3\bibliotecas\composer'
if (-not (Test-Path (Join-Path $composerDir 'vendor\autoload.php'))) {
    Write-Host 'Instalando as bibliotecas do Composer (só na primeira vez)'
    # O composer.lock fixa as versões. As extensões são as do contêiner web, não as desta imagem.
    Executar docker run --rm -v "${composerDir}:/app" -w /app composer:2.8 install --no-interaction --no-progress --ignore-platform-reqs
}

Executar $python bin/preparar-base.py
# Sempre constrói: o cache deixa rápido e uma mudança no Dockerfile chega a quem já tinha a imagem.
Executar docker build --load -q -t fixar-erp-web ./docker/php
Write-Host 'Subindo a base. Na primeira vez o banco aplica as rotinas e leva alguns minutos.'
# A fotografia entra só com o volume vazio. Um volume já preenchido permanece.
Executar docker compose up -d --no-build --wait

Write-Host ''
Write-Host 'Entrar em http://127.0.0.1:8080/login'
Write-Host '  usuário: fixar'
Write-Host '  senha:   fixar-local'
