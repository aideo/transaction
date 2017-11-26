<?php

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Ideo\Transaction\DoctrineDBALTransactionManager;

class DoctrineDBALTransactionManagerTest extends TestCase
{

    /**
     * @var Connection
     */
    private $conn;

    public function setUp()
    {
        parent::setUp();

        $this->conn = DriverManager::getConnection(['url' => 'sqlite:///:memory:']);
        $this->conn->exec('CREATE TABLE SAMPLE ( id INTEGER NOT NULL, name VARCHAR(50) NOT NULL )');

        $this->createRow(1, 'Hello !!');
    }

    public function testCommit()
    {
        $tm = new DoctrineDBALTransactionManager($this->conn);
        $tm->beginTransaction();

        $this->createRow(2, 'World !!');
        $rows = $this->fetchAll();

        $this->assertEquals($rows, [['id' => 1, 'name' => 'Hello !!'], ['id' => 2, 'name' => 'World !!']]);

        $tm->commit();

        $rows = $this->fetchAll();

        $this->assertEquals($rows, [['id' => 1, 'name' => 'Hello !!'], ['id' => 2, 'name' => 'World !!']]);
    }

    public function testRollback()
    {
        $tm = new DoctrineDBALTransactionManager($this->conn);
        $tm->beginTransaction();

        $this->createRow(2, 'World !!');
        $rows = $this->fetchAll();

        $this->assertEquals($rows, [['id' => 1, 'name' => 'Hello !!'], ['id' => 2, 'name' => 'World !!']]);

        $tm->rollback();

        $rows = $this->fetchAll();

        $this->assertEquals($rows, [['id' => 1, 'name' => 'Hello !!']]);
    }

    private function createRow($id, $name)
    {
        $this->conn->insert('SAMPLE', [
            'id' => $id,
            'name' => $name
        ]);
    }

    private function fetchAll()
    {
        $rows = $this->conn->fetchAll('SELECT * FROM SAMPLE');

        return $rows;
    }

}


