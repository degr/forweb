<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ForWeb install page</title>
</head>
<body>
<p id="view"></p>
<iframe id="console" src="?deploy=1&getDependencies=Core" name="console" onload="execute()"></iframe>
<form target="console" id="form" method="post" ></form>
<script>

    var installed = {
        core:{name: 'Core', installed: false, dependencies: []}
    };
    var onRequest = true;
    function execute(){
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

    function read(){
        var frame = document.getElementById('console');
        var holder = frame.contentWindow ? frame.contentWindow : frame.contentDocument;
        return holder.document.getElementsByTagName('body')[0].innerHTML;

    }

    function send(object, url) {
        if(onRequest) {
            setTimeout(send(object, url), 500);
            return;
        }
        console.log(url);
        var form = document.getElementById('form');
        form.action = url;
        form.setAttribute('action', url);
        form.innerHTML = '';
        var hasProperties = false;
        for(var i in object) {
            if(!hasProperties) {
                hasProperties = true;
            }
            var o = object[i];
            var input = document.createElement('input');
            input.setAttribute('name', i);
            input.value = o;
            form.appendChild(input);
        }
        if(!hasProperties) {
            form.method = 'get';
        } else {
            form.method = 'post';
        }
        form.setAttribute('method', form.method);
        form.submit();
    }

    function getModuleDependencies(moduleName){
        send({}, '?deploy=1&getDependencies='+encodeURIComponent(moduleName));
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