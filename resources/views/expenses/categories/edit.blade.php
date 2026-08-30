@extends('layouts.app')

@section('title', 'Editar Categoria')
@section('page-title', 'Editar Categoria')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Editar Categoria</h5>
    </div>

    <form method="POST" action="{{ route('expense-categories.update', $expenseCategory) }}">
        @csrf
        @method('PUT')

        <div class="card-body">

            <div class="mb-3">
                <label class="form-label">Nome *</label>
                <input type="text" class="form-control" name="name"
                       value="{{ old('name', $expenseCategory->name) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Descrição</label>
                <textarea name="description" class="form-control" rows="3">
                    {{ old('description', $expenseCategory->description) }}
                </textarea>
            </div>

        </div>

        <div class="card-footer d-flex justify-content-between">
            <a href="{{ route('expense-categories.index') }}" class="btn btn-secondary">Voltar</a>
            <button class="btn btn-primary">Salvar Alterações</button>
        </div>
    </form>
</div>
@endsection
