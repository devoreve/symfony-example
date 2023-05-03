<?php

namespace App\Model;

class ActorModel extends AbstractModel
{
    /**
     * Récupère la liste des acteurs
     *
     * @return array
     */
    public function findAll(): array
    {
        $query = $this->pdo->prepare('SELECT actor_id, first_name, last_name FROM actor');
        $query->execute();

        return $query->fetchAll();
    }

    /**
     * Récupère le détail d'un acteur
     *
     * @param int $id
     * @return array|null
     */
    public function find(int $id): ?array
    {
        $query = $this->pdo->prepare('SELECT actor_id, first_name, last_name FROM actor WHERE actor_id = :id');
        $query->execute([
            'id' => $id
        ]);

        return $query->fetch() ?: null;
    }

    /**
     * Récupère la liste des films d'un acteur
     *
     * @param int $id L'id de l'acteur
     * @return array
     */
    public function findMovies(int $id): array
    {
        $query = $this->pdo->prepare("SELECT f.title, f.release_year
            FROM film f
            INNER JOIN film_actor fa on f.film_id = fa.film_id
            WHERE fa.actor_id = :id
            ORDER BY f.release_year DESC, f.title"
        );

        $query->execute([
            'id' => $id
        ]);

        return $query->fetchAll();
    }
}