# Implementation Plan: PHP Project Restructure

## Overview

This document defines the implementation tasks for restructuring the Opti Ventas PHP project to follow PSR-4 autoloading standards. The migration is organized in 4 phases with incremental verification to ensure zero downtime and full backward compatibility.

**Implementation Language:** PHP 8.3

**Key Principles:**
- Each phase is independently verifiable and rollback-safe
- All 42 helper functions maintain identical signatures
- All 12 requirements are covered with traceability
- Tests are sub-tasks marked with `*` (optional for faster MVP)

---

## Tasks

### Phase 1: Create New Structure (No Breaking Changes)

- [ ] 1. Create configuration directory and files
  - [ ] 1.1 Create config/ directory at project root
    - Create the `config/` directory to centralize all configuration files
    - Command: `mkdir config`
    - _Requirements: 1.1_

  - [ ] 1.2 Create config/app.php with application settings
    - Create application configuration file with name, URL, debug, and session lifetime
    - Use `declare(strict_types=1)` and return array format
    - _Requirements: 1.2, 3.3, 3.4_

  - [ ] 1.3 Create config/database.php with connection settings
    - Create database configuration file with host, port, database name, username, password
    - Use `env()` helper for environment variable access
    - _Requirements: 1.3, 3.3, 3.4_

  - [ ] 1.4 Copy routes.php to config/routes.php
    - Copy existing routes definition to config directory
    - Ensure route definitions remain unchanged for backward compatibility
    - Command: `cp routes.php config/routes.php`
    - _Requirements: 1.4, 3.1_

- [ ] 2. Create Helpers directory structure
  - [ ] 2.1 Create app/Helpers/ directory
    - Create directory to hold domain-organized helper files
    - Command: `mkdir app/Helpers`
    - _Requirements: 2.1_

  - [ ] 2.2 Create app/Helpers/PathHelpers.php
    - Implement path resolution functions: `base_path()`, `app_path()`, `views_path()`, `storage_path()`, `config_path()`
    - Use namespace `App\Helpers`
    - _Requirements: 2.2, 2.4, 10.2_

  - [ ] 2.3 Create app/Helpers/ConfigHelpers.php
    - Implement configuration functions: `config()`, `env()`
    - Use namespace `App\Helpers`
    - _Requirements: 2.2, 2.4, 10.2_

  - [ ] 2.4 Create app/Helpers/OutputHelpers.php
    - Implement output functions: `e()`, `redirect()`, `back()`, `json_response()`, `assert_failed()`
    - Use namespace `App\Helpers`
    - _Requirements: 2.2, 2.4, 10.2_

  - [ ] 2.5 Create app/Helpers/UrlHelpers.php
    - Implement URL functions: `scheme()`, `host()`, `url()`, `base_url()`, `app_script_dir()`, `mount_url_prefix()`
    - Use namespace `App\Helpers`
    - _Requirements: 2.2, 2.4, 10.2_

  - [ ] 2.6 Create app/Helpers/RequestHelpers.php
    - Implement request functions: `is_post()`, `request_method()`, `input()`, `query()`, `json_input()`, `request_path()`
    - Use namespace `App\Helpers`
    - _Requirements: 2.2, 2.4, 10.2_

  - [ ] 2.7 Create app/Helpers/SessionHelpers.php
    - Implement session functions: `flash()`, `consume_flash()`, `flash_errors()`, `errors()`, `has_error()`, `field_error()`, `old_input()`, `old()`, `clear_request_state()`
    - Use namespace `App\Helpers`
    - _Requirements: 2.2, 2.4, 10.2_

  - [ ] 2.8 Create app/Helpers/CsrfHelpers.php
    - Implement CSRF functions: `csrf_token()`, `csrf_field()`, `csrf_token_valid()`
    - Use namespace `App\Helpers`
    - _Requirements: 2.2, 2.4, 10.2_

  - [ ] 2.9 Create app/Helpers/FormatHelpers.php
    - Implement format functions: `now()`, `slugify()`, `money_symbol()`, `money()`, `nullable_string()`, `setting()`
    - Use namespace `App\Helpers`
    - _Requirements: 2.2, 2.4, 10.2_

  - [ ]* 2.10 Write property tests for helper namespace consistency
    - **Property 3: Helper Namespace Consistency**
    - Verify all files declare `namespace App\Helpers`
    - Verify function count per file is 1-15
    - **Validates: Requirements 2.2, 2.4, 10.2**

