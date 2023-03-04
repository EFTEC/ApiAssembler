<?php /** @noinspection NullPointerExceptionInspection */
/** @noinspection DuplicatedCode */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpConditionAlreadyCheckedInspection */

namespace eftec\apiassembler;

use DateTime;
use eftec\CacheOne;
use eftec\CliOne\CliOneParam;
use eftec\PdoOne;
use eftec\PdoOneCli;
use eftec\ValidationOne;
use Exception;
use RuntimeException;

class ApiAssembler extends PdoOneCli
{
    //<editor-fold desc="fields">
    public const VERSION = '1.2';
    /** @var ApiAssemblerRuntime */
    public static $instance;
    public static $counter = 0;
    /** @var PdoOne */
    public $pdo;
    /** @var ValidationOne */
    public $validationOne;
    /** @var array */
    protected $config = [];
    /**
     * @var bool
     */
    protected $interactive = false;
    //</editor-fold>
    /** @var array the key is the class */
    private $tableMethods = [];
    private $templateMethods = ['insert', 'update', 'delete', 'get', 'count', 'listall'
        , 'listpaged', 'listfilter', 'listraw', 'alias', 'validation', 'createauth', 'empty'];

    public function __construct()
    {
        parent::__construct();
        $this->pdo = new PdoOne('test', '127.0.0.1', '', '', 'db');
        $this->pdo->logLevel = 3;
        /* self::$counter++;
         if (self::$counter === 2) {
             throw new RuntimeException('hi');
         }
         self::$instance = $this;
         $this->cli = new CliOne($selfphp ?? __FILE__);*/
    }

    /**
     * @param string  $dir
     * @param string  $table
     * @param string  $classApi
     * @param boolean $override
     * @return string
     */
    public function createAPI(string $dir, string $table, string $classApi, bool $override): string
    {
        if (!isset($this->tableMethods[$table])) {
            return "Table [$table] doesn't have methods assigned";
        }
        $error = '';
        $target = $dir . '/' . $classApi . 'ApiController.php';
        $template = [];
        try {
            $templatelist = $this->templateMethods;
            $templatelist[] = 'class';
            foreach ($templatelist as $t) {
                $filename = "/templates/Template_$t.php";
                $template[$t] = @file_get_contents(__DIR__ . $filename);
                if ($template[$t] === false) {
                    throw new RuntimeException("Unable to read template file $filename");
                }
                // we delete and replace the first line.
                $template[$t] = substr($template[$t], strpos($template[$t], "\n") + 1);
            }
            $methods = "";
            $namespacerepo = $this->cli->getValue('classnamespace');
            //$namespacerepo = $this->cli->getValue('namespace') . "\\" . $this->folderToNs($this->cli->getValue('classdirectory'));
            //$namespaceapi = $this->cli->getValue('namespace') . "\\" . $this->folderToNs($this->cli->getValue('folderapi'));
            $namespaceapi = $this->cli->getValue('namespaceapi');
            foreach ($this->tableMethods[$table] as $method) {
                $argument1 = @$method['argument1'];
                $argument2 = @$method['argument2'];
                switch ($method['type']) {
                    case 'listall':
                    case 'listpaged':
                    case 'get':
                        if ($argument1) {
                            // we remove ' and "
                            $argument1[0] = str_replace(['"', "'"], ["", ""], $argument1[0]);
                            // and we add '
                            $argument1 = "'" . str_replace(',', "','", $argument1) . "'";
                        }
                        break;
                }
                $verb = ($method['verb'] === 'ALL') ? '' : $method['verb'];
                $method['cache'] = $method['cache'] ?? 'nocache';
                $cache = ($method['cache'] === 'nocache') ? 'false' : $method['cache'];
                $body = $verb !== 'GET' ? '$body = $this->api->routeOne->getBody(true);' : '';
                $newmethod = str_replace(['__namespaceapi__', '__namespacerepo__', '__class__', '__classpostfix__', '__version__'
                        , '__name__', '__verb__', '__cache__', '__argument1__', '__argument2__', '__body__']
                    , [$namespaceapi, $namespacerepo, $classApi, $this->cli->getValue('classpostfix'), $this->version(),
                        $method['name'], $verb, $cache, $argument1, $argument2, $body]
                    , $template[$method['type']]);
                $methods .= $newmethod;
            }
            $templateFinal = "<?php\n" . str_replace(['__namespaceapi__', '__namespacerepo__', '__class__'
                        , '__classpostfix__', '__version__', '__methods__']
                    , [$namespaceapi, $namespacerepo, $classApi,
                        $this->cli->getValue('classpostfix'), $this->version(), $methods], $template['class']);
            if (!$override && file_exists($target)) {
                $error = "File exists $target, <yellow>skipped</yellow>";
            } else {
                if ($override && file_exists($target)) {
                    $error = "File exists $target, <green>override</green>";
                }
                $f = @PdoOne::saveFile($target, $templateFinal);
                if ($f === false) {
                    throw new RuntimeException('Unable to generate file');
                }
            }
        } catch (Exception $ex) {
            $error = "File error $target: <red>" . $ex->getMessage() . "</red>";
        }
        return $error;
    }

    /**
     * @param string  $dir
     * @param boolean $override
     * @return array
     */
    public function createAllAPI(string $dir, bool $override): array
    {
        $errors = [];
        foreach ($this->tablexclass as $table => $class) {
            $error = $this->createAPI($dir, $table, $class, $override);
            if ($error) {
                $errors[] = $error;
            }
        }
        return $errors;
    }

    public function createFolder($folderName): string
    {
        $error = '';
        try {
            if (is_dir($folderName)) {
                return "Folder exists $folderName, <yellow>skipped</yellow>";
            }
            $r = mkdir($folderName);
            if ($r === false) {
                throw new RuntimeException('Unable to create folder');
            }
        } catch (Exception $ex) {
            $error = "Folder error $folderName: <red>" . $ex->getMessage() . "</red>";
        }
        return $error;
    }

    public function createContent(): array
    {
        return [
            'databasetype' => $this->cli->getValue('databasetype'),
            'server' => $this->cli->getValue('server'),
            'user' => $this->cli->getValue('user'),
            'password' => $this->cli->getValue('password'),
            'database' => $this->cli->getValue('database'),
            'questionencryption' => $this->cli->getValue('questionencryption'),
            'encryptionpassword' => $this->cli->getValue('encryptionpassword'),
            'encryptionsalt' => $this->cli->getValue('encryptionsalt'),
            'encryptioniv' => $this->cli->getValue('encryptioniv'),
            'encryptionmethod' => $this->cli->getValue('encryptionmethod'),
            'hashmethod' => $this->cli->getValue('hashmethod'),
            'classnamespace' => $this->cli->getValue('classnamespace'),
            'namespaceapi' => $this->cli->getValue('namespaceapi'),
            'composerpath' => $this->cli->getValue('composerpath'),
            'classdirectory' => $this->cli->getValue('classdirectory'),
            'classpostfix' => $this->cli->getValue('classpostfix'),
            'folderapi' => $this->cli->getValue('folderapi'),
            'tables' => $this->cli->getValue('tables'),
            'tablexclass' => $this->tablexclass,
            'tableMethods' => $this->tableMethods,
            'questionroute' => $this->cli->getValue('questionroute'),
            'questionaccess' => $this->cli->getValue('questionhtaccess'),
            'questiondev' => $this->cli->getValue('questiondev'),
            'machineid' => $this->cli->getValue('machineid'),
            'baseurl_dev' => $this->cli->getValue('baseurl_dev'),
            'baseurl_prod' => $this->cli->getValue('baseurl_prod'),
            'templateurl' => $this->cli->getValue('templateurl'),
            'questioncache' => $this->cli->getValue('questioncache'),
            'cache_type' => $this->cli->getValue('cache_type'),
            'cache_server' => $this->cli->getValue('cache_server'),
            'cache_schema' => $this->cli->getValue('cache_schema'),
            'cache_port' => $this->cli->getValue('cache_port'),
            'cache_user' => $this->cli->getValue('cache_user'),
            'cache_password' => $this->cli->getValue('cache_password'),
            'trysavescript' => $this->cli->getValue('trysavescript'),
            'filenamescript' => $this->cli->getValue('filenamescript'),
            'conversion' => $this->conversion,
            'alias' => $this->alias,
            'extracolumn' => $this->extracolumn,
            'removecolumn' => $this->removecolumn,
            'columnsTable' => $this->columnsTable,
            'columnsAlias' => $this->columnsAlias,
            'authtype' => $this->cli->getValueKey('authtype'),
            'authtype_bear' => $this->cli->getValue('authtype_bear'),
            'authstore_type' => $this->cli->getValueKey('authstore_type'),
            'authstore_table' => $this->cli->getValue('authstore_table'),
            'authstore_user' => $this->cli->getValue('authstore_user'),
            'authstore_password' => $this->cli->getValue('authstore_password'),
        ];
    }

