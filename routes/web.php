<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\DynamicFormController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\CallLogController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DocumentCategoryController;
use App\Http\Controllers\ClientServiceController;

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

// Dashboard Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'adminDashboard'])->name('dashboard');
    Route::get('/admin/dashboard', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');
    Route::get('/employee/dashboard', [DashboardController::class, 'employeeDashboard'])->name('employee.dashboard');
    Route::get('/client/dashboard', [DashboardController::class, 'clientDashboard'])->name('client.dashboard');
});

// Admin Routes
Route::middleware(['auth'])->group(function () {
    Route::resource('clients', ClientController::class);
    Route::resource('employees', EmployeeController::class);
    Route::resource('dynamic-forms', DynamicFormController::class);
    Route::resource('services', ServiceController::class);
    Route::resource('call-logs', CallLogController::class);
    Route::resource('tasks', TaskController::class);

    // Task-specific routes
    Route::get('/my-tasks', [TaskController::class, 'myTasks'])->name('tasks.my-tasks');

    // Client-specific routes
    Route::get('/clients/{client}/manage-access', [ClientController::class, 'manageAccess'])->name('clients.manage-access');

    // Service-specific routes
    Route::patch('/services/{service}/toggle-status', [ServiceController::class, 'toggleStatus'])->name('services.toggle-status');

    // Call log specific routes
    Route::patch('/call-logs/{call_log}/status', [CallLogController::class, 'updateStatus'])->name('call-logs.update-status');
    Route::get('/call-logs/client/{client}/contacts', [CallLogController::class, 'getClientContacts'])->name('call-logs.client-contacts');

    // Document routes
    Route::resource('documents', DocumentController::class);
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
    Route::get('/documents/{document}/preview', [DocumentController::class, 'preview'])->name('documents.preview');
    Route::get('/documents/{document}/manage-access', [DocumentController::class, 'manageAccess'])->name('documents.manage-access');
    Route::patch('/documents/{document}/access', [DocumentController::class, 'updateAccess'])->name('documents.update-access');

    // Document category routes
    Route::resource('document-categories', DocumentCategoryController::class);

    // Client Service routes
    Route::resource('client-services', ClientServiceController::class);
});

// Public Dynamic Form Routes (for clients to fill)
Route::get('/forms/{form}', [DynamicFormController::class, 'showPublicForm'])->name('dynamic-forms.public');
Route::post('/forms/{form}/submit', [DynamicFormController::class, 'submitPublicForm'])->name('dynamic-forms.submit');
