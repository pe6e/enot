<?php

namespace App\Repository;

use App\Entity\ChangeRequest;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ChangeRequest>
 *
 * @method ChangeRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChangeRequest|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChangeRequest[]    findAll()
 * @method ChangeRequest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChangeRequestRepository extends ServiceEntityRepository
{
    const LIFE_TIME_MESSAGE = 30;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChangeRequest::class);
    }

    public function add(ChangeRequest $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ChangeRequest $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function removeOldRequests(): void
    {
        $this->createQueryBuilder('r')
            ->delete()
            ->where('r.dateCreate < :currentTime')
            ->getQuery()
            ->setParameter('currentTime', (new DateTime())
                ->setTimestamp(time() - self::LIFE_TIME_MESSAGE))
            ->execute();
    }
}
