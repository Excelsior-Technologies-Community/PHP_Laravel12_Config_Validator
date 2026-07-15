<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Config Validator Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-950 text-white min-h-screen">

<div class="container mx-auto px-6 py-10 max-w-7xl">

    {{-- Header --}}
    <div class="flex flex-wrap justify-between items-center mb-8 gap-4">
        <div>
            <h1 class="text-4xl font-bold">🚀 Config Validator</h1>
            <p class="text-slate-400 mt-1">Laravel 12 Advanced Dashboard</p>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            {{-- Environment Badge --}}
            <span class="px-4 py-2 rounded-xl text-sm font-semibold
                {{ $environment === 'production' ? 'bg-red-500/20 text-red-400 border border-red-500' : 'bg-blue-500/20 text-blue-400 border border-blue-500' }}">
                ENV: {{ strtoupper($environment) }}
            </span>
            <a href="{{ route('export.csv') }}"
               class="bg-emerald-500 hover:bg-emerald-600 px-5 py-2 rounded-xl font-semibold transition text-sm">
                ⬇ Export CSV
            </a>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid md:grid-cols-3 gap-6 mb-8">
        <div class="bg-slate-900 p-6 rounded-2xl border border-slate-800">
            <p class="text-slate-400 text-sm">Total Configs</p>
            <p class="text-4xl font-bold mt-2">{{ $totalConfigs }}</p>
        </div>
        <div class="bg-green-900/30 p-6 rounded-2xl border border-green-500">
            <p class="text-green-400 text-sm">Passed</p>
            <p class="text-4xl font-bold mt-2 text-green-400">{{ $totalPassed }}</p>
        </div>
        <div class="bg-red-900/30 p-6 rounded-2xl border border-red-500">
            <p class="text-red-400 text-sm">Failed</p>
            <p class="text-4xl font-bold mt-2 text-red-400">{{ $totalFailed }}</p>
        </div>
    </div>

    {{-- Search + Filter --}}
    <form method="GET" class="flex flex-wrap gap-3 mb-6">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="🔍 Search config key..."
               class="flex-1 min-w-[200px] bg-slate-900 border border-slate-700 rounded-xl px-5 py-3 focus:outline-none focus:border-blue-500 text-sm">
        <select name="filter"
                class="bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500">
            <option value="">All</option>
            <option value="passed" {{ request('filter') === 'passed' ? 'selected' : '' }}>Passed Only</option>
            <option value="failed" {{ request('filter') === 'failed' ? 'selected' : '' }}>Failed Only</option>
        </select>
        <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 px-5 py-3 rounded-xl text-sm font-semibold transition">
            Apply
        </button>
        <a href="{{ route('dashboard') }}"
           class="bg-slate-700 hover:bg-slate-600 px-5 py-3 rounded-xl text-sm font-semibold transition">
            Reset
        </a>
    </form>

    {{-- Validation Table --}}
    <div class="bg-slate-900 rounded-2xl overflow-hidden border border-slate-800 mb-10">
        <table class="w-full text-sm">
            <thead class="bg-slate-800 text-slate-300">
                <tr>
                    <th class="text-left p-4">Config Key</th>
                    <th class="text-left p-4">Current Value</th>
                    <th class="text-left p-4">Status</th>
                    <th class="text-left p-4">Message</th>
                </tr>
            </thead>
            <tbody>
                @foreach($results as $result)
                <tr class="border-t border-slate-800 hover:bg-slate-800/40 transition">
                    <td class="p-4 font-mono text-blue-300">{{ $result['key'] }}</td>
                    <td class="p-4 text-slate-400 font-mono text-xs max-w-[180px] truncate">
                        {{ is_bool($result['value']) ? ($result['value'] ? 'true' : 'false') : ($result['value'] ?? 'null') }}
                    </td>
                    <td class="p-4">
                        @if($result['status'] === 'Passed')
                            <span class="bg-green-500/20 text-green-400 px-3 py-1 rounded-lg text-xs font-semibold">✔ Passed</span>
                        @else
                            <span class="bg-red-500/20 text-red-400 px-3 py-1 rounded-lg text-xs font-semibold">✘ Failed</span>
                        @endif
                    </td>
                    <td class="p-4 text-slate-300">{{ $result['message'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mb-12">{{ $results->links() }}</div>

    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    {{-- Dynamic Live Schema Constructor Panel                               --}}
    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    <div class="border-t border-slate-800 pt-10">

        <div class="flex items-center gap-3 mb-6">
            <span class="text-2xl">🛠</span>
            <div>
                <h2 class="text-2xl font-bold">Dynamic Live Schema Constructor</h2>
                <p class="text-slate-400 text-sm mt-1">Add, simulate, and manage validation rules in real-time</p>
            </div>
        </div>

        <div class="grid lg:grid-cols-2 gap-8">

            {{-- Left: Add / Simulate Rule --}}
            <div class="bg-slate-900 rounded-2xl border border-slate-800 p-6">
                <h3 class="text-lg font-semibold mb-5 text-blue-400">⚡ Rule Builder</h3>

                <div class="space-y-4">
                    <div>
                        <label class="text-slate-400 text-xs mb-1 block">Config Key</label>
                        <input id="sb-key" type="text" placeholder="e.g. app.name"
                               class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 font-mono">
                    </div>
                    <div>
                        <label class="text-slate-400 text-xs mb-1 block">Validation Rule</label>
                        <input id="sb-rule" type="text" placeholder="e.g. string|required"
                               class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 font-mono">
                        <p class="text-slate-500 text-xs mt-1">Supports: required, string, numeric, boolean, url, email, in:a,b, nullable</p>
                    </div>

                    {{-- Simulate Result --}}
                    <div id="sb-result" class="hidden rounded-xl p-4 text-sm font-mono"></div>

                    <div class="flex gap-3 flex-wrap">
                        <button onclick="simulateRule()"
                                class="bg-yellow-500 hover:bg-yellow-600 px-5 py-2 rounded-xl text-sm font-semibold transition text-black">
                            ▶ Simulate
                        </button>
                        <button onclick="saveRule()"
                                class="bg-blue-600 hover:bg-blue-700 px-5 py-2 rounded-xl text-sm font-semibold transition">
                            💾 Save Rule
                        </button>
                        <button onclick="deleteRule()"
                                class="bg-red-600 hover:bg-red-700 px-5 py-2 rounded-xl text-sm font-semibold transition">
                            🗑 Delete Rule
                        </button>
                    </div>
                </div>

                {{-- Save/Delete feedback --}}
                <div id="sb-feedback" class="hidden mt-4 rounded-xl p-3 text-sm"></div>
            </div>

            {{-- Right: Live Rules Table --}}
            <div class="bg-slate-900 rounded-2xl border border-slate-800 p-6">
                <div class="flex justify-between items-center mb-5">
                    <h3 class="text-lg font-semibold text-purple-400">📋 Current Rules</h3>
                    <button onclick="loadRules()"
                            class="text-xs bg-slate-700 hover:bg-slate-600 px-3 py-1.5 rounded-lg transition">
                        🔄 Refresh
                    </button>
                </div>

                <div id="schema-rules-list" class="space-y-2 max-h-[420px] overflow-y-auto pr-1">
                    <p class="text-slate-500 text-sm">Loading rules...</p>
                </div>
            </div>

        </div>

        {{-- Validation Metrics --}}
        <div class="mt-8 bg-slate-900 rounded-2xl border border-slate-800 p-6">
            <h3 class="text-lg font-semibold mb-4 text-emerald-400">📊 Validation Metrics</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-slate-800 rounded-xl p-4 text-center">
                    <p class="text-slate-400 text-xs">Total Rules</p>
                    <p id="metric-total" class="text-2xl font-bold mt-1">—</p>
                </div>
                <div class="bg-green-900/30 rounded-xl p-4 text-center border border-green-800">
                    <p class="text-green-400 text-xs">Passing</p>
                    <p class="text-2xl font-bold mt-1 text-green-400">{{ $totalPassed }}</p>
                </div>
                <div class="bg-red-900/30 rounded-xl p-4 text-center border border-red-800">
                    <p class="text-red-400 text-xs">Failing</p>
                    <p class="text-2xl font-bold mt-1 text-red-400">{{ $totalFailed }}</p>
                </div>
                <div class="bg-blue-900/30 rounded-xl p-4 text-center border border-blue-800">
                    <p class="text-blue-400 text-xs">Environment</p>
                    <p class="text-lg font-bold mt-1 text-blue-400">{{ strtoupper($environment) }}</p>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

async function apiCall(url, method = 'GET', body = null) {
    const opts = {
        method,
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
    };
    if (body) opts.body = JSON.stringify(body);
    const res = await fetch(url, opts);
    return res.json();
}

function getInputs() {
    return {
        key:  document.getElementById('sb-key').value.trim(),
        rule: document.getElementById('sb-rule').value.trim(),
    };
}

async function simulateRule() {
    const { key, rule } = getInputs();
    if (!key || !rule) return showFeedback('Please enter both Config Key and Rule.', 'yellow');

    const data = await apiCall('/api/schema/simulate', 'POST', { config_key: key, rule });
    const el   = document.getElementById('sb-result');
    el.classList.remove('hidden');

    if (data.status === 'Passed') {
        el.className = 'rounded-xl p-4 text-sm font-mono bg-green-900/30 border border-green-600 text-green-300';
        el.innerHTML = `✔ <strong>PASSED</strong> — Key: <em>${data.key}</em> | Value: <em>${data.value ?? 'null'}</em>`;
    } else {
        el.className = 'rounded-xl p-4 text-sm font-mono bg-red-900/30 border border-red-600 text-red-300';
        el.innerHTML = `✘ <strong>FAILED</strong> — ${data.error}<br><span class="text-slate-400">Key: ${data.key} | Value: ${data.value ?? 'null'}</span>`;
    }
}

async function saveRule() {
    const { key, rule } = getInputs();
    if (!key || !rule) return showFeedback('Please enter both Config Key and Rule.', 'yellow');

    const data = await apiCall('/api/schema/save', 'POST', { config_key: key, rule });
    showFeedback('✔ ' + data.message, 'green');
    loadRules();
}

async function deleteRule() {
    const { key } = getInputs();
    if (!key) return showFeedback('Please enter a Config Key to delete.', 'yellow');
    if (!confirm(`Delete rule for "${key}"?`)) return;

    const data = await apiCall('/api/schema/delete', 'DELETE', { config_key: key });
    showFeedback('🗑 ' + data.message, 'red');
    loadRules();
}

async function loadRules() {
    const data = await apiCall('/api/schema');
    const list = document.getElementById('schema-rules-list');
    document.getElementById('metric-total').textContent = data.rules.length;

    if (!data.rules.length) {
        list.innerHTML = '<p class="text-slate-500 text-sm">No rules defined.</p>';
        return;
    }

    list.innerHTML = data.rules.map(r => `
        <div class="flex items-start justify-between bg-slate-800 rounded-xl px-4 py-3 gap-3 cursor-pointer hover:bg-slate-700 transition"
             onclick="fillForm('${r.key}', '${r.rule}')">
            <div class="flex-1 min-w-0">
                <p class="font-mono text-blue-300 text-xs truncate">${r.key}</p>
                <p class="text-slate-400 text-xs mt-0.5">${r.rule}</p>
            </div>
            <span class="text-slate-500 text-xs shrink-0 mt-0.5">click to edit</span>
        </div>
    `).join('');
}

function fillForm(key, rule) {
    document.getElementById('sb-key').value  = key;
    document.getElementById('sb-rule').value = rule;
    document.getElementById('sb-result').classList.add('hidden');
}

function showFeedback(msg, color) {
    const el = document.getElementById('sb-feedback');
    const colors = {
        green:  'bg-green-900/30 border border-green-600 text-green-300',
        red:    'bg-red-900/30 border border-red-600 text-red-300',
        yellow: 'bg-yellow-900/30 border border-yellow-600 text-yellow-300',
    };
    el.className = `mt-4 rounded-xl p-3 text-sm ${colors[color] || colors.green}`;
    el.textContent = msg;
    el.classList.remove('hidden');
    setTimeout(() => el.classList.add('hidden'), 4000);
}

// Load rules on page load
loadRules();
</script>

</body>
</html>
