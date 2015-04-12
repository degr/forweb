//{literal}
var Admin = {
    nowDisplayed: null,
    modulesList: null,
    dialogWindow: null,
    panel: null,
    url: '{/literal}{$url}{literal}',
    isMultipleLanguages: '{/literal}{$isMultipleLanguages}{literal}',
    init: function(){
        this.isMultipleLanguages = this.isMultipleLanguages == '1' ? true : false;
        this.getModulesList();
        this.initPanel();
        this.initNewPageButton();
        this.initEditPageButton();
        this.initPagesTree();
        this.initPageСontent();
        this.addSeparator();
        this.initTemplateMenu();
        this.addAccess();
        this.initConfig();
        this.initFiles();
        this.addSeparator();
        this.initWord();
        this.addSeparator();
        this.initPosition();
    },
    getWord: function(key){
        if(AdminWords[key]){
            return AdminWords[key];
        }
        return "::"+key;
    },
    getModulesList: function(){
        var params = {
            url:  Admin.url + "page/getModulesList?ajax=1",
            type: "get",
            success: function(r){Admin.modulesList = r},
            response: 'json'
        };
        Ajax.request(params);
    },
    getWindow: function(){
        if(this.dialogWindow == null){
            this.dialogWindow = DialogWindow.Alert('admin_panel', '', null, true);
        }
        return this.dialogWindow;
    },
    addSeparator: function(){
        var hr = newElement('hr', {'style':'margin: 0;'});
        Admin.panel.add(hr);
    },
    initPanel: function(){

        var ul = newElement('ul', {'class' : 'buttons'});

        var panel =  newElement('div', {'class':"admin_panel"}, [ul]);
        panel.setStyle({'position':'fixed',top:'50px',left:0,width:'120px', 'min-height':'100px','z-index':100, 'border':'1px solid black'});

        document.body.appendChild(panel);
        this.panel = {
            panel: panel,
            add: function(tag){
                var li = newElement('li', {});
                li.appendChild(tag);
                ul.appendChild(li);
            }
        }
    },
    initNewPageButton: function(){
        var a = newElement('a', {"class":'button', 'onclick': 'Admin.newPage();return false;', 'href': '#'});
        a.innerHTML = Admin.getWord('panel_new_page');
        Admin.panel.add(a);
    },
    initEditPageButton: function(){
        var a = newElement('a', {"class":'button', 'onclick': 'Admin.editPage();return false;', 'href': '#'});
        a.innerHTML = Admin.getWord('panel_edit_page');
        Admin.panel.add(a);
    },
    initPagesTree: function(){
        var a = newElement('a', {"class":'button', 'onclick': 'PagesTree.showPagesTree();return false;', 'href': '#'});
        a.innerHTML = Admin.getWord("panel_show_pages");
        Admin.panel.add(a);
    },
    addAccess: function(){
        var a = newElement('a', {"class":'button', 'onclick': 'AccessForm.createForm();return false;', 'href': '#'});
        a.innerHTML = Admin.getWord("panel_edit_access");
        Admin.panel.add(a);
    },
    initFiles: function(){
        var toggler = newElement('a', {"class":'button', 'onclick': 'return false;', 'href': '#'});
        toggler.innerHTML = Admin.getWord('panel_edit_files');

        var images = newElement('a', {"class":'button', 'onclick': 'FilesForm.showImages();return false;', 'href': '#'});
        images.innerHTML = Admin.getWord("panel_file_images");
        var templates = newElement('a', {"class":'button', 'onclick': 'FilesForm.showText("templates");return false;', 'href': '#'});
        templates.innerHTML = Admin.getWord("panel_file_templates");
        var css = newElement('a', {"class":'button', 'onclick': 'FilesForm.showText("css");return false;', 'href': '#'});
        css.innerHTML = Admin.getWord("panel_file_css");
        var js = newElement('a', {"class":'button', 'onclick': 'FilesForm.showText("js");return false;', 'href': '#'});
        js.innerHTML = Admin.getWord("panel_file_js");

        var innerDiv = newElement('div', {'class':'menu_holder'}, [images, templates, css, js]);
        var div = newElement('div',{'class':'files_holder'}, [toggler, innerDiv]);
        Admin.panel.add(div);
    },
    initTemplateMenu: function(){
        var toggler = newElement('a', {"class":'button', 'onclick': 'return false;', 'href': '#'});
        toggler.innerHTML = Admin.getWord('panel_templates');

        var create = newElement('a', {"class":'button', 'onclick': 'TemplateForm.createTemplate();return false;', 'href': '#'});
        create.innerHTML = Admin.getWord("panel_create_template");
        var del = newElement('a', {"class":'button', 'onclick': 'TemplateForm.deleteTemplateForm();return false;', 'href': '#'});
        del.innerHTML = Admin.getWord("panel_delete_template");
        var edit = newElement('a', {"class":'button', 'onclick': 'Admin.editTemplate();return false;', 'href': '#'});
        edit.innerHTML = Admin.getWord("panel_edit_template");

        var innerDiv = newElement('div', {'class':'menu_holder'}, [create, edit, del]);
        var div = newElement('div',{'class':'templates_holder'}, [toggler, innerDiv]);
        Admin.panel.add(div);
    },
    initPageСontent: function(){
        var a = newElement('a', {"class":'button', 'onclick': 'Admin.pageContent();return false;', 'href': '#'});
        a.innerHTML = Admin.getWord("panel_page_content");
        Admin.panel.add(a);
    },
    initConfig: function(){
        var a = newElement('a', {"class":'button', 'onclick': 'ConfigForm.showForm();return false;', 'href': '#'});
        a.innerHTML = Admin.getWord("panel_configuration");
        Admin.panel.add(a);
    },
    initPosition: function(){
        var a = newElement('a', {"class":'button', 'onclick': 'Admin.changePosition();return false;', 'href': '#'});
        a.innerHTML = Admin.getWord("panel_position");
        var position = Core.cookie.getCookie('admin_panel_position');
        if(!position)position = 'left';
        Admin.panel.position = position;
        if(position=='right')Admin.panel.panel.addClass('rightPosition');
        Admin.panel.add(a);
    },
    initWord: function(){
        var toggler = newElement('a', {"class":'button', 'onclick': 'return false;', 'href': '#'});
        toggler.innerHTML = Admin.getWord("panel_word");
        var lang = newElement('a', {"class":'button', 'onclick': 'Word.showLanguagesForm();return false;', 'href': '#'});
        lang.innerHTML = Admin.getWord("panel_languages");
        var modules = newElement('a', {"class":'button', 'onclick': 'Word.showModulesForm();return false;', 'href': '#'});
        modules.innerHTML = Admin.getWord("panel_modules");
        var word = newElement('a', {"class":'button', 'onclick': 'Word.showTermsForm();return false;', 'href': '#'});
        word.innerHTML = Admin.getWord("panel_word");
        var show_keys = newElement('a', {"class":'button', 'onclick': 'Word.showThisPageKeys();return false;', 'href': '#'});
        show_keys.innerHTML = Admin.getWord("panel_word_keys");

        var innerDiv = newElement('div', {'class':'menu_holder'}, [lang, modules, word, show_keys]);
        var div = newElement('div',{'class':'word_holder'}, [toggler, innerDiv]);
        Admin.panel.add(div);
    },
    changePosition: function(){
        if(Admin.panel.position == 'right') {
            Admin.panel.position = 'left';
            Admin.panel.panel.removeClass('rightPosition');
        } else {
            Admin.panel.position = 'right';
            Admin.panel.panel.addClass('rightPosition');
        }
        Core.cookie.setCookie('admin_panel_position', Admin.panel.position, 1);
    },
    newPage: function(){
        if(Admin.nowDisplayed == 'newPage'){
            Admin.getWindow().show();
            return;
        }
        Admin.nowDisplayed = 'newPage';
        var params = {
            url: Admin.url + "page/editPage?ajax=1",
            type: "post",
            success: Admin.showPageForm,
            response: 'json'
        };
        Ajax.request(params);
    },
    pageContent: function(){
        if(Admin.nowDisplayed == 'pageContent'){
            Admin.getWindow().show();
            return;
        }
        Admin.nowDisplayed = 'pageContent';
        var params = {
            url:  Admin.url + "page/pageContent?ajax=1",
            type: "POST",
            success: Admin.showPageContentForm,
            response: 'json',
            data: {href: document.location.href}
        };
        Ajax.request(params);
    },
    editPage: function(){
        if(Admin.nowDisplayed == 'editPage'){
            Admin.getWindow().show();
            return;
        }
        Admin.nowDisplayed = 'editPage';
        var params = {
            url:  Admin.url + "page/editPage?ajax=1",
            type: "POST",
            success: Admin.showPageForm,
            response: 'json',
            data: {href: document.location.href}
        };
        Ajax.request(params);
    },
    setResponseText: function(response){
        if(response.text) {
            var w = Admin.getWindow();
            var message = newElement('h3', {'class':'message'});
            w.appendContent(message);
            message.innerHTML = response.text;
        }
    },
    showPageForm: function(response){
        var form = UI.build(response);
        Event.add(form, 'submit', Admin.submitPageForm);
        var w = Admin.getWindow();
        w.setContent('');
        Admin.setResponseText(response);
        w.appendContent(form);
        w.setWidth(800);
        w.show();
    },
    submitPageForm: function(e){
        Core.prevent(e);
        params = Admin.getPageFormAjaxData();
        params.success = function(response){
            Admin.nowDisplayed = 'editPage';
            Admin.showPageForm(response);
        }
        Ajax.request(params);
    },
    getPageFormAjaxData: function(){
        var data = Core.serialize(document.body.get('#form_pages'));
        data.ajax_key = 'pageedit';
        var success = Admin.showPageForm
        return {
            url: Admin.url + "page/editPage?ajax=1",
            type: "POST",
            success: success,
            response: 'json',
            data: data
        };
    },
    deletePage: function(e){
        Core.prevent(e);
        params = Admin.getPageFormAjaxData();
        params.data.deletePage = true;
        params.success = function(response, data){
            Admin.showPageForm(response);
            if(response.parentLink){
                Admin.nowDisplayed = null;
                document.location.href = response.parentLink;
            }
        }
        Ajax.request(params);
    },


    showPageContentForm: function(response){
        var div = newElement("div");
        div.setStyle({height:'700px',overflow:'auto'});
        for(var i in response) {
            div.appendChild(Admin.buildBlockForm(response[i]));
        }

        var w = Admin.getWindow();
        Admin.setResponseText(response);
        w.setContent('');
        w.appendContent(div);
        w.setWidth(800);
        w.show();
    },
    buildBlockForm: function(block){
        var form = newElement('form', {method: 'post', onsubmit:'PageContent.submit(event, this);'});
        var blockTitle = newElement('h3', {'class': 'block_title'});
        blockTitle.innerHTML = block.title;
        form.appendChild(blockTitle);
        form.appendChild(newElement('div', {'class': 'serverResponse'}));
        form.appendChild(newElement('input',{name:'block_id',type:'hidden',value:block.id}));
        form.appendChild(newElement('input',{name:'page_id',type:'hidden',value:block.page_id}));
        form.appendChild(Admin.buildPageContentBlock("before", block.fields.before, true, true));
        form.appendChild(Admin.buildPageContentBlock("template", block.fields.template, false, false));
        form.appendChild(Admin.buildPageContentBlock("after", block.fields.after, true, true));
        form.appendChild(UI.builder.buildActiveElement(block.fields.submit));
        form.appendChild(PageContent.getAddTemplateButton());

        return form;
    },
    buildPageContentBlock: function(clazz, field, forPage, active ) {
        var out = newElement('div', {'class':clazz + " clearfix"});
        for (var i in field){
            out.appendChild(PageContent.getTemplate(active, field[i], forPage));
        }
        return out;
    },
    editTemplate: function(){
        if(Admin.nowDisplayed == 'editTemplateForm') {
            Admin.getWindow().show();
            return;
        }
        Admin.nowDisplayed = 'editTemplateForm';
        var params = {
            url: Admin.url + "page/editTemplateForm?ajax=1",
            type: "post",
            success: Admin.editTemplateForm,
            response: 'json',
            data: {href: document.location.href}
        };
        Ajax.request(params);
    },
    editTemplateForm: function(response){
        var form = UI.build(response.baseForm);
        Event.add(form, 'submit', TemplateForm.mainFormSubmit);
        var addBlock = newElement('input', {'class':'left', type:'button','value':'Add Block', onclick:'TemplateForm.addBlock()'})
        form.appendChild(addBlock);
        var includesForm = TemplateForm.getIncludesForm(response.templateIncludes);
        Admin.setResponseText(response);

        var out = newElement('div', {}, [newElement('div',{'class':'text'}),form, includesForm]);
        out.setStyle({height:'700px',overflow:'auto'});
        form.parentNode.get('form > div.form_holder').addClass("left");
        form.addClass("clearfix");
        var w = Admin.getWindow();
        w.setContent('');
        w.appendContent(out);
        w.setWidth(800);
        w.show();
    },
    setMethodsTo: function(select, moduleName){
        if(!moduleName)return;
        var params = {
            url: Admin.url + "page/getMethodsList?ajax=1",
            type: "post",
            success: function(r){
                select.innerHTML = '';
                for(var i in r){
                    var o = newElement('option',{value:i});
                    o.innerHTML = r[i];
                    select.appendChild(o)
                }
            },
            response: 'json',
            data: {moduleName:moduleName}
        };
        Ajax.request(params);
    }
};
Admin.init();
//{/literal}