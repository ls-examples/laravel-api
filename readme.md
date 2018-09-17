
[Демонстрация](https://books.prod3.dsxack.com/)
[Репозиторий клиентского приложения](https://gitlab.com/tt-books/books-frontend)
[API](https://booksapi.prod3.dsxack.com/api/documentation)

# Инструкция по развороту 
1. composer update
2. php artisan key:generate
3. создать базу данных, указать настройки в файле .env
4. php artisan migrate --seed
5. php artisan db:seed --class=BookTableSeeder (по необходимости)

# Запуск тестов
```
vendor/bin/phpunit
```
