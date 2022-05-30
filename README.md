# Freelancer Job Portal API

## Database Schema

## How to run

### Using docker

This project using laravel sails for easy dev environment.

Make sure [docker](https://www.docker.com/products/docker-desktop) is installed on your machine, then run this command in your terminal:

make a copy of .env file
```sh
cp .env.example .env
```

install composer dependencies
```sh
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v $(pwd):/var/www/html \
    -w /var/www/html \
    laravelsail/php81-composer:latest \
    composer install --ignore-platform-reqs
```

spin up docker container
```sh
./vendor/bin/sail up -d
```

generate app key
```sh
./vendor/bin/sail artisan key:generate
```

migrate database
```sh
./vendor/bin/sail artisan migrate
```

seed database with dummy data
```sh
./vendor/bin/sail artisan db:seed
```

You can access the application at: http://localhost:8080.

Laravel sail documentation: [https://laravel.com/docs/9.x/sail](https://laravel.com/docs/9.x/sail)

### run tests

To run tests, after you have run the sail up command, run this command in your terminal:

```sh
./vendor/bin/sail test
```
