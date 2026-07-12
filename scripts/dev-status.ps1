# Show active dev mode and whether local Docker endpoints respond.
#   .\scripts\dev-status.ps1

$ErrorActionPreference = 'Continue'
$RepoRoot = if ($PSScriptRoot) { (Resolve-Path (Join-Path $PSScriptRoot '..')).Path } else { (Get-Location).Path }
Set-Location -LiteralPath $RepoRoot

$ModeFile = Join-Path $RepoRoot '.dev-mode'
$mode = if (Test-Path -LiteralPath $ModeFile) { (Get-Content -LiteralPath $ModeFile -Raw).Trim() } else { 'local-api (default, not written yet)' }

Write-Host "Dev mode: $mode" -ForegroundColor Cyan

function Test-HttpOk($url) {
	try {
		$r = Invoke-WebRequest -Uri $url -UseBasicParsing -TimeoutSec 5
		return "OK $($r.StatusCode)"
	} catch {
		return "FAIL"
	}
}

Write-Host ''
Write-Host 'Docker / health:' -ForegroundColor Cyan
Write-Host "  :8888 /health  -> $(Test-HttpOk 'http://127.0.0.1:8888/health')   (local-api)"
Write-Host "  :9080 /health  -> $(Test-HttpOk 'http://127.0.0.1:9080/health')   (local-full)"
Write-Host "  :5173          -> $(Test-HttpOk 'http://127.0.0.1:5173/')        (admin Vite)"
Write-Host "  :5175          -> $(Test-HttpOk 'http://127.0.0.1:5175/')        (front Vite)"

if (Test-Path -LiteralPath (Join-Path $RepoRoot 'front_admin\.env.development.local')) {
	Write-Host ''
	Write-Host 'front_admin/.env.development.local:' -ForegroundColor Cyan
	Get-Content -LiteralPath (Join-Path $RepoRoot 'front_admin\.env.development.local') |
		Where-Object { $_ -match 'VITE_|DEV_MODE' } |
		ForEach-Object { Write-Host "  $_" }
}

Write-Host ''
Write-Host 'Switch mode:  .\scripts\set-dev-mode.ps1 -Mode local-api|local-full|production' -ForegroundColor DarkGray
