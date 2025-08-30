<?php

use App\Enums\UserRoles;
use Rcalicdan\FiberAsync\Api\DB;
use Rcalicdan\FiberAsync\Api\Task;

require_once __DIR__ . '/vendor/autoload.php';


Task::run(function () {
   $query = DB::table('users')->insert([
       'first_name' => 'John',
       'last_name' => 'Doe',
       'email' => 'john@example.com',
       'password' => password_hash('password', PASSWORD_BCRYPT),
       'role' => 'doctor',
   ]);

   await($query);
   $result = await(DB::table('users')->get());
   print_r($result);
   echo "Hi";
});
