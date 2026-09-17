# Requirements Document

## Introduction

This specification defines the requirements for restructuring the Opti Ventas PHP project to follow standard PHP conventions without external frameworks or libraries. The project is a Point of Sale (POS) system built with pure PHP 8.3.30, using a custom MVC architecture, MySQL 8.x database, and Tailwind CSS via CDN. The restructure will improve organization, maintainability, and adherence to PSR-4 autoloading standards while preserving 100% functionality.

## Glossary

- **Application**: The Opti Ventas POS system being restructured
- **Document_Root**: The public-facing directory containing the single entry point (public/index.php)
- **Autoloader**: The PSR-4 compliant class loading mechanism
- **Config_Directory**: The new directory containing application configuration files
- **Helpers_Directory**: The new directory containing auxiliary helper functions
- **Public_Directory**: The directory containing publicly accessible files
- **Entry_Point**: The single index.php file that handles all incoming requests
- **Route_Definition**: Configuration mapping URLs to controllers and actions

## Requirements

### Requirement 1: Create Configuration Directory Structure

**User Story:** As a developer, I want configuration files organized in a dedicated directory, so that configuration is separated from application logic and publicly accessible files.

#### Acceptance Criteria

1. THE Application SHALL have a `config/` directory at the project root level
2. THE config directory SHALL contain `app.php` for application configuration
3. THE config directory SHALL contain `database.php` for database connection configuration
4. THE config directory SHALL contain `routes.php` for route definitions
5. WHEN the Application initializes during bootstrap, THE Application SHALL load all configuration files from the config directory
6. IF a configuration file is missing from the config directory, THEN THE Application SHALL throw a `RuntimeException` with a descriptive message indicating the missing file

### Requirement 2: Create Helpers Directory Structure

**User Story:** As a developer, I want helper functions organized in a dedicated directory, so that utility functions are properly namespaced and autoloadable.

#### Acceptance Criteria

1. THE Application SHALL have a `app/Helpers/` directory
2. THE Helpers directory SHALL contain modular helper files with 1 to 15 functions per file, organized by domain
3. THE Application SHALL maintain all 42 existing helper functions with identical function signatures
4. EACH helper file SHALL use the namespace `App\Helpers`
5. WHEN a helper class is referenced, THE Autoloader SHALL load the file following PSR-4 conventions
6. FOR ALL existing calls to helper functions, THE Application SHALL maintain backward compatibility

### Requirement 3: Migrate Configuration Files

**User Story:** As a developer, I want configuration files moved to the config directory, so that configuration is centralized and separated from the document root.

#### Acceptance Criteria

1. WHEN `routes.php` is migrated, THE Application SHALL move it from the project root to `config/routes.php`
2. WHEN configuration is extracted from `config.php`, THE Application SHALL split it into `config/app.php` for application settings and `config/database.php` for database connection settings
3. FOR ALL configuration files, THE Application SHALL use return arrays for configuration values, not the `define()` function
4. FOR ALL configuration files, THE Application SHALL use PHP 8.3 syntax with `declare(strict_types=1)` at the top of each file
5. WHEN configuration is migrated, THE Application SHALL maintain backward compatibility, verified by all existing routes resolving correctly

### Requirement 4: Update Autoloader for PSR-4 Compliance

**User Story:** As a developer, I want the autoloader to follow PSR-4 standards, so that class loading follows industry best practices.

#### Acceptance Criteria

1. THE Autoloader SHALL use PSR-4 naming conventions with proper namespace-to-directory mapping
2. WHEN a class is referenced, THE Autoloader SHALL resolve the file path by converting the namespace to a directory path and appending `.php`
3. THE Autoloader SHALL handle all existing namespaces: `App\Core` mapping to `app/Core/`, `App\Controllers` mapping to `app/Controllers/`, `App\Models` mapping to `app/Models/`, `App\Helpers` mapping to `app/Helpers/`
4. THE Autoloader SHALL be registered using `spl_autoload_register()` function
5. IF a class file cannot be found or loaded, THEN THE Autoloader SHALL throw a `RuntimeException` with the class name and expected file path in the message

### Requirement 5: Update All Require/Include Statements

**User Story:** As a developer, I want all require/include statements updated to reflect new file locations, so that the application loads all dependencies correctly.

#### Acceptance Criteria

1. WHEN `public/index.php` is updated, THE Application SHALL load the autoloader using `require_once` with an absolute path resolved via `__DIR__`
2. FOR ALL controllers, THE Application SHALL remove explicit require/include statements and rely on the Autoloader
3. FOR ALL models, THE Application SHALL remove explicit require/include statements and rely on the Autoloader
4. FOR ALL views, THE Application SHALL use `__DIR__` for relative path resolution instead of hardcoded paths
5. IF the autoloader file does not exist at the expected path, THEN THE Application SHALL throw a `RuntimeException` with the expected file path in the message

