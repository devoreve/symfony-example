<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends AbstractController
{
    public function index(CategoryRepository $repository): Response
    {
        $categories = $repository->findAllWithTotalPosts();

        return $this->render('category/index.html.twig', [
            'categories' => $categories
        ]);
    }

    public function show(CategoryRepository $repository, int $id): Response
    {
        $category = $repository->find($id);

        if (! $category) {
            throw $this->createNotFoundException("La catégorie n'existe pas");
        }

        // Récupération de tous les articles de la catégorie
        $posts = $category->getPosts();

        return $this->render('post/category.html.twig', [
            'posts' => $posts,
            'category' => $category
        ]);
    }
}