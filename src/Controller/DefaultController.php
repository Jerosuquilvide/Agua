<?php

namespace App\Controller;

use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends AbstractController
{
    public function __construct() {}

    #[Route('/api', name: 'api', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Retorna Hola mundo',
        content: new OA\JsonContent(
            type: 'string',
            example: 'Hola mundo'
        )
    )]
    public function api(): JsonResponse
    {
        return new JsonResponse('Hola mundo', 200);
    }
}