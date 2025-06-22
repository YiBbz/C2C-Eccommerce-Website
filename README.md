# Freelance Marketplace Website

A comprehensive freelance marketplace platform where service providers can offer their services and customers can book them.

## Features

- User authentication (Service Providers and Customers)
- Service listing and search functionality
- Real-time chat using Pusher API
- Payment integration
- Review system
- Admin dashboard
- Responsive design using Bootstrap

## Tech Stack

- HTML5
- CSS3
- JavaScript
- PHP
- Bootstrap 5
- Pusher API for real-time chat
- MySQL Database

## Setup Instructions

1. Clone the repository
2. Set up a local web server (e.g., XAMPP, WAMP)
3. Import the database schema from `database/schema.sql`
4. Configure database connection in `config/database.php`
5. Set up Pusher credentials in `config/pusher.php`
6. Run the application through your local web server

## Directory Structure

```
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
├── config/
├── database/
├── includes/
├── admin/
└── vendor/
```

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- Composer for PHP dependencies 