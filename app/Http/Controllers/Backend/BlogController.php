<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class BlogController extends Controller
{
    public function getBlogs()
    {
        $blogs = Blog::orderBy('id','desc')->get();
        return view('backend.blog.blogs')->with('blogs', $blogs);
    }

    public function getBlogAdd()
    {
        $categories = Category::all();
        return view('backend.blog.blog-add')->with('categories', $categories);
    }

    public function getBlogEdit($blogId)
    {
        $categories = Category::all();
        $blog = Blog::where('id',$blogId)->first();
        return view('backend.blog.blog-edit')->with('categories', $categories)->with('blog',$blog);
    }


    public function postBlogs(Request $request)
    {
        if (isset($request->delete)) {
            try {
                $blogs =  Blog::where('id',$request->id)->first();
                File::delete(public_path($blogs->blog_image));
                Blog::where('id', $request->id)->delete();
                return response(['status' => 'success', 'title' => 'Success', 'content' => 'Blog Deleted successfully']);
            } catch (\Exception $e) {
                return response(['status' => 'error', 'title' => 'Failed', 'content' => 'Blog Deletion failed']);
            }
        } elseif (isset($request->blog_status)) {
            try {
                Blog::where('id', $request->id)->update($request->all());
                return response(['status' => 'success', 'title' => 'Success', 'content' => 'Blog Edition Success']);
            } catch (\Exception $e) {
                return response(['status' => 'error', 'title' => 'Failed', 'content' => 'Blog Edition Failed']);
            }
        }
    }

    public function postBlogAdd(Request $request)
    {
        try {
            $date = Str::slug(Carbon::now());
            $slug = Str::slug($request->blog_title) . '-' . $date;
            Image::make($request->file('image'))->save(public_path('/uploads/blogs/') . $slug . '.jpg')->encode('jpg', '50');
            $request->merge(['blog_image' => '/uploads/blogs/' . $slug . '.jpg']);
            $request->merge(['blog_status' => $request->blog_status == 'on' ? 'on' : 'off']);
            $request->merge(['blog_author' => Auth::user()->id]);
            $request->merge(['blog_slug' => $slug]);
            Blog::create($request->all());
            return response(['status' => 'success', 'title' => 'Success', 'content' => 'Blog Added']);
        } catch (\Exception $e) {
            return response(['status' => $e, 'title' => 'Failed', 'content' => 'Blog Addition failed. Please enter all fields.']);
        }
    }

    public function postBlogEdit(Request $request, $blogId)
    {
        try {
            $blogs =  Blog::where('id',$blogId)->first();
            $date = Str::slug(Carbon::now());
            $slug = Str::slug($request->blog_title) . '-' . $date;
            if ($request->hasFile('blog_image')){
                File::delete(public_path($blogs->blog_image));
                Image::make($request->file('blog_image'))->save(public_path('/uploads/blogs/') . $slug . '.jpg')->encode('jpg','50');
            }

            Blog::where('id',$blogId)->update([
                'blog_title' => $request->blog_title,
                'blog_description' => $request->blog_description,
                'blog_tags' => $request->blog_tags,
                'blog_content' => $request->blog_content,
                'blog_image' => $request->hasFile('blog_image') ? '/uploads/blogs/' . $slug . '.jpg' : $blogs->blog_image,
                'blog_author' => Auth::user()->id,
                'blog_slug' => $slug,
                'blog_categoryId' => $request->blog_categoryId,
                'blog_status' => $request->blog_status == 'on' ? 'on' : 'off',
            ]);

            return response(['status' => 'success', 'title' => 'Success', 'content' => 'Blog Updated ']);
        } catch (\Exception $e) {
            echo $e;
            //return response(['status' => 'error', 'title' => 'Başarısız', 'content' => 'Blog Güncellenemedi']);
        }
    }


    public function getBlogCategory()
    {
        $categories = Category::where('up_categoryId', '0')->get();
        return view('backend.blog.blog-category')->with('categories', $categories);
    }

    public function getBlogCategoryAdd()
    {
        $categories = Category::all();
        return view('backend.blog.blog-category-add')->with('categories', $categories);
    }

    public function getBlogCategoryEdit($categoryId)
    {
        $categories = Category::all();
        $category = Category::where('id', $categoryId)->first();
        return view('backend.blog.blog-category-edit')->with('categories', $categories)->with('category', $category);
    }

    public function postBlogCategory(Request $request)
    {
        if (isset($request->delete)) {
            try {
                Category::where('id', $request->id)->delete();
                return response(['status' => 'success', 'title' => 'Success', 'content' => 'Category Deleted']);
            } catch (\Exception $e) {
                return response(['status' => 'error', 'title' => 'Failed', 'content' => 'Category Delete failed']);
            }
        } elseif (isset($request->category_status)) {
            try {
                Category::where('id', $request->id)->update($request->all());
                return response(['status' => 'success', 'title' => 'Success', 'content' => 'Category Edited Successfully']);
            } catch (\Exception $e) {
                return response(['status' => 'error', 'title' => 'Failed', 'content' => 'Category Edition failed']);
            }
        }
    }

    public function postBlogCategoryAdd(Request $request)
    {
        try {
            $slug = Str::slug($request->category_name, '-');
            $request->merge(['category_slug' => $slug]);
            $request->merge(['up_categoryId'=> 0]);
            Category::create($request->all());
            return response(['status' => 'success', 'title' => 'Success', 'content' => 'Category Added.']);
        } catch (\Exception $e) {
            return response(['status' => 'error', 'title' => 'Failed', 'content' => 'Addtion Failed']);
        }
    }

    public function postBlogCategoryEdit(Request $request, $categoryId)
    {
        try {
            $slug = Str::slug($request->category_name, '-');
            $request->merge(['category_slug' => $slug]);
            $request->merge(['category_status' => $request->category_status == 'on' ? 'on' : 'off']);
            Category::where('id', $categoryId)->update($request->all());
            return response(['status' => 'success', 'title' => 'Success', 'content' => 'Category Updated']);
        } catch (\Exception $e) {
            return response(['status' => 'error', 'title' => 'Failed', 'content' => 'Category Update failed']);
        }
    }

}
