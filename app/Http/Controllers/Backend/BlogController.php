<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    public function AllBlogCategory()
    {

        $category = BlogCategory::latest()->get();
        return view('admin.backend.blogcategory.blog_category', compact('category'));
    } // End Method 

    public function StoreBlogCategory(Request $request)
    {

        BlogCategory::insert([
            'category_name' => $request->category_name,
            'category_slug' => strtolower(str_replace(' ', '-', $request->category_name)),
        ]);

        $notification = array(
            'message' => 'BlogCategory Inserted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    } // End Method 


    public function EditBlogCategory($id)
    {

        $categories = BlogCategory::find($id);
        return response()->json($categories);
    } // End Method 


    public function UpdateBlogCategory(Request $request)
    {
        $cat_id = $request->cat_id;

        BlogCategory::find($cat_id)->update([
            'category_name' => $request->category_name,
            'category_slug' => strtolower(str_replace(' ', '-', $request->category_name)),
        ]);

        $notification = array(
            'message' => 'BlogCategory Updated Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    } // End Method 

    public function DeleteBlogCategory($id)
    {

        BlogCategory::find($id)->delete();

        $notification = array(
            'message' => 'BlogCategory Deleted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    } // End Method 

    //////////// All Blog Post Method .//

    public function BlogPost()
    {
        $post = BlogPost::latest()->get();
        return view('admin.backend.post.all_post', compact('post'));
    } // End Method 


    public function AddBlogPost()
    {

        $blogcat = BlogCategory::latest()->get();
        return view('admin.backend.post.add_post', compact('blogcat'));
    } // End Method 

    public function EditBlogPost($id)
    {

        $blogcat = BlogCategory::latest()->get();
        $post = BlogPost::find($id);
        return view('admin.backend.post.edit_post', compact('post', 'blogcat'));
    } // End Method 

    public function StoreBlogPost(Request $request)
    {
        $request->validate([
            'blogcat_id' => 'required|exists:blog_categories,id',
            'post_title' => 'required|string|max:255',
            'long_descp' => 'required|string',
            'post_tags' => 'nullable|string',
            'post_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $image = $request->file('post_image');
        $name_gen = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();

        $img = Image::make($image)->resize(370, 247)->encode($image->getClientOriginalExtension());
        Storage::disk('public')->put('upload/post/' . $name_gen, (string) $img);

        $save_url = 'storage/upload/post/' . $name_gen;

        BlogPost::create([
            'blogcat_id' => $request->blogcat_id,
            'post_title' => $request->post_title,
            'post_slug' => Str::slug($request->post_title),
            'long_descp' => $request->long_descp,
            'post_tags' => $request->post_tags,
            'post_image' => $save_url,
        ]);

        return redirect()->route('blog.post')->with([
            'message' => 'Blog Post Inserted Successfully',
            'alert-type' => 'success',
        ]);
    }

    public function UpdateBlogPost(Request $request)
    {
        $request->validate([
            'blogcat_id' => 'required|exists:blog_categories,id',
            'post_title' => 'required|string|max:255',
            'long_descp' => 'required|string',
            'post_tags' => 'nullable|string',
            'post_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $post_id = $request->id;
        $blogPost = BlogPost::findOrFail($post_id);

        $data = [
            'blogcat_id' => $request->blogcat_id,
            'post_title' => $request->post_title,
            'post_slug' => Str::slug($request->post_title),
            'long_descp' => $request->long_descp,
            'post_tags' => $request->post_tags,
        ];

        if ($request->hasFile('post_image')) {
            // Hapus gambar lama jika ada
            if ($blogPost->post_image && Storage::disk('public')->exists(str_replace('storage/', '', $blogPost->post_image))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $blogPost->post_image));
            }

            $image = $request->file('post_image');
            $name_gen = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();

            $img = Image::make($image)->resize(370, 247)->encode($image->getClientOriginalExtension());
            Storage::disk('public')->put('upload/post/' . $name_gen, (string) $img);

            $save_url = 'storage/upload/post/' . $name_gen;
            $data['post_image'] = $save_url;
        }

        $blogPost->update($data);

        return redirect()->route('blog.post')->with([
            'message' => 'Blog Post Updated Successfully',
            'alert-type' => 'success',
        ]);
    }

    public function DeleteBlogPost($id)
    {

        $item = BlogPost::find($id);
        $img = $item->post_image;
        unlink($img);

        BlogPost::find($id)->delete();

        $notification = array(
            'message' => 'Blog Post Deleted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    } // End Method 

    public function BlogDetails($slug)
    {

        $blog = BlogPost::where('post_slug', $slug)->first();
        $tags = $blog->post_tags;
        $tags_all = explode(',', $tags);
        $bcategory = BlogCategory::latest()->get();
        $post = BlogPost::latest()->limit(3)->get();
        return view('frontend.blog.blog_details', compact('blog', 'tags_all', 'bcategory', 'post'));
    } // End Method 

    public function BlogCatList($id)
    {

        $blog = BlogPost::where('blogcat_id', $id)->get();
        $breadcat = BlogCategory::where('id', $id)->first();
        $bcategory = BlogCategory::latest()->get();
        $post = BlogPost::latest()->limit(3)->get();
        return view('frontend.blog.blog_cat_list', compact('blog', 'breadcat', 'bcategory', 'post'));
    } // End Method 

    public function BlogList()
    {

        $blog = BlogPost::latest()->paginate(2);
        $bcategory = BlogCategory::latest()->get();
        $post = BlogPost::latest()->limit(3)->get();
        return view('frontend.blog.blog_list', compact('blog', 'bcategory', 'post'));
    } // End Method 


}
