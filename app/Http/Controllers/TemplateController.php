<?php

namespace App\Http\Controllers;

use App\Models\Template;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $search = request()->get('search', null);
        $withTrashed = request()->get('withTrashed', false);
        
        return view('templates.index', [
            'templates' => Template::query()
                ->when($withTrashed, fn(Builder $query) => $query->withTrashed())
                ->when($search, 
                    fn(Builder $query) => $query
                        ->where('name', 'like', "%$search%")
                        ->orWhere('id', '=', $search)
                )
                ->paginate(5)
                ->appends(compact('search','withTrashed')),
                'search' => $search,
                'withTrashed' => $withTrashed
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('templates.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'body' => ['required']
        ]);

        Template::create($data);

        return to_route('templates.index')
            ->with('message', __('Template successfuly create!'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Template $template)
    {
        return view('templates.show', compact('template'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Template $template)
    {
        return view('templates.edit', compact('template'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Template $template)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'body' => ['required']
        ]);

        $template->fill($data);
        $template->save();

        return back()
            ->with('message', __('Template successfuly updated!'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Template $template)
    {
        $template->delete();

        return to_route('templates.index')
            ->with('message', __('Template successfuly deleted!'));
    }
}
