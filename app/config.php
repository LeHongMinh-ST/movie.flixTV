<?php
//app
define('APP_PATH', 'app/');
define('APP_PATH_VIEW', 'app/Views/');
define('APP_URL', 'http://localhost/movie.flix.tv');

//DB
define('DB_HOSTNAME', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'flix.tv');

//Auth
define('AUTH_DB', [
    'users' => [
        'model' => \App\Models\User::class
    ],
    'admin' => [
        'model' => \App\Models\Admin::class
    ]
]);