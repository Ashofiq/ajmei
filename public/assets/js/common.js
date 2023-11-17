$.ajaxSetup({ cache: false });
$(function(){
    $('#module-dropdown').ddslick({
        data:modules,
        width: 191,
        onSelected: function(s){
            $('.e2a-sub-navigation .navbar-nav').hide();
            $('.e2a-sub-navigation .'+s.selectedData.value).fadeIn();
            if(dashboards[s.selectedData.value] !== "")
                openTab(site+dashboards[s.selectedData.value], null, 'Dashboard');
        }
    });
    $(".e2a-toggle-right-sidebar").on("click",function(t){
        $('body').toggleClass('open-right-sidebar', 400);
    });
    $(".e2a-footer .e2a-shortcut").popover({
        html : true,
        content: function() {
          var content = $(this).attr("data-popover-content");
          return $(content).children(".popover-body").html();
        },
        title: function() {
          var title = $(this).attr("data-popover-content");
          return $(title).children(".popover-heading").html();
        }
    });
    // $('[data-toggle="tooltip"]').tooltip();
    
    $(document).click(function(event){
        if($(event.target).closest('.e2a-right-sidebar').length === 0 && $(event.target).closest('.e2a-toggle-right-sidebar').length === 0){
            $('body').removeClass('open-right-sidebar', 400);
        }
        if($(event.target).closest('.popover').length === 0 && $(event.target).closest('.e2a-shortcut').length === 0){
            $(".e2a-footer .e2a-shortcut").popover('hide');
        }
    });
    /**
     * Anik's JS document ready
     */
    /*
    * Initially disabled the tab navs delete button when the page is loaded
    */
    if ($('#e2a_tab_list li').is(':only-child')) {
        $('#e2a_tab_list li .close-btn').addClass('disabled');
        $('#e2a_tab_list li .close-btn').prop('disabled', true);
    }
    /**
     * Anik's JS Ends
     */

  /*
   * Disabled prev scroll button pn page load
   */
  $('.scroll-btn-prev')
    .prop('disabled', true)
    .addClass('disabled');

  /*
   * Scrolling Left
   */
  $('.scroll-btn-prev').click(function(event) {
    event.preventDefault();
    $('.e2a-tab-navs').animate(
      {
        scrollLeft: `-=${navContainerWidth / 2}px`,
      },
      'slow'
    );
  });

  /*
   * Scrolling Right
   */
  $('.scroll-btn-next').click(function(event) {
    event.preventDefault();
    $('.e2a-tab-navs').animate(
      {
        scrollLeft: `+=${navContainerWidth / 2}px`,
      },
      'slow'
    );
  });

  /*
   * Enable and Disable prev and next button
   * on edge of the left and right scroll
   */
  $('.e2a-tab-navs').scroll(function() {
    var $elem = $('.e2a-tab-navs');
    var newScrollLeft = $elem.scrollLeft(),
      width = $elem.outerWidth(true),
      scrollWidth = $elem.get(0).scrollWidth;

    $('.scroll-btn-prev')
      .prop('disabled', false)
      .removeClass('disabled');

    if (scrollWidth - newScrollLeft == width) {
      $('.scroll-btn-next')
        .prop('disabled', true)
        .addClass('disabled');

      $('.scroll-btn-wrapper').addClass('end-scroll');
    } else {
      $('.scroll-btn-next')
        .prop('disabled', false)
        .removeClass('disabled');
      $('.scroll-btn-wrapper').removeClass('end-scroll');
    }

    if (newScrollLeft === 0) {
      $('.scroll-btn-prev')
        .prop('disabled', true)
        .addClass('disabled');
      $('.scroll-btn-next')
        .prop('disabled', false)
        .removeClass('disabled');
    }

    scrollLeftPrev = newScrollLeft;
  });

});
function showSubMenu(cls){
    $('.e2a-sub-navigation .navbar-nav').hide();
    $('.e2a-sub-navigation .'+cls).fadeIn();
}
/**
 * If the tab content has datagrid, call resize when the tab is shown
 */
$(document).on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
    // var tab = $($(this).attr("href")+" .datagrid-f");
    // if(tab.length) $(tab).datagrid("resize");
})
$(document).on('click', '.window-link', function() {
    var url = $(this).attr('data-url');  if(!url) return;
    var size = $(this).attr('data-size') ? $(this).data('size') : 'sm';
    var el = $(this).attr('data-self') ? $(this) : null;
    return openWindow(url, size, el);
});
function openWindow(url, size, el = null){
    size = size || 'md';
    var suffix = url.indexOf('?') > -1 ? '':'/';
    var dialog = bootbox.dialog({
        size: size,
        backdrop: false,
        centerVertical: true,
        title: 'Loading...',
        message: '<p><i class="fa fa-spin fa-spinner"></i> Loading...</p>'
    });
    dialog.init(function(){
        dialog.find('.bootbox-body').load(url+suffix, function(){
            if(dialog.find('.title:first').length) dialog.find('.modal-title').html(dialog.find('.title:first').detach());
        });
    });    
}

