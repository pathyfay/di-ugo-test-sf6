<?php

namespace App\Tests\Command;

use App\Command\UgoCustomersImportCommand;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class UgoCustomersImportCommandTest extends KernelTestCase
{
    /**
     * @var string|false
     */
    private string|false $customersFile;
    /**
     * @var CommandTester
     */
    public CommandTester $commandTester;

    /**
     * @return void
     */
    public function setUp() : void
    {
        self::bootKernel();
        $application = new Application();
        $application->add(new UgoCustomersImportCommand(self::$kernel->getContainer()->get('doctrine.orm.entity_manager')));

        $this->commandTester = new CommandTester($application->find('ugo:customers:import'));
        $this->customersFile = tempnam(sys_get_temp_dir(), 'customers.csv');
    }

    /**
     * @return void
     */
    public function testExecuteWithValidFile()
    {
        file_put_contents($this->customersFile, "id,title,lastname,firstname;postal_code,city,email\n1,1,Doe,John,12345,Paris,john@example.com");
        $this->commandTester->execute([
            'customersFile' => $this->customersFile,
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Customers imported successfully.', $output);

        unlink($this->customersFile);
    }

    /**
     * @return void
     */
    public function testExecuteWithEmptyHeader()
    {
        file_put_contents($this->customersFile, "1,1,Doe,John,12345,Paris,john@example.com\n2,1,Smith,Jane,67890,Lyon,jane@example.com");
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("File '{$this->customersFile}' not header.");
        $this->commandTester->execute([
            'customersFile' => $this->customersFile,
        ]);

        unlink($this->customersFile);
    }

    /**
     * @return void
     */
    public function testExecuteWithEmptyFile()
    {
        file_put_contents($this->customersFile, "");
        $exitCode = $this->commandTester->execute([
            'customersFile' => $this->customersFile,
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString("File '$this->customersFile' is empty.", $output);
        $this->assertSame(Command::FAILURE, $exitCode);

        unlink($this->customersFile);
    }


    /**
     * @return void
     */
    public function testExecuteWithNonExistentFile()
    {
        $this->commandTester->execute([
            'customersFile' => 'non_existent_file.csv',
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString("File 'non_existent_file.csv' not exist.", $output);
        $this->assertEquals(Command::FAILURE, $this->commandTester->getStatusCode());

    }
}
