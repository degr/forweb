//{literal}
var PageContent = {
    staticContentTextarea: null,
    getTemplate: function(active, v, forPage){
        var e = {};
        var out = newElement('div', {'class': 'block include_row '+(active ? "active" : "")})
        e.id = {tag:"input",name:"id",attributes:{type:"hidden"},"class":"template",validation:null,layout:"grid"};
        e.block = {tag:"input",name:"block",options:null,attributes:{"type":"hidden"},validation:null,layout:"grid"};
        e.page = {tag:"input",name:"page",attributes:{type:'hidden'},layout:"grid"};
        e.template = {tag:"input",name:"template",attributes:{"type":"hidden"},layout:"grid"};
        e.positionNumber = {"tag":"input","name":"positionNumber","attributes":{"type":"hidden"},"layout":"grid"};
        e.type = {tag:"select",name:"type",value:"html",options:AdminIncludeOptions,"class":"content_type",id:"Persist_includes_type",title:Admin.getWord("field_type"),description:null,error:null,validation:null,layout:"grid"};
        e.content = {"tag":"input","name":"content",attributes:{type:'hidden'},"layout":"grid"};
        //e.position = {"tag":"select","name":"position","value":"before","options":{"before":"before","template":"template","after":"after"},"attributes":null,"class":"template","title":"position","description":null,"error":null,"validation":null,"layout":"grid"};
        e.position = {"tag":"input","name":"position","value":(forPage ? "after" : 'template'),"attributes":{"type":"hidden"},"validation":null,"layout":"grid"};
        e.module = {"tag":"select","name":"module",attributes:{'onchange':'PageContent.updateMethods(this);'},"options":Admin.modulesList,"title":Admin.getWord("field_module"),"layout":"grid"};
        e.method = {"tag":"select","name":"method","options":null,"title":Admin.getWord("field_method"),"layout":"grid"};
        e.comment = {"tag":"input","name":"comment",attributes:{type:'text','placeholder':Admin.getWord('put_comment')},"layout":"grid"};
        var checkForValues = v ? true : false;

        e.page['class'] = active ? 'dynamic' : 'static';
        for(var i in e) {
            if(checkForValues) {
                if(v && v.fields){
                    if(v.fields[i].value)e[i].value = v.fields[i].value;
                    if(v.fields[i].options)e[i].options = v.fields[i].options;
                }
            }
            if(!active) {
                if(!e[i].attributes)e[i].attributes={};
                e[i].attributes.disabled = true;
            }
            e[i] = (UI.builder.buildFormfield(e[i]));
        }
        out.appendChild(e.id);
        out.appendChild(e.block);
        out.appendChild(e.page);
        out.appendChild(e.template);
        out.appendChild(e.positionNumber);
        out.appendChild(e.position);

        out.appendChild(PageContent.getType(e.type));
        if(active)out.appendChild(PageContent.getArrows(forPage));
        out.appendChild(PageContent.getContentWindow(e.content, e.module, e.method));
        if(active)out.appendChild(newElement("a",{href:'#',onclick:'PageContent.deleteIclude(this);return false;','class':'icon-delete left'}));
        out.appendChild(newElement('div', {'class':'left comment_holder'}, [e.comment]));
        out.appendChild(newElement('div', {'class':'clearfix'}));
        return out;
    },
    updateMethods: function(el){
        var row = el.up('.include_row');
        console.log(row);
        var methods = row.get('select[name="method"]');
        console.log(methods);
        if(el.value != 0) {
            Admin.setMethodsTo(methods, el.value);
        } else {
            methods.innerHTML = '';
        }
    },
    getArrows: function(forPage){
        var out = newElement("div", {'class': 'arrowsHolder left'});
        var handler = forPage ? 'PageContent.shiftTo' : 'TemplateForm.shiftTo';
        out.appendChild(newElement("a", {'class': 'icon-arrow-up','href':'#','onclick':handler+'(true, this);return false'}));
        out.appendChild(newElement("a", {'class': 'icon-arrow-down', 'href':'#','onclick':handler+'(false, this);return false;'}));
        return out;
    },
    getType: function(type){
        return newElement("div", {'class':'left'},[type]);
    },
    getContentWindow: function(content, module, method){
        var a = newElement('a', {'href':'#','class':'showContentToggler', 'onclick':'PageContent.showContent(this);return false;'});
        a.innerHTML = Admin.getWord('show_content');
        var w1 = newElement('div', {'class':'staticContent hidden'}, [content]);
        var w2 = newElement('div', {'class':'dynamicContent hidden'}, [module, method]);
        return newElement('div', {'class':'left content_toggler'}, [a,w1,w2]);
    },
    shiftTo: function(up,e){
        var p= e.parentNode.parentNode;
        var n= p.parentNode.childNodes;
        var l= n.length;
        var ci= p.get("input[name='positionNumber']");
        var pos=parseInt(ci.value);

        if(up)pos--;else pos++;
        var inBlockBefore = p.parentNode.hasClass('before');
        if(up && inBlockBefore && pos<1)return;
        else if(!up && !inBlockBefore && l<=pos)return;

        var onBlockJump = false;
        if(up && !inBlockBefore && pos<1){
            onBlockJump = true;
            var block = p.parentNode.parentNode.get('.before');
            block.appendChild(p);
            p.get('input[name="position"]').value = 'before';
        }else if(!up && inBlockBefore && l<=pos){
            onBlockJump = true;
            var block = p.parentNode.parentNode.get('.after');
            if(block.childNodes.length == 0) {
                block.appendChild(p);
            } else {
                block.insertBefore(p, block.childNodes[0]);
            }
            p.get('input[name="position"]').value = 'after';
        }
        if(onBlockJump) {
            var blocks = p.parentNode.parentNode.getAll('.before, .after');
            for (var j = 0; j < blocks.length; j++) {
                var block = blocks[j];
                var l = block.childNodes.length;
                for (var i = 0; i < l; i++) {
                    block.childNodes[i].get("input[name='positionNumber']").value = i + 1;
                }
            }
            return;
        }

        var cp;
        for(var i=0;i<l;i++){
            if(n[i]===p){cp=i;break;}
        }
        var t=up?n[cp-1]:n[cp+1];
        var ti= t.get("input[name='positionNumber']");
        ci.value=pos;
        if(up) {
            ti.value=pos+1;
            p.moveUp();
        }else{
            ti.value=pos-1;
            p.moveDown();
        }
    },
    showContent: function(el){
        var p=el.parentNode;
        var showContent = el.getAttribute("data-shown");
        if(!showContent || showContent == '0') {
            el.innerHTML = Admin.getWord('hide_content');
            el.setAttribute("data-shown", '1');
            var i = p.parentNode.get('select[name="type"]').value;

            if (i == 'executable') {
                p.get('.dynamicContent').show();
                var method = p.get('.dynamicContent select[name="method"]');
                if(method.options.length == 0) {
                    var mod = p.get('.dynamicContent select[name="module"]');
                    var val = null;
                    for(var o =0; 0<mod.options.length; o++){
                        if(mod.options[o].value != 0) {
                            val = mod.options[o].value;
                            mod.options[o].selected = true;
                            break;
                        }
                    }
                    if(val)Admin.setMethodsTo(method, val);
                }
            } else {
                var ta = PageContent.getStaticContentTextarea(p.get('.staticContent').get('input'));
                ta.show();
                p.up('form').appendChild(ta)
                p.get('.dynamicContent').hide();
            }
        } else {
            el.innerHTML = Admin.getWord('show_content');
            el.setAttribute("data-shown", '0');
            p.get('.staticContent').hide();
            p.get('.dynamicContent').hide();
            if(PageContent.staticContentTextarea != null) {
                PageContent.staticContentTextarea.hide();
            }
        }
    },
    getAddTemplateButton: function(){
        return newElement("input", {type:'button',onclick:'PageContent.addInclude(this);',value:'+','class':'addNewInclude'});
    },
    addInclude: function(e){
        var form = e.parentNode;
        var values = {
            fields:{
                id: {value:''},
                block: {value:form.get('input[name="block_id"]').value},
                page: {value:form.get('input[name="page_id"]').value},
                template: {value:0},
                positionNumber:{value:form.get('.after').childNodes.length+1},
                type:{value:'html'},
                position:{value:'after'},
                content:{value:''},
                module:{value:''},
                method:{value:''},
                comment:{value:''}
            }
        };
        var b= e.parentNode.get('.after').appendChild(PageContent.getTemplate(true, values, true));
    },
    getStaticContentTextarea: function (input) {
        if(PageContent.staticContentTextarea == null) {
            var p = newElement('p', {});
            p.innerHTML = Admin.getWord('include_textarea');
            var t = newElement('textarea', {onblur:'PageContent.closeTextarea();'});
            var c = newElement('a', {href:'#',onclick:'PageContent.closeTextarea();return false;'});
            c.innerHTML = "x";
            PageContent.staticContentTextarea = newElement('div', {'class':'staticContentTextarea'},[p, t,c]);
            PageContent.staticContentTextarea.textarea = t;
            var h = Admin.getWindow().window.getHeight();
            PageContent.staticContentTextarea.style.height = (h-30)+"px";
            PageContent.staticContentTextarea.get('textarea').style.height = (h-55)+"px";

            Admin.getWindow().appendContent(PageContent.staticContentTextarea);
        }
        PageContent.staticContentTextarea.refInput = input;
        PageContent.staticContentTextarea.textarea.value = input.value;
        return PageContent.staticContentTextarea;
    },
    closeTextarea: function(){
        PageContent.staticContentTextarea.refInput.value = PageContent.staticContentTextarea.textarea.value;
        PageContent.staticContentTextarea.hide();
        var e = PageContent.staticContentTextarea.
            refInput.parentNode.parentNode.get('a.showContentToggler');
        PageContent.showContent(e);
    },
    submit: function(e, form){
        Core.prevent(e);
        var data = Core.serialize(form, '.include_row.active');
        var pageId = form.get('input[name="page_id"]').value;
        var blockId = form.get('input[name="block_id"]').value;
        var params =  {
            url: Admin.url + "page/processPageContent?ajax=1&page="+pageId+"&block="+blockId,
            type: "POST",
            success: PageContent.submitSuccess,
            response: 'json',
            data: data
        };
        Ajax.request(params);
    },
    submitSuccess: function(a,r){
        var b = document.body.getAll('#admin_panel form > input[name="block_id"]');
        for(var i = 0; i < b.length; i++) {
            if(b[i].value == a.block){
                var f = b[i].parentNode;
                f.get('.serverResponse').innerHTML = a.text;
                setTimeout(function(){f.get('.serverResponse').innerHTML="";}, 5000)
                var ids = f.getAll('.before .include_row input[name="id"], .after .include_row input[name="id"]');
                for(var j = 0; j < ids.length; j++) {
                    ids[j].value = a.ids[j];
                }
                break;
            }
        }
    },
    deleteIclude: function(el){
        var clb = function(r) {
            if(!r)return;
            var b = el.parentNode;
            var id = b.get('input[name="id"]').value;
            var success = function (text, v) {
                if (text == '1') {
                    b.remove();
                }
            };
            var params = {
                url: Admin.url + "page/deletePageInclude?ajax=1&include=" + id,
                type: "POST",
                success: success,
                response: 'text'
            };
            Ajax.request(params);
        };
        DialogWindow.Confirm(
            'delete_include',
            Admin.getWord('delete_include_confirm'),
            clb,
            Admin.getWord('confirm_yes'),
            "No"
        )
    }
};
//{/literal}