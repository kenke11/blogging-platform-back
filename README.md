# Blogging platform back

## Installation

### Clone repository

```bash
git clone https://github.com/kenke11/blogging-platform-back.git
```

### Go to project folder

```bash
cd blogging-platform-back
```

### Create local env file

```bash
cp .env.example .env
```

### change .env

#### Enter your database information

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```

### Install dependencies

```bash
composer install
```

### Generate Application key

```php
php artisan key:generate
```

### Migrate database and seed data

```bash
php artisan migrate --seed
```

### Start local server

```make
php artisan serve
```

### Run tests

```make
php artisan test
```
