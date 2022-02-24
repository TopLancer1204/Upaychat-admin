<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\Http\Request;

class JoBController extends Controller
{
    public function index()
    {
        $pages = Job::all();
        return view('backend.jobs.jobs')->with('pages', $pages);
    }

    public function add()
    {
        return view('backend.jobs.job-add');
    }

    public function getJobEdit($pageId)
    {
        $pages = Job::where('id', $pageId)->first();
        return view('backend.jobs.job-edit')->with('pages', $pages);
    }

    public function postPages(Request $request)
    {
        if (isset($request->delete)) {
            try {
                $pages = Job::where('id', $request->id)->first();
                Job::where('id', $request->id)->delete();
                return response(['status' => 'success', 'title' => 'Successful', 'content' => 'Job Deleted']);
            } catch (\Exception $e) {
                return response(['status' => 'success', 'title' => 'Error', 'content' => $e]);
            }
        }
    }

    public function savejob(Request $request)
    {
        try {
            Job::create($request->all());
            return response(['status' => 'success', 'title' => 'Success', 'content' => 'Job successfully saved']);
        } catch (\Exception $e) {
            return response(['status' => 'error', 'title' => 'Error!', 'content' => $e]);
        }
    }

    public function postJobEdit(Request $request, $pageId)
    {
        try {
            $pages = Job::where('id', $pageId)->first();

            Job::where('id', $pageId)->update([
                'question' => $request->question,
                'answer' => $request->answer,
            ]);

            return response(['status' => 'success', 'title' => 'Success', 'content' => 'Job Saved successfully ']);
        } catch (\Exception $e) {
            return response(['status' => 'error', 'title' => 'Error', 'content' => 'Job could not saved']);
        }
    }
}
