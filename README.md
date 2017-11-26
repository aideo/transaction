# ideo/transaction
[![Build Status](https://travis-ci.org/aideo/transaction.svg?branch=master)](https://travis-ci.org/aideo/transaction)

We will share different transaction implementations for each framework.

When implementing repository patterns for database access, framework specific classes may be exposed in the service layer transaction implementation, but we will prevent that.

The corresponding framework is as follows.

- PDO
- Doctrine DBAL
- Doctrine ORM
- Laravel
- Lumen

## Usage

    // class corresponding to each framework
    $tm = new ... 
    
    $tm->beginTransaction();
    
    ...
    
    $tm->commit();

Or

    // class corresponding to each framework
    $tm = new ... 
    
    $tm->transactional(function () {
        ...
    });
