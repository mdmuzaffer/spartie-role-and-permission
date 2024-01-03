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

Step 3: Install spatie/laravel-permission Packages
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
