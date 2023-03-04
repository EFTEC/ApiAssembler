<?php
/** @noinspection PhpUnusedParameterInspection
* @noinspection PhpClassConstantAccessedViaChildClassInspection
* @noinspection PhpClasspublic constantAccessedViaChildClassInspection
* @noinspection NullCoalescingOperatorCanBeUsedInspection
* @noinspection PhpPureAttributeCanBeAddedInspection
* @noinspection PhpArrayShapeAttributeCanBeAddedInspection
* @noinspection PhpMissingParamTypeInspection
* @noinspection AccessModifierPresentedInspection
* @noinspection PhpMissingReturnTypeInspection
* @noinspection UnknownInspectionInspection
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
namespace eftec\tests\tmp\repo2;
use eftec\PdoOne;
use eftec\PdoOneQuery;

use Exception;

/**
* Class AbstractFumJobRepo. Copyright (c) Jorge Castro C. (https://github.com/EFTEC/PdoOne)<br>
* Generated by PdoOne Version 2.27 Date generated Mon, 07 Mar 2022 08:48:22 -0400.<br>
* <b>DO NOT EDIT THIS CODE</b>. This code is generated<br>
* If you want to make some changes, then add the changes to the Repository class.<br>
* <pre>
* $code=$pdoOne->generateCodeClass('fum_jobs','eftec\tests\tmp\repo2',array(),array('actor'=>'ActorRepo','actor2'=>'Actor2Repo','address'=>'AddresRepo','category'=>'CategoryRepo','city'=>'CityRepo','country'=>'CountryRepo','customer'=>'CustomerRepo','dummyt'=>'DummytRepo','dummytable'=>'DummytableRepo','film'=>'FilmRepo','film2'=>'Film2Repo','film_actor'=>'FilmActorRepo','film_category'=>'FilmCategoryRepo','film_text'=>'FilmTextRepo','fum_jobs'=>'FumJobRepo','fum_logs'=>'FumLogRepo','inventory'=>'InventoryRepo','language'=>'LanguageRepo','mysec_table'=>'MysecTableRepo','payment'=>'PaymentRepo','product'=>'ProductRepo','producttype'=>'ProducttypeRepo','producttype_auto'=>'ProducttypeAutoRepo','rental'=>'RentalRepo','staff'=>'StaffRepo','store'=>'StoreRepo','tablachild'=>'TablachildRepo','tablagrandchild'=>'TablagrandchildRepo','tablaparent'=>'TablaparentRepo','tabletest'=>'TabletestRepo','test_products'=>'TestProductRepo','typetable'=>'TypetableRepo',),array(),'','','BaseSakila','',array(),array());
* </pre>
*/
abstract class AbstractFumJobRepo extends BaseSakila
{
    public const TABLE = 'fum_jobs';
    public const IDENTITY = 'idjob';
    public const PK = [
	    'idjob'
	];
    public const ME=__CLASS__;
    public const EXTRACOLS='';
    /** @var string|null $schema you can set the current schema/database used by this class. [Default is null] */
    public static $schema;