    public function openFile($file): void
    {
        try {
            $content = @file_get_contents($file . '.config.php');
            if ($content === false || $content === '') {
                throw new RuntimeException('file not found or unable to read it');
            }
            $this->cli->showCheck('OK', 'green', " Configuration file open: $file.config.php'\n");
        } catch (Exception $e) {
            $this->cli->showCheck('WARNING', 'yellow', "Unable to read configuration file $file: {$e->getMessage()}");
            $this->reset();
            return;
        }
        $content = substr($content, strpos($content, "\n") + 1);
        $this->config = json_decode($content, true);
        $this->cli->setParam('databasetype', $this->config['databasetype']);
        $this->cli->setParam('server', $this->config['server']);
        $this->cli->setParam('user', $this->config['user']);
        $this->cli->setParam('password', $this->config['password']);
        $this->cli->setParam('database', $this->config['database']);
        $this->cli->setParam('database', $this->config['database']);
        // questionencryption,encryptionpassword,encryptionsalt,encryptioniv,encryptionmethod,hashmethod
        $this->cli->setParam('questionencryption', $this->config['questionencryption']);
        $this->cli->setParam('encryptionpassword', $this->config['encryptionpassword']);
        $this->cli->setParam('encryptionsalt', $this->config['encryptionsalt']);
        $this->cli->setParam('encryptioniv', $this->config['encryptioniv']);
        $this->cli->setParam('encryptionmethod', $this->config['encryptionmethod']);
        $this->cli->setParam('hashmethod', $this->config['hashmethod']);
        $this->cli->setParam('classnamespace', $this->config['classnamespace']);
        $this->cli->setParam('namespaceapi', $this->config['namespaceapi']);
        $this->cli->setParam('composerpath', $this->config['composerpath']);
        $this->cli->setParam('classdirectory', $this->config['classdirectory']);
        $this->cli->setParam('classpostfix', @$this->config['classpostfix']);
        $this->cli->setParam('folderapi', $this->config['folderapi']);
        $this->cli->setParam('tables', $this->config['tables']);
        $this->cli->setParam('hashmethod', $this->config['hashmethod']);
        $this->tablexclass = $this->config['tablexclass'] ?? [];
        $this->tableMethods = $this->config['tableMethods'] ?? [];
        $this->columnsTable = $this->config['columnsTable'] ?? [];
        $this->columnsAlias = $this->config['columnsAlias'] ?? [];
        $this->conversion = $this->config['conversion'] ?? [];
        if (count($this->conversion) <= 1) {
            $this->conversion = $this->convertReset();
        }
        $this->alias = $this->config['alias'] ?? [];
        $this->extracolumn = $this->config['extracolumn'] ?? [];
        $this->removecolumn = $this->config['removecolumn'] ?? [];
        //         'namespacerepo'
        $this->cli->setParam('questionroute', @$this->config['questionroute']);
        $this->cli->setParam('questionhtaccess', @$this->config['questionhtaccess']);
        $this->cli->setParam('questiondev', $this->config['questiondev']);
        $this->cli->setParam('machineid', $this->config['machineid']);
        $this->cli->setParam('baseurl_dev', $this->config['baseurl_dev']);
        $this->cli->setParam('baseurl_prod', $this->config['baseurl_prod']);
        $this->cli->setParam('templateurl', $this->config['templateurl']);
        $this->cli->setParam('questioncache', $this->config['questioncache']);
        $this->cli->setParam('cache_type', $this->config['cache_type']);
        $this->cli->setParam('cache_server', $this->config['cache_server']);
        $this->cli->setParam('cache_schema', $this->config['cache_schema']);
        $this->cli->setParam('cache_port', $this->config['cache_port']);
        $this->cli->setParam('cache_user', $this->config['cache_user']);
        $this->cli->setParam('cache_password', $this->config['cache_password']);
        $this->cli->setParam('trysavescript', $this->config['trysavescript']);
        $this->cli->setParam('filenamescript', $this->config['filenamescript']);
        $this->cli->setParam('authtype', @$this->config['authtype'], true);
        $this->cli->setParam('authtype_bear', @$this->config['authtype_bear']);
        $this->cli->setParam('authstore_type', @$this->config['authstore_type'], true);
        $this->cli->setParam('authstore_table', @$this->config['authstore_table']);
        $this->cli->setParam('authstore_user', @$this->config['authstore_user']);
        $this->cli->setParam('authstore_password', @$this->config['authstore_password']);
    }

    public function createRouter($override, $file = 'router.php'): void
    {
        if ($this->cli->getValue('questionroute') !== 'yes') {
            $this->cli->showCheck('WARNING', 'yellow', "Router not created");
            return;
        }
        $links = '';
        foreach ($this->tablexclass as $table => $classApi) {
            $links .= "echo '<ul>';\n\t\t";
            $links .= "echo '<li><b>$classApi</b></li>';\n\t\t";
            if (!isset($this->tableMethods[$table]) || count($this->tableMethods[$table]) === 0) {
                $links .= "echo \"<li>no method defined</li>\";\n\t\t";
            } else {
                foreach ($this->tableMethods[$table] as $method) {
                    $links .= "echo \"<li><a href='./$classApi/" . $method['name'] . "'>$classApi/" . $method['name'] . " (" . $method['verb'] . ")</a></li>\";\n\t\t";
                }
            }
            $links .= "echo '</ul>';\n\t\t";
        }
        try {
            $content = @file_get_contents(__DIR__ . "/templates/Template_router.php");
            if (!$content) {
                throw new RuntimeException('file not found or unable to read it');
            }
            $content = substr($content, strpos($content, "\n") + 1); // remove first line
        } catch (Exception $e) {
            $this->cli->showCheck('ERROR', 'red', " Unable to read file $file: {$e->getMessage()}");
            $this->reset();
            return;
        }
        $content = "<?php\n" . $content;
        /*
         *            'authtype' => $this->cli->getValueKey('authtype'),
            'authstore_type' => $this->cli->getValueKey('authstore_type'),
            'authstore_table' => $this->cli->getValue('authstore_table'),
            'authstore_user' => $this->cli->getValue('authstore_user'),
            'authstore_password' => $this->cli->getValue('authstore_password'),
         */
        $content = str_replace(['__databasetype__',
                '__server__',
                '__user__',
                '__password__',
                '__database__',
                '__namespaceapi__',
                '__folderrepo__',
                '__classpostfix__',
                '__folderapi__',
                '__questiondev__',
                '__composerpath__',
                '__machineid__',
                '__baseurl_dev__',
                '__baseurl_prod__',
                '__templateurl__',
                '__questioncache__',
                '__cache_type__',
                '__cache_server__',
                '__cache_schema__',
                '__cache_port__',
                '__cache_user__',
                '__cache_password__',
                '__authtype__',
                '__authtype_bear__',
                '__authstore_type__',
                '__authstore_table__',
                '__authstore_user__',
                '__authstore_password__',
                '__links__',
                '__version__',
                '__encryptionpassword__',
                '__questionencryption__',
                '__encryptionsalt__',
                '__encryptionmethod__',
                '__hashmethod__',
                '__encryptioniv__']
            , [$this->cli->getValue('databasetype'),
                $this->cli->getValue('server'),
                $this->cli->getValue('user'),
                $this->cli->getValue('password'),
                $this->cli->getValue('database'),
                $this->cli->getValue('namespaceapi'),
                $this->cli->getValue('classdirectory'),
                $this->cli->getValue('classpostfix'),
                $this->cli->getValue('folderapi'),
                $this->cli->getValue('questiondev') === 'dev' ? '=' : '!',
                $this->cli->getValue('composerpath'),
                $this->cli->getValue('machineid'),
                $this->cli->getValue('baseurl_dev'),
                $this->cli->getValue('baseurl_prod'),
                $this->cli->getValue('templateurl'),
                $this->cli->getValue('questioncache') === 'yes' ? 'true' : 'false',
                $this->cli->getValueKey('cache_type'),
                $this->cli->getValue('cache_server'),
                $this->cli->getValue('cache_schema'),
                $this->cli->getValue('cache_port'),
                $this->cli->getValue('cache_user'),
                $this->cli->getValue('cache_password'),
                $this->cli->getValueKey('authtype'),
                $this->cli->getValue('authtype_bear') === 'yes' ? 'true' : 'false',
                $this->cli->getValueKey('authstore_type'),
                $this->cli->getValue('authstore_table'),
                $this->cli->getValue('authstore_user'),
                $this->cli->getValue('authstore_password'),
                $links,
                $this->version(),
                $this->cli->getValue('encryptionpassword'),
                $this->cli->getValue('questionencryption') === 'yes' ? 'true' : 'false',
                $this->cli->getValue('encryptionsalt'),
                $this->cli->getValue('encryptionmethod'),
                $this->cli->getValue('hashmethod'),
                $this->cli->getValue('encryptioniv') === 'yes' ? 'true' : 'false']
            , $content);
        if (!$override && file_exists($file)) {
            $this->cli->showLine("File exists $file, <yellow>skipped</yellow>");
        } else {
            try {
                $f = PdoOne::saveFile($file, $content);
                if ($f === false) {
                    throw new RuntimeException('Unable to save file');
                }
                $this->cli->showCheck('OK', 'green', " Router.php saved correctly");
            } catch (Exception $ex) {
                $this->cli->showCheck('ERROR', 'red', " Unable to save router.php file: {$ex->getMessage()}");
            }
        }
    }

    public function reset(): void
    {
        $this->tablexclass = [];
        $this->tableMethods = [];
    }

    public function runParam(): void
    {
        $this->cli->createParam('file')
            ->setDefault('file')
            ->setDescription('open a file')->add();
    }

    /** @noinspection PhpUnused */
    public function runParamInit(): void
    {
        $this->cli->createParam('interactive', 'longflag', 'i')
            ->setDescription('Interactive mode')
            ->setAllowEmpty()
            ->setInput(false)->add();
    }

    protected function injectEvalGenerate($command): void
    {
        parent::injectEvalGenerate($command);
        switch ($command) {
            case 'apifolder':
                $this->runNameSpaceFolders();
                break;
            case 'encryption':
                $this->runEncryption();
                break;
            case 'router':
                $this->runRoute();
                break;
            case 'auth':
                $this->runAuth();
                break;
            case 'users':
                $this->runUser();
                break;
            case 'apimethod':
                $this->runApiMethod();
                break;
            case 'cache':
                $this->runCache();
                break;
            case 'runother':
                $this->runApiMethod();
                $this->runRoute();
                break;
            case 'generateapi':
                if (!$this->cli->getValue('folderapi')) {
                    $this->cli->showCheck('error', 'red', 'Folder is missing, set a folder in "apifolder"');
                } else if (!$this->cli->getValue('classdirectory')) {
                    $this->cli->showCheck('error', 'red', 'Folder is missing, set a folder in "folder"');
                } else {
                    $this->runCreateClasses();
                }
                break;
            case 'postman':
                if (!$this->cli->getValue('folderapi')) {
                    $this->cli->showCheck('error', 'red', 'Folder is missing, set a folder in "apifolder"');
                } else if (!$this->cli->getValue('classdirectory')) {
                    $this->cli->showCheck('error', 'red', 'Folder is missing, set a folder in "folder"');
                } else {
                    $this->runCreatePostman();
                }
                break;
            case 'saveapi':
                $this->runSaveFile();
                $this->runSaveScript();
                break;
            case 'quit':
                $this->runSaveFile();
                $this->runSaveScript();
                $this->runEnd();
                exit(0);
        }
    }

