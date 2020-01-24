<?php

namespace Tests\AppBundle\Services;

use AppBundle\Entity\Transaction;
use AppBundle\EntityRepository\CurrencyRepository;
use AppBundle\EntityRepository\MerchantRepository;
use AppBundle\Services\ImportCsv;
use Tests\Mock\EntityCreator;

class ImportCsvTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function dataReadRow()
    {
        $data = [];

        $data['test-01'] = [
            '$inCurrencyRepository' => $this->getMock(CurrencyRepository::class, ['findOneBySymbol'], [], '', false),
            '$inMerchantRepository' => $this->getMock(MerchantRepository::class, ['find'], [], '', false),
            '$inRow' => ['1', '01/05/2010', '$10.01'],
            '$expTransaction' =>
                (new Transaction())
                    ->setCurrency(EntityCreator::createCurrency(1, '$', 'USD'))
                    ->setMerchant(EntityCreator::createMerchant(1, 'name'))
                    ->setValue('10.01')
                    ->setDate(new \DateTime('2010-01-05 00:00:00'))
            ,
        ];
        $data['test-01']['$inCurrencyRepository']->expects(self::once())
            ->method('findOneBySymbol')
            ->with('$')
            ->willReturn(EntityCreator::createCurrency(1, '$', 'USD'));
        $data['test-01']['$inMerchantRepository']->expects(self::once())
            ->method('find')
            ->with(1)
            ->willReturn(EntityCreator::createMerchant(1, 'name'));

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
