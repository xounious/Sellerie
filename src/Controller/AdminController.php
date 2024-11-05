<?php

namespace App\Controller;

use App\Entity\Employee;
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
        $employee = new Employee();

        $form = $this->createForm(EmployeeType::class, $employee);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $employee = $form->getData();
            $employee->setRoles(['ROLE_EMPLOYEE']);
            $entityManager->persist($employee);
            $entityManager->flush();

            return $this->redirectToRoute('app_gestion_employees', [
                'message' => "L'employé a bien été modifié.",
            ]);
        }

        return $this->render('admin/editEmployee.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
