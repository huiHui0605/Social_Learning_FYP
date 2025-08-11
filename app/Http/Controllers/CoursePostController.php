<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CoursePostController extends Controller
{
    public function store(Request $request, Course $course)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $course->posts()->create([
            'user_id' => auth()->id(),
            'content' => $request->content,
        ]);

        return back();
    }
}
