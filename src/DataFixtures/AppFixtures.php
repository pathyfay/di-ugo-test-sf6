<?php

namespace App\DataFixtures;

use App\Entity\FileToUser;
use App\Entity\FilterToFile;
use App\Entity\User;
use App\Entity\Civility;
use App\Enum\FileStatusEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $users = [];
        $roles = [['ROLE_USER', 'ROLE_ADMIN'], ['ROLE_USER'], ['ROLE_ADMIN']];
        $civilityOptions = [['nom' => 'Mr', 'desc' => 'Monsieur'], ['nom' => 'Mme', 'desc' => 'Madame'], ['nom' => 'Mlle', 'desc' => 'Mademoiselle']];
        $civility = new Civility();
        $civility->setCode($faker->randomElement($civilityOptions)['nom']);
        $civility->setLabel($faker->randomElement($civilityOptions)['desc']);
        $random = strtoupper(bin2hex(random_bytes(5)));
        $matriculate = 'M-' . substr($random, 0, 5) . '-' . substr($random, 5, 5);

        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setUuid($faker->uuid());
            $user->setEmail($faker->firstName() . "@gmail.com");
            $user->setPassword($this->hasher->hashPassword($user, 'password'));
            $user->setFirstname($faker->firstName());
            $user->setLastname($faker->lastName());
            $user->setPhone($faker->phoneNumber());
            $user->setMobile($faker->phoneNumber());
            $user->setCity($faker->city());
            $user->setPostalCode($faker->postcode());
            $user->setStreetAddress($faker->streetAddress());
            $user->setCivility($civility);
            $user->setRoles($faker->randomElement($roles));
            $user->setMatriculate($matriculate);
            $user->setDateOfBirth($faker->dateTimeBetween('-50 years', '-18 years'));
            $users[] = $user;
            $manager->persist($user);
        }

        $arrFileModels = [];
        for ($l = 0; $l < 5; $l++) {
            $filterToFile = new FilterToFile();
            $filterToFile->setName($faker->word());
            $filterToFile->setRegex($faker->regexify('/[A-Za-z0-9]{5,10}/'));
            $filterToFile->setStatus($faker->randomElement(['active', 'inactive']));
            $filterToFile->setPriority($faker->numberBetween(1, 10));
            $filterToFile->setDateCreated($faker->dateTimeBetween('-90 days', '-10 days'));
            $arrFileModels[] = $filterToFile;
            $manager->persist($filterToFile);
        }

        for ($j = 0; $j < 45; $j++) {
            $file = new FileToUser();
            $ext = $faker->randomElement(['pdf', 'png', 'jpg', 'docx']);
            $file->setFilename("document_{$j}.{$ext}");
            $file->setFileType($ext);
            $file->setFilterToFile($arrFileModels[array_rand($arrFileModels)]);
            $file->setDateCreated($faker->dateTimeBetween('-10 days', '-3 days'));
            $file->setDateUpdated($faker->dateTimeBetween('-2 days', 'now'));
            $file->setFileStatus($faker->randomElement(FileStatusEnum::cases()));
            $file->setFileSize($faker->numberBetween(1000, 1000000));
            $file->setFilePath($faker->filePath());
            $file->setExtension($ext);

            $assignedUsers = $faker->randomElements($users, rand(1, 3));
            foreach ($assignedUsers as $user) {
                $file->addUser($user);
            }

            $file->setLastModifiedBy($faker->randomElement($assignedUsers));

            $manager->persist($file);
        }

        $manager->flush();
    }
}
