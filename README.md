
## Resources

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


## FullCalendar
• https://github.com/saade/filament-fullcalendar

## New Resource Steps
•	php artisan make:filament-resource ResourceName
•	check app/Models
•	php artisan shield:generate --all
•	check app/Policy

## CLONE STEPS
<ul>
<li> clone https://github.com/revshare18/Employee-forms.git </li>
<li> copy .env.example to .env </li>
<li> setup .env (database credentials, db name) </li>
<li> npm install </li>
<li> composer install </li>
<li> php artisan migrate </li>
<li> php artisan db:seed --class=LeaveTypeSeeder </li>
<li> php artisan shield:install </li>
<li> composer dump-autoload </li>
<li> npm run build </li>
<li> (local only) php artisan serve </li>
<li> (can access via network) php artisan serve --host=ip </li>
<li> browse http://127.0.0.1:8000/ </li>
</ul>


