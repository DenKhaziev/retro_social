mysql -u retro_user -p retro_social < sql/schema.sql // мигрируем базу данных
php -S localhost:8002 -t public public/router.php - запуск сервера