    /**
    * It returns the definitions of the columns<br>
    * <b>Example:</b><br>
    * <pre>
         * self::getDef(); // ['colName'=>[php type,php conversion type,type,size,nullable,extra,sql],'colName2'=>..]
         * self::getDef('sql'); // ['colName'=>'sql','colname2'=>'sql2']
         * self::getDef('identity',true); // it returns the columns that are identities ['col1','col2']
         * </pre>
    * <b>PHP Types</b>: binary, date, datetime, decimal/float,int, string,time, timestamp<br>
    * <b>PHP Conversions</b>:  datetime (datetime class), datetime2 (iso),datetime3 (human string)
    *                         , datetime4 (sql no conversion!), timestamp (int), bool, int, float<br>
    * <b>Param Types</b>: PDO::PARAM_LOB, PDO::PARAM_STR, PDO::PARAM_INT<br>
    *
    * @param string|null $column =['phptype','conversion','type','size','null','identity','sql'][$i]
    *                             if not null then it only returns the column specified.
    * @param string|null $filter If filter is not null, then it uses the column to filter the result.
    *
    * @return array|array[]
    */
    public static function getDef($column = null, $filter = null): array
    {
        $r = [
		    'idjob' => [
		        'phptype' => 'int',
		        'conversion' => NULL,
		        'type' => 'int',
		        'size' => NULL,
		        'null' => FALSE,
		        'identity' => TRUE,
		        'sql' => 'int not null auto_increment'
		    ],
		    'idactive' => [
		        'phptype' => 'int',
		        'conversion' => NULL,
		        'type' => 'int',
		        'size' => NULL,
		        'null' => TRUE,
		        'identity' => FALSE,
		        'sql' => 'int'
		    ],
		    'idstate' => [
		        'phptype' => 'int',
		        'conversion' => NULL,
		        'type' => 'int',
		        'size' => NULL,
		        'null' => TRUE,
		        'identity' => FALSE,
		        'sql' => 'int'
		    ],
		    'dateinit' => [
		        'phptype' => 'timestamp',
		        'conversion' => NULL,
		        'type' => 'timestamp',
		        'size' => NULL,
		        'null' => TRUE,
		        'identity' => FALSE,
		        'sql' => 'timestamp default \'1970-01-01 00:00:01\''
		    ],
		    'datelastchange' => [
		        'phptype' => 'timestamp',
		        'conversion' => NULL,
		        'type' => 'timestamp',
		        'size' => NULL,
		        'null' => TRUE,
		        'identity' => FALSE,
		        'sql' => 'timestamp default \'1970-01-01 00:00:01\''
		    ],
		    'dateexpired' => [
		        'phptype' => 'timestamp',
		        'conversion' => NULL,
		        'type' => 'timestamp',
		        'size' => NULL,
		        'null' => TRUE,
		        'identity' => FALSE,
		        'sql' => 'timestamp default \'1970-01-01 00:00:01\''
		    ],
		    'dateend' => [
		        'phptype' => 'timestamp',
		        'conversion' => NULL,
		        'type' => 'timestamp',
		        'size' => NULL,
		        'null' => TRUE,
		        'identity' => FALSE,
		        'sql' => 'timestamp default \'1970-01-01 00:00:01\''
		    ],
		    'text_job' => [
		        'phptype' => 'binary',
		        'conversion' => NULL,
		        'type' => 'mediumtext',
		        'size' => NULL,
		        'null' => TRUE,
		        'identity' => FALSE,
		        'sql' => 'mediumtext'
		    ],
		    'IDPROCESS' => [
		        'phptype' => 'int',
		        'conversion' => NULL,
		        'type' => 'int',
		        'size' => NULL,
		        'null' => TRUE,
		        'identity' => FALSE,
		        'sql' => 'int'
		    ],
		    'IDFUMIGATION' => [
		        'phptype' => 'int',
		        'conversion' => NULL,
		        'type' => 'int',
		        'size' => NULL,
		        'null' => TRUE,
		        'identity' => FALSE,
		        'sql' => 'int'
		    ],
		    'IDCHAMBER' => [
		        'phptype' => 'int',
		        'conversion' => NULL,
		        'type' => 'int',
		        'size' => NULL,
		        'null' => TRUE,
		        'identity' => FALSE,
		        'sql' => 'int'
		    ],
		    'START' => [
		        'phptype' => 'int',
		        'conversion' => NULL,
		        'type' => 'int',
		        'size' => NULL,
		        'null' => TRUE,
		        'identity' => FALSE,
		        'sql' => 'int'
		    ],
		    'DUMPOPEN' => [
		        'phptype' => 'int',
		        'conversion' => NULL,
		        'type' => 'int',
		        'size' => NULL,
		        'null' => TRUE,
		        'identity' => FALSE,
		        'sql' => 'int'
		    ],
		    'INJECTOPEN' => [
		        'phptype' => 'int',
		        'conversion' => NULL,
		        'type' => 'int',
		        'size' => NULL,
		        'null' => TRUE,
		        'identity' => FALSE,
		        'sql' => 'int'
		    ],
		    'FANOPEN' => [
		        'phptype' => 'int',
		        'conversion' => NULL,
		        'type' => 'int',
		        'size' => NULL,
		        'null' => TRUE,
		        'identity' => FALSE,
		        'sql' => 'int'
		    ],
		    'CHIMNEYOPEN' => [
		        'phptype' => 'int',
		        'conversion' => NULL,
		        'type' => 'int',
		        'size' => NULL,
		        'null' => TRUE,
		        'identity' => FALSE,
		        'sql' => 'int'
		    ],
		    'CHIMNEYPRESENT' => [
		        'phptype' => 'int',
		        'conversion' => NULL,
		        'type' => 'int',
		        'size' => NULL,
		        'null' => TRUE,
		        'identity' => FALSE,
		        'sql' => 'int'
		    ],
		    'MINTEMP' => [
		        'phptype' => 'int',
		        'conversion' => NULL,
		        'type' => 'int',
		        'size' => NULL,
		        'null' => TRUE,
		        'identity' => FALSE,
		        'sql' => 'int'
		    ],
		    'MAXTEMP' => [
		        'phptype' => 'int',
		        'conversion' => NULL,
		        'type' => 'int',
		        'size' => NULL,
		        'null' => TRUE,
		        'identity' => FALSE,
		        'sql' => 'int'
		    ],
		    'RANGEMINTEMP' => [
		        'phptype' => 'int',
		        'conversion' => NULL,
		        'type' => 'int',
		        'size' => NULL,
		        'null' => TRUE,
		        'identity' => FALSE,
		        'sql' => 'int'
		    ],
		    'RANGETEMP' => [
		        'phptype' => 'int',
		        'conversion' => NULL,
		        'type' => 'int',
		        'size' => NULL,
		        'null' => TRUE,
		        'identity' => FALSE,
		        'sql' => 'int'
		    ],
		    'INJECTCOUNTER' => [
		        'phptype' => 'int',
		        'conversion' => NULL,
		        'type' => 'int',
		        'size' => NULL,
		        'null' => TRUE,
		        'identity' => FALSE,
		        'sql' => 'int'
		    ],
		    'PESO_INICIAL' => [
		        'phptype' => 'int',
		        'conversion' => NULL,
		        'type' => 'int',
		        'size' => NULL,
		        'null' => TRUE,
		        'identity' => FALSE,
		        'sql' => 'int'
		    ],
		    'PESO_FINAL' => [
		        'phptype' => 'int',
		        'conversion' => NULL,
		        'type' => 'int',
		        'size' => NULL,
		        'null' => TRUE,
		        'identity' => FALSE,
		        'sql' => 'int'
		    ],
		    'PESOACTUAL' => [
		        'phptype' => 'int',
		        'conversion' => NULL,
		        'type' => 'int',
		        'size' => NULL,
		        'null' => TRUE,
		        'identity' => FALSE,
		        'sql' => 'int'
		    ],
		    'PESOESPERADO' => [
		        'phptype' => 'int',
		        'conversion' => NULL,
		        'type' => 'int',
		        'size' => NULL,
		        'null' => TRUE,
		        'identity' => FALSE,
		        'sql' => 'int'
		    ],
		    'TIMEELAPSED' => [
		        'phptype' => 'int',
		        'conversion' => NULL,
		        'type' => 'int',
		        'size' => NULL,
		        'null' => TRUE,
		        'identity' => FALSE,
		        'sql' => 'int'
		    ],
		    'TIMEINIT' => [
		        'phptype' => 'int',
		        'conversion' => NULL,
		        'type' => 'int',
		        'size' => NULL,
		        'null' => TRUE,
		        'identity' => FALSE,
		        'sql' => 'int'
		    ],
		    'TIMENOFAN' => [
		        'phptype' => 'int',
		        'conversion' => NULL,
		        'type' => 'int',
		        'size' => NULL,
		        'null' => TRUE,
		        'identity' => FALSE,
		        'sql' => 'int'
		    ],
		    'TIMEPREEND' => [
		        'phptype' => 'int',
		        'conversion' => NULL,
		        'type' => 'int',
		        'size' => NULL,
		        'null' => TRUE,
		        'identity' => FALSE,
		        'sql' => 'int'
		    ],
		    'TIMEENDTIMEOUT' => [
		        'phptype' => 'int',
		        'conversion' => NULL,
		        'type' => 'int',
		        'size' => NULL,
		        'null' => TRUE,
		        'identity' => FALSE,
		        'sql' => 'int'
		    ],
		    'DELTATIMEEVACUATION' => [
		        'phptype' => 'int',
		        'conversion' => NULL,
		        'type' => 'int',
		        'size' => NULL,
		        'null' => TRUE,
		        'identity' => FALSE,
		        'sql' => 'int'
		    ],
		    'TIMEEVACUATION' => [
		        'phptype' => 'int',
		        'conversion' => NULL,
		        'type' => 'int',
		        'size' => NULL,
		        'null' => TRUE,
		        'identity' => FALSE,
		        'sql' => 'int'
		    ],
		    'CURMEDICION' => [
		        'phptype' => 'int',
		        'conversion' => NULL,
		        'type' => 'int',
		        'size' => NULL,
		        'null' => TRUE,
		        'identity' => FALSE,
		        'sql' => 'int'
		    ],
		    'TIMEEND' => [
		        'phptype' => 'int',
		        'conversion' => NULL,
		        'type' => 'int',
		        'size' => NULL,
		        'null' => TRUE,
		        'identity' => FALSE,
		        'sql' => 'int'
		    ]
		];
        if ($column !== null) {
            if ($filter === null) {
                foreach ($r as $k => $v) {
                    $r[$k] = $v[$column];
                }
            } else {
                $new = [];
                foreach ($r as $k => $v) {
                    if ($v[$column] === $filter) {
                        $new[] = $k;
                    }
                }
                return $new;
            }
        }
        return $r;
    }

