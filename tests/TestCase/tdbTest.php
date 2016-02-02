<?php
/**
 * Created by PhpStorm.
 * User: tim
 * Date: 01/02/16
 * Time: 8:36 PM
 */

namespace TextDb\Test;

use TextDb\tdb;

class tdbTest extends \PHPUnit_Framework_TestCase
{
    // TODO setup function should create temporary directories

    public function testCreateDatabase()
    {
        $tdb = new tdb();

        $tdb->createDatabase("./tmp/", "test");
        $this->assertEquals(true, file_exists("./tmp/test.tdb"));

        $tdb->removeDatabase();
        $this->assertEquals(false, file_exists("./tmp/test.tdb"));
    }
}
