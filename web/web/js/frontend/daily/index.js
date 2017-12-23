$(function(){
    $("[name='delete_one']").on('click', function (e) {
        e.preventDefault();
        href = $(this).attr("href");
        var post_data = {
            'ids' : $(this).attr("model_id")
        };
        var text_info = "是否删除该作息模式";
        var hint = "该作息模式删除后，关联的作息安排会丢失";
        checkPost(text_info, hint, href, post_data);
    });
});
