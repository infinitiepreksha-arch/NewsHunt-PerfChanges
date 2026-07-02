<!-- Wrapper start -->
<div id="wrapper" class="wrap overflow-hidden-x">

    <div class="section py-4 lg:py-6 xl:py-8">
        <div class="container max-w-xl">
            <div class="page-wrap panel vstack gap-4 lg:gap-6 xl:gap-8">
                <header class="page-header panel vstack justify-center gap-2 lg:gap-4 text-center">
                    <div class="panel">
                        <h1 class="h3 lg:h1 m-0">{{ $title }}</h1>
                    </div>
                </header>
                <div class="text-black page-content panel fs-6 md:fs-5">{!! $terms_conditions->value ?? '' !!}</div>
            </div>
        </div>
    </div>
</div>
