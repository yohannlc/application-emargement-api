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

    public function getSessions($date,$idGroupe,$idIntervenant,$idMatiere,$idSalle): array
    {
        $qb = $this->createQueryBuilder('s');
        $qb->select('s.id',"DATE_FORMAT(s.date,'%Y-%m-%d') AS date", "DATE_FORMAT(s.heureDebut,'%H:%i') as heureDebut", "DATE_FORMAT(s.heureFin,'%H:%i') AS heureFin", 'm.matiere', 't.type', "GROUP_CONCAT(DISTINCT sa.salle SEPARATOR ', ') as salles", "GROUP_CONCAT(DISTINCT CONCAT(UPPER(st.nom),' ',st.prenom) SEPARATOR ', ') AS intervenants", "GROUP_CONCAT(DISTINCT g.groupe SEPARATOR ', ') as groupes");
        if($date != 0){
            $qb->where('s.date = :dateDuJour');
            $qb->setParameter('dateDuJour', $date);
        }
        if($idGroupe != 0){
            $qb->andWhere('g.id = :groupe');
            $qb->setParameter('groupe', $idGroupe);
        }
        if($idIntervenant != 0){
            $qb->andWhere('st.id = :intervenant');
            $qb->setParameter('intervenant', $idIntervenant);
        }
        if($idMatiere != 0){
            $qb->andWhere('m.id = :matiere');
            $qb->setParameter('matiere', $idMatiere);
        }
        if($idSalle != 0){
            $qb->andWhere('sa.id = :salle');
            $qb->setParameter('salle', $idSalle);
        }
        $qb->leftJoin('s.idMatiere', 'm');
        $qb->leftJoin('s.idSalle', 'sa');
        $qb->leftJoin('s.idStaff', 'st');
        $qb->leftJoin('s.idGroupe', 'g');
        $qb->leftJoin('s.type', 't');
        $qb->groupBy('s.id');
        $qb->orderBy('s.heureDebut', 'ASC');
        $query = $qb->getQuery();
        $results = $query->getArrayResult();

        $newResults = [];
        /* 
         * Si on a un filtre sur un groupe, intervenant, matière ou salle,
         * il faut de nouveau récupérer les sessions à partir de leurs id
         * car sinon on ne récupère pas certaines informations
         * Ex : si on filtre par le groupe 1, et qu'une session a le groupe 1 et 2, on ne récupère pas le groupe 2
         */
        if($idGroupe != 0 || $idIntervenant != 0 || $idMatiere != 0 || $idSalle != 0){
            foreach($results as &$result){
                array_push($newResults, $this->getSessionById($result['id'])[0]);
            }
            return $newResults;
        }

        // Convertir les chaînes en tableaux
        foreach ($results as &$result) {
            $result['intervenants'] = explode(', ', $result['intervenants']);
            $result['salles'] = explode(', ', $result['salles']);
            $result['groupes'] = explode(', ', $result['groupes']);
        }

        return $results;
    }

    public function getSessionById($id): array
    {
        $qb = $this->createQueryBuilder('s');
        $qb->select('s.id',"DATE_FORMAT(s.date,'%Y-%m-%d') AS date", "DATE_FORMAT(s.heureDebut,'%H:%i') as heureDebut", "DATE_FORMAT(s.heureFin,'%H:%i') AS heureFin", 'm.matiere', 't.type', "GROUP_CONCAT(DISTINCT sa.salle SEPARATOR ', ') as salles", "GROUP_CONCAT(DISTINCT CONCAT(UPPER(st.nom),' ',st.prenom) SEPARATOR ', ') AS intervenants", "GROUP_CONCAT(DISTINCT g.groupe SEPARATOR ', ') as groupes");
        $qb->where('s.id = :id');
        $qb->setParameter('id', $id);
        $qb->leftJoin('s.idMatiere', 'm');
        $qb->leftJoin('s.idSalle', 'sa');
        $qb->leftJoin('s.idStaff', 'st');
        $qb->leftJoin('s.idGroupe', 'g');
        $qb->leftJoin('s.type', 't');
        $qb->groupBy('s.id');
        $qb->orderBy('s.heureDebut', 'ASC');
        $query = $qb->getQuery();
        $results = $query->getArrayResult();

        // Convertir les chaînes en tableaux
        foreach ($results as &$result) {
            $result['intervenants'] = explode(', ', $result['intervenants']);
            $result['salles'] = explode(', ', $result['salles']);
            $result['groupes'] = explode(', ', $result['groupes']);
        }

        return $results;
    }

    public function getTodaySessionsByIntervenant($id){
        $qb = $this->createQueryBuilder('s');
        $qb->select('s.id',"DATE_FORMAT(s.date,'%Y-%m-%d') AS date", "DATE_FORMAT(s.heureDebut,'%H:%i') as heureDebut", "DATE_FORMAT(s.heureFin,'%H:%i') AS heureFin", 'm.matiere', 't.type', "GROUP_CONCAT(DISTINCT sa.salle SEPARATOR ', ') as salles", "GROUP_CONCAT(DISTINCT CONCAT(UPPER(st.nom),' ',st.prenom) SEPARATOR ', ') AS intervenants", "GROUP_CONCAT(DISTINCT g.groupe SEPARATOR ', ') as groupes");
        $qb->where('s.date = :dateDuJour');
        $qb->andWhere('st.id = :intervenant');
        $qb->setParameter('dateDuJour', date('Y-m-d'));
        $qb->setParameter('intervenant', $id);
        $qb->leftJoin('s.idMatiere', 'm');
        $qb->leftJoin('s.idSalle', 'sa');
        $qb->leftJoin('s.idStaff', 'st');
        $qb->leftJoin('s.idGroupe', 'g');
        $qb->leftJoin('s.type', 't');
        $qb->groupBy('s.id');
        $qb->orderBy('s.heureDebut', 'ASC');
        $query = $qb->getQuery();
        $results = $query->getArrayResult();

        $newResults = [];
        foreach($results as &$result){
            array_push($newResults, $this->getSessionById($result['id'])[0]);
        }
        return $newResults;
    }

    public function getTodaySessionsByEtudiant($ineEtudiant){
        $qb = $this->createQueryBuilder('s');
        $qb->select('s.id',"DATE_FORMAT(s.date,'%Y-%m-%d') AS date", "DATE_FORMAT(s.heureDebut,'%H:%i') as heureDebut", "DATE_FORMAT(s.heureFin,'%H:%i') AS heureFin", 'm.matiere', 't.type', "GROUP_CONCAT(DISTINCT sa.salle SEPARATOR ', ') as salles", "GROUP_CONCAT(DISTINCT CONCAT(UPPER(st.nom),' ',st.prenom) SEPARATOR ', ') AS intervenants", "GROUP_CONCAT(DISTINCT g.groupe SEPARATOR ', ') as groupes");
        $qb->where('s.date = :dateDuJour');
        $qb->andWhere('e.ine = :ineEtudiant');
        $qb->setParameter('dateDuJour', date('Y-m-d'));
        $qb->setParameter('ineEtudiant', $ineEtudiant);
        $qb->leftJoin('s.idMatiere', 'm');
        $qb->leftJoin('s.idSalle', 'sa');
        $qb->leftJoin('s.idStaff', 'st');
        $qb->leftJoin('s.idGroupe', 'g');
        $qb->leftJoin('s.type', 't');
        $qb->leftJoin('g.ine', 'e');
        $qb->groupBy('s.id');
        $qb->orderBy('s.heureDebut', 'ASC');
        $query = $qb->getQuery();
        $results = $query->getArrayResult();

        $newResults = [];
        foreach($results as &$result){
            array_push($newResults, $this->getSessionById($result['id'])[0]);
        }
        return $newResults;
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
