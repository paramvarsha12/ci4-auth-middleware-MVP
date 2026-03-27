<?php

namespace App\Config;

$routes = Services::routes();

if (is_file(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(false);

$routes->get('/', 'Home::index');

// API Routes
$routes->group('api', ['namespace' => 'App\Controllers'], function($routes) {
    // Public routes
    $routes->post('register', 'Auth::register');
    $routes->post('login', 'Auth::login');
    
    // Protected routes
    $routes->post('create-teacher', 'Auth::createTeacher', ['filter' => 'auth']);
    $routes->get('users', 'Auth::getUsers', ['filter' => 'auth']);
    $routes->get('teachers', 'Auth::getTeachers', ['filter' => 'auth']);
});
