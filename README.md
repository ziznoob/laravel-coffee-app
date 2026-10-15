# Coffee Bean Shop

A small e-commerce site for selling coffee beans, built with Laravel and React.

## How to run

Requirements: PHP 8.5, Composer, Node.js and npm.

```bash
composer install
npm install

cp .env.example .env
php artisan key:generate

touch database/database.sqlite
php artisan migrate --seed

npm run build        # builds the React / JS / CSS assets with Vite
php artisan serve    # starts Laravel at http://127.0.0.1:8000
```

Admin login (created by the seeder):

- Email: `test@example.com`
- Password: `password`

Run the tests with:

```bash
php artisan test
```

## Tech stack and decisions

- **Laravel 13.35.0** on **PHP 8.5.11**
- Using `php artisan serve` instead of XAMPP
- Using **SQLite** for the local database, viewed with the **SQLite Viewer** VS Code extension
- Using **React 19** as the frontend framework. `react-dom` is installed and used for the Cart component only, because the cart page does not need SEO
- Using **Bootstrap 5** as the CSS library
- It is a hybrid architecture: Blade renders the pages on the server, and React is mounted into the cart page. Vite is needed to build the JSX files into JS
- Using the **OpenArt MCP** to generate the product images (`public/images/products`)
- Using **seeders** to insert predefined products, sizes/prices, brew methods and product images into the tables

## Features

- Shop page with search and filter by bean type
- Cart (React) with quantity update, remove and clear
- Checkout that creates an order and reduces stock
- Admin order list where you can view the products purchased and change the order status
