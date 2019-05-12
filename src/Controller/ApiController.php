<?php

namespace App\Controller;

use App\Service\DeviceManagement\ChangeState;
use App\Service\Producers\DeviceProducer;
use App\Tools\Logger;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ApiController
 * @package App\Controller
 * @Route("/api")
 */
class ApiController extends AbstractController
{

    /**
     * @var \App\Model\Device\Device
     */
    private $deviceModel;

    /**
     * @var ChangeState
     */
    private $changeState;

    /**
     * @var \Monolog\Logger
     */
    private $logger;

    /**
     * ApiController constructor.
     * @param \App\Model\Device\Device $device
     * @param ChangeState $changeState
     * @throws \Exception
     */
    public function __construct(\App\Model\Device\Device $device, ChangeState $changeState)
    {
        $this->deviceModel = $device;
        $this->changeState = $changeState;
        $this->logger = Logger::getLogger('api/api', Logger::INFO, 'api');
    }

    /**
     * @param Request $request
     * @Route("/device/change-state", name="api-device-change-state")
     * @return JsonResponse
     * @throws \Exception
     */
    public function changedevicestate(Request $request)
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
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     * @Route("/device/correct-rotation", name="api-device-correct-rotation")
     */
    public function correctrotation(Request $request)
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
     * @param Request $request
     * @param DeviceProducer $deviceProducer
     * @return JsonResponse
     * @Route("/device/set-rotation", name="api-device-set-rotation")
     */
    public function setrotation(Request $request, DeviceProducer $deviceProducer)
    {
        $deviceId = $request->request->get('deviceId', null);
        $step = $request->request->get('step', null);

        if($request->isMethod(Request::METHOD_POST)){
            $device = $this->deviceModel->getDevice($deviceId);
            if(!empty($device)){
                try{
                    $message = new AMQPMessage();
                    $message->setBody(json_encode(['device_id' => $deviceId, 'step' => $step]));
                    $deviceProducer->publish($message);
                    $deviceProducer->disconnect();
                }catch (\Exception $e){
                    $this->logger->error($e->getMessage());
                }
            }
        }

        return new JsonResponse($this->deviceModel->getDeviceDto($deviceId));
    }
}
