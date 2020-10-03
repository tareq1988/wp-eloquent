WordPress Eloquent Models
===========================

Le composant WordPress Eloquent Model est une boîte à outils complète fournissant un ORM et un générateur de schéma. Il prend en charge MySQL, Postgres, SQL Server et SQLite. Elle traduit les tables WordPress en [modèles compatibles avec Eloquent](https://laravel.com/docs/7.x/eloquent).

La bibliothèque est idéale pour une utilisation avec Bedrock / Sage de Roots.

Plus besoin d'utiliser la vieille classe moisie WP_Query, on entre dans le monde du futur en produisant du code lisible et ré-utilisable ! Des fonctionnalités supplémentaires sont également disponibles pour une expérience d'utilisation personnalisée à WordPress.


La librairie assurant la compatibilité avec Eloquent, vous pouvez consulter la [documentation de l'ORM](https://laravel.com/docs/7.x/eloquent) si vous êtes un peu perdu :)

# Sommaire

 - [Installation](#installation)
 - [Mise en place](#mise-en-place)
 - [Modèles supportés](#modèles-supportés)
    - [Posts](#posts)
    - [Comments](#comments)
    - [Terms](#terms)
    - [Users](#users)
    - [Options](#options)
    - [Menus](#menus)
 - [Images](#images)
 - [Alias de champs](#alias-de-champs)
 - [Scope personnalisés](#scopes-personnalisés)
 - [Pagination](#pagination)
 - [Meta](#meta)
 - [Requête d'un Post à partir d'un champs personnalisé (Meta)](#requète-dun-post--partir-dun-champs-personnalisé-meta)
 - [Advanced Custom Fields](#advanced-custom-fields)
 - [Création de table](#creation-de-table)
 - [Requètes avancées](#requètes-avancées)
 - [Type de contenu personnalisés](#type-de-contenu-personnalisés)
 - [Modèles personnalisés](#modles-personnalisés)
    - [Définition du modèle Eloquent](#définition-du-modèle-eloquent)
    - [Requètes sur modèles personnalisés](#requètes-sur-modèles-personnalisés)
 - [Shortcode](#shortcode)
 - [Logs des requêtes](#logs-des-requêtes)


## Installation

La méthode d'installation recommandée est [Composer](https://getcomposer.org/).

```
composer require amphibee/wordpress-eloquent-models
```

## Mise en place

La connection à la base de données (via $wpdb) s'effectue au première appel d'un modèle Eloquent.
Si vous avez besoin de récupérer l'instance de connection, lancer simplement le code suivant (privilégiez l'usage de `use`) :

```php
AmphiBee\Eloquent\Database::instance();
```

## Modèles supportés

### Posts

```php

use \AmphiBee\Eloquent\Model\Post;

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

use \AmphiBee\Eloquent\Model\Comment;

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

use \AmphiBee\Eloquent\Model\User;

// Tous les utilisateurs
$users = User::get();

// récupère l'utilisateur ayant pour ID 123
$user = User::find(123);

```

### Options

Dans WordPress, la récupération d'options s'effectue avec la fonction `get_option`. Avec Eloquent, pour se passer d'un chargement inutile du Core WordPress, vous pourrez utiliser la fonction `get` du modèle `Option`.

```php
$siteUrl = Option::get('siteurl');
```

Vous pouvez également ajouter d'autres options :

```php
Option::add('foo', 'bar'); // stockée en tant que chaine de caractères
Option::add('baz', ['one' => 'two']); // le tableau sera sérialisé
```

Vous pouvez récupérez l'ensemble des options en tant que tableau (attention aux performances...) :

```php
$options = Option::asArray();
echo $options['siteurl'];
```

Vous pouvez également spécifier les options spécifiques à récupérer :

```php
$options = Option::asArray(['siteurl', 'home', 'blogname']);
echo $options['home'];
```

### Menus

Pour récupérer un menu à partir de son alias, utiliser la syntaxe ci-dessous. Les éléments du menu seront retournés dans une variable `items` (c'est une collection d'objet de type `AmphiBee\Eloquent\Model\MenuItem`).

Les types de menu supportés actuellements sont : Pages, Posts, Custom Links et Categories.

Une fois que vous avez le modèle `MenuItem`, si vous souhaitez utiliser l'instance d'origine (tels que Page ou Term, par exemple), appelez juste la méthode `MenuItem::instance()`. L'objet `MenuItem` est juste un post dont le `post_type` est égal à `nav_menu_item`:

```php
$menu = Menu::slug('primary')->first();

foreach ($menu->items as $item) {
    echo $item->instance()->title; // si c'est un Post
    echo $item->instance()->name; // si c'est un Term
    echo $item->instance()->link_text; // si c'est un Custom Link
}
```

La méthode `instance()` retournera les objets correspondant :

- `Post` instance pour un élément de menu de type `post`;
- `Page` instance pour un élément de menu de type `page`;
- `CustomLink` instance pour un élément de menu de type `custom`;
- `Term` instance pour un élément de menu de type  `category`.

#### Multi-levels Menus

Pour gérer les menus à multi-niveaux, vous pouvez effectuer des itérations pour les placer au bon niveau, par exemple.

Vous pouvez utiliser la méthode `MenuItem::parent()` pour récupérer l'instance parent de l'élément du menu :

```php
$items = Menu::slug('foo')->first()->items;
$parent = $items->first()->parent(); // Post, Page, CustomLink ou Term (categorie)
```

Pour grouper les menus par parent, vous pouvez utiliser la méthode `->groupBy()`dans la collection `$menu->items`, qui rassemblera les éléments selon leur parent (`$item->parent()->ID`).

Pour en savoir plus sur la méthode `groupBy()`, [consulter la documentation de Eloquent](https://laravel.com/docs/5.4/collections#method-groupby).


## Alias de champs

Le modèle `Post` support les alias, donc si vous inspectez un objet `Post` vous pourrez retrouvez des alias dans le tableau statique `$aliases` (tels que `title` pour `post_title` et `content` pour `post_content`.

```php
$post = Post::find(1);
$post->title === $post->post_title; // true
```

Vous pouvez étendre le modèle `Post` pour créer vos propres. Ajoutez juste vos alias dans le modèle étendu, il héritera automatiquement de ceux définis dans le modèle `Post`:

```php
class A extends \AmphiBee\Eloquent\Model\Post
{
    protected static $aliases = [
        'foo' => 'post_foo',
    ];
}

$a = A::find(1);
echo $a->foo;
echo $a->title; // récupéré depuis le modèle Post
```

## Scopes personnalisés

Pour ordonner les modèles de type `Post` ou `User`, vous pouvez utiliser les scopes `newest()` et `oldest()`:

```php
$newest = Post::newest()->first();
$oldest = Post::oldest()->first();
```

## Pagination

Pour paginer les résultats, utiliser simplement la méthode `paginate()` de Eloquent :

```php
// Affiche les posts avec 5 éléments par page
$posts = Post::published()->paginate(5);
foreach ($posts as $post) {
    // ...
}
```

Pour afficher les liens de paginations, utiliser la méthode `links()` :

 ```php
 {{ $posts->links() }}
 ```

## Meta

L'ensemble de modèles Eloquent intègre une gestion des méta-données de WordPress.

Voici un exemple pour récupérer des metas-données :

```php
// Récupère un méta (ici 'link') depuis le modèle Post (on aurait pu utiliser un autre modèle comme User)
$post = Post::find(31);
echo $post->meta->link; // OU
echo $post->fields->link;
echo $post->link; // OU
```

Pour créer ou mettre à jour les metas données d'un utilisateur, utilisez juste les méthodes `saveMeta()` ou `saveField()`. Elles retourne un booléen à l'instar de la méthode `save()` de Eloquent.

```php
$post = Post::find(1);
$post->saveMeta('username', 'amphibee');
```

Il est possible de sauvegarder plusieurs metas données en un seul appel :

```php
$post = Post::find(1);
$post->saveMeta([
    'username' => 'amphibee',
    'url' => 'https://amphibee.fr',
]);
```

La libraire met également les méthodes `createMeta()` et `createField()`, qui fonctionnement comment les méthodes `saveX()`, mais elles ne sont utilisées que pour la création et retourne l'objet de type `PostMeta` créé par l'instance, au lieu d'un booléen.

```php
$post = Post::find(1);
$postMeta = $post->createMeta('foo', 'bar'); // instance of PostMeta class
$trueOrFalse = $post->saveMeta('foo', 'baz'); // boolean
```

## Requête d'un Post à partir d'un champs personnalisé (Meta)

Il existe différent moyen d'effectuer une requête à partir d'une méta-donnée (meta) en utilisant des scopes sur un modèle `Post` (ou tout autre modèle utilisant le trait `HasMetaFields`) :

Pour vérifier qu'une méta-donnée existe, utiliser le scope `hasMeta()` :
```
// Récupère le premier article ayant la méta "featured_article"
$post = Post::published()->hasMeta('featured_article')->first();
```

Si vous souhaiter cible une méta-donnée avec une valeur spécifique, il est possible d'utiliser le scope `hasMeta()` avec une valeur.

```php
// Récupère le premier article ayant une méta "username" et ayant pour valeur "amphibee"
$post = Post::published()->hasMeta('username', 'amphibee')->first();
```

Il est également possible d'effectuer une requête en définissant plusieurs meta-données et plusieurs valeurs associées en passant un tableau de valeur au scope scope `hasMeta()` :

```php
$post = Post::hasMeta(['username' => 'amphibee'])->first();
$post = Post::hasMeta(['username' => 'amphibee', 'url' => 'amphibee.fr'])->first();
// Ou juste en fournissant les clés de méta-données
$post = Post::hasMeta(['username', 'url'])->first();
```

Si vous devez faire correspondre une chaîne de caractère insensible à la casse ou une correspondance avec des caractères génériques, vous pouvez utiliser le scope `hasMetaLike()` avec une valeur. Cela utilisera l'opérateur SQL `LIKE`, il est donc important d'utiliser l'opérateur générique '%'.

```php
// Will match: 'B Gosselet', 'B BOSSELET', and 'b gosselet'.
$post = Post::published()->hasMetaLike('author', 'B GOSSELET')->first();

// En utilisant l'opérateur %, les résultats suivants seront retournés : 'N Leroy', 'N LEROY', 'n leroy', 'Nico Leroy' etc.
$post = Post::published()->hasMetaLike('author', 'N%Leroy')->first();
```


## Images

Récupération d'une image depuis un modèle `Post` ou `Page`.

```php
$post = Post::find(1);

// Récupère une instance de AmphiBee\Eloquent\Model\Meta\ThumbnailMeta.
print_r($post->thumbnail);

// Vous devez afficher l'instance de l'image pour récupérer l'url de l'image d'origine
echo $post->thumbnail;
```

Pour récupérer une taille d'image spécifique, utiliser la méthode `->size()` sur l'objet et renseigner l'alias de taille dans la paramètre (ex. `thumbnail` ou `medium`). Si la miniature a été générée, la méthode retourne un objet avec les méta-données, à défaut, c'est l'url d'origine qui est renvoyée (comportement WordPress).

```php
if ($post->thumbnail !== null) {
    /**
     * [
     *     'file' => 'filename-300x300.jpg',
     *     'width' => 300,
     *     'height' => 300,
     *     'mime-type' => 'image/jpeg',
     *     'url' => 'http://localhost/wp-content/uploads/filename-300x300.jpg',
     * ]
     */
    print_r($post->thumbnail->size(AmphiBee\Eloquent\Model\Meta\ThumbnailMeta::SIZE_THUMBNAIL));

    // http://localhost/wp-content/uploads/filename.jpg
    print_r($post->thumbnail->size('invalid_size'));
}
```

## Advanced Custom Fields

La librairie met à disposant la quasi-totalité des champs ACF (à l'exception du champs Google Map). Il permet de récupérer les champs de manière optimale sans passer par le module ACF.

### Utilisation basique

Pour récupérer une valeur d'un champs, il suffit d'initialiser un modèle de type `Post` et d'invoquer le champs personnalisé :

```php
$post = Post::find(1);
echo $post->acf->website_url; // retourne l'url fournie dans un champs ayant pour clé website_url
```

### Performance

Lorque l'on utilise `$post->acf->website_url`, des requètes additionnels sont exécutées pour récupérer le champs selon l'approche de ACF. Il est possible d'utiliser une méthode spécifique pour éviter ces requêtes additionnelles. Il suffit de renseigner le type de contenu personnalisé utilisé en tant que fonction :

 ```php
 // La méthode effectuant des requètes additionnelles
 echo $post->acf->author_username; // c'est un champs relatif à User

 // Sans requète additionnelle
 echo $post->acf->user('author_username');
 
 // Autres exemples sans requètes
echo $post->acf->text('text_field_name');
echo $post->acf->boolean('boolean_field_name');
 ```

 > PS: La méthode doit être appelée au format camel case. Part eemple, pour le champs de type `date_picker` vous devez écrire `$post->acf->datePicker('fieldName')`. La libraire effectue la conversion de camel casse vers snake case pour vous.

## Création de table

Doc à venir

## Requètes avancées

La librairie étant compatible avec Eloquent, vous pouvez sans problème effectuer des requètes complexes sans tenir compte du contexte WordPress.

Par exemple, pour récupérer les clients dont l'age est supérieur à 40 ans :

```PHP
$users = Capsule::table('customers')->where('age', '>', 40)->get();
```

## Modèles personnalisés

### Définition du modèle Eloquent

Pour ajouter vos propres méthode à un modèle existant, vous pouvez réaliser des "extends" de ce modèles. Par exemple, pour le modèle `User`, vous pourriez produire le code suivant :

```php
namespace App\Model;

use \AmphiBee\Eloquent\Model\User as BaseUser;

class User extends BaseUser {

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

user \AmphiBee\Eloquent\Model\Post as BasePost;

class Post extends BasePost {

    public function countries() {
        return $this->terms()->where('taxonomy', 'country');
    }

}

Post::with(['categories', 'countries'])->find(1);
```

Pour accéder au modèle d'un nouveau type de contenu, voici un exemple de ce qui pourrait être proposé :

```php
namespace App\Model;

class CustomPostType extends \AmphiBee\Eloquent\Model\Post {
    protected $post_type  = 'custom_post_type';

    public static function getBySlug(string $slug): self
    {
        return self::where('post_name', $slug)->firstOrfail();
    }
}

CustomPostType::with(['categories', 'countries'])->find(1);

```

### Requètes sur modèles personnalisés

Il est également possible de travailler avec des types de contenus personnalisés. Vous pouvez utiliser la méthode `type(string)` ou créer vos propres classes :

```php
// en utilisatn la méthode type()
$videos = Post::type('video')->status('publish')->get();

// en définissant sa propore classe
class Video extends AmphiBee\Eloquent\Model\Post
{
    protected $postType = 'video';
}
$videos = Video::status('publish')->get();
```

En utilisant la méthode `type()`, l'objet retourné sera de type `AmphiBee\Eloquent\Model\Post`. En utilisant son propre modèle, cela permet d'aller plus loin dans les possibilités en pouvant y associer des méthodes et des propriétés personnalisés et en retournant le résultat en tant qu'objet `Video` par exemple.

Type de contenu personnalisé et méta-données :

```php
// Récupération de 3 élément d'un type de contenu personnalisé et en récupérant une méta-donnée (address)
$stores = Post::type('store')->status('publish')->take(3)->get();
foreach ($stores as $store) {
    $storeAddress = $store->address; // option 1
    $storeAddress = $store->meta->address; // option 2
    $storeAddress = $store->fields->address; // option 3
}
```

## Shortcode

Implémentation en cours



## Logs des requêtes

La Capsule de connexion étant directement rattachée à `wpdb`, l'ensemble des requètes pourront être visualiser sur des outils de debugs tels que Query Monitor.
