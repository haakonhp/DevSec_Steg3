require_once(DIR.'/vendor/autoload.php');
use MonologLogger;
use MonologHandlerStreamHandler;
use MonologHandlerFirePHPHandler;

$logger = new Logger('logger');
$logger->pushHandler(new StreamHandler(DIR.'/test_app.log', Logger::DEBUG));
$logger->pushHandler(new FirePHPHandler());
$logger->error('Logger is now Ready');
