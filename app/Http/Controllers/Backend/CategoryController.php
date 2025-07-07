<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class CategoryController extends Controller
{
    public function AllCategory()
    {

        $category = Category::latest()->get();
        return view('admin.backend.category.all_category', compact('category'));
    } // End Method 

    public function AddCategory()
    {
        return view('admin.backend.category.add_category');
    } // End Method 

    public function StoreCategory(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $image = $request->file('image');
        $name_gen = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();

        // Resize dan simpan ke sementara
        $img = Image::make($image)->resize(370, 246)->encode($image->getClientOriginalExtension());

        // Simpan ke storage/app/public/upload/category
        Storage::disk('public')->put('upload/category/' . $name_gen, (string) $img);

        // Path untuk disimpan di DB
        $save_url = 'storage/upload/category/' . $name_gen;

        Category::create([
            'category_name' => $request->category_name,
            'category_slug' => strtolower(str_replace(' ', '-', $request->category_name)),
            'image' => $save_url,
        ]);

        return redirect()->route('all.category')->with([
            'message' => 'Category Inserted Successfully',
            'alert-type' => 'success',
        ]);
    }

    public function EditCategory($id)
    {

        $category = Category::find($id);
        return view('admin.backend.category.edit_category', compact('category'));
    } // End Method 

    public function UpdateCategory(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $cat_id = $request->id;
        $category = Category::findOrFail($cat_id);

        $data = [
            'category_name' => $request->category_name,
            'category_slug' => strtolower(str_replace(' ', '-', $request->category_name)),
        ];

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($category->image && Storage::disk('public')->exists(str_replace('storage/', '', $category->image))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $category->image));
            }

            $image = $request->file('image');
            $name_gen = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();

            $img = Image::make($image)->resize(370, 246)->encode($image->getClientOriginalExtension());
            Storage::disk('public')->put('upload/category/' . $name_gen, (string) $img);

            $save_url = 'storage/upload/category/' . $name_gen;
            $data['image'] = $save_url;
        }

        $category->update($data);

        return redirect()->route('all.category')->with([
            'message' => $request->hasFile('image')
                ? 'Category updated with image successfully.'
                : 'Category updated without image successfully.',
            'alert-type' => 'success',
        ]);
    }

    public function DeleteCategory($id)
    {

        $item = Category::find($id);
        $img = $item->image;
        unlink($img);

        Category::find($id)->delete();

        $notification = array(
            'message' => 'Category Deleted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    } // End Method 

    ////////// All SubCategory Methods //////////////

    public function AllSubCategory()
    {

        $subcategory = SubCategory::latest()->get();
        return view('admin.backend.subcategory.all_subcategory', compact('subcategory'));
    } // End Method 


    public function AddSubCategory()
    {

        $category = Category::latest()->get();
        return view('admin.backend.subcategory.add_subcategory', compact('category'));
    } // End Method 


    public function StoreSubCategory(Request $request)
    {

        SubCategory::insert([
            'category_id' => $request->category_id,
            'subcategory_name' => $request->subcategory_name,
            'subcategory_slug' => strtolower(str_replace(' ', '-', $request->subcategory_name)),

        ]);

        $notification = array(
            'message' => 'SubCategory Inserted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('all.subcategory')->with($notification);
    } // End Method 


    public function EditSubCategory($id)
    {

        $category = Category::latest()->get();
        $subcategory = SubCategory::find($id);
        return view('admin.backend.subcategory.edit_subcategory', compact('category', 'subcategory'));
    } // End Method


    public function UpdateSubCategory(Request $request)
    {

        $subcat_id = $request->id;

        SubCategory::find($subcat_id)->update([
            'category_id' => $request->category_id,
            'subcategory_name' => $request->subcategory_name,
            'subcategory_slug' => strtolower(str_replace(' ', '-', $request->subcategory_name)),

        ]);

        $notification = array(
            'message' => 'SubCategory Updated Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('all.subcategory')->with($notification);
    } // End Method 


    public function DeleteSubCategory($id)
    {

        SubCategory::find($id)->delete();

        $notification = array(
            'message' => 'SubCategory Deleted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    } // End Method 




}
