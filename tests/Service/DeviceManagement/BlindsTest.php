<?php
/**
 * Created by PhpStorm.
 * User: leszek
 * Date: 03.05.19
 * Time: 10:56
 */

namespace App\Tests\Service\DeviceManagement;

use App\Entity\Device;
use App\Model\Device\DeviceDirection;
use App\Model\Device\DeviceType;
use App\Model\Device\StateType;
use App\Service\DeviceManagement\ChangeState;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BlindsTest extends KernelTestCase
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
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $this->changeState = self::$container->get('App\Service\DeviceManagement\ChangeState');
        $this->deviceModel = self::$container->get('App\Model\Device\Device');

        $this->deviceModel->deleteAllDevices();
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     */
    public function tearDown(): void
    {
        parent::tearDown();
        $this->deviceModel->deleteAllDevices();
    }

    /**
     * @param int $deviceState
     * @param int $deviceCurrentTurn
     * @param int $deviceTurns
     * @param int $percent
     * @param int $expectedCurentTurn
     * @param int $expectedState
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     * @dataProvider moveByPercentDataProvider
     */
    public function testMoveByPercent(int $deviceState, int $deviceCurrentTurn, int $deviceTurns, int $percent, int $expectedCurentTurn, int $expectedState)
    {
        $blinds = new Device();
        $blinds
            ->setDeviceDirection(DeviceDirection::LEFT)
            ->setState($deviceState)
            ->setCurrentTurn($deviceCurrentTurn)
            ->setTurns($deviceTurns)
            ->setName('roleta prawa')
            ->setDeviceType(DeviceType::BLINDS)
            ->setStatus(true)
            ->setPins([1,2]);

        $deviceId = $this->deviceModel->addDevice($blinds);

        $device = $this->deviceModel->getDevice($deviceId);

        if(is_null($device)){
            $this->markTestIncomplete('No device found');
        }

        $this->changeState->moveByPercent($device, $percent);
        $deviceAfterUpdate = $this->deviceModel->getDevice($deviceId);

        $this->assertEquals([
            'state' => $expectedState,
            'current_turn' => $expectedCurentTurn
        ], [
            'state' => $deviceAfterUpdate->getState(),
            'current_turn' => $deviceAfterUpdate->getCurrentTurn()
        ]);
    }

    public function moveByPercentDataProvider(): array
    {
        return [

            [ StateType::BLINDS_ROLLED_UP, 10, 10, 10, 1, StateType::BLINDS_ROLLED_UP],
            [ StateType::BLINDS_ROLLED_UP, 10, 10, 20, 2, StateType::BLINDS_ROLLED_UP],
            [ StateType::BLINDS_ROLLED_UP, 10, 10, 30, 3, StateType::BLINDS_ROLLED_UP],
            [ StateType::BLINDS_ROLLED_UP, 10, 10, 40, 4, StateType::BLINDS_ROLLED_UP],
            [ StateType::BLINDS_ROLLED_UP, 10, 10, 50, 5, StateType::BLINDS_ROLLED_UP],
            [ StateType::BLINDS_ROLLED_UP, 10, 10, 60, 6, StateType::BLINDS_ROLLED_UP],
            [ StateType::BLINDS_ROLLED_UP, 10, 10, 70, 7, StateType::BLINDS_ROLLED_UP],
            [ StateType::BLINDS_ROLLED_UP, 10, 10, 80, 8, StateType::BLINDS_ROLLED_UP],
            [ StateType::BLINDS_ROLLED_UP, 10, 10, 90, 9, StateType::BLINDS_ROLLED_UP],
            [ StateType::BLINDS_ROLLED_UP, 10, 10, 100, 10, StateType::BLINDS_ROLLED_UP],

            [StateType::BLINDS_ROLLED_DOWN, 0, 10, 0, 0, StateType::BLINDS_ROLLED_DOWN],
            [StateType::BLINDS_ROLLED_DOWN, 0, 10, 10, 1, StateType::BLINDS_ROLLED_UP],
            [StateType::BLINDS_ROLLED_DOWN, 0, 10, 20, 2, StateType::BLINDS_ROLLED_UP],
            [StateType::BLINDS_ROLLED_DOWN, 0, 10, 30, 3, StateType::BLINDS_ROLLED_UP],
            [StateType::BLINDS_ROLLED_DOWN, 0, 10, 40, 4, StateType::BLINDS_ROLLED_UP],
            [StateType::BLINDS_ROLLED_DOWN, 0, 10, 50, 5, StateType::BLINDS_ROLLED_UP],
            [StateType::BLINDS_ROLLED_DOWN, 0, 10, 60, 6, StateType::BLINDS_ROLLED_UP],
            [StateType::BLINDS_ROLLED_DOWN, 0, 10, 70, 7, StateType::BLINDS_ROLLED_UP],
            [StateType::BLINDS_ROLLED_DOWN, 0, 10, 80, 8, StateType::BLINDS_ROLLED_UP],
            [StateType::BLINDS_ROLLED_DOWN, 0, 10, 90, 9, StateType::BLINDS_ROLLED_UP],
            [StateType::BLINDS_ROLLED_DOWN, 0, 10, 100, 10, StateType::BLINDS_ROLLED_UP],

            [ StateType::BLINDS_ROLLED_UP, 10, 10, 0, 0, StateType::BLINDS_ROLLED_DOWN],
            [ StateType::BLINDS_ROLLED_UP, 1, 10, 0, 0, StateType::BLINDS_ROLLED_DOWN],
        ];
    }
}
