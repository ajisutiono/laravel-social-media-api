# 📱 Social Media API

A RESTful API built with Laravel, providing core social media features: authentication, posts, comments, likes, and user follow/unfollow.  
Designed with clean architecture using Services and Repositories for scalability and maintainability.

---

## ⚙️ Tech Stack

- [Laravel 11](https://laravel.com/)
- MySQL
- Laravel Passport (Authentication & Authorization)
- OpenAPI/Swagger (API documentation)

---

## ✨ Features

- User Authentication (Register, Login, Logout)
- User Profile Management & Profile Picture Upload
- CRUD Posts (Create, Read, Update, Delete)
- Comments on Posts
- Like/Unlike Posts
- Follow/Unfollow Users

---

## 🚀 Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/username/social-media-api.git
   cd social-media-api
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Copy `.env.example` to `.env`**
   ```bash
   cp .env.example .env
   ```

4. **Generate Laravel application key**
   ```bash
   php artisan key:generate
   ```

5. **Configure your database in `.env`, then run migrations and seeders**
   ```bash
   php artisan migrate --seed
   ```

6. **Start the development server**
   ```bash
   php artisan serve
   ```
   The API will be available at [http://localhost:8000/](http://localhost:8000/)

---

## 📖 API Documentation

API docs are available in OpenAPI (Swagger) format in the `docs/` directory.  
You can open it using [Swagger Editor](https://editor.swagger.io/) or import into Postman.

---

## 📂 Project Structure

```
app/
 ┣ Http/
 ┃ ┣ Controllers/
 ┃ ┗ Middleware/
 ┣ Models/
 ┣ Services/        # Business logic
 ┣ Repositories/    # Data access layer
config/
database/
routes/
tests/
```

---

## 🧪 Testing

Run the tests with PHPUnit:
```bash
php artisan test
```
Or use Postman for API testing.

---

## 📄 License

This project is released under the Apache 2.0 License.
