# Start minimal API stack for local admin debugging (no full site nginx / front containers).
# Requires Docker Desktop (Linux engine) running — http://127.0.0.1:8888/health only works after this succeeds.
# If /health fails:  .\scripts\diagnose-debug-api.ps1
#
# From repo root:
#   .\scripts\start-debug-api.ps1

param(
	# Docker Compose env-file. Default points to local MySQL container (safe).
	[string] $EnvFile = "docker.debug.env",
	# Remove local MySQL data directory to start from a clean schema (recommended when migrations conflict).
	[switch] $ResetDb
)

$ErrorActionPreference = "Stop"
$RepoRoot = if ($PSScriptRoot) { (Resolve-Path (Join-Path $PSScriptRoot "..")).Path } else { (Get-Location).Path }
Set-Location -LiteralPath $RepoRoot

# Avoid NativeCommandException / stderr noise when Docker Engine is off ($ErrorActionPreference = Stop).
$nullOut = Join-Path $env:TEMP ("docker-null-" + [Guid]::NewGuid().ToString("N") + ".log")
$nullErr = Join-Path $env:TEMP ("docker-null-" + [Guid]::NewGuid().ToString("N") + ".log")
try {
	$dockerProbe = Start-Process -FilePath "docker" -ArgumentList @("info") -Wait -NoNewWindow -PassThru `
		-RedirectStandardOutput $nullOut -RedirectStandardError $nullErr
} catch {
	Remove-Item -LiteralPath $nullOut, $nullErr -Force -ErrorAction SilentlyContinue
	Write-Host ""
	Write-Host "docker CLI failed to start. Install Docker Desktop or add docker to PATH." -ForegroundColor Red
	Write-Host $_.Exception.Message -ForegroundColor DarkGray
	Write-Host ""
	exit 1
}
Remove-Item -LiteralPath $nullOut, $nullErr -Force -ErrorAction SilentlyContinue
if ($dockerProbe.ExitCode -ne 0) {
	Write-Host ""
	Write-Host "Docker Engine is not available (Docker Desktop not running or wrong context)." -ForegroundColor Red
	Write-Host "  1) Start Docker Desktop and wait until it says the engine is running." -ForegroundColor Yellow
	Write-Host "  2) Run:  docker context ls   (use desktop-linux / default)" -ForegroundColor Yellow
	Write-Host "  3) Retry this script." -ForegroundColor Yellow
	Write-Host ""
	exit 1
}

if (-not (Test-Path -LiteralPath (Join-Path $RepoRoot $EnvFile))) {
	Write-Host "Missing env file: $EnvFile" -ForegroundColor Red
	exit 1
}

if ($ResetDb -and ($EnvFile -ieq "docker.debug.env")) {
	Write-Host "Resetting local debug MySQL data (./data/mysql-debug)..." -ForegroundColor Yellow
	docker compose --env-file $EnvFile -f docker-compose.debug-api.yml down
	$mysqlDataDir = Join-Path $RepoRoot "data\mysql-debug"
	if (Test-Path -LiteralPath $mysqlDataDir) {
		Remove-Item -LiteralPath $mysqlDataDir -Recurse -Force
	}
}

Write-Host "Starting debug API stack (mysql, redis, backend, nginx on :8888)..." -ForegroundColor Cyan
docker compose --env-file $EnvFile -f docker-compose.debug-api.yml up -d --build
if ($LASTEXITCODE -ne 0) {
	exit $LASTEXITCODE
}

Write-Host "Waiting for MySQL to become ready..." -ForegroundColor Cyan
Start-Sleep -Seconds 12

# Host-mounted bootstrap/cache from a full composer install can list dev-only providers (e.g. Laravel Pail)
# while the production image vendor was built with --no-dev — artisan then fails before migrate.
$bootstrapCache = Join-Path $RepoRoot "back\bootstrap\cache"
if (Test-Path -LiteralPath $bootstrapCache) {
	Write-Host "Removing stale Laravel bootstrap/cache/*.php (must match image vendor)..." -ForegroundColor Cyan
	Get-ChildItem -LiteralPath $bootstrapCache -Filter "*.php" -File -ErrorAction SilentlyContinue | Remove-Item -Force
}

if ($EnvFile -ieq "docker.debug.env") {
	Write-Host "Running migrations (safe to repeat)..." -ForegroundColor Cyan
	docker compose --env-file $EnvFile -f docker-compose.debug-api.yml exec -T backend php artisan migrate --force
	if ($LASTEXITCODE -ne 0) {
		exit $LASTEXITCODE
	}

	Write-Host "Seeding local debug admin (idempotent)..." -ForegroundColor Cyan
	docker compose --env-file $EnvFile -f docker-compose.debug-api.yml exec -T backend php artisan db:seed --class=DebugLocalAdminSeeder --force

	Write-Host "Seeding mock data (users/roles/branches)..." -ForegroundColor Cyan
	docker compose --env-file $EnvFile -f docker-compose.debug-api.yml exec -T backend php artisan db:seed --class=DebugLocalMockSeeder --force

	Write-Host "Seeding mock orders (~20)..." -ForegroundColor Cyan
	docker compose --env-file $EnvFile -f docker-compose.debug-api.yml exec -T backend php artisan db:seed --class=DebugLocalOrdersSeeder --force
} else {
	Write-Host ""
	Write-Host "NOTE: Using custom env-file ($EnvFile)." -ForegroundColor Yellow
	Write-Host "For safety, this script WILL NOT run migrate/seed against non-default databases." -ForegroundColor Yellow
	Write-Host "If you really need that, run artisan commands manually on purpose." -ForegroundColor Yellow
	Write-Host ""
}

Write-Host ""
Write-Host "Health (nginx): http://127.0.0.1:8888/health   Laravel: http://127.0.0.1:8888/up" -ForegroundColor Green
try {
	$h = Invoke-WebRequest -Uri "http://127.0.0.1:8888/health" -UseBasicParsing -TimeoutSec 15
	Write-Host "Probe /health: $($h.StatusCode) $($h.Content.Trim())" -ForegroundColor $(if ($h.StatusCode -eq 200) { "Green" } else { "Yellow" })
} catch {
	Write-Host "Probe /health failed: $($_.Exception.Message)" -ForegroundColor Yellow
	Write-Host '  - Start Docker Desktop, then run this script again from repo root.' -ForegroundColor DarkGray
	Write-Host '  - If Docker is off, port 8888 is empty (browser connection errors).' -ForegroundColor DarkGray
	Write-Host "  - If Docker is on: wait ~30s for MySQL, then: docker compose --env-file $EnvFile -f docker-compose.debug-api.yml ps" -ForegroundColor DarkGray
	Write-Host '  - Diagnose:  .\scripts\diagnose-debug-api.ps1' -ForegroundColor DarkGray
}
Write-Host "API (raw):  http://127.0.0.1:8888/api/v1/admin/auth/login (POST JSON)" -ForegroundColor Green
Write-Host ""
Write-Host "Admin UI:  .\scripts\start-admin-dev.ps1   (or: cd front_admin, yarn dev)" -ForegroundColor Cyan
Write-Host "  http://127.0.0.1:5173/login" -ForegroundColor Green
Write-Host "  Login:  admin@local.debug  /  password" -ForegroundColor Green
Write-Host "  (also: manager@local.debug / password, user1@local.debug / password)" -ForegroundColor DarkGray
Write-Host "  (Vite proxies /api -> :8888)" -ForegroundColor DarkGray
Write-Host ""
Write-Host "Stop: docker compose -f docker-compose.debug-api.yml down" -ForegroundColor DarkGray
