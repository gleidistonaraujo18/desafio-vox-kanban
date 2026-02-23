<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Criar Conta</title>
    @vite(['resources/js/app.js', 'resources/css/app.css'])
</head>

<body class="bg-light">
    <div class="container vh-100 d-flex align-items-center justify-content-center">
        <div class="card shadow-sm" style="max-width:480px; width:100%; border-radius: .75rem;">
            <div class="card-body p-4">
                <h4 class="card-title mb-3 text-center">Criar conta</h4>
                <form method="POST" action="{{ route('register.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Nome</label>
                        <input id="name" type="text" class="form-control" name="name" required autofocus autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input id="email" type="email" class="form-control" name="email" required autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Senha</label>
                        <input id="password" type="password" class="form-control" name="password" required autocomplete="off">
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-dark btn-lg">Criar conta</button>
                    </div>
                </form>
                <div class="text-center mt-3 small">
                    <a href="{{ url('/') }}">Já tem conta? Entrar</a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
