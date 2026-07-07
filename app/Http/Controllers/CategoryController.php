<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Constants;
use App\Models\GlobalFunction;
use App\Models\Language;
use App\Models\MusicCategories;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class CategoryController extends Controller
{


    public function categoryList(Request $request)
    {
        $query = Category::where('is_deleted', Constants::isDeletedNo);
        $totalData = $query->count();

        $columns = ['id'];
        $limit = $request->input('length');
        $start = $request->input('start');
        $orderColumn = $columns[$request->input('order.0.column')];
        $orderDir = $request->input('order.0.dir');
        $searchValue = $request->input('search.value');

        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('title', 'LIKE', "%{$searchValue}%");
            });
        }
        $totalFiltered = $query->count();

        $result = $query->orderBy($orderColumn, $orderDir)
                        ->offset($start)
                        ->limit($limit)
                        ->get();

        $data = $result->map(function ($item) {

            $imageUrl = $item->image;
            if (!$imageUrl) {
                $imageUrl = env('APP_URL') . 'assets/img/placeholder.png';
            }

            $title = "<a href='./$item->id' class='table-user'>
                        <img src='{$imageUrl}' alt='table-user' class='me-2 rounded object-fit-contain bg-primary-lighten bg-info-lighten rounded'>
                        <span class='text-body fw-semibold'>{$item->title}</span>
                    </a>";

            $viewSubcategory = "<a href='./$item->id'
                        rel='{$item->id}'
                        data-key='{$item->title}'
                        class='d-flex align-items-center justify-content-center btn border rounded-2 text-dark fs-6'>
                        ". __('viewSubCategories')."
                        </a>";

            $edit = "<a href='#'
                        rel='{$item->id}'
                        data-key='{$item->title}'
                        data-image='{$item->image}'
                        class='action-btn edit d-flex align-items-center justify-content-center btn border rounded-2 text-success ms-1'>
                        <i class='uil-pen'></i>
                        </a>";

            $delete = "<a href='#'
                          rel='{$item->id}'
                          class='action-btn delete d-flex align-items-center justify-content-center btn border rounded-2 text-danger ms-1'>
                            <i class='uil-trash-alt'></i>
                        </a>";
            $action = "<span class='d-flex justify-content-end align-items-center'>{$viewSubcategory}{$edit}{$delete}</span>";

            return [
                $title,
                $action
            ];
        });

        $json_data = [
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data,
        ];

        return response()->json($json_data);
    }

    public function updateCategory(Request $request)
    {

        $category = Category::where('id', $request->category_id)->first();
        if (!$category) {
            return response()->json([
                'status' => false,
                'message' => 'Category Not Found',
            ]);
        }

        $oldTitle = $category->title;

        if ($request->hasFile('image')) {
            GlobalFunction::deleteFile($category->image);
            $files = $request->file('image');
            $path = GlobalFunction::saveFileAndGivePath($files);
            $category->image = $path;
        }


        $category->title = $request->key;
        $category->save();

        $languages = Language::all();

        foreach ($languages as $language) {
            $strings = $language->strings;

            if (isset($strings[$oldTitle])) {
                $strings[$request->key] = $strings[$oldTitle];
                unset($strings[$oldTitle]);
            }

            $language->strings = $strings;
            $language->save();
        }

        return response()->json([
            'status' => true,
            'message' => 'Category Updated Successfully',
            'data' => $category,
        ]);
    }

    public function deleteCategory(Request $request)
    {
        $category = Category::where('id', $request->category_id)->first();
        if (!$category) {
            return response()->json([
                'status' => false,
                'message' => 'Category Not Found',
            ]);
        }

        GlobalFunction::deleteFile($category->image);
        $category->image = null;
        $category->is_deleted = Constants::isDeletedYes;
        $category->save();

        // $category->delete();

        return response()->json([
            'status' => true,
            'message' => 'Category Deleted Successfully',
        ]);
    }


    // Sub Category

    function viewSubCategory($id)
    {
        $category = Category::where('id', $id)->first();
        return view('sub-category.index', compact('category'));
    }

    function addSubCategory($id)
    {
        $category = Category::where('id', $id)->first();
        $languages = Language::all();
        return view('sub-category.add', compact('languages', 'category'));
    }

}
