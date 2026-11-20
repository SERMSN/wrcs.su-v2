@extends('layouts.app')

@section('content')
    <section class="notfound">
        <div class="container notfound__inner" data-reveal>
            <p class="eyebrow">// error 404</p>
            <h1 class="notfound__title">{{ __('ui.notfound.title') }}</h1>
            <p class="notfound__lead">{{ __('ui.notfound.lead') }}</p>

            <pre class="notfound__code mono"><code><span class="t-prompt">~ $</span> kubectl get page --all-namespaces
<span class="t-out">Error from server (NotFound): pages "this-one" not found</span>
<span class="t-prompt">~ $</span> <span class="t-caret"></span></code></pre>

            <a class="btn btn--primary" href="{{ url('/') }}">{{ __('ui.notfound.cta') }}</a>
        </div>
    </section>
@endsection
