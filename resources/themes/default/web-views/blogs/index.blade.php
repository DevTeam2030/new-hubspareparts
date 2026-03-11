@extends(isset($blogPlatform) && $blogPlatform == 'app' ? 'web-views.blogs.blog-layouts' : 'layouts.front-end.app')

@section('title', $blogTitle != '' ? $blogTitle : translate('Blogs'))

@push('css_or_js')
    @if(isset($blogPlatform) && $blogPlatform == 'app')
        <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/app-blog.css') }}"/>
    @endif

    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,600;0,700;1,600&family=Plus+Jakarta+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --ink:         #0d0f14;
            --ink-soft:    #3d4452;
            --muted:       #8892a4;
            --border:      #eaedf2;
            --accent:      #1d6ef5;
            --accent-glow: rgba(29,110,245,.10);
            --accent-dk:   #1557cc;
            --bg:          #f5f7fb;
            --surface:     #ffffff;
            --radius-lg:   18px;
            --radius-md:   12px;
            --radius-sm:   8px;
            --shadow-sm:   0 1px 4px rgba(0,0,0,.06);
            --shadow-md:   0 4px 20px rgba(0,0,0,.08);
            --shadow-lg:   0 12px 40px rgba(0,0,0,.10);
            --font-display:'Cormorant Garamond', Georgia, serif;
            --font-body:   'Plus Jakarta Sans', system-ui, sans-serif;
        }

        .bi-root *, .bi-root *::before, .bi-root *::after { box-sizing: border-box; }
        .bi-root {
            background: var(--bg);
            min-height: 100vh;
            padding-bottom: 80px;
        }

        /* ═══════════════════════════════════
           HERO
        ═══════════════════════════════════ */
        .bi-hero {
            background: linear-gradient(135deg, #070c1c 0%, #0f1f4a 55%, #0a1530 100%);
            padding: clamp(52px, 8vw, 96px) 0 clamp(40px, 6vw, 72px);
            position: relative;
            overflow: hidden;
        }
        .bi-hero::before {
            content: '';
            position: absolute; top: -100px; right: -60px;
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(29,110,245,.15), transparent 60%);
            pointer-events: none;
        }
        .bi-hero::after {
            content: '';
            position: absolute; bottom: -80px; left: -40px;
            width: 380px; height: 380px;
            background: radial-gradient(circle, rgba(96,165,250,.10), transparent 60%);
            pointer-events: none;
        }
        /* subtle grid overlay */
        .bi-hero__grid {
            position: absolute; inset: 0; z-index: 0;
            background-image:
                linear-gradient(rgba(255,255,255,.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.03) 1px, transparent 1px);
            background-size: 48px 48px;
            pointer-events: none;
        }
        .bi-hero__inner {
            position: relative; z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        .bi-hero__eyebrow {
            display: inline-flex; align-items: center; gap: 8px;
            background: rgba(29,110,245,.15);
            border: 1px solid rgba(29,110,245,.3);
            color: #93c5fd;
            font-family: var(--font-body);
            font-size: 11px; font-weight: 600;
            letter-spacing: .14em; text-transform: uppercase;
            padding: 6px 16px; border-radius: 30px;
            margin-bottom: 20px;
        }
        .bi-hero__eyebrow::before {
            content: '';
            width: 6px; height: 6px;
            background: #60a5fa; border-radius: 50%;
        }
        .bi-hero__title {
            font-family: var(--font-display);
            font-size: clamp(34px, 5.5vw, 64px);
            font-weight: 700;
            color: #fff;
            margin: 0 0 14px;
            line-height: 1.12;
            letter-spacing: -.01em;
        }
        .bi-hero__title em {
            font-style: italic;
            color: #93c5fd;
        }
        .bi-hero__sub {
            font-family: var(--font-body);
            font-size: 15px; color: rgba(255,255,255,.5);
            max-width: 500px; margin: 0 auto 40px;
            line-height: 1.65;
        }

        /* ── Search ── */
        .bi-search-wrap { max-width: 560px; width: 100%; }
        .bi-search {
            display: flex; align-items: center;
            background: rgba(255,255,255,.08);
            border: 1.5px solid rgba(255,255,255,.15);
            border-radius: 50px;
            padding: 6px 6px 6px 22px;
            gap: 10px;
            backdrop-filter: blur(8px);
            transition: border-color .25s, background .25s;
        }
        .bi-search:focus-within {
            border-color: rgba(29,110,245,.55);
            background: rgba(255,255,255,.11);
        }
        .bi-search input {
            flex: 1; background: none; border: none; outline: none;
            font-family: var(--font-body); font-size: 14px; color: #fff; min-width: 0;
        }
        .bi-search input::placeholder { color: rgba(255,255,255,.38); }
        .bi-search__btn {
            display: flex; align-items: center; gap: 7px;
            background: var(--accent); color: #fff; border: none;
            border-radius: 50px; padding: 9px 22px;
            font-family: var(--font-body); font-size: 13px; font-weight: 600;
            cursor: pointer; transition: background .2s, transform .15s; flex-shrink: 0;
        }
        .bi-search__btn:hover { background: var(--accent-dk); transform: scale(1.02); }

        .bi-clear {
            display: inline-flex; align-items: center; gap: 6px;
            margin-top: 12px;
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.14);
            color: rgba(255,255,255,.65);
            font-family: var(--font-body); font-size: 12px; font-weight: 500;
            padding: 5px 13px; border-radius: 30px; cursor: pointer;
            transition: background .2s;
        }
        .bi-clear:hover { background: rgba(255,255,255,.15); color: #fff; }

        /* ── Hero stats row ── */
        .bi-stats {
            display: flex; gap: 32px; margin-top: 36px;
            justify-content: center; flex-wrap: wrap;
        }
        .bi-stat {
            display: flex; flex-direction: column; align-items: center;
            gap: 4px;
        }
        .bi-stat__num {
            font-family: var(--font-display);
            font-size: 28px; font-weight: 700; color: #fff;
            line-height: 1;
        }
        .bi-stat__label {
            font-family: var(--font-body);
            font-size: 11px; color: rgba(255,255,255,.45);
            letter-spacing: .06em; text-transform: uppercase;
        }
        .bi-stat-divider {
            width: 1px; background: rgba(255,255,255,.12);
            align-self: stretch; margin: 4px 0;
        }

        /* ═══════════════════════════════════
           BODY LAYOUT
        ═══════════════════════════════════ */
        .bi-body { padding-top: 36px; }

        /* ── Category pills ── */
        .bi-cats {
            position: relative; margin-bottom: 32px;
        }
        .bi-cats__scroll {
            display: flex; align-items: center; gap: 8px;
            overflow-x: auto; scrollbar-width: none;
            padding: 4px 2px 8px;
        }
        .bi-cats__scroll::-webkit-scrollbar { display: none; }
        .bi-cats__pill {
            display: inline-flex; align-items: center; gap: 6px;
            white-space: nowrap; padding: 8px 18px;
            border-radius: 50px; border: 1.5px solid var(--border);
            background: var(--surface);
            font-family: var(--font-body); font-size: 13px; font-weight: 500;
            color: var(--ink-soft); text-decoration: none;
            transition: all .2s; flex-shrink: 0;
            box-shadow: var(--shadow-sm);
        }
        .bi-cats__pill:hover { border-color: var(--accent); color: var(--accent); background: var(--accent-glow); }
        .bi-cats__pill.active {
            background: var(--accent); border-color: var(--accent); color: #fff;
            box-shadow: 0 4px 14px rgba(29,110,245,.3);
        }
        .bi-cats__arrow {
            position: absolute; top: 50%; transform: translateY(-58%);
            width: 32px; height: 32px;
            display: flex; align-items: center; justify-content: center;
            background: var(--surface); border: 1px solid var(--border);
            border-radius: 50%; box-shadow: var(--shadow-md);
            cursor: pointer; z-index: 2; transition: all .2s;
        }
        .bi-cats__arrow:hover { background: var(--accent); border-color: var(--accent); color: #fff; }
        .bi-cats__arrow--left  { left: -16px; }
        .bi-cats__arrow--right { right: -16px; }

        /* ── Top bar (cats + search row) ── */
        .bi-topbar {
            display: flex; align-items: center; justify-content: space-between;
            gap: 20px; margin-bottom: 28px; flex-wrap: wrap;
        }
        .bi-topbar__search {
            display: flex; align-items: center;
            gap: 10px;
            background: var(--surface);
            border: 1.5px solid var(--border);
            border-radius: 50px;
            padding: 7px 10px 7px 18px;
            transition: border-color .2s;
            min-width: 240px;
        }
        .bi-topbar__search:focus-within { border-color: var(--accent); }
        .bi-topbar__search input {
            flex: 1; background: none; border: none; outline: none;
            font-family: var(--font-body); font-size: 13.5px;
            color: var(--ink); min-width: 0;
        }
        .bi-topbar__search input::placeholder { color: var(--muted); }
        .bi-topbar__search button {
            display: flex; align-items: center; justify-content: center;
            width: 32px; height: 32px;
            background: var(--accent); color: #fff;
            border: none; border-radius: 50%; cursor: pointer;
            transition: background .2s; flex-shrink: 0;
        }
        .bi-topbar__search button:hover { background: var(--accent-dk); }

        /* search result count */
        .bi-result-count {
            display: flex; align-items: center; gap: 8px;
            padding: 10px 16px;
            background: var(--surface); border: 1px solid var(--border);
            border-radius: var(--radius-md);
            font-family: var(--font-body); font-size: 13px; color: var(--ink-soft);
            margin-bottom: 24px;
        }
        .bi-result-count i { color: var(--accent); }
        .bi-result-count strong { color: var(--ink); }

        /* ── Featured card (first article) ── */
        .bi-featured {
            position: relative; border-radius: var(--radius-lg);
            overflow: hidden; background: var(--ink);
            box-shadow: var(--shadow-lg);
            cursor: pointer; display: block; text-decoration: none;
            margin-bottom: 28px;
            transition: transform .35s cubic-bezier(.22,.61,.36,1), box-shadow .35s;
        }
        .bi-featured:hover { transform: translateY(-4px); box-shadow: 0 20px 50px rgba(0,0,0,.14); }
        .bi-featured__img-wrap {
            aspect-ratio: 16/7; overflow: hidden;
        }
        .bi-featured__img {
            width: 100%; height: 100%; object-fit: cover;
            opacity: .75; transform: scale(1.04);
            transition: transform 6s ease, opacity .4s;
            display: block;
        }
        .bi-featured:hover .bi-featured__img { transform: scale(1); opacity: .85; }
        .bi-featured__overlay {
            position: absolute; inset: 0;
            background: linear-gradient(to top, rgba(8,10,18,.90) 0%, rgba(8,10,18,.3) 50%, transparent 100%);
            pointer-events: none;
        }
        .bi-featured__body {
            position: absolute; bottom: 0; left: 0; right: 0;
            padding: clamp(20px, 3vw, 36px);
            max-width: 680px;
        }
        .bi-featured__tag {
            display: inline-flex; align-items: center; gap: 5px;
            background: var(--accent); color: #fff;
            font-family: var(--font-body); font-size: 11px; font-weight: 600;
            letter-spacing: .1em; text-transform: uppercase;
            padding: 4px 12px 4px 9px; border-radius: 30px; margin-bottom: 14px;
        }
        .bi-featured__tag::before {
            content: ''; width: 5px; height: 5px;
            background: rgba(255,255,255,.7); border-radius: 50%;
        }
        .bi-featured__title {
            font-family: var(--font-display);
            font-size: clamp(20px, 3vw, 32px); font-weight: 700;
            color: #fff; margin: 0 0 14px; line-height: 1.22;
            text-shadow: 0 2px 12px rgba(0,0,0,.3);
        }
        .bi-featured__meta {
            display: flex; align-items: center; gap: 14px; flex-wrap: wrap;
            font-family: var(--font-body); font-size: 12.5px; color: rgba(255,255,255,.6);
        }
        .bi-featured__meta-item { display: flex; align-items: center; gap: 5px; }
        .bi-featured__meta-item i { font-size: 11px; color: rgba(255,255,255,.35); }
        .bi-featured__meta a { color: rgba(255,255,255,.8); text-decoration: none; font-weight: 500; }
        .bi-featured__meta a:hover { color: #fff; }
        .bi-featured__dot { color: rgba(255,255,255,.2); }
        .bi-featured__badge {
            position: absolute; top: 18px; left: 18px;
            display: flex; align-items: center; gap: 6px;
            background: rgba(255,255,255,.12); backdrop-filter: blur(6px);
            border: 1px solid rgba(255,255,255,.18);
            color: rgba(255,255,255,.85);
            font-family: var(--font-body); font-size: 11px; font-weight: 600;
            letter-spacing: .08em; text-transform: uppercase;
            padding: 5px 12px; border-radius: 30px;
        }

        /* ── Sidebar ── */
        .bi-sidebar { position: sticky; top: 88px; }

        /* Recent posts card */
        .bi-recent {
            background: var(--surface); border: 1px solid var(--border);
            border-radius: var(--radius-lg); overflow: hidden;
            box-shadow: var(--shadow-sm); margin-bottom: 20px;
        }
        .bi-recent__header {
            padding: 18px 20px 14px;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
        }
        .bi-recent__title {
            font-family: var(--font-display);
            font-size: 18px; font-weight: 700; color: var(--ink); margin: 0;
        }
        .bi-recent__title-accent {
            display: inline-block; width: 4px; height: 18px;
            background: var(--accent); border-radius: 2px; margin-right: 10px;
            vertical-align: middle;
        }
        .bi-recent__list { padding: 8px 0; }
        .bi-recent__item {
            display: flex; align-items: center; gap: 14px;
            padding: 12px 20px;
            text-decoration: none;
            transition: background .2s;
            border-bottom: 1px solid var(--border);
        }
        .bi-recent__item:last-child { border-bottom: none; }
        .bi-recent__item:hover { background: var(--bg); }
        .bi-recent__thumb {
            width: 62px; height: 62px; flex-shrink: 0;
            border-radius: var(--radius-sm); object-fit: cover;
            box-shadow: var(--shadow-sm);
            transition: transform .3s;
        }
        .bi-recent__item:hover .bi-recent__thumb { transform: scale(1.05); }
        .bi-recent__info { flex: 1; min-width: 0; }
        .bi-recent__name {
            font-family: var(--font-body); font-size: 13px; font-weight: 600;
            color: var(--ink); line-height: 1.4; margin-bottom: 4px;
            display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
            transition: color .2s;
        }
        .bi-recent__item:hover .bi-recent__name { color: var(--accent); }
        .bi-recent__time {
            display: flex; align-items: center; gap: 4px;
            font-family: var(--font-body); font-size: 11.5px; color: var(--muted);
        }
        .bi-recent__time i { font-size: 10px; }
        .bi-recent__num {
            width: 22px; height: 22px; flex-shrink: 0;
            background: var(--bg); border: 1px solid var(--border);
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-family: var(--font-body); font-size: 11px; font-weight: 700; color: var(--muted);
        }

        /* ── Section divider ── */
        .bi-divider {
            display: flex; align-items: center; gap: 12px; margin-bottom: 22px;
        }
        .bi-divider__line { flex: 1; height: 1px; background: var(--border); }
        .bi-divider__label {
            font-family: var(--font-body); font-size: 11px; font-weight: 600;
            letter-spacing: .12em; text-transform: uppercase; color: var(--muted);
            white-space: nowrap;
        }

        /* ── Pagination ── */
        .bi-pagination {
            display: flex; justify-content: flex-start;
            padding-top: 28px; margin-top: 8px;
            border-top: 1px solid var(--border);
        }
        .bi-pagination .pagination { gap: 5px; }
        .bi-pagination .page-item .page-link {
            border-radius: var(--radius-sm) !important;
            border: 1.5px solid var(--border);
            color: var(--ink-soft); font-family: var(--font-body);
            font-size: 13px; font-weight: 500; padding: 7px 13px;
            transition: all .2s;
        }
        .bi-pagination .page-item.active .page-link {
            background: var(--accent); border-color: var(--accent); color: #fff;
            box-shadow: 0 4px 12px rgba(29,110,245,.3);
        }
        .bi-pagination .page-item .page-link:hover {
            border-color: var(--accent); color: var(--accent); background: var(--accent-glow);
        }

        /* ── Reveal animation ── */
        .bi-reveal {
            opacity: 0; transform: translateY(22px);
            animation: biReveal .55s cubic-bezier(.22,.61,.36,1) forwards;
        }
        @keyframes biReveal { to { opacity: 1; transform: translateY(0); } }
        .bi-reveal-1 { animation-delay: .05s; }
        .bi-reveal-2 { animation-delay: .14s; }
        .bi-reveal-3 { animation-delay: .23s; }

        @media (max-width: 576px) {
            .bi-stats { gap: 20px; }
            .bi-stat__num { font-size: 22px; }
            .bi-featured__body { padding: 16px; }
        }
    </style>
@endpush

@section('content')
    @include('web-views.blogs.partials._app-blog-preloader')

    <?php
        $downloadAppStatus = getWebConfig(name: 'blog_feature_download_app_status') ?? 0;
        $appTitleData      = getWebConfig(name: 'blog_feature_download_app_title') ?? [];
    ?>

    <div class="bi-root">

        {{-- ══════════════ HERO ══════════════ --}}
        <div class="bi-hero">
            <div class="bi-hero__grid"></div>
            <div class="container">
                <div class="bi-hero__inner">
                    <div class="bi-hero__eyebrow">{{ translate('Our Journal') }}</div>

                    <h1 class="bi-hero__title">
                        {{ $blogTitle != '' ? $blogTitle : translate('Blog') }}
                    </h1>

                    @if($blogSubTitle)
                        <p class="bi-hero__sub">{{ $blogSubTitle }}</p>
                    @else
                        <p class="bi-hero__sub">{{ translate('Stories, insights, and ideas worth reading') }}</p>
                    @endif

                    {{-- Search --}}
                    <div class="bi-search-wrap">
                        <form action="{{ isset($blogPlatform) && $blogPlatform == 'app' ? route('app.blog.index', ['locale' => request('locale'), 'theme' => request('theme')]) : route('frontend.blog.index') }}"
                              method="get" id="search-form">
                            <input type="hidden" name="locale"   value="{{ request('locale') }}">
                            <input type="hidden" name="theme"    value="{{ request('theme') }}">
                            <input type="hidden" name="category" value="{{ request('category') }}">
                            <div class="bi-search">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.4)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0">
                                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                                </svg>
                                <input type="text" name="search" id="search"
                                       value="{{ request('search') }}"
                                       placeholder="{{ translate('Search_Blog') }}" required>
                                <button type="submit" class="bi-search__btn">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                                    </svg>
                                    {{ translate('Search') }}
                                </button>
                            </div>
                        </form>

                        @if(request('search'))
                            <div class="text-center mt-2">
                                <span class="bi-clear clear-all-search">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                                    {{ translate('Clear_Search') }}
                                </span>
                            </div>
                        @endif
                    </div>

                    {{-- Stats row --}}
                    @if($blogList->total() > 0)
                        <div class="bi-stats">
                            <div class="bi-stat">
                                <span class="bi-stat__num">{{ $blogList->total() }}+</span>
                                <span class="bi-stat__label">{{ translate('Articles') }}</span>
                            </div>
                            <div class="bi-stat-divider"></div>
                            <div class="bi-stat">
                                <span class="bi-stat__num">{{ count($blogCategoryList) }}</span>
                                <span class="bi-stat__label">{{ translate('Categories') }}</span>
                            </div>
                            <div class="bi-stat-divider"></div>
                            <div class="bi-stat">
                                <span class="bi-stat__num">{{ count($recentBlogList) }}</span>
                                <span class="bi-stat__label">{{ translate('Recent') }}</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ══════════════ BODY ══════════════ --}}
        <div class="bi-body">
            <div class="container">

                @if($blogList->total() > 0 || request()->has('search') || request()->has('category') || request()->has('writer'))

                    {{-- Category Pills --}}
                    @if(!request('search'))
                        <div class="bi-cats bi-reveal bi-reveal-1">
                            <div class="bi-cats__scroll" id="biCatScroll">
                                <a href="{{ isset($blogPlatform) && $blogPlatform == 'app' ? route('app.blog.index', ['locale' => request('locale'), 'theme' => request('theme')]) : route('frontend.blog.index') }}"
                                   class="bi-cats__pill {{ request('category') == '' ? 'active' : '' }}">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                                    {{ translate('All') }}
                                </a>
                                @foreach($blogCategoryList as $blogCategory)
                                    @if(isset($blogPlatform) && $blogPlatform == 'app')
                                        <a href="{{ route('app.blog.index', ['category' => $blogCategory?->name, 'locale' => request('locale'), 'theme' => request('theme')]) }}"
                                           class="bi-cats__pill {{ request('category') == $blogCategory?->name ? 'active' : '' }}">
                                            {{ Str::limit($blogCategory->name, 25) }}
                                        </a>
                                    @else
                                        <a href="{{ route('frontend.blog.index', ['category' => $blogCategory?->name]) }}"
                                           class="bi-cats__pill {{ request('category') == $blogCategory?->name ? 'active' : '' }}">
                                            {{ Str::limit($blogCategory->name, 25) }}
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                            <button class="bi-cats__arrow bi-cats__arrow--left" id="biCatPrev" aria-label="Previous">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="m15 18-6-6 6-6"/></svg>
                            </button>
                            <button class="bi-cats__arrow bi-cats__arrow--right" id="biCatNext" aria-label="Next">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="m9 18 6-6-6-6"/></svg>
                            </button>
                        </div>
                    @endif

                    {{-- Search result count --}}
                    @if(request('search'))
                        <div class="bi-result-count bi-reveal bi-reveal-1">
                            <i class="fa fa-search"></i>
                            <span>
                                <strong>{{ $blogList->count() }}</strong>
                                {{ translate('Search_Result_Found') }}
                                {{ translate('for') }} "<strong>{{ request('search') }}</strong>"
                            </span>
                        </div>
                    @endif

                @endif

                {{-- No results --}}
                @if($blogList->total() <= 0 && !empty(request('search')))
                    <div class="row">
                        <div class="col-lg-8">
                            @include('web-views.blogs.partials._no-result-found')
                        </div>
                        <div class="col-lg-4">
                            <div class="bi-sidebar">
                                <div class="bi-recent bi-reveal bi-reveal-2">
                                    <div class="bi-recent__header">
                                        <h5 class="bi-recent__title">
                                            <span class="bi-recent__title-accent"></span>{{ translate('Recent_Posts') }}
                                        </h5>
                                    </div>
                                    <div class="bi-recent__list">
                                        @foreach($recentBlogList->take(6) as $i => $blogItem)
                                            @php $rRoute = (isset($blogPlatform) && $blogPlatform == 'app') ? route('app.blog.details', ['slug' => $blogItem?->slug, 'locale' => request('locale'), 'theme' => request('theme')]) : route('frontend.blog.details', ['slug' => $blogItem?->slug]); @endphp
                                            <a href="{{ $rRoute }}" class="bi-recent__item">
                                                <div class="bi-recent__num">{{ $i + 1 }}</div>
                                                <img class="bi-recent__thumb"
                                                     src="{{ getStorageImages(path: $blogItem?->thumbnail_full_url, type:'wide-banner') }}"
                                                     alt="{{ $blogItem?->title }}">
                                                <div class="bi-recent__info">
                                                    <div class="bi-recent__name">{{ $blogItem?->title }}</div>
                                                    <div class="bi-recent__time"><i class="fa fa-clock-o"></i>{{ $blogItem->publish_date->diffForHumans() }}</div>
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                                @if($appTitleData && $downloadAppStatus)
                                    @include('web-views.blogs.partials._download-app-card')
                                @endif
                            </div>
                        </div>
                    </div>

                @elseif($blogList->total() <= 0)
                    <div class="row">
                        <div class="col-lg-8">
                            @include('web-views.blogs.partials._no-blog-found')
                        </div>
                        <div class="col-lg-4">
                            <div class="bi-sidebar">
                                <div class="bi-recent bi-reveal bi-reveal-2">
                                    <div class="bi-recent__header">
                                        <h5 class="bi-recent__title">
                                            <span class="bi-recent__title-accent"></span>{{ translate('Recent_Posts') }}
                                        </h5>
                                    </div>
                                    <div class="bi-recent__list">
                                        @foreach($recentBlogList->take(6) as $i => $blogItem)
                                            @php $rRoute = (isset($blogPlatform) && $blogPlatform == 'app') ? route('app.blog.details', ['slug' => $blogItem?->slug, 'locale' => request('locale'), 'theme' => request('theme')]) : route('frontend.blog.details', ['slug' => $blogItem?->slug]); @endphp
                                            <a href="{{ $rRoute }}" class="bi-recent__item">
                                                <div class="bi-recent__num">{{ $i + 1 }}</div>
                                                <img class="bi-recent__thumb"
                                                     src="{{ getStorageImages(path: $blogItem?->thumbnail_full_url, type:'wide-banner') }}"
                                                     alt="{{ $blogItem?->title }}">
                                                <div class="bi-recent__info">
                                                    <div class="bi-recent__name">{{ $blogItem?->title }}</div>
                                                    <div class="bi-recent__time"><i class="fa fa-clock-o"></i>{{ $blogItem->publish_date->diffForHumans() }}</div>
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                                @if($appTitleData && $downloadAppStatus)
                                    @include('web-views.blogs.partials._download-app-card')
                                @endif
                            </div>
                        </div>
                    </div>

                @else
                    {{-- ── Main content + sidebar ── --}}
                    <div class="row g-4">

                        {{-- Left: Blog list --}}
                        <div class="{{ !request('search') ? 'col-lg-8' : 'col-lg-12' }}">

                            @php $blogListIndex = 0; @endphp

                            @foreach($blogList as $blogItem)
                                @php
                                    $detailRoute = (isset($blogPlatform) && $blogPlatform == 'app')
                                        ? route('app.blog.details', ['slug' => $blogItem?->slug, 'locale' => request('locale'), 'theme' => request('theme')])
                                        : route('frontend.blog.details', ['slug' => $blogItem?->slug]);
                                @endphp

                                @if($blogListIndex === 0 && !request('search'))
                                    {{-- Featured first post --}}
                                    <a href="{{ $detailRoute }}" class="bi-featured bi-reveal bi-reveal-1">
                                        <div class="bi-featured__img-wrap">
                                            <img class="bi-featured__img"
                                                 src="{{ getStorageImages(path: $blogItem?->thumbnail_full_url, type:'wide-banner') }}"
                                                 alt="{{ $blogItem?->title }}">
                                        </div>
                                        <div class="bi-featured__overlay"></div>
                                        <div class="bi-featured__badge">
                                            <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                            {{ translate('Featured') }}
                                        </div>
                                        <div class="bi-featured__body">
                                            @if($blogItem?->category?->name)
                                                <div class="bi-featured__tag">{{ Str::limit($blogItem->category->name, 22) }}</div>
                                            @endif
                                            <h2 class="bi-featured__title">{{ $blogItem?->title }}</h2>
                                            <div class="bi-featured__meta">
                                                @if($blogItem->writer)
                                                    <div class="bi-featured__meta-item">
                                                        <i class="fa fa-user-circle"></i>
                                                        <a href="#" onclick="event.preventDefault()">{{ Str::limit($blogItem->writer, 20) }}</a>
                                                    </div>
                                                    <span class="bi-featured__dot">·</span>
                                                @endif
                                                <div class="bi-featured__meta-item">
                                                    <i class="fa fa-calendar-o"></i>
                                                    <span>{{ date('M d, Y', strtotime($blogItem->publish_date)) }}</span>
                                                </div>
                                                <span class="bi-featured__dot">·</span>
                                                <div class="bi-featured__meta-item">
                                                    <i class="fa fa-eye"></i>
                                                    <span>{{ number_format($blogItem->click_count ?? 0) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </a>

                                    {{-- Divider before grid --}}
                                    <div class="bi-divider bi-reveal bi-reveal-2">
                                        <div class="bi-divider__line"></div>
                                        <span class="bi-divider__label">{{ translate('More Articles') }}</span>
                                        <div class="bi-divider__line"></div>
                                    </div>

                                    <div class="row g-4 bi-reveal bi-reveal-2">
                                @elseif($blogListIndex === 1 && !request('search'))
                                    {{-- Opening tag for 2-col grid --}}
                                @endif

                                @if($blogListIndex > 0 || request('search'))
                                    <div class="{{ request('search') ? 'col-md-4' : 'col-md-6' }}">
                                        @include('web-views.blogs.partials._single-blog-card', ['blogItem' => $blogItem])
                                    </div>
                                @endif

                                @php $blogListIndex++; @endphp
                            @endforeach

                            {{-- Close the inner row opened after featured --}}
                            @if(!request('search') && $blogListIndex > 1)
                                </div>{{-- /row --}}
                            @endif

                            {{-- Pagination --}}
                            @if($blogList->hasPages())
                                <div class="bi-pagination">
                                    {!! $blogList->links() !!}
                                </div>
                            @endif
                        </div>

                        {{-- Right: Sidebar (only when not searching) --}}
                        @if(!request('search'))
                            <div class="col-lg-4">
                                <div class="bi-sidebar bi-reveal bi-reveal-3">

                                    {{-- Recent Posts --}}
                                    <div class="bi-recent">
                                        <div class="bi-recent__header">
                                            <h5 class="bi-recent__title">
                                                <span class="bi-recent__title-accent"></span>{{ translate('Recent_Posts') }}
                                            </h5>
                                        </div>
                                        <div class="bi-recent__list">
                                            @foreach($recentBlogList->take(6) as $i => $blogItem)
                                                @php $rRoute = (isset($blogPlatform) && $blogPlatform == 'app') ? route('app.blog.details', ['slug' => $blogItem?->slug, 'locale' => request('locale'), 'theme' => request('theme')]) : route('frontend.blog.details', ['slug' => $blogItem?->slug]); @endphp
                                                <a href="{{ $rRoute }}" class="bi-recent__item">
                                                    <div class="bi-recent__num">{{ $i + 1 }}</div>
                                                    <img class="bi-recent__thumb"
                                                         src="{{ getStorageImages(path: $blogItem?->thumbnail_full_url, type:'wide-banner') }}"
                                                         alt="{{ $blogItem?->title }}">
                                                    <div class="bi-recent__info">
                                                        <div class="bi-recent__name">{{ $blogItem?->title }}</div>
                                                        <div class="bi-recent__time">
                                                            <i class="fa fa-clock-o"></i>
                                                            {{ $blogItem->publish_date->diffForHumans() }}
                                                        </div>
                                                    </div>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- Download App Card --}}
                                    @if($appTitleData && $downloadAppStatus)
                                        <div class="pb-4">
                                            @include('web-views.blogs.partials._download-app-card')
                                        </div>
                                    @endif

                                </div>
                            </div>
                        @endif

                    </div>{{-- /row --}}
                @endif

            </div>{{-- /container --}}
        </div>{{-- /bi-body --}}

    </div>{{-- /bi-root --}}
@endsection

@push('script')
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/blog.js') }}"></script>
    <script>
        // Category scroll arrows
        (function () {
            const scroll = document.getElementById('biCatScroll');
            const prev   = document.getElementById('biCatPrev');
            const next   = document.getElementById('biCatNext');
            if (!scroll) return;
            const update = () => {
                if (prev) prev.style.opacity = scroll.scrollLeft > 8 ? '1' : '0';
                if (next) next.style.opacity = scroll.scrollLeft < scroll.scrollWidth - scroll.clientWidth - 8 ? '1' : '0';
            };
            if (prev) prev.addEventListener('click', () => scroll.scrollBy({ left: -200, behavior: 'smooth' }));
            if (next) next.addEventListener('click', () => scroll.scrollBy({ left:  200, behavior: 'smooth' }));
            scroll.addEventListener('scroll', update, { passive: true });
            update();
        })();
    </script>
@endpush
