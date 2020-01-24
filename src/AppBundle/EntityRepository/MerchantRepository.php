<?php

namespace AppBundle\EntityRepository;

use AppBundle\Entity\Merchant;
use Doctrine\ORM\EntityRepository;

/**
 * Class MerchantRepository
 */
class MerchantRepository extends EntityRepository
{
    /**
     * @param Merchant $merchant
     *
     * @return $this
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function register(Merchant $merchant): MerchantRepository
    {
        if ($merchant->getId() !== null) {
            throw new \InvalidArgumentException('$merchant must not have an existing id');
        }
        $this->_em->persist($merchant);
        $this->_em->flush($merchant);

        return $this;
    }
}
