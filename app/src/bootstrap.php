<?php
namespace Bootstrap;

require_once __DIR__ . '/../vendor/autoload.php';
/**
 * @todo Autoload them later ;-)
 */
require_once __DIR__ . '/framework/utils.php';

use Symfony\Component\Dotenv\Dotenv;


$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/../config/.env.app', __DIR__ . '/../config/.env.sensitive.data');

if (array_key_exists('APP_TOKEN_FILE', $_ENV))
   $_ENV['APP_TOKEN_FILE'] = APP_DIR() . $_ENV['APP_TOKEN_FILE'];

function APP_DIR() : string
{
   return __DIR__ . '/../';
}