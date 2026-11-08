# Pergas CRM

Internal customer relationship management system for managing marketers, leads, pipeline status, reports, management notes, and reminders.

## Requirements

- PHP 8.1 or later with MySQLi enabled
- MySQL 8 or compatible MariaDB
- A web server configured to serve this directory

## Database configuration

Keep database credentials outside the repository. Configure the application with these environment variables:

- `PERGASCRM_DB_HOST`
- `PERGASCRM_DB_USERNAME`
- `PERGASCRM_DB_PASSWORD`
- `PERGASCRM_DB_NAME`

For local development, set `PERGASCRM_LOCAL_DB_CONFIG` to the path of a PHP file outside the repository. That file must return an array with `host`, `username`, `password`, and `database` keys. Do not commit the local config or database exports.

This repository intentionally excludes database dumps because they may contain private customer and account data. Obtain an authorized schema/data export through a secure channel before setting up the database.

## Local preview

From the project directory, start PHP's development server:

```powershell
php -S 127.0.0.1:8000
```

Open `http://127.0.0.1:8000/` in a browser. Configure the database before signing in.

## Security notes

- Never put database credentials, production exports, runtime logs, or backup archives in Git.
- Use a unique database account with only the permissions the application needs.
- Serve the application over HTTPS in production.
