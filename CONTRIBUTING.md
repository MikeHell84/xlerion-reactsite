# Contributing to Xlerion ReactJS Site

Thank you for your interest in contributing! This document provides guidelines and instructions for contributing to this project.

## Code of Conduct

- Be respectful and inclusive
- Provide constructive feedback
- Focus on what is best for the community
- Show empathy towards other community members

## Getting Started

### Prerequisites

- PHP 8.0+
- Node.js 18+
- MariaDB 10.5+
- Git
- A code editor (VS Code, PHPStorm, etc.)

### Setting Up Development Environment

1. **Fork and Clone**
   ```bash
   git clone https://github.com/YOUR_USERNAME/xlerion-reactsite.git
   cd xlerion-reactsite
   ```

2. **Install Dependencies**
   ```bash
   npm install
   ```

3. **Configure Environment**
   ```bash
   cp .env.example .env
   # Edit .env with your local database credentials
   ```

4. **Set Up Database**
   ```bash
   mysql -u root -p < database/schema.sql
   ```

5. **Build and Run**
   ```bash
   # Terminal 1: Watch and rebuild React on changes
   npm run dev
   
   # Terminal 2: Run PHP development server
   php -S localhost:8000 server.php
   ```

6. **Open Browser**
   Navigate to `http://localhost:8000`

## Development Workflow

### Branch Strategy

- `main` - Production-ready code
- `develop` - Development branch
- `feature/your-feature-name` - Feature branches
- `bugfix/issue-description` - Bug fix branches

### Making Changes

1. **Create a Branch**
   ```bash
   git checkout -b feature/your-feature-name
   ```

2. **Make Your Changes**
   - Write clean, readable code
   - Follow existing code style
   - Comment complex logic
   - Keep changes focused and atomic

3. **Test Your Changes**
   - Test in multiple browsers
   - Test responsive design
   - Test API endpoints
   - Verify database interactions

4. **Commit Your Changes**
   ```bash
   git add .
   git commit -m "Add: Brief description of your changes"
   ```

   Commit message prefixes:
   - `Add:` - New feature
   - `Fix:` - Bug fix
   - `Update:` - Update existing feature
   - `Refactor:` - Code refactoring
   - `Docs:` - Documentation changes
   - `Style:` - Code style changes
   - `Test:` - Adding or updating tests

5. **Push and Create Pull Request**
   ```bash
   git push origin feature/your-feature-name
   ```
   
   Then create a Pull Request on GitHub.

## Code Style Guidelines

### PHP

- Follow PSR-12 coding standard
- Use meaningful variable and function names
- Use type hints where applicable
- Write PHPDoc comments for functions
- Use prepared statements for database queries
- Validate and sanitize all input

Example:
```php
<?php
/**
 * Get user by ID
 *
 * @param int $userId User ID
 * @return array|null User data or null
 */
function getUserById(int $userId): ?array {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetch() ?: null;
}
```

### JavaScript/React

- Use functional components with hooks
- Use meaningful component and variable names
- Keep components small and focused
- Use PropTypes or TypeScript for type checking
- Follow React best practices
- Use ES6+ features

Example:
```javascript
import React, { useState, useEffect } from 'react';

const UserProfile = ({ userId }) => {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchUser();
  }, [userId]);

  const fetchUser = async () => {
    try {
      const response = await fetch(`/api/user.php?id=${userId}`);
      const data = await response.json();
      setUser(data);
    } catch (error) {
      console.error('Failed to fetch user:', error);
    } finally {
      setLoading(false);
    }
  };

  if (loading) return <div>Loading...</div>;
  
  return (
    <div className="user-profile">
      <h2>{user.name}</h2>
      <p>{user.email}</p>
    </div>
  );
};

export default UserProfile;
```

### CSS

- Use Bootstrap classes where possible
- Use Tailwind utilities for custom styling
- Keep custom CSS minimal
- Use BEM naming for custom classes
- Ensure responsive design

### SQL

- Use meaningful table and column names
- Add appropriate indexes
- Use foreign keys for relationships
- Include comments for complex queries
- Use transactions for multi-step operations

