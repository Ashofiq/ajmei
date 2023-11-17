@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/blogic_css/acc_tb.css') }}" />
@stop
@section('content')
<section class="content">
<input type="hidden" name="menu_selection" id="menu_selection" value="ACC@1" class="form-control" required>
<div class="title"><legend>Customers Information</legend></div>
<div class="container">

  <div class="row">
    <div class="col-12">
        @if(Session::has('message'))
           <p class="alert alert-success">{{ Session::get('message') }}</p>
        @endif

        @if(Session::has('check'))
          <script>
            Swal.fire({
              position: 'top-end',
              icon: 'error',
              title: "{{ Session::get('check') }}",
              showConfirmButton: false,
              timer: 3000
            })
          </script>
        @endif
    </div>
  </div>

<div class="container">
 <div class="panel panel-success">

<form action="{{route('chartofacc.acchead.child.store2')}}" method="POST">
{{ csrf_field() }}
  <div class="row justify-content-center ">
    <div class="col-md-10">
      <div class="container-fluid">
        <table  id="dynamic_field"  class="table table-striped table-report table-sm">
            <tr>
              <!-- <td>
                <b>Customers Information</b>
              </td> -->
              <!-- <td style="width: 4px"><button type="button" name="goBack" id="goBack" class="btn btn-success">&nbsp;Back</button></td> -->
            </tr>
           <tr> 
                <input type="hidden" name="parent_id"  value="{{$parent_id}}"  />
                <input type="hidden" name="acc_code"  value="{{$chartofdata->acc_code}}"  />
                <input type="hidden" name="acc_level"  value="{{$chartofdata->acc_level}}"  />
                <input type="hidden" name="acc_head"  value="{{$chartofdata->acc_head}}"  />
                <input type="hidden" name="company_code" id="company_code" value="{{$chartofdata->company_id}}"  />

                <td><input type="text" name="name[]" placeholder="Enter Account Head" class="form-control name_list" /></td>
                <td style="width: 4px"><button type="button" name="add" id="add" class="btn btn-success">&nbsp;Add More</button></td>
            </tr>
        </table><br/>
        <input type="submit" name="submit" id="submit" class="btn btn-info" value="Save" />
    </div>
  </div></div>
  </form>

  <form action="{{route('chartofacc.acchead.child.search')}}" method="POST">
  {{ csrf_field() }}
  {{ method_field('PATCH') }}
  <input type="hidden" name="s_parent_id"  value="{{$parent_id}}"  />
  <input type="hidden" name="company_code" id="company_code" value="{{$chartofdata->company_id}}"  />

  <div class="row justify-content-center">
    <div class="col-md-4">
       <div class="input-group mb-2">
         <div class="input-group-prepend">
           <span class="input-group-text">Account Name&nbsp;:</span>
         </div>
         <input type="text" id="acc_Ledger" name="acc_Ledger" value="{{request()->get('acc_Ledger')??''}}" class="form-control" onkeyup="autocompleteAccHead()">
         <input type="hidden" name="ledger_id" id="ledger_id" value="{{request()->get('ledger_id')}}">
        </div>
    </div>
    <div class="col-md-2">
      <button type="submit" name="acc_ch_search" id="acc_ch_search" class="btn btn-success">&nbsp;Search</button>
    </div>
  </div>
  </form>
  <div class="row justify-content-center">
       <div class="col-md-10">
         <div class="card-body table-responsive p-0">
         @csrf
        <table class="table table-striped table-report table-sm">
          <thead class="thead-dark">
                 <tr>
                   <th style="" class="text-center" scope="col">Sys.ID</th>
                   <th style="display:none;" class="text-center" scope="col">Parent Id</th>
                   <th style="display:none;" style="width: 8px" class="text-center" scope="col">Comapny Code</th>
                   <th style="display:none;" style="width: 8px" class="text-center" scope="col">Comapny</th>
                   <th style="display:none;" style="width: 10px" class="text-center" scope="col">Account Code</th>
                   <th class="text-center" scope="col">Account Head</th>
                   <th style="width: 4px" class="text-center" scope="col">Account Level</th>
                   <th style="width: 4px" class="text-center" scope="col">File Level</th>
                   <th style="width: 4px" class="text-center" scope="col">Action</th>
                 </tr>
               </thead>
               <tbody>
                 <?php $i = 1; ?>
                 @foreach($chartofaccounts as $d)
                <tr>
                  <td style="">AJS{{ $d->customerId }}</td>
                  <td style="display:none;">{{$parent_id}}</td>
                  <td style="display:none;">{{$chartofdata->company_id}}</td>
                  <td style="display:none;">{{ $d->name }}</td>
                  <td style="display:none;">{{ $d->acc_code }}</td>
                  <td>{{ $d->acc_head }}</td>
                  <td class="text-center">{{ $d->acc_level }}</td>
                  <td class="text-center">{{ $d->file_level }}</td>
                  <td style="display:none;" class="text-center">{{ $d->is_cash_sheet }}</td>

                  <td>
                    <form  method="post" action="{{ url('/chartofacc/child/destroy2') }}" class="delete_form">
                      {{ csrf_field() }}
                    <input type="hidden" name="id"  value="{{$d->id}}"  />
                    <input type="hidden" name="s_parent_id"  value="{{$parent_id}}"  />
                    <input type="hidden" name="company_code" id="company_code" value="{{$chartofdata->company_id}}"  />

                    <div class="btn-group btn-corner">
                        <span href="#"  data-toggle="modal" class="btn btn-sm btn-success edit" title="Edit">
                            Edit
                        </span>

                        <a target="_blank" href="{{ route('cust.edit', $d->customer_id) }}" class="btn btn-sm btn-success">
                            Customer Details
                        </a>


                         <!-- <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are You Sure? Want to Delete It.');"
                                title="Delete">Delete
                         </button> -->
                    </div>
                     </form>
                  </td>
                </tr>
                <?php $i++; ?>
                @endforeach
               </tbody>
          </table>
        </div>
        <div class="card-tools">
          <ul class="pagination pagination-sm float-right">
            <p class="pull-right">
               {{ $chartofaccounts->render("pagination::bootstrap-4") }} 
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
        <h5 class="modal-title" id="exampleModalLabel">Edit::Chart Of Account Main Head</h5>
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
                    <input type="text" name="company_code" id="company_code1" readonly required>
                    <input type="text" name="company_name" id="company_name" class="form-control" readonly required>
               </div>
             </div>
          </div>

          <div class="row">
             <div class="col-md-8">
               <div class="input-group mb-3">
                 <div class="input-group-prepend">
                   <span class="input-group-text">Account Code&nbsp;:</span>
                 </div>
                  <input type="text" name="acc_code" id="acc_code" class="form-control" readonly required>
               </div>
   	         </div>
          </div>

          <div class="row">
              <div class="col-md-8">
                <div class="input-group mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text">Account Head&nbsp;:</span>
                  </div>
                  <input type="text" name="acc_head" id="acc_head" class="form-control" required>
                </div>
    	       </div>
           </div>

           <div class="row">
               <div class="col-md-8">
                 <div class="input-group mb-3">
                   <div class="input-group-prepend">
                     <span class="input-group-text">File Level&nbsp;:</span>
                   </div>
                   <input type="text" name="file_level" id="file_level" class="form-control" required>
                 </div>
     	       </div>
          </div>
        
        <div class="row">
              <div class="col-md-8">
                <div class="input-group mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text">Cash&nbsp;Statement:</span>
                  </div>
                  &nbsp;&nbsp;<input type="checkbox" name="cash_stm_level" id="cash_stm_level">
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
<script src="{{ asset('assets/js/ace-elements.min.js') }}"></script>
<script src="{{ asset('assets/js/ace.min.js') }}"></script>
<script>
  function autocompleteAccHead(){
    compcode = $('#company_code').val()
      $('#acc_Ledger').autocomplete({
        source: function(req, res){
          $.ajax({
            url: "/report/get-ledger-head",
            dataType: "json",
            data:{'item':encodeURIComponent(req.term),
                'compcode':encodeURIComponent(compcode) },

            error: function (request, error) {
                 console.log(arguments);
                 alert(" Can't do because: " +  console.log(arguments));
            },

            success: function (data) {
              res($.map(data.data, function (item) {
              //alert('IQII:'+item.acc_head)
              return {
                  label: item.acc_head,
                  value: item.acc_head,
                  acc_id: item.id,
                };
              }));
            }
          });
        },
        autoFocus:true,
        select: function(event, ui){
          //alert(ui.item.acc_id)
          $('#ledger_id').val(ui.item.acc_id)
        }
      })
  }

