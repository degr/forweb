/**
 * Created by rsmirnou on 7/8/2015.
 */
var Autocomplete = function(element, searchHandler){
    this.element = element;/*your element*/
    this.input = null;/*input element (nameless)*/
    this.wrapper = null;/*wrapper*/
    this.box = null;/*dom element with hint items*/
    this.data = [];/*data array*/
    this.currentSelection = null;
    this.search = null;/*search word*/
    this.selectedItem = null;/*currently selected search item*/
    this.activeItem = null;/*current active item. Chosen selected item.*/
    this.searchHandler = searchHandler;/*handler for onchange event. Recieve this.search as argument*/
    var me = this;

    this.setData = function(data){
        this.data = data;
    };

    this.showHint = function(search){
        if(!this.data)return;
        search = search.trim();
        if(search == me.search)return;
        me.search = search;
        this.box.removeClass('hidden');
        this.box.innerHTML = '';

        for(var i = 0; i < this.data.length; i++) {
            var c = this.data[i];
            if(c.value.indexOf(search) == -1)continue;
            var li = document.createElement('li');
            li.innerHTML = me.makeTextBold(c.value);
            li.className = 'autocomplete-item';
            li.setAttribute('data-id', c.id);
            li.addEvent('click', me.onmouseclick);
            this.box.appendChild(li);
        }
    };

    this.hideHint = function(){
        me.box.addClass('hidden');
        me.box.innerHTML = '';
    };
    this.highlightSearchItem = function(up){
        var items = me.box.querySelectorAll('li.autocomplete-item');
        var length = items.length;
        var index;
        switch(length) {
            case 0:
                return;
            case 1:
                index = 0;
                break;
            default:
                index = me.currentSelection != null ? me.currentSelection + (up ? -1 : 1) : 0;
                if(index == length) {
                    index--;
                } else if(index < 0) {
                    index = 0;
                }
        }
        if(me.currentSelection != index) {
            me.currentSelection = index;
            me.highlight(items[index]);
        }
    };
    this.highlight = function(el) {
        var active = me.box.querySelector('li.autocomplete-item.active');
        if(active)active.removeClass('active');
        el.addClass('active');
    };

    this.onmouseclick = function(e){
        var target;
        e.stop();
        if(!e.target.hasClass('autocomplete-item')) {
            var a = e.target;
            while(!a.hasClass('autocomplete-item')) {
                a = a.parentNode;
            }
            target = a;
        } else {
            target = e.target;
        }

        var id = target.getAttribute('data-id');
        if(target.hasClass('active')){
            me.selectItem();
        } else {
            var items = me.box.querySelectorAll('.autocomplete-item');
            for(var i = 0; i < items.length; i++) {
                if(items[i] == target) {
                    me.currentSelection = i;
                    me.highlight(target);
                    break;
                }
            }
        }
    };
    this.onkeydown = function(e){
        //we need prevent enter onkeydown, because form onsubmit triggers earlier than onkeyup
        if(e.keyCode == 27) {
            //escape
            me.mountActiveItem();
            me.resetSearch();
            e.stop();
        } else if(e.keyCode == 13) {
            //enter
            console.log('autocomplete enter');
            me.selectItem();
            e.stop();
        }
    };
    this.onkeyup = function(e){
        console.log(e.keyCode);
        if(e.keyCode == 37 || e.keyCode == 39 || e.keyCode == 13 || e.keyCode == 27) {
            //do nothing, left and right arrows
        } else if(e.keyCode == 38) {
            //up arrow
            me.highlightSearchItem(true);
            e.stop();
        } else if(e.keyCode == 40) {
            //down arrow
            me.highlightSearchItem(false);
            e.stop();
        } else {
            if(me.searchHandler) {
                me.searchHandler(me.input.value, me);
            }
        }
    };
    this.resetSearch = function(){
        me.currentSelection = null;
        me.search = '';
        me.selectedItem = null;
        me.hideHint();
        me.box.innerHTML = '';
    };
    this.selectItem = function(){
        var items = me.box.querySelectorAll('li.autocomplete-item');
        if(!items[me.currentSelection])return;
        var id = items[me.currentSelection].getAttribute('data-id');
        me.activeItem = null;
        for(var i in me.data) {
            console.log(me.data[i], id);
            if(id == me.data[i].id){
                me.activeItem = me.data[i];
                break;
            }
        }
        if(me.activeItem) {
            me.mountActiveItem();
            me.resetSearch();
        }
    };
    this.mountActiveItem = function(){
        if(!me.activeItem)return;
        me.element.value = me.activeItem.id;
        me.input.value = me.activeItem.value;
    };
    this.makeTextBold = function(text){
        var subSearch = this.search.split(' ');
        subSearch = subSearch.sort(function(v1, v2){return v1.length < v2.length});
        for(var i = 0; i < subSearch.length; i++) {
            if(!subSearch[i])continue;
            var re = new RegExp('(' + subSearch[i].trim() + ')', 'gi');
            text = text.replace(re, '<b>$1</b>');
        }
        return text;
    };

    (function(){
        var input = document.createElement('input');
        input.type = element.type ? element.type : 'text';
        input.addClass('autocomplete-input');
        me.input = input;

        element.type = 'hidden';

        me.wrapper = document.createElement('div');
        me.wrapper.className = 'autocomplete';
        element.parentNode.appendChild(me.wrapper);
        element.remove();

        me.box = document.createElement('ul');
        me.box.className = 'autocomplete-box';

        me.wrapper.appendChild(element);
        me.wrapper.appendChild(input);
        me.wrapper.appendChild(me.box);
        input.addEvent('keyup', me.onkeyup);
        input.addEvent('keydown', me.onkeydown);

    })();
};