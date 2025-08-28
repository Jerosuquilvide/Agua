<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends AbstractController
{
    private $bienesPerVigenteRepository;
    private $kernel;
    private $entityManager;

    public function __construct() {}

    #[Route('/prueba', name: 'periodo_vigente', methods: ['GET'])]
    public function getPerVigente(): JsonResponse
    {
        return new JsonResponse('Hola mundo', 200);
    }
}