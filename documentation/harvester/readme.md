Harvesting data in order to create or modify Tools and/or Services
==================================================================

#Introduction

As Supervisor or Administrator, one can also harvest webpages in order to add content to TERESAH.
This page is available in the administrative section of TERESAH under the "Data Sources" dropdown menu and is labeled as "Harvester".
In the context of TERESAH, we talk about harvesting when retrieving information from a single HTML page containing RDFa data. This RDFa data can describe either one Tool / Service or many of those.

#Installation

These harvests are done weekly on Sundays at 3am, but in order to do this, we need to tell our application to launch a script regularly to check if there is a harvest to be done.
You will need to add a cronjob to your server for this, and it has to be launched by your apache user (or the user that owns the TERESAH source code), first open crontab with your user, here `apache`:
```bash
sudo crontab -u apache -e
```
Then add this line to your contab, this will launch a script every 5 minutes by going to TERESAH installation (here /var/www/html/) and starting a php (which is in /usr/bin/php) artisan command:
```bash
*/5 * * * * cd /var/www/html/ && /usr/bin/php artisan cron:run
```

#Metadata Application Profile 

In order to be able to harvest correctly a webpage, you would need to know the [metadata application profile](./../../app/assets/application_profile/HaS_WP8.1_Application_Metadata_Profile.pdf) we use and to correctly use the [vocabularies](./../../app/assets/application_profile/empty.pdf.txt) that goes with it. Of course more information are given here in this document.

###Explanations for webmasters
Adding RDFa to an existing website is relatively easy once you know the properties that are used by the ones monitoring your website, that’s why we created this Metadata Application Profile which are the properties that can be set in HTML in order to have descriptive information for machines. Firstly, you will need to set a vocabulary at the top HTML element that encapsulates the description of your tool or service. This can be at the very top of an HTML page if the page only describes one tool / service or multiple times within the page. Once the vocab has been given, it would be need to be given a type, our AP is set on a type of SoftwareApplication (http://schema.org/SoftwareApplication). Therefore your top level HTML element that encapsulate the description of your item should look like the following (with @vocab and @typeof):
```xml
<article vocab="http://schema.org/" typeof="SoftwareApplication">
    [...]
</article>
```
Within this HTML element, you can then add all the descriptive elements you wish to have for this tool or service. All the descriptive elements you wish to use in RDFa will come from the Metadata Application Profile, for example you wish to add a TaDiRAH Research Activities item like “Publishing”, you can do so by a property “applicationCategory” around your “Publishing” description:
```xml
<a href="http://teresah.dariah.eu/tools/by-facet/application-category/publishing" property="applicationCategory">
    Publishing
</a>
```
 Now, by reading the Metadata Application Profile, you will realize that in order to include a TaDiRAH Research Activities item, you will need to use http://schema.org/applicationCategory and since we are already in the vocabulary of http://schema.org, a simple property “applicationCategory” would suffice to describe that element. However, would you choose to describe the type of the item, which can only be Tool or Service (as defined by NeMO Instrument class), you would need to use dc:type which is outside the scope of our current vocabulary. Therefore, a full property declaration would be needed as follow:
```xml
<span property="http://purl.org/dc/elements/1.1/type">
    Tool
</span>
```


###Following is a full example:
```xml
<div vocab="http://schema.org/" typeof="SoftwareApplication">
   <div property="name">TERESAH Tool</div>
   <div>
       <h3>Available Data</h3>
       <dl>
           <dt>Application Category</dt>
           <dd property="applicationCategory">Transcription</dd>
           <dt>Browser Requirements</dt>
           <dd property="browserRequirements">Requires HTML5 support</dd>
           <dt>Contributor</dt>
           <dd>
               <span property="contributor">Some contributor</span>
           </dd>
           <dt>Creator</dt>
           <dd property="creator">The creator</dd>
           <dt>Date Created</dt>
           <dd property="dateCreated">2017-08-01</dd>
           <dt>Date Modified</dt>
           <dd property="dateModified">2017-08-03</dd>
           <dt>Description</dt>
           <dd property="description">This is simply a tool we needed</dd>
           <dt>Is Used For</dt>
           <dd>
               <span>Gamification</span> <!-- Value to show the user -->
               <span style="display: none;" property="http://purl.org/dc/elements/1.1/subject">Gamification > Dissemination-Crowdsourcing</span> <!-- Value for the harvester -->
           </dd>
           <dt>Keyword</dt>
           <dd property="keywords">game</dd>
           <dt>License</dt>
           <dd>
               <a href="https://creativecommons.org/licenses/by/4.0/" property="license">https://creativecommons.org/licenses/by/4.0/</a>
           </dd>
           <dt>Memory Requirements</dt>
           <dd property="memoryRequirements">8GB</dd>
           <dt>Operating System</dt>
           <dd property="operatingSystem">Windows 10</dd>
           <dt>Processor Requirements</dt>
           <dd property="processorRequirements">IA64</dd>
           <dt>Provider</dt>
           <dd property="provider">Provider of service</dd>
           <dt>Research Object</dt>
           <dd property="object">Images</dd>
           <dt>Service Type</dt>
           <dd property="serviceType">Processing service</dd>
           <dt>Software Requirements</dt>
           <dd property="softwareRequirements">Java 7+</dd>
           <dt>Standard</dt>
           <dd>
               <span property="supportingData">XML</span>, <span property="supportingData">JPG</span>
           </dd>
           <dt>Storage Requirements</dt>
           <dd property="storageRequirements">100GB</dd>
           <dt>Type</dt>
           <dd property="http://purl.org/dc/elements/1.1/type">Tool</dd>
           <dt>Url</dt>
           <dd>
               <a href="http://teresah.dev/" property="url">http://teresah.dev/</a>
           </dd>
       </dl>
   </div>
</div>
```