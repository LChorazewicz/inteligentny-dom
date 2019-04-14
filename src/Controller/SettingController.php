<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SettingController extends AbstractController
{
    /**
     * @Route("/setting", name="setting")
     */
    public function index()
    {
        return $this->render('setting/index.html.twig', [
            'controller_name' => 'SettingController',
        ]);
    }

    /**
     * @Route("/motor-up", name="motor-up")
     */
    public function motorup()
    {
        $command = "cd ../src/Scripts && python motor.py ";
        exec($command);
        return $this->render('setting/index.html.twig', [
            'controller_name' => 'SettingController',
        ]);
    }

    /**
     * @Route("/motor-down", name="motor-down")
     */
    public function motordown()
    {
        $command = "cd ../src/Scripts && python motor2.py ";
        exec($command);
        return $this->render('setting/index.html.twig', [
            'controller_name' => 'SettingController',
        ]);
    }
}
