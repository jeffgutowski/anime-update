#!/bin/bash
chmod 777 storage/app/;
chmod 777 storage/app/public/games/;
chmod 777 storage/app/public/users/;
chmod 777 storage/app/public/listings/;
chmod 777 storage/app/public/articles/;
chmod 777 storage/framework/;
chmod 777 storage/framework/cache/;
chmod 777 storage/framework/sessions/;
chmod 777 storage/framework/views/;
chmod 777 storage/logs/;
chmod 777 bootstrap/cache/;
chmod 777 .env;
chmod 777 config/app.php;
echo "File permissions are now unsecure";