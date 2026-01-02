<?php

namespace App\Http\Requests\Admin;

use App\Models\Agent;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AgentRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $agentId = $this->route('agent')?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique(Agent::class)->ignore($agentId),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'website' => ['nullable', 'url', 'max:255'],
            'docs_url' => ['nullable', 'url', 'max:255'],
            'github_url' => ['nullable', 'url', 'max:255'],
            'supported_config_types' => ['nullable', 'array'],
            'supported_config_types.*' => ['string'],
            'supported_file_formats' => ['nullable', 'array'],
            'supported_file_formats.*' => ['string'],
            'supports_mcp' => ['boolean'],
            'mcp_transport_types' => ['nullable', 'array'],
            'mcp_transport_types.*' => ['string'],
            'mcp_config_paths' => ['nullable', 'array'],
            'mcp_config_template' => ['nullable', 'array'],
            'skills_config_template' => ['nullable', 'array'],
            'config_type_templates' => ['nullable', 'array'],
            'rules_filename' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'string', 'max:255'],
        ];
    }
}
