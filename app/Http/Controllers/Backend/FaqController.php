<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index()
    {
        $pages = Faq::all();
        return view('backend.faqs.faqs')->with('pages', $pages);
    }

    public function add()
    {
        return view('backend.faqs.faq-add');
    }

    public function getFaqEdit($pageId)
    {
        $pages = Faq::where('id', $pageId)->first();
        return view('backend.faqs.faq-edit')->with('pages', $pages);
    }

    public function postPages(Request $request)
    {
        if (isset($request->delete)) {
            try {
                $pages = Faq::where('id', $request->id)->first();
                Faq::where('id', $request->id)->delete();
                return response(['status' => 'success', 'title' => 'Successful', 'content' => 'Page Deleted']);
            } catch (\Exception $e) {
                return response(['status' => 'success', 'title' => 'Error', 'content' => 'Page could not deleted']);
            }
        }
    }

    public function savefaq(Request $request)
    {
        try {
            Faq::create($request->all());
            return response(['status' => 'success', 'title' => 'Success', 'content' => 'FAQ successfully saved']);
        } catch (\Exception $e) {
            return response(['status' => 'error', 'title' => 'Error!', 'content' => $e]);
        }
    }

    public function postFaqEdit(Request $request, $pageId)
    {
        try {
            $pages = Faq::where('id', $pageId)->first();

            Faq::where('id', $pageId)->update([
                'question' => $request->question,
                'answer' => $request->answer,
            ]);

            return response(['status' => 'success', 'title' => 'Success', 'content' => 'FAQ Saved successfully ']);
        } catch (\Exception $e) {
            return response(['status' => 'error', 'title' => 'Error', 'content' => 'FAQ could not saved']);
        }
    }
}