    /**
    * It converts a row returned from the database.<br>
    * If the column is missing then it sets the field as null.
    *
    * @param array $row [ref]
    */
    public static function convertOutputVal(&$row)
    {
        if ($row === false || $row === null) {
            return;
        }
        		!isset($row['idjob']) and $row['idjob']=null; // int
		!isset($row['idactive']) and $row['idactive']=null; // int
		!isset($row['idstate']) and $row['idstate']=null; // int
		!isset($row['dateinit']) and $row['dateinit']=null; // timestamp
		!isset($row['datelastchange']) and $row['datelastchange']=null; // timestamp
		!isset($row['dateexpired']) and $row['dateexpired']=null; // timestamp
		!isset($row['dateend']) and $row['dateend']=null; // timestamp
		!isset($row['text_job']) and $row['text_job']=null; // mediumtext
		!isset($row['IDPROCESS']) and $row['IDPROCESS']=null; // int
		!isset($row['IDFUMIGATION']) and $row['IDFUMIGATION']=null; // int
		!isset($row['IDCHAMBER']) and $row['IDCHAMBER']=null; // int
		!isset($row['START']) and $row['START']=null; // int
		!isset($row['DUMPOPEN']) and $row['DUMPOPEN']=null; // int
		!isset($row['INJECTOPEN']) and $row['INJECTOPEN']=null; // int
		!isset($row['FANOPEN']) and $row['FANOPEN']=null; // int
		!isset($row['CHIMNEYOPEN']) and $row['CHIMNEYOPEN']=null; // int
		!isset($row['CHIMNEYPRESENT']) and $row['CHIMNEYPRESENT']=null; // int
		!isset($row['MINTEMP']) and $row['MINTEMP']=null; // int
		!isset($row['MAXTEMP']) and $row['MAXTEMP']=null; // int
		!isset($row['RANGEMINTEMP']) and $row['RANGEMINTEMP']=null; // int
		!isset($row['RANGETEMP']) and $row['RANGETEMP']=null; // int
		!isset($row['INJECTCOUNTER']) and $row['INJECTCOUNTER']=null; // int
		!isset($row['PESO_INICIAL']) and $row['PESO_INICIAL']=null; // int
		!isset($row['PESO_FINAL']) and $row['PESO_FINAL']=null; // int
		!isset($row['PESOACTUAL']) and $row['PESOACTUAL']=null; // int
		!isset($row['PESOESPERADO']) and $row['PESOESPERADO']=null; // int
		!isset($row['TIMEELAPSED']) and $row['TIMEELAPSED']=null; // int
		!isset($row['TIMEINIT']) and $row['TIMEINIT']=null; // int
		!isset($row['TIMENOFAN']) and $row['TIMENOFAN']=null; // int
		!isset($row['TIMEPREEND']) and $row['TIMEPREEND']=null; // int
		!isset($row['TIMEENDTIMEOUT']) and $row['TIMEENDTIMEOUT']=null; // int
		!isset($row['DELTATIMEEVACUATION']) and $row['DELTATIMEEVACUATION']=null; // int
		!isset($row['TIMEEVACUATION']) and $row['TIMEEVACUATION']=null; // int
		!isset($row['CURMEDICION']) and $row['CURMEDICION']=null; // int
		!isset($row['TIMEEND']) and $row['TIMEEND']=null; // int
        
    }

