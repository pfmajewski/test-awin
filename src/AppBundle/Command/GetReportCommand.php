<?php

namespace AppBundle\Command;

use AppBundle\Entity\Currency;
use AppBundle\EntityRepository\CurrencyRepository;
use AppBundle\EntityRepository\MerchantRepository;
use AppBundle\Services\TransactionReport;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class GetReportCommand
 */
class GetReportCommand extends Command
{
    protected static $defaultName = 'app:get-report';

    /**
     * @var TransactionReport
     */
    private $transactionReportService;

    /**
     * @var CurrencyRepository
     */
    private $currencyRepository;

    /**
     * @var MerchantRepository
     */
    private $merchantRepository;

    /**
     * @var Currency
     */
    private $currency;

    /**
     * @var \DateTime
     */
    private $exchangeRateDate;

    /**
     * GetReportCommand constructor.
     *
     * @param TransactionReport $transactionReportService
     * @param CurrencyRepository $currencyRepository
     * @param MerchantRepository $merchantRepository
     */
    public function __construct(
        TransactionReport $transactionReportService,
        CurrencyRepository $currencyRepository,
        MerchantRepository $merchantRepository
    ) {
        parent::__construct();
        $this->transactionReportService = $transactionReportService;
        $this->currencyRepository = $currencyRepository;
        $this->merchantRepository = $merchantRepository;
    }

    /**
     * Configuration
     */
    protected function configure()
    {
        $this
            ->setDescription('Get report of merchant\'s transactions in one currency')
            ->addArgument('merchant', InputArgument::REQUIRED, 'Id of the merchant')
            ->addOption('currency', null, InputOption::VALUE_REQUIRED, 'Currency ISO code', 'GBP')
            ->addOption('exchange-rate-date', null, InputOption::VALUE_REQUIRED, 'Date of exchange rate, use current date if not provided', null)
            ->addOption('format', null, InputOption::VALUE_REQUIRED, 'Output format JSON or CSV', 'CSV')
            ->addOption('csv-field-separator', null, InputOption::VALUE_REQUIRED, 'Field separator for CSV format', ';')
        ;
    }

    /**
     * Validate parameters
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);

        if (!preg_match('/^[0-9]+$/', $input->getArgument('merchant'))) {
            throw new \InvalidArgumentException('Incorrect merchant id specified: ' . $input->getArgument('merchant'));
        }

        if (!preg_match('/^(CSV)|(JSON)$/', strtoupper($input->getOption('format')))) {
            throw new \InvalidArgumentException('Incorrect format specified, requires JSON or CSV, given: ' . $input->getOption('format'));
        }

        try {
            $this->currency = $this->currencyRepository->findOneByIsoCode($input->getOption('currency'));
        } catch (\Exception $e) {
            // Ignore Doctrine exceptions
            $this->currency = null;
        }
        if (!$this->currency instanceof Currency) {
            throw new \InvalidArgumentException('Unknown currency specified, given: ' . $input->getOption('currency'));
        }

        if ($input->getOption('exchange-rate-date')) {
            $this->exchangeRateDate = new \DateTime($input->getOption('exchange-rate-date'));
        } else {
            $this->exchangeRateDate = new \DateTime('NOW');
        }
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|void|null
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $merchant = $this->merchantRepository->getReference(intval($input->getArgument('merchant')));
        $items = $this->transactionReportService->getReport($merchant, $this->exchangeRateDate, $this->currency);

        $report = '';
        switch (strtoupper($input->getOption('format'))) {

            case 'CSV':
                $report = $this->transactionReportService->formatCsv(
                    $items,
                    false,
                    $input->getOption('csv-field-separator')
                );
                break;
            case 'JSON':
                $report = $this->transactionReportService->formatJson($items);
                break;
        }

        $output->write($report);
    }
}