<?php echo 'Ok';

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require_once 'src/autoload.php';

use Labile\Bot\Bot as Bot;

define('VK_TOKEN', 'e11bc8967c430628289810cf218862baf69f442c7a52ca4f6b1908dd9d6818f19f3ce6c350b5a95929a85');
define('VK_CONFIRM_KEY', '3a36fa4a');
define('VK_VERSION', 5.126);

define('DB_LOGIN', 'sliva');
define('DB_PASSWORD', '93870329d');

define('DIR_IMG', __DIR__ . '/img/');

$bot = new Bot(VK_TOKEN, VK_VERSION, VK_CONFIRM_KEY);
R::setup('mysql:host=localhost;dbname=db', DB_LOGIN, DB_PASSWORD);

$bot->run();
