<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use App\Classe\Cart;

class StripeController extends AbstractController
{
    /**
     * @Route("/commande/create-session", name="stripe_create_session")
     */
    public function index(Cart $cart): Response
    {
         $products_for_stripe = [];
         $YOUR_DOMAIN = 'http://127.0.0.1:8000';

        foreach($cart->getFull() as $product){
                //dd($product);
                $products_for_stripe[] =[
                    'price_data' => [
                        'currency' => 'eur',
                        'unit_amount' => $product['product']->getPrice(),
                        'product_data' => [
                            'name' => $product['product']->getName(),
                            'images' => [$YOUR_DOMAIN.'/uploads/'.$product['product']->getIllustration()],
                        ],
                    ],
                    'quantity' => $product['quantity'],
                    ];

            }

        Stripe::setApiKey('sk_test_51L0k8ECmEinODSSg6e5GGkbkaG30pFxQyuM9hZoF1IvaY1PsT1x38Wv2bBAkkglf469jrhEiGxL8mmf6By99coDT00JMNsGS9Y');

            $checkout_session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [$products_for_stripe],
                    # Provide the exact Price ID (e.g. pr_1234) of the product you want to sell
                    //'price' => $PRICE_ID, //'{{PRICE_ID}}',
                    //'quantity' => 1,

                'mode' => 'payment',
                'success_url' => $YOUR_DOMAIN . '/success.html',
                'cancel_url' => $YOUR_DOMAIN . '/cancel.html',
                //'automatic_tax' => ['enabled' => true,],
            ]);

            $response = new JsonResponse(['id' => $checkout_session->id]);

            return $response;
    }
}
