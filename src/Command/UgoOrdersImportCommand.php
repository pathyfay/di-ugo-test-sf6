<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use League\Csv\Reader;

#[AsCommand(
    name: 'ugo:orders:import',
    description: 'Import customers & purchase from a CSV file ',
)]
class UgoOrdersImportCommand extends Command
{
    /**
     * @param EntityManagerInterface $entityManager
     * @param UgoCustomersImportCommand $customersImportCommand
     * @param UgoPurchasesImportCommand $purchasesImportCommand
     */
    public function __construct(
        public EntityManagerInterface    $entityManager,
        public UgoCustomersImportCommand $customersImportCommand,
        public UgoPurchasesImportCommand $purchasesImportCommand)
    {
        parent::__construct();
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->addOption('customers', null, InputOption::VALUE_NONE, 'Import customers from the CSV file')
            ->addOption('purchases', null, InputOption::VALUE_NONE, 'Import purchases from the CSV file')
            ->addArgument('customersFile', InputArgument::REQUIRED, 'Path to customers CSV file')
            ->addArgument('purchasesFile', InputArgument::REQUIRED, 'Path to purchases CSV file');;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws ExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $importCustomers = $input->getOption('customers');
        $importPurchases = $input->getOption('purchases');
        if (!$importCustomers && !$importPurchases) {
            $output->writeln('You must specify at least one of the options: --customers or --purchases.');
            return Command::FAILURE;
        }

        $requiredFiles = [
            'customers' => 'You must add a csv customer.csv.',
            'purchases' => 'You must add a csv purchases.csv.',
        ];

        foreach ($requiredFiles as $key => $message) {
            if ($input->getOption($key) && !$input->getArgument("{$key}File")) {
                $output->writeln($message);
                return Command::FAILURE;
            }
        }

        $customersImportCommand = $this->getApplication()->find('ugo:customers:import');
        $customersArguments = new ArrayInput(['customersFile' => $input->getArgument('customersFile')]);
        $customersImportCommand->run($customersArguments, $output);

        $purchasesImportCommand = $this->getApplication()->find('ugo:purchases:import');
        $purchasesArguments = new ArrayInput(['purchasesFile' => $input->getArgument('purchasesFile')]);
        $purchasesImportCommand->run($purchasesArguments, $output);

        $output->writeln('Data imported successfully.');
        return Command::SUCCESS;
    }
}
