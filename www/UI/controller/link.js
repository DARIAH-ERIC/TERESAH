var LinkCtrler = portal.controller('LinkCtrl', ['$scope', 'ui',  'Item', function($scope, $ui, $item) {
	$scope.results = false;
	console.log($item.data)
	$scope.item = $item.data;
	
	$scope.ui = {
		form : {
			fields : {
				tool : $item.data.identifier.id
			},
			save : function() {
				postData = $scope.ui.form.fields;
				facets = $scope.ui.facets.group($scope.ui.facets.used);
				postData["facets"] = facets["facets"];
				
				console.log(postData);
				
				$item.resolver.tools.link(postData, function(data) {
					$scope.ui.form.return = data;
				});
			}
		},
		facets : {
			error : false,
			facets : null,
			search : function(facet) {
				if(facet.option) {
					option = facet.option;
				} else {
					option = {}
				}
				option["request"] = facet.filter;
				
				return $item.resolver.facets(facet.facetParam, option)
			},
			group : function(constructor) {
				if(!constructor) {
					constructor = { facets : []};
				} else {
					constructor["facets"] = [];
				}
				angular.forEach($scope.ui.facets.used, function(val) {
					if(val.active) {
						//Wont work if keyword list is reloaded
						angular.forEach(val.possibilities, function(opt) {
							if(opt.active) {
								constructor.facets.push({"facet" : val.facetParam, "element" : opt.id});
							}
						});
					}
				});
				return constructor;
			},
			submit : function(constructor) {
				constructor = $scope.ui.facets.group(constructor);
				
				$item.resolver.search.faceted(constructor, function(data) {
					if(data.Error) {
						$scope.ui.facets.error = data.Error;
					} else {
						$scope.ui.facets.error = false;
					}
					if(data.response) {
						if(data.response.length == 0) {
							$scope.results = false;
							$scope.ui.facets.error = "No results";
						} else {
							$scope.ui.url.val = data.parameters.url;
							$ui.url.set(data.parameters.url);
							$scope.results = { items : data.response }
							$scope.ui.pages.totalItem = data.parameters.total;
						}
					} else {
						$scope.results = false;
					}
					
				});
				
			},
			select : function(facet, keyword) {
				if(!$scope.ui.facets.used[facet.facetParam]) {
					$scope.ui.facets.used[facet.facetParam] = { "facetParam" : facet.facetParam, "facetLegend" : facet.facetLegend, active: true, "possibilities" : {} }
				} 
				if(keyword.active == false) {
					delete $scope.ui.facets.used[facet.facetParam].possibilities[keyword.id];
				} else {
					$scope.ui.facets.used[facet.facetParam].possibilities[keyword.id] = keyword;
				}
			},
			del : function (par, key) {
				delete $scope.ui.facets.used[par].possibilities[key];
			},
			used : {}
		}
	};
	$item.resolver.facets(false, false, function(data) {
		x = []
		angular.forEach(data, function(val) {
			val["option"] = { case_insensitivity : true };
			x.push(val);
		});
		$scope.ui.facets.facets = x;
		
	});
	$ui.title("Tool | Link Metadata | ");
	
}]);
LinkCtrler.resolveLinkCtrl = {
	itemData: function($route, Item) {
		ret = {}

		Item.resolver.tools.one($route.current.params.toolId, {});
		
	},
	delay: function($q, $timeout) {
		var delay = $q.defer();
		$timeout(delay.resolve, 1000);
		return delay.promise;
	}
}