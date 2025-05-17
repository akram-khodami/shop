<div align="center">
 <a href="https://laravel.com" target="_blank">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
 </a>
</div>

<div align="center">
 <img src="https://img.shields.io/badge/AKRAM-KHODAMI-blue?style=for-the-badge" />
</div>

<div align="center">
 <img src="https://img.shields.io/badge/Laravel-API%20Auth-red?style=for-the-badge&logo=laravel" />
 <img src="https://img.shields.io/badge/PHP-8.2%2B-blue?style=for-the-badge&logo=php" />
 <img src="https://img.shields.io/badge/Auth-Sanctum-orange?style=for-the-badge" />
</div>

<div align="center">
 <img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status">
 <img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version">
 <img src="https://img.shields.io/packagist/l/laravel/framework" alt="License">
</div>

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

## 📂 Project Structure
- `app/Http/Controllers`: Contains controllers for products, cart, etc.
- `app/Models`: Data models.
- `app/Services`: Business logic layer.
- `app/repositories`: Repository Pattern.

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