    protected function injectEvalParam($firstCommand, $interactive): void
    {
        parent::injectEvalParam($firstCommand, $interactive);
        if ($firstCommand === 'createapi') {
            //$this->runCreate();
            $this->cli->createParam('create')
                ->setDefault('')
                ->setAllowEmpty()
                ->setDescription('Create the classes')->add();
            $this->cli->createParam('try', false)
                ->setDescription('', 'Do you want to retry?')
                ->setInput(true, 'optionshort', ['yes', 'no'])->add();
            // usage
            $this->cli->evalParam('create');
            $this->runCreate();
            // add here.
            //$this->RunNameSpaceFolders();
            //$this->runSelectTable();
        }
    }

    protected function injectInitParam(): void
    {
        parent::injectInitParam();
        $this->cli->createParam('createapi', [], 'first')
            ->setRequired(false)
            ->setAllowEmpty()
            ->setDescription('It generates the api classes', '', [
                'Example: <dim>"createapi --loadconfig myconfig"</dim>.Load a config and generate in interactive mode',
                'Example: <dim>"createapi --command scan --loadconfig .\p2.php -og yes"</dim>. Load a config, scan for changes and override'])
            ->setDefault('')
            ->setInput(false)
            ->add();
    }

    protected function injectInitParam2($firstCommand, $interactive): void
    {
        parent::injectInitParam2($firstCommand, $interactive);
        $this->runParam();
        $this->runEncryptionParam();
        $this->runNamespaceParameter();
        $this->runRouteParam();
        $this->runCacheParam();
        $this->runSaveScriptParam();
        $this->runCreatePostmanParam();
        $this->runAuthParam();
        $this->runUserParam();
        $this->cli->createParam('selectclassapimethod')
            ->setInput(true, 'option2', [])
            ->setDefault('')
            ->setAllowEmpty()
            ->setDescription('You can select the table where you can want to add or remove a method'
                , 'Select the table you want to add a or remove a (empty to continue)')
            ->add();
        $this->cli->createParam('selectclassapimethod_add_remove', [], 'none')
            ->setInput(true, 'optionshort', ['add', 'remove'])
            ->setDefault('')
            ->setAllowEmpty()
            ->setDescription('Select if you want to add or remove an API method'
                , 'Do you want to add or remove? (empty to continue)')
            ->add();
        /*$this->cli->createParam('newnameid2')
            ->setInput(true, 'range', [1, count($this->cli->getValue('tables'))])
            ->setDescription('the folder of the repository class', 'Select the class to add a method')
            ->add();*/
        $this->cli->createParam('newmethod')
            ->setInput()
            ->setDefault('')
            //->setAllowEmpty()
            ->setDescription('The name of the new method'
                , 'Select the name of the new method (or * for empty)')
            ->add();
        $this->cli->createParam('newmethod_verb')
            ->setDefault('ALL')
            ->setInput(true, 'optionshort', ['GET', 'POST', 'PUT', 'DELETE', 'ALL'])
            ->setDescription('The WEB VERB of the new method'
                , 'Select the HTML VERB of the new method')
            ->add();
        $this->cli->createParam('newmethod_cache')
            ->setDefault('nocache')
            ->setInput(true, 'wide-option', [
                'nocache' => 'no cache',
                '0' => 'unlimited',
                '60' => '1 minute',
                '900' => '15 minutes',
                '1800' => '30 minutes',
                '3600' => '1 hour',
                '7200' => '2 hours',
                '28800' => '8 hours',
                '86400' => '1 day',
                '1296000' => '15 days',
                '2592000' => '30 days'])
            ->setDescription('The duration of the cache'
                , 'What is the duration of the cache?')
            ->add();
        $this->cli->createParam('newmethod_argument1')
            ->setInput()
            ->setDefault('')
            ->setAllowEmpty()
            ->setDescription('If you want to specify an aditional argument'
                , 'Add an extra argument')
            ->add();
        $this->cli->createParam('newmethod_argument2')
            ->setInput()
            ->setDefault('')
            ->setAllowEmpty()
            ->setDescription('If you want to specify an aditional argument'
                , 'Add an extra argument')
            ->add();
        $this->cli->setErrorType()->createParam('tables')
            ->setDefault($this->cli->getValue('tables'))
            ->setInput(true, 'multiple', [])
            ->setDescription('The tables to process', 'Select or de-select a table to process')
            ->add();
    }

    protected function InjectLoadFile($firstCommand, $interactive): void
    {
        parent::InjectLoadFile($firstCommand, $interactive);
        $loadconfig = $this->cli->evalParam('loadconfig');
        if ($loadconfig->value) {
            $this->openFile($this->cli->getValue('loadconfig'));
        } else {
            $this->reset();
        }
    }

    protected function injectRunParam($firstCommand, $interactive): void
    {
        parent::injectRunParam($firstCommand, $interactive); // TODO: Change the autogenerated stub
        if ($firstCommand === 'createapi') {
            if (!$this->help->missing) {
                $this->showHelpCreateApi();
            } else {
                unset(
                    $this->cli->getParameter('command')->inputValue['save'],
                    $this->cli->getParameter('command')->inputValue['exit']);
                $this->cli->getParameter('command')->inputValue['apifolder'] = 'Select the folder and the PHP namespace of the API';
                $this->cli->getParameter('command')->inputValue['encryption'] = 'Set the parameters used for the encryption';
                $this->cli->getParameter('command')->inputValue['router'] = 'Set the parameters of the router and .htaccess';
                $this->cli->getParameter('command')->inputValue['apimethod'] = 'Add or remove API methods';
                $this->cli->getParameter('command')->inputValue['cache'] = 'Configure the cache. It optimizes the queries and it could be required for authentication';
                $this->cli->getParameter('command')->inputValue['auth'] = 'Configure the Authetication';
                $this->cli->getParameter('command')->inputValue['users'] = 'Manage users for authentication';
                $this->cli->getParameter('command')->inputValue['generateapi'] = 'Generate the API controller classes';
                $this->cli->getParameter('command')->inputValue['postman'] = 'Generate a Postman(tm) collection v2.1.';
                $this->cli->getParameter('command')->inputValue['saveapi'] = 'Save the configuration and shell script';
                $this->cli->getParameter('command')->inputValue['quit'] = 'Save and quit';
                $this->runCliGeneration();
                //$this->runRename();
                $this->runApiMethod();
                $this->runAuth();
                $this->runRoute();
                $this->runCache();
                $this->runCreateClasses();
                $this->runSaveFile();
                $this->runSaveScript();
                $this->runEnd();
                $this->cli->evalParam('file');
                if ($this->cli->getValue('file')) {
                    $this->openFile($this->cli->getValue('file'));
                } else {
                    $this->reset();
                }
            }
        }
    }

    protected function showHelpCreateApi(): void
    {
        $this->cli->showParamSyntax2('Commands:', ['first'], [], null, null, 25);
        $this->cli->showParamSyntax2('Flags for createapi:',
            ['flag', 'longflag'],
            ['classdirectory',
                'classnamespace',
                'tables',
                'tablescolumns',
                'tablecommand',
                'convertionselected',
                'convertionnewvalue',
                'newclassname',
            ]
            , null, 'generate', 25);
    }

    protected function runUserParam(): void
    {
        //todo:
        $this->cli->createParam('user_question', '', 'none')
            ->setDefault('')
            ->setDescription('', 'Do you want to add or remove an user? (empty to return)')
            ->setAllowEmpty()
            ->setInput(true, 'optionshort', ['add', 'update', 'remove'])
            ->add(true);
        $this->cli->createParam('user_remove', '', 'none')
            ->setDefault('')
            ->setDescription('', 'Select the user to remove (empty to return)')
            ->setAllowEmpty()
            ->setInput(true, 'option2', [])
            ->add(true);
        $this->cli->createParam('user_field', '', 'none')
            ->setDefault('')
            ->setAllowEmpty()
            ->setDescription('', 'Select the field **** of the new user')
            ->setInput()
            ->add(true);
    }

    protected function runAuthParam(): void
    {
        $this->cli->createParam('authtype', '', 'onlyinput')
            ->setCurrentAsDefault()
            ->setDescription('', 'Select the type of authentication (empty for exit)')
            ->setAllowEmpty()
            ->setInput(true, 'option', [
                'none' => 'no authentication',
                'session' => 'PHP session',
                'token' => 'token',
                'userpwd' => 'User and password',
                'jwtlite' => 'JWT like token bearer'
            ])
            ->add(true);
        $this->cli->createParam('authtype_bear', '', 'onlyinput')
            ->setCurrentAsDefault()
            ->setDescription('', 'Do you want to use token bearer')
            ->setAllowEmpty()
            ->setInput(true, 'optionshort', ['yes', 'no'])
            ->add(true);
        $this->cli->createParam('authstore_type', '', 'onlyinput')
            ->setDescription('', 'Select the type of authentication (empty for exit)')
            ->setAllowEmpty()
            ->setCurrentAsDefault()
            ->setInput(true, 'option', [
                'pdo' => 'It uses PDO (database)',
                'document' => 'It uses the file-system',
                'token' => 'it uses a cache-library that it could use redis,pdo,document,memcached and apcu'
            ])
            ->add(true);
        $this->cli->createParam('authstore_table', '', 'onlyinput')
            ->setDescription('', 'Select the table')
            ->setAllowEmpty()
            ->setCurrentAsDefault()
            ->setInput()
            ->add(true);
        $this->cli->createParam('authstore_user', '', 'onlyinput')
            ->setDescription('', 'Select the user field')
            ->setAllowEmpty()
            ->setCurrentAsDefault()
            ->setInput()
            ->add(true);
        $this->cli->createParam('authstore_password', '', 'onlyinput')
            ->setDescription('', 'Select the password field')
            ->setAllowEmpty()
            ->setCurrentAsDefault()
            ->setInput()
            ->add(true);
    }

