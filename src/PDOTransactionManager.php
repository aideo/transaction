<?php

namespace Ideo\Transaction;

use PDO;
use InvalidArgumentException;
use Exception;

/**
 * PDO の TransactionManager 実装。
 *
 * @package Ideo\Transaction\Doctrine
 */
class PDOTransactionManager implements TransactionManager
{

    /**
     * PDO オブジェクトを保持します。
     *
     * @var PDO
     */
    private $conn;

    /**
     * PDOTransactionManager constructor.
     *
     * @param PDO $conn Connection オブジェクト。
     */
    public function __construct(PDO $conn)
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
