// startup compiler polling
(function(){
    function extractId(href) {
        return href.replace(/^[a-z-]+:\/+?[^\/]+/, '' )  // Remove protocol & domain
            .replace(/^\//,                 '' )  // Remove root /
            .replace(/\.[a-zA-Z]+$/,        '' )  // Remove simple extension
            .replace(/[^\.\w-]+/g,          '-')  // Replace illegal characters
            .replace(/\./g,                 ':'); // Replace dots with colons(for valid id)
    }
    function fireEvent(element, event) {
        var evt;
        var isString = function(it) {
            return typeof it == "string" || it instanceof String;
        }
        element = (isString(element)) ? document.getElementById(element) : element;
        if (document.createEventObject) {
            // dispatch for IE
            evt = document.createEventObject();
            return element.fireEvent('on' + event, evt)
        }
        else {
            // dispatch for firefox + others
            evt = document.createEvent("HTMLEvents");
            evt.initEvent(event, true, true); // event type,bubbling,cancelable
            return !element.dispatchEvent(evt);
        }
    }

    var lessmap = [];
    var pollTimer;

    function poll(){
        var passed = 0;
        for(var i in lessmap){
            var config = lessmap[i];
            if(config.writing || config.written){
                passed++;
                continue;
            }
            var cssSheet = document.getElementById(config.id);
            if(cssSheet){
                var sheets = {};
                config.writing = true;
                sheets[config.href] = cssSheet.textContent;

                //todo send csrf token
                $.ajax(less.lessnichy.url,{
                    "type":"post",
                    "dataType":"json",
                    data: {
                        "sheets":sheets
                    }
                }).success(function(response){
                    console.log(response);
                    config.writing = false;
                    config.written = true;
                });
            }
        }
        if (passed == lessmap.length){
            clearInterval(pollTimer);
        }
    }

    // start polling
    $('link[rel="stylesheet/less"]').each(function(){
        lessmap.push({
            id:"less:"+extractId($(this).attr('href')),
            href: $(this).attr('href'),
            written: false,
            writing: false
        });
    });

    setTimeout(function(){
        pollTimer = setInterval(poll, 500);
    },less.poll+1000);
})();

//todo popup notifiers