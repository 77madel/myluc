<header>
    {{-- En-tête par défaut minimal, personnalisez selon votre thème --}}
    <div class="container">
        <div class="d-flex justify-content-between align-items-center py-3">
            <a href="{{ url('/') }}" class="navbar-brand">{{ config('app.name', 'MyLMS') }}</a>
            <nav>
                <a href="{{ url('/') }}">{{ translate('Accueil') }}</a>
            </nav>
        </div>
    </div>
</header>


