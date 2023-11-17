@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.min.css') }}" />
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
         <a href="{{route('itm.index')}}" class="blue"><i class="fa fa-list"></i> List</a>
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
  <form action="{{route('itm.search')}}" method="post">
  {{ csrf_field() }}
   <div class="row">
       <div class="col-md-2">
         <a href="{{route('itm.create')}}"  class="btn btn-success btn-sm">
                  <i class="fa fa-plus"></i>Add New</a>
      </div>

      <div class="col-md-6">
            <div class="input-group-prepend">
              <div class="input-group-text" style="min-width:110px">Item Name:</div>
              <select name="item_id" class="chosen-select" id="item_id" required>
                <option value="" disabled selected>- Select Items -</option>
                @foreach($item_list as $item)
                    <option {{ old('item_id') == $item->id ? 'selected' : '' }} value="{{ $item->id }}">{{ $item->item_name }} - {{ $item->itm_cat_name }}-{{ $item->item_code }}</option>
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
            <th>Desc</th>
            <th>Barcode</th>
            <!-- th>QR Code</th -->
            <th>Packing</th>
            <th>Unit</th>
            <th>Options</th> 
          </tr>
        </thead>
        <tbody>
          @foreach($rows as $row)
          <tr>
            <td>{{ $row->id }}</td>
            <td>{{ $row->itm_cat_name }} ( {{ $row->itm_cat_origin}})</td>
            <td>{{ $row->item_code }}</td>
            <td>{{ $row->item_name }}</td>
            <td>{{ $row->item_desc }}</td>
            <td>{{ $row->item_bar_code }}</td>
            <!-- td>{{ $row->item_qr_code }}</td -->
            <td>{{ $row->packing_id }}</td>
            <td style="display:none;">{{ $row->unit_id }}</td>
            <td>{{ $row->vUnitName }}</td>
            <td>
              <form  method="post" action="{{ url('/itm/destroy/'.$row->id) }}" class="delete_form">
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
        <h5 class="modal-title" id="exampleModalLabel">	<b>Edit:: Item Master</b></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="#" method="post" id="editForm" enctype="multipart/form-data">
        {{ csrf_field() }}
         {{ method_field('POST') }}
        <input type="text" name="id" id="id" class="form-control" readonly="true" />
        <br/>
        <div class="modal-body">

          <div class="row">
             <div class="col-md-8">
               <div class="input-group mb-3">
                 <div class="input-group-prepend">
                   <span class="input-group-text">Item Code&nbsp;:</span>
                 </div>
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
                 <input type="text" name="itm_name" id="itm_name" class="form-control" required/>

               </div>
   	       </div>
          </div>

         <div class="row">
            <div class="col-md-8">
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text">Item Desc&nbsp;:</span>
                </div>
                 <input type="text" name="itm_desc" id="itm_desc" class="form-control"/>

              </div>
            </div>
          </div>

          <div class="row">
             <div class="col-md-8">
               <div class="input-group mb-3">
                 <div class="input-group-prepend">
                   <span class="input-group-text">Packing&nbsp;:</span>
                 </div>
                  <input type="text" name="itm_pack" id="itm_pack" class="form-control" autocomplete="off"/>

               </div>
             </div>
           </div>

          <div class="row">
           <div class="col-md-8">
             <div class="input-group mb-3">
               <div class="input-group-prepend">
                 <span class="input-group-text">Unit&nbsp;:</span>
               </div>
                  &nbsp;&nbsp;<select name="itm_unit" id="itm_unit" required>
                     @foreach($unit_list as $u)
                         <option {{ old('itm_unit') == $u->id ? 'selected' : '' }} value="{{ $u->id }}">{{ $u->vUnitName }}</option>
                     @endforeach
                 </select>
             </div>
           </div>
         </div>
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
          $('#itm_code').val(data[2]);
          $('#itm_name').val(data[3]);
          $('#itm_desc').val(data[4]);
          $('#itm_pack').val(data[6]);
          $('#itm_unit').val(data[7]);

          $('#editForm').attr('action','{{route("itm.update")}}');
          $('#editModal').modal('show');
      });

    });
  </script>

@stop