    /**
    * It converts a row to be inserted or updated into the database.<br>
    * If the column is missing then it is ignored and not converted.
    *
    * @param array $row [ref]
    */
    public static function convertInputVal(&$row) {
        
    }


    /**
    * It gets all the name of the columns.
    *
    * @return string[]
    */
    public static function getDefName() {
        return [
		    'idjob',
		    'idactive',
		    'idstate',
		    'dateinit',
		    'datelastchange',
		    'dateexpired',
		    'dateend',
		    'text_job',
		    'IDPROCESS',
		    'IDFUMIGATION',
		    'IDCHAMBER',
		    'START',
		    'DUMPOPEN',
		    'INJECTOPEN',
		    'FANOPEN',
		    'CHIMNEYOPEN',
		    'CHIMNEYPRESENT',
		    'MINTEMP',
		    'MAXTEMP',
		    'RANGEMINTEMP',
		    'RANGETEMP',
		    'INJECTCOUNTER',
		    'PESO_INICIAL',
		    'PESO_FINAL',
		    'PESOACTUAL',
		    'PESOESPERADO',
		    'TIMEELAPSED',
		    'TIMEINIT',
		    'TIMENOFAN',
		    'TIMEPREEND',
		    'TIMEENDTIMEOUT',
		    'DELTATIMEEVACUATION',
		    'TIMEEVACUATION',
		    'CURMEDICION',
		    'TIMEEND'
		];
    }

