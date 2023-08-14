


## REQUIREMENTS

## PHP
•	v8.2 (https://laragon.org/)
•	if windows (setup php version via environment variable->path)

## Composer
•	https://getcomposer.org/download/


## Node.js
•	LTS (https://nodejs.org/en/)

## Laravel 10
•	https://laravel.com/docs/10.x

## Filamentphp 2
•	https://filamentphp.com/docs/2.x/admin/installation

## Icons
• https://v1.heroicons.com/


## New Resource Steps
•	php artisan make:filament-resource ResourceName
•	check app/Models
•	php artisan shield:generate --all
•	check app/Policy

## CLONE STEPS
•	clone https://gitlab.com/_smx/filamentphp10.git 
•	copy .env.example to .env
•	setup .env (database credentials, db name)
•	npm install
•	composer install
•	php artisan key:generate
•	php artisan migrate
•	php artisan shield:install
•	composer dump-autoload
•	npm run build
•	(local only) php artisan serve
•	(can access via network) php artisan serve --host=ip
•	browse http://127.0.0.1:8000/



