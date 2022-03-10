<?php

namespace App\Repository;

use App\Entity\Wiki;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Wiki|null find($id, $lockMode = null, $lockVersion = null)
 * @method Wiki|null findOneBy(array $criteria, array $orderBy = null)
 * @method Wiki[]    findAll()
 * @method Wiki[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WikiRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Wiki::class);
    }
}
