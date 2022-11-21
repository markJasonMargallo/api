<?php
// header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json;');
header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT, OPTIONS');
header("Access-Control-Max-Age: 3600");
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, X-Auth-User');
header('Access-Control-Expose-Headers: Authorization');

date_default_timezone_set('Asia/Manila');
set_time_limit(1000);
error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once('./vendor/autoload.php');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

define('SERVER', $_ENV['__SERVER_']);
define('DBASE', $_ENV['__DBASE_']);
define('USER', $_ENV['__USER_']);
define('PASSWORD', $_ENV['__PASSWORD_']);
define('CHARSET', $_ENV['__CHARSET_']);
define('SECRET', $_ENV['__SECRET_']);
define('ISSUER', $_ENV['ISSUER']);
define('AUDIENCE', $_ENV['AUDIENCE']);

class Connection
{

    protected $conString = 'mysql:host=' . SERVER . ";dbname=" . DBASE . ";charset=" . CHARSET;

    /* Setting the options for the PDO object. */
    protected $option = [
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        \PDO::ATTR_EMULATE_PREPARES => false
    ];

    /**
     * It returns a new PDO object with the connection string, username, password, and options.
     *
     * @return PDO, a new PDO object.
     */
    public function connect()
    {
        return new \PDO($this->conString, USER, PASSWORD, $this->option);
    }
}