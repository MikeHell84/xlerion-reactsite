#!/usr/bin/env pwsh
Write-Host "Running basic validation..."

# PHP syntax check for all .php files
Get-ChildItem -Path .. -Recurse -Include *.php | ForEach-Object {
  Write-Host "Checking $_"
  php -l $_.FullName | Write-Host
}

Write-Host "Validation finished. Add more checks as needed."
