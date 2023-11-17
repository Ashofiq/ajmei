@extends('layouts.app')
@section('content')
<section class="content">
<input type="hidden" name="menu_selection" id="menu_selection" value="MM@1" class="form-control" required>
<div class="title"><legend>Item Category Sub Head</legend></div>
<div class="container">

  <div class="row">
    <div class="col-12">
        @if(Session::has('message'))
           <p class="alert alert-success">{{ Session::get('message') }}</p>
        @endif
    </div>
  </div>

<div class="container">
 <div class="panel panel-success">

<form action="{{route('itm.cat.child.store')}}" method="POST">
{{ csrf_field() }}
  <div class="row justify-content-center ">
    <div class="col-md-10">
      <div class="container-fluid">
        <table  id="dynamic_field"  class="table table-striped table-report table-sm">
            <tr>
              <td>
                <b>{{$data->itm_cat_code}} ::: {{$origin}}</b>
              </td>
              <td style="width: 4px"><button type="button" name="goBack" id="goBack" class="btn btn-success">&nbsp;Back</button></td>
            </tr>
            @if(Auth::user()->id == 2)
           <tr>
                <input type="hidden" name="parent_id"  value="{{$parent_id}}"  />
                <input type="hidden" name="itm_cat_code"  value="{{$data->itm_cat_code}}"  />
                <input type="hidden" name="itm_cat_level"  value="{{$data->itm_cat_level}}"  />
                <input type="hidden" name="itm_cat_name"  value="{{$data->itm_cat_name}}"  />
                <input type="hidden" name="company_code"  value="{{$data->itm_comp_id}}"  />

                <td><input type="text" name="name[]" placeholder="Enter Sub Category" class="form-control name_list" /></td>
                <td style="width: 4px"><button type="button" name="add" id="add" class="btn btn-success">&nbsp;Add More</button></td>
            </tr>
          @endif
        </table><br/>
        @if(Auth::user()->id == 2)
        <input type="submit" name="submit" id="submit" class="btn btn-info" value="Save" />
        @endif
    </div>
  </div></div>
  </form><br/>

  <div class="row justify-content-center">
       <div class="col-md-10">
         <div class="card-body table-responsive p-0">
         @csrf
        <table class="table table-striped table-report table-sm">
          <thead class="thead-dark">
                 <tr>
                   <th style="display:none;" class="text-center" scope="col">Sys.ID</th>
                   <th style="display:none;" class="text-center" scope="col">Parent Id</th>
                   <th style="display:none;" style="width: 8px" class="text-center" scope="col">Comapny</th>
                   <th style="display:none;" style="width: 10px" class="text-center" scope="col">Item Cate Code</th>
                   <th style="width: 45px" class="text-center" scope="col">Sub Category</th>
                   <th style="width: 10px" class="text-center" scope="col">Category Level</th>
                   <th style="width: 8px" class="text-center" scope="col">Action</th>
                 </tr>
               </thead>
               <tbody>
                 @foreach($data1 as $d)
                <tr>
                  <td style=display:none;>{{ $d->id }}</td>
                  <td style=display:none;>{{$parent_id}}</td>
                  <td style=display:none;>{{$d->itm_comp_id}}</td>
                  <td style=display:none;>{{$d->name}}</td>
                  <td style="display:none;">{{ $d->itm_cat_code }}</td>
                  <td><a href="{{ route('itm.cat.mkchild',$d->id) }}">{{ $d->itm_cat_name }}</a></td>
                  <td class="text-center">{{ $d->itm_cat_origin }}</td>
                  <td>
                  @if(Auth::user()->id == 2)
                    <div class="btn-group btn-corner">
                        <span href="#"  data-toggle="modal" class="btn btn-sm btn-success edit" title="Edit">
                            <i class="fa fa-pencil-square-o">Edit</i>
                        </span> &nbsp;&nbsp;
                        <form  method="post" action="{{ url('/itm-cat-child/destroy',$d->id) }}" class="delete_form">
                          {{ csrf_field() }}
                          {{ method_field('DELETE') }}
                         <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are You Sure? Want to Delete It.');"
                                title="Delete">
                            <i class="fa fa-trash">Delete</i>
                         </button>
                         </form>
                    </div>
                  @endif
                  </td>
                </tr>
                @endforeach
               </tbody>
          </table>
        </div>
        <div class="card-tools">
          <ul class="pagination pagination-sm float-right">
            <p class="pull-right">
               {{ $data1->render("pagination::bootstrap-4") }} 
            </p>
          </ul>
        </div>
      </div>
  </div>

