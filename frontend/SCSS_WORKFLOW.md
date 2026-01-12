Quick SCSS / CSS workflow (Xlerion project)

- Source SCSS: `frontend/src/styles/xlerion.scss`
- Compiled CSS (served): `public/xlerion.css`

To recompile only the global CSS after edits:

```bash
cd frontend
npm run build:css
```

Notes:
- Never edit `public/xlerion.css` directly — edit the SCSS partials in `frontend/src/styles/` and run `npm run build:css`.
- The admin login styles live in the `.login-box`, `.login-title` and `.xlerion-btn-primary` rules inside `xlerion.scss`.
- If changes don't appear in the browser, do a hard refresh (Ctrl+Shift+R) or clear cache; ensure the local PHP server is running (`C:\\tools\\php85\\php.exe -S localhost:8000 -t public`).
