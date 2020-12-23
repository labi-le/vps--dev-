<?php echo 'Ok';

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require_once 'src/autoload.php';

use Labile\Bot\Bot as Bot;

define('VK_TOKEN', '96964480401aef6479a3cdab070eeab8cab0e10fceb03c1ee474e055a83a2493af9049ff2a55b39599ae6');
define('VK_CONFIRM_KEY', 'b1be9583');
define('VK_VERSION', 5.126);

define('DB_LOGIN', 'sliva');
define('DB_PASSWORD', '93870329d');

define('DIR_IMG', __DIR__ . '/img/');

$bot = new Bot(VK_TOKEN, VK_VERSION, VK_CONFIRM_KEY);
R::setup('mysql:host=localhost;dbname=db', DB_LOGIN, DB_PASSWORD);

$bot->run();