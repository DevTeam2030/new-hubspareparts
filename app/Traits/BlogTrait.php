<?php

namespace App\Traits;

use App\Models\Translation;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use App\Models\Blog;

trait BlogTrait
{
    public function getBlogReadableId(): int
    {
        try {
            $autoIncrement = DB::table('INFORMATION_SCHEMA.TABLES')
                ->where('TABLE_SCHEMA', DB::getDatabaseName())
                ->where('TABLE_NAME', 'blogs')
                ->value('AUTO_INCREMENT');
            $readableId = 100000 + (int) $autoIncrement;
            while (Blog::where('readable_id', $readableId)->exists()) {
                $readableId++;
            }
            return $readableId;
        } catch (\Throwable $th) {
            $lastReadable = Blog::max('readable_id') ?? 100000;
            return $lastReadable + 1;
        }
    }

    public function getPriorityWiseBlogQuery($query, $dataLimit = 'all', $offset = null, $appends = null)
    {
        $blogPriority = getWebConfig(name: 'blog_list_priority');
        if ($blogPriority && ($blogPriority['custom_sorting_status'] == 1)) {
            $query = $query->get();

            if ($blogPriority['sort_by'] == 'most_clicked') {
                $query = $query->sortByDesc('click_count');
            }  elseif ($blogPriority['sort_by'] == 'a_to_z') {
                $query = $query->sortBy('title', SORT_NATURAL | SORT_FLAG_CASE);
            } elseif ($blogPriority['sort_by'] == 'z_to_a') {
                $query = $query->sortByDesc('title', SORT_NATURAL | SORT_FLAG_CASE);
            }

            if ($dataLimit != 'all') {
                $currentPage = $offset ?? Paginator::resolveCurrentPage('page');
                $totalSize = $query->count();
                $results = $query->forPage($currentPage, $dataLimit);
                return new LengthAwarePaginator($results, $totalSize, $dataLimit, $currentPage, [
                    'path' => Paginator::resolveCurrentPath(),
                    'query' => request()->all(),
                    'appends' => $appends,
                ]);
            }

            return $query;
        }

        if ($dataLimit != 'all') {
            return $query->paginate($dataLimit, ['*'], 'page', request()->get('page', $offset))->appends(request()->all());
        }

        return $query->get();
    }



    public function addBlogTranslation(object $request, int|string $id): bool
    {
        foreach ($request->lang as $index => $key) {
            foreach (['name', 'description', 'title'] as $type) {
                if (isset($request[$type][$index]) && $key != 'en') {
                    Translation::insert([
                        'translationable_type' => 'App\Models\Blog',
                        'translationable_id' => $id,
                        'locale' => $key,
                        'key' => $type,
                        'value' => $request[$type][$index],
                    ]);
                }
            }
        }
        return true;
    }

    public function updateBlogTranslation(object $request, int|string $id): bool
    {
        foreach ($request->lang as $index => $key) {
            foreach (['name', 'description', 'title'] as $type) {
                if (isset($request[$type][$index]) && $key != 'en') {
                    Translation::updateOrInsert(
                        [
                            'translationable_type' => 'App\Models\Blog',
                            'translationable_id' => $id,
                            'locale' => $key,
                            'key' => $type,
                            //'is_draft' => $request['is_draft'] ?? 0,
                        ],
                        [
                            'value' => $request[$type][$index],
                            'translationable_type' => 'App\Models\Blog',
                            'translationable_id' => $id,
                            'locale' => $key,
                            'key' => $type,
                            //'is_draft' => $request['is_draft'] ?? 0,
                        ]
                    );
                }
            }
        }
        return true;
    }

    public function updateTranslationById(string $id, string $lang, string $key, string $value): bool
    {
        Translation::updateOrInsert([
            'translationable_type' => 'App\Models\Blog',
            'translationable_id' => $id,
            'locale' => $lang,
            'key' => $key
        ], [
            'value' => $value,
            //'is_draft' => $request['is_draft'] ?? 0
        ]);
        return true;
    }

    public function deleteTranslation(int|string $id): bool
    {
        Translation::where([
            'translationable_type' => 'App\Models\Blog',
            'translationable_id' => $id
        ])->delete();
        return true;
    }

}
