var DragManager = {
	store: [],
	agents:{},
	agentClasses: {},
	isOnDrag: false,
	dragStart: false,
	object: null,
	consume: function(o){
		o.target = o.target?o.target:o.key;
		with(DragManager){
			o.key.addEvent('mousedown', dockMouse);
			fixTarget(o);
			store.push(o);
		}
	},
	init: function(){
		document.body.addEvent('mouseup', DragManager.undockMouse);
		document.body.addEvent('mousemove', DragManager.follow);
	},
	follow: function(e){
		with(DragManager){
			if(!isOnDrag)return;
			if(!dragStart)onDragStart(e);
			var a = agents[object['agent']];
			
			object._.mc.x = parseInt(e.x);
			object._.mc.y = parseInt(e.y);
			object._.sc = DragManager.getScroll();
			agents[object['agent']].render(object);
		}
	},
	dockMouse: function(e){
		with(DragManager){
			isOnDrag = true;
			object = find(e.target);
		}
	},
	undockMouse: function(e){
		if(!DragManager.isOnDrag)return;
		document.body.removeClass('no-selection');
		with(DragManager){
			agents[object.agent].onDrop(object);
			isOnDrag = false;
			object = null;
			dragStart = false;
		}
	},
	onDragStart: function(e){
		document.body.addClass('no-selection');
		with(DragManager){
			var o = find(e.target);
			var el=o.target, i=o.key;
			if(!el)return;
			o._={};
			o._.el={x:parseInt(el.style.left),y:parseInt(el.style.top)};
			o._.mc={x:0,y:0};
			o._.mi={x:parseInt(e.x),y:parseInt(e.y)};
			o._.cs=null;/*@see getScroll*/
			dragStart = true;
			agents[o.agent].onGrab(o);
		}
	},
	find: function(el){
		if(el === document.body)return null;
		for(var i=0;i<DragManager.store.length;i++){
			var o=DragManager.store[i];
			if(o.key === el)return o;
		}
		return DragManager.find(el.parentNode);
	},
	calculate: function(o, i){
		var r=o._.el[i]+o._.mc[i]-o._.mi[i];
		if(r < 0)return 0;
		var w=DragManager.getWindowSize(i);
		var e = DragManager.object.target[(i=='x' ? 'offsetWidth' : 'offsetHeight')]
		return (r + e < w) ? r : w - e;
	},
	getWindowSize: function(d){
		var w = window[d === 'x' ? 'innerWidth' : 'innerHeight'];
		var b = document.body[d === 'x' ? 'offsetWidth' : 'offsetHeight'];
		var s = screen[d === 'x' ? 'availHeight' : 'availWidth'];
		if(s > w && s > b)return s;
		if(w > s && w > b)return w;
		return b;
	},
	fixTarget: function(obj){
		var s;
		if(obj.container){
		
		} else obj.container = window;
		
		with(obj){
			obj.oX = obj.oX===false?0:1;
			obj.oY = obj.oY===false?0:1;
			if(obj.fixPosition !== false){
				s = target.style.position
				if(!s)s=getComputedStyle(target)['position'];
				if(s != 'absolute' & s != 'fixed')target.style.position='absolute';
			}
		}
		obj.target.remove = function(){
			DragManager.dropFromStore(obj.target);
			Element.prototype.remove.apply(obj.target);
		};
		var isT,isL;
		with(obj.target.style){isT=!!top;isL=!!left;}
		var r = DragManager.getLeftTop(obj.target, isT, isL);
		if(!isL)obj.target.style.left=r.left+'px';
		if(!isT)obj.target.style.top=r.top+'px';
	},
	dropFromStore: function(el){
		var s = DragManager.store;
		for(var i = 0; i < s.length;i++)
			if(s[i].target === el)s.splice(i, 1);
	},
	getLeftTop: function(o,isT, isL, absolute){
		var r = {left: !isL ? o.offsetLeft : 0, top: !isT ? o.offsetTop : 0};
		while(o = o.offsetParent) {
			if(absolute !== true && DragManager.isNotStatic(o))break;
			if(!isL)r.left += o.offsetLeft;
			if(!isT)r.top += o.offsetTop;
		}
		return r;
	},
	isNotStatic: function(el) {
		var s = ['relative', 'absolute', 'fixed', 'sticky'];
		if(s.indexOf(el.style.position) > -1)return true;
		var p = getComputedStyle(el)['position'];
		return s.indexOf(p) > -1;
	},
	getScroll: function(){
		var w = window, d = document;
		return {x: ((w.pageXOffset || d.scrollLeft) - (d.clientLeft || 0)) || 0,
			y: ((w.pageYOffset || d.scrollTop ) - (d.clientTop || 0)) || 0};
	}
};