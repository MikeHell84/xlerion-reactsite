<#
Start-PhpServer.ps1

Lightweight helper to start the PHP built-in webserver for local development.
It attempts to locate `php.exe` (PATH, Scoop install locations, common folders)
and starts `php -S localhost:<port> -t public` in a background process.

Usage:
  ./scripts/start-php-server.ps1            # start on default port 8000
  ./scripts/start-php-server.ps1 -Port 8080 # start on port 8080

This script does NOT modify your PATH automatically. If php is not found
you can install it with Scoop (`scoop install php`) or add your php.exe
location to PATH.
#>

param(
    [int]$Port = 8000
)

function Find-PHP {
    # Check PATH first
    $cmd = Get-Command php -ErrorAction SilentlyContinue
    if ($cmd) { return $cmd.Source }

    # Common scoop location
    $scoopPhp = Join-Path $env:USERPROFILE "scoop\apps\php\current\php.exe"
    if (Test-Path $scoopPhp) { return $scoopPhp }

    # Common installs
    $candidates = @(
        'C:\php\php.exe',
        'C:\Program Files\PHP\php.exe',
        'C:\Program Files (x86)\PHP\php.exe',
        'C:\wamp64\bin\php\php.exe',
        'C:\xampp\php\php.exe'
    )
    foreach ($p in $candidates) { if (Test-Path $p) { return $p } }

    return $null
}

$phpPath = Find-PHP
if (-not $phpPath) {
    Write-Host 'PHP executable not found.'
    Write-Host 'Install PHP with Scoop: scoop install php or add php.exe to your PATH.'
    Write-Host 'Fallback: start a Python server: python -m http.server 8000 --directory public'
    exit 1
}

Write-Host "Starting PHP built-in server using: $phpPath (port $Port)"
Start-Process -FilePath $phpPath -ArgumentList ('-S', 'localhost:{0}' -f $Port, '-t', 'public') -WindowStyle Hidden
Write-Host "PHP server started on http://localhost:$Port"
