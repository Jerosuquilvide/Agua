<?php

namespace App\Controller;

use App\Entity\Sensors;
use App\Entity\SensorMagnitudes;
use App\Entity\Magnitudes;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

final class SensorController extends AbstractController
{

    public function __construct(
        #[Autowire(service:'doctrine.orm.default_entity_manager')] private EntityManagerInterface $em
    ){

    }

    #[Route('/api/sensor/list', name: 'list_sensors', methods:['GET'])]
    public function getSensorList(EntityManagerInterface $em) {
        try {
            $sensors = $this->em->getRepository(Sensors::class)->findAll();
            return new JsonResponse(array_map(fn($sensor) => $sensor->jsonSerialize(), $sensors), 200);
        } catch (Exception $e) {
            return new JsonResponse('Error al buscar la lista de sensores '.$e->getMessage() ,500);
        }
    }

    #[Route('/api/sensors/{id}', name: 'get_sensor_info', methods:['GET'])]
    public function getSensorInfo(int $id) {
        try {
            if(!$id){
                return new JsonResponse('El id del sensor es obligatorio', 400);
            }
            $sensor = $this->em->getRepository(Sensors::class)->find($id);
            if (!$sensor) {
                return new JsonResponse('Sensor no encontrado',404);
            }
            return new JsonResponse($sensor->jsonSerialize(), 200);
        } catch (Exception $e) {
            return new JsonResponse('Error al buscar el sensor. '.$e->getMessage() ,500);
        }
    }

    #[Route('/api/sensors', name: 'create_sensor', methods:['POST'])]
    public function createSensor(Request $request) {
        try {
            $data = json_decode($request->getContent(), true);
            $sensor = new Sensors();
            $sensor->setName($data['name']);
            $sensor->setManufacturer($data['manufacturer']);
            $sensor->setModel($data['model']);
            $sensor->setSerialNumber($data['serial_number']);
            $sensor->setSensorType($data['sensor_type']);
            $sensor->setInstalledAt(new \DateTimeImmutable($data['installed_at']));
            $sensor->setActive($data['active']);
            $sensor->setNotes($data['notes']);
            if(isset($data['magnitude_id']) && $data['magnitude_id'] != null && $data['magnitude_id'] != ''){
                if(!$this->em->getRepository('App\Entity\SensorMagnitudes')->find($data['magnitude_id'])){
                    return new JsonResponse('No se encontró la magnitud del sensor', 404);
                }
                $sensor->setSensorMagnitudes($data['magnitude_id'] ? $this->em->getRepository('App\Entity\SensorMagnitudes')->find($data['magnitude_id']): null);
            }
            if(isset($data['measurement_id']) && $data['measurement_id'] != null && $data['measurement_id'] != ''){
                if(!$this->em->getRepository('App\Entity\Measurements')->find($data['measurement_id'])){
                    return new JsonResponse('No se encontró la medición del sensor', 404);
                }                
                $sensor->setMeasurements($data['measurement_id'] ? $this->em->getRepository('App\Entity\Measurements')->find($data['measurement_id']): null);
            }
            $this->em->persist($sensor);
            $this->em->flush();
            return new JsonResponse('Sensor creado con éxito', 201);
        } catch (Exception $e) {
            return new JsonResponse('Error al crear el sensor. '.$e->getMessage() ,500);
        }
    }

    #[Route('/api/sensors/{id}', name: 'edit_sensor', methods:['PUT'])]
    public function editSensor(int $id, Request $request) {
        try {
            if(!$id){
                return new JsonResponse('El id del sensor es obligatorio', 400);
            }
            $data = json_decode($request->getContent(), true);
            $sensor = $this->em->getRepository(Sensors::class)->find($id);
            if (!$sensor) {
                return new JsonResponse('Sensor no encontrado',404);
            }else{
                $sensor->setName(isset($data['name']) ? $data['name'] : $sensor->getName());
                $sensor->setManufacturer(isset($data['manufacturer']) ? $data['manufacturer'] : $sensor->getManufacturer());
                $sensor->setModel(isset($data['model']) ? $data['model'] : $sensor->getModel());
                $sensor->setSerialNumber(isset($data['serial_number']) ? $data['serial_number'] : $sensor->getSerialNumber());
                $sensor->setSensorType(isset($data['sensor_type']) ? $data['sensor_type'] : $sensor->getSensorType());
                $sensor->setInstalledAt(isset($data['installed_at']) ? new \DateTimeImmutable($data['installed_at']) : $sensor->getInstalledAt());
                $sensor->setActive(isset($data['active']) ? $data['active'] : $sensor->isActive());
                $sensor->setNotes(isset($data['notes']) ? $data['notes'] : $sensor->getNotes());
                if(isset($data['magnitude_id']) && $data['magnitude_id'] != null && $data['magnitude_id'] != ''){
                    if(!$this->em->getRepository('App\Entity\SensorMagnitudes')->find($data['magnitude_id'])){
                        return new JsonResponse('No se encontró la magnitud del sensor', 404);
                    }
                    $sensor->setSensorMagnitudes($data['magnitude_id'] ? $this->em->getRepository('App\Entity\SensorMagnitudes')->find($data['magnitude_id']): null);
                }
                if(isset($data['measurement_id']) && $data['measurement_id'] != null && $data['measurement_id'] != ''){
                    if(!$this->em->getRepository('App\Entity\Measurements')->find($data['measurement_id'])){
                        return new JsonResponse('No se encontró la medición del sensor', 404);
                    }                
                    $sensor->setMeasurements($data['measurement_id'] ? $this->em->getRepository('App\Entity\Measurements')->find($data['measurement_id']): null);
                }
                $this->em->flush();
                return new JsonResponse('Sensor editado con éxito',200);
            }
        } catch (Exception $e) {
            return new JsonResponse('Error al editar el sensor. '.$e->getMessage() ,500);
        }
    }

