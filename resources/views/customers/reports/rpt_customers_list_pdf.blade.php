<!DOCTYPE>
<html>
<head>
  <style>
  body { margin: 0; font-size: 11px; font-family: "Arrial Narrow";}

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
    <title>Control Wise Subsidiary Ledger Balance</title>

</head>
<body>
<section class="content">
<div class="container">

  <div class="row justify-content-center">
    <div class="col-md-12">
      <table>
        <thead>
            <tr><th class="text-center" colspan="5"><font size="6"><b>{{$comp_name}}</b><font></th></tr>
            <tr><th align="center" colspan="5"><font size="4"><b>Customer List</b><font></th></tr>
            <tr><th align="center" colspan="5"><font size="2"><b>Date:{{date('d-m-Y')}}</b><font></th></tr>
        </thead>
        <thead>
          <tr>
            <td align="right" colspan="5"><b>Total: {{$rows->count()}}</b></td> 
          </tr>
          <tr>
            <!-- td align="center"><b>SL No</b></td -->
            <td align="center"><b>Code</b></td>
            <td align="center"><b>Name</b></td>
            <td align="center"><b>Address</b></td>
            <td align="center"><b>Phone</b></td>
          </tr>
        </thead>
        <tbody>
         <?php
          $i = 0;
         ?>
          @foreach($rows as $row)
          <tr>
             <!-- td align="center">{{ $i += 1 }}</td -->
             <td align="center">{{ $row->cust_slno }}</td>
             <td align="left">{{ $row->cust_name }}</td>
             <td align="left">{{ $row->cust_add1 }} {{ $row->cust_add2 }}</td>
             <td align="left">{{ $row->cust_mobile }} {{ $row->cust_phone }}</td>
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
