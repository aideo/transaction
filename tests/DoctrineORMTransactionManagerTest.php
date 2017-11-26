<?php

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Setup;
use Ideo\Transaction\DoctrineORMTransactionManager;

class DoctrineORMTransactionManagerTest extends TestCase
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function setUp()
    {
        parent::setUp();

        $conn = DriverManager::getConnection(['url' => 'sqlite:///:memory:']);
        $conn->exec('CREATE TABLE SAMPLE ( id INTEGER NOT NULL, name VARCHAR(50) NOT NULL )');

        $config = Setup::createAnnotationMetadataConfiguration([__DIR__], false, null, new ArrayCache(), false);

        $this->em = EntityManager::create($conn, $config);

        $this->createRow(1, 'Hello !!');
    }

    public function testCommit()
    {
        $tm = new DoctrineORMTransactionManager($this->em);
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
        $tm = new DoctrineORMTransactionManager($this->em);
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
        $entity = new DoctrineORMSampleEntity();
        $entity->id = $id;
        $entity->name = $name;

        $this->em->persist($entity);
        $this->em->flush();

        $this->em->clear();
    }

    private function fetchAll()
    {
        $results = $this->em->createQuery("SELECT e FROM DoctrineORMSampleEntity e");

        $rows = $results->getArrayResult();

        return $rows;
    }

}
