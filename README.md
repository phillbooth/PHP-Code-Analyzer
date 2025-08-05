# PHP Code Analyzer

This is a standalone, command-line PHP tool designed to perform static code analysis on PHP files. It allows developers to enforce coding standards, identify potential code smells, and flag security risks based on dynamically loaded, configurable JSON rulesets.

## Features

* **Configurable Rulesets:** Load different sets of rules (e.g., generic PHP, Laravel-specific, security-focused, PHP 8.3-specific) from JSON files.
* **Targeted Analysis:** Analyze specific files or entire directories.
* **Exclusion Support:** Automatically ignores common non-source directories like `vendor/`, `node_modules/`, `storage/`, etc.
* **Detailed Reporting:** Provides clear output in the console, indicating the file, line number, rule name, description, and the offending code.
* **Security Checks:** Includes rules to identify common security vulnerabilities and anti-patterns.
* **Code Quality Checks:** Flags stylistic issues and encourages modern PHP practices (e.g., type hints, strict comparison, PHP 8.x features).
* **File/Folder Naming Conventions:** Checks for problematic characters or spaces in file and directory names.
* **Exposed File Detection:** Identifies potentially sensitive files (like `.env`, `config.php`, logic files) that might be directly accessible via a web server.

## Requirements

* PHP >= 8.0 (with `json` extension enabled)
* Composer (optional, but recommended for dependency management and autoloading)

## Installation

1.  **Clone or Download:**
    Download or clone this repository to your local machine.

    ```bash
    git clone [https://github.com/your-repo/php-code-analyzer.git](https://github.com/your-repo/php-code-analyzer.git) # Replace with your actual repo
    cd php-code-analyzer
    ```

2.  **Install Composer Dependencies (Optional but Recommended):**
    If you plan to add any PHP libraries in the future or simply want to use Composer's autoloader for better structure, run:

    ```bash
    composer install
    ```
    This will create the `vendor/` directory and `composer.lock` file.

3.  **Ensure Rulesets are in Place:**
    Verify that the `rules/` directory exists in the root of your project and contains the `.json` ruleset files:

    ```
    php-code-analyzer/
    ├── code-reviewer.php
    ├── composer.json (if used)
    ├── vendor/       (if composer install was run)
    └── rules/
        ├── php/
        │   ├── generic.json
        │   ├── php8-3.json
        │   ├── security.json
        │   └── laravel/
        │       └── v12.json
    ```

## Usage

Navigate to the root directory of the `php-code-analyzer` project in your terminal.

### Basic Usage

To run a code review, specify the `--path` to the code you want to analyze and optionally the `--ruleset` to use.

```bash
php code-reviewer.php --path=<path_to_code> [--ruleset=<ruleset_name>]