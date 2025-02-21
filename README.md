# Disclaimer
#### This library is under active development and the public api is likely to change without warning

# Usage
#### Attribute based routing can be activated for a namespace <br> by adding the following to your routes/api.php or routes/web.php file:
```php
<?php

use Gregs\AttributeRouter\AttributeRouter;

AttributeRouter::namespace('App\Http\Your\Namespace');

```
#### Routes can then be defined like this:
```php
<?php

namespace App\Http\Your\Namespace;

use Gregs\AttributeRouter\Attributes\Controller;
use Gregs\AttributeRouter\Attributes\Get;

#[Controller]
class YourController
{
     #[Get('uri')]
    public function index()
    {
      return;
    }
}
```
#### ⚠️ If the controller attribute is not present the ``` #[Get('uri')] ``` attribute wont have any effect

#### This will result in the following routing setup:

```php
Route::controller(YourController::class)->group(function() {
  Route::get('uri','index');
});
```

#### Prefixes and middlewares can be used on a controller or method level:
```php
<?php

namespace App\Http\Your\Namespace;

use Gregs\AttributeRouter\Attributes\Controller;
use Gregs\AttributeRouter\Attributes\Get;
use Gregs\AttributeRouter\Attributes\Post;
use Gregs\AttributeRouter\Attributes\Patch;
use Gregs\AttributeRouter\Attributes\Prefix;
use Gregs\AttributeRouter\Attributes\Middleware;

#[Controller]
#[Prefix('v1')]
class YourController
{
    #[Get('uri')]
    #[Prefix('entity')]
    public function index()
    {
      return;
    }

    #[Post('uri')]
    #[Prefix('entity')]
    #[Middleware('auth-sanctum')]
    public function post()
    {
      return;
    }

    #[Patch('uri')]
    #[Prefix('entity')]
    #[Middleware('auth-sanctum')]
    public function patch()
    {
      return;
    }
}
```

#### This will result in the following routing setup:

```php
Route::prefix('v1')->group(function() {
  Route::controller('YourController::class')->group(function() {
    Route::prefix('entity')->group(function() {
      Route::get('uri','index');
      Route::middleware('auth-sanctum')->group(function() {
          Route::post('uri','post');
          Route::patch('uri','patch');
      });
    });
  });
});

```
