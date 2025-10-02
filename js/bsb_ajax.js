/* [LDM] not needed * /
jQuery(document).ready(function($) {

  try{ //This maybe would work if we used .on('event' .... for future added or deleted elements 
  $.each(bsb_info_boxes_meta, function (key, val) {
   $("."+key).html(val.toString());
   console.log(key + ":" + val.toString() +"\n");
  });
  }catch(e){}

}); */
