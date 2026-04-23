<?php

use App\Livewire\CreateTicket;
use App\Livewire\DirectoryTable;
use App\Livewire\ItDashboard;
use App\Livewire\TicketDetail;
use App\Livewire\ManageInstructions;
use App\Livewire\Admin\UserManagement;
use App\Livewire\Admin\ResourceManagement;
use App\Livewire\Admin\DirectoryManagement;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home', [
        'resources' => \App\Models\GovernmentResource::all()
    ]);
})->name('home');

Route::get('/directory', function () {
    return view('directory');
})->name('directory');

Route::get('/support', CreateTicket::class)->name('support');

Route::middleware(['auth'])->group(function () {
    // Shared for Technicians and Admins
    Route::middleware(['role:it_support|admin'])->group(function () {
        Route::get('/it-dashboard', ItDashboard::class)->name('it-dashboard');
        Route::get('/tickets/{ticket}', TicketDetail::class)->name('ticket-detail');
        Route::get('/manage-instructions', ManageInstructions::class)->name('manage-instructions');
    });

    // Admin only
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin/users', UserManagement::class)->name('admin.users');
    });

    // Manager and Admin
    Route::middleware(['role:manager|admin'])->group(function () {
        Route::get('/admin/directory', DirectoryManagement::class)->name('admin.directory');
        Route::get('/admin/resources', ResourceManagement::class)->name('admin.resources');
    });
});

// Demo Login Routes
Route::get('/login', function () {
    return view('login');
})->name('login');

Route::post('/login', function () {
    $user = User::where('email', request('email'))->first();
    if ($user) {
        auth()->login($user);
        return redirect()->intended('/it-dashboard'); // Redirect to panel if role user
    }
    return back()->withErrors(['email' => 'User not found']);
});

Route::post('/logout', function () {
    auth()->logout();
    return redirect('/');
})->name('logout');
