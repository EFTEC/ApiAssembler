<?php
/** @noinspection PhpMissingParamTypeInspection
 * @noinspection PhpMissingReturnTypeInspection
 * @noinspection PhpMissingFieldTypeInspection
 * @noinspection UnknownInspectionInspection
 * @noinspection AccessModifierPresentedInspection
 * @noinspection PhpIncompatibleReturnTypeInspection
 * @noinspection ReturnTypeCanBeDeclaredInspection
 * @noinspection DuplicatedCode
 * @noinspection PhpUnused
 * @noinspection PhpUndefinedMethodInspection
 * @noinspection PhpUnusedLocalVariableInspection
 * @noinspection PhpUnusedAliasInspection
 * @noinspection NullPointerExceptionInspection
 * @noinspection SenselessProxyMethodInspection
 * @noinspection PhpParameterByRefIsNotUsedAsReferenceInspection
 */
namespace examples\newexample\repo;
use eftec\PdoOne;
use eftec\_BasePdoOneRepo;use eftec\PdoOneQuery;
use Exception;

/**
 * Generated by PdoOne Version 2.25 Date generated Fri, 11 Feb 2022 11:34:47 -0400.
 * @copyright (c) Jorge Castro C. MIT License  https://github.com/EFTEC/PdoOne
 * Class BaseApiassembler
 */
class BaseApiassembler extends _BasePdoOneRepo
{
    const type = 'mysql';
    const COMPILEDVERSION=7;
    const NS = 'examples\newexample\repo\\';

    /**
     * @var bool if true then it uses objects (instead of array) in the
     * methods tolist(),first(),insert(),update() and delete()
     */
    public static $useModel=false;


    /** @var string[] it is used to set the relations betweeen table (key) and class (value) */
    const RELATIONS = [
	    'productcategories' => 'ProductCategoryRepo',
	    'products' => 'ProductRepo',
	    'users' => 'UserRepo'
	];
    /** @var PdoOne */
    public static $pdoOne;
    /** @var string|null $schema the current schema/database */
    public static $schema;
    /** @var PdoOneQuery */
    public static $pdoOneQuery;
    /** @var array $gQuery =[['columns'=>[],'joins'=>[],'where'=>[]] */
    public static $gQuery = [];
    public static $gQueryCounter = 0;
    public static $pageSize = 20;
    public static $lastException = '';
    /** @var bool if true then it returns a false on error. If false, it throws an exception in case of error */
    protected static $falseOnError = false;

    /** @var null|string the unique id generate by sha256 and based in the query, arguments, type and methods */
    protected static $uid;



    /**
     * With the name of the table, we get the class
     * @param string $tableName
     *
     * @return string[]
     */
    protected function tabletoClass($tableName) {
        return static::RELATIONS[$tableName];
    }

    /**
     * It sets the field self::$pdoOne
     *
     * @param $pdoOne
     */
    public static function setPdoOne($pdoOne)
    {
        static::$pdoOne = $pdoOne;
    }
    /**
     * It is used for DI.<br>
     * If the field is not null, it returns the field self::$pdoOne<br>
     * If the global function pdoOne exists, then it is used<br>
     * if the global variable $pdoOne exists, then it is used<br>
     * Otherwise, it returns null
     *
     * @return PdoOne
     */
    protected static function getPdoOne()
    {
        if (self::$pdoOne !== null) {
            return self::$pdoOne;
        }
        if (self::BINARYVERSION !== static::COMPILEDVERSION) {
            $p = new PdoOne('test', 'no database', '', '');
            $p->throwError('Repository classes requires a new version. Please update PdoOne and rebuild'
                , self::class);
        }

        if(PdoOne::instance(false)!==null) {
            self::$pdoOne=PdoOne::instance(false);
            return self::$pdoOne;
        }

        if (function_exists('PdoOne')) {
            self::$pdoOne = PdoOne();
            return self::$pdoOne;
        }
        if (isset($GLOBALS['pdoOne']) && $GLOBALS['pdoOne'] instanceof PdoOne) {
            self::$pdoOne = $GLOBALS['pdoOne'];
            return self::$pdoOne;
        }
        return self::$pdoOne;
    }
}