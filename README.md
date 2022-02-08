## Setup
1. Run `composer install`
2. Run `npm install`
3. Run `npm run dev`
4. Copy `.env.example` to `.env`
5. Update Database details in the .env file
6. Run `php artisan migrate --seed`
7. Admin Login credentials will be seeded directly through seeder.(You will find them in POST MAN Collection)
8. Run `php artisan serve`
9. Now if you have are running the server on different IP/PORT then update the `base_url` variable in postman collection.
10. Thats It.
