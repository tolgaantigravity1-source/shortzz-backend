<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\GlobalFunction;
use App\Models\Language;
use App\Models\ShopCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LanguageController extends Controller
{
    public function csv()
    {
        return view('csv');
    }

    public function edit_csv(Request $request)
    {
        $language = Language::where('id', $request->id)->first();
        $itemBaseUrl = GlobalFunction::getItemBaseUrl();

        return view('edit_csv', [
            'language' => $language,
            'itemBaseUrl' => $itemBaseUrl
        ]);
    }

    function languages()
    {
        $languages = Language::all();
        return view('languages', compact('languages'));
    }

    public function addLanguage(Request $request)
    {
        $language = new Language();
        $language->code = $request->code;
        $language->title = $request->title;
        $language->localized_title = $request->localized_title;
        $language->csv_file = GlobalFunction::saveFileAndGivePath($request->csv_file);

        $language->save();

        return response()->json([
            'status' => true,
            'message' => 'Language Added Successfully',
        ]);
    }

    public function updateLanguage(Request $request)
    {

        $language = Language::where('id', $request->language_id)->first();
        if (!$language) {
            return response()->json([
                'status' => false,
                'message' => 'Language Not Found',
            ]);
        }

        $language->code = $request->code;
        $language->title = $request->title;
        $language->localized_title = $request->localized_title;
        if($request->has('csv_file')){
            if($language->csv_file != null){
                GlobalFunction::deleteFile($language->csv_file);
            }
            $language->csv_file = GlobalFunction::saveFileAndGivePath($request->csv_file);
        }
        $language->save();

        return response()->json([
            'status' => true,
            'message' => 'Language Updated Successfully',
            'data' => $language,
        ]);
    }


    public function languageList(Request $request)
    {
        $query = Language::query();
        $totalData = $query->count();

        $columns = 'id';
        $orderDir = 'desc';
        $limit = $request->input('length');
        $start = $request->input('start');
        $orderColumn = $columns;
        $searchValue = $request->input('search.value');

        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('code', 'LIKE', "%{$searchValue}%")
                ->orWhere('title', 'LIKE', "%{$searchValue}%")
                ->orWhere('localized_title', 'LIKE', "%{$searchValue}%");
            });
        }

        $totalFiltered = $query->count();

        $result = $query->orderBy($orderColumn, $orderDir)
                        ->offset($start)
                        ->limit($limit)
                        ->get();

        $data = $result->map(function ($item) {

            $checkStatus = $item->status == 1 ? 'checked' : '';
            $checkDefault = $item->is_default == 1 ? 'checked' : '';

            $status = "<div class='d-flex text-end'>
                            <input type='checkbox' rel='{$item->id}' class='languageEnableDisableSwitch' id='switch-{$item->code}' {$checkStatus} data-switch='primary' />
                            <label for='switch-{$item->code}'></label>
                        </div>";

            $default = "<div class='d-flex text-end'>
                            <input type='checkbox' rel='{$item->id}' class='makeDefaultLanguage' id='dSwitch-{$item->code}' {$checkDefault} data-switch='primary' />
                            <label for='dSwitch-{$item->code}'></label>
                        </div>";

            $csvUrl = GlobalFunction::generateFileUrl($item->csv_file);
            $csv = "<a href='{$csvUrl}' download
                        class='btn border rounded-2 text-dark fs-6'>
                        <i class='me-2 uil-down-arrow'></i>
                        ". __('Download CSV') ."
                        </a>";

            $editCsv = "<a href='./edit_csv/{$item->id}' class='btn border rounded-2 text-dark fs-6'>
            ". __('Edit CSV') ."
            </a>";

            $edit = "<a href='#'
                        rel='{$item->id}'
                        data-title='{$item->title}'
                        data-code='{$item->code}'
                        data-localized_title='{$item->localized_title}'
                        class='action-btn edit d-flex align-items-center justify-content-center btn border rounded-2 text-success ms-1'>
                        <i class='uil-pen'></i>
                        </a>";

            $delete = "<a href='#'
                          rel='{$item->id}'
                          class='action-btn delete d-flex align-items-center justify-content-center btn border rounded-2 text-danger ms-1'>
                            <i class='uil-trash-alt'></i>
                        </a>";

            $action = "<span class='d-flex justify-content-end align-items-center'>{$edit}{$delete}</span>";

            return [
                $item->title,
                $item->code,
                $item->localized_title,
                $status,
                $default,
                $csv,
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

    public function deleteLanguage(Request $request)
    {
        $language = Language::where('id', $request->id)->first();
        if (!$language) {
            return response()->json([
                'status' => false,
                'message' => 'Language Not Found',
            ]);
        }

        // Ensure at least one language remains in the system
        if (Language::count() <= 1) {
            return response()->json([
                'status' => false,
                'message' => 'At least one language must be available.',
            ]);
        }

        if ($language->code == 'en' || $language->is_default == 1) {
            return response()->json([
                'status' => false,
                'message' => 'You can not delete this language.',
            ]);
        }

        $language->delete();

        return response()->json([
            'status' => true,
            'message' => 'Language Deleted Successfully',
        ]);
    }

    public function languageEnableDisable(Request $request)
    {
        $language = Language::where('id', $request->id)->first();

        if (!$language) {
            return response()->json([
                'status' => false,
                'message' => 'Language not found.'
            ]);
        }

        $language->status = $request->value;
        $language->save();

        return response()->json([
            'status' => true,
            'message' => 'Language Status changed successfully!'
        ]);
    }

    public function makeDefaultLanguage(Request $request)
    {
        $languages = Language::get();

        foreach ($languages as $language) {
            $language->is_default = 0;
            $language->save();
        }

        $language = Language::where('id', $request->id)->first();
        $language->status = 1;
        $language->is_default = 1;
        $language->save();

        return response()->json([
            'status' => true,
            'message' => 'Language Status changed successfully!'
        ]);

    }

}
