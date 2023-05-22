<?php

namespace App\Http\Controllers;

use App\Review;
use App\CourseUser;
use Inertia\Inertia;
use App\Course;
use Illuminate\Support\Facades\Request;

use Illuminate\Support\Facades\Redirect;
use Auth;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $reviews = Review::where("user_student_id", Auth::id())->get()->transform(function($query){
            return [
                'id' => $query->id,
                'review' => $query->review,
                'star' => $query->star,
                'is_active' => $query->is_active,
                'course' => $query->course,
                'student' => $query->student,
            ];
        });
        return Inertia::render('Reviews/List', compact('reviews'));

    }

    public function getReviews(Course $course)
    {
        $reviews = $course->reviews->transform(function($query){
            return [
                'id' => $query->id,
                'review' => $query->review,
                'star' => $query->star,
                'is_active' => $query->is_active,
                'course' => $query->course,
                'student' => $query->student,
            ];
        });
        return $reviews;
    }

    public function list_approve_reviews(Course $course)
    {
        $arrayRole = [1, 4]; // admin
        if (!in_array(Auth::user()->role, $arrayRole)) {
            return redirect()->back();
        }

        $reviews        = $this->getReviews($course);
        $courseData     = $course;

        return Inertia::render('Reviews/List', compact('reviews','courseData'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Course $course)
    {
        /// from helpers
        if(checkAccessCourse($course->id, Auth::id()) ===  false){
            return Redirect::route('courses.index');
        };


        $reviews = $course->reviews->where('is_active', 1)->transform(function($query){
            return [
                'id' => $query->id,
                'review' => $query->review,
                'star' => $query->star,
                'is_active' => $query->is_active,
                'course' => $query->course,
                'student' => $query->student,
            ];
        });
        $myreviews = $course->reviews->where('student.id', Auth::id())->transform(function($query){
            return [
                'id' => $query->id,
                'review' => $query->review,
                'star' => $query->star,
                'is_active' => $query->is_active,
                'course' => $query->course,
                'student' => $query->student,
            ];
        });;
        return Inertia::render('Reviews/Create', compact('course', 'reviews', 'myreviews'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $arData = Request::validate([
            'review' => 'required',
            'star' => 'required',
            'course_id' => 'required',
        ]);
        $arData = array_merge($arData, ['is_active' => 0,'created_by'=> Auth::id(), 'user_student_id' => Auth::id()]);
        Review::create($arData);;
        return redirect()->back()->with('success', 'Successfully created review');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Review  $review
     * @return \Illuminate\Http\Response
     */
    public function show(Review $review)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Review  $review
     * @return \Illuminate\Http\Response
     */
    public function edit(Review $review)
    {
        $course = $review->course;
        return Inertia::render('Reviews/Edit', compact('review', 'course'));
        //
    }
    public function approve_reviews(Review $review)
    {
        $review->is_active =1;
        $review->save();
        return redirect()->back()->with('success', "Successfully approved review");
        //
    }
    public function decline_reviews(Review $review)
    {
        $review->is_active = -1;
        $review->save();
        return redirect()->back()->with('success', "Successfully refused review");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Review  $review
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Review $review)
    {
        $arData = Request::validate([
            'review' => 'required',
            'star' => 'required',
        ]);
        $review->review = $arData['review'];
        $review->star = $arData['star'];
        // dd($arData);
        $review->is_active = 0;
        $review->save();
        return redirect('reviews')->with('success', 'Review updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Review  $review
     * @return \Illuminate\Http\Response
     */
    public function destroy(Review $review)
    {
        $review->is_active = -2;
        $review->save();
        return redirect()->back()->with('success', 'Review deleted');
    }
}
