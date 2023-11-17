<div class="row">
<input type="hidden" name="menu_selection" id="menu_selection" value="MM@1" class="form-control" required>
  <div class="col-sm-6">
    <dl id="dt-list-1" class="dl-horizontal">
      <dt>Issue Date:</dt><dd>&nbsp;<?php echo $rows_m->r_cons_order_date; ?></dd>  
      <dt>Issue Invoice No:</dt><dd>&nbsp;<?php echo  $rows_m->r_cons_order_no; ?></dd> 
    </dl>
  </div>
  <div class="col-sm-6">
    <dl id="dt-list-1" class="dl-horizontal"> 
      <dt>Total Qty:</dt><dd>&nbsp;<?php echo  $rows_m->r_cons_total_qty; ?></dd>
      <dt>Total Amount:</dt><dd>&nbsp;<?php echo  $rows_m->r_cons_total_amount; ?></dd> 
    </dl>
  </div>

  <div class="row">
    <div class="col-sm-12">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>Item&nbsp;Code</th>
            <th>Item&nbsp;Name</th>
            <th>Item&nbsp;Specification</th>
            <th>Remarks</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Total</th>  
          </tr>
        </thead>
        <tbody>
            <?php foreach($rows_d as $p_details) {  
               $issue_amount = $p_details->r_cons_item_qty*$p_details->r_cons_item_price;
            ?>
            <tr>
              <td><?php echo  $p_details->item_code; ?></td>
              <td><?php echo  $p_details->item_name; ?>(<?php echo  $p_details->itm_cat_name; ?>)</td>
              <td><?php echo  $p_details->r_cons_item_desc; ?></td>
              <td><?php echo  $p_details->r_cons_item_remarks; ?></td>
              <td align="right"><?php echo  number_format($p_details->r_cons_item_price, 2) ?></td>
              <td align="right"><?php echo  $p_details->r_cons_item_qty; ?></td>
              <td align="right"><?php echo  number_format($issue_amount, 2) ?></td> 
            </tr>
         <?php } ?>
        </tbody>
      </table>
    </div>
  </div>

</div>