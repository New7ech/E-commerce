@extends('layouts.app') {{-- Adaptez si vous utilisez un autre layout principal --}}

@section('title', 'Mentions Légales')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Mentions Légales</h1>
    <div class="bg-white p-6 shadow rounded-lg">
        <p class="mb-4"><strong>Dénomination sociale :</strong> Votre E-commerce Burkinabé SARL (Exemple)</p>
        <p class="mb-4"><strong>Adresse du siège social :</strong> 123 Rue de Ouaga, Ouagadougou, Burkina Faso (Exemple)</p>
        <p class="mb-4"><strong>Numéro de téléphone :</strong> +226 XX XX XX XX (Exemple)</p>
        <p class="mb-4"><strong>Adresse e-mail :</strong> contact@votresite.bf (Exemple)</p>
        <p class="mb-4"><strong>RCCM :</strong> BF OUA XXXX X XXXX (Exemple)</p>
        <p class="mb-4"><strong>Numéro IFU :</strong> XXXXXXXXXX (Exemple)</p>
        <p class="mb-4"><strong>Hébergeur du site :</strong> Nom de l'hébergeur, Adresse, Contact (Exemple)</p>
        <p class="mb-4"><strong>Directeur de la publication :</strong> Nom du Directeur (Exemple)</p>
        <p>Toutes les informations fournies sur ce site sont sujettes à modification sans préavis. Nous nous efforçons de fournir des informations aussi précises que possible, toutefois nous ne saurions garantir l'exactitude, la complétude et l'actualité des informations diffusées sur notre site.</p>
    </div>
</div>
@endsection
