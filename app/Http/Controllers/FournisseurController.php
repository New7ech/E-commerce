<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFournisseurRequest;
use App\Http\Requests\UpdateFournisseurRequest;
use App\Models\Fournisseur;

class FournisseurController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('fournisseurs.index', [
            'fournisseurs' => Fournisseur::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('fournisseurs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFournisseurRequest $request)
    {
        // It's better to move validation to StoreFournisseurRequest
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:fournisseurs,name',
            'description' => 'nullable|string',
            'nom_entreprise' => 'required|string|max:255',
            'adresse' => 'required|string|max:255',
            'telephone' => 'required|string|max:20',
            'email' => 'required|email|unique:fournisseurs,email',
            'ville' => 'required|string|max:255',
            'pays' => 'required|string|max:255',
        ]);

        Fournisseur::create($validatedData);

        return redirect()->route('fournisseurs.index')
            ->with('success', 'Fournisseur créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Fournisseur $fournisseur)
    {
        // Typically, show is not heavily used if edit covers it, or if index is detailed enough.
        // For consistency, you might want a simple view.
        return view('fournisseurs.show', compact('fournisseur')); // Assuming a show view exists or will be created.
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Fournisseur $fournisseur)
    {
        return view('fournisseurs.edit', compact('fournisseur'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFournisseurRequest $request, Fournisseur $fournisseur)
    {
        // It's better to move validation to UpdateFournisseurRequest
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:fournisseurs,name,' . $fournisseur->id,
            'description' => 'nullable|string',
            'nom_entreprise' => 'required|string|max:255',
            'adresse' => 'required|string|max:255',
            'telephone' => 'required|string|max:20',
            'email' => 'required|email|unique:fournisseurs,email,' . $fournisseur->id,
            'ville' => 'required|string|max:255',
            'pays' => 'required|string|max:255',
        ]);

        $fournisseur->update($validatedData);

        return redirect()->route('fournisseurs.index')
            ->with('success', 'Fournisseur mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Fournisseur $fournisseur)
    {
        if ($fournisseur->articles()->count() > 0) {
            return redirect()->route('fournisseurs.index')
                ->with('error', 'Impossible de supprimer le fournisseur car il est associé à des articles.');
        }
        $fournisseur->delete();

        return redirect()->route('fournisseurs.index')
            ->with('success', 'Fournisseur supprimé avec succès.');
    }
}
