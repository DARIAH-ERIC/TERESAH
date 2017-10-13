<?php

class DataTypesTableSeeder extends Seeder
{ 
    public function run()
    {
        $userId = User::first()->id;
        $dataTypes = array(
            array("label" => "Description", 
                  "rdf_mapping" => "http://schema.org/description",
                  "description" => "A description of the item",
                  "linkable" => false
                 ),
            array("label" => "Url",
                  "rdf_mapping" => "http://schema.org/url", 
                  "description" => "URL of the item",
                  "linkable" => false
                 ),
            array("label" => "Creator",
                  "rdf_mapping" => "http://schema.org/creator",
                  "description" => "The creator/author of this CreativeWork. This is the same as the Author property for CreativeWork"
                 ),
            array("label" => "Keyword", 
                  "rdf_mapping" => "http://schema.org/keywords",
                  "description" => "Keywords describing an item"
                 ),
            array("label" => "License", 
                  "rdf_mapping" => "http://schema.org/license",
                  "description" => "A license document that applies to this content, typically indicated by URL"
                 ),
            array("label" => "Operating System",
                  "rdf_mapping" => "http://schema.org/operatingSystem",
                  "description" => "Operating systems supported (Windows 7, OSX 10.6, Android 1.6)"
                 ),
            array("label" => "Standard", 
                  "rdf_mapping" => "http://schema.org/supportingData",
                  "description" => "Standards used in this item"
            ),
            array("label" => "Type",
                  "rdf_mapping" => "http://purl.org/dc/elements/1.1/type",
                  "description" => "The nature or genre of the resource"
                 ),
            array("label" => "Contributor",
                "rdf_mapping" => "http://schema.org/contributor",
                "description" => "A secondary contributor to the CreativeWork or Event"
            ),
            array("label" => "Application Category",
                "rdf_mapping" => "http://schema.org/applicationCategory",
                "description" => "Type of software application, e.g. 'Game, Multimedia'"
            ),
            array("label" => "Service Type",
                "rdf_mapping" => "http://schema.org/serviceType",
                "description" => "The type of service being offered, e.g. veterans' benefits, emergency relief, etc"
            ),
            array("label" => "Is Used For",
                "rdf_mapping" => "http://purl.org/dc/elements/1.1/subject",
                "description" => "TaDiRAH Research Techniques describes what the tools and services can be used for"
            ),
            array("label" => "Research Object",
                "rdf_mapping" => "http://schema.org/object",
                "description" => "The object upon which the action is carried out. Used with the ontology of TaDiRAH Research Object"
            ),
            array("label" => "Date Created",
                "rdf_mapping" => "http://schema.org/dateCreated",
                "description" => "The data on which the CreativeWork was created or the item was added to a DataFeed",
                "is_date_field" => true
            ),
            array("label" => "Date Modified",
                "rdf_mapping" => "http://schema.org/dateModified",
                "description" => "The data on which the CreativeWork was most recently modified or when the item's entry was modified within a DataFeed",
                "is_date_field" => true
            ),
            array("label" => "Provider",
                "rdf_mapping" => "http://schema.org/provider",
                "description" => "The service provider, service operator, or service performer; the goods producer. Another party (a seller) may offer those services or goods on behalf of the provider. A provider may also serve as the seller"
            ),
            array("label" => "Memory Requirements",
                "rdf_mapping" => "http://schema.org/memoryRequirements",
                "description" => "Minimum memory requirements"
            ),
            array("label" => "Processor Requirements",
                "rdf_mapping" => "http://schema.org/processorRequirements",
                "description" => "Processor architecture required to run the application (e.g. IA64)"
            ),
            array("label" => "Software Requirements",
                "rdf_mapping" => "http://schema.org/softwareRequirements",
                "description" => "Component dependency requirements for application. This includes runtime environments and shared libraries that are not included in the application distribution package, but required to run the application (Examples: DirectX, Java or .NET runtime)"
            ),
            array("label" => "Browser Requirements",
                "rdf_mapping" => "http://schema.org/browserRequirements",
                "description" => "Specifies browser requirements in human-readable text. For example, 'requires HTML5 support'"
            ),
            array("label" => "Storage Requirements",
                "rdf_mapping" => "http://schema.org/storageRequirements",
                "description" => "Storage requirements (free space required)"
            )
        );

        DB::table("data_types")->delete();

        foreach ($dataTypes as $dataType) {
            $dataType["user_id"] = $userId;
            DataType::create($dataType);
        }
    }
}
