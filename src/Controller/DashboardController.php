<?php

namespace App\Controller;

use App\Service\DeviceManagement\ChangeState;
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

    /**
     * @var ChangeState
     */
    private $changeState;

    public function __construct(\App\Model\Device\Device $device, ChangeState $changeState)
    {
        $this->deviceModel = $device;
        $this->changeState = $changeState;
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
        $deviceId = $request->request->get('deviceId', null);

        if($request->isMethod(Request::METHOD_POST)){
            $device = $this->deviceModel->getDevice($deviceId);
            if(!empty($device)){
                $this->changeState->change($device);
            }
        }

        return new JsonResponse($this->deviceModel->getDeviceDto($deviceId));
    }
}
