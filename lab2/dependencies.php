<?php

use App\JWT;
use App\UserController;
use App\UserRepository;
use MongoDB\Client;

return [
    UserController::class => function () {
        $mongo = new Client(getenv('MONGO_URL'));
        return new UserController(
            new JWT(getenv('JWT_SECRET')),
            new UserRepository($mongo->selectCollection('app', 'users'))
        );
    }
];