<?php

namespace App\Form;

use App\Entity\Address;
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

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',TextType::class,[
                'label'=>'Quel nom souhaitez vous donner à votre adresse?',
                'attr'=>['placeholder' => 'Nommer votre adresse']])

            ->add('firstname',TextType::class,[
                'label'=>'Entrez votre prenom',
                'attr'=>['placeholder' => 'Entrez votre prenom']])

            ->add('lastname',TextType::class,[
                'label'=>'Votre nom',
                'attr'=>['placeholder' => 'Entrez votre nom']])

            ->add('company',TextType::class,[
                'label'=>'Votre société',
                'required' => false,
                'attr'=>['placeholder' => '(facultatif)Nommer votre société']])

            ->add('address',TextType::class,[
                'label'=>'Votre adresse',
                'attr'=>['placeholder' => 'Saisir votre adresse']])

            ->add('postal',TextType::class,[
                'label'=>'votre code postal',
                'attr'=>['placeholder' => 'Saisir le code postal']])

            ->add('city',TextType::class,[
                'label'=>'Votre Ville',
                'attr'=>['placeholder' => 'Saisir votre ville']])

            ->add('country',CountryType::class,[
                'label'=>'Votre pays',
                'attr'=>['placeholder' => 'Saisissez votre pays']])

            ->add('phone',TelType::class,[
                'label'=>'Votre téléhpone',
                'attr'=>['placeholder' => 'Saisir votre téléphone']])
            ->add('submit',SubmitType::class,['label'=>'Valider',
                'attr' => ['class'=>'btn btn-block btn-info']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
