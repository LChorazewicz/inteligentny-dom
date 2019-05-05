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
     * @param int $expectedCurrentTurn
     * @param int $expectedState
     * @param string $message
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     * @dataProvider moveByPercentDataProvider
     */
    public function testMoveByStep(int $deviceState,
                                      int $deviceCurrentTurn, int $deviceTurns, int $expectedCurrentTurn, int $expectedState, string $message)
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

        $this->changeState->moveByStep($device, $expectedCurrentTurn);
        $deviceAfterUpdate = $this->deviceModel->getDevice($deviceId);

        $this->assertEquals([
            'state' => $expectedState,
            'current_turn' => $expectedCurrentTurn
        ], [
            'state' => $deviceAfterUpdate->getState(),
            'current_turn' => $deviceAfterUpdate->getCurrentTurn()
        ], $message);
    }

    public function moveByPercentDataProvider(): array
    {
        return [

            [ StateType::BLINDS_ROLLED_UP, 10, 10, 1, StateType::BLINDS_ROLLED_UP, 'case #1'],
            [ StateType::BLINDS_ROLLED_UP, 10, 10, 2, StateType::BLINDS_ROLLED_UP, 'case #2'],
            [ StateType::BLINDS_ROLLED_UP, 10, 10, 3, StateType::BLINDS_ROLLED_UP, 'case #3'],
            [ StateType::BLINDS_ROLLED_UP, 10, 10, 4, StateType::BLINDS_ROLLED_UP, 'case #4'],
            [ StateType::BLINDS_ROLLED_UP, 10, 10, 5, StateType::BLINDS_ROLLED_UP, 'case #5'],
            [ StateType::BLINDS_ROLLED_UP, 10, 10, 6, StateType::BLINDS_ROLLED_UP, 'case #6'],
            [ StateType::BLINDS_ROLLED_UP, 10, 10, 7, StateType::BLINDS_ROLLED_UP, 'case #7'],
            [ StateType::BLINDS_ROLLED_UP, 10, 10, 8, StateType::BLINDS_ROLLED_UP, 'case #8'],
            [ StateType::BLINDS_ROLLED_UP, 10, 10, 9, StateType::BLINDS_ROLLED_UP, 'case #9'],
            [ StateType::BLINDS_ROLLED_UP, 10, 10, 10, StateType::BLINDS_ROLLED_UP, 'case #10'],

            [StateType::BLINDS_ROLLED_DOWN, 0, 10, 0, StateType::BLINDS_ROLLED_DOWN, 'case #11'],
            [StateType::BLINDS_ROLLED_DOWN, 0, 10, 1, StateType::BLINDS_ROLLED_UP, 'case #12'],
            [StateType::BLINDS_ROLLED_DOWN, 0, 10, 2, StateType::BLINDS_ROLLED_UP, 'case #13'],
            [StateType::BLINDS_ROLLED_DOWN, 0, 10, 3, StateType::BLINDS_ROLLED_UP, 'case #14'],
            [StateType::BLINDS_ROLLED_DOWN, 0, 10, 4, StateType::BLINDS_ROLLED_UP, 'case #15'],
            [StateType::BLINDS_ROLLED_DOWN, 0, 10, 5, StateType::BLINDS_ROLLED_UP, 'case #16'],
            [StateType::BLINDS_ROLLED_DOWN, 0, 10, 6, StateType::BLINDS_ROLLED_UP, 'case #17'],
            [StateType::BLINDS_ROLLED_DOWN, 0, 10, 7, StateType::BLINDS_ROLLED_UP, 'case #18'],
            [StateType::BLINDS_ROLLED_DOWN, 0, 10, 8, StateType::BLINDS_ROLLED_UP, 'case #19'],
            [StateType::BLINDS_ROLLED_DOWN, 0, 10, 9, StateType::BLINDS_ROLLED_UP, 'case #20'],
            [StateType::BLINDS_ROLLED_DOWN, 0, 10, 10, StateType::BLINDS_ROLLED_UP, 'case #21'],

            [ StateType::BLINDS_ROLLED_UP, 10, 10, 0, StateType::BLINDS_ROLLED_DOWN, 'case #22'],
            [ StateType::BLINDS_ROLLED_UP, 1, 10, 0, StateType::BLINDS_ROLLED_DOWN, 'case #23'],
//----------------------------------------------------------------------------------------------------------------------

            [ StateType::BLINDS_ROLLED_UP, 10, 29, 1, StateType::BLINDS_ROLLED_UP, 'case #24'],
            [ StateType::BLINDS_ROLLED_UP, 10, 29, 2, StateType::BLINDS_ROLLED_UP, 'case #25'],
            [ StateType::BLINDS_ROLLED_UP, 10, 29, 3, StateType::BLINDS_ROLLED_UP, 'case #26'],
            [ StateType::BLINDS_ROLLED_UP, 10, 29, 4, StateType::BLINDS_ROLLED_UP, 'case #27'],
            [ StateType::BLINDS_ROLLED_UP, 10, 29, 5, StateType::BLINDS_ROLLED_UP, 'case #28'],
            [ StateType::BLINDS_ROLLED_UP, 10, 29, 6, StateType::BLINDS_ROLLED_UP, 'case #29'],
            [ StateType::BLINDS_ROLLED_UP, 10, 29, 7, StateType::BLINDS_ROLLED_UP, 'case #30'],
            [ StateType::BLINDS_ROLLED_UP, 10, 29, 8, StateType::BLINDS_ROLLED_UP, 'case #31'],
            [ StateType::BLINDS_ROLLED_UP, 10, 29, 9, StateType::BLINDS_ROLLED_UP, 'case #32'],
            [ StateType::BLINDS_ROLLED_UP, 10, 29, 29, StateType::BLINDS_ROLLED_UP, 'case #33'],

            [StateType::BLINDS_ROLLED_DOWN, 0, 29, 0, StateType::BLINDS_ROLLED_DOWN, 'case #34'],
            [StateType::BLINDS_ROLLED_DOWN, 0, 29, 1, StateType::BLINDS_ROLLED_UP, 'case #35'],
            [StateType::BLINDS_ROLLED_DOWN, 0, 29, 2, StateType::BLINDS_ROLLED_UP, 'case #36'],
            [StateType::BLINDS_ROLLED_DOWN, 0, 29, 3, StateType::BLINDS_ROLLED_UP, 'case #37'],
            [StateType::BLINDS_ROLLED_DOWN, 0, 29, 4, StateType::BLINDS_ROLLED_UP, 'case #38'],
            [StateType::BLINDS_ROLLED_DOWN, 0, 29, 5, StateType::BLINDS_ROLLED_UP, 'case #39'],
            [StateType::BLINDS_ROLLED_DOWN, 0, 29, 6, StateType::BLINDS_ROLLED_UP, 'case #40'],
            [StateType::BLINDS_ROLLED_DOWN, 0, 29, 7, StateType::BLINDS_ROLLED_UP, 'case #41'],
            [StateType::BLINDS_ROLLED_DOWN, 0, 29, 8, StateType::BLINDS_ROLLED_UP, 'case #42'],
            [StateType::BLINDS_ROLLED_DOWN, 0, 29, 9, StateType::BLINDS_ROLLED_UP, 'case #43'],
            [StateType::BLINDS_ROLLED_DOWN, 0, 29, 10, StateType::BLINDS_ROLLED_UP, 'case #44'],

            [ StateType::BLINDS_ROLLED_UP, 10, 29, 0, StateType::BLINDS_ROLLED_DOWN, 'case #45'],
            [ StateType::BLINDS_ROLLED_UP, 1, 29, 0, StateType::BLINDS_ROLLED_DOWN, 'case #46'],
        ];
    }
}
