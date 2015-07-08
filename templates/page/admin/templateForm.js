//{literal}
var TemplateForm = {
    getIncludesForm: function(blocks){
        var out = newElement('div', {'class':'template_blocks'});
        for(var i in blocks) {
            out.appendChild(TemplateForm.getBlock(blocks[i]))
        }
        return out;
    },
    getBlock: function (block) {
        var deleteBlock = newElement("a",{href:'#',onclick:'TemplateForm.deleteBlock(this, '+block.id+');return false;','class':'delete_block icon-delete right'});
        var titleText = newElement('span', {});
        titleText.innerHTML = block.name;
        var title = newElement('h3', {'class':'block_title left'}, [titleText,deleteBlock]);
        var arrows = PageContent.getArrows();

        var ar = arrows.getAll('a');
        for(var i = 0; i < ar.length; i++){
            ar[i].setAttribute('onclick', 'TemplateForm.moveBlock(this,'+(i==0?'true':'false')+');return false;');
        }
        var clearfix = newElement('div',{'class':'clearfix'});
        var form = newElement('form', {method:'post', 'data-id':block.id, 'class':'clearfix','onsubmit':'TemplateForm.updateBlock(this);return false;'},[title,arrows,clearfix]);
        form.appendChild(newElement('input',{'type':'hidden','name':'block_id',value:block.id}));
        form.appendChild(newElement('div', {'class':'serverResponse'}));
        var incHolder = newElement('div', {'class':'include_holder'});
        if(block.includes) {
            for (var i = 0; i < block.includes.length; i++) {
                var fields = {};
                for(var ff in block.includes[i]){
                    fields[ff] = {value:block.includes[i][ff]};
                    if(ff == 'method') {
                        fields[ff].options = block.includes[i].methods_list;
                    }
                }

                var dto = {fields:fields};
                incHolder.appendChild(PageContent.getTemplate(true, dto, false, false));
            }
        }
        form.appendChild(incHolder);
        form.appendChild(newElement('input', {type:'submit', 'class':'left'}));
        form.appendChild(newElement('input', {type:'button', value:'+', 'class':'left', 'onclick':'TemplateForm.newInclude(this)'}));
        return form;
    },
    mainFormSubmit: function(e){
        Core.prevent(e);
        var data = Core.serialize(document.body.get('#form_templates'));
        params = {
            success: TemplateForm.mainFormSuccess,
            url: Admin.url + "ajax/page/editTemplate",
            type: "POST",
            response: 'text',
            data: data
        };
        Ajax.request(params);
    },
    mainFormSuccess: function(value, d){
        var text = document.body.get('#form_templates, #delete_template_form').parentNode.get(".text");
        text.innerHTML = value;
        setTimeout(function(){text.innerHTML = '';}, 5000);
    },
    deleteTemplate: function(e){
        Core.prevent(e);
        var s = document.body.get('#delete_template_form select[name="id"]');
        var id = s.value;
        var data = {id: id};
        params = {
            success: function(e){TemplateForm.mainFormSuccess(e.text);if(e.success)s.get('option[value="'+id+'"]').remove();},
            url: Admin.url + "ajax/page/deleteTemplate",
            type: "POST",
            response: 'json',
            data: data
        };
        Ajax.request(params);
    },
    createTemplate: function(){
        if(Admin.nowDisplayed == 'createTemplate') {
            Admin.getWindow().show();
            return;
        }
        Admin.nowDisplayed = 'createTemplate';

        params = {
            success: TemplateForm.createNewTemplateForm,
            url: Admin.url + "ajax/page/createTemplate",
            type: "post",
            response: 'json',
            data: {'action':'getForm'}
        };
        Ajax.request(params);
    },
    createNewTemplateForm: function(response){
        var form = UI.build(response);
        Event.add(form, 'submit', TemplateForm.submitCreateTemplate);
        var w = Admin.getWindow();
        w.setContent('');
        Admin.setResponseText(response);
        w.appendContent(form);
        w.setWidth(0);
        w.show();
    },
    submitCreateTemplate: function(e){
        Core.prevent(e);
        params = {
            success: TemplateForm.onCreateNewTemplateForm,
            url: Admin.url + "ajax/page/createTemplate",
            type: "post",
            response: 'json',
            data: {'action':'save', form:Core.serialize(document.body.get('#form_templates'))}
        };
        Ajax.request(params);
    },
    onCreateNewTemplateForm: function(text){
        Admin.nowDisplayed = '';
        var w = Admin.getWindow();
        w.setContent('');
        var d = newElement('div', {});
        d.innerHTML = text.text;
        w.appendContent(d);
        w.setWidth(0);
        w.show();
    },
    deleteTemplateForm: function(){
        if(Admin.nowDisplayed == 'deleteTemplateForm') {
            Admin.getWindow().show();
            return;
        }
        Admin.nowDisplayed = 'deleteTemplateForm';
        var params = {
            success: TemplateForm.showDeleteTemplateForm,
            url: Admin.url + "ajax/page/deleteTemplateForm",
            type: "post",
            response: 'json',
            data: {'action':'getForm'}
        };
        Ajax.request(params);

    },
    showDeleteTemplateForm: function(r){
        var form = UI.build(r);
        var t = newElement('div', {'class':'text'});
        Event.add(form, 'submit', TemplateForm.deleteTemplate);
        var w = Admin.getWindow();
        w.setContent('');
        w.appendContent(newElement('div', {}, [t, form]));
        w.setWidth(0);
        w.show();
    },
    addBlock: function() {
        DialogWindow.Prompt('template_add_block', 'Enter new block name:', TemplateForm.createBlock);
    },
    createBlock: function(c, t){
        if(!c)return;
        var template = document.body.get('#form_templates input[name="id"]').value;
        var params = {
            success: TemplateForm.addCreatedBlock,
            url: Admin.url + "ajax/page/createBlock",
            type: "post",
            response: 'json',
            data: {'name':t, templateId:template}
        };
        Ajax.request(params);
    },
    addCreatedBlock: function(r){
        var block = TemplateForm.getBlock(r.block);
        document.body.get('.template_blocks').appendChild(block);
    },
    deleteBlock: function(el, id){
        DialogWindow.Confirm(
            "templates_delete_block",
            Admin.getWord('delete_template_block'),
            function(a){if(!a)return;TemplateForm.processDelete(id)},
            Admin.getWord('confirm_yes'),
            Admin.getWord("confirm_no")
        );
    },
    processDelete: function(id){
        var template = document.body.get('#form_templates input[name="id"]').value;
        var params = {
            success: function(){TemplateForm.removeBlock(id);},
            url: Admin.url + "ajax/page/deleteBlock",
            type: "post",
            response: 'text',
            data: {'id':id,templateId:template}
        };
        Ajax.request(params);
    },
    removeBlock: function(id){
        var form = document.body.get('.template_blocks form[data-id="'+id+'"]');
        if(form)form.remove();
    },
    moveBlock: function (el, toTop) {
        var f = el.parentNode.parentNode;
        if(toTop)f.moveUp();
        else f.moveDown();
        TemplateForm.saveBlocksPosition();
    },
    saveBlocksPosition: function(){
        var forms = document.body.getAll('.template_blocks form');
        var template = document.body.get('#form_templates input[name="id"]').value;
        var data = {blocks:{},template:template};
        for(var i =0;i<forms.length;i++){
            if(forms[i].getAttribute('data-id')){
                data.blocks[i] =forms[i].getAttribute('data-id');
            }
        }
        var params = {
            url: Admin.url + "ajax/page/saveBlocksPosition",
            type: "post",
            response: 'json',
            data: data
        }
        Ajax.request(params);
    },
    newInclude: function(el){
        var form = el.up('form');
        var b = form.get('.include_holder');
        var inc = PageContent.getTemplate(true);
        var length = b.getAll('.include_row').length;
        inc.get('input[name="template"]').value = document.body.get('#form_templates input[name="id"]').value;
        inc.get('input[name="block"]').value = form.getAttribute('data-id');
        inc.get('input[name="positionNumber"]').value = length;
        b.appendChild(inc);
    },
    updateBlock: function(form){
        var blockId = form.getAttribute('data-id');
        var templateId = document.body.get('#form_templates input[name="id"]').value;

        var data = Core.serialize(form, '.include_row');
        var params = {
            success: TemplateForm.submitSuccess,
            url: Admin.url + "ajax/page/updateBlock?block="+blockId+"&template="+templateId,
            type: "post",
            response: 'json',
            data: data
        }
        Ajax.request(params);
    },
    submitSuccess: function(a,r){
        var b = document.body.getAll('#admin_panel form > input[name="block_id"]');
        for(var i = 0; i < b.length; i++) {
            if(b[i].value == a.block){
                var f = b[i].parentNode;
                f.get('.serverResponse').innerHTML = a.text;
                setTimeout(function(){f.get('.serverResponse').innerHTML="";}, 5000)
                var ids = f.getAll('.include_row input[name="id"]');
                for(var j = 0; j < ids.length; j++) {
                    ids[j].value = a.ids[j];
                }
                break;
            }
        }
    },
    shiftTo: function(up, el){
        var holder = el.up('.include_row');
        if(up)holder.moveUp();
        else holder.moveDown();
        var h = holder.up().getAll('.include_row');
        for(var i = 0; i< h.length;i++) {
            h[i].get('input[name="positionNumber"]').value = i+1;
        }
    }
};
//{/literal}