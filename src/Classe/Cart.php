<?php
/**
 * Created by PhpStorm.
 * User: rkamdemkouam
 * Date: 25/04/2022
 * Time: 10:36
 */

namespace App\Classe;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;

class Cart
{
    private $entityManager;
    private $session;

    public function __construct(EntityManagerInterface $entityManager, SessionInterface $session)
    {
        $this->session = $session;
        $this->entityManager = $entityManager;
    }

    //Ici on ajoute un article dans son panier
    public function add($id)
    {

        $cart = $this->session->get('cart', []);
        if (!empty($cart[$id])) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }

        $this->session->set('cart', $cart);
        /*
        $this->session->set('cart',[
                [
                'id' => $id,
                'quantity' => 1
                ]
            ]
        );*/
    }

    //Ma fonction qui me permet de recuperer mon panier
    public function get()
    {
        return $this->session->get('cart');
    }

    //ici on supprime le panier
    public function remove()
    {
        return $this->session->remove('cart');
    }

    //ici on supprime un produit de mon panier
    public function delete($id)
    {
        $cart = $this->session->get('cart', []);
        unset($cart[$id]);
        $this->session->set('cart', $cart);
        return $cart;
        //return $this->session->set('cart',$cart);;
    }

    //ici on diminue la quantité d'un article du panier
    public function decrease($id)
    {
        $cart = $this->session->get('cart', []);
        if ($cart[$id] == 1) {
            unset($cart[$id]);
        } else {
            $cart[$id]--;
        }

        return $this->session->set('cart', $cart);
    }

    //Ici on créé notre panier
    public function getFull()
    {
        $cartComplete = [];
        if ($this->get()) {
            foreach ($this->get() as $id => $quantity) {
                $product_objet = $this->entityManager->getRepository(Product::class)->findOneById($id);
                if (!$product_objet) {
                    $this->delete($id);
                    continue;
                }

                $cartComplete[] = [
                    'product' => $product_objet,
                    'quantity' => $quantity
                ];
            }
        }

        return $cartComplete;

    }

}