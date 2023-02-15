<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

use App\Controller\ApiController;

use App\Entity\Salle;

#[Route('/api/v1.0', name: 'api_')]
class ApiSalleController extends AbstractController
{
    private $doctrine;
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * Récupérer la liste des salles
     * 
     * @OA\Response(
     *    response=200,
     *    description="Retourne la liste des salles",
     *    @OA\JsonContent(
     *       type="array",
     *       @OA\Items(
     *         type="object",
     *         @OA\Property(property="id", type="integer"),
     *         @OA\Property(property="salle", type="string")
     *       )
     *    )
     * )
     * 
     * @OA\Tag(name="Salle")
     */
    #[Route('/salles', name: 'salle',methods: ['GET'])]
    public function getAllSalles(): Response
    {
        $salles = $this->doctrine->getRepository(Salle::class)->findAll();

        $response = new Response();
        $response->setContent(json_encode($salles));
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }
}


?>