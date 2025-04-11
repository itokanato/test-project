<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;

class PostController extends Controller
{

    /** 
     * 投稿一覧を表示
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
        $posts = Post::with('user')->get();

        return view('post.index', compact('posts'));
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
        return back()->with("message", "保存しました");
    }
}
