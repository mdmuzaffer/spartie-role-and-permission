### Spatie Roles And Permissions In Laravel 10

#### Step 1: Install Laravel 10 For The User Role And Permission Tutorial
#### Step 2: Create Authentication Using Laravel 10
#### Step 3: Install spatie/laravel-permission Packages 
#### Step 4: Create Product Migration
#### Step 5: Create Models
#### Step 6: Add Middleware
#### Step 7: Create Routes
#### Step 8: Add Controllers
#### Step 9: Create Blade File
#### Step 10: Create Seeder For Permissions and AdminUser


## Step 1: Install Laravel 10 For The User Role And Permission Tutorial
In this step, we will install the laravel 10 application using the below command.

```bash

composer create-project laravel/laravel spartie

```

#### Step 2: Create Authentication in Laravel 10
Now, we need to generate auth scaffolding in laravel 10 using the laravel UI command.

```bash
composer require laravel/ui
```
After that, we will Install bootstrap auth using the below command.

```bash
php artisan ui bootstrap --auth
```
Now, install npm and run dev for better UI results. 

```bash
npm install
npm run dev
```

#### Step 3: Install spatie/laravel-permission Packages
Now, we will install the spatie package for ACL.

```bash
composer require spatie/laravel-permission
```
Also, install the form collection package using the below command.

```bash
composer require laravelcollective/html
```
Optional: The service provider will automatically get registered or you may manually add the service provider to your config/app.php file.

```bash
'providers' => [
	....
	Spatie\Permission\PermissionServiceProvider::class,
],
```

Now, publish this package as below.

```bash
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```
Now you can see the permission.php file and migration. So, we need to run migration using the following command.

```bash
php artisan migrate
```

#### Step 4: Create Product Migration
In this step, we will create migration for the products table.

```bash
php artisan make:migration create_products_table
```

```bash
<?php


use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;


class CreateProductsTable extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('detail');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
}

```


#### Step 5: Create Models
Now, create the User.php file
app/Models/User.php

```bash
<?php
  
namespace App\Models;
  
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
  
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;
  
    protected $fillable = [
        'name',
        'email',
        'password',
    ];
  
    protected $hidden = [
        'password',
        'remember_token',
    ];
  
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
```


After that, we will create the Product.php file.
app/Models/Product.php

```bash
<?php
  
namespace App\Models;
  
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
  
class Product extends Model
{
    use HasFactory;
  
    protected $fillable = [
        'name', 'detail'
    ];
}
```

#### Step 6: Add Middleware
In this step, we will add spatie package provides its in-built middleware, add middleware in the Kernel.php file

```bash
protected $routeMiddleware = [
    ....
    'role' => \Spatie\Permission\Middlewares\RoleMiddleware::class,
    'permission' => \Spatie\Permission\Middlewares\PermissionMiddleware::class,
    'role_or_permission' => \Spatie\Permission\Middlewares\RoleOrPermissionMiddleware::class,
]

```

#### Step 7: Create Routes
Now, we will add routes in the web.php file.
routes/web.php

<?php
  
use Illuminate\Support\Facades\Route;
  
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
  
  ```bash
Route::get('/', function () {
    return view('welcome');
});
  
Auth::routes();
  
Route::get('/home', [HomeController::class, 'index'])->name('home');
  
Route::group(['middleware' => ['auth']], function() {
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
    Route::resource('products', ProductController::class);
});

```
