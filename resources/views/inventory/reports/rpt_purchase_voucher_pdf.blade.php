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
                top: -45px;
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
            <div class="col-sm-12">
              <table class="table">
                <tr><td width="25%"></td>
                <td valign="middle" width="65%"><b><font size="7"></font></b><br/>
                  <!-- Phone : 01313-772676<br/>
                  Email: jistlifecare@gmail.com --></td>
                <td valign="middle" width="10%"><h1>Purchase</h1></td></tr>
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

                <table class="table table-bordered" border="0">
                  <tr>
                      <td width="15%"><b>Purchase Date:</b></td><td>{{ date('d-m-Y', strtotime($rows_m->pur_order_date)) }}</td>
                      <td width="15%"><b>PO No:</b></td><td>{{ $rows_m->pur_order_no }}</td>
                  </tr>
                  <tr>
                    <td width="15%"><b>Supplier Name:</b></td><td>{{ $rows_m->supp_name }}</td>
                    <td width="15%"><b>PI No:</b></td><td>{{ $rows_m->pur_pi_no }}</td>
                  </tr>
                  <tr>
                    <td width="15%"><b>Supplier Phone:</b></td><td colspan="3">{{ $rows_m->supp_mobile }}</td>
                  </tr>
                  <tr>
                    <td width="15%"><b>Address:</b></td><td colspan="3">{{ $rows_m->supp_add1 }} {{ $rows_m->supp_add2 }}</td>
                  </tr>

                </table><br/>

                <table class="table table-bordered" border="1">
                 <tr>
                   <td align="center"><b>SL</b></td>
                   <td align="center"><b>Warehouse</b></td>
                   <td align="center"><b>Item&nbsp;Name</b></td>
                   <td align="center"><b>Remarks</b></td>
                   <td align="center"><b>LOT&nbsp;No</b></td>
                   <td align="center"><b>Qty</b></td>
                   <td align="center"><b>Price</b></td>
                   <td align="center"><b>Total</b></td>
                </tr>
                  <?php $i=0; ?>
                  @foreach($rows_d as $p_details)
                  <?php
                    $pur_amount = $p_details->pur_item_qty*$p_details->pur_item_price;
                  ?>
                  <tr>
                    <td align="center">{{ $i += 1 }}</td>
                    <td align="left">{{ $p_details->ware_name }}</td>
                    <td>{{ $p_details->item_name }}({{ $p_details->itm_cat_name }})</td>
                    <td>{{ $p_details->pur_item_remarks }}</td>
                    <td align="center">{{ $p_details->pur_lot_no }}</td>
                    <td align="right">{{ $p_details->pur_item_qty }}</td>
                    <td align="right">{{ number_format($p_details->pur_item_price, 2) }}</td>
                    <td align="right">{{ number_format($pur_amount, 2) }}</td>
                  </tr>
                  @endforeach
                  <tr>
                    <td align="center" colspan="5">&nbsp;</td>
                    <td align="right"><b>{{ $rows_m->pur_total_qty }}</b></td>
                    <td align="right">&nbsp;</td>
                    <td align="right"><b>{{ number_format($rows_m->pur_total_amount, 2) }}</b></td>
                  </tr>
                </table>
              </div>
             </div>
            </p>
        </main>
    </body>
</html>
