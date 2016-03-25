price_obj = {
	price_list:function(){
		$(".price-store").on("click",".item-act-add",function(){
			var _priceVal = $(this).parents(".price-data").find("input[name=store_price]");
			var _obj = {
				action : "edit_price",
				price : {},
				store : $(this).attr("store-id")
			};
			$.each(_priceVal,function(i,n){
				_data_id = $(n).attr('data-id');
				_val = $(n).val();
				_obj['price'][_data_id] = _val;
			})
			btsalert.loading();
			$.post('?',_obj,function(data){
				btsalert.loading(1);
                btsalert.alert(data.msg);
                if(parseInt(data.status)){
                    window.location.reload();
                }
			},'json')
		})
	}
}