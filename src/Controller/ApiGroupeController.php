<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

use App\Controller\ApiController;

use App\Entity\Groupe;
use App\Entity\Etudiant;
use App\Repository\EtudiantRepository;
use App\Repository\GroupeRepository;


#[Route('/api/v1.0', name: 'api_')]
class ApiGroupeController extends AbstractController{
    private $doctrine;
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }
    
    /**
     * Récupérer la liste des groupes
     * 
     * @OA\Response(
     *    response=200,
     *    description="Retourne la liste des groupes",
     *    @OA\JsonContent(
     *       type="array",
     *       @OA\Items(
     *         type="object",
     *         @OA\Property(property="id", type="integer"),
     *         @OA\Property(property="groupe", type="string")
     *       )
     *    )
     * )
     * 
     * @OA\Tag(name="Groupe")
     */

    #[Route('/groupes', name: 'groupes',methods: ['GET'])]
    public function getAllGroupes(): Response
    {
        $groupes = $this->doctrine->getRepository(Groupe::class)->findAll();

        $response = new Response();
        $response->setContent(json_encode($groupes));
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }

    /**
     * Récupérer le groupe par id 
     * 
     * @OA\Response(
     *   response=200,
     *   description="Retourne le groupe par id",
     *   @OA\JsonContent(
     *     type="object",
     *     @OA\Property(property="id", type="integer"),
     *     @OA\Property(property="groupe", type="string")
     *   )
     * )
     * 
     * @OA\Tag(name="Groupe")
     */
    #[Route('/groupe/{id}', name: 'id',methods: ['GET'])]
    public function getGroupeById($id): Response{
        $groupe = $this->doctrine->getRepository(Groupe::class)->getGroupeById($id);

        $response = new Response();
        $response->setContent(json_encode($groupe[0]));
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }
    
    /**
     * Création d'un groupe
     * 
     * @OA\Response(
     *   response=201,
     *   description="Groupe créé"
     * )
     * 
     * @OA\Parameter(
     *   name="nom",
     *   in="path",
     *   description="Nom du groupe",
     *   required=true,
     *   @OA\Schema(
     *     type="string"
     *   )
     * )
     * 
     * 
     * @OA\Tag(name="Groupe")
     */
    #[Route('/groupe/{nom}', name: 'groupe',methods: ['POST'])]
    public function createGroupe($nom): Response{
        $groupe = new Groupe();
        $groupe->setGroupe($nom);
        $entityManager = $this->doctrine->getManager();
        $entityManager->persist($groupe);
        $entityManager->flush();

        $response = new Response();
        $response->setContent(json_encode($groupe));
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }

    /** 
     * Ajouter un étudiant à un groupe
     * 
     * @OA\Response(
     *  response=201,
     *   description="Etudiant ajouté au groupe"
     * )
     * 
     * @OA\Parameter(
     *  name="idGroupe",
     *  in="path",
     *  description="Id du groupe",
     *  required=true,
     *  @OA\Schema(
     *     type="integer"
     *  )
     * )
     * 
     * @OA\Parameter(
     *   name="ine",
     *   in="path",
     *   description="INE de l'étudiant",
     *   required=true,
     *   @OA\Schema(
     *     type="string"
     *   )
     * )
     * 
     * @OA\Tag(name="Groupe")
     */

    #[Route('/groupe/{idGroupe}/etudiant/{ine}', name: 'groupe_ajout_etudiant',methods: ['POST'])]
    public function addEtudiantToGroupe(int $idGroupe, string $ine): Response{
        $groupe = $this->doctrine->getRepository(Groupe::class)->findOneById($idGroupe);
        $etudiant = $this->doctrine->getRepository(Etudiant::class)->findOneByIne($ine);
        $groupe->addEtudiant($etudiant);
        $entityManager = $this->doctrine->getManager();
        $entityManager->persist($groupe);
        $entityManager->flush();

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }

    /**
     * Supprimer un étudiant d'un groupe
     * 
     * @OA\Response(
     *   response=201,
     *   description="Etudiant supprimé du groupe"
     * )
     * 
     * @OA\Parameter(
     *   name="idGroupe",
     *   in="path",
     *   description="Id du groupe",
     *   required=true,
     *   @OA\Schema(
     *     type="integer"
     *   )
     * )
     * 
     * @OA\Parameter(
     *   name="ine",
     *   in="path",
     *   description="INE de l'étudiant",
     *   required=true,
     *   @OA\Schema(
     *     type="string"
     *   )
     * )
     * 
     * @OA\Tag(name="Groupe")
     */
    #[Route('/groupe/{idGroupe}/etudiant/{ine}', name: 'groupe_suppression_etudiant',methods: ['DELETE'])]
    public function removeEtudiantToGroupe(int $idGroupe,string $ine){
        $groupe = $this->doctrine->getRepository(Groupe::class)->findOneById($idGroupe);
        $etudiant = $this->doctrine->getRepository(Etudiant::class)->findOneByIne($ine);
        $groupe->removeEtudiant($etudiant);
        $entityManager = $this->doctrine->getManager();
        $entityManager->persist($groupe);
        $entityManager->flush();

        $response = new Response();
        $response->setContent(json_encode($groupe));
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }





}
?>