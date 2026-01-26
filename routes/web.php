<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UserController;
// use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
// use Illuminate\Validation\ValidationException;
// use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\TerritoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\HotpointController;

use App\Http\Controllers\IwantItController;
use App\Models\Project;
use App\Http\Controllers\OptionController;
use App\Http\Controllers\DatisionController;

use App\Http\Controllers\ProductIaClassAjax;

use App\Http\Controllers\DatisionParameterController;

use App\Http\Controllers\AiTaskController;
use App\Http\Controllers\AiGatewayController;

use App\Http\Controllers\HotpointsDatesController;

use App\Http\Controllers\ClickStatisticController;

/*
$path = public_path('build/manifest.json');
$manifestContent = file_get_contents($path);
$manifest = json_decode($manifestContent, true);
*/

// Route::view('/', 'welcome')->name( 'home' )->middleware( 'auth' );
Route::get('/', function () {
    return redirect('/projects');
});
//Route::view('login', 'login', ['path_js' => $manifest['resources/js/app.js']['file'], 'path_css' => $manifest['resources/sass/app.scss']['file']])
Route::view('login', 'login', ['path_js' => './assets/index.66764821.js', 'path_css' => './assets/index.de6c802a.css'])
    ->name('login')
    ->middleware('guest');
// Route::view('dashboard', 'dashboard' )->name( 'dashboard' )->middleware( 'auth' );

Route::resource('users', UserController::class)->name('*', 'users')->middleware('auth');
Route::resource('tags', TagController::class)->name('*', 'tags')->middleware('auth');
Route::resource('languages', LanguageController::class)->name('*', 'languages')->middleware('auth');
Route::resource('territories', TerritoryController::class)->name('*', 'territories')->middleware('auth');

// Reemplaza esta línea:
// Route::resource('products', ProductController::class)->name('*', 'products')->middleware('auth');

// Por estas rutas individuales:

// Mostrar listado de productos (GET /products)
Route::get('/products', [ProductController::class, 'index'])
    ->name('products.index')
    ->middleware('auth');

// Mostrar formulario de crear producto (GET /products/create)
Route::get('/products/create', [ProductController::class, 'create'])
    ->name('products.create')
    ->middleware('auth');

// Guardar nuevo producto (POST /products)
Route::post('/products', [ProductController::class, 'store'])
    ->name('products.store')
    ->middleware('auth');

// Mostrar un producto específico (GET /products/{product})
Route::get('/products/{product}', [ProductController::class, 'show'])
    ->name('products.show')
    ->middleware('auth');

// Mostrar formulario de editar producto (GET /products/{product}/edit)
Route::get('/products/{product}/edit', [ProductController::class, 'edit'])
    ->name('products.edit')
    ->middleware('auth');

// Actualizar producto existente (PUT/PATCH /products/{product})
Route::put('/products/{product}', [ProductController::class, 'update'])
    ->name('products.update')
    ->middleware('auth');

Route::patch('/products/{product}', [ProductController::class, 'update'])
    ->name('products.update')
    ->middleware('auth');

// Eliminar producto (DELETE /products/{product})
Route::delete('/products/{product}', [ProductController::class, 'destroy'])
    ->name('products.destroy')
    ->middleware('auth');

// Ruta adicional para IA classes (que ya tienes)
Route::get('/products/{product}/ia-classes', [App\Http\Controllers\ProductIaClassController::class, 'index'])
    ->name('products.ia-classes')
    ->middleware('auth');

// Ruta para asociar clase IA (que ya tienes al final)
Route::post('/products/{product}/associate-ia-class', [ProductController::class, 'associateIaClass'])
    ->name('products.associate-ia-class');

// Guardar nuevo producto (POST /products)
Route::post('/products/guarda_ia', [ProductController::class, 'store'])
    ->name('products.guarda_ia');

Route::resource('brands', BrandController::class)->name('*', 'brands')->middleware('auth');
// Route::resource('projects', ProjectController::class)->name( '*', 'projects' )->middleware( 'auth' );
Route::resource('projects', ProjectController::class)->name('*', 'projects')->middleware('auth');
Route::resource('options', OptionController::class);


Route::get('/projects/{id}/inform', function (string $id) {
    $pr = new ProjectController();
    return $pr->inform($id);
})->name('projects.inform')
    ->middleware('auth');

Route::resource('hotpoints', HotpointController::class)->name('*', 'hotpoints')->middleware('auth');

