<?php

namespace App\Controller;

use App\Entity\Locations;
use App\Entity\LocationMagnitudes;
use App\Entity\LocationSensors;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

final class LocationController extends AbstractController
{

    public function __construct(
        #[Autowire(service:'doctrine.orm.default_entity_manager')] private EntityManagerInterface $em
    ){

    }

    #[Route('/api/locations/list', name: 'list_locations', methods:['GET'])]
    public function getLocationList(EntityManagerInterface $em) {
        try {
            $locationList = $this->em->getRepository(Locations::class)->findAll();
            return new JsonResponse(array_map(fn($location) => $location->jsonSerialize(), $locationList), 200);

        } catch (Exception $e) {

            return new JsonResponse('Error al buscar la lista de ubicaciones '.$e->getMessage() ,500);
        }
    }

    #[Route('/api/locations/{id}', name: 'get_location_info', methods:['GET'])]
    public function getLocationInfo(int $id) {
        try {
            if(!$id){
                return new JsonResponse('El id de la ubicación es obligatorio', 400);
            }
            $location = $this->em->getRepository(Locations::class)->find($id);
            if (!$location) {
                return new JsonResponse('Ubicación no encontrada',404);
            }
            return new JsonResponse($location->jsonSerialize(), 200);
        } catch (Exception $e) {
            return new JsonResponse('Error al buscar la ubicación. '.$e->getMessage() ,500);
        }
    }

    #[Route('/api/locations', name: 'create_location', methods:['POST'])]
    public function createLocation(Request $request) {
        try {
            $data = json_decode($request->getContent(), true);
            $location = new Locations();
            $location->setName($data['name']);
            $location->setDescription($data['description']);
            $location->setLatDd($data['latitude']);
            $location->setLonDd($data['longitude']);
            $location->setAltitudeM($data['altitude']);
            $location->setAddress($data['address']);
            $location->setCreatedAt(new \DateTimeImmutable());
            $location->setActive(true);
            $this->em->persist($location);
            $this->em->flush();
            return new JsonResponse('Ubicación creada con éxito',201);
        } catch (Exception $e) {
            return new JsonResponse('Error al crear la ubicación. '.$e->getMessage() ,500);
        }
    }

    #[Route('/api/locations/{id}', name: 'edit_location', methods:['PUT'])]
    public function editLocation(int $id, Request $request) {
        try {
            if(!$id){
                return new JsonResponse('El id de la ubicación es obligatorio', 400);
            }
            $data = json_decode($request->getContent(), true);
            $location = $this->em->getRepository(Locations::class)->find($id);
            if (!$location) {
                return new JsonResponse('Ubicación no encontrada',404);
            }else{
                $location->setName(isset($data['name']) ? $data['name'] : $location->getName());
                $location->setDescription(isset($data['description']) ? $data['description'] : $location->getDescription());
                $location->setLatDd(isset($data['latitude']) ? $data['latitude'] : $location->getLatDd());
                $location->setLonDd(isset($data['longitude']) ? $data['longitude'] : $location->getLonDd());
                $location->setAltitudeM(isset($data['altitude']) ? $data['altitude'] : $location->getAltitudeM());
                $location->setAddress(isset($data['address']) ? $data['address'] : $location->getAddress());
                $location->setActive(isset($data['active']) ? $data['active']: $location->isActive());
                $this->em->flush();
                return new JsonResponse('Ubicación editada con éxito',200);
            }
        } catch (Exception $e) {
            return new JsonResponse('Error al editar la ubicación. '.$e->getMessage() ,500);
        }
    }
    
    #[Route('/api/locations/{id}', name: 'delete_location', methods:['DELETE'])]
    public function deleteLocation(int $id) {
        try {
            if(!$id){
                return new JsonResponse('El id de la ubicación es obligatorio', 400);
            }
            $location = $this->em->getRepository(Locations::class)->find($id);
            if (!$location) {
                return new JsonResponse('Ubicación no encontrada',404);
            }
            if($location->getMeasurements()->count() > 0){
                return new JsonResponse('No se puede eliminar la ubicación porque está siendo usada por alguna medición',400);
            }
            $this->em->remove($location);
            $this->em->flush();
            return new JsonResponse('Ubicación eliminada con éxito',200);
        } catch (Exception $e) {
            return new JsonResponse('Error al eliminar la ubicación. '.$e->getMessage() ,500);
        }
    }

