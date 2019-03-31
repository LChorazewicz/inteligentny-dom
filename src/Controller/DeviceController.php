<?php

namespace App\Controller;

use App\Entity\Device;
use App\Form\DeviceType;
use App\Repository\DeviceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/device")
 */
class DeviceController extends AbstractController
{
    /**
     * @Route("/", name="device_index", methods={"GET"})
     * @param \App\Model\Device\Device $deviceModel
     * @return Response
     */
    public function index(\App\Model\Device\Device $deviceModel): Response
    {
        return $this->render('device/index.html.twig', [
            'devices' => $deviceModel->findAllDevicesDto(),
        ]);
    }

    /**
     * @Route("/new", name="device_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $device = new Device();
        $form = $this->createForm(DeviceType::class, $device);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($device);
            $entityManager->flush();

            return $this->redirectToRoute('device_index');
        }

        return $this->render('device/new.html.twig', [
            'device' => $device,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="device_show", methods={"GET"})
     * @param \App\Model\Device\Device $deviceModel
     * @param $id
     * @return Response
     */
    public function show(\App\Model\Device\Device $deviceModel, $id): Response
    {
        return $this->render('device/show.html.twig', [
            'device' => $deviceModel->getDeviceDto($id),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="device_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Device $device
     * @return Response
     */
    public function edit(Request $request, Device $device): Response
    {
        $form = $this->createForm(DeviceType::class, $device);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('device_index', [
                'id' => $device->getId(),
            ]);
        }

        return $this->render('device/edit.html.twig', [
            'device' => $device,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="device_delete", methods={"DELETE"})
     * @param Request $request
     * @param Device $device
     * @return Response
     */
    public function delete(Request $request, Device $device): Response
    {
        if ($this->isCsrfTokenValid('delete'.$device->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($device);
            $entityManager->flush();
        }

        return $this->redirectToRoute('device_index');
    }
}
