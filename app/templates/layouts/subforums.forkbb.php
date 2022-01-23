@foreach ($forums as $cur)
    @if ($cur->redirect_url)
            <li id="forum-{{ $cur->id }}" class="f-row f-fredir">
              <div class="f-cell f-cmain">
                <div class="f-ficon"></div>
                <div class="f-finfo">
                  <h3 class="f-finfo-h3">
                    <span class="f-frsname">
                      <span class="f-fredirtext">{!! __('Link to') !!}</span>
                      <a class="f-ftname" href="{{ $cur->redirect_url }}">{{ $cur->forum_name }}</a>
                    </span>
                  </h3>
        @if ('' != $cur->forum_desc)
                  <p class="f-fdesc">{!! $cur->forum_desc !!}</p>
        @endif
                </div>
              </div>
            </li>
    @else
            <li id="forum-{{ $cur->id }}" class="f-row @if ($cur->tree->newMessages) f-fnew @endif">
              <div class="f-cell f-cmain">
                <div class="f-ficon"></div>
                <div class="f-finfo">
                  <h3 class="f-finfo-h3">
                    <span class="f-frsname">
                      <a class="f-ftname" href="{{ $cur->link }}">{{ $cur->forum_name }}</a>
                    </span>
        @if ($cur->tree->newMessages)
                    <small class="f-fnew"><a href="{{ $cur->linkNew }}" title="{{ __('New posts') }}"><span class="f-newtxt">{!! __('New posts') !!}</span></a></small>
        @endif
                  </h3>
        @if ($cur->subforums)
                  <dl class="f-inline f-fsub"><!-- inline -->
                    <dt>{!! __(['Sub forum', \count($cur->subforums)]) !!}</dt>
            @foreach ($cur->subforums as $sub)
                @if ($sub->redirect_url)
                    <dd>
                      <span class="f-frdrsub">
                        <a href="{{ $sub->redirect_url }}">{{ $sub->forum_name }}</a>
                      </span>
                    </dd>
                @else
                    <dd><a href="{{ $sub->link }}">{{ $sub->forum_name }}</a></dd>
                @endif
            @endforeach
                  </dl><!-- endinline -->
        @endif
        @if ('' != $cur->forum_desc)
                  <p class="f-fdesc">{!! $cur->forum_desc !!}</p>
        @endif
        @if ($cur->moderators)
                  <dl class="f-inline f-modlist"><!-- inline -->
                    <dt>{!! __(['Moderated by', \count($cur->moderators)]) !!}</dt>
            @foreach ($cur->moderators as $mod)
                @if ($mod['link'])
                    <dd><a href="{{ $mod['link'] }}">{{ $mod['name'] }}</a></dd>
                @else
                    <dd>{{ $mod['name'] }}</dd>
                @endif
            @endforeach
                  </dl><!-- endinline -->
        @endif
                </div>
              </div>
              <div class="f-cell f-cstats">
                <span>{!! __(['%s Topic', $cur->tree->num_topics, num($cur->tree->num_topics)]) !!}</span>
                <span>{!! __(['%s Post', $cur->tree->num_posts, num($cur->tree->num_posts)]) !!}</span>
              </div>
              <div class="f-cell f-clast">
        @if ($cur->tree->last_post_id)
                <span class="f-cltopic">{!! __(['Last post in the topic "<a href="%1$s">%2$s</a>"', $cur->tree->linkLast, $cur->tree->censorLast_topic]) !!}</span>
                <span class="f-clposter">{!! __(['by %s', $cur->tree->last_poster]) !!}</span>
                <span class="f-cltime">{{ dt($cur->tree->last_post) }}</span>
        @else
                <span class="f-cltopic">{!! __('Never') !!}</span>
        @endif
              </div>
            </li>
    @endif
@endforeach
