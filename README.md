# Laravel-ORM

* [ORM Relationships](#ORM-Relationships)
* [ORM Polymorphic Relationships](#ORM-Polymorphic-Relationships)

## ORM Relationships

### Introduction

ORM (Object-Relational Mapping) relationships refer to the way objects in an object-oriented programming language are associated with tables and records in a relational database. To make a relationship between tables we often use foreign keys but in this, we refer to objects.

### One-to-One Relationship
A one-to-one relationship is a very basic relation. In this one entity is associated with oother entity through single thing. For example, a User model might be associated with one Phone. To define this relationship, we place a phone method on the User model. The phone method should call the hasOne method and return its result:

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
        return $this->hasOne('App\Models\Phone');
    }
}
```
hasOne method return us the phone which user has when we pass the id of the user in the routes.
```sh
$phone = User::find(1)->phone;
```

### Defining The Inverse Of The Relationship
Inverse is just same. The only difference is that we do things oppositely. The user hasOne phon but in this we will say that phone belongsTo one user. 
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
        return $this->belongsTo('App\Models\User');
    }
}
```
belongsTo returns the user that owns the phone.

### One To Many

A one-to-many relationship is between two entities in which one hasMany of other entity. Like for example, the user hasMany cars. 
```sh
<?php
 
namespace App;
 
use Illuminate\Database\Eloquent\Model;
 
class User extends Model
{
    /**
     * Get the Cars for the user.
     */
    public function cars()
    {
        return $this->hasMany('App\Models\Car');
    }
}
```
What hasMany does is that it returns us the array of cars to which the user belongs.



```sh
$cars = User::find(1)->cars;
 
foreach ($cars as $car) {
    //
}
```

What this does is that it finds the user whom id is given then it returns the cars that belong to the user.

###  One To Many (Inverse)

It is same as the one to many but inverse of it. Like we say this specific car of Many cars belongs to the this user. Let's see example:

```sh
<?php
 
namespace App;
 
use Illuminate\Database\Eloquent\Model;
 
class Car extends Model
{
    /**
     * Get the user that owns the Car.
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
```
Once the relationship has been defined, we can retrieve the Post model for a Car by accessing the post "dynamic property"
```sh
$Car = Car::find(1);
 
echo $Car->user->name;
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


### Has Many Through Relationship
The "has-many-through" relationship provides a convenient shortcut for accessing distant relations via an intermediate relation. For example, Let us have a scenario where we are creating a restaurant's items/menu and Items belongs to the Type and Types belongs to Category.

In simple words Category has many Types and Type has many Items. Now if we want all the Items which belongs to the Category, we need to keep the category_id in items table.:

```sh
category
    id - integer
    name - string
 
types
    id - integer
    category_id - integer
    name - string
 
items
    id - integer
    types_id - integer
    title - string
```



Now that we have examined the table structure for the relationship, let's define it on the Category model:

```sh
<?php
 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'category';
    use HasFactory;

    public function items(){
        return $this->hasManyThrough('App\Models\Item', 'App\Models\Type');
    }
}

```

The first argument passed to the hasManyThrough method is the name of the final model we wish to access, while the second argument is the name of the intermediate model.

What this hasMany is doing is that it returns the many items that category has through the type model.

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
In this case, morphOne does the work of hasOne in traditional relationships.

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
Many-to-many polymorphic relations are slightly more complicated than morphOne and morphMany relationships. For example, you have three tables in your database: User, Group, Post. Now if we do things traditionally, we will have to create two lookup tables: group_user, post_user. But as we are doing things through polymorphic relations, we can just create one taggable table that will belong to more than one model and can link all the tables.

```sh
users
    id - integer
    name - string
 
groups
    id - integer
    name - string
 
posts
    id - integer
    title - string
 
taggables
    user_id - integer
    taggable_id - integer
    taggable_type - string
```

#### Model Structure

Next, we're ready to define the relationships on the model:
Group Model:
```sh
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    public function user(){
        return $this->morphToMany('App\Models\User', 'taggable');
    }
}

```

Post Model:
```sh
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;


    public function user(){
        return $this->morphToMany('App\Models\User', 'taggable');
    }
}

```
#### Defining The Inverse Of The Relationship

Next, on the Tag model, you should define a method for each of its related models. So, for this example, we will define a posts method and a videos method:

```sh
<?php

namespace App\Models;
use App\Models\Role;


use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    

    use HasApiTokens, HasFactory, Notifiable;

  

    public function groups(){
        return $this->morphedByMany('App\Models\Group','taggable');
    }

    public function posts(){
        return $this->morphedByMany('App\Models\Post','taggable');
    }



}
```

#### Retrieving The Relationship

Once your database table and models are defined, you may access the relationships via your models. For example, to access all of the groups of a user, you can do:

```sh
Route::get('/morph/{id}', function($id) {
    $user_groups = User::find($id);
    $new_groups = $user_groups->groups;
    return $new_groups;
  
});
```