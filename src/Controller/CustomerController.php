<?php

namespace App\Controller;

use App\Model\CustomerModel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class CustomerController extends AbstractController
{
    public function index(): Response
    {
        $model = new CustomerModel();
        $customers = $model->findAll();

        return $this->render('customer/index.html.twig', [
            'customers' => $customers
        ]);
    }

    // $id correspond au numéro dans l'url
    // On peut le récupérer car la route contient un paramètre "id"
    public function show(int $id): Response
    {
        $model = new CustomerModel();
        $customer = $model->find($id);
        $movies = $model->findMovies($id);

        return $this->render('customer/show.html.twig', [
            'customer' => $customer,
            'movies' => $movies
        ]);
    }
}