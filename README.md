# 🛒 Osman Bhai E-commerce – Laravel Backend

<p align="center">
<a href="https://laravel.com" target="_blank">
<img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
</a>
</p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

---

## 📌 Project Overview

**Osman Bhai E-commerce** is a complete backend system built with **Laravel 10**.  
This backend provides all necessary APIs and functionality for a React-based frontend e-commerce platform.

All business logic, database management, and API endpoints are handled from this Laravel project.

---

## 🛠 Technology Stack

- Laravel 10  
- PHP  
- MySQL Database  
- RESTful API  
- MVC Architecture  
- Eloquent ORM  
- Authentication System  
- PDF Invoice Generation  
- Courier API Integration  

---

## 🔧 What I Have Implemented

In this project I have handled:

- Database Schema Design  
- MySQL Database Management  
- Model Creation  
- Controller Functions  
- API Routes Declaration  
- Business Logic Implementation  
- Admin Panel APIs  
- Order Processing System  
- User Management  
- Product Management  
- Invoice Generation  
- Courier API Integration  

---

## 🌐 API Purpose

This Laravel project works as a **backend RESTful API server** for the React frontend application.

All frontend requests are handled through this backend including:

- Product data  
- User authentication  
- Order management  
- Cart processing  
- Admin operations  

---

## 🗂 Database

- Database used: **MySQL**
- All tables, relations and schemas are properly designed
- Used Laravel migrations for schema management

Main tables include:

- users  
- products  
- categories  
- orders  
- order_items  
- carts  
- admins  

---

## 🚀 Main Features

### 👤 User Features

- User Registration & Login  
- Product Browsing  
- Add to Cart  
- Order Placement  
- Order History  
- Invoice Download  

### 🛡 Admin Panel Features

- Admin Authentication  
- Product CRUD  
- Category Management  
- Order Management  
- User Management  
- Invoice Generation  
- Courier API Integration  

---

## 🧩 Architecture

This project follows:

- MVC Pattern  
- RESTful API Structure  
- Clean Code Principles  
- Proper Route Organization  
- Service Based Logic  

---

## 📁 Important Components

### Models

- User  
- Product  
- Order  
- OrderItem  
- Category  
- Cart  

### Controllers

- AuthController  
- ProductController  
- OrderController  
- AdminController  
- UserController  

### Installation Guidline

 -composer install
### Create Environment File
 -cp .env.example .env

 ### Generate Application Key
 - php artisan key:generate
 ### Configure Database in .env

  -DB_DATABASE=your_database_name
  -DB_USERNAME=root
  -DB_PASSWORD=

  ### Run Migrations
  -php artisan serve
  ### Run Server
   -php artisan serve

### Routes

All API routes are declared inside:

