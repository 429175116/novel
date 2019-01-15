<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Author;
use App\Service\UserService;

class AuthorController extends Controller
{
    //
    public function applicateToBeAnAnthor(Request $request) {
        $return_data = UserService::getUser($request->header('AuthrizeOpenId'));
        if($return_data['errcode'] > 0) { return $this->apiError($return_data['errmsg']); } else {$user = $return_data['data']; $input = $request->all(); }
        if(!empty($errors = $this->myValidator($request, Author::$rules, Author::$message))) return $errors;//验证
        $author = $this->_setAuthorForApplicateToBeAnAnthor(new Author(), $input, $user);
        $author->save();
        $data = [
            'author' => $author
        ];
        return $this->apiSuccess($data);
    }

    private function _setAuthorForApplicateToBeAnAnthor($author, $input, $user) {
        $author->user_id = $user->id;
        $author->real_name = $input['real_name'];
        $author->pen_name = $input['pen_name'];
        $author->profile = $input['profile'];
        $author->account = $input['account'];
        $author->password = bcrypt($input['password']);
        if(!empty($input['id_number'])) $author->id_number = $input['id_number'];
        if(!empty($input['bank_number'])) $author->bank_number = $input['bank_number'];
        if(!empty($input['phone_number'])) $author->phone_number = $input['phone_number'];
        return $author;
    }
}
