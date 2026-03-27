@extends(isset($blogPlatform) && $blogPlatform == 'app' ? 'web-views.blogs.blog-layouts' : 'layouts.front-end.app')

@section('title', $blogData['title'] ?? translate('Blog_Details'))

@push('css_or_js')
    @include(VIEW_FILE_NAMES['blog_seo_meta_content_partials'], ['metaContentData' => $blogData?->seoInfo, 'blogDetails' => $blogData])

    @if(isset($blogPlatform) && $blogPlatform == 'app')
        <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/app-blog.css') }}"/>
    @endif

    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Plus+Jakarta+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --ink:         #0d0f14;
            --ink-soft:    #3d4452;
            --muted:       #8892a4;
            --border:      #eaedf2;
            --accent:      #1d6ef5;
            --accent-glow: rgba(29,110,245,.12);
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

        .bd-root *, .bd-root *::before, .bd-root *::after { box-sizing: border-box; }
        .bd-root {
            background: var(--bg);
            min-height: 100vh;
            padding-bottom: 100px;
        }

        /* ═══════════════ HERO ═══════════════ */
        .bd-hero {
            position: relative;          /* ← critical */
            width: 100%;
            height: min(62vh, 620px);
            overflow: hidden;
            background: var(--ink);
            display: flex;               /* keeps children in flow */
            flex-direction: column;
        }
        .bd-hero__img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: .75;
            transform: scale(1.04);
            transition: transform 7s ease, opacity .4s;
            z-index: 0;
        }
        .bd-hero:hover .bd-hero__img { transform: scale(1); }

        .bd-hero__overlay {
            position: absolute;
            inset: 0;
            z-index: 1;
            background:
                linear-gradient(to top,  rgba(8,10,18,.88) 0%, rgba(8,10,18,.3) 50%, transparent 100%),
                linear-gradient(to right, rgba(8,10,18,.4) 0%, transparent 60%);
            pointer-events: none;
        }

        .bd-hero__corner {
            position: absolute;
            top: 0; right: 0;
            width: 220px; height: 220px;
            z-index: 1;
            background: radial-gradient(circle at top right, rgba(29,110,245,.25), transparent 70%);
            pointer-events: none;
        }

        /* ← This is the key fix: z-index above overlays, positioned at bottom */
        .bd-hero__content {
            position: absolute;
            bottom: 0; left: 0; right: 0;
            z-index: 2;
            padding: clamp(24px, 5vw, 52px) clamp(20px, 5vw, 52px);
            max-width: 860px;
        }

        .bd-hero__breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            font-family: var(--font-body);
            font-size: 11.5px;
            font-weight: 500;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: rgba(255,255,255,.55);
            margin-bottom: 14px;
        }
        .bd-hero__breadcrumb span { color: rgba(255,255,255,.3); }

        .bd-hero__tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--accent);
            color: #fff;
            font-family: var(--font-body);
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .1em;
            text-transform: uppercase;
            padding: 5px 14px 5px 10px;
            border-radius: 30px;
            margin-bottom: 16px;
        }
        .bd-hero__tag::before {
            content: '';
            display: block;
            width: 6px; height: 6px;
            background: rgba(255,255,255,.7);
            border-radius: 50%;
        }

        .bd-hero__title {
            font-family: var(--font-display);
            font-size: clamp(24px, 4.2vw, 52px);
            font-weight: 700;
            line-height: 1.18;
            color: #fff;
            margin: 0 0 18px;
            text-shadow: 0 2px 18px rgba(0,0,0,.3);
        }

        .bd-hero__meta {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 6px 16px;
            font-family: var(--font-body);
            font-size: 13px;
            color: rgba(255,255,255,.65);
        }
        .bd-hero__meta-item { display: flex; align-items: center; gap: 6px; }
        .bd-hero__meta-item i { font-size: 11px; color: rgba(255,255,255,.4); }
        .bd-hero__meta a {
            color: rgba(255,255,255,.85);
            text-decoration: none;
            font-weight: 500;
            transition: color .2s;
        }
        .bd-hero__meta a:hover { color: #fff; }
        .bd-hero__dot { color: rgba(255,255,255,.25); }

        .bd-hero__read-time {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: rgba(255,255,255,.12);
            backdrop-filter: blur(6px);
            border: 1px solid rgba(255,255,255,.18);
            color: rgba(255,255,255,.8);
            font-family: var(--font-body);
            font-size: 11.5px;
            font-weight: 500;
            padding: 4px 12px;
            border-radius: 30px;
        }

        /* ═══════════════ DRAFT ═══════════════ */
        .bd-draft {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #fff5f5;
            border: 1px solid #fecaca;
            color: #b91c1c;
            border-radius: var(--radius-md);
            padding: 13px 18px;
            font-family: var(--font-body);
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 24px;
        }

        /* ═══════════════ PROGRESS BAR ═══════════════ */
        .bd-progress-bar {
            position: fixed;
            top: 0; left: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--accent), #60a5fa);
            width: 0%;
            z-index: 9999;
            transition: width .1s linear;
            border-radius: 0 2px 2px 0;
        }

        /* ═══════════════ LAYOUT ═══════════════ */
        .bd-layout {
            display: grid;
            grid-template-columns: 1fr;   /* default: single column */
            gap: 28px;
            align-items: start;
            margin-top: 36px;
        }
        /* with left TOC */
        .bd-layout.has-toc {
            grid-template-columns: 240px 1fr;
        }
        /* with left TOC + right sidebar */
        .bd-layout.has-toc.has-sidebar {
            grid-template-columns: 240px 1fr 260px;
        }
        /* right sidebar only (no TOC) */
        .bd-layout.has-sidebar:not(.has-toc) {
            grid-template-columns: 1fr 260px;
        }
        @media (max-width: 1140px) {
            .bd-layout.has-toc.has-sidebar { grid-template-columns: 200px 1fr; }
            .bd-layout.has-sidebar:not(.has-toc) { grid-template-columns: 1fr; }
            .bd-aside-right { display: none !important; }
        }
        @media (max-width: 800px) {
            .bd-layout,
            .bd-layout.has-toc,
            .bd-layout.has-toc.has-sidebar { grid-template-columns: 1fr; }
            .bd-aside-left { display: none !important; }
        }

        /* ═══════════════ TOC ═══════════════ */
        .bd-toc {
            position: sticky;
            top: 88px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 22px;
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }
        .bd-toc::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--accent), #60a5fa);
        }
        .bd-toc__label {
            font-family: var(--font-body);
            font-size: 10.5px;
            font-weight: 600;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 14px;
        }
        .bd-toc__list { list-style: none; padding: 0; margin: 0; }
        .bd-toc__list li + li { margin-top: 2px; }
        .bd-toc__list a {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            padding: 7px 8px;
            border-radius: var(--radius-sm);
            font-family: var(--font-body);
            font-size: 12.5px;
            color: var(--ink-soft);
            text-decoration: none;
            line-height: 1.45;
            transition: background .2s, color .2s;
        }
        .bd-toc__list a::before {
            content: '';
            display: block;
            width: 4px; height: 4px;
            margin-top: 6px;
            background: var(--muted);
            border-radius: 50%;
            flex-shrink: 0;
            transition: background .2s, transform .2s;
        }
        .bd-toc__list a:hover,
        .bd-toc__list a.active { background: var(--accent-glow); color: var(--accent); }
        .bd-toc__list a:hover::before,
        .bd-toc__list a.active::before { background: var(--accent); transform: scale(1.5); }
        .bd-toc__list a.active { font-weight: 600; }

        /* ═══════════════ ARTICLE ═══════════════ */
        .bd-article {
            background: var(--surface);
            border-radius: var(--radius-lg);
            padding: clamp(24px, 4vw, 48px);
            box-shadow: var(--shadow-md);
            font-family: var(--font-body);
            font-size: 16.5px;
            line-height: 1.82;
            color: var(--ink-soft);
            border: 1px solid var(--border);
            overflow: hidden;
            overflow-wrap: break-word;
            word-break: break-word;
            word-wrap: break-word;
        }
        .bd-article__byline {
            display: flex;
            align-items: center;
            gap: 14px;
            padding-bottom: 24px;
            margin-bottom: 28px;
            border-bottom: 1px solid var(--border);
            flex-wrap: wrap;
        }
        .bd-article__avatar {
            width: 44px; height: 44px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), #60a5fa);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-family: var(--font-display);
            font-size: 18px;
            font-weight: 700;
            flex-shrink: 0;
        }
        .bd-article__byline-info { flex: 1; min-width: 0; overflow: hidden; }
        .bd-article__author {
            font-family: var(--font-body);
            font-size: 13.5px;
            font-weight: 600;
            color: var(--ink);
            text-decoration: none;
            display: block;
            margin-bottom: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .bd-article__author:hover { color: var(--accent); }
        .bd-article__date {
            font-family: var(--font-body);
            font-size: 12px;
            color: var(--muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .bd-article__views-badge {
            display: flex;
            align-items: center;
            gap: 5px;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 30px;
            padding: 5px 12px;
            font-family: var(--font-body);
            font-size: 12px;
            color: var(--muted);
            font-weight: 500;
        }
        .bd-article h2,.bd-article h3,.bd-article h4 {
            font-family: var(--font-display);
            color: var(--ink);
            font-weight: 700;
            line-height: 1.28;
            margin-top: 2.2em; margin-bottom: .6em;
        }
        .bd-article h2 { font-size: 1.75em; }
        .bd-article h3 { font-size: 1.45em; }
        .bd-article h4 { font-size: 1.2em; }
        .bd-article p { margin-bottom: 1.3em; overflow-wrap: break-word; word-break: break-word; }
        .bd-article * { max-width: 100%; }
        .bd-article table { width: 100%; table-layout: fixed; overflow-x: auto; display: block; }
        .bd-article pre, .bd-article code { white-space: pre-wrap; word-break: break-word; overflow-x: auto; }
        .bd-article a { color: var(--accent); text-underline-offset: 3px; }
        .bd-article img { max-width: 100%; border-radius: var(--radius-md); display: block; margin: 1.5em auto; box-shadow: var(--shadow-md); }
        .bd-article blockquote {
            position: relative;
            border-left: 4px solid var(--accent);
            background: linear-gradient(to right, var(--accent-glow), transparent);
            border-radius: 0 var(--radius-md) var(--radius-md) 0;
            padding: 18px 22px 18px 24px;
            margin: 1.8em 0;
            font-family: var(--font-display);
            font-size: 1.12em;
            font-style: italic;
            color: var(--ink);
        }

        /* ═══════════════ RIGHT SIDEBAR ═══════════════ */
        .bd-aside-right {
            position: sticky;
            top: 88px;
            display: flex;
            flex-direction: column;
            gap: 18px;
        }
        .bd-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 22px;
            box-shadow: var(--shadow-sm);
        }
        .bd-card__label {
            font-family: var(--font-body);
            font-size: 10.5px;
            font-weight: 600;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 16px;
        }
        .bd-share-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
        .bd-share-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 9px 8px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--border);
            background: var(--bg);
            text-decoration: none;
            font-family: var(--font-body);
            font-size: 12px;
            font-weight: 500;
            color: var(--ink-soft);
            transition: all .2s;
            cursor: pointer;
        }
        .bd-share-btn img { width: 15px; height: 15px; }
        .bd-share-btn:hover { border-color: var(--accent); color: var(--accent); background: var(--accent-glow); transform: translateY(-1px); }

        /* ═══════════════ SHARE STRIP ═══════════════ */
        .bd-share-strip {
            background: linear-gradient(135deg, #0d1b3e 0%, #0f2456 50%, #102a66 100%);
            border-radius: var(--radius-lg);
            padding: 30px 36px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 56px;
            box-shadow: var(--shadow-lg);
            position: relative;
            overflow: hidden;
        }
        .bd-share-strip::after {
            content: '';
            position: absolute;
            right: -40px; top: -40px;
            width: 180px; height: 180px;
            background: radial-gradient(circle, rgba(29,110,245,.3), transparent 70%);
            pointer-events: none;
        }
        .bd-share-strip__text {
            font-family: var(--font-display);
            font-size: clamp(18px, 2.2vw, 24px);
            font-weight: 600;
            color: #fff;
        }
        .bd-share-strip__sub {
            font-family: var(--font-body);
            font-size: 13px;
            color: rgba(255,255,255,.5);
            margin-top: 4px;
        }
        .bd-share-strip__icons { display: flex; gap: 10px; position: relative; z-index: 1; }
        .bd-strip-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 42px; height: 42px;
            border-radius: 50%;
            background: rgba(255,255,255,.1);
            border: 1px solid rgba(255,255,255,.2);
            transition: all .25s;
            cursor: pointer;
            text-decoration: none;
        }
        .bd-strip-icon:hover { background: var(--accent); border-color: var(--accent); transform: translateY(-3px); box-shadow: 0 8px 20px rgba(29,110,245,.4); }
        .bd-strip-icon:hover img { filter: brightness(10); }
        .bd-strip-icon img { width: 17px; height: 17px; }

        /* ═══════════════ POPULAR SECTION ═══════════════ */
        .bd-section-header { display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 28px; gap: 16px; }
        .bd-section-eyebrow {
            font-family: var(--font-body);
            font-size: 11px; font-weight: 600;
            letter-spacing: .14em; text-transform: uppercase;
            color: var(--accent); margin-bottom: 6px;
            display: flex; align-items: center; gap: 8px;
        }
        .bd-section-eyebrow::before { content: ''; display: block; width: 22px; height: 2px; background: var(--accent); border-radius: 2px; }
        .bd-section-title {
            font-family: var(--font-display);
            font-size: clamp(22px, 2.8vw, 32px);
            font-weight: 700; color: var(--ink); margin: 0;
        }
        .bd-see-more {
            display: flex; align-items: center; gap: 6px;
            font-family: var(--font-body); font-size: 13px; font-weight: 600;
            color: var(--accent); text-decoration: none;
            padding: 8px 16px; border: 1.5px solid var(--accent); border-radius: 30px;
            transition: all .2s; white-space: nowrap; flex-shrink: 0;
        }
        .bd-see-more:hover { background: var(--accent); color: #fff; box-shadow: 0 4px 14px rgba(29,110,245,.3); }
        .bd-see-more svg { transition: transform .2s; }
        .bd-see-more:hover svg { transform: translateX(3px); }

        /* ═══════════════ DIVIDER ═══════════════ */
        .bd-divider {
            height: 1px;
            background: linear-gradient(to right, transparent, var(--border) 30%, var(--border) 70%, transparent);
            margin: 52px 0;
        }

        /* ═══════════════ MAIN CONTENT ═══════════════ */
        .bd-layout > main {
            min-width: 0;
            max-width: 100%;
            overflow: hidden;
        }

        /* ═══════════════ ANIMATIONS ═══════════════ */
        .bd-reveal {
            opacity: 0;
            transform: translateY(28px);
            animation: bdReveal .65s cubic-bezier(.22,.61,.36,1) forwards;
        }
        @keyframes bdReveal { to { opacity: 1; transform: translateY(0); } }
        .bd-reveal-1 { animation-delay: .05s; }
        .bd-reveal-2 { animation-delay: .18s; }
        .bd-reveal-3 { animation-delay: .3s; }

        @media (max-width: 576px) {
            .bd-article { padding: 20px 16px; }
            .bd-share-strip { padding: 22px 20px; }
            .bd-hero__content { padding: 20px 16px; }
        }
    </style>
@endpush

@section('content')
    @include('web-views.blogs.partials._app-blog-preloader')

    <div class="bd-progress-bar" id="bdProgressBar"></div>

    <div class="bd-root" data-platform="{{ isset($blogPlatform) && $blogPlatform == 'app' ? 'app' : 'web' }}">

        {{-- ══════════ HERO ══════════ --}}
        <div class="bd-hero bd-reveal bd-reveal-1">
            <img class="bd-hero__img"
                 src="{{ getStorageImages(path: $blogData['thumbnail_full_url'] ?? null, type:'wide-banner') }}"
                 alt="{{ $blogData['title'] ?? null }}">
            <div class="bd-hero__overlay"></div>
            <div class="bd-hero__corner"></div>

            <div class="bd-hero__content">
                {{-- Breadcrumb --}}
                <div class="bd-hero__breadcrumb">
                    {{ translate('Blog') }}
                    <span>/</span>
                    @if($blogData?->category?->name)
                        {{ Str::limit($blogData?->category?->name, 30) }}
                    @endif
                </div>

                {{-- Category tag --}}
                @if($blogData?->category?->name)
                    <div class="bd-hero__tag">{{ Str::limit($blogData?->category?->name, 25) }}</div>
                @endif

                {{-- Title --}}
                <h1 class="bd-hero__title">{{ $blogData['title'] ?? null }}</h1>

                {{-- Meta row --}}
                <div class="bd-hero__meta">
                    @if($blogData->writer)
                        <div class="bd-hero__meta-item">
                            <i class="fa fa-user-circle"></i>
                            <span>{{ translate('By') }}&nbsp;
                                @if(isset($blogPlatform) && $blogPlatform == 'app')
                                    <a href="{{ route('app.blog.index', ['writer' => $blogData['writer'], 'locale' => request('locale'), 'theme' => request('theme')]) }}">{{ Str::limit($blogData['writer'], 22) }}</a>
                                @else
                                    <a href="{{ route('frontend.blog.index', ['writer' => $blogData['writer']]) }}">{{ Str::limit($blogData['writer'], 22) }}</a>
                                @endif
                            </span>
                        </div>
                        <span class="bd-hero__dot">·</span>
                    @endif
                    <div class="bd-hero__meta-item">
                        <i class="fa fa-calendar-o"></i>
                        <span>{{ date('M d, Y', strtotime($blogData['publish_date'] ?? null)) }}</span>
                    </div>
                    <span class="bd-hero__dot">·</span>
                    <div class="bd-hero__meta-item">
                        <i class="fa fa-eye"></i>
                        <span>{{ number_format($blogData['click_count'] ?? 0) }} {{ translate('views') }}</span>
                    </div>
                    <div class="bd-hero__read-time">
                        <i class="fa fa-clock-o"></i>
                        {{ max(1, round(str_word_count(strip_tags($updatedDescription)) / 200)) }} {{ translate('min read') }}
                    </div>
                </div>
            </div>
        </div>
        {{-- /hero --}}

        <div class="container">

            {{-- Draft Notice --}}
            @if(request('source') == 'draft')
                <div class="bd-draft mt-4">
                    <i class="fa fa-exclamation-circle"></i>
                    <span>{{ translate('This_is_a_draft_copy.') }} {{ translate('It_has_not_been_published_yet.') }}</span>
                </div>
            @endif

            {{-- ══════════ 3-COLUMN LAYOUT ══════════ --}}
            <?php
                $downloadAppStatus = getWebConfig(name: 'blog_feature_download_app_status') ?? 0;
                $appTitleData      = getWebConfig(name: 'blog_feature_download_app_title') ?? [];
            ?>
            @php
                $hasToc     = count($articleLinks) > 0;
                $hasSidebar = isset($blogPlatform) && $blogPlatform == 'web' && ($appTitleData && $downloadAppStatus);
                $layoutClass = 'bd-layout bd-reveal bd-reveal-2'
                    . ($hasToc     ? ' has-toc'     : '')
                    . ($hasSidebar ? ' has-sidebar'  : '');
            @endphp
            <div class="{{ $layoutClass }}">

                {{-- LEFT — TOC --}}
                @if(count($articleLinks) > 0)
                    <aside class="bd-aside-left">
                        <nav class="bd-toc">
                            <div class="bd-toc__label">{{ translate('In_this_article') }}</div>
                            <ul class="bd-toc__list scrollspy-blog-details-menu">
                                @foreach ($articleLinks as $link)
                                    @if(!empty($link['text']))
                                        <li><a href="#{{ $link['id'] }}">{{ $link['text'] }}</a></li>
                                    @endif
                                @endforeach
                            </ul>
                        </nav>
                    </aside>
                @endif

                {{-- CENTRE — Article --}}
                <main>
                    <article class="bd-article scrollspy-blog-details"
                             id="bdArticle"
                             data-bs-spy="scroll"
                             data-bs-target="#simple-list-example"
                             data-bs-offset="0"
                             data-bs-smooth-scroll="true"
                             tabindex="0">

                        @if($blogData->writer || $blogData['publish_date'])
                            <div class="bd-article__byline">
                                @if($blogData->writer)
                                    <div class="bd-article__avatar">{{ strtoupper(substr($blogData['writer'], 0, 1)) }}</div>
                                @endif
                                <div class="bd-article__byline-info">
                                    @if($blogData->writer)
                                        @if(isset($blogPlatform) && $blogPlatform == 'app')
                                            <a class="bd-article__author" href="{{ route('app.blog.index', ['writer' => $blogData['writer'], 'locale' => request('locale'), 'theme' => request('theme')]) }}">{{ $blogData['writer'] }}</a>
                                        @else
                                            <a class="bd-article__author" href="{{ route('frontend.blog.index', ['writer' => $blogData['writer']]) }}">{{ $blogData['writer'] }}</a>
                                        @endif
                                    @endif
                                    <div class="bd-article__date">{{ translate('Published') }} {{ date('F d, Y', strtotime($blogData['publish_date'] ?? null)) }}</div>
                                </div>
                                <div class="bd-article__views-badge">
                                    <i class="fa fa-eye"></i>
                                    {{ number_format($blogData['click_count'] ?? 0) }}
                                </div>
                            </div>
                        @endif

                        {!! $updatedDescription !!}
                    </article>
                </main>

                {{-- RIGHT — Share + App --}}
                @if(isset($blogPlatform) && $blogPlatform == 'web' && ($appTitleData && $downloadAppStatus))
                    <aside class="bd-aside-right">
                        <div class="bd-card">
                            <div class="bd-card__label">{{ translate('Share_Now') }}</div>
                            <div class="bd-share-grid">
                                <a href="javascript:" class="bd-share-btn share-on-social-media"
                                   data-action="{{ route('frontend.blog.details', ['slug' => $blogData['slug'] ?? null]) }}"
                                   data-social-media-name="facebook.com/sharer/sharer.php?u=">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                                    Facebook
                                </a>
                                <a href="javascript:" class="bd-share-btn share-on-social-media"
                                   data-action="{{ route('frontend.blog.details', ['slug' => $blogData['slug'] ?? null]) }}"
                                   data-social-media-name="twitter.com/intent/tweet?text=">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                    Twitter
                                </a>
                                <a href="javascript:" class="bd-share-btn share-on-social-media"
                                   data-action="{{ route('frontend.blog.details', ['slug' => $blogData['slug'] ?? null]) }}"
                                   data-social-media-name="linkedin.com/shareArticle?mini=true&url=">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-4 0v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg>
                                    LinkedIn
                                </a>
                                <a href="javascript:" class="bd-share-btn share-on-social-media"
                                   data-action="{{ route('frontend.blog.details', ['slug' => $blogData['slug'] ?? null]) }}"
                                   data-social-media-name="api.whatsapp.com/send?text=">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/></svg>
                                    WhatsApp
                                </a>
                            </div>
                        </div>
                        @include('web-views.blogs.partials._download-app-card')
                    </aside>
                @endif

            </div>{{-- /bd-layout --}}

            <div class="bd-divider"></div>

            {{-- ══════════ SHARE STRIP ══════════ --}}
            <div class="bd-share-strip bd-reveal bd-reveal-3">
                <div>
                    <div class="bd-share-strip__text">{{ translate('Share_this_article') }}</div>
                    <div class="bd-share-strip__sub">{{ translate('Help others discover this story') }}</div>
                </div>
                <div class="bd-share-strip__icons">
                    {{-- Facebook --}}
                    <a href="javascript:" class="bd-strip-icon share-on-social-media"
                       data-action="{{ route('frontend.blog.details', ['slug' => $blogData['slug'] ?? null]) }}"
                       data-social-media-name="facebook.com/sharer/sharer.php?u=">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="white"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                    </a>
                    {{-- Twitter/X --}}
                    <a href="javascript:" class="bd-strip-icon share-on-social-media"
                       data-action="{{ route('frontend.blog.details', ['slug' => $blogData['slug'] ?? null]) }}"
                       data-social-media-name="twitter.com/intent/tweet?text=">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="white"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    </a>
                    {{-- LinkedIn --}}
                    <a href="javascript:" class="bd-strip-icon share-on-social-media"
                       data-action="{{ route('frontend.blog.details', ['slug' => $blogData['slug'] ?? null]) }}"
                       data-social-media-name="linkedin.com/shareArticle?mini=true&url=">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="white"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg>
                    </a>
                    {{-- WhatsApp --}}
                    <a href="javascript:" class="bd-strip-icon share-on-social-media"
                       data-action="{{ route('frontend.blog.details', ['slug' => $blogData['slug'] ?? null]) }}"
                       data-social-media-name="api.whatsapp.com/send?text=">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="white"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/></svg>
                    </a>
                </div>
            </div>

            {{-- ══════════ POPULAR ARTICLES ══════════ --}}
            <div class="bd-reveal bd-reveal-3">
                <div class="bd-section-header">
                    <div>
                        <div class="bd-section-eyebrow">{{ translate('Trending') }}</div>
                        <h2 class="bd-section-title">{{ translate('Popular_articles') }}</h2>
                    </div>
                    <a class="bd-see-more"
                       href="{{ isset($blogPlatform) && $blogPlatform == 'app' ? route('app.blog.popular-blog', ['locale' => request('locale'), 'theme' => request('theme')]) : route('frontend.blog.popular-blog') }}">
                        {{ translate('See_more') }}
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 17 17" fill="none">
                            <path d="M10.8367 2.6847C10.6187 2.4591 10.256 2.4591 10.0304 2.6847C9.81239 2.90267 9.81239 3.26546 10.0304 3.48292L14.119 7.57158H0.626997C0.312484 7.57209 0.0625 7.82208 0.0625 8.13659C0.0625 8.4511 0.312484 8.70922 0.626997 8.70922H14.119L10.0304 12.7903C9.81239 13.0159 9.81239 13.3791 10.0304 13.5966C10.256 13.8222 10.6192 13.8222 10.8367 13.5966L15.8933 8.54002C16.1189 8.32204 16.1189 7.95926 15.8933 7.7418L10.8367 2.6847Z" fill="currentColor"/>
                        </svg>
                    </a>
                </div>
                <div class="row g-4 mb-4">
                    @foreach($popularBlogList as $blogItem)
                        <div class="col-lg-4 col-md-6">
                            @include('web-views.blogs.partials._single-blog-card', ['blogItem' => $blogItem])
                        </div>
                    @endforeach
                </div>
            </div>

        </div>{{-- /container --}}
    </div>{{-- /bd-root --}}
@endsection

@push('script')
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/blog.js') }}"></script>
    <script>
        // Reading progress bar
        (function () {
            const bar = document.getElementById('bdProgressBar');
            const article = document.getElementById('bdArticle');
            if (!bar || !article) return;
            const update = () => {
                const total = article.offsetHeight - window.innerHeight;
                const scrolled = Math.max(0, -(article.getBoundingClientRect().top));
                bar.style.width = Math.min(100, (scrolled / total) * 100) + '%';
            };
            window.addEventListener('scroll', update, { passive: true });
            update();
        })();

        // TOC active state
        (function () {
            const links = document.querySelectorAll('.bd-toc__list a');
            if (!links.length) return;
            const obs = new IntersectionObserver(entries => {
                entries.forEach(e => {
                    if (e.isIntersecting) {
                        links.forEach(l => l.classList.remove('active'));
                        const a = document.querySelector('.bd-toc__list a[href="#' + e.target.id + '"]');
                        if (a) a.classList.add('active');
                    }
                });
            }, { rootMargin: '-20% 0px -70% 0px' });
            links.forEach(l => {
                const el = document.getElementById(l.getAttribute('href').replace('#', ''));
                if (el) obs.observe(el);
            });
        })();
    </script>
@endpush
