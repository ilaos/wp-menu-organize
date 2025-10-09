# WordPress Backup Script - Enhanced Version
param(
    [string]$BackupPath = ".\BACKUPS\manual-backup-$(Get-Date -Format 'yyyy-MM-dd-HHmmss')",
    [switch]$IncludeDatabase = $true,
    [switch]$IncludeUploads = $true,
    [switch]$IncludeConfig = $true,
    [switch]$IncludePlugins = $false
)

Write-Host "Starting WordPress backup..." -ForegroundColor Green

# Create backup directory
New-Item -ItemType Directory -Path $BackupPath -Force | Out-Null
Write-Host "Backup directory created: $BackupPath" -ForegroundColor Yellow

# 1. Backup wp-config.php (if it exists)
if ($IncludeConfig -and (Test-Path "app\public\wp-config.php")) {
    $configBackupPath = Join-Path $BackupPath "wp-config-backup.php"
    Copy-Item "app\public\wp-config.php" $configBackupPath
    Write-Host "✓ wp-config.php backed up" -ForegroundColor Green
}

# 2. Backup uploads directory
if ($IncludeUploads -and (Test-Path "app\public\wp-content\uploads")) {
    $uploadsBackupPath = Join-Path $BackupPath "uploads"
    Copy-Item "app\public\wp-content\uploads" $uploadsBackupPath -Recurse
    Write-Host "✓ Uploads directory backed up" -ForegroundColor Green
}

# 3. Backup plugins (optional)
if ($IncludePlugins -and (Test-Path "app\public\wp-content\plugins")) {
    $pluginsBackupPath = Join-Path $BackupPath "plugins"
    Copy-Item "app\public\wp-content\plugins" $pluginsBackupPath -Recurse
    Write-Host "✓ Plugins directory backed up" -ForegroundColor Green
}

# 4. Enhanced Database Backup for Local by Flywheel
if ($IncludeDatabase) {
    $dbBackupPath = Join-Path $BackupPath "database"
    New-Item -ItemType Directory -Path $dbBackupPath -Force | Out-Null
    
    # Try to use Local by Flywheel's database export
    $localDbPath = "app\sql\local.sql"
    if (Test-Path $localDbPath) {
        $dbBackupFile = Join-Path $dbBackupPath "database-backup-$(Get-Date -Format 'yyyy-MM-dd-HHmmss').sql"
        Copy-Item $localDbPath $dbBackupFile
        Write-Host "✓ Database backed up from Local by Flywheel" -ForegroundColor Green
    } else {
        Write-Host "⚠ Local database file not found: $localDbPath" -ForegroundColor Yellow
        Write-Host "   Please export your database manually from Local by Flywheel" -ForegroundColor Yellow
    }
}

# 5. Backup log files
$logBackupPath = Join-Path $BackupPath "logs"
New-Item -ItemType Directory -Path $logBackupPath -Force | Out-Null

# Copy any log files
Get-ChildItem -Path "app\public" -Recurse -Include "*.log", "error_log", "debug.log" | ForEach-Object {
    $relativePath = $_.FullName.Replace((Get-Location).Path + "\", "")
    $targetPath = Join-Path $logBackupPath $relativePath
    $targetDir = Split-Path $targetPath -Parent
    New-Item -ItemType Directory -Path $targetDir -Force | Out-Null
    Copy-Item $_.FullName $targetPath
    Write-Host "✓ Log file backed up: $relativePath" -ForegroundColor Green
}

# 6. Create enhanced backup manifest
$manifestPath = Join-Path $BackupPath "backup-manifest.txt"
$manifest = @"
WordPress Backup Manifest
Created: $(Get-Date)
Backup Path: $BackupPath
Git Repository: $(git rev-parse HEAD 2>$null || "Not a git repository")

Included in this backup:
- wp-config.php: $IncludeConfig
- Uploads directory: $IncludeUploads
- Plugins directory: $IncludePlugins
- Database: $IncludeDatabase
- Log files: Yes

Files backed up:
"@

Get-ChildItem -Path $BackupPath -Recurse -File | ForEach-Object {
    $relativePath = $_.FullName.Replace($BackupPath + "\", "")
    $size = [math]::Round($_.Length / 1MB, 2)
    $manifest += "`n- $relativePath ($size MB)"
}

$manifest | Out-File -FilePath $manifestPath -Encoding UTF8
Write-Host "✓ Backup manifest created" -ForegroundColor Green

# 7. Calculate total backup size
$totalSize = (Get-ChildItem -Path $BackupPath -Recurse -File | Measure-Object -Property Length -Sum).Sum
$totalSizeMB = [math]::Round($totalSize / 1MB, 2)

Write-Host "`nBackup completed successfully!" -ForegroundColor Green
Write-Host "Backup location: $BackupPath" -ForegroundColor Cyan
Write-Host "Total backup size: $totalSizeMB MB" -ForegroundColor Cyan
Write-Host "`nNext steps:" -ForegroundColor Yellow
Write-Host "1. Verify the backup contents" -ForegroundColor White
Write-Host "2. Test the backup by restoring to a test environment" -ForegroundColor White
Write-Host "3. Consider uploading this backup to cloud storage" -ForegroundColor White
Write-Host "4. Keep this backup safe - it contains sensitive data!" -ForegroundColor Red