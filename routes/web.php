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
use Illuminate\Support\Facades\Log;

// Client-specific Controllers (Make sure these exist!)
use App\Http\Controllers\ClientServicesController;
use App\Http\Controllers\ClientEmployeesController;
use App\Http\Controllers\ClientDocumentController; // Specific controller for client's own documents
use App\Http\Controllers\ClientFormController;     // Specific controller for client's forms

use App\Http\Controllers\DocumentApprovalController; // For document approval by admin/employee.


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
Route::get('/csrf-token', function() {
    return response()->json(['csrf_token' => csrf_token()]);
});

// Session extend route
Route::post('/extend-session', function() {
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
Route::get('/dynamic-forms/public/{form}', [DynamicFormController::class, 'showPublicForm'])->name('dynamic-forms.public-show');
Route::post('/dynamic-forms/public/{form}/submit', [DynamicFormController::class, 'submitPublicForm'])->name('dynamic-forms.submit');


// Authenticated Routes - All routes below this require authentication
Route::middleware(['auth'])->group(function () {

    // Dashboard redirection for all authenticated users
    Route::get('/', [DashboardController::class, 'redirectBasedOnRole'])->name('dashboard');

    Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');

    // --- Admin Routes ---
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin/dashboard', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');

        // Resources for Admin
        Route::resource('clients', ClientController::class);
        Route::resource('employees', EmployeeController::class);
        Route::resource('dynamic-forms', DynamicFormController::class);
        Route::get('/dynamic-forms/{id}/share', [DynamicFormController::class, 'share'])->name('dynamic-forms.share');
        Route::post('/dynamic-forms/{id}/send', [DynamicFormController::class, 'send'])->name('dynamic-forms.send');
        Route::resource('services', ServiceController::class);
        Route::resource('call-logs', CallLogController::class);
        Route::resource('tasks', TaskController::class);
        Route::resource('documents', DocumentController::class); // Admin's comprehensive document management
        Route::resource('document-categories', DocumentCategoryController::class);
        Route::resource('client-services', ClientServiceController::class); // Admin managing client-service pivot

        // Admin-specific routes
        Route::get('/clients/{client}/manage-access', [ClientController::class, 'manageAccess'])->name('clients.manage-access');
        Route::patch('/services/{service}/toggle-status', [ServiceController::class, 'toggleStatus'])->name('services.toggle-status');
        Route::patch('/call-logs/{call_log}/status', [CallLogController::class, 'updateStatus'])->name('call-logs.update-status');
        Route::get('/call-logs/client/{client}/contacts', [CallLogController::class, 'getClientContacts'])->name('call-logs.client-contacts');
        Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
        Route::get('/documents/{document}/preview', [DocumentController::class, 'preview'])->name('documents.preview');
        Route::get('/documents/{document}/manage-access', [DocumentController::class, 'manageAccess'])->name('documents.manage-access');
        Route::patch('/documents/{document}/access', [DocumentController::class, 'updateAccess'])->name('documents.update-access');
    });

    // --- Employee Routes ---
    Route::middleware(['role:employee'])->group(function () {
        Route::get('/employee/dashboard', [DashboardController::class, 'employeeDashboard'])->name('employee.dashboard');

        // Employee-specific tasks/logs
        Route::get('/my-tasks', [TaskController::class, 'myTasks'])->name('tasks.my-tasks'); // Shared route name, ensure logic handles employee context
        Route::get('/employee/call-logs', [CallLogController::class, 'index'])->name('employee.call-logs.index');
        Route::get('/employee/tasks', [TaskController::class, 'index'])->name('employee.tasks.index'); // Employee's view of all tasks

        // Employee access to general documents (perhaps filtered by their accessible clients)
        Route::get('/employee/documents', [DocumentController::class, 'index'])->name('employee.documents.index');
        // Employee's view of client list (if they manage clients)
        Route::get('/employee/clients', [ClientController::class, 'index'])->name('employee.clients.index'); // Or specific list of accessible clients
    });

    // --- Client Routes ---
    Route::middleware(['role:client'])->group(function () {
        Route::get('/client/dashboard', [DashboardController::class, 'clientDashboard'])->name('client.dashboard');

        // Client's dedicated pages - These were missing!
        Route::get('/client/documents', [ClientDocumentController::class, 'index'])->name('clients.documents.index');
        Route::get('/client/documents/{document}/show', [ClientDocumentController::class, 'show'])->name('clients.documents.show');
        Route::get('/client/documents/{document}/preview', [ClientDocumentController::class, 'preview'])->name('clients.documents.preview');
        Route::get('/client/services', [ClientServicesController::class, 'index'])->name('clients.services.index');
        Route::get('/client/employees', [ClientEmployeesController::class, 'index'])->name('clients.employees.index');
        //Route::get('/client/documents', [ClientDocumentController::class, 'index'])->name('documents.index');
        Route::get('/clients/forms', [ClientFormController::class, 'index'])->name('clients.forms.index');

        // Optional: Client-specific document actions (if allowed to upload/download their own)
        // Route::get('/client/documents/upload', [ClientDocumentController::class, 'create'])->name('documents.create');
        // Route::post('/client/documents', [ClientDocumentController::class, 'store'])->name('documents.store');
        // Route::get('/client/documents/{document}/download', [ClientDocumentController::class, 'download'])->name('documents.download');
        // Route::get('/client/documents/{document}/show', [ClientDocumentController::class, 'show'])->name('documents.show');
        //Route::get('/client/images', [ClientImageController::class, 'index'])->name('client.images.index');

        // Optional: Client-specific form response viewing
        Route::get('/client/form-responses/{dynamicFormResponse}', [ClientFormController::class, 'show'])->name('clients.form-responses.show');

        // Temporary debug route
        Route::get('/debug-client-forms', function() {
            Log::info('Debug route accessed', [
                'user_authenticated' => Auth::check(),
                'user_role' => Auth::check() ? Auth::user()->role : 'not-authenticated'
            ]);

            if (!Auth::check()) {
                return 'Not authenticated';
            }

            $user = Auth::user();
            return response()->json([
                'user_id' => $user->id,
                'user_role' => $user->role,
                'has_client_profile' => $user->client ? true : false,
                'client_id' => $user->client ? $user->client->id : null
            ]);
        });
    });

    // --- Shared Document Routes (Admin, Employee, Client) ---
    Route::resource('documents', DocumentController::class);
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
    Route::get('/documents/{document}/edit', [DocumentController::class, 'edit'])->name('documents.edit');
    Route::post('/documents/{document}/approve', [DocumentController::class, 'approve'])->name('documents.approve');
    Route::post('/documents/{document}/reject', [DocumentController::class, 'reject'])->name('documents.reject');


     // --- Document Approval Routes (Shared by Admin & Assigned Employees) ---
     Route::prefix('document-approvals')->name('document-approvals.')->group(function () {
        Route::get('/', [DocumentApprovalController::class, 'index'])->name('index');
        Route::get('/{document}', [DocumentApprovalController::class, 'show'])->name('show');
        Route::post('/{document}/approve', [DocumentApprovalController::class, 'approve'])->name('approve');
        Route::post('/{document}/reject', [DocumentApprovalController::class, 'reject'])->name('reject');
    });

    Route::post('/documents/{document}/approve', [DocumentApprovalController::class, 'approve'])->name('documents.approve');
    Route::post('/documents/{document}/reject', [DocumentApprovalController::class, 'reject'])->name('documents.reject');
});

// Simple test route outside of any middleware
Route::get('/test-route', function() {
    return 'Test route working';
});