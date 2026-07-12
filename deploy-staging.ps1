# Deploy selected files to production (goldtocash.us).
# Prefer git-based deploy (works with Cursor mobile workflow):
#   .\scripts\deploy-from-git.ps1 -Mode last-commit
#   .\scripts\deploy-from-git.ps1 -Mode manifest -Manifest mobile-pagespeed.txt
#
# Low-level (explicit file list):
#   .\deploy-staging.ps1 -DeployFiles "front/src/App.vue","back/app/Console/Kernel.php"
#   .\deploy-staging.ps1 -FilesFrom deploy/manifests/mobile-pagespeed.txt
#   .\deploy-staging.ps1 -GitSince "origin/main...HEAD"
#
# Options:
#   .\deploy-staging.ps1 -RunMigrations -BuildAdmin -BackupAndRollback
#   .\deploy-staging.ps1 -SkipRemoteDocker   # upload only, no docker on server
#
param(
    [string[]]$DeployFiles = @(),
    [string]$FilesFrom = "",
    [switch]$GitStaged,
    [switch]$GitUncommitted,
    [string]$GitSince = "",

    [switch]$SkipRemoteDocker,
    [switch]$RunMigrations,
    [string]$MigrationFile = "",
    [switch]$BackupAndRollback,
    [switch]$BuildAdmin,
    [switch]$CleanupThisBackup,
    [int]$CleanupBackupsOlderThanDays = 0
)

$ErrorActionPreference = "Stop"

$SERVER = "root@159.89.186.46"
$REMOTE = "/var/www/goldtocash.us"

$SshOpts = @("-o", "StrictHostKeyChecking=no")

$RepoRoot = if ($PSScriptRoot) { $PSScriptRoot } else { Get-Location }
Set-Location -LiteralPath $RepoRoot

. (Join-Path $RepoRoot "scripts/Deploy-Common.ps1")

