'use strict'
angular.element(document).ready(function () {

	var app = angular.module('appModule', []);

	app.config(['$compileProvider', '$httpProvider', function ($compileProvider, $httpProvider){
				$compileProvider.debugInfoEnabled(false);
				$httpProvider.interceptors.push(function(){
				return {
					request: function(config) {
						config.headers['X-Api-Key'] = 'ejemplodeapikyenviado';
						return config;
					}
				};
			});
	}]);

	app.factory('decimalformat', function () {
	    return {
	      decimal: function (value) {
	        return currency(value, { separator: ',', precision: 2, errorOnInvalid: true, symbol: 'S/ ' });
	      },
	      value : function(value){
	        return value.value;
	      },
	      format : function(value){
	        return value.format();
	      }
	    };
	});

	app.filter('decimal', function(decimalformat) {
	    return function(input) {
	      return decimalformat.format(decimalformat.decimal(input));
	    };
	});
	
	app.directive('inputQuantity', ['$timeout', function(timeout) {
      return {
        restrict: 'E',
        replace: true,
        scope: {
          value: '=',
          onUpdate: '&'
        },
        link: function(scope, elem, attrs) {

          scope.safeApply = function(fn) {
            var phase = this.$root.$$phase;
            if(phase == '$apply' || phase == '$digest') {
              if(fn && (typeof(fn) === 'function')) {
                fn();
              }
            } else {
              this.$apply(fn);
            }
          };

          scope.onUpdateFix = function(){
            if(angular.isDefined(attrs.onUpdate)){
              timeout(function(){
                scope.onUpdate();
              }, 50);
            }
          };

          scope.property = {
            min_value: 1,
            max_value: 999,
            step: 1
          };

          scope.value = !isNaN(parseInt(scope.value)) ? scope.value : scope.property.min_value;

          $(elem).children('input').on('input', function (evt) {
                var num = parseInt(this.value, 10);
                num = !isNaN(num) ? num : scope.property.min_value;
                scope.safeApply(function(){
                  if(num >= scope.property.min_value && num <= scope.property.max_value){
                    scope.value = num;
                  }else if(num > scope.property.max_value){
                    scope.value = scope.property.max_value;
                  }else{
                    scope.value = scope.property.min_value;
                  }
                  scope.onUpdateFix();
                });
          });

          scope.decrease = function() {
            if(scope.value > scope.property.min_value){
              scope.value = scope.value - scope.property.step;
              scope.onUpdateFix();
            }
          };

          scope.increase = function() {
            if(scope.value < scope.property.max_value){
              scope.value = scope.value + scope.property.step;
              scope.onUpdateFix();
            }
          };

        },
        template:
        '<div style="width: 100%;position: relative;">'+
          '<input type="text" data-ng-model="value" style="width: 100%;padding: 14px 50px 14px 10px;border: 1px solid #ced4da;border-radius: 10px;outline: none;text-align: center;">'+
          '<button type="button" data-ng-click="increase()" style="display: block;background: white;border: 1px solid #ced4da; margin-bottom: -1px;border-top-right-radius: 10px;outline: none;position:absolute;right:0;top:0;width:50px;height:27px;">+</button>'+
          '<button type="button" data-ng-click="decrease()" style="display: block;background: white;border: 1px solid #ced4da;outline: none;border-bottom-right-radius: 10px;position:absolute;right:0;bottom:0;width:50px;height:28px;">-</button>'+
        '</div>'
      };
    }]);

	app.controller('appController', ['$scope', '$window', '$timeout', 'decimalformat', '$http', function (ng, w, t, decimalformat, http) {

		ng.cart = [];

		ng.count = 0;

		ng.total = {
			total : 0
		};

		ng.products = [
			{
				id : 1,
				code : '1234',
				name : 'Celular LG K22 - 6.2" 32GB 2GB - titan',
				description : 'Dispositivo móvil de Tienda e-commerce',
				price : 399.00,
				image: w.configuration.url + 'statics/img/one.jpg',
				currency : 'PEN'
			},
			{
				id : 2,
				code : '1234',
				name : 'Smartphone Huawei P40 Lite Verde + Speaker Gold',
				description : 'Dispositivo móvil de Tienda e-commerce',
				price : 1199.50,
				image:w.configuration.url + 'statics/img/two.jpg',
				currency : 'PEN'
			},
			{
				id : 3,
				code : '1234',
				name : 'Samsung Galaxy A71 128 GB Crush Silver',
				description : 'Dispositivo móvil de Tienda e-commerce',
				price : 1500.80,
				image:w.configuration.url + 'statics/img/three.jpg',
				order : 'iamdanielavilavera@gmail.com',
				currency : 'PEN'
			},
			{
				id : 4,
				code : '1234',
				name : 'Smartphone Huawei Y7A Verde + Tripode Selfie Stick +Circular Kickstand+Audífonos',
				description : 'Dispositivo móvil de Tienda e-commerce',
				price : 849.00,
				image:w.configuration.url + 'statics/img/four.jpg',
				currency : 'PEN'
			},
		];

		var fn = (function(){
			return {
				loadCart : function(){
			      var cart = localStorage.getItem('cart');
			      if (cart === null) {
			        localStorage.setItem('cart', angular.toJson(ng.cart));
			        cart = localStorage.getItem('cart');
			      }
			      ng.cart = angular.fromJson(cart);
			      console.log('cargando cart', ng.cart);
			    },
				addCart : function(quantity, product){
					var exist = -1;
			        for (var i = 0; i < ng.cart.length; i++) {
			          if ( ng.cart[i].id === product.id ) {
			            exist = i;
			            break;
			          }
			        }

			        if (exist === -1) {
			        	var prod = angular.copy(product);
			        	prod.quantity = quantity;
			        	ng.cart.push(prod);
			        }else{
			        	ng.cart[exist].quantity = ng.cart[exist].quantity +  quantity;
			        }
			        localStorage.setItem('cart', angular.toJson(ng.cart));
			        fn.countProducts();
				},
				countProducts : function(){
			      var count = 0;
			      for (var i = 0; i < ng.cart.length; i++) {
			        count += ng.cart[i].quantity;
			      }
			      ng.count = count;
			      this.getTotal();
			    },
			    getTotal : function(){

			      ng.total.total = decimalformat.decimal(0);

			      angular.forEach(ng.cart, function(val){

			        val.sale_price = decimalformat.decimal(val.price);

			        val.importe = val.sale_price.multiply(val.quantity);

			        ng.total.total = ng.total.total.add(val.importe);

			      });

			    },
			    removeCart : function(){
			      ng.cart = [];
			      localStorage.setItem('cart', angular.toJson(ng.cart));
			      this.countProducts();
			    }
			};
		})();

		ng.onClick = (function(){
			return {
				addCart : function(product){
					fn.removeCart();
					fn.addCart(1, product);
					w.location.href = w.configuration.url + 'cart';
				},
				removeCart : function(pos){
			      ng.cart.splice(pos, 1);
			      localStorage.setItem('cart', angular.toJson(ng.cart));
			      fn.countProducts();
			    },
			    checkout : function(){
			    	http.post(w.configuration.url + 'api/preferences', {order : 'iamdanielavilavera@gmail.com', items: ng.cart}).then(function(response){
			    		console.log(response);
			    		if(response.status === 200 && response.data.status === 'SUCCESS'){
			    			var url = response.data.data.init_point;
			    			w.location.href = url;
			    		}
			    	}, function(error){

			    	});
			    }
			};
		})();


	ng.onChange = (function(){
      return{
          quantity : function(){
            localStorage.setItem('cart', angular.toJson(ng.cart));
            fn.countProducts();
          }
      };
    })();


		fn.loadCart();

		fn.countProducts();
		
	}]);

	angular.bootstrap(angular.element(document), [app.name]);


});