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

    // public function index()
    // {
    //     //ユーザー情報を取得
    //     $user = \Auth::user();
    //     //メモ一覧を取得
    //     // $memos_all = Memo::get(); //こうするとテーブル内の全てのメモを取ってこれる(ユーザー関係なしに)
    //     // dd($memos_all);
    //     $memos = Memo::where('user_id', $user['id'])->where('status', 1)->orderBy('updated_at', 'DESC')->get(); //statusは論理削除用
    //     // dd($memos);
    //     return view('create', compact('user', 'memos'));
    // }

    public function index()
    {
        return view('create');
    }

    // public function create()
    // {
    //     //ログインしているユーザー情報を渡す
    //     $user = \Auth::user();
    //     $memos = Memo::where('user_id', $user['id'])->where('status', 1)->orderBy('updated_at', 'DESC')->get(); //statusは論理削除用
    //     return view('create', compact('user', 'memos'));
    // }

    public function create()
    {
        return view('create');
    }

    public function store(Request $request)
    {
        $data = $request->all(); //$requestで受け取ったデータを全て取得・格納
        // dd($data);
        //POSTされたデータをDB(memosテーブル)に挿入
        //MEMOモデルにDBへ保存する命令を出す

        //すでに同じタグがあるか先に判定する
        $exist_tag = Tag::where('name', $data['tag'])->where('user_id', $data['user_id'])->first(); //first()のところをexists()で同じタグが存在するか判定(boolean)する方法もある
        // dd($is_exist);
        if(empty($exist_tag['id'])){           //同じユーザーで、かつ新規で作成したタグが既存のタグとダブっていないか確認(ユーザーが違えばタグの名前がダブってても判定できるので問題なし)
            //先にタグをインサート
            $tag_id = Tag::insertGetId([       //insertGetId()はテーブルに新規データをinsertし、成功したらIdを返す。
                'name' => $data['tag'], 
                'user_id' => $data['user_id']
            ]);
        }else{                                 //既存のダブっているタグが存在している場合、そのタグを使う
            $tag_id = $exist_tag;
        }
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

    // public function edit($id)
    // {
    //     $user = \Auth::user();
    //     $memo = Memo::where('status', 1)->where('id', $id)->where('user_id', $user['id'])->first();
    //     // dd($memo);
    //     $memos = Memo::where('user_id', $user['id'])->where('status', 1)->orderBy('updated_at', 'DESC')->get(); //statusは論理削除用
    //     $tags = Tag::where('user_id', $user['id'])->get();
        
    //     return view('edit', compact('user', 'memo', 'memos', 'tags'));
    // }

    public function edit($id)
    {
        $user = \Auth::user();
        $memo = Memo::where('status', 1)->where('id', $id)->where('user_id', $user['id'])->first();
        
        return view('edit', compact('memo'));
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

    public function delete(Request $request, $id) 
    {
        $inputs = $request->all();
        
        //論理削除モデルを採用しているので、今回はstatusを2に変えることで削除したものとする
        Memo::where('id', $id)->update([ 'status' => 2 ]);

        //普通の削除の場合
        // Memo::where('id', $id)->delete();

        return redirect()->route('home')->with('success', '削除が完了しました。');  //with()でapp.blade.php内の@if(session('success'))でメッセージを表示
    }
}
