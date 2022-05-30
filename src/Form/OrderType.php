<?php

namespace App\Form;

use App\Entity\Address;
use App\Entity\Carrier;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {   //dd($options);
        $user = $options['user'];

        $builder
            ->add('address',EntityType::class,[
                    'label' => false,//'Choisissez votre adresse de livraison',
                    'required' => true,
                    'class' => Address::class,
                    'choices' => $user->getAddresses(),
                    'multiple' =>false,
                    'expanded' => true])

            ->add('carriers',EntityType::class,[
                    'label' => 'Choisissez votre transporteur',
                    'required' => true,
                    'class' => Carrier::class,
                    'multiple' =>false,
                    'expanded' => true])
            ->add('submit', SubmitType::class,[
                'label' => 'Valider ma commande',
                'attr' => [
                    'class' => 'btn btn-success btn-block'] ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
            'user' => array()
        ]);
    }
}
