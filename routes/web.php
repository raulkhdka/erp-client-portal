<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController; // Assuming this is your login controller

// Dashboard Controllers
use App\Http\Controllers\DashboardController;

// Admin/Shared Controllers
use App\Http\Controllers\ClientController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\DynamicFormController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\CallLogController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\DocumentController; // For admin/employee general document management
use App\Http\Controllers\DocumentCategoryController;
use App\Http\Controllers\ClientServiceController; // For managing client-service assignments by admin/employee
use App\Http\Controllers\Employee\TaskController as EmployeeTaskController;
use Illuminate\Support\Facades\Log;

// Client-specific Controllers (Make sure these exist!)
use App\Http\Controllers\ClientServicesController;
use App\Http\Controllers\ClientEmployeesController;
use App\Http\Controllers\ClientDocumentController; // Specific controller for client's own documents
use App\Http\Controllers\ClientFormController;     // Specific controller for client's forms

use App\Http\Controllers\DocumentApprovalController; // For document approval by admin/employee.
use App\Http\Controllers\EmployeeCallLogController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// CSRF Token refresh route
Route::get('/csrf-token', function () {
    return response()->json(['csrf_token' => csrf_token()]);
});

// Session extend route
Route::post('/extend-session', function () {
    if (Auth::check()) {
        session()->regenerate();
        return response()->json(['success' => true, 'csrf_token' => csrf_token()]);
    }
    return response()->json(['success' => false], 401);
})->middleware('auth');

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Public Dynamic Form Routes (for clients to fill without being logged in if needed)
// Keep these outside the 'auth' middleware if anonymous submission is allowed
//Route::post('/dynamic-forms/store', [DynamicFormController::class, 'store'])->name('dynamic-forms.store');
Route::get('/dynamic-forms/public/{form}', [DynamicFormController::class, 'showPublicForm'])->name('admin.dynamic-forms.public-show');
Route::post('/dynamic-forms/public/{form}/submit', [DynamicFormController::class, 'submitPublicForm'])->name('admin.dynamic-forms.submit');


