
function resetForm(form_name) {
    $(':input','#' + form_name) 
    .not(':button, :submit, :reset, :hidden') 
    .val('') 
    .removeAttr('checked') 
    .removeAttr('selected');
}
