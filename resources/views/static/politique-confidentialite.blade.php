@extends('layouts.app')

@section('title', 'Politique de Confidentialité')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Politique de Confidentialité</h1>
    <div class="bg-white p-6 shadow rounded-lg prose max-w-none">
        <p>La présente politique de confidentialité décrit la manière dont [Votre Nom de Société] collecte, utilise et protège les informations personnelles que vous nous fournissez lorsque vous utilisez notre site web.</p>

        <h2 class="text-xl font-semibold mt-4 mb-2">Collecte de l'information</h2>
        <p>Nous collectons des informations lorsque vous vous inscrivez sur notre site, lorsque vous vous connectez à votre compte, effectuez un achat, participez à un concours, et / ou lorsque vous vous déconnectez. Les informations collectées incluent votre nom, votre adresse e-mail, votre numéro de téléphone, et / ou votre adresse postale.</p>
        <p>En outre, nous recevons et enregistrons automatiquement des informations à partir de votre ordinateur et navigateur, y compris votre adresse IP, vos logiciels et votre matériel, et la page que vous demandez.</p>

        <h2 class="text-xl font-semibold mt-4 mb-2">Utilisation des informations</h2>
        <p>Toutes les informations que nous recueillons auprès de vous peuvent être utilisées pour :</p>
        <ul>
            <li>Personnaliser votre expérience et répondre à vos besoins individuels</li>
            <li>Fournir un contenu publicitaire personnalisé</li>
            <li>Améliorer notre site Web</li>
            <li>Améliorer le service client et vos besoins de prise en charge</li>
            <li>Vous contacter par e-mail</li>
            <li>Administrer un concours, une promotion, ou une enquête</li>
        </ul>

        <h2 class="text-xl font-semibold mt-4 mb-2">Confidentialité du commerce en ligne</h2>
        <p>Nous sommes les seuls propriétaires des informations recueillies sur ce site. Vos informations personnelles ne seront pas vendues, échangées, transférées, ou données à une autre société pour n'importe quelle raison, sans votre consentement, en dehors de ce qui est nécessaire pour répondre à une demande et / ou une transaction, comme par exemple pour expédier une commande.</p>

        <h2 class="text-xl font-semibold mt-4 mb-2">Divulgation à des tiers</h2>
        <p>Nous ne vendons, n'échangeons et ne transférons pas vos informations personnelles identifiables à des tiers. Cela ne comprend pas les tierces parties de confiance qui nous aident à exploiter notre site Web ou à mener nos affaires, tant que ces parties conviennent de garder ces informations confidentielles.</p>

        <h2 class="text-xl font-semibold mt-4 mb-2">Protection des informations</h2>
        <p>Nous mettons en œuvre une variété de mesures de sécurité pour préserver la sécurité de vos informations personnelles. Nous utilisons un cryptage à la pointe de la technologie pour protéger les informations sensibles transmises en ligne. Nous protégeons également vos informations hors ligne.</p>

        <h2 class="text-xl font-semibold mt-4 mb-2">Cookies</h2>
        <p>Nos cookies améliorent l'accès à notre site et identifient les visiteurs réguliers. En outre, nos cookies améliorent l'expérience d'utilisateur grâce au suivi et au ciblage de ses intérêts. Cependant, cette utilisation des cookies n'est en aucune façon liée à des informations personnelles identifiables sur notre site.</p>

        <h2 class="text-xl font-semibold mt-4 mb-2">Consentement</h2>
        <p>En utilisant notre site, vous consentez à notre politique de confidentialité.</p>

        <p class="mt-6">Date de dernière mise à jour : {{ date('d/m/Y') }}</p>
    </div>
</div>
@endsection
