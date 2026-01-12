$errors = @()
Get-ChildItem -Path 'X:\Programacion\UltimateSite' -Recurse -Filter '*.php' -File | ForEach-Object {
    $out = & php -l $_.FullName 2>&1
    if ($out -match 'Parse error|Errors parsing') {
        Write-Output "ERROR_IN:$($_.FullName)"
        Write-Output $out
        $errors += $_.FullName
    }
}
if ($errors.Count -eq 0) { Write-Output "No parse errors found." }
else { Write-Output "Total files with parse errors: $($errors.Count)" }
