<?php

namespace Tests\AppBundle\Services;

use AppBundle\Entity\Merchant;
use AppBundle\Entity\Transaction;
use AppBundle\EntityRepository\CurrencyRepository;
use AppBundle\EntityRepository\MerchantRepository;
use AppBundle\Services\ImportCsv;
use Tests\Mock\EntityCreator;
use PHPUnit\Framework\TestCase;

class ImportCsvTest extends TestCase
{
    /**
     * @return array
     */
    public function dataReadRow()
    {
        $data = [];

        $inCurrencyRepository = 0;
        $inMerchantRepository = 1;
        $inRow = 2;
        $expTransaction = 3;

        // Test existing merchant
        $data['test-create-existing-merchant'] = [
            $inCurrencyRepository => $this->getMockBuilder(CurrencyRepository::class)
                ->setMethods(['findOneBySymbol'])->disableOriginalConstructor()->getMock(),
            $inMerchantRepository => $this->getMockBuilder(MerchantRepository::class)
                ->setMethods(['find'])->disableOriginalConstructor()->getMock(),
            $inRow => ['1', '01/05/2010', '$10.01'],
            $expTransaction =>
                (new Transaction())
                    ->setCurrency(EntityCreator::createCurrency(1, '$', 'USD'))
                    ->setMerchant(EntityCreator::createMerchant(1, 'name'))
                    ->setValue('10.01')
                    ->setDate(new \DateTime('2010-01-05 00:00:00'))
            ,
        ];
        $data['test-create-existing-merchant'][$inCurrencyRepository]->expects(self::once())
            ->method('findOneBySymbol')
            ->with('$')
            ->willReturn(EntityCreator::createCurrency(1, '$', 'USD'));
        $data['test-create-existing-merchant'][$inMerchantRepository]->expects(self::once())
            ->method('find')
            ->with(1)
            ->willReturn(EntityCreator::createMerchant(1, 'name'));

        // Test new merchant
        $data['test-create-new-merchant'] = [
            $inCurrencyRepository => $this->getMockBuilder(CurrencyRepository::class)
                ->setMethods(['findOneBySymbol'])->disableOriginalConstructor()->getMock(),
            $inMerchantRepository => $this->getMockBuilder(MerchantRepository::class)
                ->setMethods(['find', 'register'])->disableOriginalConstructor()->getMock(),
            $inRow => ['1', '01/05/2010', '£50.00'],
            $expTransaction =>
                (new Transaction())
                    ->setCurrency(EntityCreator::createCurrency(1, '£', 'GBP'))
                    ->setMerchant((new Merchant())->setName('1'))
                    ->setValue('50.00')
                    ->setDate(new \DateTime('2010-01-05 00:00:00'))
            ,
        ];
        $data['test-create-new-merchant'][$inCurrencyRepository]->expects(self::once())
            ->method('findOneBySymbol')
            ->with('£')
            ->willReturn(EntityCreator::createCurrency(1, '£', 'GBP'));
        $data['test-create-new-merchant'][$inMerchantRepository]->expects(self::once())
            ->method('find')
            ->with(1)
            ->willReturn(null)
        ;
        $data['test-create-new-merchant'][$inMerchantRepository]->expects(self::once())
            ->method('register')
            ->with((new Merchant())->setName('1'))
            ->willReturn($data['test-create-new-merchant'][$inMerchantRepository])
        ;

        return $data;
    }

    /**
     * @param CurrencyRepository $inCurrencyRepository
     * @param MerchantRepository $inMerchantRepository
     * @param array              $inRow
     * @param Transaction        $expTransaction
     *
     * @dataProvider dataReadRow
     */
    public function testReadRow(
        CurrencyRepository $inCurrencyRepository,
        MerchantRepository $inMerchantRepository,
        array $inRow,
        Transaction $expTransaction
    ) {
        $importCsv = new ImportCsv($inCurrencyRepository, $inMerchantRepository);

        $outTransaction = $importCsv->readRow($inRow);
        self::assertEquals($expTransaction, $outTransaction);
    }
}
