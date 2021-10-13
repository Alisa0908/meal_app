<?php

namespace App\Http\Controllers;

// use Illuminate\Http\Request;
use App\Http\Requests\PostRequest;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Like;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Post $post)
    {
        $posts = Post::with('user')->latest()->paginate(4);

        $create_time = $post->created_at;
        $today = date("Y-m-d H:i:s");

        //  1時間以内は分表示    
        //  1日以内は時間表示 
        //  1月以内は日数表示 
        //  1年以内は月表示 

        $diff = $create_time->diff($today);
        if ($diff->format('%i') < 60) {
            $passed = $diff->format('%i分前');
        } elseif ($diff->format('%h') < 24) {
            $passed = $diff->format('%h時間前');
        } elseif ($diff->format('%d') < 31) {
            $passed = $diff->format('%d日前');
        } elseif ($diff->format('%m月前') < 12) {
            $passed = $diff->format('%m月前');
        } else {
            $passed = '1年以上前';
        }

        return view('posts.index', compact('posts'));
        // return view('posts.index', compact('posts', 'passed'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $post = new Post();
        $categories = Category::all();
        return view('posts.create', compact('post', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\PostRequest;  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostRequest $request)
    {
        $post = new Post($request->all());
        $post->user_id = $request->user()->id;

        $file = $request->file('image');
        $post->image = self::createFileName($file);

        //トランザクション
        DB::beginTransaction();
        try {
            $post->save();

            if (!Storage::putFileAs('images/posts', $file, $post->image)) {
                throw new \Exception('画像ファイルの保存に失敗しました｡');
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors($e->getMessage());
        }

        return redirect()
            ->route('posts.show', $post)
            ->with('notice', '記事を登録しました');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        // dd(auth()->user()->id);
        // $like = Like::where('post_id', $post->id)->where('user_id', auth()->user()->id)->first();


        $create_time = $post->created_at;
        $today = date("Y-m-d H:i:s");

        $diff = $create_time->diff($today);
        if ($diff->format('%h')<1) {
            $passed = $diff->format('%i分前');
        }elseif ($diff->format('%d')<1) {
            $passed = $diff->format('%h時間前');
        }elseif ($diff->format('%m') < 1) {
            $passed = $diff->format('%d日前');
        }elseif ($diff->format('%y') <1) {
            $passed = $diff->format('%mヵ月前');
        }else {
            $passed = $post->created_at->format('Y年m月d日');
        }

        // return view('posts.show', compact('post', 'like', 'passed'));
        return view('posts.show', compact('post', 'passed'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        $categories = Category::all();
        return view('posts.edit', compact('post', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\PostRequest;  $request
     * @param  int  Post $post
     * @return \Illuminate\Http\Response
     */
    public function update(PostRequest $request, Post $post)
    {
        if ($request->user()->cannot('update', $post)) {
            return redirect()->route('posts.show', $post)
                ->withErrors('自分の記事以外は更新できません');
        }

        $file = $request->file('image');
        if ($file) {
            $delete_file_path = $post->image_path;
            $post->image = self::createFileName($file);
        }
        $post->fill($request->all());

        // トランザクション開始
        DB::beginTransaction();
        try {
            // 更新
            $post->save();

            if ($file) {
                // 画像アップロード
                if (!Storage::putFileAs('images/posts', $file, $post->image)) {
                    // 例外を投げてロールバックさせる
                    throw new \Exception('画像ファイルの保存に失敗しました。');
                }
                if (!Storage::delete($delete_file_path)) {
                    // 例外を投げてロールバックさせる
                    throw new \Exception('画像ファイルの削除に失敗しました。');
                }
            }

            // トランザクション終了(成功)
            DB::commit();
        } catch (\Exception $e) {
            // トランザクション終了(失敗)
            DB::rollback();
            return back()->withInput()->withErrors($e->getMessage());
        }

        return redirect()->route('posts.show', $post)
            ->with('notice', '記事を更新しました');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  Post $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        // トランザクション開始
        DB::beginTransaction();
        try {
            $post->delete();

            // 画像削除
            if (!Storage::delete($post->image_path)) {
                // 例外を投げてロールバックさせる
                throw new \Exception('画像ファイルの削除に失敗しました。');
            }

            // トランザクション終了(成功)
            DB::commit();
        } catch (\Exception $e) {
            // トランザクション終了(失敗)
            DB::rollback();
            return back()->withInput()->withErrors($e->getMessage());
        }

        return redirect()->route('posts.index')
            ->with('notice', '記事を削除しました');
    }

    private static function createFileName($file)
    {
        return date('YmdHis') . '_' . $file->getClientOriginalName();
    }
}
