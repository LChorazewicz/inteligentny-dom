<?php

namespace App\Controller;

use App\Service\DeviceManagement\ChangeState;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
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

    /**
     * @Route("/correct-rotation", name="correct-rotation")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function correcttherotationoftheengine(Request $request)
    {
        $deviceId = $request->request->get('deviceId', null);
        $rotation = $request->request->get('rotation', null);

        if($request->isMethod(Request::METHOD_POST)){
            $device = $this->deviceModel->getDevice($deviceId);
            if(!empty($device)){
                $this->changeState->correctState($device, $rotation);
            }
        }

        return new JsonResponse($this->deviceModel->getDeviceDto($deviceId));
    }

    /**
     * @Route("/move", name="move")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     * @Method("POST")
     */
    public function moveblindsbypercent(Request $request)
    {
        $deviceId = $request->request->get('deviceId', null);
        $percent = $request->request->get('percent', null);

        if($request->isMethod(Request::METHOD_POST)){
            $device = $this->deviceModel->getDevice($deviceId);
            if(!empty($device)){
                try{
                    $this->changeState->moveByPercent($device, $percent);
                }catch (\Exception $e){
                }
            }
        }

        return new JsonResponse($this->deviceModel->getDeviceDto($deviceId));
    }
}
