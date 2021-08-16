<?php

namespace App\Containers\UserSection\UserContainer\UI\API\Controllers;

use App\Containers\UserSection\UserContainer\Models\AdminBlogs;
use App\Containers\UserSection\UserContainer\Models\adminModel;
use App\Containers\UserSection\UserContainer\Models\UserContainer;
use App\Ship\Parents\Controllers\ApiController;
use DB;
use Exception;
use Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;

class UserBlogController extends ApiController
{
    public function adminRegister(Request $request)
    {
        $req = Validator::make($request->all(), [
            'firstName' => 'required|string|between:2,100',
            'lastName' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:container_admin'
        ]);

        $req2 = Validator::make($request->all(), [
            'password' => 'required|required_with:password_confirmation|min:3',
            'password_confirmation' => 'required|same:password'
        ]);

        $user = new adminModel();
        $user->firstName = $request->input('firstName');
        $user->lastName = $request->input('lastName');
        $user->email = $request->input('email');
        $user->password = bcrypt($request->input('password'));

        $userEmail = adminModel::where('email', $user->email)->first();
        if ($userEmail) {
            return response()->json(['status' => 409, 'message' => "This email already exists...."]);
        }

        if ($req->fails()) {
            return response()->json(['status' => 403, 'message' => "Please enter all details"]);
        }

        if ($req2->fails()) {
            return response()->json(['status' => 403, 'message' => "Password doesn't match"]);
        }
        $user->save();
        return response()->json([
            'status' => 201,
            'message' => 'Admin succesfully registered!'
        ]);
    }

