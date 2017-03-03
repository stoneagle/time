
function resetForm(form_name) {
    $(':input','#' + form_name) 
    .not(':button, :submit, :reset') 
    .val('') 
    .removeAttr('checked')
    .removeAttr('selected');
}
