# Development Tools Guide

This document describes the development tools configured for the Global Harmony Initiative website project.

## Overview

This project uses a comprehensive set of PHP and JavaScript development tools to ensure code quality, consistency, and maintainability.

## PHP Development Tools

### 1. Rector - Automated Refactoring

**Purpose**: Automatically refactor and upgrade code, remove dead code, and improve code quality.

**Configuration**: `rector.php`

**Usage**:
```bash
# Dry run (preview changes without applying)
composer rector

# Apply refactoring changes
composer rector:refactor
```

**What it does**:
- Upgrades code from PHP 8.1 to newer versions
- Removes unused code (DEAD_CODE rules)
- Applies CODE_QUALITY improvements
- Simplifies if statements
- Uses modern PHP features

### 2. PHP CS Fixer - Code Style Fixer

**Purpose**: Ensures consistent code formatting across the entire project (PSR-12 standard).

**Configuration**: `.php-cs-fixer.php`

**Usage**:
```bash
# Check code style (dry run)
composer cs-check

# Fix code style automatically
composer cs-fix
```

**What it does**:
- Formats all PHP files to PSR-12 standard
- Ensures consistent indentation, spacing, and structure
- Removes unused imports
- Standardizes array syntax

### 3. PHPStan - Static Analysis

**Purpose**: Finds bugs and logical errors before runtime.

**Configuration**: `phpstan.neon`

**Usage**:
```bash
composer phpstan
```

**What it finds**:
- Methods that might return null but are treated as strings
- If statements that are always true/false
- Calls to methods that don't exist
- Type mismatches

### 4. PHPMD - PHP Mess Detector

**Purpose**: Detects code smells and potential problems.

**Configuration**: `phpmd.xml`

**Usage**:
```bash
composer phpmd
```

**What it detects**:
- Classes that are too large
- Methods that are too long or complex
- Unused local variables
- Code duplication patterns

### 5. Symfony Var Dumper - Enhanced Debugging

**Purpose**: Beautiful, collapsible variable dumper (replacement for var_dump).

**Usage**:
```php
use Symfony\Component\VarDumper\VarDumper;

// Instead of var_dump($variable)
dump($variable); // Global function available after installation
```

### 6. Phinx - Database Migrations

**Purpose**: Version control for database schema.

**Configuration**: Create `phinx.php` in project root (if needed)

**Usage**:
```bash
# Create a new migration
vendor/bin/phinx create MyNewMigration

# Run migrations
vendor/bin/phinx migrate

# Rollback migrations
vendor/bin/phinx rollback
```

## JavaScript Development Tools

### 1. Prettier - Code Formatter

**Purpose**: Automatically formats JavaScript, CSS, and HTML files.

**Configuration**: `.prettierrc.json`, `.prettierignore`

**Usage**:
```bash
# Format all files
npm run format

# Check formatting (without fixing)
npx prettier --check "**/*.{js,css,html,json}"
```

### 2. ESLint - Code Linter

**Purpose**: Finds bugs, bad practices, and logical errors in JavaScript.

**Configuration**: `.eslintrc.json`, `.eslintignore`

**Usage**:
```bash
# Lint JavaScript files
npm run lint

# Lint and auto-fix issues
npm run lint:fix
```

**What it finds**:
- Unused variables
- Impossible-to-reach code
- Best practice violations (e.g., use === instead of ==)

### 3. JSCPD - Duplication Detector

**Purpose**: Finds duplicated code blocks (JavaScript equivalent of phpcpd).

**Usage**:
```bash
npm run check-duplicates
```

**What it finds**:
- Copy/paste code blocks
- Repeated patterns that could be refactored

### 4. Vite - Build Tool

**Purpose**: Modern build tool for bundling and optimizing JavaScript/CSS.

**Configuration**: `vite.config.js`

**Usage**:
```bash
# Start development server with hot reload
npm run dev

# Build for production
npm run build

# Preview production build
npm run preview
```

## Quick Reference Commands

### PHP Quality Checks (All Tools)
```bash
# Run all quality checks (dry run)
composer quality

# Fix all quality issues automatically
composer quality:fix
```

### Individual PHP Tools
```bash
composer cs-check      # Check code style
composer cs-fix        # Fix code style
composer phpstan       # Static analysis
composer phpmd         # Mess detection
composer rector        # Preview refactoring
composer rector:refactor # Apply refactoring
```

### JavaScript Tools
```bash
npm run format         # Format code
npm run lint           # Lint code
npm run lint:fix       # Lint and fix
npm run check-duplicates # Find duplicates
npm run dev            # Development server
npm run build          # Production build
```

## Workflow Recommendations

### Before Committing Code

1. **Format code**:
   ```bash
   composer cs-fix
   npm run format
   ```

2. **Run quality checks**:
   ```bash
   composer quality
   npm run lint
   ```

3. **Fix issues**:
   ```bash
   composer quality:fix
   npm run lint:fix
   ```

### Regular Maintenance

1. **Weekly refactoring**:
   ```bash
   composer rector:refactor
   ```

2. **Check for duplicates**:
   ```bash
   npm run check-duplicates
   composer phpmd
   ```

3. **Update dependencies**:
   ```bash
   composer update
   npm update
   ```

## Configuration Files

- `.php-cs-fixer.php` - PHP CS Fixer rules
- `phpstan.neon` - PHPStan analysis level and paths
- `phpmd.xml` - PHPMD rules and thresholds
- `rector.php` - Rector refactoring rules
- `.prettierrc.json` - Prettier formatting rules
- `.eslintrc.json` - ESLint linting rules
- `vite.config.js` - Vite build configuration
- `.prettierignore` - Files to exclude from Prettier
- `.eslintignore` - Files to exclude from ESLint

## Integration with IDE

Most modern IDEs can integrate with these tools:

- **VS Code**: Install extensions for Prettier, ESLint, PHP CS Fixer
- **PhpStorm**: Built-in support for most PHP tools
- **Sublime Text**: Install packages for Prettier and ESLint

## Troubleshooting

### PHP Tools Not Found
```bash
# Reinstall Composer dependencies
composer install
```

### JavaScript Tools Not Found
```bash
# Reinstall npm dependencies
npm install
```

### Configuration Issues
- Check that configuration files are in the project root
- Verify file paths in configuration files match your project structure
- Ensure excluded directories (vendor, node_modules) are properly configured

## Next Steps

1. Run initial quality checks: `composer quality` and `npm run lint`
2. Fix any issues found
3. Set up pre-commit hooks (optional) to run these tools automatically
4. Integrate with CI/CD pipeline (if applicable)

