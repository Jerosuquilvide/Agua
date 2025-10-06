<?php

namespace App\Controller;

use App\Entity\Units;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

final class UnitController extends AbstractController
{

    public function __construct(
        #[Autowire(service:'doctrine.orm.default_entity_manager')] private EntityManagerInterface $em
    ){

    }

    #[Route('/api/units/list', name: 'list_units', methods:['GET'])]
    public function getUnitList(EntityManagerInterface $em) {
        try {
            $unitList = $this->em->getRepository(Units::class)->findAll();
            return new JsonResponse(array_map(fn($unit) => $unit->jsonSerialize(), $unitList), 200);
        } catch (Exception $e) {
            return new JsonResponse('Error al buscar la lista de unidades '.$e->getMessage() ,500);
        }
    }

    #[Route('/api/units/{id}', name: 'get_unit_info', methods:['GET'])]
    public function getUnitInfo(int $id) {
        try {
            if(!$id){
                return new JsonResponse('El id de la unidad es obligatorio', 400);
            }
            $unit = $this->em->getRepository(Units::class)->find($id);
            if (!$unit) {
                return new JsonResponse('Unidad no encontrada',404);
            }
            return new JsonResponse($unit->jsonSerialize(), 200);
        } catch (Exception $e) {
            return new JsonResponse('Error al buscar la unidad. '.$e->getMessage() ,500);
        }
    }
    
    #[Route('/api/units', name: 'create_unit', methods:['POST'])]
    public function createUnit(Request $request) {
        try {
            $data = json_decode($request->getContent(), true);
            $unit = new Units();
            $unit->setUcumCode($data['ucum_code']);
            $unit->setUncefactCode(isset($data['uncefact_code']) ? $data['uncefact_code'] : null);
            $unit->setDisplay($data['display']);
            $this->em->persist($unit);
            $this->em->flush();
            return new JsonResponse('Unidad creada correctamente', 201);
        } catch (Exception $e) {
            return new JsonResponse('Error al crear la unidad. '.$e->getMessage() ,500);
        }
    }

    #[Route('/api/units/{id}', name: 'update_unit', methods:['PUT'])]
    public function updateUnit(int $id, Request $request) {
        try {
            if(!$id){
                return new JsonResponse('El id de la unidad es obligatorio', 400);
            }
            $unit = $this->em->getRepository(Units::class)->find($id);
            if (!$unit) {
                return new JsonResponse('Unidad no encontrada',404);
            }
            $data = json_decode($request->getContent(), true);
            $unit->setUcumCode(isset($data['ucum_code']) ? $data['ucum_code'] : $unit->getUcumCode());
            $unit->setUncefactCode(isset($data['uncefact_code']) ? $data['uncefact_code'] : $unit->getUncefactCode());
            $unit->setDisplay(isset($data['display']) ? $data['display'] : $unit->getDisplay());
            $this->em->persist($unit);
            $this->em->flush();
            return new JsonResponse('Unidad actualizada correctamente', 200);
        } catch (Exception $e) {
            return new JsonResponse('Error al actualizar la unidad. '.$e->getMessage() ,500);
        }
    }

    #[Route('/api/units/{id}', name: 'delete_unit', methods:['DELETE'])]
    public function deleteUnit(int $id) {
        try {
            if(!$id){
                return new JsonResponse('El id de la unidad es obligatorio', 400);
            }
            $unit = $this->em->getRepository(Units::class)->find($id);
            if (!$unit) {
                return new JsonResponse('Unidad no encontrada',404);
            }
            $this->em->remove($unit);
            $this->em->flush();
            return new JsonResponse('Unidad eliminada correctamente', 200);
        } catch (Exception $e) {
            return new JsonResponse('Error al eliminar la unidad. '.$e->getMessage() ,500);
        }
    }
}
