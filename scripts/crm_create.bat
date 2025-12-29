@echo off
REM Login and save cookies
curl -c "%~dp0cookies.txt" -d "username=admin&password=admin1984!" -X POST "http://localhost:8000/public/admin/login.php" -s -L -o nul

REM Fetch the admin customers page using saved cookies
curl -b "%~dp0cookies.txt" "http://localhost:8000/public/admin/crm/customers.php" -s -o "%~dp0customers_page.html"

REM Extract CSRF token using PowerShell Select-String
powershell -NoProfile -Command "(Select-String -Path '%~dp0customers_page.html' -Pattern \"CSRF_TOKEN = '\'([a-f0-9]+)\'\" -AllMatches).Matches | ForEach-Object { $_.Groups[1].Value } | Out-File -FilePath '%~dp0token.txt' -Encoding ASCII"

set token=
if exist "%~dp0token.txt" (
  set /p token=<"%~dp0token.txt"
)
echo TOKEN=%token%

REM POST JSON create (use token in payload)
curl -b "%~dp0cookies.txt" -H "Content-Type: application/json" -d "{\"name\":\"Test Client 20251228\",\"email\":\"test+20251228@example.com\",\"phone\":\"3200000000\",\"csrf_token\":\"%token%\"}" -X POST "http://localhost:8000/public/api/crm/customers.php" -s -o "%~dp0api_response.json"

echo API response:
type "%~dp0api_response.json"

echo Saved files: %~dp0cookies.txt %~dp0customers_page.html %~dp0token.txt %~dp0api_response.json
