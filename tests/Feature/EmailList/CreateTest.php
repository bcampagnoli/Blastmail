<?php

use App\Models\User;
use GuzzleHttp\Psr7\UploadedFile;
use Illuminate\Http\UploadedFile as HttpUploadedFile;

pest()->group('email-list');

beforeEach(function () {
    login();
});

test('title should be required', function () {
    $this->post(route('email-list.create'), [])->assertSessionHasErrors(['title']);
});

test('title should have a max of 255 characters', function () {
    $this->post(route('email-list.create'), ['title' => str_repeat('*', 256)])
            ->assertSessionHasErrors(['title']);
});

test('file should be required', function () {
    $this->post(route('email-list.create'), [])->assertSessionHasErrors(['file']);
});

test('it should be able create an email list', function () {
    $this->withoutExceptionHandling();

    // Arrange - Os dados do teste
    $data = [
        'title' => 'Email List Test',
        'file' => HttpUploadedFile::fake()->createWithContent(
            'sample_names.csv', 
            <<<'CSV'
                Name;Email
                Joe Doe;joe@doe.com
                CSV
        ),
    ];

    //Act - A ação do teste
    $request = $this->post(route('email-list.create'), $data);

    //Assert - a verificação do resultado do request
    $request->assertRedirectToRoute('email-list.index');

    $this->assertDatabaseHas('email_lists', [
        'title' => 'Email List Test',
    ]);

    $this->assertDatabaseHas('subscribers', [
        'email_list_id' => 1,
        'name' => 'Joe Doe',
        'email' => 'joe@doe.com'
    ]);
});
