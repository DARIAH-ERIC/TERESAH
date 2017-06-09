@if (isset($type) && $type == "block-grid")
<li>
    @endif
    <article class="tool align row" itemscope itemtype="http://schema.org/SoftwareApplication">
        <div class="small-3 columns">
            <a href="{{ URL::route("tools.show", $tool->slug) }}" class="symbol" title="{{{ $tool->name }}}"><abbr title="{{{ $tool->name }}}">{{{ $tool->abbreviation }}}</abbr></a>
        </div>
        <!-- /small-3.columns -->

        <div class="small-9 columns">
            @if (!$tool->is_filled)
            <h1 itemprop="name"><a href="{{ URL::route("tools.show", $tool->slug) }}" title="All the information are not filled" class="red-warning">{{{ $tool->name }}}</a></h1>
            @else
            <h1 itemprop="name"><a href="{{ URL::route("tools.show", $tool->slug) }}" title="{{{ $tool->name }}}">{{{ $tool->name }}}</a></h1>
            @endif

            <p>about {{{ BaseHelper::diffForHumans($tool->updated_at) }}}</p>
        </div>
        <!-- /small-9.columns -->
    </article>
    <!-- /tool.align.row -->
    @if (isset($type) && $type == "block-grid")
</li>
@endif
