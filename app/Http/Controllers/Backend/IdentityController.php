<?php

namespace App\Http\Controllers\Backend;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\IDVerification;
use App\Models\IDVerificationMeta;

class IdentityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $data = IDVerification::get();
        foreach ($data as $key => $value) {
            $data[$key]['goverment'] = $value->metadata()->where('type', 0)->get();
            $data[$key]['proof'] = $value->metadata()->where('type', 1)->get();
            $data[$key]['id_card'] = $value->metadata()->where('type', 2)->get();
        };
        return view('backend.identity.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $data = IDVerification::find($id);
        return view('backend.identity.detail', ['data' => $data]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $status = $request->status;
        $reason = $request->reason;
        IDVerification::where('id', $id)->update(['status' => $status, 'result' => $reason]);
        return response(['status' => 'success', 'title' => 'Success', 'content' => 'ID Verification status changed to ']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            IDVerification::where('id', $id)->delete();
            IDVerificationMeta::where('verify_id', $id)->delete();
            return response(['status' => 'success', 'title' => 'Success', 'content' => 'Successfully deleted identity info.']);
        } catch (\Throwable $th) {
            return response(['status' => 'error', 'title' => 'Error', 'content' => $th->getMessages()]);
        }
    }
}
