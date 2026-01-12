$ErrorActionPreference = 'Stop'
$session = New-Object Microsoft.PowerShell.Commands.WebRequestSession
Write-Output "Logging in as admin..."
$login = Invoke-WebRequest -Uri 'http://localhost:8000/public/admin/login.php' -Method Post -Body @{username = 'admin'; password = 'admin1984!' } -WebSession $session -ErrorAction Stop
Write-Output "Login done. Fetching admin customers page..."
$page = Invoke-WebRequest -Uri 'http://localhost:8000/public/admin/crm/customers.php' -WebSession $session -UseBasicParsing -ErrorAction Stop
$content = $page.Content
if ($content -match "CSRF_TOKEN\s*=\s*'([a-f0-9]+)'") {
    $token = $matches[1]
    Write-Output "TOKEN:$token"
}
else {
    Write-Error 'CSRF token not found'
    exit 2
}
$bodyObj = @{ name = 'Test Client 20251228'; email = 'test+20251228@example.com'; phone = '3200000000'; csrf_token = $token }
$body = $bodyObj | ConvertTo-Json
Write-Output 'Posting to API...'
$resp = Invoke-RestMethod -Uri 'http://localhost:8000/public/api/crm/customers.php' -Method Post -Body $body -WebSession $session -ContentType 'application/json' -ErrorAction Stop
Write-Output 'CREATE_RESP:'
Write-Output ($resp | ConvertTo-Json)
