<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Memo;
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
        $memos = Memo::where('user_id', $user['id'])->where('status', 1)->get();
        // dd($memos);
        return view('home', compact('memos'));
    }

    public function create()
    {
        //ログインしているユーザー情報を渡す
        $user = \Auth::user();
        return view('create', compact('user'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        // dd($data);
        //POSTされたデータをDB(memosテーブル)に挿入
        //MEMOモデルにDBへ保存する命令を出す
        $memo_id = Memo::insertGetId([
            'content' => $data['content'], 'user_id' => $data['user_id'], 'status' => 1 
        ]);

        return redirect('home');
    }
}