    protected function runUser(): void
    {
        if (!$this->cli->getParameter('authtype')->value || !$this->cli->getParameter('authstore_table')) {
            $this->cli->showCheck('ERROR', 'red', "You are not set the authentication\n");
            return;
        }
        // setting encryption
        if ($this->cli->getValue('questionencryption') === 'yes') {
            $this->pdo->setEncryption(
                $this->cli->getValue('encryptionpassword'),
                $this->cli->getValue('encryptionsalt'),
                $this->cli->getValue('encryptionmethod')
            );
        }
        /*            'questionencryption' => $this->cli->getValue('questionencryption'),
            'encryptionpassword' => $this->cli->getValue('encryptionpassword'),
            'encryptionsalt' => $this->cli->getValue('encryptionsalt'),
            'encryptioniv' => $this->cli->getValue('encryptioniv'),
            'encryptionmethod' => $this->cli->getValue('encryptionmethod'),
        */
        $table = $this->cli->getValue('authstore_table');
        $pwdfield = $this->cli->getValue('authstore_password');
        $this->cli->upLevel('Authentication users');
        while (true) {
            $this->cli->setColor(['byellow'])->showBread();
            $getUsers = $this->pdo->select('*')->from($table)->toList();
            $tableSchema = $this->pdo->columnTable($table);
            $columns = [];
            foreach ($tableSchema as $t) {
                //if($t['isidentity'])
                $columns[] = $t['colname'];
            }
            $pkField = @$this->pdo->getPK($table)[0];
            if (!$pkField) {
                $this->cli->showCheck('ERROR', 'red', " Table doesn't have a primary key, using first column.");
                $pkField = $columns[0];
            }
            $getUserNoPwd = [];
            foreach ($getUsers as $usr) {
                $getUserNoPwd[] = $this->removeColumnsArray($usr, [$pwdfield]);
            }
            $this->cli->showTable($getUserNoPwd, false, false, false, 4);
            $user_remove = $this->cli->evalParam('user_question', true)->value;
            switch ($user_remove) {
                case 'add':
                    // adding a new user
                    $this->runUserAdd($columns, $pwdfield, $table);
                    break;
                case 'update':
                    // update a new user
                    $this->runUserUpdate($columns, $pkField, $pwdfield, $table);
                    break;
                case 'remove':
                    $this->runUserRemove($table, $pkField, $pwdfield);
                    break;
                case '':
                    break(2);
            }
        }
        $this->cli->downLevel();
    }

    public function runUserAdd(array $columns, $pwdfield, $table): void
    {
        $this->pdo->setEncryption($this->cli->getValue('encryptionpassword'),
            $this->cli->getValue('encryptionsalt'),
            $this->cli->getValue('encryptionmethod'));
        $this->cli->upLevel('Add');
        $this->cli->setColor(['byellow'])->showBread();
        $user = [];
        foreach ($columns as $column) {
            $value = $this->cli
                ->getParameter('user_field')
                ->setDescription('', "Select the field <cyan>$column</cyan> of the new user (or empty for default)")
                ->evalParam(true)->value;
            if ($value) {
                $user[$column] = $value;
            }
        }
        // encrypt the password
        $originalpwd = $user[$pwdfield];
        $user[$pwdfield] = $this->pdo->hash($user[$pwdfield]);
        if ($originalpwd !== $user[$pwdfield]) {
            $this->cli->showCheck('OK', 'green', " Password encrypted correctly");
        } else {
            $this->cli->showCheck('WARNING', 'yellow', " Encryption is not set, password will not be encrypted");
        }
        // inserting user
        try {
            $this->pdo->insertObject($table, $user);
            $this->cli->showCheck('OK', 'green', " User created correctly");
        } catch (Exception $ex) {
            $this->cli->showCheck('ERROR', 'red', " Error in creation of user <red>{$ex->getMessage()}</red>.");
        }
        $this->cli->downLevel();
    }

    public function runUserUpdate(array $columns, $pkField, $pwdfield, $table): void
    {
        $this->cli->upLevel('Update');
        $this->cli->setColor(['byellow'])->showBread();
        $pk = $this->cli
            ->getParameter('user_field')
            ->setDefault('')
            ->setDescription('', "Select the primary key ($pkField) of the user to update (or empty for default)")
            ->evalParam(true)->value;
        $user = $this->pdo->select('*')->from($table)->first($pk);
        if (!$user) {
            $this->cli->showCheck('WARNING', 'yellow', " User not found");
            $this->cli->downLevel();
            return;
        }
        unset($user[$pkField]);
        $originalpwd = $user[$pwdfield];
        $where = [$pkField => $pk];
        foreach ($columns as $column) {
            if ($column !== $pkField) {
                $value = $this->cli
                    ->getParameter('user_field')
                    ->setDefault($column === $pwdfield ? '' : @$user[$column])
                    ->setDescription('', "Select the value of the field <cyan>$column</cyan> of the user to update (or empty to default)")
                    ->evalParam(true)->value;
                if ($value) {
                    $user[$column] = $value;
                }
            }
        }
        // encrypt the password
        $editedPwd = $user[$pwdfield];
        if ($originalpwd === $editedPwd) {
            // password not changed
            unset($user[$pwdfield]);
        } else {
            // password changed, so it must be encrypted
            $user[$pwdfield] = $this->pdo->encrypt($user[$pwdfield]);
            if ($originalpwd !== $user[$pwdfield]) {
                $this->cli->showCheck('OK', 'green', " Password encrypted correctly");
            } else {
                $this->cli->showCheck('WARNING', 'yellow', " Encryption is not set, password will not be encrypted");
            }
        }
        // updating user
        try {
            $this->pdo->from($table)->set($user)->where($where)->update();
            $this->cli->showCheck('OK', 'green', " User updated correctly");
        } catch (Exception $ex) {
            $this->cli->showCheck('ERROR', 'red', " Error in creation of user <red>{$ex->getMessage()}</red>.");
        }
        $this->cli->downLevel();
    }

    public function runUserRemove($table, $pkField, $pwdfield): void
    {
        $this->cli->upLevel('Remove');
        $this->cli->setColor(['byellow'])->showBread();
        $getUsers = $this->pdo->select('*')->from($table)->toList();
        $usersOption = [];
        foreach ($getUsers as $user) {
            $usersOption[$user[$pkField]] = str_replace(['"', '{', '}', ','], ['', '', '', ', ']
                , json_encode($this->removeColumnsArray($user, [$pkField, $pwdfield])));
        }
        $idUser = $this->cli->getParameter('user_remove')
            ->setInput(true, 'option2', $usersOption)
            ->evalParam(true)->valueKey;
        if ($idUser !== '' && $idUser !== $this->cli->emptyValue) {
            try {
                $this->pdo->delete($table, [$pkField => $idUser]);
                $this->cli->showCheck('OK', 'green', " User removed correctly");
            } catch (Exception $ex) {
                $this->cli->showCheck('ERROR', 'red', " Error in deletion of user <red>{$ex->getMessage()}</red>.");
            }
        }
        $this->cli->downLevel();
    }

    protected function removeColumnsArray(array $assocArray, array $columns): array
    {
        foreach ($columns as $col) {
            unset($assocArray[$col]);
        }
        return $assocArray;
    }

    protected function runAuth(): void
    {
        $this->cli->upLevel('Authentication');
        /** @noinspection PhpLoopNeverIteratesInspection */
        while (true) {
            $this->cli->setColor(['byellow'])->showBread();
            $authtype = $this->cli->evalParam('authtype', true);
            switch ($authtype->valueKey) {
                case 'session':
                case 'token':
                case 'userpwd':
                case 'jwtlite':
                    break;
                case 'none':
                case '':
                    break(2);
            }
            while (true) {
                //$this->cli->setColor(['byellow'])->showBread();
                $authtype = $this->cli->evalParam('authstore_type', true);
                switch ($authtype->valueKey) {
                    case 'pdo':
                    case 'document':
                    case 'token':
                        $this->cli->evalParam('authtype_bear', true);
                        $this->cli->evalParam('authstore_table', true);
                        $this->cli->evalParam('authstore_user', true);
                        $this->cli->evalParam('authstore_password', true);
                        break(3);
                    case '':
                        break(3);
                }
            }
        }
        $this->cli->downLevel();
    }

