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
     *     @OA\Property(property="nom",type="string"),
     *     @OA\Property(property="ines",type="array",@OA\Items(type="string"))
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
        $response->headers->set('Access-Control-Allow-Origin', '*');


        $data = json_decode($request->getContent(), true);
        $nom = $data['nom'];
        $ines = $data['ines'];

        if (!$nom) {
            throw new BadRequestHttpException('Nom de groupe manquant');
        }
    
        $groupe = new Groupe();
        $groupe->setGroupe($nom);
        $test_existance = $this->doctrine->getRepository(Groupe::class)->findOneBy(['groupe' => $nom]);
        if ($test_existance) {
            throw new BadRequestHttpException('Ce groupe existe déjà');
        }

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
     * Mise à jour d'un groupe
     * 
     * @OA\Response(
     *    response=200,
     *    description="Groupe mis à jour"
     * )
     * 
     * @OA\Response(
     *    response=400,
     *    description="Groupe introuvable"
     * )
     * 
     * @OA\RequestBody(
     *   request="MiseAJourGroupe",
     *   required=true,
     *   @OA\JsonContent(
     *     @OA\Property(property="id", type="integer"),
     *     @OA\Property(property="nom", type="string"),
     *     @OA\Property(property="ines", type="array", @OA\Items(type="string", example="A12345"))
     *   )
     * )
     * 
     * @OA\Tag(name="Groupe")
     */
    #[Route('/groupe/miseajour', name: 'groupe_miseajour', methods: ['PUT'])]
    public function updateGroupe(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $idGroupe = $data['id'];
        $nom = $data['nom'];
        $ines = $data['ines'];
    
        $groupe = $this->doctrine->getRepository(Groupe::class)->findOneBy(['id' => $idGroupe]);
        if (!$groupe) {
            throw $this->createNotFoundException(sprintf('Groupe non trouvé'));
        }
    
        $groupe->setGroupe($nom);

        // On supprime tous les étudiants du groupe
        $groupe->removeAllEtudiants();

        // Puis on ajoute les étudiants passés en paramètre    
        foreach ($ines as $ine) {
            $etudiant = $this->doctrine->getRepository(Etudiant::class)->findOneBy(['ine' => $ine]);
            if (!$etudiant) {
                throw $this->createNotFoundException(sprintf('Etudiant non trouvé'));
            }else{
                $groupe->addEtudiant($etudiant);
            }
        }
    
        $entityManager = $this->doctrine->getManager();
        $entityManager->persist($groupe);
        $entityManager->flush();
    
        $response = new Response();
        $response->setStatusCode(Response::HTTP_OK);
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Methods', 'PUT,GET,OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type');
        $response->headers->set('Access-Control-Allow-Origin', '*');
    
        return $response;
    }

        /** 
     * Supprimer un groupe
     * 
     * @OA\Response(
     *    response=204,
     *    description="Groupe supprimé"
     * )
     * 
     * @OA\Response(
     *    response=400,
     *    description="Groupe introuvable"
     * )
     * 
     * @OA\RequestBody(
     *   request="SuppressionGroupe",
     *   required=true,
     *   @OA\JsonContent(
     *     @OA\Property(property="id", type="integer")
     *   )
     * )
     * 
     * @OA\Tag(name="Groupe")
     */
    #[Route('/groupe/suppression', name: 'groupe_suppression', methods: ['DELETE'])]
    public function removeGroupe(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $idGroupe = $data['id'];
    
        $groupe = $this->doctrine->getRepository(Groupe::class)->findOneBy(['id' => $idGroupe]);
        if (!$groupe) {
            throw $this->createNotFoundException(sprintf('Groupe non trouvé'));
        }
    
        $entityManager = $this->doctrine->getManager();
        $entityManager->remove($groupe);
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