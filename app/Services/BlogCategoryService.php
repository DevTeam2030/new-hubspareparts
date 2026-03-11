<?php

namespace App\Services;

use Illuminate\Support\Str;
use App\Traits\FileManagerTrait;

class BlogCategoryService
{
    use FileManagerTrait;

    public function getAddData(object|array $request): array
    {
        $storage = config('filesystems.disks.default') ?? 'public';
        return [
            'name' => $request['name'][array_search('en', $request['lang'])],
            'slug' => $this->getCategorySlug(request: $request),
            'status' => 1,
            'click_count' => 0,
            //'image' => $request->has('image') ? $this->upload('blog-category/', 'webp', $request->file('image')) : null,
            //'image_storage_type' => $request->has('image') ? $storage : null,
            //'image_alt_text' => $request['image_alt_text'] ?? null,
        ];
    }

    public function getUpdateData(object|array $request): array
    {
        $storage = config('filesystems.disks.default') ?? 'public';
        $data = [
            'name' => $request->name[array_search('en', $request['lang'])],
            'slug' => $this->getCategorySlug(request: $request),
            //'image_alt_text' => $request['image_alt_text'] ?? null,
        ];

        if ($request->has('image')) {
            $data['image'] = $this->upload('blog-category/', 'webp', $request->file('image'));
            $data['image_storage_type'] = $storage;
        }

        return $data;
    }

    public function deleteImage(object $data): bool
    {
        if ($data['image']) {
            $this->delete('blog-category/' . $data['image']);
        }
        return true;
    }

    public function getCategorySlug(object $request): string
    {
        return Str::slug($request['name'][array_search('en', $request['lang'])], '-') . '-' . Str::random(6);
    }

    public function getCategoryLanguageData(object|array $category): array
    {
        $languages = getWebConfig(name: 'pnc_language') ?? [];
        $categoryLang = [];
        foreach ($languages as $language) {
            $value = '';

            foreach ($category?->translations as $translation) {
                if ($translation->locale === $language) {
                    $value = $translation->value;
                    break;
                }
            }

            // Use category name as fallback if no translation exists
            if (empty($value)) {
                $value = $category->name ?? '';
            }

            $categoryLang[] = [
                'locale' => $language,
                'value' => $value,
            ];
        }
        return $categoryLang;
    }

    public function getCategoryDropdown(object $request, object $categories): string
    {
        $dropdown = '<option value="' . 0 . '" disabled selected>' . translate("Select") . '</option>';
        foreach ($categories as $category) {
            if (getDefaultLanguage() == 'en') {
                $defaultName = $category->name;
            } else {
                $defaultName = $category?->translations()->where('key', 'name')->where('locale', getDefaultLanguage())->first()?->value ?? $category?->name;
            }
            $dropdown .= '<option value="' . $category->id . '">' . $defaultName . '</option>';
        }
        return $dropdown;
    }

}
