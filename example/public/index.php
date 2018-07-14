<?php
defined('ROOT_PATH') or define('ROOT_PATH', dirname(__FILE__));
defined('BASE_PATH') or define('BASE_PATH', dirname(ROOT_PATH));

/**
 * Step 1: Require the Kaiser Framework using Composer's autoloader
 */
$autoload = BASE_PATH . '/vendor/autoload.php';
if (!file_exists($autoload)) {
    exit ('You need to execute <strong>composer install</strong>');
}
$loader = require_once $autoload;

/**
 * Step 2: Instantiate a Kaiser application Controller
 */
$config = ['settings' => [
    'displayErrorDetails' => true, // set to false in production
    'addContentLengthHeader' => false, // Allow the web server to send the content-length header
]];
$app = new Mcl\Kaiser\App($config);

/**
 * Step 3: Setting Kaiser Container
 */
$container = $app->getContainer();

$container ['MYSQL'] = function ($c) {
    $dbname = 'mysql';
    $user = 'root';
    $pass = '';

    try {
        return new PDO('mysql:host=localhost;dbname=' . $dbname, $user, $pass);
    } catch (PDOException $e) {
        die('Connection failed.' . $e->getMessage());
    }
};

/**
 * Step 4: Define app routes
 */
$app->get('/hello/{name}', function ($request, $response, $args) {
    return $response->write("Hello " . $args['name']);
});


/**
 * Step 5: Run the Kaiser application
 */
$app->run([BASE_PATH . '/app']);
