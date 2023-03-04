<?php http_response_code(404); die(1); // it is a template, it is protected to be called directly ?>
/** @noinspection PhpRedundantVariableDocTypeInspection */

/**
 * It is the enrouter of the application.<br>
 * <b>This file is generated by the CLI</b>
 * @see           https://github.com/EFTEC/ApiAssemblerRuntime
 * @package       eftec
 * @author        Jorge Castro Castillo
 * @copyright (c) Jorge Castro C. Dual Licence GPL-v3 and Commercial  https://github.com/EFTEC/ApiAssemblerRuntime
 * @version       __version__
 */
use eftec\_BasePdoOneRepo;
use eftec\apiassembler\ApiAssemblerRuntime;
use eftec\CacheOne;
use eftec\authone\AuthOne;
use eftec\PdoOne;
use eftec\routeone\RouteOne;
use eftec\ValidationOne;
//use Exception;
include '__composerpath__/autoload.php';



config();

/** @noinspection PhpConditionAlreadyCheckedInspection
 * @noinspection PhpUnhandledExceptionInspection
 * @noinspection HtmlUnknownTarget
 * @noinspection PhpUnusedParameterInspection
 * @noinspection PhpIfWithCommonPartsInspection
 */
function config()
{
    $apiAssemblerRuntime = new ApiAssemblerRuntime();
    $apiAssemblerRuntime->init('__authtype__'); // note: this line could end the execution.
    // todo: edit your configurations here
    if (gethostname() __questiondev__== '__machineid__') {
        // ************************************
        // * developer machine configuration  *
        // ************************************
        // [EDIT:content_dev] you can edit this part
        // [/EDIT] end of edit part
        $apiAssemblerRuntime->debug=true;
        define('DATABASE_CONFIG', [
            'databaseType' => '__databasetype__', // 'mysql','sqlsrv','oci'
            'server' => '__server__',
            'user' => '__user__',
            'pwd' => '__password__',
            'db' => '__database__',
        ]);
        $encryption = __questionencryption__;
        define('ENCRYPTION', [
            'password' => '__encryptionpassword__',
            'salt' => '__encryptionsalt__',
            'encMethod' => '__encryptionmethod__'
        ]);

        $baseUrl = '__baseurl_dev__';
        /** @var string $templateUrl the expected url (starting from the base) */
        $templateUrl = '__templateurl__/{controller}/{action}/{id}/{idparent}';
        /** @var string $templateClass the class to be called when we call a controller */
        $templateClass = '__namespaceapi__\{controller}ApiController';
        $debug = true;
        $cache = __questioncache__;
        if ($cache) {
            define('CACHE_CONFIG', [
                'type' => '__cache_type__',
                'server' => '__cache_server__',
                'schema' => '__cache_schema__',
                'port' => '__cache_port__',
                'user' => '__cache_user__',
                'password' => '__cache_password__'
            ]);
        }
    } else {
        // ************************************
        // * production machine configuration *
        // ************************************
        // [EDIT:content_dev] you can edit this part
        // [/EDIT] end of edit part
        $apiAssemblerRuntime->debug=true;
        define('DATABASE_CONFIG', [
            'databaseType' => '__databasetype__', // 'mysql','sqlsrv','oci'
            'server' => '__server__',
            'user' => '__user__',
            'pwd' => '__password__',
            'db' => '__database__',
        ]);
        $encryption = __questionencryption__;
        define('ENCRYPTION', [
            'password' => '__encryptionpassword__',
            'salt' => '__encryptionsalt__',
            'encMethod' => '__encryptionmethod__'
        ]);
        $baseUrl = '__baseurl_prod__';
        /** @var string $templateUrl the expected url (starting from the base) */
        $templateUrl = '__templateurl__/{controller}/{action}/{id}/{idparent}';
        /** @var string $templateClass the class to be called when we call a controller */
        $templateClass = '__namespaceapi__\{controller}ApiController';
        $debug = false;
        $cache = __questioncache__;
        if ($cache) {
            define('CACHE_CONFIG', [
                'type' => '__cache_type__',
                'server' => '__cache_server__',
                'schema' => '__cache_schema__',
                'port' => '__cache_port__',
                'user' => '__cache_user__',
                'password' => '__cache_password__'
            ]);
        }
    }
    // end configurations


    // database connection

    // [EDIT:content_database] you can edit this part
    if(PHP_MAJOR_VERSION>=8) {
        $apiAssemblerRuntime->pdo = new PdoOne(...DATABASE_CONFIG);
        if ($encryption) {
            $apiAssemblerRuntime->pdo->setEncryption(...ENCRYPTION);
        }
        $apiAssemblerRuntime->pdo->logLevel = $debug === true ? 2 : 0;
        if ($cache) {
            $apiAssemblerRuntime->pdo->cacheService = new CacheOne(...CACHE_CONFIG);
        }
    } else {
        $apiAssemblerRuntime->pdo = new PdoOne(...array_values(DATABASE_CONFIG));
        if ($encryption) {
            $apiAssemblerRuntime->pdo->setEncryption(...array_values(ENCRYPTION));
        }
        $apiAssemblerRuntime->pdo->logLevel = $debug === true ? 2 : 0;
        if ($cache) {
            $apiAssemblerRuntime->pdo->cacheService = new CacheOne(...array_values(CACHE_CONFIG));
        }
    }
    // [/EDIT] end of edit content_database
    // [EDIT:content_auth] you can edit this part
    /**
     * This function authenticate some operation and returns true if it is allowed or false if not.<br>
     * <b>$api->routeOne</b>:  You can obtain information about the router such as input values, header, body, parameters,
     * url,etc.<br>
     * <b>$api->pdo</b>:  You can access to the database and read,insert, etc.<br>
     * <b>$api->validationOne</b>:  With this service, you can validate objects, variables, etc.<br>
     * <b>$api->pdo->cacheService</b>: You can access and use the cache service<br>
     *
     * @param ApiAssemblerRuntime $api      An instance of the ApiAssemblerRuntime
     * @param string|null         $action   It contains the name of the method called<br>
     *                                      Example "api\api\CityApiController::listallAction"
     * @param mixed               $id       The id of the current route.<br>
     *                                      Example: http://localhost/api/Customer/1  $id=1
     * @param mixed               $idparent the  id parent of the current route<br>
     *                                      Example: http://localhost/api/Customer/1/2  $id=1,$idparent=2
     * @param mixed               $event    the event (if any)<br>
     *                                      Example: http://localhost/api/Customer/?_event=click $event=click
     * @return bool true if it passes the validation. Otherwise, it returns false.
     * @throws Exception
     * @link https://github.com/EFTEC/RouteOne
     * @link https://github.com/EFTEC/PdoOne
     * @link https://github.com/EFTEC/ValidationOne
     * @link https://github.com/EFTEC/CacheOne
     */
    function myAuth(ApiAssemblerRuntime $api,?string $action, $id = null, $idparent = null, $event = null) : bool {
        $isValid=true;
        //todo: edit your authentication here
        //
        // Example of authentication using Auth instance and tokens.
        // Uncomment those lines if you want a default authentication.
        /*
            if($action==='User/createauth') {
                // this action is allowed without authentication.
                return true;
            }
            $auth=$api->validateAuth();
            $auth !== null;
        */
        return $isValid;
    }
    if (true) {
        // We use the method defined in router_auth.php to do the authentication.
        // Why? It is because this file could be override, so we write the logic in a file that it is never override.
        $apiAssemblerRuntime->setAuthMethod('myAuth');
    }
    // [/EDIT] end of edit content_auth
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

    // for the authentication
    if('__authtype__'!=='none' && '__authtype__'!=='') {
        $apiAssemblerRuntime->auth=new AuthOne('__authtype__','__authstore_type__',null,null);
        $apiAssemblerRuntime->isBear=__authtype_bear__;
        $apiAssemblerRuntime->auth->setEncryptConfigUsingPDO(); // use the same paremters of encryption than PdoOne.
        $apiAssemblerRuntime->auth->setUserStoreConfig('__authstore_table__','__authstore_user__','__authstore_password__');
    }

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
        echo '<h1 style="background-color:blue; color:white; padding: 4px">Running DEBUG VERSION __version__</h1>';
        __links__
    } else {
        $apiAssemblerRuntime->errorShow(401, 'no controller selected', 'calling');
    }
}


