/**
 * Created by Ror on 29.03.2015.
 */
//{literal}
var PagesTree = {
    showPagesTree: function(){
        if(Admin.nowDisplayed == 'pagesTree') {
            Admin.getWindow().show();
            return;
        }
        Admin.nowDisplayed = 'pagesTree';
        var params = {
            url: Admin.url + "page/showPagesTree?ajax=1",
            type: "get",
            success: PagesTree.renderPagesTree,
            response: 'json',
            data: {href: document.location.href}
        };
        Ajax.request(params);
    },
    renderPagesTree: function(response){
        var table = newElement('table', {});
        PagesTree.fillPagesTree(table, response.pages, 0);
        var colspan = PagesTree.setPaddingsPagesTree(table);
        PagesTree.generateLinks(table);
        PagesTree.buildTableHead(table, [Admin.getWord('page_form_field_name'), Admin.getWord('page_form_field_url')], colspan);

        var w = Admin.getWindow();
        w.setContent(table);
        w.setWidth(700);
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
            PagesTree.fillPagesTree(table, out, parent);
        }
    }
}

//{/literal}
