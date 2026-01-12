<#
Create a logical backup of the local MySQL/MariaDB database using credentials
from the repo `.env` file. This script only runs on Windows PowerShell.

Usage:
  .\scripts\backup_local_db.ps1

It writes a timestamped .sql file into the `backups/` folder.
#>
param()

$root = Split-Path -Parent $MyInvocation.MyCommand.Definition
$envPath = Join-Path $root '..\.env' | Resolve-Path -ErrorAction SilentlyContinue
if (-not $envPath) {
    Write-Error ".env file not found at repo root. Create one or export DB vars."
    exit 1
}

$lines = Get-Content $envPath
$map = @{}
foreach ($line in $lines) {
    if ($line.TrimStart().StartsWith('#') -or -not $line.Contains('=')) { continue }
    $parts = $line -split ('=', 2)
    $map[$parts[0].Trim()] = $parts[1].Trim('"')
}

$dbHost = if ($map.ContainsKey('DB_HOST')) { $map['DB_HOST'] } elseif ($map.ContainsKey('DB_HOSTNAME')) { $map['DB_HOSTNAME'] } else { '127.0.0.1' }
$dbPort = if ($map.ContainsKey('DB_PORT')) { $map['DB_PORT'] } else { '3306' }
$db = if ($map.ContainsKey('DB_NAME')) { $map['DB_NAME'] } elseif ($map.ContainsKey('DB_DATABASE')) { $map['DB_DATABASE'] } else { 'xlerion' }
$dbUser = if ($map.ContainsKey('DB_USER')) { $map['DB_USER'] } elseif ($map.ContainsKey('DB_USERNAME')) { $map['DB_USERNAME'] } else { 'root' }
$dbPass = if ($map.ContainsKey('DB_PASS')) { $map['DB_PASS'] } elseif ($map.ContainsKey('DB_PASSWORD')) { $map['DB_PASSWORD'] } else { '' }

$backupDir = Join-Path $root '..\backups'
if (-not (Test-Path $backupDir)) { New-Item -ItemType Directory -Path $backupDir | Out-Null }

$timestamp = Get-Date -Format yyyyMMdd_HHmmss
$outFile = Join-Path $backupDir "$($db)_backup_$timestamp.sql"

Write-Output ("Backing up database '{0}' on {1}:{2} to {3}" -f $db, $dbHost, $dbPort, $outFile)

$mysqldump = 'mysqldump'
if (-not (Get-Command $mysqldump -ErrorAction SilentlyContinue)) {
    Write-Error "mysqldump not found in PATH. Install MySQL client tools or add to PATH."
    exit 2
}

$mdArgs = @(
    ("-h$($dbHost)"),
    ("-P$($dbPort)"),
    ("-u$($dbUser)")
)
if ($dbPass -ne '') { $mdArgs += ("--password=$($dbPass)") }
$mdArgs += @(("--single-transaction"), ("--quick"), ("--routines"), ("--events"), ("$($db)"))

Write-Output ("Running: {0} {1}" -f $mysqldump, ($mdArgs -join ' '))

$proc = Start-Process -FilePath $mysqldump -ArgumentList $mdArgs -NoNewWindow -RedirectStandardOutput $outFile -Wait -PassThru
if ($proc.ExitCode -ne 0) { Write-Error "mysqldump failed with exit code $($proc.ExitCode)"; exit $proc.ExitCode }

Write-Output "Backup complete: $outFile"
