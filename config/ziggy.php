<?php

// Route names exposed to the frontend via @routes.
return [
    'except' => array_merge(
        ['debugbar.*', 'ignition.*', 'sanctum.*', 'storage.*', 'api.*'],
        // hide the sign-up link unless public registration is switched on
        env('USER_REGISTRATION_ALLOWED', false) ? [] : ['register'],
    ),
];
