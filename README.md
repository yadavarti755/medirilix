# Publish mail views to customize the mail layout

php artisan vendor:publish --tag=laravel-mail

/usr/bin/php /path/to/your/project/artisan queue:work --stop-when-empty --tries=3 --timeout=90
