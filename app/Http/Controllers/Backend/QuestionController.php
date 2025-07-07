<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Question;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class QuestionController extends Controller
{
    public function UserQuestion(Request $request)
    {

        $course_id = $request->course_id;
        $instructor_id = $request->instructor_id;

        Question::insert([
            'course_id' => $course_id,
            'user_id' => Auth::user()->id,
            'instructor_id' => $instructor_id,
            'subject' => $request->subject,
            'question' => $request->question,
            'created_at' => Carbon::now(),
        ]);

        $notification = array(
            'message' => 'Message Send Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    } // End Method 

    public function InstructorAllQuestion()
    {
        $id = Auth::user()->id;

        $question = Question::where('instructor_id', $id)
            ->whereNull('parent_id')
            ->whereIn('id', function ($query) use ($id) {
                $query->selectRaw('MAX(id)')
                    ->from('questions')
                    ->where('instructor_id', $id)
                    ->whereNull('parent_id')
                    ->groupBy('user_id');
            })
            ->with('user') // relasi tetap jalan
            ->orderByDesc('id')
            ->get();

        return view('instructor.question.all_question', compact('question'));
    } // End Method 

    public function QuestionDetails($id)
    {
        $question = Question::find($id);
        $questions = Question::where([
            'course_id' => $question->course_id,
            'user_id' => $question->user_id,
        ])
            ->orderBy('id', 'asc')
            ->get();
        $replay = Question::where('parent_id', $id)->orderBy('id', 'asc')->get();

        return view('instructor.question.question_details', compact('question', 'questions', 'replay'));
    } // End Method 

    public function InstructorReplay(Request $request)
    {
        $que_id = $request->qid;
        $user_id = $request->user_id;
        $course_id = $request->course_id;
        $instructor_id = $request->instructor_id;

        Question::insert([
            'course_id' => $course_id,
            'subject' => auth()->user()->name,
            'user_id' => $user_id,
            'instructor_id' => $instructor_id,
            'parent_id' => $que_id,
            'question' => $request->question,
            'created_at' => Carbon::now(),
        ]);

        $notification = array(
            'message' => 'Message Send Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('instructor.all.question')->with($notification);
    } // End Method 
}
