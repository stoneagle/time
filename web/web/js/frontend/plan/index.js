$(function(){
    $("[name='delete_one']").on('click', function (e) {
        e.preventDefault();
        href = $(this).attr("href");
        var post_data = {
            'ids' : $(this).attr("model_id")
        };
        var text_info = "是否删除该计划";
        var hint = "该计划删除后，关联的计划隶属会丢失";
        checkPost(text_info, hint, href, post_data);
    });
});
