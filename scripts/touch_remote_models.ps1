$path = 'X:\Programacion\UltimateSite\includes\remote_models'
$files = Get-ChildItem -Path $path -Filter '*.php' -File
foreach ($f in $files) {
    $f.LastWriteTime = Get-Date
    Write-Output ("Touched: $($f.FullName)")
}