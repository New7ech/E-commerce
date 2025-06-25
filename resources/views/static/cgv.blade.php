@extends('layouts.app')

@section('title', 'Conditions Générales de Vente')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Conditions Générales de Vente</h1>
    <div class="bg-white p-6 shadow rounded-lg prose max-w-none">
        <p>Les présentes conditions générales de vente régissent l'ensemble des relations entre la société [Votre Nom de Société] et ses clients.</p>

        <h2 class="text-xl font-semibold mt-4 mb-2">Article 1 : Objet</h2>
        <p>Les présentes conditions visent à définir les modalités de vente entre [Votre Nom de Société] et l'Utilisateur, de la passation de commande aux services, en passant par le paiement et la livraison.</p>

        <h2 class="text-xl font-semibold mt-4 mb-2">Article 2 : Produits</h2>
        <p>Les produits régis par les présentes Conditions Générales sont ceux qui figurent sur le site internet et qui sont indiqués comme vendus et expédiés par [Votre Nom de Société]. Ils sont proposés dans la limite des stocks disponibles.</p>

        <h2 class="text-xl font-semibold mt-4 mb-2">Article 3 : Prix</h2>
        <p>Les prix de nos produits sont indiqués en Francs CFA (FCFA) toutes taxes comprises (TTC) hors participation aux frais de traitement et d'expédition. [Votre Nom de Société] se réserve le droit de modifier ses prix à tout moment mais les produits seront facturés sur la base des tarifs en vigueur au moment de la validation de la commande.</p>

        {{-- Ajoutez d'autres articles selon vos besoins : Commande, Paiement, Livraison, Droit de rétractation, Garanties, Responsabilité, Propriété intellectuelle, Données personnelles, Litiges... --}}

        <h2 class="text-xl font-semibold mt-4 mb-2">Article X : Droit applicable et juridiction compétente</h2>
        <p>Les présentes conditions générales de vente sont soumises au droit burkinabè. En cas de litige, compétence exclusive est attribuée aux tribunaux compétents de Ouagadougou.</p>

        <p class="mt-6">Date de dernière mise à jour : {{ date('d/m/Y') }}</p>
    </div>
</div>
@endsection
