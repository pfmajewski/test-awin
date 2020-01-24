<?php

namespace AppBundle\Services;

use AppBundle\Entity\Merchant;
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
        if ($merchant == null) {
            $merchant = new Merchant();
            $merchant->setName($row[0]);
            $this->merchantRepository->register($merchant);
        }

        $symbol = mb_substr($row[2], 0, 1);
        $currency = $this->currencyRepository->findOneBySymbol($symbol);

        $transaction->setMerchant($merchant);
        $transaction->setCurrency($currency);
        $transaction->setValue(mb_substr($row[2],1));
        $transaction->setDate(new \DateTime($row[1]));

        return $transaction;
    }

}
