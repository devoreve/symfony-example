<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends AbstractController
{
    public function index(CategoryRepository $repository): Response
    {
        $categories = $repository->findAllOrdered();

        return $this->render('category/index.html.twig', [
            'categories' => $categories
        ]);
    }
}