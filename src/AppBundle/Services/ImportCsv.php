<?php

namespace AppBundle\Services;

use AppBundle\Entity\Transaction;
use AppBundle\EntityRepository\CurrencyRepository;
use AppBundle\EntityRepository\MerchantRepository;

/**
 * Class ImportCsv
 */
class ImportCsv
{
    /**
     * @var CurrencyRepository
     */
    private $currencyRepository;

    /**
     * @var MerchantRepository
     */
    private $merchantRepository;

    /**
     * ImportCsv constructor.
     *
     * @param CurrencyRepository $currencyRepository
     * @param MerchantRepository $merchantRepository
     */
    public function __construct(CurrencyRepository $currencyRepository, MerchantRepository $merchantRepository)
    {
        $this->currencyRepository = $currencyRepository;
        $this->merchantRepository = $merchantRepository;
    }

    /**
     * @param array $row
     *
     * @return Transaction
     */
    public function readRow(array $row)
    {
        $transaction = new Transaction();


        $merchant = $this->merchantRepository->find($row[0]);

        $currency = $this->currencyRepository->findOneBySymbol(substr($row[2], 0, 1));

        $transaction->setMerchant($merchant);
        $transaction->setCurrency($currency);
        $transaction->setValue(substr($row[2],1));
        $transaction->setDate(new \DateTime($row[1]));




        return $transaction;
    }

}
