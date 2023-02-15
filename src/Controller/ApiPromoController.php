<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

use App\Entity\Promo;


#[Route('/api/v1.0', name: 'api_')]
class ApiPromoController extends AbstractController
{
    private $doctrine;
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * Récupérer la liste des promotions
     * 
     * @OA\Response(
     *    response=200,
     *    description="Retourne la liste des promotions",
     *    @OA\JsonContent(
     *       type="array",
     *       @OA\Items(
     *         type="object",
     *         @OA\Property(property="promo", type="integer")
     *       )
     *    )
     * )
     * 
     * @OA\Tag(name="Promo")
     */
    #[Route('/promos', name: 'promo',methods: ['GET'])]
    public function getAllPromos(): Response
    {
        $promos = $this->doctrine->getRepository(Promo::class)->findAll();

        $response = new Response();
        $response->setContent(json_encode($promos));
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }
    
}
