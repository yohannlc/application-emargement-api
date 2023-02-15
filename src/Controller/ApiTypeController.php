<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

use App\Entity\Type;

#[Route('/api/v1.0', name: 'api_')]
class ApiTypeController extends AbstractController
{
    private $doctrine;
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * Récupérer les types de cours
     * 
     * @OA\Response(
     *    response=200,
     *    description="Retourne la liste des types",
     *    @OA\JsonContent(
     *       type="array",
     *       @OA\Items(
     *         type="object",
     *         @OA\Property(property="type", type="string")
     *       )
     *    )
     * )
     * 
     * @OA\Tag(name="Type")
     */
    #[Route('/types', name: 'type',methods: ['GET'])]
    public function getAllTypes(): Response
    {
        $types = $this->doctrine->getRepository(Type::class)->findAll();

        $response = new Response();
        $response->setContent(json_encode($types));
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }
}

?>