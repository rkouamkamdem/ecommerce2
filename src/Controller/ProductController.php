<?php

namespace App\Controller;

use App\Form\SearchType;
use App\Classe\Search;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;

class ProductController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/nos-produits", name="products")
     */
    public function index(Request $request): Response
    {


        $search = new Search();
        $form = $this->createForm(SearchType::class,$search);
        $form->handleRequest($request);

        if( $form->isSubmitted() and $form->isValid() ){
            //on peut ne pas mettre la ligne ci-dessous($formData = $form->getData();) car l'objet $search est déja hydraté
            //ici => $this->createForm(SearchType::class,$search);
            //$formData = $form->getData();
            $products = $this->entityManager->getRepository(Product::class)->findWithSearch($search);
        }else{
            $products = $this->entityManager->getRepository(Product::class)->findAll();
        }

        return $this->render('product/index.html.twig',
                            ['products' => $products,
                            'form' => $form->createView()]);
    }

/**
 * @Route("/produit/{slug}", name="product")
 */
public function show($slug): Response
    {
        $product = $this->entityManager->getRepository(Product::class)->findOneBySlug($slug);

        if(!$product){
            return $this->redirectToRoute('products');
        }
        return $this->render('product/show.html.twig',
            ['product' => $product]);
    }
}
