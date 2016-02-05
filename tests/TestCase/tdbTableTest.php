<?php
/**
 * Created by PhpStorm.
 * User: tim
 * Date: 01/02/16
 * Time: 8:36 PM
 */

namespace TextDb\Test;

use TextDb\tdb;

class tdbTableTest extends \PHPUnit_Framework_TestCase
{
    /** @var tdb */
    public $tdb;

    /**
     * @var string
     */
    public $tmpFolder = "./tmp";

    /** @var string */
    public $tmpNotWritableFolder;

    /** @var string */
    public $dbName;

    protected function setUp()
    {
        parent::setUp();

        $this->tdb = new tdb();

        // Create the tmp directory if it doesn't exist
        if(!file_exists($this->tmpFolder)) {
            mkdir($this->tmpFolder);
        }

        $this->tmpNotWritableFolder = $this->tmpFolder ."/notWritable";
        if(!file_exists($this->tmpNotWritableFolder)) {
            mkdir($this->tmpNotWritableFolder, 0444);
        }

        $this->dbName = uniqid();

        $this->tdb->createDatabase($this->tmpFolder, $this->dbName);
    }

    protected function tearDown()
    {
        $this->tdb->tdb($this->tmpFolder, $this->dbName);
        $this->tdb->removeDatabase();

        parent::tearDown();
    }

    public function testGetNumberOfRecords()
    {
        $this->tdb->createTable("test", [["id", "id"], ["name", "string", 50]]);
        $this->tdb->setFp("test", "test");

        $this->assertEquals(0, $this->tdb->getNumberOfRecords("test"));

        $this->tdb->add("test", ["name" => "tim"]);

        $this->assertEquals(1, $this->tdb->getNumberOfRecords("test"));

        $this->tdb->add("test", ["name" => "sally"]);
        $this->tdb->add("test", ["name" => "joe"]);
        $this->tdb->add("test", ["name" => "martha"]);

        $this->assertEquals(4, $this->tdb->getNumberOfRecords("test"));

        $this->tdb->delete("test", 2);

        $this->assertEquals(3, $this->tdb->getNumberOfRecords("test"));
    }
}
