<?php

namespace App\Controller;

use App\Entity\Loan;
use App\Entity\Status;
use App\Entity\Storage;
use App\Entity\Building;
use App\Entity\Equipment;
use App\Entity\EquipmentLogs;
use App\Entity\EquipmentCategory;
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
            'equipments' => $equipmentRepository->findBy([], ['name' => 'ASC']),
        ]);
    }

    #[Route('/new', name: 'nouvel_equipement', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('GET')) {
            return $this->render('equipment/new.html.twig', [
                'employee' => $this->getUser(),
                'statuses' => $entityManager->getRepository(Status::class)->findAll(),
                'categories' => $entityManager->getRepository(EquipmentCategory::class)->findAll(),
                'buildings' => $entityManager->getRepository(Building::class)->findAll(),
            ]);
        }
        if (!$request->request->get('name') || !$request->request->get('description') || !$request->request->get('stock') || !$request->request->get('status') || !$request->request->get('category') || !$request->request->get('building') || !$request->request->get('laneLetter') || !$request->request->get('laneNumber') || !$request->request->get('size')) {
            return $this->render('equipment/new.html.twig', [
                'employee' => $this->getUser(),
                'error' => 'Veuillez remplir tous les champs.',
                'statuses' => $entityManager->getRepository(Status::class)->findAll(),
                'categories' => $entityManager->getRepository(EquipmentCategory::class)->findAll(),
                'buildings' => $entityManager->getRepository(Building::class)->findAll(),
            ]);
        } else {
            if ($request->isMethod('POST')) {
                $equipment = new Equipment();
                $equipment->setName($request->request->get('name'));
                $equipment->setDescription($request->request->get('description'));
                $equipment->setStockQuantity($request->request->get('stock'));
                $equipment->setSize($request->request->get('size'));
                $equipment->setStatus($entityManager->getRepository(Status::class)->find($request->request->get('status')));
                $equipment->setCategory($entityManager->getRepository(EquipmentCategory::class)->find($request->request->get('category')));
                $storage = $entityManager->getRepository(Storage::class)->findOneBy(['building' => $request->request->get('building'), 'laneLetter' => $request->request->get('laneLetter'), 'laneNumber' => $request->request->get('laneNumber')]);

                $log = new EquipmentLogs();
                $log->setEquipment($equipment);
                $log->setAction('Création équipement');
                $log->setCreatedAt(new \DateTimeImmutable());
                $log->setStock($equipment->getStockQuantity());
                $entityManager->persist($log);
                $entityManager->flush();

                if (!$storage) {
                    $storage = new Storage();
                    $storage->setBuilding($entityManager->getRepository(Building::class)->find($request->request->get('building')));
                    $storage->setLaneLetter($request->request->get('laneLetter'));
                    $storage->setLaneNumber($request->request->get('laneNumber'));
                    $entityManager->persist($storage);
                    $entityManager->flush();
                }
                $equipment->setStorage($storage);
                $entityManager->persist($equipment);
                $entityManager->flush();
    
                return $this->redirectToRoute('interface_equipement', ['id' => $equipment->getId()]);
            }
        }
    }

    #[Route('/{id}', name: 'interface_equipement', methods: ['GET'])]
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

    #[Route('/{id}/modifyStatus', name: 'equipment_modify_status', methods: ['POST'])]
    public function modifyStatus(Request $request, Equipment $equipment, EntityManagerInterface $entityManager): Response
    {
        if ($request->get('status')) {
            $ancientStatus = $equipment->getStatus();
            $status = $entityManager->getRepository(Status::class)->find($request->get('status'));
            $equipment->setStatus($status);
            $entityManager->persist($equipment);
            
            $log = new EquipmentLogs();
            $log->setEquipment($equipment);
            $log->setAction('Modification statut: de '.$ancientStatus->getName().' à '.$status->getName());
            $log->setCreatedAt(new \DateTimeImmutable());
            $log->setStock($equipment->getStockQuantity());
            $entityManager->persist($log);
            $entityManager->flush();
            
            return $this->redirectToRoute('interface_equipement', ['id' => $equipment->getId(), 'succes' => 'Le statut à été modifié en "'.$status->getName().'".'], Response::HTTP_SEE_OTHER);
        }
    }

    #[Route('/{id}/rendreEquipement', name: 'rendre_un_equipement', methods: ['GET' ,'POST'])]
    public function ramenerEquipement(Request $request, Equipment $equipment, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('GET')) {
            return $this->render('equipment/return.html.twig', [
                'employee' => $this->getUser(),
                'equipment' => $equipment,
                'loans' => $equipment->getLoans(),
            ]);
        }
        if ($request->isMethod('POST')) {
            $equipment->setStockQuantity($equipment->getStockQuantity() + 1);
            $loan = $entityManager->getRepository(Loan::class)->find($request->request->get('loan'));
            $loan->setEndDate(new \DateTime());

            $log = new EquipmentLogs();
            $log->setEquipment($equipment);
            $log->setAction('Retour équipement');
            $log->setCreatedAt(new \DateTimeImmutable());
            $log->setStock($equipment->getStockQuantity());
            $entityManager->persist($log);

            $entityManager->flush();
            return $this->redirectToRoute('interface_equipement', ['id' => $equipment->getId()]);
        }
    }

    //afficher les logs d'un équipement
    #[Route('/{id}/historique', name: 'equipment_logs', methods: ['GET'])]
    public function showLogs(Equipment $equipment): Response
    {
        return $this->render('equipment/logs.html.twig', [
            'employee' => $this->getUser(),
            'equipment' => $equipment,
            'logs' => array_reverse($equipment->getEquipmentLogs()->toArray()),
        ]);
    }
}
