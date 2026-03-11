@extends(isset($blogPlatform) && $blogPlatform == 'app' ? 'web-views.blogs.blog-layouts' : 'layouts.front-end.app')

@section('title', translate('Popular_Blogs'))

@push('css_or_js')
    @if(isset($blogPlatform) && $blogPlatform == 'app')
        <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/app-blog.css') }}"/>
    @endif

    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --ink:         #0d0f14;
            --ink-soft:    #3d4452;
            --muted:       #8892a4;
            --border:      #eaedf2;
            --accent:      #1d6ef5;
            --accent-glow: rgba(29,110,245,.10);
            --bg:          #f5f7fb;
            --surface:     #ffffff;
            --radius-lg:   18px;
            --radius-md:   12px;
            --radius-sm:   8px;
            --shadow-sm:   0 1px 4px rgba(0,0,0,.06);
            --shadow-md:   0 4px 20px rgba(0,0,0,.08);
            --font-display:'Cormorant Garamond', Georgia, serif;
            --font-body:   'Plus Jakarta Sans', system-ui, sans-serif;
        }

        .pop-root *, .pop-root *::before, .pop-root *::after { box-sizing: border-box; }
        .pop-root {
            background: var(--bg);
            min-height: 100vh;
            padding-bottom: 80px;
        }

        /* ═══════════════ HERO HEADER ═══════════════ */
        .pop-hero {
            background: linear-gradient(135deg, #0a0f1e 0%, #0f1f4a 50%, #0d1b3e 100%);
            padding: clamp(48px, 8vw, 88px) 0 clamp(36px, 5vw, 64px);
            position: relative;
            overflow: hidden;
        }
        /* decorative blobs */
        .pop-hero::before {
            content: '';
            position: absolute;
            top: -80px; left: -80px;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(29,110,245,.18), transparent 65%);
            pointer-events: none;
        }
        .pop-hero::after {
            content: '';
            position: absolute;
            bottom: -60px; right: -60px;
            width: 320px; height: 320px;
            background: radial-gradient(circle, rgba(96,165,250,.12), transparent 65%);
            pointer-events: none;
        }
        .pop-hero__inner {
            position: relative;
            z-index: 1;
            text-align: center;
        }
        .pop-hero__eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(29,110,245,.15);
            border: 1px solid rgba(29,110,245,.3);
            color: #93c5fd;
            font-family: var(--font-body);
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .14em;
            text-transform: uppercase;
            padding: 6px 16px;
            border-radius: 30px;
            margin-bottom: 20px;
        }
        .pop-hero__eyebrow::before {
            content: '';
            width: 6px; height: 6px;
            background: #60a5fa;
            border-radius: 50%;
        }
        .pop-hero__title {
            font-family: var(--font-display);
            font-size: clamp(32px, 5vw, 58px);
            font-weight: 700;
            color: #fff;
            margin: 0 0 16px;
            line-height: 1.15;
            letter-spacing: -.01em;
        }
        .pop-hero__sub {
            font-family: var(--font-body);
            font-size: 15px;
            color: rgba(255,255,255,.5);
            max-width: 480px;
            margin: 0 auto 36px;
            line-height: 1.6;
        }

        /* ── Search Bar ── */
        .pop-search-wrap {
            max-width: 540px;
            margin: 0 auto;
        }
        .pop-search {
            display: flex;
            align-items: center;
            background: rgba(255,255,255,.08);
            border: 1.5px solid rgba(255,255,255,.15);
            border-radius: 50px;
            padding: 6px 6px 6px 22px;
            gap: 10px;
            backdrop-filter: blur(8px);
            transition: border-color .25s, background .25s;
        }
        .pop-search:focus-within {
            border-color: rgba(29,110,245,.6);
            background: rgba(255,255,255,.12);
        }
        .pop-search input {
            flex: 1;
            background: none;
            border: none;
            outline: none;
            font-family: var(--font-body);
            font-size: 14px;
            color: #fff;
            min-width: 0;
        }
        .pop-search input::placeholder { color: rgba(255,255,255,.4); }
        .pop-search__btn {
            flex-shrink: 0;
            display: flex;
            align-items: center;
            gap: 7px;
            background: var(--accent);
            color: #fff;
            border: none;
            border-radius: 50px;
            padding: 9px 20px;
            font-family: var(--font-body);
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: background .2s, transform .15s;
        }
        .pop-search__btn:hover { background: #1557cc; transform: scale(1.02); }

        /* ── Clear search tag ── */
        .pop-clear {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 14px;
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.15);
            color: rgba(255,255,255,.7);
            font-family: var(--font-body);
            font-size: 12px;
            font-weight: 500;
            padding: 5px 12px;
            border-radius: 30px;
            cursor: pointer;
            transition: background .2s;
        }
        .pop-clear:hover { background: rgba(255,255,255,.15); color: #fff; }

        /* ═══════════════ BODY ═══════════════ */
        .pop-body { padding-top: 36px; }

        /* ── Category Pills ── */
        .pop-cats {
            position: relative;
            margin-bottom: 32px;
        }
        .pop-cats__scroll {
            display: flex;
            align-items: center;
            gap: 8px;
            overflow-x: auto;
            scrollbar-width: none;
            padding: 4px 2px 8px;
        }
        .pop-cats__scroll::-webkit-scrollbar { display: none; }

        .pop-cats__pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
            padding: 8px 18px;
            border-radius: 50px;
            border: 1.5px solid var(--border);
            background: var(--surface);
            font-family: var(--font-body);
            font-size: 13px;
            font-weight: 500;
            color: var(--ink-soft);
            text-decoration: none;
            transition: all .2s;
            flex-shrink: 0;
            box-shadow: var(--shadow-sm);
        }
        .pop-cats__pill:hover {
            border-color: var(--accent);
            color: var(--accent);
            background: var(--accent-glow);
        }
        .pop-cats__pill.active {
            background: var(--accent);
            border-color: var(--accent);
            color: #fff;
            box-shadow: 0 4px 14px rgba(29,110,245,.3);
        }
        .pop-cats__pill-count {
            background: rgba(0,0,0,.08);
            border-radius: 30px;
            padding: 1px 7px;
            font-size: 11px;
            font-weight: 600;
        }
        .pop-cats__pill.active .pop-cats__pill-count { background: rgba(255,255,255,.2); }

        /* scroll arrows */
        .pop-cats__arrow {
            position: absolute;
            top: 50%; transform: translateY(-60%);
            width: 32px; height: 32px;
            display: flex; align-items: center; justify-content: center;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 50%;
            box-shadow: var(--shadow-md);
            cursor: pointer;
            z-index: 2;
            transition: background .2s, transform .2s;
        }
        .pop-cats__arrow:hover { background: var(--accent); border-color: var(--accent); color: #fff; transform: translateY(-60%) scale(1.1); }
        .pop-cats__arrow--left  { left: -16px; }
        .pop-cats__arrow--right { right: -16px; }

        /* ── Search result count ── */
        .pop-result-count {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 18px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            margin-bottom: 24px;
            font-family: var(--font-body);
            font-size: 13.5px;
            color: var(--ink-soft);
        }
        .pop-result-count strong { color: var(--ink); font-weight: 700; }
        .pop-result-count i { color: var(--accent); }

        /* ── Section label ── */
        .pop-section-label {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 24px;
        }
        .pop-section-label__line {
            flex: 1;
            height: 1px;
            background: var(--border);
        }
        .pop-section-label__text {
            font-family: var(--font-body);
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--muted);
            white-space: nowrap;
        }

        /* ── Pagination override ── */
        .pop-pagination {
            display: flex;
            justify-content: center;
            margin-top: 16px;
            padding-top: 32px;
            border-top: 1px solid var(--border);
        }
        .pop-pagination .pagination { gap: 6px; }
        .pop-pagination .page-item .page-link {
            border-radius: var(--radius-sm) !important;
            border: 1.5px solid var(--border);
            color: var(--ink-soft);
            font-family: var(--font-body);
            font-size: 13px;
            font-weight: 500;
            padding: 7px 13px;
            transition: all .2s;
        }
        .pop-pagination .page-item.active .page-link {
            background: var(--accent);
            border-color: var(--accent);
            color: #fff;
            box-shadow: 0 4px 12px rgba(29,110,245,.3);
        }
        .pop-pagination .page-item .page-link:hover {
            border-color: var(--accent);
            color: var(--accent);
            background: var(--accent-glow);
        }

        /* ── Reveal animation ── */
        .pop-reveal {
            opacity: 0;
            transform: translateY(22px);
            animation: popReveal .55s cubic-bezier(.22,.61,.36,1) forwards;
        }
        @keyframes popReveal { to { opacity: 1; transform: translateY(0); } }
        .pop-reveal:nth-child(1) { animation-delay: .04s; }
        .pop-reveal:nth-child(2) { animation-delay: .10s; }
        .pop-reveal:nth-child(3) { animation-delay: .16s; }
        .pop-reveal:nth-child(4) { animation-delay: .22s; }
        .pop-reveal:nth-child(5) { animation-delay: .28s; }
        .pop-reveal:nth-child(6) { animation-delay: .34s; }
    </style>