    #[Route('/api/locations/{locationId}/magnitudes/list', name: 'list_location_magnitudes', methods:['GET'])]
    public function getLocationMagnitudeList(int $locationId) {
        try {
            if(!$locationId){
                return new JsonResponse('El id de la ubicación es obligatorio', 400);
            }
            if(!$this->em->getRepository('App\Entity\Locations')->find($locationId)){
                return new JsonResponse('No se encontró la ubicación', 404);
            }
            $magnitudeList = $this->em->getRepository(LocationMagnitudes::class)->findBy(['location' => $locationId]);
            if(empty($magnitudeList)){
                return new JsonResponse('No se encontraron magnitudes para esta ubicación', 404);
            }
            return new JsonResponse(array_map(fn($magnitude) => $magnitude->jsonSerialize(), $magnitudeList), 200);
        } catch (Exception $e) {
            return new JsonResponse('Error al buscar la lista de magnitudes. '.$e->getMessage() ,500);
        }
    }

    #[Route('/api/locations/{locationId}/magnitudes/{magnitudeId}', name: 'get_location_magnitude', methods:['GET'])]
    public function getLocationMagnitudeInfo(int $locationId, int $magnitudeId) {
        try {
            if(!$locationId){
                return new JsonResponse('El id de la ubicación es obligatorio', 400);
            }
            if(!$this->em->getRepository('App\Entity\Locations')->find($locationId)){
                return new JsonResponse('No se encontró la ubicación', 404);
            }
            if(!$magnitudeId){
                return new JsonResponse('El id de la magnitud es obligatorio', 400);
            }
            $magnitude = $this->em->getRepository(LocationMagnitudes::class)->findOneBy(['location' => $locationId, 'magnitude' => $magnitudeId]);
            if (!$magnitude) {
                return new JsonResponse('Magnitud no encontrada',404);
            }
            return new JsonResponse($magnitude->jsonSerialize(), 200);
        } catch (Exception $e) {
            return new JsonResponse('Error al buscar la magnitud. '.$e->getMessage() ,500);
        }
    }

    #[Route('/api/locations/{locationId}/magnitudes', name: 'create_location_magnitude', methods:['POST'])]
    public function createLocationMagnitude(int $locationId, Request $request) {
        try {
            if(!$locationId){
                return new JsonResponse('El id de la ubicación es obligatorio', 400);
            }
            if(!$this->em->getRepository('App\Entity\Locations')->find($locationId)){
                return new JsonResponse('No se encontró la ubicación', 404);
            }
            $data = json_decode($request->getContent(), true);
            $magnitude = new LocationMagnitudes();
            $magnitude->setLocation($this->em->getReference('App\Entity\Locations', $locationId));
            $magnitude->setMagnitude($this->em->getReference('App\Entity\Magnitudes', $data['magnitude_id']));
            $magnitude->setMinAcceptable($data['min_acceptable'] ? $data['min_acceptable']: null);
            $magnitude->setMaxAcceptable($data['max_acceptable'] ? $data['max_acceptable']: null);
            $magnitude->setAlertLow($data['alert_low'] ? $data['alert_low']: null);
            $magnitude->setAlertHigh($data['alert_high'] ? $data['alert_high']: null);
            $magnitude->setSamplingPlan($data['sampling_plan'] ? $data['sampling_plan']: null);
            $magnitude->setRequired($data['required'] ? $data['required']: true);
            $magnitude->setNotes($data['notes'] ? $data['notes']: null);
            $this->em->persist($magnitude);
            $this->em->flush();
            return new JsonResponse('Magnitud asociada a la ubicación con éxito', 201);
        } catch (Exception $e) {
            return new JsonResponse('Error al crear la magnitud. '.$e->getMessage() ,500);
        }
    }

