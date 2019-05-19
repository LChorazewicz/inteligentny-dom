<?php
/**
 * Created by PhpStorm.
 * User: leszek
 * Date: 03.05.19
 * Time: 10:56
 */

namespace App\Tests\Service\DeviceManagement;

use App\Entity\Device;
use App\Model\Device\DeviceAction;
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
     * @param int $direction
     * @param string $message
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     * @dataProvider moveByPercentDataProvider
     */
    public function testMoveByStep(int $deviceState,
                                      int $deviceCurrentTurn, int $deviceTurns, int $expectedCurrentTurn, int $expectedState, int $direction, string $message)
    {
        $blinds = new Device();
        $blinds
            ->setDeviceDirection($direction)
            ->setState($deviceState)
            ->setCurrentTurn($deviceCurrentTurn)
            ->setTurns($deviceTurns)
            ->setName('roleta prawa')
            ->setDeviceType(DeviceType::BLINDS)
            ->setStatus(true)
            ->setStatus(DeviceAction::INACTIVE)
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

            [ StateType::BLINDS_ROLLED_UP, 10, 10, 1, StateType::BLINDS_ROLLED_UP, DeviceDirection::LEFT, 'case #1'],
            [ StateType::BLINDS_ROLLED_UP, 10, 10, 2, StateType::BLINDS_ROLLED_UP, DeviceDirection::LEFT, 'case #2'],
            [ StateType::BLINDS_ROLLED_UP, 10, 10, 3, StateType::BLINDS_ROLLED_UP, DeviceDirection::LEFT, 'case #3'],
            [ StateType::BLINDS_ROLLED_UP, 10, 10, 4, StateType::BLINDS_ROLLED_UP, DeviceDirection::LEFT, 'case #4'],
            [ StateType::BLINDS_ROLLED_UP, 10, 10, 5, StateType::BLINDS_ROLLED_UP, DeviceDirection::LEFT, 'case #5'],
            [ StateType::BLINDS_ROLLED_UP, 10, 10, 6, StateType::BLINDS_ROLLED_UP, DeviceDirection::LEFT, 'case #6'],
            [ StateType::BLINDS_ROLLED_UP, 10, 10, 7, StateType::BLINDS_ROLLED_UP, DeviceDirection::LEFT, 'case #7'],
            [ StateType::BLINDS_ROLLED_UP, 10, 10, 8, StateType::BLINDS_ROLLED_UP, DeviceDirection::LEFT, 'case #8'],
            [ StateType::BLINDS_ROLLED_UP, 10, 10, 9, StateType::BLINDS_ROLLED_UP, DeviceDirection::LEFT, 'case #9'],
            [ StateType::BLINDS_ROLLED_UP, 10, 10, 10, StateType::BLINDS_ROLLED_UP, DeviceDirection::LEFT, 'case #10'],

            [StateType::BLINDS_ROLLED_DOWN, 0, 10, 0, StateType::BLINDS_ROLLED_DOWN, DeviceDirection::LEFT, 'case #11'],
            [StateType::BLINDS_ROLLED_DOWN, 0, 10, 1, StateType::BLINDS_ROLLED_UP, DeviceDirection::LEFT, 'case #12'],
            [StateType::BLINDS_ROLLED_DOWN, 0, 10, 2, StateType::BLINDS_ROLLED_UP, DeviceDirection::LEFT, 'case #13'],
            [StateType::BLINDS_ROLLED_DOWN, 0, 10, 3, StateType::BLINDS_ROLLED_UP, DeviceDirection::LEFT, 'case #14'],
            [StateType::BLINDS_ROLLED_DOWN, 0, 10, 4, StateType::BLINDS_ROLLED_UP, DeviceDirection::LEFT, 'case #15'],
            [StateType::BLINDS_ROLLED_DOWN, 0, 10, 5, StateType::BLINDS_ROLLED_UP, DeviceDirection::LEFT, 'case #16'],
            [StateType::BLINDS_ROLLED_DOWN, 0, 10, 6, StateType::BLINDS_ROLLED_UP, DeviceDirection::LEFT, 'case #17'],
            [StateType::BLINDS_ROLLED_DOWN, 0, 10, 7, StateType::BLINDS_ROLLED_UP, DeviceDirection::LEFT, 'case #18'],
            [StateType::BLINDS_ROLLED_DOWN, 0, 10, 8, StateType::BLINDS_ROLLED_UP, DeviceDirection::LEFT, 'case #19'],
            [StateType::BLINDS_ROLLED_DOWN, 0, 10, 9, StateType::BLINDS_ROLLED_UP, DeviceDirection::LEFT, 'case #20'],
            [StateType::BLINDS_ROLLED_DOWN, 0, 10, 10, StateType::BLINDS_ROLLED_UP, DeviceDirection::LEFT, 'case #21'],

            [ StateType::BLINDS_ROLLED_UP, 10, 10, 0, StateType::BLINDS_ROLLED_DOWN, DeviceDirection::LEFT, 'case #22'],
            [ StateType::BLINDS_ROLLED_UP, 1, 10, 0, StateType::BLINDS_ROLLED_DOWN, DeviceDirection::LEFT, 'case #23'],

            [ StateType::BLINDS_ROLLED_UP, 10, 29, 1, StateType::BLINDS_ROLLED_UP, DeviceDirection::LEFT, 'case #24'],
            [ StateType::BLINDS_ROLLED_UP, 10, 29, 2, StateType::BLINDS_ROLLED_UP, DeviceDirection::LEFT, 'case #25'],
            [ StateType::BLINDS_ROLLED_UP, 10, 29, 3, StateType::BLINDS_ROLLED_UP, DeviceDirection::LEFT, 'case #26'],
            [ StateType::BLINDS_ROLLED_UP, 10, 29, 4, StateType::BLINDS_ROLLED_UP, DeviceDirection::LEFT, 'case #27'],
            [ StateType::BLINDS_ROLLED_UP, 10, 29, 5, StateType::BLINDS_ROLLED_UP, DeviceDirection::LEFT, 'case #28'],
            [ StateType::BLINDS_ROLLED_UP, 10, 29, 6, StateType::BLINDS_ROLLED_UP, DeviceDirection::LEFT, 'case #29'],
            [ StateType::BLINDS_ROLLED_UP, 10, 29, 7, StateType::BLINDS_ROLLED_UP, DeviceDirection::LEFT, 'case #30'],
            [ StateType::BLINDS_ROLLED_UP, 10, 29, 8, StateType::BLINDS_ROLLED_UP, DeviceDirection::LEFT, 'case #31'],
            [ StateType::BLINDS_ROLLED_UP, 10, 29, 9, StateType::BLINDS_ROLLED_UP, DeviceDirection::LEFT, 'case #32'],
            [ StateType::BLINDS_ROLLED_UP, 10, 29, 29, StateType::BLINDS_ROLLED_UP, DeviceDirection::LEFT, 'case #33'],

            [StateType::BLINDS_ROLLED_DOWN, 0, 29, 0, StateType::BLINDS_ROLLED_DOWN, DeviceDirection::LEFT, 'case #34'],
            [StateType::BLINDS_ROLLED_DOWN, 0, 29, 1, StateType::BLINDS_ROLLED_UP, DeviceDirection::LEFT, 'case #35'],
            [StateType::BLINDS_ROLLED_DOWN, 0, 29, 2, StateType::BLINDS_ROLLED_UP, DeviceDirection::LEFT, 'case #36'],
            [StateType::BLINDS_ROLLED_DOWN, 0, 29, 3, StateType::BLINDS_ROLLED_UP, DeviceDirection::LEFT, 'case #37'],
            [StateType::BLINDS_ROLLED_DOWN, 0, 29, 4, StateType::BLINDS_ROLLED_UP, DeviceDirection::LEFT, 'case #38'],
            [StateType::BLINDS_ROLLED_DOWN, 0, 29, 5, StateType::BLINDS_ROLLED_UP, DeviceDirection::LEFT, 'case #39'],
            [StateType::BLINDS_ROLLED_DOWN, 0, 29, 6, StateType::BLINDS_ROLLED_UP, DeviceDirection::LEFT, 'case #40'],
            [StateType::BLINDS_ROLLED_DOWN, 0, 29, 7, StateType::BLINDS_ROLLED_UP, DeviceDirection::LEFT, 'case #41'],
            [StateType::BLINDS_ROLLED_DOWN, 0, 29, 8, StateType::BLINDS_ROLLED_UP, DeviceDirection::LEFT, 'case #42'],
            [StateType::BLINDS_ROLLED_DOWN, 0, 29, 9, StateType::BLINDS_ROLLED_UP, DeviceDirection::LEFT, 'case #43'],
            [StateType::BLINDS_ROLLED_DOWN, 0, 29, 10, StateType::BLINDS_ROLLED_UP, DeviceDirection::LEFT, 'case #44'],

            [ StateType::BLINDS_ROLLED_UP, 10, 29, 0, StateType::BLINDS_ROLLED_DOWN, DeviceDirection::LEFT, 'case #45'],
            [ StateType::BLINDS_ROLLED_UP, 1, 29, 0, StateType::BLINDS_ROLLED_DOWN, DeviceDirection::LEFT, 'case #46'],

            //---------------------------------------------------------------------DeviceDirection::RIGHT---------------
            [ StateType::BLINDS_ROLLED_UP, 10, 10, 1, StateType::BLINDS_ROLLED_UP, DeviceDirection::RIGHT, 'case #47'],
            [ StateType::BLINDS_ROLLED_UP, 10, 10, 2, StateType::BLINDS_ROLLED_UP, DeviceDirection::RIGHT, 'case #48'],
            [ StateType::BLINDS_ROLLED_UP, 10, 10, 3, StateType::BLINDS_ROLLED_UP, DeviceDirection::RIGHT, 'case #49'],
            [ StateType::BLINDS_ROLLED_UP, 10, 10, 4, StateType::BLINDS_ROLLED_UP, DeviceDirection::RIGHT, 'case #50'],
            [ StateType::BLINDS_ROLLED_UP, 10, 10, 5, StateType::BLINDS_ROLLED_UP, DeviceDirection::RIGHT, 'case #51'],
            [ StateType::BLINDS_ROLLED_UP, 10, 10, 6, StateType::BLINDS_ROLLED_UP, DeviceDirection::RIGHT, 'case #52'],
            [ StateType::BLINDS_ROLLED_UP, 10, 10, 7, StateType::BLINDS_ROLLED_UP, DeviceDirection::RIGHT, 'case #53'],
            [ StateType::BLINDS_ROLLED_UP, 10, 10, 8, StateType::BLINDS_ROLLED_UP, DeviceDirection::RIGHT, 'case #54'],
            [ StateType::BLINDS_ROLLED_UP, 10, 10, 9, StateType::BLINDS_ROLLED_UP, DeviceDirection::RIGHT, 'case #55'],
            [ StateType::BLINDS_ROLLED_UP, 10, 10, 10, StateType::BLINDS_ROLLED_UP, DeviceDirection::RIGHT, 'case #56'],

            [StateType::BLINDS_ROLLED_DOWN, 0, 10, 0, StateType::BLINDS_ROLLED_DOWN, DeviceDirection::RIGHT, 'case #57'],
            [StateType::BLINDS_ROLLED_DOWN, 0, 10, 1, StateType::BLINDS_ROLLED_UP, DeviceDirection::RIGHT, 'case #58'],
            [StateType::BLINDS_ROLLED_DOWN, 0, 10, 2, StateType::BLINDS_ROLLED_UP, DeviceDirection::RIGHT, 'case #59'],
            [StateType::BLINDS_ROLLED_DOWN, 0, 10, 3, StateType::BLINDS_ROLLED_UP, DeviceDirection::RIGHT, 'case #60'],
            [StateType::BLINDS_ROLLED_DOWN, 0, 10, 4, StateType::BLINDS_ROLLED_UP, DeviceDirection::RIGHT, 'case #61'],
            [StateType::BLINDS_ROLLED_DOWN, 0, 10, 5, StateType::BLINDS_ROLLED_UP, DeviceDirection::RIGHT, 'case #62'],
            [StateType::BLINDS_ROLLED_DOWN, 0, 10, 6, StateType::BLINDS_ROLLED_UP, DeviceDirection::RIGHT, 'case #63'],
            [StateType::BLINDS_ROLLED_DOWN, 0, 10, 7, StateType::BLINDS_ROLLED_UP, DeviceDirection::RIGHT, 'case #64'],
            [StateType::BLINDS_ROLLED_DOWN, 0, 10, 8, StateType::BLINDS_ROLLED_UP, DeviceDirection::RIGHT, 'case #65'],
            [StateType::BLINDS_ROLLED_DOWN, 0, 10, 9, StateType::BLINDS_ROLLED_UP, DeviceDirection::RIGHT, 'case #66'],
            [StateType::BLINDS_ROLLED_DOWN, 0, 10, 10, StateType::BLINDS_ROLLED_UP, DeviceDirection::RIGHT, 'case #67'],

            [ StateType::BLINDS_ROLLED_UP, 10, 10, 0, StateType::BLINDS_ROLLED_DOWN, DeviceDirection::RIGHT, 'case #68'],
            [ StateType::BLINDS_ROLLED_UP, 1, 10, 0, StateType::BLINDS_ROLLED_DOWN, DeviceDirection::RIGHT, 'case #69'],

            [ StateType::BLINDS_ROLLED_UP, 10, 29, 1, StateType::BLINDS_ROLLED_UP, DeviceDirection::RIGHT, 'case #70'],
            [ StateType::BLINDS_ROLLED_UP, 10, 29, 2, StateType::BLINDS_ROLLED_UP, DeviceDirection::RIGHT, 'case #71'],
            [ StateType::BLINDS_ROLLED_UP, 10, 29, 3, StateType::BLINDS_ROLLED_UP, DeviceDirection::RIGHT, 'case #72'],
            [ StateType::BLINDS_ROLLED_UP, 10, 29, 4, StateType::BLINDS_ROLLED_UP, DeviceDirection::RIGHT, 'case #73'],
            [ StateType::BLINDS_ROLLED_UP, 10, 29, 5, StateType::BLINDS_ROLLED_UP, DeviceDirection::RIGHT, 'case #74'],
            [ StateType::BLINDS_ROLLED_UP, 10, 29, 6, StateType::BLINDS_ROLLED_UP, DeviceDirection::RIGHT, 'case #75'],
            [ StateType::BLINDS_ROLLED_UP, 10, 29, 7, StateType::BLINDS_ROLLED_UP, DeviceDirection::RIGHT, 'case #76'],
            [ StateType::BLINDS_ROLLED_UP, 10, 29, 8, StateType::BLINDS_ROLLED_UP, DeviceDirection::RIGHT, 'case #77'],
            [ StateType::BLINDS_ROLLED_UP, 10, 29, 9, StateType::BLINDS_ROLLED_UP, DeviceDirection::RIGHT, 'case #78'],
            [ StateType::BLINDS_ROLLED_UP, 10, 29, 29, StateType::BLINDS_ROLLED_UP, DeviceDirection::RIGHT, 'case #79'],

            [StateType::BLINDS_ROLLED_DOWN, 0, 29, 0, StateType::BLINDS_ROLLED_DOWN, DeviceDirection::RIGHT, 'case #80'],
            [StateType::BLINDS_ROLLED_DOWN, 0, 29, 1, StateType::BLINDS_ROLLED_UP, DeviceDirection::RIGHT, 'case #81'],
            [StateType::BLINDS_ROLLED_DOWN, 0, 29, 2, StateType::BLINDS_ROLLED_UP, DeviceDirection::RIGHT, 'case #82'],
            [StateType::BLINDS_ROLLED_DOWN, 0, 29, 3, StateType::BLINDS_ROLLED_UP, DeviceDirection::RIGHT, 'case #83'],
            [StateType::BLINDS_ROLLED_DOWN, 0, 29, 4, StateType::BLINDS_ROLLED_UP, DeviceDirection::RIGHT, 'case #84'],
            [StateType::BLINDS_ROLLED_DOWN, 0, 29, 5, StateType::BLINDS_ROLLED_UP, DeviceDirection::RIGHT, 'case #85'],
            [StateType::BLINDS_ROLLED_DOWN, 0, 29, 6, StateType::BLINDS_ROLLED_UP, DeviceDirection::RIGHT, 'case #86'],
            [StateType::BLINDS_ROLLED_DOWN, 0, 29, 7, StateType::BLINDS_ROLLED_UP, DeviceDirection::RIGHT, 'case #87'],
            [StateType::BLINDS_ROLLED_DOWN, 0, 29, 8, StateType::BLINDS_ROLLED_UP, DeviceDirection::RIGHT, 'case #88'],
            [StateType::BLINDS_ROLLED_DOWN, 0, 29, 9, StateType::BLINDS_ROLLED_UP, DeviceDirection::RIGHT, 'case #89'],
            [StateType::BLINDS_ROLLED_DOWN, 0, 29, 10, StateType::BLINDS_ROLLED_UP, DeviceDirection::RIGHT, 'case #90'],

            [ StateType::BLINDS_ROLLED_UP, 10, 29, 0, StateType::BLINDS_ROLLED_DOWN, DeviceDirection::RIGHT, 'case #91'],
            [ StateType::BLINDS_ROLLED_UP, 1, 29, 0, StateType::BLINDS_ROLLED_DOWN, DeviceDirection::RIGHT, 'case #92'],
        ];
    }
}
