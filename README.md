# ZF-3 Data mapper module

Simple Data Mapper implementation based on `zend-db`  
This is not just work in progress, but beginning of work in progress. Don't even think about using it.


## Example:
    
### Define our Post model
EntityInterface defines getter and setter for id, nothing more.
 
```php
class Post implements EntityInterface
{
    public $id;
    public $user;
    public $title;


    public function setId($id)
    ...
    public function getId()
    ...
}
```
    
### Define our mapper
    
```php
class PostRepository extends Repository\Mapper\StandardMapper
{
    protected static $entityClass = Post::class;
    protected static $table = 'posts';

    protected static $features = [
        Repository\Mapper\Feature\Relations::class => [
            'user' => [Repository\Hydrator\HasOne::class, User::class]
        ]
    ];
}
```
    
### Add stuff in configuration

```php
[
    'mappers' => [
        'maps' => [
            Post::class => PostRepository::class
        ],
        'aliases' => [
            'posts' => Post::class
        ]
    ]
]
```
    
### In your code
    
```php
$repository = $serviceLocator->get(\Repository\Repository\RepositoryPluginManager::class);
$mapper = $repository->get(Post::class);

$singlePost = $mapper->id(10);
$allPosts   = $mapper->fetch();
$somePosts  = $mapper->fetch(['title' => 'My post title']);

$somePosts  = $mapper->fetch(function(Select $select){
    $select->where(['title' => 'My post title'])->order('cteated_at')->limit(2);
});
```
    
## Features

### \Repository\Mapper\Feature\SoftDelete

Enables "soft-delete" feature. Exposes `recover` method

```php
// In your mapper
protected static $features = [
    Repository\Mapper\Feature\SoftDelete::class => 'deleted_at' // default field is 'deleted_at'
];
....

$post = $mapper->id(10);
$mapper->delete($post);

$mapper->recover($post);
```

### \Repository\Mapper\Feature\Timestamps

Enables created and updated fields

```php
// In your mapper
protected static $features = [
    Repository\Mapper\Feature\Timestamps::class => ['created_at', 'updated_at']
];
....
```

### \Repository\Mapper\Feature\Transaction

```php
// In your mapper
protected static $features = [
    Repository\Mapper\Feature\Transaction::class,
];
....

$mapper->withTransaction();

$mapper->update($post1);
$mapper->update($post2);
$mapper->insert($post3);
$mapper->delete($post4);

$mapper->commitTransaction();
```

### \Repository\Mapper\Feature\Relations

```php
// In your mapper
protected static $features = [
    Repository\Mapper\Feature\Relations::class => [
        'users'  => [HasManyThrough::class, User::class, ['post', 'user'], 'post_users'],
        'author' => [HasOne::class, User::class],
    ],
];
....

$post = $mapper->withRelation(['users', 'author])->id(10);
$post->author->name;
```

### \Repository\Mapper\Feature\SelectStrategy (Work in progress)

Lets you define custom query logic or hide implementation details.  
Would be useful if you're going to expose mappers to a ViewHelper or anything monkey would have access too.

```php
// In your mapper
protected static $features = [
    Repository\Mapper\Feature\SelectStrategy::class,
];
....

$post = $mapper->withStrategy(['limit' => 10, 'where' => 'author=2,age<55', 'order' => '-created_at'])->fetch();
// Or simply
$post = $mapper->fetchWithStrategy(['limit' => 10, 'where' => 'author=2,age<55']);
```


    