    /**
    * It returns an associative array (colname=>key type) with all the keys/indexes (if any)
    *
    * @return string[]
    */
    public static function getDefKey() {
        return [
		    'idjob' => 'PRIMARY KEY',
		    'idactive' => 'KEY',
		    'idstate' => 'KEY',
		    'dateinit' => 'KEY'
		];
    }

    /**
    * It returns a string array with the name of the columns that are skipped when insert
    * @return string[]
    */
    public static function getDefNoInsert() {
        return [
		    'idjob'
		];
    }

    /**
    * It returns a string array with the name of the columns that are skipped when update
    * @return string[]
    */
    public static function getDefNoUpdate() {
        return [
		    'idjob'
		];
    }

    /**
    * It adds a where to the query pipeline. It could be stacked with many where()
    * <b>Example:</b><br>
    * <pre>
         * self::where(['col'=>'value'])::toList();
         * self::where(['col']=>['value'])::toList(); // s= string/double/date, i=integer, b=bool
         * self::where(['col=?']=>['value'])::toList(); // s= string/double/date, i=integer, b=bool
         * </pre>
    *
    * @param array|string   $sql =self::factory()
    * @param null|array|int $param
    *
    * @return PdoOneQuery
    */
    public static function where($sql, $param = PdoOne::NULL)
    {
        return static::newQuery()->where($sql, $param,false,FumJobRepo::TABLE);
    }

    public static function getDefFK($structure=false) {
        if ($structure) {
            return [

			];
        }
        /* key,refcol,reftable,extra */
        return [

		];
    }

    /**
    * It returns all the relational fields by type. '*' returns all types.<br>
    * It doesn't return normal columns.
    *
    * @param string $type=['*','MANYTOONE','ONETOMANY','ONETOONE','MANYTOMANY'][$i]
    *
    * @return string[]
    * @noinspection SlowArrayOperationsInLoopInspection
    */
    public static function getRelations($type = 'all')
    {
        $r = [

		];
        if ($type === '*') {
            $result = [];
            foreach ($r as $arr) {
                $result = array_merge($result, $arr);
            }
            return $result;
        }
        return $r[$type] ?? [];
    }

    /**
    * @param array|int  $filter      (optional) if we want to filter the results.
    * @param array|null $filterValue (optional) the values of the filter
    * @return array|bool|null
    * @throws Exception
    */
    public static function toList($filter=PdoOne::NULL,$filterValue=null) {
        if(self::$useModel) {
            return false; // no model set
        }
        return self::_toList($filter, $filterValue);
    }

