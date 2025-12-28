<#
start-dev.ps1

Simple entrypoint to start the local development server reliably.
It will attempt to start the PHP built-in server using `scripts/start-php-server.ps1`.
If PHP is not available it will fall back to a Python HTTP server.

Usage:
  ./scripts/start-dev.ps1            # default port 8000
  ./scripts/start-dev.ps1 -Port 8080

#>

param(
    [int]$Port = 8000
)

Push-Location -Path (Split-Path -Parent $MyInvocation.MyCommand.Definition)
Pop-Location

$scriptRoot = Split-Path -Parent $MyInvocation.MyCommand.Definition
$startPhp = Join-Path $scriptRoot 'start-php-server.ps1'

if (Test-Path $startPhp) {
    Write-Host "Using helper: $startPhp"
    & $startPhp -Port $Port
    if ($LASTEXITCODE -eq 0) {
        Write-Host "PHP server started (requested port $Port)."
        return
    }
}

Write-Host "Falling back to Python HTTP server on port $Port"
try {
    if (Get-Command python -ErrorAction SilentlyContinue) {
        Start-Process -FilePath (Get-Command python).Source -ArgumentList '-m', 'http.server', $Port, '--directory', 'public' -WindowStyle Hidden
    }
    elseif (Get-Command py -ErrorAction SilentlyContinue) {
        Start-Process -FilePath (Get-Command py).Source -ArgumentList '-3', '-m', 'http.server', $Port, '--directory', 'public' -WindowStyle Hidden
    }
    else {
        Write-Host 'Python not found. Please install Python or provide php.exe in PATH.'
        exit 1
    }
    Write-Host "Python server started on http://localhost:$Port"
}
catch {
    Write-Host "Failed to start fallback server: $_"
    exit 1
}
