<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;
use App\Repositories\Interfaces\TagRepositoryInterface;

class TagController extends Controller
{
    protected $tagRepository;

    public function __construct(TagRepositoryInterface $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    public function index()
    {
        $tags = $this->tagRepository->all();
        return view('tags.index', compact('tags'));
    }

    public function create()
    {
        return view('tags.create');
    }

    
    public function store(StoreTagRequest $request)
    {
        $this->tagRepository->create($request->validated());
        
        return redirect()->route('tags.index')
            ->with('success', 'Tag created successfully.');
    }



    public function show($id)
    {
        $tag = $this->tagRepository->find($id);
        $articles = $this->tagRepository->getArticles($id);
        return view('tags.show', compact('tag', 'articles'));
    }

    public function edit($id)
    {
        $tag = $this->tagRepository->find($id);
        return view('tags.edit', compact('tag'));
    }

    public function update(UpdateTagRequest $request, $id)
    {
        $this->tagRepository->update($id, $request->validated());

        return redirect()->route('tags.index')
            ->with('success', 'Tag updated successfully');
    }

    public function destroy($id)
    {
        $this->tagRepository->delete($id);

        return redirect()->route('tags.index')
            ->with('success', 'Tag deleted successfully');
    }
}