<h1>Créer un projet</h1>

<form method="POST" action="/projets">
    @csrf

    <input type="text" name="nom" placeholder="Nom du projet"><br><br>

    <textarea name="description" placeholder="Description"></textarea><br><br>

    <input type="date" name="date_debut"><br><br>

    <input type="date" name="date_fin"><br><br>

    <button type="submit">Enregistrer</button>
</form>