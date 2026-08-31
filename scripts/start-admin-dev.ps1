# Set dev mode (if needed), start local API when required, run admin Vite.
#
#   .\scripts\start-admin-dev.ps1
#   .\scripts\start-admin-dev.ps1 -Mode local-api
#   .\scripts\start-admin-dev.ps1 -SkipDocker

param(
	[ValidateSet('local-api', 'local-full', 'production')]
	[string] $Mode,
	[switch] $SkipDocker
)

$ErrorActionPreference = 'Stop'
$RepoRoot = if ($PSScriptRoot) { (Resolve-Path (Join-Path $PSScriptRoot '..')).Path } else { (Get-Location).Path }
Set-Location -LiteralPath $RepoRoot

$ModeFile = Join-Path $RepoRoot '.dev-mode'
if ($Mode) {
	& (Join-Path $RepoRoot 'scripts\set-dev-mode.ps1') -Mode $Mode
} elseif (-not (Test-Path -LiteralPath $ModeFile)) {
	& (Join-Path $RepoRoot 'scripts\set-dev-mode.ps1') -Mode local-api
}

$activeMode = (Get-Content -LiteralPath $ModeFile -Raw).Trim()

if (-not $SkipDocker) {
	switch ($activeMode) {
		'local-api' {
			$healthOk = $false
			try {
				$h = Invoke-WebRequest -Uri 'http://127.0.0.1:8888/health' -UseBasicParsing -TimeoutSec 3
				$healthOk = ($h.StatusCode -eq 200)
			} catch { }

			if (-not $healthOk) {
				Write-Host 'Local API not up — starting debug stack...' -ForegroundColor Cyan
				& (Join-Path $RepoRoot 'scripts\start-debug-api.ps1')
			} else {
				Write-Host 'Local API already running on :8888' -ForegroundColor Green
			}
		}
		'local-full' {
			$healthOk = $false
			try {
				$h = Invoke-WebRequest -Uri 'http://127.0.0.1:9080/health' -UseBasicParsing -TimeoutSec 3
				$healthOk = ($h.StatusCode -eq 200)
			} catch { }

			if (-not $healthOk) {
				Write-Host 'Local full stack not up — starting...' -ForegroundColor Cyan
				& (Join-Path $RepoRoot 'scripts\start-local-full.ps1')
				Write-Host ''
				Write-Host 'Admin in Docker: http://127.0.0.1:9082/login' -ForegroundColor Green
				Write-Host 'Or continue below for Vite on :5173 with proxy to :9080.' -ForegroundColor DarkGray
			} else {
				Write-Host 'Local full stack already running on :9080' -ForegroundColor Green
			}
		}
		'production' {
			Write-Host 'Production mode — Docker not started.' -ForegroundColor DarkGray
		}
	}
}

Write-Host ''
Write-Host 'Starting admin Vite (Ctrl+C to stop)...' -ForegroundColor Cyan
Set-Location -LiteralPath (Join-Path $RepoRoot 'front_admin')

# loadEnv in vite.config reads ADMIN_API_PROXY_TARGET from .env.development.local
yarn dev
