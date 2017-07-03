<?php

/**
 * Created by IntelliJ IDEA.
 * User: yoann
 * Date: 23.06.17
 * Time: 10:58
 */
class DataTypeOptionsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table("data_type_options")->delete();

        $dataTypes = DataType::all();
        $types = array();
        foreach($dataTypes as $dataType) {
            $types[$dataType->slug] = $dataType->id;
        }

        /**
         * For Application Category
         */
        $dataTypeOptions = array(
            array("label" => "Encoding",
                "value" => "Encoding",
                "order" => 1
            ),
            array("label" => "Gamification > Dissemination-Crowdsourcing",
                "value" => "Gamification > Dissemination-Crowdsourcing",
                "order" => 2
            ),
            array("label" => "Georeferencing > Enrichment-Annotation",
                "value" => "Georeferencing > Enrichment-Annotation",
                "order" => 3
            )
        );
        foreach ($dataTypeOptions as $dataTypeOption) {
            $dataTypeOption["data_type_id"] = $types["application-category"];
            DataTypeOption::create($dataTypeOption);
        }

        /**
         * For Service Type
         */
        $dataTypeOptions = array(
            array("label" => "Hosting services",
                "value" => "Hosting services",
                "order" => 1
            ),
            array("label" => "Processing services",
                "value" => "Processing services",
                "order" => 2
            ),
            array("label" => "Support services",
                "value" => "Support services",
                "order" => 3
            ),
            array("label" => "Access to resources",
                "value" => "Access to resources",
                "order" => 4
            ),
            array("label" => "Access/availability to software",
                "value" => "Access/availability to software",
                "order" => 5
            )
        );
        foreach ($dataTypeOptions as $dataTypeOption) {
            $dataTypeOption["data_type_id"] = $types["service-type"];
            DataTypeOption::create($dataTypeOption);
        }

        /**
         * For Is Used For
         */
        $dataTypeOptions = array(
            array("label" => "1_Capture",
                "value" => "1_Capture",
                "order" => 1
            ),
                array("label" => "Conversion",
                    "value" => "- Conversion",
                    "order" => 2
                ),
                array("label" => "Data Recognition",
                    "value" => "- Data Recognition",
                    "order" => 3
                ),
                array("label" => "Discovering",
                    "value" => "- Discovering",
                    "order" => 4
                ),
                array("label" => "Gathering",
                    "value" => "- Gathering",
                    "order" => 5
                ),
                array("label" => "Imaging",
                    "value" => "- Imaging",
                    "order" => 6
                ),
                array("label" => "Recording",
                    "value" => "- Recording",
                    "order" => 7
                ),
                array("label" => "Transcription",
                    "value" => "- Transcription",
                    "order" => 8
                ),
            array("label" => "2_Creation",
                "value" => "2_Creation",
                "order" => 9
            ),
                array("label" => "Designing",
                    "value" => "- Designing",
                    "order" => 10
                ),
                array("label" => "Programmimg",
                    "value" => "- Programming",
                    "order" => 11
                ),
                array("label" => "Translation",
                    "value" => "- Translation",
                    "order" => 12
                ),
                array("label" => "Web development",
                    "value" => "- Web development",
                    "order" => 13
                ),
                array("label" => "Writing",
                    "value" => "- Writing",
                    "order" => 14
                ),
            array("label" => "3_Enrichment",
                "value" => "3_Enrichment",
                "order" => 15
            ),
                array("label" => "Annotation",
                    "value" => "- Annotation",
                    "order" => 16
                ),
                array("label" => "Cleanup",
                    "value" => "- Cleanup",
                    "order" => 17
                ),
                array("label" => "Editing",
                    "value" => "-  Editing",
                    "order" => 18
                ),
            array("label" => "4_Analysis",
                "value" => "4_Analysis",
                "order" => 19
            ),
                array("label" => "Content Analysis",
                    "value" => "- Content Analysis",
                    "order" => 20
                ),
                array("label" => "Network Analysis",
                    "value" => "- Network Analysis",
                    "order" => 21
                ),
                array("label" => "Relational Analysis",
                    "value" => "- Relational Analysis",
                    "order" => 22
                ),
                array("label" => "Spatial Analysis",
                    "value" => "- Spatial Analysis",
                    "order" => 23
                ),
                array("label" => "Structural Analysis",
                    "value" => "- Structural Analysis",
                    "order" => 24
                ),
                array("label" => "Stylistic Analysis",
                    "value" => "- Stylistic Analysis",
                    "order" => 25
                ),
                array("label" => "Visualization",
                    "value" => "- Visualization",
                    "order" => 26
                ),
            array("label" => "5_Interpretation",
                "value" => "5_Interpretation",
                "order" => 27
            ),
                array("label" => "Contextualizing",
                    "value" => "- Contextualizing",
                    "order" => 28
                ),
                array("label" => "Modeling",
                    "value" => "- Modeling",
                    "order" => 29
                ),
                array("label" => "Theorizing",
                    "value" => "- Theorizing",
                    "order" => 30
                ),
            array("label" => "6_Storage",
                "value" => "6_Storage",
                "order" => 31
            ),
                array("label" => "Archiving",
                    "value" => "- Archiving",
                    "order" => 32
                ),
                array("label" => "Identifying",
                    "value" => "- Identifying",
                    "order" => 33
                ),
                array("label" => "Organizing",
                    "value" => "- Organizing",
                    "order" => 34
                ),
                array("label" => "Preservation",
                    "value" => "- Preservation",
                    "order" => 35
                ),
            array("label" => "7_Dissemination",
                "value" => "7_Dissemination",
                "order" => 36
            ),
                array("label" => "Collaboration",
                    "value" => "- Collaboration",
                    "order" => 37
                ),
                array("label" => "Commenting",
                    "value" => "- Commenting",
                    "order" => 38
                ),
                array("label" => "Communicating",
                    "value" => "- Communicating",
                    "order" => 39
                ),
                array("label" => "Crowdsourcing",
                    "value" => "- Crowdsourcing",
                    "order" => 40
                ),
                array("label" => "Publishing",
                    "value" => "- Publishing",
                    "order" => 41
                ),
                array("label" => "Sharing",
                    "value" => "- Sharing",
                    "order" => 42
                ),
            array("label" => "0_Meta-Activities",
                "value" => "0_Meta-Activities",
                "order" => 43
            ),
                array("label" => "Meta: Assessing",
                    "value" => "- Meta: Assessing",
                    "order" => 44
                ),
                array("label" => "Meta: Community Building",
                    "value" => "- Meta: Community Building",
                    "order" => 45
                ),
                array("label" => "Meta: Give Overview",
                    "value" => "- Meta: Give Overview",
                    "order" => 46
                ),
                array("label" => "Meta: Project Management",
                    "value" => "- Meta: Project Management",
                    "order" => 47
                ),
                array("label" => "Meta: Teaching / Learning",
                    "value" => "- Meta: Teaching / Learning",
                    "order" => 48
                )
        );
        foreach ($dataTypeOptions as $dataTypeOption) {
            $dataTypeOption["data_type_id"] = $types["is-used-for"];
            DataTypeOption::create($dataTypeOption);
        }

        /**
         * For Research Object
         */
        $dataTypeOptions = array(
            array("label" => "Artifacts",
                "value" => "Artifacts",
                "order" => 1
            ),
            array("label" => "Bibliographic Listings",
                "value" => "Bibliographic Listings",
                "order" => 2
            ),
            array("label" => "Code",
                "value" => "Code",
                "order" => 3
            ),
            array("label" => "Computers",
                "value" => "Computers",
                "order" => 4
            ),
            array("label" => "Curricula",
                "value" => "Curricula",
                "order" => 5
            ),
            array("label" => "Digital Humanities",
                "value" => "Digital Humanities",
                "order" => 6
            ),
            array("label" => "Data",
                "value" => "Data",
                "order" => 7
            ),
            array("label" => "File",
                "value" => "File",
                "order" => 8
            ),
            array("label" => "Images",
                "value" => "Images",
                "order" => 9
            ),
            array("label" => "Images (3D)",
                "value" => "Images (3D0",
                "order" => 10
            ),
            array("label" => "Infrastructure",
                "value" => "Infrastructure",
                "order" => 11
            ),
            array("label" => "Interaction",
                "value" => "Interaction",
                "order" => 12
            ),
            array("label" => "Language",
                "value" => "Language",
                "order" => 13
            ),
            array("label" => "Link",
                "value" => "Link",
                "order" => 14
            ),
            array("label" => "Literature",
                "value" => "Literature",
                "order" => 15
            ),
            array("label" => "Manuscript",
                "value" => "Manuscript",
                "order" => 16
            ),
            array("label" => "Map",
                "value" => "Map",
                "order" => 17
            ),
            array("label" => "Metadata",
                "value" => "Metadata",
                "order" => 18
            ),
            array("label" => "Methods",
                "value" => "Methods",
                "order" => 19
            ),
            array("label" => "Multimedia",
                "value" => "Multimedia",
                "order" => 20
            ),
            array("label" => "Multimodal",
                "value" => "Multimodal",
                "order" => 21
            ),
            array("label" => "Named Entities",
                "value" => "Named Entities",
                "order" => 22
            ),
            array("label" => "Persons",
                "value" => "Persons",
                "order" => 23
            ),
            array("label" => "Projects",
                "value" => "Projects",
                "order" => 24
            ),
            array("label" => "Research",
                "value" => "Research",
                "order" => 25
            ),
            array("label" => "Research Process",
                "value" => "Research Process",
                "order" => 26
            ),
            array("label" => "Research Results",
                "value" => "Research Results",
                "order" => 27
            ),
            array("label" => "Sheet Music",
                "value" => "Sheet Music",
                "order" => 28
            ),
            array("label" => "Software",
                "value" => "Software",
                "order" => 29
            ),
            array("label" => "Sound",
                "value" => "Sound",
                "order" => 30
            ),
            array("label" => "Standards",
                "value" => "Standards",
                "order" => 31
            ),
            array("label" => "Text",
                "value" => "Text",
                "order" => 32
            ),
            array("label" => "Text Bearing Objects",
                "value" => "Text Bearing Objects",
                "order" => 33
            ),
            array("label" => "Toolds",
                "value" => "Tools",
                "order" => 34
            ),
            array("label" => "Video",
                "value" => "Video",
                "order" => 35
            ),
            array("label" => "VREs",
                "value" => "VREs",
                "order" => 36
            )
        );
        foreach ($dataTypeOptions as $dataTypeOption) {
            $dataTypeOption["data_type_id"] = $types["research-object"];
            DataTypeOption::create($dataTypeOption);
        }
    }
}