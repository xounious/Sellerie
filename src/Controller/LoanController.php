<?php

namespace App\Controller;

use App\Entity\Loan;
use App\Entity\Borrower;
use App\Entity\Customer;
use App\Entity\Employee;
use App\Entity\Equipment;
use App\Entity\BorrowerType;
use App\Entity\EquipmentLogs;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/pret')]
final class LoanController extends AbstractController
{
    #[Route('/reservation', name: 'nouvelle_reservation', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('GET')) {
            return $this->render('loan/new.html.twig', [
                'equipment' => $entityManager->getRepository(Equipment::class)->find($request->get('equipment_id')),
                'customers' => $entityManager->getRepository(Customer::class)->findAll(),
                'employees' => $entityManager->getRepository(Employee::class)->findAll(),
                'employee' => $this->getUser(),
            ]);
        }

        if (!$request->request->get('start_date') || !$request->request->get('end_date')) {
            return $this->redirectToRoute('nouvelle_reservation', ['equipment_id' => $request->get('equipment_id')]);
        }
        if (new \DateTime($request->request->get('start_date')) > new \DateTime($request->request->get('end_date'))) {
            return $this->redirectToRoute('nouvelle_reservation', ['equipment_id' => $request->get('equipment_id')]);
        }

        $loan = new Loan();
        $loan->setStartDate(new \DateTime($request->request->get('start_date')));
        $loan->setEndDate(new \DateTime($request->request->get('end_date')));

        $borrowerType = $request->request->get('borrower_type');
        if ($borrowerType === 'employee') {
            $employee = $entityManager->getRepository(Employee::class)->find($request->request->get('employee'));
            $loan->setBorrower($employee->getBorrower());
        } elseif ($borrowerType === 'customer') {
            $customerChoice = $request->request->get('customer_choice');
            if ($customerChoice === 'select') {
                $customer = $entityManager->getRepository(Customer::class)->find($request->request->get('customer'));
                $borrower = $customer->getBorrower();
                $loan->setBorrower($borrower);
            } elseif ($customerChoice === 'create') {
                $customer = new Customer();
                $customer->setFirstName($request->request->get('customer_first_name'));
                $customer->setLastName($request->request->get('customer_last_name'));
                $customer->setPhone($request->request->get('customer_phone'));
                $entityManager->persist($customer);

                $borrower = new Borrower();
                $borrower->setType($entityManager->getRepository(BorrowerType::class)->findOneBy(['name' => 'Customer']));
                $entityManager->persist($borrower);
                $customer->setBorrower($borrower);
                $loan->setBorrower($borrower);
            }
        }

        $equipment = $entityManager->getRepository(Equipment::class)->find($request->request->get('equipment_id'));
        $loan->setEquipment($equipment);

        $log = new EquipmentLogs();
        $log->setEquipment($equipment);
        $log->setAction('Nouveau prêt');
        $log->setCreatedAt(new \DateTimeImmutable());
        $log->setStock($equipment->getStockQuantity());
        $entityManager->persist($log);

        $entityManager->persist($loan);
        $entityManager->flush();

        return $this->redirectToRoute('borrower_show', ['id' => $loan->getBorrower()->getId()]);
    }
}
