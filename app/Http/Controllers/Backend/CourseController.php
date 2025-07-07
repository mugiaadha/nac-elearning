<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Course;
use App\Models\Course_goal;
use App\Models\CourseSection;
use App\Models\CourseLecture;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    public function AllCourse()
    {

        $id = Auth::user()->id;
        $courses = Course::where('instructor_id', $id)->orderBy('id', 'desc')->get();
        return view('instructor.courses.all_course', compact('courses'));
    } // End Method 

    public function AddCourse()
    {

        $categories = Category::latest()->get();
        return view('instructor.courses.add_course', compact('categories'));
    } // End Method 


    public function GetSubCategory($category_id)
    {

        $subcat = SubCategory::where('category_id', $category_id)->orderBy('subcategory_name', 'ASC')->get();
        return json_encode($subcat);
    } // End Method 

    public function StoreCourse(Request $request)
    {
        $request->validate([
            'video' => 'required|mimes:mp4|max:50000',
            'course_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Proses Gambar
        $image = $request->file('course_image');
        $imageName = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();
        $img = Image::make($image)->resize(370, 246)->encode($image->getClientOriginalExtension());
        Storage::disk('public')->put('upload/course/thumbnail/' . $imageName, (string) $img);
        $save_image_url = 'upload/course/thumbnail/' . $imageName;

        // Proses Video
        $video = $request->file('video');
        $videoName = time() . '_' . uniqid() . '.' . $video->getClientOriginalExtension();
        $video->storeAs('upload/course/video', $videoName, 'public');
        $save_video_url = 'upload/course/video/' . $videoName;

        $course_id = Course::insertGetId([
            'category_id' => $request->category_id,
            'subcategory_id' => $request->subcategory_id,
            'instructor_id' => Auth::id(),
            'course_title' => $request->course_title,
            'course_name' => $request->course_name,
            'course_name_slug' => Str::slug($request->course_name),
            'description' => $request->description,
            'video' => $save_video_url,
            'label' => $request->label,
            'duration' => $request->duration,
            'resources' => $request->resources,
            'certificate' => $request->certificate,
            'selling_price' => $request->selling_price,
            'discount_price' => $request->discount_price,
            'prerequisites' => $request->prerequisites,
            'bestseller' => $request->bestseller,
            'featured' => $request->featured,
            'highestrated' => $request->highestrated,
            'status' => 1,
            'course_image' => $save_image_url,
            'created_at' => now(),
        ]);

        // Course Goals
        if ($request->course_goals) {
            foreach ($request->course_goals as $goal) {
                Course_goal::create([
                    'course_id' => $course_id,
                    'goal_name' => $goal,
                ]);
            }
        }

        return redirect()->route('all.course')->with([
            'message' => 'Course Inserted Successfully',
            'alert-type' => 'success',
        ]);
    }

    public function EditCourse($id)
    {

        $course = Course::find($id);
        $goals = Course_goal::where('course_id', $id)->get();
        $categories = Category::latest()->get();
        $subcategories = SubCategory::latest()->get();
        return view('instructor.courses.edit_course', compact('course', 'categories', 'subcategories', 'goals'));
    } // End Method 

    public function UpdateCourse(Request $request)
    {
        $cid = $request->course_id;

        Course::find($cid)->update([
            'category_id' => $request->category_id,
            'subcategory_id' => $request->subcategory_id,
            'instructor_id' => Auth::user()->id,
            'course_title' => $request->course_title,
            'course_name' => $request->course_name,
            'course_name_slug' => strtolower(str_replace(' ', '-', $request->course_name)),
            'description' => $request->description,

            'label' => $request->label,
            'duration' => $request->duration,
            'resources' => $request->resources,
            'certificate' => $request->certificate,
            'selling_price' => $request->selling_price,
            'discount_price' => $request->discount_price,
            'prerequisites' => $request->prerequisites,

            'bestseller' => $request->bestseller,
            'featured' => $request->featured,
            'highestrated' => $request->highestrated,

        ]);

        $notification = array(
            'message' => 'Course Updated Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('all.course')->with($notification);
    } // End Method 

    public function UpdateCourseImage(Request $request)
    {
        $request->validate([
            'course_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $course_id = $request->id;
        $course = Course::findOrFail($course_id);

        // Hapus gambar lama jika ada
        if ($course->course_image && Storage::disk('public')->exists($course->course_image)) {
            Storage::disk('public')->delete($course->course_image);
        }

        // Upload gambar baru
        $image = $request->file('course_image');
        $imageName = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();
        $img = Image::make($image)->resize(370, 246)->encode($image->getClientOriginalExtension());
        Storage::disk('public')->put('upload/course/thumbnail/' . $imageName, (string) $img);
        $save_image_url = 'storage/upload/course/thumbnail/' . $imageName;

        $course->update([
            'course_image' => $save_image_url,
            'updated_at' => now(),
        ]);

        return redirect()->back()->with([
            'message' => 'Course Image Updated Successfully',
            'alert-type' => 'success',
        ]);
    }

    public function UpdateCourseVideo(Request $request)
    {
        $request->validate([
            'video' => 'required|mimes:mp4,mov,avi,wmv|max:50000', // 50MB max (sesuaikan)
        ]);

        $course_id = $request->vid;
        $course = Course::findOrFail($course_id);

        // Hapus video lama jika ada
        if ($course->video && Storage::disk('public')->exists($course->video)) {
            Storage::disk('public')->delete($course->video);
        }

        // Upload video baru
        $video = $request->file('video');
        $videoName = time() . '_' . uniqid() . '.' . $video->getClientOriginalExtension();
        $video->storeAs('upload/course/video', $videoName, 'public');

        $save_video = 'storage/upload/course/video/' . $videoName;

        $course->update([
            'video' => $save_video,
            'updated_at' => Carbon::now(),
        ]);

        return redirect()->back()->with([
            'message' => 'Course Video Updated Successfully',
            'alert-type' => 'success',
        ]);
    }

    public function UpdateCourseGoal(Request $request)
    {

        $cid = $request->id;

        if ($request->course_goals == NULL) {
            return redirect()->back();
        } else {

            Course_goal::where('course_id', $cid)->delete();

            $goles = Count($request->course_goals);

            for ($i = 0; $i < $goles; $i++) {
                $gcount = new Course_goal();
                $gcount->course_id = $cid;
                $gcount->goal_name = $request->course_goals[$i];
                $gcount->save();
            }  // end for
        } // end else 

        $notification = array(
            'message' => 'Course Goals Updated Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    } // End Method 


    public function DeleteCourse($id)
    {
        $course = Course::find($id);
        unlink($course->course_image);
        unlink($course->video);

        Course::find($id)->delete();

        $goalsData = Course_goal::where('course_id', $id)->get();
        foreach ($goalsData as $item) {
            $item->goal_name;
            Course_goal::where('course_id', $id)->delete();
        }

        $notification = array(
            'message' => 'Course Deleted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    } // End Method 


    public function AddCourseLecture($id)
    {

        $course = Course::find($id);

        $section = CourseSection::where('course_id', $id)->latest()->get();

        return view('instructor.courses.section.add_course_lecture', compact('course', 'section'));
    } // End Method 

    public function AddCourseSection(Request $request)
    {

        $cid = $request->id;

        CourseSection::insert([
            'course_id' => $cid,
            'section_title' => $request->section_title,
        ]);

        $notification = array(
            'message' => 'Course Section Added Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    } // End Method 

    public function SaveLecture(Request $request)
    {

        $lecture = new CourseLecture();
        $lecture->course_id = $request->course_id;
        $lecture->section_id = $request->section_id;
        $lecture->lecture_title = $request->lecture_title;
        $lecture->url = $request->lecture_url;
        $lecture->content = $request->content;
        $lecture->save();

        return response()->json(['success' => 'Lecture Saved Successfully']);
    } // End Method 


    public function EditLecture($id)
    {

        $clecture = CourseLecture::find($id);
        return view('instructor.courses.lecture.edit_course_lecture', compact('clecture'));
    } // End Method 


    public function UpdateCourseLecture(Request $request)
    {
        $lid = $request->id;

        CourseLecture::find($lid)->update([
            'lecture_title' => $request->lecture_title,
            'url' => $request->url,
            'content' => $request->content,

        ]);

        $notification = array(
            'message' => 'Course Lecture Updated Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    } // End Method 


    public function DeleteLecture($id)
    {

        CourseLecture::find($id)->delete();

        $notification = array(
            'message' => 'Course Lecture Delete Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    } // End Method 

    public function DeleteSection($id)
    {

        $section = CourseSection::find($id);

        /// Delete reated lectures 
        $section->lectures()->delete();
        // Delete the section 
        $section->delete();

        $notification = array(
            'message' => 'Course Section Delete Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    } // End Method 



}
