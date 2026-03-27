@php
    $blogRoute = (isset($blogPlatform) && $blogPlatform == 'app')
        ? route('app.blog.details', ['slug' => $blogItem?->slug, 'locale' => request('locale'), 'theme' => request('theme')])
        : route('frontend.blog.details', ['slug' => $blogItem?->slug]);

    $catRoute = (isset($blogPlatform) && $blogPlatform == 'app')
        ? route('app.blog.index', ['category' => $blogItem?->category?->name, 'locale' => request('locale'), 'theme' => request('theme')])
        : route('frontend.blog.index', ['category' => $blogItem?->category?->name]);

    $writerRoute = (isset($blogPlatform) && $blogPlatform == 'app')
        ? route('app.blog.index', ['writer' => $blogItem?->writer, 'locale' => request('locale'), 'theme' => request('theme')])
        : route('frontend.blog.index', ['writer' => $blogItem?->writer]);
@endphp

<style>
    .bc-card {
        position: relative;
        background: #fff;
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid #eaedf2;
        box-shadow: 0 2px 12px rgba(0,0,0,.05);
        transition: transform .3s cubic-bezier(.22,.61,.36,1), box-shadow .3s ease;
        cursor: pointer;
        height: 100%;
        display: flex;
        flex-direction: column;
        text-decoration: none;
    }
    .bc-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 16px 40px rgba(0,0,0,.11);
    }

    /* Image area */
    .bc-card__img-wrap {
        position: relative;
        overflow: hidden;
        aspect-ratio: 16/9;
        background: #e8ecf4;
        flex-shrink: 0;
    }
    .bc-card__img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform .55s cubic-bezier(.22,.61,.36,1);
        display: block;
    }
    .bc-card:hover .bc-card__img { transform: scale(1.07); }



    /* Category pill — floated over image */
    .bc-card__cat {
        position: absolute;
        bottom: 12px;
        left: 14px;
        display: inline-flex;
        align-items: center;
        background: rgba(255,255,255,.92);
        backdrop-filter: blur(6px);
        border-radius: 30px;
        padding: 4px 12px;
        font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: .06em;
        text-transform: uppercase;
        color: #1d6ef5;
        text-decoration: none;
        max-width: calc(100% - 28px);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        transition: background .2s;
    }
    .bc-card__cat:hover { background: #fff; color: #1557cc; }

    /* Body */
    .bc-card__body {
        padding: 18px 20px 16px;
        display: flex;
        flex-direction: column;
        flex: 1;
        gap: 10px;
    }

     Title
    .bc-card__title {
        font-family: 'Cormorant Garamond', Georgia, serif;
        font-size: 19px;
        font-weight: 700;
        line-height: 1.3;
        color: #0d0f14;
        text-decoration: none;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        transition: color .2s;
        margin: 0;
    }
    .bc-card__title:hover { color: #1d6ef5; }

    /* Excerpt */
    .bc-card__excerpt {
        font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
        font-size: 13.5px;
        line-height: 1.65;
        color: #8892a4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        margin: 0;
        flex: 1;
    }

    /* Footer */
    .bc-card__footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 12px;
        border-top: 1px solid #eaedf2;
        gap: 8px;
        margin-top: auto;
    }
    .bc-card__author {
        display: flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        min-width: 0;
    }
    .bc-card__avatar {
        width: 28px; height: 28px;
        border-radius: 50%;
        background: linear-gradient(135deg, #1d6ef5, #60a5fa);
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Cormorant Garamond', Georgia, serif;
        font-size: 13px;
        font-weight: 700;
        color: #fff;
        flex-shrink: 0;
    }
    .bc-card__author-name {
        font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
        font-size: 12.5px;
        font-weight: 600;
        color: #3d4452;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 120px;
        transition: color .2s;
    }
    .bc-card__author:hover .bc-card__author-name { color: #1d6ef5; }
    .bc-card__time {
        display: flex;
        align-items: center;
        gap: 4px;
        font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
        font-size: 12px;
        color: #b0b8c8;
        white-space: nowrap;
        flex-shrink: 0;
    }
    .bc-card__time i { font-size: 11px; }

    /* Hover accent bar */
    .bc-card::after {
        content: '';
        position: absolute;
        bottom: 0; left: 0; right: 0;
        height: 3px;
        background: linear-gradient(90deg, #1d6ef5, #60a5fa);
        transform: scaleX(0);
        transform-origin: left;
        transition: transform .35s cubic-bezier(.22,.61,.36,1);
        border-radius: 0 0 16px 16px;
    }
    .bc-card:hover::after { transform: scaleX(1); }
</style>

<div class="bc-card" data-route="{{ $blogRoute }}" onclick="window.location='{{ $blogRoute }}'">

    {{-- Image --}}
    <div class="bc-card__img-wrap">
        <img class="bc-card__img"
             src="{{ getStorageImages(path: $blogItem?->thumbnail_full_url, type:'wide-banner') }}"
             alt="{{ $blogItem?->title }}">


        @if($blogItem?->category?->name)
            <a href="{{ $catRoute }}" class="bc-card__cat" onclick="event.stopPropagation()">
                {{ Str::limit($blogItem?->category?->name, 22) }}
            </a>
        @endif
    </div>

    {{-- Body --}}
    <div class="bc-card__body">
        <a href="{{ $blogRoute }}" class="bc-card__title" onclick="event.stopPropagation()">
            {{ $blogItem?->title }}
        </a>

        @php
            $excerpt = strip_tags($blogItem?->description ?? '');
        @endphp
        @if($excerpt)
            <p class="bc-card__excerpt">{{ $excerpt }}</p>
        @endif

        {{-- Footer --}}
        <div class="bc-card__footer">
            @if($blogItem?->writer)
                <a href="{{ $writerRoute }}" class="bc-card__author" onclick="event.stopPropagation()">
                    <div class="bc-card__avatar">{{ strtoupper(substr($blogItem->writer, 0, 1)) }}</div>
                    <span class="bc-card__author-name" title="{{ $blogItem->writer }}">
                        {{ Str::limit($blogItem->writer, 22) }}
                    </span>
                </a>
            @else
                <div></div>
            @endif

            <div class="bc-card__time">
                <i class="fa fa-clock-o"></i>
                {{ $blogItem->publish_date->diffForHumans() }}
            </div>
        </div>
    </div>

</div>
