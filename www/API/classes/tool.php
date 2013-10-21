<?php
	class Tool extends Table {
	
		##Getting DB
		function __construct() {
			global $DB;
			$this->DB = $DB;
		}
		
	############
	#
	#	TOOLS
	#
	############
		
	
		private function getShorname($str, $replace=array("'"), $delimiter='-') {
			setlocale(LC_ALL, 'en_US.UTF8');
			if( !empty($replace) ) {
				$str = str_replace((array)$replace, ' ', $str);
			}

			$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
			$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
			$clean = strtolower(trim($clean, '-'));
			$clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);

			return $clean;
		}
	
	#############
	#
	#		DELETE
	#
	#############
	function delete($toolUID) {
		$req = "DELETE FROM tool WHERE tool_uid = ? LIMIT 1";
		$req = $this->DB->prepare($req);
		$req->execute(array($toolUID));
		
	}
	
	#########
	#
	#		Insert
	#
	###########
	
		
		function insertDescription($toolUID, $data) {
			$ret = "Nothing happends";
			if(isset($data["provider"])) {
				#If we have a Data Provider, it is an external Description
				$req = "INSERT INTO external_description VALUES ('', ?, ?, ?, ?); ";
				$req = $this->DB->prepare($req);
				try {
					#echo "hi";
					#print_r($data);
					$ret = $req->execute(array($toolUID, $data["description"], $data["source"], $data["provider"]));
					#print ($req);
					#return $ret->rowCount ();
				} catch(Exception $e) {
					echo "Erreur";
					$erreure = 'Erreur : '.$e->getMessage().'<br />';
					$erreure .= 'N° : '.$e->getCode();
					return array("Error" => $erreure);
				}
			
			} else {
				#Else, it is a simple description
				$req = "INSERT INTO description VALUES ('', ?, ?, ?, ?, ?, CURDATE(), NULL, ?,?,?); ";
				$req = $this->DB->prepare($req);
				try {
					$ret = $req->execute(array($data["name"], $data["description"], $data["version"], $data["homepage"],  $data["available_from"],  $data["facets"]["Licence"]["request"][0], $toolUID, $_SESSION["user"]["id"]));
					return array("Success" => "Description registered", "uid" => $this->DB->lastInsertId());
				} catch(Exception $e) {
					$erreure = 'Erreur : '.$e->getMessage().'<br />';
					$erreure .= 'N° : '.$e->getCode();
					return array("Error" => $erreure);
				}
			}
			return $ret;
		}
		
		function insertTool($data) {
			if(!isset($data["name"])) {
				return array("Error" => "The tool couldn't be save because no name was given", "fieldsError" => "name");
			}
			$req = "INSERT INTO tool (tool_uid, shortname) VALUES ( NULL , ? )";
			$req = $this->DB->prepare($req);
			$req->execute(array($this->getShorname($data["name"])));
			
			//Check
			if($req->rowCount() == 1) {
				return array("uid" => $this->DB->lastInsertId(), "shortname" => $this->getShorname($data["name"]));
			} else {
				return array("Error" => "The tool couldn't be save");
			}
			
		}
		
		function linkFacets($data) {
			if(!isset($data["facet"]) && !isset($data["element"]) && !isset($data["tool"])) {
				return array("Error" => "One facet couldn't be save. Missing data");
			}
			$table = parent::getTable($data["facet"]);
			$element = $data["element"];
			$toolUID = $data["tool"];
			
			$sql = "INSERT INTO ".$table["link"]["name"]." (".$table["link"]["tool"].", ".$table["link"]["item"]." ) VALUES ( ? , ? )";
			$req = $this->DB->prepare($sql);
			$req->execute(array($toolUID, $element));
			
			//Check
			if($req->rowCount() == 1) {
				return array("uid" => $this->DB->lastInsertId(), $data["facet"]);
			} else {
				return array("Error" => "The facet ".$data["facet"]." (".$table["table"]["name"].") couldn't be save");//, "debug" => array("request" => $sql, "input" => $data));
			}
			
		}
	#########
	#
	#		Select
	#
	#########
	
		function getFacet($name, $id) {
			switch ($name) {
				#####
				#
				#	TODO : ToolType, Licence Type
				#
				#####
				case "Suite":
					return $this->getSuite($id, "Reverse");
					break;
				case "Publication":
					return $this->getPublications($id, "Reverse");
					break;
				case "ToolType":
					return $this->getToolType($id, "Reverse");
					break;
				case "Platform":
					return $this->getPlatform($id, "Reverse");
					break;
				case "Standard":
					return $this->getStandards($id, "Reverse");
					break;
				case "Keyword":
					return $this->getKeywords($id, "Reverse");
					break;
				case "Licence":
					return $this->getLicence($id, "Reverse");
					break;
				case "Developer":
					return $this->getDevelopers($id, "Reverse");
					break;
				case "ApplicationType":
					return $this->getApplicationType($id, "Reverse");
					break;
				case "Feature":
					return $this->getFeatures($id, "Reverse");
					break;
				default:
					return false;
			}
		}
		function getDevelopers($id, $mode = "Default") {
			#REVERSE MODE : Get only data about a keyword with ID = X
			if($mode == "Reverse") {
				$req = "SELECT d.developer_uid as UID, d.name, d.contact FROM developer d WHERE d.developer_uid = ? LIMIT 1";
				$req = $this->DB->prepare($req);
				$req->execute(array($id));
			
			#DEFAULT MODE : Get keyword for tool
			} else {
				$req = "SELECT d.developer_uid as UID, d.name, d.contact FROM developer d, tool_has_developer td WHERE td.developer_uid = d.developer_uid AND td.tool_uid = ?";
				$req = $this->DB->prepare($req);
				$req->execute(array($id));
				
			}
			if($req->rowCount() > 0) {
				$data = $req->fetchAll(PDO::FETCH_ASSOC);
				$ret = array();
				foreach($data as &$keyword) {
					if($keyword["contact"] != null) {
						$ret[] = array(
									"name" => $keyword["name"],
									"contact" => $keyword["contact"],
									"identifier" => $keyword["UID"]
								);
					}	else {
						$ret[] = array(
									"name" => $keyword["name"],
									"identifier" => $keyword["UID"]
								);
					}
				}
				#Hack for formation for Reverse mode
				if($mode == "Reverse") { $ret = $ret[0]; }
				return $ret;
			} else {
				return false;
			}
		}


		function getPublications($id, $mode = "Default") {
			#REVERSE MODE : Get only data about a keyword with ID = X
			if($mode == "Reverse") {
				$req = "SELECT p.publication_uid as UID, p.reference as name FROM publication p WHERE p.publication_uid = ? LIMIT 1";
			
			#DEFAULT MODE : Get keyword for tool
			} else {
				$req = "SELECT  p.publication_uid as UID, p.reference as name FROM publication p, tool_has_publication tp WHERE tp.publication_uid = p.publication_uid AND tp.tool_uid = ?";
				
			}
			#
			#
			$req = $this->DB->prepare($req);
			$req->execute(array($id));
			#
			#
			if($req->rowCount() > 0) {
				$data = $req->fetchAll(PDO::FETCH_ASSOC);
				$ret = array();
				foreach($data as &$keyword) {
					$ret[] = array(
						"name" => $keyword["name"],
						"identifier" => $keyword["UID"]
					);
				}
				#Hack for formation for Reverse mode
				if($mode == "Reverse") { $ret = $ret[0]; }
				return $ret;
			} else {
				return false;
			}
		}
		
		function getFeatures($id, $mode = "Default") {
			#REVERSE MODE : Get only data about a keyword with ID = X
			if($mode == "Reverse") {
				$req = "SELECT f.feature_uid as UID, f.name, f.description FROM feature f WHERE f.feature_uid = ? LIMIT 1";
			
			#DEFAULT MODE : Get keyword for tool
			} else {
				$req = "SELECT f.feature_uid as UID, f.name, f.description FROM feature f, tool_has_feature tf WHERE tf.feature_uid = f.feature_uid AND tf.tool_uid = ?";
				
			}
			#
			#
			$req = $this->DB->prepare($req);
			$req->execute(array($id));
			#
			#
			if($req->rowCount() > 0) {
				$data = $req->fetchAll(PDO::FETCH_ASSOC);
				$ret = array();
				foreach($data as &$keyword) {
					$ret[] = array(
						"name" => $keyword["name"],
						"informations" => array(
							"description" => $keyword["description"]
						),
						"identifier" => $keyword["UID"]
					);
				}
				#Hack for formation for Reverse mode
				if($mode == "Reverse") { $ret = $ret[0]; }
				return $ret;
			} else {
				return false;
			}
		}		
		function getProjects($id, $mode = "Default") {
			#REVERSE MODE : Get only data about a keyword with ID = X
			if($mode == "Reverse") {
				$req = "SELECT p.project_uid as UID, p.title as name, p.description, p.contact  FROM project p WHERE p.project_uid = ? LIMIT 1";
			
			#DEFAULT MODE : Get keyword for tool
			} else {
				$req = "SELECT  p.project_uid as UID, p.title as name, p.description, p.contact FROM project p, tool_has_project tp WHERE tp.project_uid = p.project_uid AND tp.tool_uid = ?";
				
			}
			#
			#
			$req = $this->DB->prepare($req);
			$req->execute(array($id));
			#
			#
			if($req->rowCount() > 0) {
				$data = $req->fetchAll(PDO::FETCH_ASSOC);
				$ret = array();
				foreach($data as &$keyword) {
					$ret[] = array(
						"name" => $keyword["name"],
						"informations" => array(
							"description" => $keyword["description"],
							"contact" => $keyword["contact"]
						),
						"identifier" => $keyword["UID"]
					);
				}
				#Hack for formation for Reverse mode
				if($mode == "Reverse") { $ret = $ret[0]; }
				return $ret;
			} else {
				return false;
			}
		}
		
		function getStandards($id, $mode = "Default") {
			#REVERSE MODE : Get only data about a keyword with ID = X
			if($mode == "Reverse") {
				$req = "SELECT s.standard_uid as UID, s.title as name, s.version, s.source  FROM standard s WHERE s.standard_uid = ? LIMIT 1";
			
			#DEFAULT MODE : Get keyword for tool
			} else {
				$req = "SELECT s.standard_uid as UID, s.title as name, s.version, s.source FROM standard s, tool_has_standard ts WHERE ts.standard_uid = s.standard_uid AND ts.tool_uid = ?";
				
			}
			#
			#
			$req = $this->DB->prepare($req);
			$req->execute(array($id));
			#
			#
			if($req->rowCount() > 0) {
				$data = $req->fetchAll(PDO::FETCH_ASSOC);
				$ret = array();
				foreach($data as &$keyword) {
					$ret[] = array(
						"name" => $keyword["name"],
						"informations" => array(
							"version" => $keyword["version"],
							"source" => $keyword["source"]
						),
						"identifier" => $keyword["UID"]
					);
				}
				#Hack for formation for Reverse mode
				if($mode == "Reverse") { $ret = $ret[0]; }
				return $ret;
			} else {
				return false;
			}
		}
		
		function getDescriptions($toolUID, $external = true, $userName = false) {
			$userName = true;
			#We first fetch our description
			
			$req = "SELECT d.user_uid as User, d.title, d.description, d.version, d.homepage, d.registered, d.available_from, d.registered_by, FROM description dWHERE d.tool_uid = ?";
			
			if($userName) {
				$req = "SELECT d.user_uid as User_UID, u.name as User, d.title, d.description, d.version, d.homepage, d.registered, d.available_from, d.registered_by FROM description d, user u WHERE d.tool_uid = ? AND u.user_uid = d.user_uid";
			}
			
			$req = $this->DB->prepare($req);
			$req->execute(array($toolUID));
			
			if($req->rowCount() ==1) {
				#Which we put into a ret array
				$ret = $req->fetch(PDO::FETCH_ASSOC);
				#Format User Data
				if($userName) {
					$ret["user"] = array(
									"name" => $ret["User"],
									"id" => $ret["User_UID"]
								);
				} else {
					$ret["user"] = array(
									"id" => $ret["User_UID"]
								);
				}
				
				#Format Creation Data
				$ret["registration"] = array(
									"date" => $ret["registered"],
									"by" => $ret["registered_by"]
								);
				#Unseting bad data
				unset($ret["type"], $ret["text"], $ret["User_UID"], $ret["User"], $ret["registered_by"], $ret["registered"]);
				
				
				#We prepare a new array containing our description
				if($ret["description"] != "&nbsp;") {
					$desc = array(
						array(
							"provider" => "DASISH", 
							"text" => $ret["description"],
							"uri" => "/"
						)
					);
				} else { $desc = array(); }
				#Then if needed, we get our external_Description
				if($external == true) {
				
					$req = $this->DB->prepare("SELECT description, source_uri as sourceURI, registry_name FROM external_description WHERE tool_uid = ? ");
					$req->execute(array($toolUID));
					$fetched = $req->fetchAll(PDO::FETCH_ASSOC);
					
					#We then process it if > 0
					if(count($fetched) > 0) {
						foreach($fetched as &$entry) {
							if($entry["description"] != "&nbsp;") {
								$desc[] = array(
										"provider" => $entry["registry_name"], 
										"text" => $entry["description"],
										"uri" => $entry["sourceURI"]
									);
							}
						}
					}
				}
				
				$ret["description"] = $desc;
				return $ret;
			} else 	{
				return false;
			}
		}
		
		function getApplicationType($id, $mode = "Default") {
			$dictionnary = array(	
				"localDesktop" => "Desktop application",
				"other" => "Other",
				"unknown" => "Unkown",
				"webApplication" => "Web Application",
				"webService" => "Web service"
			);
			if($mode == "Reverse") {
				return array(
								"name" => $dictionnary[$id],
								"identifier" => $id
							);
			} else {
				$req = "SELECT d.application_type as UID, d.application_type as name FROM tool_application_type d WHERE d.tool_uid = ? GROUP BY application_type";
			}
			$req = $this->DB->prepare($req);
			$req->execute(array($id));
			
			if($req->rowCount() > 0) {
				$data = $req->fetchAll(PDO::FETCH_ASSOC);
				$ret = array();
				foreach($data as &$keyword) {
					$ret[] = array(
								"name" => $dictionnary[$keyword["name"]],
								"identifier" => $keyword["UID"]
							);
				}
				return $ret;
			} else {
				return false;
			}
		}
		
		
		
		function getKeywords($id, $mode = "Default") {
			if($mode == "Reverse") { 
				$req = "SELECT k.keyword_uid, k.keyword, k.source_uri as sourceURI, k.source_taxonomy as sourceTaxonomy FROM keyword k WHERE k.keyword_uid = ? LIMIT 1";
			} else {
				$req = "SELECT k.keyword_uid, k.keyword, k.source_uri as sourceURI, k.source_taxonomy as sourceTaxonomy FROM keyword k, tool_has_keyword tk WHERE tk.keyword_uid = k.keyword_uid AND tk.tool_uid = ?";
			}
			
			$req = $this->DB->prepare($req);
			$req->execute(array($id));
			
			if($req->rowCount() > 0) {
				$data = $req->fetchAll(PDO::FETCH_ASSOC);
				$ret = array();
				foreach($data as &$keyword) {
					if($keyword["sourceURI"] != "") {
						$ret[] = array(
									"identifier" => $keyword["keyword_uid"],
									"keyword" => $keyword["keyword"],
									"provider" => array(
													"uri" => $keyword["sourceURI"],
													"domain" => parse_url($keyword["sourceURI"], PHP_URL_HOST),
													"taxonomy" => $keyword["sourceTaxonomy"]
												)
								);
					}	else {
						$ret[] = array(
									"identifier" => $keyword["keyword_uid"],
									"keyword" => $keyword["keyword"]
								);
					}
				}
				
				if($mode == "Reverse") { $ret = $ret[0]; }
				return $ret;
			} else {
				return false;
			}
		}
		
		function getSuite($id, $mode = "Default") {
			###################################
			#
			#	MODES :
			#
			#		* Reverse = gets ToolType 							id is either null (List of ToolType) or int()
			#		* Default = gets ToolType from Tool					id cant be null
			#
			###################################
			
			#Default return is false :
			$ret = false;
			
			if($mode == "Reverse") {
			
				$req = "SELECT s.name, s.suite_uid as uid FROM suite s WHERE s.suite_uid = ? LIMIT 1";			
			
			} else {
			
				#In default mode, $id is an int
				$req = "SELECT s.name as name, s.suite_uid as uid FROM suite s, tool_has_suite ts WHERE ts.suite_uid = s.suite_uid AND ts.tool_uid = ?";
			
			}
				$req = $this->DB->prepare($req);
				$req->execute(array($id));
			
			#If we got data
			if($req->rowCount() > 0) {
				$data = $req->fetchAll(PDO::FETCH_ASSOC);
			
				$ret = array();
				foreach($data as &$type) {
					$ret[] = $type;
				}
				if($mode == "Reverse") { $ret = $ret[0]; }
			}
			#RETURN
			return $ret;
		}
		
		function getToolType($id, $mode = "Default") {
			###################################
			#
			#	MODES :
			#
			#		* Reverse = gets ToolType 							id is either null (List of ToolType) or int()
			#		* Default = gets ToolType from Tool					id cant be null
			#
			###################################
			
			#Default return is false :
			$ret = false;
			
			if($mode == "Reverse") {
			
				$req = "SELECT t.tool_type as name, t.source_uri as uri FROM tool_type t WHERE t.tool_type_uid = ? LIMIT 1";			
			
			} else {
			
				#In default mode, $id is an int
				$req = "SELECT t.tool_type as type, t.source_uri as uri FROM tool_type t, tool_has_tool_type tt WHERE tt.tool_type_uid = t.tool_type_uid AND tt.tool_uid = ?";
			
			}
				$req = $this->DB->prepare($req);
				$req->execute(array($id));
			
			#If we got data
			if($req->rowCount() > 0) {
				$data = $req->fetchAll(PDO::FETCH_ASSOC);
			
				$ret = array();
				foreach($data as &$type) {
					$ret[] = $type;
				}
				if($mode == "Reverse") { $ret = $ret[0]; }
			}
			#RETURN
			return $ret;
		}
		
		function getPlatform($id, $mode = "Default") {
			#Default return is false :
			$ret = false;
			
			if($mode == "Reverse") {
				$req = "SELECT p.name as platform FROM platform p WHERE p.platform_uid = ? LIMIT 1";
				#Request
			} else {
				$req = "SELECT p.name as platform FROM tool_has_platform tp, platform p WHERE tp.tool_uid = ? AND tp.platform_uid = p.platform_uid";
				#TBD
			}
			
			$req = $this->DB->prepare($req);
			$req->execute(array($id));
			
			#If we got data
			if($req->rowCount() > 0) {
			
				#Fetching data
				$data = $req->fetchAll(PDO::FETCH_ASSOC);
			
				#Format data
				$ret = array();
				foreach($data as &$v) {
					$ret[] = $v["platform"];
				}
				if($mode == "Reverse") { $ret = array("name" => $ret[0]); }
			}
			#Only one return
			return $ret;			
		}
		
		function getLicence($id, $mode = "Default") {
			#Default return is false :
			$ret = false;
			
			if($mode == "Default") {
				#Request
				$req = "SELECT l.text, lt.type FROM licence l, tool_has_licence tl, licence_type lt WHERE tl.tool_uid = ? AND l.licence_uid = tl.licence_uid AND lt.licence_type_uid = l.licence_type_uid";
				$req = $this->DB->prepare($req);
				$req->execute(array($id));
				
				#If we got data
				if($req->rowCount() > 0) {
					#Fetching data
					$data = $req->fetchAll(PDO::FETCH_ASSOC);
					
					#Format data
					$ret = array();
					foreach($data as &$v) {
						#Format licence
						$ret[] = array(
							"name" => $v["text"],
							"type" => $v["type"]
						);
					}
				}
			} elseif($mode == "Reverse") {
				$req = "SELECT l.text, lt.type FROM licence l, licence_type lt WHERE l.licence_uid = ? AND lt.licence_type_uid = l.licence_type_uid";
				$req = $this->DB->prepare($req);
				$req->execute(array($id));
				
				#If we got data
				if($req->rowCount() > 0) {
					#Fetching data
					$data = $req->fetch(PDO::FETCH_ASSOC);
					
					#Format data
					$ret = array(
						"name" => $data["text"],
						"type" => $data["type"]
					);
				}
			}
			
			#Only one return
			return $ret;			
		}
		
		function getTool($ref, $options) {
			#Setting request, following $ref is the id or the shortname
			if(is_numeric($ref)) {
				$req = "SELECT tool_uid as tool_id, shortname as tool_shortname FROM tool WHERE tool_uid = ? LIMIT 1";
			} else {
				$req = "SELECT tool_uid as tool_id, shortname as tool_shortname FROM tool WHERE shortname = ? LIMIT 1";
			}
			
			#Executing request
			$req = $this->DB->prepare($req);
			$req->execute(array($ref));
						
			#Formatting
			if($req->rowCount() > 0) {
				$data = $req->fetch(PDO::FETCH_ASSOC);
				$ret = array(
							"identifier" => array("id" => $data["tool_id"], "shortname" => $data["tool_shortname"]),
							"descriptions" => $this->getDescriptions($data["tool_id"]),
							"parameters" => $options
						);
						
				if(isset($options["keyword"])) {
					$ret["keyword"] = $this->getKeywords($data["tool_id"]);
					if(!$ret["keyword"]) { unset($ret["keyword"]); }
				}
				if(isset($options["type"])) {
					$ret["type"] = $this->getToolType($data["tool_id"]);
					if(!$ret["type"]) { unset($ret["type"]); }
				}
				if(isset($options["platform"])) {
					$ret["platform"] = $this->getPlatform($data["tool_id"]);
					if(!$ret["platform"]) { unset($ret["platform"]); }
				}
				
				if(isset($options["developer"])) {
					$ret["developers"] = $this->getDevelopers($data["tool_id"]);
					if(!$ret["developers"]) { unset($ret["developers"]); }
				}
				
				if(isset($options["projects"])) {
					$ret["projects"] = $this->getProjects($data["tool_id"]);
					if(!$ret["projects"]) { unset($ret["projects"]); }
				}
				
				if(isset($options["suite"])) {
					$ret["suite"] = $this->getSuite($data["tool_id"]);
					if(!$ret["suite"]) { unset($ret["suite"]); }
				}
				
				if(isset($options["standards"])) {
					$ret["standards"] = $this->getStandards($data["tool_id"]);
					if(!$ret["standards"]) { unset($ret["standards"]); }
				}
				
				if(isset($options["features"])) {
					$ret["features"] = $this->getFeatures($data["tool_id"]);
					if(!$ret["features"]) { unset($ret["features"]); }
				}
				
				if(isset($options["publications"])) {
					$ret["publications"] = $this->getPublications($data["tool_id"]);
					if(!$ret["publications"]) { unset($ret["publications"]); }
				}
				
				if(isset($options["licence"])) {
					$ret["licence"] = $this->getLicence($data["tool_id"]);
					if(!$ret["licence"]) { unset($ret["licence"]); }
				}
				
				if(isset($options["applicationType"])) {
					$ret["applicationType"] = $this->getApplicationType($data["tool_id"]);
					if(!$ret["applicationType"]) { unset($ret["applicationType"]); }
				}
				
			} else {
				$ret = array("Error" => "No tool for " + $ref +" identifier");
			}
			return $ret;
			
		}
	}
	$tool = new Tool();
?>