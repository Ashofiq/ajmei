@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/chosen.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/blogic_css/acc_tb.css') }}" />
    <style>
        .new-info-button{
            width: 100px;
        }
    </style>
@stop
@section('content')
<section class="content">
<input type="hidden" name="menu_selection" id="menu_selection" value="HRM@1" class="form-control" required>
<div class="title">
    <div  style="background-color:#e0e0e0" class="widget-header widget-header-small">
        <h6 class="widget-title smaller">
        <font size="2" color="blue"><b>Edit:Employee Entry</b></font>
        </h6>
        <div class="widget-toolbar">
            <a href="{{route('employees.index')}}" class="blue"><i class="fa fa-list"></i> List</a>
        </div>
    </div>
</div>
@if(Session::has('message'))
<div class="row">
   <div class="col-md-12">
     <p class="alert alert-success text-center"><b>{{ Session::get('message') }}</b></p>
   </div>
</div>
@endif
    <div class="widget-body">
      <div class="widget-main">  
       <form id="emp_Form" action="{{route('employees.update')}}" method="post">  
       {{ csrf_field() }}
    <input type="text" class="form-control input-sm" name="employee_edit_id" value="{{ $row->id }}" />
       <div class="row">
          <div class="col-md-4">
               <div class="input-group">
                 <div class="input-group-prepend">
                     <div class="input-group-text" style="min-width:130px">Company:</div>
                 </div>
                    <select name="company_code" class="autocomplete" id="company_code"  style="max-width:150px" required>
                    <option value="" >--Select--</option>
                        @if ($companies->count())
                            @foreach($companies as $company)
                                <option {{ $row->emp_com_id == $company->comp_id ? 'selected' : '' }} value="{{$company->comp_id}}" >{{ $company->comp_id }}-{{ $company->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
           </div>
         </div>
        <div class="row">
          <div class="col-md-3">
              <div class="input-group">
                  <div class="input-group-prepend">
                      <div class="input-group-text" style="min-width:130px">ID No:</div>
                      <input type="text" name="id_no" class="form-control" value="{{ $row->emp_id_no}}" required/>
                  </div>
             </div>
          </div>
          <div class="col-md-3">
              <div class="input-group">
                  <div class="input-group-prepend">
                    <div class="input-group-text" style="min-width:130px">Uid No Old:</div>
                    <input type="text" name="uid_no_old" class="form-control" 
                    value="{{ $row->emp_uid_no_old}}"/>
                  </div>
             </div>
          </div>
          <div class="col-md-4">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text" style="min-width:130px">Bank Account No:</div>
                    </div>
                    <input type="text" name="bank_account_no" class="form-control" 
                    value="{{$row->emp_bank_acc_no}}" />
               </div>
            </div>
         </div>
         <div class="row">
            <div class="col-md-6">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text" style="min-width:130px">Employee Name:</div>
                    </div>
                    <input type="text" name="employee_name" class="form-control" 
                    value="{{$row->emp_name}}" required/>
               </div>
            </div>
            <div class="col-md-4">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text" style="min-width:130px">Employee National Id:</div>
                    </div>
                    <input type="text" name="national_id" class="form-control" 
                    value="{{$row->emp_national_id}}"/>
               </div>
            </div>
         </div>
        
        <div class="row">
            <div class="col-md-6">
             <div class="input-group-prepend ss-item-required">
               <div class="input-group-prepend">
                   <div class="input-group-text" style="min-width:130px">Designation:</div>
               </div>
               
               <select id="Designation" name="designation" class="chosen-select" style="width: 400px" required>
               <option value="" >--Select Designation--</option>
                    @if ($sysinfos->count())
                        @foreach($sysinfos as $d)
                            @if($d->vComboType == 'Designation')
                                <option {{ $row->emp_desig_ref_id == $d->id ? 'selected' : '' }} value="{{ $d->id  }}" >{{ $d->vComboName }}</option>
                           @endif
                        @endforeach
                    @endif
               </select>
              </div>  
            </div>
            <div class="col-md-0">
                <div class="input-group">  
                    <button type="button" class="new-info-button btn btn-sm btn-success" data-toggle="modal" data-target-id="Designation" data-target="#addModal">New Desig</button>
               </div>
            </div> 
            
        </div> 
        <div class="row">
            <div class="col-md-6">
                <div class="input-group-prepend ss-item-required">
                    <div class="input-group-prepend">
                        <div class="input-group-text" style="min-width:130px">Section:</div>
                    </div>
                    <select id="Section" name="section" class="chosen-select" style="width: 400px" required>
                        <option value="" >--Select Section--</option>
                            @if ($sysinfos->count())
                                @foreach($sysinfos as $d)
                                    @if($d->vComboType == 'Section')
                                        <option {{ $row->emp_sec_ref_id == $d->id ? 'selected' : '' }} value="{{ $d->id  }}" >{{ $d->vComboName }}</option>
                                    @endif
                                @endforeach
                            @endif
                    </select> 
                </div>
            </div>
            <div class="col-md-0">
                <div class="input-group">  
                    <button type="button" class="new-info-button btn btn-sm btn-success" data-toggle="modal" data-target-id="Section" data-target="#addModal">New Section</button>
               </div>
            </div> 
        </div> 
        <div class="row">
            <div class="col-md-6">
             <div class="input-group-prepend ss-item-required">
               <div class="input-group-prepend">
                   <div class="input-group-text" style="min-width:130px">Department:</div>
               </div>
               <select name="department" class="chosen-select" style="width: 400px" id="Department"  required>
               <option value="" >--Select Department--</option>y
                   @if ($sysinfos->count())
                       @foreach($sysinfos as $d)
                            @if($d->vComboType == 'Department')
                                <option {{ $row->emp_dept_ref_id == $d->id ? 'selected' : '' }} value="{{ $d->id  }}" >{{ $d->vComboName }}</option>
                           @endif
                       @endforeach
                   @endif
               </select>
              </div>
            </div>
            <div class="col-md-0">
                <div class="input-group">  
                    <button type="button" class="new-info-button btn btn-sm btn-success" data-toggle="modal" data-target-id="Department" data-target="#addModal">New Dept</button>
               </div>
            </div> 
        </div> 
        <div class="row">
            <div class="col-md-6">
             <div class="input-group-prepend ss-item-required">
               <div class="input-group-prepend">
                   <div class="input-group-text" style="min-width:130px">Section Unit:</div>
               </div>
               <select name="unit" class="chosen-select" id="Category">
               <option value="" >--Select Unit--</option>y
                   @if ($sysinfos->count())
                       @foreach($sysinfos as $d)
                            @if($d->vComboType == 'Unit')
                            <option {{ $row->emp_unit_ref_id == $d->id ? 'selected' : '' }} value="{{ $d->id  }}" >{{ $d->vComboName }}</option>
                            @endif
                       @endforeach
                   @endif
               </select>
              </div>
            </div>
            <div class="col-md-0">
                <div class="input-group">  
                    <button type="button" class="new-info-button btn btn-sm btn-success" data-toggle="modal" 
                    data-target-id="Unit" data-target="#addModal">New Category</button>
               </div>
            </div> 
        </div> 
     
        <div class="row">
            <div class="col-md-6">
             <div class="input-group-prepend ss-item-required">
               <div class="input-group-prepend">
                   <div class="input-group-text" style="min-width:130px">Shift:</div>
               </div>
               <select name="shift" class="chosen-select" id="Shift" required>
               <option value="" >--Select Shift--</option>y
                   @if ($sysinfos->count())
                       @foreach($sysinfos as $d)
                            @if($d->vComboType == 'Shift')
                                <option {{ $row->emp_shift_ref_id == $d->id ? 'selected' : '' }} value="{{ $d->id  }}" >{{ $d->vComboName }}</option>
                            @endif 
                       @endforeach
                   @endif
               </select>
              </div>
            </div>
            <div class="col-md-0">
                <div class="input-group">  
                    <button type="button" class="new-info-button btn btn-sm btn-success" data-toggle="modal" data-target-id="Shift" data-target="#addModal">Office Time</button>
               </div>
            </div> 
        </div> 
        <div class="row">
          <div class="col-md-3">
            <div class="input-group ss-item-required">
                <div class="input-group-prepend ">
                    <div class="input-group-text" style="min-width:130px">Joining Date:</div>
                </div>
                <input type="text" size = "10" name="joining_date" onclick="displayDatePicker('joining_date');"  value="{{ $row->emp_joining_dt == '' ?  date('d-m-Y') :  date('d-m-Y',strtotime($row->emp_joining_dt)) }}"  required />
                <a href="javascript:void(0);" onclick="displayDatePicker('joining_date');"><img src="{{ asset('assets/images/calendar.png') }}" alt="calendar" border="0"></a> 
           </div>
          </div>
          <div class="col-md-3">
            <div class="input-group ss-item-required">
                <div class="input-group-prepend ">
                    <div class="input-group-text" style="min-width:130px">Date of Birth:</div>
                </div>
                <input type="text" size = "10" name="birth_date" onclick="displayDatePicker('birth_date');"  value="{{ $row->emp_birth_dt == '' ?  date('d-m-Y') :  date('d-m-Y',strtotime($row->emp_birth_dt)) }}"  required />
                <a href="javascript:void(0);" onclick="displayDatePicker('birth_date');"><img src="{{ asset('assets/images/calendar.png') }}" alt="calendar" border="0"></a> 
           </div>
          </div>
        </div>
        
        <div class="row">
            <div class="col-md-3">
            <div class="input-group">
                <div class="input-group-prepend">
                <div class="input-group-text" style="min-width:130px">Joining Salary:</div>
                </div>
                <input type="text" name="joining_salary" id="joining_salary" 
                value="{{$row->emp_joining_salary}}" required class="form-control"/>
            </div>
            </div>
            <div class="col-md-3">
            <div class="input-group">
                <div class="input-group-prepend">
                <div class="input-group-text" style="min-width:130px">Present Salary:</div>
                </div>
                <input type="text" name="present_salary" id="present_salary" 
                value="{{$row->emp_present_salary}}" class="form-control"/>
            </div>
            </div>
        </div>

        <div class="row">
            <!-- <div class="col-md-3">
                <div class="input-group">
                    <div class="input-group-prepend">
                    <div class="input-group-text" style="min-width:130px">Salary Grade:</div>
                    </div>
                    <input type="text" name="salary_grade" id="salary_grade" 
                    value="{{$row->emp_sal_grade}}" required class="form-control"/>
                </div>
            </div> -->
            <!-- <div class="col-md-3">
                <div class="input-group">
                    <div class="input-group-prepend">
                    <div class="input-group-text" style="min-width:130px">Actual Salary:</div>
                    </div>
                    <input type="text" name="actual_salary" id="actual_salary" 
                    value="{{$row->emp_actual_salary}}" class="form-control"/>
                </div>
            </div> -->
            <!-- <div class="col-md-3">
                <div class="input-group">
                    <div class="input-group-prepend">
                    <div class="input-group-text" style="min-width:130px">Others Salary:</div>
                    </div>
                    <input type="text" name="others_salary" id="others_salary" 
                    value="{{$row->emp_others_salary}}" class="form-control"/>
                </div>
            </div> -->
        </div>

        <div class="row">
            <div class="col-md-3">
             <div class="input-group-prepend ss-item-required">
               <div class="input-group-prepend">
                   <div class="input-group-text" style="min-width:130px">Pay Type:</div>
               </div>
               <select name="pay_type" class="chosen-select" id="pay_type" required>
                   
                    <option value="office">Office</option>
                    <option value="factory">Factory</option>
                   <!-- @if ($sysinfos->count())  
                       @foreach($sysinfos as $d)
                            @if($d->vComboType == 'Pay Type')
                                <option {{ $row->emp_paytype_ref_id == $d->id ? 'selected' : '' }} value="{{ $d->id  }}" >{{ $d->vComboName }}</option>
                            @endif  
                       @endforeach
                   @endif -->
               </select>
              </div>
            </div>



            <div class="col-md-3">
             <div class="input-group-prepend ss-item-required">
               <div class="input-group-prepend">
                   <div class="input-group-text" style="min-width:130px">OT type:</div>
               </div>
               <select name="ot_type" class="chosen-select" id="ot_type">
                    <option value="{{ $row->emp_ottype_ref_id }}">{{ $row->emp_ottype_ref_id }}</option>
                    <option value="Y">Y</option>
                    <option value="N">N</option>
               </select>
              </div>
            </div> 

        </div> 
        <div class="row">

            <div class="col-md-3">
             <div class="input-group-prepend ss-item-required">
               <div class="input-group-prepend">
                   <div class="input-group-text" style="min-width:130px">OUT Mark:</div>
               </div>
               <select name="out_mark" class="chosen-select" id="out_mark">
                    <option value="{{ $row->emp_outmark_ref_id }}">{{ $row->emp_outmark_ref_id }}</option>
                    <option value="Y">Y</option>
                    <option value="N">N</option>
                   
               </select>
              </div>
            </div> 
    
          
            <div class="col-md-3">
                <div class="input-group ss-item-required">
                    <div class="input-group-prepend ">
                        <div class="input-group-text" style="min-width:130px">Out Date:</div>
                    </div>
                    <input type="text" size = "13" name="out_date" onclick="displayDatePicker('out_date');"  
                    value="{{$row->emp_out_date == '' ?  '' : date('d-m-Y',strtotime($row->emp_out_date)) }}" />
                    <a href="javascript:void(0);" onclick="displayDatePicker('out_date');"><img src="{{ asset('assets/images/calendar.png') }}" alt="calendar" border="0"></a> 
                </div>
          </div>
        </div>


        <div class="row">
            <!-- <div class="col-md-3">
                <div class="input-group ss-item-required">
                    <div class="input-group-prepend ">
                        <div class="input-group-text" style="min-width:130px">OLD Id No:</div>
                    </div>
                    <input type="text" size = "15" name="old_id_no" value="{{$row->emp_old_id_no}}" /> 
                </div>
            </div>
            <div class="col-md-3">
                <div class="input-group ss-item-required">
                    <div class="input-group-prepend ">
                        <div class="input-group-text" style="min-width:130px">Machine ID:</div>
                    </div>
                    <input type="text" size = "15" name="secret_id" value="{{$row->emp_secret_id}}" /> 
                </div>
            </div> -->

            <div class="col-md-12">
             <div class="input-group-prepend ss-item-required">
               <div class="input-group-prepend">
                   <div class="input-group-text" style="min-width:130px">Remark:</div>
               </div>
               <textarea name="remark" style="width: 416px">{{ $row->remark }}</textarea>
              </div>
            </div> 
         </div> 
    
        <div class="row justify-content-left">
          <div class="col-sm-12 text-left">
              <button class="btn btn-sm btn-success" type="submit" ><i class="fa fa-save"></i> Save</button>
              <a href="{{route('employees.index')}}" class="btn btn-sm btn-info"><i class="fa fa-list"></i> List</a>
          </div>
        </div>
    </div>
  </div>
</form>
</section>

<!-- Start Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add System Information</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <br/>
      <form id="myForm" action="{{route('sysinfo.ajax.store')}}" method="POST" enctype="multipart/form-data">
        {{ csrf_field() }}
        <div class="modal-body">

        <div class="row">
            <div class="col-md-8">
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text" style="min-width:100px">Company Code&nbsp;:</span>
                </div>
                <select class="form-control m-bot15" id="company_code" name="company_code"  required>
                  <option value="" >--Select--</option>
                   @if ($companies->count())
                       @foreach($companies as $company)
                           <option {{ $company_code == $company->comp_id ? 'selected' : '' }} value="{{ $company->comp_id  }}" >{{ $company->comp_id }}--{{ $company->name }}</option>
                       @endforeach
                   @endif
               </select>
              </div>
          </div>
        </div>
        <div class="row">
              <div class="col-md-8">
                <div class="input-group mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text" style="min-width:100px">Dropdown Type&nbsp;:</span>
                  </div>
                  <input class="form-control" name="dropdown_type" type="text" id="dropdown_type" readonly required> 
                </div>
            </div>
        </div>

         <div class="row">
             <div class="col-md-8">
               <div class="input-group mb-3">
                 <div class="input-group-prepend">
                   <span class="input-group-text" style="min-width:100px">Name&nbsp;:</span>
                 </div>
                 <input type="text" id="dropdown_name"  name="dropdown_name"  class="form-control" placeholder="" required>
               </div>
   	       </div>
          </div>

         <div class="row">
            <div class="col-md-8">
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text" style="min-width:100px">Description&nbsp;:</span>
                </div>
                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
              </div>
            </div>
          </div>

          <div class="row">
           <div class="col-md-8">
             <div class="input-group mb-3">
               <div class="input-group-prepend">
                 <span class="input-group-text" style="min-width:100px">Level&nbsp;:</span>
               </div>
               <input type="text" id="level" name="level" class="form-control" placeholder="">
             </div>
           </div>
         </div>
        </div>
        <div class="modal-footer">
          <button id="ajaxSubmit" class="btn btn-primary" data-dismiss="modal">Save</button> 
          <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
        </div>
      </form>

    </div>
  </div>
</div>
<!-- End Add Modal -->

@stop
@section('pagescript')
  <script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
  <script src="{{ asset('assets/js/chosen.jquery.min.js') }}"></script>
  <script src="{{ asset('assets/js/ace-elements.min.js') }}"></script>
  <script src="{{ asset('assets/js/ace.min.js') }}"></script>

  <script>
         jQuery(document).ready(function(){
            jQuery('#ajaxSubmit').click(function(e){
               e.preventDefault();
               $.ajaxSetup({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  }
              });
               jQuery.ajax({
                  url: "{{ url('/sysinfo-ajax_store') }}",
                  method: 'post',
                  data: {
                    company_code: jQuery('#company_code').val(),
                    dropdown_type: jQuery('#dropdown_type').val(),
                    dropdown_name: jQuery('#dropdown_name').val(),
                    description: jQuery('#description').val(),
                    level: jQuery('#level').val()
                  },
                  success: function(result){
                    var result = JSON.parse(result);
                    if(result.statusCode == 201){
                        alert('Duplicate Entry! '+ jQuery('#dropdown_name').val() +' already exists !'); 
					}else{
                        jQuery('.alert').show();
                        jQuery('.alert').html(result.success);
                        getDropdownComboList();
                    }
                  }
                   
                });
               });
            });
      </script>

  <script type="text/javascript">
    var compcode = $('#company_code').val();
    $('#company_id').val(compcode);

    function customer() {
        var customer_id = $('#customer_id').val();
        $('#customer_code').val(customer_id);
    }

    $(document).ready(function() {

        $("#addModal").on("show.bs.modal", function (e) {
            var id = $(e.relatedTarget).data('target-id');
            $('#dropdown_type').val(id);
           // alert(id);
        });

    });

  </script>

  <script type="text/javascript">

  function removeRow  (el) {
      $(el).parents("tr").remove()
  }

  $(".addmore").on('click', function () {
      row_increment()
  });
 
  </script>
  <script type="text/javascript">
    function getDropdownComboList(){
      var compcode = jQuery('#company_code').val();
      var combo_type = jQuery('#dropdown_type').val();
      var dropdown_value = jQuery('#dropdown_name').val();
      
      //alert('PP:'+compcode+'--'+combo_type+'--'+dropdown_value);
      $.get('{{ url('/') }}/comboToLookup/' + compcode+'/'+combo_type, function(response) {
        var selectList = $('select[id="'+combo_type+'"]');
        selectList.chosen();
        selectList.empty();
        selectList.append('<option value="">--'+combo_type+'--</option>');
        $.each(response, function(index, element) {
            //  alert(element.id + "," + element.deliv_to);
            if(dropdown_value == element.vComboName){
                selectList.append('<option value="' + element.id + '" selected>' + element.vComboName + '</option>');
            }else{
                selectList.append('<option value="' + element.id + '">' + element.vComboName + '</option>');
            } 
        });
        selectList.trigger('chosen:updated');
      });

    }

    function get_customer_code() {
        var id = document.getElementById("customer_code").value;
        $.ajax({
            url: "{{ route('cust.get-customer-code') }}",
            type: "get",
            data: {id: id},
            success: function (response) {
                //   alert(response);
                $('#result').html(response);
            },
                error: function (xhr, status) {
                alert(id+' There is some error.Try after some time.');
            }
        });
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
