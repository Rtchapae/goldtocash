# Deploy changed files from git to production (goldtocash.us).
# Run from repo root on a machine with SSH access to the server.
#
# Typical workflow (Cursor mobile / desktop):
#   1. Edit files in Cursor
#   2. git add … && git commit && git push
#   3. git pull   (on PC if needed)
#   4. .\scripts\deploy-from-git.ps1 -Mode last-commit
#
# Modes:
#   uncommitted  — tracked changes + new untracked files (default)
#   staged       — only git add'ed files
#   last-commit  — files in the latest commit (best after commit+push)
#   since-main   — all commits since origin/main
#   manifest     — paths listed in deploy/manifests/<name>.txt (-Manifest)
#
# Examples:
#   .\scripts\deploy-from-git.ps1
#   .\scripts\deploy-from-git.ps1 -Mode last-commit -BackupAndRollback
#   .\scripts\deploy-from-git.ps1 -Mode since-main -BuildAdmin
#   .\scripts\deploy-from-git.ps1 -Mode manifest -Manifest mobile-pagespeed.txt
#   .\scripts\deploy-from-git.ps1 -Paths "front/src/App.vue","back/app/Console/Kernel.php"
#
param(
    [ValidateSet('uncommitted', 'staged', 'last-commit', 'since-main', 'manifest', 'paths')]
    [string]$Mode = 'uncommitted',

    [string]$Since = 'origin/main',
    [string]$Manifest = '',
    [string[]]$Paths = @(),

    [switch]$SkipRemoteDocker,
    [switch]$RunMigrations,
    [string]$MigrationFile = '',
    [switch]$BackupAndRollback,
    [switch]$BuildAdmin,
    [switch]$CleanupThisBackup,
    [int]$CleanupBackupsOlderThanDays = 0
)

$ErrorActionPreference = 'Stop'
$RepoRoot = Split-Path -Parent $PSScriptRoot
. (Join-Path $PSScriptRoot 'Deploy-Common.ps1')

$gitParams = @{}
switch ($Mode) {
    'uncommitted' {
        $gitParams.GitUncommitted = $true
    }
    'staged' {
        $gitParams.GitStaged = $true
    }
    'last-commit' {
        $gitParams.GitSince = 'HEAD~1..HEAD'
    }
    'since-main' {
        $gitParams.GitSince = "${Since}...HEAD"
    }
    'manifest' {
        if ([string]::IsNullOrWhiteSpace($Manifest)) {
            Write-Error "Mode manifest requires -Manifest <file.txt> (under deploy/manifests/)."
        }
        if ($Manifest -notmatch '[/\\]') {
            $Manifest = "deploy/manifests/$Manifest"
        }
        $gitParams.FilesFrom = $Manifest
    }
    'paths' {
        if ($Paths.Count -eq 0) {
            Write-Error "Mode paths requires -Paths 'front/...','back/...'"
        }
        $gitParams.DeployFiles = $Paths
    }
}

$files = Get-GitDeployFileList -RepoRoot $RepoRoot @gitParams
if ($files.Count -eq 0) {
    Write-Host "No deployable files found for mode '$Mode'." -ForegroundColor Yellow
    Write-Host "Deployable prefixes: front/, back/, docker/, front_admin/, config/, scripts/, deploy/" -ForegroundColor DarkGray
    exit 1
}

Write-Host "Deploy-from-git ($Mode): $($files.Count) file(s)" -ForegroundColor Cyan
$files | ForEach-Object { Write-Host "  $_" -ForegroundColor DarkGray }

$deployParams = @{
    DeployFiles                  = $files
    SkipRemoteDocker             = $SkipRemoteDocker
    RunMigrations                = $RunMigrations
    MigrationFile                = $MigrationFile
    BackupAndRollback            = $BackupAndRollback
    BuildAdmin                   = $BuildAdmin
    CleanupThisBackup            = $CleanupThisBackup
    CleanupBackupsOlderThanDays  = $CleanupBackupsOlderThanDays
}

& (Join-Path $RepoRoot 'deploy-staging.ps1') @deployParams
