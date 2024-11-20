<?php

namespace App\Command;

use App\Command\CsvNormalizerTrait;
use App\Entity\Customer;
use App\Entity\Order;
use App\Entity\Product;
use DateTime;
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
            $customer = $this->entityManager->getRepository(Customer::class)->findOneBy(['id' => $row["customer_id"]]);
            $product = $this->entityManager->getRepository(Product::class)->findOneProductById($row["product_id"]);
            $purchaseIdentifier = $this->parseDateToDateTime($row['purchase_identifier']);

            if ($customer !== null) {
                $order = $this->entityManager->getRepository(Order::class)->findOneBy([
                    'customer' => $customer,
                    'product' => $product
                ]);

                if ($order === null) {
                    $order = new Order();
                    $order->setOrderDate($purchaseIdentifier);
                    $order->setCustomer($customer);
                    $order->setProduct($product);
                    $order->setQuantity($row["quantity"]);
                    $order->setPrice($row['price']);
                    $order->setCurrency($row['currency']);
                    $order->setDate(DateTime::createFromFormat('Y-m-d', $row['date']));
                    $this->entityManager->persist($order);
                }
            }
        }

        $this->entityManager->flush();
        $output->writeln('Purchases imported successfully.');

        return Command::SUCCESS;
    }
}
