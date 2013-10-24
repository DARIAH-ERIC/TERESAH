var Tool = portal.controller('ToolCtrl', ['$scope', 'ui',  'Item', function($scope, $ui, $item) {

	$scope.item = $item.data;
	
	//Will change
	if($scope.item.descriptions.description[0]) {
		$scope.item.desc = $scope.item.descriptions.description[0];
	}
	$scope.ui = {
		changeDesc : function(provider) {
			if(typeof provider == "string") {
			} else {
				$scope.item.desc = provider;
			}
		},
		features : {
			show : true,
			filter : {
				input : null,
				show : false
			}
		},
		standards : {
			show : true,
			filter : {
				input : null,
				show : false
			}
		},
		projects : {
			show : true,
			filter : {
				input : null,
				show : false
			}
		},
		videos : {
			show : true,
			filter : {
				input : null,
				show : false
			}
		},
		publications : {
			show : true,
			filter : {
				input : null,
				show : false
			}
		},
		keywords : {
			provider : {
				selected : null,
				selected : null
			},
			taxonomy : {
				show : false,
				selected : null
			},
			show : true
		},
		comments : {
			form : {
				submit : function() {
					$item.resolver.tools.comments.post($scope.item.identifier.id, $scope.ui.comments.form.data, function(data) {
						if(data.Error) {
							$scope.ui.comments.form.error = data.Error;
						} else {
							$scope.ui.comments.form.error = false;
							$scope.ui.comments.get();
							$scope.ui.comments.form.active = false;
						}
					});
				},
				data : {},
				error : false
			},
			get : function() {
				$item.resolver.tools.comments.get($scope.item.identifier.id, function(data) {
					$scope.ui.comments.list = data.comments;
				});
			},
			list : {},
			active : false,
			activate : function() {
				this.active = !this.active;
				if(this.active == true) {
					$scope.ui.forum.active = false;
					this.get();
				}
			}	
		},
		forum : {
			form : {
				submit : function() {
					$item.resolver.tools.forum.post($scope.item.identifier.id, $scope.ui.forum.form.data, function(data) {
						if(data.Error) {
							$scope.ui.forum.form.error = data.Error;
						} else {
							$scope.ui.forum.form.error = false;
							$scope.ui.forum.get();
							$scope.ui.forum.form.active = false;
						}
					});
				},
				data : {},
				error : false
			},
			get : function() {
				$item.resolver.tools.forum.get($scope.item.identifier.id, function(data) {
					$scope.ui.forum.list = data.comments;
				});
			},
			list : {},
			active : false,
			activate : function() {
				this.active = !this.active;
				if(this.active == true) {
					$scope.ui.comments.active = false;
					this.get();
				}
			},
			topic : {
				form : {
					submit : function() {
						$item.resolver.tools.forum.topic.post($scope.item.identifier.id, $scope.ui.forum.topic.id, $scope.ui.forum.topic.form.data, function(data) {
							if(data.Error) {
								$scope.ui.forum.topic.form.error = data.Error;
							} else {
								$scope.ui.forum.topic.form.error = false;
								$scope.ui.forum.topic.read($scope.ui.forum.topic.id);
								$scope.ui.forum.topic.form.active = false;
							}
						});
					},
					data : {},
					error : false,
					active : false
				},
				read : function(id) {
					$item.resolver.tools.forum.topic.get(id, function(data) {
						$scope.ui.forum.topic.id = id;
						$scope.ui.forum.topic.list = data.topic;
						$scope.ui.forum.topic.active = true;
					});
					
				},
				new : function() {
					return true;
					$item.resolver.tools.forum.topic.post($scope.item.identifier.id, $scope.ui.forum.topic.id, $scope.ui.forum.form.data, function(data) {
						if(data.Error) {
							$scope.ui.forum.topic.form.error = data.Error;
						} else {
							$scope.ui.forum.topic.form.error = false;
							$scope.ui.forum.topic.read($scope.ui.forum.topic.id);
							$scope.ui.forum.topic.form.active = false;
						}
					});
				},
				list : {},
				data : {},
				id : false,
				active : false
			}
		}
	};
	//
	$ui.title("Tool | " + $scope.item.descriptions.title);
	
	//exec
}]);
Tool.resolveTool = {
	itemData: function($route, Item) {
		console.log($route.current.params.toolId);
		this.data = Item.resolver.tools.one($route.current.params.toolId);
		return Item.data;
	},
	delay: function($q, $timeout) {
		var delay = $q.defer();
		$timeout(delay.resolve, 1000);
		return delay.promise;
	}
}