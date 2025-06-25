@extends('layouts.app')

@section('title', 'Contactez-nous')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Contactez-nous</h1>
    <div class="bg-white p-6 shadow rounded-lg">
        <p class="mb-4">Pour toute question ou demande d'information, n'hésitez pas à nous contacter :</p>
        <ul class="list-disc list-inside mb-6">
            <li><strong>Par Email :</strong> <a href="mailto:contact@votresite.bf" class="text-primary-orange hover:underline">contact@votresite.bf</a> (Exemple)</li>
            <li><strong>Par Téléphone :</strong> +226 XX XX XX XX (Exemple)</li>
            <li><strong>Adresse Postale :</strong> 123 Rue de Ouaga, Ouagadougou, Burkina Faso (Exemple)</li>
        </ul>

        <h2 class="text-2xl font-semibold mb-4">Formulaire de Contact</h2>
        {{-- Vous pouvez ajouter un formulaire de contact ici qui soumet à une route et un contrôleur spécifiques --}}
        <form action="#" method="POST"> {{-- Remplacer # par une route de traitement de contact --}}
            @csrf
            <div class="mb-4">
                <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Nom complet :</label>
                <input type="text" id="name" name="name" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('name') border-red-500 @enderror">
                @error('name') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
            </div>
            <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email :</label>
                <input type="email" id="email" name="email" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('email') border-red-500 @enderror">
                @error('email') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
            </div>
            <div class="mb-4">
                <label for="subject" class="block text-gray-700 text-sm font-bold mb-2">Sujet :</label>
                <input type="text" id="subject" name="subject" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('subject') border-red-500 @enderror">
                @error('subject') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
            </div>
            <div class="mb-6">
                <label for="message" class="block text-gray-700 text-sm font-bold mb-2">Message :</label>
                <textarea id="message" name="message" rows="5" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline @error('message') border-red-500 @enderror"></textarea>
                @error('message') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
            </div>
            <div class="flex items-center justify-between">
                <button type="submit" class="bg-primary-orange hover:bg-opacity-90 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Envoyer le Message
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
