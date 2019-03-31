<?php

namespace App\Controller;

use App\Model\Device\DeviceType;
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
            'devices' => $this->deviceModel->findAllDevicesDto(),
        ]);
    }

    /**
     * @Route("/change/state", name="change-state")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function changestate(Request $request)
    {
        $deviceId = 0;
        if($request->isMethod(Request::METHOD_POST)){
            $params = $request->request->all();
            $device = $this->deviceModel->getDevice($params['deviceId']);

            switch ($device->getDeviceType()){
                case DeviceType::DOOR:{
                    switch ($device->getState()){
                        case StateType::UNLOCKED_DOOR:{
                            if($device->getState() == StateType::UNLOCKED_DOOR){
                                $device->setState(StateType::LOCKED_DOOR);
                                $this->deviceModel->updateState($device);
                            }
                            break;
                        }
                        case StateType::LOCKED_DOOR:{
                            if($device->getState() == StateType::LOCKED_DOOR){
                                $device->setState(StateType::UNLOCKED_DOOR);
                                $this->deviceModel->updateState($device);
                            }
                            break;
                        }
                    }
                    break;
                }
                case DeviceType::LIGHT:{
                    switch ($device->getState()){
                        case StateType::TURNED_ON_LIGHT:{
                            if($device->getState() == StateType::TURNED_OFF_LIGHT){
                                $device->setState(StateType::TURNED_ON_LIGHT);
                                $this->deviceModel->updateState($device);
                            }
                            break;
                        }
                        case StateType::TURNED_OFF_LIGHT:{
                            if($device->getState() == StateType::TURNED_ON_LIGHT){
                                $device->setState(StateType::TURNED_OFF_LIGHT);
                                $this->deviceModel->updateState($device);
                            }
                            break;
                        }
                    }
                    break;
                }
            }
        }

        return new JsonResponse($this->deviceModel->getDeviceDto($deviceId));
    }
}