    #[Route('/api/locations/{locationId}/magnitudes/{magnitudeId}', name: 'edit_location_magnitude', methods:['PUT'])]
    public function editLocationMagnitude(int $locationId, int $magnitudeId, Request $request) {
        try {
            if(!$locationId){
                return new JsonResponse('El id de la ubicación es obligatorio', 400);
            }
            if(!$this->em->getRepository('App\Entity\Locations')->find($locationId)){
                return new JsonResponse('No se encontró la ubicación', 404);
            }
            $data = json_decode($request->getContent(), true);
            if(!$magnitudeId){
                return new JsonResponse('El id de la ubicación es obligatorio', 400);
            }
            $magnitude = $this->em->getRepository(LocationMagnitudes::class)->findOneBy(['location' => $locationId, 'magnitude' => $magnitudeId]);
            if (!$magnitude) {
                return new JsonResponse('Magnitud no encontrada',404);
            }else{
                $magnitude->setLocation($this->em->getReference('App\Entity\Locations', $locationId));
                $magnitude->setMagnitude($this->em->getReference('App\Entity\Magnitudes', $magnitudeId));
                $magnitude->setMinAcceptable(isset($data['min_acceptable']) ? $data['min_acceptable']: $magnitude->getMinAcceptable());
                $magnitude->setMaxAcceptable(isset($data['max_acceptable']) ? $data['max_acceptable']: $magnitude->getMaxAcceptable());
                $magnitude->setAlertLow(isset($data['alert_low']) ? $data['alert_low']: $magnitude->getAlertLow());
                $magnitude->setAlertHigh(isset($data['alert_high']) ? $data['alert_high']: $magnitude->getAlertHigh());
                $magnitude->setSamplingPlan(isset($data['sampling_plan']) ? $data['sampling_plan']: $magnitude->getSamplingPlan());
                $magnitude->setRequired(isset($data['required']) ? $data['required']: $magnitude->getRequired());
                $magnitude->setNotes(isset($data['notes']) ? $data['notes']: $magnitude->getNotes());
                $this->em->flush();
                return new JsonResponse('Magnitud de la ubicación editada con éxito',200);
            }
        } catch (Exception $e) {
            return new JsonResponse('Error al editar la magnitud. '.$e->getMessage() ,500);
        }
    }
   
    #[Route('/api/locations/{locationId}/magnitudes/{magnitudeId}', name: 'delete_location_magnitude', methods:['DELETE'])]
    public function deleteLocationMagnitude(int $locationId, int $magnitudeId) {
        try {
            if(!$locationId){
                return new JsonResponse('El id de la ubicación es obligatorio', 400);
            }
            if(!$this->em->getRepository('App\Entity\Locations')->find($locationId)){
                return new JsonResponse('No se encontró la ubicación', 404);
            }
            if(!$magnitudeId){
                return new JsonResponse('El id de la ubicación es obligatorio', 400);
            }
            $magnitude = $this->em->getRepository(LocationMagnitudes::class)->findOneBy(['location' => $locationId, 'magnitude' => $magnitudeId]);
            if (!$magnitude) {
                return new JsonResponse('Magnitud no encontrada',404);
            }else{
                $this->em->remove($magnitude);
                $this->em->flush();
                return new JsonResponse('Magnitud desasociada de la ubicación con éxito',200);
            }
        } catch (Exception $e) {
            return new JsonResponse('Error al eliminar la magnitud. '.$e->getMessage() ,500);
        }
    }

    #[Route('/api/locations/{locationId}/sensors/list', name: 'list_location_sensors', methods:['GET'])]
    public function getLocationSensorList(int $locationId) {
        try {
            if(!$locationId){
                return new JsonResponse('El id de la ubicación es obligatorio', 400);
            }
            if(!$this->em->getRepository('App\Entity\Locations')->find($locationId)){
                return new JsonResponse('No se encontró la ubicación', 404);
            }
            $sensorList = $this->em->getRepository(LocationSensors::class)->findBy(['location' => $locationId]);
            if(empty($sensorList)){
                return new JsonResponse('No se encontraron sensores para esta ubicación', 404);
            }
            return new JsonResponse(array_map(fn($sensor) => $sensor->jsonSerialize(), $sensorList), 200);
        } catch (Exception $e) {
            return new JsonResponse('Error al buscar la lista de sensores. '.$e->getMessage() ,500);
        }
    }
    

    #[Route('/api/locations/{locationId}/sensors/{sensorId}', name: 'get_location_sensor', methods:['GET'])]
    public function getLocationSensorInfo(int $locationId, int $sensorId) {
        try {
            if(!$locationId){
                return new JsonResponse('El id de la ubicación es obligatorio', 400);
            }
            if(!$this->em->getRepository('App\Entity\Locations')->find($locationId)){
                return new JsonResponse('No se encontró la ubicación', 404);
            }
            if(!$sensorId){
                return new JsonResponse('El id del sensor es obligatorio', 400);
            }
            $sensor = $this->em->getRepository(LocationSensors::class)->findOneBy(['location' => $locationId, 'sensor' => $sensorId]);
            if (!$sensor) {
                return new JsonResponse('Sensor no encontrado', 404);
            }
            return new JsonResponse($sensor->jsonSerialize(), 200);
        } catch (Exception $e) {
            return new JsonResponse('Error al buscar el sensor. '.$e->getMessage() ,500);
        }
    }
    

