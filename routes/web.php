<?php

use App\Http\Controllers\CampaignController;
use App\Http\Controllers\EmailListController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubscribersController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\TrackingController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CampaignCreateSessionControl;




Route::get('/t/{mail}/o', [TrackingController::class, 'openings'])->name('tracking.openings');
Route::get('/t/{mail}/c', [TrackingController::class, 'clicks'])->name('tracking.clicks');

Route::middleware(['auth', 'verified'])->group(function () {
    //region Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    //endregion

    //region EmailList
    Route::get('/email-list', [EmailListController::class, 'index'])->name('email-list.index');
    Route::get('/email-list/create', [EmailListController::class, 'create'])->name('email-list.create');
    Route::post('/email-list/create', [EmailListController::class, 'store']);
    Route::get('/email-list/{emailList}/subscribers', [SubscribersController::class, 'index'])->name('subscribers.index');
    Route::get('/email-list/{emailList}/subscribers/create', [SubscribersController::class, 'create'])->name('subscribers.create');
    Route::post('/email-list/{emailList}/subscribers/create', [SubscribersController::class, 'store']);
    Route::delete('/email-list/{emailList}/subscribers/{subscriber}', [SubscribersController::class, 'destroy'])->name('subscribers.destroy');
    Route::delete('/email-list/{emailList}', [EmailListController::class, 'destroy'])->name('email-list.delete');
    //endregion

    Route::resource('templates', TemplateController::class);

    //region Campaigns
    //Como vamos usar todas as funções do controler, podemos criar uma rota que chama todo mundo
    Route::get('/', [CampaignController::class, 'index'])->name('campaigns.index');
    // Route::get('/campaigns/{campaign}/statistics', [CampaignController::class, 'showStatistics'])->name('campaigns.show.statistics');
    // Route::get('/campaigns/{campaign}/open', [CampaignController::class, 'showOpen'])->name('campaigns.show.open');
    // Route::get('/campaigns/{campaign}/clicked', [CampaignController::class, 'showClicked'])->name('campaigns.show.clicked');

    


    
    Route::get('/campaigns/create/{tab?}', [CampaignController::class, 'create'])->middleware([CampaignCreateSessionControl::class])->name('campaigns.create');
    Route::post('/campaigns/create/{tab?}', [CampaignController::class, 'store']);
    Route::patch('/campaigns/{campaign}/restore', [CampaignController::class, 'restore'])->withTrashed()->name('campaigns.restore');
    Route::delete('campaigns/{campaign}', [CampaignController::class, 'destroy'])->name('campaigns.destroy');
    Route::get('/campaigns/{campaign}/{what?}', [CampaignController::class, 'show'])->name('campaigns.show')->withTrashed();
    //endregion
});

require __DIR__.'/auth.php';
