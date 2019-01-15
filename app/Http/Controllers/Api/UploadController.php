<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    //
    public function storeImage(Request $request) {
        $rules = [
            'image' => 'required|max:4000|mimes:jpg,jpeg,gif,png'
        ];
        $validator = \Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->apiError($validator->errors()->first());
        } else {
            $path = $request->image->store('images' . date('/Y/m/d'), 'public');
//            $this->uploadToUpyun($request->image->path(), $path);
            return $this->apiSuccess(['url'=>url(Storage::url($path))]);
        }
    }
}