$(document).on('click', '.tab-link', function() {
    var url = $(this).attr('data-url'); if(!url) return;
    var el = $(this).attr('data-self') ? $(this) : null;
    var title = $(this).text();
    openTab(url, el, title);
    // return openTab(url, el, title); // when used return, submenu isn't hiding.
});

function openTab(url, el = null, title = 'Loading...'){
    var id = url.replace(site, "").replace(/[^a-z0-9]+/gi, "-");
    var suffix = url.indexOf('?') > -1 ? '':'/';

    if(el){
        $(el).parents('.tab-pane').html('<div class="loader"></div>').load(url+suffix); return false;
    }
    title = title.length > 18 ? title.substring(0, 18)+'...' : title;
    document.title = title+' - ERP2ALL';
    if($(`#${id}`).length){
        $(`#e2a-tab-list a[href="#${id}"]`).tab('show');
    }else{        
        $('#e2a-tab-list').append($(`<li>
            <a href="#${id}" role="tab" data-toggle="tab"><span>${title}</span>
                <button type="button" class="btn-close"><i class="mdi mdi-close"></i></button>
            </a>
        </li>`));
        $('#e2a-tab-content').append($(`<div class="tab-pane fade" id="${id}"><div class="loader"></div></div>`));
        $(`#e2a-tab-list a[href="#${id}"]`).tab('show');

        $(`#${id}`).load(url+suffix, function(){
            if($(this).html()=='<timeout></timeout>'){
                window.location.reload();
            }else{                
                if($(this).find('.title:first').length){
                    title = stripeHtml($(this).find('.title:first').html());
                    title = title.length > 25 ? title.substring(0, 18)+'...' : title;
                    $(`#e2a-tab-list a[href="#${id}"] span`).html(title);
                } 
            }
        });
        disableCloseBtn();
        showHideScroll();
    }
    return false;
}
function closeThis(el){
    if($(el).closest('.modal').length){
        return $(el).closest('.modal').modal('hide').remove();
    }

    var current = $(el).parents('.tab-pane').attr('id');
    var show = $(`a[href="#${current}"]`).parents('li').prev().find('a');
    $(`a[href="#${current}"]`).parents('li').remove();
    $(`#${current}`).remove();
    show.tab('show');
    disableCloseBtn();
    showHideScroll();
}
$('.modal').on('hidden.bs.modal', function (event){
    $($this).modal('dispose').remove();
});
$('#e2a-tab-list').on('click', '.btn-close', function() {
    var current = $(this).parents('a').attr('href');
    var show = $(this).parents('li').is(':first-child') ? $(this).parents('li').next().find('a') : $(this).parents('li').prev().find('a');
    if(!$(this).parents('li').is(':only-child')){
        $(this).parents('li').remove();
        $(current).remove();
        show.tab('show');
    }
    disableCloseBtn();
    showHideScroll();
});
$(function () {
    initialize();
    $('map').imageMapResize();
    $(".search-help").on("search change keyup", function (){
        var text = this.value;
        if(text == ''){
            $('#sidebar .menu-wrapper').scrollTop(0);
        }
        $(this).parent().next(".searchable").highlite({
            text: text
        });
        
        if(typeof $('.highlight:first').offset() == "undefined"){
            $('#sidebar .menu-wrapper').scrollTop(0).scrollTop(0);
        }else{
            $('#sidebar .menu-wrapper').scrollTop(0).scrollTop($('.highlight:first').offset().top-100);
        }
    });
    $('#sidebar .menu-wrapper').on('scroll', function(){
        if($(this).scrollTop()>5){
            $(".search-input-group").addClass('fixed');
        }else{
            $(".search-input-group").removeClass('fixed');
        }
    });
})
function segment(number){
    var segments = $(location).attr('href').replace(base,'').split('/');
    return segments[number-1];
}
$( document ).ajaxComplete(function() {
    initialize();
});
/**
 * Combobox
 */
