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
}
