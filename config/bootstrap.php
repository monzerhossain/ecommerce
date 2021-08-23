<?php
/**
 * Created by PhpStorm.
 * User: monzer
 * Date: 8/22/21
 * Time: 8:38 AM
 */

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

if (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}
