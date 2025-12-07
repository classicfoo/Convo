# Convo
Turning conversations into wins.

- Product requirements: [`docs/spec.md`](docs/spec.md)

## Development quickstart

1. Install PHP 8.2+.
2. Start the built-in server from the repo root:
   ```bash
   php -S localhost:8000 -t public
   ```
3. Visit `http://localhost:8000/login.php`.

The first run seeds a default admin user `admin` with password `admin123` (or the value of the `CONVO_ADMIN_PASSWORD` environment variable). Use the admin area to create additional users.
