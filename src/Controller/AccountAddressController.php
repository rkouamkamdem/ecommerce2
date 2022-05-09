<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\AddressType;
use App\Entity\Address;
use App\Entity\User;
class AccountAddressController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/compte/address", name="account_address")
     */
    public function index(): Response
    {
        return $this->render('account/address.html.twig', [
            'controller_name' => 'AccountAddressController',
        ]);
    }

    /**
     * @Route("/compte/ajouter-une-addresse", name="account_address_add")
     */
    public function add(Request $request): Response
    {
        $adresse = new Address();
        $form = $this->createForm(AddressType::class,$adresse);
        $form->handleRequest($request);

        if($form->isSubmitted() and $form->isValid()){
           $adresse->setUser($this->getUser());
            //
            $this->entityManager->persist($adresse);
            $this->entityManager->flush();
            //dd($adresse);
            return $this->redirectToRoute('account_address');
        }

        return $this->render('account/address_form.html.twig',['form'=>$form->createView()]);
    }


    /**
     * @Route("/compte/modifier-une-addresse/{id}", name="account_address_edit")
     */
    public function edit(Request $request,$id): Response
    {
        $address = $this->entityManager->getRepository(Address::class)->findOneById($id);
        if(!$address || $address->getUser() != $this->getUser()){
            return $this->redirectToRoute('account_address');
        }

        $form = $this->createForm(AddressType::class,$address);
        $form->handleRequest($request);

        if($form->isSubmitted() and $form->isValid()){

            $this->entityManager->flush();
            //dd($adresse);
            return $this->redirectToRoute('account_address');
        }

        return $this->render('account/address_form.html.twig',['form'=>$form->createView()]);
    }

    /**
     * @Route("/compte/supprimer-une-addresse/{id}", name="account_address_delete")
     */
    public function delete($id): Response
    {
        $address = $this->entityManager->getRepository(Address::class)->findOneById($id);
        if($address && $address->getUser() == $this->getUser()){
            $this->entityManager->remove($address);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('account_address');
    }

}
