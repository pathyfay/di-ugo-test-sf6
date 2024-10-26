<?php

namespace App\Tests\Command;

use App\Command\UgoPurchasesImportCommand;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class UgoPurchasesImportCommandTest extends KernelTestCase
{
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
        $application->add(new UgoPurchasesImportCommand(self::$kernel->getContainer()->get('doctrine.orm.entity_manager')));

        $this->commandTester = new CommandTester($application->find('ugo:purchases:import'));
        $this->purchasesFile = tempnam(sys_get_temp_dir(), 'purchases.csv');
    }

    /**
     * @return void
     */
    public function testExecuteWithValidFile()
    {
        file_put_contents($this->purchasesFile, "customer_id;purchase_identifier;product_id,quantity,price,currency,date\n1,2023-10-01;prod1,2,10.00,USD,2023-10-01");
        $this->commandTester->execute([
            'purchasesFile' => $this->purchasesFile,
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsStringIgnoringCase('Purchases imported successfully.', $output);

        unlink($this->purchasesFile);
    }

    /**
     * @return void
     */
    public function testExecuteWithEmptyHeader()
    {
        file_put_contents($this->purchasesFile, "1;2023-10-01;prod1;2;10.00;USD;2023-10-01\n2;2023-10-02;prod2;1;15.00;EUR;2023-10-02");
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("File '{$this->purchasesFile}' not header.");
        $this->commandTester->execute([
            'purchasesFile' => $this->purchasesFile,
        ]);

        unlink($this->purchasesFile);
    }

    /**
     * @return void
     */
    public function testExecuteWithEmptyFile()
    {
        file_put_contents($this->purchasesFile, "");
        $exitCode = $this->commandTester->execute([
            'purchasesFile' => $this->purchasesFile,
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString("File '$this->purchasesFile' is empty.", $output);
        $this->assertSame(Command::FAILURE, $exitCode);

        unlink($this->purchasesFile);
    }

    /**
     * @return void
     */
    public function testExecuteWithNonExistentFile()
    {
        $this->commandTester->execute([
            'purchasesFile' => 'non_existent_file.csv',
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString("file 'non_existent_file.csv' not exist.", $output);
    }
}
