<?php

namespace Ideo\Transaction;

use PDO;

class PDOTransactionManagerTest extends TestCase
{

    /**
     * @var PDO
     */
    private $pdo;

    private function createRow($id, $name)
    {
        $stmt = $this->pdo->prepare('INSERT INTO SAMPLE VALUES ( :id, :name )');
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':name', $name);
        $stmt->execute();
        $stmt->closeCursor();
    }

    private function fetchAll()
    {
        $stmt = $this->pdo->query('SELECT * FROM SAMPLE');
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt->closeCursor();

        return $rows;
    }

    public function setUp()
    {
        parent::setUp();

        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->exec('CREATE TABLE SAMPLE ( id INTEGER NOT NULL, name VARCHAR(50) NOT NULL )');

        $this->createRow(1, 'Hello !!');
    }

    public function testCommit()
    {
        $tm = new PDOTransactionManager($this->pdo);
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
        $tm = new PDOTransactionManager($this->pdo);
        $tm->beginTransaction();

        $this->createRow(2, 'World !!');
        $rows = $this->fetchAll();

        $this->assertEquals($rows, [['id' => 1, 'name' => 'Hello !!'], ['id' => 2, 'name' => 'World !!']]);

        $tm->rollback();

        $rows = $this->fetchAll();

        $this->assertEquals($rows, [['id' => 1, 'name' => 'Hello !!']]);
    }

}
