<?php

namespace App\Http\Controllers;

use App\Models\Blogs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BlogsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $blogs = Blogs::orderBy('created_at', 'DESC')->get();

        if ($blogs->isNotEmpty()) {
            $baseUrl = env('APP_URL');
            $blogsData = $blogs->map(function ($blog) use ($baseUrl) {
                $imageUrl = $blog->image;
                if ($imageUrl && !preg_match('/^https?:\/\//', $imageUrl)) {
                    $imageUrl = $baseUrl . '/Blog-Images/' . $imageUrl;
                }

                return [
                    'id' => $blog->id,
                    'title' => $blog->title,
                    'shortDesc' => $blog->shortDesc,
                    'image' => $imageUrl,
                    'description' => $blog->description,
                    'auther' => $blog->auther,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $blogsData,
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'data' => 'Blogs Data Not Found.',
            ], 404);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $blog = Blogs::find($id);

        if ($blog) {
            $baseUrl = env('APP_URL');
            $imageUrl = $blog->image;

            if ($imageUrl && !preg_match('/^https?:\/\//', $imageUrl)) {
                $imageUrl = $baseUrl . '/Blog-Images/' . $imageUrl;
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $blog->id,
                    'title' => $blog->title,
                    'shortDesc' => $blog->shortDesc,
                    'image' => $imageUrl,
                    'description' => $blog->description,
                    'auther' => $blog->auther,
                    'date' => \Carbon\Carbon::parse($blog->created_at)->format('d M, Y')
                ],
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'data' => 'Blog Not Found.',
            ], 404);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'shortDesc' => 'required',
            'image' => 'required',
            'description' => 'required',
            'auther' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please fix validation errors.',
                'error' => $validator->errors(),
            ], 400);
        }

        $blog = new Blogs();
        $blog->title = $request->title;
        $blog->shortDesc = $request->shortDesc;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);

            $sanitizedName = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $originalName));

            $imageName = $sanitizedName . '-' . time() . '.' . $image->extension();
            $image->move(public_path('Blog-Images'), $imageName);
            $blog->image = $imageName;
        }

        $blog->description = $request->description;
        $blog->auther = $request->auther;
        $blog->save();
        $baseUrl = env('APP_URL');

        return response()->json([
            'success' => true,
            'message' => 'Blog created successfully.',
            'data' => [
                'id' => $blog->id,
                'title' => $blog->title,
                'shortDesc' => $blog->shortDesc,
                'image' => $blog->image ? $baseUrl . '/Blog-Images/' . $blog->image : null,
                'description' => $blog->description,
                'auther' => $blog->auther,
            ],
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'shortDesc' => 'required',
            'image' => 'required',
            'description' => 'required',
            'auther' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please fix validation errors.',
                'error' => $validator->errors(),
            ], 400);
        }

        $blog = Blogs::find($id);
        $blog->title = $request->title;
        $blog->shortDesc = $request->shortDesc;

        if ($request->hasFile('image')) {
            if ($blog->image) {
                $oldImagePath = public_path('Blog-Images/' . $blog->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $image = $request->file('image');
            $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);

            $sanitizedName = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $originalName));

            $imageName = $sanitizedName . '-' . time() . '.' . $image->extension();
            $image->move(public_path('Blog-Images'), $imageName);
            $blog->image = $imageName;
        }


        $blog->description = $request->description;
        $blog->auther = $request->auther;
        $blog->save();
        $baseUrl = env('APP_URL');

        return response()->json([
            'success' => true,
            'message' => 'Blog Updated successfully.',
            'data' => [
                'id' => $blog->id,
                'title' => $blog->title,
                'shortDesc' => $blog->shortDesc,
                'image' => $blog->image ? $baseUrl . '/Blog-Images/' . $blog->image : null,
                'description' => $blog->description,
                'auther' => $blog->auther,
            ],
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $blog = Blogs::find($id);
        if ($blog) {
            if ($blog->image) {
                $oldImagePath = public_path('Blog-Images/' . $blog->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $blog->delete();

            return response()->json([
                'success' => false,
                'message' => 'Blog Deleted Successfully.',
                'data' => $blog
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Blog Not Found.'
            ], 200);
        }
    }
}
