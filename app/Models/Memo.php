<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Memo extends Model
{
    use HasFactory;
    //
    public function myMemo($user_id){
        $tag = \Request::query('tag'); //query()でURLのクエリパラメータに'tag'があるか判定
        // タグがなければ、その人が持っているメモを全て取得
        if(empty($tag)){
            return $this::select('memos.*')->where('user_id', $user_id)->where('status', 1)->get();      
        }else{
          // もしタグの指定があればタグで絞る ->wher(tagがクエリパラメーターで取得したものに一致)
          $memos = $this::select('memos.*')  //ベースをmemosテーブルに設定(memosテーブルに以下の行でとってきた値をくっつける)
              ->leftJoin('tags', 'tags.id', '=','memos.tag_id') //Joinはテーブル同士をくっつける(リレーション) tagsテーブルのidと、memosテーブルのidが一致しているものを取得する
              ->where('tags.name', $tag)
              ->where('tags.user_id', $user_id)
              ->where('memos.user_id', $user_id)
              ->where('status', 1)
              ->get();
          return $memos;
        }
    }
}
