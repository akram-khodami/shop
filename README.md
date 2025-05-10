<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## 🛍️ Shop API

This is a Laravel-based RESTful API for managing an online shop.  
It includes features like product management, category grouping, attributes, image uploads, and more.

## 🚀 Features

- Product CRUD (with images and attributes)
- Category grouping
- Attribute-based filtering
- Upload and manage product images
- RESTful API built with Laravel 12
- API validation with Form Requests
- Modular structure for future expansions

## 📦 Requirements

- PHP 8.2+
- Laravel 12
- Composer
- MySQL or compatible database
- Postman (for testing the API)

## ⚙️ Installation

```bash
git clone https://github.com/akram-khodami/shop.git
cd shop
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```
## License
