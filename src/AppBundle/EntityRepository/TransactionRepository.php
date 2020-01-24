<?php

namespace AppBundle\EntityRepository;

use AppBundle\Entity\Transaction;
use Doctrine\ORM\EntityRepository;

/**
 * Class TransactionRepository
 */
class TransactionRepository extends EntityRepository
{
    /**
     * @param Transaction $transaction
     *
     * @return TransactionRepository
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function register(Transaction $transaction): TransactionRepository
    {
        if ($transaction->getId() !== null) {
            throw new \InvalidArgumentException('$transaction must not have an existing id');
        }
        $this->_em->persist($transaction);
        $this->_em->flush($transaction);

        return $this;
    }
}