    /**
    * It sets the recursivity. By default, if we query or modify a value, it operates with the fields of the entity.
    * With recursivity, we could use the recursivity of the fields, for example, loading a MANYTOONE relation<br>
    * <b>Example:</b><br>
    * <pre>
         * self::setRecursive([]); // (default) no use recursivity.
         * self::setRecursive('*'); // recursive every MANYTOONE,ONETOONE,MANYTOONE and ONETOONE relations (first level)
         * self::setRecursive('MANYTOONE'); // recursive all relations of the type MANYTOONE (first level)
         * self::setRecursive(['_relation1','_relation2']); // recursive only the relations of the first level
         * self::setRecursive(['_relation1','_relation1/_subrelation1']); //recursive the relations (first and second level)
         * </pre>
    * If array then it uses the values to set the recursivity.<br>
    * If string then the values allowed are '*', 'MANYTOONE','ONETOMANY','MANYTOMANY','ONETOONE' (first level only)<br>
    *
    * @param string|array $recursive=self::factory();
    *
    * @return PdoOneQuery
    */
    public static function setRecursive($recursive=[])
    {
        if(is_string($recursive)) {
            $recursive=FumJobRepo::getRelations($recursive);
        }
        return parent::_setRecursive($recursive);
    }

    /**
    * It adds an "limit" in a query. It depends on the type of database<br>
    * <b>Example:</b><br>
    * <pre>
         *      ->select("")->limit("10,20")->toList();
         * </pre>
    *
    * @param string $sql Input SQL query
    *
    * @return PdoOneQuery
    * @throws Exception
    * @test InstanceOf PdoOne::class,this('1,10')
    */
    public static function limit($sql) : PdoOneQuery
    {
        return static::newQuery()->limit($sql);
    }

    /**
    * It returns the first row of a query.<br>
    * <b>Example:</b><br>
    * <pre>
         * Repo::first(); // it returns the first value encountered.
         * Repo::first(2); // it returns the first value where the primary key is equals to 2 (simple primary key)
         * Repo::first([2,3]); // it returns the first value where the primary key is equals to 2 (multiple primary keys)
         * Repo::first(['id'=>2,'id2'=>3]); // it returns the first value where id=2 and id2=3 (multiple primary keys)
         * </pre>
    * @param array|mixed|null $pk [optional] Specify the value of the primary key.
    *
    * @return array|bool It returns false if not file is found.
    * @throws Exception
    */
    public static function first($pk = PdoOne::NULL) {
        if(self::$useModel) {
            /** @noinspection PhpIncompatibleReturnTypeInspection */
            return false; // no model set
        }
        return self::_first($pk);
    }

    /**
    *  It returns true if the entity exists, otherwise false.<br>
    *  <b>Example:</b><br>
    *  <pre>
         *  $this->exist(['id'=>'a1','name'=>'name']); // using an array
         *  $this->exist('a1'); // using the primary key. The table needs a pks and it only works with the first pk.
         *  </pre>
    *
    * @param array|mixed $entity =self::factory()
    *
    * @return bool true if the pks exists
    * @throws Exception
    */
    public static function exist($entity) {
        return self::_exist($entity);
    }

    /**
    * It inserts a new entity(row) into the database<br>
    * @param array|object $entity        =self::factory()
    * @param bool         $transactional If true (default) then the operation is transactional
    *
    * @return array|false=self::factory()
    * @throws Exception
    */
    public static function insert(&$entity,$transactional=true) {
        return self::_insert($entity,$transactional);
    }

    /**
    * It merge a new entity(row) into the database. If the entity exists then it is updated, otherwise the entity is
    * inserted<br>
    * @param array|object $entity        =self::factory()
    * @param bool         $transactional If true (default) then the operation is transactional
    *
    * @return array|false=self::factory()
    * @throws Exception
    */
    public static function merge(&$entity,$transactional=true) {
        return self::_merge($entity,$transactional);
    }

    /**
    * @param array|object $entity        =self::factory()
    * @param bool         $transactional If true (default) then the operation is transactional
    *
    * @return false|int=self::factory()
    * @throws Exception
    */
    public static function update($entity,$transactional=true) {
        return self::_update($entity,$transactional);
    }

