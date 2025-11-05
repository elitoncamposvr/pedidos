<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800">
<div class="max-w-5xl mx-auto mt-10">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-3xl font-bold">Painel de Controle</h1>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg">
                Sair
            </button>
        </form>
    </div>

    <p>Bem-vindo(a) ao sistema de Solicitação de Pedidos.</p>
</div>
</body>
</html>
