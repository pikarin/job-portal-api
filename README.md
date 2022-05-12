# MX100 Freelancer Job Portal API

## Database Schema
![database schema](https://gitlab.com/aditia1/ajobthing-technical-test/-/raw/main/database-schema.png)

## How to run

### Using docker

This project using laravel sails for easy dev environment.

Make sure [docker](https://www.docker.com/products/docker-desktop) is installed on your machine, then run this command in your terminal:

make a copy of .env file
```sh
cp .env.example .env
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

You can access the application at: http://localhost:8000.

Laravel sail documentation: [https://laravel.com/docs/9.x/sail](https://laravel.com/docs/9.x/sail)

### run tests

To run tests, after you have run the sail up command, run this command in your terminal:

```sh
./vendor/bin/sail test
```
