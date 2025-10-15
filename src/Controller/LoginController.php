<?php

namespace App\Controller;

use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class LoginController extends AbstractController
{
    private $JWTManager;

    public function __construct(JWTTokenManagerInterface $JWTManager,#[Autowire(service:'doctrine.orm.default_entity_manager')] private EntityManagerInterface $em)
    {
        $this->JWTManager = $JWTManager;
    }

    public function getTokenUser(UserInterface $user)
    {
        return $this->JWTManager->create($user);
    }

    #[Route('/api/login', name: 'api_login', methods:['POST'])]
    public function index(#[CurrentUser] ?Users $user)
    {
        if (null === $user) {
            return $this->json([
                'message' => 'missing credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }
        $token = $this->getTokenUser($user);
        $user->setLastLoginAt(new \DateTimeImmutable());
        $this->em->persist($user);
        $this->em->flush();
        return $this->json([
            'token' => $token
        ]);
    }
}
