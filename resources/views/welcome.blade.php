<!DOCTYPE html>
<html lang="pt-BR" class="h-full dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Finanças Pro | Laravel 12</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <!-- Flatpickr Dark & MonthSelect -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">

    <style>
        body { font-family: 'Inter', sans-serif; background-color: #020617; color: #f8fafc; }
        h1, h2, h3, .font-display { font-family: 'Plus Jakarta Sans', sans-serif; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #0f172a; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 6px; }
        .flatpickr-monthSelect-month.selected { background: #10b981 !important; border-color: #10b981 !important; color: #fff !important; }
        .flatpickr-calendar { background: #0f172a !important; border: 1px solid #1e293b !important; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in { animation: fadeIn 0.25s ease-out forwards; }
        @media print {
            body * { visibility: hidden; }
            #statementPrintArea, #statementPrintArea * { visibility: visible; }
            #statementPrintArea { position: absolute; left: 0; top: 0; width: 100%; background: white !important; color: black !important; padding: 20px; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body class="h-full flex flex-col lg:flex-row antialiased selection:bg-emerald-500 selection:text-white overflow-x-hidden">

    <!-- TELA DE AUTENTICAÇÃO (LOGIN / CADASTRO) -->
    <div id="authScreen" class="fixed inset-0 z-50 bg-slate-950 flex items-center justify-center p-4 hidden">
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-7 w-full max-w-md shadow-2xl animate-fade-in">
            <div class="w-14 h-14 bg-gradient-to-tr from-emerald-600 to-teal-400 text-white rounded-2xl flex items-center justify-center mx-auto mb-4 text-xl shadow-lg shadow-emerald-900/40">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <h2 class="text-2xl font-extrabold text-white text-center mb-1">Finanças<span class="text-emerald-400">Pro</span></h2>
            <p class="text-slate-400 text-xs text-center mb-6">Laravel 12 • Segurança Avançada</p>

            <div class="grid grid-cols-2 gap-1.5 bg-slate-950 p-1 rounded-xl border border-slate-800 mb-5">
                <button type="button" id="tabAuthLogin" onclick="switchAuthTab('login')" class="py-2 text-xs font-bold rounded-lg bg-slate-800 text-white transition">Entrar</button>
                <button type="button" id="tabAuthRegister" onclick="switchAuthTab('register')" class="py-2 text-xs font-bold rounded-lg text-slate-400 hover:text-white transition">Criar Conta</button>
            </div>

            <!-- Form Login -->
            <form id="loginForm" onsubmit="handleLogin(event)" class="space-y-4">
                <div>
                    <label class="block text-[11px] font-semibold text-slate-400 uppercase mb-1">E-mail</label>
                    <input type="email" id="loginEmail" required placeholder="seu@email.com" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-emerald-500">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-400 uppercase mb-1">Senha</label>
                    <input type="password" id="loginPassword" required placeholder="••••••••" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-emerald-500">
                </div>
                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-3.5 rounded-xl transition text-sm shadow-lg shadow-emerald-900/30">Acessar Conta</button>
            </form>

            <!-- Form Cadastro -->
            <form id="registerForm" onsubmit="handleRegister(event)" class="space-y-3.5 hidden">
                <div>
                    <label class="block text-[11px] font-semibold text-slate-400 uppercase mb-1">Nome Completo *</label>
                    <input type="text" id="regName" required placeholder="Seu nome" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-emerald-500">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-400 uppercase mb-1">E-mail *</label>
                    <input type="email" id="regEmail" required placeholder="seu@email.com" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-emerald-500">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-400 uppercase mb-1">Senha (mín. 8 caracteres) *</label>
                    <input type="password" id="regPassword" required minlength="8" placeholder="Sua senha" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-emerald-500">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-400 uppercase mb-1">Orçamento Mensal (Opcional)</label>
                    <input type="number" step="0.01" min="0" id="regBudget" placeholder="Ex: 3500.00" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-emerald-500">
                </div>
                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-3.5 rounded-xl transition text-sm">Criar Minha Conta</button>
            </form>
        </div>
    </div>

    <!-- SIDEBAR DESKTOP -->
    <aside id="appSidebar" class="hidden lg:flex lg:w-72 lg:flex-col lg:fixed lg:inset-y-0 bg-slate-900/90 border-r border-slate-800/80 z-30">
        <div class="flex items-center gap-3 px-6 py-6 border-b border-slate-800/80">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-400 flex items-center justify-center text-white shadow-lg">
                <i class="fa-solid fa-chart-line text-lg"></i>
            </div>
            <div>
                <span class="font-display font-extrabold text-lg text-white">Finanças<span class="text-emerald-400">Pro</span></span>
                <p class="text-[11px] text-slate-400">Laravel 12 Backend</p>
            </div>
        </div>

        <div class="p-4 mx-4 mt-5 rounded-2xl bg-slate-800/50 border border-slate-700/50 flex items-center justify-between">
            <div class="flex items-center gap-3 min-w-0">
                <div id="sidebarAvatar" class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center justify-center font-bold text-sm shrink-0">U</div>
                <div class="min-w-0">
                    <p id="sidebarUserName" class="text-sm font-bold text-white truncate">Usuário</p>
                    <p id="sidebarUserRole" class="text-[11px] text-slate-400 truncate">Conta Pessoal</p>
                </div>
            </div>
            <button onclick="openProfileModal()" class="text-slate-400 hover:text-white p-2 rounded-lg hover:bg-slate-700/50 transition" title="Editar Perfil">
                <i class="fa-solid fa-pen-to-square text-xs"></i>
            </button>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-1.5">
            <button onclick="window.scrollTo({top:0,behavior:'smooth'})" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                <i class="fa-solid fa-chart-pie w-5 text-center"></i>
                <span>Resumo Geral</span>
            </button>
            <button onclick="openAddTransactionModal()" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-slate-300 hover:bg-slate-800/70 hover:text-white transition">
                <i class="fa-solid fa-circle-plus w-5 text-center text-emerald-400"></i>
                <span>Nova Despesa / Receita</span>
            </button>
            <button onclick="openStatementModal()" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-slate-300 hover:bg-slate-800/70 hover:text-white transition">
                <i class="fa-solid fa-file-invoice-dollar w-5 text-center text-sky-400"></i>
                <span>Gerar Extrato</span>
            </button>
            <button onclick="openProfileModal()" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-slate-300 hover:bg-slate-800/70 hover:text-white transition">
                <i class="fa-solid fa-user-gear w-5 text-center text-slate-400"></i>
                <span>Perfil do Usuário</span>
            </button>
        </nav>

        <div class="p-4 border-t border-slate-800/80">
            <button onclick="handleLogout()" class="w-full flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl bg-red-500/10 hover:bg-red-500/20 text-red-400 text-xs font-semibold transition">
                <i class="fa-solid fa-right-from-bracket"></i> Sair da Conta
            </button>
        </div>
    </aside>

    <!-- CONTEÚDO PRINCIPAL -->
    <div id="appMainWrapper" class="flex-1 flex flex-col lg:pl-72 min-h-screen w-full hidden">
        
        <!-- Header Mobile -->
        <header class="lg:hidden bg-slate-900/90 backdrop-blur-md border-b border-slate-800 sticky top-0 z-30 px-4 py-3 flex items-center justify-between">
            <div class="flex items-center space-x-3" onclick="openProfileModal()">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-500 flex items-center justify-center text-white font-bold text-sm" id="mobileUserAvatar">U</div>
                <div>
                    <p class="text-[11px] text-slate-400">Perfil Conectado</p>
                    <h1 id="mobileUserName" class="text-sm font-bold text-white truncate max-w-[140px]">Usuário</h1>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <button onclick="openStatementModal()" class="px-3 py-2 rounded-xl bg-sky-500/10 border border-sky-500/20 text-sky-400 text-xs font-semibold flex items-center gap-1.5">
                    <i class="fa-solid fa-file-lines"></i> Extrato
                </button>
                <button onclick="handleLogout()" class="w-9 h-9 rounded-xl bg-red-500/10 text-red-400 flex items-center justify-center" title="Sair">
                    <i class="fa-solid fa-right-from-bracket text-sm"></i>
                </button>
            </div>
        </header>

        <main class="flex-1 max-w-6xl w-full mx-auto px-4 sm:px-6 lg:px-8 pt-6 pb-28 lg:pb-12 space-y-6">
            
            <div class="hidden lg:flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-extrabold text-white">Resumo Financeiro</h1>
                    <p class="text-sm text-slate-400">Sincronizado via API nativa do Laravel 12.</p>
                </div>
                <div class="flex items-center gap-3">
                    <button onclick="openStatementModal()" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-200 text-xs font-semibold flex items-center gap-2 transition">
                        <i class="fa-solid fa-file-export text-sky-400"></i> Gerar Extrato
                    </button>
                    <button onclick="openAddTransactionModal()" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold flex items-center gap-2 shadow-lg transition">
                        <i class="fa-solid fa-plus"></i> Nova Despesa
                    </button>
                </div>
            </div>

            <!-- KPIs -->
            <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                <div class="bg-slate-900/90 border border-amber-500/20 rounded-2xl p-5 shadow-lg">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Pendente</span>
                        <div class="w-9 h-9 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center"><i class="fa-solid fa-clock text-sm"></i></div>
                    </div>
                    <h2 id="kpiPending" class="text-2xl font-extrabold text-amber-400">R$ 0,00</h2>
                    <p id="kpiPendingCount" class="text-[11px] text-slate-400 mt-1.5">0 pendentes</p>
                </div>

                <div class="bg-slate-900/90 border border-red-500/20 rounded-2xl p-5 shadow-lg">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Despesas no Período</span>
                        <div class="w-9 h-9 rounded-xl bg-red-500/10 text-red-400 flex items-center justify-center"><i class="fa-solid fa-arrow-trend-down text-sm"></i></div>
                    </div>
                    <h2 id="kpiExpenses" class="text-2xl font-extrabold text-red-400">R$ 0,00</h2>
                    <div class="mt-2">
                        <div class="flex justify-between text-[10px] text-slate-400 mb-1">
                            <span>Orçamento: <strong id="kpiBudgetLabel" class="text-slate-300">-</strong></span>
                            <span id="kpiBudgetPercent">0%</span>
                        </div>
                        <div class="w-full bg-slate-800 h-1.5 rounded-full overflow-hidden">
                            <div id="kpiBudgetBar" class="bg-red-500 h-full rounded-full transition-all duration-500" style="width: 0%"></div>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-900/90 border border-emerald-500/20 rounded-2xl p-5 shadow-lg">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Valor Pago</span>
                        <div class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center"><i class="fa-solid fa-circle-check text-sm"></i></div>
                    </div>
                    <h2 id="kpiPaid" class="text-2xl font-extrabold text-emerald-400">R$ 0,00</h2>
                    <div class="mt-2">
                        <div class="flex justify-between text-[10px] text-slate-400 mb-1">
                            <span>Progresso quitado</span>
                            <span id="kpiPaidPercent" class="text-emerald-400 font-semibold">0%</span>
                        </div>
                        <div class="w-full bg-slate-800 h-1.5 rounded-full overflow-hidden">
                            <div id="kpiPaidBar" class="bg-emerald-500 h-full rounded-full transition-all duration-500" style="width: 0%"></div>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-slate-900 to-slate-800/80 border border-slate-700/60 rounded-2xl p-5 shadow-lg">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Saldo Período</span>
                        <span id="activeFilterBadge" class="text-[10px] font-semibold bg-slate-800 text-emerald-400 px-2.5 py-0.5 rounded-full border border-slate-700">Mês Atual</span>
                    </div>
                    <h2 id="kpiBalance" class="text-2xl font-extrabold text-white">R$ 0,00</h2>
                    <p class="text-[11px] text-slate-400 mt-1.5">Receitas: <span id="kpiIncome" class="text-emerald-400 font-bold">R$ 0,00</span></p>
                </div>
            </section>

            <!-- FILTROS E LISTA -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                <section class="lg:col-span-4 bg-slate-900/80 border border-slate-800/80 rounded-2xl p-4 sm:p-5 space-y-4 lg:sticky lg:top-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-bold text-slate-200 uppercase tracking-wider flex items-center gap-2">
                            <i class="fa-solid fa-sliders text-emerald-400"></i> Filtros
                        </h3>
                        <button onclick="resetFilters()" class="text-xs text-emerald-400 hover:underline font-medium">Limpar</button>
                    </div>

                    <div>
                        <label class="text-[11px] text-slate-400 font-medium block mb-1">Buscar</label>
                        <input type="text" id="filterSearchInput" oninput="applyFilters()" placeholder="Descrição ou categoria..." class="w-full bg-slate-800/90 border border-slate-700/80 rounded-xl px-3 py-2.5 text-white text-xs focus:outline-none focus:border-emerald-500">
                    </div>

                    <div class="grid grid-cols-2 gap-1.5 bg-slate-950 p-1 rounded-xl border border-slate-800">
                        <button id="tabFilterMonth" onclick="switchFilterMode('month')" class="py-1.5 text-xs font-semibold rounded-lg bg-slate-800 text-white">Por Mês</button>
                        <button id="tabFilterPeriod" onclick="switchFilterMode('period')" class="py-1.5 text-xs font-semibold rounded-lg text-slate-400 hover:text-white">Por Período</button>
                    </div>

                    <div id="containerMonthFilter" class="space-y-1">
                        <label class="text-[11px] text-slate-400 font-medium">Mês de Referência</label>
                        <div class="relative">
                            <input type="text" id="filterMonthInput" class="w-full bg-slate-800/90 border border-slate-700/80 rounded-xl px-3 py-2.5 pr-9 text-white text-xs cursor-pointer">
                            <i class="fa-regular fa-calendar text-slate-400 text-sm absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                        </div>
                    </div>

                    <div id="containerPeriodFilter" class="grid grid-cols-2 gap-2 hidden">
                        <div>
                            <label class="text-[11px] text-slate-400 font-medium">Início</label>
                            <input type="date" id="filterStartDate" onchange="applyFilters()" class="w-full bg-slate-800/90 border border-slate-700/80 rounded-xl px-2.5 py-2 text-white text-xs">
                        </div>
                        <div>
                            <label class="text-[11px] text-slate-400 font-medium">Fim</label>
                            <input type="date" id="filterEndDate" onchange="applyFilters()" class="w-full bg-slate-800/90 border border-slate-700/80 rounded-xl px-2.5 py-2 text-white text-xs">
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-1.5">
                        <button onclick="setStatusFilter('all')" id="statusFilterAll" class="py-2 rounded-xl text-xs font-semibold bg-emerald-600 text-white">Todas</button>
                        <button onclick="setStatusFilter('pending')" id="statusFilterPending" class="py-2 rounded-xl text-xs font-semibold bg-slate-800 text-slate-400">Pendentes</button>
                        <button onclick="setStatusFilter('paid')" id="statusFilterPaid" class="py-2 rounded-xl text-xs font-semibold bg-slate-800 text-slate-400">Pagas</button>
                    </div>
                </section>

                <section class="lg:col-span-8 space-y-3">
                    <div class="flex items-center justify-between bg-slate-900/50 border border-slate-800/80 rounded-2xl px-4 py-3">
                        <div class="flex items-center gap-2">
                            <h3 class="text-xs sm:text-sm font-bold text-white uppercase">Transações & Parcelas</h3>
                            <span id="transactionCountBadge" class="text-[11px] font-semibold text-emerald-400 bg-emerald-500/10 px-2.5 py-0.5 rounded-full">0 itens</span>
                        </div>
                        <button onclick="openStatementModal()" class="text-xs text-sky-400 hover:underline font-semibold"><i class="fa-solid fa-print mr-1"></i>Extrato</button>
                    </div>
                    <div id="transactionsList" class="space-y-2.5"></div>
                </section>
            </div>
        </main>

        <!-- Bottom Nav Mobile -->
        <nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-slate-900/95 backdrop-blur-lg border-t border-slate-800 px-2 py-2 grid grid-cols-4 items-center z-30">
            <button onclick="window.scrollTo({top:0,behavior:'smooth'})" class="flex flex-col items-center text-emerald-400 py-1"><i class="fa-solid fa-chart-pie text-base mb-1"></i><span class="text-[10px]">Resumo</span></button>
            <button onclick="openAddTransactionModal()" class="flex flex-col items-center text-slate-400 py-1"><i class="fa-solid fa-circle-plus text-base mb-1"></i><span class="text-[10px]">Nova Despesa</span></button>
            <button onclick="openStatementModal()" class="flex flex-col items-center text-slate-400 py-1"><i class="fa-solid fa-file-invoice-dollar text-base mb-1"></i><span class="text-[10px]">Extrato</span></button>
            <button onclick="openProfileModal()" class="flex flex-col items-center text-slate-400 py-1"><i class="fa-solid fa-user text-base mb-1"></i><span class="text-[10px]">Perfil</span></button>
        </nav>
    </div>

    <!-- MODAL NOVA TRANSAÇÃO -->
    <div id="transactionModal" class="fixed inset-0 z-50 bg-slate-950/85 backdrop-blur-sm flex items-end sm:items-center justify-center sm:p-4 hidden">
        <div class="bg-slate-900 border border-slate-800 rounded-t-3xl sm:rounded-3xl p-6 w-full max-w-md shadow-2xl animate-fade-in">
            <div class="flex justify-between items-center mb-5">
                <h3 class="text-base font-bold text-white"><i class="fa-solid fa-circle-plus text-emerald-400 mr-1.5"></i>Novo Lançamento</h3>
                <button onclick="closeAddTransactionModal()" class="w-8 h-8 rounded-xl bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form id="transactionForm" onsubmit="saveTransaction(event)" class="space-y-4">
                <div class="grid grid-cols-2 gap-2 bg-slate-950 p-1 rounded-2xl border border-slate-800">
                    <button type="button" id="btnTypeExpense" onclick="setTransactionType('expense')" class="py-2.5 text-xs font-bold rounded-xl bg-red-500/20 text-red-400 border border-red-500/30">Despesa</button>
                    <button type="button" id="btnTypeIncome" onclick="setTransactionType('income')" class="py-2.5 text-xs font-bold rounded-xl text-slate-400">Receita</button>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-400 uppercase mb-1">Descrição *</label>
                    <input type="text" id="txDescription" required maxlength="180" placeholder="Ex: Aluguel, Supermercado..." class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 text-white text-sm">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 uppercase mb-1">Categoria</label>
                        <select id="txCategory" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-3 text-white text-xs">
                            <option value="Alimentação">Alimentação</option>
                            <option value="Moradia">Moradia</option>
                            <option value="Transporte">Transporte</option>
                            <option value="Cartão de Crédito">Cartão de Crédito</option>
                            <option value="Saúde">Saúde</option>
                            <option value="Lazer">Lazer</option>
                            <option value="Salário">Salário</option>
                            <option value="Outros">Outros</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 uppercase mb-1">Valor Total (R$) *</label>
                        <input type="number" step="0.01" min="0.01" id="txAmount" required placeholder="0,00" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-3 text-white text-sm">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 uppercase mb-1">Parcelas</label>
                        <input type="number" min="1" max="120" id="txInstallments" value="1" required class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-3 text-white text-sm">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 uppercase mb-1">1º Vencimento *</label>
                        <input type="date" id="txDueDate" required class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-3 text-white text-xs">
                    </div>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-400 uppercase mb-1">Status Inicial</label>
                    <select id="txInitialStatus" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-3 text-white text-sm">
                        <option value="pending">Pendente</option>
                        <option value="paid">Pago</option>
                    </select>
                </div>
                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-3.5 rounded-xl text-sm">Salvar Lançamento</button>
            </form>
        </div>
    </div>

    <!-- MODAL EXTRATO -->
    <div id="statementModal" class="fixed inset-0 z-50 bg-slate-950/90 backdrop-blur-sm flex items-center justify-center p-4 hidden">
        <div class="bg-slate-900 border border-slate-800 rounded-3xl w-full max-w-2xl shadow-2xl flex flex-col max-h-[90vh]">
            <div class="p-5 border-b border-slate-800 flex items-center justify-between no-print">
                <h3 class="text-base font-bold text-white"><i class="fa-solid fa-file-invoice-dollar text-sky-400 mr-2"></i>Extrato Financeiro</h3>
                <button onclick="closeStatementModal()" class="w-8 h-8 rounded-xl bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div id="statementPrintArea" class="p-6 overflow-y-auto space-y-5 flex-1">
                <div class="flex justify-between border-b border-slate-800 pb-4">
                    <div>
                        <h4 class="text-lg font-extrabold text-white">EXTRATO DE TRANSAÇÕES</h4>
                        <p class="text-xs text-slate-400">Titular: <strong id="stmtUserName" class="text-white"></strong></p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-slate-400">Período: <strong id="stmtPeriod" class="text-emerald-400"></strong></p>
                        <p class="text-[11px] text-slate-500" id="stmtGeneratedDate"></p>
                    </div>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div class="p-3 rounded-xl bg-slate-950 border border-slate-800"><p class="text-[10px] text-slate-400">Despesas</p><p id="stmtTotalExpense" class="text-sm font-bold text-red-400"></p></div>
                    <div class="p-3 rounded-xl bg-slate-950 border border-slate-800"><p class="text-[10px] text-slate-400">Pago</p><p id="stmtTotalPaid" class="text-sm font-bold text-emerald-400"></p></div>
                    <div class="p-3 rounded-xl bg-slate-950 border border-slate-800"><p class="text-[10px] text-slate-400">Pendente</p><p id="stmtTotalPending" class="text-sm font-bold text-amber-400"></p></div>
                    <div class="p-3 rounded-xl bg-slate-950 border border-slate-800"><p class="text-[10px] text-slate-400">Saldo</p><p id="stmtBalance" class="text-sm font-bold text-white"></p></div>
                </div>
                <table class="w-full text-left border-collapse text-xs">
                    <thead><tr class="border-b border-slate-800 text-slate-400"><th class="py-2">Data</th><th class="py-2">Descrição</th><th class="py-2">Parcela</th><th class="py-2">Status</th><th class="py-2 text-right">Valor</th></tr></thead>
                    <tbody id="stmtTableBody" class="divide-y divide-slate-800/70"></tbody>
                </table>
            </div>
            <div class="p-4 border-t border-slate-800 flex gap-3 justify-end no-print">
                <button onclick="exportStatementCSV()" class="px-4 py-2.5 rounded-xl bg-slate-800 text-slate-200 text-xs font-semibold">Baixar CSV</button>
                <button onclick="window.print()" class="px-5 py-2.5 rounded-xl bg-sky-600 text-white text-xs font-bold">Imprimir / PDF</button>
            </div>
        </div>
    </div>

    <!-- MODAL PERFIL -->
    <div id="profileModal" class="fixed inset-0 z-50 bg-slate-950/85 backdrop-blur-sm flex items-center justify-center p-4 hidden">
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 w-full max-w-md shadow-2xl">
            <div class="flex justify-between items-center mb-5">
                <h3 class="text-base font-bold text-white">Perfil do Usuário</h3>
                <button onclick="closeProfileModal()" class="w-8 h-8 rounded-xl bg-slate-800 text-slate-400 flex items-center justify-center"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form onsubmit="updateProfile(event)" class="space-y-4">
                <div>
                    <label class="block text-[11px] font-semibold text-slate-400 uppercase mb-1">Nome</label>
                    <input type="text" id="profNameInput" required class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 text-white text-sm">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-400 uppercase mb-1">Subtítulo / Perfil</label>
                    <input type="text" id="profRoleInput" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 text-white text-sm">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-400 uppercase mb-1">Orçamento Mensal (R$)</label>
                    <input type="number" step="0.01" min="0" id="profBudgetInput" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 text-white text-sm">
                </div>
                <button type="submit" class="w-full bg-emerald-600 text-white font-bold py-3 rounded-xl text-sm">Salvar Perfil</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/pt.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>
    <script>
        // Injeta a URL base oficial respeitando o APP_URL do .env
        window.APP_URL = "{{ config('app.url') }}";
    </script>
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        let monthPicker = null;
        let state = {
            user: null,
            transactions: [],
            filterMode: 'month',
            filterMonth: '',
            filterStartDate: '',
            filterEndDate: '',
            filterStatus: 'all',
            filterSearch: '',
            currentTxType: 'expense'
        };

        async function apiFetch(url, options = {}) {
            const headers = {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                ...(options.body ? { 'Content-Type': 'application/json' } : {}),
                ...(options.headers || {})
            };
            const response = await fetch(url, { ...options, headers, credentials: 'same-origin' });
            const data = await response.json().catch(() => ({}));
            if (!response.ok) {
                if (response.status === 401 && !url.includes('/login')) {
                    showAuthScreen();
                }
                throw new Error(data.message || data.error || 'Erro na requisição.');
            }
            return data;
        }

        document.addEventListener("DOMContentLoaded", async () => {
            initFlatpickr();
            initDefaultDates();
            await checkSession();
        });

        async function checkSession() {
            try {
                const res = await apiFetch(`${window.APP_URL}auth/me`);
                state.user = res.user;
                showAppScreen();
                await loadTransactions();
            } catch (e) {
                showAuthScreen();
            }
        }

        function showAuthScreen() {
            document.getElementById('authScreen').classList.remove('hidden');
            document.getElementById('appMainWrapper').classList.add('hidden');
            document.getElementById('appSidebar').classList.add('lg:hidden');
        }

        function showAppScreen() {
            document.getElementById('authScreen').classList.add('hidden');
            document.getElementById('appMainWrapper').classList.remove('hidden');
            document.getElementById('appSidebar').classList.remove('lg:hidden');
            updateProfileUI();
        }

        function switchAuthTab(tab) {
            const isLogin = tab === 'login';
            document.getElementById('loginForm').classList.toggle('hidden', !isLogin);
            document.getElementById('registerForm').classList.toggle('hidden', isLogin);
            document.getElementById('tabAuthLogin').className = isLogin ? "py-2 text-xs font-bold rounded-lg bg-slate-800 text-white" : "py-2 text-xs font-bold rounded-lg text-slate-400";
            document.getElementById('tabAuthRegister').className = !isLogin ? "py-2 text-xs font-bold rounded-lg bg-slate-800 text-white" : "py-2 text-xs font-bold rounded-lg text-slate-400";
        }

        async function handleLogin(e) {
            e.preventDefault();
            try {
                showToast('Realizando autenticação!', 'warning');
                const res = await apiFetch(`${window.APP_URL}auth/login`, {
                    method: 'POST',
                    body: JSON.stringify({
                        email: document.getElementById('loginEmail').value,
                        password: document.getElementById('loginPassword').value
                    })
                });
                state.user = res.user;
                window.location.reload();
            } catch (err) {
                showToast(err.message, 'error');
            }
        }

        async function handleRegister(e) {
            e.preventDefault();
            try {
                showToast('Criando usuário!', 'warning');
                const res = await apiFetch(`${window.APP_URL}auth/register`, {
                    method: 'POST',
                    body: JSON.stringify({
                        name: document.getElementById('regName').value,
                        email: document.getElementById('regEmail').value,
                        password: document.getElementById('regPassword').value,
                        monthly_budget: document.getElementById('regBudget').value
                    })
                });
                state.user = res.user;
                window.location.reload();
            } catch (err) {
                showToast(err.message, 'error');
            }
        }

        async function handleLogout() {
            showToast('Realizando logout!', 'warning');
            await apiFetch(`${window.APP_URL}auth/logout`, { method: 'POST' }).catch(() => {});
            state.user = null;
            state.transactions = [];
            window.location.reload();
        }

        async function loadTransactions() {
            const res = await apiFetch(`${window.APP_URL}transactions`);
            state.transactions = res.transactions || [];
            render();
        }

        async function saveTransaction(e) {
            e.preventDefault();
            try {
                await apiFetch(`${window.APP_URL}transactions`, {
                    method: 'POST',
                    body: JSON.stringify({
                        description: document.getElementById('txDescription').value,
                        category: document.getElementById('txCategory').value,
                        amount: document.getElementById('txAmount').value,
                        installments: document.getElementById('txInstallments').value,
                        due_date: document.getElementById('txDueDate').value,
                        type: state.currentTxType,
                        initial_status: document.getElementById('txInitialStatus').value
                    })
                });
                closeAddTransactionModal();
                document.getElementById('transactionForm').reset();
                setTransactionType('expense');
                initDefaultDates();
                await loadTransactions();
                showToast('Lançamento salvo!', 'success');
            } catch (err) {
                showToast(err.message, 'error');
            }
        }

        async function toggleInstallmentStatus(id) {
            try {
                showToast('Realizando operação...', 'success');
                await apiFetch(`${window.APP_URL}transactions/${id}/status`, { method: 'PATCH' });
                await loadTransactions();
            } catch (err) {
                showToast(err.message, 'error');
            }
        }

        async function deleteTransaction(groupUuid) {
            if (!confirm('Deseja excluir todas as parcelas deste grupo?')) return;
            try {
                await apiFetch(`${window.APP_URL}transactions/${groupUuid}`, { method: 'DELETE' });
                await loadTransactions();
                showToast('Excluído com sucesso.', 'info');
            } catch (err) {
                showToast(err.message, 'error');
            }
        }

        async function updateProfile(e) {
            e.preventDefault();
            try {
                const res = await apiFetch(`${window.APP_URL}profile`, {
                    method: 'POST',
                    body: JSON.stringify({
                        name: document.getElementById('profNameInput').value,
                        role_title: document.getElementById('profRoleInput').value,
                        monthly_budget: document.getElementById('profBudgetInput').value
                    })
                });
                state.user = res.user;
                updateProfileUI();
                closeProfileModal();
                render();
                showToast('Perfil atualizado!', 'success');
            } catch (err) {
                showToast(err.message, 'error');
            }
        }

        function updateProfileUI() {
            if (!state.user) return;
            const initial = state.user.name.charAt(0).toUpperCase();
            document.getElementById('mobileUserName').textContent = state.user.name;
            document.getElementById('mobileUserAvatar').textContent = initial;
            document.getElementById('sidebarUserName').textContent = state.user.name;
            document.getElementById('sidebarAvatar').textContent = initial;
            document.getElementById('sidebarUserRole').textContent = state.user.role_title || 'Conta Pessoal';
        }

        function initFlatpickr() {
            monthPicker = flatpickr("#filterMonthInput", {
                locale: "pt",
                defaultDate: new Date(),
                disableMobile: true,
                plugins: [new monthSelectPlugin({ shorthand: false, dateFormat: "Y-m", altFormat: "F \\d\\e Y", theme: "dark" })],
                onChange: (_, dateStr) => { state.filterMonth = dateStr; applyFilters(); }
            });
        }

        function initDefaultDates() {
            const now = new Date();
            const y = now.getFullYear(), m = String(now.getMonth() + 1).padStart(2, '0'), d = String(now.getDate()).padStart(2, '0');
            state.filterMonth = `${y}-${m}`;
            if (monthPicker) monthPicker.setDate(state.filterMonth, false);
            document.getElementById('txDueDate').value = `${y}-${m}-${d}`;
            document.getElementById('filterStartDate').value = `${y}-${m}-01`;
            document.getElementById('filterEndDate').value = `${y}-${m}-${new Date(y, now.getMonth() + 1, 0).getDate()}`;
        }

        function switchFilterMode(mode) {
            state.filterMode = mode;
            document.getElementById('containerMonthFilter').classList.toggle('hidden', mode !== 'month');
            document.getElementById('containerPeriodFilter').classList.toggle('hidden', mode === 'month');
            document.getElementById('tabFilterMonth').className = mode === 'month' ? "py-1.5 text-xs font-semibold rounded-lg bg-slate-800 text-white" : "py-1.5 text-xs font-semibold rounded-lg text-slate-400";
            document.getElementById('tabFilterPeriod').className = mode !== 'month' ? "py-1.5 text-xs font-semibold rounded-lg bg-slate-800 text-white" : "py-1.5 text-xs font-semibold rounded-lg text-slate-400";
            applyFilters();
        }

        function applyFilters() {
            state.filterSearch = document.getElementById('filterSearchInput').value.trim().toLowerCase();
            state.filterStartDate = document.getElementById('filterStartDate').value;
            state.filterEndDate = document.getElementById('filterEndDate').value;
            document.getElementById('activeFilterBadge').textContent = state.filterMode === 'month' ? formatMonthBadge(state.filterMonth) : 'Período';
            render();
        }

        function setStatusFilter(status) {
            state.filterStatus = status;
            ['all', 'pending', 'paid'].forEach(s => {
                document.getElementById('statusFilter' + s.charAt(0).toUpperCase() + s.slice(1)).className =
                    s === status ? "py-2 rounded-xl text-xs font-semibold bg-emerald-600 text-white" : "py-2 rounded-xl text-xs font-semibold bg-slate-800 text-slate-400";
            });
            render();
        }

        function resetFilters() {
            document.getElementById('filterSearchInput').value = '';
            state.filterSearch = '';
            initDefaultDates();
            setStatusFilter('all');
            switchFilterMode('month');
        }

        function getFilteredTransactions() {
            return state.transactions.filter(tx => {
                if (state.filterStatus !== 'all' && tx.status !== state.filterStatus) return false;
                if (state.filterSearch && !tx.description.toLowerCase().includes(state.filterSearch) && !tx.category.toLowerCase().includes(state.filterSearch)) return false;
                if (state.filterMode === 'month') return !state.filterMonth || tx.due_date.startsWith(state.filterMonth);
                return (!state.filterStartDate || tx.due_date >= state.filterStartDate) && (!state.filterEndDate || tx.due_date <= state.filterEndDate);
            });
        }

        function render() {
            const filtered = getFilteredTransactions();
            let pend = 0, pendCount = 0, paid = 0, exp = 0, inc = 0;

            filtered.forEach(tx => {
                if (tx.type === 'expense') {
                    exp += parseFloat(tx.amount);
                    if (tx.status === 'pending') { pend += parseFloat(tx.amount); pendCount++; }
                    else paid += parseFloat(tx.amount);
                } else {
                    inc += parseFloat(tx.amount);
                }
            });

            document.getElementById('kpiPending').textContent = formatCurrency(pend);
            document.getElementById('kpiPendingCount').textContent = `${pendCount} pendentes`;
            document.getElementById('kpiExpenses').textContent = formatCurrency(exp);
            document.getElementById('kpiPaid').textContent = formatCurrency(paid);
            
            const paidPct = exp > 0 ? Math.round((paid / exp) * 100) : 0;
            document.getElementById('kpiPaidPercent').textContent = `${paidPct}%`;
            document.getElementById('kpiPaidBar').style.width = `${paidPct}%`;

            const budget = parseFloat(state.user?.monthly_budget || 0);
            if (budget > 0) {
                const bPct = Math.round((exp / budget) * 100);
                document.getElementById('kpiBudgetLabel').textContent = formatCurrency(budget);
                document.getElementById('kpiBudgetPercent').textContent = `${bPct}%`;
                document.getElementById('kpiBudgetBar').style.width = `${Math.min(bPct, 100)}%`;
            }

            const bal = inc - exp;
            const balEl = document.getElementById('kpiBalance');
            balEl.textContent = formatCurrency(bal);
            balEl.className = bal >= 0 ? "text-2xl font-extrabold text-emerald-400" : "text-2xl font-extrabold text-red-400";
            document.getElementById('kpiIncome').textContent = formatCurrency(inc);
            document.getElementById('transactionCountBadge').textContent = `${filtered.length} itens`;

            const list = document.getElementById('transactionsList');
            if (!filtered.length) {
                list.innerHTML = `<div class="bg-slate-900/50 border border-slate-800 rounded-2xl p-8 text-center text-xs text-slate-400">Nenhuma transação encontrada.</div>`;
                return;
            }

            list.innerHTML = filtered.map(tx => {
                const isPaid = tx.status === 'paid';
                const isInc = tx.type === 'income';
                return `
                    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-3.5 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <button onclick="toggleInstallmentStatus(${tx.id})" class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 ${isPaid ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/40' : 'bg-slate-800 text-slate-400 border border-slate-700'}">
                                <i class="fa-solid ${isPaid ? 'fa-check' : 'fa-clock'} text-sm"></i>
                            </button>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <h4 class="text-xs sm:text-sm font-bold text-white truncate">${escapeHtml(tx.description)}</h4>
                                    ${tx.total_installments > 1 ? `<span class="text-[10px] bg-slate-800 text-slate-300 px-1.5 py-0.5 rounded">${tx.current_installment}/${tx.total_installments}</span>` : ''}
                                </div>
                                <p class="text-[11px] text-slate-400 mt-0.5">${formatDateBR(tx.due_date)} • ${escapeHtml(tx.category)} • <span class="${isPaid ? 'text-emerald-400' : 'text-amber-400'}">${isPaid ? 'Pago' : 'Pendente'}</span></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 shrink-0">
                            <span class="text-xs sm:text-sm font-extrabold ${isInc ? 'text-emerald-400' : 'text-slate-100'}">${isInc ? '+' : '-'} ${formatCurrency(tx.amount)}</span>
                            <button onclick="deleteTransaction('${tx.group_uuid}')" class="text-slate-500 hover:text-red-400 p-1"><i class="fa-solid fa-trash-can text-xs"></i></button>
                        </div>
                    </div>
                `;
            }).join('');
        }

        function openStatementModal() {
            const filtered = getFilteredTransactions();
            document.getElementById('stmtUserName').textContent = state.user?.name || '';
            document.getElementById('stmtPeriod').textContent = state.filterMode === 'month' ? formatMonthBadge(state.filterMonth) : `${formatDateBR(state.filterStartDate)} a ${formatDateBR(state.filterEndDate)}`;
            document.getElementById('stmtGeneratedDate').textContent = new Date().toLocaleString('pt-BR');

            let exp = 0, paid = 0, pend = 0, inc = 0;
            document.getElementById('stmtTableBody').innerHTML = filtered.map(tx => {
                const amt = parseFloat(tx.amount);
                if (tx.type === 'expense') { exp += amt; tx.status === 'paid' ? paid += amt : pend += amt; }
                else inc += amt;
                return `<tr><td class="py-2">${formatDateBR(tx.due_date)}</td><td class="py-2">${escapeHtml(tx.description)}</td><td class="py-2">${tx.current_installment}/${tx.total_installments}</td><td class="py-2">${tx.status === 'paid' ? 'Pago' : 'Pendente'}</td><td class="py-2 text-right font-bold">${formatCurrency(amt)}</td></tr>`;
            }).join('');

            document.getElementById('stmtTotalExpense').textContent = formatCurrency(exp);
            document.getElementById('stmtTotalPaid').textContent = formatCurrency(paid);
            document.getElementById('stmtTotalPending').textContent = formatCurrency(pend);
            document.getElementById('stmtBalance').textContent = formatCurrency(inc - exp);
            document.getElementById('statementModal').classList.remove('hidden');
        }

        function exportStatementCSV() {
            const filtered = getFilteredTransactions();
            let csv = "\uFEFFVencimento;Descrição;Categoria;Parcela;Status;Valor\n";
            filtered.forEach(tx => {
                csv += `${formatDateBR(tx.due_date)};"${tx.description.replace(/"/g, '""')}";"${tx.category}";${tx.current_installment}/${tx.total_installments};${tx.status === 'paid' ? 'Pago' : 'Pendente'};${parseFloat(tx.amount).toFixed(2).replace('.', ',')}\n`;
            });
            const a = document.createElement('a');
            a.href = URL.createObjectURL(new Blob([csv], { type: 'text/csv;charset=utf-8;' }));
            a.download = `extrato-${state.filterMonth}.csv`;
            a.click();
        }

        function setTransactionType(type) {
            state.currentTxType = type;
            document.getElementById('btnTypeExpense').className = type === 'expense' ? "py-2.5 text-xs font-bold rounded-xl bg-red-500/20 text-red-400 border border-red-500/30" : "py-2.5 text-xs font-bold rounded-xl text-slate-400";
            document.getElementById('btnTypeIncome').className = type === 'income' ? "py-2.5 text-xs font-bold rounded-xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/30" : "py-2.5 text-xs font-bold rounded-xl text-slate-400";
        }

        function openAddTransactionModal() { document.getElementById('transactionModal').classList.remove('hidden'); }
        function closeAddTransactionModal() { document.getElementById('transactionModal').classList.add('hidden'); }
        function closeStatementModal() { document.getElementById('statementModal').classList.add('hidden'); }
        function openProfileModal() {
            document.getElementById('profNameInput').value = state.user?.name || '';
            document.getElementById('profRoleInput').value = state.user?.role_title || '';
            document.getElementById('profBudgetInput').value = state.user?.monthly_budget || 0;
            document.getElementById('profileModal').classList.remove('hidden');
        }
        function closeProfileModal() { document.getElementById('profileModal').classList.add('hidden'); }

        function formatCurrency(v) { return (v || 0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }); }
        function formatDateBR(d) { const [y, m, day] = d.split('-'); return `${day}/${m}/${y}`; }
        function formatMonthBadge(ym) {
            const [y, m] = ym.split('-');
            const names = ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];
            return `${names[parseInt(m, 10) - 1]} / ${y}`;
        }
        function escapeHtml(s) { return String(s).replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/"/g,"&quot;").replace(/'/g,"&#039;"); }
        function showToast(msg, type) {
            const t = document.createElement('div');
            t.className = `fixed top-5 right-5 z-50 px-4 py-3 rounded-2xl shadow-2xl text-xs font-semibold text-white animate-fade-in ${type === 'error' ? 'bg-red-600' : 'bg-emerald-600'}`;
            t.textContent = msg;
            document.body.appendChild(t);
            setTimeout(() => t.remove(), 3000);
        }
    </script>
</body>
</html>