<?php

namespace Tests\Mock;

use AppBundle\Entity\Currency;
use AppBundle\Entity\Merchant;
use AppBundle\Entity\Transaction;

/**
 * Class EntityCreator
 */
class EntityCreator
{
    /**
     * @param int    $id
     * @param string $symbol
     * @param string $isoCode
     *
     * @return Currency
     */
    public static function createCurrency(int $id, string $symbol, string $isoCode): Currency
    {
        $currency = new Currency();
        $currency->setSymbol($symbol);
        $currency->setIsoCode($isoCode);

        $reflection = new \ReflectionClass(Currency::class);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($currency, $id);

        return $currency;
    }

    /**
     * @param int    $id
     * @param string $name
     *
     * @return Merchant
     */
    public static function createMerchant(int $id, string $name): Merchant
    {
        $merchant = new Merchant();
        $merchant->setName($name);

        $reflection = new \ReflectionClass(Merchant::class);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($merchant, $id);

        return $merchant;
    }

    /**
     * @param int       $id
     * @param Merchant  $merchant
     * @param \DateTime $date
     * @param Currency  $currency
     * @param string    $amount
     *
     * @return Transaction
     * @throws \ReflectionException
     */
    public static function createTransaction(int $id, Merchant $merchant, \DateTime $date, Currency $currency, string $amount): Transaction
    {
        $transaction = new Transaction();
        $transaction->setMerchant($merchant)->setDate($date)->setCurrency($currency)->setValue($amount);

        $reflection = new \ReflectionClass(Transaction::class);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($transaction, $id);

        return $transaction;
    }
}
