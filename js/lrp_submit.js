function lrp_update(post, node, key, schema, user, lrdocid){ 
	
	var data = {
		action: "lrp_update",
		post: post,
		node: node,
		key: key,
		schema: schema,
		lrdocid: lrdocid,
		user: user,
		nonce: lrp_submit_ajax.answerNonce
	};

	jQuery("html").css( 'cursor', 'wait' );
	
	jQuery.post(lrp_submit_ajax.ajaxurl, data, function(response) {
		console.log(response);
		data = jQuery.parseJSON( response );
		if(data.error==undefined){
			jQuery("#lrp_last_publish").html(data.last);
			console.log(jQuery("#myTable")
				.children()
				.first()
				.next()
				.html());
			jQuery("#myTable")
				.children()
				.first()
				.next()
				.prepend(data.publish);
		}else{
			alert(data.error);
		}
		
		jQuery("html").css( 'cursor', 'pointer' );
		
	});
	
}

function lrp_submit(post, user, get_data, single_post){ 

	var data = {
		action: "lrp_submit",
		post: post,
		user: user,
		nonce: lrp_submit_ajax.answerNonce,
		single: single_post
	};

	if(!get_data){
		data.key = jQuery("#lrkey").val();
		data.schema = jQuery("#lrschema").val();
		data.node = jQuery("#lrnode").val();	
	}else{
		data.key = jQuery("#lrkey_" + post).val();
		data.schema = jQuery("#lrschema_" + post).val();
		data.node = jQuery("#lrnode_" + post).val();
	}
	
	jQuery("html").css( 'cursor', 'wait' );
	
	jQuery.post(lrp_submit_ajax.ajaxurl, data, function(response) {
	
		console.log(response);
	
		if(single_post){
			data = jQuery.parseJSON( response );
			if(data.error==undefined){
				jQuery("#lrp_last_publish").html(data.last);
				if(jQuery("#myTable").children().first().next().children().size()==1){
					jQuery("#myTable").children().first().next().html("");
				}
				jQuery("#myTable")
					.children()
					.first()
					.next()
					.prepend(data.publish);
			}else{
				alert(data.error);
			}
		}else{
			jQuery("#lrp_document_" + post)
				.remove();
		}
		
		jQuery("html").css( 'cursor', 'pointer' );
		
	});
	
}

function lrp_submit_options(event){
	console.log(event);
	if(jQuery(".lrp_submit").first().css("display")=="none"){
		jQuery(".lrp_submit")
			.each(
				function(index, value){
					jQuery(value)
						.css("display", "block");
					jQuery("#lrp_document_show")
						.html("Hide document submission options");
				}
			)
	}else{
		jQuery(".lrp_submit")
			.each(
				function(index, value){
					jQuery(value)
						.css("display", "none");
					jQuery("#lrp_document_show")
						.html("Show document submission options");
				}
			)
	}
}