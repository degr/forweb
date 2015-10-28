var Paginator = function(){
    this.total = 0;
    this.onPage = 15;
    this.page = 0;

    this.shoulder = 3;
    this.container = null;
    this.wrapper = null;

    this.onClick = null;
    this.linkBuildingStrategy = null;

    this.build = function(){
        var nav = document.createElement("nav");
        return this.consume(nav);
    };

    this.consume = function(element){
        var ul = document.createElement('ul');
        ul.className = 'pagination';
        element.appendChild(ul);

        this.container = ul;
        this.wrapper = element;

        this.rerender();
        return element;
    };

    this.rerender = function(){
        this.container.innerHTML = '';
        this.addBackButton();
        this.addMainButtons();
        this.addForwardButton();
    };

    this.addBackButton = function(){
        if(this.page > 1) {
            this.container.appendChild(this.buildButton("«", 'button-back', this.page - 1, true));
        }
    };
    this.addMainButtons = function(){
        var pagesCount = Math.ceil(this.total / this.onPage);
        if(pagesCount <= 1)return;
        var min = this.page - this.shoulder - 1 < 0 ? 0 : this.page - this.shoulder - 1;
        var max = this.page + this.shoulder > pagesCount ? pagesCount : this.page + this.shoulder;
        for(var i = min + 1; i <= max; i++) {
            this.container.appendChild(this.buildButton(i, "button-number", i, false));
        }
    };
    this.addForwardButton = function(){
        var pagesCount = Math.ceil(this.total / this.onPage);
        if(pagesCount > this.page) {
            this.container.appendChild(this.buildButton("»", 'button-forward', this.page + 1, true));
        }
    };
    this.buildButton = function(html, className, page, isArrow){
        if(this.linkBuildingStrategy != null && typeof this.linkBuildingStrategy === 'function') {
            return this.linkBuildingStrategy(html, className, page, isArrow);
        } else {
            var out = document.createElement('li');
            var link = document.createElement('a');
            link.href = document.location.protocol + '//' + document.location.host + document.location.pathname + '?page='+page;
            if(!isArrow && page == this.page){
                out.className = 'active';
            }
            link.className = className;
            link.innerHTML = html;
            out.appendChild(link);
            var me = this;
            if(this.onClick && typeof this.onClick === 'function'){
                this._addEvent(link, 'click', function(e){me.onClick(page, me, e)});
            }
            return out;
        }
    };

    this._addEvent = function(obj, eventType, handler){
        if (obj.addEventListener) {
            obj.addEventListener(eventType, handler, false);
            return true;
        } else if (obj.attachEvent) {
            return obj.attachEvent('on' + eventType, handler);
        }
    };
};