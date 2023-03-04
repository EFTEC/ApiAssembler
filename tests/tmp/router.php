<?php
/** @noinspection PhpRedundantVariableDocTypeInspection */

/**
 *
 * @see           https://github.com/EFTEC/ApiAssemblerRuntime
 * @package       eftec
 * @author        Jorge Castro Castillo
 * @copyright (c) Jorge Castro C. Dual Licence GPL-v3 and Commercial  https://github.com/EFTEC/ApiAssemblerRuntime
 * @version       1.0 (2022-03-07T08:48:23Z)
 */
use eftec\_BasePdoOneRepo;
use eftec\apiassembler\ApiAssemblerRuntime;
use eftec\CacheOne;
use eftec\PdoOne;
use eftec\routeone\RouteOne;
use eftec\ValidationOne;
//use Exception;
include '../../vendor/autoload.php';

config();

/** @noinspection PhpConditionAlreadyCheckedInspection
 * @noinspection PhpUnhandledExceptionInspection
 * @noinspection HtmlUnknownTarget
 * @noinspection PhpUnusedParameterInspection
 * @noinspection PhpIfWithCommonPartsInspection
 */
function config()
{
// todo: edit your configurations here
    if (gethostname() === 'PCJC') {
        // developer machine configuration
        define('DATABASE_CONFIG', [
            'database' => 'mysql', // 'mysql','sqlsrv','oci'
            'server' => '127.0.0.1',
            'user' => 'root',
            'pwd' => 'abc.123',
            'db' => 'sakila',
        ]);
        $encryption = false;
        define('ENCRYPTION', [
            'password' => '',
            'salt' => '',
            'encMethod' => ''
        ]);

        $baseUrl = 'http://localhost/currentproject/ApiAssembler/tests/tmp/';
        /** @var string $templateUrl the expected url (starting from the base) */
        $templateUrl = '/{controller}/{action}/{id}/{idparent}';
        /** @var string $templateClass the class to be called when we call a controller */
        $templateClass = 'eftec\tests\tmp\{controller}ApiController';
        $debug = true;
        $cache = false;
        if ($cache) {
            define('CACHE_CONFIG', [
                'type' => '',
                'server' => '',
                'schema' => '',
                'port' => '',
                'user' => '',
                'password' => ''
            ]);
        }
    } else {
        // production machine configuration
        define('DATABASE_CONFIG', [
            'database' => 'mysql', // 'mysql','sqlsrv','oci'
            'server' => '127.0.0.1',
            'user' => 'root',
            'pwd' => 'abc.123',
            'db' => 'sakila',
        ]);
        $encryption = false;
        define('ENCRYPTION', [
            'password' => '',
            'salt' => '',
            'encMethod' => ''
        ]);
        $baseUrl = 'http://localhost/currentproject/ApiAssembler/tests/tmp/';
        /** @var string $templateUrl the expected url (starting from the base) */
        $templateUrl = '/{controller}/{action}/{id}/{idparent}';
        /** @var string $templateClass the class to be called when we call a controller */
        $templateClass = 'eftec\tests\tmp\{controller}ApiController';
        $debug = false;
        $cache = false;
        if ($cache) {
            define('CACHE_CONFIG', [
                'type' => '',
                'server' => '',
                'schema' => '',
                'port' => '',
                'user' => '',
                'password' => ''
            ]);
        }
    }
    // end configurations


    $apiAssemblerRuntime = new ApiAssemblerRuntime();
    // database connection
    $apiAssemblerRuntime->pdo = new PdoOne(...DATABASE_CONFIG);
    if ($encryption) {
        $apiAssemblerRuntime->pdo->setEncryption(...ENCRYPTION);
    }
    $apiAssemblerRuntime->pdo->logLevel = $debug === true ? 2 : 0;
    if ($cache) {
        $apiAssemblerRuntime->pdo->cacheService = new CacheOne(...CACHE_CONFIG);
    }
    if (true) {
        $apiAssemblerRuntime->setAuthService(function ($api, $action, $id = null, $idparent = null, $event = null) {
            //todo: edit your authentication here
            // Example of authentication using cache server (if any).
            /*[$class, $method] = explode('::', $action);
            if ($class === 'examples\localhost\api\UserApiController' && $method === 'tokenActionPOST') {
                // no auth required when you ask for a token
                return true;
            }

            $tokenKey = $api->routeOne->getHeader('token');
            $values = $api->pdo->getCacheService()->getCache($tokenKey, 'token');
            if ($values) {
                $api->pdo->getCacheService()->setCache($tokenKey, 'token', $values, 600); // renew by 10 minutes.
                return true;
            }
            return false;
            */
            return true;
        });
    }

    try {
        // optionally: if you have a Pdo instance, then you could use the next line:
        //             $apiAssemblerRuntime->pdo->conn1=$pdoInstance;
        $apiAssemblerRuntime->pdo->open();

    } catch (Exception $ex) {
        $apiAssemblerRuntime->errorShow(500, $ex->getMessage(), 'connecting');
    }


    // for the routing

    $apiAssemblerRuntime->routeOne = new RouteOne($baseUrl);
    $apiAssemblerRuntime->routeOne->addPath($templateUrl);
    $apiAssemblerRuntime->routeOne->fetchPath();

    // for the validation
    $apiAssemblerRuntime->validationOne = new ValidationOne();
    $apiAssemblerRuntime->validationOne->debug = $debug;


    // the routing calls the corresponding class (if any).
    if ($apiAssemblerRuntime->routeOne->controller) {
        try {
            $apiAssemblerRuntime->routeOne->callObjectEx($templateClass, true
                , '{action}Action', '{action}Action{verb}', '{action}Action{verb}', ['id', 'idparent', 'event']
                , [$apiAssemblerRuntime]);
        } catch (Exception $ex) {
            $apiAssemblerRuntime->errorShow(401, $ex->getMessage(), 'calling');
        }
    } else if ($debug) {
        echo '<ul>';
		echo '<li><b>ActorRepo</b></li>';
		echo "<li><a href='api/ActorRepo/listall'>ActorRepo/listall (ALL)</a></li>";
		echo '</ul>';
echo '<ul>';
		echo '<li><b>Actor2Repo</b></li>';
		echo "<li><a href='api/Actor2Repo/listall'>Actor2Repo/listall (ALL)</a></li>";
		echo '</ul>';
echo '<ul>';
		echo '<li><b>AddresRepo</b></li>';
		echo "<li><a href='api/AddresRepo/listall'>AddresRepo/listall (ALL)</a></li>";
		echo '</ul>';
echo '<ul>';
		echo '<li><b>CategoryRepo</b></li>';
		echo "<li><a href='api/CategoryRepo/listall'>CategoryRepo/listall (ALL)</a></li>";
		echo '</ul>';
echo '<ul>';
		echo '<li><b>CityRepo</b></li>';
		echo "<li><a href='api/CityRepo/listall'>CityRepo/listall (ALL)</a></li>";
		echo '</ul>';
echo '<ul>';
		echo '<li><b>CountryRepo</b></li>';
		echo "<li><a href='api/CountryRepo/listall'>CountryRepo/listall (ALL)</a></li>";
		echo '</ul>';
echo '<ul>';
		echo '<li><b>CustomerRepo</b></li>';
		echo "<li><a href='api/CustomerRepo/listall'>CustomerRepo/listall (ALL)</a></li>";
		echo '</ul>';
echo '<ul>';
		echo '<li><b>DummytRepo</b></li>';
		echo "<li><a href='api/DummytRepo/listall'>DummytRepo/listall (ALL)</a></li>";
		echo '</ul>';
echo '<ul>';
		echo '<li><b>DummytableRepo</b></li>';
		echo "<li><a href='api/DummytableRepo/listall'>DummytableRepo/listall (ALL)</a></li>";
		echo '</ul>';
echo '<ul>';
		echo '<li><b>FilmRepo</b></li>';
		echo "<li><a href='api/FilmRepo/listall'>FilmRepo/listall (ALL)</a></li>";
		echo '</ul>';
echo '<ul>';
		echo '<li><b>Film2Repo</b></li>';
		echo "<li><a href='api/Film2Repo/listall'>Film2Repo/listall (ALL)</a></li>";
		echo '</ul>';
echo '<ul>';
		echo '<li><b>FilmActorRepo</b></li>';
		echo "<li><a href='api/FilmActorRepo/listall'>FilmActorRepo/listall (ALL)</a></li>";
		echo '</ul>';
echo '<ul>';
		echo '<li><b>FilmCategoryRepo</b></li>';
		echo "<li><a href='api/FilmCategoryRepo/listall'>FilmCategoryRepo/listall (ALL)</a></li>";
		echo '</ul>';
echo '<ul>';
		echo '<li><b>FilmTextRepo</b></li>';
		echo "<li><a href='api/FilmTextRepo/listall'>FilmTextRepo/listall (ALL)</a></li>";
		echo '</ul>';
echo '<ul>';
		echo '<li><b>FumJobRepo</b></li>';
		echo "<li><a href='api/FumJobRepo/listall'>FumJobRepo/listall (ALL)</a></li>";
		echo '</ul>';
echo '<ul>';
		echo '<li><b>FumLogRepo</b></li>';
		echo "<li><a href='api/FumLogRepo/listall'>FumLogRepo/listall (ALL)</a></li>";
		echo '</ul>';
echo '<ul>';
		echo '<li><b>InventoryRepo</b></li>';
		echo "<li><a href='api/InventoryRepo/listall'>InventoryRepo/listall (ALL)</a></li>";
		echo '</ul>';
echo '<ul>';
		echo '<li><b>LanguageRepo</b></li>';
		echo "<li><a href='api/LanguageRepo/listall'>LanguageRepo/listall (ALL)</a></li>";
		echo '</ul>';
echo '<ul>';
		echo '<li><b>MysecTableRepo</b></li>';
		echo "<li><a href='api/MysecTableRepo/listall'>MysecTableRepo/listall (ALL)</a></li>";
		echo '</ul>';
echo '<ul>';
		echo '<li><b>PaymentRepo</b></li>';
		echo "<li><a href='api/PaymentRepo/listall'>PaymentRepo/listall (ALL)</a></li>";
		echo '</ul>';
echo '<ul>';
		echo '<li><b>ProductRepo</b></li>';
		echo "<li><a href='api/ProductRepo/listall'>ProductRepo/listall (ALL)</a></li>";
		echo '</ul>';
echo '<ul>';
		echo '<li><b>ProducttypeRepo</b></li>';
		echo "<li><a href='api/ProducttypeRepo/listall'>ProducttypeRepo/listall (ALL)</a></li>";
		echo '</ul>';
echo '<ul>';
		echo '<li><b>ProducttypeAutoRepo</b></li>';
		echo "<li><a href='api/ProducttypeAutoRepo/listall'>ProducttypeAutoRepo/listall (ALL)</a></li>";
		echo '</ul>';
echo '<ul>';
		echo '<li><b>RentalRepo</b></li>';
		echo "<li><a href='api/RentalRepo/listall'>RentalRepo/listall (ALL)</a></li>";
		echo '</ul>';
echo '<ul>';
		echo '<li><b>StaffRepo</b></li>';
		echo "<li><a href='api/StaffRepo/listall'>StaffRepo/listall (ALL)</a></li>";
		echo '</ul>';
echo '<ul>';
		echo '<li><b>StoreRepo</b></li>';
		echo "<li><a href='api/StoreRepo/listall'>StoreRepo/listall (ALL)</a></li>";
		echo '</ul>';
echo '<ul>';
		echo '<li><b>TablachildRepo</b></li>';
		echo "<li><a href='api/TablachildRepo/listall'>TablachildRepo/listall (ALL)</a></li>";
		echo '</ul>';
echo '<ul>';
		echo '<li><b>TablagrandchildRepo</b></li>';
		echo "<li><a href='api/TablagrandchildRepo/listall'>TablagrandchildRepo/listall (ALL)</a></li>";
		echo '</ul>';
echo '<ul>';
		echo '<li><b>TablaparentRepo</b></li>';
		echo "<li><a href='api/TablaparentRepo/listall'>TablaparentRepo/listall (ALL)</a></li>";
		echo '</ul>';
echo '<ul>';
		echo '<li><b>TabletestRepo</b></li>';
		echo "<li><a href='api/TabletestRepo/listall'>TabletestRepo/listall (ALL)</a></li>";
		echo '</ul>';
echo '<ul>';
		echo '<li><b>TestProductRepo</b></li>';
		echo "<li><a href='api/TestProductRepo/listall'>TestProductRepo/listall (ALL)</a></li>";
		echo '</ul>';
echo '<ul>';
		echo '<li><b>TypetableRepo</b></li>';
		echo "<li><a href='api/TypetableRepo/listall'>TypetableRepo/listall (ALL)</a></li>";
		echo '</ul>';

    } else {
        $apiAssemblerRuntime->errorShow(401, 'no controller', 'calling');
    }
}


