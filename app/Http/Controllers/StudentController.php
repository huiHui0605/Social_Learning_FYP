<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StudentController extends Controller
{
    // StudentController.php
    public function index() {
        return view('student.dashboard');
    }

    public function courses() {
    return view('student.courses');
    }



    public function message() {
    return view('student.message');
    }

    public function feedback() {
    return view('student.feedback');
}
}
