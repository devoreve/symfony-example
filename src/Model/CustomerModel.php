<?php

namespace App\Model;

class CustomerModel extends AbstractModel
{
    /**
     * Récupère tous les clients
     *
     * @return array
     */
    public function findAll(): array
    {
        $query = $this->pdo->prepare(
            'SELECT cu.customer_id, cu.first_name, cu.last_name, cu.email, a.address, a.postal_code-- , c.city
            FROM customer cu
            INNER JOIN address a on cu.address_id = a.address_id
            -- INNER JOIN city c on a.city_id = c.city_id
            ORDER BY cu.last_name'
        );
        $query->execute();

        return $query->fetchAll();
    }

    /**
     * Récupère le détail d'un client
     *
     * @param int $id
     * @return array
     */
    public function find(int $id): ?array
    {
        $query = $this->pdo->prepare(
            "SELECT cu.customer_id, cu.first_name, cu.last_name, cu.email, a.address, a.postal_code, c.city
            FROM customer cu
            INNER JOIN address a on cu.address_id = a.address_id
            INNER JOIN city c on a.city_id = c.city_id
            WHERE cu.customer_id = :id"
        );

        $query->execute([
            'id' => $id
        ]);

        return $query->fetch() ?: null;
    }

    /**
     * Récupère la liste des films loués par le client
     *
     * @param int $id
     * @return array
     */
    public function findMovies(int $id): array
    {
        $query = $this->pdo->prepare(
            "SELECT f.title, p.amount, p.payment_date, r.return_date
            FROM rental r
            INNER JOIN inventory i on r.inventory_id = i.inventory_id
            INNER JOIN film f on i.film_id = f.film_id
            INNER JOIN payment p on r.rental_id = p.rental_id
            WHERE r.customer_id = :id
            ORDER BY f.title"
        );

        $query->execute([
            'id' => $id
        ]);

        return $query->fetchAll();
    }
}