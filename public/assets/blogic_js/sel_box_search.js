jQuery(function($){

	if(!ace.vars['touch']) {
		$('.chosen-select').chosen({allow_single_deselect:true});
        //resize the chosen on window resize

        $(window)
			.off('resize.chosen')
            .on('resize.chosen', function() {
               $('.chosen-select').each(function() {
                   var $this = $(this);
                   $this.next().css({'width': $this.parent().width()});
               })
             }).trigger('resize.chosen');
            //resize chosen on sidebar collapse/expand
            $(document).on('settings.ace.chosen', function(e, event_name, event_val) {
				if(event_name != 'sidebar_collapsed') return;
                  $('.chosen-select').each(function() {
                      var $this = $(this);
                      $this.next().css({'width': $this.parent().width()});
                  })
				});


              $('#chosen-multiple-style .btn').on('click', function(e){
                  var target = $(this).find('input[type=radio]');
                  var which = parseInt(target.val());
                  if(which == 2) $('#form-field-select-4').addClass('tag-input-style');
                  else $('#form-field-select-4').removeClass('tag-input-style');
              });
     }

 })