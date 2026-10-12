<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

// Shared CRUD for Categories / Locations / Skills (all just have a "name")
abstract class LookupController extends Controller
{
    protected string $model;
    protected string $route;
    protected string $title;

    public function index()
    {
        return view('admin.lookups.index', [
            'items' => $this->model::withCount('jobs')->orderBy('name')->paginate(20),
            'title' => $this->title, 'route' => $this->route,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate(['name' => ['required', 'max:100', Rule::unique((new $this->model)->getTable())]]);
        $this->model::create($request->only('name'));
        return back()->with('success', $this->title . ' added.');
    }

    public function update(Request $request, $id)
    {
        $item = $this->model::findOrFail($id);
        $request->validate(['name' => ['required', 'max:100', Rule::unique($item->getTable())->ignore($item->id)]]);
        $item->update($request->only('name'));
        return back()->with('success', $this->title . ' updated.');
    }

    public function destroy($id)
    {
        $item = $this->model::withCount('jobs')->findOrFail($id);
        if ($item->jobs_count > 0) return back()->with('error', 'Cannot delete: it is used by ' . $item->jobs_count . ' job(s).');
        $item->delete();
        return back()->with('success', $this->title . ' deleted.');
    }
}
