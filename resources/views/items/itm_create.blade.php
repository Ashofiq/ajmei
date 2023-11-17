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
          <font size="3" color="blue"><b>Item Master Entry Form</b></font>
        </h6>
       <div class="widget-toolbar">
         <a href="{{route('itm.index')}}" class="blue"><i class="fa fa-list"></i> List</a>
        </div>
    </div></legend>
  </div>
  @if(Session::has('message'))
   <div class="row">
     <div class="col-md-12">
       <p class="alert alert-success"><b>{{ Session::get('message') }}</b></p>
     </div>
  </div>
 @endif
 @if ($errors->any())
      <div class="alert alert-danger">
          <ul>
              @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
              @endforeach
          </ul>
      </div>
  @endif

 <form id="itm_Form" action="{{route('itm.store')}}" method="post">
    {{ csrf_field() }}
    <div class="widget-body">
      <div class="widget-main">
        <div class="row">
          <div class="col-md-5">
            <div class="input-group-prepend">
                 <div class="input-group-text" style="min-width:80px">Category:</div>

                  &nbsp;<select name="itm_category" class="col-xs-10 col-sm-8 chosen-select" required>
                    <option value="" disabled selected>- Select Category -</option>
                    @foreach($itm_cat as $cat)
                        <option {{ old('itm_category') == $cat->id ? 'selected' : '' }} value="{{ $cat->id }}">{{ $cat->itm_cat_name }} - {{ $cat->itm_cat_origin }}</option>
                    @endforeach
                </select>
                @error('itm_category')
                <span class="text-danger">{{ $message }}</span>
                @enderror

            </div>
        </div>

         <div class="col-md-4">
              <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text" style="min-width:110px">Company:</div>
                </div>
                &nbsp;<select name="company_code" class="autocomplete" id="company_code"  style="max-width:150px" required>
                   <option value="-1" >--Select--</option>
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
                      <div class="input-group-text" style="min-width:80px">Item Name:</div>
                  </div>
                  <input type="text" name="item_name" value="{{ old('item_name') }}" class="form-control" autocomplete="off" required/>
             </div>
          </div>
          <div class="col-md-3">
               <div class="input-group ss-item-required">
                   <div class="input-group-prepend ">
                       <div class="input-group-text" style="min-width:90px">Item Code:</div>
                   </div>
                   <input type="text" name="itm_code" value="{{$itm_code}}" class="form-control" autocomplete="off" required readonly/>
               </div>
           </div>
      </div>

      <div class="row">
        <div class="col-md-4">
               <div class="input-group ss-item-required">
                   <div class="input-group-prepend ">
                       <div class="input-group-text" style="min-width:80px">Item Desc:</div>
                   </div>
                   <input type="text" name="item_desc" value="{{ old('item_desc') }}" class="form-control" autocomplete="off"/>
              </div>
           </div>
        <div class="col-md-3">
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text" style="min-width:80px">Item BarCode:</div>
                </div>
                <input type="text" name="itm_barcode"  value="{{ $itm_barcode }}" class="form-control" required readonly/>
           </div>
        </div>

      </div>
    <div class="row">
      <!-- div class="col-md-3">
           <div class="input-group">
             <div class="input-group-prepend">
                 <div class="input-group-text" style="min-width:80px">Item QRCode:</div>
             </div>
             <input type="text" name="itm_qrcode"  value="{{ $itm_qrcode }}" class="form-control" required readonly/>
            </div>
       </div -->

        <div class="col-md-3">
          <div class="input-group ss-item-required">
              <div class="input-group-prepend">
                <div class="input-group-text" style="min-width:80px">Packing:</div>
              </div>
              <input type="text" name="itm_pack" value="" class="form-control" autocomplete="off"/>
            </div>
         </div>
         <div class="col-md-2">
           <div class="input-group ss-item-required">
               <div class="input-group-prepend">
                 <div class="input-group-text" style="min-width:80px">Size:</div>
               </div>
               <input type="text" name="itm_size" value="0" class="form-control" autocomplete="off"/>
             </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-3">
            <div class="input-group ss-item-required">
                <div class="input-group-prepend">
                  <div class="input-group-text" style="min-width:80px">Unit:</div>
                </div>
                &nbsp;&nbsp;<select name="itm_unit" required>
                  @foreach($unit_list as $u)
                      <option {{ old('itm_unit') == $u->id ? 'selected' : '' }} value="{{ $u->id }}">{{ $u->vUnitName }}</option>
                  @endforeach
              </select>
              </div>
           </div>
          <div class="col-md-2">
            <div class="input-group ss-item-required">
                <div class="input-group-prepend ">
                    <div class="input-group-text" style="min-width:80px">Base Price:</div>
                </div>
                <input type="text" name="itm_price" value="" class="form-control" autocomplete="off"/>
            </div>
           </div>
           <div class="col-md-1">
             <button class="btn btn-sm btn-success" type="submit"><i class="fa fa-save"></i> Save</button>
           </div>
        </div>
    </div>
    </div>
  </form>
  
  <div class="row">
    <div class="col-12">
        @if(Session::has('message'))
           <p class="alert alert-success">{{ Session::get('message') }}</p>
        @endif
    </div>
  </div>
  <br/>
  <div class="row justify-content-center ">
      <div class="col-md-12">
      @csrf
        <table class="table table-striped table-data table-view" id="datatable">
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
              <th>Size</th>
              <th>Unit</th>
              <th>Price</th>
              <th>Options</th>
            </tr>
          </thead>
          <tbody>
            @foreach($rows as $row)
            <tr>
              <td>{{ $row->id }}</td>
              <td>{{ $row->itm_cat_name }} ( {{$row->itm_cat_origin}})</td>
              <td>{{ $row->item_code }}</td>
              <td>{{ $row->item_name }}</td>
              <td>{{ $row->item_desc }}</td>
              <td>{{ $row->item_bar_code }}</td>
              <td>{{ $row->packing_id }}</td>
              <td>{{ $row->size }}</td>
              <!-- <td style="display:none;">{{ $row->unit_id }}</td> -->
              <td>{{ $row->vUnitName }}</td>
              <td>{{ $row->base_price }}</td>
             <td>
                <form  method="post" action="{{ url('/itm/destroy/'.$row->id) }}" class="delete_form">
                  {{ csrf_field() }}
                  {{ method_field('DELETE') }}
                <div class="btn-group btn-corner">
                    <span href="#"  data-toggle="modal" class="btn btn-sm btn-success edit" title="Edit">
                        Edit</span>  
                    <button class='btn btn-danger btn-sm delete'  type="submit" onclick="return confirm('Are You Sure? Want to Delete It.');"><i class="fa fa-pencil-delete-o">Delete</i></button>
                </div>
                </form>
             </td>
            </tr>
            @endforeach

            </tbody>
          </table>

      </div>
      <div class="col-md-12">
          <div class="card-tools">
              <ul class="pagination pagination-sm float-right">
                <p class="pull-right">
                 
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
                 <span class="input-group-text">Category :</span>
               </div>
                  <select name="item_category" class="item_category" id="item_category" required>

                 </select>
             </div>
           </div>
          </div>

          <div class="row">
           <div class="col-md-8">
             <div class="input-group mb-3">
               <div class="input-group-prepend">
                 <span class="input-group-text">Unit&nbsp;:</span>
               </div>
               
                  &nbsp;&nbsp;
                  <select name="itm_unit" id="itm_unit" required>
                     @foreach($unit_list as $u)
                         <option {{ old('itm_unit') == $u->id ? 'selected' : '' }} value="{{ $u->id }}">{{ $u->vUnitName }}</option>
                     @endforeach
                 </select>
             </div>
           </div>
          </div>
         <div class="row">
            <div class="col-md-8">
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text">Size&nbsp;:</span>
                </div>
                 <input type="text" name="itm_size" id="itm_size" class="form-control" autocomplete="off"/>

              </div>
            </div>
          </div>
          <div class="row">
             <div class="col-md-8">
               <div class="input-group mb-3">
                 <div class="input-group-prepend">
                   <span class="input-group-text">Price&nbsp;:</span>
                 </div>
                  <input type="text" name="itm_price" id="itm_price" class="form-control" autocomplete="off"/>

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
        $('#itm_size').val(data[7]);
        $('#itm_unit').val(data[8]);
        $('#itm_price').val(data[9]);

        $('#editForm').attr('action','{{route("itm.update")}}');
        $('#editModal').modal('show');

        var url = "{{ route('get.itm') }}"
        $.ajax({
            type:'POST',
            url: url,
            data: {itemId : data[0]},
            success:function(data) {
              console.log(data);
                // if(data == true){
                //     Swal.fire({
                //         position: 'top-end',
                //         icon:  "success",
                //         title: "Successfully update",
                //         showConfirmButton: false,
                //         timer: 1500
                //     })
                // }
                // setTimeout(() => { 
                //     location.reload()
                // }, 1500);

                var itmCat = '';
                for (let index = 0; index < data.categories.length; index++) {
                  const element = data.categories[index];
                  itmCat += '<option value="'+ element.id+'" '+ (data.item.item_ref_cate_id == element.id ? 'selected' : '') +'>'+ element.itm_cat_name +' >> '+element.itm_cat_origin+'</option>';
                }
                $('.item_category').html(itmCat);

                console.log(itmCat);
            },
            error: function(e){
                console.log(e);
            }
        });
    });

  });

  $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>



<script>
      $(document).ready( function () {
        console.log('datatable')
        $('#datatable').DataTable({ 
          "id": [{ "orderSequence": [ "DESC" ] },],
          "oLanguage": {
            "sSearch": "Only Item Search:"
          }
         });
      });
</script>
@stop
