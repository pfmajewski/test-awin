<?php

namespace AppBundle\Services;

use AppBundle\Entity\Currency;

class CurrencyWebservice
{

    /**
     * @param \DateTime $dateTime
     * @param Currency  $from
     * @param Currency  $to
     *
     * @return string
     */
    public function getExchangeRate(\DateTime $dateTime, Currency $from, Currency $to): string
    {
        $rate = sprintf('%d.%d', rand(0, 99), rand(0, 999999));
        return $rate;
    }
}
