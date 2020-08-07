composer install
php artisan storage:link
rm database/database.sqlite 
touch database/database.sqlite 
php artisan voyager:install
php artisan migrate 
php artisan db:seed 
php artisan db:seed --class=LanchoneteSeeder 
php artisan serve --host=0.0.0.0 --port=8005