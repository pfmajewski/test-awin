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

        /** @var Transaction[] $transactions */
        $transactions = $this->transactionRepository->findByMerchant($merchant);
        foreach ($transactions as $transaction) {
            if ($transaction->getCurrency() == $currency) {
                $amount = $transaction->getValue();
            } else {
                $amount = $this->currencyConverter->convert(
                    $dateExchangeRate,
                    $transaction->getCurrency(),
                    $currency,
                    $transaction->getValue()
                );
            }
            $result[] = TransactionReportItem::create($transaction->getDate(), $currency, $amount);
        }

        return $result;
    }

    /**
     * @param array  $items
     * @param bool   $skipHeader
     * @param string $columnSeparator
     * @param string $rowSeparator
     *
     * @return string
     */
    public function formatCsv(array $items, $skipHeader = false, $columnSeparator = ';', $rowSeparator = "\n"): string
    {
        $results = [];
        if ($skipHeader == false) {
            $results[] = 'date' . $columnSeparator . 'amount';
        }

        $i = 0;
        foreach ($items as $item) {
            if ($item instanceof TransactionReportItem) {
                $results[] = $item->date->format('m/d/Y') . $columnSeparator . $item->currency->getSymbol() . $item->amount;
            } else {
                throw new \InvalidArgumentException("Item not instance of \AppBundle\Model\TransactionReportItem, index of: $i.");
            }
            $i++;
        }

        return implode($rowSeparator, $results);
    }

    /**
     * @param array $items
     *
     * @return string
     */
    public function formatJson(array $items): string
    {
        $json = [];
        $i = 0;
        foreach ($items as $item) {
            if ($item instanceof TransactionReportItem) {
                $json[] = [
                    'date' => $item->date->format('Y-m-d'),
                    'currency' => $item->currency->getIsoCode(),
                    'amount' => $item->amount,
                ];
            } else {
                throw new \InvalidArgumentException("Item not instance of \AppBundle\Model\TransactionReportItem, index of: $i.");
            }
            $i++;
        }
        return json_encode($json);
    }
}
