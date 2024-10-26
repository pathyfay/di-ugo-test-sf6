<?php

namespace App\Command;

use App\Command\CsvNormalizerTrait;
use App\Entity\Customer;
use App\Entity\Order;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Exception;
use League\Csv\Reader;
use League\Csv\SyntaxError;
use Random\RandomException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


#[AsCommand(
    name: 'ugo:purchases:import',
    description: 'Import purchases from a CSV file',
)]
class UgoPurchasesImportCommand extends Command
{
    use CsvNormalizerTrait;
    use UtilsTrait;

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
            ->addArgument('purchasesFile', InputArgument::REQUIRED, 'Path to customers CSV file');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws Exception
     * @throws SyntaxError
     * @throws RandomException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $pathPurchasesFile = $input->getArgument('purchasesFile');
        if (!file_exists($pathPurchasesFile)) {
            $output->writeln("<error>file '$pathPurchasesFile' not exist.</error>");
            return Command::FAILURE;
        }

        if (filesize($pathPurchasesFile) === 0) {
            $output->writeln("<error>File '$pathPurchasesFile' is empty.</error>");
            return Command::FAILURE;
        }

        $csvPurchases = $this->normalizeCsvFile($pathPurchasesFile);
        if (count($csvPurchases->getHeader()) !== count(array_unique($csvPurchases->getHeader()))) {
            throw new Exception("<error>File '$pathPurchasesFile' not header.</error>");
        }

        $records = $csvPurchases->getRecords() ?? [];
        foreach ($records as $row) {
            $order = new Order();
            $customer = $this->entityManager->getRepository(Customer::class)->findOneBy(['customer_id' => $row["customer_id"]]);
            if (!$customer) {
                $purchaseIdentifier = $this->parseDateToDateTime($row['purchase_identifier']);
                $productId = is_numeric($row["product_id"] ?? null) ? (int)$row["product_id"] : random_int(1, 100);
                $order->setOrderId($purchaseIdentifier);
                $order->setCustomer($customer);
                $order->setProductId($productId);
                $order->setQuantity($row["quantity"]);
                $order->setPrice($row['price']);
                $order->setCurrency($row['currency']);
                $order->setDate(DateTime::createFromFormat('Y-m-d', $row['date']));

                $this->entityManager->persist($order);
            }
        }

        $this->entityManager->flush();
        $output->writeln('Purchases imported successfully.');

        return Command::SUCCESS;
    }
}
