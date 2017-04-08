$(function(){
    $("[name='delete_one']").on('click', function (e) {
        e.preventDefault();
        href = $(this).attr("href");
        var post_data = {
            'ids' : $(this).attr("model_id")
        };
        var text_info = "是否删除该组织";
        var hint = "该挑战组织后，相关项目会删除";
        checkPost(text_info, hint, href, post_data);
    });
});
