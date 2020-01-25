<?php

namespace AppBundle\Services;

use AppBundle\Entity\Currency;
use AppBundle\Entity\Merchant;
use AppBundle\Entity\Transaction;
use AppBundle\EntityRepository\TransactionRepository;
use AppBundle\Model\TransactionReportItem;

/**
 * Class TransactionReport
 */
class TransactionReport
{
    /**
     * @var CurrencyConverter
     */
    private $currencyConverter;

    /**
     * @var TransactionRepository
     */
    private $transactionRepository;

    /**
     * TransactionReport constructor.
     *
     * @param CurrencyConverter     $currencyConverter
     * @param TransactionRepository $transactionRepository
     */
    public function __construct(CurrencyConverter $currencyConverter, TransactionRepository $transactionRepository)
    {
        $this->currencyConverter = $currencyConverter;
        $this->transactionRepository = $transactionRepository;
    }

    /**
     * @param Merchant  $merchant
     * @param \DateTime $dateExchangeRate
     * @param Currency  $currency
     *
     * @return TransactionReportItem[]
     */
    public function getReport(Merchant $merchant, \DateTime $dateExchangeRate, Currency $currency): array
    {
        $result = [];

        $transactions = $this->transactionRepository->findByMerchant($merchant);
        foreach ($transactions as $transaction) {
            if ($transaction instanceof Transaction) {
                // $reportItem =
            }
        }

        return $result;
    }
}
