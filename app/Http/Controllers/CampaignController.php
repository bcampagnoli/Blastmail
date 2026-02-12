<?php

namespace App\Http\Controllers;

use App\Http\Requests\CampaignShowRequest;
use App\Http\Requests\CampaignStoreRequest;
use App\Jobs\SendEmailsCampaignJob;
use App\Mail\EmailCampaign;
use App\Models\Campaign;
use App\Models\CampaignMail;
use App\Models\Template;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Models\EmailList;
use Illuminate\Database\Eloquent\Attributes\UseEloquentBuilder;
use Illuminate\Support\Facades\Mail;

class CampaignController extends Controller
{
    public function index()
    {
        $search = request()->get('search', null);
        $withTrashed = request()->get('withTrashed', false);
        
        $campaigns = Campaign::query()
            ->when($withTrashed, fn ($query) => $query->withTrashed())
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    // Se for numérico, busca também por id exato
                    if (is_numeric($search)) {
                        $q->where('id', $search);
                    }

                    // Busca por nome (independente de ser número ou texto)
                    $q->orWhere('name', 'like', "%{$search}%");
                });
            })
            ->paginate(5)
            ->appends(compact('search', 'withTrashed'));

        return view('campaigns.index', [
            'campaigns' => $campaigns,
            'search' => $search,
            'withTrashed' => $withTrashed,
        ]);
    }

    public function showStatistics(Campaign $campaign)
    {
        return view('campaigns.show.statistics');
    }

    public function showOpen(Campaign $campaign)
    {
        return view('campaigns.show.open');
    }

    public function showClicked(Campaign $campaign)
    {
        return view('campaigns.show.clicked');
    }

    public function show(CampaignShowRequest $request, Campaign $campaign, ?string $what = null)
    {
        if($redirect = $request->checkWhat()){
            return $redirect;
        }

        $search = request()->search;

        $query = $campaign ->mails()
            ->when($what == 'statistics', fn(Builder $query) => $query->statistics())
            ->when($what == 'open', fn(Builder $query) => $query->openings($search))
            ->when($what == 'click', fn(Builder $query) => $query->clicked($search))
            ->simplePaginate(5)->withQueryString();
            if ($what == 'statistics') {
                $query = $query->first()->toArray();
            }

        return view('campaigns.show', compact('campaign', 'what', 'search', 'query'));
    }

    public function create(?string $tab = null)
    {
        // Valores padrão + dados da sessão
        $data = array_merge([
            'name' => null,
            'subject' => null,
            'email_list_id' => null,
            'template_id' => null,
            'body' => null,
            'track_click' => null,
            'track_open' => null,
            'sent_at' => null,
            'send_when' => 'now',
        ], session('campaigns::create', []));

        $extra = [];

        // Aba inicial (configuração)
        if (blank($tab)) {
            $extra['emailLists'] = EmailList::select(['id', 'title'])
                ->orderBy('title')
                ->get();

            $extra['templates'] = Template::select(['id', 'name'])
                ->orderBy('name')
                ->get();
        }

        // Aba de agendamento
        if ($tab === 'schedule') {
            $extra['countEmails'] = !empty($data['email_list_id'])
                ? EmailList::find($data['email_list_id'])?->subscribers()->count()
                : 0;

            $extra['template'] = !empty($data['template_id'])
                ? Template::find($data['template_id'])?->name
                : null;
        }

        return view('campaigns.create', array_merge($extra, [
            'tab' => $tab,
            'form' => match ($tab) {
                'template' => '_template',
                'schedule' => '_schedule',
                default => '_config',
            },
            'data' => $data,
        ]));
    }

    public function store(CampaignStoreRequest $request, ?string $tab = null)
    {
        $data = $request->getData();

        // 🔧 Remover qualquer chave 'sent_at' indevida
        unset($data['sent_at']);

        session([
            'campaigns::create' => array_merge(
                session('campaigns::create', []),
                [
                    'name' => $data['name'] ?? null,
                    'subject' => $data['subject'] ?? null,
                    'email_list_id' => $data['email_list_id'] ?? null,
                    'template_id' => $data['template_id'] ?? null,
                    'body' => Template::find($data['template_id'])?->body,
                    'track_click' => $request->boolean('track_click'),
                    'track_open' => $request->boolean('track_open'),
                    'sent_at' => null,
                    'send_when' => $data['send_when'] ?? null,
                ]
            ),
        ]);

        if ($tab === 'schedule') {
            $campaign = Campaign::create($data);
            SendEmailsCampaignJob::dispatchAfterResponse($campaign);
        }

        return redirect($request->getToRoute());
    }

    public function destroy(Campaign $campaign)
    {
        $campaign->delete();

        return back()->with('message', __('Campaign successfully deleted!'));
    }

    public function restore(Campaign $campaign)
    {
        
        $campaign->restore();

        return back()->with('message', __('Campaign successfully restored!'));
    }
}