    #[Route('/api/sensors/{id}', name: 'delete_sensor', methods:['DELETE'])]
    public function deleteSensor(int $id) {
        try {
            if(!$id){
                return new JsonResponse('El id del sensor es obligatorio', 400);
            }
            $sensor = $this->em->getRepository(Sensors::class)->find($id);
            if (!$sensor) {
                return new JsonResponse('Sensor no encontrado',404);
            }else{
                if($sensor->getMeasurements()->count() > 0){ 
                    return new JsonResponse('No se puede eliminar el sensor porque está siendo utilizado por alguna medición', 409);
                }
                $this->em->remove($sensor);
                $this->em->flush();
                return new JsonResponse('Sensor eliminado con éxito',200);
            }
        } catch (Exception $e) {
            return new JsonResponse('Error al eliminar el sensor. '.$e->getMessage() ,500);
        }
    }

     #[Route('/api/sensors/{sensorId}/magnitudes/list', name: 'list_sensor_magnitudes', methods:['GET'])]
    public function getSensorMagnitudeList(int $sensorId) {
        try {
            if(!$sensorId){
                return new JsonResponse('El id del sensor es obligatorio', 400);
            }
            if (!$this->em->getRepository(Sensors::class)->find($sensorId)) {
                return new JsonResponse('Sensor no encontrado',404);
            }
            $magnitudesList = $this->em->getRepository(SensorMagnitudes::class)->findBy(['sensor' => $sensorId]);           
            return new JsonResponse(array_map(fn($magnitude) => $magnitude->jsonSerialize(), $magnitudesList), 200);
        } catch (Exception $e) {
            return new JsonResponse('Error al buscar la lista de magnitudes del sensor '.$e->getMessage() ,500);
        }
    }

    #[Route('/api/sensors/{sensorId}/magnitudes/{magnitudeId}', name: 'get_sensor_magnitude_info', methods:['GET'])]
    public function getSensorMagnitudeInfo(int $sensorId, int $magnitudeId) {
        try {
            if(!$sensorId){
                return new JsonResponse('El id del sensor es obligatorio', 400);
            }
            if(!$magnitudeId){
                return new JsonResponse('El id de la magnitud es obligatorio', 400);
            }
            if (!$this->em->getRepository(Sensors::class)->find($sensorId)) {
                return new JsonResponse('Sensor no encontrado',404);
            }
            $magnitude = $this->em->getRepository(SensorMagnitudes::class)->findOneBy(['id' => $magnitudeId, 'sensor' => $sensorId]);
            if (!$magnitude) {
                return new JsonResponse('Magnitud no encontrada para el sensor indicado',404);
            }
            return new JsonResponse($magnitude->jsonSerialize(), 200);
        } catch (Exception $e) {
            return new JsonResponse('Error al buscar la magnitud del sensor. '.$e->getMessage() ,500);
        }
    }

