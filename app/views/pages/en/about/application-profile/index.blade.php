@extends("layouts.default")

@section("content")
<section class="row">
    <div class="small-12 medium-10 columns small-centered">
        <h1>The HaS metadata application profile</h1>
        <p>The <a href="http://has.dariah.eu/" target="_blank">Humanities at Scale (HaS) project</a> has developed a metadata application profile to support the structured description of Digital Humanities (DH) tools and services. It is intended to be implemented on the websites of projects and tool providers using RDFa. This not only allows for the metadata to be harvested by TERESAH, but also improves the findability of the respective tools and services by search engines.</p>
        <p>The application profile is based on previous DH community efforts – namely the <a href="http://tadirah.dariah.eu/vocab/index.php" target="_blank">Taxonomy of Digital Research Activities in the Humanities (TaDiRAH)</a> and the <a href="http://www.nedimah.eu/content/nedimah-methods-ontology-nemo" target="_blank">NeDiMAH methods ontology (NeMO)</a> – and established metadata standards (schema.org and Dublin Core). It consists of 22 elements. Four of them (name, type, description and url) are mandatory. However, using more terms will enable you to describe your tool or service more precisely and will yield more accurate search results for future users.</p>
        <p>The HaS metadata application profile can be retrieved <a href="{{ url("/") }}/assets/HaS_WP8.1_Application_Metadata_Profile.pdf">here</a>.<br/>
            A report that describes the development of the application profile in more detail can be retrieved under <a href="https://hal.archives-ouvertes.fr/hal-01637051v1" target="_blank">https://hal.archives-ouvertes.fr/hal-01637051v1</a>. It also contains a short chapter on how to use RDFa.</p>
    </div>
    <!-- /small-12.medium-10.columns.small-centered -->
</section>
<!-- /row -->
@stop
