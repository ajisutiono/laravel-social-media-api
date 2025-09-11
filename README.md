# 📱 Social Media API

A RESTful API built with Laravel that provides core social media features such as authentication, posts, comments, likes, and user follow/unfollow.  
This project is designed with a clean architecture using Services and Repositories for better scalability and maintainability.

---

## ⚙️ Tech Stack
- [Laravel 11](https://laravel.com/)
- MySQL
- JWT Authentication
- OpenAPI/Swagger (for API documentation)

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

1. Clone the repository
   ```bash
   git clone https://github.com/username/social-media-api.git
   cd social-media-api

2. Install dependencies
   composer install

3. Copy .env.example to .env
   cp .env.example .env

4. Generate Laravel application key
    php artisan key:generate

5. Configure your database in .env, then run migrations and seeders
    php artisan migrate --seed

6. Start the development server

    php artisan serve

    The API will be available at http://localhost:8000/


📖 API Documentation
The API documentation is available in OpenAPI (Swagger) format.
File: docs/*

You can open it using Swagger Editor or import into Postman.

📂 Project Structure

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



🧪 Testing
Run the tests with:
php artisan test
or
postman


📄 License
This project is released under the Apache 2.0 License.
