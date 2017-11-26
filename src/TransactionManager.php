<?php

namespace Ideo\Transaction;

/**
 * Interface TransactionManager
 *
 * @package Ideo\Transaction
 */
interface TransactionManager
{

    /**
     * トランザクションを開始します。
     */
    public function beginTransaction();

    /**
     * トランザクションをコミットします。
     */
    public function commit();

    /**
     * トランザクションをロールバックします。
     */
    public function rollback();

    /**
     * トランザクションの処理を行います。
     *
     * @param callable $func トランザクション中に行う処理。
     *
     * @return mixed 処理結果。
     */
    public function transactional(callable $func);

}
