# Laravel-ORM

* [ORM Relationships](#ORM-Relationships)
* [ORM Polymorphic Relationships](#ORM-Polymorphic-Relationships)

## ORM Relationships

### Introduction

Database tables are often related to one another. For example, a blog post may have many comments, or an order could be related to the user who placed it. Eloquent makes managing and working with these relationships easy, and supports several different types of relationships.

### One-to-One Relationship
A one-to-one relationship is a very basic relation. For example, a User model might be associated with one Phone. To define this relationship, we place a phone method on the User model. The phone method should call the hasOne method and return its result:

```sh
<?php
 
namespace App;
 
use Illuminate\Database\Eloquent\Model;
 
class User extends Model
{
    /**
     * Get the phone record associated with the user.
     */
    public function phone()
    {
        return $this->hasOne('App\Phone');
    }
}
```

The first argument passed to the hasOne method is the name of the related model. Once the relationship is defined, we may retrieve the related record using Eloquent's dynamic properties. Dynamic properties allow you to access relationship methods as if they were properties defined on the model:

```sh
$phone = User::find(1)->phone;
```

### Defining The Inverse Of The Relationship
So, we can access the Phone model from our User. Now, let's define a relationship on the Phone model that will let us access the User that owns the phone. We can define the inverse of a hasOne relationship using the belongsTo method:
```sh
<?php
 
namespace App;
 
use Illuminate\Database\Eloquent\Model;
 
class Phone extends Model
{
    /**
     * Get the user that owns the phone.
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
```

### One To Many

A one-to-many relationship is used to define relationships where a single model owns any amount of other models. For example, a blog post may have an infinite number of comments. Like all other Eloquent relationships, one-to-many relationships are defined by placing a function on your Eloquent model:
```sh
<?php
 
namespace App;
 
use Illuminate\Database\Eloquent\Model;
 
class Post extends Model
{
    /**
     * Get the comments for the blog post.
     */
    public function comments()
    {
        return $this->hasMany('App\Comment');
    }
}
```

Remember, Eloquent will automatically determine the proper foreign key column on the Comment model. By convention, Eloquent will take the "snake case" name of the owning model and suffix it with _id. So, for this example, Eloquent will assume the foreign key on the Comment model is post_id.

Once the relationship has been defined, we can access the collection of comments by accessing the comments property. Remember, since Eloquent provides "dynamic properties", we can access relationship methods as if they were defined as properties on the model:

```sh
$comments = App\Post::find(1)->comments;
 
foreach ($comments as $comment) {
    //
}
```

###  One To Many (Inverse)

Now that we can access all of a post's comments, let's define a relationship to allow a comment to access its parent post. To define the inverse of a hasMany relationship, define a relationship function on the child model which calls the belongsTo method:

```sh
<?php
 
namespace App;
 
use Illuminate\Database\Eloquent\Model;
 
class Comment extends Model
{
    /**
     * Get the post that owns the comment.
     */
    public function post()
    {
        return $this->belongsTo('App\Post');
    }
}
```
Once the relationship has been defined, we can retrieve the Post model for a Comment by accessing the post "dynamic property"
```sh
$comment = App\Comment::find(1);
 
echo $comment->post->title;
```


### Many To Many Relationship

Many-to-many relations are slightly more complicated than hasOne and hasMany relationships. An example of such a relationship is a user with many roles, where the roles are also shared by other users. For example, many users may have the role of "Admin".

#### Table Structure

To define this relationship, three database tables are needed: users, roles, and role_user. The role_user table is derived from the alphabetical order of the related model names, and contains the user_id and role_id columns:
```sh
users
    id - integer
    name - string
 
roles
    id - integer
    name - string
 
role_user
    user_id - integer
    role_id - integer
```

#### Model Structure

Many-to-many relationships are defined by writing a method that returns the result of the belongsToMany method. For example, let's define the roles method on our User model:

```sh
<?php
 
namespace App;
 
use Illuminate\Database\Eloquent\Model;
 
class User extends Model
{
    /**
     * The roles that belong to the user.
     */
    public function roles()
    {
        return $this->belongsToMany('App\Role');
    }
}
```
Once the relationship is defined, you may access the user's roles using the roles dynamic property:
```sh
$user = App\User::find(1);
 
foreach ($user->roles as $role) {
    //
}
```

To determine the table name of the relationship's joining table, Eloquent will join the two related model names in alphabetical order. However, you are free to override this convention. You may do so by passing a second argument to the belongsToMany method:

```sh
return $this->belongsToMany('App\Role', 'role_user');
```

### Defining The Inverse Of Many To Many Relationship

To define the inverse of a many-to-many relationship, you place another call to belongsToMany on your related model. To continue our user roles example, let's define the users method on the Role model:


```sh
<?php
 
namespace App;
 
use Illuminate\Database\Eloquent\Model;
 
class Role extends Model
{
    /**
     * The users that belong to the role.
     */
    public function users()
    {
        return $this->belongsToMany('App\User');
    }
}
```
As you can see, the relationship is defined exactly the same as its User counterpart, with the exception of referencing the App\User model. Since we're reusing the belongsToMany method, all of the usual table and key customization options are available when defining the inverse of many-to-many relationships.


### Has One Through Relationship

The "has-one-through" relationship links models through a single intermediate relation.

For example, in a vehicle repair shop application, each Mechanic may have one Car, and each Car may have one Owner. While the Mechanic and the Owner have no direct connection, the Mechanic can access the Owner through the Car itself. Let's look at the tables necessary to define this relationship:

```sh
mechanics
    id - integer
    name - string
 
cars
    id - integer
    model - string
    mechanic_id - integer
 
owners
    id - integer
    name - string
    car_id - integer

```

Now that we have examined the table structure for the relationship, let's define the relationship on the Mechanic model:

```sh
<?php
 
namespace App;
 
use Illuminate\Database\Eloquent\Model;
 
class Mechanic extends Model
{
    /**
     * Get the car's owner.
     */
    public function carOwner()
    {
        return $this->hasOneThrough('App\Owner', 'App\Car');
    }
}
```
The first argument passed to the hasOneThrough method is the name of the final model we wish to access, while the second argument is the name of the intermediate model.

Typical Eloquent foreign key conventions will be used when performing the relationship's queries. If you would like to customize the keys of the relationship, you may pass them as the third and fourth arguments to the hasOneThrough method. The third argument is the name of the foreign key on the intermediate model. The fourth argument is the name of the foreign key on the final model. The fifth argument is the local key, while the sixth argument is the local key of the intermediate model:

```sh

class Mechanic extends Model
{
    /**
     * Get the car's owner.
     */
    public function carOwner()
    {
        return $this->hasOneThrough(
            'App\Owner',
            'App\Car',
            'mechanic_id', // Foreign key on cars table...
            'car_id', // Foreign key on owners table...
            'id', // Local key on mechanics table...
            'id' // Local key on cars table...
        );
    }
}

```

### Has Many Through Relationship
The "has-many-through" relationship provides a convenient shortcut for accessing distant relations via an intermediate relation. For example, a Country model might have many Post models through an intermediate User model. In this example, you could easily gather all blog posts for a given country. Let's look at the tables required to define this relationship:

```sh
countries
    id - integer
    name - string
 
users
    id - integer
    country_id - integer
    name - string
 
posts
    id - integer
    user_id - integer
    title - string
```

Though posts does not contain a country_id column, the hasManyThrough relation provides access to a country's posts via $country->posts. To perform this query, Eloquent inspects the country_id on the intermediate users table. After finding the matching user IDs, they are used to query the posts table.

Now that we have examined the table structure for the relationship, let's define it on the Country model:

```sh
<?php
 
namespace App;
 
use Illuminate\Database\Eloquent\Model;
 
class Country extends Model
{
    /**
     * Get all of the posts for the country.
     */
    public function posts()
    {
        return $this->hasManyThrough('App\Post', 'App\User');
    }
}
```

The first argument passed to the hasManyThrough method is the name of the final model we wish to access, while the second argument is the name of the intermediate model.

## ORM Polymorphic Relationships

A polymorphic relationship allows the target model to belong to more than one type of model using a single association.

### One To One (Polymorphic)

#### Table Structure
A one-to-one polymorphic relation is similar to a simple one-to-one relation; however, the target model can belong to more than one type of model on a single association. For example, a blog Post and a User may share a polymorphic relation to an Image model. Using a one-to-one polymorphic relation allows you to have a single list of unique images that are used for both blog posts and user accounts. First, let's examine the table structure:

```sh
posts
    id - integer
    name - string
 
users
    id - integer
    name - string
 
images
    id - integer
    url - string
    imageable_id - integer
    imageable_type - string
```

Take note of the imageable_id and imageable_type columns on the images table. The imageable_id column will contain the ID value of the post or user, while the imageable_type column will contain the class name of the parent model. The imageable_type column is used by Eloquent to determine which "type" of parent model to return when accessing the imageable relation.

#### Model Structure

```sh
<?php
 
namespace App;
 
use Illuminate\Database\Eloquent\Model;
 
class Image extends Model
{
    /**
     * Get the owning imageable model.
     */
    public function imageable()
    {
        return $this->morphTo();
    }
}
 
class Post extends Model
{
    /**
     * Get the post's image.
     */
    public function image()
    {
        return $this->morphOne('App\Image', 'imageable');
    }
}
 
class User extends Model
{
    /**
     * Get the user's image.
     */
    public function image()
    {
        return $this->morphOne('App\Image', 'imageable');
    }
}
```

#### Retrieving The Relationship

Once your database table and models are defined, you may access the relationships via your models. For example, to retrieve the image for a post, we can use the image dynamic property:

```sh
$post = App\Post::find(1);
 
$image = $post->image;
```


### One To Many (Polymorphic)

#### Table Structure
A one-to-many polymorphic relation is similar to a simple one-to-many relation; however, the target model can belong to more than one type of model on a single association. For example, imagine users of your application can "comment" on both posts and videos. Using polymorphic relationships, you may use a single comments table for both of these scenarios. First, let's examine the table structure required to build this relationship:

```sh
posts
    id - integer
    title - string
    body - text
 
videos
    id - integer
    title - string
    url - string
 
comments
    id - integer
    body - text
    commentable_id - integer
    commentable_type - string

```

#### Model Structure
Next, let's examine the model definitions needed to build this relationship:
```sh
<?php
 
namespace App;
 
use Illuminate\Database\Eloquent\Model;
 
class Comment extends Model
{
    /**
     * Get the owning commentable model.
     */
    public function commentable()
    {
        return $this->morphTo();
    }
}
 
class Post extends Model
{
    /**
     * Get all of the post's comments.
     */
    public function comments()
    {
        return $this->morphMany('App\Comment', 'commentable');
    }
}
 
class Video extends Model
{
    /**
     * Get all of the video's comments.
     */
    public function comments()
    {
        return $this->morphMany('App\Comment', 'commentable');
    }
}
```

#### Retrieving The Relationship

Once your database table and models are defined, you may access the relationships via your models. For example, to access all of the comments for a post, we can use the comments dynamic property:

```sh
$post = App\Post::find(1);
 
foreach ($post->comments as $comment) {
    //
}
```

### Many To Many (Polymorphic)

#### Table Structure
Many-to-many polymorphic relations are slightly more complicated than morphOne and morphMany relationships. For example, a blog Post and Video model could share a polymorphic relation to a Tag model. Using a many-to-many polymorphic relation allows you to have a single list of unique tags that are shared across blog posts and videos. First, let's examine the table structure:

```sh
posts
    id - integer
    name - string
 
videos
    id - integer
    name - string
 
tags
    id - integer
    name - string
 
taggables
    tag_id - integer
    taggable_id - integer
    taggable_type - string
```

#### Model Structure

Next, we're ready to define the relationships on the model. The Post and Video models will both have a tags method that calls the morphToMany method on the base Eloquent class:

```sh
<?php
 
namespace App;
 
use Illuminate\Database\Eloquent\Model;
 
class Post extends Model
{
    /**
     * Get all of the tags for the post.
     */
    public function tags()
    {
        return $this->morphToMany('App\Tag', 'taggable');
    }
}
```
#### Defining The Inverse Of The Relationship

Next, on the Tag model, you should define a method for each of its related models. So, for this example, we will define a posts method and a videos method:

```sh
<?php
 
namespace App;
 
use Illuminate\Database\Eloquent\Model;
 
class Tag extends Model
{
    /**
     * Get all of the posts that are assigned this tag.
     */
    public function posts()
    {
        return $this->morphedByMany('App\Post', 'taggable');
    }
 
    /**
     * Get all of the videos that are assigned this tag.
     */
    public function videos()
    {
        return $this->morphedByMany('App\Video', 'taggable');
    }
}
```

#### Retrieving The Relationship

Once your database table and models are defined, you may access the relationships via your models. For example, to access all of the tags for a post, you can use the tags dynamic property:

```sh
$post = App\Post::find(1);
 
foreach ($post->tags as $tag) {
    //
}
```