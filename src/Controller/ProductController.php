<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends AbstractController
{
    public function index(ProductRepository $repository): Response
    {
        $products = $repository->findAll();

        return $this->render('product/index.html.twig', [
            'products' => $products
        ]);
    }

    public function show(int $id, ProductRepository $repository): Response
    {
        $product = $repository->find($id);

        if (! $product) {
            throw $this->createNotFoundException("Le produit {$id} n'existe pas");
        }

        return $this->render('product/show.html.twig', [
            'product' => $product
        ]);
    }

    public function create(ProductRepository $repository, Request $request, ?int $id = null): Response
    {
        if (! $id) {
            // Si l'id n'existe pas, on est en mode création d'un nouveau produit
            // Il faut donc instancier un produit vide
            $product = new Product();
        } else {
            // Si l'id existe, on est en mode édition d'un produit existant
            // Il faut donc récupérer le produit que l'on souhaite modifier
            $product = $repository->find($id);
        }

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repository->save($product, true);

            return $this->redirectToRoute('products_index');
        }

        return $this->render('product/create.html.twig', [
            'form' => $form,
            'id' => $id
        ]);
    }
}