    protected function runApiMethod(): void
    {
        $this->cli->upLevel('API Methods');
        $this->cli->showLine("\nMethods:\n");
        if (count($this->tableMethods) === 0) {
            // reset class methods
            foreach ($this->tablexclass as $table => $class) {
                $this->tableMethods[$table] = [];
            }
        }
        $this->cli->createParam('typemethod')
            ->setInput(true, 'wide-option'
                , $this->templateMethods)
            ->setDefault('')
            ->setAllowEmpty()
            ->setDescription('', 'Select the type of method (empty for exit)')
            ->add(true);
        $this->cli->createParam('selectmethod')
            ->setInput(true, 'option', [])
            ->setDefault('')
            ->setAllowEmpty()
            ->setDescription('', 'Select the method (empty for exit)')
            ->add(true);
        $tables = $this->tablexclass; //array_keys($this->tablexclass);
        $tables['*'] = 'All tables';
        while (true) { // method
            $this->cli->setColor(['byellow'])->showBread();
            //$this->cli->upLevel('addmethod1')->setColor(['byellow'])->showBread();
            $this->cli->getParameter('selectclassapimethod')->inputValue = $tables;
            $classSelected = $this->cli->evalParam('selectclassapimethod', true);
            if ($classSelected->value === '' || $classSelected->value === null) {
                break; // while
            }
            $this->cli->upLevel($classSelected->value, ' (table)');
            while (true) {
                $this->cli->setColor(['byellow'])->showBread();
                $this->runApiMethodShowTable($classSelected);
                $tmpAction = $this->cli->evalParam('selectclassapimethod_add_remove', true);
                switch ($tmpAction->value) {
                    case 'add':
                        $this->cli->upLevel('add');
                        $this->runApiMethodAdd($classSelected);
                        $this->cli->downLevel();
                        break;
                    case 'remove':
                        $this->cli->upLevel('remove');
                        $this->runApiMethodRemove($classSelected);
                        $this->cli->downLevel();
                        break;
                    default:
                        //$this->cli->downLevel();
                        break(2);
                }
            }
            $this->cli->downLevel();
            //$id = $this->cli->evalParam( 'newnameid2');
            //$this->cli->downLevel();
        }
        $this->cli->downLevel();
    }

    protected function runApiMethodShowTable($classSelected): void
    {
        $tdisplay = [];
        $row = [];
        if ($classSelected->valueKey === '*') {
            $counter = [];
            foreach ($this->tablexclass as $ttable => $tclass) {
                foreach ($this->tableMethods[$ttable] ?? [] as $method) {
                    $uid = $method['name'] . '?' . $method['type'] . '?' . $method['argument1'] . $method['argument2'];
                    if (isset($counter[$uid])) {
                        $counter[$uid]++;
                    } else {
                        $counter[$uid] = 1;
                    }
                }
            }
            foreach ($counter as $uid => $count) {
                $tmp = explode('?', $uid);
                if ($count > 1) {
                    $row['tables'] = $count;
                    $row['name'] = $tmp[0];
                    $row['type'] = $tmp[0];
                    $row['argument1'] = $tmp[2] ?: '..';
                    $row['argument2'] = $tmp[2] ?: '..';
                    $tdisplay[] = $row;
                }
            }
        } else {
            foreach ($this->tableMethods[$classSelected->valueKey] ?? [] as $method) {
                $row['class'] = $classSelected->value;
                $row['table'] = $classSelected->valueKey;
                $row['name'] = $method['name'];
                $row['type'] = $method['type'];
                $row['argument1'] = $method['argument1'] ?: '..';
                $row['argument2'] = $method['argument2'] ?: '..';
                $tdisplay[] = $row;
            }
        }
        $this->cli->showLine();
        $this->cli->showTable($tdisplay, true, true, true);
        $this->cli->showLine();
    }

    protected function runApiMethodGetTables($classSelected): array
    {
        $methods = [];
        if ($classSelected->valueKey === '*') {
            $counter = [];
            foreach ($this->tablexclass as $ttable => $tclass) {
                foreach ($this->tableMethods[$ttable] ?? [] as $method) {
                    $uid = $method['name'] . '?' . $method['type'] . '?' . $method['argument1'] . $method['argument2'];
                    if (isset($counter[$uid])) {
                        $counter[$uid]++;
                    } else {
                        $counter[$uid] = 1;
                    }
                }
            }
            foreach ($counter as $uid => $count) {
                $tmp = explode('?', $uid);
                if ($count > 1) {
                    $methods[] = $tmp[0];
                }
            }
        } else {
            foreach ($this->tableMethods[$classSelected->valueKey] ?? [] as $method) {
                $methods[] = $method['name'];
            }
        }
        return $methods;
    }

    protected function runApiMethodAdd(CliOneParam $classSelected): void
    {
        while (true) {
            $this->cli->setColor(['byellow'])->showBread();
            $this->runApiMethodShowTable($classSelected);
            $typemethod = $this->cli->evalParam('typemethod', true);
            if ($typemethod->value === '') {
                // $this->cli->downLevel();
                break; // while method
            }
            $this->cli->getParameter('newmethod')->resetInput();
            $this->cli->getParameter('newmethod')->setDefault($typemethod->value);
            $newname = $this->cli->evalParam('newmethod', true);
            if ($newname->value === '*') {
                $newname->value = '';
            }
            $newname->value = trim($newname->value);
            $this->cli->getParameter('newmethod_verb')->resetInput();
            $verb = $this->cli->evalParam('newmethod_verb', true);
            $argument2 = null;
            switch ($typemethod->value) {
                case 'listfilter':
                case 'alias':
                case 'listall':
                case 'get':
                case 'listpaged':
                case 'listraw':
                    $this->cli->getParameter('newmethod_cache')->resetInput();
                    $cache = $this->cli->evalParam('newmethod_cache', true)->valueKey;
                    break;
                default:
                    $cache = '0';
                    break;
            }
            switch ($typemethod->value) {
                case 'listfilter':
                    $columnsAll = $this->pdo->columnTable($classSelected->valueKey);
                    $columns = [];
                    foreach ($columnsAll as $v) {
                        $columns[] = $v['colname'];
                    }
                    $this->cli->getParameter('newmethod_argument1')
                        ->setInput(true, 'option3', $columns)
                        ->setDescription('', 'select the column to filter');
                    $argument1 = $this->cli->evalParam('newmethod_argument1', true);
                    $this->cli->getParameter('newmethod_argument2')
                        ->setInput()
                        ->setDescription('', 'select the dependencies (ex: "/_col1,/col2"',
                            ['Example: "/_col1,/_col2/_col2b" ']);
                    $argument2 = $this->cli->evalParam('newmethod_argument2', true);
                    break;
                case 'alias':
                    $this->cli->getParameter('newmethod_argument1')
                        ->setInput()
                        ->setDescription('', 'select the name of the target method');
                    $argument1 = $this->cli->evalParam('newmethod_argument1', true);
                    break;
                case 'listall':
                case 'get':
                case 'listpaged':
                    $r = $this->pdo->getDefTableFK($classSelected->valueKey, false);
                    $showt = [];
                    foreach ($r as $k => $v) {
                        if ($v['key'] !== 'FOREIGN KEY') {
                            $showt[] = ['column' => $k, 'key' => $v['key'], 'table' => $v['reftable']];
                        }
                    }
                    $this->cli->showTable($showt, false, false, false, 4);
                    /*$columnsAll = $this->pdo->columnTable($classSelected->valueKey);
                    $this->cli->show("Columns:\n");
                    $columnFormat=[];
                    foreach ($columnsAll as $kcol) {
                        $this->cli->show("<cyan>{$kcol['colname']}</cyan> {$kcol['coltype']}\n");
                    }*/
                    //$this->cli->showValuesColumn($columnsAll,'multiple2');
                    $this->cli->getParameter('newmethod_argument1')
                        ->setInput()
                        ->setDescription('', 'Write down the dependencies (ex: "/_col1,/col2")',
                            ['Example: "/_col1,/_col2/_col2b" ']);
                    $argument1 = $this->cli->evalParam('newmethod_argument1', true);
                    break;
                case 'listraw':
                    $this->cli->getParameter('newmethod_argument1')
                        ->setInput()
                        ->setDescription('', 'select the sql query:');
                    $argument1 = $this->cli->evalParam('newmethod_argument1', true);
                    break;
                default:
                    $argument1 = null;
                    break;
            }
            if ($classSelected->valueKey === '*') {
                // add method to all classes
                foreach ($this->tablexclass as $tablename => $classname) {
                    $this->tableMethods[$tablename][] = ['name' => $newname->value, 'type' => $typemethod->value
                        , 'verb' => $verb->value
                        , 'cache' => $cache
                        , 'argument1' => $argument1 === null ? '' : $argument1->value
                        , 'argument2' => $argument2 === null ? '' : $argument2->value];
                }
            } else {
                // add method to a specific class
                $this->tableMethods[$classSelected->valueKey][] = ['name' => $newname->value, 'type' => $typemethod->value
                    , 'verb' => $verb->value
                    , 'cache' => $cache
                    , 'argument1' => $argument1 === null ? '' : $argument1->value
                    , 'argument2' => $argument2 === null ? '' : $argument2->value];
            }
            //$this->cli->downLevel();
        }
    }

    protected function runApiMethodRemove(CliOneParam $classSelected): void
    {
        while (true) {
            $methodNames = $this->runApiMethodGetTables($classSelected);
            $this->cli->setColor(['byellow'])->showBread();
            $this->runApiMethodShowTable($classSelected);
            $this->cli->getParameter('selectmethod')
                ->setInput(true, 'option', $methodNames);
            $selectmethod = $this->cli->evalParam('selectmethod', true);
            if ($selectmethod->value === '') {
                //$this->cli->downLevel();
                break; // while method
            }
            if ($classSelected->valueKey === '*') {
                // remove method from all classes
                foreach ($this->tablexclass as $tablename => $classname) {
                    foreach ($this->tableMethods[$tablename] as $k => $v) {
                        if ($v['name'] === $selectmethod->value) {
                            unset($this->tableMethods[$tablename][$k]);
                            break;
                        }
                    }
                }
            } else {
                // remove method from a specific class
                foreach ($this->tableMethods[$classSelected->valueKey] as $k => $v) {
                    if ($v['name'] === $selectmethod->value) {
                        unset($this->tableMethods[$classSelected->valueKey][$k]);
                        break;
                    }
                }
            }
            //$this->cli->downLevel();
        }
    }

