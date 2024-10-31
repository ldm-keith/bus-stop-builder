/* [LDM] not needed * /
jQuery(document).ready(function($) {

  try{
  $.each(bsb_info_boxes_meta, function (key, val) {
   $("."+key).html(val.toString());
   console.log(key + ":" + val.toString() +"\n");
  });
  }catch(e){}

}); */
