<?php
/**
 * Created by PhpStorm.
 * User: tim
 * Date: 01/02/16
 * Time: 8:36 PM
 */

namespace TextDb\Test;

use TextDb\tdb;

class tdbRecordTest extends \PHPUnit_Framework_TestCase
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
        $this->tdb->createTable("test", [
            ["id", "id"],
            ["name", "string", 50],
            ["notes", "memo"],
            ["favorite_number", "number", 10]
        ]);
        $this->tdb->setFp("test", "test");

        $this->tdb->add("test", [
            "name"      => "tim",
            "notes"     => "some blob of data",
            "favorite_number" => 16
        ]);

        $this->tdb->add("test", [
            "name"      => "bob",
            "notes"     => "i need milk",
            "favorite_number" => 8
        ]);

        $this->tdb->add("test", [
            "name"      => "sally",
            "notes"     => "suits is a great tv series",
            "favorite_number" => 21
        ]);

    }

    protected function tearDown()
    {
        $this->tdb->tdb($this->tmpFolder, $this->dbName);
        $this->tdb->removeDatabase();

        parent::tearDown();
    }

    public function testEditInvalidId()
    {
        $this->setExpectedException('TextDb\Exception\InvalidArgumentException');

        $this->tdb->edit("test", 1000, ["name" => "david"]);
    }

    public function testEditString()
    {
        $record = $this->tdb->get("test", 1);

        $this->assertEquals(1, $record[0]["id"]);
        $this->assertEquals("tim", $record[0]["name"]);

        $this->tdb->edit("test", 1, ["name" => "david"]);

        $record = $this->tdb->get("test", 1);

        $this->assertEquals(1, $record[0]["id"]);
        $this->assertEquals("david", $record[0]["name"]);
    }

    public function testEditMemo()
    {
        $record = $this->tdb->get("test", 1);

        $this->assertEquals(1, $record[0]["id"]);
        $this->assertEquals("some blob of data", $record[0]["notes"]);

        $this->tdb->edit("test", 1, ["notes" => "php will never die"]);

        $record = $this->tdb->get("test", 1);

        $this->assertEquals(1, $record[0]["id"]);
        $this->assertEquals("php will never die", $record[0]["notes"]);
    }

    public function testEditNumber()
    {
        $record = $this->tdb->get("test", 1);

        $this->assertEquals(1, $record[0]["id"]);
        $this->assertEquals(16, $record[0]["favorite_number"]);

        $this->tdb->edit("test", 1, ["favorite_number" => 116]);

        $record = $this->tdb->get("test", 1);

        $this->assertEquals(1, $record[0]["id"]);
        $this->assertEquals(116, $record[0]["favorite_number"]);
    }

    public function testDeleteInvalidId()
    {
        $this->setExpectedException('TextDb\Exception\InvalidArgumentException');

        $this->tdb->delete("test", 1000);
    }

    public function testDelete()
    {
        $this->assertEquals([[
            "id"        => "1",
            "name"      => "tim",
            "notes"     => "some blob of data",
            "favorite_number" => "16"
        ]], $this->tdb->get("test", 1));

        $this->tdb->delete("test", 1);

        // TODO get should return empty array if no records were found
        $this->assertEquals(false, $this->tdb->get("test", 1));
    }
}
