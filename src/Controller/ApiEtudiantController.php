<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

use App\Entity\Etudiant;

#[Route('/api/v1.0', name: 'api_')]
class ApiEtudiantController extends AbstractController{
    
    private $doctrine;
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * Autocomplétion des noms des étudiants
     * 
     * @OA\Response(
     *  response=200,
     *  description="Retourne la liste des étudiants qui matchent avec la chaîne de caractères entrée",
     *  @OA\JsonContent(
     *    type="array",
     *    @OA\Items(
     *     type="object",
     *        @OA\Property(property="nom", type="string"),
     *        @OA\Property(property="prenom", type="string"),
     *        @OA\Property(property="promo", type="int")
     *     )
     *   )
     * )
     * 
     * @OA\Tag(name="Etudiant")
     */
    #[Route('/etudiants/{chaine}', name: 'etudiants',methods: ['GET'])]
    public function getEtudiantsByNom($chaine): Response
    {
        $etudiants = $this->doctrine->getRepository(Etudiant::class)->getEtudiantsByNomPrenom($chaine);

        $response = new Response();
        $response->setContent(json_encode($etudiants));
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }

     /**
     * Récupérer les étudiants d'un groupe
     * 
     * @OA\Response(
     *    response=200,
     *    description="Retourne les étudiants d'un groupe",
     *    @OA\JsonContent(
     *       type="array",
     *       @OA\Items(
     *         type="object",
     *         @OA\Property(property="ine", type="string"),
     *         @OA\Property(property="identifiant", type="string"),
     *         @OA\Property(property="nom", type="string"),
     *         @OA\Property(property="prenom", type="string")
     *       )
     *    )
     * )
     * 
     * @OA\Tag(name="Etudiant")
     */
    #[Route('/etudiants/groupe/{id_groupe}', name: 'etudiants_groupe',methods: ['GET'])]
    public function getEtudiantsByGroupe($id_groupe): Response
    {
        $etudiants = $this->doctrine->getRepository(Etudiant::class)->getEtudiantsByGroupe($id_groupe);

        $response = new Response();
        $response->setContent(json_encode($etudiants));
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }
    
    /** 
     * Récupérer les informations d'un étudiant par son INE
     * 
     * @OA\Response(
     *   response=200,
     *   description="Retourne les informations d'un étudiant",
     *   @OA\JsonContent(
     *     type="object",
     *     @OA\Property(property="ine", type="string"),
     *     @OA\Property(property="identifiant", type="string"),
     *     @OA\Property(property="nom", type="string"),
     *     @OA\Property(property="prenom", type="string")
     *   )
     * )
     * 
     * @OA\Tag(name="Etudiant")
     */
    #[Route('/etudiant/{ine}', name: 'etudiant',methods: ['GET'])]
    public function getEtudiantByINE($ine): Response
    {
        $etudiant = $this->doctrine->getRepository(Etudiant::class)->getEtudiantByINE($ine);

        $response = new Response();
        $response->setContent(json_encode($etudiant[0]));
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }
    

}


?>