@endpush

@section('content')
    @include('web-views.blogs.partials._app-blog-preloader')

    <div class="pop-root">

        {{-- ══════════════ HERO ══════════════ --}}
        <div class="pop-hero">
            <div class="container">
                <div class="pop-hero__inner">
                    <div class="pop-hero__eyebrow">{{ translate('Trending Now') }}</div>
                    <h1 class="pop-hero__title">{{ translate('Popular_Blogs') }}</h1>
                    <p class="pop-hero__sub">{{ translate('Discover the most-read stories and insights from our community') }}</p>

                    {{-- Search --}}
                    <div class="pop-search-wrap">
                        <form action="{{ isset($blogPlatform) && $blogPlatform == 'app' ? route('app.blog.popular-blog', ['locale' => request('locale'), 'theme' => request('theme')]) : route('frontend.blog.popular-blog') }}"
                              id="popular-search-form" method="get">
                            <input type="hidden" name="locale" value="{{ request('locale') }}">
                            <input type="hidden" name="theme"  value="{{ request('theme') }}">
                            <input type="hidden" name="category" value="{{ request('category') }}">

                            <div class="pop-search">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.4)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" flex-shrink="0">
                                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                                </svg>
                                <input type="text"
                                       name="search"
                                       value="{{ request('search') }}"
                                       placeholder="{{ translate('Search_Blog') }}"
                                       required>
                                <button type="submit" class="pop-search__btn">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                                    </svg>
                                    {{ translate('Search') }}
                                </button>
                            </div>
                        </form>

                        @if(request('search'))
                            <div class="text-center mt-2">
                                <span class="pop-clear clear-all-search-popular">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                                    {{ translate('Clear_Search') }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- ══════════════ BODY ══════════════ --}}
        <div class="pop-body">
            <div class="container">

                {{-- Category Pills --}}
                <div class="pop-cats">
                    <div class="pop-cats__scroll" id="popCatScroll">
                        <a href="{{ isset($blogPlatform) && $blogPlatform == 'app' ? route('app.blog.popular-blog', ['locale' => request('locale'), 'theme' => request('theme')]) : route('frontend.blog.popular-blog') }}"
                           class="pop-cats__pill {{ request('category') == '' ? 'active' : '' }}">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                            {{ translate('All') }}
                        </a>
                        @foreach($blogCategoryList as $blogCategory)
                            <a href="{{ isset($blogPlatform) && $blogPlatform == 'app' ? route('app.blog.popular-blog', ['category' => $blogCategory?->name, 'locale' => request('locale'), 'theme' => request('theme')]) : route('frontend.blog.popular-blog', ['category' => $blogCategory?->name]) }}"
                               class="pop-cats__pill {{ request('category') == $blogCategory?->name ? 'active' : '' }}">
                                {{ Str::limit($blogCategory->name, 25) }}
                            </a>
                        @endforeach
                    </div>
                    <button class="pop-cats__arrow pop-cats__arrow--left" id="popCatPrev" aria-label="Previous">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="m15 18-6-6 6-6"/></svg>
                    </button>
                    <button class="pop-cats__arrow pop-cats__arrow--right" id="popCatNext" aria-label="Next">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="m9 18 6-6-6-6"/></svg>
                    </button>
                </div>

                {{-- Search Result Count --}}
                @if(request('search'))
                    <div class="pop-result-count">
                        <i class="fa fa-search"></i>
                        <span>
                            <strong>{{ $popularBlogList->count() }}</strong>
                            {{ translate('Search_Result_Found') }}
                            {{ translate('for') }} "<strong>{{ request('search') }}</strong>"
                        </span>
                    </div>
                @endif

                {{-- No results --}}
                @if($popularBlogList->total() <= 0 && !empty(request('search')))
                    @include('web-views.blogs.partials._no-result-found')
                @elseif($popularBlogList->total() <= 0)
                    @include('web-views.blogs.partials._no-blog-found')
                @endif

                {{-- Section label --}}
                @if($popularBlogList->total() > 0)
                    <div class="pop-section-label">
                        <div class="pop-section-label__line"></div>
                        <span class="pop-section-label__text">
                            {{ $popularBlogList->total() }} {{ translate('Articles') }}
                        </span>
                        <div class="pop-section-label__line"></div>
                    </div>
                @endif

                {{-- Blog Grid --}}
                <div class="row g-4 mb-4">
                    @foreach($popularBlogList as $blogItem)
                        <div class="col-lg-4 col-md-6 pop-reveal">
                            @include('web-views.blogs.partials._single-blog-card', ['blogItem' => $blogItem])
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if($popularBlogList->hasPages())
                    <div class="pop-pagination">
                        {!! $popularBlogList->links() !!}
                    </div>
                @endif

            </div>
        </div>

    </div>
@endsection

@push('script')
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/blog.js') }}"></script>
    <script>
        // Category scroll arrows
        (function () {
            const scroll = document.getElementById('popCatScroll');
            const prev   = document.getElementById('popCatPrev');
            const next   = document.getElementById('popCatNext');
            if (!scroll) return;

            const updateArrows = () => {
                if (prev) prev.style.opacity = scroll.scrollLeft > 8 ? '1' : '0';
                if (next) next.style.opacity = scroll.scrollLeft < scroll.scrollWidth - scroll.clientWidth - 8 ? '1' : '0';
            };

            if (prev) prev.addEventListener('click', () => { scroll.scrollBy({ left: -200, behavior: 'smooth' }); });
            if (next) next.addEventListener('click', () => { scroll.scrollBy({ left:  200, behavior: 'smooth' }); });
            scroll.addEventListener('scroll', updateArrows, { passive: true });
            updateArrows();
        })();
    </script>
@endpush
