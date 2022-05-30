<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\OrderDetails;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\OrderType;
use Doctrine\ORM\EntityManagerInterface;

class OrderController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    /**
 * @Route("/commande", name="order")
 */
public function index(Cart $cart, Request $request): Response
    {
        if(!$this->getUser()->getAddresses()->getValues()){
            return $this->redirectToRoute('account_address_add');
        }
        $form = $this->createForm(OrderType::class,null,['user'=>$this->getUser()]);

        return $this->render('order/index.html.twig',[
            'form'=> $form->createView(),
            'cart' => $cart->getFull()]);
    }

/**
 * @Route("/commande/recapitulatif", name="order_recap", methods={"POST"} )
 */
public function add(Cart $cart, Request $request): Response
    {
        $form = $this->createForm(OrderType::class,null,['user'=>$this->getUser()]);

        $form->handleRequest($request);

        if($form->isSubmitted() and $form ->isValid()){
           //dd($form->getData());
            $date = new \DateTime();
            $carriers = $form->get('carriers')->getData();
            $delivery = $form->get('address')->getData();

            $delivery_content = $delivery->getFirstname().' '.$delivery->getLastname();
            $delivery_content .='<br/>'.$delivery->getPhone();

            if($delivery->getCompany()){
                $delivery_content .='<br/>'.$delivery->getCompany();
            }
            $delivery_content .='<br/>'.$delivery->getAddress();
            $delivery_content .='<br/>'.$delivery->getPostal().' '.$delivery->getCity();
            $delivery_content .='<br/>'.$delivery->getCountry();
            //dd($delivery_content);

            //Enregistrer macommande == order()
            $order = new Order();
            $order->setUser($this->getUser());
            $order->setCreatedAt($date);
            $order->setCarrierName($carriers->getName());
            $order->setCarrierPrice($carriers->getPrice());
            $order->setDelivery($delivery_content);
            $order->setIsPaid(0);

            $this->entityManager->persist($order);

            $products_for_stripe = [];
            $YOUR_DOMAIN = 'http://127.0.0.1:8000';

            //Enregistrer mes produits OrderDetails
            foreach($cart->getFull() as $product){
                $orderDetails = new OrderDetails();
                $orderDetails->setMyorder($order);
                $orderDetails->setProduct($product['product']->getName());
                $orderDetails->setQuantity($product['quantity']);
                $orderDetails->setPrice($product['product']->getPrice());
                $orderDetails->setTotal($product['product']->getPrice() * $product['quantity'] );

                $this->entityManager->persist($orderDetails);
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
            //dd($products_for_stripe);
           //$this->entityManager->flush();

            //Ci dessous les éléments qui concernent mon paiement avec 'Stripe'

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
            //dump($checkout_session->id);
            //dd($checkout_session);


            return $this->render('order/add.html.twig',[
                'form'=> $form->createView(),
                'cart' => $cart->getFull(),
                'carrier' => $carriers,
                'delivery' => $delivery_content,
                'stripe_checkout_session' => $checkout_session->id,
            ]);
        }

        return $this->redirectToRoute('cart');
    }

}
