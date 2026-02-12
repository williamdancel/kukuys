# Kukuys Live

Kukuys Live is a community web application built for the Kukuys streamer
group (Dota 2 professionals).
It serves as a centralized platform where fans and members can view
streamer profiles, check who is currently live, and access internal
community tools.

Built with Laravel 12 and Vue 3.

------------------------------------------------------------------------

## 🚀 Features

-   🔴 Streamer Profiles
-   📡 Live Stream Status Checker
-   📊 Kukuys Dashboard
-   🎮 Dota Pub Tracker
-   🤝 Taryahan (Matchmaking)
-   📩 Partner Inquiry System
-   🔐 Authentication System

------------------------------------------------------------------------

## 🛠 Tech Stack

### Backend

-   PHP 8+
-   Laravel 12
-   RESTful APIs
-   MySQL

### Frontend

-   Vue 3
-   Tailwind CSS
-   JavaScript (ES6+)
-   Axios
-   Vite

------------------------------------------------------------------------

## 📦 Installation

Clone the repository:

git clone https://github.com/williamdancel/kukuys.git
cd kukuys

Install PHP dependencies:

composer install

Install frontend dependencies:

npm install

Create environment file:

cp .env.example .env

Generate application key:

php artisan key:generate

Configure your database inside `.env`.

Run migrations:

php artisan migrate

------------------------------------------------------------------------

## 👤 Create Admin User (UserSeeder)

Create a seeder for the default admin user:

php artisan make:seeder UserSeeder

Inside `database/seeders/UserSeeder.php`, define your default admin:

``` php
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);
    }
}
```

Then run the seeder:

php artisan db:seed --class=UserSeeder

Default login credentials:

Email: admin@example.com
Password: password

⚠️ Make sure to change the default password in production.

------------------------------------------------------------------------

Build frontend assets:

npm run dev

Start development server:

php artisan serve

------------------------------------------------------------------------

## 🧠 Project Purpose

This project was built as a passion project to:

-   Support the Kukuys streaming community
-   Practice modern Laravel + Vue full-stack architecture
-   Implement real-world dashboard and tracking features
-   Improve API structuring and frontend integration

------------------------------------------------------------------------

## 👨‍💻 Author

William Harry A. Dancel
Full-Stack PHP Developer
GitHub: https://github.com/williamdancel
Email: william.a.dancel@gmail.com

------------------------------------------------------------------------

## 📄 License

This project is for community and demonstration purposes.