### Requirement 6: Create Root Index.php Redirect

**User Story:** As a developer, I want an index.php file in the root directory that redirects to public/index.php, so that accessing the host directly navigates to the correct entry point.

#### Acceptance Criteria

1. WHEN `index.php` is created at the project root, THE Application SHALL redirect all requests to `public/index.php` using PHP `header('Location: ...')` function
2. WHEN the redirect occurs, THE Application SHALL preserve all query parameters by appending `$_SERVER['QUERY_STRING']` to the redirect URL
3. THE redirect SHALL use HTTP 302 status code for temporary redirect
4. FOR ALL HTTP methods (GET, POST, PUT, DELETE, PATCH), THE Application SHALL perform the redirect consistently

### Requirement 7: Maintain Document Root Configuration

**User Story:** As a developer, I want the public directory to remain the document root, so that security is maintained and only public files are web-accessible.

#### Acceptance Criteria

1. THE Public_Directory SHALL contain `index.php` as the single entry point
2. THE Public_Directory SHALL contain `.htaccess` with URL rewriting rules
3. WHEN a request is made, THE Web_Server SHALL route all requests through `public/index.php`
4. THE `.htaccess` in public directory SHALL deny access to non-public files
5. THE Application SHALL NOT expose sensitive files (`.env`, `config/`, `app/`) via web requests

### Requirement 8: Preserve Existing Functionality

**User Story:** As a business owner, I want all existing features to work exactly as before, so that the restructure does not break the POS system.

#### Acceptance Criteria

1. FOR ALL existing routes, THE Application SHALL resolve them correctly with response time under 2000 milliseconds
2. WHEN database operations are performed, THE Application SHALL establish connection within 5000 milliseconds
3. FOR ALL authentication flows (login, logout, registration), THE Application SHALL complete each flow within 3000 milliseconds
4. THE Application SHALL maintain user sessions with a duration of 120 minutes before expiration
5. IF a database connection fails, THEN THE Application SHALL log the error and display a user-friendly error message without exposing sensitive configuration details

### Requirement 9: Update Environment Configuration Loading

**User Story:** As a developer, I want environment variables loaded from the correct location, so that the application configuration works properly.

#### Acceptance Criteria

1. WHEN `.env` is loaded, THE Application SHALL read it from the project root
2. THE `.env.example` SHALL remain at the project root for documentation purposes
3. WHEN environment variables are loaded, THE Application SHALL use a custom `.env` parser (no external libraries)
4. THE Application SHALL validate required environment variables on startup
5. IF a required environment variable is missing, THEN THE Application SHALL throw a descriptive error

### Requirement 10: Organize Helper Functions by Domain

**User Story:** As a developer, I want helper functions organized by domain, so that related functions are grouped together and easier to maintain.

#### Acceptance Criteria

1. WHEN helpers are organized, THE Application SHALL create separate files for different domains (e.g., `helpers.php`, `format.php`, `validation.php`)
2. EACH helper file SHALL use proper namespace `App\Helpers`
3. FOR ALL helper functions, THE Application SHALL maintain existing function signatures
4. WHEN a helper function is called, THE Autoloader SHALL load the correct file automatically
5. THE Application SHALL maintain backward compatibility for any global helper functions

### Requirement 11: Update Bootstrap Configuration

**User Story:** As a developer, I want the bootstrap file updated to load configuration from new locations, so that the application initializes correctly.

#### Acceptance Criteria

1. WHEN `app/bootstrap.php` is updated, THE Application SHALL load configuration files from the `config/` directory using `require_once` with absolute paths
2. THE Bootstrap SHALL initialize and register the Autoloader before loading any configuration files
3. THE Bootstrap SHALL establish database connection using credentials from `config/database.php`
4. THE Bootstrap SHALL load route definitions from `config/routes.php`
5. IF a configuration file is missing, THEN THE Bootstrap SHALL throw a `RuntimeException` with the missing file name
6. IF database connection fails, THEN THE Bootstrap SHALL throw a `PDOException` with connection details (excluding credentials)
7. IF route definitions contain syntax errors, THEN THE Bootstrap SHALL throw a `ParseError` with the file path

### Requirement 12: Maintain View Path Resolution

**User Story:** As a developer, I want views to render correctly after the restructure, so that the user interface works without modification.

#### Acceptance Criteria

1. FOR ALL view files, THE View_Renderer SHALL resolve paths correctly
2. THE Views directory SHALL remain at the project root level
3. WHEN a view is rendered, THE Application SHALL use correct relative paths
4. THE View layouts and partials SHALL continue to work without modification
5. WHEN view helpers are used, THE Application SHALL resolve them correctly

