<<<<<<< HEAD
# nlrc-lumi
This is for the system of Finnish Languages
=======
## NLRC-LUMI

A web implementation for onboarding trainees with support for admin panels for staff members.

## Tools Used
- Filament for admin pages
- Laravel 11.x
- Livewire for UI framework
- PHP 8.2.x
- Tailwind CSS for styles
- XAMPP to run web server

## Installation and Setup

1. Enable the ff. extensions in your `php.ini` by removing the semi-colon in front of it:
    - extension=fileinfo
    - extension=intl
    - extension=pdo_mysql
    - extension=pdo_sqlite
    - extension=sqlite3
    - extension=zip
2. Run XAMPP. Start Apache and MySQL to run the server and database, respectively.
3. Create a new `.env` and use the `.env-example` as basis. Replace the necessary DB info with your credentials of phpMyAdmin.
3. Run `php artisan migrate --seed` to apply migrations and populate the database with pre-defined records (seeders).
4. Open a new terminal. Run `npm run dev` to start Vite.
5. Open a 2nd terminal Run `php artisan serve` to start the application.
6. Finally, open a 3rd terminal for all other commands.
7. *[OPTIONAL]*: Open a 4th terminal. Run `php artisan queue:work` to run the queue worker. This is for cronjobs such as the importing of users in the admin panel side.
>>>>>>> 6effe2a (first commit)
