<?php

namespace App\Controller;

use App\Entity\Loan;
use App\Entity\Borrower;
use App\Entity\Customer;
use App\Entity\Employee;
use App\Entity\Equipment;
use App\Entity\BorrowerType;
use App\Repository\LoanRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/loan')]
final class LoanController extends AbstractController
{
    #[Route(name: 'app_loan_index', methods: ['GET'])]
    public function index(LoanRepository $loanRepository): Response
    {
        return $this->render('loan/index.html.twig', [
            'loans' => $loanRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_loan_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $equipment = $entityManager->getRepository(Equipment::class)->find($request->get('equipment_id'));

        $startDate = $request->get('startDate');
        $endDate = $request->get('endDate');

        if ($startDate && $endDate) {
            $loan = new Loan();
            $loan->setEquipment($equipment);
            $loan->setStartDate(new \DateTime($startDate));
            $loan->setEndDate(new \DateTime($endDate));
            if ($request->get('typeBorrower') == 'customer') {
                $customer = null;
                if ($request->get('customerSelect') == 'default') {
                    $customer = new Customer();
                    $customer->setFirstName($request->get('firstName'));
                    $customer->setLastName($request->get('lastName'));
                    $customer->setPhone($request->get('phone'));
                    $entityManager->persist($customer);

                    $borrower = new Borrower();
                    $borrower->setType($entityManager->getRepository(BorrowerType::class)->findBy(['name' => 'customer'])[0]);
                    $borrower->setCustomer($customer);
                    $customer->setBorrower($borrower);
                    $entityManager->persist($borrower);
                    $entityManager->flush();
                } else {
                    $customer = $entityManager->getRepository(Customer::class)->find($request->get('customerSelect'));
                }
                $loan->setBorrower($customer->getBorrower());
            } else if ($request->get('typeBorrower') == 'employee') {
                $employee = $entityManager->getRepository(Employee::class)->find($request->get('employeeSelect'));
                $loan->setBorrower($employee->getBorrower());
            }
            $entityManager->persist($loan);
            $entityManager->flush();

            return $this->redirectToRoute('app_equipment_show', ['id' => $equipment->getId(), 'succes' => 'La réservation pour le produit a été ajoutée avec succès.'], Response::HTTP_SEE_OTHER);
        }

        return $this->render('loan/new.html.twig', [
            'equipment' => $equipment,
            'customers' => $entityManager->getRepository(Customer::class)->findAll(),
            'employees' => $entityManager->getRepository(Employee::class)->findAll(),
            'employee' => $this->getUser(),
        ]);
    }

    #[Route('/{id}', name: 'app_loan_show', methods: ['GET'])]
    public function show(Loan $loan): Response
    {
        return $this->render('loan/show.html.twig', [
            'loan' => $loan,
        ]);
    }

    #[Route('/{id}', name: 'app_loan_delete', methods: ['POST'])]
    public function delete(Request $request, Loan $loan, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$loan->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($loan);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_loan_index', [], Response::HTTP_SEE_OTHER);
    }
}
