# Vido Game Odyssey
Current ip adddress: http://3.81.75.12/

## Installation Requirements
- mysql version 5.7.30
- php version 7.2.17
- OpenSSL PHP Extension
- PDO PHP Extension
- Mbstring PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension
- Ctype PHP Extension
- JSON PHP Extension
- GD Library >= 2.0

## Installation Instructions
1. Clone Repo: `git clone git@github.com:kellyreef/video-game-odyssey.git`
2. Copy .env.example: `cp .env.example .env`
3. Create mysql database and import from latest copy
4. Add database credentials to .env file
    - DB_DATABASE=
    - DB_USERNAME=
    - DB_PASSWORD=
5. Install Packages: `composer install`
6. Run migrations to be sure database is caught up: `php artisan migrate`
7. For quick start execute `php artisan serve` to have local site available at: http://127.0.0.1:8000/
