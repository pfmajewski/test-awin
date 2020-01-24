<?php

namespace AppBundle\DataFixtures;

use AppBundle\Entity\Currency;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class CurrencyFixtures
 * Loads basic currencies into database
 */
class CurrencyFixtures extends Fixture
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $manager->persist((new Currency())->setIsoCode('EUR')->setSymbol('€'));
        $manager->persist((new Currency())->setIsoCode('GBP')->setSymbol('£'));
        $manager->persist((new Currency())->setIsoCode('USD')->setSymbol('$'));

        $manager->flush();
    }
}
