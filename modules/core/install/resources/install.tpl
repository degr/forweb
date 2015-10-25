<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ForWeb install page</title>
</head>
<body>
<p id="view"></p>
<script>
    var Install = {
        dependencies: null,
        write: function(t){
            var d = document.getElementById('view');
            d.innerHTML += t + '<br/>';
        },
        start: function(){
            this.requestDependencies();
            setTimeout(Install.startToResolveDependencies, 500);
        },
        requestDependencies: function(){
            Install.write("Requesting for project dependencies");
            Install.Ajax.request({
                url: '?deploy=1&dependecies=1',
                response: 'json',
                success: function(o){Install.dependencies = o;}
            });
        },
        startToResolveDependencies: function(){
            if(Install.dependencies === null) {
                setTimeout(Install.startToResolveDependencies, 500);
                return;
            }
            Install.write("Dependencies colleted, start to resolve.");
            Install.Ajax.request({
                url: '?deploy=1&installExisting=1',
                response: 'json',
                success: function(o){
                    alert('installed');
                    console.log(o);
                }
            });
        },
        Ajax: {
            request : function(obj) {
                var req = this.getXmlHttp();

                var url = obj.url || window.location.href;
                var reqType = obj.type || "GET";
                var success = obj.success||false;
                var data = obj.data || null;
                var response = obj.response || 'json';

                req.onloadend = function() {
                    if(success){
                        var answer = response == 'json' ? JSON.parse(req.responseText) : req.responseText;
                        success(answer, req);
                    }
                };

                req.open(reqType, url, true);
                //	if (reqType.toLowerCase() == "post") {
                req.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                //}
                if (!data) {
                    req.send(null);
                } else {
                    if(typeof data == 'object'){
                        var d = [];
                        for(var i in data){
                            d.push(encodeURIComponent(i)+'='+(typeof data[i] == 'object' ? Install.Ajax.encodeRecoursivly(data[i]) : encodeURIComponent(data[i])));
                        }
                        data = d.join('&');
                    }
                    req.send(data);
                }

            },
            getXmlHttp : function() {
                if (window.XMLHttpRequest && (!window.location || 'file:' != window.location.protocol || !window.ActiveXObject)) {
                    return new XMLHttpRequest;
                } else {
                    try { return new ActiveXObject('Microsoft.XMLHTTP'); } catch(e) { }
                    try { return new ActiveXObject('Msxml2.XMLHTTP.6.0'); } catch(e) { }
                    try { return new ActiveXObject('Msxml2.XMLHTTP.3.0'); } catch(e) { }
                    try { return new ActiveXObject('Msxml2.XMLHTTP'); } catch(e) { }
                }
                return null;
            },

            encodeRecoursivly: function(v){
                var d = [];
                for(var i in v) {
                    d.push('"'+encodeURIComponent(i)+'":"'+(typeof v[i] == 'object' ? Ajax.encodeRecoursivly(v[i]) : encodeURIComponent(v[i]))+'"');
                }
                return "{" + d.join(encodeURIComponent( ',' )) + '}';
            },
        }
    };
    Install.start();
    function execute(){
        if(!document.getElementById('console').onload) {
            document.getElementById('console').onload = execute;
        }
        var responseString = read();
        var response;
        var view = document.getElementById('view');
        if(responseString) {
            try {
                response = JSON.parse(responseString);
                switch (response.type) {
                    case 'dependencies':
                        onRequest = false;
                        addDependencies(response);
                        loadDependencyChain(response.dependencies);
                        return;
                    default:
                        view.innerHTML += '<span style="color:red;">Unknown object type: '+response.type+'</span>';
                }
            } catch (e) {
                view.innerHTML += '<span style="color:red;">Last command end with error.</span>';
                console.log(e);
                return;
            }
        }

    }
    function getModuleDependencies(moduleName){
        send({i:''}, '?deploy=1&getDependencies='+encodeURIComponent(moduleName));
    }

    function addDependencies(response){
        if(!response.dependencies) {
            return;
        }
        if(!installed[response.module]) {
            installed[response.module] = {name: response.module, installed: false, dependencies: response.dependencies};
        } else {
            installed[response.module].dependencies = response.dependencies;
        }
    }

    function installModule(moduleName){
        installed[moduleName] = {name: moduleName, installed: false, dependencies: []};
    }

    function loadDependencyChain(dependencies){
        if(!dependencies || !dependencies.length) {

        } else {
            var current = dependencies.pop();
            if(installed[current.moduleName]) {
                loadDependencyChain(dependencies);
            } else {
                getModuleDependencies(current.moduleName);
            }
        }
    }
</script>
</body>
</html>