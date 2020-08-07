composer install
php artisan storage:link
php artisan voyager:install
rm database/database.sqlite 
cp database/database.sqlite.presentation database/database.sqlite 
php artisan serve --host=0.0.0.0 --port=8005