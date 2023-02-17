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
        $groupe = $this->doctrine->getRepository(Groupe::class)->findOneby(['id' => $id]);
        $groupe = [
            'id' => $groupe->getId(),
            'groupe' => $groupe->getGroupe()
        ];

        $response = new Response();
        $response->setContent(json_encode($groupe));
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
     * @OA\Response(
     *   response=400,
     *   description="Le groupe existe déjà ou le nom de groupe est manquant",
     * )     
     *  
     * 
     * @OA\RequestBody(
     *   required=true,
     *   description="Nom du groupe",
     *   @OA\JsonContent(
     *     type="object",
     *     @OA\Property(property="nom",type="string")
     *   )
     * )
     * 
     * @OA\Tag(name="Groupe")
     */
    #[Route('/groupe/creation', name: 'groupe',methods: ['POST'])]
    public function createGroupe(Request $request): Response
    {
        $response = new Response();
        $response->setStatusCode(Response::HTTP_CREATED);
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');


        $data = json_decode($request->getContent(), true);
        $nom = $data['nom'];

        if (!$nom) {
            throw new BadRequestHttpException('Nom de groupe manquant');
        }
    
        $groupe = new Groupe();
        $groupe->setGroupe($nom);
        $test_existance = $this->doctrine->getRepository(Groupe::class)->findOneBy(['groupe' => $nom]);
        if ($test_existance) {
            throw new BadRequestHttpException('Ce groupe existe déjà');
        }
    
        $entityManager = $this->doctrine->getManager();
        $entityManager->persist($groupe);
        $entityManager->flush();
    
        return $response;
    }

    /**
     * Ajouter des étudiants à un groupe
     *
     * @OA\Response(
     *  response=201,
     *   description="Etudiants ajoutés au groupe"
     * )
     *
     * @OA\RequestBody(
     *     description="Données à envoyer",
     *     required=true,
     *     @OA\JsonContent(
     *         required={"idGroupe","ines"},
     *         @OA\Property(property="idGroupe", type="integer", example="1"),
     *         @OA\Property(property="ines", type="array", @OA\Items(type="string", example="A12345"))
     *     )
     * )
     *
     * @OA\Tag(name="Groupe")
     */
    #[Route('/groupe/etudiant/ajout', name: 'groupe_ajout_etudiants', methods: ['POST'])]
    public function addEtudiantsToGroupe(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $idGroupe = $data['idGroupe'];
        $ines = $data['ines'];

        $groupe = $this->doctrine->getRepository(Groupe::class)->find($idGroupe);

        foreach ($ines as $ine) {
            echo $ine;
            $etudiant = $this->doctrine->getRepository(Etudiant::class)->findOneBy(['ine' => $ine]);

            if (!$etudiant) {
                throw $this->createNotFoundException(sprintf('L\'étudiant avec INE %s n\'a pas été trouvé.', $ine));
            }

            $groupe->addEtudiant($etudiant);
        }

        $entityManager = $this->doctrine->getManager();
        $entityManager->persist($groupe);
        $entityManager->flush();

        $response = new Response();
        $response->setStatusCode(Response::HTTP_CREATED);
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Methods', 'POST,GET,OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type');
        $response->headers->set('Access-Control-Allow-Origin', '*');

        return $response;
    }

    /**
     * Supprimer un étudiant d'un groupe
     * 
     * @OA\Response(
     *   response=204,
     *   description="Etudiant supprimé du groupe"
     * )
     * 
     * @OA\Response(
     *   response=404,
     *   description="Groupe ou étudiant introuvable"
     * )
     * 
     * @OA\RequestBody(
     *   request="SuppressionEtudiant",
     *   required=true,
     *   @OA\JsonContent(
     *     @OA\Property(property="idGroupe", type="integer"),
     *     @OA\Property(property="ines", type="array", @OA\Items(type="string", example="A12345"))
     *   )
     * )
     * 
     * @OA\Tag(name="Groupe")
     */
    #[Route('/groupe/etudiant/suppression', name: 'groupe_suppression_etudiant',methods: ['DELETE'])]
    public function removeEtudiantFromGroupe(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $idGroupe = $data['idGroupe'];
        $ines = $data['ines'];

        foreach ($ines as $ine) {
            $groupe = $this->doctrine->getRepository(Groupe::class)->findOneBy(['id' => $idGroupe]);
            if (!$groupe) {
                throw $this->createNotFoundException(sprintf('Groupe non trouvé'));
            }
        
            $etudiant = $this->doctrine->getRepository(Etudiant::class)->findOneBy(['ine' => $ine]);
            if (!$etudiant) {
                throw $this->createNotFoundException(sprintf('Etudiant non trouvé'));
            }
        
            if (!$groupe->hasEtudiant($etudiant)) {
                throw new BadRequestHttpException('Cet étudiant n\'appartient pas à ce groupe');
            }
        
            $groupe->removeEtudiant($etudiant);
        }
        $entityManager = $this->doctrine->getManager();
        $entityManager->persist($groupe);
        $entityManager->flush();
    
        $response = new Response();
        $response->setStatusCode(Response::HTTP_NO_CONTENT);
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Methods', 'DELETE,GET,OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type');
        $response->headers->set('Access-Control-Allow-Origin', '*');
    
        return $response;
    }





}
?>