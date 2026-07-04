<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier l'activité</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; }
        .form-container { background: white; border-radius: 15px; padding: 30px; box-shadow: 0px 4px 15px rgba(0,0,0,0.05); max-width: 600px; margin: 40px auto; }
    </style>
</head>
<body>

<div class="container">
    <div class="form-container">
        <h3 class="fw-bold mb-4">✏️ Modifier l'activité</h3>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('tasks.update', $task->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- TITRE -->
            <div class="mb-3">
                <label class="form-label fw-bold">Titre de l'activité</label>
                <input type="text" name="title" class="form-control" value="{{ old('title', $task->title) }}" required>
            </div>

            <!-- DATE PRÉVUE -->
            <div class="mb-3">
                <label class="form-label fw-bold">Date prévue (Optionnelle)</label>
                <input type="date" name="date_prevue" class="form-control" value="{{ old('date_prevue', $task->date_prevue ? \Illuminate\Support\Carbon::parse($task->date_prevue)->format('Y-m-d') : '') }}">
            </div>

            <!-- CRÉNEAUX HORAIRES -->
            <div class="row mb-3">
                <div class="col-6">
                    <label class="form-label fw-bold">De (Heure début)</label>
                    <!-- AJOUT DE id="heure_debut" ICI -->
                    <input type="time" name="heure_debut" id="heure_debut" class="form-control" value="{{ old('heure_debut', $task->heure_debut ? \Illuminate\Support\Carbon::parse($task->heure_debut)->format('H:i') : '') }}">
                </div>
                <div class="col-6">
                    <label class="form-label fw-bold">À (Heure fin)</label>
                    <!-- AJOUT DE id="heure_fin" ICI -->
                    <input type="time" name="heure_fin" id="heure_fin" class="form-control" value="{{ old('heure_fin', $task->heure_fin ? \Illuminate\Support\Carbon::parse($task->heure_fin)->format('H:i') : '') }}">
                </div>
            </div>
            
            <!-- PRIORITÉ -->
            <div class="mb-3">
                <label for="priority" class="form-label fw-bold">Priorité</label>
                <select name="priority" id="priority" class="form-select" required>
                    <option value="high" {{ old('priority', $task->priority) == 'high' ? 'selected' : '' }}>🔴 Haute</option>
                    <option value="medium" {{ old('priority', $task->priority) == 'medium' ? 'selected' : '' }}>🟡 Moyenne</option>
                    <option value="low" {{ old('priority', $task->priority) == 'low' ? 'selected' : '' }}>🔵 Basse</option>
                </select>
            </div>

            <!-- CATÉGORIE -->
            <div class="mb-3">
                <label class="form-label fw-bold">Catégorie</label>
                <select name="category_id" class="form-select" required>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $task->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- PROJET ASSOCIÉ -->
            <div class="mb-4">
                <label class="form-label fw-bold">Projet associé (Optionnel)</label>
                <select name="project_id" class="form-select">
                    <option value="">Aucun projet</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ old('project_id', $task->project_id) == $project->id ? 'selected' : '' }}>{{ $project->title }}</option>
                    @endforeach
                </select>
            </div>

            <!-- BOUTONS -->
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100 fw-bold">Enregistrer les modifications</button>
                <a href="{{ route('tasks.index') }}" class="btn btn-light border w-100">Annuler</a>
            </div>
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