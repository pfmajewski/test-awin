<?php

namespace AppBundle\Command;

use AppBundle\EntityRepository\TransactionRepository;
use AppBundle\Services\ImportCsv;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class AppImportCsvCommand
 */
class AppImportCsvCommand extends Command
{
    protected static $defaultName = 'app:import-csv';

    /**
     * @var ImportCsv
     */
    private $importCsvService;

    /**
     * @var TransactionRepository
     */
    private $transactionRepository;

    /**
     * AppImportCsvCommand constructor.
     *
     * @param ImportCsv             $importCsvService
     * @param TransactionRepository $transactionRepository
     */
    public function __construct(ImportCsv $importCsvService, TransactionRepository $transactionRepository)
    {
        parent::__construct();
        $this->importCsvService = $importCsvService;
        $this->transactionRepository = $transactionRepository;
    }

    /**
     * Configuration
     */
    protected function configure()
    {
        $this
            ->setName('app:import-csv')
            ->setDescription('Import transaction from csv file. Format (semicolon separated values): merchant;date;value')
            ->addArgument('csv-file-path', InputArgument::REQUIRED, 'Path to csv file')
            ->addOption('import-first-line', null, InputOption::VALUE_NONE, 'Import first line')
        ;
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
        $csvFilePath = $input->getArgument('csv-file-path');

        $transactions = [];
        $row = 0;
        if (($handle = fopen($csvFilePath, "r")) !== false) {
            while (($data = fgetcsv($handle, 1000, ";")) !== false) {
                if (($row == 0) && ($input->getOption('import-first-line') == false)) {
                    $row++;
                    continue;
                }
                $transaction = $this->importCsvService->readRow($data);
                $transactions[] = $transaction;
                $row++;
            }
            fclose($handle);
        }

        foreach ($transactions as $transaction) {
            $this->transactionRepository->register($transaction);
        }

        $output->writeln('Command result.');
    }
}