$Files = Get-GitDeployFileList -RepoRoot $RepoRoot `
    -DeployFiles $DeployFiles `
    -FilesFrom $FilesFrom `
    -GitStaged:$GitStaged `
    -GitUncommitted:$GitUncommitted `
    -GitSince $GitSince

if ($Files.Count -eq 0) {
    Write-Host "Error: no deploy files. Use scripts/deploy-from-git.ps1 or -DeployFiles / -FilesFrom / -GitSince." -ForegroundColor Red
    exit 1
}

Write-Host "Deploying $($Files.Count) file(s)..." -ForegroundColor Cyan

if (-not (Get-Command tar -ErrorAction SilentlyContinue)) {
    Write-Host "Error: 'tar' not found. Use Windows 10+ or install bsdtar." -ForegroundColor Red
    exit 1
}

$relList = [System.Collections.Generic.List[string]]::new()
foreach ($rel in $Files) {
    $rel = $rel -replace '\\', '/'
    if ([string]::IsNullOrWhiteSpace($rel)) { continue }

    $localPath = Join-Path $RepoRoot $rel
    if (-not (Test-Path -LiteralPath $localPath -PathType Leaf)) {
        Write-Host "Error: missing file $rel" -ForegroundColor Red
        exit 1
    }
    $relList.Add($rel) | Out-Null
}

$n = $relList.Count
$listFile = Join-Path $env:TEMP ("deploy-staging-" + [Guid]::NewGuid().ToString("N") + ".txt")
$archiveFile = Join-Path $env:TEMP ("deploy-staging-" + [Guid]::NewGuid().ToString("N") + ".tgz")
$remoteArchiveFile = "$REMOTE/tmp/deploy-staging.tgz"
$deployStamp = (Get-Date).ToString("yyyyMMdd-HHmmss")
$remoteBackupFile = "$REMOTE/tmp/deploy-backup-$deployStamp.tgz"
$backupListFileRemote = "$REMOTE/tmp/deploy-filelist-$deployStamp.txt"

if (-not [string]::IsNullOrWhiteSpace($MigrationFile)) {
    $MigrationFile = $MigrationFile.Trim()
    if ($MigrationFile -match '[/\\]') {
        Write-Host "Error: -MigrationFile must be a filename only (no slashes). Got: $MigrationFile" -ForegroundColor Red
        exit 1
    }
    $migrationLocal = Join-Path $RepoRoot ("back/database/migrations/" + $MigrationFile)
    if (-not (Test-Path -LiteralPath $migrationLocal -PathType Leaf)) {
        Write-Host "Error: migration file not found: back/database/migrations/$MigrationFile" -ForegroundColor Red
        exit 1
    }
    $RunMigrations = $true
}

$rollbackNeeded = $false

try {
    # tar -T expects one path per line; use LF only
    [System.IO.File]::WriteAllText($listFile, ($relList -join "`n") + "`n", [System.Text.UTF8Encoding]::new($false))

    Write-Host "Creating local archive with $n file(s)..." -ForegroundColor Cyan
    Push-Location -LiteralPath $RepoRoot
    try {
        & tar @("czf", $archiveFile, "-T", $listFile)
        if ($LASTEXITCODE -ne 0) {
            Write-Host "Error: tar failed (exit $LASTEXITCODE)." -ForegroundColor Red
            exit 1
        }
    }
    finally {
        Pop-Location
    }

    Write-Host "Uploading archive (scp)..." -ForegroundColor Cyan
    & ssh @SshOpts $SERVER "mkdir -p `"$REMOTE/tmp`""
    if ($LASTEXITCODE -ne 0) {
        Write-Host "Error: remote mkdir failed (exit $LASTEXITCODE)." -ForegroundColor Red
        exit 1
    }

    if ($BackupAndRollback) {
        Write-Host "Creating remote backup (current deployed versions of listed files)..." -ForegroundColor Cyan
        # Reuse the same list file we generated for tar -T, upload it, and use it as -T on server.
        & scp -q @SshOpts $listFile "${SERVER}:${backupListFileRemote}"
        if ($LASTEXITCODE -ne 0) {
            Write-Host "Warning: failed to upload remote backup list file. Continuing, but rollback may be unavailable." -ForegroundColor Yellow
        } else {
            & ssh @SshOpts $SERVER "cd `"$REMOTE`" && tar czf `"$remoteBackupFile`" -T `"$backupListFileRemote`" 2>/dev/null || true"
            if ($LASTEXITCODE -ne 0) {
                Write-Host "Warning: remote backup tar returned non-zero. Continuing, but rollback may be unavailable." -ForegroundColor Yellow
            } else {
                Write-Host "Backup created: $remoteBackupFile" -ForegroundColor DarkGray
            }
        }
        $rollbackNeeded = $true
    }

    & scp -q @SshOpts $archiveFile "${SERVER}:${remoteArchiveFile}"
    if ($LASTEXITCODE -ne 0) {
        Write-Host "Error: scp archive failed (exit $LASTEXITCODE)." -ForegroundColor Red
        exit 1
    }

    Write-Host "Extracting on server..." -ForegroundColor Cyan
    & ssh @SshOpts $SERVER "cd `"$REMOTE`" && tar xzf `"$remoteArchiveFile`" && rm -f `"$remoteArchiveFile`""
    if ($LASTEXITCODE -ne 0) {
        Write-Host "Error: remote extract failed (exit $LASTEXITCODE)." -ForegroundColor Red
        exit 1
    }
}
catch {
    if ($BackupAndRollback -and $rollbackNeeded) {
        Write-Host ""
        Write-Host "Deploy failed. Rolling back files from backup..." -ForegroundColor Yellow
        & ssh @SshOpts $SERVER "cd `"$REMOTE`" && if [ -f `"$remoteBackupFile`" ]; then tar xzf `"$remoteBackupFile`"; else echo 'No backup archive found for rollback.'; fi"
        if (-not $SkipRemoteDocker) {
            & ssh @SshOpts $SERVER "cd `"$REMOTE`" && docker compose build front && docker compose up -d --force-recreate front && docker compose restart nginx"
        }
        Write-Host "Rollback attempted. Backup kept at: $remoteBackupFile" -ForegroundColor Yellow
    }
    throw
}
finally {
    Remove-Item -LiteralPath $listFile -Force -ErrorAction SilentlyContinue
    Remove-Item -LiteralPath $archiveFile -Force -ErrorAction SilentlyContinue
}

Write-Host "Done: uploaded $n file(s) under $REMOTE on server." -ForegroundColor Green

$hasFrontFiles = @($relList | Where-Object { $_ -like 'front/*' }).Count -gt 0
$hasBackFiles = @($relList | Where-Object { $_ -like 'back/*' }).Count -gt 0

if (-not $SkipRemoteDocker) {
    Write-Host ""
    Write-Host "Running Docker on server..." -ForegroundColor Cyan
    try {
        $dockerCmd = "cd `"$REMOTE`""
        if ($hasFrontFiles) {
            $dockerCmd += " && docker compose build front && docker compose up -d --force-recreate front"
        }
        if ($hasBackFiles) {
            $dockerCmd += " && docker compose restart backend"
        }
        $dockerCmd += " && docker compose restart nginx && docker compose ps"
        & ssh @SshOpts $SERVER $dockerCmd
        if ($LASTEXITCODE -ne 0) {
            throw "remote docker failed"
        }

        if ($BuildAdmin) {
            Write-Host ""
            Write-Host "Building admin static dist on server (front_admin)..." -ForegroundColor Cyan
            # Build into ./front_admin/dist (mounted into nginx as /var/www/html/admin).
            # Do NOT rely on the front_admin service image: on prod it may be nginx-only (no node/npm/yarn).
            # Instead, use a one-off Node container to build against the checked-out ./front_admin folder.
            & ssh @SshOpts $SERVER "cd `"$REMOTE`" && docker run --rm -v `"$REMOTE/front_admin:/app`" -w /app node:20-alpine sh -lc 'apk add --no-cache yarn >/dev/null 2>&1; yarn --version; yarn install --silent; yarn build' && docker compose restart nginx"
            if ($LASTEXITCODE -ne 0) {
                throw "admin build failed"
            }
        }
    } catch {
        if ($BackupAndRollback -and $rollbackNeeded) {
            Write-Host ""
            Write-Host "Docker step failed. Rolling back files from backup..." -ForegroundColor Yellow
            & ssh @SshOpts $SERVER "cd `"$REMOTE`" && if [ -f `"$remoteBackupFile`" ]; then tar xzf `"$remoteBackupFile`"; else echo 'No backup archive found for rollback.'; fi"
            & ssh @SshOpts $SERVER "cd `"$REMOTE`" && docker compose build front && docker compose up -d --force-recreate front && docker compose restart nginx"
            Write-Host "Rollback attempted. Backup kept at: $remoteBackupFile" -ForegroundColor Yellow
        }
        Write-Host "Error: remote Docker steps failed." -ForegroundColor Red
        exit 1
    }
    if ($LASTEXITCODE -ne 0) {
        Write-Host "Error: remote Docker steps failed (exit $LASTEXITCODE)." -ForegroundColor Red
        exit 1
    }
    Write-Host ""
    Write-Host "Done: remote Docker steps completed." -ForegroundColor Green
} else {
    Write-Host ""
    Write-Host "Skipped remote Docker (-SkipRemoteDocker). On server run:" -ForegroundColor Yellow
    Write-Host "  cd $REMOTE && docker compose build front && docker compose up -d --force-recreate front && docker compose restart nginx" -ForegroundColor DarkGray
}

if ($RunMigrations) {
    Write-Host ""
    try {
        if (-not [string]::IsNullOrWhiteSpace($MigrationFile)) {
            Write-Host "Running ONE migration on server: $MigrationFile" -ForegroundColor Cyan
            & ssh @SshOpts $SERVER "cd `"$REMOTE`" && docker compose exec -T backend php artisan migrate --force --path=database/migrations/$MigrationFile"
        } else {
            Write-Host "Running DB migrations on server (php artisan migrate --force)..." -ForegroundColor Cyan
            & ssh @SshOpts $SERVER "cd `"$REMOTE`" && docker compose exec -T backend php artisan migrate --force"
        }
        if ($LASTEXITCODE -ne 0) {
            throw "migrations failed"
        }
    } catch {
        if ($BackupAndRollback -and $rollbackNeeded) {
            Write-Host ""
            Write-Host "Migration step failed. Rolling back FILES from backup..." -ForegroundColor Yellow
            Write-Host "Note: DB changes from migrations cannot be automatically rolled back safely." -ForegroundColor Yellow
            & ssh @SshOpts $SERVER "cd `"$REMOTE`" && if [ -f `"$remoteBackupFile`" ]; then tar xzf `"$remoteBackupFile`"; else echo 'No backup archive found for rollback.'; fi"
            if (-not $SkipRemoteDocker) {
                & ssh @SshOpts $SERVER "cd `"$REMOTE`" && docker compose build front && docker compose up -d --force-recreate front && docker compose restart nginx"
            }
            Write-Host "Rollback attempted. Backup kept at: $remoteBackupFile" -ForegroundColor Yellow
        }
        Write-Host "Error: migrations failed." -ForegroundColor Red
        exit 1
    }
    Write-Host "Done: migrations completed." -ForegroundColor Green
} else {
    Write-Host ""
    Write-Host "Skipped migrations. To run them next time use: .\\deploy-staging.ps1 -RunMigrations" -ForegroundColor DarkGray
}

