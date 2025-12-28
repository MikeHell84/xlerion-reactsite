# xlerion-reactsite
xlerion.com reactjs site

## Development

- Frontend: edit files under `frontend/` and run the Vite dev server or build assets.
- Backend: PHP pages are under `public/` and simple PHP endpoints live in `public/api/`.

## Local development server (PHP)

This project expects the `public/` folder to be served by a local HTTP server while developing.

Preferred: use the PHP built-in server. If you have PHP installed (via Scoop or other), use the helper script:

PowerShell (from the project root):

```powershell
./scripts/start-php-server.ps1
```

This will attempt to locate `php.exe` (PATH, Scoop install locations, common folders) and start:

```
php -S localhost:8000 -t public
```

If `php.exe` is not found, install via Scoop (recommended on Windows):

```powershell
scoop install php
# then (optional) add shims to your PATH
setx PATH "$env:PATH;$env:USERPROFILE\scoop\shims"
```

Fallback: if you do not want to install PHP, you can temporarily serve the `public/` folder with Python:

```powershell
# from project root
python -m http.server 8000 --directory public
```

Notes:
- The helper script does not modify your PATH automatically. After installing PHP or updating PATH, restart your terminal.
- The project also includes a `frontend` build pipeline; run `npm run build` inside `frontend/` to produce updated assets in `public/build`.
