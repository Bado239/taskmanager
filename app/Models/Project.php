namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    // On met 'title' dans le fillable puisque notre table PostgreSQL utilise la colonne 'title'
    protected $fillable = ['title', 'description'];

    /**
     * Un projet possède plusieurs étapes.
     */
    public function steps()
    {
        return $this->hasMany(ProjectStep::class);
    }

    /**
     * Un projet possède plusieurs tâches.
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}