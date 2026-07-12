# Full local stack: site + API + admin (Docker) on 9080/9082/9083 — uses docker.debug.env + ./data/mysql-local-full only (no prod).
# Requires Docker Desktop.
#
# From repo root:
#   .\scripts\start-local-full.ps1
#   .\scripts\start-local-full.ps1 -ResetDb

param(
	[string] $EnvFile = "docker.debug.env",
	[switch] $ResetDb
)

$ErrorActionPreference = "Stop"
$RepoRoot = if ($PSScriptRoot) { (Resolve-Path (Join-Path $PSScriptRoot "..")).Path } else { (Get-Location).Path }
Set-Location -LiteralPath $RepoRoot

$ComposeFile = "docker-compose.local-full.yml"
$nullOut = Join-Path $env:TEMP ("docker-null-" + [Guid]::NewGuid().ToString("N") + ".log")
$nullErr = Join-Path $env:TEMP ("docker-null-" + [Guid]::NewGuid().ToString("N") + ".log")
try {
	$dockerProbe = Start-Process -FilePath "docker" -ArgumentList @("info") -Wait -NoNewWindow -PassThru `
		-RedirectStandardOutput $nullOut -RedirectStandardError $nullErr
} catch {
	Remove-Item -LiteralPath $nullOut, $nullErr -Force -ErrorAction SilentlyContinue
	Write-Host "docker CLI failed. Install/start Docker Desktop." -ForegroundColor Red
	exit 1
}
Remove-Item -LiteralPath $nullOut, $nullErr -Force -ErrorAction SilentlyContinue
if ($dockerProbe.ExitCode -ne 0) {
	Write-Host "Docker Engine is not available. Start Docker Desktop and retry." -ForegroundColor Red
	exit 1
}

if (-not (Test-Path -LiteralPath (Join-Path $RepoRoot $EnvFile))) {
	Write-Host "Missing env file: $EnvFile" -ForegroundColor Red
	exit 1
}

if ($ResetDb) {
	Write-Host "Resetting local MySQL data (./data/mysql-local-full)..." -ForegroundColor Yellow
	docker compose --env-file $EnvFile -f $ComposeFile down
	$mysqlDataDir = Join-Path $RepoRoot "data\mysql-local-full"
	if (Test-Path -LiteralPath $mysqlDataDir) {
		Remove-Item -LiteralPath $mysqlDataDir -Recurse -Force
	}
}

Write-Host "Starting full local stack (project goldtocash-local, ports 9080/9082/9083)..." -ForegroundColor Cyan
docker compose --env-file $EnvFile -f $ComposeFile up -d --build
if ($LASTEXITCODE -ne 0) {
	exit $LASTEXITCODE
}

Write-Host "Waiting for MySQL..." -ForegroundColor Cyan
Start-Sleep -Seconds 15

$bootstrapCache = Join-Path $RepoRoot "back\bootstrap\cache"
if (Test-Path -LiteralPath $bootstrapCache) {
	Write-Host "Clearing Laravel bootstrap/cache/*.php (match image vendor)..." -ForegroundColor Cyan
	Get-ChildItem -LiteralPath $bootstrapCache -Filter "*.php" -File -ErrorAction SilentlyContinue | Remove-Item -Force
}

Write-Host "Running migrations..." -ForegroundColor Cyan
docker compose --env-file $EnvFile -f $ComposeFile exec -T backend php artisan migrate --force
if ($LASTEXITCODE -ne 0) {
	exit $LASTEXITCODE
}

Write-Host "Seeding debug admin + mock data..." -ForegroundColor Cyan
docker compose --env-file $EnvFile -f $ComposeFile exec -T backend php artisan db:seed --class=DebugLocalAdminSeeder --force
docker compose --env-file $EnvFile -f $ComposeFile exec -T backend php artisan db:seed --class=DebugLocalMockSeeder --force
docker compose --env-file $EnvFile -f $ComposeFile exec -T backend php artisan db:seed --class=DebugLocalOrdersSeeder --force

Write-Host ""
Write-Host "Site + API:   http://127.0.0.1:9080/" -ForegroundColor Green
Write-Host "Admin (Vite): http://127.0.0.1:9082/login" -ForegroundColor Green
Write-Host "Adminer:      http://127.0.0.1:9083/  (server: mysql, user/pass from docker.debug.env)" -ForegroundColor Green
Write-Host "Login:        admin@local.debug / password" -ForegroundColor Green
Write-Host ""
Write-Host "Health:       http://127.0.0.1:9080/health" -ForegroundColor Cyan
Write-Host "Laravel up:   http://127.0.0.1:9080/up" -ForegroundColor Cyan
try {
	$h = Invoke-WebRequest -Uri "http://127.0.0.1:9080/health" -UseBasicParsing -TimeoutSec 20
	Write-Host "Probe /health: $($h.StatusCode) $($h.Content.Trim())" -ForegroundColor Green
} catch {
	Write-Host "Probe /health failed (wait ~30s, then retry URL or check logs): $($_.Exception.Message)" -ForegroundColor Yellow
}
Write-Host ""
Write-Host "Stop: docker compose --env-file $EnvFile -f $ComposeFile down" -ForegroundColor DarkGray
