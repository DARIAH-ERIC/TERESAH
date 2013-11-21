<?php
	class Description {
	/**
	 * Description class handles the insert and get functions for descriptions
	 *
	 *
	 */
	 
		/**
		 *	Get the DB in a PDO way, can be called through self::DB()->PdoFunctions
		 * @return PDO php object
		 */
		private static function DB() {
			global $DB;
			return $DB;
		}
		
		/**
		 *	Insert a description for a tool, wether external or internal
		 *
		 *
		 * @require	$_SESSION["user"]["id"]	Numeric Identifier of a logged in User
		 *
		 * @param $toolUID 					Numeric identifier of the tool about which the description is
		 *
		 * @param $data["provider"] 		(Optional) If set, defines the description as an external description. Should be a name
		 * @param $data["source"] 			(Optional) Uri of the external description
		 *
		 * @param $data["description"] 		Text of the description
		 * @param $data["name"] 			Title of the tool
		 * @param $data["version"] 			Version of the tool
		 * @param $data["homepage"] 		URI of the homepage of the tool
		 * @param $data["available_from"] 	Date from which the tool has been available on the market
		 *
		 * @return Common status message with array["identifier"]["id"] value to retrieve the new description
		 */
		private function insert($toolUID, $data) {
			$ret = "Nothing happends";
			if(isset($data["provider"])) {
				#If we have a Data Provider, it is an external Description
				$req = "INSERT INTO external_description VALUES ('', ?, ?, ?, ?); ";
				$req = self::DB()->prepare($req);
				try {
					$ret = $req->execute(array($toolUID, $data["description"], $data["source"], $data["provider"]));
				} catch(Exception $e) {
					return array("status" => "error", "message" => "A field might be missing");
				}
			
			} else {
				#Else, it is a simple description
				$sql = "INSERT INTO `tools_registry`.`description` 
					(`description_uid`, `title`, `description`, `version`, `homepage`, `available_from`, `registered`, `registered_by`, `tool_uid`, `user_uid`) VALUES 
					('',				 ?,	 		?, 			?,		 	?,		 	?,			CURDATE(), 			NULL,		 		?	,		 ?);";
				// $req = "INSERT INTO description VALUES ('', ?, ?, ?, ?, ?, CURDATE(), NULL, ?,?,?); ";
				$req = self::DB()->prepare($sql);
				try {
					$data = array($data["name"], $data["description"], $data["version"], $data["homepage"],  $data["available_from"],  $toolUID, $_SESSION["user"]["id"]);
					$ret = $req->execute($data);
					$uid = self::DB()->lastInsertId();
					
					Log::insert("insert", $_SESSION["user"]["id"], "description", $uid);
					return array("status" => "success", "message" => "Description registered", "identifier" => array("id" => $uid));
				} catch(Exception $e) {
					return array("status" => "error", "message" =>  "A field might be missing");
				}
			}
			return $ret;
		}
		
		/**
		 *	Get descriptions of a tool
		 *
		 *
		 * @require	$_SESSION["user"]["id"]	Numeric Identifier of a logged in User
		 *
		 * @param $toolUID 		Numeric identifier of the tool about which the description is
		 * @param $external 	(Optional) Includes external descriptions (Default : True)
		 * @param $userName 	(Optional) If set to true, returns the data about the user ["user"] = array("name", "id")
		 *
		 * @return array() of formatted descriptions with metadata
		 */
		function get($toolUID, $external = true, $userName = false) {
			$userName = true;
			#We first fetch our description
			
			$req = "SELECT d.user_uid as User, d.title, d.description, d.version, d.homepage, d.description_uid UID, d.registered, d.available_from, FROM description d WHERE d.tool_uid = ?  ORDER BY d.description_uid DESC LIMIT 1";
			
			if($userName) {
				$req = "SELECT d.user_uid as User_UID, u.name as User, d.title, d.description, d.description_uid UID, d.version, d.homepage, d.registered, d.available_from FROM description d, user u WHERE d.tool_uid = ? AND u.user_uid = d.user_uid ORDER BY d.description_uid DESC LIMIT 1";
			}
			$req = self::DB()->prepare($req);
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
									"date" => $ret["registered"]
								);
				$ret["identifier"] = array("id" => $ret["UID"]);
				#Unseting bad data
				unset($ret["type"], $ret["text"], $ret["User_UID"], $ret["User"], $ret["registered_by"], $ret["registered"], $ret["UID"]);
				
				#We prepare a new array containing our description
				if($ret["description"] != "") {
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
				
					$req = self::DB()->prepare("SELECT description, source_uri as sourceURI, registry_name FROM external_description WHERE tool_uid = ? ");
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
	}
?>