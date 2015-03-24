/**
 * Created by Ror on 23.03.2015.
 */
//{literal}

var ConfigForm = {
    showForm: function(){
        if(Admin.nowDisplayed == 'configForm'){
            Admin.getWindow().show();
            return;
        }
        Admin.nowDisplayed = 'configForm';
        var params = {
            url: Admin.url + "core/getAjaxConfig?ajax=1",
            type: "post",
            success: ConfigForm.createForm,
            response: 'json'
        };
        Ajax.request(params);
    },
    createForm: function(r){
        var form = ConfigForm.processResponse(r);
        var w = Admin.getWindow();
        w.setContent('');
        w.appendContent(form);
        w.setWidth(400);
        w.show();
    },
    processResponse: function(r){
        var menu = newElement('ul', {'class':'modules_list left'});
        var tabs = newElement('div', {'class':'module_tabs left'});
        for(var m in r) {
            var moduleTab = newElement('div', {'class':'module hidden '});
            var name;
            var a = newElement('a',{href:'#','onclick':'ConfigForm.openTab(this);return false;'});

            a.setAttribute('data-module', m)
            moduleTab.setAttribute('data-module', m)
            a.innerHTML = m;
            var li = newElement('li',{}, [a]);

            menu.appendChild(li);
            for(var key in r[m]) {
                moduleTab.appendChild(ConfigForm.buildElement(m, key, r[m][key]));
            }
            var submit = newElement('div',{},[
                newElement('input',{type:'submit'}),
                newElement('input',{type:'button','value':'+',onclick:'ConfigForm.addProperty("'+m+'", "'+key+'")'})
            ]);
            moduleTab.appendChild(submit);
            tabs.appendChild(moduleTab);
        }
        if(menu.get('li'))menu.get('li').addClass('active');
        if(tabs.get('.module'))tabs.get('.module').show();


        return newElement('form', {id:'config_form','method':'post','onsubmit':'ConfigForm.submitForm();return false;'}, [menu, tabs]);
    },
    buildElement: function(m, key, v){
        var label = newElement('label', {'for':'c_'+m+"_"+key});
        label.innerHTML = key+':';
        var input = newElement('input', {'type':'text', name:m+'['+key+']', id:'c_'+m+"_"+key, value:v});
        var del = newElement('a', {href:'#','class':'icon-delete left', onclick:'ConfigForm.deleteProperty(this, "'+m+'","'+key+'");return false;'})
        return newElement('div', {'class':'holder clearfix'},[label, input, del]);
    },
    deleteProperty: function(el, m, key){
        var clb = function(a) {
            if(!a)return;
            var params = {
                url: Admin.url + "core/deleteConfig?ajax=1",
                type: "post",
                response: 'text',
                data: {module: m, name: key}
            };
            Ajax.request(params);
            el.parentNode.remove();
        }
        DialogWindow.Confirm("config_delete_form", "Realy delete this option?<br/>All options required for stable system work.",clb,"I know what I do","No")
    },
    addProperty: function(m, key){
        var clb = function(a, b){
            if(!a)return;
            var newEl = ConfigForm.buildElement(m, b, '');
            document.body.get('#admin_panel .module_tabs .module[data-module="'+m+'"]').appendChild(newEl);
            newEl.moveUp();
        }
        DialogWindow.Prompt('config_new_property','Enter property name', clb, 'ok','cancel');
    },
    submitForm: function(){
        var data = Core.serialize(document.body.get('#config_form'));
        var params = {
            url: Admin.url + "core/saveConfig?ajax=1",
            type: "post",
            response: 'text',
            success: ConfigForm.submitFormSuccess,
            data:data
        };
        Ajax.request(params);
    },
    submitFormSuccess: function(a){
        if(a == '1') {
            DialogWindow.Alert("config_save_success", 'Configuration saved');
        }else{
            DialogWindow.Alert("config_save_success", 'Something wrong...');
        }
    },
    openTab: function(a){
        var last = document.body.get('#admin_panel .modules_list li.active');
        last.removeClass('active');
        document.body.get('#admin_panel .module_tabs .module[data-module="'+last.get('a').getAttribute('data-module')+'"]').hide();

        a.parentNode.addClass('active');
        document.body.get('#admin_panel .module_tabs .module[data-module="'+a.getAttribute('data-module')+'"]').show();
    }
};

//{/literal}