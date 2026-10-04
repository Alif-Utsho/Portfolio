<?php

namespace App\Http\Requests;

use App\Models\MediaAsset;
use App\Models\PortfolioItem;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePortfolioItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->is_admin ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $item = $this->route('item');

        return [
            'type' => ['required', Rule::in(PortfolioItem::TYPES)],
            'title' => ['required', 'string', 'max:180'],
            'slug' => ['nullable', 'alpha_dash', 'max:180', Rule::unique('portfolio_items', 'slug')->ignore($item)],
            'eyebrow' => ['nullable', 'string', 'max:120'],
            'subtitle' => ['nullable', 'string', 'max:180'],
            'summary' => ['nullable', 'string', 'max:1000'],
            'body' => ['nullable', 'string', 'max:30000'],
            'url' => ['nullable', 'string', 'max:2048', 'regex:/^(https:\/\/|\/|#)[^\\s]*$/'],
            'secondary_url' => ['nullable', 'url', 'max:2048'],
            'technologies' => ['nullable', 'string', 'max:2000'],
            'organization' => ['nullable', 'string', 'max:180'],
            'period' => ['nullable', 'string', 'max:120'],
            'group' => ['nullable', 'string', 'max:120'],
            'challenge' => ['nullable', 'string', 'max:3000'],
            'features' => ['nullable', 'string', 'max:5000'],
            'category' => ['nullable', 'string', 'max:64'],
            'image_path' => ['nullable', Rule::in(MediaAsset::query()->pluck('path')->all())],
            'is_published' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:100000'],
        ];
    }
}
