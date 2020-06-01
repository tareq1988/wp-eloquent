WordPress Eloquent Models
===========================

Le composant WordPress Eloquent Model est une boîte à outils complète fournissant un ORM et un générateur de schéma. Il prend en charge MySQL, Postgres, SQL Server et SQLite. Elle traduit les tables WordPress en [modèles compatibles avec Eloquent](https://laravel.com/docs/7.x/eloquent).

La bibliothèque est idéale pour une utilisation avec Bedrock / Sage de Roots.

Plus besoin d'utiliser la vieille classe moisie WP_Query, on entre dans le monde du futur en produisant du code lisible et ré-utilisable ! Des fonctionnalités supplémentaires sont également disponibles pour une expérience d'utilisation personnalisée à WordPress.


La librairie assurant la compatibilité avec Eloquent, vous pouvez consulter la [documentation de l'ORM](https://laravel.com/docs/7.x/eloquent) si vous êtes un peu perdu :)

# Sommaire

 - [Installation](#installation)
 - [Mise en place](#mise-en-place)
 - [Posts](#posts)
 - [Comments](#comments)
 - [Terms](#terms)
 - [Users](#users)
 - [Meta](#meta)
 - [Options](#options)
 - [Création de table](#creation-de-table)
 - [Requètes avancées](#requetes-avancees)
 - [Créer vos propres modèles](#créer-vos-propres-modèles)
 - [Logs des requêtes](#logs-des-requêtes)

## Installation

La méthode d'installation recommandée est [Composer](https://getcomposer.org/).

```
composer require amphibee/wordpress-eloquent-models
```

## Mise en place

Lancer simplement le code suivant (privilégiez l'usage de `use`) :

```php
AmphiBee\Eloquent\Core\Capsule\Capsule::bootWp();
```

## Modèles supportés

### Posts

```php

use \AmphiBee\Model\Model\Post;

// récupération du post avec l'ID 1
$post = Post::find(1);

// Données en relations disponibles
$post->author;
$post->comments;
$post->terms;
$post->tags;
$post->categories;
$post->meta;

```

***Status***

Par défaut, `Post` retourne l'ensemble des articles quelque soit leur status. Cela peut être modifié via un [scope local](https://laravel.com/docs/7.x/eloquent#query-scopes) `published` pour ne retourner que les articles publiés.

```php
Post::published()->get();
```

Il est également possible de définir le statut en question via le [scope local](https://laravel.com/docs/7.x/eloquent#query-scopes#query-scopes) `status`.

```php
Post::status('draft')->get();
```

***Post Types***

Par défaut, `Post` retourne l'ensemble des types de contenu. Cea peut être surchargé via le [scope local](https://laravel.com/docs/7.x/eloquent#query-scopes#query-scopes) `type`.

```php
Post::type('page')->get();
```

### Comments

```php

use \AmphiBee\Model\Model\Comment;

// récupère le commentaite ayant pour ID 12345
$comment = Comment::find(12345);

// Données en relation disponibles
$comment->post;
$comment->author;
$comment->meta

```

### Terms

Dans cette version `Term` est accessible en tant que modèle mais n'est accessible que par le biais d'article. Néanmoins, il suffit d'étendre `Term` pour l'appliquer aux autres types de contenus personnalisés.

```php
$post->terms()->where('taxonomy', 'country');
```

### Users

```php

use \AmphiBee\Model\Model\User;

// récupère l'utilisateur ayant pour ID 123
$user = User::find(123);

// relations disponibles pour cet utilisateur
$user->posts;
$user->meta;
$user->comments

```

### Meta

L'ensemble des modèles `Post`, `User`, `Comment` et `Term` intègrent `HasMeta`. Par conséquent, cela permet de récupérer les méta-données de WordPress très aisément via la méthode `getMeta` ou de les définir avec `setMeta`:

```php
$post = Post::find(1);
$post->setMeta('featured_image', 'abeille.jpg');
$post->setMeta('breakfast', ['waffles' => 'blueberry', 'pancakes' => 'banana']);

// autre méthode en un seul appel
$featured_image = Post::find(1)->getMeta('featured_image');
Post::find(1)->setMeta('featured_image', 'image.jpg');

// ce fonctionnement s'applique pour tous les modèles

$user = User::find(1)
$facebook = $user->getMeta('facebook');
$user->setMeta('social', ['facebook' => 'facebook.com/amphibee', 'instagram' => 'instagram.com/amphibee']);

$comment = Comment::find(1);
$meta = $comment->getMeta('some_comment_meta');

$term = Term::find(123);
$meta = $term->getMeta('some_term_meta');

// supprime une données méta
$post = Post::find(123)->deleteMeta('some_term_meta');
```

### Options

Dans WordPress, la récupération d'options s'effectue avec la fonction `get_option`. Avec Eloquent, pour se passer d'un chargement inutile du Core WordPress, vous pourrez utiliser la fonction `getValue`.

```php
use \AmphiBee\Model\Model\Post;

$siteurl = Option::getValue('siteurl');
```

Autre possibilité (la version qui serait utilisée sur Eloquent).

```php
use \AmphiBee\Model\Model\Options;

$siteurl = Option::where('option_name', 'siteurl')->value('option_value');
```

La méthode simplifiée d'ajout/édition d'option n'est pas encore gérée.

## Création de table

La création de table s'effectue très facilement via l'API [Schema](https://laravel.com/docs/7.x/migrations#creating-tables) de Eloquent.

Voici un exemple pour la création d'une table `customer` :

```PHP
Capsule::schema()->create('customers', function ($table) {
    $table->increments('id');
    $table->string('email')->unique();
    $table->timestamps();
});
```

## Requètes avancées

La librairie étant compatible avec Eloquent, vous pouvez sans problème effectuer des requètes complexes sans tenir compte du contexte WordPress.

Par exemple, pour récupérer les clients dont l'age est supérieur à 40 ans :

```PHP
$users = Capsule::table('customers')->where('age', '>', 40)->get();
```

## Créer vos propres modèles

Pour ajouter vos propres méthode à un modèle existant, vous pouvez réaliser des "extends" de ce modèles. Par exemple, pour le modèle `User`, vous pourriez produire le code suivant :

```php
namespace App\Model;

class User extends \AmphiBee\Model\Model\User {

    public function orders() {
        return $this->hasMany('\App\Model\User\Orders');
    }

    public function current() {
        // fonctionnalité spécifique à l'utilisateur courant
    }

    public function favorites() {
        return $this->hasMany('Favorites');
    }

}
```

Un autre exemple serait de définir une nouvelle taxonomie à un article, par exemple `country`

```php
namespace App\Model;

class Post extends \AmphiBee\Model\Model\Post {

    public function countries() {
        return $this->terms()->where('taxonomy', 'country');
    }

}

Post::with(['categories', 'countries'])->find(1);
```

Pour accéder au modèle d'un nouveau type de contenu, voici un exemple de ce qui pourrait être proposé :

```php
namespace App\Model;

class CustomPostType extends \AmphiBee\Model\Model\Post {
    protected $post_type  = 'custom_post_type';

    public static function getBySlug(string $slug): self
    {
        return self::where('post_name', $slug)->firstOrfail();
    }
}

CustomPostType::with(['categories', 'countries'])->find(1);
```

## Logs des requêtes

La Capsule de connexion étant directement rattachée à `wpdb`, l'ensemble des requètes pourront être visualiser sur des outils de debugs tels que Query Monitor.
