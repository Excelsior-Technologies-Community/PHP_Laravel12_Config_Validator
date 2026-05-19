<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Config Validator Dashboard</title>

    @vite(['resources/css/app.css'])

    <script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-slate-950 text-white min-h-screen">

    <div class="container mx-auto px-6 py-10">

        <div class="flex justify-between items-center mb-8">

            <div>
                <h1 class="text-4xl font-bold">
                    🚀 Config Validator
                </h1>

                <p class="text-slate-400 mt-2">
                    Laravel 12 Advanced Dashboard
                </p>
            </div>

            <a href="{{ route('export.csv') }}"
                class="bg-emerald-500 hover:bg-emerald-600 px-5 py-3 rounded-xl font-semibold transition">
                Export CSV
            </a>

        </div>

        <form method="GET" class="mb-8">

            <input type="text" name="search" value="{{ request('search') }}" placeholder="🔍 Search config key..."
                class="w-full bg-slate-900 border border-slate-700 rounded-xl px-5 py-4 focus:outline-none">

        </form>

        <div class="grid md:grid-cols-3 gap-6 mb-10">

            <div class="bg-slate-900 p-6 rounded-2xl border border-slate-800">
                <h2 class="text-slate-400">
                    Total Configs
                </h2>

                <p class="text-4xl font-bold mt-3">
                    {{ $totalConfigs }}
                </p>
            </div>

            <div class="bg-green-900/30 p-6 rounded-2xl border border-green-500">
                <h2 class="text-green-400">
                    Passed
                </h2>

                <p class="text-4xl font-bold mt-3">
                    {{ $totalPassed }}
                </p>
            </div>

            <div class="bg-red-900/30 p-6 rounded-2xl border border-red-500">
                <h2 class="text-red-400">
                    Failed
                </h2>

                <p class="text-4xl font-bold mt-3">
                    {{ $totalFailed }}
                </p>
            </div>

        </div>

        <div class="bg-slate-900 rounded-2xl overflow-hidden border border-slate-800">

            <table class="w-full">

                <thead class="bg-slate-800">

                    <tr>
                        <th class="text-left p-5">Config Key</th>
                        <th class="text-left p-5">Status</th>
                        <th class="text-left p-5">Message</th>
                    </tr>

                </thead>

                <tbody>

                    @foreach($results as $result)

                        <tr class="border-t border-slate-800">

                            <td class="p-5">
                                {{ $result['key'] }}
                            </td>

                            <td class="p-5">

                                @if($result['status'] == 'Passed')

                                    <span class="bg-green-500/20 text-green-400 px-4 py-2 rounded-lg">
                                        Passed
                                    </span>

                                @else

                                    <span class="bg-red-500/20 text-red-400 px-4 py-2 rounded-lg">
                                        Failed
                                    </span>

                                @endif

                            </td>

                            <td class="p-5 text-slate-300">
                                {{ $result['message'] }}
                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

        <div class="mt-8">
            {{ $results->links() }}
        </div>

    </div>

</body>

</html>