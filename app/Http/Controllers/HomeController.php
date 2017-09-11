<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Helpers\ValidationHelper;
use App\Result;
use File;
use Hamcrest\Description;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    protected static $perPage = 20;

    public function index()
    {
        $results['header'] = ['Id', 'Title', 'Description', 'Image', 'Posted Date', 'Edit', 'Delete'];
        $results['data'] = Result::paginate(self::$perPage);
        return response()
            -> view('welcome', ['response' => $results]);
    }

    public function editPost(Request $request)
    {
        // Fields Validation
        $validation = ValidationHelper::editPostValidations($request->all());
        if ($validation['error']) {
            return response(
                [
                    'error' => true,
                    'response' => $validation['response']
                ], 404
            );
        }

        try {
            $updateArr = [
                'title' => $request->itemTitle,
                'description' => $request->itemDescription,
                'date_upload' => $request->itemDate
            ];
            Result::where('id', $request->itemId)
                ->update($updateArr);

        } catch (\Exception $e) {
            return response(
                [
                    'error' => true,
                    'response' => $e->getMessage()
                ], 404
            );
        }
        return response(
            [
                'error' => false,
                'response' => 'Updated Successful'
            ]
        );
    }

    public function deletePost(Request $request)
    {
        // Fields Validation
        $validation = ValidationHelper::deletePostValidations($request->all());
        if ($validation['error']) {
            return response(
                [
                    'error' => true,
                    'response' => $validation['response']
                ], 404
            );
        }

        try {
            $post = Result::findOrFail($request->itemId);
            $imgSrc =  $post->main_image;
            $post->delete();
            $imgSrcArr = explode(DIRECTORY_SEPARATOR, $imgSrc);
            unset($imgSrcArr[count($imgSrcArr)-1]);
            $imgSrc = implode(DIRECTORY_SEPARATOR, $imgSrcArr);
            $dirs = [public_path(), 'img', 'scrapper_images', $imgSrc];
            File::deleteDirectory(implode(DIRECTORY_SEPARATOR, $dirs));
        } catch (\Exception $e) {
            return response(
                [
                    'error' => true,
                    'response' => $e->getMessage()
                ], 404
            );
        }
        return response(
            [
                'error' => false,
                'response' => 'Deleted Successful'
            ]
        );
    }
}
