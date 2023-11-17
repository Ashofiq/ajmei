@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/chosen.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/blogic_css/acc_tb.css') }}" />
    <script src="https://cdn.jsdelivr.net/timepicker.js/latest/timepicker.min.js"></script>
    <link href="https://cdn.jsdelivr.net/timepicker.js/latest/timepicker.min.css" rel="stylesheet"/>

@stop
@section('content')
<section class="content">
<input type="hidden" name="menu_selection" id="menu_selection" value="HRM@1" class="form-control" required>
<div class="title">
    <div  style="background-color:#e0e0e0" class="widget-header widget-header-small">
        <h6 class="widget-title smaller">
        <font size="2" color="blue"><b>Office Timing Entry</b></font>
        </h6>
        <div class="widget-toolbar">
            <a href="{{route('timesheet.index')}}" class="blue"><i class="fa fa-list"></i> List</a>
        </div>
    </div>
</div>
@if(Session::has('message'))
<div class="row">
   <div class="col-md-12">
     <p class="alert alert-success"><b>{{ Session::get('message') }}</b></p>
   </div>
</div>
@endif
    <div class="widget-body">
      <div class="widget-main">  
       <form id="emp_Form" action="{{route('timesheet.store')}}" method="post">  
       {{ csrf_field() }}
       
        <div class="row">
            <div class="col-md-6">
                <div class="input-group-prepend ss-item-required">
                    <div class="input-group-prepend">
                        <div class="input-group-text" style="min-width:130px">Shift:</div>
                    </div>
                    <select id="shift_id" name="shift_id" class="chosen-select" style="width: 400px"  required>
                        <option value="" >--Select Shift--</option>
                            @if ($sysinfos->count())
                                @foreach($sysinfos as $combo)
                                    <option {{ old('shift_id') == $combo->id ? 'selected' : '' }} value="{{ $combo->id }}">{{ $combo->vComboName }}</option>
                                @endforeach
                            @endif
                    </select> 
                </div>
            </div> 

            <div class="col-md-4">
               <div class="input-group">
                 <div class="input-group-prepend">
                     <div class="input-group-text" style="min-width:130px">Company:</div>
                 </div>
                    <select name="company_code" class="autocomplete" id="company_code"  style="max-width:150px" required>
                    <option value="" >--Select--</option>
                        @if ($companies->count())
                            @foreach($companies as $company)
                                <option {{ $company_code == $company->comp_id ? 'selected' : '' }} value="{{$company->comp_id}}" >{{ $company->comp_id }}-{{ $company->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
           </div>

        </div>
          
        <div class="row">
          <div class="col-md-4">
            <div class="input-group ss-item-required">
                <div class="input-group-prepend ">
                    <div class="input-group-text" style="min-width:130px">Start Time:</div>
                </div> 
                <input type="time" id="stime" name="stime" placeholder="Time"> 
           </div>
          </div>
          <div class="col-md-4">
            <div class="input-group ss-item-required">
                <div class="input-group-prepend ">
                    <div class="input-group-text" style="min-width:130px">End Time:</div>
                </div> 
                <input type="time" id="etime" name="etime" placeholder="Time">  
           </div>
          </div>
        </div>
         
        <div class="row">
          <div class="col-md-4">
            <div class="input-group ss-item-required">
                <div class="input-group-prepend ">
                    <div class="input-group-text" style="min-width:130px">Grace Time:</div>
                </div>
                <input type="text" id="time" name="grtime" placeholder="Time">  
           </div>
          </div> 
        </div>
        <br/>
        <div class="row justify-content-left">
          <div class="col-sm-12 text-left">
              <button class="btn btn-sm btn-success" type="submit" ><i class="fa fa-save"></i> Save</button>
              <a href="{{route('timesheet.index')}}" class="btn btn-sm btn-info"><i class="fa fa-list"></i> List</a>
          </div>
        </div>
    </div>
  </div>
</form>
</section>

@stop
@section('pagescript')
  <script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
  <script src="{{ asset('assets/js/chosen.jquery.min.js') }}"></script>
  <script src="{{ asset('assets/js/ace-elements.min.js') }}"></script>
  <script src="{{ asset('assets/js/ace.min.js') }}"></script>

<script type="text/javascript">
    function getEmployeeDetails(empid){  
        var compid = $('#company_code').val();
        //alert(compid+'--'+empid);
        //alert('/get-emp-details-inf/getdetails/'+compid+'/'+empid+'/getfirst');
        $.ajax({  //create an ajax request to display.php
            type: "GET",
            url: 'get-emp-details-inf/getdetails/'+compid+'/'+empid+'/getfirst',
            success: function (data) {
                //alert(data.designation);
                $("#id_no").val(data.emp_id_no);
                $("#desig").val(data.designation);
                $("#department").val(data.department);
                $("#section").val(data.section);
            }
        });
    }  
</script>
<script type="text/javascript">
    var timepicker = new TimePicker('time', {
        lang: 'en',
        theme: 'dark'
    });
    timepicker.on('change', function(evt) {
    
    var value = (evt.hour || '00') + ':' + (evt.minute || '00');
    evt.element.value = value;

    });

     
</script>
  <!--  Select Box Search-->
  <script type="text/javascript">

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
  </script>

@stop
