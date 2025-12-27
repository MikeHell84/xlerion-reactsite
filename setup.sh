#!/bin/bash

# Xlerion ReactJS Site Setup Script
# This script helps set up the development environment

set -e

echo "=========================================="
echo "Xlerion ReactJS Site Setup"
echo "=========================================="
echo ""

# Check for required tools
echo "Checking prerequisites..."

# Check PHP
if ! command -v php &> /dev/null; then
    echo "❌ PHP is not installed. Please install PHP 8.0 or higher."
    exit 1
fi

PHP_VERSION=$(php -r "echo PHP_VERSION;" | cut -d. -f1)
if [ "$PHP_VERSION" -lt 8 ]; then
    echo "❌ PHP 8.0 or higher is required. Current version: $(php -v | head -n1)"
    exit 1
fi
echo "✓ PHP $(php -r "echo PHP_VERSION;") found"

# Check Node.js
if ! command -v node &> /dev/null; then
    echo "❌ Node.js is not installed. Please install Node.js 18 or higher."
    exit 1
fi
echo "✓ Node.js $(node -v) found"

# Check npm
if ! command -v npm &> /dev/null; then
    echo "❌ npm is not installed."
    exit 1
fi
echo "✓ npm $(npm -v) found"

# Check MySQL/MariaDB
if ! command -v mysql &> /dev/null; then
    echo "⚠️  MySQL/MariaDB client is not installed. You'll need it to set up the database."
else
    echo "✓ MySQL/MariaDB client found"
fi

echo ""
echo "=========================================="
echo "Environment Configuration"
echo "=========================================="

# Check if .env exists
if [ ! -f .env ]; then
    echo "Creating .env file from .env.example..."
    cp .env.example .env
    echo "✓ .env file created"
    echo "⚠️  Please edit .env and update your database credentials"
else
    echo "✓ .env file already exists"
fi

echo ""
echo "=========================================="
echo "Installing Dependencies"
echo "=========================================="

echo "Installing npm packages..."
npm install

echo ""
echo "=========================================="
echo "Building React Application"
echo "=========================================="

echo "Running production build..."
npm run build

echo ""
echo "=========================================="
echo "Setup Complete!"
echo "=========================================="
echo ""
echo "Next steps:"
echo "1. Edit .env file with your database credentials"
echo "2. Create the database: mysql -u root -p < database/schema.sql"
echo "3. Start PHP development server: php -S localhost:8000 server.php"
echo "4. Open your browser to: http://localhost:8000"
echo ""
echo "For development with hot reload:"
echo "  npm run dev (in one terminal)"
echo "  php -S localhost:8000 server.php (in another terminal)"
echo ""
