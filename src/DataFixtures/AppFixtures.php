<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Borrower;
use App\Entity\Employee;
use App\Entity\BorrowerType;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;


class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // ajout des 2 types d'emprunteurs (client et employé)
        $typeCustomer = new BorrowerType();
        $typeCustomer->setName('customer');
        $manager->persist($typeCustomer);

        $typeEmployee = new BorrowerType();
        $typeEmployee->setName('employee');
        $manager->persist($typeEmployee);

        // ajout de 20 emprunteurs
        $borrowers = [];
        for ($i = 0; $i < 20; $i++) {
            $borrower = new Borrower();
            $borrower->setType($typeEmployee);
            $borrowers[] = $borrower;
        }

        //ajout de 20 employés dont 1 administrateur
        for ($i = 0; $i < 20; $i++) {
            $employee = new Employee();
            $employee->setEmail($faker->email);
            $employee->setFirstname($faker->firstName);
            $employee->setLastname($faker->lastName);
            $employee->setPassword('password');
            if ($i === 0) {
                $employee->setRoles(['ROLE_ADMIN']);
            } else {
                $employee->setRoles(['ROLE_EMPLOYEE']);
            }
            $employee->setBorrower($borrowers[$i]);
            $manager->persist($employee);
        }

        $manager->flush();
    }
}
