<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

use App\Entity\Staff;

#[Route('/api/v1.0', name: 'api_')]
class ApiStaffController extends AbstractController{
    private $doctrine;
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * Récupérer les intervenants
     * 
     * @OA\Response(
     *    response=200,
     *    description="Retourne la liste des intervenants",
     *    @OA\JsonContent(
     *     type="array",
     *       @OA\Items(
     *        type="object",
     *          @OA\Property(property="id", type="int"),
     *          @OA\Property(property="nom", type="string")
     *      )
     *    )
     * )
     * 
     * @OA\Tag(name="Staff")
     */
    #[Route('/intervenants', name: 'staff',methods: ['GET'])]
    public function getAllIntervenants(): Response
    {
        $staff = $this->doctrine->getRepository(Staff::class)->getAllIntervenants();

        $response = new Response();
        $response->setContent(json_encode($staff));
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }

    
    
}