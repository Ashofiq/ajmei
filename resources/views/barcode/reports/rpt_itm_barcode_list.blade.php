<!DOCTYPE>
<html>
<head>
  <style>
  body { margin: 0; font-size: 13px; font-family: "Arrial Narrow";}

  @media print {
      *{
          font-family: "Times New Roman" !important;
      }
      .header, .header-space,
      .footer, .footer-space {
      }
      .wrapper{
          margin-top: 50px;
      }
      .header {
          position: fixed;
          top: 14px;
      }
      .footer {
          position: fixed;

          bottom: 7px;
      }
      .footer p{
          margin: 0px 0px !important;
      }
      p{
          margin: 1px 0px;
          font-size: 15px;
          font-weight: 700;
          font-family: Khaled;
      }
      .wrapper p{

          font-size: 18px;
          font-weight: 700;
          font-family: Khaled;
      }
      .single p{
          font-size: 15px !important;
          margin: 0px 0px !important;
      }
      .single {
          min-height:20px;
          overflow: hidden;
      }
      .row{
          overflow: hidden;
      }
      .margin-t{
          height: 273px;
          width: 100%;
      }
  }

  table {

  }

  td {
    border-top: none;
    border: 1px solid black;
  }
  th {
      border: none;
  }

  table {
    width: 100%;
    border-collapse: collapse;
  }
  </style>
    <title>Item Ledger Report</title>

</head>
<body>
<section class="content">
<div class="container">

  <div class="row justify-content-center">
    <div class="col-md-12">
      <table class="table table-striped table-data table-view">
        <thead class="thead-dark">
            <tr>
                <td align="center" width="5%"><b>Id</b></td>
                <td align="center" width="25%"><b>Category</b></td>
                <td align="center" width="9%"><b>Item Code</b></td>
                <td align="center" width="9%"><b>Item Name</b></td>
                <td align="center" width="9%"><b>Desc</b></td>
                <td align="center" width="9%"><b>Barcode</b></td>
                <td align="center" width="25%"><b>Image</b></td>
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
            <td>
              <div class="col-sm-2">
                  <a href="#modal-import{{ $row->id }}" role="button" class="blue" data-toggle="modal"><i class="fa fa-print"></i></a>
                  {!! DNS1D::getBarcodeHTML($row->item_bar_code, "C39",1.3,44) !!}
                  <p><br>{{ $row->item_bar_code }}</p>
              </div>
            </td>

          </tr>
          @endforeach

          </tbody>
        </table>
        </div>
  </div>

</div>
</section>
</body>
</html>
