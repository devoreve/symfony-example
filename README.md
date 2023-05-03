# Cours symfony

## Installation

## Création d'une page

[Voir la doc](https://symfony.com/doc/current/page_creation.html)

La création d'une page se fait en plusieurs étapes :

1. Création d'une route
2. Création du contrôleur si ce dernier n'existe pas et de la méthode
3. Création d'un template pour l'affichage

### Création d'une route

Dans le fichier *config/routes.yaml*, créer la route pour la page d'accueil.

```yaml
homepage:
	path: /
    controller: App\Controller\DefaultController::homepage
```

Lorsque vous allez sur la page dont le chemin est "/", vous avez comme erreur que le contrôleur n'existe pas. On va donc maintenant créer le contrôleur.

### Création d'un contrôleur et de sa méthode

Dans le dossier *src/Controller*, créer le fichier *DefaultController.php*. Penser à indiquer le namespace du contrôleur.
Une fois le contrôleur créé, on va créer la méthode *homepage*.

```php

// Très important : indiquer le namespace du contrôleur pour qu'il soit retrouvé par Symfony
// Tous les contrôleurs (les fichiers se trouvant dans src/Controller) devront avoir ce namespace
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class DefaultController
{
    public function homepage(): Response
    {
        // Envoi d'une réponse (affichage de la page)
        return new Response("<h1>Accueil</h1>");
    }
}
```

### Création d'un template

Pour l'affichage de nos pages, nous allons créer des templates (fichiers qui vont contenir du html) qui seront appelés dans les contrôleurs.

#### Création d'un fichier twig

Twig est un moteur de templates, un outil qui va permettre de travailler plus facilement avec les fichiers html.

Dans le dossier *templates*, créer le fichier *homepage.html.twig*.

```twig
<h1>Accueil</h1>

<p>
    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Blanditiis consectetur dicta dolores earum excepturi
    exercitationem inventore ipsa itaque, libero minus natus nobis praesentium quaerat, quisquam reiciendis suscipit
    totam ullam ut!
</p>
```

#### Mettre à jour le contrôleur

Pour que le contrôleur puisse appeler un template, il faut faire quelques modifications : il faut faire hériter notre contrôleur de la classe *AbstractController* de Symfony.

```php
// Très important : indiquer le namespace du contrôleur pour qu'il soit retrouvé par Symfony
// Tous les contrôleurs (les fichiers se trouvant dans src/Controller) devront avoir ce namespace
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

// Le contrôleur doit hériter de AbstractController pour appeler la méthode render
class DefaultController extends AbstractController
{
    public function homepage(): Response
    {
        // Ce code n'est plus nécessaire : on appelle directement le template avec la méthode render
        // return new Response("<h1>Accueil</h1>");

        // Affichage du template qui s'appelle homepage.html.twig
        return $this->render('homepage.html.twig');
    }
}
```

## Réécriture d'url

[Voir la doc](https://symfony.com/doc/current/setup/web_server_configuration.html)

Avec Symfony il est possible de réécrire les url (retirer le "index.php" dans l'url pour avoir url plus jolies).
Pour ce faire, taper la commande suivante : ``` composer require symfony/apache-pack``` puis choisir "y" pour exécuter la recette.

Cela va créer un fichier *.htaccess* dans le dossier *public* qui va contenir le code suivant :

```
# Use the front controller as index file. It serves as a fallback solution when
# every other rewrite/redirect fails (e.g. in an aliased environment without
# mod_rewrite). Additionally, this reduces the matching process for the
# start page (path "/") because otherwise Apache will apply the rewriting rules
# to each configured DirectoryIndex file (e.g. index.php, index.html, index.pl).
DirectoryIndex index.php

# By default, Apache does not evaluate symbolic links if you did not enable this
# feature in your server configuration. Uncomment the following line if you
# install assets as symlinks or if you experience problems related to symlinks
# when compiling LESS/Sass/CoffeScript assets.
# Options +FollowSymlinks

# Disabling MultiViews prevents unwanted negotiation, e.g. "/index" should not resolve
# to the front controller "/index.php" but be rewritten to "/index.php/index".
<IfModule mod_negotiation.c>
    Options -MultiViews
</IfModule>

<IfModule mod_rewrite.c>
    # This Option needs to be enabled for RewriteRule, otherwise it will show an error like
    # 'Options FollowSymLinks or SymLinksIfOwnerMatch is off which implies that RewriteRule directive is forbidden'
    Options +FollowSymlinks

    RewriteEngine On

    # Determine the RewriteBase automatically and set it as environment variable.
    # If you are using Apache aliases to do mass virtual hosting or installed the
    # project in a subdirectory, the base path will be prepended to allow proper
    # resolution of the index.php file and to redirect to the correct URI. It will
    # work in environments without path prefix as well, providing a safe, one-size
    # fits all solution. But as you do not need it in this case, you can comment
    # the following 2 lines to eliminate the overhead.
    RewriteCond %{REQUEST_URI}::$0 ^(/.+)/(.*)::\2$
    RewriteRule .* - [E=BASE:%1]

    # Sets the HTTP_AUTHORIZATION header removed by Apache
    RewriteCond %{HTTP:Authorization} .+
    RewriteRule ^ - [E=HTTP_AUTHORIZATION:%0]

    # Redirect to URI without front controller to prevent duplicate content
    # (with and without `/index.php`). Only do this redirect on the initial
    # rewrite by Apache and not on subsequent cycles. Otherwise we would get an
    # endless redirect loop (request -> rewrite to front controller ->
    # redirect -> request -> ...).
    # So in case you get a "too many redirects" error or you always get redirected
    # to the start page because your Apache does not expose the REDIRECT_STATUS
    # environment variable, you have 2 choices:
    # - disable this feature by commenting the following 2 lines or
    # - use Apache >= 2.3.9 and replace all L flags by END flags and remove the
    #   following RewriteCond (best solution)
    RewriteCond %{ENV:REDIRECT_STATUS} =""
    RewriteRule ^index\.php(?:/(.*)|$) %{ENV:BASE}/$1 [R=301,L]

    # If the requested filename exists, simply serve it.
    # We only want to let Apache serve files and not directories.
    # Rewrite all other queries to the front controller.
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ %{ENV:BASE}/index.php [L]
</IfModule>

<IfModule !mod_rewrite.c>
    <IfModule mod_alias.c>
        # When mod_rewrite is not available, we instruct a temporary redirect of
        # the start page to the front controller explicitly so that the website
        # and the generated links can still be used.
        RedirectMatch 307 ^/$ /index.php/
        # RedirectTemp cannot be used instead
    </IfModule>
</IfModule>
```

## Le moteur de templates twig

Le moteur de templates twig dispose de sa propre syntaxe pour afficher des variables, pour les conditions et les boucles.

### Variables

Pour une afficher une variable avec twig on utilise les {{ }} avec le nom de la variable sans le "$" devant.

```twig
<p>{{ name }}</p>
```

#### Exemples

```php
$days = [
    'Lundi',
    'Mardi',
    'Mercredi',
    'Jeudi',
    'Vendredi',
    'Samedi',
    'Dimanche'
];

$user = [
    'email' => 'john.doe@test.fr',
    'fullname' => 'John Doe'
];
```

```twig
<p>Email : {{ user.email }}</p>
<p>Nom complet : {{ user.fullname }}</p>
<p>{{ days[0] }}</p>
```

### Structures de contrôles

[Voir la doc du if](https://twig.symfony.com/doc/2.x/tags/if.html)
[Voir la doc du for](https://twig.symfony.com/doc/2.x/tags/for.html)

Pour les structures de contrôles (conditions et boucles) là aussi twig a une syntaxe qui lui est propre.

```twig
{% if age >= 18 %}
    <p class="alert alert-success">Vous êtes majeur(e)</p>
{% else %}
    <p class="alert alert-warning">Vous êtes mineur(e)</p>
{% endif %}

<ul>
    {% for day in days %}
        <li>{{ day }}</li>
    {% endfor %}
</ul>
```

### Les filtres

[Voir la doc](https://twig.symfony.com/doc/2.x/filters/index.html)

Les filtres sont des fonctions qui vont s'appliquer sur vos données.

#### Exemples

```twig
<p>Bonjour {{ name|upper }} tu as {{ age }} ans</p>
<p>Cet article a coûté {{ product.price|number_format(2) }}€</p>
<p>L'article a été acheté le {{ product.buyDate|date('d/m/Y') }}</p>
```

### Les fonctions d'accès aux chemins

Il existe 2 fonctions importantes dans twig :
* asset
* path

#### asset

La première est une fonction qui permet de récupérer tout le chemin vers le dossier public. Elle est utilisée notamment pour récupérer le chemin vers un fichier css ou un fichier js.

```twig
	<link rel="stylesheet" href="{{ asset('css/style.css') }}">
```

#### path

La seconde est une fonction qui permet de faire le lien avec une route de l'application et de générer l'url correspondante.

```twig
	<a href="{{ path('about') }}">A propos</a>
```

Attention si votre route contient des paramètres, il faudra l'indiquer en 2ème paramètre de la fonction path.

```yaml
about:
	path: /about
    controller: ...
    
customers_show:
	path: /customers/{id}
    controller: ...
```

```twig
	<a href="{{ path('customers_show', {id: customer.id}) }}">Détail du client</a>
```

## Le routeur de Symfony

[Voir la doc](https://symfony.com/doc/current/routing.html)

C'est un mécanisme dans le framework qui va permettre d'associer une url (un chemin) à une action (un contrôleur et une méthode).

### Déclaration des routes

Il existe plusieurs façons de déclarer les routes :
* dans le fichier de routing (*config/routes.yaml*)
* directement au niveau du contrôleur

#### Dans le fichier de routing

Dans *config/routes.yaml* on peut déclarer les routes de la manière suivante :

```yaml
nomDeLaRoute:
	path: /chemin
    controller: Nom\Complet\Controller::nomDeLaMéthode
```

##### Exemples


```yaml
homepage:
    path: /
    controller: App\Controller\DefaultController::homepage

about:
    path: /about
    controller: App\Controller\DefaultController::about

hello:
    path: /hello
    controller: App\Controller\HelloController::index
```

#### Dans les contrôleurs

Il est possible également de définir les routes au-dessus des méthodes des contrôleurs :

```php
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function homepage(): Response
    {
        return $this->render('homepage.html.twig');
    }

    #[Route('/about', name: 'about')]
    public function about(): Response
    {
        return $this->render('about.html.twig');
    }
}
```

### Paramètres de route

Il est possible de passer des paramètres à une route, des informations variables à une route.

#### Déclaration

```yaml
hello:
    path: /hello/{name?Jean}
    controller: App\Controller\HelloController::index
```

Dans mon fichier de routing, j'ai créé une route *hello* avec comme chemin "/hello" et un paramètre "name" (qui a "Jean" comme valeur par défaut).
Cette valeur peut être récupérée dans le contrôleur en PHP.

```php
public function index(string $name): Response
{
    return $this->render( view: 'hello.html.twig', parameters: [
        'name' => $name,
    ]);
}
```

## Base de données

### Méthode PHP classique

Symfony est un projet PHP donc il est évidemment possible d'utiliser tout ce qui existe en PHP (notamment PDO).
Même si on peut utiliser PDO comme dans n'importe quel projet PHP, on verra par la suite de meilleures manières de travailler avec les bases de données.

### DBAL

[Voir la doc](https://symfony.com/doc/current/doctrine/dbal.html)

DBAL est une bibliothèque de Symfony pour la connexion à la base de données. Il s'agit d'une surcouche de PDO, c'est-à-dire que c'est une bibliothèque utilisant PDO mais qui s'intègre mieux au projet Symfony.

### Doctrine

[Voir la doc](https://symfony.com/doc/current/doctrine.html)

Doctrine est ce qu'on appelle un ORM (Object Relational Mapper), un outil qui permet de faire le lien avec la base de données grâce à des objets.

#### Configurer la base de données

Dans le fichier *.env* à la racine de votre projet Symfony, modifier la ligne DATABASE_URL comme ceci :

```
DATABASE_URL="mysql://root:votre_mot_passe@127.0.0.1:3306/nom_de_la_db?serverVersion=mariadb-10.4.11&charset=utf8mb4"
```

Remplacer par le mot de passe correspondant et par le nom de la base de données souhaitée.

#### Création de la base de données

On demande à Doctrine de créer pour nous la base de données (à partir des informations renseignées dans le fichier *.env*). 
Pour cela, se placer à l'aide du terminal dans le dossier Symfony puis taper la commande suivante :

```
php bin/console doctrine:database:create
```

#### Création des entités

Toujours dans le terminer, on va maintenant créer notre première entité qui se transformera après en une table dans la base de données.
Pour cela, taper la commande suivante :

```
php bin/console make:entity
```

Taper d'abord le nom de l'entité (Post) puis le noms des propriétés title et description. Taper :
* title puis string puis laisser vide 2 fois
* description puis text puis laisser vide 1 fois

Lorsque le terminal vous demande si vous souhaitez ajouter une autre propriété, taper "entrée" en laissant vide.

#### Créer une migration et mettre à jour la base de données

L'entité étant créée, on peut maintenant créer une migration à l'aide de la commande suivante :

```
php bin/console make:migration
```

Cette migration est un fichier contenant des scripts SQL basés sur le contenu de nos entités.
On va maintenant exéctuer cette migration pour mettre le schéma de la base de données. Pour ce faire, taper la commande suivante :

```
php bin/console doctrine:migrations:migrate
```

Valider la migration en tapant sur "entrée".

#### Modifier une entité et mettre à jour la base de données

Pour modifier une entité existante, on tape de nouveau la commande pour créer l'entité "make:entity" puis on choisit l'entité déjà créée "Post".
On y ajoute un champ createdAt puis on laisse les autres choix par défaut (laisser vide et taper "entrée" 3 fois de suite).

On tape la commande pour créer une migration puis on l'exécute.

#### Utiliser l'orm pour ajouter ou récupérer des données

```php
use App\Entity\Post;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class PostController extends AbstractController
{
    public function create(PostRepository $repository): Response
    {
        // Création d'un objet Post avec les données souhaitées
        $post = new Post();
        $post->setTitle('Hello world');
        $post->setDescription('.......');

        // Transfert de cet objet vers la table à laquelle il est relié dans la base de données
        // => insère un nouvel article dans la base de données
        $repository->save($post, true);

        return new Response("L'article {$post->getId()} a bien été créé !");
    }

    public function index(PostRepository $repository): Response
    {
        // Récupère tous les articles dans la base de données
        $posts = $repository->findAll();

        // Affichage de la vue
        return $this->render('post/index.html.twig', [
            'posts' => $posts
        ]);
    }

    public function show(int $id, PostRepository $repository): Response
    {
        // Récupère l'article correspondant à l'id qui se trouve dans l'url
        $post = $repository->find($id);

        // Si l'article est null ça signifique qu'il n'existe pas => envoie d'une page 404
        if ($post === null) {
            throw $this->createNotFoundException("L'article $id n'existe pas");
        }

        // Affichage de la vue
        return $this->render('post/show.html.twig', [
            'post' => $post
        ]);
    }
}
```

## Formulaire

[Voir la doc](https://symfony.com/doc/current/forms.html)

Symfony propose un système pour gérer les formulaires avec les entités.

### Classe Form

Il faut dans un premier temps créer une classe Form pour l'entité dont on veut le formulaire.
Pour ce faire il faudra taper la commande :

```
php bin/console make:form
```

Une fois la commande tapée, Symfony vous demandera le nom de la classe Form (par convention c'est le nom préfixé par "Type") ainsi que le nom de l'entité rattachée.

```php
namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'rows' => 10
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Créer l\'article'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
```

### Usage dans le contrôleur

Une fois la classe créée, on peut l'utiliser dans le contrôleur pour gérer notre formulaire.

```php
namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PostController extends AbstractController
{
    public function create(PostRepository $repository, Request $request): Response
    {
        // Afficher un formulaire ou enregistrer l'article en base de données (si le formulaire a été soumis)

        // Création d'un objet vide
        $post = new Post();

        // Création d'un formulaire pour mettre à jour la variable $post
        $form = $this->createForm(PostType::class, $post);

        // Gestion de la requête
        $form->handleRequest($request);

        // Si le formulaire est soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Enregistrement de l'article en base de données
            $repository->save($post, true);

            // Retour à la page d'accueil (ou une autre page)
            return $this->redirectToRoute('homepage');
        }

        // Affichage du formulaire
        return $this->render('post/create.html.twig', [
            'form' => $form
        ]);
    }
}
```
