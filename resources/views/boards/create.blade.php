@extends('layouts.app')

@section('title', 'Criar Quadro')

@section('content')

    <div class="container" style="max-width: 600px;">

        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">
                    <i class="bi bi-kanban me-2"></i>
                    Criar Novo Quadro
                </h5>
            </div>

            <div class="card-body">

                {{-- Exibir erros de validação --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>Ops!</strong> Verifique os erros abaixo:
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{--  <form action="{{ route('boards.store') }}" method="POST"> --}}
                <form action="{{ route('boards.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">Nome do Quadro</label>
                        <input type="text" name="name" id="name"
                            class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                            placeholder="Ex: Projeto SaaS" required>
                        @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('boards.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>
                            Voltar
                        </a>

                        <button type="submit" class="btn btn-dark">
                            <i class="bi bi-check-circle me-1"></i>
                            Criar Quadro
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </div>

@endsection
