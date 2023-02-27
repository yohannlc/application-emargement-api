<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

use App\Entity\Matiere;

#[Route('/api/v1.0', name: 'api_')]
class ApiMatiereController extends AbstractController{
    private $doctrine;
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * Récupérer les types de matières
     * 
     * @OA\Response(
     *    response=200,
     *    description="Retourne la liste des matières",
     *    @OA\JsonContent(
     *       type="array",
     *       @OA\Items(
     *         type="object",
     *           @OA\Property(property="id", type="integer"),
     *           @OA\Property(property="matiere", type="string")
     *       )
     *    )
     * )
     * 
     * @OA\Tag(name="Matiere")
     */
    #[Route('/matieres', name: 'matiere',methods: ['GET'])]
    public function getAllMatieres(): Response
    {
        $matieres = $this->doctrine->getRepository(Matiere::class)->findAll();

        $response = new Response();
        $response->setContent(json_encode($matieres));
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }


    
}