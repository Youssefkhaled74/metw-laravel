<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePageRequest;
use App\Http\Requests\UpdatePageRequest;
use App\Http\Resources\PageResource;
use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $types = $request->input('type');

            $pages = Page::query()
                ->valid()
                ->when($types, function ($q) use ($types) {
                    $q->whereIn('type', explode(',', $types));
                })
                ->get();

            return responseJson(true, 'Pages fetched successfully', PageResource::collection($pages));

        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePageRequest $request)
    {
        try{
            $validatedData = $request->validated();
            $page = Page::create($validatedData);
            return responseJson(true,'Page created successfully',$page);
        }catch(\Throwable $th){
            return responseJson(false,$th->getMessage(),null,500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try{
            $page = Page::findOrFail($id);
            return responseJson(true,'Page fetched successfully',$page);
        }catch(\Throwable $th){
            return responseJson(false,$th->getMessage(),null,500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePageRequest $request, $id)
    {
        try{
            $validatedData = $request->validated();
            $page = Page::findOrFail($id);
            $page->update($validatedData);
            return responseJson(true,'Page updated successfully',$page);
        }catch(\Throwable $th){
            return responseJson(false,$th->getMessage(),null,500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try{
            $page = Page::findOrFail($id);
            $page->delete();
            return responseJson(true,'Page deleted successfully',$page);
        }catch(\Throwable $th){
            return responseJson(false,$th->getMessage(),null,500);
        }
    }
}
