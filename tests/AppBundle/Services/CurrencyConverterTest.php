<?php

namespace Tests\AppBundle\Services;

use AppBundle\Services\CurrencyConverter;
use AppBundle\Services\CurrencyWebservice;
use PHPUnit\Framework\TestCase;
use Tests\Mock\EntityCreator;

class CurrencyConverterTest extends TestCase
{
    /**
     * @return array
     */
    public function dataConvertCalculations()
    {
        $data = [];

        $inRate = 0;
        $inAmount = 1;
        $expValue = 2;

        $data[] = [$inRate => '0.49', $inAmount => '0.01', $expValue => '0.00'];
        $data[] = [$inRate => '0.50', $inAmount => '0.01', $expValue => '0.01'];
        $data[] = [$inRate => '1.49', $inAmount => '0.01', $expValue => '0.01'];
        $data[] = [$inRate => '1.51', $inAmount => '0.01', $expValue => '0.02'];

        $data[] = [$inRate => '0.49', $inAmount => '-0.01', $expValue => '0.00'];
        $data[] = [$inRate => '0.50', $inAmount => '-0.01', $expValue => '-0.01'];
        $data[] = [$inRate => '1.49', $inAmount => '-0.01', $expValue => '-0.01'];
        $data[] = [$inRate => '1.51', $inAmount => '-0.01', $expValue => '-0.02'];

        $data[] = [$inRate => '10', $inAmount => '1', $expValue => '10.00'];
        $data[] = [$inRate => '10', $inAmount => '1.0', $expValue => '10.00'];
        $data[] = [$inRate => '10', $inAmount => '1.00', $expValue => '10.00'];

        return $data;
    }

    /**
     * @param string $inRate
     * @param string $inAmount
     * @param string $expValue
     * @dataProvider dataConvertCalculations
     */
    public function testConvertCalculations(string $inRate, string $inAmount, string $expValue)
    {
        // Not important for calculation test variables
        $date = new \DateTime('2000-01-01');
        $from = EntityCreator::createCurrency(1, '$', 'USD');
        $to = EntityCreator::createCurrency(2, '£', 'GBP');

        // Mock CurrencyWebservice
        $currencyWebserviceMock = $this->getMockBuilder(CurrencyWebservice::class)
            ->setMethods(['getExchangeRate'])->disableOriginalConstructor()->getMock();
        $currencyWebserviceMock->expects(self::once())
            ->method('getExchangeRate')
            ->with($date, $from, $to)
            ->willReturn($inRate)
        ;

        // Test
        $currencyConverter = new CurrencyConverter($currencyWebserviceMock, true);
        $outValue = $currencyConverter->convert($date, $from, $to, $inAmount);
        self::assertEquals($expValue, $outValue);
    }

