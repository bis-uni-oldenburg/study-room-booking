<style type="text/css" media="print">
#print-button { display: none; }
</style>
<div id="print-receipt" style="font-family: Arial, Helvetica, sans-serif">
  <h1 style="font-size: 32px">%~reservation_receipt% - %~institution%</h1>
  <p style="font-size: 26px">%~users%: <strong>%login_id1%, %login_id2%</strong> 
  <br />%~work_room%: %~room% %raum%
     <br />%~date%: %datum%
     <br />%~day_time%: %von% &ndash; %bis% %~time_uhr%</p>
     <br>
     <input type="submit" value="Drucken" id="print-button" onclick="window.print()">
</div>