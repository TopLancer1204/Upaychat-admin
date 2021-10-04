<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\PostComment;
use App\Models\User;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function deletecomment(Request $request)
    {
        $commid = $request->id;
        $com = PostComment::find($commid);

        try {
            $com->delete();
            return response(['status' => 'success', 'title' => 'Success', 'content' => 'Comment Deleted successfully']);
        } catch (\Exception $e) {
            return response(['status' => 'error', 'title' => 'Error', 'content' => 'Error while deleting comment']);
        }
    }

    public function blockuser(Request $request)
    {
        $user = $request->id;
        $com = User::find($user);

        try {
            $com->user_status = 'off';
            $com->save();
            return response(['status' => 'success', 'title' => 'Success', 'content' => 'User blocked successfully']);
        } catch (\Exception $e) {
            return response(['status' => 'error', 'title' => 'Error', 'content' => 'Error while blocking user']);
        }
    }

    function getPosts()
    {
        $allPosts = Transaction::where('status', 1)->where('transaction_type', 'pay')->get();
        $postList = array();
        if (count($allPosts) > 0) {
            foreach ($allPosts as $post) {

                $us = User::find($post->user_id);
                $tu = User::find($post->touser_id);
                $comments = PostComment::where('tran_id', $post->id)->get();

                $postList[] = array(
                    'fromuser' => $us->firstname . " " . $us->lastname,
                    'touser' => $tu->firstname . " " . $tu->lastname,
                    'amount' => $post->amount,
                    'caption' => $post->caption,
                    'comments' => count($comments),
                    'likes' => $post->likes,
                    'id' => $post->id
                );
            }
        }

        return view('backend.posts.postlist')->with('posts', $postList);
    }

    public function postComments(Request $request)
    {
        $comments = PostComment::where('tran_id', $request->postId)->get();
        $CommentList = array();

        foreach ($comments as $post) {
            $user = User::find($post->user_id);

            $CommentList[] = array
            (
                'commentBy' => $user->firstname . " " . $user->lastname,
                'user_id' => $post->user_id,
                'user_status' => $user->user_status,
                'comment_id' => $post->id,
                'comment' => $post->comment,
            );
        }
        return view('backend.posts.commenttlist')->with('comments', $CommentList);
    }
}
