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
      <font size="2" color="blue"><b>Edit::Customer Entry</b></font>
      </h6>
     <div class="widget-toolbar">
       <!-- <a href="{{route('cust.index')}}" class="blue"><i class="fa fa-list"></i> List</a> -->
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
<form id="acc_Form" action="{{route('cust.update') }}" method="post">
 {{ csrf_field() }}
  @foreach($rows as $d)
  <input type="hidden" class="form-control input-sm" name="customer_id" value="{{ $d->id }}" id="customer_id" readonly>
    <div class="widget-body">
      <div class="widget-main">
         <div class="row">
          <div class="col-md-6">
               <div class="input-group">
                 <div class="input-group-prepend">
                     <div class="input-group-text" style="min-width:130px">Company:</div>
                 </div>
                  <input type="hidden" name="company_id"  id="company_id"  value="{{ $d->cust_com_id }}"class="form-control input-sm" readonly />
                  <input type="text" name="company_name"  id="company_name"  value="{{ $d->name }}"class="form-control input-sm" readonly />
                </div>
           </div>
         </div>

        <div class="row">
            <div class="col-md-4">
               <div class="input-group ss-item-required">
                   <div class="input-group-prepend ">
                       <div class="input-group-text" style="min-width:130px">Customer Name:</div>
                       <input type="text" name="customer_name"  id="customer_name"  value="{{ $d->cust_name }}"class="form-control input-sm" readonly />
                   </div>
              </div>
           </div>

          <!-- <div class="col-md-3">
              <div class="input-group">
                  <div class="input-group-prepend">
                      <div class="input-group-text" style="min-width:130px">Customer Code:</div>
                      <input type="text" name="customer_code"  id="customer_code"  value="{{ $d->cust_code }}"class="form-control input-sm" readonly />
                  </div>
             </div>
          </div> -->
           
         </div>

         <div class="row">
           <div class="col-md-4">
               <div class="input-group">
                   <div class="input-group-prepend">
                       <div class="input-group-text" style="min-width:130px">Contract Person:</div>
                   </div>
                   <input type="text" name="contract_person" value="{{ $d->contract_person }}" class="form-control" required />
              </div>
           </div>
        </div>

        <div class="row">
           <div class="col-md-4">
               <div class="input-group">
                   <div class="input-group-prepend">
                       <div class="input-group-text" style="min-width:130px">Official Mobile No:</div>
                   </div>
                   <input type="text" name="mobileno" value="{{ $d->cust_mobile }}" class="form-control" required />
              </div>
           </div>
        </div>

        <div class="row">
           <div class="col-md-4">
               <div class="input-group">
                   <div class="input-group-prepend">
                      <div class="input-group-text" style="min-width:130px">Personal Mobile No:</div>
                   </div>
                   <input type="text" name="personalMobileno" value="{{ $d->personalMobileno }}" class="form-control" required />
              </div>
           </div>
        </div>

        <div class="row">
           <div class="col-md-4">
               <div class="input-group">
                   <div class="input-group-prepend">
                      <div class="input-group-text" style="min-width:130px">Email:</div>
                   </div>
                   <input type="text" name="email" value="{{ $d->email }}" class="form-control" required />
              </div>
           </div>
        </div>


        <div class="row">
             <div class="col-md-7">
                      <div class="input-group ss-item-required">
                          <div class="input-group-prepend">
                              <div class="input-group-text" style="min-width:130px">Office Address:</div>
                          </div>
                          <textarea name="address1" rows="2" cols="100" class="form-control config" placeholder="Narration" maxlength="500" required>{{ $d->cust_add1 }}</textarea>
                      </div>
              </div>
              <!-- <div class="col-md-4">
                 <div class="input-group">
                     <input type="checkbox" name="same_as_delivery" value="{{ $d->same_as_del }}" {{ $d->same_as_del == 1 ? 'checked' : '' }} />&nbsp;Same As Delivery
                </div>
             </div> -->
          </div>
          <div class="row">
               <div class="col-md-7">
                        <div class="input-group ss-item-required">
                            <div class="input-group-prepend">
                                <div class="input-group-text" style="min-width:130px">Delivery Address:</div>
                            </div>
                            <textarea name="address2" rows="2" cols="100" class="form-control config" placeholder="Narration" maxlength="500">{{ $d->cust_add2 }}</textarea>
                        </div>
                </div>
          </div>
          <!-- <div class="row">
              <div class="col-md-3">
                 <div class="input-group">
                     <select name="district_id" class="col-xs-4 col-sm-2 chosen-select" id="district_id"  style="max-width:250px" required>
                     <option value="" >--Select District--</option>
                         @if ($dist_list->count())
                             @foreach($dist_list as $cmb)
                                 <option {{ $d->cust_dist_id == $cmb->id ? 'selected' : '' }} value="{{$cmb->id}}" >{{ $cmb->vCityName }}</option>
                             @endforeach
                         @endif
                     </select>
                </div>
              </div>
              <div class="col-md-4">
                 <div class="input-group"> 
                     <select name="salesperson_id" class="col-xs-10 col-sm-8 chosen-select" id="salesperson_id">
                     <option value="" >--Select Person--</option>
                         @if ($salespersons->count())
                             @foreach($salespersons as $cmb)
                                 <option {{ $d->cust_sales_per_id == $cmb->id ? 'selected' : '' }} value="{{$cmb->id}}" >{{ $cmb->sales_name }}</option>
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
                                 <option {{ $d->cust_courrier_id == $cmb->id ? 'selected' : '' }} value="{{$cmb->id}}" >{{ $cmb->courrier_to }}</option>
                             @endforeach
                         @endif
                     </select>
                </div>
              </div>
          </div> -->
          <!-- <div class="row">
             <div class="col-md-4">
                 <div class="input-group">
                     <div class="input-group-prepend">
                        <div class="input-group-text" style="min-width:130px">Customer Disc(%):</div>
                     </div>
                     <input type="text" name="cust_own_commission" id="cust_own_commission" 
                     value="{{$d->cust_own_comm}}"  class="form-control"/>
                </div>
             </div>
          </div>
          <div class="row">
             <div class="col-md-4">
                 <div class="input-group">
                     <div class="input-group-prepend">
                         <div class="input-group-text" style="min-width:130px">SP Comm(%):</div>
                     </div>
                     <input type="text" name="commision" id="commision" value="{{$d->cust_overall_comm}}"  class="form-control"/>
                </div>
             </div>
          </div>
          <div class="row">
             <div class="col-md-4">
                 <div class="input-group">
                     <div class="input-group-prepend">
                         <div class="input-group-text" style="min-width:130px">VAT (%):</div>
                     </div>
                     <input type="text" name="cust_vat" value="{{ $d->cust_VAT }}" class="form-control" />
                </div>
             </div> 
          </div> -->
      @endforeach
    <!-- <fieldset>
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
              <th width="3%"><button type="button" class="btn btn-primary btn-sm addmore" id="addMore">+</button></th>
            </tr>
          </thead>
          <tbody class="accTable" style="background-color: #ffffff;">
            <?php $i = 0;?>
            @foreach($row_d as $row)
            <tr>
              <td width="3%" class="text-center">{{$i += 1}}</td>
              <td width="15%"><input type="text" name="con_name[]" id="con_name_{{$i}}" value="{{$row->cont_name}}" class="form-control" autocomplete="off" ></td>
              <td width="10%"><input type="text" name="cell[]" id="cell_{{$i}}"  value="{{$row->cont_mobile}}" class="form-control input-sm" autocomplete="off" ></td>
              <td width="10%"><input type="text" name="email[]" id="email_{{$i}}" value="{{$row->cont_email}}" class="form-control input-sm"  autocomplete="off"></td>
              <td width="3%">&nbsp;</td>
            </tr>
            @endforeach
           </tbody>
        </table>
      </div>
    </div>
    </fieldset> -->

   <br/>
    <div class="row justify-content-left">
          <div class="col-sm-12 text-left">
              <button class="btn btn-sm btn-success" type="submit" ><i class="fa fa-save"></i> Update</button>
              <a href="{{route('cust.index')}}" class="btn btn-sm btn-info"><i class="fa fa-list"></i> List</a>
          </div>
      </div>
    </div>
    </div>
  </form>


  <div class="row widget-main">
    <div class="col-md-12">
      @csrf
      <table class="table table-striped table-view" id="datatable">
        <thead class="thead-blue">
          <th class="text-center" scope="col">Id</th>
          <th class="text-center" scope="col">Customer Id</th>
          <th class="text-center" scope="col">Name</th>
          <th class="text-center" scope="col">Address1</th>
          <th class="text-center" scope="col">Address2</th>
          <th class="text-center" scope="col">Mobile</th>
          <th class="text-center" scope="col">Email</th>
          <th class="text-center" >Options</th>
        </thead>
        <tbody>
          @foreach($customers as $row)
          <tr>
            <td width="5%">{{ $row->cust_slno }}</td>
            <td width="5%">AJC-000{{ $row->cust_slno }}</td>
            <td width="15%">{{ $row->cust_name }}</td>
            <td width="20%">{{ $row->cust_add1 }}</td>
            <td width="16%">{{ $row->cust_add2 }}</td>
            <td width="8%">{{ $row->cust_mobile }}</td>
            <td width="8%">{{ $row->email }}</td>
            <td aling="right " width="18%">
              <a><a href="{{ route('cust.edit', $row->id) }}" class="btn btn-xs btn-primary edit">Edit</a>
              <!--<a><a href="{{ route('cust.delv.index',$row->id) }}" class="btn btn-xs btn-warning edit">Delivery</a>-->
              <!--<a><a href="{{ url('cust-price-index/'.$row->id.'/'.$row->cust_com_id) }}" class="btn btn-xs btn-info edit">Price</a>-->
            </td>
          </tr>
          @endforeach
          </tbody>
        </table>
      </div>
  </div>
  <div class="col-md-12">
    <div class="card-tools">
        <ul class="pagination pagination-sm float-right">
          <p class="pull-right">
             {{ $customers->render("pagination::bootstrap-4") }} 
          </p>
        </ul>
      </div>
    </div>
  </div>
</section>
<script>
      $(document).ready( function () {
        console.log('datatable')
        $('#datatable').DataTable();
    });
</script>
@stop
@section('pagescript')
  <script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
  <script src="{{ asset('assets/js/chosen.jquery.min.js') }}"></script>
  <script src="{{ asset('assets/js/ace-elements.min.js') }}"></script>
  <script src="{{ asset('assets/js/ace.min.js') }}"></script>

  <script type="text/javascript">
    var company_id = $('#company_id').val();
    $('#company_id').val(company_id);
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
