<?php

namespace eftec\tests;

use eftec\apiassembler\ApiAssembler;
use eftec\CliOne\CliOne;
use eftec\PdoOneCli;
use Exception;
use PHPUnit\Framework\TestCase;

class ApiAssember_Test extends TestCase
{
    public function setUp():void {

        chdir(__DIR__.'/tmp');
    }
    /**
     * @return void
     * @throws Exception
     */
    public function test1(): void
    {
        //PS > php ..\..\src\apiassembler2.php
        CliOne::testArguments(['cmd.exe','createapi',
            '-i',
            '--databasetype',
            'mysql',
            '--server',
            '127.0.0.1',
            '--user',
            'root',
            '--password',
            'abc.123',
            '',
            '--database',
            'sakila',
            '--namespace',
            'examples\localhost']);

        $api=new ApiAssembler();
        CliOne::testUserInput(['folder',
            'repo2',
            'eftec\\tests\\tmp\\repo2',
            'apifolder',
            '',
            'eftec\\tests\\tmp',
            'api',
            'addmethod',
            '*',
            '6',
            '',
            '',
            '',
            '', // regresa menu
            'router',
            'yes',
            'dev',
            '',
            'http://localhost/currentproject/ApiAssembler/tests/tmp/',
            'http://localhost/currentproject/ApiAssembler/tests/tmp/',
            'yes',
            '',
            'generateapi', // menu back
            'yes',
            'yes',
            'p20',
            'yes',
            'p20s'
            ]);
        $api->cliEngine();
        $this->assertEquals(true,true);

    }


}
