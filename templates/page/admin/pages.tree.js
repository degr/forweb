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
        var ul = PagesTree.fillPagesTree(response.pages);
        PagesTree.generateLinks(ul);
        var w = Admin.getWindow();
        w.setContent(ul);
        w.setWidth(700);
        w.show();
    },

    generateLinks: function(list){

        var home = list.get('li[data-parent="0"]');
        home.get('a').href = Admin.url;
        var homeId = home.getAttribute('data-id');
        var url = "";
        PagesTree.generateLinksForId(list, homeId, url);
    },
    generateLinksForId: function (list, id, url) {
        url = (url ? url + "/" : "");
        var items = list.getAll('ul[data-parent="'+id+'"] > li');
        for(var i = 0; i < items.length; i++) {
            var item = items[i];
            var a = item.get('div a');
            var itemUrl = a.getAttribute('data-url');
            a.href = Admin.url + url + itemUrl;
            var sublist = item.get('ul');
            if(sublist)PagesTree.generateLinksForId(sublist, item.getAttribute('data-id'), url + itemUrl);
        }
    },
    buildListItem: function (obj) {
        var a = newElement('a', {'href':'#','data-url':obj.url, 'class':'item'});
        a.innerHTML = obj.name;
        var a1 = newElement('a', {'href':'#','onclick':"PagesTree.pageShift(this, true);return false;", 'class':'icon-arrow-up'});
        var a2 = newElement('a', {'href':'#','onclick':"PagesTree.pageShift(this, false);return false;", 'class':'icon-arrow-down'});
        var container = newElement('div', {'class':'pages-tree-holder clearfix'}, [a, a1, a2]);
        var li = newElement('li', {'data-id': obj.id, 'data-parent':obj.parent, 'data-position':obj.position}, [container]);
        return li;
    },
    pageShift: function(arrow, up){
        var item = arrow.up('li');
        var check = up ? item.moveUp() : item.moveDown();
        if(check) {
            var list = item.up();
            var parent = list.getAttribute('data-parent');
            var links = list.getAll('li[data-parent="'+parent+'"]');
            var data = {parent:parent, items:{}};
            for(var i = 0; i < links.length; i++) {
                data.items['id_' + links[i].getAttribute('data-id')] = i;
            }
            var params = {
                url: Admin.url + "page/changePagePositions?ajax=1",
                type: "post",
                response: 'json',
                data: data
            };
            Ajax.request(params);
        }
    },
    fillPagesTree: function(obj){
        var out = {};
        var empty = true;
        var lists = {};
        var items = {}
        for(var i in obj) {
            var o = obj[i];
            o.parent = parseInt(o.parent);
            if(!lists[o.parent])lists[o.parent] = [];
            var item = PagesTree.buildListItem(o);
            lists[o.parent].push(item);
            items[o.id] = item;
        }
        var opts = {'class':'pages_tree'};
        for(var i in lists) {
            if(i != 0) {
                opts['data-parent'] = i;
                items[i].appendChild(newElement('ul', opts, lists[i].sort(PagesTree.sort)));
            }
        }
        return newElement('ul', opts, lists[0]);

    },
    sort: function(i1, i2) {
        var v1 = parseInt(i1.getAttribute('data-position'));
        var v2 = parseInt(i2.getAttribute('data-position'));
        if(v1 < v2)return -1;
        if(v1 > v2)return 1;
        return 0;
    }
}

//{/literal}
