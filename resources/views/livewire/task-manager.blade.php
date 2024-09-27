<!-- task-manager.blade.php -->
<!-- <div class="max-w-md mx-auto p-4 mt-2 bg-white rounded-lg shadow-md"> -->
<div x-data="{ title: '', description: '', errors: {} }" class="max-w-md mx-auto p-4 mt-4 bg-white rounded-lg shadow-md">
    <!-- Panel de Filtrado por Título -->
    <div class="mb-4">
        <input type="text" wire:model.live="searchTerm" placeholder="Buscar por título" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    <!-- Panel de Filtrado por Fecha -->
    <div class="mb-4">
        <input type="date" wire:model.live="searchDate" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    <!-- Panel de Filtrado por Estado -->
    <div class="mb-4">
        <select wire:model.live="filterStatus" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="all">Todas</option>
            <option value="completed">Completadas</option>
            <option value="pending">Pendientes</option>
        </select>
    </div>

    <form wire:submit.prevent="createTask" class="mb-4" x-data="{ errors: @entangle('errors').defer }">
        <div class="flex mb-2">
            <input type="text" wire:model="title" placeholder="Título de la tarea" class="flex-grow p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            <!--- Button con animación de Alpine.js --->
            <button 
            x-data="{ isHovered: false }"
            @mouseenter="isHovered = true" 
            @mouseleave="isHovered = false"
            :class="{
                'bg-blue-500': !isHovered, 
                'bg-blue-600': isHovered, 
                'scale-100': !isHovered, 
                'scale-105': isHovered
            }"
            class="ml-2 px-4 py-2 text-white rounded-lg transition-transform duration-300">
        {{ $isEditing ? 'Actualizar' : 'Agregar' }}
            </button>
        </div>
        @error('title')
        <p class="text-red-500 text-sm">{{ $message }}</p>
        @enderror
        <div class="flex mb-2">
            <textarea wire:model="description" placeholder="Descripción de la tarea" class="flex-grow p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
        </div>
        @error('description')
            <p class="text-red-500 text-sm">{{ $message }}</p>
        @enderror
    </form>

    <p class="text-gray-700 mb-4">Saludos, esta es tu lista de Tareas actuales:</p>

    <ul class="list-disc pl-5">
        @foreach($tasks as $task)
            <li class="flex items-center justify-between mb-2">
                <div class="flex items-center">
                    <input type="checkbox" wire:click="toggleCompleted({{ $task->id }})" {{ $task->completed ? 'checked' : '' }} class="mr-2">
                    <span class="{{ $task->completed ? 'line-through text-gray-400' : 'text-gray-800' }}">{{ $task->title }} | {{ \Carbon\Carbon::parse($task->created_at)->format('d-m') }}</span>
                </div>
                <div>

                    <button wire:click="editTask({{ $task->id }})" class="text-blue-500 hover:text-blue-600 mr-2">Editar</button>
                    <button wire:click="deleteTask({{ $task->id }})" class="text-red-500 hover:text-red-600">Eliminar</button>
                </div>
            </li>
        @endforeach
    </ul>
</div>