- [ ] 3. Create PSR-4 Autoloader
  - [ ] 3.1 Create app/Autoloader.php with PSR-4 implementation
    - Implement `register()` method using `spl_autoload_register()`
    - Implement `load()` method with namespace-to-path conversion
    - Implement `resolvePath()` method for class file resolution
    - Handle namespaces: `App\Core`, `App\Controllers`, `App\Models`, `App\Helpers`
    - Throw `RuntimeException` for missing class files
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5_

  - [ ]* 3.2 Write property tests for PSR-4 path resolution
    - **Property 1: PSR-4 Path Resolution**
    - Generate random class names in `App\*` namespace
    - Verify resolved path follows PSR-4 rules (namespace separators → directory separators)
    - **Validates: Requirements 4.1, 4.2, 4.3**

- [ ] 4. Create root index.php redirect
  - [ ] 4.1 Create index.php at project root
    - Implement PHP redirect to `public/index.php`
    - Preserve query parameters from `$_SERVER['QUERY_STRING']`
    - Use HTTP 302 status code
    - _Requirements: 6.1, 6.2, 6.3, 6.4_

  - [ ]* 4.2 Write property tests for query string preservation
    - **Property 5: Query String Preservation in Redirect**
    - Generate random query strings
    - Verify redirect preserves all query parameters
    - **Validates: Requirements 6.2, 6.4**

- [ ] 5. Checkpoint - Phase 1 verification
  - Verify `config/` directory exists with all files
  - Verify `app/Helpers/` directory exists with all 8 helper files
  - Verify `app/Autoloader.php` exists
  - Verify `index.php` exists at root
  - Run existing application to confirm no breaking changes
  - _Requirements: 1.1-1.5, 2.1-2.6_

### Phase 2: Update Bootstrap (Transition State)

- [ ] 6. Create backward compatibility bridge
  - [ ] 6.1 Update app/Helpers.php to load new helper files
    - Add `require_once` statements for all 8 helper files in `app/Helpers/`
    - Maintain global function availability during transition
    - _Requirements: 2.3, 2.6, 10.5_

  - [ ] 6.2 Verify all 42 helper functions remain callable
    - Test each helper function signature is unchanged
    - Test each function works from global scope
    - _Requirements: 2.3, 10.3_

- [ ] 7. Update bootstrap for configuration loading
  - [ ] 7.1 Update app/bootstrap.php to register autoloader first
    - Add `require_once __DIR__ . '/Autoloader.php'`
    - Call `App\Autoloader::register()` before loading config
    - _Requirements: 11.1, 11.2_

  - [ ] 7.2 Update app/bootstrap.php to load config from config/
    - Replace `require __DIR__ . '/../config.php'` with config directory loading
    - Load `config/app.php` and `config/database.php`
    - Maintain `$GLOBALS['__config']` for backward compatibility
    - _Requirements: 11.1, 11.3_

  - [ ] 7.3 Add error handling for missing configuration files
    - Throw `RuntimeException` with descriptive message if config file missing
    - Include expected file path in error message
    - _Requirements: 1.6, 11.5_

  - [ ]* 7.4 Write property tests for configuration return arrays
    - **Property 2: Configuration Return Arrays**
    - Verify all config files return associative arrays
    - Verify all use `declare(strict_types=1)`
    - **Validates: Requirements 3.3, 3.4**

- [ ] 8. Update environment configuration loading
  - [ ] 8.1 Ensure .env is loaded from project root
    - Verify `env()` function reads from correct location
    - _Requirements: 9.1_

  - [ ] 8.2 Add environment variable validation
    - Validate required environment variables on startup
    - Throw descriptive error for missing required variables
    - _Requirements: 9.4, 9.5_

- [ ] 9. Checkpoint - Phase 2 verification
  - Verify bootstrap loads configuration from `config/`
  - Verify all 42 helper functions still callable
  - Test all existing routes still work
  - Check PHP error logs for any warnings
  - _Requirements: 3.5, 2.3, 2.6_

### Phase 3: Update Public Index (Full Autoloader)

- [ ] 10. Update public/index.php entry point
  - [ ] 10.1 Update public/index.php to use autoloader
    - Replace `require_once dirname(__DIR__) . '/app/Core/Database.php'` and similar requires
    - Add `require_once dirname(__DIR__) . '/app/Autoloader.php'`
    - Call `App\Autoloader::register()` before bootstrap
    - _Requirements: 5.1_

  - [ ] 10.2 Update public/index.php to load routes from config
    - Change `require dirname(__DIR__) . '/routes.php'` to `require config_path('routes.php')`
    - _Requirements: 1.5, 11.4_

  - [ ] 10.3 Add error handling for missing autoloader
    - Throw `RuntimeException` if `Autoloader.php` not found
    - Include expected file path in error message
    - _Requirements: 5.5_

