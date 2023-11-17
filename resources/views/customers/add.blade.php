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
      <font size="2" color="blue"><b>Add Customer</b></font>
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
<form id="acc_Form" action="{{route('chartofacc.acchead.child.store2') }}" method="post">
 {{ csrf_field() }}

  <div class="widget-body">
      <div class="widget-main">
         <div class="row">
          <!-- <div class="col-md-6">
               <div class="input-group">
                 <div class="input-group-prepend">
                     <div class="input-group-text" style="min-width:130px">Company:</div>
                 </div>
                  <input type="hidden" name="company_id"  id="company_id"  value="" class="form-control input-sm" readonly />
                  <input type="text" name="company_name"  id="company_name"  value="" class="form-control input-sm" readonly />
                </div>
           </div> -->
         </div>

        <div class="row">
            <div class="col-md-6">
               <div class="input-group ss-item-required">
                   <div class="input-group-prepend ">
                       <div class="input-group-text" style="min-width:130px">Customer Name:</div>
                       <input type="text" name="customer_name"  id="customer_name" class="form-control input-sm" required />
                   </div>
              </div>
           </div>

         </div>

         <div class="row">
           <div class="col-md-4">
               <div class="input-group">
                   <div class="input-group-prepend">
                       <div class="input-group-text" style="min-width:130px">Contract Person:</div>
                   </div>
                   <input type="text" name="contract_person" value="" class="form-control"  />
              </div>
           </div>
        </div>

        <div class="row">
           <div class="col-md-4">
               <div class="input-group">
                   <div class="input-group-prepend">
                       <div class="input-group-text" style="min-width:130px">Official Mobile No:</div>
                   </div>
                   <input type="text" name="mobileno" value="" class="form-control"  />
              </div>
           </div>
        </div>

        <div class="row">
           <div class="col-md-4">
               <div class="input-group">
                   <div class="input-group-prepend">
                      <div class="input-group-text" style="min-width:130px">Personal Mobile No:</div>
                   </div>
                   <input type="text" name="personalMobileno" value="" class="form-control"  />
              </div>
           </div>
        </div>

        <div class="row">
           <div class="col-md-4">
               <div class="input-group">
                   <div class="input-group-prepend">
                      <div class="input-group-text" style="min-width:130px">Email:</div>
                   </div>
                   <input type="text" name="email" value="" class="form-control"  />
              </div>
           </div>
        </div>

        <div class="row">
           <div class="col-md-4">
               <div class="input-group">
                  <div class="input-group-prepend">
                    <div class="input-group-text" style="min-width:130px">Select Head:</div>
                  </div>
                  <select class="selectpicker" name="chartofacc" required>
                    <option value="61">SUNDRY DEBTORS ( SALES )</option>
                    <option value="3008">AJMERI GROUP</option>
                    
                  </select>
                </div>
           </div>
        </div>


        <div class="row">
             <div class="col-md-7">
                  <div class="input-group ss-item-required">
                      <div class="input-group-prepend">
                          <div class="input-group-text" style="min-width:130px">Office Address:</div>
                      </div>
                      <textarea name="address1" rows="2" cols="100" class="form-control config" placeholder="Narration" maxlength="500" ></textarea>
                  </div>
              </div>
            
          </div>
          <div class="row">
               <div class="col-md-7">
                    <div class="input-group ss-item-required">
                        <div class="input-group-prepend">
                            <div class="input-group-text" style="min-width:130px">Delivery Address:</div>
                        </div>
                        <textarea name="address2" rows="2" cols="100" class="form-control config" placeholder="Narration" maxlength="500"></textarea>
                    </div>
                </div>
          </div>
       
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


  <div class="row widget-main">
    <div class="col-md-12">
      @csrf
      <table class="table table-striped table-view" id="datatable">
        <thead class="thead-blue">
            <tr>
              <th class="text-center" scope="col">Id</th>
              <th class="text-center" scope="col">Customer Id</th>
              <th class="text-center" scope="col">Name</th>
              <th class="text-center" scope="col">Address1</th>
              <th class="text-center" scope="col">Address2</th>
              <th class="text-center" scope="col">Mobile</th>
              <th class="text-center" scope="col">Email</th>
              <th class="text-center">Options</th>
            </tr>
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
