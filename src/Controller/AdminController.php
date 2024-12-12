<?php

namespace App\Controller;

use App\Entity\Loan;
use App\Entity\Status;
use App\Entity\Employee;
use App\Entity\Equipment;
use App\Form\EmployeeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'employee' => $this->getUser(),
        ]);
    }

    #[Route('/employees/{message}', name: 'app_gestion_employees')]
    public function employees(EntityManagerInterface $entityManager, string $message=null): Response
    {
        if ($message == null) {
            $message = "";
        }
        return $this->render('admin/employees.html.twig', [
            'employee' => $this->getUser(),
            'employees' => $entityManager->getRepository(Employee::class)->findAll(),
            'message' => $message,
        ]);
        dd($message);
    }

    #[Route('/deleteEmployee/{id}', name: 'app_delete_employee')]
    public function deleteEmployee(Employee $employee, EntityManagerInterface $entityManager): Response
    {
        $name = $employee->getFirstname() . " " . $employee->getLastname();
        $entityManager->remove($employee);
        $entityManager->flush();

        return $this->redirectToRoute('app_gestion_employees', [
            'message' => "L'employé $name a bien été supprimé.",
        ]);
    }

    #[Route('/editEmployee/{id}', name: 'app_edit_employee')]
    public function editEmployee(Request $request, Employee $employee, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EmployeeType::class, $employee);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            dd($employee);
            $data = $form->getData();
            $employee->setFirstname($data->getFirstname());
            $employee->setLastname($data->getLastname());
            $employee->setEmail($data->getEmail());
            dd($employee);
            $entityManager->persist($employee);
            $entityManager->flush();

            return $this->redirectToRoute('app_gestion_employees', [
                'message' => "L'employé a bien été modifié.",
            ]);
        }

        return $this->render('admin/editEmployee.html.twig', [
            'form' => $form->createView(),
            'employee' => $employee,
        ]);
    }

    #[Route('/statistiquesInventaire', name: 'app_statistiques_inventaire')]
    public function statistiquesInventaire(EntityManagerInterface $entityManager): Response
    {
        $status = $entityManager->getRepository(Status::class)->findAll();
        $statistiquesStatus = [];
        foreach ($status as $s) {
            $statistiquesStatus[$s->getName()] = count($s->getEquipment());
        }

        $equipments = $entityManager->getRepository(Equipment::class)->findAll();
        $statistiquesStorage = [];
        $statistiquesLoaned = [
            'Prêts en cours' => 0,
            'Prêts à venir' => 0,
            'Total des prêts' => 0,
        ];
        foreach ($equipments as $e) {
            if (isset($statistiquesStorage[$e->getStorage()->getBuilding()->getName()])) {
                $statistiquesStorage[$e->getStorage()->getBuilding()->getName()]++;
            } else {
                $statistiquesStorage[$e->getStorage()->getBuilding()->getName()] = 1;
            }
            $loans = $e->getLoans();
            foreach ($loans as $l) {
                if ($l->getEndDate() > new \DateTime() && $l->getStartDate() < new \DateTime()) {
                    $statistiquesLoaned['Prêts en cours']++;
                } else if ($l->getEndDate() > new \DateTime()) {
                    $statistiquesLoaned['Prêts à venir']++;
                }
                $statistiquesLoaned['Total des prêts']++;
            }
        }
        return $this->render('admin/statistiquesInventaire.html.twig', [
            'statistiquesStatus' => $statistiquesStatus,
            'statistiquesStorage' => $statistiquesStorage,
            'statistiquesLoaned' => $statistiquesLoaned,
            'employee' => $this->getUser(),
        ]);
    }
}