## Project Structure

```
xlerion-reactsite/
├── api/              # PHP API endpoints
│   ├── base.php      # Common API functions
│   └── *.php         # Individual endpoints
├── config/           # Configuration
│   ├── database.php  # Database connection
│   └── env.php       # Environment loader
├── database/         # Database schemas
│   └── schema.sql    # Database structure
├── public/           # Web root (generated)
├── src/              # React source
│   ├── components/   # Reusable components
│   ├── sections/     # Page sections
│   ├── App.jsx       # Main app
│   └── index.js      # Entry point
└── ...
```

## Adding New Features

### Adding a New React Component

1. Create component file in `src/components/`
2. Export the component
3. Import and use in appropriate section or App.jsx
4. Add styles if needed
5. Test the component

### Adding a New API Endpoint

1. Create PHP file in `api/` directory
2. Include `base.php` for common functions
3. Implement proper error handling
4. Validate and sanitize input
5. Use prepared statements for database queries
6. Return JSON responses
7. Test the endpoint

Example:
```php
<?php
require_once __DIR__ . '/base.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        sendError('Method not allowed', 405);
    }

    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT * FROM your_table WHERE active = 1");
    $stmt->execute();
    $results = $stmt->fetchAll();

    sendResponse([
        'success' => true,
        'data' => $results
    ]);
} catch (Exception $e) {
    error_log('API Error: ' . $e->getMessage());
    sendError('Internal server error', 500);
}
```

### Adding a New Section

1. Create section file in `src/sections/`
2. Design the section layout
3. Add to `App.jsx`
4. Style appropriately
5. Test responsiveness

## Testing

### Manual Testing Checklist

- [ ] All links work correctly
- [ ] Forms submit and validate properly
- [ ] API endpoints return expected data
- [ ] Responsive design works on mobile, tablet, desktop
- [ ] Browser compatibility (Chrome, Firefox, Safari, Edge)
- [ ] No console errors
- [ ] Database operations work correctly
- [ ] Error handling works as expected

### Testing API Endpoints

Use curl or Postman:
```bash
# GET request
curl http://localhost:8000/api/example.php

# POST request
curl -X POST http://localhost:8000/api/contact.php \
  -H "Content-Type: application/json" \
  -d '{"name":"Test","email":"test@example.com","message":"Test message"}'
```

## Pull Request Process

1. **Update Documentation** - Update README or other docs if needed
2. **Test Thoroughly** - Ensure all functionality works
3. **Clean Commit History** - Squash commits if necessary
4. **Descriptive PR Title** - Clearly describe what the PR does
5. **PR Description** - Explain why the change is needed
6. **Link Issues** - Reference related issues
7. **Request Review** - Tag relevant reviewers
8. **Address Feedback** - Respond to review comments

### PR Template

```markdown
## Description
Brief description of changes

## Type of Change
- [ ] Bug fix
- [ ] New feature
- [ ] Breaking change
- [ ] Documentation update

## Testing
How was this tested?

## Checklist
- [ ] Code follows project style guidelines
- [ ] Self-review completed
- [ ] Comments added for complex code
- [ ] Documentation updated
- [ ] No new warnings generated
- [ ] Tested in multiple browsers
- [ ] Responsive design verified
```

## Security

### Reporting Vulnerabilities

If you discover a security vulnerability:
1. **DO NOT** open a public issue
2. Email the maintainers privately
3. Provide detailed information
4. Wait for response before disclosure

### Security Best Practices

- Never commit sensitive data (.env files, passwords, API keys)
- Always validate and sanitize user input
- Use prepared statements for SQL queries
- Implement proper authentication and authorization
- Keep dependencies updated
- Follow OWASP guidelines

## Questions?

- Open an issue for bugs or feature requests
- Check existing issues before creating new ones
- Be clear and provide examples
- Include environment details for bugs

## License

By contributing, you agree that your contributions will be licensed under the same license as the project (MIT License).

## Recognition

Contributors will be recognized in the project README and release notes.

Thank you for contributing to make Xlerion better!
