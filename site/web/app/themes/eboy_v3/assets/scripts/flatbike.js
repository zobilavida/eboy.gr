$.ajax({
  url: flatbikeurls.ajax_url,
  cache: false,
  type: "POST",
  headers : { "cache-control": "no-cache" },
  data: {
	'action': 'get_product_detail',
	'product_id': $productID
  },
  success:function(returned) {
      $( '#flatbike-product-detail__container' ).html(returned);
});
