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
    {include file="page/admin/pages.tree.js" url=$url}
</script>