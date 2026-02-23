<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login</title>
    @vite(['resources/js/app.js', 'resources/css/app.css'])
</head>

<body class="bg-light">
    <div class="container vh-100 d-flex align-items-center justify-content-center">

        <div class="card shadow-sm" style="max-width:420px; width:100%; border-radius: .75rem;">
            <div class="card-body p-4">

                <h4 class="card-title mb-3 text-center">Entrar</h4>
                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        @foreach ($errors->all() as $error)
                            <p class="mb-1 text-center">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i>{{ $error }}
                            </p>
                        @endforeach
                    </div>
                @endif
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input id="email" type="email" class="form-control" name="email" required autofocus autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Senha</label>
                        <input id="password" type="password" class="form-control" name="password" required autocomplete="off">
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-dark btn-lg flex-fill">Entrar</button>
                        <a href="{{ route('register') }}" class="btn btn-outline-secondary btn-lg">Criar conta</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
