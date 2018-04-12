<?php

namespace Ideo\Transaction;

use Exception;
use Illuminate\Database\ConnectionInterface;
use InvalidArgumentException;

/**
 * Illuminate ConnectionInterface の TransactionManager 実装。
 *
 * @package Ideo\Transaction\Illuminate
 */
class IlluminateTransactionManager implements TransactionManager
{

    /**
     * ConnectionInterface オブジェクトを保持します。
     *
     * @var ConnectionInterface
     */
    private $conn;

    /**
     * PDOTransactionManager constructor.
     *
     * @param ConnectionInterface $conn ConnectionInterface オブジェクト。
     */
    public function __construct(ConnectionInterface $conn)
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

            throw new TransactionException('An exception occurred during the transaction.', 0, $ex);
        }
    }

}
