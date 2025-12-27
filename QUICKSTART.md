# Quick Start Guide - Xlerion ReactJS Site

Get up and running in 5 minutes!

## Prerequisites

Make sure you have installed:
- PHP 8.0+ (`php -v` to check)
- Node.js 18+ (`node -v` to check)
- MariaDB/MySQL (`mysql --version` to check)

## Quick Setup

### 1. Clone & Install

```bash
# Clone the repository
git clone <repository-url>
cd xlerion-reactsite

# Run automated setup
./setup.sh
```

The setup script will:
- Check prerequisites
- Create `.env` file
- Install npm dependencies
- Build React application

### 2. Configure Database

Edit `.env` file with your database credentials:
```bash
nano .env
```

Update these lines:
```env
DB_HOST=localhost
DB_NAME=xlerion_db
DB_USER=root
DB_PASS=your_password
```

### 3. Create Database

```bash
# Login to MySQL
mysql -u root -p

# Create database (in MySQL prompt)
CREATE DATABASE xlerion_db;
EXIT;

# Import schema
mysql -u root -p xlerion_db < database/schema.sql
```

### 4. Start Development Server

```bash
php -S localhost:8000 server.php
```

### 5. Open in Browser

Navigate to: **http://localhost:8000**

That's it! 🎉

## Development Workflow

### Watch Mode (Auto-rebuild on changes)

Open two terminal windows:

**Terminal 1 - Watch React files:**
```bash
npm run dev
```

**Terminal 2 - Run PHP server:**
```bash
php -S localhost:8000 server.php
```

Now any changes to React components will automatically rebuild!

## Quick Testing

### Test API Endpoints

```bash
# Test example endpoint
curl http://localhost:8000/api/example.php

# Test contact form
curl -X POST http://localhost:8000/api/contact.php \
  -H "Content-Type: application/json" \
  -d '{"name":"Test User","email":"test@example.com","message":"Hello!"}'
```

### Test Database Connection

```bash
php -r "require 'config/database.php'; echo 'Database connected successfully!' . PHP_EOL;"
```

## Project Structure

```
📁 xlerion-reactsite/
├── 📁 api/              - PHP API endpoints
├── 📁 config/           - Configuration files
├── 📁 database/         - Database schemas
├── 📁 public/           - Built React app (generated)
├── 📁 src/              - React source code
│   ├── 📁 components/   - Reusable components (Navbar, Footer)
│   ├── 📁 sections/     - Page sections (Hero, About, Services, Contact)
│   ├── App.jsx          - Main application
│   └── index.js         - Entry point
├── .env                 - Environment variables (create from .env.example)
└── package.json         - Node dependencies
```

## Common Commands

```bash
# Build for production
npm run build

# Development build with watch
npm run dev

# Start development server (React)
npm start

# Run PHP server
php -S localhost:8000 server.php

# Check PHP syntax
php -l file.php

# Install new npm package
npm install package-name

# Update npm packages
npm update
```

## Making Changes

### Add a New Section

1. Create file: `src/sections/NewSection.jsx`
2. Write your component
3. Import in `src/App.jsx`
4. Add to render: `<NewSection />`
5. Save and rebuild: `npm run build`

### Add a New API Endpoint

1. Create file: `api/newsection.php`
2. Include base: `require_once __DIR__ . '/base.php';`
3. Implement your logic
4. Test: `curl http://localhost:8000/api/newsection.php`

### Modify Styles

- Bootstrap classes: Already included
- Tailwind utilities: Available in JSX
- Custom styles: Edit `src/styles.css`

## Troubleshooting

### Build fails
```bash
rm -rf node_modules package-lock.json
npm install
npm run build
```

### Database connection error
- Check `.env` file exists and has correct credentials
- Verify database exists: `mysql -u root -p -e "SHOW DATABASES;"`
- Test connection: `php -r "require 'config/database.php';"`

### PHP errors
- Check PHP version: `php -v` (needs 8.0+)
- Check syntax: `php -l api/yourfile.php`
- View errors: `tail -f /var/log/php_errors.log`

### Blank page in browser
- Check browser console (F12)
- Verify build completed: `ls -la public/js/`
- Rebuild: `npm run build`

## Next Steps

1. **Customize Content** - Edit components in `src/sections/`
2. **Add API Endpoints** - Create new PHP files in `api/`
3. **Modify Styles** - Update `src/styles.css` or use Bootstrap/Tailwind
4. **Read Documentation** - Check `README.md` for full documentation
5. **Deploy** - See `DEPLOYMENT.md` for production deployment

## Resources

- [Bootstrap 5 Docs](https://getbootstrap.com/docs/5.3/)
- [Tailwind CSS Docs](https://tailwindcss.com/docs)
- [React Docs](https://react.dev/)
- [PHP 8 Manual](https://www.php.net/manual/en/)
- [MariaDB Docs](https://mariadb.com/kb/en/)

## Need Help?

- 📖 Read `README.md` for detailed information
- 🚀 Check `DEPLOYMENT.md` for production deployment
- 🤝 See `CONTRIBUTING.md` for contribution guidelines
- 🐛 Open an issue on GitHub

Happy coding! 🚀
