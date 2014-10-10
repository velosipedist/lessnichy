// startup compiler polling. Assume that jQuery included
var Lessnichy = {
    postProcess: function (css) {

    },
    extractId: function (href) {
        return href.replace(/^[a-z-]+:\/+?[^\/]+/, '')  // Remove protocol & domain
            .replace(/^\//, '')  // Remove root /
            .replace(/\.[a-zA-Z]+$/, '')  // Remove simple extension
            .replace(/[^\.\w-]+/g, '-')  // Replace illegal characters
            .replace(/\./g, ':'); // Replace dots with colons(for valid id)
    }
};

// Task for watch all document's less stylesheets and when they're compiled send to backend saver
Lessnichy.Task = function () {
    var self = this;
    // collect LESS sources
    $('link[rel="stylesheet/less"]').each(function () {
        self.lessmap.push({
            id: "less:" + Lessnichy.extractId($(this).attr('href')),
            href: $(this).attr('href'),
            written: false,
            writing: false
        });
    });
};

Lessnichy.Task.prototype.pollTimer = null;
Lessnichy.Task.prototype.passedCount = 0;
Lessnichy.Task.prototype.lessmap = [];
Lessnichy.Task.prototype.poll = function () {
    var self = this;
    for (var i in this.lessmap) {
        var config = this.lessmap[i];
        if (config.writing || config.written) {
            continue;
        }
        var cssSheet = document.getElementById(config.id);
        if (cssSheet) {
            var sheets = {};
            config.writing = true;
            sheets[config.href] = CleanCSS.process(cssSheet.textContent);

            //todo send csrf token
            $.ajax(less.lessnichy.url + '/css', {
                "type": "post",
                "dataType": "json",
                data: {
                    "sheets": sheets
                }
            }).success(function (response) {
                try {
                    console.log(response);
                } catch (e) {
                }
                config.writing = false;
                config.written = true;
                self.passedCount++;
            });
        }
    }
    if (this.passedCount == this.lessmap.length) {
        clearInterval(this.pollTimer);
    }
};

Lessnichy.Task.prototype.run = function (interval) {
    var self = this;
    interval = interval || 250;
    setTimeout(function () {
        self.pollTimer = setInterval(function () {
            self.poll.apply(self)
        }, interval);
    }, less.poll + 1000);
}

//  start up
(function ($) {
$(document.body).append($('<link/>', {
    rel: "stylesheet",
    href: less.lessnichy.url + "/css/lessnichy.css"
}));

    var watchBtn = $('<a class="btn btn-info watch-btn">WATCH</a>').addClass(less.watchMode ? 'on' : 'off')
        .on('click', function () {
            if (watchBtn.is('.on')) {
                less.unwatch();
            } else {
                less.watch();
            }
            watchBtn.toggleClass('on off');
        });

    $('<div class="lessnichy"><div class="btn-group"></div></div>')
        .appendTo(document.body)
        .find('.btn-group')
        .append(watchBtn);

var task = new Lessnichy.Task();
task.run();
})(jQuery);

//todo popup notifiers