<?php
declare(strict_types=1);

/**
 * Point d'entrée unique
 * Toutes les requêtes passent par ici.
 */

define('ROOT_PATH', dirname(__DIR__));

require_once ROOT_PATH . '/config/config.php';
require_once ROOT_PATH . '/core/Autoloader.php';

use Core\SessionManager;
use Core\Router;

// Démarrer la session
SessionManager::start();

// Dispatcher la requête
(new Router())->dispatch();
