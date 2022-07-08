<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Memo;
use App\Models\Tag;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        //ユーザー情報を取得
        $user = \Auth::user();
        //メモ一覧を取得
        // $memos_all = Memo::get(); //こうするとテーブル内の全てのメモを取ってこれる(ユーザー関係なしに)
        // dd($memos_all);
        $memos = Memo::where('user_id', $user['id'])->where('status', 1)->orderBy('updated_at', 'DESC')->get(); //statusは論理削除用
        // dd($memos);
        return view('home', compact('user', 'memos'));
    }

    public function create()
    {
        //ログインしているユーザー情報を渡す
        $user = \Auth::user();
        $memos = Memo::where('user_id', $user['id'])->where('status', 1)->orderBy('updated_at', 'DESC')->get(); //statusは論理削除用
        return view('create', compact('user', 'memos'));
    }

    public function store(Request $request)
    {
        $data = $request->all(); //$requestで受け取ったデータを全て取得・格納
        // dd($data);
        //POSTされたデータをDB(memosテーブル)に挿入
        //MEMOモデルにDBへ保存する命令を出す

        //先にタグをインサート
        $tag_id = Tag::insertGetId([
            'name' => $data['tag'], 
            'user_id' => $data['user_id']]);   //insertGetId()はテーブルに新規データをinsertし、成功したらIdを返す。
        // dd($tag_id);

        //タグのIDが判明
        //タグIDをmemosテーブルに入れる
        $memo_id = Memo::insertGetId([
            'content' => $data['content'], 
            'user_id' => $data['user_id'], 
            'tag_id' => $tag_id,
            'status' => 1
        ]);


        return redirect('home');
    }

    public function edit($id)
    {
        $user = \Auth::user();
        $memo = Memo::where('status', 1)->where('id', $id)->where('user_id', $user['id'])->first();
        // dd($memo);
        $memos = Memo::where('user_id', $user['id'])->where('status', 1)->orderBy('updated_at', 'DESC')->get(); //statusは論理削除用
        $tags = Tag::where('user_id', $user['id'])->get();
        
        return view('edit', compact('user', 'memo', 'memos', 'tags'));
    }

    public function update(Request $request, $id)
    {
        $inputs = $request->all();
        // dd($inputs);
        Memo::where('id', $id)->update([
            'content' => $inputs['content'], 
            'tag_id' => $inputs['tag_id']
        ]);

        return redirect()->route('home');
    }
}
