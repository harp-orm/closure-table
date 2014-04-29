<?php

namespace CL\LunaClosuretable\Test;

use CL\Atlas\DB;

use PHPUnit_Framework_TestCase;

abstract class AbstractTestCase extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();

        DB::setConfig('default', array(
            'dsn' => 'mysql:dbname=test-luna-closuretable;host=127.0.0.1',
            'username' => 'root',
        ));

        DB::get()->beginTransaction();
    }

    public function tearDown()
    {
        DB::get()->rollback();

        parent::tearDown();
    }
}
