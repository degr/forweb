/**
 * get first dom element with setted css selector
 * @param t String, CSS selector.
 * @returns {HTMLElement}
 */
Element.prototype.get = function(t){
    return this.querySelector(t);
};
/**
 * get node list with setted css selector
 * @param t String, CSS selector
 * @returns {NodeList}
 */
Element.prototype.getAll = function(t){
    return this.querySelectorAll(t);
};

/**
 * check, have element class or not
 * @param c
 * @returns {boolean}
 */
Element.prototype.hasClass = function(c){
    return this.className.split(' ').indexOf(c) != -1 ? true : false;
};
/**
 * add classname to element. Chainable.
 * @param className
 * @returns {HTMLElement}
 */
Element.prototype.addClass = function(className){
    var c=this.className.split(' ');for(var i=0;i<c.length;i++)if(c[i]==className)return;this.className+=" "+className;
};
/**
 * Remove classname from element. Chainable.
 * @param className
 * @returns {HTMLElement}
 */
Element.prototype.removeClass = function(className){
    var c=this.className.split(' ');for(var i=0;i<c.length;i++)if(c[i]==className){c.splice(i,1);break;}this.className=c.join(' ',className);
};

function newElement(tag, attr, elements){
    var e=document.createElement(tag);
    for(var i in attr){e.setAttribute(i, attr[i]);};
    if(elements)for(var i=0;i<elements.length;i++)e.appendChild(elements[i]);
    return e;
}

Element.prototype.getDimensions = function(){
    return [this.offsetWidth, this.offsetHeight];
};
Element.prototype.setStyle = function(style){
    for(var s in style){this.style[s]=style[s];}
};

Element.prototype.show = function(clazz){
    this.removeClass('hidden');
};
Element.prototype.hide = function(clazz){
    this.addClass('hidden');
};
Element.prototype.getWidth = function(clazz){
    return this.getDimensions()[0];
};
Element.prototype.getHeight = function(clazz){
    return this.getDimensions()[1];
};
Element.prototype.animate = function(style,v,time) {
    var me = this;
    if (!this.style[style]) {
        if (style == 'width' || style == 'height') {
            this.style[style] = (style == 'width' ? this.getWidth() : this.getHeight ) + "px";
        } else {
            this.style[style] = getComputedStyle(this)[style];
        }
    }
    var p = this.style[style];
    if(p || p===0){
        var v0= parseInt(p);
        var d=v-v0;
        var n=p?p.replace(/\d|[.]/g, ''):'px';
        var s = d*30/time;
        var i = setInterval(function(){
            if(v0>v)v0=v;
            me.style[style]=v0+n;
            v0+=s;
            if(v0 >= v) {
                clearInterval(i);
            }
        }, 30);
    }
}

Element.prototype.drawPreloader = function(clazz){
    if(typeof clazz !== "string")clazz='';
    //@TODO gui class for round, rect etc preloaders
    b=newElement('div', {'class': 'preloader '+clazz});
    this.appendChild(b);
    this.preloader = b;
};
Element.prototype.removePreloader = function(){
    if(this.preloader){
        this.preloader.remove();
        delete(this.preloader);
    }
};
Element.prototype.moveUp = function(){
    var p=this.parentNode;
    var n= p.childNodes;
    var l=n.length;
    var t=null;
    for(var i=0;i<l;i++){if(n[i]===this)break;t=n[i];}
    if(t!=null){p.insertBefore(this,t);return true;}else return false;
}
Element.prototype.moveDown = function(){
    var p=this.parentNode;
    var n= p.childNodes;
    var l=n.length;
    var t=null;
    for(var i=0;i<l;i++){if(n[i]!==this)continue;if(i+1<l)t=n[i+1];break;}
    if(t!=null){p.insertBefore(t,this);return true;}else return false;
}
Element.prototype.up = function(s){
    if(!s)return this.parentNode;
    var p = this.parentNode;
    if(p) {
        var pp = p.parentNode;
        var n = pp.getAll(s);
        for(var i=0;i< n.length;i++){
            if(n[i] == p){
                return p;
            }
        }
        return p.up(s);
    }
    return null;
}