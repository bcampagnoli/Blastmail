<?php

use App\Models\EmailList;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Pagination\LengthAwarePaginator;

pest()->group('email-list');

test('needs to be authenticated', function () {
    $this->getJson(route('email-list.index'))->assertUnauthorized();

    login();
    $this->get(route('email-list.index'))->assertSuccessful();
});

test('it shoud be paginate', function () {
    //Arrange
    /** @var Authenticatable $user */
    $user = User::factory()->create();
    // cria um usuário
    $this->actingAs($user);
    //Faz o login no sistema
    EmailList::factory()->count(40)->create();

    //Act
    $response = $this->get(route('email-list.index'));

    //Assert 
    $response->assertViewHas('emailLists', function($list) {
        expect($list)->toBeInstanceOf(LengthAwarePaginator::class);
        expect($list)->toHaveCount(5);

        return true;
    });
});

test('it shoud be able to search a list', function () {
    //Arrange
    login();
    EmailList::factory()->count(10)->create();
    EmailList::factory()->create(['title' => 'Title 1']);
    $emailList = EmailList::factory()->create(['title' => 'Title testing 2']);

    //Act
    $response = $this->get(route('email-list.index', ['search' => 'testing 2']));

    //Assert 
    $response->assertViewHas('emailLists', function($list) use ($emailList) {
        expect($list)->toHaveCount(1);
        expect($list->first()->id)->toEqual($emailList->id);

        return true;
    });
});
