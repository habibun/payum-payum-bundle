<?php

namespace App\Form\Type;

use App\Entity\GatewayConfig;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class StripeGatewayConfigType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('factoryName', TextType::class, [
                'disabled' => true,
                'data' => 'stripe_checkout',
            ])
            ->add('gatewayName', TextType::class)
            ->add('config', ConfigStripeGatewayConfigType::class, [
                'label' => false,
                'auto_initialize' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GatewayConfig::class,
        ]);
    }
}