</div>
</section>

<!--- Start Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Edit::Category Sub Head</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="#" method="post" id="editForm" enctype="multipart/form-data">
        {{ csrf_field() }}
         {{ method_field('POST') }}
        <input type="text" name="id" id="id" class="form-control" readonly="true" />
          <input type="hidden" name="parent_id" id="parent_id" class="form-control" readonly>
        <br/>
        <div class="modal-body">

          <div class="row">
             <div class="col-md-8">
               <div class="input-group mb-3">
                 <div class="input-group-prepend">
                   <span class="input-group-text">Company Code&nbsp;:</span>
                 </div>
                  <input type="hidden" name="company_code" id="company_code" class="form-control" readonly required>
                  <input type="text" name="company_name" id="company_name" class="form-control" readonly required>
                </div>
             </div>
          </div>

          <div class="row">
             <div class="col-md-8">
               <div class="input-group mb-3">
                 <div class="input-group-prepend">
                   <span class="input-group-text">Category Code&nbsp;:</span>
                 </div>
                  <input type="text" name="itm_cat_code" id="itm_cat_code" class="form-control" readonly required>
               </div>
   	         </div>
          </div>

          <div class="row">
              <div class="col-md-8">
                <div class="input-group mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text">Sub Category&nbsp;:</span>
                  </div>
                  <input type="text" name="itm_cat_name" id="itm_cat_name" class="form-control" required>
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

<script type="text/javascript">
    $(document).ready(function(){
      autodata();
      var postURL = "<?php echo url('addProduct'); ?>";
      var i = 1;
      function autodata(){
        i++;
        for (var i = 1; i < 3; i++) {
          // $('#dynamic_field').append('<tr id="row'+i+'" class="dynamic-added"><td><input type="text" name="name[]" placeholder="Enter Sub Category" class="form-control name_list" /></td><td><button type="button" name="remove" id="'+i+'" class="btn btn-danger btn_remove">X</button></td></tr>');
        }
      }
      $('#add').click(function(){
           i++;
           $('#dynamic_field').append('<tr id="row'+i+'" class="dynamic-added"><td><input type="text" name="name[]" placeholder="Enter Sub Category" class="form-control name_list" /></td><td><button type="button" name="remove" id="'+i+'" class="btn btn-danger btn_remove">X</button></td></tr>');
      });
      $(document).on('click', '.btn_remove', function(){
           var button_id = $(this).attr("id");
           $('#row'+button_id+'').remove();
      });

      $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });

      $('#submit').click(function(){
           $.ajax({
                url:postURL,
                method:"POST",
                data:$('#product_name').serialize(),
                type:'json',
                success:function(data)
                {
                    if(data.error){
                        previewMessage(data.error);
                    }else{
                        i=1;
                        $('.dynamic-added').remove();
                        $('#product_name')[0].reset();
                        $(".print-success-msg").find("ul").html('');
                        $(".print-success-msg").css('display','block');
                        $(".error-message-display").css('display','none');
                        $(".print-success-msg").find("ul").append('<li>Record Inserted Successfully.</li>');
                    }
                }
           });
      });

      function previewMessage (msg) {
         $(".error-message-display").find("ul").html('');
         $(".error-message-display").css('display','block');
         $(".print-success-msg").css('display','none');
         $.each( msg, function( key, value ) {
            $(".error-message-display").find("ul").append('<li>'+value+'</li>');
         });
      }

      $('.edit').on('click',function() {
          $tr = $(this).closest('tr');
          var data = $tr.children("td").map(function(){
            return $(this).text();
          }).get();
          console.log(data);

          //alert('HELLO :1:'+data[1]+':2:'+ data[2]+':3:'+data[3] + ':4:'+ data[4]+':5:'+data[5] +':6:'+data[6]+':7:'+data[7]+':8:'+ data[8]+':Nine:'+data[9]+':ten:'+data[10]+':eleven:'+ data[11]+':12:'+ data[14]);
          $('#id').val(data[0]);
          $('#parent_id').val(data[1]);
          $('#company_code').val(data[2]);
          $('#company_name').val(data[3]);
          $('#itm_cat_code').val(data[4]);
          $('#itm_cat_name').val(data[5]);

          $('#editForm').attr('action','{{route("itm.cat.child.update")}}');
          $('#editModal').modal('show');
      });

      $('#goBack').click(function(){
        window.history.back();
      });

    });
</script>

@stop
