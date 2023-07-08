<?php

namespace App\Http\Controllers;

use App\Models\post;
use App\Models\User;
use App\Notifications\CreatePost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
//use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Notification;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('welcome');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $post = post::create([
            'title'=> $request->title,
            'body' => $request->body
        ]);

        $users=User::where('id','!=',auth()->user()->id)->get();
        Notification::send($users,new CreatePost($post->id,$post->title));
        
        return redirect()->route('dashboard');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $post = post::findorFail($id);
        $getID = DB::table('notifications')->where('data->post_id',$id)->pluck('id');
        DB::table('notifications')->where('id',$getID)->update(['read_at'=>now()]);
        return redirect()->route('dashboard');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, post $post)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(post $post)
    {
        //
    }
    public function markAsRead()
    {
        $user = User::find(auth()->user()->id);
        foreach($user->unreadNotifications as $notification){
            $notification->markAsRead();
        }
        return redirect()->route('dashboard');
    }
}
