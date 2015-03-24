//{literal}
var Admin = {
    nowDisplayed: null,
    modulesList: null,
    dialogWindow: null,
    panel: null,
    url: '{/literal}{$url}{literal}',
    init: function(){
        this.getModulesList();
        this.initPanel();
        this.initNewPageButton();
        this.initEditPageButton();
        this.initPagesTree();
        this.initPageСontent();
        this.addSeparator();
        this.initTemplateCreate();
        this.initTemplateEdit();
        this.initTemplateDelete();
        this.addSeparator();
        this.addAccess();
        this.addSeparator();
        this.initConfig();
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
        a.innerHTML = "New page";
        Admin.panel.add(a);
    },
    initEditPageButton: function(){
        var a = newElement('a', {"class":'button', 'onclick': 'Admin.editPage();return false;', 'href': '#'});
        a.innerHTML = "Edit page";
        Admin.panel.add(a);
    },
    initPagesTree: function(){
        var a = newElement('a', {"class":'button', 'onclick': 'Admin.showPagesTree();return false;', 'href': '#'});
        a.innerHTML = "Show pages";
        Admin.panel.add(a);
    },
    addAccess: function(){
        var a = newElement('a', {"class":'button', 'onclick': 'AccessForm.createForm();return false;', 'href': '#'});
        a.innerHTML = "Edit access";
        Admin.panel.add(a);
    },
    initTemplateCreate: function(){
        var a = newElement('a', {"class":'button', 'onclick': 'TemplateForm.createTemplate();return false;', 'href': '#'});
        a.innerHTML = "Create template";
        Admin.panel.add(a);
    },
    initTemplateDelete: function(){
        var a = newElement('a', {"class":'button', 'onclick': 'TemplateForm.deleteTemplateForm();return false;', 'href': '#'});
        a.innerHTML = "Delete template";
        Admin.panel.add(a);
    },
    initTemplateEdit: function(){
        var a = newElement('a', {"class":'button', 'onclick': 'Admin.editTemplate();return false;', 'href': '#'});
        a.innerHTML = "Edit template";
        Admin.panel.add(a);
    },
    initPageСontent: function(){
        var a = newElement('a', {"class":'button', 'onclick': 'Admin.pageContent();return false;', 'href': '#'});
        a.innerHTML = "Page content";
        Admin.panel.add(a);
    },
    initConfig: function(){
        var a = newElement('a', {"class":'button', 'onclick': 'ConfigForm.showForm();return false;', 'href': '#'});
        a.innerHTML = "Configuration";
        Admin.panel.add(a);
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
        w.setWidth(0);
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

    showPagesTree: function(){
        if(Admin.nowDisplayed == 'pagesTree') {
            Admin.getWindow().show();
            return;
        }
        Admin.nowDisplayed = 'pagesTree';
        var params = {
            url: Admin.url + "page/showPagesTree?ajax=1",
            type: "get",
            success: Admin.renderPagesTree,
            response: 'json',
            data: {href: document.location.href}
        };
        Ajax.request(params);
    },
    renderPagesTree: function(response){
        var table = newElement('table', {});
        Admin.fillPagesTree(table, response.pages, 0);
        var colspan = Admin.setPaddingsPagesTree(table);
        Admin.generateLinks(table);
        Admin.buildTableHead(table, ['Page name', 'Page url'], colspan);

        var w = Admin.getWindow();
        w.setContent(table);
        w.setWidth(400);
        w.show();

    },
    buildTableHead: function(table, headers, colspan){
        var row = newElement('tr', {'class': 'head'});

        for(var i=0; i<headers.length;i++){
            var th = newElement('th', {});
            th.innerHTML = headers[i];
            if(i == 0){
                th.setAttribute('colspan', colspan);
            }
            row.appendChild(th);
        }
        var first = table.get('tr');
        table.insertBefore(row, first);
    },

    generateLinks: function(table){
        var links = [];
        var rows = table.getAll('tr');
        for(var i = 0; i < rows.length; i++){
            var url = rows[i].get('td.url').innerHTML;
            var depth = rows[i].getAttribute('data-depth');
            if(depth > links.length) {
                links.push(url);
            }else if(links.length == 0){
                //home page
                links.push(url);
            }else if(depth == links.length){
                links.pop();
                links.push(url);
            }else if(depth < links.length){
                //exit from top branch
                while(depth != links.length + 1)
                    links.pop();
                links.push(url);
            }
            var path = links.join("/");
            var a = newElement('a', {href: Admin.url + path});
            a.innerHTML = path;
            rows[i].get('td.url').innerHTML = '';
            rows[i].get('td.url').appendChild(a);
        }
    },
    setPaddingsPagesTree: function(table){
        var rows = table.getAll('tr');
        var calculate = {};
        for(var i=0; i<rows.length;i++){
            var key = rows[i].getAttribute('data-parent');
            calculate[key] = key;
        }
        var length = 0;
        for(var i in calculate){
            length++;
        }
        var cycle = length;
        for(var i in calculate){
            var depthRows = table.getAll('tr[data-parent="'+i+'"]');
            for(var x = 0; x < depthRows.length; x++){
                for(var j = 0; j < length - cycle; j++){
                    var td = newElement('td', {'data-depth': j+1, "class": "tree placeholder"});
                    depthRows[x].insertBefore(td, depthRows[x].get('.name'))

                }
                depthRows[x].get('td.name').setAttribute('colspan', cycle);
                depthRows[x].setAttribute('data-depth', length - cycle);
            }
            cycle--;
        }
        return length;
    },
    fillPagesTree: function(table, obj, parent){
        var out = {};
        var empty = true;
        for(var i in obj){
            if(obj[i].parent == parent){
                var row = newElement('tr', {'data-parent':parent,'data-id':obj[i].id, 'id':'page_'+obj[i].id});
                var tdName = newElement('td', {'class':'name'});
                tdName.innerHTML = obj[i].name;
                var tdUrl = newElement('td', {'class':'url'});
                tdUrl.innerHTML = obj[i].url;
                row.appendChild(tdName)
                row.appendChild(tdUrl);
                if(parent == 0) {
                    table.appendChild(row);
                } else {
                    var rows = table.getAll('tr');
                    var target = table.get('#page_'+parent);

                    var next = null;
                    var detected = false;
                    var last = 0;
                    for(var i=0; i<rows.length;i++){
                        if(rows[i] === target){
                            if(rows[i+1]){
                                next = rows[i+1]
                            }else{
                                next = null;
                            }
                            detected = true;
                        }
                        if(detected && rows[i].getAttribute('data-parent') == parent){
                            if(rows[i+1]){
                                next = rows[i+1]
                            }else{
                                next = null;
                            }
                        }else if(detected){
                            break;
                        }
                    }
                    if(next){
                        table.insertBefore(row, next)
                    }else{
                        table.appendChild(row);
                    }
                }
            }else{
                empty = false;
                out[i] = obj[i];
            }
        }
        if(!empty){
            parent++;
            Admin.fillPagesTree(table, out, parent);
        }
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