    protected function runCache(): void
    {
        $this->cli->evalParam('questioncache', true);
        if ($this->cli->getValue('questioncache') === 'yes') {
            $cache_type = $this->cli->evalParam('cache_type', true);
            if ($cache_type->valueKey === 'redis' || $cache_type->valueKey === 'pdo') {
                $this->cli->evalParam('cache_server', true);
                $this->cli->evalParam('cache_port', true);
                $this->cli->evalParam('cache_user', true);
                $this->cli->evalParam('cache_password', true);
            } else {
                $this->cli->setParam('cache_server', '');
                $this->cli->setParam('cache_port', '');
                $this->cli->setParam('cache_user', '');
                $this->cli->setParam('cache_password', '');
            }
            $this->cli->evalParam('cache_schema', true);
            // testing.
            try {
                $this->pdo->cacheService =
                    new CacheOne($this->cli->getValueKey('cache_type'), $this->cli->getValue('cache_server'),
                        $this->cli->getValue('cache_schema'), $this->cli->getValue('cache_port'),
                        $this->cli->getValue('cache_user'), $this->cli->getValue('cache_password'));
                $r = $this->pdo->cacheService->setCache('abc', 'abc', 'hello', 30);
                if ($r === false) {
                    throw new RuntimeException('Unable to set testing cache');
                }
                $r = $this->pdo->cacheService->getCache('abc', 'abc');
                if ($r === false) {
                    throw new RuntimeException('Unable to get testing cache');
                }
                if ($r !== 'hello') {
                    throw new RuntimeException('Wrong value obtained by testing cache');
                }
                $this->cli->showCheck('OK', 'green', " Cache tested correctly\n");
            } catch (Exception $ex) {
                $this->cli->showCheck('ERROR', 'red', " Error in validation of cache {$ex->getMessage()}.\n");
            }
        }
    }

    protected function runCacheParam(): void
    {
        $this->cli->createParam('questioncache')
            ->setInput(true, 'optionshort', ['yes', 'no'])
            ->setCurrentAsDefault()
            ->setDescription('questioncache', 'Do you want to use cache?')
            ->add();
        $this->cli->createParam('cache_type')
            ->setInput(true, 'option', [
                'redis' => 'Redis in memory cache service',
                'apcu' => 'APCU is a PHP extension for in memory cache',
                'memcached' => 'Is a basic memory cache service',
                'pdoone' => 'It will use the database as a cache'])
            ->setDefault($this->cli->getValue('cache_type') ?: 'redis')
            ->setDescription('question type of cache', 'What type of cache do you want to use?')
            ->add();
        $this->cli->createParam('cache_server')
            ->setInput()
            ->setDefault($this->cli->getValue('cache_server') ?: '127.0.0.1')
            ->setDescription('questiondev', 'Cache server')
            ->setAllowEmpty()
            ->add();
        $this->cli->createParam('cache_port')
            ->setInput()
            ->setDefault($this->cli->getValue('cache_port') ?: '6379')
            ->setDescription('cache_port', 'Cache port')
            ->setAllowEmpty()
            ->add();
        $this->cli->createParam('cache_schema')
            ->setInput()
            ->setDefault($this->cli->getValue('cache_schema') ?: '')
            ->setAllowEmpty()
            ->setDescription('cache_schema', 'Cache schema')
            ->add();
        $this->cli->createParam('cache_user')
            ->setInput()
            ->setDefault($this->cli->getValue('cache_user') ?: '')
            ->setAllowEmpty()
            ->setDescription('cache_port', 'Cache user')
            ->add();
        $this->cli->createParam('cache_password')
            ->setInput()
            ->setDefault($this->cli->getValue('cache_password') ?: '')
            ->setAllowEmpty()
            ->setDescription('cache_password', 'Cache password')
            ->add();
    }

    protected function runCreate(): void
    {
        $first = true;
        $this->interactive = !$this->cli->evalParam('interactive')->missing;
        while (true) {
            try {
                $f = file_put_contents('dummy.html', '<h1>hello world</h1>');
                if ($f === false) {
                    throw new RuntimeException('Unable to write dummy.html file in the current folder');
                }
                @unlink('dummy.html');
                $this->cli->showCheck('OK', 'green', "Initial file permission tested correctly\n");
            } catch (Exception $ex) {
                $this->cli->showCheck('ERROR', 'red', " In the initial check of file permissions {$ex->getMessage()}. You must give r/w permission to the current folder.\n");
            }
            if (!$first) {
                // the first time we don't ask for those parameters because they are already entered.
                $this->cli->evalParam('databasetype', $this->interactive);
                $this->cli->evalParam('server', $this->interactive);
                $this->cli->evalParam('user', $this->interactive);
                $this->cli->evalParam('password', $this->interactive);
                $this->cli->evalParam('database', $this->interactive);
            } else {
                $first = false;
            }
            if (!$this->cli->getParameter('help')->missing) {
                break;
            }
            if (!$this->cli->getValue('databasetype')) {
                $this->cli->showCheck('ERROR', 'red', [
                    "Database connection error, no database selected. You can try:",
                    "<dim>" . $this->cli->reconstructPath(true, 2) . " --help</dim> for more information",
                    "<dim>" . $this->cli->reconstructPath(true, 2) . " --loadconfig <configname></dim> if you have a configuration file",
                    "<dim>" . $this->cli->reconstructPath(true, 2) . " --interactive</dim> for interactive mode"]);
                break;
            }
            $this->pdo = new PdoOne($this->cli->getValue('databasetype'), $this->cli->getValue('server'), $this->cli->getValue('user'), $this->cli->getValue('password'), $this->cli->getValue('database'));
            $this->pdo->logLevel = 2;
            try {
                $this->pdo->connect();
                $this->cli->showCheck('OK', 'green', "Database connected correctly\n");
                break;
            } catch (Exception $ex) {
                $this->cli->showMessageBox("Database connection error \n{$ex->getMessage()}", "<red>ERROR</red>", true);
                //$this->cli->showCheck('ERROR', 'red', "Database connection error {$ex->getMessage()}");
            }
            $try = $this->cli->evalParam('try');
            if ($try->value === 'no') {
                break;
            }
        } // while
    }

    protected function runCreatePostmanParam(): void
    {
        $this->cli->createParam('filepostman', [], 'onlyinput')
            ->setDefault('')
            ->setInput()
            ->setDescription('The filename (without extension) of the definition of postman',
                'Select the filename of the postman definition (empty for return)')->add();
    }

    protected function runCreatePostman(): void
    {
        $file = $this->cli->evalParam('filepostman', true);
        if ($file === '') {
            return;
        }
        $json = [
            'info' => [
                '_postman_id' => 'b86a0fa9-d72c-41e3-a9a1-1596f9c9834e',
                'name' => 'api ' . $this->cli->getValue('server'),
                'schema' => 'https://schema.getpostman.com/json/collection/v2.1.0/collection.json',
                '_exporter_id' => '13134001',
            ],
            'items' => []
        ];
        foreach ($this->tableMethods as $table => $methods) {
            if ($methods !== null && isset($this->tablexclass[$table]) && count($methods) > 0) {
                foreach ($methods as $method) {
                    $classApi = $this->tablexclass[$table];
                    if ($this->cli->getValue('questiondev') === 'dev') {
                        $fullurl = $this->cli->getValue('baseurl_dev');
                    } else {
                        $fullurl = $this->cli->getValue('baseurl_prod');
                    }
                    $fullurl .= '/' . $this->cli->getValue('folderapi') . '/' . $classApi . '/' . $method['name'];
                    $protocol = strpos($fullurl, 'https:') === 0;
                    $parts = explode('/', $fullurl);
                    //var_dump($parts);
                    $host = $parts[2];
                    array_splice($parts, 0, 3);
                    $objs = [
                        'name' => $table . ' ' . $method['name'] . ' (' . $method['verb'] . ')',
                        'request' => [
                            'method' => $method['verb'] === 'ALL' ? 'GET' : $method['verb'],
                            'header' => [
                            ],
                            'url' => [
                                'raw' => $fullurl,
                                'protocol' => $protocol ? 'https' : 'http',
                                'host' => [
                                    0 => $host,
                                ],
                                'path' => $parts,
                            ],
                        ],
                        'response' => [
                        ],
                    ];
                    $json['items'][] = $objs;
                }
            }
        }
        //$this->cli->showLine(str_repeat('-',$this->cli->getColSize()));
        try {
            $file = $this->cli->addExtensionFile($file->value, '.json');
            $r = file_put_contents($file, json_encode($json, JSON_PRETTY_PRINT));
            if ($r === false) {
                throw new RuntimeException('Unable to save file ' . $file);
            }
            $this->cli->showCheck('OK', 'green', " File <bold>$file</bold> generated correctly\n");
        } catch (Exception $ex) {
            $this->cli->showCheck('ERROR', 'red', " Error in creation $file {$ex->getMessage()}.\n");
        }
        //echo json_encode($json,JSON_PRETTY_PRINT);
        //$this->cli->showLine(str_repeat('-',$this->cli->getColSize()));
    }

