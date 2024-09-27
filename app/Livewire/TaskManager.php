<?php

namespace App\Livewire;

use App\Models\Task;
use App\Forms\TaskForm;
use Livewire\Component;

class TaskManager extends Component
{

    public $tasks, $title, $description, $taskId, $isEditing = false;
    public $searchTerm = '';
    public $searchDate = '';
    public $filterStatus = 'all';

    /**
     * Método rules()
     * Define las reglas de validación desde TaskForm para los campos del formulario.
     * Se utiliza al validar los datos de entrada en el método createTask().
     * No requiere parámetros.
     * @return array (Reglas de validación)
     */
    protected function rules()
    {
        return (new TaskForm())->rules();
    }

    /**
     * Método messages()
     * Define los mensajes de error personalizados para la validación.
     * Se utiliza al mostrar los errores de validación en la vista.
     * No requiere parámetros.
     * @return array (Mensajes de error)
     */
    protected function messages()
    {
        return (new TaskForm())->messages();
    }

    /**
     * Método mount()
     * Se ejecuta al montar el componente Livewire.
     * Carga las tareas del usuario autenticado al inicializar el componente.
     * No requiere parámetros.
     * No retorna ningún valor (void).
     */
    public function mount()
    {
        $this->loadTasks(); // Cargar las tareas al montar el componente
    }

    /**
     * Método loadTasks()
     * Carga las tareas del usuario autenticado aplicando filtros según
     * el término de búsqueda, fecha de creación y estado (completado o pendiente).
     * No requiere parámetros.
     * No retorna ningún valor (void).
     */
    public function loadTasks()
    {
        // Obtener las tareas del usuario autenticado
        $query = auth()->user()->tasks();
    
        // Filtrar por título si se proporciona un término de búsqueda
        if ($this->searchTerm) {
            $query->where('title', 'like', '%' . $this->searchTerm . '%');
        }
    
        // Filtrar por fecha de creación si se proporciona una fecha específica
        if ($this->searchDate) {
            $query->whereDate('created_at', $this->searchDate);
        }

        // Filtrar por estado (completado o pendiente)
        if ($this->filterStatus === 'completed') {
            $query->where('completed', true);
        } elseif ($this->filterStatus === 'pending') {
            $query->where('completed', false);
        }
    
        // Asignar las tareas filtradas a la propiedad $tasks
        $this->tasks = $query->get();
    }

    /**
     * Método updatedSearchTerm()
     * Se ejecuta cuando la propiedad $searchTerm del campo título de filtro es actualizada.
     * Recarga las tareas aplicando el nuevo término de búsqueda.
     * No requiere parámetros.
     * No retorna ningún valor (void).
     */
    public function updatedSearchTerm()
    {
        $this->loadTasks();
    }

    /**
     * Método updatedSearchDate()
     * Se ejecuta cuando la propiedad $searchDate del campo Fecha es actualizada.
     * Recarga las tareas aplicando la nueva fecha de búsqueda.
     * No requiere parámetros.
     * No retorna ningún valor (void).
     */
    public function updatedSearchDate()
    {
        $this->loadTasks();
    }

    /**
     * Método updatedFilterStatus()
     * Se ejecuta cuando la propiedad $filterStatus del campo Estado es actualizada.
     * Recarga las tareas aplicando el nuevo filtro de estado.
     * No requiere parámetros.
     * No retorna ningún valor (void).
     */
    public function updatedFilterStatus()
    {
        $this->loadTasks();
    }

    /**
     * Método createTask()
     * Crea una nueva tarea o actualiza una tarea existente (si está en modo edición).
     * Valida los datos de entrada y luego guarda la tarea en la base de datos.
     * No requiere parámetros.
     * No retorna ningún valor (void).
     */
    public function createTask()
    {
        $validatedData = $this->validate();

        if ($this->isEditing) {
            // Si estamos en modo edición, actualizar la tarea existente
            Task::find($this->taskId)->update($validatedData);
            $this->isEditing = false;
        } else {
            // Si no estamos en modo edición, crear una nueva tarea
            Task::create([
                ...$validatedData,
                'user_id' => auth()->id()
            ]);
        }
        // Restablecer los campos del formulario y recargar las tareas
        $this->resetInput();
        $this->loadTasks();
    }

    /**
     * Método editTask()
     * Carga los datos de una tarea existente para ser editada.
     * @param Task $task (Instancia del modelo Task)
     * No retorna ningún valor (void).
     */
    public function editTask(Task $task)
    {
        $this->taskId = $task->id;
        $this->title = $task->title;
        $this->description = $task->description;
        $this->isEditing = true; // Indicamos que estamos en modo edición
    }

    /**
     * Método deleteTask()
     * Elimina una tarea existente de la base de datos.
     * @param Task $task (Instancia del modelo Task)
     * No retorna ningún valor (void).
     */
    public function deleteTask(Task $task)
    {
        $task->delete();
        $this->isEditing = false;
        $this->resetInput();
        $this->mount();
    }

    /**
     * Método toggleCompleted()
     * Alterna el estado de completado de una tarea (completado/no completado).
     * @param Task $task (Instancia del modelo Task)
     * No retorna ningún valor (void).
     */
    public function toggleCompleted(Task $task)
    {
        $task->update(['completed' => !$task->completed]);
        $this->mount();
    }

    /**
     * Método resetInput()
     * Restablece los valores de los campos de entrada del formulario.
     * No requiere parámetros.
     * No retorna ningún valor (void).
     */
    private function resetInput()
    {
        $this->title = null;
        $this->description = null;
        $this->taskId = null;
    }

    /**
     * Método render()
     * Renderiza la vista correspondiente a este componente Livewire.
     * No requiere parámetros.
     * @return \Illuminate\View\View (Vista de Blade renderizada)
     */
    public function render()
    {
        return view('livewire.task-manager')->layout('layouts.app'); // Cambia aquí
    }
}
