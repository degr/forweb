//{literal}
var AccessForm = {
    createForm: function(){
        if(Admin.nowDisplayed == 'accessForm') {
            Admin.getWindow().show();
            return;
        }
        Admin.nowDisplayed = 'accessForm';
        AccessForm.requestAccessForm();
    },
    requestAccessForm: function(){
        var params = {
            success: AccessForm.showAccessForm,
            url: Admin.url + "access/getAccessForm?ajax=1",
            type: "post",
            response: 'json',
            data: {'action':'getForm'}
        }
        Ajax.request(params);
    },
    showAccessForm: function(r){
        var table = UI.build(r);
        table.id = 'access_table';
        var t = newElement('div', {'class':'text'});
        Event.add(table, 'submit', TemplateForm.deleteTemplate);
        var rows = table.getAll('tr');
        for(var i = 0; i < rows.length;i++) {
            var cells = rows[i].getAll('td');
            var id = rows[i].getAttribute('data-field-id')
            for(var j = 0; j < cells.length; j++) {
                var c = cells[j];
                var v = c.innerHTML;
                c.innerHTML = '';
                if(j == 0) {
                    var input = newElement('input',{'type':'text', onblur:'AccessForm.editActionName(this)',value:v});
                    c.appendChild(input);
                    continue;
                }
                var name = c.getAttribute('data-name');
                var input = newElement('input', {'type':'checkbox', 'name':name, id:name+'_'+id,'onchange':'AccessForm.switchPermissions(this)'});
                var label = newElement('label', {'for':name+'_'+id}, [input]);
                input.checked = v == '1'
                c.appendChild(label);
            }

            if(i == 0) {
                var headers = rows[0].getAll('th');
                for(var w = 1; w < headers.length; w++){
                    headers[w].appendChild(newElement("a",{href:'#',onclick:'AccessForm.confirmDeleteAccessGroup(this);return false;','class':'icon-delete'}));
                }

                var th = newElement('th', {});
                th.innerHTML = "delete"

                rows[i].appendChild(th);
            } else {
                var a = newElement("a",{href:'#',onclick:'AccessForm.confirmDeleteAction(this);return false;','class':'icon-delete'});
                rows[i].appendChild(newElement('td',{},[a]));
            }
        }
        var w = Admin.getWindow();
        var newAccessGroup = newElement('input',{type:'button', 'onclick':'AccessForm.promptCreateAccessGroup()', value:'New Access Group','class':'left'});
        var newAccessAction = newElement('input',{type:'button', 'onclick':'AccessForm.promptCreateAccessAction()', value:'New Access Action','class':'left'});
        var acPanel = newElement('div', {'class':'clearfix'},[newAccessGroup, newAccessAction]);
        w.setContent('');
        w.appendContent(newElement('div', {}, [t, table,acPanel]));
        w.setWidth(600);
        w.show();
    },
    switchPermissions: function(el){
        var td = el.parentNode.parentNode;
        td.drawPreloader('small');
        var action = td.parentNode.get('td[data-name="action"] input').value;
        var params = {
            success: function(){td.removePreloader()},
            url: Admin.url + "access/switchAccess?ajax=1",
            type: "post",
            response: 'text',
            data: {'group' : el.name, id:td.parentNode.getAttribute('data-field-id'), 'action':action}
        };
        Ajax.request(params);
    },
    editActionName: function(el){
        var td = el.parentNode;
        td.drawPreloader('line');
        var params = {
            success: function(){td.removePreloader()},
            url: Admin.url + "access/editActionName?ajax=1",
            type: "post",
            response: 'text',
            data: {id:td.parentNode.getAttribute('data-field-id'), 'action':el.value}
        };
        Ajax.request(params);
    },
    confirmDeleteAction: function(el) {
        DialogWindow.Confirm('delete_access_action', "Realy delete access action?", function(e){if(!e)return;AccessForm.deleteAction(el)}, "I know what I do", "No")
    },
    deleteAction: function(el){
        var p = el.parentNode.parentNode;
        var id = p.getAttribute('data-field-id');
        var params = {
            success: function(){p.remove()},
            url: Admin.url + "access/deleteAction?ajax=1",
            type: "post",
            response: 'text',
            data: {id:id}
        };
        Ajax.request(params);
    },
    confirmDeleteAccessGroup: function(el){
        DialogWindow.Confirm('delete_access_group', "Realy delete this access group?", function(e){if(!e)return;AccessForm.deleteAccessGroup(el)}, "I know what I do", "No")
    },
    deleteAccessGroup: function(el){
        var p = el.parentNode;
        var params = {
            success: function(){
                var table = p.parentNode.parentNode;
                var cells = table.getAll('td[data-name="'+ p.innerText+'"]');
                for(var i = 0;i<cells.length;i++)cells[i].remove();
                p.remove();
            },
            url: Admin.url + "access/deleteAccessGroup?ajax=1",
            type: "post",
            response: 'text',
            data: {group: p.innerText}
        };
        Ajax.request(params);
    },
    promptCreateAccessGroup: function(){
        DialogWindow.Prompt('create_access_group', 'Enter new access group name.<br/> Use "_" char as word separator', AccessForm.createAccessGroup);
    },
    promptCreateAccessAction: function(){
        DialogWindow.Prompt('create_access_action', 'Enter new access action name.<br/> Use "_" char as word separator', AccessForm.createAccessAction);
    },
    createAccessGroup: function(check, name){
        if(!check)return;
        var params = {
            success: AccessForm.requestAccessForm,
            url: Admin.url + "access/createAccessGroup?ajax=1",
            type: "post",
            response: 'text',
            data: {group: name}
        };
        Ajax.request(params);
    },
    createAccessAction: function(check, name){
        if(!check)return;
        var params = {
            success: AccessForm.requestAccessForm,
            url: Admin.url + "access/createAccessAction?ajax=1",
            type: "post",
            response: 'text',
            data: {action: name}
        };
        Ajax.request(params);
    }
};
//{/literal}