<?php
/**
 * Created by PhpStorm.
 * User: tim
 * Date: 01/02/16
 * Time: 8:36 PM
 */

namespace TextDb\Test;

use TextDb\tdb;

class tdbDatabaseTest extends \PHPUnit_Framework_TestCase
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
            mkdir($this->tmpNotWritableFolder);
        }

        $this->dbName = uniqid();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }


    public function testCreateAndRemoveDatabase()
    {
        $this->tdb->createDatabase($this->tmpFolder, $this->dbName);
        $this->assertEquals(true, file_exists($this->tmpFolder ."/$this->dbName.tdb"));

        $this->tdb->removeDatabase();
        $this->assertEquals(false, file_exists($this->tmpFolder ."/$this->dbName.tdb"));
    }

    public function testCreateDatabaseAlreadyExists()
    {
        $this->setExpectedException('\TextDb\Exception\DatabaseExistsException');

        $this->tdb->createDatabase($this->tmpFolder, $this->dbName);
        $this->tdb->createDatabase($this->tmpFolder, $this->dbName);
    }

    public function testCreateDatabaseNotWritable()
    {
        $this->setExpectedException('\TextDb\Exception\NotWritableException');

        $this->tdb->createDatabase($this->tmpNotWritableFolder, $this->dbName);
    }

    public function testCreateDatabaseInvalidDirectory()
    {
        $this->setExpectedException('\TextDb\Exception\InvalidDirectoryException');

        $this->tdb->createDatabase($this->tmpFolder ."/path/doesnt/exist", $this->dbName);
    }

    public function testRemoveDatabaseWithTables()
    {
        $this->tdb->createDatabase($this->tmpFolder, $this->dbName);

        $this->tdb->createTable("test_table", [
            [
                "id",
                "id",
            ],
            [
                "name",
                "string",
                50
            ]
        ]);

        $this->assertEquals(true, file_exists($this->tmpFolder . "/" . $this->dbName . "_test_table.ta"));
        $this->assertEquals(true, file_exists($this->tmpFolder . "/" . $this->dbName . "_test_table.memo"));
        $this->assertEquals(true, file_exists($this->tmpFolder . "/" . $this->dbName . "_test_table.ref"));

        $this->tdb->removeDatabase();

        $this->assertEquals(false, file_exists($this->tmpFolder . "/" . $this->dbName . "_test_table.ta"));
        $this->assertEquals(false, file_exists($this->tmpFolder . "/" . $this->dbName . "_test_table.memo"));
        $this->assertEquals(false, file_exists($this->tmpFolder . "/" . $this->dbName . "_test_table.ref"));
    }

    public function testRemoveNonExistingTable()
    {
        $this->setExpectedException('\TextDb\Exception\InvalidTableException');

        $this->tdb->createDatabase($this->tmpFolder, $this->dbName);
        $this->tdb->removeTable("invalid_table");
        $this->tdb->removeDatabase();
    }
}