    #[Route('/api/locations/{locationId}/sensors', name: 'create_location_sensor', methods:['POST'])]
    public function createLocationSensor(int $locationId, Request $request) {
        try {
            if(!$locationId){
                return new JsonResponse('El id de la ubicación es obligatorio', 400);
            }
            $data = json_decode($request->getContent(), true);
            $sensor = new LocationSensors();
            if(!$this->em->getRepository('App\Entity\Locations')->find($locationId)){
                return new JsonResponse('No se encontró la ubicación', 404);
            }
            if(empty($data['sensor_id'])){
                return new JsonResponse('El id del sensor es obligatorio', 400);
            }
            if(!$this->em->getRepository('App\Entity\Sensors')->find($data['sensor_id'])){
                return new JsonResponse('No se encontró el sensor', 404);
            }
            $sensor->setLocation($this->em->getRepository('App\Entity\Locations')->find($locationId));
            $sensor->setSensor($this->em->getRepository('App\Entity\Sensors')->find($data['sensor_id']));
            $sensor->setActive(isset($data['active']) ? $data['active'] : true);
            $sensor->setNotes(isset($data['notes']) ? $data['notes'] : '');
            $this->em->persist($sensor);
            $this->em->flush();
            return new JsonResponse('Sensor asociado a la ubicación con éxito.', 201);
        } catch (Exception $e) {
            return new JsonResponse('Error al agregar el sensor a la ubicación. '.$e->getMessage() ,500);
        }
    }
    

    #[Route('/api/locations/{locationId}/sensors/{sensorId}', name: 'edit_location_sensor', methods:['PUT'])]
    public function editLocationSensor(int $locationId, int $sensorId, Request $request) {
        try {
            if(!$locationId){
                return new JsonResponse('El id de la ubicación es obligatorio', 400);
            }
            $data = json_decode($request->getContent(), true);
            if(!$this->em->getRepository('App\Entity\Locations')->find($locationId)){
                return new JsonResponse('No se encontró la ubicación', 404);
            }
            if(!$sensorId){
                return new JsonResponse('El id del sensor es obligatorio', 400);
            }
            $sensor = $this->em->getRepository(LocationSensors::class)->findOneBy(['location' => $locationId, 'sensor' => $sensorId]);
            if(!$sensor){
                return new JsonResponse('No se encontró el sensor en la ubicación', 404);
            }
            $sensor->setActive(isset($data['active']) ? $data['active'] : $sensor->isActive());
            $sensor->setNotes(isset($data['notes']) ? $data['notes'] : $sensor->getNotes());
            $this->em->persist($sensor);
            $this->em->flush();
            return new JsonResponse('Sensor de la ubicación actualizado con éxito', 200);
        } catch (Exception $e) {
            return new JsonResponse('Error al actualizar el sensor de la ubicación. '.$e->getMessage() ,500);
        }
    }
    
   
    #[Route('/api/locations/{locationId}/sensors/{sensorId}', name: 'delete_location_sensor', methods:['DELETE'])]
    public function deleteLocationSensor(int $locationId, int $sensorId) {
        try {
            if(!$locationId){
                return new JsonResponse('El id de la ubicación es obligatorio', 400);
            }
            if(!$this->em->getRepository('App\Entity\Locations')->find($locationId)){
                return new JsonResponse('No se encontró la ubicación', 404);
            }
            if(!$sensorId){
                return new JsonResponse('El id del sensor es obligatorio', 400);
            }
            $sensor = $this->em->getRepository(LocationSensors::class)->findOneBy(['location' => $locationId, 'sensor' => $sensorId]);
            if (!$sensor) {
                return new JsonResponse('Sensor no encontrado en la ubicación', 404);
            }
            $this->em->remove($sensor);
            $this->em->flush();
            return new JsonResponse('Sensor desasociado de la ubicación con éxito', 200);
        } catch (Exception $e) {
            return new JsonResponse('Error al eliminar el sensor de la ubicación. '.$e->getMessage() ,500);
        }
    }
}
