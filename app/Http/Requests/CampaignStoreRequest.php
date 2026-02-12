<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Template;
use Illuminate\Validation\Rule;

class CampaignStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $tab = $this->route('tab');
        $rules = [];
        
        $map = array_merge([
            'name' => null,
            'subject' => null,
            'email_list_id' => null,
            'template_id' => null,
            'body' => null,
            'track_click' => null,
            'track_open' => null,
            'sent_at' => null,
            'send_when' => 'now',
        ], $this->all());

        // Regras básicas que se aplicam em todas as abas
        // $basicRules = [
        //     'name' => ['required', 'max:255'],
        //     'subject' => ['required', 'max:40'],
        //     'email_list_id' => ['required', 'exists:email_lists,id'],
        //     'template_id' => ['required', 'exists:templates,id'],
        //     'track_click' => ['sometimes', 'boolean'],
        //     'track_open' => ['sometimes', 'boolean'],
        // ];

        if (blank($tab)) {
            // Aba Setup - apenas campos básicos
            // $rules = $basicRules;
            $rules = [
                'name' => ['required', 'max:255'],
                'subject' => ['required', 'max:40'],
                'email_list_id' => ['required', 'exists:email_lists,id'],
                'template_id' => ['required', 'exists:templates,id'],
                // 'track_click' => ['sometimes', 'boolean'],
                // 'track_open' => ['sometimes', 'boolean'],
            ];
        }

        if ($tab == 'template') {
            // Aba Template - campos básicos + body
            // $rules = array_merge($basicRules, [
            //     'body' => ['required']
            // ]);

            $rules = ['body' => ['required']];
        }

        if ($tab == 'schedule') {
            // Aba Schedule - campos básicos + regras de agendamento
            // $rules = $basicRules;
            if ($map['send_when']  == 'now') {
                $map['sent_at'] = now()->format('Y-m-d');
            } else if ($map['send_when'] == 'later') {
                 $rules['sent_at'] = ['required', 'date', 'after:today'];
            } else {
                $rules = ['send_when' => ['required']];
            }

            
            // if (($map['send_when'] ?? null) == 'now') {
            //     $map['sent_at'] = now()->format('Y-m-d H:i:s');
            // } else if (($map['send_when'] ?? null) == 'later') {
            //     $rules['sent_at'] = ['required', 'date', 'after:now'];
            // } else {
            //     $rules['send_when'] = ['required'];
            // }
        }

        // Process session data
        $session = session('campaigns::create', $map);
        foreach ($map as $key => $value) {
            if(!is_null($value)){
                $session[$key] = $value;
            }
        }


        foreach ($map as $key => $value) {
            $newValue = data_get($session, $key);
            if ($key == 'track_click' || $key == 'track_open') {
                $session[$key] = $newValue;
            } else if (filled($newValue)) {
                $session[$key] = $newValue;
            }
        }

        // Preencher body do template se necessário
        if (($templateId = $session['template_id']) && blank($session['body'])) {
            $template = Template::query()->find($templateId);
            $session['body'] = $template?->body;
        }



        // $templateId = $session['template_id'] ?? null;
        // if (($templateId) && blank($session['body'] ?? null)) {
        //     $template = Template::query()->find($templateId);
        //     if ($template) {
        //         $session['body'] = $template?->body;
        //     }
        // }

        session()->put('campaigns::create', $session);

        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'email_list_id' => 'email list',
            'template_id' => 'template',
        ];
    }

    /**
     * Get the validated data from the request.
     */
    public function getData(): array
    {
        $session = session()->get('campaigns::create', []);
        
        // Remover campos temporários
        unset($session['_token']);
        unset($session['send_when']);
        
        // Garantir valores booleanos
        $session['track_click'] = (bool) ($session['track_click'] ?? false);
        $session['track_open'] = (bool) ($session['track_open'] ?? false);
        
        // Garantir que sent_at tenha valor padrão se não definido
        if (empty($session['sent_at']) && ($session['send_when'] ?? null) == 'now') {
            $session['sent_at'] = now()->format('Y-m-d H:i:s');
        }

        return $session;
    }

    /**
     * Determine where to redirect after validation.
     */
    public function getToRoute(): string
    {
        $tab = $this->route('tab');
        
        if (blank($tab)) {
            // Veio da aba Setup, vai para Template
            return route('campaigns.create', ['tab' => 'template']);
        }

        if ($tab === 'template') {
            // Veio da aba Template, vai para Schedule
            return route('campaigns.create', ['tab' => 'schedule']);
        }

        if ($tab === 'schedule') {
            // Veio da aba Schedule, vai para lista de campaigns
            return route('campaigns.index');
        }

        // Fallback
        return route('campaigns.create', ['tab' => 'template']);
    }
}