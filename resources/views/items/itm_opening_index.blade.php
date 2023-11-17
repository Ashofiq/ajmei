@extends('layouts.app')
@section('css')

    <link rel="stylesheet" href="{{ asset('assets/css/chosen.min.css') }}" />
@stop
@section('content')
<section class="content">
  <input type="hidden" name="menu_selection" id="menu_selection" value="MM@1" class="form-control" required>
  <div class="title">
    <legend>
    <div class="widget-header widget-header-small">
        <h6 class="widget-title smaller">
          <font size="3" color="blue"><b>Item Master List</b></font>
        </h6>
       <div class="widget-toolbar">
         <a href="{{route('itm.op.index')}}" class="blue"><i class="fa fa-list"></i> List</a>
        </div>
    </div></legend>
  </div>
<div class="container">

  <div class="row">
    <div class="col-12">
        @if(Session::has('message'))
           <p class="alert alert-success">{{ Session::get('message') }}</p>
        @endif
    </div>
  </div>
  <form action="{{route('itm.op.search')}}" method="post">
  {{ csrf_field() }}
   <div class="row">
       <div class="col-md-2">
         <a href="{{route('itm.op.create')}}" class="btn btn-success btn-sm">
                  <i class="fa fa-plus"></i>Add New</a>
      </div>

      <div class="col-md-6">
            <div class="input-group-prepend">
              <div class="input-group-text" style="min-width:110px">Item Name:</div>
              <select name="item_id" class="chosen-select" id="item_id" required>
                <option value="" disabled selected>- Select Items -</option>
                @foreach($item_list as $item)
                  <option {{ old('item_id') == $item->id ? 'selected' : '' }} value="{{ $item->id }}">{{ $item->item_name }} - {{ $item->itm_cat_origin }}-{{ $item->itm_cat_name }}-{{ $item->item_code }}</option>
                @endforeach
            </select>
            @error('item_id')
            <span class="text-danger">{{ $message }}</span>
            @enderror
           </div>
   </div>
   <div class="col-md-1">
     <button type="submit" name="submit"  class="btn btn-sm btn-info"><span class="fa fa-search">Search</span></button>
   </div>
 </div>
</form>
<br/>

<div class="row justify-content-center">
    <div class="col-md-12">
      <table class="table table-striped table-data table-view">
        <thead class="thead-dark">
          <tr>
            <th>Id</th>
            <th>Category</th>
            <th>Item Code</th>
            <th>Name</th>
            <th>Specification</th>
            <th>Unit</th>
            <th>Stock</th>
            <th>Price</th>
            <th>Amount</th>
            <th>Date</th>
            <th>Desc</th> 
            <th>Options</th>
          </tr>
        </thead>
        <tbody>
          @foreach($rows as $row)
          <tr>
            <td>{{ $row->id }}</td>
            <td>{{ $row->itm_cat_name }} ( {{ $row->itm_cat_origin}})</td>
            <td style="display:none;">{{ $row->item_ref_id }}</td>
            <td>{{ $row->item_code }}</td>
            <td>{{ $row->item_name }}:{{ $row->itm_cat_name }}</td> 
            <td>{{ $row->item_trans_spec }}</td>
            <td style="display:none;">{{ $row->unit_id }}</td>
            <td>{{ $row->vUnitName }}</td>
            <td>{{ $row->item_op_stock }}</td>
            <td>{{ $row->item_base_price }}</td>
            <td>{{ number_format($row->item_base_amount,2) }}</td>
            <td>{{ $row->item_op_dt != ''?date('d-m-Y',strtotime($row->item_op_dt)):'' }}</td>
            <td>{{ $row->item_op_desc }}</td>
            <td>
              <form  method="post" action="{{ url('/itm/op/destroy/'.$row->item_op_stock.'/'.$row->item_ref_id.'/'.$row->id) }}" class="delete_form">
                {{ csrf_field() }}
                {{ method_field('DELETE') }}
               <div class="btn-group btn-corner">
                 <span href="#"  data-toggle="modal" class="btn btn-sm btn-success edit" title="Edit">
                   Edit</span>
                   <button class='btn btn-danger btn-sm delete'  type="submit" onclick="return confirm('Are You Sure? Want to Delete It.');">Delete</button>
                </div>
               </form>
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
                 {{ $rows->render("pagination::bootstrap-4") }}
              </p>
            </ul>
          </div>
        </div>
</div>
</section>

