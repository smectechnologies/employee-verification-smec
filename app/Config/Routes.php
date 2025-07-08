<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->post('/', 'Home::index');
$routes->get('/admin_login', 'Admin::login');
$routes->post('/admin_login', 'Admin::loginProcess');
$routes->get('/admin/dashboard', 'Admin::dashboard');
$routes->get('/admin/logout', 'Admin::logout');
$routes->post('/admin/add_employee', 'Admin::addEmployee');
$routes->get('/admin/view_employee/(:num)', 'Admin::viewEmployee/$1');
$routes->get('/admin/download/(:num)', 'Admin::downloadDocument/$1');
$routes->get('/admin/get_employee/(:num)', 'Admin::getEmployee/$1');
$routes->post('/admin/update_employee/(:num)', 'Admin::updateEmployee/$1');
$routes->post('/admin/approve_employee/(:num)', 'Admin::approveEmployee/$1');
$routes->post('/admin/toggle_status/(:num)', 'Admin::toggleStatus/$1');
$routes->post('/admin/delete_employee/(:num)', 'Admin::deleteEmployee/$1');
$routes->get('/public/download/(:num)', 'Home::publicDownload/$1');