    public function adminLogin(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|
            min:5',
        ]);

        $email = $request->get('email');
        $password = $request->get('password');
        
        $user = adminModel::where('email', $email)
            ->first();  

        $adminPassword = adminModel::where('email', $email)->value('password');

        if (!$user) {
            return response()->json(['status' => 400, 'message' => "Invalid credentials! email doesn't exists"]);
        }

        if(!Hash::check($password, $adminPassword)){
            return response()->json([ 'message' => "Please check the password"]);
        }

        if ($validation->fails()) {
            return response()->json(['status' => 403, 'message' => "please enter the valid details"]);
        }

        $token = JWTAuth::fromUser($user);
        if (!$token) {
            return response()->json(['status' => 401, 'message' => 'Unauthenticated']);
        }
        return response()->json(['status' => 201, 'message' => 'successfully logged in', 'token' => $token]);
    }

    public function getId(){
        $token = JWTAuth::getToken();
        $details = JWTAuth::getPayload($token)->toArray();
        $id = $details["sub"];
        return $id;
    }


    public function createBlog(Request $request){
        $user = new AdminBlogs();
        try{
        $user->admin_id = $this->getId();
        }catch(Exception $e){
            return response()->json(['status' => 401, 'message' => 'invalid credentials']);
        }
        if(!$user->admin_id){
            return response()->json(['status' => 401, 'message' => 'Unauthorized']);
        }
        $user->hotelName = $request->input('hotelName');
        $user->name = $request->input('name');
        $user->price = $request->input('price');
        $user->place = $request->input('place');
        $user->type = $request->input('type');
        $user->rating = $request->input('rating');
        $user->save();
        return response()->json(['status' => 201, 'message' => 'Blog created successfully']);
    }

    public function getAdminBlogs(){
        $adminObj = new AdminBlogs();
        $token = JWTAuth::getToken();
        $id = JWTAuth::getPayload($token)->toArray();
        $adminObj->admin_id = $id["sub"];

        return AdminBlogs::where('admin_id', $adminObj->admin_id)->get();
    }


    public function getBlogs(){
        try{
        return DB::table('container_blogs')->select('id','name','price','place', 'type', 'rating')->get();
        }catch(Exception $e){
            return response()->json(['message' => 'no blogs present at the moment']);
        }
    }

    public function updateBlog(Request $request){
        $adminObj = new AdminBlogs();
        $adminObj->id = $request->input('id');

        $token = JWTAuth::getToken();
        $id = JWTAuth::getPayload($token)->toArray();
        $admin_id = $id["sub"];

        $adminPresent = adminModel::where('id', $admin_id)->value('id');
        if(!$adminPresent){
            return response()->json(['status' => 409, 'message' => 'No admin is present']);
        }
        $adminObj->admin_id = $adminPresent;
        $adminObj->hotelName = $request->input('hotelName');
        $adminObj->name = $request->input('name');
        $adminObj->price = $request->input('price');
        $adminObj->place = $request->input('place');
        $adminObj->type = $request->input('type');
        $adminObj->rating = $request->input('rating');
  
       AdminBlogs::where('id', $adminObj->id)
                    ->update([
                        'hotelName' =>  $adminObj->hotelName,
                        'name' =>   $adminObj->name,
                        'price' => $adminObj->price ,
                        'place' => $adminObj->place,
                        'type' =>  $adminObj->type,
                        'rating' =>   $adminObj->rating
                    ]);
        return response()->json(['status' => 201, 'message' => 'Blog successfully updated']);
    }


    public function deleteBlog(Request $request){
        try{
        $adminObj = new AdminBlogs();
        $token = JWTAuth::getToken();
        $id = JWTAuth::getPayload($token)->toArray();
        $admin_id = $id["sub"];
        }
        catch(Exception $e){
            return response()->json(['status' => 404, 'message' => 'Invalid credentials']);
        }
        $adminObj->id = $request->input('id');

        $adminPresent = adminModel::where('id', $admin_id)->first();
        if(!$adminPresent){
            return response()->json(['status' => 409, 'message' => 'No admin is present']);
        }
        $blogId = AdminBlogs::where('id', $adminObj->id)->value('id');
        if($blogId == $adminObj->id){
            AdminBlogs::where('id', $adminObj->id)->delete();
            return response()->json(['status' => 201, 'message' => 'Blog deleted successfully']);          
        }
        return response()->json(['status' => 409, 'message' => 'No blog is available with the given Id']);
    }

    public function userRegister(Request $request)
    {
        $req = Validator::make($request->all(), [
            'firstName' => 'required|string|between:2,100',
            'lastName' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:user_containers'
        ]);

        $req2 = Validator::make($request->all(), [
            'password' => 'required|required_with:password_confirmation|min:3',
            'password_confirmation' => 'required|same:password'
        ]);

        $user = new UserContainer();
        $user->firstName = $request->input('firstName');
        $user->lastName = $request->input('lastName');
        $user->email = $request->input('email');
        $user->password = bcrypt($request->input('password'));

        $userEmail = UserContainer::where('email', $user->email)->first();
        if ($userEmail) {
            return response()->json(['status' => 409, 'message' => "This email already exists...."]);
        }

        if ($req->fails()) {
            return response()->json(['status' => 403, 'message' => "Please enter all details"]);
        }

        if ($req2->fails()) {
            return response()->json(['status' => 403, 'message' => "Password doesn't match"]);
        }
        $user->save();
        return response()->json([
            'status' => 201,
            'message' => 'user succesfully registered!'
        ]);
    }

    public function userLogin(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|
            min:5',
        ]);

        $email = $request->get('email');
        $password = $request->get('password');
        
        $user = UserContainer::where('email', $email)
            ->first();  

        $userPassword = UserContainer::where('email', $email)->value('password');

        if (!$user) {
            return response()->json(['status' => 400, 'message' => "Invalid credentials! email doesn't exists"]);
        }

        if(!Hash::check($password, $userPassword)){
            return response()->json([ 'message' => "Please check the password"]);
        }

        if ($validation->fails()) {
            return response()->json(['status' => 403, 'message' => "please enter the valid details"]);
        }

        $token = JWTAuth::fromUser($user);
        if (!$token) {
            return response()->json(['status' => 401, 'message' => 'Unauthenticated']);
        }
        return response()->json(['status' => 201, 'message' => 'successfully logged in', 'token' => $token]);
    }

    public function quantityIncrement(Request $request)
    {
        $id = $request->input('id');
        $idPresentInDb = AdminBlogs::where('id', $id)->value('id');

        $adminObj = new UserContainer();
        $token = JWTAuth::getToken();
        $id = JWTAuth::getPayload($token)->toArray();
        $user_id = $id["sub"];
    }

}
