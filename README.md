# 🎓 EDUadmin - Complete Learning & Productivity Platform

[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-777BB4.svg?style=flat-square&logo=php)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20.svg?style=flat-square&logo=laravel)](https://laravel.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg?style=flat-square)](LICENSE)
[![Build Status](https://img.shields.io/badge/Build-Passing-brightgreen.svg?style=flat-square)](https://github.com/Mohammad-Al-Bast/EDUadmin)

<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="300" alt="Laravel Logo">
</p>

**EDUadmin** is a comprehensive educational administration platform built with Laravel 12, designed to streamline academic processes, manage student records, handle course registrations, and facilitate grade change requests with enterprise-level security and scalability.

---

## 📋 Table of Contents

-   [🚀 Quick Start](#-quick-start)
-   [✨ Features](#-features)
-   [🏗️ Architecture](#️-architecture)
-   [🛠️ Installation](#️-installation)
-   [⚙️ Configuration](#️-configuration)
-   [📚 API Documentation](#-api-documentation)
-   [🧪 Testing](#-testing)
-   [🚀 Deployment](#-deployment)
-   [🔧 Development](#-development)
-   [📊 Monitoring](#-monitoring)
-   [🤝 Contributing](#-contributing)

---

## 🚀 Quick Start

### System Requirements

-   **PHP**: 8.2 or higher
-   **Database**: MySQL 8.0+ / PostgreSQL 13+ / SQLite 3.35+
-   **Node.js**: 18+ (for asset compilation)
-   **Composer**: 2.x
-   **Memory**: 512MB minimum, 2GB recommended

### One-Line Installation

```powershell
git clone https://github.com/Mohammad-Al-Bast/EDUadmin.git && cd EDUadmin-backend && composer install && cp .env.example .env && php artisan key:generate
```

---

## ✨ Features

### 🔐 Authentication & Security

-   **Multi-factor Authentication** - Email verification + Admin approval
-   **Role-based Access Control** - Admin/Student/Guest permissions
-   **API Token Management** - Laravel Sanctum integration
-   **Password Security** - Hashing, reset, and strength validation
-   **Security Headers** - XSS, CSRF, and CORS protection

### 👥 User Management

-   **User Profiles** - Comprehensive profile management
-   **Admin Controls** - User verification, blocking, password reset
-   **Email Management** - Multi-email support per user
-   **Campus & School** - Organizational structure support
-   **Audit Logging** - Track all user activities

### 📚 Academic Management

-   **Student Records** - University ID, personal info, academic status
-   **Course Catalog** - Course management with detailed information
-   **Grade Change Requests** - Streamlined grade modification process
-   **Course Registration/Drop** - Semester-based enrollment system
-   **Reporting System** - PDF generation for academic records

### 📊 Dashboard & Analytics

-   **Real-time Statistics** - User counts, course metrics, form statistics
-   **Performance Metrics** - System health and usage analytics
-   **Data Visualization** - Charts and graphs for insights
-   **Export Capabilities** - CSV, Excel, PDF export options

### 📱 API & Integration

-   **RESTful API** - Full CRUD operations via API
-   **Rate Limiting** - Prevent API abuse
-   **Pagination** - Efficient data handling
-   **File Uploads** - Image and document handling
-   **Import/Export** - Bulk data operations

---

## 🏗️ Architecture

### System Architecture

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Frontend      │    │   API Gateway   │    │   Database      │
│   (React/Vue)   │◄──►│   (Laravel)     │◄──►│   (MySQL)       │
└─────────────────┘    └─────────────────┘    └─────────────────┘
                              │
                    ┌─────────┴─────────┐
                    │                   │
            ┌───────▼────────┐  ┌──────▼──────┐
            │  File Storage  │  │   Queue     │
            │  (Local/S3)    │  │  (Redis)    │
            └────────────────┘  └─────────────┘
```

### Database Schema

```sql
users
├── id, name, email, password
├── is_verified, is_admin
├── campus, school, profile
└── timestamps

students
├── id, university_id, user_id
├── first_name, last_name
├── academic_info
└── timestamps

courses
├── id, name, code, credits
├── description, semester
└── timestamps

change_grade_forms
├── id, student_university_id
├── current_grade, requested_grade
├── justification, status
└── timestamps
```

### Middleware Stack

-   **SecurityHeaders**: XSS, Content Security Policy
-   **EnsureUserIsAdmin**: Admin-only routes protection
-   **EnsureAdminAndVerified**: Verified admin routes
-   **Sanctum Authentication**: Token-based authentication
-   **CORS**: Cross-origin request handling
-   **Throttling**: Rate limiting (60 requests/minute)

---

## 🛠️ Installation

### Development Setup

```powershell
# 1. Clone repository
git clone https://github.com/Mohammad-Al-Bast/EDUadmin.git
cd EDUadmin-backend

# 2. Install PHP dependencies
composer install

# 3. Install Node dependencies
npm install

# 4. Environment setup
copy .env.example .env
php artisan key:generate

# 5. Database setup
php artisan migrate --seed

# 6. Build assets
npm run dev

# 7. Start development server
php artisan serve
```

### Docker Setup

```yaml
# docker-compose.yml
version: "3.8"
services:
    app:
        build: .
        ports:
            - "8000:8000"
        environment:
            - DB_HOST=mysql
            - DB_DATABASE=eduadmin
    mysql:
        image: mysql:8.0
        environment:
            - MYSQL_DATABASE=eduadmin
            - MYSQL_ROOT_PASSWORD=secret
```

---

## ⚙️ Configuration

### Environment Variables

```env
# Application
APP_NAME="EDUadmin"
APP_ENV=local
APP_KEY=base64:generated_key_here
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_TIMEZONE=UTC

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=eduadmin
DB_USERNAME=root
DB_PASSWORD=

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls

# Queue Configuration
QUEUE_CONNECTION=database
QUEUE_FAILED_DRIVER=database

# Session & Cache
SESSION_DRIVER=file
SESSION_LIFETIME=120
CACHE_DRIVER=file

# Security
SANCTUM_STATEFUL_DOMAINS=localhost,127.0.0.1,::1
SESSION_DOMAIN=null
```

### Performance Configuration

```php
// config/app.php
'providers' => [
    // Optimized service providers
    App\Providers\AppServiceProvider::class,
    App\Providers\AuthServiceProvider::class,
],

// config/database.php
'connections' => [
    'mysql' => [
        'options' => [
            PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
        ],
        'dump' => [
            'dump_binary_path' => '/usr/bin',
        ],
    ],
],
```

---

## 📚 API Documentation

### Authentication Endpoints

```http
POST /api/v1/auth/register
POST /api/v1/auth/login
POST /api/v1/auth/logout
GET  /api/v1/auth/user
PUT  /api/v1/auth/profile
```

### User Management

```http
GET    /api/v1/users              # List all users (Admin)
GET    /api/v1/users/{id}         # Get user details
POST   /api/v1/users              # Create user (Admin)
PUT    /api/v1/users/{id}         # Update user
DELETE /api/v1/users/{id}         # Delete user (Admin)
POST   /api/v1/users/{id}/verify  # Verify user (Admin)
```

### Student Management

```http
GET    /api/v1/students                    # List students
GET    /api/v1/students/{university_id}    # Get student
POST   /api/v1/students                    # Create student (Admin)
PUT    /api/v1/students/{id}               # Update student
DELETE /api/v1/students/{id}               # Delete student (Admin)
```

### Course Management

```http
GET    /api/v1/courses           # List courses
GET    /api/v1/courses/{id}      # Course details
POST   /api/v1/courses           # Create course (Admin)
PUT    /api/v1/courses/{id}      # Update course (Admin)
DELETE /api/v1/courses/{id}      # Delete course (Admin)
```

### Grade Change Forms

```http
GET    /api/v1/change-grade-forms              # List forms
POST   /api/v1/change-grade-forms              # Submit form
GET    /api/v1/change-grade-forms/{id}         # Form details
PUT    /api/v1/change-grade-forms/{id}         # Update form
GET    /api/v1/change-grade-forms/{id}/report  # Generate report
POST   /api/v1/change-grade-forms/{id}/email   # Email report
```

### Course Registration/Drop

```http
GET    /api/v1/register-drop-courses                      # List forms
POST   /api/v1/register-drop-courses                      # Create form
GET    /api/v1/register-drop-courses/{id}                 # Form details
PUT    /api/v1/register-drop-courses/{id}                 # Update form
GET    /api/v1/register-drop-courses/student/{univ_id}    # Student forms
```

### Dashboard & Statistics

```http
GET /api/v1/dashboard/summary    # Overview statistics
GET /api/v1/dashboard/stats      # Detailed analytics
```

### Response Format

```json
{
    "success": true,
    "message": "Operation completed successfully",
    "data": {
        "user": {
            "id": 1,
            "name": "Mohammad Al Bast",
            "email": "mohammad@example.com",
            "is_admin": true,
            "is_verified": true
        }
    },
    "meta": {
        "current_page": 1,
        "per_page": 15,
        "total": 100
    }
}
```

### Error Responses

```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "email": ["The email field is required."],
        "password": ["The password must be at least 8 characters."]
    },
    "code": 422
}
```

---

## 🧪 Testing

### Test Structure

```
tests/
├── Feature/           # Integration tests
│   ├── AuthTest.php
│   ├── UserTest.php
│   └── CourseTest.php
├── Unit/              # Unit tests
│   ├── ModelTest.php
│   └── ServiceTest.php
└── Pest.php          # Test configuration
```

### Running Tests

```powershell
# Run all tests
php artisan test

# Run with coverage
php artisan test --coverage

# Run specific test
php artisan test --filter UserTest

# Run Pest tests
./vendor/bin/pest

# Parallel testing
php artisan test --parallel
```

### Test Examples

```php
// Feature Test Example
test('user can login with valid credentials', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password'),
        'is_verified' => true,
    ]);

    $response = $this->postJson('/api/v1/auth/login', [
        'email' => 'test@example.com',
        'password' => 'password',
    ]);

    $response->assertStatus(200)
             ->assertJsonStructure(['token', 'user']);
});

// Unit Test Example
test('user can perform admin actions when verified admin', function () {
    $user = User::factory()->create([
        'is_admin' => true,
        'is_verified' => true,
    ]);

    expect($user->canPerformAdminActions())->toBeTrue();
});
```

---

## 🚀 Deployment

### Production Checklist

-   [ ] Environment variables configured
-   [ ] Database migrations run
-   [ ] SSL certificate installed
-   [ ] Caching configured (Redis/Memcached)
-   [ ] Queue workers configured
-   [ ] Log rotation set up
-   [ ] Backup strategy implemented
-   [ ] Monitoring tools installed

### Server Requirements

```nginx
# Nginx Configuration
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/eduadmin/public;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### Optimization Commands

```powershell
# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Clear caches
php artisan optimize:clear

# Generate IDE helpers
php artisan ide-helper:generate
php artisan ide-helper:models
```

---

## 🔧 Development

### Code Standards

-   **PSR-12**: PHP coding standard
-   **PHPStan**: Static analysis (Level 6)
-   **Laravel Pint**: Code formatting
-   **Pest**: Modern testing framework

### Development Tools

```json
{
    "scripts": {
        "dev": "vite",
        "build": "vite build",
        "format": "./vendor/bin/pint",
        "analyze": "./vendor/bin/phpstan analyse",
        "test": "php artisan test"
    }
}
```

### Git Hooks

```bash
# .git/hooks/pre-commit
#!/bin/sh
./vendor/bin/pint --test
./vendor/bin/phpstan analyse --no-progress
php artisan test --stop-on-failure
```

### IDE Configuration

```json
// VS Code settings.json
{
    "php.validate.executablePath": "/usr/bin/php",
    "php.format.codeStyle": "PSR-12",
    "emmet.includeLanguages": {
        "blade": "html"
    },
    "files.associations": {
        "*.blade.php": "blade"
    }
}
```

---

## 📊 Monitoring

### Health Checks

```php
// routes/web.php
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'database' => DB::connection()->getPdo() ? 'connected' : 'disconnected',
        'cache' => Cache::has('health_check') ? 'working' : 'not working',
        'timestamp' => now()->toISOString(),
    ]);
});
```

### Logging Configuration

```php
// config/logging.php
'channels' => [
    'single' => [
        'driver' => 'single',
        'path' => storage_path('logs/laravel.log'),
        'level' => env('LOG_LEVEL', 'debug'),
    ],
    'daily' => [
        'driver' => 'daily',
        'path' => storage_path('logs/laravel.log'),
        'level' => env('LOG_LEVEL', 'debug'),
        'days' => 14,
    ],
],
```

### Performance Metrics

-   **Response Time**: < 200ms average
-   **Database Queries**: < 10 per request
-   **Memory Usage**: < 128MB per request
-   **Cache Hit Ratio**: > 80%

---

## 🤝 Contributing

### Development Workflow

1. **Fork** the repository
2. **Create** feature branch (`git checkout -b feature/amazing-feature`)
3. **Commit** changes (`git commit -m 'Add amazing feature'`)
4. **Push** branch (`git push origin feature/amazing-feature`)
5. **Open** Pull Request

### Contribution Guidelines

-   Follow PSR-12 coding standards
-   Write tests for new features
-   Update documentation
-   Keep commits atomic and descriptive
-   Ensure CI passes before submitting PR

### Code Review Process

-   All PRs require review from maintainers
-   Automated testing must pass
-   Code coverage must not decrease
-   Documentation updates required for new features

---

## 📞 Support & Resources

### Documentation

-   **API Documentation**: [Postman Collection](link-to-postman)
-   **Database Schema**: [DBDiagram](link-to-dbdiagram)
-   **Deployment Guide**: [Wiki](link-to-wiki)

### Community

-   **GitHub Issues**: [Report bugs](https://github.com/Mohammad-Al-Bast/EDUadmin/issues)
-   **Discussions**: [GitHub Discussions](https://github.com/Mohammad-Al-Bast/EDUadmin/discussions)
-   **Discord**: [Join our server](link-to-discord)

### Changelog

See [CHANGELOG.md](CHANGELOG.md) for detailed release notes.

---

## 📄 License

This project is licensed under the **MIT License** - see the [LICENSE](LICENSE) file for details.

---

<p align="center">
  <strong>Built with ❤️ by Mohammad Al Bast</strong><br>
  <a href="https://github.com/Mohammad-Al-Bast">GitHub</a> •
  <a href="mailto:mohammad@example.com">Email</a> •
  <a href="https://linkedin.com/in/mohammad-al-bast">LinkedIn</a>
</p>

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
