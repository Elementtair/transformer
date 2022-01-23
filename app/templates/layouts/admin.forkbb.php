@include ('layouts/crumbs')
@extends ('layouts/main')
    <div class="f-nav-links f-nav-admin{{ $p->mainSuffix or '' }}-links">
@yield ('crumbs')
    </div>
    <div class="f-main f-main-admin{{ $p->mainSuffix or '' }}">
      <div id="fork-a-menu">
@if ($p->aNavigation)
        <nav id="fork-a-nav" class="f-menu">
          <input id="id-an-checkbox" class="f-menu-checkbox" type="checkbox">
          <label id="id-an-label" class="f-menu-toggle" for="id-an-checkbox"></label>
          <ul class="f-menu-items">
    @foreach ($p->aNavigation as $key => $val)
            <li id="id-anav-{{ $key }}" class="f-menu-item"><a class="f-menu-a @if ($key == $p->aIndex) active @endif" href="{{ $val[0] }}"><span class="f-menu-span">{!! __($val[1]) !!}</span></a></li>
    @endforeach
          </ul>
        </nav>
@endif
      </div>
      <div id="forka">
@yield ('content')
      </div>
    </div>
