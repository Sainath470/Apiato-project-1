<?php

/**
 * @apiGroup           UserContainer
 * @apiName            userRegister
 *
 * @api                {POST} /v1/register Endpoint title here..
 * @apiDescription     Endpoint description here..
 *
 * @apiVersion         1.0.0
 * @apiPermission      none
 *
 * @apiParam           {String}  parameters here..
 *
 * @apiSuccessExample  {json}  Success-Response:
 * HTTP/1.1 200 OK
{
  // Insert the response of the request here...
}
 */

use App\Containers\UserSection\UserContainer\UI\API\Controllers\UserBlogController;
use Illuminate\Support\Facades\Route;

Route::post('adminregister', [UserBlogController::class, 'adminRegister']);
Route::post('adminlogin', [UserBlogController::class, 'adminLogin']);
Route::post('createblog', [UserBlogController::class, 'createBlog']);
Route::get('getallblogs', [UserBlogController::class, 'getBlogs']);
Route::get('getadminblogs', [UserBlogController::class, 'getAdminBlogs']);
Route::post('updateblog', [UserBlogController::class, 'updateBlog']);
Route::post('deleteblog', [UserBlogController::class, 'deleteBlog']);

Route::post('userregister', [UserBlogController::class, 'userRegister']);
Route::post('userlogin', [UserBlogController::class, 'userLogin']);



