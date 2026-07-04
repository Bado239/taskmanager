<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer une Nouvelle Tâche</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f6f9; }
        .form-card { background: white; border-radius: 12px; padding: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

<div class="container mt-5" style="max-width: 600px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-dark">➕ Nouvelle tâche</h2>
        <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary btn-sm">Annuler</a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger mb-4">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="form-card">
        <form action="{{ route('tasks.store') }}" method="POST">
            @csrf

            <!-- TITRE -->
            <div class="mb-3">
                <label for="title" class="form-label fw-bold">Titre de la tâche</label>
                <input type="text" name="title" id="title" class="form-control" placeholder="Ex: Finir le rapport de stage" value="{{ old('title') }}" required>
            </div>

            <!-- CATÉGORIE -->
            <div class="mb-3">
                <label for="category_id" class="form-label fw-bold">Catégorie</label>
                <select name="category_id" id="category_id" class="form-select" required>
                    <option value="">-- Choisir une catégorie --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- PROJET ASSOCIÉ -->
            <div class="mb-3">
                <label for="project_id" class="form-label fw-bold">Projet associé (Optionnel)</label>
                <select name="project_id" id="project_id" class="form-select">
                    <option value="">-- Aucun --</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                            {{ $project->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- PRIORITÉ -->
            <div class="mb-3">
                <label for="priority" class="form-label fw-bold">Priorité</label>
                <select name="priority" id="priority" class="form-select" required>
                    <option value="">-- Choisir le niveau --</option>
                    <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>🔴 Haute</option>
                    <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>🟡 Moyenne</option>
                    <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>🔵 Basse</option>
                </select>
            </div>

            <!-- DATE PRÉVUE -->
            <div class="mb-3">
                <label for="date_prevue" class="form-label fw-bold">Date prévue (Optionnelle)</label>
                <input type="date" name="date_prevue" id="date_prevue" class="form-control" value="{{ old('date_prevue', date('Y-m-d')) }}">
            </div>

            <!-- BLOC HEURES -->
            <div class="row mb-4">
                <div class="col-6">
                    <label for="heure_debut" class="form-label fw-bold">Heure de début</label>
                    <input type="time" name="heure_debut" id="heure_debut" class="form-control" value="{{ old('heure_debut') }}">
                </div>
                <div class="col-6">
                    <label for="heure_fin" class="form-label fw-bold">Heure de fin</label>
                    <input type="time" name="heure_fin" id="heure_fin" class="form-control" value="{{ old('heure_fin') }}">
                </div>
            </div>

            <!-- BOUTON ENREGISTRER -->
            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">💾 Enregistrer l'activité</button>
        </form>
    </div>
</div>
<!-- Insère ce script tout en bas de ton fichier create.blade.php -->
<script>
    document.getElementById('heure_debut').addEventListener('change', function() {
        const heureDebutVal = this.value; // Format "HH:MM"
        
        if (heureDebutVal) {
            // Découpage des heures et minutes
            let [heures, minutes] = heureDebutVal.split(':').map(Number);
            
            // Ajout des 2 heures
            heures += 2;
            
            // Gestion du passage au lendemain (ex: 23:00 + 2h = 01:00)
            if (heures >= 24) {
                heures -= 24;
            }
            
            // Formatage à deux chiffres (ex: "9" devient "09")
            const heuresFormattees = String(heures).padStart(2, '0');
            const minutesFormattees = String(minutes).padStart(2, '0');
            
            // Attribution automatique à l'heure de fin
            document.getElementById('heure_fin').value = `${heuresFormattees}:${minutesFormattees}`;
        }
    });
</script>

</body>
</html>