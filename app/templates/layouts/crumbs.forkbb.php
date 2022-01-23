@section ('crumbs')
      <nav>
        <ul class="f-crumbs">
    @foreach ($p->crumbs as $cur)
        @if (\is_object($cur[0]))
          <li class="f-crumb @if ($cur[0]->is_subscribed) f-subscribed @endif"><!-- inline -->
            <a class="f-crumb-a @if ($cur[2]) active @endif" href="{{ $cur[0]->link }}">{!! __($cur[1]) !!}</a>
          </li><!-- endinline -->
        @else
          <li class="f-crumb"><!-- inline -->
            @if ($cur[0])
            <a class="f-crumb-a @if ($cur[2]) active @endif" href="{{ $cur[0] }}">{!! __($cur[1]) !!}</a>
            @else
            <span @if ($cur[2]) class="active" @endif>{!! __($cur[1]) !!}</span>
            @endif
          </li><!-- endinline -->
        @endif
    @endforeach
        </ul>
      </nav>
@endsection
