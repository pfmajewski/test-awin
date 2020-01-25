<?php

namespace Tests\AppBundle\Services;

use AppBundle\EntityRepository\TransactionRepository;
use AppBundle\Model\TransactionReportItem;
use AppBundle\Services\CurrencyConverter;
use AppBundle\Services\TransactionReport;
use PHPUnit\Framework\TestCase;
use Tests\Mock\EntityCreator;

class TransactionReportTest extends TestCase
{

    public function testGetReport()
    {
        $usd = EntityCreator::createCurrency(1, '$', 'USD');
        $gbp = EntityCreator::createCurrency(2, '£', 'GBP');
        $eur = EntityCreator::createCurrency(3, '€', 'EUR');
        $dateOne = new \DateTime('2000-01-01');
        $dateTwo = new \DateTime('2000-01-02');
        $dateThree = new \DateTime('2000-02-03');

        $inMerchant = EntityCreator::createMerchant(2, '2');
        $inDateExchangeRate = new \DateTime('2020-01-20');

        $inTransactionRepository = $this->getMockBuilder(TransactionRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['findByMerchant'])
            ->getMock();
        $inTransactionRepository->expects(self::once())
            ->method('findByMerchant')
            ->with($inMerchant)
            ->willReturn([
                EntityCreator::createTransaction(1, $inMerchant, $dateOne, $usd, '11.00'),
                EntityCreator::createTransaction(3, $inMerchant, $dateOne, $usd, '12.00'),
                EntityCreator::createTransaction(5, $inMerchant, $dateTwo, $gbp, '13.00'),
                EntityCreator::createTransaction(6, $inMerchant, $dateThree, $eur, '14.00'),
                EntityCreator::createTransaction(9, $inMerchant, $dateOne, $usd, '15.00'),
            ])
        ;

        $inCurrencyConverter = $this->getMockBuilder(CurrencyConverter::class)
            ->disableOriginalConstructor()
            ->setMethods(['convert'])
            ->getMock()
            ;
        $inCurrencyConverter->expects(self::exactly(4))
            ->method('convert')
            ->withConsecutive(
                [$inDateExchangeRate, $usd, $gbp, '11.00'],
                [$inDateExchangeRate, $usd, $gbp, '12.00'],
                [$inDateExchangeRate, $eur, $gbp, '14.00'],
                [$inDateExchangeRate, $usd, $gbp, '15.00']
            )
            ->willReturnOnConsecutiveCalls('21.00', '22.00', '24.00', '25.00')
        ;

        $expReport = [
            TransactionReportItem::create(clone $dateOne, $gbp, '21.00'),
            TransactionReportItem::create(clone $dateOne, $gbp, '22.00'),
            TransactionReportItem::create(clone $dateTwo, $gbp, '13.00'),
            TransactionReportItem::create(clone $dateThree, $gbp, '24.00'),
            TransactionReportItem::create(clone $dateOne, $gbp, '25.00'),
        ];

        $transactionReportService = new TransactionReport($inCurrencyConverter, $inTransactionRepository);

        $outReport = $transactionReportService->getReport($inMerchant, $inDateExchangeRate, $gbp);

        self::assertEquals($expReport, $outReport);
    }

    public function testFormatCsv()
    {
        $usd = EntityCreator::createCurrency(1, '$', 'USD');
        $gbp = EntityCreator::createCurrency(2, '£', 'GBP');
        $eur = EntityCreator::createCurrency(3, '€', 'EUR');
        $dateOne = new \DateTime('2000-01-01');
        $dateTwo = new \DateTime('2000-01-02');
        $dateThree = new \DateTime('2000-02-03');

        $inItems = [
            TransactionReportItem::create($dateOne, $usd, '123.45'),
            TransactionReportItem::create($dateTwo, $gbp, '-3.20'),
            TransactionReportItem::create($dateThree, $eur, '0.00'),
        ];

        $transactionReportService = new TransactionReport(
            $this->getMockBuilder(CurrencyConverter::class)->disableOriginalConstructor()->getMock(),
            $this->getMockBuilder(TransactionRepository::class)->disableOriginalConstructor()->getMock()
        );

        // Test all default values:
        $outCsvDefault = $transactionReportService->formatCsv($inItems);
        self::assertEquals(
            'date;amount'. '\n\r' . '01/01/2000;$123.45' . '\n\r' . '01/02/2000;£-3.20' . '\n\r' . '02/03/2000;€0.00',
            $outCsvDefault,
            'Test of defaults'
        );

        // Test all default values:
        $outCsvCustom = $transactionReportService->formatCsv($inItems, true, ',', '\n');
        self::assertEquals(
            '01/01/2000,$123.45' . '\n' . '01/02/2000,£-3.20' . '\n' . '02/03/2000,€0.00',
            $outCsvCustom,
            'Test of custom parameters'
        );

        // Test exception
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('index of: 0.');
        array_unshift($inItems, 'Not an object');
        $transactionReportService->formatCsv($inItems);
    }

    public function testFormatJson()
    {
        $usd = EntityCreator::createCurrency(1, '$', 'USD');
        $gbp = EntityCreator::createCurrency(2, '£', 'GBP');
        $dateOne = new \DateTime('2000-01-01');
        $dateTwo = new \DateTime('2000-02-03');

        $inItems = [
            TransactionReportItem::create($dateOne, $usd, '123.45'),
            TransactionReportItem::create($dateTwo, $gbp, '-3.20'),
        ];

        $transactionReportService = new TransactionReport(
            $this->getMockBuilder(CurrencyConverter::class)->disableOriginalConstructor()->getMock(),
            $this->getMockBuilder(TransactionRepository::class)->disableOriginalConstructor()->getMock()
        );

        // Test all default values:
        $outJson = $transactionReportService->formatJson($inItems);
        self::assertEquals(
             '[{"date":"2000-01-01","currency":"USD","amount":"123.45"},{"date":"2000-02-03","currency":"GBP","amount":"-3.20"}]',
            $outJson
        );

        // Test exception
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('index of: 2.');
        $inItems[] = 'Not an object';
        $transactionReportService->formatJson($inItems);
    }


}
