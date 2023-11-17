@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/chosen.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/blogic_css/acc_tb.css') }}" />
@stop
@section('content')
<section class="content">
<input type="hidden" name="menu_selection" id="menu_selection" value="SD@1" class="form-control" required>
<div class="title">
      <div  style="background-color:#e0e0e0" class="widget-header widget-header-small">

      <h6 class="widget-title smaller">
      <font size="2" color="blue"><b>Customer Entry</b></font>
      </h6>
     <div class="widget-toolbar">
       <a href="{{route('cust.index')}}" class="blue"><i class="fa fa-list"></i> List</a>
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
      <form  method="post" action="{{ route('get-cust') }}" class="delete_form">
        {{ csrf_field() }}
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
                                <option {{ $company_code == $company->comp_id ? 'selected' : '' }} value="{{$company->comp_id}}" >{{ $company->comp_id }}-{{ $company->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
           </div>
 
         </div>
       </form>

       <form id="acc_Form" action="{{route('cust.store')}}" method="post">
       <input type="hidden" class="form-control input-sm" readonly name="result_customer_id" value="{{ old('result_customer_id') }}" id="result_customer_id">
       <input type="hidden" class="form-control input-sm" readonly name="company_id" value="{{ old('company_id') }}" id="company_id">
       
       {{ csrf_field() }}
        <div class="row">
          <div class="col-md-3">
              <div class="input-group">
                  <div class="input-group-prepend">
                      <div class="input-group-text" style="min-width:130px">Customer Code:</div>

                      <div class="col-xs-10 col-sm-6 @error('customer_code') has-error @enderror">
                          <input type="text" class="form-control" name="customer_code" id="customer_code" onkeyup="get_customer_code()" value="{{ old('customer_code') }}" placeholder="" readonly required>
                          <span id="result"></span>
                          @error('customer_code')
                          <span class="text-danger">
                                       {{ $message }}
                                  </span>
                          @enderror
                      </div>
                  </div>
             </div>
          </div>
           <div class="col-md-4">
               <div class="input-group ss-item-required"> 
                       <select name="customer_id" class="col-xs-10 col-sm-8 chosen-select" id="customer_id" onchange="customer()" required>
                           <option value="" disabled selected>- Select Customer -</option>
                           @foreach($customers as $customer)
                               <option {{ old('customer_id') == $customer->cust_code ? 'selected' : '' }} value="{{ $customer->cust_code }}">{{ $customer->cust_name }}</option>
                           @endforeach
                       </select>
                       @error('customer_id')
                       <span class="text-danger">{{ $message }}</span>
                       @enderror 
              </div>
           </div>
         </div>
         <div class="row">
            <div class="col-md-4">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text" style="min-width:130px">Credit Amount:</div>
                    </div>
                    <input type="text" name="creditamt" class="form-control" required/>
               </div>
            </div>
         </div>
        <div class="row">
           <div class="col-md-4">
               <div class="input-group">
                   <div class="input-group-prepend">
                       <div class="input-group-text" style="min-width:130px">Mobile No:</div>
                   </div>
                   <input type="text" name="mobileno" value=" " class="form-control"/>
              </div>
           </div>
        </div>

        <div class="row">
             <div class="col-md-7">
                      <div class="input-group ss-item-required">
                          <div class="input-group-prepend">
                              <div class="input-group-text" style="min-width:130px">Address1:</div>
                          </div>
                          <textarea name="address1" rows="2" cols="100" class="form-control config" placeholder="Narration" maxlength="500" required>{{ old('address1') }}</textarea>
                      </div>
              </div>
              <div class="col-md-2">
                <div class="input-group">
                    <input type="checkbox" name="same_as_delivery" value="1" {{old('same_as_delivery') == 1 ? 'checked' : '' }} />&nbsp;Same As Delivery
               </div>
            </div>
          </div>
          <div class="row">
               <div class="col-md-7">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text" style="min-width:130px">Address2:</div>
                            </div>
                            <textarea name="address2" rows="2" cols="100" class="form-control config" placeholder="Narration" maxlength="500">{{ old('address2') }}</textarea>
                        </div>
                </div>
          </div>
          <div class="row">
            <div class="col-md-3">
               <div class="input-group ss-item-required">
                    
                   <select name="district_id" class="col-xs-4 col-sm-2 chosen-select" id="district_id"  required>
                   <option value="-1" >--Select District--</option>
                       @if ($dist_list->count())
                           @foreach($dist_list as $cmb)
                               <option {{ old('district_id') == $cmb->id ? 'selected' : '' }} value="{{$cmb->id}}" >{{ $cmb->vCityName }}</option>
                           @endforeach
                       @endif
                   </select> 
              </div>
            </div>
            <div class="col-md-4">
               <div class="input-group ss-item-required">
                   
                   <select name="salesperson_id" class="col-xs-10 col-sm-8 chosen-select" id="salesperson_id"  required>
                   <option value="-1" >--Select Person--</option>
                       @if ($salespersons->count())
                           @foreach($salespersons as $cmb)
                               <option {{ old('salesperson_id') == $cmb->id ? 'selected' : '' }} value="{{$cmb->id}}" >{{ $cmb->sales_name }}</option>
                           @endforeach
                       @endif
                   </select>
                    
              </div>
            </div>
            <div class="col-md-4">
               <div class="input-group">
                   <select name="courrier_id" class="col-xs-10 col-sm-8 chosen-select" id="courrier_id">
                   <option value="" >--Select Courrier--</option>
                       @if ($courr_list->count())
                           @foreach($courr_list as $cmb)
                               <option {{ old('courrier_id') == $cmb->id ? 'selected' : '' }} value="{{$cmb->id}}" >{{ $cmb->courrier_to }}</option>
                           @endforeach
                       @endif
                   </select>
              </div>
            </div>
          </div>
    <fieldset>
    <legend>Contact Information</legend>
     <div class="row justify-content-center">
       <div class="col-md-12">
        <table id="cust_table" class="table table-striped table-data table-report ">
          <thead class="accTable">
            <tr>
              <th width="3%" class="text-center">Id</th>
              <th width="15%" class="text-center">Contact Name</th>
              <th width="10%" class="text-center">Cell</th>
              <th width="10%" class="text-center">Email</th>
              <th width="3%" class="text-center">&nbsp;</th>
            </tr>
          </thead>
          <tbody class="accTable" style="background-color: #ffffff;">
            <tr>
              <td width="3%" class="text-center">1</td>
              <td width="15%"><input type="text" name="con_name[]" id="con_name_1" class="form-control" autocomplete="off" ></td>
              <td width="10%"><input type="text" name="cell[]" id="cell_1"  class="form-control input-sm" autocomplete="off" ></td>
              <td width="10%"><input type="text" name="email[]" id="email_1" class="form-control input-sm"  autocomplete="off"></td>
              <td width="3%"><button type="button" class="btn btn-primary btn-sm addmore" id="addMore">+</button></td>
            </tr>
           </tbody>
        </table>
      </div>
    </div>
  </fieldset>

   <br/>
    <div class="row justify-content-left">
          <div class="col-sm-12 text-left">
              <button class="btn btn-sm btn-success" type="submit" ><i class="fa fa-save"></i> Save</button>
              <a href="{{route('cust.index')}}" class="btn btn-sm btn-info"><i class="fa fa-list"></i> List</a>
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
    var compcode = $('#company_code').val();
    $('#company_id').val(compcode);

    function customer() {
        var customer_id = $('#customer_id').val();
        $('#customer_code').val(customer_id);
    }
  </script>

  <script type="text/javascript">

  function removeRow  (el) {
      $(el).parents("tr").remove()
  }

  $(".addmore").on('click', function () {
      row_increment()
  });

  function row_increment() {

      var i = $('#cust_table tr').length ;
      html = '<tr >';
      html += '<td width="3%" class="text-center">' + i + '</td>';
      html += '<td width="15%"><input type="text" name="con_name[]" id="con_name_' + i + '" class="form-control input-sm" autocomplete="off"></td>';
      html += '<td width="10%"><input type="text" name="cell[]" id="cell_' + i + '" class="form-control" autocomplete="off"></td>';
      html += '<td width="10%"><input type="text" name="email[]" id="email_' + i + '" class="form-control input-sm" autocomplete="off"></td>';
      html += '<td width="3%"><div class="btn-group btn-corner"><button type="button" tabindex="-1" class="btn btn-danger btn-xs delete" title="Delete This Row" onclick="removeRow(this)"><i class="fa fa-trash"></i>Del</button></div></td>';
      html += '</tr>';

      $('#cust_table').append(html);
      document.getElementById('con_name_'+i).focus();
      i++;
  }

  </script>
  <script type="text/javascript">
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
