// startup compiler polling. Assume that jQuery included
(function($){
    function extractId(href) {
        return href.replace(/^[a-z-]+:\/+?[^\/]+/, '' )  // Remove protocol & domain
            .replace(/^\//,                 '' )  // Remove root /
            .replace(/\.[a-zA-Z]+$/,        '' )  // Remove simple extension
            .replace(/[^\.\w-]+/g,          '-')  // Replace illegal characters
            .replace(/\./g,                 ':'); // Replace dots with colons(for valid id)
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
                sheets[config.href] = CleanCSS.process(cssSheet.textContent);

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
})(jQuery);

//todo popup notifiers