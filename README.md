# DataLogger

Welcome to DataLogger! An application to log database and url activity.

## Prerequisites

Before you begin, ensure you have met the following requirements:

A RabbitMQ service is necessary to run the application.
To get started with the project, follow these steps:

### Clone the repository:

```
git clone [repository_url]
cd [project_directory]
```

### Install PHP Dependencies:

```
composer install
```

### Install JavaScript Dependencies:

```
npm install
```

### Create a Copy of the .env File:

```
cp .env.example .env
```

### Generate an Application Key and a Encryption Key:

```
php artisan key:generate
```

Add an encryption key to the .env file.

### Set Up the Database:

Create a new database for your application.
Update the .env file with your database credentials.

### Run Migrations and Seeders:

```
php artisan migrate --seed
```

### Start the Development Servers:

```
php artisan serve (Laravel)
npm run dev (Vue/Inertia)
```

### Start the RabbitMQ Service:

php artisan mq:consume

The DataLogger application will be available at http://localhost:8000.

### Get an API key and add it to your .env as VITE_API_KEY
