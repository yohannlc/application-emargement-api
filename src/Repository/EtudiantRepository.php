<?php

namespace App\Repository;

use App\Entity\Etudiant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Etudiant>
 *
 * @method Etudiant|null find($id, $lockMode = null, $lockVersion = null)
 * @method Etudiant|null findOneBy(array $criteria, array $orderBy = null)
 * @method Etudiant[]    findAll()
 * @method Etudiant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EtudiantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Etudiant::class);
    }

    // Récupère tous les étudiants d'un groupe et les renvoie sous forme de tableau
    public function getEtudiantsByGroupe($id_groupe): array
    {
        return $this->createQueryBuilder('et')
            ->leftJoin('et.idGroupe', 'g')
            ->andWhere('g.id = :id_groupe')
            ->setParameter('id_groupe', $id_groupe)
            ->getQuery()
            ->getArrayResult();
    }    

    // Récupère tous les étudiants dont le nom ou le prénom contient la chaîne de caractères passée en paramètre
    public function getEtudiantsByNomPrenom($nom_prenom): array
    {
        return $this->createQueryBuilder('et')
            ->select('et.nom', 'et.prenom', 'p.promo')
            ->leftJoin('et.promo', 'p')
            ->andWhere('et.nom LIKE :nom_prenom')
            ->orWhere('et.prenom LIKE :nom_prenom')
            ->setParameter('nom_prenom', '%' . $nom_prenom . '%')
            ->getQuery()
            ->getArrayResult();
    }

    // Récupérer l'étudiant dont l'ine est passé en paramètre
    public function findOneByIne($ine): ?Etudiant
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.ine = :ine')
            ->setParameter('ine', $ine)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function save(Etudiant $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Etudiant $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Etudiant[] Returns an array of Etudiant objects
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

//    public function findOneBySomeField($value): ?Etudiant
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
