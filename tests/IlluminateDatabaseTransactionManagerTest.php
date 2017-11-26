<?php

use Ideo\Transaction\IlluminateDatabaseTransactionManager;
use Illuminate\Database\Connection;
use Illuminate\Database\ConnectionInterface;

class IlluminateDatabaseTransactionManagerTest extends TestCase
{

    /**
     * @var ConnectionInterface
     */
    private $conn;

    public function setUp()
    {
        parent::setUp();

        $this->conn = new Connection(new PDO('sqlite::memory:'));
        $this->conn->statement('CREATE TABLE SAMPLE ( id INTEGER NOT NULL, name VARCHAR(50) NOT NULL )');

        $this->createRow(1, 'Hello !!');
    }

    public function testCommit()
    {
        $tm = new IlluminateDatabaseTransactionManager($this->conn);
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
        $tm = new IlluminateDatabaseTransactionManager($this->conn);
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
        $this->conn->insert('INSERT INTO SAMPLE VALUES ( :id, :name )', [
            'id' => $id,
            'name' => $name
        ]);
    }

    private function fetchAll()
    {
        $rows = $this->conn->table('SAMPLE')
            ->select()
            ->get()
            ->map(function ($e) {
                return [
                    'id' => $e->id,
                    'name' => $e->name
                ];
            })
            ->toArray();

        return $rows;
    }

}
