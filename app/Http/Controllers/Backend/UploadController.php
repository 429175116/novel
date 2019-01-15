<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    //
    public function storeImage(Request $request) {
//        dd(123);
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

    public function storeTxt(Request $request) {
        $file1 = $request->file('myfile');
        if(empty($file1)) return $this->apiError('请上传文件');
        $realPath = $file1->getRealPath();
        $ext = $file1->getClientOriginalExtension();
//        dd($realPath);
        $str = file_get_contents($realPath);//将整个文件内容读入到一个字符串中
        dd($str);

        $str_encoding = mb_convert_encoding($str, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5');//转换字符集（编码）
//        $arr = explode("\r\n", $str_encoding);//转换成数组
        dd($str_encoding);

//        if($ext != 'txt') return $this->apiError('只支持txt文件上传');
//        $file = fopen($realPath,'r');
//        $content = array();
//        if(!$file){
//            return $this->apiError('文件打开失败');
//        }else{
//            $i = 0;
//            while (!feof($file)){
//                $content[$i] = mb_convert_encoding(fgets($file),"UTF-8","GBK,ASCII,ANSI,UTF-8");
////                dd(fgets($file));
//                $i++ ;
//            }
//            fclose($file);
//            $content = array_filter($content); //数组去空
//        }
//        $arr = [];
//        $str = "";
//
//        dd($content);
//
//
//        foreach ($content as $key => $value) {
//            $tmp1 = explode('|', $value);
//            if(!empty($tmp1[1])){
//                $txt = $tmp1[0];
////                fwrite($myfile, $txt);
//                $str.= $tmp1[0]."\n";
//            }
//        }
//
//        $filename = '文档名称.txt';
//        header("Content-type: text/plain");
//        header("Accept-Ranges: bytes");
//        header("Content-Disposition: attachment; filename=".$filename);
//        header("Cache-Control: must-revalidate, post-check=0,pre-check=0" );
//        header("Pragma: no-cache" );
//        header("Expires: 0" );
//        exit($str);
    }
}