// Authenticated Routes - All routes below this require authentication
Route::middleware(['auth'])->group(function () {

    // Dashboard redirection for all authenticated users
    Route::get('/', [DashboardController::class, 'redirectBasedOnRole'])->name('dashboard');

    //Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');

    // --- Admin Routes ---
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin/dashboard', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');

        // Client management routes
        // Using a prefix and controller grouping for cleaner routes
        Route::prefix('clients')
            ->name('admin.clients.')
            ->controller(App\Http\Controllers\ClientController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{id}/edit', 'edit')->name('edit');
                Route::put('/{id}', 'update')->name('update');
                Route::delete('/{id}', 'destroy')->name('destroy');
                Route::get('/{id}', 'show')->name('show');
                Route::get('/{client}/manage-access', 'manageAccess')->name('manage-access');
            });

        // Route::resource('clients', ClientController::class);
        // Employee Management Routes
        Route::prefix('employees')
            ->name('admin.employees.')
            ->controller(App\Http\Controllers\EmployeeController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{id}/edit', 'edit')->name('edit');
                Route::put('/{id}', 'update')->name('update');
                Route::delete('/{id}', 'destroy')->name('destroy');
                Route::get('/{id}', 'show')->name('show');
            });
        // Blended routes for services management
        Route::prefix('services')
            ->name('admin.services.')
            ->controller(App\Http\Controllers\ServiceController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{service}/edit', 'edit')->name('edit');
                Route::put('/{service}', 'update')->name('update');
                Route::delete('/{service}', 'destroy')->name('destroy');
                Route::get('/{service}', 'show')->name('show');
                Route::patch('/{service}/toggle-status', 'toggleStatus')->name('toggle-status');
            });

        //Call Logs management blended route
        Route::prefix('call-logs')
            ->name('admin.call-logs.')
            ->controller(App\Http\Controllers\CallLogController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{callLog}/edit', 'edit')->name('edit');
                Route::put('/{callLog}', 'update')->name('update');
                Route::delete('/{callLog}', 'destroy')->name('destroy');
                Route::get('/{callLog}', 'show')->name('show');
                Route::patch('/{callLog}/status', 'updateStatus')->name('update-status');
                Route::get('/{callLog}/contacts', 'getClientContacts')->name('client-contacts');
            });

        // Tasks Management Routes blended
        Route::prefix('tasks')
            ->name('admin.tasks.')
            ->controller(App\Http\Controllers\TaskController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{task}/edit', 'edit')->name('edit');
                Route::put('/{task}', 'update')->name('update');
                Route::delete('/{task}', 'destroy')->name('destroy');
                Route::get('/{task}', 'show')->name('show');
                Route::get('/my-tasks', 'myTasks')->name('my-tasks'); // Admin's view of all tasks
            });

        //Blended Documents Routes Management
        Route::prefix('documents')
            ->name('admin.documents.')
            ->controller(App\Http\Controllers\DocumentController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{document}/edit', 'edit')->name('edit');
                Route::put('/{document}', 'update')->name('update');
                Route::delete('/{document}', 'destroy')->name('destroy');
                Route::get('/{document}', 'show')->name('show');
                Route::get('/{document}/download', 'download')->name('download');
                Route::get('/{document}/preview', 'preview')->name('preview');
                Route::get('/{document}/manage-access', 'manageAccess')->name('manage-access');
                Route::patch('/{document}/access', 'updateAccess')->name('update-access');
            });


        // Blended Dynamic Forms Management
        Route::prefix('dynamic-forms')
            ->name('admin.dynamic-forms.')
            ->controller(App\Http\Controllers\DynamicFormController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{form}/edit', 'edit')->name('edit');
                Route::put('/{form}', 'update')->name('update');
                Route::delete('/{form}', 'destroy')->name('destroy');
                Route::get('/{form}', 'show')->name('show');
                Route::get('/{form}/share', 'share')->name('share');
                Route::post('/{form}/send', 'send')->name('send');
            });

        // Blended Document Categories Management
        Route::prefix('document-categories')
            ->name('admin.document-categories.')
            ->controller(App\Http\Controllers\DocumentCategoryController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{id}/edit', 'edit')->name('edit');
                Route::put('/{id}', 'update')->name('update');
                Route::delete('/{id}', 'destroy')->name('destroy');
                Route::get('/{id}', 'show')->name('show');
            });

        // Admin-specific routes


    });

    // --- Employee Routes ---
    Route::middleware(['role:employee'])->group(function () {
        Route::get('/employee/dashboard', [DashboardController::class, 'employeeDashboard'])->name('employee.dashboard');

        // Employee Tasks Routes
        Route::prefix('employee/tasks')->name('employees.tasks.')->controller(App\Http\Controllers\Employee\TaskController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{task}', 'show')->name('show')->where('task', '[0-9]+');
            Route::patch('/{task}/status', 'updateStatus')->name('update-status');
        });

        // Employee Call Logs Routes
        Route::prefix('employee/call-logs')->name('employees.call-logs.')->group(function () {
            Route::get('/', [EmployeeCallLogController::class, 'index'])->name('index');
            Route::get('/create', [EmployeeCallLogController::class, 'create'])->name('create');
            Route::post('/', [EmployeeCallLogController::class, 'store'])->name('store');
            Route::get('/{callLog}', [EmployeeCallLogController::class, 'show'])->name('show');
            Route::get('/{callLog}/edit', [EmployeeCallLogController::class, 'edit'])->name('edit');
            Route::put('/{callLog}', [EmployeeCallLogController::class, 'update'])->name('update');
            Route::delete('/{callLog}', [EmployeeCallLogController::class, 'destroy'])->name('destroy');
        });

        // Employee Document Routes
        Route::get('/employee/documents', [DocumentController::class, 'index'])->name('employee.documents.index');

        // Employee Client Routes
        Route::get('/employee/clients', [ClientController::class, 'index'])->name('employee.clients.index');
    });

    // --- Client Routes ---
    Route::middleware(['role:client'])->group(function () {
        Route::get('/client/dashboard', [DashboardController::class, 'clientDashboard'])->name('client.dashboard');

        // Client's dedicated pages - These were missing!
        Route::get('/client/documents', [ClientDocumentController::class, 'index'])->name('clients.documents.index');
        Route::get('/client/documents/{document}/show', [ClientDocumentController::class, 'show'])->name('clients.documents.show');
        Route::get('/client/documents/{document}/preview', [ClientDocumentController::class, 'preview'])->name('clients.documents.preview');
        Route::get('/client/services', [ClientServicesController::class, 'index'])->name('clients.services.index');
        Route::get('/clients/services/{service}/show', [ClientServicesController::class, 'show'])->name('clients.services.show');
        Route::get('/client/employees', [ClientEmployeesController::class, 'index'])->name('clients.employees.index');
        Route::get('/client/forms', [ClientFormController::class, 'index'])->name('clients.forms.index');
        Route::get('/client/form-responses/{dynamicFormResponse}', [ClientFormController::class, 'show'])->name('clients.form-responses.show');

        // // Temporary debug route
        // Route::get('/debug-client-forms', function() {
        //     Log::info('Debug route accessed', [
        //         'user_authenticated' => Auth::check(),
        //         'user_role' => Auth::check() ? Auth::user()->role : 'not-authenticated'
        //     ]);

        //     if (!Auth::check()) {
        //         return 'Not authenticated';
        //     }

        //     $user = Auth::user();
        //     return response()->json([
        //         'user_id' => $user->id,
        //         'user_role' => $user->role,
        //         'has_client_profile' => $user->client ? true : false,
        //         'client_id' => $user->client ? $user->client->id : null
        //     ]);
        // });
    });



    // --- Document Approval Routes (Shared by Admin & Assigned Employees) ---
    Route::prefix('admin.document-approvals')->name('admin.document-approvals.')->group(function () {
        Route::get('/', [DocumentApprovalController::class, 'index'])->name('index');
        Route::get('/{document}', [DocumentApprovalController::class, 'show'])->name('show');
        Route::post('/{document}/approve', [DocumentApprovalController::class, 'approve'])->name('approve');
        Route::post('/{document}/reject', [DocumentApprovalController::class, 'reject'])->name('reject');
    });

    //Shared Document Routes (Admin, Employee, Client)  If this doesnot work remove prefix and name
    Route::controller(DocumentController::class)->group(function () {
        Route::get('/{document}/download', 'download')->name('admin.documents.download');
        Route::get('/{document}/preview', 'preview')->name('admin.documents.preview');
    });

    // Document Approval Routes for Admin & Employee
    Route::prefix('admin.documents')->name('admin.documents.')->group(function () {
        Route::post('/{document}/approve', [DocumentApprovalController::class, 'approve'])->name('approve');
        Route::post('/{document}/reject', [DocumentApprovalController::class, 'reject'])->name('reject');
    });

    // Document Approval Routes for Admin & Employee
    Route::prefix('admin.documents')->name('admin.documents.')->group(function () {
        Route::post('/{document}/approve', [DocumentApprovalController::class, 'approve'])->name('approve');
        Route::post('/{document}/reject', [DocumentApprovalController::class, 'reject'])->name('reject');
    });

    // Route::post('/{document}/approve', [DocumentApprovalController::class, 'approve'])->name('admin.documents.approve');
    // Route::post('/{document}/reject', [DocumentApprovalController::class, 'reject'])->name('admin.documents.reject');
});

// Simple test route outside of any middleware
// Route::get('/test-route', function() {
//     return 'Test route working';
// });