    /**
    * It deletes an entity by the primary key
    *
    * @param array|object $entity =self::factory()
    * @param bool         $transactional If true (default) then the operation is transactional
    *
    * @return false|int
    * @throws Exception
    */
    public static function delete($entity,$transactional=true) {
        return self::_delete($entity,$transactional);
    }

    /**
    * It deletes an entity by the primary key.
    *
    * @param array|mixed $pk =self::factory()
    * @param bool        $transactional If true (default) then the operation is transactional
    *
    * @return int|false
    * @throws Exception
    */
    public static function deleteById($pk,$transactional=true) {
        return self::_deleteById($pk,$transactional);
    }

    /**
    * Returns an array with the default values (0 for numbers, empty for string, and array|null if recursive)
    *
    * @param array|null $values          =self::factory()
    * @param string     $recursivePrefix It is the prefix of the recursivity.
    *
    * @return array
    */
    public static function factory($values = null, $recursivePrefix = '') {
        $recursive=static::getRecursive();
        static::setRecursive(); // reset the recursivity.
        $row= [
		'idjob'=>0,
		'idactive'=>0,
		'idstate'=>0,
		'dateinit'=>'',
		'datelastchange'=>'',
		'dateexpired'=>'',
		'dateend'=>'',
		'text_job'=>'',
		'IDPROCESS'=>0,
		'IDFUMIGATION'=>0,
		'IDCHAMBER'=>0,
		'START'=>0,
		'DUMPOPEN'=>0,
		'INJECTOPEN'=>0,
		'FANOPEN'=>0,
		'CHIMNEYOPEN'=>0,
		'CHIMNEYPRESENT'=>0,
		'MINTEMP'=>0,
		'MAXTEMP'=>0,
		'RANGEMINTEMP'=>0,
		'RANGETEMP'=>0,
		'INJECTCOUNTER'=>0,
		'PESO_INICIAL'=>0,
		'PESO_FINAL'=>0,
		'PESOACTUAL'=>0,
		'PESOESPERADO'=>0,
		'TIMEELAPSED'=>0,
		'TIMEINIT'=>0,
		'TIMENOFAN'=>0,
		'TIMEPREEND'=>0,
		'TIMEENDTIMEOUT'=>0,
		'DELTATIMEEVACUATION'=>0,
		'TIMEEVACUATION'=>0,
		'CURMEDICION'=>0,
		'TIMEEND'=>0
		];
        
        if ($values !== null) {
            $row = array_merge($row, $values);
        }
        return $row;
    }

    /**
    * It returns an empty array with null values and no recursivity.
    * @param array|null $values=self::factoryNull()
    *
    * @return array
    */
    public static function factoryNull($values=null) {
        $row= [
		'idjob'=>null,
		'idactive'=>null,
		'idstate'=>null,
		'dateinit'=>null,
		'datelastchange'=>null,
		'dateexpired'=>null,
		'dateend'=>null,
		'text_job'=>null,
		'IDPROCESS'=>null,
		'IDFUMIGATION'=>null,
		'IDCHAMBER'=>null,
		'START'=>null,
		'DUMPOPEN'=>null,
		'INJECTOPEN'=>null,
		'FANOPEN'=>null,
		'CHIMNEYOPEN'=>null,
		'CHIMNEYPRESENT'=>null,
		'MINTEMP'=>null,
		'MAXTEMP'=>null,
		'RANGEMINTEMP'=>null,
		'RANGETEMP'=>null,
		'INJECTCOUNTER'=>null,
		'PESO_INICIAL'=>null,
		'PESO_FINAL'=>null,
		'PESOACTUAL'=>null,
		'PESOESPERADO'=>null,
		'TIMEELAPSED'=>null,
		'TIMEINIT'=>null,
		'TIMENOFAN'=>null,
		'TIMEPREEND'=>null,
		'TIMEENDTIMEOUT'=>null,
		'DELTATIMEEVACUATION'=>null,
		'TIMEEVACUATION'=>null,
		'CURMEDICION'=>null,
		'TIMEEND'=>null
		];
        if ($values !== null) {
            $row = array_merge($row, $values);
        }
        return $row;
    }
}

