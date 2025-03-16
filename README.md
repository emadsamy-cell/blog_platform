# Blog Platform API

This is a RESTful API for a blog platform built with Laravel. It supports user authentication, role-based access control, and CRUD operations for blog posts and comments.

## Table of Contents
1. [Prerequisites](#prerequisites)
2. [Installation](#installation)
3. [Database Setup](#database-setup)
4. [Running the API](#running-the-api)
5. [API Documentation](#api-documentation)
6. [Testing](#testing)

## Prerequisites

Before you begin, ensure you have the following installed:

- PHP (>= 8.0)

- Composer
  
- MySQL or another supported database




## Installation

1. Clone the repository:
    ```
    git clone https://github.com/emadsamy-cell/blog_platform.git
    cd blog-platform
    ```
2. Install dependencies:
    ```
    composer install
    ```
3. Create a .env file:
   
   Copy the .env.example file and rename it to .env:
    ```
    cp .env.example .env
    ```
4. Generate an application key:
    ```
    php artisan key:generate
    ```

## Database Setup

1. Configure the database:
   
   Update the .env file with your database credentials:
    ```
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=blog_platform
    DB_USERNAME=root
    DB_PASSWORD=yourpassword
    ```

2. Run migrations:
   
   This will create the necessary tables in your database:
    ```
    php artisan migrate
    ```


## Running the API

1. Start the development server:
    ```
    php artisan serve
    ```

2. Access the API:
   
   The API will be available at:
    ```
    127.0.0.1:8000/api
    ```


## API Documentation



You can check endpoints & documentation on Postman from here [Postman documentation](https://documenter.getpostman.com/view/43143515/2sAYkBtggk)

### Testing

Run unit tests:
    ```
    php artisan test
    ```
