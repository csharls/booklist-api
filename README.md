# Booklist API

[![tests](https://github.com/csharls/booklist-api/actions/workflows/tests.yml/badge.svg)](https://github.com/csharls/booklist-api/actions/workflows/tests.yml)

A small REST API for managing a reading list, built with **Laravel 13** and PHP 8.3. The domain is intentionally simple so the focus stays on how the API is built: request validation, consistent JSON responses, correct status codes and feature tests.

## Endpoints

| Method | URI | Description | Success |
|---|---|---|---|
| `GET` | `/api/books` | List books, newest first, paginated (15 per page) | `200` |
| `POST` | `/api/books` | Add a book | `201` |
| `DELETE` | `/api/books/{id}` | Delete a book | `204` |

### Create a book

```http
POST /api/books
Content-Type: application/json
Accept: application/json

{ "title": "Clean Code" }
```

```json
{
  "data": {
    "id": 1,
    "title": "Clean Code",
    "created_at": "2026-11-11T22:30:00+00:00"
  }
}
```

A missing or too long title (over 255 characters) returns `422` with the validation errors. Deleting a book that does not exist returns `404`.

## How it's built

- **Form Request validation** (`StoreBookRequest`): controllers only receive validated data.
- **API Resources** (`BookResource`): one place that defines the JSON shape, so the database schema never leaks into responses.
- **Mass-assignment protection** with the `#[Fillable]` model attribute.
- **Route model binding** for deletes, which gives `404` for unknown ids for free.
- **Feature tests** with PHPUnit and an in-memory SQLite database, covering listing, pagination, creation, validation and deletion.
- **CI** with GitHub Actions: code style check with Laravel Pint, then the test suite, on every push.

## Run it locally

Requirements: PHP 8.3+, Composer, SQLite.

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

The API is then available at `http://localhost:8000/api/books`.

## Tests

```bash
php artisan test
```
