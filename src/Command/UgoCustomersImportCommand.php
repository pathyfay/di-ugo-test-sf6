<?php

namespace App\Command;

use App\Command\CsvNormalizerTrait;
use App\Entity\Civility;
use App\Entity\Order;
use App\Entity\Resource;
use App\Entity\User;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
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
        $this->addArgument('customersFile', InputArgument::REQUIRED, 'Path to customers CSV file');
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
            if ($record["id"] !== null) {
                $user = new User();
                $title = $record["title"] == 1 ? 'mme' : 'm';
                $oldCustomer = $this->entityManager->getRepository(User::class)->findOneBy([
                    'title' => $title,
                    'lastname' => $record["lastname"],
                    'firstname' => $record["firstname"],
                    'email' => $record["email"]
                ]);

                if ($oldCustomer === null) {
                    $lastname = !empty($record["lastname"]) ? $record["lastname"] : "No_lastname";
                    $firstname = !empty($record["firstname"]) ? $record["firstname"] : "No_firstname";
                    $birthday = !empty($record["birthday"]) ? DateTime::createFromFormat('Y-m-d',$record["birthday"]) : null;
                    $orders = !empty($record["order_id"])  ? $this->entityManager->getRepository(Order::class)->findBy(['id' => $record["order_id"]]) : [];
                    $civilities = !empty($record["civility_id"])  ? $this->entityManager->getRepository(Civility::class)->findBy(['id' => $record["civility_id"]]) : [];
                    $resources = !empty($record["resource_id"]) ? $this->entityManager->getRepository(Resource::class)->findBy(['id' => $record["resource_id"]]) : [];

                    $user->setTitle($title);
                    $user->setLastname($lastname);
                    $user->setFirstname($firstname);
                    $user->setFirstname($firstname);
                    $user->setMobile($record["mobile"] ?? "");
                    $user->setDateOfBirth( $birthday);
                    $user->setPhoto($record["photo"] ?? "");
                    $user->setPostalCode($record["postal_code"] ?? "");
                    $user->setCity($record["city"] ?? "");
                    $user->setEmail($record["email"] ?? "");
//                    foreach ($orders as $order) {
//                        $user->addOrder($order);
//                    }
//                    foreach ($civilities as $civility) {
//                        $user->addCivility($civility);
//                    }
//                    foreach ($resources as $resource) {
//                        $user->addResources($resource);
//                    }

                    $this->entityManager->persist($user);
                }
            }
        }
        $this->entityManager->flush();
        $output->writeln('Customers imported successfully.');

        return Command::SUCCESS;
    }
}
