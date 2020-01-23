<?php


namespace Tests\Mock;


use AppBundle\Entity\Currency;
use AppBundle\Entity\Merchant;

class EntityCreator
{
    /**
     * @param int    $id
     * @param string $symbol
     * @param string $isoCode
     *
     * @return Currency
     */
    public static function createCurrency(int $id, string $symbol, string $isoCode)
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
    public static function createMerchant(int $id, string $name)
    {
        $merchant = new Merchant();
        $merchant->setName($name);

        $reflection = new \ReflectionClass(Merchant::class);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($merchant, $id);

        return $merchant;
    }

}