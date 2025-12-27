# Copilot Instructions for UltimateSite

## Project Overview
- **UltimateSite** is a hybrid PHP + React (Vite) web platform.
- The backend is classic PHP (see root and public/), while the frontend is a modern React app (frontend/), built with Vite and output to public/build/.
- Data and content are managed via JSON files (data/pages.json) and SQL migrations (database/migrations/).

## Key Directories
- `frontend/`: React app (Vite). Entry: src/main.jsx, main component: App.jsx. Styles: styles/xlerion.scss.
- `public/`: PHP entrypoint (index.php), router (router.php), and static assets. Built frontend is in public/build/.
- `database/`: Migration scripts (migrate.php, run_migrations.php), SQL migrations in migrations/.
- `data/`: JSON data files for dynamic content.
- `includes/`: Shared PHP includes (config.php, header.php, footer.php).
- `scripts/`: Utility scripts for validation and data checks.

## Developer Workflows
- **Frontend build:**
  ```bash
  cd frontend
  npm install
  npm run build
  ```
  Output: ../public/build
- **Run PHP migrations:**
  ```bash
  php database/migrate.php
  ```
- **Validate before deploy:**
  Use scripts/validate_before_deploy.ps1 (Windows) or .sh (Linux).

## Conventions & Patterns
- **Frontend:**
  - Use React functional components.
  - SCSS modules for component styles (e.g., Navbar.module.scss).
  - Main styles in styles/xlerion.scss.
- **Backend:**
  - PHP pages in root and public/.
  - Shared includes in includes/.
  - Data access via JSON or SQL migrations.
- **Data:**
  - Content pages defined in data/pages.json.
  - Contacts and other data in backup/contacts/ and data/.

## Integration Points
- Frontend communicates with backend via public/api/ endpoints (e.g., contact.php, pages.php).
- PHP includes shared via includes/.
- Static assets (images, fonts) in media/ and public/media/.

## Examples
- To add a new page: update data/pages.json, create PHP/React components as needed.
- To add a new API: add PHP file in public/api/.

## References
- See frontend/README.md for React build details.
- See database/migrations/ for SQL structure.
- See scripts/ for validation and utility scripts.

---
For questions, check README.md files or ask the project maintainer.
