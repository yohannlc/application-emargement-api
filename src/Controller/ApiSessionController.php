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

        $date = new DateTime($data['date']);
        $heureDebut = new DateTime($data['heure_debut']);
        $heureFin = new DateTime($data['heure_fin']);
        $idMatiere = $entityManager->getRepository(Matiere::class)->find($data['id_matiere']);
        $type = $entityManager->getRepository(Type::class)->find($data['type']);
        $idGroupes = $data['idGroupes'];
        $idSalles = $data['idSalles'];
        $idIntervenants = $data['idIntervenants'];

        $session = new Session();

        if($date == null){
            throw new BadRequestHttpException("La date n'est pas valide");
        }elseif($heureDebut == null){
            throw new BadRequestHttpException("L'heure de début n'est pas valide");
        }elseif($heureFin == null){
            throw new BadRequestHttpException("L'heure de fin n'est pas valide");
        }elseif($idMatiere == null){
            throw new BadRequestHttpException("La matière n'est pas valide");
        }elseif($type == null){
            throw new BadRequestHttpException("Le type n'est pas valide");
        }elseif($idGroupes == null){
            throw new BadRequestHttpException("Le groupe n'est pas valide");
        }elseif($idSalles == null){
            throw new BadRequestHttpException("La salle n'est pas valide");
        }elseif($idIntervenants == null){
            throw new BadRequestHttpException("L'intervenant n'est pas valide");
        }else{
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
            foreach($idGroupes as $idGroupe){
                $groupe = $entityManager->getRepository(Groupe::class)->find($idGroupe);
                $session->addIdGroupe($groupe);
                $etudiants = $entityManager->getRepository(Etudiant::class)->getEtudiantsByGroupe($idGroupe);

                // Création de la session et de son id
                $entityManager->persist($session);
                $entityManager->flush();
                
                foreach($etudiants as $etudiant){
                    $etudiant = $entityManager->getRepository(Etudiant::class)->find($etudiant['ine']);

                    $session->addIne($etudiant);
                    $entityManager->persist($session);
                    $entityManager->flush();

                    // Génération du code d'emargement
                    $caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789,;:!#@^';
                    $longueur = 15;
                    $code_emargement = substr(str_shuffle(str_repeat($caracteres, $longueur)), 0, $longueur);

                    //Insertion dans la table participe du code d'emargement
                    $sql = "UPDATE `participe` SET `presence` = '0', `code_emargement` = :code_emargement WHERE `participe`.`ine` = :ine AND `participe`.`id_session` = :id_session";
                    $statement = $entityManager->getConnection()->prepare($sql);
                    $statement->execute([
                        'ine' => $etudiant->getIne(),
                        'id_session' => $session->getId(),
                        'code_emargement' => $code_emargement                        
                    ]);
                    

                    
                    //var_dump($result); 

                    // $statement->execute([
                    //     'ine' => $etudiant->getIne(),
                    //     'id_session' => ($entityManager->getRepository(Session::class)->findLastSession()),
                    //     'presence' => 0,
                    //     'code_emargement' => $code_emargement                        
                    // ]);

                }
            }
        }
        //$entityManager->flush();

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