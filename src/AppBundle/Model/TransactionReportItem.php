<?php

namespace AppBundle\Model;

use AppBundle\Entity\Currency;

/**
 * Class TransactionReportItem
 */
class TransactionReportItem
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
}
