<?php
/** @noinspection PhpRedundantVariableDocTypeInspection */

/**
 *
 * @see           https://github.com/EFTEC/ApiAssemblerRuntime
 * @package       eftec
 * @author        Jorge Castro Castillo
 * @copyright (c) Jorge Castro C. Dual Licence GPL-v3 and Commercial  https://github.com/EFTEC/ApiAssemblerRuntime
 * @version       1.0 (2022-02-11T11:34:47Z)
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
    if (gethostname() !== 'seg') {
        // developer machine configuration
        define('DATABASE_CONFIG', [
            'database' => 'mysql', // 'mysql','sqlsrv','oci'
            'server' => '127.0.0.1',
            'user' => 'root',
            'pwd' => 'abc.123',
            'db' => 'api-assembler',
        ]);
        $encryption = true;
        define('ENCRYPTION', [
            'password' => 'abc.123',
            'salt' => '1222',
            'encMethod' => 'aes-256-ctr'
        ]);

        $baseUrl = 'https://www.seg.cl/api/examples/newexample';
        /** @var string $templateUrl the expected url (starting from the base) */
        $templateUrl = '/{controller}/{action}/{id}/{idparent}';
        /** @var string $templateClass the class to be called when we call a controller */
        $templateClass = 'examples\newexample\api\{controller}ApiController';
        $debug = true;
        $cache = true;
        if ($cache) {
            define('CACHE_CONFIG', [
                'type' => 'redis',
                'server' => '127.0.0.1',
                'schema' => '',
                'port' => '6379',
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
            'db' => 'api-assembler',
        ]);
        $encryption = true;
        define('ENCRYPTION', [
            'password' => 'abc.123',
            'salt' => '1222',
            'encMethod' => 'aes-256-ctr'
        ]);
        $baseUrl = 'https://www.seg.cl/api/examples/newexample';
        /** @var string $templateUrl the expected url (starting from the base) */
        $templateUrl = '/{controller}/{action}/{id}/{idparent}';
        /** @var string $templateClass the class to be called when we call a controller */
        $templateClass = 'examples\newexample\api\{controller}ApiController';
        $debug = false;
        $cache = true;
        if ($cache) {
            define('CACHE_CONFIG', [
                'type' => 'redis',
                'server' => '127.0.0.1',
                'schema' => '',
                'port' => '6379',
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
		echo '<li><b>ProductCategory</b></li>';
		echo "<li><a href='api/ProductCategory/listall'>ProductCategory/listall (ALL)</a></li>";
		echo '</ul>';
echo '<ul>';
		echo '<li><b>Product</b></li>';
		echo "<li><a href='api/Product/listall'>Product/listall (ALL)</a></li>";
		echo '</ul>';
echo '<ul>';
		echo '<li><b>User</b></li>';
		echo "<li><a href='api/User/listall'>User/listall (ALL)</a></li>";
		echo '</ul>';

    } else {
        $apiAssemblerRuntime->errorShow(401, 'no controller', 'calling');
    }
}


