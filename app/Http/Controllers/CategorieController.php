<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategorieRequest;
use App\Http\Requests\UpdateCategorieRequest;
use App\Models\Categorie;

class CategorieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('categories.index', [
            'categories' => Categorie::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategorieRequest $request)
    {
        $request->validate(['name' => 'required|unique:categories',
            'description' => 'nullable|string|max:255', // Validation pour la description
        ]);

        Categorie::create(['name' => $request->name,
            'description' => $request->description]);

        return redirect()->route('categories.index')
            ->with('success', 'categorie created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Categorie $categorie)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Categorie $categorie)
    {
        return view('categories.edit', compact('categorie'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategorieRequest $request, Categorie $categorie)
    {
        // It's better to move validation to UpdateCategorieRequest,
        // but for now, let's replicate similar logic as in store, adjusting for uniqueness.
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $categorie->id,
            'description' => 'nullable|string|max:255',
        ]);

        $categorie->update($validatedData);

        return redirect()->route('categories.index')
            ->with('success', 'Catégorie mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Categorie $categorie)
    {
        // Check if category is associated with any articles
        if ($categorie->articles()->count() > 0) {
            return redirect()->route('categories.index')
                ->with('error', 'Impossible de supprimer la catégorie car elle est associée à des articles.');
        }

        $categorie->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Catégorie supprimée avec succès.');
    }
}
