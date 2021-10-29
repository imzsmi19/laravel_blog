<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    /**
     * index
     * 
     * @return void
     */
    public function index(){
        $blogs = Blog::latest()->paginate(10);
        return view('blog.index', compact('blogs'));
    }

    /**
     * create
     * 
     * @return void
     */
    public function create(){
        return view('blog.create');
    }

    /**
     * store
     * 
     * @param mixed $request
     * @return void
     */
    public function store(Request $request){
        $this->validate($request, [
            'image'=> 'required|image|mimes:png,jpg,jpeg',
            'title'=> 'required',
            'content'=> 'required'
        ]);

        // upload image
        $image = $request->file('image');
        $image->storeAs('public/blogs', $image->hashName());
        
        $blog = Blog::create([
            'image'=> $image->hashName(),
            'title'=> $request->title,
            'content'=> $request->content
        ]);
        
        if($blog){
            // redirect dengan pesan sukses
            return redirect()->route('blog.index')->with(['success'=>'Data berhasil disimpan!']);
        }else{
            // redirect dengan pesan error
            return redirect()->route('blog.index')->with(['error'=>'Data gagal disimpan!']);
        }
    }
    
    /**
     * edit
     * 
     * @param mixed $blog
     * @return void
     */
    public function edit(Blog $blog){
        return view('blog.edit', compact('blog'));
    }
    
    /**
     * update
     * 
     * @param mixed $request
     * @param mixed $blog
     * @return void
     */
    public function update(Request $request, Blog $blog){
        $this->validate($request, [
            'title' => 'required',
            'content' => 'required'
        ]);
        
        // get data blog by ID
        $blog = Blog::findOrFail($blog->id);
        
        if($request->file('image')==""){
            $blog->update([
                'title' => $request->title,
                'content' => $request->content
            ]);
        }else{
            // delete old image
            Storage::disk('local')->delete('public/blogs/'.$blog->image);
            
            // upload new image
            $image = $request->file('image');
            $image->storeAs('public/blogs', $image->hashName());
            
            $blog->update([
                'image'=> $image->hashName(),
                'title'=> $request->title,
                'content'=> $request->content
            ]);
        }
        
        if($blog){
            // redirect dengan pesan sukses
            return redirect()->route('blog.index')->with(['success'=>'Data berhasil diupdate!']);
        }else{
            // redirect dengan pesan error
            return redirect()->route('blog.index')->with(['error'=>'Data gagal diupdate!']);
        }
    }
    
    /**
     * destroy
     * 
     * @param mixed $id
     * @return void
     */
    public function destroy($id){
        $blog = Blog::findOrFail($id);
        Storage::disk('local')->delete('public/blogs/'.$blog->image);
        $blog->delete();
        
        if($blog){
            // redirect dengan pesan sukses
            return redirect()->route('blog.index')->with(['success'=>'Data berhasil dihapus!']);
        }else{
            // redirect dengan pesan error
            return redirect()->route('blog.index')->with(['error'=>'Data gagal dihapus!']);
        }
        
    }
}
