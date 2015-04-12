//{literal}
var FilesForm = {
    nowDisplayed: null,
    uploadStart: false,
    showImages: function(path){
        if(!path)path = '';
        FilesForm.nowDisplayed = 'img';
        FilesForm.showForm('img', path);
    },
    showText: function(type, path){
        if(!path)path = '';
        FilesForm.nowDisplayed = type;
        FilesForm.showForm(type, path);
    },
    showForm: function(type, path){
        Admin.nowDisplayed = '';
        var params = {
            url: Admin.url + "files/getAjaxUserMedia?ajax=1",
            type: "post",
            success: FilesForm.createForm,
            response: 'text',
            data: {type:type, path: path}
        };
        Ajax.request(params);
    },
    createForm: function(r){
        var w = Admin.getWindow();
        w.setContent('');
        w.setContent(r);

        setTimeout(FilesForm.processLinks, 50);
        w.setWidth(800);
        w.show();
    },
    processLinks: function(){
        var w = Admin.getWindow();
        var links = w.getContentHolder().getAll('a.file-manager-item');
        for(var i = 0; i < links.length;i++) {
            var l = links[i];
            l.setAttribute('onclick', FilesForm.parseLink(l));
        }
    },
    parseLink: function(l){
        var path = l.pathname.replace(/^\//, '');
        var parts = path.split('/');
        var i = parts.shift();
        if(FilesForm.isFile(path)) {
            return 'FilesForm.showFileContent(this);return false;';
        } else {
            if(FilesForm.nowDisplayed == 'img') {
                return 'FilesForm.showImages("'+parts.join('/')+'");return false;';
            } else {
                return 'FilesForm.showText("'+i+'", "'+parts.join('/')+'");return false;';
            }
        }
    },
    showFileContent: function(l){
        var href = l.href;
        if(FilesForm.nowDisplayed == 'img') {
            var w = Admin.getWindow().getContentHolder().get('.image_wrapper');
            w.get('.canvas').src = href;
            w.get('.path').value = l.pathname;
        } else {
            var params = {
                url: Admin.url + "files/showFileContent?ajax=1",
                type: "post",
                response: 'text',
                success: FilesForm.displayTextFileContent,
                data:{path : l.pathname, type:FilesForm.nowDisplayed}
            };
            Ajax.request(params);
            Admin.getWindow().getContentHolder().get('#admin_file_content').setAttribute('data-path', l.pathname);
        }
    },

    displayTextFileContent: function(r){
        var w = Admin.getWindow().getContentHolder().get('#admin_file_content');
        w.value = r;
    },
    isFile: function(path){
        var e = ['jpg', 'png', 'gif', 'ico', 'less','sass','txt', 'css', 'js', 'tpl'];
        var ext = path.split('.').pop();
        return e.indexOf(ext) != -1;
    },
    updateTextFile: function(){
        var content = Admin.getWindow().getContentHolder().get('#admin_file_content').value;
        var path = Admin.getWindow().getContentHolder().get('#admin_file_content').getAttribute('data-path');
        var params = {
            url: Admin.url + "files/updateTextFile?ajax=1",
            type: "post",
            response: 'text',
            success: FilesForm.submitFormSuccess,
            data:{content:content, path:path}
        };
        Ajax.request(params);
    },
    submitFormSuccess: function(a){
        DialogWindow.Alert('files_form_update_file', a);
    },
    upload: function(input){
        if(!input.value)return;
        var separator = input.value.indexOf('/') > -1 ? '/' : '\\';
        var name = input.value.split(separator).pop().trim().toLowerCase();
        var filesList = Admin.getWindow().getContentHolder().get('ul.file-manager');
        var links = filesList.getAll('a.file-manager-item');
        var clb = function(r){
            if(!r){input.value = '';return};
            var path = filesList.getAttribute('data-path');
            var f = input.up('form');
            f.get('input[name="path"]').value = path;
            FilesForm.uploadStart = true;
            f.submit();
        };
        for(var i=0;i<links.length;i++) {
            console.log(links[i].innerHTML.trim().toLowerCase(), name);
            if(links[i].innerHTML.trim().toLowerCase() == name) {
                DialogWindow.Confirm('files_name_exist', Admin.getWord('file_or_folder_exist'),
                    clb, Admin.getWord('confirm_yes'), Admin.getWord("confirm_no"));
                return;
            }
        }
        clb(true);
    },
    onUpload: function(iframe){
        if(!FilesForm.uploadStart)return;
        FilesForm.uploadStart = false;
        var doc = iframe.contentDocument || iframe.contentWindow.document;
        var out = doc.body.innerHTML;
        if(out == 1) {
            var clb = function() {
                var path = Admin.getWindow().getContentHolder().get('ul.file-manager').getAttribute('data-path');
                path = path.replace(/^\/|\/$/g, '', path);
                var parts = path.split('/');
                var root = parts.shift();
                var path = parts.join('/');
                if (root == 'img') {
                    FilesForm.showImages(path);
                } else {
                    FilesForm.showText(root, path);
                }
            };
            DialogWindow.Alert("files_upload_success", Admin.getWord('file_uploaded'), clb);
        } else {
            DialogWindow.Alert("files_upload_fail", Admin.getWord('file_uploaded'));
        }
    },
    deleteFile: function(del, path){
        var clb = function (a) {
            if(!a)return;
            var params = {
                url: Admin.url + "files/adminDeleteFile?ajax=1",
                type: "post",
                response: 'text',
                success: function(r){
                    DialogWindow.Alert('files_after_delete', r);
                    del.up('li').remove();
                },
                data:{path:path}
            };
            Ajax.request(params);
        };
        DialogWindow.Confirm(
            'delete_file_file',
            Admin.getWord('files_delete_file'),
            clb,
            Admin.getWord('confirm_yes'),
            Admin.getWord("confirm_no")
        );
    },
    newFolder: function(){
        FilesForm.newFile(true)
    },
    newFile: function(folder){
        var clb = function(r, name) {
            if(!r) return;
            var path = Admin.getWindow().getContentHolder().get('ul.file-manager').getAttribute('data-path');
            var type = folder ? 'folder' : 'file';
            var params = {
                url: Admin.url + "files/adminNewFile?ajax=1",
                type: "post",
                response: 'text',
                success: function (r) {
                    var path = Admin.getWindow().getContentHolder().get('ul.file-manager').getAttribute('data-path');
                    path = path.replace(/^\/|\/$/g, '', path);
                    var parts = path.split('/');
                    var root = parts.shift();
                    var path = parts.join('/');
                    if (root == 'img') {
                        FilesForm.showImages(path);
                    } else {
                        FilesForm.showText(root, path);
                    }
                },
                data: {path: path, type: type, name:name}
            };
            Ajax.request(params);
        }
        DialogWindow.Prompt('new_file_folder_create', Admin.getWord('enter_new_file_name'), clb);
    }
};
//{/literal}