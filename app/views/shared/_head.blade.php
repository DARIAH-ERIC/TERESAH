<!DOCTYPE html>

<html dir="ltr" lang="{{ App::getLocale() }}">

@if (PageHelper::showVersionInformation())
    <!-- TERESAH {{ Lang::get("views.shared.messages.current_version.commit_id.message") }} {{ PageHelper::getCurrentCommitId() }} ({{ PageHelper::getCurrentCommitDate() }}) -->
    <!-- Environment: {{ App::environment() }} -->
@endif

<head>
    <meta charset="utf-8" />

<!-- Title -->
    <title>@if (isset($preTitle)) {{$preTitle}} - @endif{{ Lang::get("views.shared.meta.title") }}</title>

<!-- Meta -->
    <meta name="author" content="{{ Lang::get("views.shared.meta.author") }}" />
    <meta name="description" content="{{ Lang::get("views.shared.meta.description") }}" />
    <meta name="revisit-after" content="7 days" />
    {{ PageHelper::robotsMetaTag() }}
    <meta name="viewport" content="width = device-width, initial-scale = 1.0" />

@if (isset($toolSlug))
<!-- RDF alternatives -->
    @if(in_array("XML", Config::get("teresah.tool_rdf_formats")))
        <link rel="alternate" type="application/rdf+xml" href="{{ URL::to("/tools/" . $toolSlug . ".rdfxml") }}" title="Structured Descriptor Document (RDF/XML format)" />
    @endif
    @if(in_array("Turtle", Config::get("teresah.tool_rdf_formats")))
        <link rel="alternate" type="text/rdf+n3" href="{{ URL::to("/tools/" . $toolSlug . ".n3") }}" title="Structured Descriptor Document (N3/Turtle format)" />
    @endif
    @if(in_array("NTriples", Config::get("teresah.tool_rdf_formats")))
        <link rel="alternate" type="text/plain" href="{{ URL::to("/tools/" . $toolSlug . ".ntriples") }}" title="Structured Descriptor Document (N-Triples format)" />
    @endif
    @if(in_array("JsonLD", Config::get("teresah.tool_rdf_formats")))
        <link rel="alternate" type="application/ld+json" href="{{ URL::to("/tools/" . $toolSlug . ".jsonld") }}" title="Structured Descriptor Document (JSON-LD format)" />
    @endif
@endif

<!-- Favicon -->
    <link href="{{ url("/") }}/assets/favicon.png" rel="icon" type="image/x-icon" />

<!-- Stylesheets -->
    {{ stylesheet_link_tag() }}

<!-- JavaScripts -->
    {{ javascript_include_tag() }}

<!-- JavaScript Cookie consent -->
    <script type="text/javascript">
        window.addEventListener("load", function(){
            window.cookieconsent.initialise({
                "palette": {
                    "popup": {
                        "background": "#553DAB"
                    },
                    "button": {
                        "background": "#45A275",
                        "text": "#ffffff"
                    }
                },
                "content": {
                    "message": "This website uses cookies to ensure you get the best experience on our website.",
                    "dismiss": "Got it!",
                    "link": "Learn more",
                    "href": "http://ec.europa.eu/ipg/basics/legal/cookies/index_en.htm"
                }
            })});
    </script>

@if(isset($_ENV["GOOGLE_ANALYTICS"]))
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $_ENV['GOOGLE_ANALYTICS'] }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ $_ENV["GOOGLE_ANALYTICS"] }}');
    </script>
@endif
</head>
