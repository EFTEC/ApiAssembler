<?php /** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpConditionAlreadyCheckedInspection */



namespace eftec\apiassembler;

use eftec\authone\AuthOne;
use eftec\PdoOne;
use eftec\routeone\RouteOne;
use eftec\ValidationOne;
use Exception;
use RuntimeException;

class ApiAssemblerRuntime
{
    /** @var ?PdoOne */
    public $pdo;
    /** @var ?RouteOne */
    public $routeOne;
    /** @var ?ValidationOne */
    public $validationOne;
    /** @var ?AuthOne */
    public $auth;
    /** @var ApiAssemblerRuntime */
    public static $instance;
    /** @var bool */
    public $debug=false;
    public $isBear=true;

    /** @var callable */
    private $authMethod;

    public function init(string $authType):void {
        if($authType!=='session') {
            @session_write_close();
            @session_destroy();
            @session_unset();

            unset($_COOKIE[session_name()]);
        }

        //var_dump($_COOKIE);
        // session_name
        //var_dump(session_get_cookie_params());
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE, HEAD, OPTIONS');
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Token");
        header('Access-Control-Expose-Headers: *');

        $method = @$_SERVER['REQUEST_METHOD'];

        if(!$method) {
            header("HTTP/1.1 404 Not Found");
            die();
        }
        if ($method === "OPTIONS") {
            header("HTTP/1.1 200 OK");
            die();
        }

    }
    public function validateAuth() : ?array {
        if($this->isBear) {
            $auth = $this->routeOne->getHeader('token');
        } else {
            $auth=$this->routeOne->getBody(true);
        }
        if($auth===null) {
            throw new RuntimeException('You can\' access to this resource without an authentication');
        }
        if($this->isBear) {
            $result = $this->auth->validateAuth($auth, null, $this->isBear);
        } else {
            $result = $this->auth->validateAuth($auth['body'], $auth['token'], $this->isBear);
        }
        return $result;
    }


    public function __construct()
    {
        self::$instance = $this;
    }

    /**
     * It sets the authentication function.<br>
     * <b>Example:</b><br>
     * <pre>
     * $apiAssemblerRuntime->setAuthService(function ($api, $action, $id = null, $idparent = null, $event = null) {
     *      // api is the ApiAssemblerRuntime instance
     *      // action is the current method, including the class, example ns1/ns2/class::methodActionPost
     *      // id is the current id obtained from the url (optional)
     *      // idparent is the current idparent (optional)
     *      // event is the event (optional)
     *      return true; // it could return any value (such as true, role, user, etc.). However, false means it is not allowed.
     * });
     * </pre>
     * @param callable $authService
     * @return ApiAssemblerRuntime
     */
    public function setAuthMethod(callable $authService): ApiAssemblerRuntime
    {
        $this->authMethod = $authService;
        return $this;
    }


    /**
     * If false, then the authentication is not allowed.<br>
     * Otherwise, it is allowed (it could return true, a user, a role, etc.)
     * @param string      $action   is the current method (including the class, example ns1/ns2/class::methodActionPost
     * @param string|null $id       is the current id obtained from the url (optional)
     * @param string|null $idparent is the current idparent (optional)
     * @param string|null $event    is the event (optional)
     * @return mixed|false false if the operation is not allowed, anything else is allowed.
     */
    public function getAuth(string $action, string $id = null, string $idparent = null, string $event = null)
    {
        return call_user_func($this->authMethod, $this, $action, $id, $idparent, $event);
    }


    /**
     * It gets the data from pagination from the next url: url?page=1&order=IdColumn&orderdir=asc<br>
     * <b>page</b>: the number of page, it starts with 1 (default value).<br>
     * <b>pagesize</b>: the size of the page, by default it is 20.<br>
     * <b>order</b>: the column to order, it is case-sensitive and the column must be explicitily indicated<br>
     * <b>orderdir</b>: the direction of sort, asc or desc (by default it is asc
     *
     * @param string $pk                 the default column to order (when no column is set), usually the primary key.
     * @param array  $columnsAllowedSort An array with the names of the columns allowed. It is case-sensitive
     * @return array [$page,$pagesize,$order,$orderdir]
     */
    public function getPage(string $pk, array $columnsAllowedSort): array
    {
        // url?page=20&order=idfield&orderdir=asc
        $page = $this->validationOne->type('integer')->def(1)
            ->ifFailThenDefault()
            ->condition('gte', 'the field [%field] must be greater or equals than 1', 1)
            ->request('page');
        $pageSize = $this->validationOne->type('integer')->def(10)
            ->ifFailThenDefault()
            ->condition('lte', 'the field [%field] must be less or equals than 100', 100)
            ->condition('gte', 'the field [%field] must be greater or equals than 1', 1)
            ->request('pagesize');
        $order = $this->validationOne->type('string')->def($pk)
            ->ifFailThenDefault()
            ->condition('alphanumunder', 'the field [%field] is not alphanumeric [%value]')
            ->condition('eq', 'the field [%field] has an incorrect value', $columnsAllowedSort)
            ->request('order');
        $orderdir = $this->validationOne->type('string')->def('asc')
            ->ifFailThenDefault()
            ->condition('eq', 'the field [%field] has an incorrect value', ['asc', 'desc'])
            ->request('orderdir');
        return [$page, $pageSize, $order, $orderdir];
    }

    /**
     * @return ApiAssemblerRuntime
     */
    public static function getInstance(): ApiAssemblerRuntime
    {
        return self::$instance;
    }

    /**
     * @param int    $code
     * @param string $description
     * @param string $where
     * @param array  $extended
     * @return void
     */
    public function errorShow(int $code, string $description, string $where = '',$extended=[]): void
    {
        header('Content-Type: application/json; charset=utf-8');
        if($this->debug) {
            $error= json_encode(['code' => $code, 'description' => $description . "\n" . implode("\n", $extended), 'where' => $where]);
        } else {
            $error= json_encode(['code' => $code, 'description' => $description, 'where' => $where]);
        }
        echo $error;
        /** @noinspection ForgottenDebugOutputInspection */
        error_log($error);
        http_response_code($code);
    }

    public function messageShow($values): void
    {
        header('Content-Type: application/json; charset=utf-8');
        try {
            echo json_encode($values);
        } catch (Exception $e) {
            echo json_encode($e->getMessage());
        }
    }

}


