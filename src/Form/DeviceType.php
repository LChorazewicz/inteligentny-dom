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
            ->add('state', ChoiceType::class, ['choices' => [StateType::LOCKED, StateType::UNLOCKED]])
            ->add('stateValue', IntegerType::class, ['required' => false])
            ->add('deviceType', ChoiceType::class, ['choices' => [
                \App\Model\Device\DeviceType::DOOR,
                \App\Model\Device\DeviceType::ENGINE]
            ])
            ->add('pin', NumberType::class)
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
