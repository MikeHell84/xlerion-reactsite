# Xlerion.com - React Site with PHP Backend

A modern website built with PHP 8, MariaDB, Bootstrap 5, TailwindCSS, and ReactJS.

## Features

- **PHP 8 Backend** - Modern PHP with PDO for database operations
- **MariaDB Database** - Reliable and performant database
- **ReactJS Frontend** - Component-based UI with local compilation
- **Bootstrap 5** - Responsive design framework
- **TailwindCSS** - Utility-first CSS framework
- **Modular Architecture** - Separated components and sections
- **API Endpoints** - RESTful API in `/api` folder
- **Environment Configuration** - Secure `.env` file for credentials

## Project Structure

```
xlerion-reactsite/
├── api/                    # PHP API endpoints
│   ├── base.php           # Common API functions
│   ├── contact.php        # Contact form endpoint
│   └── example.php        # Example endpoint
├── config/                 # Configuration files
│   ├── database.php       # Database connection handler
│   └── env.php            # Environment loader
├── database/              # Database schemas
│   └── schema.sql         # MariaDB schema
├── public/                # Web root (generated)
│   ├── index.html         # Main HTML (generated)
│   └── js/                # Compiled JS (generated)
├── src/                   # React source code
│   ├── components/        # Global components
│   │   ├── Navbar.jsx     # Navigation bar
│   │   └── Footer.jsx     # Footer
│   ├── sections/          # Page sections
│   │   ├── Hero.jsx       # Hero section
│   │   ├── About.jsx      # About section
│   │   ├── Services.jsx   # Services section
│   │   └── Contact.jsx    # Contact section
│   ├── App.jsx            # Main App component
│   ├── index.js           # React entry point
│   ├── styles.css         # Global styles
│   └── template.html      # HTML template
├── .env                   # Environment variables (not in git)
├── .env.example           # Environment template
├── .gitignore             # Git ignore rules
├── package.json           # Node dependencies
├── webpack.config.js      # Webpack configuration
├── tailwind.config.js     # Tailwind configuration
└── postcss.config.js      # PostCSS configuration
```

## Installation

### Prerequisites

- PHP 8.0 or higher
- MariaDB 10.5 or higher
- Node.js 18 or higher
- npm or yarn

### Setup Steps

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd xlerion-reactsite
   ```

2. **Configure environment**
   ```bash
   cp .env.example .env
   # Edit .env with your database credentials
   ```

3. **Setup database**
   ```bash
   mysql -u root -p < database/schema.sql
   ```

4. **Install Node dependencies**
   ```bash
   npm install
   ```

5. **Build React application**
   ```bash
   npm run build
   ```

## Development

### Build Commands

- **Production build**: `npm run build`
- **Development build with watch**: `npm run dev`
- **Development server**: `npm start` (runs on port 3000)

### PHP Development Server

Start the PHP built-in server:
```bash
php -S localhost:8000 -t public server.php
```

Or for API testing:
```bash
cd public
php -S localhost:8000
```

### Database Setup

Import the schema:
```bash
mysql -u root -p < database/schema.sql
```

Or manually:
```sql
CREATE DATABASE xlerion_db;
USE xlerion_db;
SOURCE database/schema.sql;
```

## API Endpoints

### Example Endpoint
- **URL**: `/api/example.php`
- **Method**: GET
- **Description**: Returns example data

### Contact Form
- **URL**: `/api/contact.php`
- **Method**: POST
- **Body**: `{ "name": "string", "email": "string", "message": "string" }`
- **Description**: Submits contact form

## Configuration

### Environment Variables

Edit `.env` file with your settings:

```env
DB_HOST=localhost
DB_PORT=3306
DB_NAME=xlerion_db
DB_USER=your_username
DB_PASS=your_password
DB_CHARSET=utf8mb4

APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost

API_VERSION=v1
```

### Database Connection

The database connection uses PDO and is configured in `config/database.php`. It automatically loads credentials from the `.env` file.

## Components

### Global Components

- **Navbar** - Site navigation with Bootstrap
- **Footer** - Site footer with links and social media

### Section Components

- **Hero** - Landing section with CTA buttons
- **About** - About section with feature cards
- **Services** - Services grid with icons
- **Contact** - Contact form with API integration

## Security

- Environment variables in `.env` (not in version control)
- PDO prepared statements for SQL queries
- Input validation and sanitization
- CORS headers configured in API
- XSS protection with proper escaping

## Deployment

### Production Build

1. Build React app: `npm run build`
2. Configure web server (Apache/Nginx) to serve `public/` directory
3. Update `.env` with production credentials
4. Ensure PHP 8+ and required extensions are installed
5. Import database schema
6. Set proper file permissions

### Apache Configuration Example

```apache
<VirtualHost *:80>
    DocumentRoot /path/to/xlerion-reactsite/public
    ServerName xlerion.com

    <Directory /path/to/xlerion-reactsite/public>
        AllowOverride All
        Require all granted
    </Directory>

    # API routing
    Alias /api /path/to/xlerion-reactsite/api
    <Directory /path/to/xlerion-reactsite/api>
        Require all granted
    </Directory>
</VirtualHost>
```

### Nginx Configuration Example

```nginx
server {
    listen 80;
    server_name xlerion.com;
    root /path/to/xlerion-reactsite/public;
    index index.html;

    location / {
        try_files $uri $uri/ /index.html;
    }

    location /api {
        alias /path/to/xlerion-reactsite/api;
        location ~ \.php$ {
            fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
            fastcgi_index index.php;
            include fastcgi_params;
            fastcgi_param SCRIPT_FILENAME $request_filename;
        }
    }
}
```

## License

MIT

## Support

For issues or questions, please open an issue on GitHub.
