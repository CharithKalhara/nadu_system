# Nadu System

Nadu System has a Laravel cloud service and a Windows Electron desktop app. The desktop interface is packaged locally; sign-in, company and case data, document generation, and document storage use the cloud API at `https://nadu.office`.

## Desktop app

The desktop app includes a local login screen, dashboard, company workspaces, case entry and editing, document generation and downloads, and administrator user management. It connects to the same `users`, `companies`, and per-company case tables used by the Laravel web application. Document templates and generated Word files remain on the server.

Sign in with an existing Nadu System account. Administrator accounts can use company and document administration. Typing accounts can create typing companies and manage cases in typing companies. Desktop sign-in tokens expire after 30 days, are stored as hashes on the server, and are encrypted on the computer by Electron's secure storage.

## Deploy the desktop API

Deploy the Laravel code to the cloud server and run:

```bash
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize:clear
```

The migration creates the `desktop_tokens` table in the main Nadu System database. Keep the existing `companies` database connection configured for the per-company case tables. The Laravel server must have the Word templates under `storage/app/documents` and write access to `storage/app` for document generation.

## Build the Windows installer

From the project directory on Windows:

```powershell
npm ci
npm run electron:build
```

The installer is written to `dist-electron/Nadu System Setup <version>.exe`. To point the desktop app at another cloud host, change `APP_URL` in `electron/main.js` and rebuild.

The desktop UI is local, but it requires an internet connection to sign in and access shared company data. Changes and generated documents are written to the cloud system, so the web app and all desktop users see the same records.
