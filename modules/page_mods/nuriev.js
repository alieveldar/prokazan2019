function HidePic(id) { JsHttpRequest.query('/modules/page_mods/nuriev-dJSReq.php',{'id':id}, function(result,errors){ if(result){ $(".div"+id).fadeOut(); }},true); }