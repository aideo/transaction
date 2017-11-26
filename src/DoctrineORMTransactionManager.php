<?php

namespace Ideo\Transaction;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use InvalidArgumentException;

/**
 * Doctrine ORM の TransactionManager 実装。
 *
 * @package Ideo\Transaction\Doctrine
 */
class DoctrineORMTransactionManager implements TransactionManager
{

    /**
     * EntityManagerInterface オブジェクトを保持します。
     *
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * ORMTransactionManager constructor.
     *
     * @param EntityManagerInterface $em EntityManagerInterface オブジェクト。
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @inheritdoc
     */
    public function beginTransaction()
    {
        $this->em->beginTransaction();
    }

    /**
     * @inheritdoc
     */
    public function commit()
    {
        $this->em->flush();
        $this->em->commit();
    }

    /**
     * @inheritdoc
     */
    public function rollback()
    {
        $this->em->rollback();
    }

    /**
     * @inheritdoc
     */
    public function transactional(callable $func)
    {
        if (!is_callable($func)) {
            throw new InvalidArgumentException('Expected argument of type "callable", got "' . gettype($func) . '"');
        }

        $this->beginTransaction();

        try {
            $return = call_user_func($func, $this);

            $this->commit();

            return $return;
        } catch (Exception $e) {
            $this->rollback();

            throw $e;
        }
    }

}
