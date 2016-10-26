<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Http\Input;
use DB;
use App\Bookmark;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $bookmarks = Bookmark::with('comments')
            ->limit(10)
            ->orderBy('uid', 'desc')
            ->get()
            ->toArray();
        return response()->json($bookmarks); 
    }

    public function getUrl(Request $request)
    {
        if($_SERVER['REQUEST_METHOD'] != 'GET' or !isset($_GET['url'])){
            return response()->json(['err' => 'check the url']);
        }

        $bookmarks = Bookmark::with('comments')
            ->where('url', '=', $request->input('url'))
            ->get()
            ->toArray();
        return response()->json($bookmarks);
    }

    public function addUrl(Request $request)
    {
        if($_SERVER['REQUEST_METHOD'] != 'GET' or !isset($_GET['url'])){
            return response()->json(['uid' => 'null']);
        }
        
        $url = DB::table('bookmarks')
            ->where('url', '=', $request->input('url'))
            ->first();
           
        if(isset($url->uid)){
            $uid = $url->uid;
        }else{
            $uid = DB::table('bookmarks')->insertGetId(
                [
                    'url' => $request->input('url'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]
            ); 
        }
        
        return response()->json(['uid' => $uid]);
    }

    public function addComment(Request $request)
    {
        if($_SERVER['REQUEST_METHOD'] != 'GET' or !isset($_GET['uid']) or !isset($_GET['text'])){
            return response()->json(['err' => 'check the url']);
        }

        $url = DB::table('bookmarks')
            ->where('uid', '=', $request->input('uid'))
            ->first();

        if(!isset($url->uid)){
            return response()->json(['err' => 'wrong uid']);
        }

        $uid = DB::table('comments')->insertGetId(
            [
                'uid' => $request->input('uid'), 
                'ip'  => $_SERVER['REMOTE_ADDR'],
                'text'=> $request->input('text'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        );

        return response()->json(['id' => $uid]); 
    }

    
    public function changeComment(Request $request)
    {
        if($_SERVER['REQUEST_METHOD'] != 'GET' or !isset($_GET['id']) or !isset($_GET['text'])){
            return response()->json(['err' => 'check the url']);
        }

        $comment = DB::table('comments')
            ->where('id', '=', $request->input('id'))
            ->first();

        if(($comment->ip == $_SERVER['REMOTE_ADDR']) && ($this->lessThanAnHour($comment->created_at))){
        
            DB::table('comments')
                ->where('id', $request->input('id'))
                ->update([
                    'text' => $request->input('text'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            return response()->json(['msg' => 'text saved']);   
        }else{
            return response()->json(['err' => 'you can not change the text']);
        }
    }

    
    public function removeComment(Request $request)
    {
        if($_SERVER['REQUEST_METHOD'] != 'GET' or !isset($_GET['id'])){
            return response()->json(['err' => 'check the url']);
        }

        $comment = DB::table('comments')
            ->where('id', '=', $request->input('id'))
            ->first();

        if(!isset($comment->id)){
            return response()->json(['err' => 'сomment not found']);
        }

        if(($comment->ip == $_SERVER['REMOTE_ADDR']) && ($this->lessThanAnHour($comment->created_at))){
            
        DB::table('comments')
            ->where('id', $request->input('id'))
            ->delete();
            return response()->json(['msg' => 'text deleted']);
        }else{
            return response()->json(['err' => 'you can not remove comment']);
        }
    }

    public function lessThanAnHour($date)
    {
        if((time() - strtotime($date) > 3600)){
            return false;            
        }else{
            return true;            
        }

    }
}
