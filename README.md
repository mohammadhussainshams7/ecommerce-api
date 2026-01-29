# E-commerce API 🛒

This repository contains a **RESTful API for an E-commerce application** built with Laravel. It supports user authentication, product and category management, and order handling.

---

## Features

- User authentication (register, login, logout)
- CRUD operations for products and categories
- Order management
- RESTful API endpoints
- JWT or Sanctum authentication support
- JSON responses for easy frontend integration

---

## Installation

1. Clone the repository:

    ```bash
    git clone https://github.com/mohammadhussainshams7/ecommerce-api.git
    cd ecommerce-api
2. Install PHP dependencies:
     ```bash
    composer install
3. Install JavaScript dependencies:
   ```bash
    npm install
    npm run dev
4. Copy .env.example to .env and set your database credentials:
   ```bash
    cp .env.example .env
    php artisan key:generate
5. Run migrations:
    ```bash
    php artisan migrate
6. Start the development server:
   ```bash
    php artisan serve

## API Endpoints

| Method | Endpoint          | Description           |
| ------ | ----------------- | --------------------- |
| POST   | `/api/register`   | Register a new user   |
| POST   | `/api/login`      | User login            |
| POST   | `/api/logout`     | User logout           |
| GET    | `/api/products`   | List all products     |
| POST   | `/api/products`   | Create a new product  |
| GET    | `/api/categories` | List all categories   |
| POST   | `/api/categories` | Create a new category |
| POST   | `/api/orders`     | Create a new order    |

## Usage

Your API will be available at http://localhost:8000/api.

