jQuery(document).ready(function($){
	$('#billing_city_field').attr('style','position: relative;margin: 0; padding: 0;height: 0;background: none;opacity: 0;z-index: -1;')
	
    var apiUrl = "http://api.cdek.ru/city/getListByTerm/jsonp.php?callback=?";
   
    if (window.location.protocol == 'https:'){
        apiUrl = "https://api.cdek.ru/city/getListByTerm/jsonp.php?callback=?";
    }

		// Получаем код города ( От клиента ) на странице оформления заказа
    $("#city_cdek").autocomplete({
	    source: function(request,response) {
	      $.ajax({
	        url: apiUrl,
	        dataType: "jsonp",
	        data: {
	        	q: function () { return $("#city_cdek").val() },
	        	name_startsWith: function () { return $("#city_cdek").val() }
	        },
	        success: function(data) {
	          response($.map(data.geonames, function(item) {
	            return {
	              label: item.name,
	              value: item.name,
	              id: item.id
	            }
	          }));
					}
	      });
	    },
	    minLength: 1,
	    select: function(event,ui) {
				$('#billing_city').val(ui.item.id);
				$(document.body).trigger("update_checkout");
	    }
		});

		// Получаем код города ( От администратора ) в карточке товара
		$("#cdek_shipping_option_city_of_dispatch").autocomplete({
	    source: function(request,response) {
	      $.ajax({
	        url: apiUrl,
	        dataType: "jsonp",
	        data: {
	        	q: function () { return $("#cdek_shipping_option_city_of_dispatch").val() },
	        	name_startsWith: function () { return $("#cdek_shipping_option_city_of_dispatch").val() }
	        },
	        success: function(data) {
	          response($.map(data.geonames, function(item) {
	            return {
	              label: item.name,
	              value: item.name,
	              id: item.id
	            }
	          }));
					}
	      });
	    },
	    minLength: 1,
	    select: function(event,ui) {
				$('#cdek_shipping_option_city_code').val(ui.item.id);
	    }
		});

		// Получаем код города ( От администратора ) на странице настройки плагина
		$("#woocommerce_cdek-method_from_city").autocomplete({
	    source: function(request,response) {
	      $.ajax({
	        url: apiUrl,
	        dataType: "jsonp",
	        data: {
	        	q: function () { return $("#woocommerce_cdek-method_from_city").val() },
	        	name_startsWith: function () { return $("#woocommerce_cdek-method_from_city").val() }
	        },
	        success: function(data) {
	          response($.map(data.geonames, function(item) {
	            return {
	              label: item.name,
	              value: item.name,
	              id: item.id
	            }
	          }));
					}
	      });
	    },
	    minLength: 1,
	    select: function(event,ui) {
				$('#woocommerce_cdek-method_from_city_code').val(ui.item.id);
	    }
		});
});

