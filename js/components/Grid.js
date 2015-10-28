/**
 * Created by rsmirnou on 10/28/2015.
 */
var Grid = function(element){
    this.element = element;
    this.model = {};
    this.tHead = null;
    this.tBody = null;
    this.data = null;
    this.sort = null;
    this.onImport = null;
    var me = this;

    this.exporter = null;/*object with functions: exportCell(id, name, value), exportRow(id, me.data[id]), exportTable(me.data), deleteRow(id)*/
    this.importer = null;/*object with function: import(me, pageNumber, itemsOnPage). This function can set me.pagesLimit*/
    this.pagesLimit = null;
    this.pageNumber = 0;
    this.itemsOnPage = 20;

    this.openPage = function(pageNumber) {
        me.pageNumber = pageNumber;
        me.importer.import(me, pageNumber, me.itemsOnPage, me.onImport);
    };
    this.nextPage = function() {
        if(this.pagesLimit !== null && this.pagesLimit <= me.pageNumber + 1)return;
        me.openPage(me.pageNumber + 1);
    };
    this.previousPage = function() {
        if(me.pageNumber <= 0)return;
        me.openPage(me.pageNumber - 1);
    };
    this.updateData = function() {

    };
    /**
     * set model to grid. Remove all old data.
     */
    this.setModel = function(model){
        this.element.innerHTML = '';

        this.model = model;
        this.tHead = document.createElement('thead');
        this.tBody = document.createElement('tbody');
        this.element.appendChild(this.tHead);
        this.element.appendChild(this.tBody);


        for(var i in model.columns){
            if(model.columns.hasOwnProperty(i)) {
                var c = model.columns[i];
                var th = document.createElement('th');
                th.setAttribute('data-name', i);
                if (c.renderer === 'attribute') {
                    continue;
                }
                if (!c.label && c.label !== '') {
                    th.innerHTML = i;
                } else if (c.label) {
                    th.innerHTML = c.label;
                }
                this.tHead.appendChild(th);
                if (c.sort) {
                    var sorter = document.createElement('a');
                    sorter.href = '#';
                    sorter.className = 'grid-sorter';
                    sorter.setAttribute('data-sort-order', c.sort.direction == 'desc' ? 'desc' : 'asc');
                    var me = this;
                    this._addEvent(sorter, 'click', me.applySortOnClick);

                    th.appendChild(sorter);

                    if (c.sort.active) {
                        this.sort = c.sort;
                        this.sort.id = i;
                    }
                }
            }
        }
    };
    /**
     * Apply sort on sorter click
     */
    this.applySortOnClick = function(e){
        var el = this._getTarget(e);
        var cols = me.model.columns;
        var id= el.parentNode.getAttribute('data-name');
        var colModel = cols[id];
        var sort = colModel.sort;

        if(sort.active) {
            sort.order = sort.order == 'desc' ? 'asc' : 'desc';
            el.setAttribute('data-sort-order', sort.order);
        } else {
            for(var i in cols) {
                if(cols.hasOwnProperty(i)) {
                    if (cols[i].sort && cols[i].sort.active) {
                        me.model.columns[i].sort.active = false;
                    }
                }
            }
            sort.active = true;
        }
        if(!sort.id)sort.id = id;
        me.sort = sort;
        me.setData(me.data);
    };
    /**
     * set data to grid. Array of {} with key-value pairs according to model
     */
    this.setData = function(data){
        this.data = data;
        if(this.sort){
            this.applySort(this.sort);
        }
        this.tBody.innerHTML = '';
        for(var i = 0; i < data.length; i++){
            this.addRow(data[i]);
        }
    };
    /**
     * apply active sorter
     */
    this.applySort = function(sort){
        this.data.sort(sort.customSort ? sort.customSort : function(i1, i2){
            if(sort.order && sort.order.toLowerCase() == 'desc') {
                return i1[sort.id] < i2[sort.id];
            } else {
                return i1[sort.id] > i2[sort.id];
            }
        });
    };
    /**
     * add one row. Row is {} with key-value pairs according to model
     */
    this.addRow = function(row){
        var r = document.createElement('tr');
        var id = row[me.model.primaryKey];

        for(var i in this.model.columns) {
            if(this.model.columns.hasOwnProperty(i)) {
                var m = this.model.columns[i];
                var td = document.createElement('td');
                td.setAttribute('data-name', i);
                var notAdd = false;
                if (m.renderer) {
                    if (typeof m.renderer === 'string') {
                        switch (m.renderer) {
                            case 'attribute':
                                r.setAttribute('data-' + i, row[i]);
                                notAdd = true;
                                break;
                            case 'cell':
                                td.innerHTML = row[i] ? row[i] : null;
                                if (m.editable && i != me.model.primaryKey) {
                                    me.createEditable(td, id, i);
                                }
                                break;
                            default:
                                throw new Error('Undefined renderer type: ' + m.renderer);
                        }
                    } else if (typeof m.renderer === 'function') {
                        var o = m.renderer(row[i], row, me);
                        if (typeof o == 'object') {
                            td.appendChild(o);
                        } else {
                            td.innerHTML = o;
                        }
                    } else {
                        throw new Error('Undefined renderer type: ' + m.renderer);
                    }
                } else {
                    td.innerHTML = row[i] ? row[i] : null;
                    if (m.editable && i != me.model.primaryKey) {
                        me.createEditable(td, id, i);
                    }
                }
                if (!notAdd)r.appendChild(td);
            }
        }
        if(typeof this.model.rowRenderer === 'function') {
            this.model.rowRenderer(r, row);
        }
        this.tBody.appendChild(r);
    };

    this.deleteRow = function(id){
        var pk = this.model.primaryKey;
        for(var i = 0; i < this.data.length; i++) {
            var r = this.data[i];
            if(r[pk] == id) {
                var deleteCallback = function(pass) {
                    if(!pass)return;
                    me.data.splice(i, 1);
                    if (me.model.columns[pk].renderer === 'attribute') {
                        var tr = me.element.querySelector('*[data-' + pk + '="' + id + '"]');
                        if (tr)tr.remove();
                    } else {
                        me.setData(me.data);
                    }
                };
                if(this.exporter !== null) {
                    this.exporter.deleteRow(id, deleteCallback);
                } else {
                    deleteCallback(true);
                }
                break;
            }
        }
    };
    /**
     * set new sort order, but not apply it.
     */
    this.setSort = function(sort){
        this.sort = sort;
    };

    this.onEditStart = function(td, id, name){
        if(td.getAttribute('data-on-edit') == '1')return;
        td.setAttribute('data-on-edit', '1');
        var i = document.createElement('input');
        i.value = td.innerHTML;
        this._addEvent(i, 'blur', function(){me.onEditEnd(td, id, name)});
        td.innerHTML = null;
        td.appendChild(i);
        i.focus();
    };

    this.onEditEnd = function(td, id, name){
        var value = td.getElementsByTagName('input')[0].value;
        td.innerHTML = value;
        td.removeAttribute('data-on-edit');
        if(!me.model.primaryKey)return;

        for(var j = 0;j< me.data.length;j++){
            if(me.data[j][me.model.primaryKey] == id) {
                me.data[j][name] = value;
                if(me.exporter) {
                    me.exporter.exportCell(id, name, value);
                }
                break;
            }
        }
    };

    this.createEditable = function(td, id, name){
        this._addEvent(td, 'click', function(){me.onEditStart(td, id, name)});
    };
    this._addEvent = function(obj, eventType, handler){
        if (obj.addEventListener) {
            obj.addEventListener(eventType, handler, false);
            return true;
        } else if (obj.attachEvent) {
            return obj.attachEvent('on' + eventType, handler);
        }
    };
    this._getTarget = function(e){
        var out;
        if (!e)e = window.event;
        if (e.target) out = e.target; else out = e.srcElement;
        return out.nodeType == 3 ? out.parentNode : out;
    }
};
