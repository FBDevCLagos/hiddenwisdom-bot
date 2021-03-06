<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$app->get('/', function () use ($app) {
    return $app->version();
});

$app->get('/webhook', 'HiddenWisdomController@verify');
$app->post('/webhook', 'HiddenWisdomController@handleMessage');

// $app->get('/api/search/{lang}/{tag}', 'HiddenWisdomController@getProverb');
$app->get('/api/v1/proverbs', 'HiddenWisdomController@getProverb');