// $.extend($.fn.combobox.defaults.inputEvents, {
//     focus: function(e){
//         $(this).closest('.textbox').prev().combobox('showPanel');
//     },
// })
function initialize(){
    console.log('initialized');
    $('select.autocomplete').selectAutocomplete();
    $('.date').datepicker({
        format: "yyyy-mm-dd",
        daysOfWeekHighlighted: "5,6",
        autoclose: true,
        todayHighlight: true
    });  
    // Float only input
    $('input.float').on('input', function() {
        this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
    });
    $('input.float').each(function(){
        this.value = parseFloat(this.value || 0).toFixed(2);
    });
    $('input.float').on('change', function() {
        this.value = parseFloat(this.value || 0).toFixed(2);
    });
    $('input.float:not([readonly])').on('focus', function() {
        if(this.value == 0.00) this.value='' ;
    });
    $('input.float').on('blur', function() {
        if(this.value == '') this.value = parseFloat(this.value || 0).toFixed(2);
    });
    $('[data-toggle="tooltip"]').tooltip();
    $('[data-toggle="popover"]').popover();
    $('input[data-toggle="toggle"]').bootstrapToggle();
    $.event.trigger({
        type: "initialize",
        message: "On Initialize",
        time: new Date()
    });
    $('a.print').on('click', function(){
        let url = $(this).data('url');
        let w = window.open(url, '_blank');
    });
    $('.nav-tabs').tab();
}
function showAlert(data, ref){
    var alert = $(ref).closest('.modal').length ? $(ref).closest('.modal').find('.alert:first') : $(ref).closest('.tab-pane').find('.alert:first');
    if( alert.length ){
        if( typeof data['error'] !== 'undefined' )
            alert.removeClass('alert-success').addClass('alert-danger').html(data['error']).slideDown();
        if( typeof data['success'] !== 'undefined' )
            alert.removeClass('alert-danger').addClass('alert-success').html(data['success']).slideDown();
        $('html, body').animate({
            scrollTop: alert.offset().top - 100
        }, 300);
        setTimeout(function(){
            alert.slideUp();
        }, 10000);
    }
}
function goBack(){
    if (document.referrer.indexOf(window.location.host) !== -1){
        history.go(-1);
        return false;
    }else{
        window.location.href = base;
    }
}
function printDiv(id) {
    var content = document.getElementById(id).innerHTML;
    var head = document.getElementsByTagName('head')[0].innerHTML;
    
    var win = window.open();
    win.document.body.innerHTML = '<html><head>'+head+'</head><body>'+content+'</body></html>';
    setTimeout(function(){
        win.print();
        win.close();
    }, 500);
}
function screenShot(){
    html2canvas(document.body, {
        onrendered: function(canvas) {
            var link = document.createElement('a');
            link.download = 'screenshot.jpg';
            link.href = canvas.toDataURL();
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            delete link;            
            //var win=window.open();
            //win.document.write("<img src='"+canvas.toDataURL()+"'/>");
            //win.print();
        }
    });
    return false;
}
function setLang(locale){
    var expire = new Date();
    expire.setMonth(expire.getMonth() + 12);
    document.cookie = "lang="+locale+";expires="+expire+";path=/";
    window.location.reload();
}
function redirectTo(uri=''){
    if(uri==''){
        window.location.reload();
    }else{
        window.location = site+uri;
    }
}
function tableToExcel(table, filename){
    var elt = document.getElementById(table);
    var wb = XLSX.utils.table_to_book(elt, {sheet:filename});
    XLSX.writeFile(wb, filename+'.xlsx');
}

/**
 * Anik's JS for Footer
 */
var tabID = 1;
// var button = '<button type="button" class="dropdown-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="dropdownMenu" data-offset="30, 16"><i class="mdi mdi-menu-down"></i></button>';

// var tabCloseMenu = '<div class="dropdown-menu tab-close-menu dropdown-menu-right" aria-labelledby="dropdownMenu"><button class="close-btn">Delete</button><button>Option 1</button></div>';

var navContainerWidth = $('.e2a-tab-navs').width();
var scrollLeftPrev = 0;

/*
 * Function Name: Enable / Disable close button for only tab
 */

function disableCloseBtn() {
  if ($('#e2a_tab_list li').is(':only-child')) {
    $('#e2a_tab_list li .close-btn').addClass('disabled');
    $('#e2a_tab_list li .close-btn').prop('disabled', true);
  } else {
    $('#e2a_tab_list li .close-btn').removeClass('disabled');
    $('#e2a_tab_list li .close-btn').prop('disabled', false);
  }
}

/*
 * Function Name: Show Hide scroll
 */

function showHideScroll() {
  var totalNavItemsWidth = 0;
  $('#e2a-tab-list li').each(function() {
    totalNavItemsWidth += $(this).outerWidth(true);
  });
  if (totalNavItemsWidth > navContainerWidth) {
    $('.scroll-btn-wrapper').addClass('show-scroll');
  } else {
    $('.scroll-btn-wrapper').removeClass('show-scroll');
  }
}
/**
 * Footer JS Ends
 */
window.onerror = function (msg, url, lineNo, columnNo, error) {
    //alert(msg+url+lineNo+columnNo+error);
}
function stripeHtml(html){
    return (typeof html == "string") ? html.replace(/<(?:.|\n)*?>/gm, ''): html;
}
function setLoading(btn){
    btn.addClass('disabled');
    btn.data('original-text', btn.html());
    btn.html(btn.data('loading-text'));
}
function unsetLoading(btn){
    btn.html(btn.data('original-text'));
    btn.removeClass('disabled');
}
function dataURLtoBlob(dataurl){
    var arr = dataurl.split(','), mime = arr[0].match(/:(.*?);/)[1],
        bstr = atob(arr[1]), n = bstr.length, u8arr = new Uint8Array(n);
    while(n--){
        u8arr[n] = bstr.charCodeAt(n);
    }
    return new Blob([u8arr], {type:mime});
}