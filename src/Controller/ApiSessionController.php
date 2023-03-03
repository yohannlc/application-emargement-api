<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Constraints\DateTimeInterface;
use Doctrine\ORM\Query\ResultSetMapping;

use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

use App\Entity\Session;
use App\Entity\Etudiant;
use App\Entity\Matiere;
use App\Entity\Type;
use App\Entity\Groupe;
use App\Entity\Salle;
use App\Entity\Staff;


use DateTime;

#[Route('/api/v1.0', name: 'api_')]
class ApiSessionController extends AbstractController{
    private $doctrine;
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /** 
     * Récupérer une session en fonction de son id
     * 
     * @OA\Response(
     *   response=200,
     *   description="Retourne la session en fonction de son id",
     *   @OA\JsonContent(
     *     type="object",
     *     @OA\Property(property="id", type="int"),
     *     @OA\Property(property="date", type="string"),
     *     @OA\Property(property="heureDebut", type="string"),
     *     @OA\Property(property="heureFin", type="string"),
     *     @OA\Property(property="matiere", type="string"),
     *     @OA\Property(property="type", type="string"),
     *     @OA\Property(property="salles", type="string"),
     *     @OA\Property(property="intervenants", type="string"),
     *     @OA\Property(property="groupes", type="string"),
     *   )
     * )
     * 
     * @OA\Parameter(
     *   name="id",
     *   in="path",
     *   description="Id de la session",
     *   required=true,
     *   @OA\Schema(type="integer")
     * )
     * 
     * @OA\Tag(name="Session")
     */
    #[Route('/session/{id}', name: 'session', methods: ['GET'])]
    public function getSession($id): Response
    {
        $session = $this->doctrine->getRepository(Session::class)->getSessionById($id);

        $response = new Response();
        $response->setContent(json_encode($session));
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }

    /**
     * Récupérer les sessions en fonction des paramètres
     * 
     * @OA\Response(
     *    response=200,
     *    description="Retourne la liste des sessions en fonction des paramètres",
     *    @OA\JsonContent(
     *       type="object",
     *          @OA\Property(property="id", type="int"),
     *          @OA\Property(property="date", type="string"),
     *          @OA\Property(property="heureDebut", type="string"),
     *          @OA\Property(property="heureFin", type="string"),     
     *          @OA\Property(property="matiere", type="string"),
     *          @OA\Property(property="type", type="string"),     
     *          @OA\Property(property="salles", type="string"),
     *          @OA\Property(property="intervenants", type="string"),
     *          @OA\Property(property="groupes", type="string"),
     *    )
     * )
     * 
     * @OA\Parameter(
     *   name="date",
     *   in="path",
     *   description="Date au format YYYY-MM-JJ. Si inutilisé, mettre à 0",
     *   required=true,
     *   @OA\Schema(type="string")
     * )
     * 
     * @OA\Parameter(
     *   name="idGroupe",
     *   in="path",
     *   description="Id du groupe. Si inutilisé, mettre à 0",
     *   required=true,
     *   @OA\Schema(type="integer")
     * )
     * 
     * @OA\Parameter(
     *   name="idIntervenant",
     *   in="path",
     *   description="Id de l'intervenant. Si inutilisé, mettre à 0",
     *   required=true,
     *   @OA\Schema(type="integer")
     * )
     * 
     * @OA\Parameter(
     *   name="idMatiere",
     *   in="path",
     *   description="Id de la matière. Si inutilisé, mettre à 0",
     *   required=true,  
     *   @OA\Schema(type="integer")
     * )
     * 
     * @OA\Parameter(
     *   name="idSalle",
     *   in="path",
     *   description="Id de la salle. Si inutilisé, mettre à 0",
     *   required=true,
     *   @OA\Schema(type="integer")
     * )
     * 
     * @OA\Tag(name="Session")
     */
    #[Route('/sessions/date={date}/groupe={idGroupe}/matiere={idMatiere}/intervenant={idIntervenant}/salle={idSalle}', name: 'sessions', methods: ['GET'])]
    public function getSessions($date=null,$idGroupe=null,$idIntervenant=null,$idMatiere=null, $idSalle=null): Response
    {
        $sessions = $this->doctrine->getRepository(Session::class)->getSessions($date,$idGroupe,$idIntervenant,$idMatiere, $idSalle);

        $response = new Response();
        $response->setContent(json_encode($sessions));
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }

