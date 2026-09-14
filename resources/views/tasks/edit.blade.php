<!DOCTYPE html>
<html>
<head>
    <title>Modification tâche</title>
</head>

<body>

<h1>Page modification OK</h1>

<p>Titre : {{ $task->title }}</p>

<p>Projet : {{ $task->project->title }}</p>

<p>Catégorie : {{ $task->category->name }}</p>

</body>
</html>