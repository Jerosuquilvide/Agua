<?php

namespace App\Controller;

use App\Entity\Users;
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

    #[Route('/api/users/list', name: 'list_users', methods:['GET'])]
    public function getUserList(EntityManagerInterface $em) {
        try {
            $userList = $this->em->getRepository(Users::class)->findAll();
            return new JsonResponse(array_map(fn($user) => $user->jsonSerialize(), $userList), 200);

        } catch (Exception $e) {
            return new JsonResponse('Error al buscar la lista de usuarios: '.$e->getMessage(), 500);
        }
    }

    #[Route('/api/users/{id}', name: 'get_user_info', methods:['GET'])]
    public function getUserInfo(int $id) {
        try {
            if(!$id){
                return new JsonResponse('El id del usuario es obligatorio', 400);
            }
            $user = $this->em->getRepository(Users::class)->find($id);
            if (!$user) {
                return new JsonResponse('Usuario no encontrado',404);
            }
            return new JsonResponse($user->jsonSerialize(), 200);
        } catch (Exception $e) {
            return new JsonResponse('Error al buscar el usuario. '.$e->getMessage() ,500);
        }
    }

    #[Route('/api/users/create', name: 'user_create', methods:['POST'])]
    public function createUser(Request $request,UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $this->em->beginTransaction();
        try {
            $content = json_decode($request->getContent(),true);
             if(!$content['email'] || !$content['first_name'] || !$content['last_name'] || !$content['username']) return new JsonResponse('Faltan parámetros',412);

            $emailBD = $this->em->getRepository(Users::class)->findBy(['email' => $content['email']]);
            if(count($emailBD) > 0)  return new JsonResponse('Ya existe una cuenta con ese email',500);

            $user = new Users();
            $user->setActive(true);
            $user->setUsername($content['username']);
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
            $user->setCreatedByUserId($this->getUser() !== null ? $this->getUser()->getId() : null);
            $user->setUpdatedAt(DateTimeImmutable::createFromFormat("Y-m-d", date("Y-m-d")));
            $user->setDocumentNumber($content['document_number']);
            $user->setDocumentType($content['document_type']);
            $user->setEmail($content['email']);
            $user->setRole(isset($content['role']) ? $content['role'] : 'ROLE_OPERARIO');
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

    #[Route('/api/users/{id}', name: 'update_user', methods:['PUT'])]
    public function updateUser(int $id, Request $request, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $this->em->beginTransaction();
        try {
            if(!$id){
                return new JsonResponse('El id del usuario es obligatorio', 400);
            }
            $user = $this->em->getRepository(Users::class)->find($id);
            if (!$user) {
                return new JsonResponse('Usuario no encontrado',404);
            }
            $content = json_decode($request->getContent(), true);
            if (!$content) {
                return new JsonResponse('Contenido inválido', 400);
            }
            // Actualizar los campos del usuario
            $user->setUsername(isset($content['username']) ? $content['username'] : $user->getUsername());
            $user->setFirstName(isset($content['first_name']) ? $content['first_name'] : $user->getFirstName());
            $user->setLastName(isset($content['last_name']) ? $content['last_name'] : $user->getLastName());
            $user->setEmail(isset($content['email']) ? $content['email'] : $user->getEmail());
            $user->setPhone(isset($content['phone']) ? $content['phone'] : $user->getPhone());
            $user->setAddressLine1(isset($content['address_line1']) ? $content['address_line1'] : $user->getAddressLine1());
            $user->setAddressLine2(isset($content['address_line2']) ? $content['address_line2'] : $user->getAddressLine2());
            $user->setCity(isset($content['city']) ? $content['city'] : $user->getCity());
            $user->setStateProvince(isset($content['state_province']) ? $content['state_province'] : $user->getStateProvince());
            $user->setPostalCode(isset($content['postal_code']) ? $content['postal_code'] : $user->getPostalCode());
            $user->setCountry(isset($content['country']) ? $content['country'] : $user->getCountry());
            $user->setOrganization(isset($content['organization']) ? $content['organization'] : $user->getOrganization());
            $user->setDepartment(isset($content['department']) ? $content['department'] : $user->getDepartment());
            $user->setPositionTitle(isset($content['position_title']) ? $content['position_title'] : $user->getPositionTitle());
            $user->setDocumentNumber(isset($content['document_number']) ? $content['document_number'] : $user->getDocumentNumber());
            $user->setDocumentType(isset($content['document_type']) ? $content['document_type'] : $user->getDocumentType());
            $user->setRole(isset($content['role']) ? $content['role'] : $user->getRole());
            $user->setActive(isset($content['active']) ? $content['active'] : $user->isActive());
            $user->setUpdatedAt(DateTimeImmutable::createFromFormat("Y-m-d", date("Y-m-d")));
            $user->setUpdatedByUserId($this->getUser() !== null ? $this->getUser()->getId() : null);
            if (isset($content['password'])) {
                $hashedPassword = $passwordHasher->hashPassword($user, $content['password']);
                $user->setPassword($hashedPassword);
            }
            $this->em->persist($user);
            $this->em->flush();
            $this->em->commit();
            return new JsonResponse('Usuario actualizado con éxito', 200);
        } catch (Exception $e) {
            $this->em->rollback();
            return new JsonResponse('Error al actualizar el usuario: '.$e->getMessage(), 500);
        }
    }

    #[Route('/api/users/{id}', name: 'delete_user', methods:['DELETE'])]
    public function deleteUser(int $id): JsonResponse
    {
        $this->em->beginTransaction();
        try {
            if(!$id){
                return new JsonResponse('El id del usuario es obligatorio', 400);
            }
            $user = $this->em->getRepository(Users::class)->find($id);
            if (!$user) {
                return new JsonResponse('Usuario no encontrado',404);
            }
            $this->em->remove($user);
            $this->em->flush();
            $this->em->commit();
            return new JsonResponse('Usuario eliminado con éxito', 200);
        } catch (Exception $e) {
            $this->em->rollback();
            return new JsonResponse('Error al eliminar el usuario: '.$e->getMessage(), 500);
        }    
    }

}
