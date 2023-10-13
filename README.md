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

This application requires a properly configured RabbitMQ environment!

```
php artisan mq:consume
```

### Send API requests from the application you want to log:

Make certain your axios request is correct and has all the fields as displayed
in the example below. You can place this axios call in the methods of a Model Observer to fire
everytime a model's data is modified, created or deleted.

ALL FIELDS ARE REQUIRED! If either the new_data or original_data field is empty in case for
example a create or delete event pass null instead.

```
axios.post('<YOUR_APP_URL>/api/events/log', {
        'new_data' => <CHANGED_DATA>,
        'original_data' => <ORIGINAL_DATA>,
        'user_email' => <USER_EMAIL>,
        'event_type' => <TYPE_OF_THE_EVENT>,
        'model' => <MODEL_OF_DATA_CHANGED>,
        'app_id' => <YOUR_APP_NAME>,
        'route' => <ROUTE>,
        'ip_address' => <IP_ADDRESS>
    }, {
        'headers' =>
        {
            'Authorization' => "Bearer {YOUR_TOKEN}",
            'Accept' => 'application/json'
        }
    })
    .then(function(res) => {
        // Do something with the response
    })
    .catch(function(err) => {
        // Do something with the error
    })

```

The DataLogger application will be available at http://localhost:8000.
