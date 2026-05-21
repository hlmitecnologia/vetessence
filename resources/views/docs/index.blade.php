@extends('layouts.adminlte')
@section('title', 'Documentação')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-book"></i> Documentação</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($sidebar as $section)
                            <div class="list-group-item">
                                <strong><i class="fas {{ $section['icon'] }} mr-2"></i>{{ $section['title'] }}</strong>
                            </div>
                            @foreach($section['pages'] as $page)
                                <a href="{{ route($page['route'], $page['params']) }}"
                                   class="list-group-item list-group-item-action pl-4 {{ request()->route('section') == ($page['params']['section'] ?? null) ? 'active' : '' }}">
                                    {{ $page['label'] }}
                                </a>
                            @endforeach
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="card">
                <div class="card-body docs-content">
                    {!! $content !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
(function() {
    function bindSvgLightbox() {
        document.querySelectorAll('.docs-content img[src$=".svg"]').forEach(function(img) {
            img.style.cursor = 'pointer';
            img.title = img.title || 'Clique para ampliar';
            if (img.dataset.lightboxBound) return;
            img.dataset.lightboxBound = '1';
            img.addEventListener('click', function() {
                var modal = document.createElement('div');
                modal.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.9);z-index:9999;display:flex;align-items:center;justify-content:center;cursor:zoom-out;';
                var closeBtn = document.createElement('button');
                closeBtn.innerHTML = '&times;';
                closeBtn.style.cssText = 'position:absolute;top:20px;right:30px;color:#fff;font-size:40px;background:none;border:none;cursor:pointer;z-index:10000;font-weight:bold;';
                closeBtn.addEventListener('click', function(e) { e.stopPropagation(); modal.remove(); });
                var imgEl = document.createElement('img');
                imgEl.src = this.src;
                imgEl.style.cssText = 'max-width:95vw;max-height:95vh;object-fit:contain;background:#fff;border-radius:4px;box-shadow:0 0 30px rgba(0,0,0,0.5);';
                modal.appendChild(imgEl);
                modal.appendChild(closeBtn);
                modal.addEventListener('click', function() { modal.remove(); });
                document.body.appendChild(modal);
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bindSvgLightbox);
    } else {
        bindSvgLightbox();
    }

    document.addEventListener('livewire:initialized', bindSvgLightbox);
})();
@endpush

@push('styles')
<style>
.docs-content h1 { font-size: 1.75rem; font-weight: 700; margin-bottom: 1rem; border-bottom: 2px solid #e5e7eb; padding-bottom: 0.5rem; }
.docs-content h2 { font-size: 1.35rem; font-weight: 600; margin-top: 1.5rem; margin-bottom: 0.75rem; }
.docs-content h3 { font-size: 1.15rem; font-weight: 600; margin-top: 1.25rem; margin-bottom: 0.5rem; }
.docs-content h4 { font-size: 1.05rem; font-weight: 600; margin-top: 1.5rem; margin-bottom: 0.75rem; }
.docs-content p { margin-bottom: 0.75rem; line-height: 1.6; }
.docs-content ul, .docs-content ol { margin-bottom: 0.75rem; padding-left: 1.5rem; }
.docs-content li { margin-bottom: 0.25rem; }
.docs-content code { background: #f3f4f6; padding: 0.1rem 0.3rem; border-radius: 0.25rem; font-size: 0.875rem; }
.docs-content pre { background: #1f2937; color: #f3f4f6; padding: 1rem; border-radius: 0.5rem; overflow-x: auto; margin-bottom: 1rem; }
.docs-content pre code { background: transparent; color: inherit; padding: 0; }
.docs-content table { width: 100%; margin-bottom: 1rem; border-collapse: collapse; }
.docs-content th, .docs-content td { border: 1px solid #d1d5db; padding: 0.5rem; text-align: left; }
.docs-content th { background: #f9fafb; font-weight: 600; }
.docs-content hr { margin: 1.5rem 0; border-color: #e5e7eb; }
.docs-content a { color: #4f46e5; text-decoration: underline; }
.docs-content a:hover { color: #4338ca; }
.docs-content blockquote { border-left: 4px solid #6366f1; padding-left: 1rem; margin-left: 0; color: #6b7280; }
.docs-content strong { font-weight: 600; }
.docs-content img { max-width: 100%; height: auto; }
.docs-content svg { max-width: 100%; height: auto; }
</style>
@endpush
