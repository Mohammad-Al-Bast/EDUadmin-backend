<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

---
# EDUadmin Backend Documentation

## 1. Project Overview

EDUadmin Backend is a robust server-side application designed to manage educational administration tasks. It provides APIs and services for handling users, courses, grade changes, and student records, supporting both administrative and student-facing operations. The backend is built for scalability, security, and ease of integration with frontend clients.

## 2. Technologies Used

- **Framework:** Laravel (PHP)
- **Database:** MySQL (configurable)
- **Package Manager:** Composer (PHP), npm (JavaScript assets)
- **Testing:** PHPUnit, Pest
- **Build Tool:** Vite
- **Other Libraries:** FakerPHP, GuzzleHTTP, Symfony components, etc.

## 3. Folder and File Structure

- `app/` – Main application code
  - `Http/Controllers/` – API and web controllers
  - `Http/Middleware/` – Request middleware
  - `Http/Requests/` – Form request validation
  - `Models/` – Eloquent models (AdminUser, Student, Course, etc.)
  - `Providers/` – Service providers
  - `Services/` – Business logic services
- `bootstrap/` – Application bootstrapping
- `config/` – Configuration files (database, mail, cache, etc.)
- `database/`
  - `factories/` – Model factories for testing
  - `migrations/` – Database migration scripts
  - `seeders/` – Database seeders
- `public/` – Publicly accessible files (entry point: `index.php`)
- `resources/`
  - `css/` – Stylesheets
  - `js/` – JavaScript assets
  - `views/` – Blade templates
- `routes/` – Route definitions (`api.php`, `web.php`, `console.php`)
- `storage/` – File, cache, and log storage
- `tests/` – Test suites (Feature, Unit)
- `vendor/` – Composer dependencies
- `artisan` – Laravel CLI tool
- `composer.json` – PHP dependencies and scripts
- `package.json` – JavaScript dependencies
- `phpunit.xml` – PHPUnit configuration
- `README.md` – Project documentation
- `SECURITY_REVIEW.md` – Security review and notes

## 4. Setup Instructions

### Prerequisites

- PHP >= 8.1
- Composer
- Node.js & npm
- MySQL or compatible database

### Steps

1. **Clone the repository:**
	```powershell
	git clone https://github.com/Mohammad-Al-Bast/EDUadmin.git
	cd EDUadmin-backend
	```

2. **Install PHP dependencies:**
	```powershell
	composer install
	```

3. **Install JavaScript dependencies:**
	```powershell
	npm install
	```

4. **Copy and configure environment file:**
	```powershell
	cp .env.example .env
	```
	Edit `.env` to set your database and mail credentials.

5. **Generate application key:**
	```powershell
	php artisan key:generate
	```

6. **Run migrations and seeders:**
	```powershell
	php artisan migrate --seed
	```

7. **Start the development server:**
	```powershell
	php artisan serve
	```

## 5. Usage

- Access the backend via API endpoints (see below) or connect a frontend client.
- For admin operations, authenticate using provided credentials.
- Use the web interface (if available) for direct interaction.
- API requests can be made using tools like Postman or via frontend integration.

## 6. API Endpoints

### Example Endpoints

| Route                        | Method | Description                    | Request Body / Params | Response Format      |
|------------------------------|--------|-------------------------------|----------------------|---------------------|
| `/api/login`                 | POST   | User login                    | `{email, password}`  | `{token, user}`     |
| `/api/users`                 | GET    | List all users                | -                    | `[users]`           |
| `/api/courses`               | GET    | List all courses              | -                    | `[courses]`         |
| `/api/students`              | GET    | List all students             | -                    | `[students]`        |
| `/api/students/{university_id}` | GET | Get student by university ID  | -                    | `{student}`         |
| `/api/change-grade-forms`    | POST   | Submit grade change request   | `{university_id, ...}`| `{status, message}` |
| `/api/logout`                | POST   | Logout user                   | -                    | `{message}`         |

*See `routes/api.php` for the full list of endpoints, parameters, and authentication requirements.*

## 7. Deployment

### VPS/Server Deployment

1. **Upload files to your server.**
2. **Install dependencies:**
	```bash
	composer install
	npm install
	npm run build
	```
3. **Configure `.env` for production.**
4. **Set up web server (e.g., Apache/Nginx) to point to `public/` directory.**
5. **Run migrations:**
	```bash
	php artisan migrate --force
	```
6. **Set up supervisor for queue workers (if needed).**
7. **Configure SSL and security settings.**

### Cloud Platforms

- For platforms like Heroku, DigitalOcean, or AWS, follow their PHP/Laravel deployment guides.
- Vercel/Netlify are not recommended for PHP backends.

## 8. Contribution Guidelines

- Fork the repository and create a feature branch.
- Follow PSR coding standards and write tests for new features.
- Submit pull requests with clear descriptions.
- Report issues via GitHub Issues.

## 9. License

This project is licensed under the MIT License. See the `LICENSE` file for details.

---
## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:


Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners


## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