if ($BackupAndRollback) {
    if ($CleanupThisBackup) {
        Write-Host ""
        Write-Host "Cleaning up THIS deploy backup on server..." -ForegroundColor Cyan
        & ssh @SshOpts $SERVER "rm -f `"$remoteBackupFile`" `"$backupListFileRemote`""
        if ($LASTEXITCODE -ne 0) {
            Write-Host "Warning: cleanup failed. Backup kept at: $remoteBackupFile" -ForegroundColor Yellow
        }
    }

    if ($CleanupBackupsOlderThanDays -gt 0) {
        Write-Host ""
        Write-Host "Cleaning up old deploy backups on server (older than $CleanupBackupsOlderThanDays day(s))..." -ForegroundColor Cyan
        # Busybox find may not support -delete reliably; use -exec rm.
        & ssh @SshOpts $SERVER "cd `"$REMOTE/tmp`" && find . -maxdepth 1 -type f \\( -name 'deploy-backup-*.tgz' -o -name 'deploy-filelist-*.txt' \\) -mtime +$CleanupBackupsOlderThanDays -exec rm -f {} \\;"
        if ($LASTEXITCODE -ne 0) {
            Write-Host "Warning: old-backup cleanup failed. You can remove them manually under $REMOTE/tmp" -ForegroundColor Yellow
        }
    }
}

Write-Host ""
if (-not $BuildAdmin) {
	Write-Host 'Reminder: this deploy includes admin files — run with -BuildAdmin to refresh m.goldtocash.us admin UI.' -ForegroundColor Yellow
}
Write-Host 'Optional: rebuild admin manually:' -ForegroundColor DarkGray
Write-Host '  .\deploy-staging.ps1 -BuildAdmin' -ForegroundColor DarkGray
