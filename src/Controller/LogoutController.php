<?php
    namespace App\Controller;

    use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

    class LogoutController extends AbstractController
    {
        #[Route('/api/logout', name: 'api_logout', methods: ['POST'])]
        public function logout(TokenStorageInterface $tokenStorage, JWTTokenManagerInterface $jwtManager): JsonResponse
        {
            $token = $tokenStorage->getToken();

            if ($token && $token->getCredentials()) {
                // Invalidate the current JWT
                $jwtManager->invalidateToken($token->getCredentials());
            }

            // Clear the token from the security context (optional, as it's stateless)
            $tokenStorage->setToken(null);

            return new JsonResponse(['message' => 'Successfully logged out']);
        }
    }
