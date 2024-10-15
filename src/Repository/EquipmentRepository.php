<?php

namespace App\Repository;

use App\Entity\Equipment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Equipment>
 */
class EquipmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Equipment::class);
    }

    //    /**
    //     * @return Equipment[] Returns an array of Equipment objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Equipment
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function displayTable(): string
    {
        $s = '<tr>';
        $s .= '<td>'.$this->getName().'</td>';
        $s .= '<td>'.$this->getDescription().'</td>';
        $s .= '<td>'.$this->getSize().'</td>';
        $s .= '<td>'.$this->getStockQuantity().'</td>';
        $s .= '<td>'.$this->getCategory().'</td>';
        $s .= '<td>'.$this->getStatus().'</td>';
        $s .= '<td>'.$this->getStorage().'</td>';
        $s .= '<td><a href="/equipment/'.$this->getId().'">Show</a></td>';
        $s .= '<td><a href="/equipment/'.$this->getId().'/edit">Edit</a></td>';
        $s .= '</tr>';
        return $s;
    }
}
