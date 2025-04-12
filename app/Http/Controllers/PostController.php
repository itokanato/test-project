<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\Post;

class PostController extends Controller
{

    /** 
     * 投稿一覧を表示する。
     * 
     * WHY:Eagerロードを使用してユーザー情報を取得すると1回のDBアクセスでリレーション先のデータも取得できる。(withメソッド)
     *     $posts = Post::all();の実装では、$postレコードが100個ある場合、postsテーブルには1回のアクセスで全てのデータを取得できるが、user->nameを取得するためにforeach内で毎回usersテーブルにアクセスする必要が発生する(N+1問題)。
     *     $posts = Post::with('user')->get();であれば、2回のアクセスでpostsテーブルとusersテーブルのデータを取得できる。
     *     つまり、DBアクセス時負荷を大幅に軽減できる。
     * 
     * @return \Illuminate\View\View
     * */
    public function index()
    {
        // 投稿一覧を取得する
        // $posts = Post::with('user')->get();
        $posts = Post::with('user')->paginate(10);

        return view('post.index', compact('posts'));
    }

    /**
     * 投稿を個別表示する。
     * 
     * WHY:Post $postとタイプヒントして引数を指定することで、postsテーブルからルートパラメータの数字が一致するレコードを取得してくれる(依存注入)。
     *     依存注入無しでパラメータを数字情報として渡すと、数字情報からレコードを取得する処理が必要となる。=>Post::find($id)
     * 
     * @param \App\Models\Post $post
     * @return \Illuminate\View\View
     */
    public function show(Post $post)
    {
        return view('post.show', compact('post'));
    }

    /**
     * 投稿作成フォームを表示する。
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('post.create');
    }

    /**
     * 投稿を保存する。
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // testゲートを実行
        Gate::authorize('test');

        // 入力値のバリデーション
        $validated = $request->validate([
            'title' => 'required|max:20',
            'body' => 'required|max:400',
        ]);

        // WHY: セキュリティ上、user_id はフォームからではなく、ログイン中のユーザーから付与する
        $validated['user_id'] = Auth::user()->id;

        // データベースに保存
        $post = Post::create($validated);

        // フラッシュメッセージをセット
        $request->session()->flash('message', '保存しました');

        // 前のページにリダイレクト
        return back();
    }

    /**
     * 編集画面を表示する。
     *
     * @param \App\Models\Post $post
     * @return \Illuminate\View\View
     */
    public function edit(Post $post)
    {
        return view('post.edit', compact('post'));
    }

    /**
     * 投稿を更新する。
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Post $post
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Post $post)
    {
        // 入力値のバリデーション
        $validated = $request->validate([
            'title' => 'required|max:20',
            'body' => 'required|max:400',
        ]);

        // WHY: セキュリティ上、user_id はフォームからではなく、ログイン中のユーザーから付与する
        $validated['user_id'] = Auth::user()->id;

        // データベースを更新
        $post->update($validated);
        // フラッシュメッセージをセット
        $request->session()->flash('message', '更新しました');

        return back();
    }

    /**
     * 投稿を削除する。
     *
     * @param \App\Models\Post $post
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, Post $post)
    {
        $post->delete();
        $request->session()->flash('message', '削除しました');
        return redirect()->route('post.index');
    }
}
