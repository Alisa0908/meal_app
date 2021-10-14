<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Post;
use App\Models\Like;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function store(Post $post)
    {
        $like = new Like();
        $like->post_id = $post->id;
        $like->user_id = Auth::user()->id;
        $like->save();

        return back();
    }

    public function destroy(Post $post, Like $like)
    {
        $user_id = Auth::user()->id;
        $like = Like::where('post_id', $post->id)->where('user_id', $user_id)->first();
        $like->delete();

        return back();
    }

}
