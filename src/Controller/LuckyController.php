<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LuckyController
{
    private $entityManager;
    private $connection;
    public function __construct(ManagerRegistry $managerRegistry,Connection $connection)
    {
        // $this->entityManager = $managerRegistry->getManager('postgres');
        $this->connection = $connection;
    }


    #[Route('/prueba', name: 'app_lucky_number')]
    public function number()
    {
        $sql = "select * from users;";
        $result = $this->connection->fetchAllAssociative($sql);
        return new JsonResponse($result);
    }
}
