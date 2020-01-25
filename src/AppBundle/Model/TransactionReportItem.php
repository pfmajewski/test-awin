<?php

namespace AppBundle\Model;

use AppBundle\Entity\Currency;

/**
 * Class TransactionReportItem
 */
class TransactionReportItem implements \JsonSerializable
{
    /**
     * The date of the transaction
     * @var \DateTime
     */
    public $date;

    /**
     * The currency of the transaction
     * @var Currency
     */
    public $currency;

    /**
     * Amount of the transaction in decimal format
     * @var string
     */
    public $amount;

    /**
     * @param \DateTime $date
     * @param Currency  $currency
     * @param string    $amount
     *
     * @return TransactionReportItem
     */
    static public function create(\DateTime $date, Currency $currency, string $amount): TransactionReportItem
    {
        $item = new TransactionReportItem();
        $item->date = $date;
        $item->currency = $currency;
        $item->amount = $amount;
        return $item;
    }

    /**
     * Returns row of data in csv format. Amount is prefixed with currency symbol.
     * @param string $separator
     *
     * @return string
     */
    public function getCsvRow($separator = ';'): string
    {
        $csv = $this->date->format('m/d/Y') . $separator . $this->currency->getSymbol() . $this->amount;
        return $csv;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'date' => $this->date->format('Y-m-d'),
            'currency' => $this->currency->getIsoCode(),
            'amount' => $this->amount,
        ];
    }
}
