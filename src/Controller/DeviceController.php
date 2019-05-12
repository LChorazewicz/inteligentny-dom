<?php

namespace App\Controller;

use App\Entity\Device;
use App\Model\Device\DeviceAction;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/device")
 */
class DeviceController extends AbstractController
{
    /**
     * @var \App\Model\Device\Device
     */
    private $deviceModel;

    public function __construct(\App\Model\Device\Device $deviceModel)
    {
        $this->deviceModel = $deviceModel;
    }

    /**
     * @Route("/", name="device_index", methods={"GET"})
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('device/index.html.twig', [
            'devices' => $this->deviceModel->findAllDevicesDto(),
        ]);
    }

    /**
     * @Route("/device-info", name="device_info", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function deviceinfo(Request $request): Response
    {
        $deviceId = $request->query->getInt('device_id', null);

        return new JsonResponse(!is_null($deviceId) ? $this->deviceModel->findDeviceDto($deviceId) : []);
    }

    /**
     * @Route("/new", name="device_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function new(Request $request): Response
    {
        if($request->isMethod(Request::METHOD_POST)){
            $device = new Device();
            $device->setStatus(true);
            $device->setDeviceType($request->request->getInt("type", null));
            $device->setName($request->request->get("name", null));
            $device->setState($request->request->getInt("state", null));
            $device->setTurns($request->request->getInt("number-of-turns", null));
            $device->setDeviceDirection($request->request->getInt("direction", null));
            $pins = $request->request->get("pins", []);
            $device->setPins(explode(',', $pins));
            $device->setCurrentAction(DeviceAction::INACTIVE);
            $this->deviceModel->addDevice($device);
            return $this->redirectToRoute('device_index');
        }

        return $this->render('device/new.html.twig');
    }

    /**
     * @Route("/{id}", name="device_show", methods={"GET"})
     * @param \App\Model\Device\Device $deviceModel
     * @param $id
     * @return Response
     */
    public function show(\App\Model\Device\Device $deviceModel, $id): Response
    {
        return $this->render('device/show.html.twig', [
            'device' => $deviceModel->getDeviceDto($id),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="device_edit", methods={"GET","POST"})
     * @param Request $request
     * @param $id
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function edit(Request $request, int $id): Response
    {
        $deviceToEdit = $this->deviceModel->getDevice($id);

        if ($request->isMethod(Request::METHOD_POST)) {
            if(!empty($deviceToEdit)){
                $deviceToEdit->setDeviceType($request->request->getInt("type", $deviceToEdit->getDeviceType()));
                $deviceToEdit->setStatus($request->request->getBoolean('status', $deviceToEdit->getStatus()));
                $deviceToEdit->setName($request->request->get("name", $deviceToEdit->getName()));
                $deviceToEdit->setState($request->request->getInt("state", $deviceToEdit->getState()));
                $deviceToEdit->setTurns($request->request->getInt("number-of-turns", $deviceToEdit->getTurns()));
                $deviceToEdit->setDeviceDirection($request->request->getInt("direction", $deviceToEdit->getDeviceDirection()));
                $pins = $request->request->get("pins", $deviceToEdit->getPins());
                $deviceToEdit->setPins(explode(',', $pins));
                $deviceToEdit->setCurrentAction(DeviceAction::INACTIVE);
                $this->deviceModel->addDevice($deviceToEdit);
            }

            return $this->redirectToRoute('device_index', [
                'id' => $id,
            ]);
        }

        return $this->render('device/edit.html.twig', [
            'device' => $deviceToEdit,
        ]);
    }

    /**
     * @Route("/{id}", name="device_delete", methods={"DELETE"})
     * @param Request $request
     * @param Device $device
     * @return Response
     */
    public function delete(Request $request, Device $device): Response
    {
        if ($this->isCsrfTokenValid('delete'.$device->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($device);
            $entityManager->flush();
        }

        return $this->redirectToRoute('device_index');
    }
}
