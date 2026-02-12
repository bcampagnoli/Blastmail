<?php

use App\Mail\EmailCampaign;
use App\Models\Campaign;
use App\Models\CampaignMail;
use App\Models\Template;
use App\Models\EmailList;
use App\Models\Subscriber;

test('links on the body should be replace with the tracking link', function(){
    $template = Template::factory()->create([
        'body' => '<div>Hello World<a href="http://www.google.com">Click here</a></div>'
    ]);
    $emailList = EmailList::factory()->has(Subscriber::factory()->count(3))->create();
    $campaign = Campaign::factory()->for($emailList)->create(['body' => $template->body, 'sent_at' => now()->format('Y-m-d')]);
    $subscriber = $emailList->subscribers->first();

    $mail = CampaignMail::query()
        ->create([
            'campaign_id' => $campaign->id,
            'subscriber_id' => $subscriber->id,
            'sent_at' => $campaign->sent_at,
        ]);

    $email = (new EmailCampaign($campaign, $mail));

    $pattern = '/href="([^"]*)google.com"/';
    preg_match_all($pattern, $email, $matches);

    $value = $matches[1][0];
    expect($value)->toBe(
        route('tracking.clicks', ['mail' => $mail, 'f' => 'http://www.'])
    );
});