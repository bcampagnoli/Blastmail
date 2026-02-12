<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CampaignShowRequest extends FormRequest
{
    public function checkWhat()
    {
        if (is_null($this->route('what'))) {
            return to_route('campaigns.show', ['campaign' => $this->route('campaign'), 'what' => 'statistics']);
        }

        return;
    }
    
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        $campaign = $this->route('campaign');
        $what = $this->route('what') ?: 'statistics';
        
        abort_unless(in_array($what, ['open', 'clicked', 'statistics']), 404);

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
        ];
    }
}
