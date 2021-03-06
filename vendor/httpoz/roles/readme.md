# Roles for Laravel 5.3

[![Build Status](https://travis-ci.org/httpoz/roles.svg)](https://travis-ci.org/httpoz/roles)
[![Total Downloads](https://poser.pugx.org/httpoz/roles/d/total.svg)](https://packagist.org/packages/httpoz/roles)
[![Latest Stable Version](https://poser.pugx.org/httpoz/roles/v/stable.svg)](https://packagist.org/packages/httpoz/roles)
[![Latest Unstable Version](https://poser.pugx.org/httpoz/roles/v/unstable.svg)](https://packagist.org/packages/httpoz/roles)
[![License](https://poser.pugx.org/httpoz/roles/license.svg)](https://packagist.org/packages/httpoz/roles)

Powerful package for handling roles in Laravel 5.3. 

#### History
This project was largely inspired by Roman's [romanbican/roles](https://github.com/romanbican/roles/) Laravel package. However at the time Laravel 5.3 was released his package was not actively maintained. I have ommitted permissions in this package in favour of Laravel's [Authorization](https://laravel.com/docs/5.3/authorization). I intend to keep this package as simple and minimal as is possible.



- [Installation](#installation)
    - [Composer](#composer)
    - [Service Provider](#service-provider)
    - [Config File And Migrations](#config-file-and-migrations)
    - [HasRole Trait And Contract](#hasrole-trait-and-contract)
- [Usage](#usage)
    - [Creating Roles](#creating-roles)
    - [Attaching, Syncing and Detaching Roles](#attaching-syncing-and-detaching-roles)
    - [Checking For Roles](#checking-for-roles)
    - [Find users by their role](#find-users-by-their-role)
    - [Groups](#groups)
    - [Blade Extensions](#blade-extensions)
    - [Middleware](#middleware)
    - [Displaying Errors](#displaying-errors)
- [Config File](#config-file)
- [More Information](#more-information)
- [License](#license)

## Installation

This package is very easy to set up. There are only couple of steps.

### Composer

Run this command inside your terminal to add the package into your project.

	composer require httpoz/roles

### Service Provider

Add the package to your application service providers in `config/app.php` file.

```php
'providers' => [
    
    /*
     * Laravel Framework Service Providers...
     */
    Illuminate\Foundation\Providers\ArtisanServiceProvider::class,
    Illuminate\Auth\AuthServiceProvider::class,
    ...
    
    /**
     * Third Party Service Providers...
     */
    HttpOz\Roles\RolesServiceProvider::class,

],
```

### Config File And Migrations

Publish the package config file and migrations to your application. Run this command inside your terminal.

    php artisan vendor:publish --provider="HttpOz\Roles\RolesServiceProvider"

And also run migrations.

    php artisan migrate

> This uses the default users table which is in Laravel. You should already have the migration file for the users table available and migrated.

### HasRole Trait And Contract

Include `HasRole` trait and also implement `HasRole` contract inside your `User` model.

```php
use HttpOz\Roles\Traits\HasRole;
use HttpOz\Roles\Contracts\HasRole as HasRoleContract;

class User extends Authenticatable implements HasRoleContract
{
    use Notifiable, HasRole;
```

And that's it!

## Usage

### Creating Roles

```php
use HttpOz\Roles\Models\Role;

$adminRole = Role::create([
    'name' => 'Admin',
    'slug' => 'admin',
    'description' => '', // optional
    'group' => 'default', // optional, set as 'default' by default
]);

$moderatorRole = Role::create([
    'name' => 'Forum Moderator',
    'slug' => 'forum.moderator',
]);
```

> Because of `Sluggable` trait, if you make a mistake and for example leave a space in slug parameter, it'll be replaced with a dot automatically, because of `str_slug` function.

### Attaching, Syncing And Detaching Roles

It's really simple. You fetch a user from database and call `attachRole` method. There is `BelongsToMany` relationship between `User` and `Role` model.
Learn about [Many To Many Relationships](https://laravel.com/docs/5.3/eloquent-relationships#updating-many-to-many-relationships)

```php
use App\User;

$user = User::find($id);

$user->attachRole($adminRole); // you can pass whole object, or just an id
$user->detachRole($adminRole); // in case you want to detach role
$user->detachAllRoles(); // in case you want to detach all roles
$user->syncRoles($roles); // you can pass Eloquent collection, or just an array of ids
```

### Checking For Roles

You can now check if the user has required role.

```php
if ($user->isRole('admin')) { // you can pass an id or slug
    // or alternatively $user->hasRole('admin')
}
```

You can also do this:

```php
if ($user->isAdmin()) {
    //
}
```

And of course, there is a way to check for multiple roles:

```php
if ($user->isRole('admin|moderator')) { 
    /*
    | Or alternatively:
    | $user->isRole('admin, moderator'), $user->isRole(['admin', 'moderator']),
    | $user->isOne('admin|moderator'), $user->isOne('admin, moderator'), $user->isOne(['admin', 'moderator'])
    */

    // if user has at least one role
}

if ($user->isRole('admin|moderator', true)) {
    /*
    | Or alternatively:
    | $user->isRole('admin, moderator', true), $user->isRole(['admin', 'moderator'], true),
    | $user->isAll('admin|moderator'), $user->isAll('admin, moderator'), $user->isAll(['admin', 'moderator'])
    */

    // if user has all roles
}
```

### Find users by their role
 There are multiple ways to get a list of users by their given role.
 
 **Using the role's id**
 ```php 
 $admins = Role::find(1)->users;
 ```
 
 **Using the role's slug**
 ```php
 $adminRole = Role::where('slug', 'admin')->first();
 $admins = $role->users;
 ```
 **Using the role's group**
 ```php
 $adminRole = Role::where('group', 'application.managers')->first();
 $admins = $role->users;
 ```

### Groups

When you are creating roles, there is optional parameter `group`. It is set as `default` by default, but you can overwrite it and then you can do something like this:
 
```php
if ($user->group() == 'application.managers') {
    //
}
```

> If user has multiple roles, method `group` returns the first one in alphabetical order (a better implementation of this will be explored).

`Group` is intended to collectively organise and assign permissions (Laravel's built in authorization feature) to a role group that can be shared by multiple roles (examples and implementation to be added to documentation in future).

### Blade Extensions

There are two Blade extensions. Basically, it is replacement for classic if statements.

```php
@role('admin') // @if(Auth::check() && Auth::user()->isRole('admin'))
    // user is admin
@endrole

@group('application.managers') // @if(Auth::check() && Auth::user()->group() == 'application.managers')
    // user belongs to 'application.managers' group
@endgroup

@role('admin|moderator', 'all') // @if(Auth::check() && Auth::user()->isRole('admin|moderator', 'all'))
    // user is admin and also moderator
@else
    // something else
@endrole
```

### Middleware

This package comes with `VerifyRole` and `VerifyGroup` middleware. You must add them inside your `app/Http/Kernel.php` file.

```php
/**
 * The application's route middleware.
 *
 * @var array
 */
protected $routeMiddleware = [

    // ...

    'role' => \HttpOz\Roles\Middleware\VerifyRole::class,
    'group' => \HttpOz\Roles\Middleware\VerifyGroup::class,
];
```

Now you can easily protect your routes.

```php
$router->get('/example', [
    'as' => 'example',
    'middleware' => 'role:admin',
    'uses' => 'ExampleController@index',
]);

$router->get('/example', [
    'as' => 'example',
    'middleware' => 'group:application.managers',
    'uses' => 'ExampleController@index',
]);
```

It throws `\HttpOz\Roles\Exceptions\RoleDeniedException` or `\HttpOz\Roles\Exceptions\GoupDeniedException` exceptions if it goes wrong.

You can catch these exceptions inside `app/Exceptions/Handler.php` file and do whatever you want, example below.

### Displaying Errors

Extending from the above, you can control the error page that your application users see when they try to open a page their role is not allowed to. This package already has a view bundled with it that should have been published to `resources/views/vendor/roles/error.blade.php` when you published the package. Simply add the below condition inside your `app\Exceptions\Handler.php`'s render function. Feel free to point to another view of your choice.

```php
/**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof \HttpOz\Roles\Exceptions\RoleDeniedException || $exception instanceof \HttpOz\Roles\Exceptions\GroupDeniedException) {
            return response()->view('vendor.roles.errors', compact('exception'), 403);
        }

        return parent::render($request, $exception);
    }
```

## Config File

You can change connection for models, slug separator, models path and there is also a handy pretend feature. Have a look at config file for more information.

## More Information

For more information, please have a look at [HasRole](https://github.com/httpoz/roles/blob/master/src/HttpOz/Roles/Contracts/HasRole.php) contract.

## License

This package is free software distributed under the terms of the MIT license.