    /**
     * It creates the folders, create the repo classes and create the api controllers.
     * @return void
     */
    protected function runCreateClasses(): void
    {
        $this->cli->createParam('override', [], 'none')
            ->setInput(true, 'optionshort', ['yes', 'no'])
            ->setDescription('', 'Do you want to override the files already generated (if any)?')
            ->setDefault(null)
            ->add(true);
        while (true) {
            $override = $this->cli->evalParam('override', true);
            if ($override->value === 'yes' || $override->value === 'no') {
                break;
            }
        }
        $errors = [];
        $this->cli->showLine("\nErrors found:\n");
        $errors[] = $this->createFolder($this->cli->getValue('classdirectory'));
        $errors[] = $this->createFolder($this->cli->getValue('folderapi'));
        foreach ($errors as $v) {
            $v = str_replace('skipped', '<yellow>skipped</yellow>', $v);
            $this->cli->showLine("Folder: " . $v);
        }
        $tmpTableXClass = [];
        foreach ($this->tablexclass as $k => $v) {
            $tmpTableXClass[$k] = $v . $this->cli->getValue('classpostfix');
        }
        $this->pdo->generateCodeClassConversions($this->conversion);
        $errors = $this->pdo->generateAllClasses(
            $tmpTableXClass
            , 'Base' . ucfirst(str_replace('-', '', $this->cli->getValue('database')))
            , $this->cli->getValue('classnamespace')
            , $this->cli->getValue('classdirectory')
            , $override->value === 'yes'
            , $this->columnsTable
            , $this->extracolumn
            , $this->removecolumn
            , $this->columnsAlias
        );
        foreach ($errors as $v) {
            $v = str_replace('skipped', '<yellow>skipped</yellow>', $v);
            $this->cli->showLine("Repository Class: " . $v);
        }
        $errors = $this->createAllAPI($this->cli->getValue('folderapi'), $override->value === 'yes');
        foreach ($errors as $v) {
            $this->cli->showLine("Api Class: " . $v);
        }
    }

    protected function runEncryption(): void
    {
        if (function_exists('openssl_get_cipher_methods')) {
            $arr = openssl_get_cipher_methods();
        } else {
            $this->cli->showCheck('ERROR', 'red', " openssl_get_cipher_methods not defined.\n");
            $arr = ['aes-128-ctr', 'aes-256-ctr'];
        }
        $key = array_search($this->cli->getValue('encryptionmethod'), $arr, true);
        $key2 = array_search('aes-256-ctr', $arr, true);
        $this->cli->getParameter('encryptionmethod')
            ->setInput(true, 'option3', $arr)
            ->setDefault($key !== false ? $key + 1 : $key2 + 1);
        $arr = hash_algos();
        $key = array_search($this->cli->getValue('hashmethod'), $arr, true);
        $key2 = array_search('sha256', $arr, true);
        $this->cli->getParameter('hashmethod')
            ->setInput(true, 'option3', $arr)
            ->setDefault($key !== false ? $key + 1 : $key2 + 1);
        $this->cli->evalParam('questionencryption', true);
        if ($this->cli->getValue('questionencryption') === 'yes') {
            $this->cli->evalParam('encryptionpassword', true);
            $this->cli->evalParam('encryptionsalt', true);
            $this->cli->evalParam('encryptioniv', true);
            $this->cli->evalParam('encryptionmethod', true);
            $this->cli->evalParam('hashmethod', true);
            try {
                $this->pdo->setEncryption($this->cli->getValue('encryptionpassword'),
                    $this->cli->getValue('encryptionsalt'),
                    $this->cli->getValue('encryptionmethod'));
                $this->pdo->encryption->iv = $this->cli->getValue('encryptioniv') === 'yes';
                $this->pdo->encryption->hashType = $this->cli->getValue('hashmethod');
                $result = $this->pdo->encryption->encrypt('hello world');
                if ($result === null || $result === 'hello world') {
                    throw new RuntimeException('Unable to encrypt value');
                }
                $result = $this->pdo->hash('hello world');
                if ($result === '' || $result === 'hello world') {
                    throw new RuntimeException('Unable to hash value');
                }
                $this->cli->showCheck('OK', 'green', " Encryption tested correctly\n");
            } catch (Exception $ex) {
                $this->cli->showCheck('ERROR', 'red', " Error in Encryption test {$ex->getMessage()}.\n");
            }
        }
    }

    protected function runEncryptionParam(): void
    {
        $this->cli->createParam('questionencryption')
            ->setInput(true, 'optionshort', ['yes', 'no'])
            ->setDefault($this->cli->getValue('questionencryption') ?: 'yes')
            ->setDescription('questionencryption', 'Do you want to use encryption?')
            ->add();
        //
        $this->cli->createParam('encryptionpassword')
            ->setInput()
            ->setDefault($this->cli->getValue('encryptionpassword') ?: '')
            ->setAllowEmpty()
            ->setDescription('The password used for encryption', 'Set the password')
            ->add();
        $this->cli->createParam('encryptionsalt')
            ->setInput()
            ->setDefault($this->cli->getValue('encryptionsalt') ?: '')
            ->setAllowEmpty()
            ->setDescription('The SALT used for encryption', 'Set the salt')
            ->add();
        $this->cli->createParam('encryptioniv')
            ->setInput(true, 'optionshort', ['yes', 'no'])
            ->setDefault($this->cli->getValue('encryptioniv') ?: 'yes')
            ->setAllowEmpty()
            ->setDescription('If you want an initialization vector (IV)', 'Set the IV')
            ->add();
        $this->cli->createParam('encryptionmethod')
            ->setInput(true, 'option3', [])
            ->setDescription('The encryption method. This method could be single way or two way', 'Select the encryption method')
            ->add();
        $this->cli->createParam('hashmethod')
            ->setDescription('The method used to generate an hash.', 'Select the hash method')
            ->add();
    }

    protected function runEnd(): void
    {
        if ($this->cli->getValue('questiondev') === 'dev') {
            $url = $this->cli->getValue('baseurl_dev') . '/router.php';
        } else {
            $url = $this->cli->getValue('baseurl_prod') . '/router.php';
        }
        $this->cli->showLine("You can start here: <underline>$url</underline>");
        $this->cli->showLine("Done.");
    }

    protected function runNameSpaceFolders(): void
    {
        $cur = getcwd();
        $defaultvendor = self::findVendorPath($cur);
        $this->cli->getParameter('composerpath')
            ->setDefault($this->cli->getValue('composerpath') ?: $defaultvendor);
        //$this->cli->getParameter('namespace')
        //    ->setDefault($this->cli->getValue('namespace') ?: 'folder\folder2');
        while (true) {
            $this->cli->showCheck('INFO', 'yellow', " The current path is " . getcwd() . "\n");
            $this->cli->evalParam('composerpath', true);
            $this->cli->getParameter('composerpath')
                ->setValue(trim($this->cli->getValue('composerpath'), " \t\n\r\0\x0B/"));
            $composerpath = $this->cli->getValue('composerpath') . '/autoload.php';
            if (@file_exists($composerpath)) {
                $this->cli->showCheck('OK', 'green', " Composer autoload.php found\n");
                break;
            }
            $this->cli->showCheck('ERROR', 'red', " Unable to read file : $composerpath\n");
            $try = $this->cli->evalParam('try', true);
            if ($try->value === 'no') {
                break;
            }
        }
        $file = '';
        try {
            $file = $this->cli->getValue('composerpath') . '/../composer.json';
            $composerjson = @file_get_contents($file);
            if ($composerjson === false) {
                throw new RuntimeException('Unable to open file');
            }
            $jsonArr = @json_decode($composerjson, true);
            $this->cli->showLine("composer.json:");
            if (isset($jsonArr['autoload']['psr-4'])) {
                $this->cli->showLine("<magenta>autoload: " .
                    json_encode(@$jsonArr['autoload']['psr-4'], JSON_PRETTY_PRINT) . "</magenta>");
            }
            if (isset($jsonArr['autoload-dev']['psr-4'])) {
                $this->cli->showLine("<magenta>autoload-dev: " .
                    json_encode(@$jsonArr['autoload-dev']['psr-4'], JSON_PRETTY_PRINT) . "</magenta>");
            }
        } catch (Exception $ex) {
            $this->cli->showCheck('ERROR', 'red', " Unable to read file : $file " . $ex->getMessage() . "\n");
        }
        //$this->cli->evalParam('classdirectory',true);
        $this->cli->evalParam('folderapi', true);
        //$this->cli->getParameter('namespace')
        //    ->setInput(true)
        //    ->setDescription('the namespace of the base namespace', 'Select the base namespace corresponding to the current folder');
        while (true) {
            $this->cli->showCheck('INFO', 'yellow', " The api path is " . getcwd() . '/' . $this->cli->getValue('folderapi') . "\n");
            $this->cli->evalParam('namespaceapi', true);
            // dummy class
            $content = "<?php
                namespace " . $this->cli->getValue('namespaceapi') . ";
                class DummyClass {
                
                }
                ";
            try {
                /** @noinspection MkdirRaceConditionInspection */
                @mkdir(getcwd() . '/' . $this->cli->getValue('folderapi'));
                $r = @file_put_contents($this->cli->getValue('folderapi') . '/DummyClass.php', $content);
                if ($r === false) {
                    throw new RuntimeException('Unable to create DummyClass.php');
                }
            } catch (Exception $ex) {
                $this->cli->showCheck('ERROR', 'red', " Error in creation DummyClass.php {$ex->getMessage()}.\n");
            }
            $ok = class_exists($this->cli->getValue('namespaceapi') . '\DummyClass');
            if ($ok) {
                $this->cli->showCheck('OK', 'green', " Class called correctly\n");
                break;
            }
            $this->cli->showCheck('ERROR', 'red', " Unable to call test class. Remember you must edit composer.json and dump the autoload\n");
            $try = $this->cli->evalParam('try', true);
            if ($try->value === 'no') {
                break;
            }
        }
        @unlink('DummyClass.php');
    }

    protected function runNamespaceParameter(): void
    {
        /*$this->cli->createParam('classdirectory')
            ->setDefault('repo')
            ->setInput()
            ->add();
        */
        $this->cli->createParam('folderapi')
            ->setInput()
            ->setDefault('api')
            ->setDescription('the folder of the api class', 'Select the api folder (relative)')
            ->add();
        $this->cli->createParam('composerpath')
            ->setInput()
            ->setDescription('the namespace of the base namespace', 'Select the composer (vendor) relative path')
            ->add();
        $this->cli->createParam('namespaceapi')
            ->setInput()
            ->setDescription('the namespace of the api', 'Select the namespace')
            ->setCurrentAsDefault()
            ->add();
        /*$this->cli->createParam('classdirectory')
            ->setInput(true)
            ->setDescription('the folder of the repository class', 'Select the repository folder (relative)')
            ->add();*/
        /*$this->cli->createParam('tables')
            ->setInput(true)
            ->add();*/
        /*$this->cli->createParam('folderapi')
            ->setInput()
            ->setDescription('the folder of the api class', 'Select the api folder (relative)')
            ->add();*/
    }

