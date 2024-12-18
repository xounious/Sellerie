<?php

namespace App\Controller;

use App\Entity\Status;
use App\Entity\Storage;
use App\Entity\Building;
use App\Entity\Equipment;
use App\Form\StorageType;
use App\Form\EquipmentType;
use Doctrine\DBAL\Query\Limit;
use App\Repository\EquipmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/equipment')]
final class EquipmentController extends AbstractController
{
    #[Route(name: 'equipments', methods: ['GET'])]
    public function index(EquipmentRepository $equipmentRepository): Response
    {
        return $this->render('equipment/index.html.twig', [
            'employee' => $this->getUser(),
            'equipments' => $equipmentRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_equipment_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $equipment = new Equipment();
        $form = $this->createForm(EquipmentType::class, $equipment);
        $formStorage = $this->createForm(StorageType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($equipment);
            $entityManager->flush();

            return $this->redirectToRoute('equipments', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('equipment/new.html.twig', [
            'employee' => $this->getUser(),
            'equipment' => $equipment,
            'form' => $form,
            'formStorage' => $formStorage,
            'buildings' => $entityManager->getRepository(Building::class)->findAll(),
            'laneLettersGroupedByBuilding' => $entityManager->getRepository(Building::class)->getLaneLettersGroupedByBuilding(),
        ]);
    }

    #[Route('/{id}', name: 'app_equipment_show', methods: ['GET'])]
    public function show(Request $request, Equipment $equipment, EntityManagerInterface $entityManager): Response
    {
        return $this->render('equipment/show.html.twig', [
            'employee' => $this->getUser(),
            'equipment' => $equipment,
            'loans' => $equipment->getLoans(),
            'status' => $equipment->getStatus(),
            'allStatus' => $entityManager->getRepository(Status::class)->findAll(),
            'succes' => $request->get('succes'),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_equipment_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Equipment $equipment, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EquipmentType::class, $equipment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_equipment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('equipment/edit.html.twig', [
            'equipment' => $equipment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_equipment_delete', methods: ['POST'])]
    public function delete(Request $request, Equipment $equipment, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$equipment->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($equipment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_equipment_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/modifyStatus', name: 'equipment_modify_status', methods: ['POST'])]
    public function modifyStatus(Request $request, Equipment $equipment, EntityManagerInterface $entityManager): Response
    {
        if ($request->get('status')) {
            $status = $entityManager->getRepository(Status::class)->find($request->get('status'));
            $equipment->setStatus($status);
            $entityManager->persist($equipment);
            $entityManager->flush();
            return $this->redirectToRoute('app_equipment_show', ['id' => $equipment->getId(), 'succes' => 'Le statut à été modifié en "'.$status->getName().'".'], Response::HTTP_SEE_OTHER);
        }
    }
}
