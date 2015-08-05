var Search = {
    onAjaxSearch: function(form){
        var value = form.get('.searchbar').value;
        if(!value)return;
        Ajax.request({
            url: Core.url + "ajax/search/search",
            type: "post",
            success: Search.onSearchResponse,
            data: {'search': value},
            response: 'json'
        });
    },
    onSearchResponse: function(r) {
        if(!r.search){
            DialogWindow.Alert('search_window_empty', "Nothing found");
            return;
        }
        var search = [];
        for(var i = 0; i < r.search.length; i++){
            var s = r.search[i];
            var textNode = newElement('div', {}, s.value);
            var number = newElement('span', {}, (i * (r.page + 1) +1) + " " );
            var name = newElement('a', {href:s.url, 'class': 'bold'}, s.name);
            var locale = newElement('span', {'class': 'locale'}, "(" + s.locale+")");

            var header = newElement('div', {'class': 'border-bottom'}, [number, name, locale]);

            var text = typeof textNode.textContent !== 'undefined' ? textNode.textContent : textNode.innerText;
            if(text.length > 200) {
                text = text.substring(0, 200) + "...";
            }
            var body = newElement('a', {href:s.url}, text);
            search.push(newElement('div', {'class': 'search-item'},[header, newElement('p', {}, [body])]));
        }
        DialogWindow.Alert('search_window', newElement('div', {'class':'search-result'}, search)).setWidth(600);
    }

};