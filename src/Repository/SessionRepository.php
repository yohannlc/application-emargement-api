<?php

namespace App\Repository;

use App\Entity\Session;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Session>
 *
 * @method Session|null find($id, $lockMode = null, $lockVersion = null)
 * @method Session|null findOneBy(array $criteria, array $orderBy = null)
 * @method Session[]    findAll()
 * @method Session[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Session::class);
    }

    public function save(Session $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Session $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getSessionsDuJour(): array
    {
        $today = new \DateTime();
        $qb = $this->createQueryBuilder('s');
        $qb->select('s.id', 's.heureDebut', 's.heureFin', 'm.matiere as matiere', 'GROUP_CONCAT(sa.salle SEPARATOR ", ") as salles', 'GROUP_CONCAT(CONCAT(UPPER(st.nom)," ",st.prenom) SEPARATOR ", ") AS intervenants', 'GROUP_CONCAT(g.groupe SEPARATOR ", ") as groupes');
        $qb->where('s.date = :dateDuJour');
        $qb->leftJoin('s.idMatiere', 'm');
        $qb->leftJoin('s.idSalle', 'sa');
        $qb->leftJoin('s.idStaff', 'st');
        $qb->leftJoin('s.idGroupe', 'g');
        $qb->setParameter('dateDuJour', '2023-02-03');
        $qb->groupBy('s.id');
        $qb->orderBy('s.heureDebut', 'ASC');
        $query = $qb->getQuery();
        return $query->getArrayResult();
    }
        


//    /**
//     * @return Session[] Returns an array of Session objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Session
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
