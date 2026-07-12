# Why http://127.0.0.1:8888/health fails? Run from repo root:
#   .\scripts\diagnose-debug-api.ps1

$ErrorActionPreference = "Continue"
$RepoRoot = if ($PSScriptRoot) { (Resolve-Path (Join-Path $PSScriptRoot "..")).Path } else { (Get-Location).Path }
$EnvFile = "docker.debug.env"
$Compose = "docker-compose.debug-api.yml"

Write-Host ""
Write-Host '=== 1) Docker Engine ===' -ForegroundColor Cyan
$nullOut = Join-Path $env:TEMP ("docker-info-" + [Guid]::NewGuid().ToString("N") + ".out.log")
$nullErr = Join-Path $env:TEMP ("docker-info-" + [Guid]::NewGuid().ToString("N") + ".err.log")
try {
	$p = Start-Process -FilePath "docker" -ArgumentList @("info") -Wait -NoNewWindow -PassThru `
		-RedirectStandardOutput $nullOut -RedirectStandardError $nullErr
} finally {
	Remove-Item -LiteralPath $nullOut, $nullErr -Force -ErrorAction SilentlyContinue
}
if (-not $p -or $p.ExitCode -ne 0) {
	Write-Host "FAIL: Docker Engine is not running (or wrong context)." -ForegroundColor Red
	Write-Host "  Start Docker Desktop and wait until it is fully started, then retry." -ForegroundColor Yellow
	Write-Host '  While Docker is off, nothing listens on port 8888 - /health will always fail.' -ForegroundColor Yellow
	Write-Host "  Try:  docker context ls   (pick desktop-linux / default)" -ForegroundColor DarkGray
	Write-Host ""
	exit 1
}
Write-Host "OK: Docker responds." -ForegroundColor Green

Set-Location -LiteralPath $RepoRoot
Write-Host ""
Write-Host '=== 2) Debug stack containers (project goldtocash-debug-api) ===' -ForegroundColor Cyan
if (-not (Test-Path -LiteralPath (Join-Path $RepoRoot $EnvFile))) {
	Write-Host "Missing $EnvFile in repo root." -ForegroundColor Red
	exit 1
}
& docker compose --env-file $EnvFile -f $Compose ps -a 2>&1

Write-Host ""
Write-Host '=== 3) Port 8888 (TCP) ===' -ForegroundColor Cyan
$t = Test-NetConnection -ComputerName 127.0.0.1 -Port 8888 -WarningAction SilentlyContinue
if ($t.TcpTestSucceeded) {
	Write-Host "OK: Something accepts TCP on 127.0.0.1:8888." -ForegroundColor Green
} else {
	Write-Host "FAIL: Nothing is listening on 127.0.0.1:8888." -ForegroundColor Red
	Write-Host "  Start the stack:  .\scripts\start-debug-api.ps1" -ForegroundColor Yellow
	Write-Host "  Or:  docker compose --env-file $EnvFile -f $Compose up -d" -ForegroundColor Yellow
}

Write-Host ""
Write-Host '=== 4) HTTP /health ===' -ForegroundColor Cyan
try {
	$h = Invoke-WebRequest -Uri "http://127.0.0.1:8888/health" -UseBasicParsing -TimeoutSec 10
	Write-Host "OK: $($h.StatusCode) $($h.Content.Trim())" -ForegroundColor Green
} catch {
	Write-Host "FAIL: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""
