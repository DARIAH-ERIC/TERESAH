<footer id="footer" class="row" role="contentinfo">
    <div class="small-12 medium-6 columns">
        <ul class="creative-commons">
            <li>{{ image_tag("icons/creative_commons_cc.png", array("alt" => "Creative Commons CC")) }}</li>
            <li>{{ image_tag("icons/creative_commons_by.png", array("alt" => "Creative Commons BY")) }}</li>
            <li>{{ image_tag("icons/creative_commons_nc.png", array("alt" => "Creative Commons NC")) }}</li>
            <li>{{ image_tag("icons/creative_commons_sa.png", array("alt" => "Creative Commons SA")) }}</li>
        </ul>
        <!-- /creative-commons -->

        <p>{{ Lang::get("views.shared.footer.license.prefix") }}<a href="http://creativecommons.org/licenses/by-nc-sa/4.0/" title="Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International license">Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International</a> {{ Lang::get("views.shared.footer.license.suffix") }}.</p>

        <p>{{ Lang::get("views.shared.footer.dasish") }}</p>
        <p>{{ Lang::get("views.shared.footer.has") }}</p>

        @include("shared._version_information")
    </div>
    <!-- /small-12.medium-6.columns -->

    <div class="small-12 medium-2 columns">
        <h1>TERESAH</h1>

        <nav role="navigation">
            <ul>
                <li>{{ Link_to("/", Lang::get("views.shared.navigation.home.title")) }}</li>
                <li>{{ link_to_route("pages.show", Lang::get("views.shared.navigation.about.name"), array("path" => "about"), array("title" => Lang::get("views.shared.navigation.about.title"))) }}</li>
                <li>{{ link_to_route("pages.show", Lang::get("views.shared.navigation.about.privacy_policy.name"), array("path" => "about/privacy"), array("title" => Lang::get("views.shared.navigation.about.privacy_policy.title"))) }}</li>
                <li>{{ link_to_route("pages.show", Lang::get("views.shared.navigation.about.license.name"), array("path" => "about/license"), array("title" => Lang::get("views.shared.navigation.about.license.title"))) }}</li>
                <li>{{ link_to("/help", Lang::get("controllers.help"))}}</li>
            </ul>
        </nav>
    </div>
    <!-- /small-12.medium-2.columns -->

    <div class="small-12 medium-2 columns">
        <h1>{{ Lang::get("views.shared.navigation.browse.title") }}</h1>

        <nav role="navigation">
            <ul>
                <li>{{ link_to_route("tools.search", Lang::get("views.shared.navigation.browse.search.title"), null, array("title" => Lang::get("views.shared.navigation.browse.search.title"))) }}</li>
                <li>{{ link_to_route("tools.index", Lang::get("views.shared.navigation.browse.all.title"), null, array("title" => Lang::get("views.shared.navigation.browse.all.title"))) }}</li>
                <li>{{ link_to_route("by-facet", Lang::get("views.shared.navigation.browse.facets.title"), null, array("title" => Lang::get("views.shared.navigation.browse.facets.title"))) }}</li>
                <li>{{ link_to_route("tools.popular", Lang::get("views.shared.navigation.browse.popular.title"), null, array("title" => Lang::get("views.shared.navigation.browse.popular.title"))) }}</li>
            </ul>
        </nav>
    </div>
    <!-- /small-12.medium-2.columns -->

    <div class="small-12 medium-2 columns end">
        <h1>{{ Lang::get("views.shared.navigation.contribute.title") }}</h1>

        <nav role="navigation">
            <ul>
                <li><a href="{{ URL::route("sessions.create") }}" title="{{ Lang::get("views.shared.navigation.login.title") }}" title="{{ Lang::get("views.shared.navigation.login.title") }}">{{ Lang::get("views.shared.navigation.login.name") }}</a></li>
                <li>{{ link_to_route("pages.show", Lang::get("views.shared.navigation.about.api.name"), array("path" => "about/api"), array("title" => Lang::get("views.shared.navigation.about.api.title"))) }}</li>
                <li>{{ link_to_route("pages.show", Lang::get("views.shared.navigation.about.rdf.name"), array("path" => "about/rdf"), array("title" => Lang::get("views.shared.navigation.about.rdf.title"))) }}</li>
                <li>{{ link_to("https://github.com/DARIAH-ERIC/TERESAH", Lang::get("views.shared.navigation.fork.name"))}}</li>
            </ul>
        </nav>
    </div>
    <!-- /small-2.columns -->
</footer>
<!-- /footer.row -->

@if(isset($_ENV["ADDTHIS"]))
    <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid={{ $_ENV["ADDTHIS"] }}"></script>
@endif

@if(isset($_ENV["GOOGLE_ANALYTICS"]))
    <script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

      ga('create', '{{ $_ENV["GOOGLE_ANALYTICS"] }}', 'auto');
      ga('send', 'pageview');

    </script>
@endif
