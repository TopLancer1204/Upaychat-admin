<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogTabs;
use Illuminate\Support\Facades\Auth;

class BlogController extends Controller
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function getBlogs(Request $requesaddbankt)
    {
        // $user = Auth::user();
        // $userid = $user->id;

        $blogList = Blog::select('id', 'blog_title', 'blog_description', 'blog_type', 'blog_tags', 'blog_content', 'blog_image', 'blog_author', 'blog_slug', 'blog_status', 'blog_categoryId', 'updated_at')->get();
        // return $joblist;
        $blist = array();

        if (count($blogList) < 1)
            $response['message'] = 'You have not added any blog details';
        else {
            foreach ($blogList as $blog) {
                $image = $blog->blog_image;
                $image = env('APP_URL', 'http://localhost:8000') . '/' . $image;
                // $url = env('APP_URL', 'http://localhost/')+$image;
                $date = $blog->updated_at;
                $MMM = date('M d, Y', strtotime($date));
                array_push($blist, array(
                    'id' => $blog->id,
                    'blog_title' => $blog->blog_title,
                    'blog_description' => $blog->blog_description,
                    'blog_tags' => $blog->blog_tags,
                    'blog_type' => $blog->blog_type,
                    'blog_content' => $blog->blog_content,
                    'blog_image' => $image,
                    'blog_author' => $blog->blog_author,
                    'blog_slug' => $blog->blog_slug,
                    'blog_categoryId' => $blog->blog_categoryId,
                    'blog_status' => $blog->blog_status,
                    'blog_date' => $MMM,
                ));
            }
            $response['message'] = 'Success';
        }
        $response['data'] = $blist;
        $response['status'] = "true";
        return response()->json($response);
    }

    public function getBlog(Request $request)
    {
        // $user = Auth::user();

        try {
            $ban = Blog::where("id", $request->blog_id)->first();

            $bank = array(
                'id' => $ban->id,
                'blog_title' => $ban->blog_title,
                'blog_content' => $ban->blog_content,
            );

            $response['status'] = "true";
            $response['message'] = 'Success';
            $response['data'] = $bank;
            return response()->json($response);
        } catch (\Exception $e) {
            $response['status'] = "false";
            $response['message'] = $e->getMessage();
            $response['data'] = '';
            return response()->json($response);
        }
    }
}
