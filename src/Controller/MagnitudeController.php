<?php

namespace App\Controller;

use App\Entity\Magnitudes;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

final class MagnitudeController extends AbstractController
{

    public function __construct(
        #[Autowire(service:'doctrine.orm.default_entity_manager')] private EntityManagerInterface $em
    ){

    }

    #[Route('/api/magnitudes/list', name: 'list_magnitudes', methods:['GET'])]
    public function getMagnitudesList(EntityManagerInterface $em) {
        try {
            $magnitudesList = $this->em->getRepository(Magnitudes::class)->findAll();
            return new JsonResponse(array_map(fn($magnitude) => $magnitude->jsonSerialize(), $magnitudesList), 200);
        } catch (Exception $e) {
            return new JsonResponse('Error al buscar la lista de magnitudes '.$e->getMessage() ,500);
        }
    }

    #[Route('/api/magnitudes/{id}', name: 'get_magnitude_info', methods:['GET'])]
    public function getMagnitudeInfo(int $id) {
        try {
            if(!$id){
                return new JsonResponse('El id de la magnitud es obligatorio', 400);
            }
            $magnitude = $this->em->getRepository(Magnitudes::class)->find($id);
            if (!$magnitude) {
                return new JsonResponse('Magnitud no encontrada',404);
            }
            return new JsonResponse($magnitude->jsonSerialize(), 200);
        } catch (Exception $e) {
            return new JsonResponse('Error al buscar la magnitud. '.$e->getMessage() ,500);
        }
    }

    #[Route('/api/magnitudes', name: 'create_magnitude', methods:['POST'])]
    public function createMagnitude(Request $request) {
        try {
            $data = json_decode($request->getContent(), true);
            $magnitude = new Magnitudes();
            if (!isset($data['unit_id'])) {
                return new JsonResponse('La unidad es obligatoria', 400);
            }
            $unit = $this->em->getRepository('App\Entity\Units')->find($data['unit_id']);
            if (!$unit) {
                return new JsonResponse('Unidad no encontrada', 404);
            }
            $magnitude->setGroupName($data['group_name']);
            $magnitude->setNameEn($data['name_en']);
            $magnitude->setAbbreviation($data['abbreviation']);            
            $magnitude->setUnit($unit);       
            $magnitude->setWqxCode(isset($data['wqx_code']) ? $data['wqx_code'] : null);
            $magnitude->setWmoCode(isset($data['wmo_code']) ? $data['wmo_code'] : null);
            $magnitude->setIsoIeeeCode(isset($data['iso_ieee_code']) ? $data['iso_ieee_code'] : null);
            $magnitude->setDecimals(isset($data['decimals']) ? $data['decimals'] : 2);
            $magnitude->setAllowNegative($data['allow_negative']);
            $magnitude->setMinValue(isset($data['min_value']) ? $data['min_value'] : null);
            $magnitude->setMaxValue(isset($data['max_value']) ? $data['max_value'] : null);
            $this->em->persist($magnitude);
            $this->em->flush();
            return new JsonResponse('Magnitud creada con éxito',201);
        } catch (Exception $e) {
            return new JsonResponse('Error al crear la magnitud. '.$e->getMessage() ,500);
        }
    }

    #[Route('/api/magnitudes/{id}', name: 'update_magnitude', methods:['PUT'])]
    public function updateMagnitude(int $id, Request $request) {
        try {
            if(!$id){
                return new JsonResponse('El id de la magnitud es obligatorio', 400);
            }
            $magnitude = $this->em->getRepository(Magnitudes::class)->find($id);
            if (!$magnitude) {
                return new JsonResponse('Magnitud no encontrada',404);
            }
            $data = json_decode($request->getContent(), true);
            if (!isset($data['unit_id'])) {
                return new JsonResponse('La unidad es obligatoria', 400);
            }
            $unit = $this->em->getRepository('App\Entity\Units')->find($data['unit_id']);
            if (!$unit) {
                return new JsonResponse('Unidad no encontrada', 404);
            }
            $magnitude->setGroupName(($data['group_name']) ? $data['group_name'] : $magnitude->getGroupName());
            $magnitude->setNameEn(isset($data['name_en']) ? $data['name_en'] : $magnitude->getNameEn());
            $magnitude->setAbbreviation(isset($data['abbreviation']) ? $data['abbreviation'] : $magnitude->getAbbreviation());
            $magnitude->setUnit($unit);       
            $magnitude->setWqxCode(isset($data['wqx_code']) ? $data['wqx_code'] : $magnitude->getWqxCode());
            $magnitude->setWmoCode(isset($data['wmo_code']) ? $data['wmo_code'] : $magnitude->getWmoCode());
            $magnitude->setIsoIeeeCode(isset($data['iso_ieee_code']) ? $data['iso_ieee_code'] : $magnitude->getIsoIeeeCode());
            $magnitude->setDecimals(isset($data['decimals']) ? $data['decimals'] : $magnitude->getDecimals());
            $magnitude->setAllowNegative(isset($data['allow_negative']) ? $data['allow_negative'] : $magnitude->getAllowNegative());
            $magnitude->setMinValue(isset($data['min_value']) ? $data['min_value'] : $magnitude->getMinValue());
            $magnitude->setMaxValue(isset($data['max_value']) ? $data['max_value'] : $magnitude->getMaxValue());
            $this->em->flush();
            return new JsonResponse('Magnitud actualizada con éxito',200);
        } catch (Exception $e) {
            return new JsonResponse('Error al actualizar la magnitud. '.$e->getMessage() ,500);
        }
    }

    #[Route('/api/magnitudes/{id}', name: 'delete_magnitude', methods:['DELETE'])]
    public function deleteMagnitude(int $id) {
        try {
            if(!$id){
                return new JsonResponse('El id de la magnitud es obligatorio', 400);
            }
            $magnitude = $this->em->getRepository(Magnitudes::class)->find($id);
            if (!$magnitude) {
                return new JsonResponse('Magnitud no encontrada',404);
            }
            $this->em->remove($magnitude);
            $this->em->flush();
            return new JsonResponse('Magnitud eliminada con éxito',200);
        } catch (Exception $e) {
            return new JsonResponse('Error al eliminar la magnitud. '.$e->getMessage() ,500);
        }
    }
}
