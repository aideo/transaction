<?php

namespace Ideo\Transaction;

use Doctrine\DBAL\Connection;
use Exception;
use InvalidArgumentException;

/**
 * Doctrine DBAL の TransactionManager 実装。
 *
 * @package Ideo\Transaction\Doctrine
 */
class DoctrineDBALTransactionManager implements TransactionManager
{

    /**
     * Connection オブジェクトを保持します。
     *
     * @var Connection
     */
    private $conn;

    /**
     * DBALTransactionManager constructor.
     *
     * @param Connection $conn Connection オブジェクト。
     */
    public function __construct(Connection $conn)
    {
        $this->conn = $conn;
    }

    /**
     * @inheritdoc
     */
    public function beginTransaction()
    {
        $this->conn->beginTransaction();
    }

    /**
     * @inheritdoc
     */
    public function commit()
    {
        $this->conn->commit();
    }

    /**
     * @inheritdoc
     */
    public function rollback()
    {
        $this->conn->rollBack();
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
        } catch (Exception $ex) {
            $this->rollback();

            throw $ex;
        }
    }

}
