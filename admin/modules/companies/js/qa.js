function ItemDelete(id, pg) { caption="Подтвердите удаление"; text='Удалить запись?<br>Данное действие будет невозможно отменить.'+"<div class='C25'></div><div class='LinkG' style='float:left; margin-right:5px;'><a href='javascript:void(0);' onclick='ActionAndUpdate("+id+", \"DEL\", "+pg+");'>Удалить</a></div><div class='LinkR'><a href='javascript:void(0);' onclick='CloseBlank();'>Отмена</a></div><div class='C10'></div>"; ViewBlank(caption, text); } 

function ActionAndUpdate(id, act, tab) { $("#Line"+id+" .delete").html(loader); JsHttpRequest.query('modules/companies/qa-JSReq.php',{'id':id,'act':act,'tab':tab},
function(result,errors){ if(result){  /**/ if (act=="DEL"){ $("#Line"+id).remove(); } /**/ }},true); }