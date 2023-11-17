jQuery.fn.selectAutocomplete = function( options ) {
    return this.each(function(){
        if (this.tagName.toLowerCase() != 'select') { return; }
        var select = this;
        var items = [];
        $(select).children('option').each(function(){
            var item = $(this);
            if (item.val() != ''){
                var label = item.text(); // .html() is not decoding htmls like &amp; (Muhit)
                var value = item.val();
                items.push( {label: label, value: value} );
            }
        });
        if($(select).next().is('input[type=text]')){
            $(select).next().remove();
        }
        var input = $("<input type='text' class='form-control autocomplete-input' />").appendTo('body');
        $(select).after(input);
        input.on('blur', function(){
            $(this).val($(select).find(':selected').text());
        });
        input.on('focus', function(){
            $(this).val("");
        });
        autocomplete({
            input: input[0],
            minLength: 0,
            showOnFocus: true,
            emptyMsg: 'Sorry! No Match!',
            fetch: function(text, update) {
                text = text.toLowerCase();
                var suggestions = text === "" ? items.slice(0,5) : items.filter(n => n.label.toLowerCase().indexOf(text) >= 0)
                update(suggestions);
            },
            onSelect: function(item){
                input[0].value = item.label;
                $(select).find("option").removeAttr('selected');
                $(select).find("option[value='" + item.value + "']").attr('selected', 'selected');
                $(select).val(item.value).trigger('change');
            }
        });        
        $(input).val($(select).find(':selected').text());
        $(select).hide();
    });
};