@extends('test::layouts.master')

@section('content')
    <div style="max-width: 800px; margin: 50px auto; padding: 20px;">
        <div style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <h1 style="color: #2c3e50; margin-bottom: 20px;">✅ Bienvenue dans le module Test</h1>
            <p style="color: #27ae60; font-size: 18px;">Le module fonctionne correctement !</p>
            <hr style="margin: 20px 0;">
            <p><strong>Module :</strong> Test</p>
            <p><strong>Route :</strong> /test</p>
            <p><strong>Contrôleur :</strong> ExampleController</p>
        </div>
    </div>
@endsection
