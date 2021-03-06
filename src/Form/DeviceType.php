<?php

namespace App\Form;

use App\Entity\Device;
use App\Model\Device\StateType;
use App\Model\Device\StatusType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeviceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('deviceType', ChoiceType::class, ['choices' => [
                'Door' => \App\Model\Device\DeviceType::DOOR,
                'Engine' => \App\Model\Device\DeviceType::ENGINE,
                'Light' => \App\Model\Device\DeviceType::LIGHT
            ]])
            ->add('state', ChoiceType::class, ['choices' => [
                'Locked' => StateType::DOOR_LOCKED,
                'Unlocked' => StateType::DOOR_UNLOCKED,
                'Turned on' => StateType::LIGHT_TURNED_ON,
                'Turned off' => StateType::LIGHT_TURNED_OFF,
            ]])
            ->add('stateValue', IntegerType::class, ['required' => false])

            ->add('pins', NumberType::class)
            ->add('status', ChoiceType::class, ['choices' => [
                StatusType::ACTIVE => 'active',
                StatusType::INACTIVE => 'inactive',
            ]])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Device::class,
        ]);
    }
}
