 

---

# **Laravel Project Coding Standards**

## **1\. General Standards**

### **1.1 Redundancy Checks**

* **Ensure no redundant code blocks or logic duplications.**  
  * Example: Refactor repetitive code into a reusable function or service.

`// Old implementation`  
`public function index()`  
`{`  
    `$users = User::all();`  
    `return view('users.index', compact('users'));`  
`}`

`// Refactored implementation`  
`public function index()`  
`{`  
    `$users = $this->service->getAllUsers();`  
    `return view('users.index', compact('users'));`  
`}`

* **Regularly refactor code to eliminate redundancy and improve maintainability.**

### **1.2 Completeness Checks**

* **Verify all code and features are fully implemented and thoroughly tested.**  
  * Example: Ensure all endpoints have corresponding tests.

`public function testIndex()`  
`{`  
    `$response = $this->get('/users');`  
    `$response->assertStatus(200);`  
`}`

* **Conduct code reviews and use checklists to ensure completeness.**

### **1.3 Code Formatting**

* **Use the Prettier package for code formatting.**  
  * Example: Configure Prettier in your project.

`npm install --save-dev prettier`  
`echo {}> .prettierrc.json`

* **Format code every time changes are made to ensure consistency.**

### **1.4 Indentation**

* **Use 4 spaces for indentation across all files.**

### **1.5 Line Length**

* **Limit lines to 120 characters.**  
* **Wrap lines that exceed this limit to maintain readability.**

`public function exampleFunction()`  
`{`  
    `$longVariableName = "This is a very long string that exceeds the line length limit and needs to be wrapped.";`  
`}`

## **2\. Directory Structure**

* **Follow the standard Laravel directory structure.**  
* **Group related classes, traits, and interfaces into appropriate subdirectories under the `app` directory.**

## **3\. Class Naming Conventions**

### **3.1 Controllers**

* **Use PascalCase and suffix with `Controller`.**  
  * Example: `UserController`

### **3.2 Models**

* **Use singular PascalCase.**  
  * Example: `User`, `Order`

### **3.3 Factories**

* **Suffix with `Factory`.**  
  * Example: `UserFactory`

### **3.4 Migrations**

* **Use PascalCase for migration files matching the model name for easy identification.**  
  * Example: `CreateUserTable`

## **4\. Method Naming Conventions**

* **Use camelCase for method names.**  
* **Use descriptive names for methods that convey their purpose.**  
  * Example: `getActiveUsers`, `calculateTotalAmount`

## **5\. Variable Naming Conventions**

* **Use camelCase for variable names.**  
* **Use meaningful and descriptive names for variables.**  
  * Example: `$userEmail`, `$totalAmount`

**Use Enums for listing constant variables to avoid mistakes.**  
php  
Copy code  
`enum UserTypeEnum: string {`  
    `case ADMIN = 'admin';`  
    `case RESTAURANT_OWNER = 'restaurant_owner';`  
    `// More cases...`  
`}`  
`// Accessing:`  
`UserTypeEnum::ADMIN->value`

## **6\. Controller Standards**

### **6.1 Thin Controllers**

**Keep controllers thin by delegating logic to service classes and traits for repetitive methods.**

`// OrderController`  
`public function store(OrderRequest $request)`  
`{`  
	`$data = CreateOrderData::from($request->validated());`  
    `$result = $this->service->createOrder($data);`  
    `return $this->successfulResponse($result, __('Order created successfully'));`  
`}` 

### **6.2 Resource Controllers**

* **Use resource controllers for CRUD operations.**

`Route::resource('users', UserController::class);`

### **6.3 Method Order**

* **Follow the order: constructor, middleware, public methods, and private methods.**

### **6.4 Service Injection**

**Inject service classes into controllers.**  
`// OrderController`  
`public function __construct(private OrderService $service) {}`

## **7\. Model Standards**

### **7.1 Eloquent Models**

* **Use Eloquent models for database interactions.**

`$user = User::find(1); // Find user by ID`

`$users = User::where('email', 'john@test.com')->get(); // Find users by email`

### **7.2 Fillable Properties**

**Define `$fillable` properties instead of `$guarded`.**

`protected $fillable = ['name', 'email', 'password'];`

### **7.3 Relationships**

**Define relationships using appropriate methods.**  
`public function posts()`  
`{`  
    `return $this->hasMany(Post::class);`  
`}`

## **8\. Migration Standards**

### **8.1 Descriptive Names**

* **Use descriptive names for migration files.**  
- `2024_06_27_154523_add_email_column_to_users_table.php`  
- `2024_06_27_183012_update_posts_table_add_status_column.php`

