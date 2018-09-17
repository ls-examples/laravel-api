
[Демонтсрация](https://books.prod3.dsxack.com/)
[Репозиторий клиентского приложения](http://books.prod3.dsxack.com/)

# Инструкция по развороту 
1. composer update
2. php artisan key:generate
3. создать базу данных, указать настройки в файле .env
4. php artisan migrate --seed
5. php artisan db:seed --class=BookTableSeeder (по необходимости)

