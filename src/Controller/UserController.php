<?php

namespace App\Controller;

use App\Entity\User;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserController extends AbstractController
{

    public function __construct(
        #[Autowire(service:'doctrine.orm.default_entity_manager')] private EntityManagerInterface $em
    ){

    }

    #[Route('/user', name: 'app_user')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/UserController.php',
        ]);
    }

    #[Route('/user/create', name: 'user_create', methods:['POST'])]
    public function createUser(Request $request,UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $this->em->beginTransaction();
        try {
            $content = json_decode($request->getContent(),true);
             if(!$content['email'] || !$content['first_name'] || !$content['last_name']) return new JsonResponse('Faltan parámetros',412);

            $emailBD = $this->em->getRepository(User::class)->findBy(['email' => $content['email']]);
            if(count($emailBD) > 0)  return new JsonResponse('Ya existe una cuenta con ese email',500);

            $user = new User();
            $user->setActive(true);
            $user->setFirstName($content['first_name']);
            $user->setIsSuperuser(false);
            $user->setLastName($content['last_name']);
            $user->setPhone($content['phone']);
            $user->setAddressLine1($content['address_line1']);
            isset($content['address_line2']) ? $user->setAddressLine2($content['address_line2']) : $user->setAddressLine2(null);
            $user->setCity($content['city']);
            $user->setStateProvince($content['state_province']);
            $user->setPostalCode($content['postal_code']);
            $user->setCountry($content['country']);
            $user->setOrganization($content['organization']);
            $user->setDepartment($content['department']);
            $user->setPositionTitle($content['position_title']);
            $user->setCreatedAt(DateTimeImmutable::createFromFormat("Y-m-d", date("Y-m-d")));
            $user->setDocumentNumber($content['document_number']);
            $user->setDocumentType($content['document_type']);
            $user->setEmail($content['email']);
            $user->setRole('ROLE_USER');
            $plaintextPassword = $content['password'];
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $plaintextPassword
            );
            $user->setPassword($hashedPassword);
            $this->em->persist($user);
            $this->em->flush();
            $this->em->commit();
            return new JsonResponse('Usuario creado con éxito',201);
        } catch (Exception $e) {
            $this->em->rollback();
            return new JsonResponse('Error al guardar el usuario'.$e->getMessage(),500);
        }
    }
}
