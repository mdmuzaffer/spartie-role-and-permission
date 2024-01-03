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
protected $middlewareAliases = [
    ....
    'role' => \Spatie\Permission\Middlewares\RoleMiddleware::class,
    'permission' => \Spatie\Permission\Middlewares\PermissionMiddleware::class,
    'role_or_permission' => \Spatie\Permission\Middlewares\RoleOrPermissionMiddleware::class,
]

```

![image](https://github.com/mdmuzaffer/spartie-role-and-permission/assets/58267203/6047c674-5524-4e36-98c2-e0659fbc6136)

#### Step 7: Create Routes
Now, we will add routes in the web.php file.
routes/web.php
```bash
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

#### Step 8: Add Controllers
Now, we will create a controller and add the below code in the controller.
app/Http/Controllers/UserController.php

```bash

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use DB;
use Hash;
use Illuminate\Support\Arr;

class UserController extends Controller
{

    function __construct()
    {
         $this->middleware('permission:user-list|user-create|user-edit|user-delete', ['only' => ['index','show']]);
         $this->middleware('permission:user-create', ['only' => ['create','store']]);
         $this->middleware('permission:user-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:user-delete', ['only' => ['destroy']]);

        /* $this->middleware('permission:user-list|user-create|user-edit|user-delete', ['only' => ['index','show', 'create','store', 'edit','update', 'destroy']]);*/

    }
     

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = User::orderBy('id','DESC')->paginate(5);
        return view('users.index',compact('data'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::pluck('name','name')->all();
        return view('users.create',compact('roles'));
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:confirm-password',
            'roles' => 'required'
        ]);
    
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
    
        $user = User::create($input);
        $user->assignRole($request->input('roles'));
    
        return redirect()->route('users.index')
                        ->with('success','User created successfully');
    }
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
        return view('users.show',compact('user'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::find($id);
        $roles = Role::pluck('name','name')->all();
        $userRole = $user->roles->pluck('name','name')->all();
    
        return view('users.edit',compact('user','roles','userRole'));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
            'password' => 'same:confirm-password',
            'roles' => 'required'
        ]);
    
        $input = $request->all();
        if(!empty($input['password'])){ 
            $input['password'] = Hash::make($input['password']);
        }else{
            $input = Arr::except($input,array('password'));    
        }
    
        $user = User::find($id);
        $user->update($input);
        DB::table('model_has_roles')->where('model_id',$id)->delete();
    
        $user->assignRole($request->input('roles'));
    
        return redirect()->route('users.index')
                        ->with('success','User updated successfully');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
    */

    public function destroy($id)
    {
        User::find($id)->delete();
        return redirect()->route('users.index')
                        ->with('success','User deleted successfully');
    }

}
```

 app/Http/Controllers/ProductController.php
 
```bash
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
         $this->middleware('permission:product-list|product-create|product-edit|product-delete', ['only' => ['index','show']]);
         $this->middleware('permission:product-create', ['only' => ['create','store']]);
         $this->middleware('permission:product-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:product-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::latest()->paginate(5);
        return view('products.index',compact('products'))->with('i', (request()->input('page', 1) - 1) * 5);
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('products.create');
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate([
            'name' => 'required',
            'detail' => 'required',
        ]);
    
        Product::create($request->all());
    
        return redirect()->route('products.index')->with('success','Product created successfully.');
    }
    
    /**
     * Display the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        return view('products.show',compact('product'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        return view('products.edit',compact('product'));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
         request()->validate([
            'name' => 'required',
            'detail' => 'required',
        ]);
    
        $product->update($request->all());
        return redirect()->route('products.index')->with('success','Product updated successfully');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success','Product deleted successfully');
    }

}
```

app/Http/Controllers/RoleController.php