    #[Route('/api/sensors/{sensorId}/magnitudes', name: 'create_sensor_magnitude', methods:['POST'])]
    public function createSensorMagnitude(int $sensorId, Request $request) {
        try {
            if(!$sensorId){
                return new JsonResponse('El id del sensor es obligatorio', 400);
            }
            $sensor = $this->em->getRepository(Sensors::class)->find($sensorId);
            if (!$sensor) {
                return new JsonResponse('Sensor no encontrado',404);
            }
            $data = json_decode($request->getContent(), true);
            $sensorMagnitude = new SensorMagnitudes();
            if(!$data['magnitude_id']){
                return new JsonResponse('El id de la magnitud es obligatorio', 400);
            }
            $magnitude = $this->em->getRepository(Magnitudes::class)->find($data['magnitude_id']);
            if (!$magnitude) {
                return new JsonResponse('Magnitud no encontrada',404);
            }
            $sensorMagnitude->setSensor($sensor);
            $sensorMagnitude->setMagnitude($magnitude);
            $sensorMagnitude->setValueMin(isset($data['value_min']) ? $data['value_min'] : null);
            $sensorMagnitude->setValueMax(isset($data['value_max']) ? $data['value_max'] : null);
            $sensorMagnitude->setResolution(isset($data['resolution']) ? $data['resolution'] : null);
            $sensorMagnitude->setAccuracy(isset($data['accuracy']) ? $data['accuracy'] : null);
            $sensorMagnitude->setCalibratedAt(isset($data['calibrated_at']) ? new \DateTimeImmutable($data['calibrated_at']) : null);
            $sensorMagnitude->setChannelName(isset($data['channel_name']) ? $data['channel_name'] : null);
            $sensorMagnitude->setNotes(isset($data['notes']) ? $data['notes'] : null);
            $this->em->persist($sensorMagnitude);
            $this->em->flush();            
            return new JsonResponse('Magnitud asociada al sensor con éxito', 201);
        } catch (Exception $e) {
            return new JsonResponse('Error al crear la magnitud del sensor. '.$e->getMessage() ,500);
        }
    }

    #[Route('/api/sensors/{sensorId}/magnitudes/{magnitudeId}', name: 'edit_sensor_magnitude', methods:['PUT'])]
    public function editSensorMagnitude(int $sensorId, int $magnitudeId, Request $request) {
        try {
            if(!$sensorId){
                return new JsonResponse('El id del sensor es obligatorio', 400);
            }
            if (!$this->em->getRepository(Sensors::class)->find($sensorId)) {
                return new JsonResponse('Sensor no encontrado',404);
            }
            $data = json_decode($request->getContent(), true);
            if(!$magnitudeId){
                return new JsonResponse('El id de la magnitud es obligatorio', 400);
            }
            $magnitude = $this->em->getRepository(SensorMagnitudes::class)->findOneBy(['sensor' => $sensorId, 'magnitude' => $magnitudeId]);
            if (!$magnitude) {
                return new JsonResponse('Magnitud no encontrada para el sensor indicado',404);
            }
            $magnitude->setValueMin(isset($data['value_min']) ? $data['value_min'] : $magnitude->getValueMin());
            $magnitude->setValueMax(isset($data['value_max']) ? $data['value_max'] : $magnitude->getValueMax());
            $magnitude->setResolution(isset($data['resolution']) ? $data['resolution'] : $magnitude->getResolution());
            $magnitude->setAccuracy(isset($data['accuracy']) ? $data['accuracy'] : $magnitude->getAccuracy());
            $magnitude->setCalibratedAt(isset($data['calibrated_at']) ? new \DateTimeImmutable($data['calibrated_at']) : $magnitude->getCalibratedAt());
            $magnitude->setChannelName(isset($data['channel_name']) ? $data['channel_name'] : $magnitude->getChannelName());
            $magnitude->setNotes(isset($data['notes']) ? $data['notes'] : $magnitude->getNotes());
            $this->em->flush();
            return new JsonResponse('Magnitud del sensor actualizada con éxito', 200);
        } catch (Exception $e) {
            return new JsonResponse('Error al actualizar la magnitud del sensor. '.$e->getMessage() ,500);
        }
    }

    #[Route('/api/sensors/{sensorId}/magnitudes/{magnitudeId}', name: 'delete_sensor_magnitude', methods:['DELETE'])]
    public function deleteSensorMagnitude(int $sensorId, int $magnitudeId) {
        try {
            if(!$sensorId){
                return new JsonResponse('El id del sensor es obligatorio', 400);
            }
            if(!$magnitudeId){
                return new JsonResponse('El id de la magnitud es obligatorio', 400);
            }
            if (!$this->em->getRepository(Sensors::class)->find($sensorId)) {
                return new JsonResponse('Sensor no encontrado',404);
            }
            $magnitude = $this->em->getRepository(SensorMagnitudes::class)->findOneBy(['id' => $magnitudeId, 'sensor' => $sensorId]);
            if (!$magnitude) {
                return new JsonResponse('Magnitud no encontrada para el sensor indicado',404);
            }
            $this->em->remove($magnitude);
            $this->em->flush();
            return new JsonResponse('Magnitud desasociada del sensor con éxito',200);
        } catch (Exception $e) {
            return new JsonResponse('Error al eliminar la magnitud del sensor. '.$e->getMessage() ,500);
        }
    }

}
