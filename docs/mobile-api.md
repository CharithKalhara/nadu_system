# Mobile API v1

The mobile API is a versioned JSON interface for any existing account with valid credentials. All account roles can use mobile entry and access companies currently in the typing queue. It uses Sanctum personal access tokens and the existing `Nadu`, `Company`, and `Document` models. The API does not expose Filament routes and never accepts a database table name. Expired token rows are pruned daily by Laravel's scheduler; keep the scheduler running in production.

## Setup

Install only the Sanctum token table migration during a reviewed deployment:

```sh
php artisan migrate --path=database/migrations/2026_11_14_094036_create_personal_access_tokens_table.php --force
```

The migration creates Sanctum's `personal_access_tokens` table in the default application database. It does not run migrations against the `companies` connection and does not alter company case tables. Configure `SANCTUM_EXPIRATION` in minutes; the default is 10,080 minutes (7 days). Keep `APP_DEBUG=false` in production and serve `/api/*` only through HTTPS.

The existing application has no user-to-company assignment relation. Any authenticated mobile account may select companies whose `status` is `typing` and whose validated `table_name` resolves to an existing `company_####_cases` table. Company status is rechecked for every case and attachment operation.

## Authentication and response format

Success responses wrap the result in `data`. Validation failures use Laravel's JSON `message` and `errors` response. Tokens are returned only on login, hashed by Sanctum in the database, granted the `mobile:use` ability, and expire after the configured duration. Logout revokes only the current token. Login is limited to 6 requests per minute per client IP; authenticated endpoints are limited to 120 requests per minute per account.

| Method | Path | Authentication | Purpose |
| --- | --- | --- | --- |
| `POST` | `/api/v1/auth/login` | Public, rate limited | Accept `email`, `password`, optional `device_name`; any account with valid credentials receives a token |
| `POST` | `/api/v1/auth/logout` | Sanctum token with `mobile:use` ability | Revoke the current token |
| `GET` | `/api/v1/me` | Sanctum token with `mobile:use` ability | Return authenticated user identity |
| `GET` | `/api/v1/companies` | Sanctum token with `mobile:use` ability | Return active typing companies, without table names |
| `GET` | `/api/v1/reference-data` | Sanctum token with `mobile:use` ability | Return the case form sections and field descriptors |
| `GET` | `/api/v1/cases?company_id={id}&search={term}&per_page=25` | Sanctum token with `mobile:use` ability | Paginated company cases, optionally searched by case number or party name |
| `GET` | `/api/v1/cases/{id}?company_id={id}` | Sanctum token with `mobile:use` ability | Return a case only from the selected active typing company |
| `PUT` | `/api/v1/cases/{id}` | Sanctum token with `mobile:use` ability | Validate and update a case in the selected active typing company |
| `POST` | `/api/v1/cases` | Sanctum token with `mobile:use` ability | Validate and create a case in the existing company-specific table |
| `POST` | `/api/v1/attachments` | Sanctum token with `mobile:use` ability | Upload a PDF or image to a case the user can access |

Case creation accepts `company_id`, required `nadu_ankaya`, and the remaining case fields from the existing typing form. It requires an `Idempotency-Key` header. A matching retry replays the original record for 24 hours; reusing a key with different payload returns `409`. Idempotency metadata is stored in Laravel's configured cache, so production must use a shared persistent cache store that supports atomic locks (the current default database cache does).

Attachment requests use multipart fields `company_id`, `case_id`, and `file`. The server permits PDF/JPEG/PNG content up to 10 MB and stores generated-name files on the existing private `local` disk. The original filename is metadata only. The response never exposes the private filesystem path.

Successful login, logout, case creation, and attachment upload write audit log events containing actor and record IDs, without credentials, tokens, or case contents.

## Status codes

- `401`: no valid token or invalid login credentials
- `403`: token lacks mobile ability
- `404`: company is not in the typing queue, a table is invalid/missing, or record is outside the selected company
- `409`: idempotency key conflict or a matching request is still processing
- `422`: request validation failure
- `429`: login rate limit exceeded

## Existing behavior preserved

The API validates the selected company, then sets the trusted request company context and uses the existing `Nadu` model. The model continues to resolve and scope the dynamic company table. API input is whitelisted to the actual typing form fields, and `company_id` is assigned by the existing model event. Attachments use the existing `Document` model and its company/case checks. No Filament panel authorization or case-table schema was changed.
