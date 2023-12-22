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

Fill in all the necessary fields in the .env file and pay special attention
to the MQ_XXXXXX fields as these are required by RabbitMQ!

```
cp .env.example .env
```

### Generate an Application Key and a Encryption Key:

```
php artisan key:generate
```

Add an encryption key to the .env file and make certain to never change
this key or your encrypted data in the database is lost!.

Example key:

```
ENC_KEY="ksdjsk2344wcjlwfhwfjhf34755wfwvt3g593n37nyc.lp02hou!234id#!aqp"
```

### Set Up the Database:

Create a new database for your application.
Update the .env file with your database credentials. And add the following
variables (update the links to the right location of the logger):

```
DATALOGGER_API_KEY={{ The API key you received }}
DATALOGGER_API_URL_EVENTS_URL=http://localhost:8000/api/event/message
DATALOGGER_API_DB_EVENTS_URL=http://localhost:8000/api/event/log
```

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

This application requires a properly configured RabbitMQ environment!

```
php artisan mq:consume
```

### Send API requests from the application you want to log:

Make certain your guzzle request is correct and has all the fields as displayed
in the example below. You can place this guzzle request in the methods of a Model Observer to fire
everytime a model's data is modified, created or deleted or if you are monitoring URL event create a laravel middleware
that fires on every page load.

```
$body =
    [
        "ip" => $ip,
        "new_data" => $changed_data,
        "original_data" => $original_data,
        "date" => now(),
        "route" => $route,
        "model" => $model,
        "event_type" => "update" | "delete" | "create",
        "name" => $name,
        "user_id" => Auth::user()->id,
        "app_id" => $app_id
    ];
$token = env('DATA_LOGGER_API_KEY');
$apiResponse = Http::withToken($token)->accept('application/json')->post(env('DATA_LOGGER_API_URL'), $body);
```

The DataLogger application will be available at http://localhost:8000.