    /**
     * Création d'une session
     * 
     * @OA\Response(
     *    response=201,
     *    description="Session créée"
     * )
     * 
     * @OA\Response(
     *   response=400,
     *   description="Requete invalide"
     * )
     * 
     * @OA\RequestBody(
     *    @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="date", type="date"),
     *       @OA\Property(property="heure_debut", type="string", format="time", example="08:00:00"),
     *       @OA\Property(property="heure_fin", type="string", format="time", example="10:00:00"),
     *       @OA\Property(property="id_matiere", type="integer"),
     *       @OA\Property(property="type", type="string"),
     *       @OA\Property(property="idGroupes", type="array", @OA\Items(type="integer")),
     *       @OA\Property(property="idSalles", type="array", @OA\Items(type="integer")),
     *       @OA\Property(property="idIntervenants", type="array", @OA\Items(type="integer"))
     *    )
     * )
     * 
     * @OA\Tag(name="Session")
     */
    #[Route('/session/create', name: 'create_session',methods: ['POST'])]
    public function createSession(Request $request){
        $entityManager = $this->doctrine->getManager();
        $data = json_decode($request->getContent(), true);        
        // Création de la session
        $session = new Session();

        // Variables pour la génération du code d'emargement
        $longueur = 15;                    
        $caracteres = ',;:!#@^ABCDEFGHIJKLMNOPQRSTUVWXYZ,;:!#@^abcdefghijklmnopqrstuvwxyz,;:!#@^0123456789,;:!#@^';

        // Vérification des données de la requête
        if($data['date'] == null && !preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $data['date'])) {
            throw new BadRequestHttpException("La date n'est pas valide");
        }elseif($data['heure_debut'] == null && !preg_match('/^[0-9]{2}:[0-9]{2}:[0-9]{2}$/', $data['heure_debut'])){
            throw new BadRequestHttpException("L'heure de début n'est pas valide");
        }elseif($data['heure_fin'] == null && !preg_match('/^[0-9]{2}:[0-9]{2}:[0-9]{2}$/', $data['heure_fin'])){
            throw new BadRequestHttpException("L'heure de fin n'est pas valide");
        }elseif($data['id_matiere'] == null && !preg_match('/^[0-9]+$/', $data['id_matiere'])){
            throw new BadRequestHttpException("La matière n'est pas valide");
        }elseif($data['type'] == null && !preg_match('/^[a-zA-Z]+$/', $data['type'])){
            throw new BadRequestHttpException("Le type n'est pas valide");
        }elseif($data['idGroupes'] == null && !preg_match('/^[0-9]+$/', $data['idGroupes'])){
            throw new BadRequestHttpException("Le groupe n'est pas valide");
        }elseif($data['idSalles'] == null && !preg_match('/^[0-9]+$/', $data['idSalles'])){
            throw new BadRequestHttpException("La salle n'est pas valide");
        }elseif($data['idIntervenants'] == null && !preg_match('/^[0-9]+$/', $data['idIntervenants'])){
            throw new BadRequestHttpException("L'intervenant n'est pas valide");
        }else{
            // Récupération des données de la requête
            $date = new DateTime($data['date']);
            $heureDebut = new DateTime($data['heure_debut']);
            $heureFin = new DateTime($data['heure_fin']);
            $idMatiere = $entityManager->getRepository(Matiere::class)->find($data['id_matiere']);
            $type = $entityManager->getRepository(Type::class)->find($data['type']);
            $idGroupes = $data['idGroupes'];
            $idSalles = $data['idSalles'];
            $idIntervenants = $data['idIntervenants'];

            $session->setDate($date);
            $session->setHeureDebut($heureDebut);
            $session->setHeureFin($heureFin);
            $session->setIdMatiere($idMatiere);
            $session->setType($type);
            foreach($idSalles as $idSalle){
                $session->addIdSalle($entityManager->getRepository(Salle::class)->find($idSalle));
            }
            foreach($idIntervenants as $idIntervenant){
                $session->addIdStaff($entityManager->getRepository(Staff::class)->find($idIntervenant));
            }

            $sql = "UPDATE `participe` SET `presence` = '0', `code_emargement` = CASE `ine` ";
            $params = array();

            foreach($idGroupes as $idGroupe){
                $groupe = $entityManager->getRepository(Groupe::class)->find($idGroupe);
                $session->addIdGroupe($groupe);
                $etudiants = $entityManager->getRepository(Etudiant::class)->getEtudiantsByGroupe($idGroupe);

                $codes_emargement = array();
                
                foreach($etudiants as $etudiant){
                    $code_emargement = substr(str_shuffle(str_repeat($caracteres, $longueur)), 0, $longueur);
                    $codes_emargement[$etudiant['ine']] = $code_emargement;
                    $etudiant = $entityManager->getRepository(Etudiant::class)->find($etudiant['ine']);
                    $session->addIne($etudiant);
                    
                }
                $entityManager->persist($session);
                $entityManager->flush();

                foreach ($etudiants as $etudiant) {
                    $sql .= "WHEN :ine{$etudiant['ine']} THEN :code_emargement{$etudiant['ine']} ";
                    $params["ine{$etudiant['ine']}"] = $etudiant['ine'];
                    $params["code_emargement{$etudiant['ine']}"] = $codes_emargement[$etudiant['ine']];
                }
            }
            $sql .= "END WHERE `id_session` = :id_session";
            $params['id_session'] = $session->getId();
            $stmt = $entityManager->getConnection()->prepare($sql);
            $stmt->execute($params);
        }
        $response = new Response();
        $response->setStatusCode(Response::HTTP_CREATED);
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }

    // Supprimer une session
    /**
     * Suppression d'une session
     * 
     * @OA\Response(
     *    response=200,
     *    description="Session supprimée"
     * )
     * 
     * @OA\Response(
     *   response=400,
     *   description="Requete invalide"
     * )
     * 
     * @OA\RequestBody(
     *   @OA\JsonContent(
     *      type="object",
     *      @OA\Property(property="id", type="integer")
     *   )
     * )
     * 
     * @OA\Tag(name="Session")
     */
    #[Route('/session/suppression', name: 'suppression_session',methods: ['DELETE'])]
    public function suppressionSession(Request $request){
        $entityManager = $this->doctrine->getManager();

        $data = json_decode($request->getContent(), true);

        $id = $data['id'];

        $session = $entityManager->getRepository(Session::class)->find($id);

        if($session == null){
            throw new BadRequestHttpException("La session n'existe pas");
        }else{
            $entityManager->remove($session);
            $entityManager->flush();
        }

        $response = new Response();
        $response->setStatusCode(Response::HTTP_OK);
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }
}