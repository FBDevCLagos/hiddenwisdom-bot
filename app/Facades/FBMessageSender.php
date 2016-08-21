<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;
 
class FBMessageSender extends Facade {

    protected static function getFacadeAccessor() { 

        return 'FBMessageSender'; 
    }
}