# Shared helpers for deploy-staging.ps1 and deploy-from-git.ps1

function Get-DeployRepoRoot {
    param([string]$StartPath = $PSScriptRoot)
    $root = Split-Path -Parent $StartPath
    if (-not (Test-Path -LiteralPath (Join-Path $root ".git") -PathType Container)) {
        $root = Get-Location
    }
    return (Resolve-Path -LiteralPath $root).Path
}

function Test-DeployableRepoPath {
    param([string]$RelativePath)

    $rel = ($RelativePath -replace '\\', '/').Trim()
    if ([string]::IsNullOrWhiteSpace($rel)) { return $false }
    if ($rel -match '(^|/)(node_modules|vendor|dist|build|tmp|\.git)(/|$)') { return $false }
    if ($rel -match '\.(log|tmp|temp|bak|backup)$') { return $false }
    if ($rel -match '(^|/)\.env(\.|$|$)') { return $false }
    if ($rel -match '^data/mysql/') { return $false }

    return $rel -match '^(front|back|docker|front_admin|config|scripts|deploy)/'
}

function Get-GitDeployFileList {
    param(
        [string]$RepoRoot,
        [string[]]$DeployFiles = @(),
        [string]$FilesFrom = "",
        [switch]$GitStaged,
        [switch]$GitUncommitted,
        [string]$GitSince = ""
    )

    $candidates = [System.Collections.Generic.List[string]]::new()

    if ($DeployFiles.Count -gt 0) {
        $candidates.AddRange([string[]]$DeployFiles)
    }
    elseif (-not [string]::IsNullOrWhiteSpace($FilesFrom)) {
        $manifestPath = if ([System.IO.Path]::IsPathRooted($FilesFrom)) { $FilesFrom } else { Join-Path $RepoRoot $FilesFrom }
        if (-not (Test-Path -LiteralPath $manifestPath -PathType Leaf)) {
            throw "Manifest not found: $FilesFrom"
        }
        foreach ($line in Get-Content -LiteralPath $manifestPath) {
            $line = ($line -replace '#.*$', '').Trim()
            if ($line) { $candidates.Add($line) | Out-Null }
        }
    }
    elseif ($GitStaged) {
        $out = & git -C $RepoRoot diff --cached --name-only --diff-filter=ACMR 2>$null
        if ($LASTEXITCODE -ne 0) { throw "git diff --cached failed" }
        foreach ($line in $out) { if ($line) { $candidates.Add($line) | Out-Null } }
    }
    elseif ($GitUncommitted) {
        $tracked = & git -C $RepoRoot diff --name-only --diff-filter=ACMR 2>$null
        $untracked = & git -C $RepoRoot ls-files --others --exclude-standard 2>$null
        if ($LASTEXITCODE -ne 0) { throw "git diff failed" }
        foreach ($line in @($tracked) + @($untracked)) { if ($line) { $candidates.Add($line) | Out-Null } }
    }
    elseif (-not [string]::IsNullOrWhiteSpace($GitSince)) {
        $range = $GitSince.Trim()
        $out = & git -C $RepoRoot diff --name-only --diff-filter=ACMR $range 2>$null
        if ($LASTEXITCODE -ne 0) { throw "git diff $range failed (check branch/ref exists)" }
        foreach ($line in $out) { if ($line) { $candidates.Add($line) | Out-Null } }
    }

    $result = [System.Collections.Generic.List[string]]::new()
    $seen = @{}

    foreach ($rel in $candidates) {
        $rel = ($rel -replace '\\', '/').Trim()
        if (-not (Test-DeployableRepoPath $rel)) { continue }

        $localPath = Join-Path $RepoRoot $rel
        if (-not (Test-Path -LiteralPath $localPath -PathType Leaf)) { continue }
        if ($seen.ContainsKey($rel)) { continue }

        $seen[$rel] = $true
        $result.Add($rel) | Out-Null
    }

    return ,$result.ToArray()
}
