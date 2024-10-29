<?php

namespace App\Command;

use App\Command\CsvNormalizerTrait;
use App\Entity\Customer;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Exception;
use League\Csv\Reader;
use League\Csv\SyntaxError;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'ugo:customers:import',
    description: 'Import customers from a CSV file',
)]
class UgoCustomersImportCommand extends Command
{
    use CsvNormalizerTrait;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(public EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->addArgument('customersFile', InputArgument::REQUIRED, 'Path to customers CSV file');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws Exception
     * @throws SyntaxError
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $pathCustomersFile = $input->getArgument('customersFile');
        if (!file_exists($pathCustomersFile)) {
            $output->writeln("<error>File '$pathCustomersFile' not exist.</error>");
            return Command::FAILURE;
        }

        if (filesize($pathCustomersFile) === 0) {
            $output->writeln("<error>File '$pathCustomersFile' is empty.</error>");
            return Command::FAILURE;
        }

        $csvCustomers = $this->normalizeCsvFile($pathCustomersFile);
        if (count($csvCustomers->getHeader()) !== count(array_unique($csvCustomers->getHeader()))) {
            throw new Exception("<error>File '$pathCustomersFile' not header.</error>");
        }

        $records = $csvCustomers->getRecords() ?? [];
        foreach ($records as $record) {
            if ($record["customer_id"] !== null) {
                $customer = new Customer();
                $title = $record["title"] == 1 ? 'mme' : 'm';
                $oldCustomer = $this->entityManager->getRepository(Customer::class)->findby(['customer_id' => $record["customer_id"], 'title' => $title]);
                if (empty($oldCustomer)) {
                    $customer->setCustomerId($record["customer_id"]);
                    $customer->setTitle($title);
                    $customer->setLastname($record["lastname"] ?? "");
                    $customer->setFirstname($record["firstname"] ?? "");
                    $customer->setPostalCode($record["postal_code"] ?? "");
                    $customer->setCity($record["city"] ?? "");
                    $customer->setEmail($record["email"] ?? "");

                    $this->entityManager->persist($customer);
                }
            }
        }
        $this->entityManager->flush();
        $output->writeln('Customers imported successfully.');

        return Command::SUCCESS;
    }
}