- [ ] 11. Remove explicit requires from controllers and models
  - [ ] 11.1 Remove require statements from all controllers
    - Remove `require`/`include` statements for class files
    - Rely on autoloader for all class loading
    - Files: `app/Controllers/*.php`
    - _Requirements: 5.2_

  - [ ] 11.2 Remove require statements from all models
    - Remove `require`/`include` statements for class files
    - Rely on autoloader for all class loading
    - Files: `app/Models/*.php`
    - _Requirements: 5.3_

  - [ ]* 11.3 Write property tests for no explicit requires in controllers/models
    - **Property 6: No Explicit Requires in Controllers and Models**
    - Scan all controller and model files
    - Verify no `require`/`include` statements for class files
    - **Validates: Requirements 5.2, 5.3**

- [ ] 12. Update view path resolution
  - [ ] 12.1 Verify view paths work with new structure
    - Ensure `views_path()` helper resolves correctly
    - Test all view rendering paths
    - _Requirements: 12.1, 12.2_

  - [ ] 12.2 Update view files to use __DIR__ for relative paths
    - Replace hardcoded paths with `__DIR__` based resolution
    - Ensure layouts and partials work correctly
    - _Requirements: 5.4, 12.3, 12.4_

  - [ ]* 12.3 Write property tests for view path resolution
    - **Property 7: View Path Resolution**
    - Test all view file references resolve correctly
    - Test layouts and partials render correctly
    - **Validates: Requirements 12.1**

- [ ] 13. Checkpoint - Phase 3 verification
  - Verify `public/index.php` uses autoloader
  - Verify all Core classes load via autoloader
  - Test all existing routes work
  - Test database connection works
  - Test authentication flows (login, logout, register)
  - _Requirements: 4.1-4.5, 5.1-5.5_

### Phase 4: Cleanup (Remove Legacy Files)

- [ ] 14. Remove legacy files
  - [ ] 14.1 Remove backward compatibility bridge from app/Helpers.php
    - Remove `require_once` statements for helper files
    - Either delete file or leave empty for autoloader-based loading
    - _Requirements: 2.5, 10.4_

  - [ ] 14.2 Delete config.php from project root
    - Remove legacy configuration file after migration complete
    - Verify config loads from `config/` directory
    - _Requirements: 3.2_

  - [ ] 14.3 Delete routes.php from project root
    - Remove legacy routes file after migration to `config/routes.php`
    - _Requirements: 3.1_

- [ ] 15. Final verification and testing
  - [ ] 15.1 Run full application smoke test
    - Test all routes resolve with response time < 2000ms
    - Test database connection establishes < 5000ms
    - Test authentication flows complete < 3000ms
    - _Requirements: 8.1, 8.2, 8.3_

  - [ ] 15.2 Verify security configuration
    - Verify `.env` not accessible via web
    - Verify `config/` directory not accessible via web
    - Verify `app/` directory not accessible via web
    - Test `.htaccess` rules in public directory
    - _Requirements: 7.1-7.5_

  - [ ] 15.3 Verify session configuration
    - Verify session duration is 120 minutes
    - Test session persistence across requests
    - _Requirements: 8.4_

  - [ ] 15.4 Final cleanup and documentation
    - Remove any temporary files created during migration
    - Update any documentation references to file locations
    - Verify no PHP errors in logs
    - _Requirements: 8.5_

---

## Notes

- Tasks marked with `*` are optional property-based tests that can be skipped for faster MVP
- Each task references specific requirements for traceability to the requirements document
- Checkpoints ensure incremental validation at each phase boundary
- The 4-phase approach allows rollback to any previous phase if issues arise
- All 42 helper functions maintain identical signatures throughout migration
- Property tests validate universal correctness properties defined in the design document
- Unit tests validate specific examples and edge cases

## Migration Summary

| Phase | Tasks | Purpose |
|-------|-------|---------|
| Phase 1 | 1-5 | Create new structure without breaking changes |
| Phase 2 | 6-9 | Update bootstrap with backward compatibility bridge |
| Phase 3 | 10-13 | Switch to autoloader, update entry point |
| Phase 4 | 14-15 | Remove legacy files, final verification |

## Task Dependency Graph

```json
{
  "waves": [
    { "id": 0, "tasks": ["1.1", "2.1", "3.1", "4.1"] },
    { "id": 1, "tasks": ["1.2", "1.3", "1.4", "2.2", "2.3", "2.4", "2.5", "2.6", "2.7", "2.8", "2.9"] },
    { "id": 2, "tasks": ["2.10", "3.2", "4.2", "6.1"] },
    { "id": 3, "tasks": ["6.2", "7.1"] },
    { "id": 4, "tasks": ["7.2", "7.3", "7.4", "8.1", "8.2"] },
    { "id": 5, "tasks": ["10.1", "10.2", "10.3", "11.1", "11.2", "11.3"] },
    { "id": 6, "tasks": ["12.1", "12.2", "12.3"] },
    { "id": 7, "tasks": ["14.1", "14.2", "14.3"] },
    { "id": 8, "tasks": ["15.1", "15.2", "15.3", "15.4"] }
  ]
}
```
