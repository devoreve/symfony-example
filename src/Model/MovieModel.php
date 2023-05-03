<?php

namespace App\Model;

class MovieModel extends AbstractModel
{
    public function findAll(): array
    {
        $query = $this->pdo->prepare(
            'SELECT f.film_id, f.title, f.description, f.release_year, l.name AS lang, c.name AS cat
            FROM film f
            INNER JOIN language l on f.language_id = l.language_id
            INNER JOIN film_category fc on f.film_id = fc.film_id
            INNER JOIN category c on fc.category_id = c.category_id
            ORDER BY f.release_year DESC, f.title'
        );

        $query->execute();

        return $query->fetchAll();
    }

    public function find(int $id): ?array
    {
        $query = $this->pdo->prepare(
            'SELECT f.film_id, f.title, f.description, f.release_year, l.name AS lang, c.name AS cat
            FROM film f
            INNER JOIN language l on f.language_id = l.language_id
            INNER JOIN film_category fc on f.film_id = fc.film_id
            INNER JOIN category c on fc.category_id = c.category_id
            WHERE f.film_id = :id'
        );

        $query->execute([
            'id' => $id
        ]);

        return $query->fetch() ?: null;
    }

    public function findActors(int $id): array
    {
        $query = $this->pdo->prepare(
            'SELECT a.actor_id, a.first_name, a.last_name
            FROM film_actor fa
            INNER JOIN actor a on fa.actor_id = a.actor_id
            WHERE fa.film_id = :id'
        );

        $query->execute([
            'id' => $id
        ]);

        return $query->fetchAll();
    }
}