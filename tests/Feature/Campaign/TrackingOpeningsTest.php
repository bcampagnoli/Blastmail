<?php

use App\Mail\EmailCampaign;
use App\Models\Campaign;
use App\Models\CampaignMail;
use App\Models\Template;
use App\Models\EmailList;
use App\Models\Subscriber;

use function Pest\Laravel\get;
use function PHPUnit\Framework\assertTrue;

beforeEach(function(){
    $template = Template::factory()->create([
        'body' => '<div>Hello World<a href="http://www.google.com">Click here</a></div>'
    ]);
    $emailList = EmailList::factory()->has(Subscriber::factory()->count(3))->create();
    $this->campaign = Campaign::factory()->for($emailList)->create(['body' => $template->body, 'sent_at' => now()->format('Y-m-d')]);
    $subscriber = $emailList->subscribers->first();

    $this->mail = CampaignMail::query()
        ->create([
            'openings' => 0,
            'campaign_id' => $this->campaign->id,
            'subscriber_id' => $subscriber->id,
            'sent_at' => $this->campaign->sent_at,
        ]);
});

it('should increment openings on the database if the campaign is tracking openings', function(){
    $this->campaign->update(['track_open' => true]);
    get(route('tracking.openings', ['mail' => $this->mail]));

    expect($this->mail)->refresh()->openings->toBe(1);
});

it('should not increment openings on the database if the campaign is tracking openings', function(){
    $this->campaign->update(['track_open' => false]);
    get(route('tracking.openings', ['mail' => $this->mail]));

    expect($this->mail)->refresh()->openings->toBe(0);
});

test('check if on the email has the link for the tracking openings', function(){
    $email = (new EmailCampaign($this->campaign, $this->mail))->render();

    assertTrue(str($email)->contains(route('tracking.openings', $this->mail)));
});