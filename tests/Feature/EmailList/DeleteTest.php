<?php

use App\Models\EmailList;
use App\Models\Subscriber;

pest()->group('email-list');

use function Pest\Laravel\{delete, assertSoftDeleted};

it('test it should be able to delete an email list', function() {

    login();
    $emailList = EmailList::factory()->create();
    $subscribers = Subscriber::factory()->count(10)->create(['email_list_id' => $emailList->id]);

    $response = delete(route('email-list.delete', ['emailList' => $emailList]));

    assertSoftDeleted('email_lists', ['id' => $emailList->id]);
    foreach($subscribers as $subscriber){
        assertSoftDeleted('subscribers', ['id' => $subscriber->id]);
    }

});