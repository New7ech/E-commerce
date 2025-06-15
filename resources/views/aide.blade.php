@extends('layouts.app')

@section('title', 'Aide & Support') {{-- Changed title --}}

@section('content') {{-- Changed from contenus to content --}}
<div class="page-header">
    <h3 class="fw-bold mb-3">Aide & Support</h3>
    <ul class="breadcrumbs">
        <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item">Aide</li>
    </ul>
</div>

<div class="row">
    {{-- The original 'aide.blade.php' had a lot of statistics cards.
         It seems like a duplicate of a dashboard. For an 'Aide' page,
         this content might not be relevant. I will create a standard help page structure.
         If the statistics were indeed intended for 'aide.blade.php',
         they would need to be refactored similar to 'statistiques.index.blade.php'
         and ensure all variables like $nombreFournisseurs, $facturesPar3Jours, etc., are passed to this view.
         For now, creating a more typical help/FAQ page structure.
    --}}
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Foire Aux Questions (FAQ)</h4>
            </div>
            <div class="card-body">
                <div class="accordion accordion-flush" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faqHeading1">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse1" aria-expanded="false" aria-controls="faqCollapse1">
                                Comment puis-je réinitialiser mon mot de passe ?
                            </button>
                        </h2>
                        <div id="faqCollapse1" class="accordion-collapse collapse" aria-labelledby="faqHeading1" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Pour réinitialiser votre mot de passe, allez sur la page de connexion et cliquez sur le lien "Mot de passe oublié ?". Suivez les instructions envoyées à votre adresse e-mail.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faqHeading2">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse2" aria-expanded="false" aria-controls="faqCollapse2">
                                Où puis-je voir l'historique de mes commandes ?
                            </button>
                        </h2>
                        <div id="faqCollapse2" class="accordion-collapse collapse" aria-labelledby="faqHeading2" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Vous pouvez consulter l'historique de vos commandes dans la section "Profil" sous "Historique des Commandes".
                                <a href="{{ route('profile.orders') }}" class="btn btn-link">Voir mes commandes</a>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faqHeading3">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse3" aria-expanded="false" aria-controls="faqCollapse3">
                                Comment contacter le support client ?
                            </button>
                        </h2>
                        <div id="faqCollapse3" class="accordion-collapse collapse" aria-labelledby="faqHeading3" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Vous pouvez contacter notre support client par email à <a href="mailto:support@example.com">support@example.com</a> ou par téléphone au +123 456 7890. Nos heures d'ouverture sont du lundi au vendredi, de 9h à 18h.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h4 class="card-title">Soumettre une Demande d'Assistance</h4>
            </div>
            <div class="card-body">
                <p>Si vous n'avez pas trouvé de réponse à votre question dans la FAQ, veuillez nous envoyer un message via le formulaire ci-dessous :</p>
                <form>
                    <div class="form-group">
                        <label for="help_name">Votre Nom</label>
                        <input type="text" class="form-control" id="help_name" placeholder="Entrez votre nom">
                    </div>
                    <div class="form-group">
                        <label for="help_email">Votre Email</label>
                        <input type="email" class="form-control" id="help_email" placeholder="Entrez votre email">
                    </div>
                    <div class="form-group">
                        <label for="help_subject">Sujet</label>
                        <input type="text" class="form-control" id="help_subject" placeholder="Sujet de votre demande">
                    </div>
                    <div class="form-group">
                        <label for="help_message">Message</label>
                        <textarea class="form-control" id="help_message" rows="5" placeholder="Décrivez votre problème ou question"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Envoyer la Demande</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Add any specific JS for the help page if needed --}}
@endpush
