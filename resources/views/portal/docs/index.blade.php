<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php $currentLabel = 'Manual'; foreach($sidebar as $item) { if($item['page'] === $currentPage) { $currentLabel = $item['label']; break; } } @endphp
    <title>{{ $currentPage === 'index' ? 'Manual do Tutor' : $currentLabel }} - {{ $clinicName }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .docs-content h1 { font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem; border-bottom: 2px solid #e5e7eb; padding-bottom: 0.5rem; color: #1f2937; }
        .docs-content h2 { font-size: 1.25rem; font-weight: 600; margin-top: 1.5rem; margin-bottom: 0.75rem; color: #374151; }
        .docs-content h3 { font-size: 1.1rem; font-weight: 600; margin-top: 1.25rem; margin-bottom: 0.5rem; color: #374151; }
        .docs-content h4 { font-size: 1.05rem; font-weight: 600; margin-top: 1rem; margin-bottom: 0.5rem; }
        .docs-content p { margin-bottom: 0.75rem; line-height: 1.7; color: #4b5563; }
        .docs-content ul, .docs-content ol { margin-bottom: 0.75rem; padding-left: 1.5rem; }
        .docs-content li { margin-bottom: 0.25rem; color: #4b5563; }
        .docs-content code { background: #f3f4f6; padding: 0.1rem 0.3rem; border-radius: 0.25rem; font-size: 0.875rem; color: #dc2626; }
        .docs-content pre { background: #1f2937; color: #f3f4f6; padding: 1rem; border-radius: 0.5rem; overflow-x: auto; margin-bottom: 1rem; }
        .docs-content pre code { background: transparent; color: inherit; padding: 0; }
        .docs-content table { width: 100%; margin-bottom: 1rem; border-collapse: collapse; }
        .docs-content th, .docs-content td { border: 1px solid #d1d5db; padding: 0.5rem 0.75rem; text-align: left; font-size: 0.9rem; }
        .docs-content th { background: #f9fafb; font-weight: 600; color: #374151; }
        .docs-content hr { margin: 1.5rem 0; border-color: #e5e7eb; }
        .docs-content a { color: #2563eb; text-decoration: underline; }
        .docs-content a:hover { color: #1d4ed8; }
        .docs-content blockquote { border-left: 4px solid #6366f1; padding-left: 1rem; margin-left: 0; color: #6b7280; background: #f9fafb; padding: 0.75rem 1rem; border-radius: 0.5rem; }
        .docs-content strong { font-weight: 600; color: #1f2937; }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased">
    <nav class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <a href="{{ route('portal.docs.index') }}" class="text-xl font-bold text-blue-600 flex items-center gap-2">
                    <i class="fas fa-book"></i> Manual do Tutor
                </a>
                <div class="flex items-center gap-4">
                    <a href="{{ route('portal.dashboard') }}" class="text-sm text-gray-500 hover:text-blue-600 transition flex items-center gap-1">
                        <i class="fas fa-arrow-left"></i> Voltar ao Portal
                    </a>
                    <form method="POST" action="{{ route('portal.logout') }}">
                        @csrf
                        <button class="text-sm text-gray-500 hover:text-red-600 transition">
                            <i class="fas fa-sign-out-alt mr-1"></i>Sair
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex flex-col lg:flex-row gap-6">
            <aside class="lg:w-72 flex-shrink-0">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden sticky top-20">
                    <div class="p-4 bg-gray-50 border-b border-gray-200">
                        <h2 class="font-semibold text-gray-800 text-sm flex items-center gap-2">
                            <i class="fas fa-list"></i> Conteúdo
                        </h2>
                    </div>
                    <div class="p-2">
                        @foreach($sidebar as $item)
                            <a href="{{ route('portal.docs.show', $item['page']) }}"
                               class="block px-3 py-2 rounded-lg text-sm transition {{ $currentPage === $item['page'] ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100' }}">
                                {{ $item['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </aside>

            <main class="flex-1 min-w-0">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 sm:p-8 docs-content">
                    {!! $html !!}
                </div>
            </main>
        </div>
    </div>
</body>
</html>