### **8.2 Schema Methods**

**Use `up` and `down` methods to define schema changes clearly and revert changes if necessary.**

`public function up()`  
`{`  
    `Schema::create('users', function (Blueprint $table) {`  
        `$table->id();`  
        `$table->string('name');`  
        `$table->timestamps();`  
    `});`  
`}`

`public function down()`  
`{`  
    `Schema::dropIfExists('users');`  
`}`

## **9\. Database Standards**

### **9.1 Table Naming**

* **Use singular PascalCase for table names.**  
  * Example: `User`, `Order`

### **9.2 Column Naming**

* **Use snake\_case for column names.**  
  * Example: `first_name`, `created_at`

### **9.3 Indexes**

**Define indexes where necessary to optimise query performance.**  
`$table->index('email');`

## **10\. Route Standards**

### **10.1 RESTful Routes**

**Use RESTful routes for resource controllers.**

`Route::resource('users', UserController::class);`

### **10.2 Naming Conventions**

* **Use kebab-case for route names.**  
  * Example: `/user-profile`, `/order-items`

### **10.3 Main ID Route**

* **Use `/orders/{id}` instead of `orders?id={id}`.**

## **11\. Service and Repository Standards**

### **11.1 Service Classes**

**Encapsulate business logic in service classes.**  
`class OrderService`  
`{`  
    `public function createOrder(CreateOrderData $data)`  
    `{`  
        `// Business logic here`  
    `}`  
`}`

* 

### **11.2 Repositories**

* **Use repositories for database interactions beyond basic CRUD operations.**

### **11.3 Service Injection**

* **Inject services into controllers.**

`// OrderController`  
`public function __construct(private OrderService $service) {}`

## **12\. Validation**

### **12.1 Form Requests**

**Use Form Request classes for validating incoming data for every \[POST\] and \[PUT\] API.**

`class CreateUserRequest extends FormRequest`  
`{`  
    `public function rules()`  
    `{`  
        `return [`  
            `'name' => 'required|string',`  
            `'email' => 'required|email',`  
            `// More rules...`  
        `];`  
    `}`  
`}`

* 

### **12.2 Spatie Data Package**

**Use the Spatie Data package to convert FormRequest data into listed attributes.**  
`use Spatie\LaravelData\Data;`

`class CreateUserData extends Data`  
`{`  
    `public string $name;`  
    `public string $email;`  
`}`

### 

### **12.3 Custom Validation Rules**

* **Create custom validation rules where needed.**

`<?php`

`namespace App\Rules;`

`use Illuminate\Contracts\Validation\Rule;`

`class PhoneNumber implements Rule`

`{`

    `public function passes($attribute, $value, $arguments = [])`

    `{`

        `$pattern = '/^\d{3}-\d{3}-\d{4}$/';`

        `return preg_match($pattern, $value);`

    `}`

    `public function message($attribute, $parameters)`

    `{`

        `return 'The :attribute must be a valid phone number';`

    `}`

`}`

## **13\. Error Handling**

### **13.1 Try-Catch Blocks**

**Use try-catch blocks to handle exceptions gracefully and log errors.**  
Copy code  
`try {`  
    `// Code that may throw an exception`  
`} catch (Exception $e) {`  
    `Log::error([`  
`‘message’ => $e->getMessage(),`  
`‘line’ => $e->getLine(),`  
`‘trace’ => $e->getTrace(),`  
`‘timestamp’ => now()`  
`]);`  
`}`

* 

### **13.2 Custom Exceptions**

**Create custom exceptions for domain-specific errors.**  
`class CustomException extends Exception`  
`{`  
    `// Custom exception logic`  
`}`

## **14\. Testing Standards**

### **14.1 Testing Framework**

* **Use Pest for testing.**

### **14.2 Test Naming**

* **Use descriptive names for test methods.**

### **14.3 Test Coverage**

* **Ensure high test coverage for critical parts of the application.**

### **14.4 Test Organization**

* **Organise tests into appropriate directories.**

## **15\. Security Standards**

### **15.1 Input Sanitization**

* **Sanitise and validate all input data.**

### **15.2 XSS Protection**

* **Use Blade’s `{{ }}` to escape output.**

### **15.3 CSRF Protection**

* **Ensure all forms include CSRF tokens.**

	`<form method="POST" action="{{ route('user.store') }}">`

  		`@csrf`

		`// your codes`

	`</form>`

## **16\. Documentation**

### **16.1 DocBlocks**

* **Use PHPDoc for methods and classes.**

	`class User {` 

`/**` 

`* Get the user's full name.` 

