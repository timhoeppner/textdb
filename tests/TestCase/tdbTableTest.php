<?php
/**
 * Created by PhpStorm.
 * User: tim
 * Date: 01/02/16
 * Time: 8:36 PM
 */

namespace TextDb\Test;

use TextDb\tdb;

class tdbFieldTest extends \PHPUnit_Framework_TestCase
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
        $this->tdb->createTable("test", [["id", "id"], ["name", "string", 50]]);
        $this->tdb->setFp("test", "test");
    }

    protected function tearDown()
    {
        $this->tdb->tdb($this->tmpFolder, $this->dbName);
        $this->tdb->removeDatabase();

        parent::tearDown();
    }

    public function testAddFieldAlreadyExists()
    {
        $this->setExpectedException('TextDb\Exception\InvalidArgumentException');

        $this->tdb->addField("test", ["name"]);
    }

    public function testAddFieldMissingType()
    {
        $this->setExpectedException('TextDb\Exception\InvalidArgumentException');

        $this->tdb->addField("test", ["email"]);
    }

    public function testAddFieldInvalidType()
    {
        $this->setExpectedException('TextDb\Exception\InvalidArgumentException');

        $this->tdb->addField("test", ["email", "blob"]);
    }

    public function testAddFieldMemo()
    {
        $this->tdb->addField("test", ["email", "memo"]);

        $expectedField = [
            "fName" => "email",
            "fType" => "memo",
            "fLength" => 7
        ];
        $fieldList = $this->tdb->getFieldList("test");
        $this->assertEquals(true, in_array($expectedField, $fieldList));
    }

    public function testAddFieldIdWithRecords()
    {
        // TODO this test is highlighting a bug when adding an id field
        $this->markTestSkipped();

        $this->tdb->add("test", ["name" => "tim"]);

        $this->tdb->addField("test", ["id2", "id"]);

        $expectedField = [
            "fName" => "id2",
            "fType" => "id",
            "fLength" => 7
        ];
        $fieldList = $this->tdb->getFieldList("test");
        $this->assertEquals(true, in_array($expectedField, $fieldList));

        $record = $this->tdb->get("test", 1);

        $this->assertEquals(1, $record[0]["id2"]);
    }

    public function testAddFieldStringWithRecords()
    {
        $this->tdb->add("test", ["name" => "tim"]);

        $this->tdb->addField("test", ["email", "string", 50]);

        $expectedField = [
            "fName" => "email",
            "fType" => "string",
            "fLength" => 50
        ];
        $fieldList = $this->tdb->getFieldList("test");
        $this->assertEquals(true, in_array($expectedField, $fieldList));

        $record = $this->tdb->get("test", 1);

        $this->assertEquals("", $record[0]["email"]);
    }

    public function testEditFieldMissing()
    {
        $this->setExpectedException('TextDb\Exception\InvalidArgumentException');

        $this->tdb->editField("test", "invalidfield", ["email", "string", 50]);
    }

    public function testEditFieldInvalidType()
    {
        $this->setExpectedException('TextDb\Exception\InvalidArgumentException');

        $this->tdb->editField("test", "name", ["email", "blob", 50]);
    }

    public function testEditFieldToStringWithRecords()
    {
        // TODO possible other bug found here
        $this->markTestSkipped();

        $this->tdb->add("test", ["name" => "tim"]);

        $this->tdb->editField("test", "name", ["email", "string", 50]);

        $record = $this->tdb->get("test", 1);

        // TODO check record has new field and old one is removed
    }

    public function testRemoveFieldMissing()
    {
        $this->setExpectedException('TextDb\Exception\InvalidArgumentException');

        $this->tdb->removeField("test", "invalidfield");
    }
}