<!--- Start Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">	<b>Edit:: Item Opening</b></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="#" method="post" id="editForm" enctype="multipart/form-data">
        {{ csrf_field() }}
         {{ method_field('POST') }}
        <input type="text" name="id" id="id" class="form-control" readonly="true" />
        <input type="hidden" name="company_code" id="company_code" value="{{$company_code}}" class="form-control" readonly="true" />
        <br/>
        <div class="modal-body">
          <div class="row">
            <input type="hidden" name="result_storage_id" id="result_storage_id" value="{{old('result_storage_id')}}" class="form-control" readonly required/>
              <div class="col-md-4">
               <div class="input-group-prepend">
                 <div class="input-group-prepend">
                     <div class="input-group-text" style="min-width:80px">Wearhouse:</div>
                 </div>
                 <select id="itm_warehouse" name="itm_warehouse" onchange="getStorageByWearId(this.value)" required>
                 <option value="" >--Select Wearhouse--</option>
                     @if ($warehouse_list->count())
                         @foreach($warehouse_list as $list)
                             <option value="{{$list->w_ref_id}}" >{{$list->w_ref_id}}-{{ $list->ware_name }}</option>
                         @endforeach
                     @endif
                 </select>
                </div>
           </div>
        </div><br/>  
          <div class="row">
             <div class="col-md-8">
               <div class="input-group mb-3">
                 <div class="input-group-prepend">
                   <span class="input-group-text">Item Code&nbsp;:</span>
                 </div>
                 <input type="text" name="itm_id" id="itm_id" class="form-control" autocomplete="off" required readonly/>
                 <input type="text" name="itm_code" id="itm_code" class="form-control" autocomplete="off" required readonly/>
               </div>
   	       </div>
          </div>

         <div class="row">
             <div class="col-md-8">
               <div class="input-group mb-3">
                 <div class="input-group-prepend">
                   <span class="input-group-text">Item Name&nbsp;:</span>
                 </div>
                 <input type="text" name="itm_name" id="itm_name" class="form-control" required readonly/>

               </div>
   	       </div>
          </div>

         <div class="row">
            <div class="col-md-8">
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text">Item Unit&nbsp;:</span>
                </div>
                 <input type="text" name="itm_unit" id="itm_unit" class="form-control" readonly/>

              </div>
            </div>
          </div>

          <div class="row">
             <div class="col-md-8">
               <div class="input-group mb-3">
                 <div class="input-group-prepend">
                   <span class="input-group-text">Date&nbsp;:</span>
                 </div>
                 <input type="text" size = "10" name="op_date" id="op_date" onclick="displayDatePicker('op_date');"  />
                 <a href="javascript:void(0);" onclick="displayDatePicker('op_date');"><img src="{{ asset('assets/images/calendar.png') }}" alt="calendar" border="0"></a></td>

               </div>
             </div>
           </div>

           <div class="row">
            <div class="col-md-8">
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text">Opening Qty&nbsp;:</span>
                </div>
                <input type="text" name="itm_op_prev_qty" id="itm_op_prev_qty" class="form-control" autocomplete="off" readonly/>

                <input type="text" name="itm_op_qty" id="itm_op_qty" class="form-control" autocomplete="off"/>
              </div>
            </div>
          </div>

          <div class="row">
           <div class="col-md-8">
             <div class="input-group mb-3">
               <div class="input-group-prepend">
                 <span class="input-group-text">Price&nbsp;:</span>
               </div>
               <input type="text" name="itm_op_price" id="itm_op_price" class="form-control" autocomplete="off"/>
             </div>
           </div>
         </div>

         <div class="row">
          <div class="col-md-8">
            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <span class="input-group-text">Description&nbsp;:</span>
              </div>
              <input type="text" name="itm_op_desc" id="itm_op_desc" class="form-control" autocomplete="off"/>
            </div>
          </div>
        </div>
       
        <!-- <div class="row">
           <div class="col-md-8">
             <div class="input-group mb-3">
               <div class="input-group-prepend">
                 <span class="input-group-text">Expiry Date&nbsp;:</span>
               </div>
               <input type="text" size = "10" name="exp_date" id="exp_date" onclick="displayDatePicker('exp_date');"  />
               <a href="javascript:void(0);" onclick="displayDatePicker('exp_date');"><img src="{{ asset('assets/images/calendar.png') }}" alt="calendar" border="0"></a></td>

             </div>
           </div>
         </div> -->
        </div>
         <div class="modal-footer">
           <button type="submit" class="btn btn-primary">Update</button>
           <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
         </div>
       </form>

    </div>
  </div>
</div>
<!--- End Edit Modal -->
@stop

@section('pagescript')
  <script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
  <script src="{{ asset('assets/js/chosen.jquery.min.js') }}"></script>
  <script src="{{ asset('assets/js/ace-elements.min.js') }}"></script>
  <script src="{{ asset('assets/js/ace.min.js') }}"></script>
  <script src="{{ asset('assets/blogic_js/sel_box_search.js') }}"></script>

  <script type="text/javascript">
    $(document).ready(function() {

      $('.edit').on('click',function() {
          $tr = $(this).closest('tr');
          var data = $tr.children("td").map(function(){
            return $(this).text();
          }).get();
          console.log(data);

          //alert('HELLO :1:'+data[1]+':2:'+ data[2]+':3:'+data[3] + ':4:'+ data[4]+':5:'+data[5] +':6:'+data[6]+':7:'+data[7]+':8:'+ data[8]+':Nine:'+data[9]+':ten:'+data[10]+':eleven:'+ data[11]+':12:'+ data[14]);
          $('#id').val(data[0]);
          $('#itm_category').val(data[1]);
          $('#itm_id').val(data[2]);
          $('#itm_code').val(data[3]);
          $('#itm_name').val(data[4]);
          $('#itm_unit').val(data[7]);
          $('#itm_op_prev_qty').val(data[8]);
          $('#itm_op_qty').val(data[9]);
          $('#itm_op_price').val(data[10]);
          $('#op_date').val(data[11]);
          $('#itm_op_desc').val(data[12]);
          //$('#itm_lot_no').val(data[11]);
          $('#exp_date').val(data[13]);
          $('#itm_warehouse').val(data[14]);
          $('#result_storage_id').val(data[15]);
          $('#editForm').attr('action','{{route("itm.op.update")}}');
          $('#editModal').modal('show');
      });

    });
    
    
      function getStorageByWearId(w_house){
        var comp_code = $('#company_code').val();
       //alert('get-storage-inf/getdetails/'+comp_code+'/'+w_house+'/getfirst');

        $.ajax({  //create an ajax request to display.php
          type: "GET",
          url: 'get-storage-inf/getdetails/'+comp_code+'/'+w_house+'/getfirst',
          success: function (data) {
            $("#result_storage_id").val(data.id)
          }
        });
    }

  </script>

@stop
