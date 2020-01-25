<?php

namespace AppBundle\Services;

use AppBundle\Entity\Currency;

/**
 * Class CurrencyConverter
 */
class CurrencyConverter
{
    /**
     * @var CurrencyWebservice
     */
    private $currencyWebservice;

    /**
     * Whether to use local cache
     * @var bool
     */
    private $useCache = true;

    /**
     * @var array
     */
    private $exchangeRateCache = [];

    /**
     * CurrencyConverter constructor.
     *
     * @param CurrencyWebservice $currencyWebservice
     * @param bool               $useCache
     */
    public function __construct(CurrencyWebservice $currencyWebservice, bool $useCache)
    {
        $this->currencyWebservice = $currencyWebservice;
        $this->useCache = $useCache;
    }

    /**
     * @param \DateTime $dateTime
     * @param Currency  $from
     * @param Currency  $to
     * @param string    $amount
     *
     * @return string
     */
    public function convert(\DateTime $dateTime, Currency $from, Currency $to, string $amount): string
    {
        $rate = $this->getExchangeRate($dateTime, $from, $to);
        $result = self::bcround(bcmul($amount, $rate, 8), 2);
        return $result;
    }

    /**
     * @param \DateTime $dateTime
     * @param Currency  $from
     * @param Currency  $to
     *
     * @return mixed|string|null
     */
    private function getExchangeRate(\DateTime $dateTime, Currency $from, Currency $to)
    {
        $rate = null;
        $cacheKey = sprintf('%d-%s-%s', $dateTime->getTimestamp(), $from->getIsoCode(), $to->getIsoCode());

        if ($this->useCache && !empty($this->exchangeRateCache[$cacheKey])) {
            $rate = $this->exchangeRateCache[$cacheKey];
        } else {
            $rate = $this->currencyWebservice->getExchangeRate($dateTime, $from, $to);
            $this->exchangeRateCache[$cacheKey] = $rate;
        }

        return $rate;
    }

    /**
     * Rounds decimal number to given precision and also adds zeros to fill precision, e.g. ("1",2) => "1.00"
     *
     * @param string $number
     * @param int    $precision
     *
     * @return string
     */
    static private function bcround(string $number, $precision = 0): string
    {
        if ($number[0] != '-') {
            $result = bcadd($number, '0.' . str_repeat('0', $precision) . '5', $precision);
        } else {
            $result = bcsub($number, '0.' . str_repeat('0', $precision) . '5', $precision);
            if (preg_match('/^\-0\.?0{0,}$/', $result)) {
                $result = substr($result, 1);
            }
        }
        return $result;
    }
}