</script>

<script type="text/javascript">
    $(document).ready(function(){
      autodata();
      var postURL = "<?php echo url('addProduct'); ?>";
      var i = 1;
      function autodata(){
        i++;
        for (var i = 1; i < 3; i++) {
          $('#dynamic_field').append('<tr id="row'+i+'" class="dynamic-added"><td><input type="text" name="name[]" placeholder="Enter Account Head" class="form-control name_list" /></td><td><button type="button" name="remove" id="'+i+'" class="btn btn-danger btn_remove">X</button></td></tr>');
        }
      }
      $('#add').click(function(){
           i++;
           $('#dynamic_field').append('<tr id="row'+i+'" class="dynamic-added"><td><input type="text" name="name[]" placeholder="Enter Account Head" class="form-control name_list" /></td><td><button type="button" name="remove" id="'+i+'" class="btn btn-danger btn_remove">X</button></td></tr>');
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

          //alert('HELLO :1:'+data[1]+':2:'+ data[2]+':3:'+data[3] + ':4:'+ data[4]);
          $('#id').val(data[0]);
          $('#parent_id').val(data[1]);
          $('#company_code1').val(data[2]);
          $('#company_name').val(data[3]);
          $('#acc_code').val(data[4]);
          $('#acc_head').val(data[5]);
          $('#acc_level').val(data[6]);
          $('#file_level').val(data[7]);
          if(data[8] == 1) $("#cash_stm_level").prop("checked", true);
          $('#editForm').attr('action','{{route("chartofacc.acchead.child.update2")}}');
          $('#editModal').modal('show');
      });

      $('#goBack').click(function(){
        window.history.back();
      });

    });
</script>

@stop
