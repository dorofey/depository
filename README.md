# ZF-3 Repository module

Simple Data Mapper implementation based on `zend-db`


## Example:
    
### Define our Post model
EntityInterface defines getter and setter for id, nothing more. 
    
    class Post implements EntityInterface
    {
        public $id;
        public $user;
        public $title;
    
    
        /**
         * @param mixed $id
         * @return EntityInterface
         */
        public function setId($id)
        {
            $this->id = $id;
    
            return $this;
        }
    
        /**
         * @return mixed
         */
        public function getId()
        {
            return $this->id;
        }
    }
    
### Define our mapper
    
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
    
### Add stuff in configuration

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
    
### In your code
    
    $repository = $serviceLocator->get(\Repository\Repository\RepositoryPluginManager::class);
    $mapper = $repository->get(Post::class);
    
    $singlePost = $mapper->id(10);
    $allPosts   = $mapper->fetch();
    $somePosts  = $mapper->fetch(['title' => 'My post title'])
    
## Features

### \Repository\Mapper\Feature\SoftDelete

Enables "soft-delete" feature. Exposes `recover` method

    // In your repository
    protected static $features = [
        Repository\Mapper\Feature\SoftDelete::class => 'deleted_at' // default field is 'deleted_at'
    ];
    ....

    $post = $mapper->id(10);
    $mapper->delete($post);
    
    $mapper->recover($post);

### \Repository\Mapper\Feature\Timestamps

Enables created and updated fields

    // In your repository
    protected static $features = [
        Repository\Mapper\Feature\Timestamps::class => ['created_at', 'updated_at']
    ];
    ....

### \Repository\Mapper\Feature\Transaction

    // In your repository
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

### \Repository\Mapper\Feature\Relations

    // In your repository
    protected static $features = [
        Repository\Mapper\Feature\Relations::class => [
            'users'  => [HasManyThrough::class, User::class, ['post', 'user'], 'post_users'],
            'author' => [HasOne::class, User::class],
        ],
    ];
    ....
    
    $post = $mapper->withRelation(['users', 'author])->id(10);
    $post->author->name;


    
