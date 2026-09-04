# Maintenance & Tools Folder

This directory contains one-off scripts, migration tools, and legacy utilities that are NOT part of the core application. 

> [!CAUTION]
> DO NOT move these files back to the `public/` directory or project root as they may contain sensitive operations or expose database details.

## Contents:
- `scripts/`: Legacy automation scripts.
- `test_server/`: Temporary local test server configuration.
- Various `.php` and `.sql` files: Database fixes, mock data insertion, and administrative tools (e.g., `create_admin.php`).
