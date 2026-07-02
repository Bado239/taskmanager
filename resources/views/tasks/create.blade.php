<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une activité</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; }
        .form-container { background: white; border-radius: 15px; padding: 30px; box-shadow: 0px 4px 15px rgba(0,0,0,0.05); max-width: 600px; margin: 40px auto; }
    </style>
</head>
<body>

<div class="container">
    <div class="form-container">
        <h3 class="fw-bold mb-4">➕ Nouvelle activité</h3>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('tasks.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-bold">Titre de l'activité</label>
                <input type="text" name="title" class="form-control" value="{{ old('title') }}" placeholder="Ex: Rédaction du rapport, Sport..." required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Date prévue</label>
                <input type="date" name="date_prevue" class="form-control" value="{{ old('date_prevue', now()->format('Y-m-d')) }}">
            </div>

            <!-- NOUVEAU : CRÉNEAUX HORAIRES -->
            <div class="row mb-3">
                <div class="col-6">
                    <label class="form-label fw-bold">De (Heure début)</label>
                    <input type="time" name="heure_debut" class="form-control" value="{{ old('heure_debut') }}">
                </div>
                <div class="col-6">
                    <label class="form-label fw-bold">À (Heure fin)</label>
                    <input type="time" name="heure_fin" class="form-control" value="{{ old('heure_fin') }}">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Catégorie</label>
                <select name="category_id" class="form-select" required>
                    <option value="">Choisir une catégorie</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Projet associé (Optionnel)</label>
                <select name="project_id" class="form-select">
                    <option value="">Aucun projet</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>{{ $project->title }}</option>
                    @endforeach
                </select>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100">Enregistrer</button>
                <a href="{{ route('tasks.index') }}" class="btn btn-light border w-100">Annuler</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>