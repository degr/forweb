/**
 * Created by Ror on 25.03.2015.
 */
    //{literal}
var Word = {
    showLanguagesForm: function(){
        if(Admin.nowDisplayed == 'languagesForm'){
            Admin.getWindow().show();
            return;
        }
        Admin.nowDisplayed = 'languagesForm';
        var params = {
            url:  Admin.url + "word/onUiLanguagesOverview?ajax=1",
            type: "POST",
            success: Word.createLanguagesForm,
            response: 'json'
        };
        Ajax.request(params);
    },
    showModulesForm: function(){
        if(Admin.nowDisplayed == 'wordModulesForm'){
            Admin.getWindow().show();
            return;
        }
        Admin.nowDisplayed = 'wordModulesForm';
        var params = {
            url:  Admin.url + "word/onUiModulesOverview?ajax=1",
            type: "POST",
            success: Word.createModulesForm,
            response: 'json'
        };
        Ajax.request(params);
    },
    createLanguagesForm: function(r){
        var form = UI.build(r);
        form.id = 'word_languages_overview';

        var w = Admin.getWindow();
        w.setContent("");
        w.setWidth(500);
        var radio = form.getAll('input[name="is_default"]');
        for(var i = 0; i < radio.length;i++){
            var v = radio[i].value;
            radio[i].setAttribute('type','radio');
            if(v == 1) {
                radio[i].checked = true;
            }
            radio[i].setAttribute('onclick', 'Word.switchDefault(this);');
        }
        var locales = form.getAll('input[name="locale"]');
        for(var i = 0; i < locales.length;i++){
            var v = locales[i].value;
            locales[i].setAttribute('onblur','Word.updateItem(this, "onAjaxUpdateLanguage")');
        }
        var addLang = newElement('div',{},[newElement('input',{'type':'button',onclick:'Word.addLanguage();',value:'+'})]);
        var div = newElement('div', {}, [form, addLang]);
        Admin.getWindow().appendContent(div);
        w.show();
    },
    switchDefault: function(item){
        var params = {
            url:  Admin.url + "word/onAjaxSetDefaultLanguage?ajax=1",
            type: "POST",
            success: Word.createModulesOverviewForm,
            response: 'json',
            data: {id:item.up('tr').getAttribute('data-field-id')}
        };
        Ajax.request(params);
    },
    addLanguage: function(){
        var locale = newElement('input', {type:"text",name:"locale", 'onblur':'Word.updateItem(this, "onAjaxUpdateLanguage")'});
        var isDefault = newElement('input', {type:"radio",name:"is_default", onclick:'Word.switchDefault(this);'});
        var deleteIcon = newElement('a', {'href':'#','onclick':'Word.deleteItem(this, "deleteLanguage");return false', 'class':'icon-delete'});
        var tr = newElement('tr', {}, [
            newElement('td', {}, [locale]),
            newElement('td', {}, [isDefault]),
            newElement('td', {}, [deleteIcon])
        ]);
        document.body.get('#word_languages_overview').appendChild(tr);
    },
    updateItem: function (el, func) {
        if(el.value == '')return;
        el.parentNode.drawPreloader('line');
        el.parentNode.setStyle({'position':'relative'});
        var params = {
            url:  Admin.url + "word/"+func+"?ajax=1",
            type: "POST",
            success: function(r){
                el.parentNode.removePreloader();
                el.parentNode.parentNode.setAttribute('data-field-id', r);
            },
            response: 'text',
            data: {id:el.up('tr').getAttribute('data-field-id'), item:el.value}
        };
        Ajax.request(params);
    },
    deleteItem: function(el, func){
        var clb = function(r){
            if(!r)return;
            var params = {
                url:  Admin.url + "word/"+func+"?ajax=1",
                type: "POST",
                success: function(){el.parentNode.parentNode.remove()},
                response: 'text',
                data: {id: el.parentNode.parentNode.getAttribute('data-field-id')}
            };
            Ajax.request(params);
        }
        DialogWindow.Confirm("delete_language", Admin.getWord('delete_language_confirm'), clb, Admin.getWord('confirm_yes'), Admin.getWord("confirm_no"));
    },
    createModulesForm: function(r){
        var form = UI.build(r);
        form.id = 'word_modules_overview';

        var w = Admin.getWindow();
        w.setContent("");
        w.setWidth(400);

        var modules = form.getAll('input[name="module"]');
        for(var i = 0; i < modules.length;i++){
            var v = modules[i].value;
            modules[i].setAttribute('onblur','Word.updateItem(this, "onAjaxUpdateModule")');
        }
        var addLang = newElement('div',{},[newElement('input',{'type':'button',onclick:'Word.addModule();',value:'+'})]);
        var div = newElement('div', {}, [form, addLang]);
        Admin.getWindow().appendContent(div);
        w.show();
    },
    addModule: function(){
        var module = newElement('input', {type:"text",name:"module", 'onblur':'Word.updateItem(this, "onAjaxUpdateModule")'});
        var deleteIcon = newElement('a', {'href':'#','onclick':'Word.deleteItem(this, "deleteModule");return false', 'class':'icon-delete'});
        var tr = newElement('tr', {}, [
            newElement('td', {}, [module]),
            newElement('td', {}, [deleteIcon])
        ]);
        document.body.get('#word_modules_overview').appendChild(tr);
    },
    showModuleTerms: function(link, id, page, filters){
        if(!id && link) {
            id = link.parentNode.parentNode.getAttribute('data-field-id');
        }
        if(!page)page = 0;
        if(!id)return;
        var params = {
            url:  Admin.url + "word/showModuleTerms?ajax=1",
            type: "POST",
            success: Word.displayModuleTerms,
            response: 'json',
            data: {id:id,page:page, filters:filters}
        };
        Ajax.request(params);
    },
    displayModuleTerms: function(r){
        Admin.nowDisplayed = 'showTermsForm';
        var table = UI.build(r.form);
        table.id = 'word_terms_overview';

        var w = Admin.getWindow();
        w.setContent("");
        w.setWidth(800);
        var elems = [];
        if(r.title) {
            var title = newElement('h4', {});
            title.innerHTML = r.title;
            elems.push(title);
        }
        var filters = UI.build(r.filters);
        elems.push(filters);
        var back = newElement('a',{href:'#',onclick:'Word.showModulesForm();return false;'});
        back.innerHTML = Admin.getWord('back_to_dictionaries');
        elems.push(back);
        elems.push(table);
        table.setAttribute('data-module', r.module);
        elems.push(newElement('div',{},[newElement('input',{'type':'button',onclick:'Word.addTerm(null, '+ r.module+');',value:'+'})]));
        elems.push(Word.buildPaginator(r.paginator, r.module));
        var div = newElement('div', {}, elems);
        Admin.getWindow().appendContent(div);
        w.show();
    },
    buildPaginator: function(p, m){
        for(var i in p)p[i]=parseInt(p[i]);
        var attributes = {'class':'paginator','data-page':p.page,'data-filters':p.filters};
        if(p.count <= p.itemsOnPage)return newElement('span',attributes);
        console.log(p);
        attributes['class'] += " clearfix";
        var out = newElement('div', attributes);
        var maxBlocks = 3;
        var pagesCount = Math.ceil(p.count/ p.itemsOnPage);
        var link = ['Word.showModuleTerms(null,'+m+',', ");return false"];
        if(p.page > 0) {
            var back = newElement('a',{href:'#',onclick:link[0]+ (p.page-1)+link[1]});
            back.innerText = "<";
            out.appendChild(back);
        }
        for(var i = maxBlocks; i > 0; i--) {
            if(p.page-i < 0){
                continue;
            }
            var block = newElement('a',{href:'#',onclick:link[0]+ (p.page-i)+link[1]});
            block.innerText = p.page - i + 1;
            out.appendChild(block);
        }
        var thisLink = newElement('a',{href:'#',onclick:'return false;','class':'active'});
        thisLink.innerText = p.page+1;
        out.appendChild(thisLink);
        if(p.page+1 < pagesCount) {
            for(var i = p.page; i < maxBlocks + p.page; i++) {
                if(i+1 >= pagesCount)continue;
                var block = newElement('a',{href:'#',onclick:link[0]+ (i+1)+link[1]});
                block.innerText = i+2;
                out.appendChild(block);
            }
            var forward = newElement('a',{href:'#',onclick:link[0]+ (p.page+1)+link[1]});
            forward.innerText = ">";
            out.appendChild(forward);
        }
        return out;
    },
    showTermsForm: function(){
        if(Admin.nowDisplayed == 'showTermsForm') {
            Admin.getWindow().show();
        } else {
            Word.showModulesForm();
        }
    },
    addTerm: function(link, module){
        var id = 0;
        if(link) {
            id= link.up('tr').getAttribute('data-field-id');
        }
        var params = {
            url:  Admin.url + "word/getTermForm?ajax=1",
            type: "POST",
            success: Word.buildTermForm,
            response: 'json',
            data: {id:id,module:module}
        };
        Ajax.request(params);
    },
    buildTermForm: function(r){
        Admin.nowDisplayed = 'showTermsForm';
        var form = UI.build(r.form);
        form.setAttribute('onsubmit', 'Word.submitTerm(this);return false;');
        var onclick;
        if(r.module) {
            onclick = 'Word.showModuleTerms(null, '+r.module+')';
        }else{
            onclick = 'Word.showModulesForm()';
        }
        var back = newElement('a', {'href':'#',onclick:onclick, 'class':'backlink'});
        back.innerHTML = Admin.getWord('back_to_current_dict');
        var reset = newElement('input',{value:Admin.getWord('word_reset_term'),type:'button','class':'new_term','onclick':'Word.resetTermForm()'})
        form.appendChild(reset);
        var div = newElement('div', {}, [back, form]);
        Admin.getWindow().setContent(div);
        return div;
    },
    submitTerm: function(form){
        var data = Core.serialize(form);
        var params = {
            url:  Admin.url + "word/saveTerm?ajax=1",
            type: "POST",
            success: Word.onSaveTerm,
            response: 'json',
            data: data
        };
        Ajax.request(params);
    },
    onSaveTerm: function(r) {
        if(!r.error && r.id) {
            document.body.get('#form_word input[name="id"]').value = r.id;
        }
        DialogWindow.Alert('save_term_alert', r.text);
    },
    resetTermForm: function(){
        var form = document.body.get('#form_word');
        var els = form.getAll('textarea, input[name="name"], input[name="id"]');
        for(var i = 0;i< els.length;i++) {
            els[i].value = "";
        }
    },
    filterTerms: function(){
        var paginator = document.body.get('#admin_panel .paginator');
        var filters = {
            name: document.body.get('#word_filters input[name="name"]').value,
            value: document.body.get('#word_filters input[name="value"]').value,
            page: paginator.getAttribute('data-page'),
            oldFilters: paginator.getAttribute('data-filters'),
            module: document.body.get('#word_terms_overview').getAttribute('data-module')
        };
        Word.showModuleTerms(null, filters.module, null, JSON.stringify(filters));
    },
    showThisPageKeys: function(){
        if(document.location.search.indexOf("?") != 0) {
            document.location.search = "?show_keys";
        } else {
            if(document.location.search.indexOf("&show_keys") == -1) {
                document.location.search += "&show_keys";
            }
        }
        console.log(document.location);
    }
};
//{/literal}