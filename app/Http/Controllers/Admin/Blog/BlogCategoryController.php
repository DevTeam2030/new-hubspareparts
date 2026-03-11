<?php

namespace App\Http\Controllers\Admin\Blog;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\BlogCategoryAddRequest;
use App\Http\Requests\Admin\BlogCategoryUpdateRequest;
use App\Models\BlogCategory;
use App\Services\BlogCategoryService;
use App\Traits\BlogCategoryTrait;

class BlogCategoryController extends Controller
{
    use BlogCategoryTrait;

    public function __construct(
        private readonly BlogCategory        $blogCategory,
        private readonly BlogCategoryService $blogCategoryService,
    )
    {
    }

    public function getAddView()
    {
        $languages = getWebConfig(name: 'pnc_language') ?? [];
        $defaultLanguage = $languages[0] ?? 'en';
        return view('admin-views.blog.category.add-new', compact('languages', 'defaultLanguage'));
    }

    public function getList(Request $request)
    {
        $categories = BlogCategory::query()
            ->with('translations')
            ->when($request->get('searchValue'), function ($query) use ($request) {
                $query->where(function($q) use ($request) {
                    $q->where('name', 'LIKE', "%{$request->get('searchValue')}%")
                      ->orWhereHas('translations', function($q) use ($request) {
                          $q->where('value', 'LIKE', "%{$request->get('searchValue')}%");
                      });
                });
            })
            ->orderBy('updated_at', 'desc')
            ->paginate(
                perPage: getWebConfig(name: 'pagination_limit'),
                columns: ['*'],
                pageName: 'page',
                page: $request->get('page')
            )->withQueryString();

        return view('admin-views.blog.category.list', compact('categories'));
    }

    public function getUpdateView($id)
    {
        $category = $this->blogCategory->withoutGlobalScopes()->with('translations')->findOrFail($id);
        $languages = getWebConfig(name: 'pnc_language') ?? [];
        $defaultLanguage = $languages[0] ?? 'en';
        $categoryLang = $this->blogCategoryService->getCategoryLanguageData(category: $category);

        return view('admin-views.blog.category.edit', compact('category', 'languages', 'defaultLanguage', 'categoryLang'));
    }

    public function add(BlogCategoryAddRequest $request)
    {
        $blogCategory = $this->blogCategory->create($this->blogCategoryService->getAddData(request: $request));
        $this->addBlogCategoryTranslation(request: $request, id: $blogCategory->id);

//        $result = $this->renderBlogCategoryList();
//        return response()->json([
//            'message' => translate('category_added_successfully'),
//            'html' => $result['html'],
//            'count' => $result['count'],
//        ], 200);

        return redirect()->route('admin.blog.category.list');
    }

    public function update(BlogCategoryUpdateRequest $request)
    {
        $categoryId = $request->validated()['id'] ?? $request->id;



        $this->blogCategory->where('id', $categoryId)
            ->update($this->blogCategoryService->getUpdateData(request: $request));
        $this->updateBlogCategoryTranslation(request: $request, id: $categoryId);
        $result = $this->renderBlogCategoryList();

        return redirect()->route('admin.blog.category.list');

//        return response()->json([
//            'message' => translate('Category_updated_successfully.'),
//            'html' => $result['html'],
//            'count' => $result['count'],
//        ]);
    }

    public function getCategoryInfo(Request $request): JsonResponse
    {
        $category = $this->blogCategory->withoutGlobalScopes()->with('translations')->findOrFail($request['id']);
        $categoryLang = $this->blogCategoryService->getCategoryLanguageData(category: $category);

        return response()->json([
            'data' => $category,
            'lang_data' => $categoryLang,
        ]);
    }

    public function updateStatus(Request $request): JsonResponse
    {
        $this->blogCategory->where('id', $request['category_id'])->update(['status' => ($request['status'] ?? 0)]);
        $result = $this->renderBlogCategoryList();

        return response()->json([
            'message' => translate('Status_updated_successfully.'),
            'html' => $result['html'],
            'count' => $result['count'],
        ]);
    }

    public function deleteCategory(Request $request): JsonResponse
    {
        $category = $this->blogCategory->where('id', $request['category_id'])->first();
        if ($category) {
            $this->blogCategoryService->deleteImage(data: $category);
            $this->blogCategory->where('id', $request['category_id'])->delete();
        }
        $result = $this->renderBlogCategoryList();

        return response()->json([
            'message' => translate('Category_deleted_successfully.'),
            'html' => $result['html'],
            'count' => $result['count'],
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        $result = $this->renderBlogCategoryList($request['searchValue'], $request->get('page'));
        return response()->json([
            'html' => $result['html'],
            'count' => $result['count'],
        ]);
    }

    private function renderBlogCategoryList($search = null, $page = null): array
    {
        $categories = BlogCategory::query()
            ->with('translations')
            ->when($search, function ($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhereHas('translations', function($q) use ($search) {
                          $q->where('value', 'LIKE', "%{$search}%");
                      });
                });
            })
            ->orderBy('updated_at', 'desc')
            ->paginate(
                perPage: getWebConfig(name: 'pagination_limit'),
                columns: ['*'],
                pageName: 'page',
                page: $page
            )->withQueryString();

        $html = view('admin-views.blog.category.partials.table-rows', compact('categories'))->render();
        return [
            'html' => $html,
            'count' => $categories->total(),
        ];

    }

    public function getDropdown(Request $request): JsonResponse
    {
        try {
            $categories = $this->blogCategory->withoutGlobalScopes()->with('translations')->get();
            $dropdown = $this->blogCategoryService->getCategoryDropdown(request: $request, categories: $categories);
            return response()->json([
                'select_tag' => $dropdown,
                'debug' => [
                    'categories_count' => $categories->count(),
                    'categories' => $categories->toArray(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
}
