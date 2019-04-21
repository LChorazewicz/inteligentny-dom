<?php
/**
 * Created by PhpStorm.
 * User: leszek
 * Date: 31.03.19
 * Time: 22:52
 */

namespace App\Service\DeviceManagement;


use App\Model\Device\StateType;
use Psr\Log\LoggerInterface;

class Light implements DeviceChangeStateInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Light constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param int $state
     * @param array $pins
     * @param int $turns
     * @return string|null
     * @throws \Exception
     */
    public function changeState(int $state, array $pins, int $turns)
    {
        $outputState = null;
        $command = "cd ../src/Scripts && python light.py " . implode(',', $pins);
        $this->logger->info("Change light state in progress", ['state' => $state, 'pin' => $pins]);
        switch ($state){
            case StateType::LIGHT_TURNED_ON:{
                $command = $command  . " 1";
                $this->logger->info("run ", ['command' => $command]);
                $outputState = exec($command);
                break;
            }
            case StateType::LIGHT_TURNED_OFF:{
                $command = $command  . " 2";
                $this->logger->info("run ", ['command' => $command]);
                $outputState = exec($command);
                break;
            }
            default:
                throw new \Exception("Unknown state type " . $state);
        }

        $this->logger->info('response status', ['output' => $outputState]);

        return $outputState;
    }
}