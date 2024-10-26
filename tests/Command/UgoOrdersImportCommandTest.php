<?php

namespace App\Tests\Command;

use App\Command\UgoCustomersImportCommand;
use App\Command\UgoOrdersImportCommand;
use App\Command\UgoPurchasesImportCommand;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Application;

class UgoOrdersImportCommandTest extends KernelTestCase
{
    /**
     * @var string|false
     */
    private string|false $customersFile;

    /**
     * @var string|false
     */
    private string|false $purchasesFile;

    /**
     * @var CommandTester
     */
    public CommandTester $commandTester;

    /**
     * @return void
     */
    public function setUp(): void
    {
        self::bootKernel();
        $application = new Application();
        $entityManager = self::$kernel->getContainer()->get('doctrine.orm.entity_manager');
        $customersImportCommand = new UgoCustomersImportCommand($entityManager);
        $purchasesImportCommand = new UgoPurchasesImportCommand($entityManager);
        $application->add(new UgoOrdersImportCommand($entityManager, $customersImportCommand, $purchasesImportCommand));
        $application->add($customersImportCommand);
        $application->add($purchasesImportCommand);

        $this->commandTester = new CommandTester($application->find('ugo:orders:import'));
        $this->customersFile = tempnam(sys_get_temp_dir(), 'customers.csv');
        $this->purchasesFile = tempnam(sys_get_temp_dir(), 'purchasesFile.csv');
    }

    /**
     * @return void
     */
    public function testExecuteWithNonOptions()
    {
        file_put_contents($this->customersFile, "customer_id,title,lastname,firstname,postal_code,city,email\n1,1,Doe,John,12345,Paris,john@example.com");
        file_put_contents($this->purchasesFile, "customer_id;purchase_identifier;product_id,quantity,price,currency,date\n1,2023-10-01;prod1,2,10.00,USD,2023-10-01");
        $this->commandTester->execute([
                'customersFile' => $this->customersFile,
                'purchasesFile' => $this->purchasesFile
            ]
        );

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('You must specify at least one of the options: --customers or --purchases.', $output);

        unlink($this->customersFile);
        unlink($this->purchasesFile);
    }

    /**
     * @return void
     */
    public function testExecuteWithMissingCustomersFile()
    {
        file_put_contents($this->purchasesFile, "customer_id;purchase_identifier;product_id,quantity,price,currency,date\n1,2023-10-01;prod1,2,10.00,USD,2023-10-01");
        $this->commandTester->execute([
            'customersFile' => '',
            'purchasesFile' => $this->purchasesFile,
            '--customers' => true,
            '--purchases' => true
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('You must add a csv customer.csv.', $output);

        unlink($this->customersFile);
    }

    /**
     * @return void
     */
    public function testExecuteWithMissingPuchasesFile()
    {
        file_put_contents($this->customersFile, "customer_id,title,lastname,firstname,postal_code,city,email\n1,1,Doe,John,12345,Paris,john@example.com");
        $this->commandTester->execute([
            'customersFile' => $this->customersFile,
            'purchasesFile' => '',
            '--customers' => true,
            '--purchases' => true
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('You must add a csv purchases.csv.', $output);

        unlink($this->customersFile);
    }

    /**
     * @return void
     */
    public function testExecuteWithValidFiles()
    {
        file_put_contents($this->customersFile, "customer_id,title,lastname,firstname,postal_code,city,email\n1,1,Doe,John,12345,Paris,john@example.com");
        file_put_contents($this->purchasesFile, "customer_id;purchase_identifier;product_id,quantity,price,currency,date\n1,2023-10-01;prod1,2,10.00,USD,2023-10-01");
        $this->commandTester->execute([
            'customersFile' => $this->customersFile,
            'purchasesFile' => $this->purchasesFile,
            '--customers' => true,
            '--purchases' => true
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Data imported successfully.', $output);

        unlink($this->customersFile);
        unlink($this->purchasesFile);
    }
}
