<?php

namespace App\Controller;

use App\Entity\MeasuredValues;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

final class MeasuredValueController extends AbstractController
{

    public function __construct(
        #[Autowire(service:'doctrine.orm.default_entity_manager')] private EntityManagerInterface $em
    ){

    }
    #[Route('/api/measured-value/list', name: 'list_measured_values', methods: ['GET'])]
    public function getMeasuredValueList(EntityManagerInterface $em) {
        try {
            $measuredValueList = $em->getRepository(MeasuredValues::class)->findAll();
            return new JsonResponse(array_map(fn($measuredValue) => $measuredValue->jsonSerialize(), $measuredValueList), 200);
        } catch (Exception $e) {
            return new JsonResponse('Error al buscar la lista de valores medidos '.$e->getMessage() ,500);
        }
    }

    #[Route('/api/measured-value/{id}', name: 'get_measured_value_info', methods: ['GET'])]
    public function getMeasuredValueInfo(int $id) {
        try {
            if(!$id){
                return new JsonResponse('El id del valor medido es obligatorio', 400);
            }
            $measuredValue = $this->em->getRepository(MeasuredValues::class)->find($id);
            if (!$measuredValue) {
                return new JsonResponse('Valor medido no encontrado', 404);
            }
            return new JsonResponse($measuredValue->jsonSerialize(), 200);
        } catch (Exception $e) {
            return new JsonResponse('Error al buscar el valor medido. '.$e->getMessage() ,500);
        }
    }

    #[Route('/api/measured-value', name: 'create_measured_value', methods: ['POST'])]
    public function createMeasuredValue(Request $request) {
        try {
            $data = json_decode($request->getContent(), true);
            $measuredValue = new MeasuredValues();
            $measuredValue->setMeasurement($this->em->getRepository('App\Entity\Measurements')->find($data['measurement_id']));
            $measuredValue->setMagnitude($this->em->getRepository('App\Entity\Magnitudes')->find($data['magnitude_id']));
            $measuredValue->setUnit($this->em->getRepository('App\Entity\Units')->find($data['unit_id']));
            $measuredValue->setValueNumeric($data['value_numeric']);
            $measuredValue->setQcFlag(isset($data['qc_flag']) ? $data['qc_flag'] : null);
            $measuredValue->setStatus($data['status']);
            $measuredValue->setTakenAt(isset($data['taken_at']) ? new \DateTimeImmutable($data['taken_at']) : null);
            $measuredValue->setComments(isset($data['comments']) ? $data['comments'] : null);
            $measuredValue->setSnapshotMinAcceptable(isset($data['snapshot_min_acceptable']) ? $data['snapshot_min_acceptable'] : null);
            $measuredValue->setSnapshotMaxAcceptable(isset($data['snapshot_max_acceptable']) ? $data['snapshot_max_acceptable'] : null);
            $measuredValue->setSnapshotAlertLow(isset($data['snapshot_alert_low']) ? $data['snapshot_alert_low'] : null);
            $measuredValue->setSnapshotAlertHigh(isset($data['snapshot_alert_high']) ? $data['snapshot_alert_high'] : null);
            $measuredValue->setSnapshotAllowNegative(isset($data['snapshot_allow_negative']) ? $data['snapshot_allow_negative'] : null);
            $this->em->persist($measuredValue);
            $this->em->flush();
            return new JsonResponse('Valor medido creado con éxito',201);
        }catch (Exception $e) {
            return new JsonResponse('Error al crear el valor medido. '.$e->getMessage() ,500);
        }
    }

    #[Route('/api/measured-value/{id}', name: 'update_measured_value', methods: ['PUT'])]
    public function updateMeasuredValue(int $id, Request $request) {
        try {
            if(!$id){
                return new JsonResponse('El id del valor medido es obligatorio', 400);
            }
            $measuredValue = $this->em->getRepository(MeasuredValues::class)->find($id);
            if (!$measuredValue) {
                return new JsonResponse('Valor medido no encontrado',404);
            }
            $data = json_decode($request->getContent(), true);
            $measuredValue->setMeasurement(isset($data['measurement_id']) ? $this->em->getRepository('App\Entity\Measurements')->find($data['measurement_id']) : $measuredValue->getMeasurement());
            $measuredValue->setMagnitude(isset($data['magnitude_id']) ? $this->em->getRepository('App\Entity\Magnitudes')->find($data['magnitude_id']) : $measuredValue->getMagnitude());
            $measuredValue->setUnit(isset($data['unit_id']) ? $this->em->getRepository('App\Entity\Units')->find($data['unit_id']) : $measuredValue->getUnit());
            $measuredValue->setValueNumeric(isset($data['value_numeric']) ? $data['value_numeric'] : $measuredValue->getValueNumeric());
            $measuredValue->setQcFlag(isset($data['qc_flag']) ? $data['qc_flag'] : $measuredValue->getQcFlag());
            $measuredValue->setStatus(isset($data['status']) ? $data['status'] : $measuredValue->getStatus());
            $measuredValue->setTakenAt(isset($data['taken_at']) ? new \DateTimeImmutable($data['taken_at']) : $measuredValue->getTakenAt());
            $measuredValue->setComments(isset($data['comments']) ? $data['comments'] : $measuredValue->getComments());
            $measuredValue->setSnapshotMinAcceptable(isset($data['snapshot_min_acceptable']) ? $data['snapshot_min_acceptable'] : $measuredValue->getSnapshotMinAcceptable());
            $measuredValue->setSnapshotMaxAcceptable(isset($data['snapshot_max_acceptable']) ? $data['snapshot_max_acceptable'] : $measuredValue->getSnapshotMaxAcceptable());
            $measuredValue->setSnapshotAlertLow(isset($data['snapshot_alert_low']) ? $data['snapshot_alert_low'] : $measuredValue->getSnapshotAlertLow());
            $measuredValue->setSnapshotAlertHigh(isset($data['snapshot_alert_high']) ? $data['snapshot_alert_high'] : $measuredValue->getSnapshotAlertHigh());
            $measuredValue->setSnapshotAllowNegative(isset($data['snapshot_allow_negative']) ? $data['snapshot_allow_negative'] : $measuredValue->isSnapshotAllowNegative());
            $this->em->persist($measuredValue);
            $this->em->flush();
            return new JsonResponse('Valor medido actualizado con éxito',200);
        } catch (Exception $e) {
            return new JsonResponse('Error al actualizar el valor medido. '.$e->getMessage() ,500);
        }
    }

    #[Route('/api/measured-value/{id}', name: 'delete_measured_value', methods: ['DELETE'])]
    public function deleteMeasuredValue(int $id) {
        try {
            if(!$id){
                return new JsonResponse('El id del valor medido es obligatorio', 400);
            }
            $measuredValue = $this->em->getRepository(MeasuredValues::class)->find($id);
            if (!$measuredValue) {
                return new JsonResponse('Valor medido no encontrado',404);
            }
            $this->em->remove($measuredValue);
            $this->em->flush();
            return new JsonResponse('Valor medido eliminado con éxito',200);
        } catch (Exception $e) {
            return new JsonResponse('Error al eliminar el valor medido. '.$e->getMessage() ,500);
        }
    }
}