    /**
     * @return array
     */
    public function dataConvertCache()
    {
        $data = [];

        $inWebservice = 0;
        $inUseCache = 1;
        $scenarios = 2;

        $dateOne = new \DateTime('2000-01-01');
        $dateTwo = new \DateTime('2000-01-02');
        $usd = EntityCreator::createCurrency(1, '$', 'USD');
        $gbp = EntityCreator::createCurrency(2, '£', 'GBP');
        $eur = EntityCreator::createCurrency(3, '€', 'EUR');

        // Cached
        $data['cached'] = [
            $inWebservice => $this->getMockBuilder(CurrencyWebservice::class)
                ->setMethods(['getExchangeRate'])->disableOriginalConstructor()->getMock(),
            $inUseCache => true,
            $scenarios => [
                '1-1' => ['inDate' => $dateOne, 'inFrom' => $usd, 'inTo'   => $gbp, 'inAmount' => '1.00', 'expValue' => '1.00'],
                '1-2' => ['inDate' => $dateOne, 'inFrom' => $usd, 'inTo'   => $gbp, 'inAmount' => '1.00', 'expValue' => '1.00'],
                '2-1' => ['inDate' => $dateTwo, 'inFrom' => $usd, 'inTo'   => $gbp, 'inAmount' => '1.00', 'expValue' => '2.00'],
                '1-3' => ['inDate' => $dateOne, 'inFrom' => $usd, 'inTo'   => $gbp, 'inAmount' => '1.00', 'expValue' => '1.00'],
                '3-1' => ['inDate' => $dateOne, 'inFrom' => $eur, 'inTo'   => $gbp, 'inAmount' => '1.00', 'expValue' => '3.00'],
                '4-1' => ['inDate' => $dateOne, 'inFrom' => $usd, 'inTo'   => $eur, 'inAmount' => '1.00', 'expValue' => '4.00'],
                '2-2' => ['inDate' => $dateTwo, 'inFrom' => $usd, 'inTo'   => $gbp, 'inAmount' => '1.00', 'expValue' => '2.00'],
                '1-4' => ['inDate' => $dateOne, 'inFrom' => $usd, 'inTo'   => $gbp, 'inAmount' => '1.00', 'expValue' => '1.00'],
            ],
        ];

        $data['cached'][$inWebservice]->expects(self::exactly(4))->method('getExchangeRate')
            ->withConsecutive([$dateOne, $usd, $gbp], [$dateTwo, $usd, $gbp], [$dateOne, $eur, $gbp])
            ->willReturnOnConsecutiveCalls('1.00', '2.00', '3.00', '4.00');

        // Non cached
        $data['non-cached'] = [
            $inWebservice => $this->getMockBuilder(CurrencyWebservice::class)
                ->setMethods(['getExchangeRate'])->disableOriginalConstructor()->getMock(),
            $inUseCache => false,
            $scenarios => [
                '1-1' => ['inDate' => $dateOne, 'inFrom' => $usd, 'inTo'   => $gbp, 'inAmount' => '1.00', 'expValue' => '1.00'],
                '1-2' => ['inDate' => $dateOne, 'inFrom' => $usd, 'inTo'   => $gbp, 'inAmount' => '1.00', 'expValue' => '2.00'],
                '2-1' => ['inDate' => $dateTwo, 'inFrom' => $usd, 'inTo'   => $gbp, 'inAmount' => '1.00', 'expValue' => '3.00'],
                '1-3' => ['inDate' => $dateOne, 'inFrom' => $usd, 'inTo'   => $gbp, 'inAmount' => '1.00', 'expValue' => '4.00'],
                '3-1' => ['inDate' => $dateOne, 'inFrom' => $eur, 'inTo'   => $gbp, 'inAmount' => '1.00', 'expValue' => '5.00'],
                '4-1' => ['inDate' => $dateOne, 'inFrom' => $usd, 'inTo'   => $eur, 'inAmount' => '1.00', 'expValue' => '6.00'],
                '2-2' => ['inDate' => $dateTwo, 'inFrom' => $usd, 'inTo'   => $gbp, 'inAmount' => '1.00', 'expValue' => '7.00'],
                '1-4' => ['inDate' => $dateOne, 'inFrom' => $usd, 'inTo'   => $gbp, 'inAmount' => '1.00', 'expValue' => '8.00'],
            ],
        ];

        $data['non-cached'][$inWebservice]->expects(self::exactly(8))->method('getExchangeRate')
            ->withConsecutive(
                [$dateOne, $usd, $gbp],
                [$dateOne, $usd, $gbp],
                [$dateTwo, $usd, $gbp],
                [$dateOne, $usd, $gbp],
                [$dateOne, $eur, $gbp],
                [$dateOne, $usd, $eur],
                [$dateTwo, $usd, $gbp],
                [$dateOne, $usd, $gbp]
            )
            ->willReturnOnConsecutiveCalls('1.00', '2.00', '3.00', '4.00', '5.00', '6.00', '7.00', '8.00');


        return $data;
    }

    /**
     * @param CurrencyWebservice $inWebservice
     * @param bool               $inUseCache
     * @param array              $scenarios
     * @dataProvider dataConvertCache
     */
    public function testConvertCache(CurrencyWebservice $inWebservice, bool $inUseCache, array $scenarios)
    {
        $currencyConverter = new CurrencyConverter($inWebservice, $inUseCache);
        foreach ($scenarios as $key => $scenario) {
            $outValue = $currencyConverter->convert($scenario['inDate'], $scenario['inFrom'], $scenario['inTo'], $scenario['inAmount']);
            self::assertEquals($scenario['expValue'], $outValue, 'scenario: ' . $key);
        }
    }
}
