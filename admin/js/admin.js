(function($) {
	/**
	 * Datepicker Init
	 **/
    $(function() {
        // Check to make sure the input box exists
        if( 0 < $('.datepicker').length ) {
            $('.datepicker').datepicker({
				dateFormat: 'yy/mm/dd'
			});
        } // end if
    });

    /**
	 * Tab Functionality
	 */
	$(document).ready(function(){
		$('[data-tab]').on('click', function(e){
			$(this).addClass('active').siblings('[data-tab]').removeClass('active');
			$(this).parent().siblings('[data-content=' + $(this).data('tab') + ']').addClass('active').siblings('[data-content]').removeClass('active');
		});
	});

	/**
	 * Sorting 
	 **/
	"use strict";
	
	//Attach sortable to the tbody, NOT tr
	var tbody = $("body.post-type-dynamic-perk tbody#the-list");
	var data = {
			'action': 'sort-posts', //Set an action for our ajax function
		};

	tbody.sortable({
		cursor: "move",
	    update: function (event, ui) {
	    	//grabs all of the ids of the post rows and pushes them into an array
	        data.sort = $(this).sortable('toArray');
	        
	        $.post(ajaxurl, data)
	        .done(function(response) {
				console.log( "Sorting Successful." );
			}).fail(function() {
				console.log( "Uh Oh! You tried to divide by zero." );
			});
	    }
	});
    $(document).ready(function () {
    	var inputType = ['input','select'];
    	var attributes = ['name', 'id', 'value'];

	    /**
	     * Repeatable Function
	     */
	    $('.group').on('click', ".repeat-me", function(){
	        var funcType = 'repeat';
	        var clonedField = $(this).parents('.repeater li').clone();

	       	$.each(inputType, function(i, type) {
	       		$.each(attributes, function(i, attribute) {
	       			if(attribute !== 'value' ){
			       		$(type, clonedField).val('').attr(attribute, function(index, name) {
			       		    return name.replace(/(\d+)/, function(fullMatch, n) {
			       		        return Number(n) + 1;
			       		    });
			       		});
	       			} else {
	       				$(type, clonedField).val('').attr(attribute, '');
	       			}
	       		});
	       	});
            
	        $(this).parents('.repeater').append(clonedField);
	        $(this).parents('.repeater li').removeClass('last-item');
	    });

	    /**
	     * Delete Function
	     */
	    $('.group').on('click', ".delete-me", function(){
	       	var itemsVisible = 1;

	        //cycle through each cloned item
	        $.each($(this).parents('.repeater li').siblings(), function(i) {
	        	if($(this).is(':visible')){
	        		itemsVisible++;
	        	}
	        });

	        console.log(itemsVisible);
	        if(itemsVisible == 1){
		        var deletedField = $(this).parents('.repeater li');
	        } else {
	        	var deletedField = $(this).parents('.repeater li').hide();
	        	$(this).parents('.repeater li').removeClass('last-item');	
	        	$(this).parents('.repeater li').prev().addClass('last-item');	
	        }

           	$.each(inputType, function(i, type) {
           		$.each(attributes, function(i, attribute) {
					$(type, deletedField).val('').attr(attribute, '');
           		});
           	});
	    });

	     /**
	      *  Serve certain fields to user based on selected input
	      */
	     $('.group').on('change','.link-type', function() {
	         var x = $(this).val();
	         if(x == 'internal'){
	            $(this).parent().find('.link-fields').removeClass('external-active internal-active print-active');
	            $(this).parent().find('.link-fields').addClass('internal-active');
	         } else if(x == 'print'){
	         	$(this).parent().find('.link-fields').removeClass('external-active internal-active print-active');
	         	$(this).parent().find('.link-fields').addClass('print-active');
	         } else {
	         	$(this).parent().find('.link-fields').removeClass('external-active internal-active print-active');
	         	$(this).parent().find('.link-fields').addClass('external-active');
	         }
	     });

	     /**
	      *  Hide Page Tab when Pagebuilder Content is used
	      */
	    $('.group').on('change','#promo_pagebuilder', function() {
	    	if($('.ed-admin-mb .nav-tab-wrapper ').hasClass('page-disabled')){
	    		$('.ed-admin-mb .nav-tab-wrapper ').removeClass('page-disabled');
	    	} else {
	    		$('.ed-admin-mb .nav-tab-wrapper ').addClass('page-disabled');
	    	}
	    });

	});	
}(jQuery));