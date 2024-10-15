<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Loan;
use App\Entity\Status;
use App\Entity\Storage;
use App\Entity\Borrower;
use App\Entity\Building;
use App\Entity\Employee;
use App\Entity\Equipment;
use App\Entity\BorrowerType;
use App\Entity\EquipmentCategory;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;


class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // ###########################################
        //       ajout des 2 types d'emprunteurs
        // ###########################################
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

        // ###########################################
        //       ajout des 20 employés dont 1 admin
        // ###########################################
        for ($i = 0; $i < 19; $i++) {
            $employee = new Employee();
            if ($i === 0) {
                // email de test rapide
                $employee->setEmail('test@test.fr');
            } else {
                $employee->setEmail($faker->email);
            }
            $employee->setFirstname($faker->firstName);
            $employee->setLastname($faker->lastName);
            $employee->setPassword(password_hash('password', PASSWORD_BCRYPT));
            if ($i === 0) {
                $employee->setRoles(['ROLE_ADMIN']);
            } else {
                $employee->setRoles(['ROLE_EMPLOYEE']);
            }
            $employee->setBorrower($borrowers[$i]);
            $manager->persist($employee);
        }

        // ###########################################
        //       ajout des buildings
        // ###########################################
        $buildings = [];
        $names = ['Atelier', 'Sellerie', 'Magasin', 'Bureau', 'Cantine'];
        for ($i = 0; $i < 5; $i++) {
            $building = new Building();
            $building->setName($names[$i]);
            $buildings[] = $building;
            $manager->persist($building);
        }


        // ###########################################
        //       ajout des storages
        // ###########################################
        $storages = [];
        $laneLetters = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
        for ($i = 0; $i < 3; $i++) {
            for ($j = 0; $j < 5; $j++) {
                $storage = new Storage();
                $storage->setBuilding($buildings[$i]);
                $storage->setLaneLetter($laneLetters[$faker->numberBetween(0, 6)]);
                $storage->setLaneNumber($j);
                $storages[] = $storage;
                $manager->persist($storage);
            }
        }

        // ###########################################
        //       ajout des catégories d'équipements
        // ###########################################
        $categories = [];
        $names = ['Outils', 'Consommables', 'Vêtements', 'Selles', 'Brides', 'Mors', 'Etriers', 'Bottes', 'Casques'];
        for ($i = 0; $i < 9; $i++) {
            $category = new EquipmentCategory();
            $category->setName($names[$i]);
            $categories[] = $category;
            $manager->persist($category);
        }

        // ###########################################
        //       ajout des status d'équipements
        // ###########################################
        $status = [];
        $names = ['Neuf', 'Bon état', 'Usé', 'En réparation', 'A réparer', 'Hors service'];
        for ($i = 0; $i < 6; $i++) {
            $stat = new Status();
            $stat->setName($names[$i]);
            $status[] = $stat;
            $manager->persist($stat);
        }

        // ###########################################
        //       ajout des équipements
        // ###########################################
        $sizes = ['S', 'M', 'L', 'XL', 'XXL'];
        $equipments = [];
        for ($i = 0; $i < 100; $i++) {
            $equipment = new Equipment();
            $equipment->setStorage($storages[$faker->numberBetween(0, 14)]);
            $equipment->setStatus($status[$faker->numberBetween(0, 5)]);
            $equipment->setCategory($categories[$faker->numberBetween(0, 8)]);
            $equipment->setName($faker->word);
            $equipment->setDescription($faker->sentence(10));
            $equipment->setSize($sizes[$faker->numberBetween(0, 4)]);
            $equipment->setStockQuantity($faker->numberBetween(0, 10));
            $equipments[] = $equipment;
            $manager->persist($equipment);
        }

        // ###########################################
        //       ajout des Prêts
        // ###########################################
        $loans = [];
        for ($i = 0; $i < 100; $i++) {
            $loan = new Loan();
            $startDate = $faker->dateTimeBetween('-1 months', '+3 months');
            $loan->setStartDate($startDate);
            $loan->setEndDate($faker->dateTimeBetween($startDate, '+5 months'));
            $loan->setBorrower($borrowers[$i%19]);
            $loan->setEquipment($equipments[$faker->numberBetween(0, 99)]);
            $loans[] = $loan;
            $manager->persist($loan);
        }



        $manager->flush();
    }
}
