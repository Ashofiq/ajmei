@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/chosen.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/blogic_css/acc_tb.css') }}" />
@stop
@section('content')
<section class="content">
<input type="hidden" name="menu_selection" id="menu_selection" value="HRM@1" class="form-control" required>
<div class="title">
    <div  style="background-color:#e0e0e0" class="widget-header widget-header-small">
        <h6 class="widget-title smaller">
        <font size="2" color="blue"><b>Employee Attendance Entry</b></font>
        </h6>
        <div class="widget-toolbar">
            <a href="{{route('attendance.index')}}" class="blue"><i class="fa fa-list"></i> List</a>
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
       <form id="emp_Form" action="{{route('attendance.store')}}" method="post">  
       {{ csrf_field() }}
       <div class="row">
          <div class="col-md-4">
               <div class="input-group-prepend">
                 <div class="input-group-prepend">
                     <div class="input-group-text" style="min-width:130px">Company:</div>
                 </div> 
                    <select name="company_code" id="company_code"  class="chosen-select" onchange="getEmployeeList(this.value)" required>
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
            <div class="col-md-6">
                <div class="input-group-prepend ss-item-required">
                    <div class="input-group-prepend">
                        <div class="input-group-text" style="min-width:130px">Employee:</div>
                    </div> 
                    <div class="input-group"><select id="employee_id" name="employee_id" class="chosen-select" style="width: 400px" onchange="getEmployeeDetails(this.value)" required>
                        <option value="" >--Select Employee--</option>
                            @if ($employees->count())
                                @foreach($employees as $employee)
                                    <option {{ old('employee_id') == $employee->id ? 'selected' : '' }} value="{{ $employee->id }}">{{ $employee->emp_name }}</option>
                                @endforeach
                            @endif
                    </select> </div>
                </div>
            </div> 
            <div class="col-md-3">
              <div class="input-group">
                  <div class="input-group-prepend">
                      <div class="input-group-text" style="min-width:130px">Employee ID No:</div>

                      <input type="text" id="id_no" name="id_no" class="form-control" readonly required/>
                  </div>
             </div>
          </div> 
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text" style="min-width:130px">Designation:</div>
                    </div>
                    <input type="text" id="desig" name="designation" class="form-control" readonly/>
               </div>
            </div>
        </div> 
        <div class="row">   
            <div class="col-md-6">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text" style="min-width:130px">Department:</div>
                    </div>
                    <input type="text" id="department" name="department" class="form-control" readonly/>
               </div>
            </div>
        </div> 
        <div class="row">  
            <div class="col-md-6">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text" style="min-width:130px">Section:</div>
                    </div>
                    <input type="text" id="section" name="section" class="form-control" readonly/>
               </div>
            </div>
        </div> 
        <div class="row">
          <div class="col-md-4">
            <div class="input-group ss-item-required">
                <div class="input-group-prepend ">
                    <div class="input-group-text" style="min-width:130px">In Time:</div>
                </div>
                <input type="Text" name="inDate" id="demo1" maxlength="25" size="25" value ="" />
				<a href="javascript:void(0);" onclick="javascript:NewCssCal ('demo1','ddMMyyyy','dropdown',true,'12',true)" ><img src="{{ asset('assets/images/calendar.png') }}" style="cursor:pointer"/> </a> 
           </div>
          </div>
          <div class="col-md-4">
            <div class="input-group ss-item-required">
                <div class="input-group-prepend ">
                    <div class="input-group-text" style="min-width:130px">Out Time:</div>
                </div> 
                <input type="Text" name="outDate" id="demo2" maxlength="25" size="25" value ="" />
				<a href="javascript:void(0);" onclick="javascript:NewCssCal ('demo2','ddMMyyyy','dropdown',true,'12',true)" ><img src="{{ asset('assets/images/calendar.png') }}" style="cursor:pointer"/></a>
           </div>
          </div>
        </div>
         
        <div class="row">
            <div class="col-md-3">
             <div class="input-group-prepend ss-item-required">
               <div class="input-group-prepend">
                   <div class="input-group-text" style="min-width:130px">Status:</div>
               </div>
               <select name="att_type" class="chosen-select" id="att_type" required>
               <option value="" >--Select Status--</option>y
                    <option {{ old('att_type') == 'Present' ? 'selected' : '' }} value="Present" >Present</option>
                    <option {{ old('att_type') == 'Absent' ? 'selected' : '' }} value="Absent">Absent</option>
               </select>
              </div>
            </div> 
        </div> 
        <br/>
        <div class="row justify-content-left">
          <div class="col-sm-12 text-left">
              <button class="btn btn-sm btn-success" type="submit" ><i class="fa fa-save"></i> Save</button>
              <a href="{{route('attendance.index')}}" class="btn btn-sm btn-info"><i class="fa fa-list"></i> List</a>
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
    
    function getEmployeeList(comp_id){  
        getDropdownEmployeeList(comp_id,0);
    }

    function getDropdownEmployeeList(comp_id,oldItem){
        var compcode = comp_id;  
        //alert(compcode);
      $.get('{{ url('/') }}/employeeToLookup/'+compcode, function(response) {
        var selectList = $('select[id="employee_id"]');
        selectList.chosen();
        selectList.empty();
        //$('#itemid_' + i).append('<option value="">--Select Item--</option>');
        selectList.append('<option value="" disabled selected>--Select Item1--</option>');
        $.each(response, function(index, element) { 
          if (oldItem ==  element.id){
            selectList.append('<option value="' + element.id + '" selected>' + element.emp_name +' ('+ element.itm_cat_name +')</option>');
          }else{
            selectList.append('<option value="' + element.id + '">' + element.emp_name +'</option>');
          }
        });
        selectList.trigger('chosen:updated');
      });
    }
    
</script>

  <script type="text/javascript">
    var compcode = $('#company_code').val();
    $('#company_id').val(compcode);
  
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
