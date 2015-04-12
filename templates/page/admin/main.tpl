<style>
    {include file="page/admin/styles.css"}
</style>
<script>
    var AdminWords;
    try{
        AdminWords = JSON.parse("{$admin_translations}");
        var AdminIncludeOptions = JSON.parse("{$adminIncludeOptions}");
    }catch(e){
        AdminWords = {};
    }

    {include file="page/admin/admin.js" url=$url isMultipleLanguages=$isMultipleLanguages}
    {include file="page/admin/pagesTree.js" url=$url}
    {include file="page/admin/pageContent.js"}
    {include file="page/admin/templateForm.js"}
    {include file="page/admin/accessForm.js"}
    {include file="page/admin/config.js"}
    {include file="page/admin/word.js"}
    {include file="page/admin/files.js"}
</script>