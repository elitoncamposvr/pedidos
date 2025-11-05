<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login - Solicitação de Pedidos</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
<div class="w-full max-w-sm p-6 bg-white rounded-lg shadow">
    <h2 class="text-2xl font-bold text-center mb-4">Acesso ao Sistema</h2>

    @if($errors->any())
        <div class="mb-3 text-red-600 text-sm">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('login.post') }}">
        @csrf
        <div class="mb-3">
            <label for="email" class="block mb-1 text-gray-700">E-mail</label>
            <input type="email" name="email" id="email" required
                   class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200">
        </div>

        <div class="mb-3">
            <label for="password" class="block mb-1 text-gray-700">Senha</label>
            <input type="password" name="password" id="password" required
                   class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200">
        </div>

        <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-lg transition">
            Entrar
        </button>
    </form>
</div>
</body>
</html>
