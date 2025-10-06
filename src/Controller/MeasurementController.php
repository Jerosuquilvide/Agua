<?php

namespace App\Controller;

use App\Entity\Measurements;
use App\Entity\Sensors;
use App\Entity\Locations;
use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

final class MeasurementController extends AbstractController
{

    public function __construct(
        #[Autowire(service:'doctrine.orm.default_entity_manager')] private EntityManagerInterface $em
    ){

    }

    #[Route('/api/measurements/list', name: 'list_measurements', methods:['GET'])]
    public function getMeasurementList(EntityManagerInterface $em) {
        try {
            $measurementList = $this->em->getRepository(Measurements::class)->findAll();
            return new JsonResponse(array_map(fn($measurement) => $measurement->jsonSerialize(), $measurementList), 200);
        } catch (Exception $e) {
            return new JsonResponse('Error al buscar la lista de mediciones '.$e->getMessage() ,500);
        }
    }

    #[Route('/api/measurements/{id}', name: 'get_measurement_info', methods:['GET'])]
    public function getMeasurementInfo(int $id) {
        try {
            if(!$id){
                return new JsonResponse('El id de la medicion es obligatorio', 400);
            }
            $measurement = $this->em->getRepository(Measurements::class)->find($id);
            if (!$measurement) {
                return new JsonResponse('Medicion no encontrada',404);
            }
            return new JsonResponse($measurement->jsonSerialize(), 200);
        } catch (Exception $e) {
            return new JsonResponse('Error al buscar la medicion. '.$e->getMessage() ,500);
        }
    }

    #[Route('/api/measurements', name: 'create_measurement', methods:['POST'])]
    public function createMeasurement(Request $request) {
        try {
            $data = json_decode($request->getContent(), true);
            $measurement = new Measurements();
            $sensor = $this->em->getRepository(Sensors::class)->find($data['sensor_id']);
            if (!$sensor) {
                return new JsonResponse('Sensor no encontrado',404);
            }
            $measurement->setSensor($sensor);
            $location = $this->em->getRepository(Locations::class)->find($data['location_id']);
            if (!$location) {
                return new JsonResponse('Ubicacion no encontrada',404);
            }
            $measurement->setLocation($location);
            $measurement->setEnteredBy(isset($data['entered_by']) ? $this->em->getRepository('App\Entity\Users')->find($data['entered_by']) : null);
            $measurement->setSampledBy(isset($data['sampled_by']) ? $this->em->getRepository('App\Entity\Users')->find($data['sampled_by']) : null);
            $measurement->setRegisteredAt(isset($data['registered_at']) ? new \DateTimeImmutable($data['registered_at']) : new \DateTimeImmutable());
            $measurement->setSampledAt(isset($data['sampled_at']) ? new \DateTimeImmutable($data['sampled_at']) : new \DateTimeImmutable());
            $measurement->setStatus($data['status']);
            $measurement->setSource($data['source']);
            $measurement->setBatchId(isset($data['batch_id']) ? (int) $data['batch_id'] : null);
            $measurement->setComments(isset($data['comments']) ? $data['comments'] : null);
            $this->em->persist($measurement);
            $this->em->flush();
            return new JsonResponse('Medicion creada con éxito',201);
        }catch (Exception $e) {
            return new JsonResponse('Error al crear la medicion. '.$e->getMessage() ,500);
        }
    }

    #[Route('/api/measurements/{id}', name: 'update_measurement', methods:['PUT'])]
    public function updateMeasurement(int $id, Request $request) {
        try {
            if(!$id){
                return new JsonResponse('El id de la medicion es obligatorio', 400);
            }
            $measurement = $this->em->getRepository(Measurements::class)->find($id);
            if (!$measurement) {
                return new JsonResponse('Medicion no encontrada',404);
            }
            $data = json_decode($request->getContent(), true);
            $measurement->setSensor(isset($data['sensor_id']) ? $this->em->getRepository('App\Entity\Sensors')->find($data['sensor_id']) : $measurement->getSensor());
            $measurement->setLocation(isset($data['location_id']) ? $this->em->getRepository('App\Entity\Locations')->find($data['location_id']) : $measurement->getLocation());
            $measurement->setEnteredBy(isset($data['entered_by']) ? $this->em->getRepository('App\Entity\Users')->find($data['entered_by']) : $measurement->getEnteredBy());
            $measurement->setSampledBy(isset($data['sampled_by']) ? $this->em->getRepository('App\Entity\Users')->find($data['sampled_by']) : $measurement->getSampledBy());
            $measurement->setRegisteredAt(isset($data['registered_at']) ? new \DateTimeImmutable($data['registered_at']) : $measurement->getRegisteredAt());
            $measurement->setSampledAt(isset($data['sampled_at']) ? new \DateTimeImmutable($data['sampled_at']) : $measurement->getSampledAt());
            $measurement->setStatus(isset($data['status']) ? $data['status'] : $measurement->getStatus());
            $measurement->setSource(isset($data['source']) ? $data['source'] : $measurement->getSource());
            $measurement->setBatchId(isset($data['batch_id']) ? (int) $data['batch_id'] : $measurement->getBatchId());
            $measurement->setComments(isset($data['comments']) ? $data['comments'] : $measurement->getComments());
            $this->em->flush();
            return new JsonResponse('Medicion actualizada con éxito',200);
        } catch (Exception $e) {
            return new JsonResponse('Error al actualizar la medicion. '.$e->getMessage() ,500);
        }
    }

    #[Route('/api/measurements/{id}', name: 'delete_measurement', methods:['DELETE'])]
    public function deleteMeasurement(int $id) {
        try {
            if(!$id){
                return new JsonResponse('El id de la medicion es obligatorio', 400);
            }
            $measurement = $this->em->getRepository(Measurements::class)->find($id);
            if (!$measurement) {
                return new JsonResponse('Medicion no encontrada',404);
            }
            $this->em->remove($measurement);
            $this->em->flush();
            return new JsonResponse('Medicion eliminada con éxito',200);
        } catch (Exception $e) {
            return new JsonResponse('Error al eliminar la medicion. '.$e->getMessage() ,500);
        }
    }

}