`* @return string The user's full name.` 

`*/` 

`public function getFullName(): string {` 

`return $this->firstName . ' ' . $this->lastName;` 

`}` 

`}`

### **16.2 README**

* **Maintain a README.md with setup instructions and usage examples.**

### **16.3 API Documentation**

* **Use tools like Swagger or Postman to document APIs.**

## **17\. Version Control**

### **17.1 Git Branches**

* **Use a branching strategy (e.g., Git Flow).**

### **17.2 Commit Messages**

* **Use meaningful commit messages.**

## **18\. Environment Configuration**

### **18.1 Environment Variables**

* **Use .env for configuration.**

### **18.2 Config Files**

* **Store environment-specific settings in config files in the config folder.**

## **19\. Consistent Input Parameters**

* **Use common naming conventions for input parameters.**

## **20\. Query Optimization**

### **20.1 Eager Loading**

**Follow eager loading in queries.**  
php  
Copy code  
`$users = User::with('posts')->get();`

### **20.2 Business Logic Separation**

* **Separate business logic from the controller.**

### 

### **20.3 Client-Side Validation**

* **Manage validation at the client-side before sending data.**                        
  `$("#myForm").validate({` 

  `rules: { name: "required", email: { required: true, email: true } },` 

  `messages: {` 

  `name: "Please enter a name.",`   
  `email: "Please enter a valid email address."` 

  `}` 

`});` 

## **21\. Use of Traits**

* **Use traits for methods instead of applying queries directly in controllers.**

## **22\. Use of Soft Deletes**

* **Add softdeletes trait in models to prevent permanent deletes**  
  `class ModelName extends Model`  
  `{`  
      `use SoftDeletes;`  
  `}`  
* **Add deleted\_at column in migrations to support soft deletes**  
  `Schema::create(‘table_name’, function (Blueprint $table) {`  
  `// table columns`  
       `$table->softDeletes();`

`});`

## **23\. API Response Format**

**Standardise API responses using methods from pp/Http/Controllers/Controller.php like:.**  
`return response()->json([`  
    `'success' => false,`  
    `'message' => $message,`  
    `'errors' => $errors,`  
    `'timestamp' => now()->format('Y-m-d, H:i:s'),`  
    `'execution_time' => (microtime(true) - START_EXECUTION_TIME) * 1000 . ' ms',`  
    `'cached' => PROCESS_CACHED`  
`], Response::HTTP_UNPROCESSABLE_ENTITY);`

## **24\. URL Conventions**

### **24.1 Resource Routes**

* **Use Route::resource methods for all resource routes.**

**Example:** `Route::resource(‘users', UserController::class);`

### **24.2 Kebab Casing**

* **Use kebab casing for URLs.**  
  **Example:**  `/user-profile`, `/order-items`

### **24.3 Named Routes**

* **Use the name method for all URLs.**

	`Route::get('/users/{id}', [UserController::class, ‘show’])->name('user.show');`

## **25\. Logging and Activity Logs**

### **25.1 Request Logging**

* **Log each request to the controller.**

### **25.2 Activity Logs**

* **Create activity logs for each endpoint/action.**

### **25.3 Error Logging**

* **Log errors with the method name.**

## **26\. Use of Lang Facade**

* **Use the Lang facade to support multiple languages.**

`$welcomeMessage = Lang::get('welcome'); or`

`$welcomeMessage = trans('welcome');`

## **27\. Security Enhancements**

### **27.1 Additional Security Measures**

* **Implement additional security measures for API requests.**

### **27.2 Login Attempt Limits**

* **Implement login attempt limits and account suspension.**

## **28\. Comments and Descriptions**

* **Add comments and descriptions before creating any new function.**  
  `/**`  
  `* This function retrieves all active users from the database.`  
  `*/`  
  `function getActiveUsers()`  
  `{`  
      `// ... function logic`  
  `}`

## **29\. GET Endpoint Guidelines**

* **Every GET endpoint returning a list should be paginated.**

**Use helper methods for consistent responses created in app/Http/Controllers/Controller.php. Like:**  
`function jsonResponseWithPagination($data, $total)`  
`{`  
    `return response()->json([`  
        `'data' => $data,`  
        `'total' => $total,`  
        `'timestamp' => now()->format('Y-m-d, H:i:s'),`  
        `'execution_time' => (microtime(true) - START_EXECUTION_TIME) * 1000 . ' ms'`  
    `]);`

`}`

* **Every list endpoint should include search capabilities and status check.**

**Implement search capabilities with necessary columns and add a status column to check the status of the model (the status column should be nullable)**