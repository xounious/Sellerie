<?php

namespace App\Controller;

use App\Entity\Loan;
use App\Entity\Borrower;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/borrower')]
final class BorrowerController extends AbstractController
{
    #[Route('/{id}', name: 'borrower_show', methods: ['GET'])]
    public function show(Borrower $borrower, EntityManagerInterface $entityManager, Request $request): Response
    {
        return $this->render('borrower/show.html.twig', [
            'employee' => $this->getUser(),
            'borrower' => $borrower,
            'loans' => $borrower->getLoans(),
        ]);
    }
}
