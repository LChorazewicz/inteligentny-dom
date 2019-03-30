<?php

namespace App\Controller;

use App\Model\Device\StateType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /**
     * @var \App\Model\Device\Device
     */
    private $deviceModel;

    public function __construct(\App\Model\Device\Device $device)
    {
        $this->deviceModel = $device;
    }
    /**
     * @Route("/", name="home")
     * @Route("/dashboard", name="dashboard")
     */
    public function index()
    {
        return $this->render('dashboard/index.html.twig', [
            'devices' => $this->deviceModel->getAllDoorDevicesDto(),
        ]);
    }

    /**
     * @Route("/change/state", name="change-state")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function changedoorstate(Request $request)
    {
        if($request->isMethod(Request::METHOD_POST)){
            $params = $request->request->all();
            $device = $this->deviceModel->getDevice($params['deviceId']);
            switch ($device->getState()){
                case StateType::UNLOCKED:{
                    if($device->getState() == StateType::UNLOCKED){
                        $device->setState(StateType::LOCKED);
                        $this->deviceModel->updateState($device);
                    }
                    break;
                }
                case StateType::LOCKED:{
                    if($device->getState() == StateType::LOCKED){
                        $device->setState(StateType::UNLOCKED);
                        $this->deviceModel->updateState($device);
                    }
                    break;
                }
            }
        }

        return new JsonResponse([
            'deviceId' => $device->getId(),
            'state' => $device->getState()
        ]);
    }

    private function mapToDeviceState($newValue)
    {
        $return = null;
        switch ($newValue){
            case 'lock':{
                $return = 'lock';
                break;
            }
            case 'unlock':{
                $return = 'unlock';
                break;
            }
        }
        return $return;
    }
}
