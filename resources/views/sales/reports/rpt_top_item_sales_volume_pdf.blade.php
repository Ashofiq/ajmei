<html>
    <head>
        <style>
        body { margin: 0; font-size: 12px; font-family: "Arrial Narrow";}
            /** Define the margins of your page **/
            @page {
                margin: 100px 55px;
            }

            header {
                position: fixed;
                top: -50px;
                left: 0px;
                right: 0px;
                height: 50px;
                /** Extra personal styles **/

                color: white;
                text-align: center;
                line-height: 35px;
            }

            footer {
                position: fixed;
                bottom: 10px;
                text-align: center;
            }

            table {
              width: 100%;
              border-collapse: collapse;
            }
            h1 {
              border-bottom-style: solid;
            }
        </style>
    </head>
    <body>
        <!-- Define header and footer blocks before your content -->
        <header>
          <div class="row justify-content-center">
            <div class="col-md-12">
              <table class="table" >
                <tr><td align="center"><font size="5"><b>{{$comp_name}}</b></font></td></tr>
                <tr><td align="center"><font size="4"><b>To 20 Item Sales By Volume Report</b></font></td></tr>
                <tr><td align="center"><font size="3"><b>{{ date('d-m-Y', strtotime($fromdate)) }} TO
                {{ date('d-m-Y', strtotime($todate)) }}</b></font></td></tr>
              </table>
            </div>
          </div>
        </header>

        <footer>
          <div class="row">
            <div class="col-sm-12">
              <table border="0" cellspacing="0" cellpadding="0"
              style='font-family:"Arrial Narrow", Courier, monospace; font-size:80px'>

                  <tr>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td height="40%">&nbsp;</td>
                  </tr>

              </table>
            </div>
          </div>
        </footer>
        <!-- Wrap the content of your PDF inside a main tag -->
        <main>
            <p>
              <div class="row justify-content-center">
                <div class="col-md-12">
                <table class="table table-bordered" border="1">
                <tr>
                   <td align="center"><b>SL No</b></td>
                   <td align="center"><b>Item Name</b></td>
                   <td align="center"><b>Sales Volume</b></td>
                   <td align="center"><b>Sales Qty</b></td>
                </tr>
                <tbody>
                 <?php
                    $i=1;
                    $total_qty = 0;
                    $total_volume = 0;
                 ?>
                  @foreach($rows as $row)
                  <?php
                    $total_qty += $row->inv_qty;
                    $total_volume += $row->inv_net_amt;
                  ?>
                  <tr>
                    <td align="center">{{$i++}}</td>
                    <td>{{ $row->item_name }}</td>
                    <td align="right">{{ number_format($row->inv_net_amt,2) }}</td>
                    <td align="right">{{ number_format($row->inv_qty,2) }}</td>
                  </tr>
                  @endforeach
                  <tr>
                    <td colspan="2" align="center"><b>Total</b></td>
                    <td align="right"><b>{{ number_format($total_volume,2) }}</b></td>
                    <td align="right"><b>{{ number_format($total_qty,2) }}</b></td>
                  </tr>
                  </tbody>
                </table>
              </div>
             </div>
            </p>
        </main>
    </body>
</html>
