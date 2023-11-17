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
        <font size="2" color="blue"><b>Employee Leave Entry</b></font>
        </h6>
        <div class="widget-toolbar">
            <a href="{{route('leave.index')}}" class="blue"><i class="fa fa-list"></i> List</a>
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
       <form id="leave_Form" action="{{route('leave.store')}}" method="post">  
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
            <div class="col-md-6">
                <div class="input-group-prepend ss-item-required">
                    <div class="input-group-prepend">
                        <div class="input-group-text" style="min-width:130px">Leave Type:</div>
                    </div>
                    <div class="input-group"><select id="leave_type_id" name="leave_type_id" class="chosen-select" style="width: 400px" onchange="getLeaveDetails(this.value)" required>
                        <option value="" >--Select Leave Type--</option>
                            @if ($leavetypes->count())
                                @foreach($leavetypes as $leavetype)
                                    <option {{ old('employee_id') == $leavetype->id ? 'selected' : '' }} value="{{ $leavetype->id }}">{{ $leavetype->leave_type }}</option>
                                @endforeach
                            @endif
                    </select></div>
                </div>
            </div>
            <div class="col-md-1"><b>=</b></div>
            <div class="col-md-1">
                <input type="text" size = "3"  id="leave_Remain" name="leave_Remain" class="form-control" readonly/>&nbsp;(Remain)
            </div> 
        </div>
        <div class="row">
          <div class="col-md-3">
            <div class="input-group ss-item-required">
                <div class="input-group-prepend ">
                    <div class="input-group-text" style="min-width:130px">From Date:</div>
                </div>
                <input type="text" size = "10" id="from_date" name="from_date" onclick="displayDatePicker('from_date');"  value="{{ old('from_date') == '' ? '' :  date('d-m-Y',strtotime(old('from_date'))) }}"  required />
                <a href="javascript:void(0);" onclick="displayDatePicker('from_date');"><img src="{{ asset('assets/images/calendar.png') }}" alt="calendar" border="0"></a> 
           </div>
          </div>
          <div class="col-md-3">
            <div class="input-group ss-item-required">
                <div class="input-group-prepend ">
                    <div class="input-group-text" style="min-width:130px">To Time:</div>
                </div> 
                <input type="text" size = "10" id="to_date" name="to_date" onclick="displayDatePicker('to_date');"  value="{{ old('to_date') == '' ? '' :  date('d-m-Y',strtotime(old('to_date'))) }}"  required />
                <a href="javascript:void(0);" onclick="displayDatePicker('to_date');"><img src="{{ asset('assets/images/calendar.png') }}" alt="calendar" border="0"></a> 
           </div>
          </div>
          <div class="col-md-1"><b>=</b></div>
          <div class="col-md-1">
            <input type="text" size = "10"  id="leave_days" name="leave_days" class="form-control" readonly/>
          </div>
        </div>
         
        <div class="row">
            <div class="col-md-6">
             <div class="input-group-prepend ss-item-required">
               <div class="input-group-prepend">
                   <div class="input-group-text" style="min-width:130px">Reasons:</div>
               </div>
               <textarea id="reasons" name="reasons" rows="5" cols="100" class="form-control config" placeholder="Reasons" maxlength="500">{{ old('reasons') }}</textarea>
              </div>
            </div> 
        </div> 
        <br/>
        <div class="row justify-content-left">
          <div class="col-sm-12 text-left">             
              <button class="btn btn-sm btn-success" type="button" id='btn2' ><i class="fa fa-save"></i> Save</button>
              <a href="{{route('leave.index')}}" class="btn btn-sm btn-info"><i class="fa fa-list"></i> List</a>
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
    var form = document.getElementById('leave_Form');
    document.getElementById('btn2').onclick = function() {
        if(formcheck()){
            form.submit();
        }
    }

    function formcheck() {
        var isSubmit = true; 
        var leave_days = $('#leave_days').val();
        var leave_Remain = $('#leave_Remain').val();

       // alert(parseFloat(leave_days) +'>'+ parseFloat(leave_Remain));
        if( parseFloat(leave_days) >= parseFloat(leave_Remain) ){
            alert('Balance is not available');
            isSubmit = false;
            return isSubmit;
        }   
        return isSubmit; 
    }

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
                $("#leave_Remain").val(0); 
            }
        });
    }

    function getLeaveDetails(leavetypeid){  
        var compid = $('#company_code').val();
        var empid = $('#employee_id').val(); 
        
        //alert(compid+'--'+empid);
        //alert('get-emp-leave-bal-inf/getdetails/'+compid+'/'+empid+'/'+leavetypeid+'/getfirst');
        $.ajax({  //create an ajax request to display.php
            type: "GET",
            url: 'get-emp-leave-bal-inf/getdetails/'+compid+'/'+empid+'/'+leavetypeid+'/getfirst',
            success: function (data) {
                //alert(data);
                $("#leave_Remain").val(data); 
            }
        });
    }
    
    function getEmployeeList(comp_id){  
        getDropdownEmployeeList(comp_id,0);
        getDropdownLeaveTypeList(comp_id,0);
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

    function getDropdownLeaveTypeList(comp_id,oldItem){
        var compcode = comp_id;  
       //alert(compcode);
      $.get('{{ url('/') }}/leaveTypeToLookup/'+compcode, function(response) {
        var selectList = $('select[id="leave_type_id"]');
        selectList.chosen();
        selectList.empty();
        //$('#itemid_' + i).append('<option value="">--Select Item--</option>');
        selectList.append('<option value="" disabled selected>--Select Item1--</option>');
        $.each(response, function(index, element) { 
          if (oldItem ==  element.id){
            selectList.append('<option value="' + element.id + '" selected>' + element.leave_type +' ('+ element.itm_cat_name +')</option>');
          }else{
            selectList.append('<option value="' + element.id + '">' + element.leave_type +'</option>');
          }
        });
        selectList.trigger('chosen:updated');
      });
    }

    
</script>

  <script type="text/javascript">
    $(document).ready(function() {
        $('#from_date').blur(function() {  
            var v_start = $('#from_date').val(); 
            var v_end = $('#to_date').val(); 
            if ( v_start != '' && v_end != '') {
                calucateDays(v_start,v_end);
            }
        }); 

        $('#to_date').blur(function() {  
            var v_start = $('#from_date').val(); 
            var v_end = $('#to_date').val(); 
            if ( v_start != '' && v_end != '') {
                calucateDays(v_start,v_end);
            }
        }); 

    });

    function calucateDays (v_start,v_end){ 
        var date1 = v_start.split('-');
        var date2 = v_end.split('-');
        // var newDate = date1[1] + '/' +date1[0] +'/' +date1[2]; 
        var newDate1 = new Date(date1[2], date1[1] - 1, date1[0])
        var newDate2 = new Date(date2[2], date2[1] - 1, parseInt(date2[0])+1)
                    
        // To calculate the time difference of two dates
        var Difference_In_Time = newDate2.getTime() - newDate1.getTime();
                    
        // To calculate the no. of days between two dates
        var Difference_In_Days = Difference_In_Time / (1000 * 3600 * 24);
        //alert(Difference_In_Days);
        $('#leave_days').val(Difference_In_Days);
    }
 
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