    protected function runRoute(): void
    {
        $this->cli->upLevel('Router');
        $this->cli->setColor(['byellow'])->showBread();
        $this->cli->setAlign('left', 'left')->showMessageBox([
            "Information:"
            , "The router is who enroutes the call from the user to each controller."
            , "It is also in charge to store the configurations."
            , "It store the information of a developer machine or production machine"
            , "This library uses the name of the machine to determine the ambiance"
        ], $this->cli->makeBigWords('?'), true);
        $this->cli->evalParam('questionroute');
        if ($this->cli->getValue('questionroute') === 'yes') {
            $this->cli->evalParam('questiondev', true);
            $this->cli->evalParam('machineid', true);
            $this->cli->showCheck('INFO', 'yellow', " The current path is " . getcwd() . "\n");
            while (true) {
                $this->cli->getParameter('baseurl_dev')->setDefault($this->cli->getValue('baseurl_dev') ?: 'http://localhost');
                $this->cli->getParameter('baseurl_prod')->setDefault($this->cli->getValue('baseurl_prod') ?: 'http://localhost');
                $this->cli->evalParam('baseurl_dev', true);
                $this->cli->evalParam('baseurl_prod', true);
                $urltest = $this->cli->getValue('questiondev') === 'dev' ? $this->cli->getValue('baseurl_dev') : $this->cli->getValue('baseurl_prod');
                // testing url
                try {
                    $f = file_put_contents('dummy.html', '<h1>hello world</h1>');
                    if ($f === false) {
                        throw new RuntimeException('Unable to write dummy.html file in the current folder');
                    }
                    $r = @file_get_contents($urltest . '/dummy.html');
                    if ($r !== '<h1>hello world</h1>') {
                        throw new RuntimeException('Unable to read url :' . $urltest . '/dummy.html');
                    }
                    $this->cli->showCheck('OK', 'green', " Url tested correctly\n");
                    @unlink('dummy.html');
                    break; // whie
                } catch (Exception $ex) {
                    $this->cli->showCheck('ERROR', 'red', " Error in testing url:{$ex->getMessage()}\n");
                    $try = $this->cli->evalParam('try', true);
                    if ($try->value === 'no') {
                        break; // break loop
                    }
                }
            } // loop
            $this->cli->evalParam('questionhtaccess');
            if ($this->cli->getValue('questionhtaccess') === 'yes') {
                try {
                    $filename = "/templates/Template_htaccess.php";
                    $templateContent = @file_get_contents(__DIR__ . $filename);
                    if ($templateContent === false) {
                        throw new RuntimeException("Unable to read template file $filename");
                    }
                    // we delete and replace the first line.
                    $templateContent = substr($templateContent, strpos($templateContent, "\n") + 1);
                    $r = PdoOne::saveFile('.htaccess', $templateContent);
                    if (!$r) {
                        throw new RuntimeException('Unable to create .htaccess file');
                    }
                    $this->cli->showCheck('OK', 'green', " .htaccess created\n");
                } catch (Exception $ex) {
                    $this->cli->showCheck('ERROR', 'red', " Error in creating .htaccess:{$ex->getMessage()}\n");
                }
            }
            $this->cli->evalParam('templateurl');
        }
        if ($this->cli->getValue('questionroute') === 'yes') {
            $this->createRouter('yes');
            //$this->cli->showCheck('INFO', 'yellow', "The router file will be created during <bold>generate api</bold>");
        }
        $this->cli->downLevel();
    }

    protected function runRouteParam(): void
    {
        $this->cli->createParam('questionroute')
            ->setInput(true, 'optionshort', ['yes', 'no'])
            ->setDefault($this->cli->getValue('questionroute') ?: 'yes')
            ->setDescription('questionroute', 'Do you want to generate the route file?')
            ->add();
        $this->cli->createParam('questionhtaccess')
            ->setInput(true, 'optionshort', ['yes', 'no'])
            ->setDefault($this->cli->getValue('questionhtaccess') ?: 'yes')
            ->setDescription('questionhtaccess', 'Do you want to generate the .htaccess file?')
            ->add();
        $this->cli->createParam('questiondev')
            ->setInput(true, 'optionshort', ['dev', 'prod'])
            ->setDefault($this->cli->getValue('questiondev') ?: 'dev')
            ->setDescription('Is the current machine the developer or production machine?', 'Is it a developer or production machine?', ['Indicates if the current machine is the developer or production machine', 'It is used together with the name of machine'])
            ->add();
        $this->cli->createParam('machineid')
            ->setInput()
            ->setDefault($this->cli->getValue('machineid') ?: gethostname())
            ->setAllowEmpty()
            ->setDescription('machineid', 'Set your machine name')
            ->add();
        $this->cli->createParam('baseurl_dev')
            ->setInput()
            ->setDefault($this->cli->getValue('baseurl_dev') ?: 'http://localhost')
            ->setDescription('The base URL of the developer folder', 'Select the base url of the current folder (dev)')
            ->add();
        $this->cli->createParam('baseurl_prod')
            ->setInput()
            ->setDefault($this->cli->getValue('baseurl_prod') ?: 'http://localhost')
            ->setDescription('The base URL of the production folder', 'Select the base url of the current folder (prod)')
            ->add();
        $this->cli->createParam('templateurl')
            ->setInput()
            ->setDefault($this->cli->getValue('templateurl') ?: '')
            ->setAllowEmpty()
            ->setDescription('templateurl', 'Select the folder url (empty if you are not sure)')
            ->add();
    }

    protected function runSaveFile(): void
    {
        $this->cli->showLine();
        // save file
        $this->cli->createParam('trysave')
            ->setDefault('yes')
            ->setDescription('', 'Do you want to save the configuration? Note: it includes the database password')
            ->setInput(true, 'optionshort', ['yes', 'no'])->add(true);
        $try = $this->cli->evalParam('trysave');
        if ($try->value === 'yes') {
            $defConfig = $this->cli->getValue('filename') ?? $this->cli->getValue('loadconfig');
            $this->cli->createParam('filename')
                ->setDefault($defConfig)
                ->setDescription('', 'select the filename')
                ->setInput()->add(true);
            $this->saveAllConfig();
        }
    }

    protected function runSaveScript(): void
    {
        $this->cli->showLine();
        // save file
        $this->cli->evalParam('trysavescript', true);
        if ($this->cli->getValue('trysavescript') === 'yes') {
            $this->cli->evalParam('filenamescript', true);
            $file1 = $this->cli->getValue('filenamescript') . '.bat';
            $content = "@php " . $_SERVER['SCRIPT_NAME'] . " createapi --loadconfig " . $this->cli->getValue('filename') . "  %1% %2% %3%\r\n";
            try {
                $f = @PdoOne::saveFile($file1, $content);
                if ($f === false) {
                    throw new RuntimeException('Unable to save content');
                }
                $this->cli->showCheck('OK', 'green', " File [$file1] saved correctly");
            } catch (Exception $ex) {
                $this->cli->showCheck('ERROR', 'red', " Unable to save $file1 file: {$ex->getMessage()}");
            }
            $file1 = $this->cli->getValue('filenamescript') . '.sh';
            $content = "php " . $_SERVER['SCRIPT_NAME'] . " createapi --loadconfig " . $this->cli->getValue('filename') . " \${flag} \${OPTARG} \n";
            try {
                $f = @PdoOne::saveFile($file1, $content);
                if ($f === false) {
                    throw new RuntimeException('Unable to save content');
                }
                $this->cli->showCheck('OK', 'green', " File $file1 saved correctly");
            } catch (Exception $ex) {
                $this->cli->showCheck('ERROR', 'red', " Unable to save $file1 file: {$ex->getMessage()}");
            }
            if (PHP_OS_FAMILY === 'Linux') {
                $r = @chmod($file1, 0740);
                if ($r === false) {
                    $this->cli->showCheck('ERROR', 'red', " Unable to change execution permission to file $file1");
                }
            }
            $this->saveAllConfig();
        }
    }

    protected function runSaveScriptParam(): void
    {
        $this->cli->createParam('trysavescript')
            ->setDefault($this->cli->getValue('trysavescript') ?: 'yes')
            ->setDescription('', 'Do you want to save the shell script?')
            ->setInput(true, 'optionshort', ['yes', 'no'])->add();
        $this->cli->createParam('filenamescript')
            ->setDefault($this->cli->getValue('filenamescript'))
            ->setCurrentAsDefault()
            ->setDescription('', 'select the filename (without extension)')
            ->setInput()->add();
    }

    protected function saveAllConfig(): void
    {
        $filename = $this->cli->evalParam('filename');
        $content = $this->createContent();
        $contentData = "<?php http_response_code(404); die(1); // it is a configuration file. This line prevents to display it online ?>\n";
        $contentData .= json_encode($content, JSON_PRETTY_PRINT);
        try {
            $f = @file_put_contents($filename->value . '.config.php', $contentData);
            if ($f === false) {
                throw new RuntimeException('Unable to save file');
            }
            $this->cli->showCheck('OK', 'green', " Configuration [$filename->value.config.php] saved correctly");
        } catch (Exception $ex) {
            $this->cli->showCheck('ERROR', 'red', " Unable to save configuration file: {$ex->getMessage()}");
        }
    }

    private function version(): string
    {
        $date = new DateTime('now');
        return self::VERSION . ' (' . $date->format('Y-m-d\TH:i:s\Z') . ')';
    }
}