/*
Route::get('/users', [ UserController::class, 'index' ] )->name( 'users.index' )->middleware( 'auth' );
Route::get('/users/create', [ UserController::class, 'create' ] )->name( 'users.create' )->middleware( 'auth' );
Route::get('/users/{user}', [ UserController::class, 'show' ] )->name( 'users.show' )->middleware( 'auth' );
Route::post('/users/', [ UserController::class, 'store' ] )->name( 'users.store' )->middleware( 'auth' );
*/

Route::post('login', [LoginController::class, 'login'])->name('login');
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

/**
 * Api REST Routing
 */
Route::post('api-iwi', [IwantitController::class, 'api_iwi_post'])->name('api-iwi');
Route::get('api-iwi', [IwantitController::class, 'api_iwi_get'])->name('api-iwi');
// Route::get('api-iwi', [ IwantitController::class, 'not_allowed' ] )->name('api-iwi');

//
// Ajax para sacar el listado de usuarios de un proyecto
//
Route::get('/projects/{id}/available-users', [ProjectController::class, 'getAvailableUsers']);
Route::post('/projects/{id}/add-user', [ProjectController::class, 'addUserToProject']);
Route::post('/projects/{id}/remove-user', [ProjectController::class, 'removeUserFromProject']);
Route::post('/projects/{project}/update-role', [ProjectController::class, 'updateUserRole']);

//
// ENDPOINTS PARA DATISION
//

// Obtener detecciones de un objeto
Route::get('/datision-detections/{project_id}/{object_class}/{distance_frames}', [DatisionController::class, 'getObjectDetections']);
Route::get('/datision-link-detections/{projectId}/{detection_id}/{product_id}', [DatisionController::class, 'updateLinkDetections']);

// Endpoints legacy para compatibilidad con servicios externos
// Endpoint para recibir datos de Datision upgrade
Route::post('/datision-upgrade', [DatisionController::class, 'upgrade']);

// Endpoint para que Datision (o cualquier cliente externo) obtenga un token CSRF
Route::get('/datision-csrf', [DatisionController::class, 'getCsrfToken'])->name('datision.csrf');

// Endpoint para que Datision para obtener un listado con todos los proyectos (con relaciones)
Route::get('/datision-get-projects', [DatisionController::class, 'getProjects']);

// routes/web.php
Route::get('/products/{product}/ia-classes', [App\Http\Controllers\ProductIaClassController::class, 'index'] )->name('products.ia-classes');

// …
Route::post( '/products/{product}/ia-classes', [ProductIaClassAjax::class, 'update'] )->name('products.ia-classes.update');


Route::middleware(['web'/*, 'auth'*/])->group(function () {
    Route::resource('datision-parameters', DatisionParameterController::class);
});

Route::middleware(['web'])->group(function () {
    // Guarda el task_id devuelto por la IA en la tabla projects
    Route::post('/ai-tasks', [AiTaskController::class, 'store'])->name('ai.tasks.store');
});

Route::middleware(['web'])->group(function () {
    Route::post('/ai/launch', [AiGatewayController::class, 'launch'])->name('ai.launch');
    Route::post('/ai-tasks', [AiGatewayController::class, 'store'])->name('ai.tasks.store');
    Route::post('/ai/result', [AiGatewayController::class, 'result'])->name('ai.result');
    Route::post('/ai/progress', [AiGatewayController::class, 'progressByProject'])->name('ai.progress');
});


// Ruta para actualizar hotpoints via AJAX
Route::post('/hotpoints/update', [HotpointsDatesController::class, 'updateHotpoints'])->name('hotpoints.update');

// Ruta opcional para obtener hotpoints (debugging)
Route::get('/hotpoints/project/{projectId}', [HotpointsDatesController::class, 'getHotpointsByProject'])->name('hotpoints.by-project');

// Ruta para la documentación de la API
Route::get('/api-docs', function () { return view('api-docs-scalar'); })->name('api.docs');
Route::get('/api-docs-stoplight', function () { return view('api-docs-stoplight'); });
Route::get('/api-docs-redoc', function () { return view('api-docs-redoc'); });
Route::get('/api-docs-rapidoc', function () {   return view('api-docs-rapidoc'); });

// Ruta para tracking de clics (estadísticas)
Route::get('/track/{type}/{id}', [ClickStatisticController::class, 'redirect'])
    ->name('track.click')
    ->where('type', 'product|brand');