```bash 
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Middlewares\PermissionMiddleware;

use DB;


class RoleController extends Controller
{
     

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {

         $this->middleware('permission:role-list|role-create|role-edit|role-delete', ['only' => ['index','store']]);
         $this->middleware('permission:role-create', ['only' => ['create','store']]);
         $this->middleware('permission:role-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:role-delete', ['only' => ['destroy']]);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $roles = Role::orderBy('id','DESC')->paginate(5);
        return view('roles.index',compact('roles'))->with('i', ($request->input('page', 1) - 1) * 5);
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $permission = Permission::get();
        return view('roles.create',compact('permission'));
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validate($request, [
            'name' => 'required|unique:roles,name',
            'permission' => 'required',
        ]);
    
        $role = Role::create(['name' => $request->input('name')]);
        $role->syncPermissions($request->input('permission'));
    
        return redirect()->route('roles.index')->with('success','Role created successfully');
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role = Role::find($id);
        $rolePermissions = Permission::join("role_has_permissions","role_has_permissions.permission_id","=","permissions.id")
            ->where("role_has_permissions.role_id",$id)
            ->get();
    
        return view('roles.show',compact('role','rolePermissions'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = Role::find($id);
        $permission = Permission::get();
        $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id",$id)
            ->pluck('role_has_permissions.permission_id','role_has_permissions.permission_id')
            ->all();
    
        return view('roles.edit',compact('role','permission','rolePermissions'));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'permission' => 'required',
        ]);
    
        $role = Role::find($id);
        $role->name = $request->input('name');
        $role->save();
    
        $role->syncPermissions($request->input('permission'));
    
        return redirect()->route('roles.index')->with('success','Role updated successfully');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::table("roles")->where('id',$id)->delete();
        return redirect()->route('roles.index')->with('success','Role deleted successfully');
    }

}

```

#### Step 9: Create Blade File

![image](https://github.com/mdmuzaffer/spartie-role-and-permission/assets/58267203/c6eb5789-2a83-422f-bb43-37cd335aa4c5)

#### Step 10: Create Seeder For Permissions and AdminUser
Now, we will add a seeder for permission.

role-list
role-create
role-edit
role-delete
product-list
product-create
product-edit
product-delete
Now, run the below command

``` bash
php artisan make:seeder PermissionTableSeeder
```

Now, add the code below code in the database/seeds/PermissionTableSeeder.php file.

``` bash

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Permissions
        $permissions = [
            'role-list',
            'role-create',
            'role-edit',
            'role-delete',
            'product-list',
            'product-create',
            'product-edit',
            'product-delete',
            'user-list',
            'user-create',
            'user-edit',
            'user-delete'
        ];
       
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
}

```


Now, run the below command in your terminal
``` bash
php artisan db:seed --class=PermissionTableSeeder
```
Now, create a new seeder for creating admin users.

```bash
php artisan make:seeder CreateAdminUserSeeder
```
Now, add the below code in the database/seeds/CreateAdminUserSeeder.php file.

``` bash

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        

        //Admin Seeder
        $user = User::create([
            'name' => 'Muzaffer', 
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password')
        ]);
      
        $role = Role::create(['name' => 'Admin']);
        $permissions = Permission::pluck('id','id')->all();
        $role->syncPermissions($permissions);
        $user->assignRole([$role->id]);


    }
}

```
##### now data seed into table

```bash
php artisan db:seed --class=CreateAdminUserSeeder

```

#### Here login and register form link 


![image](https://github.com/mdmuzaffer/spartie-role-and-permission/assets/58267203/adc59ded-46ef-4c2b-963d-ab516a703972)

#### After login dashboard is showing here have all option to do for user create, update, show & delete

![image](https://github.com/mdmuzaffer/spartie-role-and-permission/assets/58267203/b7d1648b-25d7-49ca-8e5f-320532c23e09)

#### similar of user role create and assign permission


![image](https://github.com/mdmuzaffer/spartie-role-and-permission/assets/58267203/c907d3f5-d615-469e-8702-898c10749953)


##### See the screenshort 

![image](https://github.com/mdmuzaffer/spartie-role-and-permission/assets/58267203/b28fb1fd-c28c-450f-b154-ec3d076b8bce)


###### Similar create product if have user permission to create a product 

![image](https://github.com/mdmuzaffer/spartie-role-and-permission/assets/58267203/930926ff-c108-4a77-9b03-fa566369a5b4)

#### This tutorial develope by Muzaffer 
#### Thanks 




